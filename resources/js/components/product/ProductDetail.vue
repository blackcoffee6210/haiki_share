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
					<img :src="product.image"
							 alt=""
							 class="p-product-detail__img">
					<div class="p-product-detail__category">{{ product.category_name }}</div>
				</div>
				
				
				
				<!-- 商品名と金額のコンテナ	-->
				<div class="p-product-detail__name-container">
					<h2 class="p-product-detail__product-name">{{ product.name }}</h2>
					<div class="p-product-detail__price">
						{{ product.price | numberFormat }}
					</div>
				</div>
				
				<div class="p-product-detail__flex">
					<!-- ユーザー情報のコンテナ(左側) -->
					<div class="p-product-detail__user-info">
						<!-- 詳細画面のリンク -->
						<router-link class="c-card__link"
												 :to="{ name: 'user.detail',
								  							params: { id: product.user_id.toString() }}"/>
						<!-- ユーザー画像 -->
						<img :src="product.user_image"
								 alt=""
								 class="c-icon p-product-detail__icon">
						<!-- todo: 画像がない場合はno-imgを表示 -->
						<div>
							<div class="c-flex">
								<!-- コンビニ名	-->
								<div class="p-product-detail__shop-name">
									{{ product.user_name }}
								</div>
								<!-- 支店名	-->
								<div class="p-product-detail__branch">
									{{ product.branch }}
								</div>
							</div>
							<!-- 商品登録日 -->
							<div class="p-product-detail__date">
								{{ product.created_at | moment }}
							</div>
						</div>
					</div>
					<!-- ボタンコンテナ(右側)	-->
					<div class="p-product-detail__btn-container">
						<!-- 商品編集ボタン(自分の商品のときだけ & 購入されていない) -->
						<router-link class="c-btn p-product-detail__btn--edit"
												 v-show="product.is_my_product && !product.is_purchased"
												 :to="{ name: 'product.edit', params: {id: id.toString() } }">編集する
						</router-link>
						
						<!-- お気に入りボタン	-->
						<!-- 自分の商品または購入されている商品は押せない -->
						<button class="c-btn c-btn--white p-product-detail__btn--like"
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
						
						<!-- 商品購入ボタン	-->
						<!-- 利用者ユーザー、かつ自分の商品じゃない、かつ自分が購入してないときに出す -->
						<!-- コンビニユーザー、または自分の商品、または購入されている商品は押せない -->
						<button class="c-btn p-product-detail__btn--purchase"
										@click="purchase"
										v-show="!isShopUser && !product.is_my_product && !purchasedByUser"
										:disabled="isShopUser || product.is_my_product || product.is_purchased">
							<span v-if="product.is_purchased">購入済み</span>
							<span v-else>購入</span>
						</button>
						
						<!-- レビュー投稿ボタン -->
						<!-- 購入したユーザーかつレビューを投稿していないときに表示 -->
						<router-link class="c-btn p-product-detail__btn"
												 v-show="purchasedByUser && !isReviewed"
												 :to="{ name: 'review.register', params: {id: id.toString() } }">レビュー投稿
						</router-link>
						
						<!-- 購入キャンセルボタン	-->
						<!-- 自分が購入した商品のときに表示 -->
						<!--todo: 購入から3日以内または賞味期限1日前まではキャンセル可能-->
						<button v-show="purchasedByUser"
										@click="cancel"
										class="c-btn c-btn--white p-product-detail__btn">購入キャンセル
						</button>
					
					</div>
				</div>
				
				<!-- 商品の詳細	-->
				<div class="p-product-detail__detail">
					{{ product.detail }}
				</div>
				
				<!-- twitterシェアボタン -->
				<!-- 自分の商品のときは表示しない -->
				<div class="p-product-detail__twitter-container"
						 v-show="!product.is_my_product">
					<social-sharing url="http://127.0.0.1:8000/products/48"
													v-show="isLogin"
													title="vue-social-sharingのテスト"
													quote="Vue is a progressive framework for building user interfaces."
													hashtags="haiki_share"
													inline-template class="c-btn c-btn--twitter">
						<network network="twitter">
							<font-awesome-icon :icon="['fab', 'twitter']" /> Twitter
						</network>
					</social-sharing>
				</div>
				
				<!-- 出品者の他の商品-->
				<div class="c-title p-product-detail__title"
						 v-show="otherProducts.length > 0">この出品者の他の商品
				</div>
				<div class="p-product-detail__other-container">
					<Product v-show="!loading"
									 v-for="product in otherProducts"
									 v-if="!product.is_purchased"
									 :key="product.id"
									 :product="product" />
				</div>
				
				<!-- この商品に似た商品-->
				<div class="c-title p-product-detail__title"
						 v-show="similarProducts.length > 0">
					同じカテゴリーの商品
				</div>
				<div class="p-product-detail__other-container">
					<Product v-show="!loading"
									 v-for="product in similarProducts"
									 v-if="!product.is_purchased"
									 :key="product.id"
									 :product="product" />
				</div>
				
				<!-- TOPへ	-->
				<transition name="top">
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
import { Carousel, Slide } from 'vue-carousel';

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
		Carousel,
		Slide
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
		}
	},
	computed: {
		...mapGetters({
			isLogin: 'auth/check',        //true または false が返ってくる
			userId: 'auth/userId',        //ユーザーIDを取得
			isShopUser: 'auth/isShopUser' //コンビニユーザならtrueが入る
		})
	},
	methods: {
		async getPurchasedByUser() {
			const response = await axios.get(`/api/products/${this.id}/purchasedByUser`);
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			response.data[0] ? this.purchasedByUser = true : this.purchasedByUser = false;
		},
		async getCanceledByUser() {
			const response = await axios.get(`/api/products/${this.id}/canceledByUser`);
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			response.data[0] ? this.canceledByUser = true : this.canceledByUser = false;
		},
		async getProduct() { //商品詳細情報取得
			const response = await axios.get(`/api/products/${this.id}`);
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			this.product = response.data; //responseデータをproductプロパティに代入
			
			if(this.product.liked_by_user) { //ログインユーザーが既に「いいね」を押していたらtrueをセット
				this.isLike = true;
			}
		},
		async getOtherProducts() { //出品者の他の商品（購入されていないもの）を取得
			const response = await axios.get(`/api/products/${this.product.user_id}/${this.id}/other`); //API接続
			
			if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
				this.$store.commit('error/setCode', response.status);
				return false; //後続の処理を抜ける
			}
			this.otherProducts = response.data; //responseデータをプロパティに代入
			console.log('otherProductsの中身')
			console.log(this.otherProducts);
		},
		async getSimilarProducts() {
			const response = await axios.get(`/api/products/${this.product.category_id}/${this.id}/similar`); //API接続
			
			if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
				this.$store.commit('error/setCode', response.status);
				return false; //後続の処理を抜ける
			}
			this.similarProducts = response.data; //responseデータをプロパティに代入
		},
		// goProfileDetail() { //プロフィール詳細画面へ遷移
		// 	this.$router.push({
		// 		name: 'user.detail',
		// 		params: { id: this.product.user_id.toString() } });
		// },
		async onLikeClick() { //「お気に入りボタン」を押したときの処理を行うメソッド
			if(!this.isLogin) { //ログインしていなかったらアレートを出す
				if(confirm('いいね機能を使うにはログインしてください')) {
					this.$router.push({name: 'login'}); //ログインページに遷移
					return false;
				}
			}
			if(this.product.liked_by_user) { //すでにいいねを押していたらいいねを外す
				this.unlike();
			} else { //いいねしていなかったらいいねをつける
				this.like();
			}
		},
		async like() { //お気に入り登録メソッド
			const response = await axios.post(`/api/products/${this.id}/like`);
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
				this.$store.commit('error/setCode', response.status);
				return false; //後続の処理を抜ける
			}
			this.product.likes_count += 1; //トータルのいいね数を1増やす
			this.product.liked_by_user = true; //ログインユーザーが「いいね」をしたのでtrueをセット
			this.isLike = true;
		},
		async unlike() { //お気に入り解除メソッド
			const response = await axios.delete(`/api/products/${this.id}/unlike`);
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			this.product.likes_count -= 1; //トータルのいいね数を1減らす
			this.product.liked_by_user = false; //ログインユーザーが「いいね解除」したのでfalseをセット
			this.isLike = false;
		},
		async purchase() { //商品購入処理

			if(!this.isLogin) { //ユーザーがログインしているかチェック
				if(confirm('商品を購入するにはログインしてください')) {
					this.$router.push({name: 'login'}); //ログインページに遷移
				}
				return false;
			}
			if(this.canceledByUser) { //商品をキャンセルしたユーザーは再度購入できない
				alert('一度キャンセルした商品は購入できません');
				return false;
			}
			if(confirm('購入しますか？')) { //アレートで「購入しますか?」と表示し、「はい」を押すと以下の処理を実行
				this.loading   = true; //ローディングを表示する
				
				const response = await axios.post(`/api/products/${this.id}/purchase`, this.product); //商品購入APIに接続
				
				this.loading   = false; //API通信が終わったらローディングを非表示にする
				
				if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
					this.$store.commit('error/setCode', response.status);
					return false; //後続の処理を抜ける
				}
				
				this.purchasedByUser = true;
				// this.product.purchased_by_user = true; //ログインユーザーが商品を購入したのでtrueをセット(購入済み)にする
				
				if(this.product.liked_by_user) { //商品を購入したため、「いいね」をしていたら外す
					this.unlike();
				}
				
				this.$store.commit('message/setContent', { //メッセージ登録
					content: '商品を購入しました！'
				});
				
				this.$router.push({name: 'product.detail', params: {id: this.id}}).catch(() => {} ); //自画面(商品詳細)に遷移する
			}
		},
		async cancel() { //購入キャンセル
			if(confirm('購入をキャンセルしますか？(キャンセルした商品は再度購入できません)')) {
				this.loading = true; //ローディングを表示する
				
				const response = await axios.post(`/api/products/${this.id}/cancel`, this.product); //API通信
				
				this.loading = false; //API通信が終わったらローディングを非表示にする
				
				if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
				// this.product.purchased_by_user = false; //購入キャンセルをしたのでpurchased_by_userにfalseをセット
				// this.product.canceled_by_user  = true; //canceled_by_userにtrueをセット
				
				this.$store.commit('message/setContent', { //メッセージ登録
					content: '購入をキャンセルしました'
				});
				
				this.$router.push({ name: 'index' }); //インデックス画面に遷移する
			}
		},
		async getMyReview() { //レビュー投稿済みかどうか
			const response = await axios.get(`/api/products/${this.product.user_id}/isReviewed`); //API接続
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			
			response.data[0] ? this.isReviewed = true : this.isReviewed = false; //レビュー投稿済みならtrue、なければfalseをセット
		},
		returnTop() { //「TOPにもどる」ボタンを押したときの処理
			window.scrollTo({
				top: 0,
				behavior: 'smooth'
			});
		},
		scrollWindow() { //「TOPにもどる」ボタンを表示するメソッド
			const top = 100; //ボタンを表示させたい位置
			this.scroll = window.scrollY;
			
			(top <= this.scroll) ? this.buttonActive = true //scrollがtop以上になったらボタンを表示する
													 : this.buttonActive = false;
		},
	},
	mounted() {
		window.addEventListener('scroll', this.scrollWindow);
	},
	watch: {
		$route: {
			async handler() {
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

<style>
.top-enter-active,
.top-leave-active {
	transition: opacity .5s;
}
.top-enter,
.top-leave-to {
	opacity: 0;
}
</style>
