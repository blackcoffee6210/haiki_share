<template>
	<div class="l-main">
		
		<main class="l-main__2column">
			<div class="p-list">
				<h2 class="c-title p-list__title">出品した商品一覧</h2>
				
				<!-- ローディング -->
				<Loading v-show="loading" />
				
				<!-- 商品がなければ表示する -->
				<div v-if="!products.length"
						 class="p-list__no-product">出品した商品はありません
				</div>
				
				<div class="p-list__card-container" v-show="!loading">
					<!-- カード -->
					<div class="c-card p-list__card"
							 v-for="product in products"
							 :key="product.id">
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
								<!-- 編集ボタン -->
								<router-link class="c-btn p-list__btn p-list__btn--edit"
														 v-show="!product.is_purchased"
														 :to="{ name: 'product.edit', params: { id: product.id.toString() }}">編集する
								</router-link>
								<!-- 購入していたら詳細ボタンを表示(CSS型崩れ防止) -->
								<router-link class="c-btn p-list__btn p-list__btn--detail"
														 v-show="product.is_purchased"
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
