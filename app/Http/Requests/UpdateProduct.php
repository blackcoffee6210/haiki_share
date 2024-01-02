<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProduct extends FormRequest
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
    	//賞味期限は含めない(不正防止のため)
        return [
	        'category_id' => 'required|integer',
	        'name'        => 'required|string|max:255',
	        'detail'      => 'required|string|max:255',
	        'price'       => 'required|integer|between:50,10000'
        ];
    }

	public function attributes()
	{
		parent::attributes();
		return [
			'image' => '商品画像',
			'name'  => '商品名'
		];
	}
}
