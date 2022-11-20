<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserTableSeeder::class);
        $this->call(ManufacturerTableSeeder::class);
        $this->call(LaptopMakeTableSeeder::class);
        $this->call(LaptopTableSeeder::class);
        $this->call(UserFavoriteTableSeeder::class);
    }
}
