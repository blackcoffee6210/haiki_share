<?php

namespace App\Http\Controllers;

use App\Cancel;
use App\History;
use App\Http\Requests\StoreProduct;
use App\Http\Requests\UpdateProduct;
use App\Mail\CanceledBuyerNotification;
use App\Mail\CanceledSellerNotification;
use App\Mail\PurchasedBuyerNotification;
use App\Mail\PurchasedSellerNotification;
use App\Product;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ProductController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth')
			 ->except(['index', 'show', 'ranking']); //認証なしでアクセスしたいAPIはexceptに書く
	}

	public function index() //商品一覧取得
	{
		$products = Product::with(['user', 'category', 'likes', 'history'])
						   ->orderByDesc('created_at')
						   ->paginate(); //get()の代わりにpaginateを使うとtotalやcurrent_pageが自動的に追加される
		return $products;
	}

	public function ranking() //お気に入りが多い５件を取得
	{
		$products = Product::withCount('likes')->orderByDesc('likes_count')->take(5)->get();
		return $products;
	}

	/**
	 * 商品詳細
	 * @param string $id
	 * @return Product
	 */
	public function show(string $id) //商品情報取得
	{
		$product = Product::with(['user', 'category', 'likes', 'history'])
						  ->find($id);
		return $product ?? abort(404); //商品が見つからなかったら404を返す
	}

	public function otherProducts(string $u_id, string $p_id) //出品者が投稿した商品を取得
	{
		return User::find($u_id)
			->products()
			->where('products.id', '!=', $p_id)  //詳細画面で表示している商品以外を取得
			->orderByDesc('products.created_at')
			->get();
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

	public function destroy(string $id) //商品削除
	{
		Product::find($id)->delete();
		return response(['product_id' => $id], 200); //商品情報取得
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
		$buyer        = Auth::user();
		$seller       = User::find($request->user_id);
		$buyer_email  = $buyer->email;
		$seller_email = $seller->email;

		if(!$buyer_email || !$seller_email) { //変数が一つでも空だったらj
			abort(404);
		}

		$history = new History;

		$history->buyer_id   = $buyer->id;
		$history->seller_id  = $seller->id;
		$history->product_id = $id;
		$history->save();

		$params = [ //メール送信に必要な情報を用意
			'product_id'   => $request->id,
			'user_name'    => $buyer->name,
			'shop_name'    => $seller->name,
			'product_name' => $request->name,
			'detail'       => $request->detail,
			'price'        => $request->price,
			'expire'       => $request->expire,
			'purchased_at' => Carbon::now(),
		];
		Mail::to($buyer_email)->send(new PurchasedBuyerNotification($params));   //買い手にメールを送信
		Mail::to($seller_email)->send(new PurchasedSellerNotification($params)); //売り手にメールを送信

		return response(['product_id' => $id], 200);
	}

	public function purchasedByUser(string $id) //購入したユーザー取得
	{
		$product = History::where('product_id', $id)
						  ->where('buyer_id', Auth::id())
						  ->get();
		return $product;
	}

	public function canceledByUser(string $id)
	{
		$product = Cancel::where('product_id', $id)
						 ->where('cancel_user_id', Auth::id())
						 ->get();
		return $product;
	}

	public function cancel(Request $request, string $id) //購入キャンセル
	{
		$buyer_email  = Auth::user()->email;                           //買い手のメールアドレス
		$seller_email = $request->email;                               //売り手のメールアドレス

		if(!$buyer_email || !$seller_email) { //変数が一つでも空だったら以下の処理を行う
			abort(404);
		}

		History::where('product_id', $id)->delete(); //購入した商品を取得して削除


		$cancel = new Cancel; //インスタンス生成
		$cancel->cancel_user_id = Auth::id();
		$cancel->post_user_id   = $request->user_id;
		$cancel->product_id     = $id;
		$cancel->save();

		$params = [ //メール送信に必要な情報を用意
			'product_id'   => $request->id,
			'user_name'    => Auth::user()->name,
			'shop_name'    => $request->user_name,
			'product_name' => $request->name,
			'detail'       => $request->detail,
			'price'        => $request->price,
			'expire'       => $request->expire,
			'canceled_at'  => Carbon::now()
		];
		Mail::to($buyer_email)->send(new CanceledBuyerNotification($params));   //買い手にメールを送信
		Mail::to($seller_email)->send(new CanceledSellerNotification($params)); //売り手にメールを送信

		return response(['product_id', $id], 200);
	}

	public function isReviewed(string $id) {
		$review = DB::table('reviews')
					->join('products', 'reviews.receiver_id', '=', 'products.user_id')
					->where('reviews.sender_id', '=', Auth::id())
					->where('products.user_id', $id)
					->get();
		return $review;
	}
}















