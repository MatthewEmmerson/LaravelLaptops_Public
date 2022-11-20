<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class ProfileController extends Controller
{

    // If any database interaction fails, return to the main page with an error message
    private function errorMessage($message)
    {
            return redirect()->route('profile')->withErrors([$message]);
    }

    // Update Profile Details
    public function update(UpdateProfileRequest $request){
        try {
            // Update the user's name and email
            auth()->user()->update($request->only('name', 'email'));

            // If the user chose to update their password, update this as well
            if($request->input('password')) {
                auth()->user()->update([
                    'password' => bcrypt($request->input('password'))
                ]);
            }
        } catch (Exception $ex) {
            return $this->errorMessage('Could not update your details');
        }

        return redirect()->route('profile')->with('success', 'Your details have been updated');
    }

    // Delete Profile
    public function delete(){
        try {
            // Delete this user's account
            auth()->user()->delete();
        } catch (Exception $ex) {
            return $this->errorMessage('Could not delete your account');
        }

        return redirect()->route('laptops')->with('success', "Your account has been deleted");
    }
}
