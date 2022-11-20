<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Laptop;
use App\Models\LaptopMake;
use App\Models\Manufacturer;

class SearchTest extends TestCase
{

    // Add the previous page to the search URL
    private function getPreviousPage($page)
    {
        $basePageURL = '&search_previous=laptops.';
        return  $basePageURL . $page;
    }

    // Build the search URL
    private function buildSearchURL($fields, $page)
    {
        $searchPrefix = '&search_';
        $searchQuery = '/searchlaptops?_method=GET';

        // Build search URL from given fields, such as:
        // http://localhost/searchlaptops?_method_GET&search_make=ideapad&search_manufacturer=Lenovo&search_model=i&search_price_greater=400&search_price_lesser=700&search_previous=laptops.index
        foreach ($fields as $field) {
            $searchQuery = $searchQuery . $searchPrefix . $field;
        }

        $searchQuery = $searchQuery . $this->getPreviousPage($page);
        return $searchQuery;
    }

    // Search for the laptop (default to index page)
    private function search($fields, $expectedSearchResults, $page='index')
    {
        $url = $this->buildSearchURL($fields, $page);

        $response = $this->followingRedirects()
            ->get($url)
            ->assertSeeInOrder($expectedSearchResults);
    }

    // Ensure the search does not find these results
    private function searchDontFind($fields, $unexpectedSearchResults, $page='index')
    {
        $url = $this->buildSearchURL($fields, $page);

        $response = $this->followingRedirects()
            ->get($url)
            ->assertDontSee($unexpectedSearchResults);
    }


    /* Searches for a basic field.
     * This builds up the searchFields (i.e. what will go in the URL) and the expectedSearchResults (i.e. the expected search results) values
     */
    private function searchBasicField($queryField, $pageField, $page='index')
    {
        $numberOfLaptops = 5;
        $this->seed_default_test_database($numberOfLaptops);

        /* Search for all instances of this field
         * For example:
         * $searchFields = model=test_laptop
         * $expectedSearchResults = 'test_laptop_1, test_laptop_2, test_laptop_3'
         */

        $searchFields = [$queryField . '=test_' . $pageField];
        $expectedSearchResults = ['test_' . $pageField . '_1', 'test_' . $pageField . '_2', 'test_' . $pageField . '_3', 'test_' . $pageField . '_4', 'test_' . $pageField . '_5'];
        $response = $this->search($searchFields, $expectedSearchResults, $page);

        // Search for one particular instance
        $searchFields = [$queryField . '=test_' . $pageField . '_1'];
        $expectedSearchResults = ['test_' . $pageField . '_1'];
        $unexpectedSearchResults = ['test_' . $pageField . '_2', 'test_' . $pageField . '_3', 'test_' . $pageField . '_4', 'test_' . $pageField . '_5'];
        $response = $this->search($searchFields, $expectedSearchResults, $page);
        $response = $this->searchDontFind($searchFields, $unexpectedSearchResults, $page);
    }

    // Test basic searches (one field at a time)

    public function test_search_for_model()
    {
        $searchQuery = 'model'; // The name of the field in the query string
        $expectedSearchResult = 'laptop'; // The name of the field on the page
        $this->searchBasicField($searchQuery, $expectedSearchResult);
    }

    public function test_search_for_manufacturer()
    {
        $searchQuery = 'manufacturer';
        $expectedSearchResult = 'manufacturer';
        $this->searchBasicField($searchQuery, $expectedSearchResult);
    }

    public function test_search_for_make()
    {
        $searchQuery = 'make';
        $expectedSearchResult = 'make';
        $this->searchBasicField($searchQuery, $expectedSearchResult);
    }

    // Search the greater and lesser than fields

