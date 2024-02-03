<template>
	<div class="l-main">
		<main class="l-main__2column">
			<div class="p-list">
				<h2 class="c-title p-list__title">
					<span v-show="isShopUser">キャンセルされた商品一覧</span>
					<span v-show="!isShopUser">キャンセルした商品一覧</span>
				</h2>
				
				<!-- ローディング -->
				<Loading v-show="loading" />
				
				<!-- 商品がなければ表示する -->
				<div v-if="!products.length"
						 class="p-list__no-product">
					<span v-show="isShopUser">キャンセルされた商品はありません</span>
					<span v-show="!isShopUser">キャンセルした商品はありません</span>
				</div>
				
				<div class="p-list__card-container" v-show="!loading">
					
					<!-- Productコンポーネント -->
					<Product v-for="product in products"
									 v-show="product.is_canceled"
									 :key="product.id"
									 :product="product">
						<!-- btnスロット -->
						<div class="p-product__btn-container"
								 slot="btn">
							<!-- 編集ボタン -->
							<router-link class="c-btn c-btn--white p-list__btn"
													 v-show="!product.is_purchased && isShopUser"
													 :to="{ name: 'product.edit',
																	params: { id: product.id.toString() }}">編集する
							</router-link>
							<!--&lt;!&ndash; 詳細を見るボタン &ndash;&gt;-->
							<router-link class="c-btn p-product__btn"
													 v-show="!isShopUser"
													 :to="{ name: 'product.detail',
													  			params: { id: product.id.toString() }}">詳細を見る
							</router-link>
						</div>
						<!-- キャンセルスロット -->
						<!-- キャンセルされた数をカウントして表示（「いいね」みたいに） -->
						<div class="p-product__cancel"
								 slot="cancel_count"
								 v-show="isShopUser">{{ product.cancels_count }}回
						</div>
					</Product>
					
				</div>
			</div>
		</main>
		
		<!-- サイドバー	-->
		<Sidebar :id="id" />
	</div>
</template>

<script>
import Loading        from "../Loading";
import Product        from "../product/Product";
import Sidebar        from "../Sidebar";
import { OK }         from "../../util";
import { mapGetters } from 'vuex';

export default {
	name: "Canceled",
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
			products: {}
		}
	},
	computed: {
		...mapGetters({
			isShopUser: 'auth/isShopUser'
		})
	},
	methods: {
		async getProducts() { //キャンセルした商品一覧取得
			this.loading = true; //ローディングを表示する
			
			const response = await axios.get(`/api/users/${this.id}/canceled`); //API通信
			
			this.loading = false; //API通信が終わったらローディングを非表示にする
			
			if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			this.products = response.data; //プロパティにデータをセット
		},
		async getWasCanceled() { //キャンセルされた商品一覧
			this.loading = true;
			
			const response = await axios.get(`/api/users/${this.id}/wasCanceled`); //API通信
			
			this.loading = false; //API通信が終わったらローディングを非表示にする
			
			if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			this.products = response.data; //プロパティにデータをセット
		}
	},
	watch: {
		$route: {
			async handler() {
				this.isShopUser ? await this.getWasCanceled() : await this.getProducts();
			},
			immediate: true //immediateをtrueにすると、コンポーネントが生成されたタイミングでも実行する
		}
	}
}
</script>
