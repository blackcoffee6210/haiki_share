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
								 class="p-mypage__no-Product">お気に入りした商品はありません
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
								 class="p-mypage__no-Product">出品した商品はありません
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
								 class="p-mypage__no-Product">購入した商品はありません
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
							<!--<a class="p-mypage__link">全件表示</a>-->
							<router-link class="p-mypage__link"
													 :to="{ name: 'user.purchased', params: { id: id.toString() }}"
													 v-if="wasPurchasedProducts.length">全件表示
							</router-link>
						</div>
						
						<!-- 購入された商品がなければ表示する -->
						<div v-if="!wasPurchasedProducts.length"
								 class="p-mypage__no-Product">購入された商品はありません
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
								 class="p-mypage__no-Product">キャンセルした商品はありません
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
							<!--todo: RouterLinkに変更-->
							<a class="p-mypage__link"
								 v-if="wasCanceledProducts.length">全件表示</a>
							<!--<router-link class="p-mypage__link"-->
							<!--						 :to="{ name: 'user.reviewed',-->
							<!--										params: { id: id.toString() }}"-->
							<!--						 v-if="reviewedProducts.length">全件表示-->
							<!--</router-link>-->
						</div>
						
						<!-- 商品がなければ表示する -->
						<div v-if="!canceledProducts.length"
								 class="p-mypage__no-Product">キャンセルされた商品はありません
						</div>
						
						<div v-else class="p-mypage__card-container">
							<!-- Productコンポーネント -->
							<Product v-show="!loading"
											 v-for="product in canceledProducts"
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
													 v-if="reviewedShopUsers.length">全件表示
							</router-link>
						</div>
						
						<!-- 商品がなければ表示する -->
						<div v-if="!reviewedShopUsers.length"
								 class="p-mypage__no-Product">投稿したレビューはありません
						</div>
						
						<div v-else class="p-mypage__card-container">
							<!-- todo: コンポーネントにまとめる	-->
							<!-- カード -->
							<div class="c-card p-list__card__review"
									 v-for="review in reviewedShopUsers"
									 :key="review.id">
								<!--レビュー詳細画面のリンク-->
								<router-link class="c-card__link"
														 :to="{ name: 'review.detail',
												 						params: { id: review.id.toString() }}" />
								
								<div class="p-list__user-info__review">
									<!-- ユーザーの画像	-->
									<img class="c-icon p-list__review-icon"
											 :src="review.sender_image"
											 v-show="isShopUser"
											 alt="">
									<img class="c-icon p-list__review-icon"
											 :src="review.receiver_image"
											 v-show="!isShopUser"
											 alt="">
									<div>
										<!-- レビュー相手の名前	-->
										<div class="p-list__name">
											<span v-show="isShopUser">{{ review.sender_name }}</span>
											<span v-show="!isShopUser">{{ review.receiver_name }}</span>
										</div>
										<!-- ユーザーの評価 -->
										<div class="p-list__recommendation">{{ review.recommend }}</div>
									</div>
								</div>
								
								<!-- レビュータイトル -->
								<div class="p-list__review-title">{{ review.title }}</div>
								<!-- レビューの内容 -->
								<div class="p-list__detail">{{ review.detail }}</div>
								
								<!--&lt;!&ndash; ボタン	&ndash;&gt;-->
								<!--<div class="p-list__btn-container">-->
								<!--	&lt;!&ndash; 詳細を見るボタン(コンビニユーザー) &ndash;&gt;-->
								<!--	<router-link class="c-btn p-list__btn p-list__btn&#45;&#45;detail"-->
								<!--							 v-show="isShopUser"-->
								<!--							 :to="{ name: 'review.detail',-->
								<!--									params: { id: review.id.toString() }}">詳細を見る-->
								<!--	</router-link>-->
								<!--	&lt;!&ndash; 編集ボタン(利用者) &ndash;&gt;-->
								<!--	<router-link class="c-btn p-list__btn p-list__btn&#45;&#45;detail"-->
								<!--							 v-show="!isShopUser"-->
								<!--							 :to="{ name: 'review.edit',-->
								<!--											params: {-->
								<!--												s_id: review.sender_id,-->
								<!--												r_id: review.receiver_id-->
								<!--											}-->
								<!--					 			}">編集する-->
								<!--	</router-link>-->
								<!--</div>-->
								
							</div>
						</div>
					</section>
					
					
					<!-- 購入者からのレビュー一覧 -->
					<section class="p-mypge__section" v-show="isShopUser">
						<div class="p-mypage__title-container">
							<h2 class="c-title p-mypage__title">購入者からのレビュー一覧</h2>
							<!-- 全件表示のリンク -->
							<!--todo: RouterLinkに変更-->
							<a class="p-mypage__link"
								 v-if="reviewedUsers.length">全件表示</a>
							<!--<router-link class="p-mypage__link"-->
							<!--						 :to="{ name: 'user.reviewed',-->
							<!--										params: { id: id.toString() }}"-->
							<!--						 v-if="reviewedProducts.length">全件表示-->
							<!--</router-link>-->
						</div>
						
						<!-- 商品がなければ表示する -->
						<div v-if="!reviewedUsers.length"
								 class="p-mypage__no-Product">購入者からのレビューはありません
						</div>
						
						<div v-else class="p-mypage__card-container">
							<!-- Productコンポーネント -->
							<Product v-show="!loading"
											 v-for="review in reviewedUsers"
											 :key="review.id"
											 :product="review" />
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
import Sidebar from "../Sidebar";
import { mapGetters } from 'vuex';
import {OK} from "../../util";
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
		Sidebar
	},
	data() {
		return {
			loading: false,           //ローディング
			likedProducts: {},        //お気に入りした商品
			purchasedProducts: {},    //購入した商品
			wasPurchasedProducts: {}, //購入された商品
			canceledProducts: {},     //キャンセルした商品
			wasCanceledProducts: {},  //キャンセルされた商品
			reviewedShopUsers: {},    //出品者へのレビュー（利用者）
			postedProducts: {},       //出品した商品
			reviewedUsers: {}         //購入者へのレビュー（コンビニ）
		}
	},
	computed: {
		...mapGetters({
			isShopUser: 'auth/isShopUser',
		})
	},
	methods: {
		async getLikedProducts() { //お気に入りした商品(利用者)
			this.loading   = true; //ローディングを表示する
			const response = await axios.get(`/api/mypage/${this.id}/liked`);
			this.loading   = false; //API通信が終わったらローディングを非表示にする
		
			if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
				this.$store.commit('error/setCode', response.status);
				return false; //後続の処理を抜ける
			}
			this.likedProducts = response.data; //responseデータをプロパティに代入
			console.log('お気に入りした商品一覧');
			console.log(response.data);
		},
		async getPurchasedProducts() { //購入した商品(利用者)
			this.loading   = true; //ローディングを表示する
			const response = await axios.get(`/api/mypage/${this.id}/purchased`);
			this.loading   = false; //API通信が終わったらローディングを非表示にする
			
			// if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
			// 	this.$store.commit('error/setCode', response.status);
			// 	return false; //後続の処理を抜ける
			// }
			this.purchasedProducts = response.data; //responseデータをプロパティに代入
			console.log('購入した商品一覧');
			console.log(response.data);
		},
		async getCanceledProducts() { //キャンセルした商品(利用者)
			this.loading   = true; //ローディングを表示する
			const response = await axios.get(`/api/mypage/${this.id}/canceled`);
			this.loading   = false; //API通信が終わったらローディングを非表示にする
			
			// if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
			// 	this.$store.commit('error/setCode', response.status);
			// 	return false; //後続の処理を抜ける
			// }
			this.canceledProducts = response.data; //responseデータをプロパティに代入
			console.log('キャンセルした商品');
			console.log(response.data);
		},
		async getReviewedShopUsers() { //レビューした出品者(利用者)
			this.loading   = true; //ローディングを表示する
			const response = await axios.get(`/api/mypage/${this.id}/reviewedShopUser`);
			this.loading   = false; //API通信が終わったらローディングを非表示にする
			
			// if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
			// 	this.$store.commit('error/setCode', response.status);
			// 	return false; //後続の処理を抜ける
			// }
			this.reviewedShopUsers = response.data; //responseデータをプロパティに代入
		},
		async getPostedProducts() { //投稿した商品(コンビニユーザー)
			this.loading   = true; //ローディングを表示する
			const response = await axios.get(`/api/mypage/${this.id}/posted`);
			this.loading   = false; //API通信が終わったらローディングを非表示にする
			
			// if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
			// 	this.$store.commit('error/setCode', response.status);
			// 	return false; //後続の処理を抜ける
			// }
			this.postedProducts = response.data; //responseデータをプロパティに代入
		},
		async getWasPurchasedProducts() { //購入された商品(コンビニユーザー)
			this.loading   = true; //ローディングを表示する
			const response = await axios.get(`/api/mypage/${this.id}/wasPurchased`);
			this.loading   = false; //API通信が終わったらローディングを非表示にする
			
			// if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
			// 	this.$store.commit('error/setCode', response.status);
			// 	return false; //後続の処理を抜ける
			// }
			this.wasPurchasedProducts = response.data; //responseデータをプロパティに代入
			console.log('購入された記事');
			console.log(this.wasPurchasedProducts);
		},
	},
	watch: {
		$route: { //$routerを監視してページが変わったときにメソッドが実行されるようにする
			async handler() {
				this.isShopUser ? await this.getWasPurchasedProducts() : await this.getPurchasedProducts();
				if(!this.isShopUser) await this.getLikedProducts();
				await this.getCanceledProducts();
				await this.getReviewedShopUsers();
				await this.getPostedProducts();
				
				// await this.getLikedProducts();
				// await this.getPurchasedProducts();
				// await this.getCanceledProducts();
				// await this.getReviewedShopUsers();
				// await this.getPostedProducts();
				// await this.getWasPurchasedProducts();
			},
			immediate: true //immediateをtrueにすると、コンポーネントが生成されたタイミングでも実行する
		}
	}
}
</script>
