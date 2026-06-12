<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all room types
        $roomTypes = RoomType::all();

        foreach ($roomTypes as $roomType) {
            // Create 3-5 rooms for each room type
            $roomCount = random_int(3, 5);
            for ($i = 1; $i <= $roomCount; $i++) {
                $floor = random_int(1, 3);
                $roomNumber = $floor . '-' . sprintf('%02d', $i);

                Room::create([
                    'room_type_id' => $roomType->id,
                    'room_number' => $roomNumber,
                    'floor' => $floor,
                    'status' => random_int(0, 1) ? 'available' : 'occupied',
                ]);
            }
        }
    }
}
