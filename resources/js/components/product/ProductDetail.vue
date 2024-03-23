<template>
	<main class="l-main">
		<div class="p-product-detail">
			
			<!-- ローディング -->
			<Loading v-show="loading" />
			
			<div v-show="!loading">
				
				<!-- SOLDバッジ -->
				<div class="c-badge" v-show="product.is_purchased">
					<div class="c-badge__sold">SOLD</div>
				</div>
				
				<div class="u-p-relative">
					<!-- 商品の画像	-->
					<img :src="product.image" alt="" class="p-product-detail__img">
					<!-- カテゴリー名 -->
					<div class="p-product-detail__category">{{ product.category_name }}</div>
				</div>
				
				
				
				<!-- 商品名と金額のコンテナ	-->
				<div class="p-product-detail__name-container">
					<!-- 商品名 -->
					<h2 class="p-product-detail__product-name">{{ product.name }}</h2>
					<!-- 金額 -->
					<div class="p-product-detail__price" v-if="product.price">{{ product.price | numberFormat }}</div>
				</div>
				
				<!-- ユーザー情報と賞味期限のコンテナ -->
				<div class="p-product-detail__flex">
					<!-- ユーザー情報のコンテナ(左側) -->
					<div class="p-product-detail__user-info">
						<!-- 詳細画面のリンク -->
						<router-link class="c-card__link"
												 v-if="product && product.user_id"
												 :to="{ name: 'user.detail', params: { id: product.user_id.toString() }}"/>
						<!-- ユーザー画像 -->
						<img :src="product.user_image"
								 alt=""
								 v-if="product.user_image"
								 class="c-icon p-product-detail__icon">
						<!-- no-img -->
						<img src="/storage/images/no-image.png"
								 alt=""
								 v-else
								 class="c-icon p-product-detail__icon">
						<div>
							<div class="c-flex">
								<!-- コンビニ名	-->
								<div class="p-product-detail__shop-name">{{ product.user_name }}</div>
								<!-- 支店名	-->
								<div class="p-product-detail__branch">{{ product.branch }}</div>
							</div>
							<!-- 商品登録日 -->
							<div class="p-product-detail__date">{{ product.created_at | moment }}</div>
						</div>
					</div>
					<!-- 賞味期限 -->
					<div class="p-product-detail__expire">
						<!-- 賞味期限が過ぎていたときの表示 -->
						<div v-if="expireDate">
							<span class="u-font-bold u-color__main">賞味期限切れ</span>
							<span class="p-product-detail__expire__date">{{ product.expire | fromExpire }}</span>
							日
						</div>
						<!-- 正味期限内のときの表示 -->
						<div v-else>
							<span >賞味期限 残り</span>
							<span class="p-product-detail__expire__date">{{ product.expire | momentExpire }}</span>
							日
						</div>
					</div>
				</div>
				
				<!-- ボタンコンテナ	-->
				<div class="p-product-detail__btn-container">
					<!-- お気に入りボタン	-->
					<!-- 自分の商品または購入されている商品は押せない -->
					<button class="c-btn c-btn--white p-product-detail__btn p-product-detail__btn--like"
									:style="{
										'border-color': [isLike ? '#ff3c53' : 'lightgray'],
										'background'  : [isLike ? '#ffd5da' : 'white']
									}"
									:disabled="product.is_my_product || product.is_purchased || purchasedByUser"
									@click="onLikeClick">
						<span v-if="isLike">お気に入り済み</span>
						<span v-else>気になる！</span>
						<font-awesome-icon v-if="isLike"
															 :icon="['fas', 'heart']"
															 color="#ff6f80" />
						<font-awesome-icon v-else
															 :icon="['fas', 'heart']"
															 color="#ccc" />
						{{ product.likes_count }}
					</button>
					
					<!-- 商品編集ボタン(自分の商品のときだけ & 購入されていない) -->
					<router-link class="c-btn p-product-detail__btn"
											 v-show="product.is_my_product && !product.is_purchased && !product.deleted_at"
											 :to="{ name: 'product.edit', params: {id: id.toString() } }">編集する
					</router-link>
					
					<!-- 商品購入ボタン	-->
					<!-- 利用者ユーザー、かつ自分の商品じゃない、かつ自分が購入してないときに出す -->
					<!-- コンビニユーザー、または自分の商品、または購入されている商品は押せない -->
					<button class="c-btn p-product-detail__btn"
									@click="purchase"
									v-show="!isShopUser && !product.is_my_product && !purchasedByUser"
									:disabled="isShopUser || product.is_my_product || product.is_purchased">
						<span v-if="product.is_purchased">購入済み</span>
						<span v-else>購入</span>
					</button>
					
					<!-- レビュー投稿ボタン 購入したユーザーかつレビューを投稿していないときに表示 -->
					<router-link class="c-btn p-product-detail__btn p-product-detail__btn--review"
											 v-show="purchasedByUser && !isReviewed"
											 :to="{ name: 'review.register', params: { p_id: id.toString() }}">レビュー投稿
					</router-link>
					
					<!-- 購入キャンセルボタン 自分が購入した商品のときに表示	-->
					<button v-show="purchasedByUser"
									@click="cancel"
									class="c-btn c-btn--white p-product-detail__btn">購入キャンセル
					</button>
					
					
					<!-- 論理削除された商品を完全削除するボタン	-->
					<button v-show="isShopUser && product.deleted_at"
									class="c-btn c-btn--white p-product-detail__btn--delete"
									@click="forceDelete">完全に削除
					</button>
					
					<!-- 論理削除された商品を復元するボタン	-->
					<button v-show="isShopUser && product.deleted_at"
									class="c-btn"
									@click="restore">復元する
					</button>
				</div>
				
				<!-- 商品の詳細	-->
				<div class="p-product-detail__detail">{{ product.detail }}</div>
				
				
				<!-- twitterシェアボタン(自分の商品のときは表示しない) -->
				<!-- todo: URL修正 -->
				<div class="p-product-detail__twitter-container"
						 v-show="!product.is_my_product">
					<social-sharing url="localhost:8000/products/48"
													v-show="isLogin"
													:title="product.name"
													:hashtags="twitterHashTags.join(',')"
													inline-template
													class="c-btn c-btn__twitter">
						<network network="twitter">
							<font-awesome-icon :icon="['fab', 'twitter']" /> Twitter
						</network>
					</social-sharing>
				</div>
				
				<!-- 出品者の他の商品-->
				<div class="c-title p-product-detail__title"
						 v-show="otherProducts.length > 0">この出品者の他の商品
				</div>
				<div class="p-product-detail__products-container">
					<Product v-show="!loading"
									 v-for="product in otherProducts"
									 v-if="!product.is_purchased"
									 :key="product.id"
									 :product="product" />
				</div>
				
				<!-- この商品に似た商品-->
				<div class="c-title p-product-detail__title"
						 v-show="similarProducts.length > 0">同じカテゴリーの商品
				</div>
				<div class="p-product-detail__products-container">
					<Product v-show="!loading"
									 v-for="product in similarProducts"
									 v-if="!product.is_purchased"
									 :key="product.id"
									 :product="product" />
				</div>
				
				<!-- TOPへ	-->
				<transition name="c-go-top">
					<button class="c-go-top"
									v-show="buttonActive"
									@click="returnTop">TOP</button>
				</transition>
			
			</div>
		</div>
	</main>
