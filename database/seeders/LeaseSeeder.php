<?php

namespace Database\Seeders;

use App\Models\Lease;
use App\Models\Tenant;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class LeaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manager = User::where('email', 'manager@fluty.com')->first();
        $tenants = Tenant::all();
        $occupiedRooms = Room::where('status', 'occupied')->get();

        foreach ($tenants->take(min($tenants->count(), $occupiedRooms->count())) as $index => $tenant) {
            $room = $occupiedRooms[$index];
            $startDate = Carbon::now()->subMonths(random_int(1, 6));
            $endDate = $startDate->clone()->addMonths(12);

            Lease::create([
                'tenant_id' => $tenant->id,
                'room_id' => $room->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'due_date_per_month' => 1, // Invoice due on the 1st of each month
                'deposit_amount' => $room->roomType->price,
                'status' => 'active',
                'created_by' => $manager?->id ?? 1,
            ]);
        }
    }
}
