<template>
	<div class="l-main">
		<!-- メインコンテンツ -->
		<main class="l-main__2column">
			<div class="p-mypage">
				
				<!-- ローディング -->
				<Loading v-show="loading" />
				
				<div v-show="!loading">
					
					<!-- お気に入りした商品(利用者) -->
					<section class="p-mypge__section" v-show="!isShopUser">
						<div class="p-mypage__title-container">
							<h2 class="c-title p-mypage__title">お気に入りした商品</h2>
							<!-- 全件表示のリンク -->
							<router-link class="p-mypage__link"
													 :to="{ name: 'user.liked',
																	params: { id: id.toString() }}"
													 v-if="likedProducts.length">全件表示
							</router-link>
						</div>
						<!-- 商品がなければ表示する -->
						<div v-if="!likedProducts.length"
								 class="p-mypage__no-product">お気に入りした商品はありません
						</div>
						<div v-else class="p-mypage__card-container">
							<!-- Productコンポーネント -->
							<Product v-show="!loading"
											 v-for="product in likedProducts"
											 :key="product.id"
											 :product="product" />
						</div>
					</section>
					
					<!-- 出品した商品(コンビニユーザー) -->
					<section class="p-mypge__section" v-show="isShopUser">
						<div class="p-mypage__title-container">
							<h2 class="c-title p-mypage__title">出品した商品</h2>
							<!-- 全件表示のリンク -->
							<router-link class="p-mypage__link"
													 :to="{ name: 'user.posted',
																	params: { id: id.toString() }}"
													 v-if="postedProducts.length">全件表示
							</router-link>
						</div>
						<!-- 商品がなければ表示する -->
						<div v-if="!postedProducts.length"
								 class="p-mypage__no-product">出品した商品はありません
						</div>
						<div v-else class="p-mypage__card-container">
							<!-- Productコンポーネント -->
							<Product v-show="!loading"
											 v-for="product in postedProducts"
											 :key="product.id"
											 :product="product" />
						</div>
					</section>
					
					<!-- 購入した商品(利用者) -->
					<section class="p-mypge__section" v-show="!isShopUser">
						<div class="p-mypage__title-container">
							<h2 class="c-title p-mypage__title">購入した商品</h2>
							<!-- 全件表示のリンク -->
							<router-link class="p-mypage__link"
													 :to="{ name: 'user.purchased', params: { id: id.toString() }}"
													 v-if="purchasedProducts.length">全件表示
							</router-link>
						</div>
						<!-- 購入した商品がなければ表示する -->
						<div v-if="!purchasedProducts.length"
								 class="p-mypage__no-product">購入した商品はありません
						</div>
						<div v-else class="p-mypage__card-container">
							<!-- Productコンポーネント -->
							<Product v-show="!loading"
											 v-for="product in purchasedProducts"
											 :key="product.id"
											 :product="product" />
						</div>
					</section>
					
					<!-- 購入された商品(コンビニユーザー) -->
					<section class="p-mypage__section" v-show="isShopUser">
						<div class="p-mypage__title-container">
							<h2 class="c-title p-mypage__title">購入された商品</h2>
							<!-- 全件表示のリンク -->
							<router-link class="p-mypage__link"
													 :to="{ name: 'user.purchased', params: { id: id.toString() }}"
													 v-if="wasPurchasedProducts.length">全件表示
							</router-link>
						</div>
						<!-- 購入された商品がなければ表示する -->
						<div v-if="!wasPurchasedProducts.length"
								 class="p-mypage__no-product">購入された商品はありません
						</div>
						<div v-else class="p-mypage__card-container">
							<!-- Productコンポーネント -->
							<Product v-show="!loading"
											 v-for="product in wasPurchasedProducts"
											 :key="product.id"
											 :product="product" />
						</div>
					</section>
					
					<!-- キャンセルした商品一覧(利用者) -->
					<section class="p-mypge__section" v-show="!isShopUser">
						<div class="p-mypage__title-container">
							<h2 class="c-title p-mypage__title">キャンセルした商品一覧</h2>
							<!-- 全件表示のリンク -->
							<router-link class="p-mypage__link"
													 :to="{ name: 'user.canceled',
																	params: { id: id.toString() }}"
													 v-if="canceledProducts.length">全件表示
							</router-link>
						</div>
						<!-- 商品がなければ表示する -->
						<div v-if="!canceledProducts.length"
								 class="p-mypage__no-product">キャンセルした商品はありません
						</div>
						<div v-else class="p-mypage__card-container">
							<!-- Productコンポーネント -->
							<Product v-show="!loading"
											 v-for="product in canceledProducts"
											 :key="product.id"
											 :product="product" />
						</div>
					</section>
					
					
					<!-- キャンセルされた商品一覧(コンビニ) -->
					<section class="p-mypge__section" v-show="isShopUser">
						<div class="p-mypage__title-container">
							<h2 class="c-title p-mypage__title">キャンセルされた商品一覧</h2>
							<!-- 全件表示のリンク -->
							<router-link class="p-mypage__link"
													 :to="{ name: 'user.canceled',
																	params: { id: id.toString() }}"
													 v-if="wasCanceledProducts.length">全件表示
							</router-link>
						</div>
						<!-- 商品がなければ表示する -->
						<div v-if="!wasCanceledProducts.length"
								 class="p-mypage__no-product">キャンセルされた商品はありません
						</div>
						<div v-else class="p-mypage__card-container">
							<!-- Productコンポーネント -->
							<Product v-show="!loading"
											 v-for="product in wasCanceledProducts"
											 :key="product.id"
											 :product="product" >
								<!-- キャンセルスロット -->
								<!-- キャンセルされた数をカウントして表示（「いいね」みたいに） -->
								<div class="p-product__cancel"
										 slot="cancel_count"
										 v-show="isShopUser">{{ product.cancels_count }}回
								</div>
							</Product>
						</div>
					</section>
					
					<!-- 削除した商品一覧(コンビニ) -->
					<section class="p-mypge__section" v-show="isShopUser">
						<div class="p-mypage__title-container">
							<h2 class="c-title p-mypage__title">削除した商品一覧</h2>
							<!-- 全件表示のリンク -->
							<router-link class="p-mypage__link"
													 :to="{ name: 'user.deleted',
																	params: { id: id.toString() }}"
													 v-if="deletedProducts.length">全件表示
							</router-link>
						</div>
						<!-- 商品がなければ表示する -->
						<div v-if="!deletedProducts.length"
								 class="p-mypage__no-product">削除した商品はありません
						</div>
						<div v-else class="p-mypage__card-container">
							<!-- Productコンポーネント -->
							<Product v-show="!loading"
											 v-for="product in deletedProducts"
											 :key="product.id"
											 :product="product" />
						</div>
					</section>
					
					<!-- 投稿したレビュー一覧 -->
					<section class="p-mypge__section" v-show="!isShopUser">
						<div class="p-mypage__title-container">
							<h2 class="c-title p-mypage__title">投稿したレビュー一覧</h2>
							<!-- 全件表示のリンク -->
							<router-link class="p-mypage__link"
													 :to="{ name: 'user.reviewed',
																	params: { id: id.toString() }}"
													 v-if="reviewedUsers.length">全件表示
							</router-link>
						</div>
						
						<!-- 投稿したレビューがなければ表示する -->
						<div v-if="!reviewedUsers.length"
								 class="p-mypage__no-review">投稿したレビューはありません
						</div>
						
						<div v-else class="p-mypage__card-container">
							<!-- todo: レビュー投稿日を実装 -->
							<!-- Reviewコンポーネント -->
							<Review v-show="!loading"
											v-for="review in reviewedUsers"
											:key="review.id"
											:review="review" />
						</div>
					</section>
					
					<!-- 投稿されたレビュー一覧 -->
					<section class="p-mypge__section" v-show="isShopUser">
						<div class="p-mypage__title-container">
							<h2 class="c-title p-mypage__title">投稿されたレビュー一覧</h2>
							<!-- 全件表示のリンク -->
							<router-link class="p-mypage__link"
													 :to="{ name: 'user.reviewed',
																	params: { id: id.toString() }}"
													 v-if="wasReviewedUsers.length">全件表示
							</router-link>
						</div>
						
						<!-- 投稿されたレビューがなければ表示する -->
						<div v-if="!wasReviewedUsers.length"
								 class="p-mypage__no-review">投稿されたレビューはありません
						</div>
						
						<div v-else class="p-mypage__card-container">
							<!-- todo: レビュー投稿日を実装 -->
							<!-- Reviewコンポーネント -->
							<Review v-show="!loading"
											v-for="review in wasReviewedUsers"
											:key="review.id"
											:review="review" />
						</div>
					</section>
				
				</div>
			</div>
		</main>
		
		<!-- サイドバー -->
		<Sidebar :id="id" />
	
	</div>
