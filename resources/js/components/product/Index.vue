<template>
	<div class="l-main">
		<main class="l-main__2column">
			<div class="p-index">
				
				<!-- ローディング -->
				<Loading v-show="loading" />
				
				<div class="p-index__sort-container">
					<!-- 検索件数 -->
					<div class="p-index__total">
						<span class="u-font-bold">検索結果</span>
						<span>
							{{ from }} -
							{{ to }}件 /
							{{ total }}件中
						</span>
						<!--<span>-->
						<!--	{{ currentMinNum + 1 }} - -->
						<!--	{{ currentMinNum + perPage }}件 /-->
						<!--	{{ total }}件中-->
						<!--</span>-->
					</div>
					<!--&lt;!&ndash; 検索件数 &ndash;&gt;-->
					<!--<div class="p-index__total">-->
					<!--	<span class="u-font-bold">検索結果</span>-->
					<!--	<span>{{ count }}件 / {{ total }}件中</span>-->
					<!--</div>-->
					
					<div class="p-index__checkbox-container">
						<!-- 賞味期限切れの商品のみ表示 -->
						<input type="checkbox"
									 id="expire"
									 class="c-checkbox p-index__checkbox"
									 v-model="showExpired">
						<label for="expire" class="p-index__label">賞味期限切れのみ表示</label>
						
						<!-- 販売中の商品のみ表示 -->
						<input type="checkbox"
									 id="sale"
									 class="c-checkbox p-index__checkbox--sale"
									 v-model="showSale">
						<label for="sale" class="p-index__label">販売中のみ表示</label>
					</div>
				</div>
				
				<!-- 商品コンテナ -->
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
			<ul class="c-pagination">
				<li class="c-pagination__block c-pagination--inactive"
						v-show="currentPage != 1"
						@click="changePage(currentPage - 1)">
					&lt;
				</li>
				<li v-for="page in frontPageRange"
						:key="page"
						@click="changePage(page)"
						class="c-pagination__block"
						:class="(isCurrent(page)) ? 'c-pagination--active' : 'c-pagination--inactive'">
					{{ page }}
				</li>
				<li v-show="frontDot"
						class="c-pagination__block inactive u-disabled">...
				</li>
				<li v-for="page in middlePageRange"
						:key="page"
						@click="changePage(page)"
						class="c-pagination__block"
						:class="(isCurrent(page)) ? 'c-pagination--active' : 'c-pagination--inactive'">
					{{ page }}
				</li>
				<li v-show="endDot"
						class="c-pagination__block c-pagination--inactive u-disabled">...
				</li>
				<li v-for="page in endPageRange"
						:key="page"
						@click="changePage(page)"
						class="c-pagination__block"
						:class="(isCurrent(page)) ? 'c-pagination--active' : 'c-pagination--inactive'">
					{{ page }}
				</li>
				<li class="c-pagination__block c-pagination--inactive"
						v-show="currentPage != lastPage"
						@click="changePage(currentPage + 1)">
					&gt;
				</li>
			</ul>
			
		</main>
		
		<!-- サイドバー	-->
		<aside class="l-sidebar" v-show="!loading">
			<div class="p-sidebar-index">
				
				<!-- 検索ボックス -->
				<div class="p-sidebar-index__search">
					<label class="p-sidebar-index__title"
								 for="search">商品検索
					</label>
					<input type="text"
								 placeholder="SEARCH"
								 v-model="keyword"
								 id="search"
								 class="c-input p-sidebar-index__input">
					<!-- ゴミ箱アイコン -->
					<font-awesome-icon :icon="['fas', 'trash']"
														 @click="deleteSearch"
														 class="p-sidebar-index__trash-icon"
														 color="#ff6f80" />
				</div>
				
				<!-- 金額ソート -->
				<div class="p-sidebar-index__sort">
					<label for="sort_price" class="p-sidebar-index__title">
						金額
					</label>
					<select id="sort_price"
									class="c-select p-sidebar-index__select"
									v-model.number="sortPrice">
						<option value="1">標準</option>
						<option value="2">価格が安い順</option>
						<option value="3">価格が高い順</option>
					</select>
				</div>
				
				<!-- カテゴリー -->
				<div class="p-sidebar-index__sort">
					<label for="sort_category" class="p-sidebar-index__title">
						カテゴリー
					</label>
					<select id="sort_category"
									class="c-select p-sidebar-index__select"
									v-model.number="sortCategory">
						<option value="0">選択してください</option>
						<option v-for="category in categories"
										:value="category.id"
										:key="category.id">
							{{ category.name }}
						</option>
					</select>
				</div>
				
				<!-- 出品したコンビニのある都道府県 -->
				<div class="p-sidebar-index__sort">
					<label for="sort_prefecture" class="p-sidebar-index__title">
						出品したコンビニの<br class="p-sidebar-index__br">都道府県
					</label>
					<select id="sort_prefecture"
									class="c-select p-sidebar-index__select"
									v-model.number="sortPrefecture">
						<option value="0">選択してください</option>
						<option v-for="prefecture in ascPrefecture"
										:value="prefecture.prefecture_id"
										:key="prefecture.prefecture_id">
							{{ prefecture.prefecture_name }}
						</option>
					</select>
				</div>
				
				<!-- おすすめの商品 -->
				<h2 class="c-title p-sidebar-index__title">おすすめの商品</h2>
				<div class="p-sidebar-index__card-container">
					<div class="c-card p-sidebar-index__card u-space-between"
							 v-for="product in recommendProducts"
							 v-show="!product.is_purchased"
							 :key="product.id">
						<router-link class="c-card__link"
												 :to="{ name: 'product.detail',
																params: { id: product.id.toString() }}" />
						<!-- 商品画像 -->
						<img class="p-sidebar-index__img"
								 :src="product.image"
								 alt="">
						<div class="p-sidebar-index__right">
							<!-- 商品名 -->
							<div class="p-sidebar-index__card-title">{{ product.name }}</div>
							<div class="p-sidebar-index__price-container">
								<!-- 金額 -->
								<div class="p-sidebar-index__price">{{ product.price | numberFormat }}</div>
								
								<!-- 賞味期限 -->
								<div class="p-sidebar-index__expire" v-if="sidebarExpireDate(product)">
									<span class="u-color__main u-font-bold">切れ</span>
									<span class="p-product__expire__date">
										{{ product.expire | fromExpire }}
									</span>
									日
								</div>
								<div class="p-sidebar-index__expire" v-else>
									残り
									<span class="p-product__expire__date">
										{{ product.expire | momentExpire }}
									</span>
									日
								</div>
							</div>
							<!-- ユーザー情報 -->
							<div class="c-flex">
								<img class="c-icon p-sidebar-index__icon"
										 :src="product.user_image"
										 alt="">
								<div class="p-sidebar-index__name">{{ product.user_name }}</div>
							</div>
							
						</div>
					</div>
				</div>
			</div>
		</aside>
	
	</div>
