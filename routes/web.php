<?php

use App\Http\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// All routes that do not need to be logged in for
Route::group([], function() {
    // Default location is laptops
    Route::get('/', function () {
        return redirect()->route('laptops');
    });

    // 'View All Laptops' can be accessed when logged in or not
    Route::get('/laptops', [\App\Http\Controllers\LaptopController::class, 'index'])->name('laptops');

    // Laptops can be searched when logged in or not
    Route::get('/searchlaptops', [\App\Http\Controllers\SearchController::class, 'search'])->name('laptop.search');

    // Get the image associated with a laptop (it's icon)
    Route::get('/laptops/images/{id}', [\App\Http\Controllers\LaptopController::class, 'getLaptopImage'])->name('getlaptopimage');
});

// Group for 'Log in with ...' routes
Route::group([], function () {
    // Redirect to the provider's 'Log in With' screen
    Route::get('login/{provider}', [\App\Http\Controllers\Auth\ExternalOAuthAccountController::class, 'redirectToProvider'])->name('oauth.provider');

    // Handle the callback after provider 'Log in With' interaction
    Route::get('login/{provider}/callback', [\App\Http\Controllers\Auth\ExternalOAuthAccountController::class, 'handleProviderCallback'])->name('oauth.callback');
});

// User routes with 'authentication' middleware needed
Route::group(['middleware' => 'auth'], function () {
    // View User Profile
    Route::view('profile', 'users.profile')->name('profile');

    // Update User Profile
    Route::put('profile', [\App\Http\Controllers\ProfileController::class, 'update'])
        ->name('profile.update');

    // Delete User Profile
    Route::delete('profile', [\App\Http\Controllers\ProfileController::class, 'delete'])
        ->name('profile.delete');
});

// Laptop routes with 'authentication' middleware needed
Route::group(['middleware' => 'auth'], function () {
    // View Manage Laptop Page
    Route::get('/managelaptops', [\App\Http\Controllers\LaptopController::class, 'manage'])->name('managelaptops');

    // View 'Your Favorited Laptops' Page
    Route::get('/favoritedlaptops', [\App\Http\Controllers\LaptopController::class, 'viewFavoritedLaptops'])->name('favoritedlaptops');

    // View Add Laptop Page
    Route::get('/addlaptop', [\App\Http\Controllers\LaptopController::class, 'create'])->name('addlaptop');

    // Add the Laptop into the database
    Route::put('/addlaptop', [\App\Http\Controllers\LaptopController::class, 'store'])->name('laptop.add');

    // Edit Laptop Page
    Route::get('/laptops/edit/{id?}', [\App\Http\Controllers\LaptopController::class, 'edit'])->name('editlaptops');

    // Update Laptop's Details
    Route::put('laptop', [\App\Http\Controllers\LaptopController::class, 'update'])
        ->name('laptop.update');

    // Update Laptop's Image
    Route::put('laptop/image', [\App\Http\Controllers\LaptopController::class, 'updateImage'])
        ->name('laptop.update_image');

    // Delete Laptop's Image
    Route::delete('laptop/deleteimage/{id}', [\App\Http\Controllers\LaptopController::class, 'deleteImage'])
        ->name('laptop.delete_image');

    // Delete Laptop
    Route::delete('laptop/delete/{id}', [\App\Http\Controllers\LaptopController::class, 'destroy'])
        ->name('laptop.delete');

    // Toggle Favorite Laptop
    Route::get('laptop/togglefavorite/{laptopID}', [\App\Http\Controllers\LaptopController::class, 'togglefavorite'])
        ->name('laptop.togglefavorite');

});

// Admin user group
Route::group(['middleware' => ['auth', 'admin']], function() {
    // Admin users see all user's favorite laptops
    Route::get('admin/favorites/all', [\App\Http\Controllers\AdminController::class, 'index'])
        ->name('adminfavorites');

    // See all users who have favorited this laptop
    Route::get('admin/favorites/laptop/', [\App\Http\Controllers\AdminController::class, 'laptop'])
        ->name('adminfavoriteslaptop');

    // See all laptops which have been favorited by this user
    Route::get('admin/favorites/user/', [\App\Http\Controllers\AdminController::class, 'user'])
        ->name('adminfavoritesuser');

    // Toggle admin status
    Route::put('admin/toggleuseradmin', [\App\Http\Controllers\AdminController::class, 'toggleUserAdminStatus'])
        ->name('admin.toggle_user_admin');
});

require __DIR__.'/auth.php';