    // Alter some details of a laptop
    private function alterLaptopDetails($laptopDetails, $fieldToAlter, $alterTo, $user)
    {
        // Alter the details of the laptop
        $laptopDetails[$fieldToAlter] = $alterTo;

        // Logged in as a user, save the new details
        $response = $this->actingAs($user)
            ->put('laptop', $laptopDetails);

        // Assert the database has a laptop with these details
        $response->assertStatus(302);
        $this->assertDatabaseHas('laptops', $laptopDetails);

        return $laptopDetails;
    }

    // Search other fields

    // Apply the sign to the value (turn '£' and '50' in '£50', and turn 'gb RAM' and '128' into '128gb RAM')
    private function applySign($sign, $value)
    {
        if ($sign == '£') {
            return $sign . $value;
        }

        return $value . $sign;
    }

    private function searchGreaterLesserField($field, $defaultTestValue, $sign, $lowerValue, $upperValue, $lowerLessThan, $upperGreaterThan, $lowerRange, $upperRange, $bothRange, $defaultRange, $page='index')
    {
        // Make user and laptops
        $numberOfLaptops = 5;
        $user = $this->create_user();
        $this->create_and_save_laptops($numberOfLaptops);
        $this->assertDatabaseCount('laptops', $numberOfLaptops);

        // Alter some details of the laptops (so can find laptop with same model name but different (e.g) prices to test search with)
        $laptopToSearchName = 'laptop_model_to_search_for';                 // The name of the laptop to search for (so only the two altered laptops are searched for) in most tests
        $laptopToSearchNameQueryField = 'model=' . $laptopToSearchName;     // model=laptop_model_to_search_for in the search query
        $laptopFieldLesser = $field . '_lesser=';                           // Build equivalent to search_price_lesser
        $laptopFieldGreater = $field . '_greater=';                         // Build equivalent to search_price_greater
        $laptop_one_details = $this->get_laptop_details(1);
        $laptop_three_details = $this->get_laptop_details(3);
        $laptop_one_details = $this->alterLaptopDetails($laptop_one_details, 'model', $laptopToSearchName, $user);
        $laptop_three_details = $this->alterLaptopDetails($laptop_three_details, 'model', $laptopToSearchName, $user);
        $laptop_one_details = $this->alterLaptopDetails($laptop_one_details, $field, $lowerValue, $user);
        $laptop_three_details = $this->alterLaptopDetails($laptop_three_details, $field, $upperValue, $user);

        // Apply the signs to the values
        $lowerValue = $this->applySign($sign, $lowerValue);
        $upperValue = $this->applySign($sign, $upperValue);

        // Search for laptops with shared name
        $queryFields = [$laptopToSearchNameQueryField]; // The name of the laptop
        $expectedSearchResults = [$laptopToSearchName, $laptopToSearchName]; // Expected search results- two laptops with this name
        $response = $this->search($queryFields, $expectedSearchResults, $page);

        // Search for a laptop that has a (e.g) Price lower than the 'lowerFieldLessThan' value- should find the one (e.g) cheaper laptop
        $lowerFieldLessThan = $laptopFieldLesser . $lowerLessThan;
        $queryFields = [$laptopToSearchNameQueryField, $lowerFieldLessThan];
        $expectedSearchResults = [$laptopToSearchName, $lowerValue];
        $response = $this->search($queryFields, $expectedSearchResults, $page);

        // Search for a laptop that has a (e.g) Price higher than the 'upperFieldGreaterThan' value- should find the one (e.g) more expensive laptop
        $upperFieldGreaterThan = $laptopFieldGreater . $upperGreaterThan;
        $queryFields = [$laptopToSearchNameQueryField, $upperFieldGreaterThan];
        $expectedSearchResults = [$laptopToSearchName, $upperValue];
        $response = $this->search($queryFields, $expectedSearchResults, $page);

        // Search for a laptop that has a (e.g) Price between the lower range- should find the one (e.g) cheaper laptop
        $lowerFieldGreaterThan = $laptopFieldGreater . $lowerRange[0];
        $lowerFieldLessThan = $laptopFieldLesser . $lowerRange[1];
        $queryFields = [$laptopToSearchNameQueryField, $lowerFieldGreaterThan, $lowerFieldLessThan];
        $expectedSearchResults = [$laptopToSearchName, $lowerValue];
        $response = $this->search($queryFields, $expectedSearchResults, $page);

        // Search for a laptop that has a (e.g) Price between the upper range- should find the one (e.g) more expensive laptop
        $upperFieldGreaterThan = $laptopFieldGreater . $upperRange[0];
        $upperFieldLessThan = $laptopFieldLesser . $upperRange[1];
        $queryFields = [$laptopToSearchNameQueryField, $upperFieldGreaterThan, $upperFieldLessThan];
        $expectedSearchResults = [$laptopToSearchName, $upperValue];
        $response = $this->search($queryFields, $expectedSearchResults, $page);

        // Search for a laptop that has a (e.g) Price between the both range- should find both laptops with altered names and (e.g) prices
        $lowerFieldGreaterThan = $laptopFieldGreater . $bothRange[0];
        $upperFieldLessThan = $laptopFieldLesser . $bothRange[1];
        $queryFields = [$laptopToSearchNameQueryField, $lowerFieldGreaterThan, $upperFieldLessThan];
        $expectedSearchResults = [$laptopToSearchName, $lowerValue, $laptopToSearchName, $upperValue];
        $response = $this->search($queryFields, $expectedSearchResults, $page);

        // Search for a laptop that has a (e.g) Price between the default range- should find all the laptops that were not altered- have their default names and (e.g) prices
        $defaultRangeLowerGreaterThan = $laptopFieldGreater . $defaultRange[0];
        $defaultRangeUpperLessThan = $laptopFieldLesser . $defaultRange[1];
        $queryFields = [$defaultRangeLowerGreaterThan, $defaultRangeUpperLessThan];
        $expectedSearchResults = ['test_laptop_2', 'test_laptop_4', 'test_laptop_5']; // All unaltered laptops- laptops 1 and 3 where altered
        $response = $this->search($queryFields, $expectedSearchResults, $page);
    }

