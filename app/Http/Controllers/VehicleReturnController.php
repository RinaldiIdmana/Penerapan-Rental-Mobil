<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessReturnRequest;
use App\Models\Reservation;
use App\Models\VehicleReturn;
use App\Services\ReturnService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleReturnController extends Controller
{
    public function __construct(
        protected ReturnService $returnService
    ) {}

    /**
     * Display a listing of returns.
     */
    public function index(): View
    {
        $returns = VehicleReturn::with(['reservation.vehicle', 'reservation.user', 'invoice'])
            ->latest()
            ->paginate(10);

        return view('returns.index', compact('returns'));
    }

    /**
     * Show return form for an active/confirmed reservation.
     */
    public function create(Reservation $reservation): View|RedirectResponse
    {
        if (in_array($reservation->status, ['completed', 'cancelled'], true)) {
            return redirect()->route('reservations.show', $reservation)
                ->with('error', 'Reservasi ini sudah selesai atau dibatalkan.');
        }

        $reservation->load(['vehicle', 'user', 'invoice']);

        $today = Carbon::now()->toDateString();
        $preview = $this->returnService->previewReturn($reservation, $today);

        return view('returns.create', compact('reservation', 'preview', 'today'));
    }

    /**
     * Preview calculation for AJAX in return form when user picks a return date or fine rate.
     */
    public function preview(Reservation $reservation, Request $request): JsonResponse
    {
        $request->validate([
            'return_date' => ['required', 'date'],
            'fine_per_day' => ['nullable', 'numeric', 'min:0'],
        ]);

        $preview = $this->returnService->previewReturn(
            $reservation,
            $request->return_date,
            $request->filled('fine_per_day') ? (float) $request->fine_per_day : null
        );

        return response()->json([
            'late_days' => $preview['late_days'],
            'fine_per_day' => $preview['fine_per_day'],
            'total_fine' => $preview['total_fine'],
            'formatted_fine' => 'Rp ' . number_format($preview['total_fine'], 0, ',', '.'),
            'final_total_amount' => $preview['final_total_amount'],
            'formatted_final_total' => 'Rp ' . number_format($preview['final_total_amount'], 0, ',', '.'),
            'is_late' => $preview['late_days'] > 0,
        ]);
    }

    /**
     * Store return process.
     */
    public function store(Reservation $reservation, ProcessReturnRequest $request): RedirectResponse
    {
        $vehicleReturn = $this->returnService->processReturn($reservation, $request->validated());

        return redirect()->route('reservations.show', $reservation)
            ->with('success', "Armada {$reservation->vehicle->license_plate} berhasil diproses pengembaliannya! Denda: {$vehicleReturn->formatted_total_fine}.");
    }
}
