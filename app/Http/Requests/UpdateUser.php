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
		    'name'      => 'required|string|max:50',
		    'branch'    => 'sometimes|required|string|max:50',
		    'address'   => 'sometimes|required|string|max:255',
		    'email'     => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
		    'introduce' => 'nullable|string|max:255'
	    ];
    }
}
