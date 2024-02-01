<template>
	<div class="c-card p-review">
		<!--レビュー詳細画面のリンク-->
		<router-link class="c-card__link"
								 :to="{ name: 'review.detail',
												params: {
								 					s_id: review.sender_id.toString(),
								 					r_id: review.receiver_id.toString()
								 				}}" />
		
		<div class="p-review__user-info">
			<!-- 利用者ユーザーの画像	-->
			<img class="c-icon p-review__icon"
					 :src="review.sender_image"
					 v-show="isShopUser"
					 v-if="review.sender_image"
					 alt="">
			<img class="c-icon p-review__icon"
					 src="/storage/images/no-image.png"
					 v-show="isShopUser"
					 v-else
					 alt="">
			<!-- コンビニユーザーの画像	-->
			<img class="c-icon p-review__icon"
					 :src="review.receiver_image"
					 v-show="!isShopUser"
					 v-if="review.receiver_image"
					 alt="">
			<img class="c-icon p-review__icon"
					 src="/storage/images/no-image.png"
					 v-show="!isShopUser"
					 v-else
					 alt="">
			<div>
				<!-- レビュー相手の名前	-->
				<div class="p-review__name">
					<span v-show="isShopUser">{{ review.sender_name }}</span>
					<span v-show="!isShopUser">{{ review.receiver_name }}</span>
				</div>
				<!-- ユーザーの評価 -->
				<div class="p-review__recommendation">{{ review.recommend }}</div>
			</div>
		</div>
		
		<!-- レビュータイトル -->
		<div class="p-review__review-title">{{ review.title }}</div>
		<!-- レビューの内容 -->
		<div class="p-review__detail">{{ review.detail }}</div>
		
		<!-- ボタン	-->
		<slot name="btn"></slot>
	</div>
</template>

<script>
import { mapGetters } from 'vuex';
export default {
	name: "Review",
	props: {
		review: {
			type: Object,
			required: true
		}
	},
	computed: {
		...mapGetters({
			isShopUser: 'auth/isShopUser',
		})
	}
}
</script>
