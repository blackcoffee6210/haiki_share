<template>
	<div class="l-main">
		<main class="l-main__2column">
			<div class="p-list">
				<h2 class="c-title p-list__title">購入された商品一覧</h2>
				
				<!-- ローディング -->
				<Loading v-show="loading" />
				
				<!-- 商品がなければ表示する -->
				<div v-if="!products.length"
						 class="p-list__no-product">購入された商品はありません
				</div>
				
				<div class="p-list__card-container">
				
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
								<router-link class="c-btn p-list__btn p-list__btn--detail"
														 :to="{ name: 'product.detail', params: { id: product.id.toString() }}">詳細を見る
								</router-link>
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
			products: {}
		}
	},
	methods: {
		//購入された商品一覧
		async getProducts() {
			console.log('商品を取得します');
			//ローディングを表示する
			this.loading = true;
			//API通信
			const response = await axios.get(`/api/users/${this.id}/purchased`);
			//API通信が終わったらローディングを非表示にする
			this.loading = false;
			
			//responseステータスがOKじゃなかったらエラーコードをセット
			if(response.status !== OK) {
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			//プロパティにデータをセット
			this.products = response.data;
			console.log('productの中身：' + this.products);
		}
	},
	watch: {
		$route: {
			async handler() {
				await this.getProducts();
			},
			//immediateをtrueにすると、コンポーネントが生成されたタイミングでも実行する
			immediate: true
		}
	}
}
</script>

<style scoped>

</style>
