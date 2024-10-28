<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sale; //追加

class SalesTableSeeder extends Seeder
{

    public function run(): void
    {
        // ダミーレコードを10件作成
        Sale::factory()->count(10)->create();
    }
}

