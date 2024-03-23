<template>
	<div class="l-main">
		
		<main class="l-main__2column">
			<div class="p-list">
				<h2 class="c-title p-list__title">削除した商品一覧</h2>
				
				<!-- ローディング -->
				<Loading v-show="loading" />
				
				<!-- 商品がなければ表示する -->
				<div v-if="!products.length" class="p-list__no-product">削除した商品はありません</div>
				
				<div class="p-list__card-container" v-show="!loading">
					
					<!-- Productコンポーネント -->
					<Product v-for="product in products"
									 :key="product.id"
									 :product="product">
						<div class="p-product__btnContainer" slot="btn">
							<!-- 論理削除された商品を復元するボタン	-->
							<button class="c-btn p-list__btn" @click="restore(product)">復元する</button>
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
	name: "Deleted",
	props: {
		id: { //出品者のログインユーザーID
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
			products: [],
			product: {}
		}
	},
	methods: {
		async getProducts() { //削除した商品一覧取得
			this.loading = true; //ローディングを表示する
			
			try {
				const response = await axios.get(`/api/users/${this.id}/deleted`); //API通信
				
				if(response.status === OK) {
					this.products = response.data; //プロパティにresponseデータをセットする
					
				}else {
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
			}catch (error) {
				console.error('削除した商品取得中にエラーが発生しました', error);
				
			}finally {
				this.loading = false; //ローディングを非表示にする
			}
		},
		async restore(product) { //論理削除した商品を復元する
			this.product = product; //引数の値をプロパティにセット
			
			if(!confirm('この商品を復元しますか？')) return;
			try {
				const response = await axios.post(`/api/products/${this.product.id}/restore`); //API通信
				
				if(response.status === OK) { //成功
					this.$store.commit('message/setContent', { content: '商品を復元しました！' }); //メッセージ登録
					this.$router.push({ name: 'user.mypage', params: { id: this.id.toString() }}); //マイページに遷移する
					
				}else { //失敗
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
			}catch (error) {
				console.error('商品の復元処理に失敗しました', error);
			}
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
