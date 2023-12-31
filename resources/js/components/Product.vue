<template>
	<div class="c-card p-product">
		<!-- 詳細画面のリンク -->
		<router-link class="c-card__link"
								 :to="{ name: 'product.detail', params: { id: product.id.toString() }}"/>
		
		<!-- 商品の画像	-->
		<img :src="product.image"
				 alt=""
				 class="p-product__img">
		
		<!-- カードボディ -->
		<div class="p-product__card-body">
			<!-- 商品の名前	-->
			<div class="p-product__name">{{ product.name }}</div>
			<!-- 料金	-->
			<div class="p-product__price">{{ product.price | numberFormat }}</div>
			<!-- 賞味期限 -->
			<div class="p-product__expire">{{ product.expire }}</div>
			<!-- ユーザーの情報	-->
			<div class="p-product__user-info">
				<!-- 投稿したユーザーの画像	-->
				<img class="c-icon p-product__icon"
						 v-if="product.user_image"
						 :src="product.user_image"
						 alt="">
				<img class="c-icon p-product__icon"
						 v-else
						 :src="'images/no-image.png'"
						 alt="">
				<div class="p-product__user-info--right">
					<!-- 投稿したユーザー名 -->
					<div class="p-product__usr-name">{{ product.user_name }}</div>
					<div class="c-flex u-space-between">
						<!-- 投稿日	-->
						<div class="p-product__date">{{ product.created_at | moment }}</div>
						<!-- カテゴリー名	-->
						<div class="p-product__category">{{ product.category_name }}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--<div class="c-card p-product">-->
	<!--	&lt;!&ndash; 詳細画面のリンク &ndash;&gt;-->
	<!--	<router-link class="c-card__link"-->
	<!--							 :to="{ name: 'product.detail', params: { id: product.id.toString() }}"/>-->
	<!--	-->
	<!--	&lt;!&ndash; 商品の画像	&ndash;&gt;-->
	<!--	<img :src="product.image"-->
	<!--			 alt=""-->
	<!--			 class="p-product__img">-->
	<!--	-->
	<!--	&lt;!&ndash; カードボディ &ndash;&gt;-->
	<!--	<div class="p-product__card-body">-->
	<!--		&lt;!&ndash; 商品の名前	&ndash;&gt;-->
	<!--		<div class="p-product__name">{{ product.name }}</div>-->
	<!--		&lt;!&ndash; 商品の概要	&ndash;&gt;-->
	<!--		<div class="p-product__summary">{{ product.summary }}</div>-->
	<!--		&lt;!&ndash; 平均評価	&ndash;&gt;-->
	<!--		<star-rating class="p-product__avg"-->
	<!--								 :active-color="['#f96204']"-->
	<!--								 :inactive-color="'#fed4ba'"-->
	<!--								 :star-size="18"-->
	<!--								 :increment=".5"-->
	<!--								 :show-rating="true"-->
	<!--								 :read-only="true"-->
	<!--								 :rating="product.star_avg" />-->
	<!--		<div class="p-product__flex">-->
	<!--			&lt;!&ndash; 料金	&ndash;&gt;-->
	<!--			<div class="p-product__price">{{ product.price | numberFormat }}</div>-->
	<!--			-->
	<!--			&lt;!&ndash; レビューの件数	&ndash;&gt;-->
	<!--			<div class="p-product__review">-->
	<!--				<font-awesome-icon :icon="['far','comment']" />-->
	<!--				{{ product.reviews_count }}件-->
	<!--			</div>-->
	<!--		</div>-->
	<!--		&lt;!&ndash; ユーザーの情報	&ndash;&gt;-->
	<!--		<div class="p-product__user-info">-->
	<!--			&lt;!&ndash; 投稿したユーザーの画像	&ndash;&gt;-->
	<!--			<img class="c-icon p-product__icon"-->
	<!--					 v-if="product.user_image"-->
	<!--					 :src="product.user_image"-->
	<!--					 alt="">-->
	<!--			<img class="c-icon p-product__icon"-->
	<!--					 v-else-->
	<!--					 :src="'images/no-image.png'"-->
	<!--					 alt="">-->
	<!--			<div class="p-product__user-info&#45;&#45;right">-->
	<!--				&lt;!&ndash; 投稿したユーザー名 &ndash;&gt;-->
	<!--				<div class="p-product__usr-name">{{ product.user_name }}</div>-->
	<!--				<div class="c-flex u-space-between">-->
	<!--					&lt;!&ndash; 投稿日	&ndash;&gt;-->
	<!--					<div class="p-product__date">{{ product.created_at | moment }}</div>-->
	<!--					&lt;!&ndash; カテゴリー名	&ndash;&gt;-->
	<!--					<div class="p-product__category">{{ product.category_name }}</div>-->
	<!--				</div>-->
	<!--			</div>-->
	<!--		</div>-->
	<!--	-->
	<!--	</div>-->
	<!--</div>-->
</template>

<script>
export default {
	name: "Product",
	props:{
		product: {
			type: Object,
			required: true
		}
	}
}
</script>

<style scoped>

</style>
