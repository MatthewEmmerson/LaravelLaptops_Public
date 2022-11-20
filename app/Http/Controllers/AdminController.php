<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Laptop;
use App\Models\User;
use Auth;

class AdminController extends Controller
{
    // If any database interaction fails, return to the main page with an error message
    private function errorMessage($message)
    {
        return redirect()->route('adminfavorites')->withErrors([$message]);
    }

    // Load the admin page
    private function loadAdminPage($userFavorites, $previousPage) {
        // Get users and laptops to populate the select lists
        $usersSelect = User::all();
        $laptopsSelect = Laptop::get();

        return view('laptops.admin', compact('usersSelect', 'laptopsSelect', 'userFavorites', 'previousPage'));
    }

    // Load the admin 'View User Favorites' page.
    public function index() {
        // Return a list of all laptops and who favorited them.
        $previousPage = 'all';
        $DISPLAY_LAPTOP_COUNT = 10;
        $allFavorites = Laptop::paginate($DISPLAY_LAPTOP_COUNT);

        // Load the admin page with all user favorites.
        return $this->loadAdminPage($allFavorites, $previousPage);
    }

    // Only show the details for this individual user
    public function user(Request $request) {
        $previousPage = 'user';
        $DISPLAY_LAPTOP_COUNT = 10;

        $user = User::where('id', $request->user_id)->first();
        if ($user == null) {
            return $this->errorMessage('Could not find this user');
        }

        $favoriteLaptops = $user->favorites()->paginate($DISPLAY_LAPTOP_COUNT)->withQueryString();
        return $this->loadAdminPage($favoriteLaptops, $previousPage);
    }

    // Only show the details for this individual laptop
    public function laptop(Request $request) {
        $previousPage = 'laptop';

        $laptop = Laptop::where('id', $request->laptop_id)->first();
        if ($laptop == null) {
            return $this->errorMessage('Could not find this laptop');
        }

        return $this->loadAdminPage($laptop, $previousPage);
    }

    // Toggle the given user's admin status
    public function toggleUserAdminStatus(Request $request) {
        $user = User::where('id', $request->user_id)->first();
        if ($user == null) {
            return $this->errorMessage('Could not find this user');
        }

        if ($user->id == Auth::user()->id) {
            return $this->errorMessage('Cannot toggle your own admin status');
        }

        $currentlyAnAdmin = $user->admin == 1;
        $newAdminState = $currentlyAnAdmin ? 0 : 1;
        $user->admin = $newAdminState;
        $user->save();

        return redirect()->route('adminfavorites')->with('success', "Toggled this user's admin status");
    }
}