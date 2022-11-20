<?php

namespace Database\Seeders;

use DB;
use File;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the User seeds.
     * All accounts will have the password 'password'.
     * User accounts that you register yourself can
     * have any password you want.
     *
     * @return void
     */
    public function run()
    {
        // Clear the table
        DB::table('users')->delete();

        // Get the data from the relevant JSON file
        $json = File::get("database/data/users.json");
        $data = json_decode($json);

        // Add the data into the database
        foreach ($data as $row) {
            User::create(array(
                'id' => $row->id,
                'name' => $row->name,
                'email' => $row->email,
                'password' => $row->password,
                'remember_token' => $row->remember_token,
                'admin' => $row->admin
            ));
        }
    }
}