    public function test_search_for_various_prices()
    {
        $field = 'price';            // Make this look at the 'price' field
        $defaultTestValue = 700;     // The default price field is £700
        $sign = '£';                 // Look for a '£' sign on the page
        $lowerValue = 500;           // Set the lower price value to be £500
        $upperValue = 900;           // Set the upper price value to be £900
        $lowerLessThan = 600;        // Check for a laptop less than £600 (lower price laptop)
        $upperGreaterThan = 750;     // Check for a laptop greater than £750 (upper price laptop)
        $lowerRange = [400, 700];    // Check for a laptop between £400 and £600 (lower price laptop)
        $upperRange = [800, 1000];   // Check for a laptop between £800 and £1000 (upper price laptop)
        $bothRange = [400, 1200];    // Check for laptops between £400 and £1200 (both altered laptops)
        $defaultRange = [600, 800];  // Check for laptops between £600 and £800 (default laptops)
        $this->searchGreaterLesserField($field, $defaultTestValue, $sign, $lowerValue, $upperValue, $lowerLessThan, $upperGreaterThan, $lowerRange, $upperRange, $bothRange, $defaultRange);
    }

    public function test_search_for_various_RAM()
    {
        $field = 'ram';              // Make this look at the 'RAM' field
        $defaultTestValue = 16;      // The default RAM field is 16gb
        $sign = 'gb RAM';            // Look for a 'gb RAM' sign on the page
        $lowerValue = 4;             // Set the lower RAM value to be 4gb
        $upperValue = 32;            // Set the upper RAM value to be 32gb
        $lowerLessThan = 5;          // Check for a laptop less than 5gb (lower RAM laptop)
        $upperGreaterThan = 30;      // Check for a laptop greater than 30gb (upper RAM laptop)
        $lowerRange = [2, 6];        // Check for a laptop between 2gb and 6gb (lower RAM laptop)
        $upperRange = [16, 64];      // Check for a laptop between 16gb and 64gb (upper RAM laptop)
        $bothRange = [3, 60];        // Check for laptops between 3gb and 60gb (both altered laptops when also searching for altered laptop model name)
        $defaultRange = [10, 20];    // Check for laptops between 10gb and 20gb (default laptops)
        $this->searchGreaterLesserField($field, $defaultTestValue, $sign, $lowerValue, $upperValue, $lowerLessThan, $upperGreaterThan, $lowerRange, $upperRange, $bothRange, $defaultRange);
    }

