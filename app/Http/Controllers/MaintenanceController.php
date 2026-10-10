<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaintenanceRequest;
use App\Models\Maintenance;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of maintenance logs and schedules.
     */
    public function index(Request $request): View
    {
        $query = Maintenance::with('vehicle');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->input('vehicle_id'));
        }

        if ($request->input('filter') === 'overdue') {
            $query->overdue();
        } elseif ($request->input('filter') === 'due_soon') {
            $query->dueSoonOrOverdue(7);
        }

        $maintenances = $query->orderBy('next_service_due_date', 'asc')->paginate(10)->withQueryString();
        $vehicles = Vehicle::orderBy('brand')->orderBy('model')->get();

        $overdueCount = Maintenance::overdue()->count();
        $dueSoonCount = Maintenance::dueSoonOrOverdue(7)->count();

        return view('maintenances.index', compact('maintenances', 'vehicles', 'overdueCount', 'dueSoonCount'));
    }

    /**
     * Show the form for creating a new maintenance record.
     */
    public function create(Request $request): View
    {
        $selectedVehicleId = $request->query('vehicle_id');
        $vehicles = Vehicle::orderBy('brand')->orderBy('model')->get();

        return view('maintenances.create', compact('vehicles', 'selectedVehicleId'));
    }

    /**
     * Store newly created maintenance record.
     */
    public function store(StoreMaintenanceRequest $request): RedirectResponse
    {
        $maintenance = Maintenance::create($request->validated());

        // Modul 4 auto-update: if maintenance status is in_progress or scheduled for today, set vehicle status to 'maintenance'
        if (in_array($maintenance->status, ['in_progress', 'scheduled'], true)) {
            $maintenance->vehicle->update(['status' => 'maintenance']);
        }

        return redirect()->route('maintenances.index')
            ->with('success', "Jadwal servis untuk {$maintenance->vehicle->license_plate} berhasil dicatat.");
    }

    /**
     * Show the form for editing the maintenance record.
     */
    public function edit(Maintenance $maintenance): View
    {
        $vehicles = Vehicle::orderBy('brand')->orderBy('model')->get();

        return view('maintenances.edit', compact('maintenance', 'vehicles'));
    }

    /**
     * Update maintenance record.
     */
    public function update(Request $request, Maintenance $maintenance): RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'service_date' => ['required', 'date'],
            'service_type' => ['required', 'string', 'max:150'],
            'cost' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'next_service_due_date' => ['required', 'date', 'after_or_equal:service_date'],
            'status' => ['required', 'in:scheduled,in_progress,completed'],
        ]);

        $maintenance->update($validated);

        if ($maintenance->status === 'completed') {
            // Check if vehicle has any other in-progress maintenance
            $hasOtherPending = Maintenance::where('vehicle_id', $maintenance->vehicle_id)
                ->where('id', '!=', $maintenance->id)
                ->whereIn('status', ['in_progress'])
                ->exists();

            if (!$hasOtherPending && $maintenance->vehicle->status === 'maintenance') {
                $maintenance->vehicle->update(['status' => 'available']);
            }
        } elseif (in_array($maintenance->status, ['in_progress', 'scheduled'], true)) {
            $maintenance->vehicle->update(['status' => 'maintenance']);
        }

        return redirect()->route('maintenances.index')
            ->with('success', "Data servis armada {$maintenance->vehicle->license_plate} diperbarui.");
    }

    /**
     * Quick action to complete a maintenance task.
     */
    public function complete(Maintenance $maintenance): RedirectResponse
    {
        $maintenance->update(['status' => 'completed']);

        // Check if other maintenances still active
        $hasOtherActive = Maintenance::where('vehicle_id', $maintenance->vehicle_id)
            ->where('id', '!=', $maintenance->id)
            ->whereIn('status', ['in_progress'])
            ->exists();

        if (!$hasOtherActive && $maintenance->vehicle->status === 'maintenance') {
            $maintenance->vehicle->update(['status' => 'available']);
        }

        return back()->with('success', "Servis armada {$maintenance->vehicle->license_plate} telah ditandai Selesai dan status armada siap disewa kembali.");
    }
}
