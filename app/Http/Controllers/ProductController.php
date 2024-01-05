<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProduct;
use App\Http\Requests\UpdateProduct;
use App\Mail\CanceledBuyerNotification;
use App\Mail\CanceledSellerNotification;
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
		$this->middleware('auth')->except(['index', 'show']); //認証なしでアクセスしたいAPIはexceptに書く
	}

	public function index() //商品一覧取得
	{
		$products = Product::with(['user', 'category', 'likes', 'histories'])
						   ->orderByDesc('created_at')
						   ->paginate();
		return $products;
	}

	public function show(string $id) //商品情報取得
	{
		$product = Product::with(['user', 'category', 'likes', 'histories'])
						  ->find($id);
		return $product ?? abort(404); //商品が見つからなかったら404を返す
	}

	public function store(StoreProduct $request) //商品登録
	{
		$product = new Product; //インスタンス生成

		if($request->file('image')) {
			$original_name = $request->file('image')->getClientOriginalName();                        //requestから画像名を取得する
			$file_name     = date('Ymd_His') . '_' . $original_name;                                //保存する画像名を作成
			$image_path    = $request->file('image')->storeAs('public/images', $file_name);      //名前を変更して保存。保存先のパスを返す
			$image_path    = str_replace('public/images/', '/storage/images/', $image_path); //HTML出力用の整形とパスの修正
		}else {
			$image_path = '';
		}

		$product->user_id      = $request->user_id; //DBに保存する
		$product->category_id  = $request->category_id;
		$product->image        = $image_path;
		$product->name         = $request->name;
		$product->detail       = $request->detail;
		$product->price        = $request->price;
		$product->expire       = $request->expire;
		$product->save();

		return response($product, 201); //新規作成なので、responseは201(CREATED)を返す
	}

	public function update(UpdateProduct $request) //商品更新
	{
		$product = Product::find($request->id); //商品情報取得

		if($request->file('image')) { //画像が送信されたらバリデーションを行う
			$request->validate([           //画像のバリデーション
				'image' => 'required|file|mimes:jpg,jpeg,png'
			]);

			$original_name = $request->file('image')->getClientOriginalName();                        //requestから画像名を取得する
			$file_name     = date('Ymd_His') . '_' . $original_name;                                //保存する画像名を作成
			$image_path    = $request->file('image')->storeAs('public/images', $file_name);      //名前を変更して保存。保存先のパスを返す
			$image_path    = str_replace('public/images/', '/storage/images/', $image_path); //HTML出力用の整形とパスの修正

		}else { //画像が送信されなかったらDBの画像をパスに入れる
			$image_path = $product->image;
		}

		$product->user_id      = $request->user_id; //DBに保存
		$product->category_id  = $request->category_id;
		$product->image        = $image_path;
		$product->name         = $request->name;
		$product->detail       = $request->detail;
		$product->price        = $request->price;
		$product->save();

		return response($product, 200); //商品情報とstatusを返す
	}

	public function like(string $id) //お気に入り登録
	{
		$product = Product::where('id', $id)->with('likes')->first();

		if(!$product) {
			abort(404);
		}

		$product->likes()->detach(Auth::user()->id); //何回実行しても1個しかいいねがつかないように、まず特定の商品およびログインユーザーに紐づくいいねを削除(detach)
		$product->likes()->attach(Auth::user()->id); //そのあと新たに追加(attach)する
		return response($product, 200);
	}

	public function unlike(string $id) //お気に入り解除
	{
		$product = Product::where('id', $id)->with('likes')->first();

		if(!$product) {
			abort(404);
		}
		$product->likes()->detach(Auth::user()->id); //likesテーブルのいいねを削除する
		return response($product, 200);
	}

	public function purchase(Request $request, string $id) //商品購入
	{
		$product      = Product::with('histories')->find($id); //購入する商品の情報をDBから取得
		$buyer_email  = Auth::user()->email;                           //買い手のメールアドレス
		$seller_email = $product->email;                               //売り手のメールアドレス

		if(!$product || !$buyer_email || !$seller_email) { //変数が一つでも空だったら
			abort(404);
		}

		$product->histories()->attach(Auth::user()->id); //historiesテーブルにデータを追加する

		$params = [ //メール送信に必要な情報を用意
			'product_id'   => $request->id,
			'user_name'    => Auth::user()->name,
			'shop_name'    => $product->user_name,
			'product_name' => $request->name,
			'detail'       => $request->detail,
			'price'        => $request->price,
			'expire'       => $request->expire,
			'purchased_at' => Carbon::now(),
		];
		Mail::to($buyer_email)->send(new PurchasedBuyerNotification($params));   //買い手にメールを送信
		Mail::to($seller_email)->send(new PurchasedSellerNotification($params)); //売り手にメールを送信

		return response($product, 200);
	}

	public function cancel(Request $request, string $id) //購入キャンセル
	{
		$product      = Product::with('histories')->find($id); //テーブルから情報を取得する
		$buyer_email  = Auth::user()->email;                           //買い手のメールアドレス
		$seller_email = $product->email;                               //売り手のメールアドレス

		if(!$product || !$buyer_email || !$seller_email) { //変数が一つでも空だったら以下の処理を行う
			abort(404);
		}

		$product->histories()->detach(Auth::user()->id); //historiesテーブルのデータを削除する
		$product->cancels()->attach(Auth::user()->id);   //cancelsテーブルにデータを追加する

		$params = [ //メール送信に必要な情報を用意
			'product_id'   => $request->id,
			'user_name'    => Auth::user()->name,
			'shop_name'    => $product->user_name,
			'product_name' => $request->name,
			'detail'       => $request->detail,
			'price'        => $request->price,
			'expire'       => $request->expire,
			'canceled_at'  => Carbon::now()
		];
		Mail::to($buyer_email)->send(new CanceledBuyerNotification($params));   //買い手にメールを送信
		Mail::to($seller_email)->send(new CanceledSellerNotification($params)); //売り手にメールを送信

		return response($product, 200);
	}
}















