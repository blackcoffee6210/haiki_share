<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProduct;
use App\Http\Requests\UpdateProduct;
use App\Mail\PurchasedBuyerNotification;
use App\Mail\PurchasedSellerNotification;
use App\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ProductController extends Controller
{
	public function __construct()
	{
		//認証
		//認証なしでアクセスしたいAPIにはexceptに書く
		$this->middleware('auth')->except(['index', 'show']);
	}

	//商品一覧取得
	public function index()
	{
		$products = Product::with(['user', 'category', 'likes', 'histories'])
					->orderByDesc('created_at')
					->paginate();
		return $products;
	}

	//商品情報取得
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

	//商品更新
	public function update(UpdateProduct $request)
	{
		//商品情報取得
		$product = Product::find($request->id);

		//画像が送信されたらバリデーションを行う
		if($request->file('image')) {
			//画像のバリデーション
			$request->validate([
				'image' => 'required|file|mimes:jpg,jpeg,png'
			]);

			//requestから画像名を取得する
			$original_name = $request->file('image')->getClientOriginalName();
			//保存する画像名を作成
			$file_name = date('Ymd_His') . '_' . $original_name;
			//名前を変更して保存。保存先のパスを返す
			$image_path = $request->file('image')->storeAs('public/images', $file_name);
			//HTML出力用の整形とパスの修正
			$image_path = str_replace('public/images/', '/storage/images/', $image_path);
		}
		//画像が送信されなかったらDBの画像をパスに入れる
		else {
			$image_path = $product->image;
		}

		//DBに保存
		$product->user_id      = $request->user_id;
		$product->category_id  = $request->category_id;
		$product->image        = $image_path;
		$product->name         = $request->name;
		$product->detail       = $request->detail;
		$product->price        = $request->price;
		$product->save();

		//商品情報とstatusを返す
		return response($product, 200);
	}

	//お気に入り登録
	public function like(string $id)
	{
		$product = Product::where('id', $id)->with('likes')->first();

		if(!$product) {
			abort(404);
		}
		//何回実行しても1個しかいいねがつかないように、まず特定の商品およびログインユーザーに紐づくいいねを削除(detach) してから、新たに追加(attach)する
		$product->likes()->detach(Auth::user()->id);
		$product->likes()->attach(Auth::user()->id);
		return ['product_id' => $id];
	}

	//お気に入り解除
	public function unlike(string $id)
	{
		$product = Product::where('id', $id)->with('likes')->first();

		if(!$product) {
			abort(404);
		}
		$product->likes()->detach(Auth::user()->id);
		return $product;
	}

	//商品購入
	public function purchase(Request $request, string $id)
	{
		//購入する商品の情報をDBから取得
		$product = Product::with('histories')->find($id);

		//買い手のメールアドレス
		$buyer_email = Auth::user()->email;
		//売り手のメールアドレス
		$seller_email = $product->email;

		//変数が一つでも空だったら
		if(!$product || !$buyer_email || !$seller_email) {
			abort(404);
		}

		//historiesテーブルにデータを追加する
		$product->histories()->attach(Auth::user()->id);
		print_r('historiesテーブルにデータ追加!');

		//メール送信機能実装
		$params = [
			'product_id'   => $request->id,
			'user_name'    => Auth::user()->name,
			'shop_name'    => $product->user_name,
			'product_name' => $request->name,
			'detail'       => $request->detail,
			'price'        => $request->price,
			'expire'       => $request->expire,
			'purchased_at' => Carbon::now(),
		];
		//買い手にメールを送信
		Mail::to($buyer_email)->send(new PurchasedBuyerNotification($params));
		//売り手にメールを送信
		Mail::to($seller_email)->send(new PurchasedSellerNotification($params));

		return ['product_id' => $id];
	}

	//購入キャンセル
	public function cancel(Request $request, string $id)
	{
		//todo: メールを送る
	}
}















