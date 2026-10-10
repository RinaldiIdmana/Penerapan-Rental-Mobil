<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Maintenance;
use App\Models\Reservation;
use App\Models\Vehicle;
use App\Models\VehicleReturn;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalVehicles = Vehicle::count();
        $availableVehicles = Vehicle::where('status', 'available')->count();
        $rentedVehicles = Vehicle::where('status', 'rented')->count();
        $maintenanceVehicles = Vehicle::where('status', 'maintenance')->count();

        $activeReservations = Reservation::where('status', 'active')->count();
        $pendingReservations = Reservation::where('status', 'pending')->count();
        $confirmedReservations = Reservation::where('status', 'confirmed')->count();

        $totalRevenue = Invoice::where('payment_status', 'paid')->sum('grand_total');
        $unpaidInvoicesCount = Invoice::where('payment_status', 'unpaid')->count();
        $unpaidInvoicesSum = Invoice::where('payment_status', 'unpaid')->sum('grand_total');

        // Modul 4: Monitoring Servis Berkala - Alerts for due soon or overdue
        $maintenanceAlerts = Maintenance::with('vehicle')
            ->dueSoonOrOverdue(7)
            ->orderBy('next_service_due_date', 'asc')
            ->get();

        $overdueCount = $maintenanceAlerts->filter(fn($m) => $m->is_overdue)->count();

        // Recent reservations
        $recentReservations = Reservation::with(['vehicle', 'user'])
            ->latest()
            ->take(6)
            ->get();

        // Recent returns
        $recentReturns = VehicleReturn::with(['reservation.vehicle', 'reservation.user'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalVehicles',
            'availableVehicles',
            'rentedVehicles',
            'maintenanceVehicles',
            'activeReservations',
            'pendingReservations',
            'confirmedReservations',
            'totalRevenue',
            'unpaidInvoicesCount',
            'unpaidInvoicesSum',
            'maintenanceAlerts',
            'overdueCount',
            'recentReservations',
            'recentReturns'
        ));
    }
}