</template>

<script>
import Loading from "../Loading";
import Product from "../product/Product";
import Review  from "../review/Review";
import Sidebar from "../Sidebar";
import { mapGetters } from 'vuex';
import { OK } from "../../util";
export default {
	name: "MyPage",
	props: {
		id: {
			type: String,
			required: true
		}
	},
	components: {
		Loading,
		Product,
		Review,
		Sidebar
	},
	data() {
		return {
			loading: false,           //ローディング
			likedProducts: {},        //お気に入りした商品(利用者)
			postedProducts: {},       //出品した商品（コンビニ）
			purchasedProducts: {},    //購入した商品（利用者）
			wasPurchasedProducts: {}, //購入された商品（コンビニ）
			canceledProducts: {},     //キャンセルした商品（利用者）
			wasCanceledProducts: {},  //キャンセルされた商品（コンビニ）
			deletedProducts: {},      //削除した商品（コンビニ）
			reviewedUsers: {},        //投稿したレビュー（利用者）
			wasReviewedUsers: {},     //投稿されたレビュー（コンビニ）
		}
	},
	computed: {
		...mapGetters({
			isShopUser: 'auth/isShopUser',
		}),
	},
	methods: {
		async getLikedProducts() { //お気に入りした商品(利用者)
			this.loading   = true; //ローディングを表示する
			const response = await axios.get('/api/mypage/liked');
			this.loading   = false; //API通信が終わったらローディングを非表示にする
		
			if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
				this.$store.commit('error/setCode', response.status);
				return false; //後続の処理を抜ける
			}
			this.likedProducts = response.data; //responseデータをプロパティに代入
			console.log('お気に入りした商品一覧');
			console.log(response.data);
		},
		async getPostedProducts() { //出品した商品(コンビニユーザー)
			this.loading   = true; //ローディングを表示する
			const response = await axios.get('/api/mypage/posted');
			this.loading   = false; //API通信が終わったらローディングを非表示にする
			
			if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
				this.$store.commit('error/setCode', response.status);
				return false; //後続の処理を抜ける
			}
			this.postedProducts = response.data; //responseデータをプロパティに代入
			console.log('出品した商品');
			console.log(response.data);
		},
		async getPurchasedProducts() { //購入した商品(利用者)
			this.loading   = true; //ローディングを表示する
			const response = await axios.get('/api/mypage/purchased');
			this.loading   = false; //API通信が終わったらローディングを非表示にする
			
			if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
				this.$store.commit('error/setCode', response.status);
				return false; //後続の処理を抜ける
			}
			this.purchasedProducts = response.data; //responseデータをプロパティに代入
			console.log('購入した商品一覧');
			console.log(response.data);
		},
		async getWasPurchasedProducts() { //購入された商品(コンビニユーザー)
			this.loading   = true; //ローディングを表示する
			const response = await axios.get('/api/mypage/wasPurchased');
			this.loading   = false; //API通信が終わったらローディングを非表示にする
			
			if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
				this.$store.commit('error/setCode', response.status);
				return false; //後続の処理を抜ける
			}
			this.wasPurchasedProducts = response.data; //responseデータをプロパティに代入
			console.log('購入された商品一覧');
			console.log(this.wasPurchasedProducts);
		},
		async getCanceledProducts() { //キャンセルした商品(利用者)
			this.loading   = true; //ローディングを表示する
			const response = await axios.get('/api/mypage/canceled');
			this.loading   = false; //API通信が終わったらローディングを非表示にする
			
			if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
				this.$store.commit('error/setCode', response.status);
				return false; //後続の処理を抜ける
			}
			this.canceledProducts = response.data; //responseデータをプロパティに代入
			console.log('キャンセルした商品');
			console.log(response.data);
		},
		async getWasCanceledProducts() { //キャンセルされた商品(コンビニユーザー)
			this.loading   = true; //ローディングを表示する
			const response = await axios.get('/api/mypage/wasCanceled');
			this.loading   = false; //API通信が終わったらローディングを非表示にする
			
			if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
				this.$store.commit('error/setCode', response.status);
				return false; //後続の処理を抜ける
			}
			this.wasCanceledProducts = response.data; //responseデータをプロパティに代入
			console.log('キャンセルされた商品');
			console.log(this.wasPurchasedProducts);
		},
		async getDeletedProducts() { //削除した商品(コンビニ)
			this.loading   = true; //ローディングを表示する
			const response = await axios.get('/api/mypage/deleted');
			this.loading   = false; //API通信が終わったらローディングを非表示にする
			
			if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
				this.$store.commit('error/setCode', response.status);
				return false; //後続の処理を抜ける
			}
			this.deletedProducts = response.data; //responseデータをプロパティに代入
			console.log('削除した商品');
			console.log(response.data);
		},
		async getReviewed() { //投稿したレビュー(利用者)
			this.loading   = true; //ローディングを表示する
			const response = await axios.get('/api/mypage/reviewed');
			this.loading   = false; //API通信が終わったらローディングを非表示にする
			
			if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
				this.$store.commit('error/setCode', response.status);
				return false; //後続の処理を抜ける
			}
			this.reviewedUsers = response.data; //responseデータをプロパティに代入
			console.log('投稿したレビュー');
			console.log(this.reviewedUsers);
		},
		async getWasReviewed() { //投稿されたレビュー（コンビニユーザー）
			this.loading   = true; //ローディングを表示する
			const response = await axios.get('/api/mypage/wasReviewed');
			this.loading   = false; //API通信が終わったらローディングを非表示にする
			
			if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
				this.$store.commit('error/setCode', response.status);
				return false; //後続の処理を抜ける
			}
			this.wasReviewedUsers = response.data; //responseデータをプロパティに代入
			console.log('投稿されたレビュー');
			console.log(this.wasReviewedUsers);
		}
	},
	watch: {
		$route: { //$routerを監視してページが変わったときにメソッドが実行されるようにする
			async handler() {
				if(this.isShopUser) { //コンビニユーザーの場合
					await this.getPostedProducts();
					await this.getWasPurchasedProducts();
					await this.getWasCanceledProducts();
					await this.getDeletedProducts();
					await this.getWasReviewed();
				}else { //利用者の場合
					await this.getLikedProducts();
					await this.getPurchasedProducts();
					await this.getCanceledProducts();
					await this.getReviewed();
				}
			},
			immediate: true //immediateをtrueにすると、コンポーネントが生成されたタイミングでも実行する
		}
	}
}
</script>
