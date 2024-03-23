<template>
	<div class="l-main">
		
		<main class="l-main__2column">
			<div class="p-list">
				
				<!-- タイトル -->
				<h2 class="c-title p-list__title">出品した商品一覧</h2>
				
				<!-- ローディング -->
				<Loading v-show="loading" />
				
				<!-- 出品した商品がなければ表示する -->
				<div v-if="!products.length"
						 class="p-list__no-product">出品した商品はありません
				</div>
				
				<div class="p-list__card-container" v-show="!loading">
					
					<!-- Productコンポーネント -->
					<Product v-for="product in products"
									 :key="product.id"
									 :product="product">
						
						<div class="p-product__btnContainer"
								 slot="btn">
							<!-- 編集ボタン -->
							<router-link class="c-btn c-btn--white p-list__btn"
													 v-show="!product.is_purchased"
													 :to="{ name: 'product.edit',
																	params: { id: product.id.toString() }}">編集する
							</router-link>
							<!-- 商品が購入されていたら詳細ボタンを表示 -->
							<router-link class="c-btn p-list__btn"
													 v-show="product.is_purchased"
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
import Loading from "../Loading";
import Product from "../product/Product";
import Sidebar from "../Sidebar";
import { OK }  from "../../util";

export default {
	name: "Posted",
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
	methods: {
		async getProducts() { //出品した商品一覧取得
			this.loading = true; //ローディングを表示する
			
			const response = await axios.get(`/api/users/${this.id}/posted`); //API通信
			
			this.loading = false; //API通信が終わったらローディングを非表示にする
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			this.products = response.data; //プロパティにresponseデータをセットする
		}
	},
	watch: {
		$route: { //$routeを監視してページが変わったときにgetProductsメソッドが実行されるようにする
			async handler() {
				await this.getProducts();
			},
			immediate: true //immediateをtrueにすると、コンポーネントが生成されたタイミングでも実行する
		}
	}
}
</script>
