<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company; //追加

class CompaniesTableSeeder extends Seeder
{

    public function run(): void
    {
        Company::factory()->count(10)->create();
    }
}


