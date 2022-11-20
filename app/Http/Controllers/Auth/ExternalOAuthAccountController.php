<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExternalOAuthAccountController extends Controller
{
    // Call the external OAuth providers 'Log in with ...' system
    public function redirectToProvider($provider) {
        return \Socialite::driver($provider)->redirect();
    }

    // Handle the callback from the external OAuth provider
    public function handleProviderCallback(\App\ExternalOAuthAccountService $accountService, $provider) {
        // Try to get the external account's details
        try {
            $oauthUser = \Socialite::with($provider)->user();
        } catch (Exception $ex) {
            return redirect()->route('login');
        }

        // Find or create a Laptop Site account for this external user
        $laptopSiteUser = $accountService->findOrCreate($oauthUser, $provider);

        // Login and redirect to the main page
        auth()->login($laptopSiteUser, true);
        return redirect()->route('laptops');
    }
}
