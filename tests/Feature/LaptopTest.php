<?php

namespace Tests\Feature;

use App\Upload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use App\Models\Laptop;

class LaptopTest extends TestCase
{
    use RefreshDatabase;

    // Test that helper functions can create one laptop
    public function test_helper_add_one_laptop()
    {
        $numberOfLaptops = 1;

        // Create one laptop
        $this->create_and_save_laptops($numberOfLaptops);

        // Ensure that the database has one laptop
        $this->assertDatabaseCount('laptops', $numberOfLaptops);
    }

    // Test that helper functions can create five laptops
    public function test_helper_add_five_laptops()
    {
        $numberOfLaptops = 5;

        // Create five laptops
        $this->create_and_save_laptops($numberOfLaptops);

        // Ensure that the database has five laptops
        $this->assertDatabaseCount('laptops', $numberOfLaptops);
    }

    // Test that helper functions can create the specified laptop
    public function test_helper_create_specified_laptop()
    {
        $numberOfLaptops = 1;
        $laptopID = 3;

        // Create manufacturer and make needed for laptop '3'
        $manufacturer = $this->create_manufacturer($laptopID);
        $make = $this->create_laptop_make($laptopID);

        // Create one laptop, with the ID of '3'
        $laptop = $this->create_laptop($laptopID);

        // Ensure that the database has one laptop
        $this->assertDatabaseCount('laptops', $numberOfLaptops);

        // Get the details of laptop '3'
        $laptopDetails = $this->get_laptop_details($laptopID);

        // Ensure the laptop in the database has the expected details
        $this->assertDatabaseHas('laptops', $laptopDetails);
    }

    /**
     * Test that trying to add a laptop when logged in
     * adds the new valid laptop
     *
     * @return void
     */
    public function test_add_new_valid_laptop()
    {
        $numberOfLaptops = 1;
        $laptopID = 1;

        // Log in as a user
        $user = $this->create_user();

        // Create the needed manufacturer and make for this laptop.
        // Each laptop needs a manufacturer and make, so these must be made first.
        $this->create_and_save_manufactures($numberOfLaptops);
        $this->create_and_save_makes($numberOfLaptops);

        // Assert manufacturers and makes added correctly (same
        // number of these as laptops due to how helper functions work)
        $this->assertDatabaseCount('manufacturers', $numberOfLaptops);
        $this->assertDatabaseCount('laptop_makes', $numberOfLaptops);

        // Get a laptops details
        $laptopDetails = $this->get_laptop_details($laptopID);

        // Try to add a new valid laptop
        $response = $this->actingAs($user)
            ->followingRedirects()
            ->put('/addlaptop', $laptopDetails);

        // Assert the laptop has been added
        $response->assertSuccessful();
        $this->assertDatabaseCount('laptops', $numberOfLaptops);
        $this->assertDatabaseHas('laptops', $laptopDetails);
    }

    /**
     * Test that a logged out user cannot access the 'add laptop' page
     *
     * @return void
     */
    public function test_view_add_laptop_page_logged_out_redirects_to_login()
    {
        // Ensure that '/addlaptop' redirect to login page for logged out users
        $response = $this->followingRedirects()
            ->get('addlaptop')
            ->assertViewIs('auth.login');
    }

    /**
     * Test that a logged in user can access the 'add laptop' page
     *
     * @return void
     */
    public function test_view_add_laptop_page_logged_in_loads_add_page()
    {
        // Log in as a user
        $user = $this->create_user();

        // Ensure that '/addlaptop' loads for logged in users
        $response = $this->actingAs($user)
            ->followingRedirects()
            ->get('addlaptop')
            ->assertViewIs('laptops.add');
    }

    // Test that a laptop can be edited
    public function test_edit_laptop()
    {
        $numberOfLaptops = 1;
        $laptopID = 1;

        // Log in as a user
        $user = $this->create_user();

        // Get a laptop's details
        $laptopDetails = $this->get_laptop_details();

        // Create a new laptop
        $this->create_and_save_laptops($numberOfLaptops);
        $this->assertDatabaseCount('laptops', 1);
        $this->assertDatabaseHas('laptops', $laptopDetails);

        // Create the new laptop details
        $newLaptopDetails = $laptopDetails;

        // Update the model name and price of the laptop
        $newLaptopModel = 'Updated Model Name';
        $newLaptopPrice = 150.00;
        $newLaptopDetails["model"] = $newLaptopModel;
        $newLaptopDetails["price"] = $newLaptopPrice;

        // Update this laptop with the new details
        $response = $this->actingAs($user)
            ->put('laptop', $newLaptopDetails);

        // Assert there is one laptop with the new details
        $response->assertStatus(302);
        $this->assertDatabaseCount('laptops', 1);
        $this->assertDatabaseHas('laptops', $newLaptopDetails);
    }

    // Test that a laptop cannot be edited when logged out
    public function test_cannot_edit_laptop_when_logged_out()
    {
        $numberOfLaptops = 1;
        $laptopID = 1;

        // Get a laptop's details
        $originalLaptopDetails = $this->get_laptop_details();

        // Create a new laptop
        $this->create_and_save_laptops($numberOfLaptops);
        $this->assertDatabaseCount('laptops', 1);
        $this->assertDatabaseHas('laptops', $originalLaptopDetails);

        // Create the new laptop details
        $newLaptopDetails = $originalLaptopDetails;

        // Update the model name and price of the laptop
        $newLaptopModel = 'Updated Model Name';
        $newLaptopPrice = 150.00;
        $newLaptopDetails["model"] = $newLaptopModel;
        $newLaptopDetails["price"] = $newLaptopPrice;

        // Update this laptop with the new details
        $response = $this->put('laptop', $newLaptopDetails);

        // Assert that the laptop still has the original laptop details
        $response->assertStatus(302);
        $this->assertDatabaseCount('laptops', 1);
        $this->assertDatabaseHas('laptops', $originalLaptopDetails);
    }


