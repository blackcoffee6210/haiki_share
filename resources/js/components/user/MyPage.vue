<template>
	<div class="l-main">
		<!-- メインコンテンツ -->
		<main class="l-main__2column">
			<div class="p-mypage">
				<!-- ローディング -->
				<Loading v-show="loading" />
				
				<!-- ローディング中でなければセクションを表示 -->
				<div v-show="!loading">
					
					<!-- 各セクションの表示 -->
					<section v-for="section in sections"
									 :key="section.key"
									 class="p-mypage__section"
									 v-show="section.condition">
						<div class="p-mypage__title-container">
							<!-- タイトル -->
							<h2 class="c-title p-mypage__title">{{ section.title }}</h2>
							<!-- 全件表示のリンク -->
							<router-link v-if="section.products.length"
													 class="p-mypage__link"
													 :to="section.routerLink">全件表示
							</router-link>
						</div>
						<!-- 商品がない場合のメッセージ -->
						<div v-if="!section.products.length" class="p-mypage__no-product">{{ section.noProductMessage }}</div>
						<!-- 商品一覧 -->
						<div v-else class="p-mypage__card-container">
							<component v-for="item in section.products"
												 :is="section.component"
												 :key="item.id"
												 v-bind="item"
												 v-show="!loading" />
						</div>
					</section>
					
				</div>
			</div>
		</main>
		
		<!-- サイドバー -->
		<Sidebar :id="id" />
	
	</div>
</template>

<script>
import Loading from "../Loading";
import Product from "../product/Product";
import Review  from "../review/Review";
import Sidebar from "../Sidebar";
import { OK }  from "../../util";
import { mapGetters } from 'vuex';

export default {
	name: "MyPage",
	props: {
		id: {
			type: String,
			required: true
		}
	},
	components: {
		Loading,
		Sidebar,
	},
	data() {
		return {
			loading: false, //ローディング
			sections: []    //各セクションのデータを格納する配列
		}
	},
	computed: {
		...mapGetters({
			isShopUser: 'auth/isShopUser', //コンビニユーザーかどうか
		}),
	},
	methods: {
		async fetchData(section) { //APIからデータを取得する共通メソッド
			this.loading = true; //loadingを表示
			
			try {
				const response = await axios.get(`/api/mypage/${section.apiEndpoint}`); //API接続
				
				if(response.status === OK) { //成功
					this.$set(this.sections.find(s => s.key === section.key), 'products', response.data);
					
				}else { //失敗
					this.$store.commit('error/setCode', response.status);
				}
				
			}catch (error) {
				console.error(`${section.apiEndpoint}取得中にエラーが発生しました`, error);
				this.$store.commit('error/setCode', 500);
				
			}finally {
				this.loading = false; //loadingを非表示
			}
		},
		initializeSections() { //セクションデータを初期化し、データを取得する
			this.sections = [
				{
					key: 'likedProducts',
					title: 'お気に入りした商品',
					noProductMessage: 'お気に入りした商品はありません',
					routerLink: { name: 'user.liked', params: { id: this.id.toString() } },
					condition: !this.isShopUser,
					apiEndpoint: 'liked',
					products: [],
					component: 'Product'
				},
				{
					key: 'postedProducts',
					title: '出品した商品',
					noProductMessage: '出品した商品はありません',
					routerLink: { name: 'user.posted', params: { id: this.id.toString() } },
					condition: this.isShopUser,
					apiEndpoint: 'posted',
					products: [],
					component: 'Product'
				},
				{
					key: 'purchasedProducts',
					title: '購入した商品',
					noProductMessage: '購入した商品はありません',
					routerLink: { name: 'user.purchased', params: { id: this.id.toString() } },
					condition: !this.isShopUser,
					apiEndpoint: 'purchased',
					products: [],
					component: 'Product'
				},
				{
					key: 'wasPurchasedProducts',
					title: '購入された商品',
					noProductMessage: '購入された商品はありません',
					routerLink: { name: 'user.wasPurchased', params: { id: this.id.toString() } },
					condition: this.isShopUser,
					apiEndpoint: 'wasPurchased',
					products: [],
					component: 'Product'
				},
				{
					key: 'canceledProducts',
					title: 'キャンセルした商品',
					noProductMessage: 'キャンセルした商品はありません',
					routerLink: { name: 'user.canceled', params: { id: this.id.toString() } },
					condition: !this.isShopUser,
					apiEndpoint: 'canceled',
					products: [],
					component: 'Product'
				},
				{
					key: 'wasCanceledProducts',
					title: 'キャンセルされた商品',
					noProductMessage: 'キャンセルされた商品はありません',
					routerLink: { name: 'user.wasCanceled', params: { id: this.id.toString() } },
					condition: this.isShopUser,
					apiEndpoint: 'wasCanceled',
					products: [],
					component: 'Product'
				},
				{
					key: 'deletedProducts',
					title: '削除した商品',
					noProductMessage: '削除した商品はありません',
					routerLink: { name: 'user.deleted', params: { id: this.id.toString() } },
					condition: this.isShopUser,
					apiEndpoint: 'deleted',
					products: [],
					component: 'Product'
				},
				{
					key: 'reviewedProducts',
					title: '投稿したレビュー',
					noProductMessage: '投稿したレビューはありません',
					routerLink: { name: 'user.reviewed', params: { id: this.id.toString() } },
					condition: !this.isShopUser,
					apiEndpoint: 'reviewed',
					products: [],
					component: 'Review'
				},
				{
					key: 'wasReviewedProducts',
					title: '投稿されたレビュー',
					noProductMessage: '投稿されたレビューはありません',
					routerLink: { name: 'user.wasReviewed', params: { id: this.id.toString() } },
					condition: this.isShopUser,
					apiEndpoint: 'wasReviewed',
					products: [],
					component: 'Review'
				},
			];
			
			this.sections.forEach(section => {
				if(section.condition) this.fetchData(section);
			});
		},
	},
	mounted() {
		this.initializeSections(); //コンポーネントがマウントされたらセクションデータを初期化
	},
	watch: {
		$route: { //$routerを監視してページが変わったときにメソッドが実行されるようにする
			handler() {
				this.initializeSections(); //ルートが変わったらセクションデータを再初期化
			},
			immediate: true
		}
	}
}
</script>