</template>

<script>
import { OK }         from "../../util";
import { mapGetters } from 'vuex';
import Loading        from "../Loading";
import Product        from "./Product";
import moment from "moment";

export default {
	name: "ProductDetail",
	props: { //router.jsからidが渡される
		id: {
			type: String,
			required: true
		}
	},
	components: {
		Loading,
		Product,
	},
	data() {
		return {
			product: {},            //商品情報
			isLike: false,          //「いいね」しているかどうか
			errors: null,           //エラーメッセージを格納するプロパティ
			buttonActive: false,    //TOPボタンを表示する
			scroll: 0,              //scroll
			loading: false,         //ローディングを表示するかどうかを判定するプロパティ
			isReviewed: false,      //ログインした利用者がレビューしたかどうか
			purchasedByUser: false, //ログインした利用者が購入したかどうか
			canceledByUser: false,  //ログインした利用者がキャンセルしたかどうか
			otherProducts: [],      //出品者の他の商品
			similarProducts: [],    //出品した商品に似た商品
			twitterHashTags: [],    //Twitterのハッシュタグで表示する
		}
	},
	computed: {
		...mapGetters({
			isLogin: 'auth/check',        //true または false が返ってくる
			userId: 'auth/userId',        //ユーザーIDを取得
			isShopUser: 'auth/isShopUser' //コンビニユーザならtrueが入る
		}),
		expireDate() { //商品の賞味期限が過ぎているかどうかを返す
			let dt = moment().format('YYYY-MM-DD');
			if(this.product.expire <= dt) {
				return true;
			}
		}
	},
	methods: {
		async checkReviewedByUser() { //レビューしたかどうかを返す
			if (!this.userId || !this.product.user_id) return; // ユーザーIDまたは商品の出品者IDがない場合はチェックしない
			
			try {
				const response = await axios.get(`/api/reviews/${this.product.user_id}/reviewedByUser`);
				if (response.status === OK) {
					this.isReviewed = response.data.isReviewed; // レビュー投稿状況を更新
				} else {
					console.error('レビュー状態の取得に失敗しました。');
				}
			} catch (error) {
				console.error('レビュー状態の取得中にエラーが発生しました:', error);
			}
		},
		async getPurchasedByUser() { //ログインユーザーが商品を購入したかどうかを返す
			try {
				const response = await axios.get(`/api/products/${this.id}/purchasedByUser`);
				
				if(response.status === OK) { //成功なら
					this.purchasedByUser = !!response.data[0];
					
				}else { //失敗なら
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
			}catch(error) {
				console.error('購入情報の取得中にエラーが発生しました');
			}
		},
		async getCanceledByUser() { //ログインユーザーが商品をキャンセルしたかどうかを返す
			try {
				const response = await axios.get(`/api/products/${this.id}/canceledByUser`);
				
				if(response.status === OK) { //成功なら
					this.canceledByUser = !!response.data[0];
					
				}else { //失敗なら
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
			}catch (error) {
				console.error('キャンセル商品情報取得中にエラーが発生しました', error);
			}
		},
		async getProduct() { //商品詳細情報取得
			this.loading = true; //loadingをtrueにする
			
			try {
				const response = await axios.get(`/api/products/${this.id}`); //API通信
				
				if(response.status === OK) { //成功なら
					this.product = response.data; //responseデータをproductプロパティに代入
					
					await this.checkReviewedByUser();
					
					if(this.product.liked_by_user) { //ログインユーザーが既に「いいね」を押していたらtrueをセット
						this.isLike = true;
					}
					this.twitterHashTags.push(  //Twitterのハッシュタグに追加
							this.product.name,
							'HaikiShare',
							'ハイキシェア',
							'フードシェアリング',
							'フードロス',
					);
					
				}else { //失敗なら
					this.$store.commit('error/setCode', response.status); //エラーコードをセット
					return false;
				}
				
			}catch (error) {
				console.error('商品詳細情報取得に失敗しました', error);
				
			}finally {
				this.loading = false; //loadingをfalseにする
			}
		},
		async getOtherProducts() { //出品者の他の商品（購入されていないもの）を取得
			try {
				const response = await axios.get(`/api/products/${this.product.user_id}/${this.id}/other`); //API接続
				
				if(response.status === OK) {
					this.otherProducts = response.data; //responseデータをプロパティに代入
					
				}else {
					this.$store.commit('error/setCode', response.status);
					return false; //後続の処理を抜ける
				}
				
			}catch (error) {
				console.error('出品者の他の商品取得中にエラーが発生しました', error);
			}
		},
		async getSimilarProducts() { //同じカテゴリーの商品を取得
			try {
				const response = await axios.get(`/api/products/${this.product.category_id}/${this.id}/similar`); //API接
				
				if(response.status === OK) {
					this.similarProducts = response.data; //responseデータをプロパティに代入
					
				}else {
					this.$store.commit('error/setCode', response.status);
					return false; //後続の処理を抜ける
				}
				
			}catch (error) {
				console.error('同じカテゴリーの商品取得中にエラーが発生しました', error);
			}
		},
		async onLikeClick() { //「お気に入りボタン」を押したときの処理を行うメソッド
			
			if(!this.isLogin && confirm('いいね機能を使うにはログインしてください')) {
				this.$router.push({ name: 'login' }); //ログインしていない場合、ログインページに遷移
			}
			
			try {
				if(this.product.liked_by_user) {
					await this.unlike(); //すでにいいねを押していたらいいねを外す
				}else {
					this.like(); //いいねしていなかったらいいねをつける
				}
				
			}catch(error) {
				console.error('お気に入りの切り替えに失敗しました', error);
			}
		},
		async like() { //お気に入り登録メソッド
			try {
				const response = await axios.post(`/api/products/${this.id}/like`);
				
				if(response.status === OK) {
					this.product.likes_count += 1; //トータルのいいね数を1増やす
					this.product.liked_by_user = true; //ログインユーザーが「いいね」をしたのでtrueをセット
					this.isLike = true;
				}else {
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
			}catch(error) {
				console.error('いいねの処理中にエラーが発生しました', error);
			}
		},
		async unlike() { //お気に入り解除メソッド
			try {
				const response = await axios.delete(`/api/products/${this.id}/unlike`);
				
				if(response.status === OK) {
					this.product.likes_count -= 1;      //トータルのいいね数を1減らす
					this.product.liked_by_user = false; //ログインユーザーが「いいね解除」したのでfalseをセット
					this.isLike = false;
					
				}else {
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
			}catch(error) {
				console.error('いいね解除の処理中にエラーが発生しました', error)
			}
		},
		async purchase() { //商品購入処理
			if(!this.isLogin) { //ユーザーがログインしているかチェック
				alert('商品を購入するにはログインしてください。')
				this.$router.push({name: 'login'}); //ログインページに遷移
				return;
			}
			
			if(this.canceledByUser) { //商品をキャンセルしたユーザーは再度購入できない
				alert('一度キャンセルした商品は購入できません'); //アラートを表示
				return;
			}
			if(!confirm('購入しますか？')) return; //アレートで「購入しますか?」と表示し、「はい」を押すと以下の処理を実行
			this.loading = true; //ローディングを表示する
			
			try {
				const response = await axios.post(`/api/products/${this.id}/purchase`, this.product); //商品購入APIに接続
				
				if(response.status === OK) { //成功なら
					this.processPurchaseSuccess(response.data); //購入成功処理
					
				}else { //失敗なら
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
			}catch(error) {
				console.error('購入処理に失敗しました', error);
				
			}finally {
				this.loading = false; //ローディングを非表示
			}
		},
		processPurchaseSuccess(data) { //購入成功時の処理
			this.product = { ...this.product, ...data }; //商品データの更新
			
			if(this.product.liked_by_user) { //商品を購入したため、「いいね」をしていたら外す
				this.isLike = false;
				this.product.likes_count--;
			}
			this.purchasedByUser = true; //ログインユーザーが購入したので、プロパティをtrueにする
			this.$store.commit('message/setContent', { content: '商品を購入しました！' }); //メッセージ登録
			this.$router.push({name: 'product.detail', params: {id: this.product.id}}).catch(err => {} ); //自画面(商品詳細)に遷移する
		},
		async cancel() { //購入キャンセル
			if(!confirm('購入をキャンセルしますか？(キャンセルした商品は再度購入できません)')) return;
			this.loading = true; //ローディングを表示する
			
			try {
				const response = await axios.post(`/api/products/${this.id}/cancel`, this.product); //API通信
				
				if(response.status === OK) { //成功
					this.$store.commit('message/setContent', { content: '購入をキャンセルしました' }); //メッセージ登録
					this.$router.push({ name: 'user.mypage', params: { id: this.product.user_id.toString() } }); //マイページに遷移する
					
				}else { //失敗
					this.$store.commit('error/setCode', response.status); //エラーコードをセット
					return false;
				}
				
			}catch (error) {
				console.error('キャンセル処理に失敗しました', error);
				
			}finally {
				this.loading = false; //ローディングを非表示にする
			}
		},
		async restore() { //論理削除した商品を復元する
			if(!confirm('この商品を復元しますか？')) return;
			try {
				const response = await axios.post(`/api/products/${this.id}/restore`); //API通信
				
				if (response.status === OK) { //成功したら
					this.$store.commit('message/setContent', { content: '商品を復元しました！' }); //メッセージ登録
					this.$router.push({name: 'user.mypage', params: {id: this.product.user_id.toString()}}); //マイページに遷移する
					
				}else { //失敗したら
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
			}catch (error) {
				console.error('復元に失敗しました', error);
			}
		},
		async forceDelete() { //論理削除した商品を完全に削除する
			if(!confirm('この商品を完全に削除しますか？（復元できません）')) return;
			
			try {
				const response = await axios.post(`/api/products/${this.id}/forceDelete`); //API通信
				
				if (response.status === OK) { //成功
					this.$store.commit('message/setContent', { content: '商品を完全に削除しました！' }); //メッセージ登録
					this.$router.push({ name: 'user.mypage', params: { id: this.product.user_id.toString() }}); //マイページに遷移する
					
				}else { //失敗
					this.$store.commit('error/setCode', response.status); //エラーコードをセット
					return false;
				}
				
			}catch(error) {
				console.error('商品の完全削除に失敗しました。', error);
			}
		},
		async getMyReview() { //レビュー投稿済みかどうか
			try {
				const response = await axios.get(`/api/products/${this.product.user_id}/isReviewed`); //API接続
				
				if(response.status === OK) { //成功
					this.isReviewed = response.data.isReviewed;
					
				}else { //失敗
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
			}catch(error) {
				console.error('レビュー状態の取得に失敗しました', error);
			}
		},
		returnTop() { //「TOPにもどる」ボタンを押したときの処理
			window.scrollTo({
				top: 0,
				behavior: 'smooth'
			});
		},
		scrollWindow() { //「TOPにもどる」ボタンを表示するメソッド
			this.buttonActive = window.scrollY > 100;
		},
	},
	mounted() {
		window.addEventListener('scroll', this.scrollWindow);
	},
	watch: {
		$route: {
			async handler() {
				await this.checkReviewedByUser();
				await this.getPurchasedByUser();
				await this.getCanceledByUser();
				await this.getProduct();
				await this.getOtherProducts();
				await this.getSimilarProducts();
				await this.getMyReview();
			},
			immediate: true
		}
	}
}
</script>
