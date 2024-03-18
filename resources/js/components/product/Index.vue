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
						<span>{{ from }} - {{ to }}件 / {{ total }}件中</span>
					</div>
					
					<!-- 絞り込み条件のチェックボックス -->
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
									 v-if="sortCategory === 0 || sortCategory === product.category_id"
									 :key="product.id"
									 :product="product" />
				</div>
			</div>
			
			<!-- ページネーション	-->
			<ul class="c-pagination" v-show="!loading">
				<!-- もどるボタン -->
				<li v-if="currentPage > 1"
						@click="changePage(currentPage - 1)"
						class="c-pagination__block c-pagination--inactive">
					&lt;
				</li>
				<!-- ページの数字部分 -->
				<li v-for="page in pages"
						:key="page"
						@click="changePage(page)"
						class="c-pagination__block"
						:class="{ 'c-pagination--active': isCurrent(page), 'c-pagination--inactive': !isCurrent(page) }">
					{{ page }}
				</li>
				<!-- すすむボタン -->
				<li v-if="currentPage < lastPage"
						@click="changePage(currentPage + 1)"
						class="c-pagination__block c-pagination--inactive">
					&gt;
				</li>
			</ul>
			
		</main>
		
		<!-- サイドバー	-->
		<aside class="l-sidebar" v-show="!loading">
			<div class="p-sidebar-index">
				
				<!-- 絞り込みを解除するボタン -->
				<button v-if="isFiltered"
								@click="clearFilters"
								class="c-btn p-sidebar-index__clear-filter">
					絞り込みを解除
				</button>
				
				<!-- 検索ボックス -->
				<div class="p-sidebar-index__search">
					<label class="p-sidebar-index__title" for="search">商品検索</label>
					<input type="text"
								 placeholder="SEARCH"
								 v-model="tempKeyword"
								 id="search"
								 class="c-input p-sidebar-index__input"
								 @keydown.enter="handleEnter"
								 @compositionstart="handleCompositionStart"
								 @compositionend="handleCompositionEnd"
								 @input="handleInput">
					<!-- 検索アイコン -->
					<font-awesome-icon :icon="['fas', 'search']"
														 class="p-sidebar-index__search-icon"
														 @click="searchProducts"
														 color="#ff6f80" />
					<!-- ゴミ箱アイコン -->
					<font-awesome-icon :icon="['fas', 'trash']"
														 @click="deleteSearch"
														 class="p-sidebar-index__trash-icon"
														 color="#ff6f80" />
				</div>
				
				<!-- 金額ソート -->
				<div class="p-sidebar-index__sort">
					<label for="sort_price" class="p-sidebar-index__title">金額</label>
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
					<label for="sort_category" class="p-sidebar-index__title">カテゴリー</label>
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
												 :to="{ name: 'product.detail', params: { id: product.id.toString() }}" />
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
	props: {
		page: { // ルーターから受け取るページ番号プロパティ
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
			loading: false,        //ローディングを表示するかどうかを判定するプロパティ
			products: [],          //商品リスト
			currentPage: 1,        //現在のページ番号
			lastPage: 0,           //最後のページ番号
			total: 0,              //商品の合計数
			from: 0,               //表示範囲の開始
			to: 0,                 //表示範囲の終了
			showExpired: false,    //賞味期限切れ商品の表示フラグ
			showSale: false,       //販売中商品の表示フラグ
			tempKeyword: '',       //検索文字列を一時保存するキーワード
			keyword: '',           //実際の検索に使われるキーワード
			sortPrice: 1,          //金額「並び替え」の選択値（1: 標準, 2: 安い順, 3: 高い順）
			sortCategory: 0,       //「カテゴリー」絞り込みの初期値（0: 全て）
			sortPrefecture: 0,     //「都道府県」絞り込みの初期値（0: 全て）
			categories: [],        //カテゴリーリスト
			prefectures: [],       //出品したコンビニのある都道府県リスト
			recommendProducts: [], //おすすめ商品
			range: 5,              //ページネーション表示範囲
			frontDot: false,       //前方のドット表示
			endDot: false,         //後方のドット表示
			isComposing: false,    // 日本語入力の確定状態を追跡するためのフラグ
		}
	},
	async beforeRouteEnter(to, from, next) {
		next(vm => vm.updatePageAndData(to.query)); //ルートに入る前にデータを更新
	},
	beforeRouteUpdate(to, from, next) {
		this.updatePageAndData(to.query); //ルートが更新されたときデータを更新
		next();
	},
	computed: {
		pages() { //ページネーションに表示するページ番号の計算
			return [...Array(this.lastPage).keys()].map(i => i + 1);
		},
		isFiltered() { //絞り込みが行われているかどうか
			return this.showExpired ||        // 賞味期限切れの商品が選択されている
						 this.showSale ||           // 販売中の商品が選択されている
						 this.keyword !== '' ||     // 検索キーワードが入力されている
						 this.sortPrice !== 1 ||    // 金額ソートがデフォルト値以外
						 this.sortCategory !== 0 || // カテゴリーが選択されている
						 this.sortPrefecture !== 0; // 都道府県が選択されている
		},
		filteredProducts() { //絞り込み後の商品リスト
			return this.products.filter(product => {
				const today             = moment(new Date).format('YYYY-MM-DD');
				const isExpired         = product.expire > today;
				const isPurchased       = product.is_purchased;
				const matchesCategory   = this.sortCategory === 0 || this.sortCategory === product.category_id;
				const matchesPrefecture = this.sortPrefecture === 0 || this.sortPrefecture === product.prefecture_id;
				const matchesKeyword    = this.katakanaToHiragana(product.name).includes(this.katakanaToHiragana(this.keyword.trim()));
				
				return(!this.showExpired || !isExpired) &&
						  (!this.showSale || !isPurchased) &&
						  matchesCategory && matchesPrefecture && matchesKeyword;
			}).sort((a, b) => { //金額でソート
				switch(this.sortPrice) {
					case 1: break;                    //選択なし（デフォルト）
					case 2: return a.price - b.price; //価格が安い順
					case 3: return b.price - a.price; //価格が高い順
				}
			});
		},
		ascPrefecture() { //都道府県idを昇順にして返す
			return this.prefectures.sort(function(a, b) {
				return a.prefecture_id - b.prefecture_id;
			});
		},
	},
	methods: {
		handleCompositionStart() { //日本語入力開始
			this.isComposing = true;
		},
		handleCompositionEnd() { //日本語入力終了
			this.isComposing = false;
		},
		handleEnter() { //Enterキー押下時の処理
			if(!this.isComposing) { //日本語入力が確定していれば検索
				this.searchProducts();
			}
		},
		handleInput() { //入力フィールドの値が変更されたときの処理
			if(!this.tempKeyword) { //入力が空になったら検索絞り込みを解除
				this.deleteSearch();
			}
		},
		async updatePageAndData(query) { // ページまたはクエリパラメータが変更された際にデータを更新
			this.currentPage    = parseInt(query.page, 10) || 1;
			this.showExpired    = query.showExpired === 'true';
			this.showSale       = query.showSale === 'true';
			this.keyword        = this.$route.query.keyword || '';
			this.sortPrice      = parseInt(query.sortPrice, 10) || 1;
			this.sortCategory   = parseInt(query.sortCategory, 10) || 0;
			this.sortPrefecture = parseInt(query.sortPrefecture, 10) || 0;
			
			await Promise.all([
				this.getProducts(),
				this.getCategories(),
				this.getPrefectures(),
				this.getRecommend(),
			]);
		},
		clearFilters() { //絞り込みを解除する
			this.showExpired    = false; // 賞味期限切れの商品の表示をリセット
			this.showSale       = false; // 販売中の商品の表示をリセット
			this.tempKeyword    = '';    // 検索キーワードをリセット
			this.keyword        = '';    // 検索キーワードをリセット
			this.sortPrice      = 1;     // 金額ソートをデフォルトにリセット
			this.sortCategory   = 0;     // カテゴリー絞り込みを全てにリセット
			this.sortPrefecture = 0;     // 都道府県絞り込みを全てにリセット
			
			this.searchProducts(); // キーワードをクリアしてから検索を実行
		},
		searchProducts() { //商品を検索する
			this.keyword = this.tempKeyword; // 一時キーワードを確定キーワードに設定
		},
		updateQueryParams() { // URLのクエリパラメータを更新する
			this.$router.push({
				name: 'index',
				query: {
					showExpired:    this.showExpired,
					showSale:       this.showSale,
					keyword:        this.keyword,
					sortPrice:      this.sortPrice,
					sortCategory:   this.sortCategory,
					sortPrefecture: this.sortPrefecture,
					page:           this.currentPage || 1 // 現在のページ番号を追加
				}
			}).catch(err => {});
		},
		katakanaToHiragana(str) { //カタカナをひらがなに変換する
			return str.replace(/[\u30A1-\u30F6]/g, match => {
				return String.fromCharCode(match.charCodeAt(0) - 0x60);
			});
		},
		sidebarExpireDate(product) { //商品の賞味期限が過ぎているかどうかを判定する
			let dt = moment().format('YYYY-MM-DD');
			return product.expire <= dt;
		},
		async getRecommend() { //おすすめの商品を取得
			try {
				const response = await axios.get('/api/products/ranking');
				
				if(response.status === OK) { //成功なら
					this.recommendProducts = response.data;
					
				}else { //失敗なら
					this.$store.commit('error/setCode', response.status);
				}
				
			}catch (error) {
				console.error('おすすめの商品取得に失敗しました: ', error);
			}
		},
		async getCategories() { //カテゴリー取得メソッド
			try {
				const response = await axios.get('/api/categories'); //API接続
				
				if(response.status === OK) {
					this.categories = response.data; //プロパティにresponseデータを代入
					
				}else {
					this.$store.commit('error/setCode', response.status);
				}
				
			}catch (error) {
				console.error('カテゴリーの取得に失敗しました: ', error);
			}
		},
		async getPrefectures() { //出品したコンビニのある都道府県を取得
			try {
				const response = await axios.get('/api/products/prefecture'); //API接続
				
				if(response.status === OK) {
					this.prefectures = this.prefectures.filter((v1, i1, a1) => { //取得した都道府県を照準に並び替える
						return a1.findIndex(v => v1.prefecture_id === v.prefecture_id) === i1;
					});
				}else {
					this.$store.commit('error/setCode', response.status);
				}
				
			}catch (error) {
				console.error('都道府県データの取得に失敗しました: ', error);
			}
		},
		async getProducts() { //商品リスト取得
			this.loading = true; //ローディングを表示する
			try {
				const response = await axios.get(`/api/products?page=${ this.currentPage }`); //API接続
				
				if(response.status === OK) { //成功なら
					//response.dataだとレスポンスのJSONの取得になる
					//productはresponse.data.dataの中になるので、下記のような書き方になる
					this.products    = response.data.data;         //商品情報
					this.currentPage = response.data.current_page; //現在のページ
					this.lastPage    = response.data.last_page;    //最後のページ
					this.total       = response.data.total;        //商品の合計数
					this.from        = response.data.from;         //表示範囲の開始
					this.to          = response.data.to;           //表示範囲の終了
				}
				
			}catch (error) {
				console.error('商品の取得に失敗しました: ', error);
				
			}finally {
				this.loading = false; //ローディングを非表示にする
			}
		},
		deleteSearch() { //検索条件をクリアして再検索する
			this.tempKeyword = '';
			this.keyword = '';
			this.searchProducts();
		},
		changePage(page) { //ページを変更する
			if(page > 0 && page <= this.lastPage) {
				this.$router.push({ query: { ...this.$route.query, page }}).catch(err => {});
			}
		},
		isCurrent(page) { //現在のページかどうかを判定する
			return page === this.currentPage;
		},
	},
	watch: {
		'$route.query': { //ルートクエリが変更された場合、データを更新
			async handler(newQuery) {
				await this.updatePageAndData(newQuery);
			},
			immediate: true,
			deep: true
		}
	}
}
</script>
