<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'store_name' => ['required','string', 'max:100'],
            'address' => ['required','string', 'max:255'],
            'city' => ['required','string', 'max:50'],
            'province' => ['required','string', 'max:50'],
            'store_image[]' => ['image'],
            'user_id' => []
        ];
    }
}
