<template>
	<div class="l-main">
		<main class="l-main__2column">
			<div class="p-index">
				
				<!-- ローディング -->
				<Loading v-show="loading" />
				
				<div class="p-index__sort-container">
					<!-- 検索件数 -->
					<div class="p-index__search">
						<span class="u-font-bold">検索結果</span>
						<span>{{ count }}件 / {{ total }}件中</span>
					</div>
					
					<!-- todo: 賞味期限でソートできる機能を追加 -->
					<!-- todo: 出品している都道府県でソートできる機能を追加 -->
					<!-- 金額ソート -->
					<div class="p-index__sort">
						<label for="sort_price" class="p-index__label">並び替え</label>
						<select id="sort_price"
										class="p-index__select"
										v-model.number="sortPrice">
							<option value="1">標準</option>
							<option value="2">価格が安い順</option>
							<option value="3">価格が高い順</option>
						</select>
					</div>
					
					<!-- カテゴリー -->
					<div class="p-index__select-category">
						<label for="sort_category" class="p-index__label">
							カテゴリー
						</label>
						<select id="sort_category"
										class="p-index__select"
										v-model.number="sortCategory">
							<option value="0">選択してください</option>
							<option v-for="category in categories"
											:value="category.id"
											:key="category.id">
								{{ category.name }}
							</option>
						</select>
					</div>
				</div>
				
				<div class="p-index__product-container">
					<!-- Productコンポーネント -->
					<!--<Product v-show="!loading"-->
					<!--				 v-for="product in filteredProducts"-->
					<!--				 v-if="sortCategory !== 0 || sortCategory !== product.category_id"-->
					<!--				 :key="product.id"-->
					<!--				 :product="product" />-->
				</div>
			</div>
			
			<!-- ページネーション	-->
			<Pagination v-show="!loading"
									:current-page="currentPage"
									:last-page="lastPage"
									:link-max="7"/>
		</main>
		
		<!-- サイドバー	-->
		<aside class="l-sidebar" v-show="!loading">
			<div class="p-sidebar-index">
				<!--&lt;!&ndash;title&ndash;&gt;-->
				<!--<h2 class="c-title p-sidebar-index__title">おすすめ記事</h2>-->
				
				<!--&lt;!&ndash; おすすめの記事 &ndash;&gt;-->
				<!--<div class="p-sidebar-index__recommend"></div>-->
				<!--&lt;!&ndash; card	&ndash;&gt;-->
				<!--<div class="c-card p-sidebar-index__card"-->
				<!--		 v-for="article in recommendArticles"-->
				<!--		 :key="article.id">-->
				<!--	<router-link class="c-card__link"-->
				<!--							 :to="{ name: 'article.detail', params: { id: article.id.toString() }}" />-->
				<!--	<img class="p-sidebar-index__img"-->
				<!--			 :src="article.image"-->
				<!--			 alt="">-->
				<!--	<div class="p-sidebar-index__right">-->
				<!--		<div class="p-sidebar-index__card-title">{{ article.name }}</div>-->
				<!--		<div class="c-flex">-->
				<!--			<img class="c-icon p-sidebar-index__icon"-->
				<!--					 :src="article.user_image"-->
				<!--					 alt="">-->
				<!--			<div class="p-sidebar-index__name">{{ article.user_name }}</div>-->
				<!--		</div>-->
				<!--		<div class="p-sidebar-index__price">{{ article.price | numberFormat }}</div>-->
				<!--	</div>-->
				<!--</div>-->
				
				<!--&lt;!&ndash; 検索ボックス &ndash;&gt;-->
				<!--<input type="text"-->
				<!--			 placeholder="SEARCH"-->
				<!--			 v-model="keyword"-->
				<!--			 class="c-input p-sidebar-index__search">-->
			</div>
		</aside>
	
	</div>
</template>

<script>
import Loading    from "./Loading";
// import Product    from "./Product";
import Pagination from "./Pagination";
import { OK }     from "../util";
export default {
	name: "Index",
	props: {
		//ルーター(router.js)から渡されるpageプロパティを受け取る
		page: {
			type: Number,
			required: false,
			default: 1
		}
	},
	components: {
		Loading,
		Pagination,
		// Product
	},
	data() {
		return {
			loading: false, 			//ローディングを表示するかどうかを判定するプロパティ
			keyword: '', 					//リアルタイム検索をするための検索ボックス
			sortPrice: 1,					//金額「並び替え」の選択値
			sortCategory: 0, 			//「カテゴリー」絞り込みの初期値
			products: [], 				//商品リスト
			currentPage: 0,			  //現在のページ
			lastPage: 0, 					//最後のページ
			total: 0, 						//記事の合計数
			categories: [],       //カテゴリー
			preview: '',          //セレクトしたカテゴリーを格納するプロパティ
			recommendProducts: {} //おすすめ商品
		}
	},
	computed: {
		//絞り込んだ商品を返す
		filteredProducts() {
			//絞り込み後の商品を格納する新しい配列
			let newProducts = [];
			
			//カテゴリーが追加されたら、カテゴリーIDが一致する商品だけを表示する
			for(let i = 0; i < this.products.length; i++) {
				//表示対象かどうかを判定するフラグ
				let isShow = true;
				
				//i番目の商品が表示可能かどうかを判定する
				if(this.sortCategory !== 0 && this.sortCategory !== this.products[i].category_id) {
					//カテゴリーのセレクトボックスが選択されている
					// かつカテゴリーのセレクトボックスと商品カテゴリーが一致しないものは非表示にする
					isShow = false;
				}
				//リアルタイム検索をするための処理
				if(isShow && this.products[i].name.indexOf(this.keyword) !== -1) {
					newProducts.push(this.products[i]);
				}
			}
			//新しい配列を並び替える
			if(this.sortPrice === 1) {
				//元の順番にsortしているので並び替えはなし
			}
			//価格が安い順に並び替える
			else if(this.sortPrice === 2) {
				newProducts.sort(function(a, b) {
					return a.price - b.price;
				});
			}
			//価格が高い順に並び替える
			else if(this.sortPrice === 3) {
				newProducts.sort(function(a, b) {
					return b.price - a.price;
				})
			}
			
			//todo: ここに賞味期限でソートする処理を実装する
			
			//絞り込み後の商品を返す
			return newProducts;
		},
		//商品数のカウント
		count() {
			return this.filteredProducts.length;
		}
	},
	methods: {
		//カテゴリー取得メソッド
		async getCategories() {
			//API接続
			console.log('categoryを取得します');
			const response = await axios.get('/api/categories');
			//responseステータスがOKじゃなかったらエラーコードをセット
			if(response.status !== OK) {
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			//プロパティにresponseデータを代入
			this.categories = response.data;
			console.log(this.categories);
		}
	},
	watch: {
		async handler() {
			await this.getCategories();
		},
		//immediateをtrueにすると、コンポーネントが生成されたタイミングでも実行する
		immediate: true
	}
}
</script>
