    // Test that a laptop can be deleted
    public function test_delete_laptop()
    {
        $numberOfLaptops = 1;
        $laptopID = 1;

        // Log in as a user
        $user = $this->create_user();

        // Create a new laptop
        $this->create_and_save_laptops($numberOfLaptops);
        $this->assertDatabaseCount('laptops', 1);

        // Try to delete this laptop
        $response = $this->actingAs($user)
            ->delete('/laptop/delete/' . $laptopID);

        // Ensure that this laptop is deleted.
        $response->assertStatus(302);
        $this->assertDatabaseCount('laptops', 0);
    }

    // Test that a laptop cannot be deleted if you are not logged in
    public function test_cannot_delete_laptop_when_logged_out()
    {
        $numberOfLaptops = 1;
        $laptopID = 1;

        // Create a new laptop
        $this->create_and_save_laptops($numberOfLaptops);
        $this->assertDatabaseCount('laptops', 1);

        // Try to delete this laptop when logged out
        $response = $this->delete('/laptop/delete/' . $laptopID);

        // Ensure that this laptop has not been deleted.
        $response->assertStatus(302);
        $this->assertDatabaseCount('laptops', 1);
    }

    // Test to see if a disclaimer is shown when trying to view all laptops
    // if there are none in the system.
    public function test_view_laptops_page_with_no_laptops_in_the_system()
    {
        // Initialise string to test if disclaimer shown
        $pageTitle = 'View All Laptops';
        $visibleIfNoLaptop = 'There are no laptops in the system';

        // Visit the page
        $response = $this->followingRedirects()->get('/laptops')
            ->assertSeeInOrder([$pageTitle, $visibleIfNoLaptop]);
    }

    // Test to see if a disclaimer is shown when trying to manage laptops
    // if there are none in the system.
    public function test_manage_laptops_page_with_no_laptops_in_the_system()
    {
        // Log in as a user (needed for manage page but not for 'view all' page)
        $user = $this->create_user();

        // Initialise string to test if disclaimer shown
        $pageTitle = 'Manage Laptops';
        $visibleIfNoLaptop = 'There are no laptops in the system';

        // Visit the page
        $response = $this->actingAs($user)
            ->followingRedirects()->get('/managelaptops')
            ->assertSeeInOrder([$pageTitle, $visibleIfNoLaptop]);
    }

    // Test uploading laptop images

    private function getLaptopWithImageDetails($laptopID, $fileType='png')
    {
        $imageFilePath = 'public/images/test_only_laptop_images/file_type.' . $fileType;
        $imageName = 'laptop-' . $laptopID . '-image.' . $fileType;
        $image = new \Illuminate\Http\UploadedFile($imageFilePath, $imageName, $fileType, null, true);
        return ['id' => $laptopID, 'image' => $image];
    }

    private function uploadLaptopWithImageDetails($user, $laptopDetails) {
        return $this->actingAs($user)
            ->followingRedirects()
            ->put(route('laptop.update_image'), $laptopDetails);
    }

    private function uploadLaptopWithInvalidFileType($user, $laptopID, $fileType)
    {
        $laptopDetails = $this->getLaptopWithImageDetails($laptopID, $fileType);
        $response = $this->uploadLaptopWithImageDetails($user, $laptopDetails);

        $laptop = Laptop::find($laptopID);
        $this->assertNull($laptop->image);
    }

    public function test_user_can_upload_laptop_image()
    {
        $laptopID = 1;
        $user = $this->create_user();
        $this->create_and_save_laptops($laptopID);
        $this->assertDatabaseCount('laptops', $laptopID);

        $laptopDetails = $this->getLaptopWithImageDetails($laptopID);
        $response = $this->uploadLaptopWithImageDetails($user, $laptopDetails);
        $response->assertOk();

        $laptop = Laptop::find($laptopID);
        $this->assertNotNull($laptop->image);
    }

    public function test_user_cannot_upload_invalid_file_types_for_laptop_image()
    {
        $laptopID = 1;
        $user = $this->create_user();
        $this->create_and_save_laptops($laptopID);
        $this->assertDatabaseCount('laptops', $laptopID);

        $this->uploadLaptopWithInvalidFileType($user, $laptopID, 'jpg');
        $this->uploadLaptopWithInvalidFileType($user, $laptopID, 'txt');
        $this->uploadLaptopWithInvalidFileType($user, $laptopID, 'gif');
    }

    public function test_uploaded_laptop_image_applied_to_correct_laptop()
    {
        $laptopToUpdateID = 1;
        $laptopToNotUpdateID = 2;
        $numberOfLaptops = 2;
        $user = $this->create_user();
        $this->create_and_save_laptops($numberOfLaptops);

        $laptopDetails = $this->getLaptopWithImageDetails($laptopToUpdateID);
        $response = $this->uploadLaptopWithImageDetails($user, $laptopDetails);
        $response->assertOk();

        $laptopOne = Laptop::find($laptopToUpdateID);
        $laptopTwo = Laptop::find($laptopToNotUpdateID);
        $this->assertNotNull($laptopOne->image);
        $this->assertNull($laptopTwo->image);
    }

}
