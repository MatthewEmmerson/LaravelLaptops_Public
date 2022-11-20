<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Models\Laptop;
use App\Models\UserFavorite;
use App\Models\Manufacturer;
use App\Models\LaptopMake;
use App\Http\Requests\SearchLaptopRequest;
use App\Http\Requests\UpdateLaptopRequest;
use App\Http\Requests\UploadImageRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Exception;

class SearchController extends Controller
{
    // Apply the 'WHERE' query to the search for any searched field
    private function searchQueryApplyWhere($query, $fieldName, $requestField) {
        // If the field has been set, add a 'WHERE' filter to the query
        if (!is_null($requestField)) {
            $query = $query->where($fieldName, 'LIKE', '%' . $requestField . '%');
        }

        return $query;
    }

    // Apply the make field to the query
    private function searchQueryApplyMake($query, $request) {
        // Set the field to search from and JOIN onto the necessary table
        $fieldName = 'laptop_makes.name';
        $query = $query->join('laptop_makes', 'laptop_makes.id', '=', 'laptops.make_id')
            ->selectRaw('laptop_makes.id AS makeID');
        $query = $this->searchQueryApplyWhere($query, $fieldName, $request->search_make);

        return $query;
    }

    // Apply the manufacturer field to the query
    private function searchQueryApplyManufacturer($query, $request) {
        // Set the field to search from and JOIN onto the necessary table
        $fieldName = 'manufacturers.name';
        $query = $query->join('manufacturers', 'manufacturers.id', '=', 'laptops.manufacturer_id')
            ->addSelect('manufacturers.id AS manufacturerID');
        $query = $this->searchQueryApplyWhere($query, $fieldName, $request->search_manufacturer);

        return $query;
    }

    // Apply the model field to the query
    private function searchQueryApplyModel($query, $request) {#
        $fieldName = 'laptops.model';
        $query = $this->searchQueryApplyWhere($query, $fieldName, $request->search_model);

        return $query;
    }

    // Apply the relevant 'greater' and 'lesser' search restrictions if needed.
    private function searchQueryApplyGreaterLesser($query, $greaterThanThisValue, $lessThanThisValue, $requestField) {
        if (!is_null($greaterThanThisValue)) {
            $query = $query->where($requestField, '>', $greaterThanThisValue);
        }

        if (!is_null($lessThanThisValue)) {
            $query = $query->where($requestField, '<', $lessThanThisValue);
        }

        return $query;
    }

    // Apply the price field to the query
    private function searchQueryApplyPrice($query, $request) {
        $DATABASE_FIELD = 'laptops.price';
        $query = $this->searchQueryApplyGreaterLesser($query, $request->search_price_greater, $request->search_price_lesser, $DATABASE_FIELD);

        return $query;
    }

    // Apply the RAM field to the query
    private function searchQueryApplyRAM($query, $request) {
        $DATABASE_FIELD = 'laptops.ram';
        $query = $this->searchQueryApplyGreaterLesser($query, $request->search_ram_greater, $request->search_ram_lesser, $DATABASE_FIELD);

        return $query;
    }

    // Apply the SSD field to the query
    private function searchQueryApplySSD($query, $request) {
        $DATABASE_FIELD = 'laptops.ssd';
        $query = $this->searchQueryApplyGreaterLesser($query, $request->search_ssd_greater, $request->search_ssd_lesser, $DATABASE_FIELD);

        return $query;
    }

    // Apply the Screen Size field to the query
    private function searchQueryApplyScreenSize($query, $request) {
        $DATABASE_FIELD = 'laptops.screen_size';
        $query = $this->searchQueryApplyGreaterLesser($query, $request->search_screen_size_greater, $request->search_screen_size_lesser, $DATABASE_FIELD);

        return $query;
    }

    // Apply the Default OS field to the query
    private function searchQueryApplyDefaultOS($query, $request) {
        if (!is_null($request->search_default_os)) {
            $query = $query->where('laptops.default_os', 'LIKE', '%' . $request->search_default_os . '%');
        }

        return $query;
    }

    /**
     * Get the base laptops for the 'Search' query. The possibilities are:
     * * Logged in user's favorited laptops
     * * Logged in user- all laptops (including favorite information)
     * * Logged out user- all laptops (does not include favorite information)
     */
    private function searchQueryBaseLaptops($previousPage) {
        try {
            // Favorites Page
            $favoritesPage = 'laptops.favorited';

            // If the user is searching from the favorites page,
            if ($previousPage == $favoritesPage && Auth::check()) {
                $laptops = Laptop::getCurrentUserFavorites();
            } else {
                $laptops = Laptop::addSelect('laptops.*');
            }

            return $laptops;
        } catch (Exception $ex) {
            return $this->errorMessage('Could not find the base laptops for this search');
        }
    }

    // Apply all search queries to the laptops to only pull back what the user requested
    private function searchQueryApplySearchFields($query, $request) {
        // Apply the relevant fields to the search
        $query = $this->searchQueryApplyMake($query, $request);
        $query = $this->searchQueryApplyManufacturer($query, $request);
        $query = $this->searchQueryApplyModel($query, $request);
        $query = $this->searchQueryApplyPrice($query, $request);
        $query = $this->searchQueryApplyRAM($query, $request);
        $query = $this->searchQueryApplySSD($query, $request);
        $query = $this->searchQueryApplyScreenSize($query, $request);
        $query = $this->searchQueryApplyDefaultOS($query, $request);

        // Return the query with all needed refinements added
        return $query;
    }

    // Return from the search
    private function searchQueryReturn($query, $request)
    {
        // Set the return page
        $indexPage = 'laptops.index';
        $favoritedPage = 'laptops.favorited';
        $managePage = 'laptops.manage';

        $previousPages = [$indexPage, $favoritedPage, $managePage];
        $previousPage = in_array($request->search_previous, $previousPages) ? $request->search_previous : $indexPage;
        $returnPage = $previousPage;

        if (!Auth::check()) {
            $returnPage = $indexPage;
        }

        // Decide how many laptops to show on this page
        if ($returnPage != $managePage) {
            $DISPLAY_LAPTOP_COUNT = 6;
        } else {
            $DISPLAY_LAPTOP_COUNT = 50;
        }

        // Go back to that page with the previous input
        $laptops = $query->paginate($DISPLAY_LAPTOP_COUNT)->withQueryString();
        $request->flash();
        return view($returnPage, compact('laptops'));
    }

    /**
     * Search for a laptop(s) based on information the user enters
     */
    public function search(SearchLaptopRequest $request)
    {
        try {
            // Get the base laptops for the Search Query
            $query = $this->searchQueryBaseLaptops($request->search_previous);

            // Apply any search criteria to limit the selection of laptops
            $query = $this->searchQueryApplySearchFields($query, $request);

            // Return the searched for laptops to the relevant page
            return $this->searchQueryReturn($query, $request);
        } catch (Exception $ex) {
            return $this->errorMessage('Failed to search for these details');
        }
    }
}
