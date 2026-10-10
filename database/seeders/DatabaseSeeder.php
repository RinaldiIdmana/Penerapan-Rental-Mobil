<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Maintenance;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleReturn;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $reservationService = app(ReservationService::class);

        // 1. Users Seeding
        $admin = User::firstOrCreate(
            ['email' => 'admin@rental.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '081234567890',
                'driver_license_number' => 'SIM-A-999000111',
            ]
        );

        $staff = User::firstOrCreate(
            ['email' => 'staff@rental.com'],
            [
                'name' => 'Staff Operasional',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'phone' => '081298765432',
                'driver_license_number' => 'SIM-A-888000222',
            ]
        );

        $customer1 = User::firstOrCreate(
            ['email' => 'budi@gmail.com'],
            [
                'name' => 'Budi Pratama',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '085712345678',
                'driver_license_number' => 'SIM-A-777111222',
            ]
        );

        $customer2 = User::firstOrCreate(
            ['email' => 'siti@gmail.com'],
            [
                'name' => 'Siti Rahmawati',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '087812349999',
                'driver_license_number' => 'SIM-A-666333444',
            ]
        );

        // 2. Vehicles Seeding (Minimum 5 vehicles with variations in status)
        $v1 = Vehicle::create([
            'license_plate' => 'B 1234 ABC',
            'brand' => 'Toyota',
            'model' => 'Innova Zenix Q Hybrid',
            'year' => 2024,
            'transmission' => 'automatic',
            'rental_rate_per_day' => 650000.00,
            'status' => 'available',
            'image_url' => 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=800&q=80',
            'capacity' => 7,
        ]);

        $v2 = Vehicle::create([
            'license_plate' => 'B 2345 DEF',
            'brand' => 'Honda',
            'model' => 'HR-V SE',
            'year' => 2023,
            'transmission' => 'automatic',
            'rental_rate_per_day' => 500000.00,
            'status' => 'rented',
            'image_url' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=800&q=80',
            'capacity' => 5,
        ]);

        $v3 = Vehicle::create([
            'license_plate' => 'B 3456 GHI',
            'brand' => 'Mitsubishi',
            'model' => 'Pajero Sport Dakar 4x2',
            'year' => 2023,
            'transmission' => 'automatic',
            'rental_rate_per_day' => 850000.00,
            'status' => 'available',
            'image_url' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=800&q=80',
            'capacity' => 7,
        ]);

        $v4 = Vehicle::create([
            'license_plate' => 'B 4567 JKL',
            'brand' => 'Toyota',
            'model' => 'Avanza Veloz Q CVT',
            'year' => 2023,
            'transmission' => 'automatic',
            'rental_rate_per_day' => 400000.00,
            'status' => 'maintenance',
            'image_url' => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=800&q=80',
            'capacity' => 7,
        ]);

        $v5 = Vehicle::create([
            'license_plate' => 'B 5678 MNO',
            'brand' => 'Daihatsu',
            'model' => 'All New Xenia 1.3 R',
            'year' => 2022,
            'transmission' => 'manual',
            'rental_rate_per_day' => 350000.00,
            'status' => 'available',
            'image_url' => 'https://images.unsplash.com/photo-1502877338535-766e1452684a?auto=format&fit=crop&w=800&q=80',
            'capacity' => 7,
        ]);

        $v6 = Vehicle::create([
            'license_plate' => 'B 6789 PQR',
            'brand' => 'Suzuki',
            'model' => 'XL7 Alpha Hybrid',
            'year' => 2023,
            'transmission' => 'automatic',
            'rental_rate_per_day' => 450000.00,
            'status' => 'out_of_service',
            'image_url' => 'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?auto=format&fit=crop&w=800&q=80',
            'capacity' => 7,
        ]);

        // 3. Maintenance Seeding (With Due Alert scenarios)
        // Scenario 1: Avanza in-progress maintenance with due soon
        Maintenance::create([
            'vehicle_id' => $v4->id,
            'service_date' => Carbon::now()->subDays(2)->toDateString(),
            'service_type' => 'Perbaikan Sistem Rem & Kaki-kaki',
            'cost' => 1250000.00,
            'description' => 'Ganti kampas rem depan dan rotasi ban',
            'next_service_due_date' => Carbon::now()->addDays(2)->toDateString(), // Approaching due!
            'status' => 'in_progress',
        ]);

        // Scenario 2: Pajero Sport scheduled maintenance due in 5 days (Alert due soon)
        Maintenance::create([
            'vehicle_id' => $v3->id,
            'service_date' => Carbon::now()->subMonths(2)->toDateString(),
            'service_type' => 'Servis Berkala 20.000 KM',
            'cost' => 850000.00,
            'description' => 'Ganti oli mesin sintetis dan filter oli',
            'next_service_due_date' => Carbon::now()->addDays(4)->toDateString(), // Approaching due!
            'status' => 'scheduled',
        ]);

        // Scenario 3: Completed historical service
        Maintenance::create([
            'vehicle_id' => $v1->id,
            'service_date' => Carbon::now()->subMonth()->toDateString(),
            'service_type' => 'Pengecekan Baterai Hybrid & Tune Up',
            'cost' => 600000.00,
            'description' => 'Pemeriksaan rutin motor elektrik dan filter AC',
            'next_service_due_date' => Carbon::now()->addMonths(4)->toDateString(),
            'status' => 'completed',
        ]);

        // 4. Reservations & Invoices Seeding
        // Reservation 1: Active rental (Honda HR-V) by Budi Pratama
        $r1StartDate = Carbon::now()->subDays(1)->toDateString();
        $r1EndDate = Carbon::now()->addDays(2)->toDateString();
        $r1Days = 3;
        $r1BaseAmount = $r1Days * (float) $v2->rental_rate_per_day;

        $res1 = Reservation::create([
            'reservation_code' => $reservationService->generateReservationCode(),
            'user_id' => $customer1->id,
            'vehicle_id' => $v2->id,
            'start_date' => $r1StartDate,
            'end_date' => $r1EndDate,
            'total_days' => $r1Days,
            'base_amount' => $r1BaseAmount,
            'status' => 'active',
            'notes' => 'Perjalanan dinas ke Bandung selama 3 hari.',
        ]);

        Invoice::create([
            'invoice_number' => $reservationService->generateInvoiceNumber(),
            'reservation_id' => $res1->id,
            'return_id' => null,
            'subtotal' => $r1BaseAmount,
            'fine_amount' => 0,
            'grand_total' => $r1BaseAmount,
            'payment_status' => 'paid',
            'paid_at' => Carbon::now()->subDays(1),
        ]);

        // Reservation 2: Completed with return and fine (Innova Zenix) by Siti Rahmawati
        $r2StartDate = Carbon::now()->subDays(6)->toDateString();
        $r2EndDate = Carbon::now()->subDays(3)->toDateString();
        $r2ActualReturn = Carbon::now()->subDays(2)->toDateString(); // Late by 1 day!
        $r2Days = 3;
        $r2BaseAmount = $r2Days * (float) $v1->rental_rate_per_day;
        $r2FinePerDay = 325000.00; // 50%
        $r2TotalFine = 1 * $r2FinePerDay;
        $r2FinalAmount = $r2BaseAmount + $r2TotalFine;

        $res2 = Reservation::create([
            'reservation_code' => $reservationService->generateReservationCode(),
            'user_id' => $customer2->id,
            'vehicle_id' => $v1->id,
            'start_date' => $r2StartDate,
            'end_date' => $r2EndDate,
            'total_days' => $r2Days,
            'base_amount' => $r2BaseAmount,
            'status' => 'completed',
            'notes' => 'Acara keluarga di Bogor.',
        ]);

        $return2 = VehicleReturn::create([
            'reservation_id' => $res2->id,
            'return_date' => $r2ActualReturn,
            'late_days' => 1,
            'fine_per_day' => $r2FinePerDay,
            'total_fine' => $r2TotalFine,
            'condition_notes' => 'Armada dikembalikan dalam kondisi prima dan bersih, terlambat 1 hari karena macet jalur puncak.',
            'final_total_amount' => $r2FinalAmount,
        ]);

        Invoice::create([
            'invoice_number' => $reservationService->generateInvoiceNumber(),
            'reservation_id' => $res2->id,
            'return_id' => $return2->id,
            'subtotal' => $r2BaseAmount,
            'fine_amount' => $r2TotalFine,
            'grand_total' => $r2FinalAmount,
            'payment_status' => 'paid',
            'paid_at' => Carbon::now()->subDays(2),
        ]);

        // Reservation 3: Upcoming confirmed booking for Daihatsu Xenia
        $r3StartDate = Carbon::now()->addDays(2)->toDateString();
        $r3EndDate = Carbon::now()->addDays(5)->toDateString();
        $r3Days = 3;
        $r3BaseAmount = $r3Days * (float) $v5->rental_rate_per_day;

        $res3 = Reservation::create([
            'reservation_code' => $reservationService->generateReservationCode(),
            'user_id' => $customer2->id,
            'vehicle_id' => $v5->id,
            'start_date' => $r3StartDate,
            'end_date' => $r3EndDate,
            'total_days' => $r3Days,
            'base_amount' => $r3BaseAmount,
            'status' => 'confirmed',
            'notes' => 'Liburan ke Yogyakarta.',
        ]);

        Invoice::create([
            'invoice_number' => $reservationService->generateInvoiceNumber(),
            'reservation_id' => $res3->id,
            'return_id' => null,
            'subtotal' => $r3BaseAmount,
            'fine_amount' => 0,
            'grand_total' => $r3BaseAmount,
            'payment_status' => 'unpaid',
        ]);
    }
}
