<template>
	<main class="l-main">
		<div class="p-review-detail">
			<h2 class="c-title p-review-detail__title">レビュー詳細</h2>
			
			
			<div class="p-review-detail__user-info">
				<img class="c-icon p-review-detail__image"
						 v-show="isShopUser"
						 :src="review.sender_image" alt="">
				<img class="c-icon p-review-detail__image"
						 v-show="!isShopUser"
						 :src="review.receiver_image" alt="">
				<div>
					<!-- 投稿した利用者名	-->
					<div class="p-review-detail__name">
						<span v-show="isShopUser">{{ review.sender_name }}</span>
						<span v-show="!isShopUser">{{ review.receiver_name }}</span>
					</div>
					<!-- 投稿日	-->
					<div class="p-review-detail__send_date">
						{{ review.created_at | moment }}
					</div>
				</div>
			</div>
			
			<!-- ユーザーの評価	-->
			<div class="p-review-detail__recommend">{{ review.recommend }}</div>
			<div class="p-review-detail__container">
				<!-- レビュータイトル	-->
				<div class="p-review-detail__review-title">{{ review.title }}</div>
				<!-- 編集ボタン(利用者) -->
				<router-link class="c-btn p-review-detail__btn"
										 v-show="!isShopUser"
										 :to="{ name: 'review.edit',
													params: {
														s_id: review.sender_id.toString(),
														r_id: review.receiver_id.toString()
												}}">編集する
				</router-link>
			</div>
			<!-- レビューの内容	-->
			<div class="p-review-detail__detail">{{ review.detail }}</div>
			
			<!-- 出品者の購入されていない商品を表示 -->
			<div class="c-title p-product-detail__title"
					 v-show="otherProducts.length > 0">この出品者の商品
			</div>
			<div class="p-review-detail__other-container">
				<Product v-for="product in otherProducts"
								 :key="product.id"
								 v-if="!product.is_purchased"
								 :product="product" />
			</div>
		</div>
	</main>
</template>

<script>
import { OK }         from "../../util";
import Loading        from "../Loading";
import Product        from "../product/Product";
import { mapGetters } from 'vuex';

export default {
	name: "ReviewDetail",
	props: {
		s_id: {
			type: String,
			required: true
		},
		r_id: {
			type: String,
			required: true
		}
	},
	components: {
		Loading,
		Product
	},
	data() {
		return {
			review: {},
			otherProducts: []
		}
	},
	computed: {
		...mapGetters({
			isShopUser: 'auth/isShopUser',
		}),
	},
	methods: {
		async getReview() { //レビュー取得
			const response = await axios.get(`/api/reviews/${this.s_id}/${this.r_id}`);
			
			if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			this.review = response.data[0];
			console.log('getReviewの中身');
			console.log(response.data[0]);
			console.log('r_idの中身');
			console.log(this.r_id);
		},
		async getOtherProducts() {
			const response = await axios.get(`/api/reviews/${this.r_id}/otherProducts`);
			
			if(response.status !== OK) {
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			this.otherProducts = response.data;
			console.log('otherProductsの中身');
			console.log(response.data);
		}
	},
	watch: {
		$route: {
			async handler() {
				await this.getReview();
				if(!this.isShopUser) await this.getOtherProducts();
			},
			immediate: true
		}
	}
}
</script>
