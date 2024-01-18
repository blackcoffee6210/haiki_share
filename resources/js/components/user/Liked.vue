<template>
	<div class="l-main">
		<main class="l-main__2column">
			<div class="p-list">
				<h2 class="c-title p-list__title">お気に入りした商品一覧</h2>
				
				<!-- ローディング -->
				<Loading v-show="loading" />
				
				<!-- 商品がなければ表示する -->
				<div v-if="!products.length"
						 class="p-list__no-product">お気に入りした商品はありません
				</div>
				
				<div class="p-list__card-container" v-show="!loading">
					<!-- Productコンポーネント -->
					<Product v-for="product in products"
									 v-if="isLike"
									 :key="product.id"
									 :product="product">
						<div class="p-product__btn-container" slot="btn">
							<!--&lt;!&ndash; 詳細を見るボタン &ndash;&gt;-->
							<!--<router-link class="c-btn p-product__btn"-->
							<!--						 :to="{ name: 'product.detail',-->
							<!--						  			params: { id: product.id.toString() }}">詳細を見る-->
							<!--</router-link>-->
							<!-- いいね解除ボタン -->
							<button class="c-btn c-btn--white p-product__btn"
											@click="unlike(product)">お気に入り解除
							</button>
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
import { mapGetters } from "vuex";

export default {
	name: "Liked",
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
			product: {},
			isLike: true
		}
	},
	computed: {
		...mapGetters({
			isShopUser: 'auth/isShopUser'
		})
	},
	methods: {
		async getProducts() {  //いいねした商品一覧
			this.loading = true; //ローディングを表示する
			
			const response = await axios.get(`/api/users/${this.id}/liked`); //API通信
			
			this.loading = false; //API通信が終わったらローディングを非表示にする
			
			if (response.status !== OK) { //responseステータスがOKじゃなかったら後続の処理を行う
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			this.products = response.data; //プロパティにデータをセット
			console.log(response.data);
		},
		async unlike(product) { //お気に入りを削除する
			this.product = product; //引数の値をプロパティにセット
			
			if(confirm('お気に入りを解除しますか?')) {
				const response = await axios.delete(`/api/products/${this.product.id}/unlike`); //API通信
				
				if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
				this.product.likes_count -= 1; //いいねの数を1個減らす
				this.product.liked_by_user = false; //いいね解除したので、ユーザーのいいねをtrueからfalseにする
				this.isLike = false; //一覧表示に表示しない
				
				this.$store.commit('message/setContent', { //メッセージ登録
					content: 'お気に入りを解除しました',
				});
				
				this.$router.push({name: 'user.mypage', params: {id: this.id}}); //マイページに遷移する
			}
		}
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