    public function test_search_for_various_SSD()
    {
        $field = 'ssd';              // Make this look at the 'SSD' field
        $defaultTestValue = 256;     // The default SSD field is 256 gb
        $sign = 'gb SSD';            // Look for a 'gb SSD' sign on the page (so this laptop has a '256gb SSD' displayed on the Laptop Card)
        $lowerValue = 128;           // Set the lower SSD value to be 128gb
        $upperValue = 1024;          // Set the upper SSD value to be 1024gb
        $lowerLessThan = 150;        // Check for a laptop less than 150gb (lower SSD laptop)
        $upperGreaterThan = 1000;    // Check for a laptop greater than 1000gb (upper SSD laptop)
        $lowerRange = [100, 250];    // Check for a laptop between 100gb and 250gb (lower SSD laptop)
        $upperRange = [512, 2048];   // Check for a laptop between 512gb and 2048gb (upper SSD laptop)
        $bothRange = [64, 1048];     // Check for laptops between 64gb and 1048gb (both altered laptops when also searching for altered laptop model name)
        $defaultRange = [100, 300];  // Check for laptops between 100gb and 300gb (default laptops)
        $this->searchGreaterLesserField($field, $defaultTestValue, $sign, $lowerValue, $upperValue, $lowerLessThan, $upperGreaterThan, $lowerRange, $upperRange, $bothRange, $defaultRange);
    }

    public function test_search_for_various_screen_size()
    {
        $field = 'screen_size';      // Make this look at the 'screen_size' field
        $defaultTestValue = 14;      // The default screen_size field is 14 inches
        $sign = ' Inches';           // Look for a 'Inches' sign on the page (so this laptop has a 14 Inches displayed on the Laptop Card)
        $lowerValue = 10;            // Set the lower screen_size value to be 10"
        $upperValue = 17;            // Set the upper screen_size value to be 17"
        $lowerLessThan = 12;         // Check for a laptop less than 12" (lower screen_size laptop)
        $upperGreaterThan = 16;      // Check for a laptop greater than 16" (upper screen_size laptop)
        $lowerRange = [8, 12];       // Check for a laptop between 8" and 12" (lower screen_size laptop)
        $upperRange = [16, 20];      // Check for a laptop between 16" and 20" (upper screen_size laptop)
        $bothRange = [5, 20];        // Check for laptops between 5" and 20" (both altered laptops when also searching for altered laptop model name)
        $defaultRange = [12, 15];    // Check for laptops between 12" and 15" (default laptops)
        $this->searchGreaterLesserField($field, $defaultTestValue, $sign, $lowerValue, $upperValue, $lowerLessThan, $upperGreaterThan, $lowerRange, $upperRange, $bothRange, $defaultRange);
    }

    // Search for OS

