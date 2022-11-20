<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLaptopRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Maybe only if this user uploaded the laptop.
        // That could make sense, as that could be something that you
        // could put into a test.
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
            'id' => ['required'],
            'manufacturer_id' => ['required', 'integer'],
            'make_id' => ['required', 'integer'],
            'model' => ['required', 'string'],
            'price' => ['required', 'numeric'],
            'ram' => ['required', 'lte:127'],
            'ssd' => ['required', 'lte:32767'],
            'screen_size' => ['required', 'lte:127'],
            'default_os' => ['required', Rule::in(['Windows', 'MacOS', 'ChromeOS', 'Linux'])],
            'image' => ['mimes:png', 'max: 4096'],
        ];
    }
}
