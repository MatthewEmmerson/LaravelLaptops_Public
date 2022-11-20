<?php

namespace Database\Seeders;

use DB;
use File;
use Illuminate\Database\Seeder;
use App\Models\UserFavorite;

class UserFavoriteTableSeeder extends Seeder
{
    /**
     * Run the user favorite laptops seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear the table
        DB::table('user_favorites')->delete();
        
        // Get the data from the relevant JSON file
        $json = File::get("database/data/favorites.json");
        $data = json_decode($json);

        // Add the data into the database
        foreach ($data as $row) {
            UserFavorite::create(array(
                'id' => $row->id,
                'user_id' => $row->user_id,
                'laptop_id' => $row->laptop_id
            ));
        }
    }
}
