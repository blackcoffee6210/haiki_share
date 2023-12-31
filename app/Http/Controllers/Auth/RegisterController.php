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
        if($data['group'] == 1) {
	        return Validator::make($data, [
	            'group'    => ['required', 'integer'],
		        'name'     => ['required', 'string', 'max:255'],
		        'email'    => ['required', 'string', 'email', 'max:255',
			                    Rule::unique('users', 'email')->whereNull('deleted_at')],
				'password' => ['required', 'string', 'min:8', 'confirmed']
	        ]);
        }
        else if($data['group'] == 2) {
	        return Validator::make($data, [
		        'group'         => ['required', 'integer'],
		        'name'          => ['required', 'string', 'max:255'],
		        'branch'        => ['required', 'string', 'max:255'],
		        'address'       => ['required', 'string', 'max:255'],
		        'prefecture_id' => ['required', 'integer'],
		        'email'         => ['required', 'string', 'email', 'max:255',
			                           Rule::unique('users', 'email')->whereNull('deleted_at')],
		        'password'      => ['required', 'string', 'min:8', 'confirmed']
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
    	if($data['group'] == 1) {
    		return User::create([
    			'group'    => $data['group'],
	            'name'     => $data['name'],
	            'email'    => $data['email'],
	            'password' => Hash::make($data['password']),
	        ]);
	    }
    	else if($data['group'] == 2) {
    		return User::create([
    			'group'         => $data['group'],
    	  	  	'name'          => $data['name'],
	            'branch'        => $data['branch'],
			    'prefecture_id' => $data['prefecture_id'],
			    'address'       => $data['address'],
			    'email'         => $data['email'],
			    'password'      => Hash::make($data['password'])
	        ]);
        }
    }

    //レスポンスをカスタマイズしたい場合はregisteredメソッドをオーバーライドする
		protected function registered(Request $request, $user)
		{
			return $user;
		}
}
