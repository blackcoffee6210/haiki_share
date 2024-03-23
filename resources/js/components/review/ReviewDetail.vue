<template>
	<main class="l-main">
		<div class="p-reviewDetail">
			<Loading v-show="loading" />
			
			<div v-if="!loading && review">
				<h2 class="c-title p-reviewDetail__title">レビュー詳細</h2>
				
				<div class="p-reviewDetail__userInfo">
					<img :src="ReviewerImage" alt="" class="c-icon p-reviewDetail__image">
					<div>
						<!-- 投稿した利用者名	-->
						<div class="p-reviewDetail__name">{{ review.sender_name }}</div>
						<!-- 投稿日	-->
						<div class="p-reviewDetail__send_date">{{ review.created_at | moment }}</div>
					</div>
					<div class="p-reviewDetail__recommend">{{ review.recommend }}</div>
				</div>
				
				<!-- ユーザーの評価	-->
				<div class="p-reviewDetail__container">
					<!-- レビュータイトル	-->
					<div class="p-reviewDetail__reviewTitle">{{ review.title }}</div>
					<!-- 編集ボタン(利用者) -->
					<router-link v-if="isMyReview"
											 class="c-btn p-reviewDetail__btn"
											 :to="{ name: 'review.edit',
															params: {
																s_id: review.sender_id.toString(),
																r_id: review.receiver_id.toString()
														}}">編集する
					</router-link>
				</div>
				<!-- レビューの内容	-->
				<div class="p-reviewDetail__detail">{{ review.detail }}</div>
				
				<!-- 出品者の情報 -->
				<h2 class="c-title p-productDetail__title">出品者情報</h2>
				<div class="p-reviewDetail__userInfo">
					<!-- 詳細画面のリンク -->
					<router-link class="c-card__link"
											 v-if="review && review.receiver_id"
											 :to="{ name: 'user.detail', params: { id: review.receiver_id.toString() }}"/>
					<img :src="review.receiver_image || '/storage/images/no-image.png'" alt="" class="c-icon p-reviewDetail__image">
					<div>
						<div class="p-reviewDetail__name">{{ review.receiver_name }}</div>
						<div class="p-reviewDetail__branch">{{ review.receiver_branch }}</div>
					</div>
					
				</div>
				
				<!-- 出品者の購入されていない商品を表示 -->
				<h2 class="c-title p-productDetail__title" v-if="otherProducts.length > 0">この出品者の商品</h2>
				<div class="p-reviewDetail__productContainer">
					<Product v-for="product in otherProducts"
									 :key="product.id"
									 :product="product" />
				</div>
			</div>
		</div>
	</main>
</template>

<script>
import { OK }         from "../../util";
import Loading        from "../Loading";
import Product        from "../product/Product";
import { mapGetters, mapState } from 'vuex';

export default {
	name: "ReviewDetail",
	props: {
		s_id: { type: String, required: true },  //送信者ID
		r_id: { type: String, required: true },  //受信者ID
	},
	components: {
		Loading,
		Product
	},
	data() {
		return {
			loading: true,   //ローディング
			review: null,       //レビュー
			otherProducts: [] //他の商品
		}
	},
	computed: {
		...mapState({
			user: state => state.auth.user, //ログインユーザー
		}),
		...mapGetters({
			isShopUser: 'auth/isShopUser', //ログインしたユーザーがコンビニユーザーならtrueを返す
			userId: 'auth/userId', //ユーザーIDを取得
		}),
		isMyReview() {
			return this.s_id == this.userId; //自分のレビューならtrueを返す
		},
		ReviewerImage() { //レビュー投稿者の画像
			if (this.review && this.review.sender_image) {
				return this.review.sender_image;
			}
			return '/storage/images/no-image.png';
		},
	},
	async created() {
		await this.getReview();
		if(!this.isShopUser) await this.getOtherProducts(); //利用者ユーザーなら
	},
	methods: {
		async getReview() { //レビュー取得
			this.loading = true; //loadingをtrueにする
			
			try {
				const response = await axios.get(`/api/reviews/${this.s_id}/${this.r_id}`);
				
				if(response.status === OK) { //API通信が成功なら
					this.review = response.data;
					
				}else { //失敗なら
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
			}catch(error) {
				console.error('レビュー取得処理中にエラーが発生しました', error);
				
			}finally {
				this.loading = false; //loadingをfalseにする
			}
		},
		async getOtherProducts() { //レビューしたユーザーの他の商品を取得
			this.loading = true; //loadingをtrueにする
			
			try {
				const response = await axios.get(`/api/reviews/${this.r_id}/otherProducts`);
				
				if(response.status === OK) { //成功
					this.otherProducts = response.data;
					
				}else { //失敗
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
			}catch(error) {
				console.error('レビューしたユーザーの他の商品を取得中にエラーが発生しました', error);
				
			}finally {
				this.loading = false; //loadingをfalseにする
			}
		}
	},
	watch: {
		$route: {
			async handler() {
				await this.getReview();
				await this.getOtherProducts();
			},
			immediate: true
		}
	}
}
</script>
