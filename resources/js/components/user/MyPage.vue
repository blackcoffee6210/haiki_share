<template>
	<div class="l-main">
		<!-- メインコンテンツ -->
		<main class="l-main__2column">
			<div class="p-mypage">
				
				<!-- ローディング -->
				<Loading v-show="loading" />
				
				<div v-show="!loading">
					
					<!-- 購入された商品 -->
					<!--todo: ユーザーがコンビニの時に表示-->
					<div class="p-mypage__title-container">
						<h2 class="c-title p-mypage__title">購入された商品</h2>
						<!-- 全件表示のリンク -->
						<a class="p-mypage__link">全件表示</a>
						<!--<router-link class="p-mypage__link"-->
						<!--						 :to="{ name: 'user.purchased', params: { id: id.toString() }}"-->
						<!--						 v-if="purchasedProducts.length">全件表示-->
						<!--</router-link>-->
					</div>
					
					<!-- 購入した商品がなければ表示する -->
					<div v-if="!purchasedProducts.length"
							 class="p-mypage__no-Product">購入された商品はありません
					</div>
					
					<section v-else class="p-mypage__section">
						<!-- カード（5件表示） -->
						<div class="c-card p-mypage__card"
								 v-for="product in purchasedProducts"
								 :key="product.id">
							<!-- カードリンク -->
							<router-link class="c-card__link"
													 :to="{ name: 'product.detail',
																	params: { id: product.id.toString() }}" />
							<img class="p-mypage__image"
									 :src="product.image"
									 alt="">
							<div class="p-mypage__body">
								<div class="p-mypage__product-name">
									{{ product.name }}
								</div>
								<div class="p-mypage__expire">
									{{ product.expire | momentExpire }}
								</div>
								<div class="p-mypage__price">
									{{ product.price | numberFormat }}
								</div>
								<!-- ユーザー情報 -->
								<div class="p-mypage__user-info">
									<img class="c-icon p-mypage__icon"
											 v-if="product.user_image"
											 :src="product.user_image"
											 alt="">
									<img class="c-icon p-mypage__icon"
											 v-else
											 :src="'images/no-image.png'"
											 alt="">
									<div class="u-w70">
										<div class="p-mypage__text">{{ product.user_name }}</div>
										<div class="p-mypage__text">{{ product.created_at | moment }}</div>
									</div>
								</div>
							</div>
						</div>
					</section>
					
					<!-- 購入した商品 -->
					<!--todo: ユーザーが利用者の時に表示-->
					<div class="p-mypage__title-container">
						<h2 class="c-title p-mypage__title">購入した商品</h2>
						<!-- 全件表示のリンク -->
						<a class="p-mypage__link">全件表示</a>
						<!--<router-link class="p-mypage__link"-->
						<!--						 :to="{ name: 'user.purchased', params: { id: id.toString() }}"-->
						<!--						 v-if="purchasedProducts.length">全件表示-->
						<!--</router-link>-->
					</div>
					
					<!-- 購入した商品がなければ表示する -->
					<div v-if="!purchasedProducts.length"
							 class="p-mypage__no-Product">購入した商品はありません
					</div>
					
					<section v-else class="p-mypage__section">
						<!-- カード（5件表示） -->
						<div class="c-card p-mypage__card"
								 v-for="product in purchasedProducts"
								 :key="product.id">
							<!-- カードリンク -->
							<router-link class="c-card__link"
													 :to="{ name: 'product.detail',
																	params: { id: product.id.toString() }}" />
							<img class="p-mypage__image"
									 :src="product.image"
									 alt="">
							<div class="p-mypage__body">
								<div class="p-mypage__product-name">
									{{ product.name }}
								</div>
								<div class="p-mypage__expire">
									{{ product.expire | momentExpire }}
								</div>
								<div class="p-mypage__price">
									{{ product.price | numberFormat }}
								</div>
								<!-- ユーザー情報 -->
								<div class="p-mypage__user-info">
									<img class="c-icon p-mypage__icon"
											 v-if="product.user_image"
											 :src="product.user_image"
											 alt="">
									<img class="c-icon p-mypage__icon"
											 v-else
											 :src="'images/no-image.png'"
											 alt="">
									<div class="u-w70">
										<div class="p-mypage__text">{{ product.user_name }}</div>
										<div class="p-mypage__text">{{ product.created_at | moment }}</div>
									</div>
								</div>
							</div>
						</div>
					</section>
					
					
					<!-- お気に入りした商品 -->
					<!--todo: ユーザーが利用者のとき表示-->
					<div class="p-mypage__title-container">
						<h2 class="c-title p-mypage__title">お気に入りした商品</h2>
						<!-- 全件表示のリンク -->
						<router-link class="p-mypage__link"
												 :to="{ name: 'user.liked',
																params: { id: id.toString() }}"
												 v-if="likedProducts.length">全件表示
						</router-link>
					</div>
					
					<!-- 商品がなければ表示する -->
					<div v-if="!likedProducts.length"
							 class="p-mypage__no-Product">お気に入りした商品はありません
					</div>
					<section v-else class="p-mypage__section">
						<!-- カード（5件表示） -->
						<div class="c-card p-mypage__card"
								 v-for="product in likedProducts"
								 :key="product.id">
							<!-- カードリンク -->
							<router-link class="c-card__link"
													 :to="{ name: 'product.detail',
																	params: { id: product.id.toString() }
													 }" />
							<img class="p-mypage__image"
									 :src="product.image"
									 alt="">
							<div class="p-mypage__body">
								<div class="p-mypage__product-name">
									{{ product.name }}
								</div>
								<div class="p-mypage__expire">
									{{ product.expire | momentExpire }}
								</div>
								<div class="p-mypage__price">
									{{ product.price | numberFormat }}
								</div>
								<!-- ユーザー情報 -->
								<div class="p-mypage__user-info">
									<img class="c-icon p-mypage__icon"
											 :src="product.user_image"
											 alt="">
									<div class="u-w70">
										<div class="p-mypage__text">{{ product.user_name }}</div>
										<div class="p-mypage__text">{{ product.created_at | moment }}</div>
									</div>
								</div>
							</div>
						</div>
					</section>
					
					
					<!--todo: ユーザーがコンビニの時に表示-->
					<!-- 出品した商品 -->
					<div class="p-mypage__title-container">
						<h2 class="c-title p-mypage__title">出品した商品</h2>
						<!-- 全件表示のリンク -->
						<router-link class="p-mypage__link"
												 :to="{ name: 'user.posted',
																params: { id: id.toString() }}"
												 v-if="postedProducts.length">全件表示
						</router-link>
					</div>
					
					<!-- 商品がなければ表示する -->
					<div v-if="!postedProducts.length"
							 class="p-mypage__no-Product">出品した商品はありません
					</div>
					<section class="p-mypage__section">
						<!-- カード（5件表示） -->
						<div class="c-card p-mypage__card"
								 v-for="product in postedProducts"
								 :key="product.id">
							<!-- カードリンク -->
							<router-link class="c-card__link"
													 :to="{ name: 'product.detail',
																	params: { id: product.id.toString() }}" />
							<img class="p-mypage__image"
									 :src="product.image"
									 alt="">
							<div class="p-mypage__body">
								<div class="p-mypage__product-name">
									{{ product.name }}
								</div>
								<div class="p-mypage__expire">
									{{ product.expire | momentExpire }}
								</div>
								<div class="p-mypage__price">
									{{ product.price | numberFormat }}
								</div>
								<!-- ユーザー情報 -->
								<div class="p-mypage__user-info">
									<img class="c-icon p-mypage__icon"
											 :src="product.user_image"
											 alt="">
									<div class="u-w70">
										<div class="p-mypage__text">
											{{ product.user_name }}
										</div>
										<div class="p-mypage__text">
											{{ product.created_at | moment }}
										</div>
									</div>
								</div>
							</div>
						</div>
					</section>
					
					
					<!--todo: ユーザーがコンビニのときに表示-->
					<!-- 購入者からのレビュー一覧 -->
					<div class="p-mypage__title-container">
						<h2 class="c-title p-mypage__title">購入者からのレビュー一覧</h2>
						<!-- 全件表示のリンク -->
						<!--todo: RouterLinkに変更-->
						<a class="p-mypage__link"
							 v-if="reviews.length">全件表示</a>
						<!--<router-link class="p-mypage__link"-->
						<!--						 :to="{ name: 'user.reviewed',-->
						<!--										params: { id: id.toString() }}"-->
						<!--						 v-if="reviewedProducts.length">全件表示-->
						<!--</router-link>-->
					</div>
					
					<!-- 商品がなければ表示する -->
					<div v-if="!reviews.length"
							 class="p-mypage__no-Product">レビューされた商品はありません
					</div>
					
					<section class="p-mypage__section--review">
						<div class="c-card p-mypage__card--review"
								 v-for="review in reviewedProducts"
								 :key="review.id">
							<!-- カードリンク -->
							<router-link class="c-card__link"
													 :to="{ name: 'review.reviewDetail',
																	params: { id: review.id.toString() }}" />
							<div class="p-mypage__product-info">
								<div class="p-mypage__card--left">
									<img class="p-mypage__image--review"
											 :src="review.Product_image"
											 alt="">
								</div>
								<div class="p-mypage__card--right">
									<div class="p-mypage__product-name--review">{{ review.Product_name }}</div>
									<div class="p-mypage__summary">{{ review.Product_summary }}</div>
									<div class="p-mypage__price">{{ review.Product_price | numberFormat }}</div>
								</div>
							</div>
							
							<div class="p-mypage__user-info--review">
								<img :src="review.user_image"
										 class="c-icon p-mypage__icon"
										 alt="">
								<div class="p-mypage__user-name--review">{{ review.user_name }}</div>
								<div class="p-mypage__date">{{ review.created_at | moment }}</div>
							</div>
							
							<div class="p-mypage__review-info">
								<div class="p-mypage__review-title">{{ review.title }}</div>
								<star-rating
										:increment=".5"
										:show-rating="false"
										:star-size="14"
										:read-only="true"
										:rating="review.star" />
								<read-more more-str="read more"
													 less-str="read less"
													 :text="review.comment"
													 link="#"
													 class="p-mypage__review-comment" :max-chars="maxChars" />
								<!--<div class="p-reviewed__comment">{{ review.comment }}</div>-->
							</div>
						</div>
					</section>
				
				</div>
			</div>
		</main>
		
		<!-- サイドバー -->
		<Sidebar :id="id" />
	
	</div>
</template>

<script>
import Loading from "../Loading";
import Sidebar from "../Sidebar";
import { mapGetters } from 'vuex';
export default {
	name: "MyPage",
	props: {
		id: {
			type: String,
			required: true
		}
	},
	components: {
		Loading,
		Sidebar
	},
	data() {
		return {
			loading: false,        //ローディング
			purchasedProducts: {}, //購入された商品
			likedProducts: {},     //お気に入りされた商品
			postedProducts: {},    //出品した商品
			reviews: {}            //購入者からのレビュー
		}
	},
	computed: {
		...mapGetters({
			isShopUser: 'auth/isShopUser',
		})
	}
}
</script>

<style scoped>

</style>
