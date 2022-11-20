<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\UserFavorite;

class UserTest extends TestCase
{
    use RefreshDatabase;

    // Test if a user can edit their own profile's details
    public function test_user_can_edit_own_details()
    {
        $userID = 1;
        $numberOfUsers = 1;
        $userDetails = $this->get_user_details();

        // Create a default user
        $user = $this->create_user();
        $this->assertDatabaseCount('users', $numberOfUsers);
        $this->assertDatabaseHas('users', $userDetails);

        // Get new details to edit the user to have (update details from
        // 'test_user_1' to 'test_user_2')
        $newUserDetails = $this->get_user_details(2);

        // The above function gets a brand new user's set of details, including
        // a new ID and password. These would not (necessarily) be changed by
        // an update query, so these values are being set to their original values.
        $newUserDetails['id'] = $userDetails['id'];
        unset($newUserDetails["password"]);

        // Update this user's details
        $response = $this->actingAs($user)
            ->followingRedirects()
            ->put(route('profile.update'), $newUserDetails);

        // Assert that this users details have been updated
        $response->assertOk();
        $this->assertDatabaseCount('users', $numberOfUsers);
        $this->assertDatabaseHas('users', $newUserDetails);
    }

    /**
     * Test going to the '/' route redirects to the '/laptops' page
     * when there is no user logged in.
     *
     * @return void
     */
    public function test_redirect_to_laptops_page_logged_out()
    {

        // Ensure that '/' redirects to '/laptops;
        $response = $this->get('/');
        $response->assertRedirect('/laptops');
    }

    /**
     * Test going to the '/' route redirects to the '/laptops' page
     * when there is a user logged in.
     *
     * @return void
     */
    public function test_redirect_to_laptops_page_logged_in()
    {
        // Log in as a user
        $user = $this->create_user();

        // Initialise string to test if logged in
        $visibleToAllUsers = 'View All Laptops';
        $visibleToLoggedInUsers = 'Your Favorited Laptops';

        // Ensure that '/' redirects to '/laptops;
        $response = $this->actingAs($user)
            ->followingRedirects()
            ->get('/')

            // If the page contains 'Your Favorited Laptops'
            // the user must be logged in.
            ->assertSeeInOrder([$visibleToAllUsers, $visibleToLoggedInUsers]);
    }

    /**
     * Tests if a logged out user is redirected to the login page if they try to view their profile
     */
    public function test_view_profile_page_redirects_to_login_if_logged_out()
    {
        // Ensure that '/profile' redirect to login page for logged out users
        $response = $this->followingRedirects()
            ->get('profile')
            ->assertViewIs('auth.login');
    }

    /**
     * Tests if a logged in user can see their profile page
     */
    public function test_view_profile_page()
    {
        // Log in as a user
        $user = $this->create_user();

        // Ensure that 'profile' page loads for logged in users
        $response = $this->actingAs($user)
            ->followingRedirects()
            ->get('profile')
            ->assertViewIs('users.profile');
    }

    // Test if a user's profile can be deleted
    public function test_user_can_delete_own_profile()
    {
        // Create a default user
        $user = $this->create_user();
        $this->assertDatabaseCount('users', 1);

        // Delete this user's account
        $response = $this->actingAs($user)
            ->delete('profile');

        // Assert that this users account has been deleted
        $this->assertDatabaseCount('users', 0);
    }

    // Test if a user's profile cannot be deleted when not logged in
    // As users are deleted based on who is logged in, a user would
    // not be able to delete another user's account if this test passes.
    public function test_profile_cannot_be_deleted_when_logged_out()
    {
        // Create a default user
        $user = $this->create_user();
        $this->assertDatabaseCount('users', 1);

        // Delete this user's account without 'actingAs' the user
        $response = $this->delete('profile');

        // Assert that this users account has not been deleted
        $this->assertDatabaseCount('users', 1);
    }

    // Test that users are linked to their expected favorites
    public function test_helper_users_linked_to_favorites()
    {
        $numberOfRecords = 5;
        $favoriteOneDetails = ['id' => 1, 'user_id' => 1, 'laptop_id' => 1];
        $favoriteTwoDetails = ['id' => 2, 'user_id' => 2, 'laptop_id' => 2];
        $favoriteThreeDetails = ['id' => 3, 'user_id' => 3, 'laptop_id' => 3];
        $favoriteFourDetails = ['id' => 4, 'user_id' => 4, 'laptop_id' => 4];
        $favoriteFiveDetails = ['id' => 5, 'user_id' => 5, 'laptop_id' => 5];

        // Seed a database with 5 users, laptops, etc.
        // Each user is linked to one favorite that shares
        // the ID with the user- so user 1 has laptop 1 as
        // their favorite.
        $this->seed_default_test_database($numberOfRecords);

        // Check there are five favorites
        $this->assertDatabaseCount('user_favorites', $numberOfRecords);

        // Check if the favorites have the expected details
        $this->assertDatabaseHas('user_favorites', $favoriteOneDetails);
        $this->assertDatabaseHas('user_favorites', $favoriteTwoDetails);
        $this->assertDatabaseHas('user_favorites', $favoriteThreeDetails);
        $this->assertDatabaseHas('user_favorites', $favoriteFourDetails);
        $this->assertDatabaseHas('user_favorites', $favoriteFiveDetails);
    }

