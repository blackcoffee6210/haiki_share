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
    	$rules = [
		    'image'     => 'nullable',
		    'name'      => 'required|string|max:50',
		    'introduce' => 'nullable|string|max:255'
	    ];

	    if ($this->input('group') == 2) { //コンビニユーザー(groupが2)の場合は、branchとaddressを必須にする
		    $rules['branch']  = 'required|string|max:50';
		    $rules['address'] = 'required|string|max:255';
	    } else if($this->input('group') == 1) { //利用者ユーザー(groupが1)の場合は、branchとaddressをnullableにする
		    $rules['branch']  = 'nullable|string|max:50';
		    $rules['address'] = 'nullable|string|max:255';
	    }

    	return $rules;
    }
}
