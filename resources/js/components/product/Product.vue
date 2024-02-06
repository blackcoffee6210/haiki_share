<template>
	<div class="c-card p-product">
		<!-- 詳細画面のリンク -->
		<router-link class="c-card__link"
								 :to="{ name: 'product.detail',
								  			params: { id: product.id.toString() }}"/>
		
		<!-- SOLDバッジ -->
		<div class="c-badge" v-show="product.is_purchased">
			<div class="c-badge__sold">SOLD</div>
		</div>
		<!-- 商品の画像	-->
		<div class="p-product__img-container">
			<img :src="product.image"
					 alt=""
					 class="p-product__img">
			<div class="p-product__heart">
				<font-awesome-icon :icon="['fas', 'heart']"
													 color="#ff6f80" />
				{{ product.likes_count }}
			</div>
			<slot name="cancel_count"></slot>
		</div>
		
		<!-- カードボディ -->
		<div class="p-product__card-body">
			<div class="p-product__container">
				<!-- 商品の名前	-->
				<div class="p-product__name">
					{{ product.name }}
				</div>
				<!-- 料金	-->
				<div class="p-product__price">
					{{ product.price | numberFormat }}
				</div>
			</div>
			
			<!-- 賞味期限 -->
			<div class="p-product__expire">
				<div>賞味期限</div>
				<div v-if="expireDate"><!-- 賞味期限が過ぎたときの表示 -->
					<span class="u-color__main u-font-bold">切れ</span>
					<span class="p-product__expire__date">
						{{ product.expire | fromExpire }}
					</span>
					日
				</div>
				<div v-else><!-- 賞味期限内のときの表示 -->
					残り
					<span class="p-product__expire__date">
						{{ product.expire | momentExpire }}
					</span>
					 日
				</div>
			</div>

			<!-- ユーザーの情報	-->
			<div class="p-product__user-info">
				<!-- 出品したユーザーの画像	-->
				<img class="c-icon p-product__icon"
						 v-if="product.user_image"
						 :src="product.user_image"
						 alt="">
				<img class="c-icon p-product__icon"
						 v-else
						 src="/storage/images/no-image.png"
						 alt="">
				<div class="p-product__user-info--right">
					<!-- 出品したユーザー名 -->
					<div class="p-product__usr-name">{{ product.user_name }}</div>
					<div class="c-flex u-space-between">
						<!-- 出品日	-->
						<div class="p-product__date">{{ product.created_at | moment }}</div>
						<!-- カテゴリー名	-->
						<!--<div class="p-product__category">{{ product.category.name }}</div>-->
						<div class="p-product__category">{{ product.category_name }}</div>
					</div>
				</div>
			</div>
			
			<!-- ボタン	-->
			<slot name="btn"></slot>
		</div>
	</div>

</template>

<script>
import moment from 'moment';
export default {
	name: "Product",
	props:{
		product: {
			type: Object,
			required: true
		}
	},
	computed: {
		expireDate() { //商品の賞味期限が過ぎているかどうかを返す
			let dt = moment().format('YYYY-MM-DD');
			if(this.product.expire <= dt) {
				return true;
			}
		}
	}
}
</script>