    public function test_search_for_OS()
    {
        // Make user and laptops
        $numberOfLaptops = 5;
        $user = $this->create_user();
        $this->create_and_save_laptops($numberOfLaptops);
        $this->assertDatabaseCount('laptops', $numberOfLaptops);

        // Alter the OS (two Windows, one of the others)
        $field = 'default_os';
        $laptop_one_details = $this->get_laptop_details(1);
        $laptop_three_details = $this->get_laptop_details(3);
        $laptop_four_details = $this->get_laptop_details(4);
        $laptop_one_details = $this->alterLaptopDetails($laptop_one_details, $field, 'Linux', $user);
        $laptop_three_details = $this->alterLaptopDetails($laptop_three_details, $field, 'MacOS', $user);
        $laptop_four_details = $this->alterLaptopDetails($laptop_four_details, $field, 'ChromeOS', $user);

        // Search for the Windows laptops
        $searchFields = [$field . '=Windows'];
        $expectedSearchResults = ['test_laptop_2', 'test_laptop_5'];
        $unexpectedSearchResults = ['test_laptop_1', 'test_laptop_3', 'test_laptop_4'];
        $response = $this->search($searchFields, $expectedSearchResults);
        $response = $this->searchDontFind($searchFields, $unexpectedSearchResults);

        // Search for the Linux laptop
        $searchFields = [$field . '=Linux'];
        $expectedSearchResults = ['test_laptop_1'];
        $unexpectedSearchResults = ['test_laptop_2', 'test_laptop_3', 'test_laptop_4', 'test_laptop_5'];
        $response = $this->search($searchFields, $expectedSearchResults);
        $response = $this->searchDontFind($searchFields, $unexpectedSearchResults);

        // Search for the MacOS laptop
        $searchFields = [$field . '=MacOS'];
        $expectedSearchResults = ['test_laptop_3'];
        $unexpectedSearchResults = ['test_laptop_1','test_laptop_2', 'test_laptop_4', 'test_laptop_5'];
        $response = $this->search($searchFields, $expectedSearchResults);
        $response = $this->searchDontFind($searchFields, $unexpectedSearchResults);

        // Search for the ChromeOS laptop
        $searchFields = [$field . '=ChromeOS'];
        $expectedSearchResults = ['test_laptop_4'];
        $unexpectedSearchResults = ['test_laptop_1','test_laptop_2', 'test_laptop_3', 'test_laptop_5'];
        $response = $this->search($searchFields, $expectedSearchResults);
        $response = $this->searchDontFind($searchFields, $unexpectedSearchResults);
    }

    // Search for Multiple fields

    public function test_search_for_manufacturer_and_make()
    {
        // Create the manufacturers to search for
        $manufacturer_one_details = $this->get_manufacturer_details(1);
        $manufacturer_two_details = $this->get_manufacturer_details(2);
        $manufacturer_one_details['name'] = 'Lenovo';
        $manufacturer_two_details['name'] = 'Apple';
        Manufacturer::create($manufacturer_one_details);
        Manufacturer::create($manufacturer_two_details);

        // Create the makes to search for
        $make_one_details = $this->get_make_details(1);
        $make_two_details = $this->get_make_details(2);
        $make_three_details = $this->get_make_details(3);
        $make_one_details['name'] = 'ThinkPad';
        $make_two_details['name'] = 'Think Pro';
        $make_three_details['name'] = 'Mac Pro';
        LaptopMake::create($make_one_details);
        LaptopMake::create($make_two_details);
        LaptopMake::create($make_three_details);

        // Create the three laptops.
        // Lenovo ThinkPad, Lenovo Think Pro and Apple Mac Pro
        $laptop_one_details = $this->get_laptop_details(1);
        $laptop_two_details = $this->get_laptop_details(2);
        $laptop_three_details = $this->get_laptop_details(3);
        $laptop_two_details['manufacturer_id'] = '1';
        $laptop_three_details['manufacturer_id'] = '2';
        Laptop::create($laptop_one_details);
        Laptop::create($laptop_two_details);
        Laptop::create($laptop_three_details);

        // Search for Lenovo Think... Should find two
        $searchFields = ['manufacturer=Lenovo', 'make=Think'];
        $expectedSearchResults = ['Lenovo', 'ThinkPad', 'Lenovo', 'Think Pro'];
        $unexpectedSearchResults = ['Apple', 'Mac Pro'];
        $response = $this->search($searchFields, $expectedSearchResults);
        $response = $this->searchDontFind($searchFields, $unexpectedSearchResults);


        // Search for Pro. Should find two
        $searchFields = ['make=Pro'];
        $expectedSearchResults = ['Lenovo', 'Think Pro', 'Apple', 'Mac Pro'];
        $unexpectedSearchResults = ['ThinkPad'];
        $response = $this->search($searchFields, $expectedSearchResults);
        $response = $this->searchDontFind($searchFields, $unexpectedSearchResults);

        // Search for Lenovo ThinkPad. Should find one.
        $searchFields = ['manufacturer=Lenovo', 'make=ThinkPad'];
        $expectedSearchResults = ['Lenovo', 'ThinkPad'];
        $unexpectedSearchResults = ['Think Pro', 'Apple', 'Mac Pro'];
        $response = $this->search($searchFields, $expectedSearchResults);
        $response = $this->searchDontFind($searchFields, $unexpectedSearchResults);

        // Search for Apple Pro. Should find one
        $searchFields = ['manufacturer=Apple', 'make=Pro'];
        $expectedSearchResults = ['Apple', 'Mac Pro'];
        $unexpectedSearchResults = ['ThinkPad', 'Think Pro'];
        $response = $this->search($searchFields, $expectedSearchResults);
        $response = $this->searchDontFind($searchFields, $unexpectedSearchResults);
    }

