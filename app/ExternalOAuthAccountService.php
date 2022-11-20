<?php

namespace App;

use App\Models\ExternalOAuthAccount;
use App\Models\User;
use Laravel\Socialite\Contracts\User as ProviderUser;

class ExternalOAuthAccountService
{
    // Return an existing external OAuth account (if one exists) based on ID
    private function findExistingOAuthAccount(ProviderUser $providerUser, $provider) {
        return ExternalOAuthAccount::where('provider_name', $provider)
            ->where('provider_id', $providerUser->getId())
            ->first();
    }

    // Create a new user from the Provider details
    private function createNewUser(ProviderUser $providerUser) {
        // The user may not have a 'name' (such as if GitHub has no public profile name set), so default to email in this case
        $name = $providerUser->getName() ? $providerUser->getName() : $providerUser->getEmail();

        return User::create([
            'email' => $providerUser->getEmail(),
            'name'  => $name,
        ]);
    }

    // Create a new External OAuth account
    private function createNewUserExternalOAuthAccount($oauthAccount, $providerUser, $provider) {
        $oauthAccount->accounts()->create([
            'provider_id'   => $providerUser->getId(),
            'provider_name' => $provider,
        ]);
    }

    // Find/Create a new User from the OAuth account provided
    private function createOrLinkExistingOAuthAccount(ProviderUser $providerUser, $provider) {
        // Find if a use signed up with External OAuth account Email manually
        $oauthAccount = User::where('email', $providerUser->getEmail())->first();

        // If the user has not manually signed up with this External account before
        if (!$oauthAccount) {
            // Create an Laptop Site account for this user.
            $oauthAccount = $this->createNewUser($providerUser);
        }

        // Link this Laptop Site account to the External OAUth account
        $this->createNewUserExternalOAuthAccount($oauthAccount, $providerUser, $provider);

        // Return this external OAuth account
        return $oauthAccount;
    }


    // Find or create the relevant Laptop Site account for this external OAuth provider
    public function findOrCreate(ProviderUser $providerUser, $provider)
    {
        // Find the existing account (if one exists)
        $oauthAccount = $this->findExistingOAuthAccount($providerUser, $provider);

        // If the account exists (based on ID)
        if ($oauthAccount) {
            // Return this external OAuth account
            return $oauthAccount->user;
        } else {
            // Create/Link a OAuth account to a Laptop Site account and return this
            return $this->createOrLinkExistingOAuthAccount($providerUser, $provider);
        }
    }
}