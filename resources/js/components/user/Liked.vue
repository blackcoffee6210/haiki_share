<template>
	<div class="l-main">
		<main class="l-main__2column">
			<div class="p-list">
				<h2 class="c-title p-list__title">お気に入り一覧</h2>
				
				<!-- ローディング -->
				<Loading v-show="loading" />
				
				<!-- 商品がなければ表示する -->
				<div v-if="!products.length"
						 class="p-list__no-product">お気に入りした商品はありません
				</div>
				
				<div class="p-list__card-container" v-show="!loading">
					<!-- カード -->
					<div class="c-card p-list__card"
							 v-for="product in products"
							 :key="product.id"
							 v-if="isLike">
						<!-- 詳細画面のリンク -->
						<router-link class="c-card__link"
												 :to="{ name: 'product.detail',
												 			  params: { id: product.id.toString() }}" />
						<!-- SOLDバッジ -->
						<div class="c-badge" v-show="product.is_purchased">
							<div class="c-badge__sold">SOLD</div>
						</div>
						<!-- 商品の画像 -->
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
								<button class="c-btn p-list__btn p-list__btn--like"
												@click="unlike(product)">お気に入り解除
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
import Loading        from "../Loading";
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
		//いいねした商品一覧
		async getProducts() {
			//ローディングを表示する
			this.loading = true;
			//API通信
			const response = await axios.get(`/api/users/${this.id}/liked`);
			//API通信が終わったらローディングを非表示にする
			this.loading = false;
			
			//responseステータスがOKじゃなかったら後続の処理を行う
			if(response.status !== OK) {
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			//プロパティにデータをセット
			this.products = response.data;
		},
		//お気に入りを削除する
		async unlike(product) {
			//引数の値をプロパティにセット
			this.product = product;
			
			if(confirm('お気に入りを解除しますか?')) {
				//API通信
				// const response = await axios.delete(`/api/products/${this.product.id}/unlike`);
				const response = await axios.delete(`/api/products/${this.product.id}/unlike`);
				
				//responseステータスがOKじゃなかったらエラーコードをセット
				if (response.status !== OK) {
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
				//いいねの数を1個減らす
				this.product.likes_count -= 1;
				//いいね解除したので、ユーザーのいいねをtrueからfalseにする
				this.product.liked_by_user = false;
				//一覧表示に表示しない
				this.isLike = false;
				
				//メッセージ登録
				this.$store.commit('message/setContent', {
					content: 'お気に入りを解除しました',
				});
				
				//マイページに遷移する
				this.$router.push({name: 'user.mypage', params: {id: this.id}});
				
				//自画面に遷移する
				// this.$router.push({name: 'user.liked', params: {id: this.id}}).catch(() => {} );
			}
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
