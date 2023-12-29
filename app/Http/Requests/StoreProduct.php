<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProduct extends FormRequest
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
            'image'       => 'required|file|mimes:jpg,jpeg,png',
	        'category_id' => 'required|integer',
	        'name'        => 'required|string|max:255',
	        'detail'      => 'required|string|max:255',
	        'expire'      => 'required|date|after:today',
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