    // Test if a logged out user cannot favorite laptops
    public function test_logged_out_user_trying_to_favorite_laptops_redirect_to_login()
    {
        // Seed a testing database with one record for each table
        $this->seed_default_test_database(1);

        // Ensure that a logged out user trying to favorite a laptop redirects to the login screen
        $response = $this->followingRedirects()
            ->get('laptop/togglefavorite/1')
            ->assertViewIs('auth.login');

        $response->assertOK();
    }


    // Toggle a users favorite laptop (either favorite or unfavorite it based on current status)
    private function toggleUserFavorite($user, $laptopID)
    {
        $this->actingAs($user)
            ->followingRedirects()
            ->get('laptop/togglefavorite/' . $laptopID);
    }

    // Test if a logged in user can favorite laptops
    public function test_logged_in_user_can_toggle_favorite_laptops()
    {
        $numberOfRecords = 1;
        $userID = 1;
        $laptopID = 1;

        // Some of the details that the seeded database should have.
        // If these are in, then the helper function works as expected
        $userDetails = $this->get_user_details();
        $laptopDetails = $this->get_laptop_details();
        $favoriteDetails = $this->get_favorite_details();

        // Seed a testing database with one record for each table.
        // By default, there will be one user and one laptop. This
        // user will have this laptop as a favorite- so favoriting
        // the laptop will first remove the favorite then doing it
        // again will re-favorite the laptop.
        $this->seed_default_test_database($numberOfRecords);

        // Check that the seeding details are as expected
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('laptops', 1);
        $this->assertDatabaseCount('user_favorites', 1);
        $this->assertDatabaseHas('users', $userDetails);
        $this->assertDatabaseHas('laptops', $laptopDetails);
        $this->assertDatabaseHas('user_favorites', $favoriteDetails);

        // Get the user
        $user = User::find(1);

        // See if toggling the favorite whilst logged in removes the favorite
        $this->toggleUserFavorite($user, $laptopID);

        /// Check there are now 0 favorites in the system
        $this->assertDatabaseCount('user_favorites', 0);

        // See if toggling the favorite again refavorites the laptop
        $this->toggleUserFavorite($user, $laptopID);

        /// Check there are now 1 favorites in the system
        $this->assertDatabaseCount('user_favorites', 1);
    }

    // Test to see if a disclaimer is shown when trying to view your favorite laptops
    // if there are none in the system.
    public function test_view_favorites_when_user_has_no_favorites()
    {
        // Log in as a user
        $user = $this->create_user();

        // Initialise string to test if disclaimer shown
        $pageTitle = 'Your Favorited Laptops';
        $visibleIfNoFavorites = 'You have no favorited laptops';

        // Visit the page
        $response = $this->actingAs($user)
            ->followingRedirects()->get('/favoritedlaptops')
            ->assertSeeInOrder([$pageTitle, $visibleIfNoFavorites]);
    }

    // Test that helper functions can create one user
    public function test_helper_create_one_user()
    {
        $numberOfUsers = 1;

        // Create one user
        $this->create_and_save_users($numberOfUsers);

        // Ensure that the database has one user
        $this->assertDatabaseCount('users', $numberOfUsers);
    }

    // Test that helper functions can create five users
    public function test_helper_create_five_users()
    {
        $numberOfUsers = 5;

        // Create five users
        $this->create_and_save_users($numberOfUsers);

        // Ensure that the database has five users
        $this->assertDatabaseCount('users', $numberOfUsers);
    }

    // Test that helper functions can create the specified user
    public function test_helper_create_specified_user()
    {
        $userID = 3;
        $numberOfUsers = 1;

        // Create one user, with the ID of '3'
        $user = $this->create_user($userID);

        // Ensure that the database has one user
        $this->assertDatabaseCount('users', $numberOfUsers);

        // Get the details of user '3'
        $userDetails = $this->get_user_details($userID);

        // Ensure the user in the database has the expected details
        $this->assertDatabaseHas('users', $userDetails);
    }
}
