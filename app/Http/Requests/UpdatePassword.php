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
	        'current_password'          => 'required|string|min:8',
	        'new_password'              => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string|min:8'
        ];
    }

	public function withValidator(Validator $validator) {
		$validator->after(function ($validator) {
			$auth = Auth::user(); //ログインユーザー情報を変数に入れる

			//現在のパスワードとDBパスワードが合わなければエラー
			if(!(Hash::check($this->input('current_password'), $auth->password))
				&& !$this->input('current_password') == '') {
				$validator->errors()->add('current_password', '現在のパスワードが違います');
			}
		});
	}
}