    public function test_search_for_model_and_price()
    {
        // Create manufacturers and makes
        $numberOfLaptops = 3;
        $this->create_and_save_makes($numberOfLaptops);
        $this->create_and_save_manufactures($numberOfLaptops);
        // Create laptops with different names and prices
        $laptop_one_details = $this->get_laptop_details(1);
        $laptop_two_details = $this->get_laptop_details(2);
        $laptop_three_details = $this->get_laptop_details(3);
        $laptopWithSameName = 'laptop_with_same_name';
        $laptopWithDifferentName = 'laptop_with_different_name';
        $laptopWithDifferentPrice = 500;
        $laptop_one_details['model'] = $laptopWithSameName;
        $laptop_two_details['model'] = $laptopWithSameName;
        $laptop_two_details['price'] = $laptopWithDifferentPrice;
        $laptop_three_details['model'] = $laptopWithDifferentName;
        Laptop::create($laptop_one_details);
        Laptop::create($laptop_two_details);
        Laptop::create($laptop_three_details);

        // Search for laptops under £600 (laptop two)
        $searchFields = ['price_lesser=600'];
        $expectedSearchResults = [$laptopWithSameName, '£500'];
        $response = $this->search($searchFields, $expectedSearchResults);

        // Search for laptops above 600 (laptops one and three)
        $searchFields = ['price_greater=600'];
        $expectedSearchResults = [$laptopWithSameName, '£700', $laptopWithDifferentName, '£700' ];
        $response = $this->search($searchFields, $expectedSearchResults);

        // Search for laptops above 600 with 'same' in their name (laptop one)
        $searchFields = ['model=same', 'price_greater=600'];
        $expectedSearchResults = [$laptopWithSameName, '£700'];
        $unexpectedSearchResults = [$laptopWithDifferentName];
        $response = $this->search($searchFields, $expectedSearchResults);
        $response = $this->searchDontFind($searchFields, $unexpectedSearchResults);
    }

    // Search Favorited Laptops page
    // Only returns matches from the laptops that the user has favorited, rather than from all laptops

    // Toggle a users favorite laptop (either favorite or unfavorite it based on current status)
    private function toggleUserFavorite($user, $laptopID)
    {
        $this->actingAs($user)
            ->followingRedirects()
            ->get('laptop/togglefavorite/' . $laptopID);
    }

    private function searchAsUser($user, $searchFields, $expectedSearchResults, $page)
    {
        $url = $this->buildSearchURL($searchFields, $page);

        $response = $this->actingAs($user)
            ->followingRedirects()
            ->get($url)
            ->assertSeeInOrder($expectedSearchResults);
    }

