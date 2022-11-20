<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    // Ensure page redirects as expected depending on permission level

    public function test_redirect_to_login_page_logged_out()
    {
        $response = $this->followingRedirects()
            ->get('/admin/favorites/all');

        $response->assertViewIs('auth.login');
    }

    public function test_redirect_to_laptop_page_not_admin()
    {
        $pageHeader = 'View All Laptops';
        $errorMessage = 'You are not an admin user';

        $user = $this->create_user();
        $response = $this->actingAs($user)
            ->followingRedirects()
            ->get('/admin/favorites/all')
            ->assertSeeInOrder([$pageHeader, $errorMessage]);

        $response->assertViewIs('laptops.index');
    }

    public function test_access_admin_page_logged_in_as_admin()
    {
        $adminUser = $this->create_admin_user();
        $response = $this->actingAs($adminUser)
            ->get('/admin/favorites/all');

        $response->assertViewIs('laptops.admin');
    }

    // Test can view all user favorites in the system

    public function test_admin_page_view_all_favorites()
    {
        // Seed database
        $numberOfLaptops = 5;
        $this->seed_default_test_database($numberOfLaptops);

        // Create admin user
        $adminUser = $this->create_admin_user(6);

        // Test can see all favorites
        $allFavorites = [
            'test_laptop_1',
            'test_laptop_2',
            'test_laptop_3',
            'test_laptop_4',
            'test_laptop_5',
        ];

        $response = $this->actingAs($adminUser)
            ->followingRedirects()
            ->get('/admin/favorites/all')
            ->assertSeeInOrder($allFavorites);
    }

    public function test_admin_page_view_specific_user_favorites()
    {
        // Seed database
        $numberOfLaptops = 5;
        $this->seed_default_test_database($numberOfLaptops);

        // Create admin user
        $adminUser = $this->create_admin_user(6);

        // Test can see one user's favorites
        $userFourFavorites = [
            'test_laptop_4',
            'Favorited by:',
            'test_user_4',
        ];

        $response = $this->actingAs($adminUser)
            ->followingRedirects()
            ->get('/admin/favorites/user?user_id=4')
            ->assertSeeInOrder($userFourFavorites);

        // Have the admin user favorite this laptop as well
        $laptopID = 4;
        $this->actingAs($adminUser)
            ->followingRedirects()
            ->get('laptop/togglefavorite/' . $laptopID);

        // Add the admin user to the list of people who should have favorited this laptop
        $userFourFavorites[] = 'test_user_6';

        // Test if this laptop has been favorited by the 'admin user 6' as well
        $response = $this->actingAs($adminUser)
            ->followingRedirects()
            ->get('admin/favorites/user?user_id=4')
            ->assertSeeInOrder($userFourFavorites);
    }

    public function test_admin_page_view_specific_laptop_favorited_by()
    {
        // Seed database
        $numberOfLaptops = 5;
        $this->seed_default_test_database($numberOfLaptops);

        // Create admin user
        $adminUser = $this->create_admin_user(6);

        // Test can see one user's favorites
        $laptopThreeFavoritedBy = [
            'test_laptop_3',
            'Favorited by:',
            'test_user_3',
        ];

        // Test can see default favorite
        $response = $this->actingAs($adminUser)
            ->followingRedirects()
            ->get('admin/favorites/laptop?laptop_id=3')
            ->assertSeeInOrder($laptopThreeFavoritedBy);


        // Have the admin user favorite this laptop as well
        $laptopID = 3;
        $this->actingAs($adminUser)
            ->followingRedirects()
            ->get('laptop/togglefavorite/' . $laptopID);

        // Add the admin user to the list of people who should have favorited this laptop
        $laptopThreeFavoritedBy[] = 'test_user_6';

        // Test if this laptop has been favorited by the 'admin user 6' as well
        $response = $this->actingAs($adminUser)
            ->followingRedirects()
            ->get('admin/favorites/laptop?laptop_id=3')
            ->assertSeeInOrder($laptopThreeFavoritedBy);
    }

    // Test the 'Toggle Admin Status' Functionality

    public function test_admin_can_toggle_regular_user_to_admin() {
        $adminOneID = 1;
        $userTwoID = 2;
        $desiredAdminState = 1;
        $adminOne = $this->create_admin_user($adminOneID);
        $adminTwo = $this->create_user($userTwoID);

        $response = $this->actingAs($adminOne)
            ->put('admin/toggleuseradmin', ['user_id' => $userTwoID]);

        $userTwoDetails = ['id' => $userTwoID, 'admin' => $desiredAdminState];
        $this->assertDatabaseHas('users', $userTwoDetails);
    }

    public function test_admin_can_toggle_admin_user_to_regular() {
        $adminOneID = 1;
        $adminTwoID = 2;
        $desiredAdminState = 0;
        $adminOne = $this->create_admin_user($adminOneID);
        $adminTwo = $this->create_admin_user($adminTwoID);

        $response = $this->actingAs($adminOne)
            ->put('admin/toggleuseradmin', ['user_id' => $adminTwoID]);

        $userTwoDetails = ['id' => $adminTwoID, 'admin' => $desiredAdminState];
        $this->assertDatabaseHas('users', $userTwoDetails);
    }

    public function test_admin_cannot_toggle_themselves() {
        $adminUser = $this->create_admin_user();

        $errorMessage = ['Error', 'Cannot toggle your own admin status'];

        $response = $this->actingAs($adminUser)
            ->followingRedirects()
            ->post('admin/toggleuseradmin', ['user_id' => $adminUser->id])
            ->assertSeeInOrder($errorMessage);
    }

    public function test_admin_cannot_toggle_nonexistent_user() {
        $adminUser = $this->create_admin_user();
        $nonExistentUserID = 2;

        $errorMessage = ['Error', 'Could not find this user'];

        $response = $this->actingAs($adminUser)
            ->followingRedirects()
            ->post('admin/toggleuseradmin', ['user_id' => $nonExistentUserID])
            ->assertSeeInOrder($errorMessage);
    }
}
