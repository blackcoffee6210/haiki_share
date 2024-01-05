<template>
	<div class="l-main">
		<main class="l-main__2column">
			<div class="p-list">
				<h2 class="c-title p-list__title">
					<span v-show="isShopUser">購入された商品一覧</span>
					<span v-show="!isShopUser">購入した商品一覧</span>
				</h2>
				
				<!-- ローディング -->
				<Loading v-show="loading" />
				
				<!-- 商品がなければ表示する -->
				<div v-if="!products.length"
						 class="p-list__no-product">
					<span v-show="isShopUser">購入された商品はありません</span>
					<span v-show="!isShopUser">購入した商品はありません</span>
				</div>
				
				<div class="p-list__card-container" v-show="!loading">
				
			<!--		&lt;!&ndash; Productコンポーネント &ndash;&gt;-->
			<!--		<Product v-show="!loading"-->
			<!--						 v-for="product in products"-->
			<!--						 :key="product.id"-->
			<!--						 :product="product" />-->
			<!--	</div>-->
			<!--</div>-->
					
					<!-- カード -->
					<div class="c-card p-list__card"
							 v-for="product in products"
							 :key="product.id">
						<!-- 詳細画面のリンク -->
						<router-link class="c-card__link"
												 :to="{ name: 'product.detail',
												 				params: { id: product.id.toString() }}" />
						<!-- SOLDバッジ -->
						<div class="c-badge" v-show="product.is_purchased">
							<div class="c-badge__sold">SOLD</div>
						</div>
						<!-- 商品の画像	-->
						<img class="p-list__img"
								 :src="product.image"
								 alt="">
						<!-- カードボディ -->
						<div class="p-list__card-body">
							<div class="p-list__card-body__container">
								<!-- 商品の名前	-->
								<div class="p-list__name">{{ product.name }}</div>
								<!-- 料金	-->
								<div class="p-list__price">
									{{ product.price | numberFormat }}
								</div>
							</div>
							<!-- 賞味期限 -->
							<div class="p-list__expire">
								<div>賞味期限</div>
								<div>
									残り
									<span class="p-product__expire__date">
										{{ product.expire | momentExpire }}
									</span>
									日
								</div>
							</div>
							<div class="p-list__usr-info">
								<img :src="product.user_image"
										 alt=""
										 class="c-icon p-list__icon">
								<div>
									<div class="p-list__usr-name">
										{{ product.user_name }}
									</div>
									<div class="p-list__date">
										{{ product.created_at | moment }}
									</div>
								</div>
							</div>
							<!-- ボタン	-->
							<div class="p-list__btn-container">
								<!--<router-link class="c-btn p-list__btn p-list__btn&#45;&#45;detail"-->
								<!--						 :to="{ name: 'product.detail', params: { id: product.id.toString() }}">詳細を見る-->
								<!--</router-link>-->
								<!-- 購入キャンセルボタン -->
								<button class="c-btn c-btn--white p-list__btn p-list__btn--cancel"
												@click="cancel(product)">購入キャンセル
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
		
		<!-- サイドバー	-->
		<Sidebar :id="id" />
	</div>
</template>

<script>
import Loading from "../Loading";
import Product from "../product/Product";
import Sidebar from "../Sidebar";
import { OK }  from "../../util";
import { mapGetters } from 'vuex';

export default {
	name: "Purchased",
	props: {
		id: {
			type: String,
			required: true
		}
	},
	components: {
		Loading,
		Product,
		Sidebar
	},
	data() {
		return {
			loading: false,
			products: {},
			product: {}
		}
	},
	computed: {
		...mapGetters({
			isShopUser: 'auth/isShopUser'
		})
	},
	methods: {
		async getProducts() { //購入された商品一覧
			this.loading = true; //ローディングを表示する
			
			const response = await axios.get(`/api/users/${this.id}/purchased`); //API通信
			
			this.loading = false; //API通信が終わったらローディングを非表示にする
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			this.products = response.data; //プロパティにデータをセット
		},
		async cancel(product) { //購入キャンセル処理
			this.product = product; //プロパティに値をセット
			
			if(confirm('購入をキャンセルしますか？')) {
				this.loading = true; //ローディングを表示する
				
				const response = await axios.post(`/api/products/${this.product.id}/cancel`, this.product); //API通信
				
				this.loading = false; //API通信が終わったらローディングを非表示にする
				
				if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				this.product.purchased_by_user = false; //購入キャンセルをしたのでpurchased_by_userにfalseをセット
				this.product.canceled_by_user  = true; //canceled_by_userにtrueをセット
				
				this.$store.commit('message/setContent', { //メッセージ登録
					content: '購入をキャンセルしました',
				});
				
				this.$router.push({name: 'user.mypage', params: {id: this.id}}); //マイページに遷移する
			}
		},
	},
	watch: {
		$route: {
			async handler() {
				await this.getProducts();
			},
			immediate: true //immediateをtrueにすると、コンポーネントが生成されたタイミングでも実行する
		}
	}
}
</script>
