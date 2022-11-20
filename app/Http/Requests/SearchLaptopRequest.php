<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchLaptopRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'search_make' => ['nullable'],
            'search_manufacturer' => ['nullable'],
            'search_model' => ['nullable'],
            'search_price_greater' => ['nullable', 'numeric'],
            'search_price_lesser' => ['nullable', 'numeric', 'gt:search_price_greater'], // The upper value in the price range must be higher than the lower value
            'search_ram_greater' => ['nullable', 'numeric'],
            'search_ram_lesser' => ['nullable', 'numeric', 'gt:search_ram_greater'],
            'search_ssd_greater' => ['nullable', 'numeric'],
            'search_ssd_lesser' => ['nullable', 'numeric', 'gt:search_ssd_greater'],
            'search_screen_size_greater' => ['nullable', 'numeric'],
            'search_screen_size_lesser' => ['nullable', 'numeric', 'gt:search_screen_size_greater'],
            'search_default_os' => ['nullable'],
        ];
    }

    // Custom error messages for the 'greater' and 'lesser' fields
    public function messages () {
        return [
            'search_price_lesser.gt' => 'The "Price Lesser Than" option must be more than the "Price Greater Than" option',
            'search_ram_lesser.gt' => 'The "RAM Lesser Than" option must be more than the "RAM Greater Than" option',
            'search_ssd_lesser.gt' => 'The "SSD Lesser Than" option must be more than the "SSD Greater Than" option',
            'search_screen_size_lesser.gt' => 'The "Screen Size Lesser Than" option must be more than the "Screen Size Greater Than" option'
        ];
    }

    // If the user does not enter a value for the search price/RAM/SSD/Screen size to be greater than, default to 0.
    protected function prepareForValidation() {
        $this->merge([
            'search_price_greater' => $this->search_price_greater ?? 0,
            'search_ram_greater' => $this->search_ram_greater ?? 0,
            'search_ssd_greater' => $this->search_ssd_greater ?? 0,
            'search_screen_size_greater' => $this->search_screen_size_greater ?? 0
        ]);
    }
}
