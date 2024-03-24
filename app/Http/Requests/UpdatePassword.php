<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UpdatePassword extends FormRequest
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
	        'current_password'          => 'required|string|max:255',
	        'new_password'              => 'required|string|min:8|max:255|confirmed',
            'new_password_confirmation' => 'required|string|min:8|max:255'
        ];
    }

	public function withValidator(Validator $validator) {
		$validator->after(function ($validator) {
			$auth = Auth::user(); //ログインユーザー情報を変数に入れる

			if(!(Hash::check($this->input('current_password'), $auth->password)) //現在のパスワードとDBパスワードが合わなければエラー
				&& !$this->input('current_password') == '') {
				$validator->errors()->add('current_password', '現在のパスワードが違います');
			}


			if(Hash::check($this->input('new_password'), $auth->password)) { //新しいパスワードが現在のパスワードと同じであればエラー
				$validator->errors()->add('new_password', '新しいパスワードが現在のパスワードと同じです。');
			}
		});
	}
}
