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
			<img class="c-icon p-review__icon" :src="imageSrc" alt="">
			<div>
				<!-- レビュー相手の名前	-->
				<div class="p-review__name">{{ reviewName }}</div>
				<!-- ユーザーの評価 -->
				<div class="p-review__recommendation">{{ review.recommend }}</div>
			</div>
		</div>
		<!--<div class="p-review__userInfo">-->
		<!--	&lt;!&ndash; アイコン &ndash;&gt;-->
		<!--	<img class="c-icon p-review__icon"-->
		<!--			 :src="review.sender_image"-->
		<!--			 v-if="review.sender_image"-->
		<!--			 alt="">-->
		<!--	&lt;!&ndash; no-img &ndash;&gt;-->
		<!--	<img class="c-icon p-review__icon"-->
		<!--			 src="/storage/images/no-image.png"-->
		<!--			 v-else-->
		<!--			 alt="">-->
		<!--	<div>-->
		<!--		&lt;!&ndash; レビュー相手の名前	&ndash;&gt;-->
		<!--		<div class="p-review__name">{{ review.sender_name }}</div>-->
		<!--		&lt;!&ndash; ユーザーの評価 &ndash;&gt;-->
		<!--		<div class="p-review__recommendation">{{ review.recommend }}</div>-->
		<!--	</div>-->
		<!--</div>-->
		
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
			userId: 'auth/userId', // ログインユーザーのIDを取得
		}),
		imageSrc() { //投稿者=ログインユーザーなら出品者の画像を表示、それ以外は投稿者の画像を表示
			let image = this.review.sender_id === this.userId ? this.review.receiver_image : this.review.sender_image;
			return image || '/storage/images/no-image.png'; //画像がなければno-imageを表示
		},
		reviewName() {
			return this.review.sender_id === this.userId ? this.review.receiver_name : this.review.sender_name;
		}
	}
}
</script>
