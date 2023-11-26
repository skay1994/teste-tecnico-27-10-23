<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\PatientAddress;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Patient::factory()->has(PatientAddress::factory(), 'addresses')->count(100)->create();
    }
}
