<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laptop extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'manufacturer_id',
        'make_id',
        "model",
        "price",
        "ram",
        "ssd",
        "screen_size",
        "default_os",
        "image"
    ];

    // Get the user's ID if they are logged in
    private static function getUserID() {
        if (Auth::check()) {
            return auth()->user()->id;
        } else {
            return 0;
        }
    }

    public function users() {
        return $this->belongsToMany(User::class, 'user_favorites');
    }

    public function manufacturer() {
        return $this->belongsTo(Manufacturer::class);
    }

    public function make() {
        return $this->belongsTo(LaptopMake::class);
    }

    // Get if this laptop is favorited by the current user
    public function favoritedByCurrentUser() {
        $userID = Laptop::getUserID();
        return $this->selectRaw('EXISTS(SELECT * FROM user_favorites WHERE user_favorites.user_id = ? AND user_favorites.laptop_id = laptops.id) AS "favorited"', [$userID])->find($this->id)->favorited;
    }

    // Get the total number of people who have favorited this laptop
    public function adminGetTotalFavoriteCount() {
        return $this->selectRaw('(SELECT COUNT(user_favorites.laptop_id) FROM user_favorites WHERE user_favorites.laptop_id = laptops.id) AS "favorited_count"')->find($this->id)->favorited_count;
    }

    // If any database interaction fails, return to the main page with an error message
    private static function errorMessage($message)
    {
        return redirect()->route('laptops')->withErrors([$message]);
    }

    // Get all the laptops that this particular user has favorited
    public static function getCurrentUserFavorites() {
        $userID = Laptop::getUserID();
        return User::find($userID)->favorites();
    }
}
