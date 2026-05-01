<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\State as StateModel;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        // Indian country seed
        $india = Country::firstOrCreate(
            ['name' => 'India'],
            [
                'iso_code' => 'IN',
                'status' => 1,
            ]
        );

        // Indian states and union territories seed
        // Only seed if there are no states for India yet
        if (StateModel::where('country_id', $india->id)->count() > 0) {
            return;
        }

        $states = [
            // 28 states
            ['name' => 'Andhra Pradesh', 'code' => 'AP'],
            ['name' => 'Arunachal Pradesh', 'code' => 'AR'],
            ['name' => 'Assam', 'code' => 'AS'],
            ['name' => 'Bihar', 'code' => 'BR'],
            ['name' => 'Chhattisgarh', 'code' => 'CG'],
            ['name' => 'Goa', 'code' => 'GA'],
            ['name' => 'Gujarat', 'code' => 'GJ'],
            ['name' => 'Haryana', 'code' => 'HR'],
            ['name' => 'Himachal Pradesh', 'code' => 'HP'],
            ['name' => 'Jharkhand', 'code' => 'JH'],
            ['name' => 'Karnataka', 'code' => 'KA'],
            ['name' => 'Kerala', 'code' => 'KL'],
            ['name' => 'Madhya Pradesh', 'code' => 'MP'],
            ['name' => 'Maharashtra', 'code' => 'MH'],
            ['name' => 'Manipur', 'code' => 'MN'],
            ['name' => 'Meghalaya', 'code' => 'ML'],
            ['name' => 'Mizoram', 'code' => 'MZ'],
            ['name' => 'Nagaland', 'code' => 'NL'],
            ['name' => 'Odisha', 'code' => 'OD'],
            ['name' => 'Punjab', 'code' => 'PB'],
            ['name' => 'Rajasthan', 'code' => 'RJ'],
            ['name' => 'Sikkim', 'code' => 'SK'],
            ['name' => 'Tamil Nadu', 'code' => 'TN'],
            ['name' => 'Telangana', 'code' => 'TS'],
            ['name' => 'Tripura', 'code' => 'TR'],
            ['name' => 'Uttar Pradesh', 'code' => 'UP'],
            ['name' => 'Uttarakhand', 'code' => 'UK'],
            ['name' => 'West Bengal', 'code' => 'WB'],
            // 8 Union Territories
            ['name' => 'Andaman and Nicobar Islands', 'code' => 'AN'],
            ['name' => 'Chandigarh', 'code' => 'CH'],
            ['name' => 'Dadra and Nagar Haveli and Daman and Diu', 'code' => 'DN'],
            ['name' => 'Lakshadweep', 'code' => 'LD'],
            ['name' => 'Delhi', 'code' => 'DL'],
            ['name' => 'Puducherry', 'code' => 'PY'],
            ['name' => 'Ladakh', 'code' => 'LA'],
            ['name' => 'Jammu and Kashmir', 'code' => 'JK'],
        ];

        foreach ($states as $s) {
            StateModel::create([
                'country_id' => $india->id,
                'name' => $s['name'],
                'code' => $s['code'],
                'status' => 1,
            ]);
        }
    }
}
