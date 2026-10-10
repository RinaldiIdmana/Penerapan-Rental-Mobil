<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Vehicle;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AvailabilityCalendarController extends Controller
{
    public function __construct(
        protected ReservationService $reservationService
    ) {}

    /**
     * Display the availability calendar view.
     */
    public function index(Request $request): View
    {
        $vehicles = Vehicle::orderBy('brand')->orderBy('model')->get();
        $selectedVehicleId = $request->query('vehicle_id');

        return view('calendar.index', compact('vehicles', 'selectedVehicleId'));
    }

    /**
     * Provide JSON feed of reservation events for FullCalendar.
     */
    public function events(Request $request): JsonResponse
    {
        $query = Reservation::with(['vehicle', 'user'])
            ->whereIn('status', ['confirmed', 'active', 'completed', 'pending']);

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('start') && $request->filled('end')) {
            $start = Carbon::parse($request->start)->toDateString();
            $end = Carbon::parse($request->end)->toDateString();
            $query->overlapping($start, $end);
        }

        $reservations = $query->get();

        $events = $reservations->map(function (Reservation $reservation) {
            $color = match ($reservation->status) {
                'active' => '#059669', // Emerald
                'confirmed' => '#2563eb', // Blue
                'pending' => '#d97706', // Amber
                'completed' => '#64748b', // Slate
                default => '#94a3b8',
            };

            // FullCalendar end date is exclusive for all-day events, so add 1 day
            $endDateForCalendar = Carbon::parse($reservation->end_date)->addDay()->toDateString();

            return [
                'id' => $reservation->id,
                'title' => "{$reservation->vehicle->license_plate} - {$reservation->vehicle->model} ({$reservation->user->name})",
                'start' => $reservation->start_date->toDateString(),
                'end' => $endDateForCalendar,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'code' => $reservation->reservation_code,
                    'customer' => $reservation->user->name,
                    'vehicle' => $reservation->vehicle->full_name,
                    'plate' => $reservation->vehicle->license_plate,
                    'status' => ucfirst($reservation->status),
                    'totalDays' => $reservation->total_days,
                    'amount' => $reservation->formatted_base_amount,
                    'url' => route('reservations.show', $reservation),
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * Anti-overlapping check endpoint for AJAX form validation.
     */
    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $isAvailable = $this->reservationService->isVehicleAvailable(
            (int) $request->vehicle_id,
            $request->start_date,
            $request->end_date
        );

        return response()->json([
            'available' => $isAvailable,
            'message' => $isAvailable
                ? 'Armada tersedia pada rentang tanggal tersebut.'
                : 'Peringatan: Jadwal bertabrakan dengan reservasi lain yang aktif/terkonfirmasi.',
        ]);
    }
}
