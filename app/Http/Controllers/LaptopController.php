<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use finfo;
use App\Models\Laptop;
use App\Models\UserFavorite;
use App\Models\Manufacturer;
use App\Models\LaptopMake;
use App\Http\Requests\UpdateLaptopRequest;
use App\Http\Requests\UploadImageRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Exception;

class LaptopController extends Controller
{
    // If any database interaction fails, return to the main page with an error message
    private function errorMessage($message)
    {
        return redirect()->route('laptops')->withErrors([$message]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $DISPLAY_LAPTOP_COUNT = 6;
        $laptops = Laptop::paginate($DISPLAY_LAPTOP_COUNT);
        return view('laptops.index', compact('laptops'));
    }

    /**
     * Display the 'manage' listing of the resource
     *
     * @return \Illuminate\Http\Response
     */
    public function manage()
    {
        $DISPLAY_LAPTOP_COUNT = 50;
        $laptops = Laptop::paginate($DISPLAY_LAPTOP_COUNT);
        return view('laptops.manage', compact('laptops'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            // Get all manufacturers and makes that can be used
            // Needed to pre-populate fields in the 'create new laptop' form.
            $manufacturers = Manufacturer::get();
            $laptop_makes = LaptopMake::get();
        } catch (QueryException $ex) {
            return $this->errorMessage('Could not find manufacturer or make details');
        }

        // Load the 'Add Laptop' page
        return view('laptops.add', compact('manufacturers', 'laptop_makes'));
}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UpdateLaptopRequest $request)
    {
        try {
            // Make a new laptop model
            $laptop = new Laptop;

            // Add the required details into the new Laptop
            $laptop->manufacturer_id = $request->manufacturer_id;
            $laptop->make_id = $request->make_id;
            $laptop->model = $request->model;
            $laptop->price = $request->price;
            $laptop->ram = $request->ram;
            $laptop->ssd = $request->ssd;
            $laptop->screen_size = $request->screen_size;
            $laptop->default_os = $request->default_os;

            // Add the optional image to the laptop
            if ($request->hasFile('image')) {
                $path = $request->file('image')->getRealPath();
                $image = file_get_contents($path);
                $laptop->image = $image;
            }

            // Save the new laptop
            $laptop->save();
        } catch (Exception $ex) {
            return $this->errorMessage('Could not save this new laptop');
        }

        return redirect('/laptops')->with('success', 'The laptop has been added successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Laptop  $laptop
     * @return \Illuminate\Http\Response
     */
    public function edit($id=0)
    {
        // If the user does not pass in a laptop
        if($id == 0) {
            return redirect('/laptops')->withErrors(['You did not choose a laptop to edit']);
        }

        // Else, try to get the laptop's details
        try {
            $laptop = Laptop::where('laptops.id', $id)->first();

            // Get manufacturers and makes to fill the 'manufacture/make' select boxes.
            $manufacturers = Manufacturer::get();
            $laptop_makes = LaptopMake::get();
        } catch (Exception $ex) {
            return $this->errorMessage('Could not find this laptops details');
        }

        // If this laptop does not exists
        if(!$laptop) {
            return redirect('/laptops')->withErrors(['That Laptop does not exist']);
        }

        return view('laptops.edit', compact('laptop', 'manufacturers', 'laptop_makes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Laptop  $laptop
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLaptopRequest $request)
    {
        try {
            // Update the details of this laptop from the Request.
            // created_at should not change, and updated_at is done automatically.
            $laptop = Laptop::find($request->id);
            $laptop->manufacturer_id = $request->manufacturer_id;
            $laptop->make_id = $request->make_id;
            $laptop->model = $request->model;
            $laptop->price = $request->price;
            $laptop->ram = $request->ram;
            $laptop->ssd = $request->ssd;
            $laptop->screen_size = $request->screen_size;
            $laptop->default_os = $request->default_os;

            // Update the laptop
            $laptop->save();
        } catch (Exception $ex) {
            return $this->errorMessage('Could not update this laptops details');
        }

        return back()->with('success', 'The specified laptop has been updated');
    }

    // Update the image used by a laptop
    public function updateImage(UploadImageRequest $request) {
        // If the user submitted a file to set at this laptops image
        if ($request->hasFile('image')) {
            $laptop = Laptop::find($request->id);
            $path = $request->file('image')->getRealPath();
            $image = file_get_contents($path);
            $laptop->image = $image;
            $laptop->save();
            return back()->with('success', 'This laptop has had their image updated');
        }
    }

    // Get the image associated with this laptop if one exists
    public function getLaptopImage($id)
    {
        $laptop = Laptop::find($id);
        $image = $laptop->image;

        if($image) {
            // Bogdan. (2016). How to store and retrieve Image contents from Database using Laravel. Stack Overflow. https://stackoverflow.com/a/35431752
            return response()->make($laptop->image, 200, array(
                'Content-Type' => (new finfo(FILEINFO_MIME))->buffer($laptop->image)
            ));
        } else {
            return new \Exception;
        }
    }

    // Delete the image from the laptop
    public function deleteImage($id) {
        $laptop = Laptop::find($id);
        $laptop->update(['image' => null]);
        $laptop->save();
        return back()->with('success', 'This laptop has had their image deleted');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Laptop  $laptop
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            // Delete this laptop
            $laptop = Laptop::find($id);
            $laptop->delete();
        } catch (Exception $ex) {
            return $this->errorMessage('Could not delete this laptop');
        }

        return redirect()->route('laptops')->with('success', "The specified laptop has been deleted");
    }

    // Delete a favorite
    private function deleteFavorite($favorite)
    {
        $favorite->delete();
    }

    // Set this laptop as a favorite for this user
    private function favoriteLaptop($laptopID, $userID)
    {
        try {
            // Create a new 'User Favorite' connection
            $userFavorite = new UserFavorite;
            $userFavorite->user_id = $userID;
            $userFavorite->laptop_id = $laptopID;

            // Save the favorite and return back to the previous page
            $userFavorite->save();
        } catch (Exception $ex) {
            return $this->errorMessage('Could not favorite this laptop');
        }
    }

    /**
     * Toggle whether the user has this laptop has a favorite or not
     */
    public function togglefavorite($laptopID)
    {
        // Get the ID of the logged in user
        $userID = auth()->user()->id;

        // Check if this laptop is already favorited by this user
        $alreadyFavorite = UserFavorite::where('user_id', '=', $userID)
            ->where('laptop_id', '=', $laptopID)->first();

        if($alreadyFavorite) {
            // If the laptop is already favorited, delete this favorite link
            $this->deleteFavorite($alreadyFavorite);
            return redirect()->back()->with('success', 'You unfavorited that laptop');
        } else {
            // Else, create this favorite link
            $this->favoriteLaptop($laptopID, $userID);
            return redirect()->back()->with('success', 'You favorited that laptop');
        }
    }

    /**
     * View a page that only has this user's favorited laptops
     */
    public function viewFavoritedLaptops()
    {
        try {
            $DISPLAY_LAPTOP_COUNT = 6;
            $laptops = Laptop::getCurrentUserFavorites()->paginate($DISPLAY_LAPTOP_COUNT);
        } catch (QueryException $ex) {
            return $this->errorMessage('Could not find your favorited laptops');
        }

        return view('laptops.favorited', compact('laptops'));
    }

}