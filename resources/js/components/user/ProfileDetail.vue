<template>
	<main class="l-main">
		<div class="p-profile-detail">
			<h2 class="c-title p-profile-detail__a">プロフィール詳細</h2>
			
			<!-- ユーザー情報	-->
			<div class="p-profile-detail__container">
				<img :src="user.image" class="p-profile-detail__image" alt="">
				<div>
					<div class="p-profile-detail__name">
						{{ user.name }}
						<span v-show="shopUser">{{ user.branch }}</span>
					</div>
					<!-- 住所（コンビニユーザーのみ表示） -->
					<div class="p-profile-detail__address" v-show="shopUser">
						{{ user.prefecture_name }}
						{{ user.address }}
					</div>
					<!-- todo: ユーザー評価取得 -->
					<div class="p-profile-detail__recommend" v-show="shopUser">
						{{ user.recommend }}
					</div>
				</div>
			</div>
			
			<!-- 出品数 -->
			<div class="p-profile-detail__container u-space-between">
				<div>
					<span class="p-profile-detail__count">{{ products.length }}</span>
					<span class="p-profile-detail__text"
								v-show="shopUser">出品数
					</span>
					<span class="p-profile-detail__text"
								v-show="!shopUser">購入数
					</span>
					
					<span class="p-profile-detail__count u-ml15">{{ reviews.length }}</span>
					<span class="p-profile-detail__text"
								v-show="shopUser">レビューされた数
					</span>
					<span class="p-profile-detail__text"
								v-show="!shopUser">レビューした数
					</span>
				</div>
				<!-- プロフィール編集	-->
				<router-link class="c-btn p-profile-detail__btn"
										 v-show="isMyProfile"
										 :to="{ name: 'user.editProfile',
									  			params: { id: id.toString() }}" >プロフィール編集
				</router-link>
			</div>
			
			<div class="p-profile-detail__introduce">{{ user.introduce }}</div>
			
			<!-- コンビニユーザーの出品した商品 -->
			<div v-show="shopUser">
				<input type="checkbox"
							 id="sale"
							 class="c-checkbox u-mb2"
							 v-show="products.length > 0"
							 v-model="showSale">
				<label for="sale"
							 v-show="products.length > 0"
							 class="p-profile-detail__label">販売中のみ表示
				</label>
				<div class="p-profile-detail__product-container">
					<Product v-show="!loading"
									 v-for="product in filteredProducts"
									 :key="product.id"
									 :product="product" />
				</div>
			</div>
			
			<!-- 投稿されたレビュー	-->
			<div v-show="shopUser">
				<div class="u-font-bold u-mt30" v-show="reviews.length > 0">投稿されたレビュー</div>
				<div class="p-profile-detail__review-container">
					<Review v-show="!loading"
									v-for="review in reviews"
									:key="review.id"
									:review="review" />
				</div>
			</div>
			
			<!-- 利用者の購入した商品 -->
			<div v-show="!shopUser">
				<div class="u-font-bold">購入した商品</div>
				<div class="p-profile-detail__product-container">
					<Product v-show="!loading"
									 v-for="product in products"
									 :key="product.id"
									 :product="product" />
				</div>
			</div>
			
			<!-- 投稿したレビュー	-->
			<div v-show="!shopUser">
				<div class="u-font-bold u-mt30">投稿したレビュー</div>
				<div class="p-profile-detail__review-container">
					<Review v-show="!loading"
									v-for="review in reviews"
									:key="review.id"
									:review="review" />
				</div>
			</div>
			
		</div>
	</main>
</template>

<script>
import Product from "../product/Product";
import Review  from "../review/Review";
import { OK }  from "../../util";
import { mapGetters } from 'vuex';
export default {
	name: "ProfileDetail",
	props: {
		id: {
			type: String,
			required: true
		}
	},
	components: {
		Product,
		Review
	},
	data() {
		return {
			user: {},
			reviews: [],
			products: [],
			loading: false,
			showSale: false
		}
	},
	computed: {
		...mapGetters({
			userId: 'auth/userId'
		}),
		shopUser () {
			return this.user.group === 2;
		},
		isMyProfile () {
			return this.id == this.userId;
		},
		filteredProducts() { //絞り込んだ商品を返す
			let newProducts = []; //絞り込み後の商品を格納する新しい配列
			
			for(let i = 0; i < this.products.length; i++) { //カテゴリーが選択されたら、カテゴリーIDが一致する商品だけを表示する
				let isShow = true; //表示対象かどうかを判定するフラグ
				
				if (this.showSale &&
						this.products[i].is_purchased) { //「販売中のみ表示」チェックあり、かつ購入済み商品は非表示にする
					isShow = false;
				}
				if(isShow) { //表示対象の商品を新しい配列に詰めていく
					newProducts.push(this.products[i]);
				}
			}
			return newProducts; //絞り込み後の商品を返す
		},
	},
	methods: {
		async getUser() {
			const response = await axios.get(`/api/users/${this.id}`); //API接続
			
			if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
				this.$store.commit('error/setCode', response.status);
				return false; //後続の処理を抜ける
			}
			this.user = response.data; //responseデータをプロパティに代入
			console.log('getUserの中身')
			console.log(response.data);
			if(!this.user) { //存在しなかったら商品一覧画面に遷移する
				this.$router.push({name: 'index'})
			}
		},
		async getPurchasedProducts() { //利用者ユーザーの購入した商品を取得
			const response = await axios.get(`/api/users/${this.user.id}/purchased`); //API接続
			
			if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
				this.$store.commit('error/setCode', response.status);
				return false; //後続の処理を抜ける
			}
			this.products = response.data; //responseデータをプロパティに代入
			console.log('getPurchasedProductの中身')
			console.log(response.data);
		},
		async getReviews(){ //利用者が投稿したレビューを取得する
			const response = await axios.get(`/api/users/${this.user.id}/reviewed`); //API接続
			
			if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
				this.$store.commit('error/setCode', response.status);
				return false; //後続の処理を抜ける
			}
			this.reviews = response.data;
			console.log('getReviewの中身')
			console.log(response.data);
		},
		async getPostedProducts() { //コンビニユーザーの出品した商品を取得
			const response = await axios.get(`/api/users/${this.user.id}/posted`); //API接続
			
			if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
				this.$store.commit('error/setCode', response.status);
				return false; //後続の処理を抜ける
			}
			this.products = response.data; //responseデータをプロパティに代入
			console.log('getPostedProductsの中身')
			console.log(response.data);
		},
		async getWasReviews(){ //投稿されたレビューを取得する
			const response = await axios.get(`/api/users/${this.user.id}/wasReviewed`); //API接続
			
			if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
				this.$store.commit('error/setCode', response.status);
				return false; //後続の処理を抜ける
			}
			this.reviews = response.data;
			console.log('getWasReviewsの中身');
			console.log(response.data);
		}
	},
	watch: {
		$route: {
			async handler() {
				await this.getUser();
				switch(this.user.group) {
					case 1: //取得したユーザーが利用者なら
						await this.getReviews();
						await this.getPurchasedProducts();
						break;
					case 2: //取得したユーザーがコンビニユーザーなら
						await this.getWasReviews();
						await this.getPostedProducts();
						break;
				}
				
				// if(this.user.group === 1) { //取得したユーザーが利用者なら
				// 	await this.getReviews();
				// 	await this.getPurchasedProducts();
				// } else if(this.user.group === 2) { //取得したユーザーがコンビニユーザーなら
				// 	await this.getWasReviews();
				// 	await this.getPostedProducts();
				// }
				
			},
			immediate: true
		}
	}
}
</script>
