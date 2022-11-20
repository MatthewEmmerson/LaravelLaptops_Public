<?php

namespace Tests;

use App\Models\User;
use App\Models\UserFavorite;
use App\Models\Manufacturer;
use App\Models\Laptop;
use App\Models\LaptopMake;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Contains functions that can be used by every other test file
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    // Get the details of a valid user
    public function get_user_details($userNumber=1) {
        // Initalise the name and email
        $userName = 'test_user_' . $userNumber;
        $userEmail = $userName . '@email.com';

        // Password is 'password'
        // 'const' keyword to define constants does not work in this version of PHP
        // so capitals are still used as these are supposed to be constants
        $USER_PASSWORD = '$2y$10$/t9ac/nMHJnOWox3dHIGxO19pE2v5tZEyLQ1WnxPwe4jGZzeLh1Z.';

       return array(
            'id' => $userNumber,
            'name' => $userName,
            'email' => $userEmail,
            'password' => $USER_PASSWORD,
            'remember_token' => 'NULL',
            'admin' => '0'
       );
    }

    // Get the details of a valid manufacturer
    public function get_manufacturer_details($manufacturerNumber=1)
    {
        // Initalise the name
        $manufacturerName = 'test_manufacturer_' . $manufacturerNumber;

        return array(
            'id' => $manufacturerNumber,
            'name' => $manufacturerName
        );
    }

    // Get the details of a valid make
    public function get_make_details($makeNumber=1)
    {
        // Initialiase the name
        $makeName = 'test_make_' . $makeNumber;

        return array(
            'id' => $makeNumber,
            'name' => $makeName
        );
    }

    // Get the details of a valid laptop
    public function get_laptop_details($laptopNumber=1)
    {
        // Initalise the name
        $laptopName = 'test_laptop_' . $laptopNumber;

        // Initialise default test laptop details
        $LAPTOP_PRICE = 700.00;
        $LAPTOP_RAM = 16;
        $LAPTOP_SSD = 256;
        $LAPTOP_SCREEN_SIZE = 14;
        $LAPTOP_DEFAULT_OS = "Windows";

        // Create the laptop
        // The manufacturer and make will be the same ID as the laptop
        // itself to ensure all laptops are unique.
        return array(
            'id' => $laptopNumber,
            'manufacturer_id' => $laptopNumber,
            'make_id' => $laptopNumber,
            'model' => $laptopName,
            'price' => $LAPTOP_PRICE,
            'ram' => $LAPTOP_RAM,
            'ssd' => $LAPTOP_SSD,
            'screen_size' => $LAPTOP_SCREEN_SIZE,
            'default_os' => $LAPTOP_DEFAULT_OS,
        );
    }

    // Get the details of a valid favorite
    public function get_favorite_details($favoriteNumber=1)
    {
        // Create the user favorite.
        // The user ID and laptop ID will be the same ID as the laptop
        // itself to ensure all favorites are unique.
        return array(
            'id' => $favoriteNumber,
            'user_id' => $favoriteNumber,
            'laptop_id' => $favoriteNumber,
        );
    }

    // Create a valid user
    public function create_user($userNumber=1)
    {
        // Get the user's details
        $userDetails = $this->get_user_details($userNumber);

        // Create the user
        return User::create($userDetails);
    }

    // Create an admin user
    public function create_admin_user($userNumber=1)
    {
        // Get the user's details and make them an admin
        $userDetails = $this->get_user_details($userNumber);
        $userDetails['admin'] = 1;

        // Create the user
        return User::create($userDetails);
    }

    // Create a valid manufacturer
    public function create_manufacturer($manufacturerNumber=1)
    {
        // Get the manufacturer's details
        $manufacturerDetails = $this->get_manufacturer_details($manufacturerNumber);

        // Create the manufacturer
        return Manufacturer::create($manufacturerDetails);
    }

    // Create a valid laptop make
    public function create_laptop_make($laptopMakeNumber=1)
    {
        // Get the make details
        $makeDetails = $this->get_make_details($laptopMakeNumber);

        // Create the make
        return LaptopMake::create($makeDetails);
    }

    // Create a valid laptop
    public function create_laptop($laptopNumber=1)
    {
        // Get the laptop's details
        $laptopDetails = $this->get_laptop_details($laptopNumber);

        // Create the laptop
        return Laptop::create($laptopDetails);
    }

    // Create a valid laptop make
    public function create_user_favorite($favoriteNumber=1)
    {
        // Get the favorite details
        $favoriteDetails = $this->get_favorite_details($favoriteNumber);

        // Create the favorite
        return UserFavorite::create($favoriteDetails);
    }

    // Create the specified number of users
    public function create_and_save_users($numberOfUsers)
    {
        // Create and save the users
        for ($i=1; $i <= $numberOfUsers; $i++) {
            $this->create_user($i);
        }
    }

    // Create the specified number of manufacturers
    public function create_and_save_manufactures($numberOfManufacturers)
    {
        // Create and save the manufacturers
        for ($i=1; $i <= $numberOfManufacturers; $i++) {
            $this->create_manufacturer($i);
        }
    }

    // Create the specified number of laptop makes
    public function create_and_save_makes($numberOfMakes)
    {
        // Create and save the laptop makes
        for ($i=1; $i <= $numberOfMakes; $i++) {
            $this->create_laptop_make($i);
        }
    }

    // Create the specified number of laptop
    public function create_and_save_laptops($numberOfLaptops)
    {
        // If you want to create a laptop, you need to create manufacturers and
        // makes beforehand so a laptop can be linked to, for example, a Lenovo.
        $this->create_and_save_manufactures($numberOfLaptops);
        $this->create_and_save_makes($numberOfLaptops);

        // Create and save the laptops
        for ($i=1; $i <= $numberOfLaptops; $i++) {
            $this->create_laptop($i);
        }
    }

    // Create the specified number of favorites
    public function create_and_save_favorites($numberOfFavorites)
    {
        // Create and save the laptop makes
        for ($i=1; $i <= $numberOfFavorites; $i++) {
            $this->create_user_favorite($i);
        }
    }

    /* Seed the database with some testing data
       The 'Number of default records' parameter is how many records are
       created for each table. For example, if 5 is passed in, there will be
       five users, laptops, manufacturers and makes. However, each user will only
       have one favorite laptop- and this will be the laptop with the same ID as
       the user- so user 1 will have laptop 1 as a favorite. Laptop 1 will be
       linked to manufacturer 1 and make 1. This is to ensure that the laptops
       and favorites are all unique
    */
    public function seed_default_test_database($numberOfDefaultRecords) {
        // Create and save all data
        $this->create_and_save_users($numberOfDefaultRecords);
        $this->create_and_save_laptops($numberOfDefaultRecords);
        $this->create_and_save_favorites($numberOfDefaultRecords);

        // Test database seeded correctly
        $this->assertDatabaseCount('users', $numberOfDefaultRecords);
        $this->assertDatabaseCount('laptops', $numberOfDefaultRecords);
        $this->assertDatabaseCount('laptop_makes', $numberOfDefaultRecords);
        $this->assertDatabaseCount('manufacturers', $numberOfDefaultRecords);
        $this->assertDatabaseCount('user_favorites', $numberOfDefaultRecords);
    }
}
