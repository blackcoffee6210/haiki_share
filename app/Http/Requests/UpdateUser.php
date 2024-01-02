<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateUser extends FormRequest
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
            'image'     => 'nullable',
            'name'      => 'required|string|max:255',
            'branch'    => 'required|string|max:255',
            'address'   => 'required|string|max:255',
	        'email'     => ['required', 'string', 'email:filter', 'max:255',
		                     Rule::unique('users')->ignore(Auth::user()->id)
	                       ],
            'introduce' => 'nullable|string|max:255'
        ];
    }
}
