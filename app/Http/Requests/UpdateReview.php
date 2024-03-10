<?php

namespace App\Http\Requests;

class UpdateReview extends StoreReview
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

	public function rules()
	{
		return [
			'sender_id'         => 'required|integer',
			'receiver_id'       => 'required|integer',
			'recommendation_id' => 'required|integer',
			'title'             => 'required|string|max:50',
			'detail'            => 'required|string|max:255',
		];
	}

	public function attributes()
	{
		parent::attributes();
		return [
			'detail' => 'レビューの内容'
		];
	}
}
