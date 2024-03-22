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
		    'email'     => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
		    'introduce' => 'nullable|string|max:255'
	    ];

	    //利用者ユーザー(group == 1)の場合、branchとaddressはnullable許容
	    if ($this->input('group') == 1) {
		    $rules['branch']  = 'nullable|string|max:50';
		    $rules['address'] = 'nullable|string|max:255';

	    //コンビニユーザー(group == 2)の場合、branchとaddressはnullableは必須
	    }else if($this->input('group') == 2) {
		    $rules['branch']  = 'required|string|max:50';
		    $rules['address'] = 'required|string|max:255';
	    }

	    return $rules;
    }
}
