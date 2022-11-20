<?php

namespace Database\Seeders;

use DB;
use File;
use Illuminate\Database\Seeder;
use App\Models\Manufacturer;

class ManufacturerTableSeeder extends Seeder
{
    /**
     * Run the manufacturer seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear the table
        DB::table('manufacturers')->delete();
        
        // Get the data from the relevant JSON file
        $json = File::get("database/data/manufacturers.json");
        $data = json_decode($json);

        // Add the data into the database
        foreach ($data as $row) {
            Manufacturer::create(array(
                'id' => $row->id,
                'name' => $row->name,
            ));
        }
    }
}