    private function searchAsUserDontFind($user, $searchFields, $unexpectedSearchResults, $page)
    {
        $url = $this->buildSearchURL($searchFields, $page);

        $response = $this->actingAs($user)
            ->followingRedirects()
            ->get($url)
            ->assertDontSee($unexpectedSearchResults);
    }

    public function test_search_from_favorited_page_only_searches_through_favorited_laptops()
    {
        $laptopOne = 'test_laptop_1';
        $laptopTwo = 'test_laptop_2';
        $laptopThree = 'test_laptop_3';
        $laptopFour = 'test_laptop_4';
        $laptopFive = 'test_laptop_5';

        $page = 'favorited';
        $numberOfLaptops = 5;
        $user = $this->create_user();
        $firstLaptopToFavorite = 3;
        $secondLaptopToFavorite = 5;
        $this->create_and_save_laptops($numberOfLaptops);
        $this->toggleUserFavorite($user, $firstLaptopToFavorite);

        // Only find laptop three (as that is the only laptop that this person has favorited so far)
        $searchFields = ['model=laptop'];
        $expectedSearchResults = [$laptopThree];
        $unexpectedSearchResults = [$laptopOne, $laptopTwo, $laptopFour, $laptopFive];
        $response = $this->searchAsUser($user, $searchFields, $expectedSearchResults, $page);
        $response = $this->searchAsUserDontFind($user, $searchFields, $unexpectedSearchResults, $page);

        // Find laptop five as well after favoriting this laptop
        $this->toggleUserFavorite($user, $secondLaptopToFavorite);
        $expectedSearchResults = [$laptopThree, $laptopFive];
        $unexpectedSearchResults = [$laptopOne, $laptopTwo, $laptopFour];
        $response = $this->searchAsUser($user, $searchFields, $expectedSearchResults, $page);
        $response = $this->searchAsUserDontFind($user, $searchFields, $unexpectedSearchResults, $page);

        // Only find laptop five after toggling laptop three to no longer be favorited
        $this->toggleUserFavorite($user, $firstLaptopToFavorite);
        $expectedSearchResults = [$laptopFive];
        $unexpectedSearchResults = [$laptopOne, $laptopTwo, $laptopThree, $laptopFour];
        $response = $this->searchAsUser($user, $searchFields, $expectedSearchResults, $page);
        $response = $this->searchAsUserDontFind($user, $searchFields, $unexpectedSearchResults, $page);

        // Only find error message if you have favorited no laptops
        $this->toggleUserFavorite($user, $secondLaptopToFavorite);
        $expectedSearchResults = ['None of your favorited laptops match that search criteria'];
        $unexpectedSearchResults = [$laptopOne, $laptopTwo, $laptopThree, $laptopFour, $laptopFive];
        $response = $this->searchAsUser($user, $searchFields, $expectedSearchResults, $page);
        $response = $this->searchAsUserDontFind($user, $searchFields, $unexpectedSearchResults, $page);
    }

    public function test_search_from_manage_page_returns_user_to_manage_page()
    {
        $numberOfLaptops = 1;
        $laptopName = 'test_laptop_1';
        $laptopPrice = '£700';
        $user = $this->create_user();
        $this->create_and_save_laptops($numberOfLaptops);

        // Index page should contain price
        $page = 'index';
        $searchFields = ['model=laptop'];
        $expectedSearchResults = [$laptopName, $laptopPrice] ;
        $response = $this->searchAsUser($user, $searchFields, $expectedSearchResults, $page);

        // Manage page should not contain price (only logged in users can visit manage page)
        $page = 'manage';
        $expectedSearchResults = [$laptopName];
        $unexpectedSearchResults = [$laptopPrice];
        $response = $this->searchAsUser($user, $searchFields, $expectedSearchResults, $page);
        $response = $this->searchAsUserDontFind($user, $searchFields, $unexpectedSearchResults, $page);
    }
}
