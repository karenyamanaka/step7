<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(ProductsTableSeeder::class);
        $this->call(CompaniesTableSeeder::class);
        $this->call(SalesTableSeeder::class);
        // 他のシーダーも呼び出す場合は、ここに追加します。
    }
}

