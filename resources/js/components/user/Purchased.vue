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
					<!-- Productコンポーネント -->
					<Product v-for="product in products"
									 v-show="product.is_purchased"
									 :key="product.id"
									 :product="product">
						<div class="p-product__btn-container"
								 slot="btn">
							<!-- 購入キャンセルボタン --> <!-- 利用者の場合 -->
							<button class="c-btn c-btn--white p-product__btn"
											v-show="!isShopUser"
											@click="cancel(product)">購入キャンセル
							</button>
							<!-- 詳細を見るボタン --> <!-- コンビニユーザー -->
							<router-link class="c-btn p-product__btn"
													 v-show="isShopUser"
													 :to="{ name: 'product.detail',
													  			params: { id: product.id.toString() }}">詳細を見る
							</router-link>
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
		}
	},
	computed: {
		...mapGetters({
			isShopUser: 'auth/isShopUser'
		})
	},
	methods: {
		async getProducts() {  //購入した商品一覧
			this.loading   = true; //ローディングを表示する
			const response = await axios.get(`/api/users/${this.id}/purchased`); //API通信
			this.loading   = false; //API通信が終わったらローディングを非表示にする
			
			// if(response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
			// 	this.$store.commit('error/setCode', response.status);
			// 	return false;
			// }
			this.products = response.data; //プロパティにデータをセット
			console.log('getProductsの中身');
			console.log(this.products);
		},
		async getWasPurchased() { //購入された商品一覧
			this.loading   = true;  //ローディングを表示する
			const response = await axios.get(`/api/users/${this.id}/wasPurchased`); //API通信
			this.loading   = false; //API通信が終わったらローディングを非表示にする
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			this.products = response.data; //プロパティにデータをセット
		},
		async cancel(product) {   //購入キャンセル処理
			this.product = product; //プロパティに値をセット
			
			if(confirm('購入をキャンセルしますか？(キャンセルした商品は再度購入できません)')) {
				this.loading = true; //ローディングを表示する
				
				const response = await axios.post(`/api/products/${this.product.id}/cancel`, this.product); //API通信
				
				this.loading = false; //API通信が終わったらローディングを非表示にする
				
				if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				this.product.purchased_by_user = false; //購入キャンセルをしたのでpurchased_by_userにfalseをセット
				this.product.canceled_by_user  = true;  //canceled_by_userにtrueをセット
				
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
				this.isShopUser ? await this.getWasPurchased() : await this.getProducts();
			},
			immediate: true //immediateをtrueにすると、コンポーネントが生成されたタイミングでも実行する
		}
	}
}
</script>
