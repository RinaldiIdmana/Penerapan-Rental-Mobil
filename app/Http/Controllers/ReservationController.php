<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function __construct(
        protected ReservationService $reservationService
    ) {}

    /**
     * Display listing of reservations for management.
     */
    public function index(Request $request): View
    {
        $query = Reservation::with(['vehicle', 'user', 'invoice', 'returnRecord']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('reservation_code', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'LIKE', "%{$search}%")->orWhere('email', 'LIKE', "%{$search}%"))
                  ->orWhereHas('vehicle', fn($vq) => $vq->where('license_plate', 'LIKE', "%{$search}%")->orWhere('model', 'LIKE', "%{$search}%"));
            });
        }

        $reservations = $query->latest()->paginate(10)->withQueryString();

        return view('reservations.index', compact('reservations'));
    }

    /**
     * Show booking form.
     */
    public function create(Request $request): View
    {
        $selectedVehicleId = $request->query('vehicle_id');
        $selectedVehicle = $selectedVehicleId ? Vehicle::find($selectedVehicleId) : null;

        // Only vehicles available for rent
        $vehicles = Vehicle::whereNotIn('status', ['maintenance', 'out_of_service'])->orderBy('brand')->get();
        $customers = Auth::user()->hasAdminPrivileges()
            ? User::where('role', 'customer')->orderBy('name')->get()
            : collect([Auth::user()]);

        return view('reservations.create', compact('vehicles', 'selectedVehicle', 'customers'));
    }

    /**
     * Store new reservation using ReservationService.
     */
    public function store(StoreReservationRequest $request): RedirectResponse
    {
        $user = Auth::user();

        // If staff/admin is booking for a specific customer
        if ($user->hasAdminPrivileges() && $request->filled('customer_id')) {
            $bookingUser = User::findOrFail($request->input('customer_id'));
        } else {
            $bookingUser = $user;
        }

        $reservation = $this->reservationService->createReservation($request->validated(), $bookingUser);

        $redirectRoute = Auth::user()->hasAdminPrivileges()
            ? route('reservations.show', $reservation)
            : route('reservations.my');

        return redirect($redirectRoute)
            ->with('success', "Reservasi {$reservation->reservation_code} berhasil dibuat! Invoice resmi telah diterbitkan.");
    }

    /**
     * Display the specified reservation.
     */
    public function show(Reservation $reservation): View
    {
        // Customer authorization: only view their own unless admin/staff
        if (!Auth::user()->hasAdminPrivileges() && $reservation->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat reservasi ini.');
        }

        $reservation->load(['vehicle', 'user', 'invoice', 'returnRecord']);

        return view('reservations.show', compact('reservation'));
    }

    /**
     * Confirm a reservation.
     */
    public function confirm(Reservation $reservation): RedirectResponse
    {
        $this->reservationService->confirmReservation($reservation);

        return back()->with('success', "Reservasi {$reservation->reservation_code} berhasil dikonfirmasi.");
    }

    /**
     * Cancel a reservation.
     */
    public function cancel(Reservation $reservation): RedirectResponse
    {
        // Customer can only cancel their own pending reservations
        if (!Auth::user()->hasAdminPrivileges() && ($reservation->user_id !== Auth::id() || $reservation->status !== 'pending')) {
            abort(403, 'Anda hanya dapat membatalkan reservasi berstatus pending milik Anda sendiri.');
        }

        $this->reservationService->cancelReservation($reservation);

        return back()->with('info', "Reservasi {$reservation->reservation_code} berhasil dibatalkan.");
    }

    /**
     * Customer view for their own reservations.
     */
    public function myReservations(): View
    {
        $reservations = Reservation::with(['vehicle', 'invoice', 'returnRecord'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('reservations.my', compact('reservations'));
    }

    /**
     * AJAX endpoint to calculate cost preview on date selection.
     */
    public function calculateCost(Request $request): JsonResponse
    {
        $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $result = $this->reservationService->calculateDaysAndAmount($vehicle, $request->start_date, $request->end_date);
        $isAvailable = $this->reservationService->isVehicleAvailable($vehicle->id, $request->start_date, $request->end_date);

        return response()->json([
            'total_days' => $result['total_days'],
            'base_amount' => $result['base_amount'],
            'formatted_amount' => 'Rp ' . number_format($result['base_amount'], 0, ',', '.'),
            'rate_per_day' => (float) $vehicle->rental_rate_per_day,
            'is_available' => $isAvailable,
        ]);
    }
}
