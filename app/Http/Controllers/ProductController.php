<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProduct;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
	public function __construct()
	{
		//認証
		//認証なしでアクセスしたいAPIにはexceptに書く
		$this->middleware('auth')->except(['index']);
	}

	//商品一覧取得
	public function index()
	{
		$products = Product::with(['user', 'category'])
					->orderByDesc('created_at')
					->paginate();
		return $products;
	}

	//商品詳細
	public function show(string $id)
	{
		$product = Product::with(['user', 'category', 'likes', 'histories'])
						  ->find($id);
		//商品が見つからなかったら404を返す
		return $product ?? abort(404);
	}

	//商品登録
	public function store(StoreProduct $request)
	{
		//インスタンス生成
		$product = new Product;

		if($request->file('image')) {
			//requestから画像名を取得する
			$original_name = $request->file('image')->getClientOriginalName();
			//保存する画像名を作成
			$file_name = date('Ymd_His') . '_' . $original_name;
			//名前を変更して保存。保存先のパスを返す
			$image_path = $request->file('image')->storeAs('public/images', $file_name);
			//HTML出力用の整形とパスの修正
			$image_path = str_replace('public/images/', '/storage/images/', $image_path);
		}else {
			$image_path = '';
		}

		//DBに保存する
		$product->user_id      = $request->user_id;
		$product->category_id  = $request->category_id;
		$product->image        = $image_path;
		$product->name         = $request->name;
		$product->detail       = $request->detail;
		$product->price        = $request->price;
		$product->expire       = $request->expire;

		$product->save();

		//新規作成なので、responseは201(CREATED)を返す
		return response($product, 201);
	}
}
