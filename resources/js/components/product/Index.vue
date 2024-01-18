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
					<Product v-show="!loading"
									 v-for="product in filteredProducts"
									 v-if="sortCategory !== 0 || sortCategory !== product.category_id"
									 :key="product.id"
									 :product="product" />
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
				
				<!-- おすすめの商品 -->
				<h2 class="c-title p-sidebar-index__title">おすすめの商品</h2>
				<div class="p-sidebar-index__card-container">
					<div class="c-card p-sidebar-index__card"
							 v-for="product in recommendProducts"
							 v-show="!product.is_purchased"
							 :key="product.id">
						<router-link class="c-card__link"
												 :to="{ name: 'product.detail',
																params: { id: product.id.toString() }}" />
						<img class="p-sidebar-index__img"
								 :src="product.image"
								 alt="">
						<div class="p-sidebar-index__right">
							<div class="p-sidebar-index__card-title">{{ product.name }}</div>
							<div class="p-sidebar-index__price-container">
								<div class="p-sidebar-index__price">{{ product.price | numberFormat }}</div>
								<div class="p-sidebar-index__expire">
									残り
									{{ product.expire | momentExpire }}
									日
								</div>
							</div>
							<div class="c-flex">
								<img class="c-icon p-sidebar-index__icon"
										 :src="product.user_image"
										 alt="">
								<div class="p-sidebar-index__name">{{ product.user_name }}</div>
							</div>
						</div>
					</div>
				</div>
				
				<!--賞味期限の近い商品 -->
				<h2 class="c-title p-sidebar-index__title">賞味期限の近い商品</h2>
				<div class="p-sidebar-index__card-container">
					<div class="c-card p-sidebar-index__card"
							 v-for="product in recommendProducts"
							 v-show="!product.is_purchased"
							 :key="product.id">
						<router-link class="c-card__link"
												 :to="{ name: 'product.detail',
																params: { id: product.id.toString() }}" />
						<img class="p-sidebar-index__img"
								 :src="product.image"
								 alt="">
						<div class="p-sidebar-index__right">
							<div class="p-sidebar-index__card-title">{{ product.name }}</div>
							<div class="p-sidebar-index__price-container">
								<div class="p-sidebar-index__price">{{ product.price | numberFormat }}</div>
								<div class="p-sidebar-index__expire">
									残り
									{{ product.expire | momentExpire }}
									日
								</div>
							</div>
							<div class="c-flex">
								<img class="c-icon p-sidebar-index__icon"
										 :src="product.user_image"
										 alt="">
								<div class="p-sidebar-index__name">{{ product.user_name }}</div>
							</div>
						</div>
					</div>
				</div>
				
				<!-- 検索ボックス -->
				<label class="c-label p-sidebar-index__title u-font-bold"
							 for="search">商品検索
				</label>
				<input type="text"
							 placeholder="SEARCH"
							 v-model="keyword"
							 id="search"
							 class="c-input p-sidebar-index__search">
			</div>
		</aside>
	
	</div>
</template>

<script>
import Loading    from "../Loading";
import Product    from "./Product";
import Pagination from "../Pagination";
import { OK }     from "../../util";
export default {
	name: "Index",
	props: {
		page: { //ルーター(router.js)から渡されるpageプロパティを受け取る
			type: Number,
			required: false,
			default: 1
		}
	},
	components: {
		Loading,
		Product,
		Pagination
	},
	data() {
		return {
			loading: false, 			//ローディングを表示するかどうかを判定するプロパティ
			keyword: '', 					//リアルタイム検索をするための検索ボックス
			sortPrice: 1,					//金額「並び替え」の選択値
			sortCategory: 0, 			//「カテゴリー」絞り込みの初期値
			categories: {},       //カテゴリー
			products: {}, 				//商品リスト
			currentPage: 0,			  //現在のページ
			lastPage: 0, 					//最後のページ
			total: 0, 						//商品の合計数
			recommendProducts: {} //おすすめ商品
		}
	},
	computed: {
		filteredProducts() { //絞り込んだ商品を返す
			let newProducts = []; //絞り込み後の商品を格納する新しい配列
			
			for(let i = 0; i < this.products.length; i++) { //カテゴリーが追加されたら、カテゴリーIDが一致する商品だけを表示する
				let isShow = true; //表示対象かどうかを判定するフラグ
				
				if(this.sortCategory !== 0 &&
					 this.sortCategory !== this.products[i].category_id) { //i番目の商品が表示可能かどうかを判定する
					isShow = false; //カテゴリーが選択されていて(0じゃない) かつカテゴリーと商品カテゴリーIDが一致しないものは非表示にする
				}
				if(isShow && this.products[i].name.indexOf(this.keyword) !== -1) { //リアルタイム検索をするための処理
					newProducts.push(this.products[i]);
				}
			}
			
			if(this.sortPrice === 1) { //新しい配列を並び替える
				//元の順番にsortしているので並び替えはなし
				
			}else if(this.sortPrice === 2) { //価格が安い順に並び替える
				newProducts.sort(function(a, b) {
					return a.price - b.price;
				});
				
			}else if(this.sortPrice === 3) { //価格が高い順に並び替える
				newProducts.sort(function(a, b) {
					return b.price - a.price;
				})
			}
			
			//todo: ここに賞味期限でソートする処理を実装する
			
			return newProducts; //絞り込み後の商品を返す
		},
		count() { //商品数のカウント
			return this.filteredProducts.length;
		}
	},
	methods: {
		async getRecommend() { //おすすめの商品を5件取得
			const response = await axios.get('/api/products/ranking');
			
			if(response.status !== OK) {
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			this.recommendProducts = response.data;
			console.log('getRecommendの中身');
			console.log(response.data);
		},
		async getCategories() { //カテゴリー取得メソッド
			const response = await axios.get('/api/categories'); //API接続
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			this.categories = response.data; //プロパティにresponseデータを代入
		},
		async getProducts() { //商品取得メソッド
			this.loading   = true; //ローディングを表示する
			const response = await axios.get(`/api/products?page=${this.page}`); //API接続
			this.loading   = false; //API通信が終わったらローディングを非表示にする
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			//response.dataだとレスポンスのJSONの取得になる
			//productはresponse.data.dataの中になるので、下記のような書き方になる
			this.products    = response.data.data;         //商品情報
			this.currentPage = response.data.current_page; //現在のページ
			this.lastPage    = response.data.last_page;    //最後のページ
			this.total       = response.data.total;        //商品の数
		}
	},
	watch: {
		$route: {
			async handler() {
				await this.getRecommend();
				await this.getCategories();
				await this.getProducts();
			},
			immediate: true //immediateをtrueにすると、コンポーネントが生成されたタイミングでも実行する
		}
	}
}
</script>
























