<template>
	<div class="c-card p-review">
		<!--レビュー詳細画面のリンク-->
		<router-link class="c-card__link"
								 :to="{ name: 'review.detail',
												params: {
								 					s_id: review.sender_id.toString(),
								 					r_id: review.receiver_id.toString()
								 				}}" />
		
		<div class="p-review__userInfo">
			<!-- アイコン -->
			<img class="c-icon p-review__icon"
					 :src="review.sender_image"
					 v-if="review.sender_image"
					 alt="">
			<!-- no-img -->
			<img class="c-icon p-review__icon"
					 src="/storage/images/no-image.png"
					 v-else
					 alt="">
			<div>
				<!-- レビュー相手の名前	-->
				<div class="p-review__name">{{ review.sender_name }}</div>
				<!-- ユーザーの評価 -->
				<div class="p-review__recommendation">{{ review.recommend }}</div>
			</div>
		</div>
		
		<!-- レビュータイトル -->
		<div class="p-review__reviewTitle">{{ review.title }}</div>
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
			isShopUser: 'auth/isShopUser', //コンビニユーザーならtrueを返す
		})
	}
}
</script>
