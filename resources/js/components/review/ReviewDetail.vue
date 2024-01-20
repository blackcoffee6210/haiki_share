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
			<!-- レビュータイトル	-->
			<div class="p-review-detail__review-title">{{ review.title }}</div>
			<!-- レビューの内容	-->
			<div class="p-review-detail__detail">{{ review.detail }}</div>
		</div>
	</main>
</template>

<script>
import { OK } from "../../util";
import { mapGetters } from 'vuex';

export default {
	name: "ReviewDetail",
	props: {
		s_id: String, //利用者のユーザーid
		r_id: String, //コンビニユーザーid
		required: true
	},
	data() {
		return {
			review: {}
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
			console.log(response.data[0]);
		},
	},
	watch: {
		$route: {
			async handler() {
				this.getReview();
			},
			immediate: true
		}
	}
}
</script>
