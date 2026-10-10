<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    /**
     * Display a listing of vehicles for staff/admin.
     */
    public function index(Request $request): View
    {
        $query = Vehicle::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('brand', 'LIKE', "%{$search}%")
                  ->orWhere('model', 'LIKE', "%{$search}%")
                  ->orWhere('license_plate', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('transmission')) {
            $query->where('transmission', $request->input('transmission'));
        }

        $vehicles = $query->with(['maintenances' => fn($q) => $q->latest()->limit(1)])
            ->orderBy('brand')
            ->orderBy('model')
            ->paginate(10)
            ->withQueryString();

        return view('vehicles.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new vehicle.
     */
    public function create(): View
    {
        return view('vehicles.create');
    }

    /**
     * Store a newly created vehicle in storage.
     */
    public function store(StoreVehicleRequest $request): RedirectResponse
    {
        $vehicle = Vehicle::create($request->validated());

        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', "Armada {$vehicle->full_name} ({$vehicle->license_plate}) berhasil didaftarkan.");
    }

    /**
     * Display the specified vehicle.
     */
    public function show(Vehicle $vehicle): View
    {
        $vehicle->load([
            'reservations' => fn($q) => $q->with('user')->latest()->take(10),
            'maintenances' => fn($q) => $q->latest()->take(10),
        ]);

        return view('vehicles.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified vehicle.
     */
    public function edit(Vehicle $vehicle): View
    {
        return view('vehicles.edit', compact('vehicle'));
    }

    /**
     * Update the specified vehicle in storage.
     */
    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $vehicle->update($request->validated());

        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', "Data armada {$vehicle->license_plate} berhasil diperbarui.");
    }

    /**
     * Remove the specified vehicle from storage.
     */
    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        // Check if vehicle has active reservations
        $hasActive = $vehicle->reservations()
            ->whereIn('status', ['confirmed', 'active'])
            ->exists();

        if ($hasActive) {
            return back()->with('error', 'Tidak dapat menghapus armada yang sedang memiliki reservasi aktif atau terkonfirmasi.');
        }

        $name = $vehicle->full_name;
        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with('success', "Armada {$name} berhasil dihapus dari sistem.");
    }

    /**
     * Customer catalog view - filters out maintenance/out_of_service vehicles.
     */
    public function customerCatalog(Request $request): View
    {
        $query = Vehicle::query()
            ->whereNotIn('status', ['maintenance', 'out_of_service']);

        if ($request->filled('brand')) {
            $query->where('brand', $request->input('brand'));
        }

        if ($request->filled('transmission')) {
            $query->where('transmission', $request->input('transmission'));
        }

        if ($request->filled('capacity')) {
            $query->where('capacity', '>=', (int) $request->input('capacity'));
        }

        if ($request->filled('max_rate')) {
            $query->where('rental_rate_per_day', '<=', (float) $request->input('max_rate'));
        }

        $vehicles = $query->orderBy('rental_rate_per_day', 'asc')->paginate(9)->withQueryString();
        $brands = Vehicle::distinct()->pluck('brand');

        return view('catalog.index', compact('vehicles', 'brands'));
    }
}
