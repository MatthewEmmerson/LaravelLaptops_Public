<?php

namespace Database\Seeders;

use DB;
use File;
use Illuminate\Database\Seeder;
use App\Models\Laptop;

class LaptopTableSeeder extends Seeder
{
    /**
     * Run the laptop seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear the table
        DB::table('laptops')->delete();

        // Get the data from the relevant JSON file
        $json = File::get("database/data/laptops.json");
        $data = json_decode($json);

        // Add the data into the database
        foreach ($data as $row) {
            // Make the data array
            $laptopDataArray = array(
                'id' => $row->id,
                'manufacturer_id' => $row->manufacturer_id,
                'make_id' => $row->make_id,
                "model" => $row->model,
                "price" => $row->price,
                "ram" => $row->ram,
                "ssd" => $row->ssd,
                "screen_size" => $row->screen_size,
                "default_os" => $row->default_os
            );

            // Add the image to seed if there is one
            if($row->image != 'no_seeded_image'){
                $path = 'public/images/seed_only_laptop_images/' . $row->image . '.jpg';
                $image = file_get_contents($path);
                $laptopDataArray += ["image" => $image];
            }

            // Create the new laptop
            Laptop::create($laptopDataArray);
        }
    }
}
