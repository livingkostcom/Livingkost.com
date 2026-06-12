<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\RoomType;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample properties
        $properties = [
            [
                'name' => 'Kos Elite Pusat',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'description' => 'Kos modern dengan fasilitas lengkap di pusat kota',
            ],
            [
                'name' => 'Kos Budget Bandung',
                'address' => 'Jl. Dago No. 45, Bandung',
                'description' => 'Kos terjangkau untuk mahasiswa dan karyawan muda',
            ],
            [
                'name' => 'Kos Premium Surabaya',
                'address' => 'Jl. Basuki Rahmat No. 78, Surabaya',
                'description' => 'Kos premium dengan layanan lengkap',
            ],
        ];

        foreach ($properties as $propertyData) {
            $property = Property::create($propertyData);

            // Create room types for each property
            $roomTypes = [
                [
                    'name' => 'Single Room',
                    'price' => 1500000,
                    'facilities' => ['Kasur', 'Lemari', 'Meja', 'Kamar mandi pribadi'],
                ],
                [
                    'name' => 'Double Room',
                    'price' => 2000000,
                    'facilities' => ['2 Kasur', 'Lemari', 'Meja', 'Kamar mandi pribadi', 'AC'],
                ],
                [
                    'name' => 'Suite Room',
                    'price' => 3000000,
                    'facilities' => ['2 Kasur', 'Ruang tamu', 'Lemari', 'AC', 'Kamar mandi pribadi', 'Dapur kecil'],
                ],
            ];

            foreach ($roomTypes as $roomTypeData) {
                $roomTypeData['property_id'] = $property->id;
                RoomType::create($roomTypeData);
            }
        }
    }
}
