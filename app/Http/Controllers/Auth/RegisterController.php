<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
    	switch ($data['group']) {
		    case 1: //groupが1であれば「利用者」なので、以下のバリデーションを実行
			    return Validator::make($data, [
				    'group'                 => ['required', 'integer'],
				    'name'                  => ['required', 'string', 'max:50'],
				    'email'                 => ['required', 'string', 'email:filter', 'max:255',
					                            Rule::unique('users', 'email')],
				    'password'              => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
				    'password_confirmation' => ['required', 'string', 'min:8', 'max:255'],
			    ]);
		    case 2: //groupが2であれば「コンビニの人」なので、以下のバリデーションを実行
			    return Validator::make($data, [
				    'group'                 => ['required', 'integer'],
				    'name'                  => ['required', 'string', 'max:50'],
				    'branch'                => ['required', 'string', 'max:50'],
				    'address'               => ['required', 'string', 'max:255'],
				    'prefecture_id'         => ['required', 'integer'],
				    'email'                 => ['required',  'string', 'email:filter', 'max:255',
					                            Rule::unique('users', 'email')],
				    'password'              => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
				    'password_confirmation' => ['required', 'string', 'min:8', 'max:255']
			    ]);
	    }
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
	    switch ($data['group']) {
		    case 1: //groupが1であれば「利用者」なので、以下のバリデーションを実行
			    return User::create([
				    'group'     => $data['group'],
				    'name'      => $data['name'],
				    'email'     => $data['email'],
				    'password'  => Hash::make($data['password']),
				    'introduce' => null, // 自己紹介文をnullで登録
			    ]);
		    case 2: //groupが2であれば「コンビニの人」なので、以下のバリデーションを実行
			    return User::create([
				    'group'         => $data['group'],
				    'name'          => $data['name'],
				    'branch'        => $data['branch'],
				    'prefecture_id' => $data['prefecture_id'],
				    'address'       => $data['address'],
				    'email'         => $data['email'],
				    'password'      => Hash::make($data['password']),
				    'introduce'     => null, // 自己紹介文をnullで登録
			    ]);
	    }
    }

	protected function registered(Request $request, $user) //レスポンスをカスタマイズしたい場合はregisteredメソッドをオーバーライドする
	{
		return $user;
	}
}
