<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::role('owner')->first();
        $properties = Property::all();

        if (!$owner || $properties->isEmpty()) {
            return;
        }

        $expenses = [
            // Bulan ini
            ['title' => 'Bayar listrik bulanan', 'category' => 'utility', 'amount' => 2850000, 'days_ago' => 2],
            ['title' => 'Bayar air PDAM', 'category' => 'utility', 'amount' => 1200000, 'days_ago' => 3],
            ['title' => 'Gaji cleaning service', 'category' => 'salary', 'amount' => 1500000, 'days_ago' => 5],
            ['title' => 'Beli lampu LED kamar 3 & 7', 'category' => 'supplies', 'amount' => 180000, 'days_ago' => 7],
            ['title' => 'Perbaikan keran air kamar 5', 'category' => 'maintenance', 'amount' => 250000, 'days_ago' => 10],
            ['title' => 'Beli sabun & pembersih lantai', 'category' => 'cleaning', 'amount' => 95000, 'days_ago' => 12],
            ['title' => 'Service AC kamar 2 & 4', 'category' => 'maintenance', 'amount' => 400000, 'days_ago' => 14],

            // Bulan lalu
            ['title' => 'Bayar listrik bulanan', 'category' => 'utility', 'amount' => 2720000, 'days_ago' => 32],
            ['title' => 'Bayar air PDAM', 'category' => 'utility', 'amount' => 1150000, 'days_ago' => 33],
            ['title' => 'Gaji cleaning service', 'category' => 'salary', 'amount' => 1500000, 'days_ago' => 35],
            ['title' => 'Beli galon air minum', 'category' => 'supplies', 'amount' => 120000, 'days_ago' => 38],
            ['title' => 'Perbaikan pintu kamar 8', 'category' => 'maintenance', 'amount' => 350000, 'days_ago' => 40],
            ['title' => 'Beli sapu & pel', 'category' => 'cleaning', 'amount' => 75000, 'days_ago' => 42],
            ['title' => 'Pajak bumi dan bangunan', 'category' => 'tax', 'amount' => 1800000, 'days_ago' => 45],

            // 2 bulan lalu
            ['title' => 'Bayar listrik bulanan', 'category' => 'utility', 'amount' => 2650000, 'days_ago' => 62],
            ['title' => 'Bayar air PDAM', 'category' => 'utility', 'amount' => 1100000, 'days_ago' => 63],
            ['title' => 'Gaji cleaning service', 'category' => 'salary', 'amount' => 1500000, 'days_ago' => 65],
            ['title' => 'Cat ulang kamar 1 & 6', 'category' => 'maintenance', 'amount' => 750000, 'days_ago' => 68],
            ['title' => 'Beli kasur baru kamar 9', 'category' => 'supplies', 'amount' => 1200000, 'days_ago' => 70],
            ['title' => 'Asuransi properti tahunan', 'category' => 'insurance', 'amount' => 3500000, 'days_ago' => 72],
            ['title' => 'Jasa sedot WC', 'category' => 'cleaning', 'amount' => 350000, 'days_ago' => 75],

            // 3 bulan lalu
            ['title' => 'Bayar listrik bulanan', 'category' => 'utility', 'amount' => 2500000, 'days_ago' => 92],
            ['title' => 'Bayar air PDAM', 'category' => 'utility', 'amount' => 1050000, 'days_ago' => 93],
            ['title' => 'Gaji cleaning service', 'category' => 'salary', 'amount' => 1500000, 'days_ago' => 95],
            ['title' => 'Ganti shower kamar 10', 'category' => 'maintenance', 'amount' => 200000, 'days_ago' => 98],
            ['title' => 'Beli WiFi router baru', 'category' => 'supplies', 'amount' => 450000, 'days_ago' => 100],
        ];

        foreach ($expenses as $data) {
            Expense::create([
                'property_id' => $properties->random()->id,
                'title' => $data['title'],
                'description' => null,
                'amount' => $data['amount'],
                'expense_date' => now()->subDays($data['days_ago'])->toDateString(),
                'category' => $data['category'],
                'created_by' => $owner->id,
            ]);
        }
    }
}
