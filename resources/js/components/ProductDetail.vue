
<template>
	<main class="l-main">
		
		<div class="p-product-detail">
			
			<!-- ローディング -->
			<Loading v-show="loading" />
			
			<div v-show="!loading">
				
				<!-- 商品の画像	-->
				<img :src="product.image"
						 alt=""
						 class="p-product-detail__img">
				
				<!-- タイトル	-->
				<h2 class="p-product-detail__product-name">{{ product.name }}</h2>
				
				<div class="p-product-detail__flex">
					<!-- ユーザー情報のコンテナ(左側) -->
					<div class="p-product-detail__user-info">
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
						<!-- 商品編集ボタン(自分の商品のときだけ) -->
						<router-link class="c-btn p-product-detail__btn--edit"
												 v-if="myProduct"
												 :to="{ name: 'product.edit', params: {id: id.toString() } }">編集する
						</router-link>
						<!-- お気に入りボタン	-->
						<button class="p-product-detail__btn--like"
										:style="{
											'border-color': [isLike ? '#ff3c53' : 'lightgray'],
											'background'  : [isLike ? '#ffd5da' : 'white']
										}"
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
						<!-- todo: 今の状態だと購入しててもボタンを押さないと「購入済みです」とでないので、購入していたら押せないようにリファクタ -->
						<!-- todo: 購入後にコンビニユーザーにレビューを投稿できるようにする -->
						<button class="c-btn p-product-detail__btn--price"
										@click="purchase">
							<span v-if="product.purchased_by_user">購入済み</span>
							<span v-else>{{ product.price | numberFormat }}</span>
						</button>
					
					</div>
				</div>
				
				<!-- 商品の詳細	-->
				<div class="p-product-detail__detail">
					{{ product.detail }}
				</div>
				
				<!-- twitterシェアボタン -->
				<div class="p-product-detail__twitter-container" v-if="product.purchased_by_user">
					<social-sharing url="http://127.0.0.1:8001/products/48"
													title="vue-social-sharingのテスト"
													quote="Vue is a progressive framework for building user interfaces."
													hashtags="haiki_share"
													inline-template class="c-btn--twitter">
						<network network="twitter">
							<font-awesome-icon :icon="['fab', 'twitter']" /> Twitter
						</network>
					</social-sharing>
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
import { OK, CREATED, UNPROCESSABLE_ENTITY } from "../util";
import { mapGetters } from 'vuex';
import Loading from "./Loading";
import StarRating from 'vue-star-rating';