</template>

<script>
import Loading from "../Loading";
import Product from "./Product";
import { OK }  from "../../util";
import moment  from "moment";

export default {
	name: "Index",
	props: { //todo: 削除する（ページネーション自作したので）
		page: { //ルーター(router.js)から渡されるpageプロパティを受け取る
			type: Number,
			required: false,
			default: 1
		}
	},
	components: {
		Loading,
		Product,
	},
	data() {
		return {
			loading: false,     //ローディングを表示するかどうかを判定するプロパティ
			showExpired: false, //賞味期限切れかどうかを判定
			showSale: false,    //販売中かどうかを判定
			keyword: '',        //リアルタイム検索をするための検索ボックス
			sortPrice: 1,       //金額「並び替え」の選択値
			sortCategory: 0,    //「カテゴリー」絞り込みの初期値
			sortPrefecture: 0,  //「都道府県」絞り込みの初期値
			categories: [],     //カテゴリー
			prefectures: [],    //出品したコンビニのある都道府県
			products: [],       //商品リスト
			product: {
				image: '',
				category_id: '',
				name: '',
				detail: '',
				expire: '',
				price: ''
			},
			recommendProducts: [], //おすすめ商品
			currentPage: 1,        //現在のページ
			lastPage: 0,           //最後のページ
			total: 0,              //商品の合計数
			range: 5,
			frontDot: false,
			endDot: false,
			from: 0,
			to: 0,
		}
	},
	computed: {
		filteredProducts() { //絞り込んだ商品を返す
			let newProducts = []; //絞り込み後の商品を格納する新しい配列
			const today = moment(new Date).format('YYYY-MM-DD hh:mm:ss'); //今日の日付を用意
			
			for(let i = 0; i < this.products.length; i++) { //カテゴリーが選択されたら、カテゴリーIDが一致する商品だけを表示する
				let isShow = true; //表示対象かどうかを判定するフラグ
				
				if(this.showExpired &&
					 this.products[i].expire > today) { //「賞味期限切れのみ表示」チェックありで賞味期限が本日の日付より大きい（賞味期限内）場合は非表示にする
					isShow = false;
				}
				
				if(this.showSale &&
					 this.products[i].is_purchased) { //「販売中のみ未表示」チェックあり、かつ購入済み商品は非表示にする
					isShow = false;
				}
				
				if(this.sortCategory !== 0 &&
					 this.sortCategory !== this.products[i].category_id) { //i番目の商品が表示可能かどうかを判定する
					isShow = false; //カテゴリーが選択されていて(0じゃない) かつカテゴリーIDと商品カテゴリーIDが一致しない商品は非表示にする
				}
				
				if(this.sortPrefecture !== 0 &&
					 this.sortPrefecture !== this.products[i].prefecture_id) { //i番目の商品が表示可能かどうかを判定する
					isShow = false; //都道府県が選択されていて(0じゃない)、かつ都道府県IDと商品の都道府県IDが一致しない商品は非表示にする
				}
				
				if(isShow && this.products[i].name.indexOf(this.keyword) !== -1) { //リアルタイム検索をするための処理
					newProducts.push(this.products[i]);
				}
			}
			
			switch (this.sortPrice) { //新しい配列を並び替える
				case 1:  //金額ソート選択なし（デフォルト）
					break; //元の順番にsortしているので並び替えはなし
				case 2:  //価格が安い順に並び替える
					newProducts.sort(function(a, b) {
						return a.price - b.price;
					});
					break;
				case 3: //価格が高い順に並び替える
					newProducts.sort(function(a, b) {
						return b.price - a.price;
					});
					break;
			}
			return newProducts; //絞り込み後の商品を返す
		},
		count() { //絞り込み後の商品数のカウント
			return this.filteredProducts.length;
		},
		ascPrefecture() { //都道府県idを昇順にして返す
			return this.prefectures.sort(function(a, b) {
				return a.prefecture_id - b.prefecture_id;
			});
		},
		frontPageRange() {
			if(!this.sizeCheck) {
				this.frontDot = false;
				this.endDot   = false;
				return this.calRange(1, this.lastPage);
			}
			return this.calRange(1, 2);
		},
		middlePageRange() {
			if(!this.sizeCheck) return [];
			
			let start = '';
			let end   = '';
			if(this.currentPage <= this.range) {
				start = 3;
				end   = this.range + 2;
				this.frontDot = false;
				this.endDot = true;
			}else if(this.currentPage > this.lastPage - this.range) {
				start = this.lastPage - this.range - 1;
				end   = this.lastPage - 2;
				this.frontDot = true;
				this.endDot = false;
			}else {
				start = this.currentPage - Math.floor(this.range / 2);
				end   = this.currentPage + Math.floor(this.range / 2);
				this.frontDot = true;
				this.endDot = true;
			}
			return this.calRange(start, end);
		},
		endPageRange() {
			if(!this.sizeCheck) return [];
			return this.calRange(this.lastPage - 1, this.lastPage);
		},
		sizeCheck() {
			if(this.lastPage <= this.range + 4) {
				return false;
			}
			return true;
		},
	},
	methods: {
		sidebarExpireDate(product) { //商品の賞味期限が過ぎているかどうかを返す
			let dt = moment().format('YYYY-MM-DD');
			if(product.expire <= dt) {
				return true;
			}
		},
		async getRecommend() { //おすすめの商品を5件取得
			const response = await axios.get('/api/products/ranking');
			
			if(response.status !== OK) {
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			this.recommendProducts = response.data;
		},
		async getCategories() { //カテゴリー取得メソッド
			const response = await axios.get('/api/categories'); //API接続
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			this.categories = response.data; //プロパティにresponseデータを代入
		},
		async getPrefectures() { //出品したコンビニのある都道府県を取得
			const response = await axios.get('/api/products/prefecture'); //API接続
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			this.prefectures = response.data;
			
			this.prefectures = this.prefectures.filter((v1, i1, a1) => { //取得した都道府県を照準に並び替える
				return a1.findIndex(v => v1.prefecture_id === v.prefecture_id) === i1
			});
		},
		async getProducts() { //商品取得メソッド
			this.loading   = true; //ローディングを表示する
			const response = await axios.get(`/api/products?page=${this.currentPage}`); //API接続
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
			this.from        = response.data.from;
			this.to          = response.data.to;
		},
		calRange(start, end) {
			const range = [];
			for(let i = start; i <= end; i++) {
				range.push(i);
			}
			return range;
		},
		deleteSearch() { //サイドバー商品検索欄の文字列を削除するメソッド
			this.keyword = '';
		},
		changePage(page) {
			if(page > 0 && page <= this.lastPage) {
				this.currentPage = page;
				this.getProducts();
			}
		},
		isCurrent(page) {
			return page === this.currentPage;
		},
	},
	watch: {
		$route: {
			async handler() {
				await this.getRecommend();
				await this.getCategories();
				await this.getPrefectures();
				await this.getProducts();
			},
			immediate: true //immediateをtrueにすると、コンポーネントが生成されたタイミングでも実行する
		}
	}
}
</script>





















