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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
	public function __construct() //認証なしでアクセスしたいAPIはexceptに書く
	{
		$this->middleware('auth')
			->only(['purchasedByUser', 'canceledByUser']);
	}

	protected $errorMessages = [
		'fetchFailed'    => 'データの取得に失敗しました',
		'saveFailed'     => 'データの保存に失敗しました',
		'updateFailed'   => 'データの更新に失敗しました',
		'deleteFailed'   => 'データの削除に失敗しました',
		'purchaseFailed' => '購入に失敗しました',
		'restoreFailed'  => 'データの復元に失敗しました',
	];

	private function handleException($e, $messageKey)
	{
		Log::error($this->errorMessages[$messageKey]. ': '. $e->getMessage());
		return response()->json(['message' => $this->errorMessages[$messageKey]], 500);
	}

	private function saveImage($image) //画像処理専用メソッド
	{
		if($image) {
			$file_name  = date('Ymd_His') . '_' . $image->getClientOriginalName(); //保存する画像名を作成
			$image_path = $image->storeAs('public/images', $file_name);                   //名前を変更して保存。保存先のパスを返す
			return Storage::url($image_path);
		}
		return null; //画像がない場合はnullを返す
	}

	public function index() //商品一覧取得
	{
		try {
			$products = Product::with(['user', 'category', 'likes', 'history'])
				->orderByDesc('updated_at')
				->paginate(); //get()の代わりにpaginateを使うとtotalやcurrent_pageが自動的に追加される
			return response($products, 200);
		}catch(\Exception $e) {
			return $this->handleException($e, 'fetchFailed');
		}
	}

	public function ranking() //お気に入りが多い５件を取得(おすすめ商品)
	{
		try {
			$products = Product::withCount('likes')->orderByDesc('likes_count')->take(5)->get();
			return $products;
		}catch (\Exception $e) {
			return $this->handleException($e, 'fetchFailed');
		}
	}

	public function prefecture() //出品したユーザーの都道府県IDを取得
	{
		try {
			$products = Product::get();
			return $products;
		}catch (\Exception $e) {
			return $this->handleException($e, 'fetchFailed');
		}
	}

	public function show(string $id) //商品情報取得
	{
		try {
			$product = Product::with(['user', 'category', 'likes', 'history'])->withTrashed()->find($id);
			if (!$product) {
				return response()->json(['error' => '商品が見つかりませんでした。'], 404);
			}
			return $product;
		} catch (\Exception $e) {
			return $this->handleException($e, 'fetchFailed');
		}
	}

	public function otherProducts(string $u_id, string $p_id) //出品者が投稿した商品を取得
	{
		try {
			$products = Product::where('user_id', $u_id)->where('id', '!=', $p_id)->orderByDesc('created_at')->get();
			return $products;
		}catch (\Exception $e) {
			return $this->handleException($e, 'fetchFailed');
		}
	}

	public function similarProducts(string $c_id, string $p_id) //出品した商品に似た商品を取得
	{
		try {
			$products = Product::where('category_id', $c_id)
				->where('id', '!=', $p_id) //詳細画面で表示している商品以外を取得
				->orderByDesc('created_at')
				->get();
			return $products;
		}catch (\Exception $e) {
			return $this->handleException($e, 'fetchFailed');
		}
	}

	public function store(StoreProduct $request) //商品登録
	{
		try {
			$product = new Product; //インスタンス生成

			if($request->hasFile('image')) {
				$image_path = $this->saveImage($request->file('image'));
				$product->image = $image_path; //DBに保存
			}
			//============================================
			//その他のカラムデータをDBに保存する
			$product->user_id     = Auth::id();
			$product->category_id = $request->category_id;
			$product->name        = $request->name;
			$product->detail      = $request->detail;
			$product->price       = $request->price;
			$product->expire      = $request->expire;
			$product->save();
			//============================================

			return response($product, 201); //新規作成なので、responseは201(CREATED)を返す
		}catch (\Exception $e) {
			return $this->handleException($e, 'saveFailed');
		}
	}

	public function update(UpdateProduct $request) //商品更新
	{
		try {
			$product = Product::find($request->id); //商品情報取得

			if($request->hasFile('image')) {
				$image_path = $this->saveImage($request->file('image'));
				$product->image = $image_path;
			}
			//DBに保存
			$product->user_id     = $request->user_id;
			$product->category_id = $request->category_id;
			$product->name        = $request->name;
			$product->detail      = $request->detail;
			$product->price       = $request->price;
			$product->save();

			return response($product, 200); //商品情報とstatusを返す

		}catch(\Exception $e) {
			return $this->handleException($e, 'updateFailed');
		}
	}

	public function destroy(string $id) //商品削除(論理削除)
	{
		try {
			$product = Product::find($id);
			if(!$product) {
				return response()->json(['error' => '商品が見つかりませんでした。'], 404);
			}
			$product->delete();
			return response()->json(['message' => '商品を削除しました。'],200);
		}catch(\Exception $e) {
			return $this->handleException($e, 'deleteFailed');
		}
	}

	public function restore(string $id) //論理削除した商品を復元する
	{
		try {
			$product = Product::withTrashed()->find($id);
			if(!$product) {
				return response()->json(['error' => '商品が見つかりませんでした。'], 404);
			}
			$product->restore();
			return response()->json(['message' => '商品を復元しました。'], 200);
		}catch (\Exception $e) {
			return $this->handleException($e, 'restoreFailed');
		}
	}

	public function forceDelete(string $id) //商品を物理削除する
	{
		try {
			$product = Product::withTrashed()->find($id);
			if(!$product) {
				return response()->json(['error' => '商品が見つかりませんでした。'], 404);
			}
			$product->forceDelete();
			return response()->json(['message' => '商品を完全に削除しました。'], 200);
		}catch (\Exception $e) {
			return $this->handleException($e, 'deleteFailed');
		}
	}

	public function like(string $id) //お気に入り登録
	{
		try {
			$product = Product::find($id);
			if(!$product) {
				return response()->json(['error' => '商品が見つかりませんでした。'], 404);
			}
			$product->likes()->syncWithoutDetaching(Auth::id()); // detachせずにattach（重複なし）
			return response()->json(['message' => 'お気に入りに登録しました。'], 200);
		} catch (\Exception $e) {
			return $this->handleException($e, 'saveFailed');
		}
	}

	public function unlike(string $id) //お気に入り解除
	{
		try {
			$product = Product::find($id);
			if(!$product) {
				return response()->json(['error' => '商品が見つかりませんでした。'], 404);
			}
			$product->likes()->detach(Auth::id()); //likesテーブルのいいねを削除する
			return response()->json(['message' => 'お気に入りを解除しました。'], 200);
		}catch(\Exception $e) {
			return $this->handleException($e, 'deleteFailed');
		}
	}

	public function purchase(Request $request, string $id) //商品購入
	{
		DB::beginTransaction(); //トランザクション開始
		try {
			$buyer   = Auth::user();
			$seller  = User::find($request->user_id);
			$product = Product::find($id);

			if(!$buyer || !$seller || !$product) {
				return response()->json(['message' => '必要な情報が見つかりません。'], 404);
			}

			$history = new History([
				'buyer_id'   => $buyer->id,
				'seller_id'  => $seller->id,
				'product_id' => $product->id
			]);
			$history->save();

			$params = [ //メール送信に必要な情報を用意
				'product_id'   => $product->id,     //商品ID
				'user_name'    => $buyer->name,     //利用者の名前
				'shop_name'    => $seller->name,    //コンビニ名
				'product_name' => $product->name,   //商品名
				'detail'       => $product->detail, //商品の内容
				'price'        => $product->price,  //金額
				'expire'       => $product->expire, //賞味期限
				'purchased_at' => Carbon::now(),    //現在の日時
			];

			try {
				Mail::to($buyer->email)->send(new PurchasedBuyerNotification($params));   //買い手にメールを送信
				Mail::to($seller->email)->send(new PurchasedSellerNotification($params)); //売り手にメールを送信
			}catch(\Exception $mailException) {
				Log::error('メール送信に失敗しました: '. $mailException->getMessage());
				DB::rollBack();
				return response()->json(['message' => 'メール送信に失敗しました', 500]);
			}

			DB::commit(); //トランザクション確定
			return response()->json(['product_id' => $id], 200);

		}catch(\Exception $e) {
			DB::rollBack(); //エラー発生時はトランザクションをロールバック
			return $this->handleException($e, 'purchaseFailed');
		}
	}

	public function purchasedByUser(string $p_id) //購入したユーザー取得
	{
		try {
			$product = History::where('product_id', $p_id)->where('buyer_id', Auth::id())->get();
			return $product;
		}catch (\Exception $e) {
			return $this->handleException($e, 'fetchFailed');
		}
	}

	public function canceledByUser(string $id) //ログインユーザーがキャンセルしたかを取得
	{
		try {
			$product = Cancel::where('product_id', $id)->where('cancel_user_id', Auth::id())->first();
			return $product;
		} catch (\Exception $e) {
			return $this->handleException($e, 'fetchFailed');
		}
	}

	public function cancel(Request $request, string $id) //商品の購入キャンセル
	{
		DB::beginTransaction(); //トランザクション開始
		try {
			$history = History::where('product_id', $id)->where('buyer_id', Auth::id())->first(); //購入履歴を取得

			if(!$history) { //購入履歴がなければ
				return response()->json(['error' => '該当する購入履歴が見つかりません。'], 404);
			}
			$history->delete(); //購入履歴を削除

			$cancel = new Cancel; //インスタンス生成
			$cancel->cancel_user_id = Auth::id();
			$cancel->post_user_id   = $request->user_id;
			$cancel->product_id     = $id;
			$cancel->save();

			$buyer  = Auth::user();
			$seller = User::find($history->seller_id); //販売者情報を取得
			if(!$seller) { //販売者情報を取得できなかったら
				throw new \Exception('販売者が見つかりません。');
			}
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

			try {
				Mail::to($buyer->email)->send(new CanceledBuyerNotification($params));   //買い手にメールを送信
				Mail::to($seller->email)->send(new CanceledSellerNotification($params)); //売り手にメールを送信
			}catch(\Exception $mailException) {
				Log::error('メール送信に失敗しました: '. $mailException->getMessage());
				DB::rollBack();
				return response()->json(['message' => 'メール送信に失敗しました', 500]);
			}

			DB::commit(); //トランザクション確定
			return response()->json(['message' => '商品の購入をキャンセルしました。'], 200);

		}catch(\Exception $e) {
			DB::rollBack(); //トランザクションロールバック
			return $this->handleException($e, 'saveFailed');
		}
	}

	public function isReviewed(string $id) { //ログインしたユーザーがレビューしたかどうか
		try {
			$review = DB::table('reviews')
				->join('products', 'reviews.receiver_id', '=', 'products.user_id')
				->where('reviews.sender_id', '=', Auth::id())
				->where('products.user_id', $id)
				->get();
			return $review;
		}catch (\Exception $e) {
			return $this->handleException($e, 'fetchFailed');
		}
	}
}
