<template>
	<div class="l-main">
		<main class="l-main__2column">
			<div class="p-list">
				
				<!-- タイトル -->
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
						<div class="p-product__btnContainer" slot="btn">
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
			loading: false, //ローディング）
			products: {},   //商品
			product: {},
			isLike: true    //いいねしたかどうか
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
			
			try {
				const response = await axios.get(`/api/users/${this.id}/liked`); //API通信
				
				if (response.status === OK) { //成功
					this.products = response.data; //プロパティにデータをセット
					
				}else { //失敗
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
			}catch(error) {
				console.error('商品一覧取得中にエラーが発生しました', error);
				
			}finally {
				this.loading = false; //ローディングを非表示にする
			}
		},
		async unlike(product) {   //お気に入りを削除する
			this.product = product; //引数の値をプロパティにセット
			
			if(!confirm('お気に入りを解除しますか?')) return;
			try {
				const response = await axios.delete(`/api/products/${this.product.id}/unlike`); //API通信
				
				if(response.status === OK) { //成功
					this.product.likes_count -= 1;      //お気に入りを解除したので、いいねの数を1個減らす
					this.product.liked_by_user = false; //いいね解除したので、ユーザーのいいねをtrueからfalseにする
					this.isLike = false;                //一覧表示に表示しない
					
					this.$store.commit('message/setContent', { content: 'お気に入りを解除しました' }); //メッセージ登録
					this.$router.push({name: 'user.mypage', params: {id: this.id.toString()}}); //マイページに遷移する
					
				}else {
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
			}catch (error) {
				console.error('お気に入り解除処理中にエラーが発生しました', error);
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