export default {
	name: "ProductDetail",
	props: {
		//router.jsからidが渡される
		id: {
			type: String,
			required: true
		}
	},
	components: {
		Loading,
		StarRating
	},
	data() {
		return {
			product: null,       //商品情報
			isLike: false,       //「いいね」しているかどうか
			errors: null,        //エラーメッセージを格納するプロパティ
			buttonActive: false, //TOPボタンを表示する
			scroll: 0,           //scroll
			loading: false       //ローディングを表示するかどうかを判定するプロパティ
		}
	},
	mounted() {
		window.addEventListener('scroll', this.scrollWindow);
	},
	computed: {
		...mapGetters({
			isLogin: 'auth/check', //true または false が返ってくる
			userId: 'auth/userId'  //ユーザーIDを取得
		}),
		//自分の商品かどうかを真偽値で返す
		myProduct() {
			//商品idとログインidが同じ、かつ購入されていなければtrueを返す
			if(this.product.user_id === this.userId && !this.product.is_purchased) {
				return true;
			}
		}
	},
	methods: {
		//商品詳細情報取得
		async getProduct() {
			const response = await axios.get(`/api/products/${this.id}`);
			
			//responseステータスがOKじゃなかったらエラーコードをセット
			if(response.status !== OK) {
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			//responseデータをproductプロパティに代入
			this.product = response.data;
			//ログインユーザーが既に「いいね」を押していたらtrueをセットする
			if(this.product.liked_by_user) {
				this.isLike = true;
			}
		},
		//「お気に入りボタン」を押したときの処理を行うメソッド
		async onLikeClick() {
			//ログインしていなかったらアレートを出す
			if(!this.isLogin) {
				alert('いいね機能を使うにはログインしてください');
				return false;
			}
			//自分の商品には「お気に入り」できないようにする
			if(this.product.user_id === this.userId) {
				alert('自分の商品には「お気に入り」できません');
				return false;
			}
			//購入された商品には「いいね」できないようにする
			if(this.product.purchased_by_user) {
				alert('購入した商品には「お気に入り」できません');
				return false;
			}
			//いいねを押していたらいいねを外す
			if(this.product.liked_by_user) {
				this.unlike();
			//いいねしていなかったらいいねをつける
			} else {
				this.like();
			}
		},
		//お気に入り登録
		async like() {
			const response = await axios.post(`/api/products/${this.id}/like`);
			
			//responseステータスがOKじゃなかったらエラーコードをセット
			if(response.status !== OK) {
				this.$store.commit('error/setCode', response.status);
				//後続の処理を抜ける
				return false;
			}
			//トータルのいいね数を1増やす
			this.product.likes_count += 1;
			//ログインユーザーが「いいね」をしたのでtrueをセット
			this.product.liked_by_user = true;
			this.isLike = true;
		},
		//お気に入り解除
		async unlike() {
			const response = await axios.delete(`/api/products/${this.id}/like`);
			
			//responseステータスがOKじゃなかったらエラーコードをセット
			if(response.status !== OK) {
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			//トータルのいいね数を1減らす
			this.product.likes_count -= 1;
			//ログインユーザーが「いいね解除」したのでfalseをセット
			this.product.liked_by_user = false;
			this.isLike = false;
		},
		//商品購入処理
		async purchase() {
			//ユーザーがログインしているかチェック
			if(!this.isLogin) {
				alert('商品を購入するにはログインしてください');
				return false;
			}
			//自分の商品は購入できないようにする
			if(this.product.user_id === this.userId) {
				alert('自分の商品は購入できません');
				return false;
			}
			//一度購入した商品は購入できないようにする
			if(this.product.purchased_by_user) {
				alert('この商品はすでに購入済みです');
				return false;
			}
			//アレートで「購入しますか?」と表示し、「はい」を押すと以下の処理を実行
			if(confirm('購入しますか？')) {
				//ローディングを表示する
				this.loading = true;
				//商品購入APIに接続
				const response = await axios.post(`/api/products/${this.id}/purchase`, this.product);
				
				//API通信が終わったらローディングを非表示にする
				this.loading = false;
				
				//responseステータスがOKじゃなかったらエラーコードをセットする
				if (response.status !== OK) {
					this.$store.commit('error/setCode', response.status);
					//後続の処理を抜ける
					return false;
				}
				
				//ログインユーザーが商品を購入したのでtrueをセット(購入済み)にする
				this.product.purchased_by_user = true;
				
				//商品を購入したため、「いいね」をしていたら外す
				if(this.product.liked_by_user) {
					this.unlike();
				}
				
				//メッセージ登録
				this.$store.commit('message/setContent', {
					content: '商品を購入しました！',
					timeout: 5000
				});
				//自画面(商品詳細)に遷移する
				this.$router.push({name: 'product.detail', params: {id: this.id}}).catch(() => {} );
			}
		},
		//「TOPにもどる」ボタンを押したときの処理
		returnTop() {
			window.scrollTo({
				top: 0,
				behavior: 'smooth'
			});
		},
		//「TOPにもどる」ボタンを表示するメソッド
		scrollWindow() {
			const top = 100; //ボタンを表示させたい位置
			this.scroll = window.scrollY;
			//scrollがtop以上になったらボタンを表示する
			(top <= this.scroll) ? this.buttonActive = true
													 : this.buttonActive = false;
		},
	},
	watch: {
		$route: {
			async handler() {
				await this.getProduct();
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
