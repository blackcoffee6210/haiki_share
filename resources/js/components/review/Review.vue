<template>
	<div class="c-card p-review">
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
		
		<!-- ボタン	-->
		<slot name="btn"></slot>
	</div>
</template>

<script>
export default {
	name: "Review",
	props: {
		review: {
			type: Object,
			required: true
		}
	}
}
</script>
