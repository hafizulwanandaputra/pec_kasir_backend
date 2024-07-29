<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OutpatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('outpatients')->insert([
            'no_registration' => 'RJBPJS' . date('jmy') . '-' . rand(100, 999),
            'code_of_poli' => rand(10, 100),
            'code_of_doctor' => rand(10, 100),
            'no_mr' => rand(10, 100),
            'code_of_assurance' => rand(10, 100),
            'poli_name' => 'Contoh 1',
            'doctor_name' => 'Contoh 1',
            'patient_name' => 'Contoh 1',
            'assurance' => 'Contoh 1',
            'date' => date('Y-m-d'),
            'created_at' => date('Y-m-d h:i:s'),
            'updated_at' => date('Y-m-d h:i:s')
        ]);
    }
}
