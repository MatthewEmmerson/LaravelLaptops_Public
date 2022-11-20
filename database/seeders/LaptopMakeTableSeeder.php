<?php

namespace Database\Seeders;

use DB;
use File;
use Illuminate\Database\Seeder;
use App\Models\LaptopMake;

class LaptopMakeTableSeeder extends Seeder
{
    /**
     * Run the laptop make seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear the table
        DB::table('laptop_makes')->delete();
        
        // Get the data from the relevant JSON file
        $json = File::get("database/data/laptop_make.json");
        $data = json_decode($json);

        // Add the data into the database
        foreach ($data as $row) {
            LaptopMake::create(array(
                'id' => $row->id,
                'name' => $row->name,
            ));
        }
    }
}
