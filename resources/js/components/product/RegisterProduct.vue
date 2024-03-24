<template>
	<main class="l-main">
		<div class="p-productForm">
			
			<h2 class="c-title p-productForm__title">商品出品</h2>
			
			<div class="p-productForm__background">
				<!-- ローディング -->
				<Loading color="#f96204" v-show="loading"/>
				
				<!-- フォームエリア	-->
				<form class="p-productForm__form" v-show="!loading" @submit.prevent="submit">
					
					<!-- 商品画像	-->
					<div class="u-p-relative">
						<label class="p-productForm__labelImg"
									 :class="{
											'p-productForm__labelImg--err': errors.image,
											'p-productForm__img--enter': isEnter
									 }"
									 @dragenter="dragEnter"
									 @dragleave="dragLeave"
									 @dragover.prevent
									 @drop.stop="dropFile">
							<span class="p-productForm__imgTxt" v-if="!preview">
								ドラッグ&ドロップ<br>またはファイルを選択
							</span>
							<input type="file"
										 class="p-productForm__img"
										 @change="onFileChange">
							<output class="p-productForm__output" v-if="preview">
								<img :src="preview"
										 class="p-productForm__output-img"
										 alt="">
							</output>
						</label>
					</div>
					
					<!-- エラーメッセージ	-->
					<div v-if="errors">
						<div v-for="msg in errors.image"
								 :key="msg"
								 class="p-error">{{ msg }}
						</div>
					</div>
					
					<!-- カテゴリー -->
					<label for="category_id" class="c-label p-productForm__label">カテゴリー</label>
					<select class="c-select p-productForm__input"
									:class="{ 'c-select--err': errors.category_id }"
									id="category_id"
									v-model="product.category_id">
						<option value="" disabled>カテゴリーを選択してください</option>
						<option v-for="category in categories"
										:value="category.id"
										:key="category.id">{{ category.name }}
						</option>
					</select>
					<!-- エラーメッセージ	-->
					<div v-if="errors">
						<div v-for="msg in errors.category_id"
								 :key="msg"
								 class="p-error">{{ msg }}
						</div>
					</div>
					
					<!-- 商品名	-->
					<label for="name" class="c-label p-productForm__label">商品名</label>
					<input type="text"
								 class="c-input p-productForm__input"
								 :class="{ 'c-input--err': errors.name || maxCounter(product.name,50) }"
								 id="name"
								 v-model="product.name"
								 placeholder="商品名を入力してください">
					<!-- エラーメッセージ -->
					<div class="u-d-flex u-space-between">
						<!-- エラーメッセージ（フロントエンド） -->
						<div v-if="maxCounter(product.name,50) && !errors.name"
								 class="p-error">
							<p class="u-mb20">商品名は50文字以下で指定してください</p>
						</div>
						<!-- エラーメッセージ（バックエンド）	-->
						<div v-if="errors">
							<div v-for="msg in errors.name"
									 :key="msg"
									 class="p-error u-mb20">
								{{ msg }}
							</div>
						</div>
						<!-- 文字数カウンター -->
						<p class="c-counter"
							 :class="{ 'c-counter--err': maxCounter(product.name,50) }">
							{{ product.name.length }}/50
						</p>
					</div>
					
					<!-- 商品の内容	-->
					<label for="detail" class="c-label p-productForm__label u-mt0">商品の内容</label>
					<textarea	class="c-input p-productForm__textarea"
										 :class="{ 'c-input--err': errors.detail || maxCounter(product.detail, 255) }"
										 id="detail"
										 v-model="product.detail"
										 placeholder="商品の内容を入力してください"
					></textarea>
					<div class="u-d-flex u-space-between">
						<!-- エラーメッセージ（フロントエンド） -->
						<div v-if="maxCounter(product.detail, 255) && !errors.detail" class="p-error">
							<p class="u-mb20">商品の内容は255文字以下で指定してください</p>
						</div>
						<!-- エラーメッセージ（バックエンド）	-->
						<div v-if="errors">
							<div v-for="msg in errors.detail"
									 :key="msg"
									 class="p-error u-mb20">
								{{ msg }}
							</div>
						</div>
						<!-- 文字数カウンター -->
						<p class="c-counter" :class="{ 'c-counter--err': maxCounter(product.detail,255) }">
							{{ product.detail.length }}/255
						</p>
					</div>
					
					<!-- 賞味期限 -->
					<label for="expire_date" class="c-label p-productForm__label u-mt0">賞味期限</label>
					<input type="text"
								 onfocusin="this.type='date'"
								 onfocusout="this.type='text'"
								 class="c-input p-productForm__input"
								 :class="{ 'c-input--err': errors.expire }"
								 id="expire_date"
								 :min="tomorrow"
								 :max="maxDate"
								 placeholder="本日以降の日付を選択してください"
								 v-model="product.expire">
					 <!--エラーメッセージ-->
					<div v-if="errors">
						<div v-for="msg in errors.expire"
								 :key="msg"
								 class="p-error">{{ msg }}
						</div>
					</div>
					
					<!-- 金額	-->
					<label for="price" class="c-label p-productForm__label">金額</label>
					<div class="u-d-flex">
						<input type="text"
									 class="c-input p-productForm__input p-productForm__input__yen"
									 :class="{ 'c-input--err': errors.price || errorMessage }"
									 id="price"
									 v-model="product.price"
									 @input="validatePrice"
									 placeholder="50〜10000円の間で入力してください">
						<div class="p-productForm__yen"
								 :class="{'p-productForm__yen--err': errors.price || errorMessage }">円
						</div>
					</div>
					<div class="u-d-flex u-space-between">
						<!-- エラーメッセージ（フロントエンド） -->
						<div v-if="errorMessage && !errors.price"
								 class="p-error">
							<p class="">{{ errorMessage }}</p>
						</div>
						<!-- エラーメッセージ（バックエンド）	-->
						<div v-if="errors">
							<div v-for="msg in errors.price"
									 :key="msg"
									 class="p-error">
								{{ msg }}
							</div>
						</div>
					</div>
					
					<!-- ボタンコンテナ -->
					<div class="p-productForm__btnContainer">
						<!-- ボタン -->
						<a @click="$router.back()"
							 class="c-btn c-btn--white p-productForm__btn p-productForm__btn__back">もどる
						</a>
						<!-- ボタン	-->
						<button class="c-btn p-productForm__btn" type="submit">出品する</button>
					</div>
				</form>
			</div>
		</div>
	</main>
</template>

<script>
import Loading from "../Loading";
import moment  from "moment";
import { CREATED, OK, UNPROCESSABLE_ENTITY } from "../../util";

export default {
	name: "RegisterProduct",
	components: {
		Loading
	},
	data() {
		return {
			loading: false, //ローディングを表示するかどうかを判定するプロパティ
			product: {
				image: '',       //商品画像
				category_id: '', //カテゴリーID
				name: '',        //商品名
				detail: '',      //商品の内容
				expire: '',      //賞味期限
				price: ''        //金額
			},
			categories: [], //カテゴリーを格納するプロパティ
			preview: null,  //データURLを格納するプロパティ
			errors: {       //エラーメッセージを格納するプロパティ
				image: null,
				category_id: null,
				name: null,
				detail: null,
				expire: null,
				price: null
			},
			isEnter: false, //画像のクラスバインドを行う
			files: [],
			errorMessage: ''
		}
	},
	computed: {
		tomorrow() { //明日の日付をYYYY-MM-DDの書式で返す
			let date = new Date();
			date.setDate(date.getDate() + 1);
			return moment(date).format('YYYY-MM-DD');
		},
		maxDate() { //賞味期限を登録できる最大の日付をYYYY-MM-DDの書式で返す
			let date = new Date();
			date.setDate(date.getDate() + 30);
			return moment(date).format('YYYY-MM-DD');
		}
	},
	methods: {
		dragEnter() { //画像が要素内に入ったら
			this.isEnter = true;
		},
		dragLeave() { //画像が要素から出たら
			this.isEnter = false;
		},
		dropFile(event) {
			try {
				event.preventDefault(); // デフォルトのイベントを防ぐ
				this.isEnter = false;
				
				if (event.dataTransfer.files.length !== 1) { // ドロップされたファイルがない、または複数ファイルがドロップされた場合は処理しない
					return;
				}
				const file = event.dataTransfer.files[0];
				
				if (!file.type.match('image.*')) { // ドロップされたファイルが画像であるかを確認
					this.reset(); // ファイルが画像でなければリセット
					return;
				}
				const reader = new FileReader(); // FileReaderを使用して画像を読み込み、プレビューとして表示
				reader.onload = e => {
					this.preview = e.target.result; // プレビュー用のデータURLをセット
					this.product.image = file; // 実際に送信するファイルをセット
				};
				reader.readAsDataURL(file); // ファイルをデータURLとして読み込む
				
			}catch (error) {
				console.error('ドラッグ＆ドロップ処理にエラーが発生しました。', error);
			}
		},
		maxCounter(content, maxValue) { //カウンターの文字数上限
			return content.length > maxValue;
		},
		async getCategories() { //カテゴリー取得
			try {
				const response = await axios.get('/api/categories');
				if(response.status === OK) {
					this.categories = response.data; //プロパティに値をセットする
					
				}else {
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
			}catch (error) {
				console.error('カテゴリー取得中にエラーが発生しました。', error);
			}
		},
		validatePrice() {
			const value = Number(this.product.price);
			if(isNaN(value) || value < 50 || value > 10000) {
				this.errorMessage = '金額は50〜10000円の間で指定してください';
			}else {
				this.errorMessage = ''; //エラーなし
			}
		},
		onFileChange(event) { //フォームでファイルが選択されたら実行されるメソッド
			if (event.target.files.length === 0) { //何も選択されていなかったら処理中断
				this.reset(); // 選択されたファイルがなければリセット
				return;
			}
			const file = event.target.files[0];
			
			if (!file.type.match('image.*')) { //ファイルが画像ではなかったら処理中断
				this.reset(); // ファイルが画像でなければリセット
				return;
			}
			const reader = new FileReader();
			reader.onload = e => {
				this.preview = e.target.result; // プレビュー用のデータURLをセット
				this.product.image = file; // 実際に送信するファイルをセット
			};
			reader.readAsDataURL(file); // ファイルをデータURLとして読み込む
		},
		reset() { //入力欄の値とプレビュー表示をクリアするメソッド
			this.preview = null;
			this.product.image = null;
			this.$el.querySelector('input[type="file"]').value = null;
		},
		async submit() { //商品登録メソッド
			this.loading = true; //ローティングを表示する
			
			try {
				const formData = new FormData;
				formData.append('image',       this.product.image);
				formData.append('category_id', this.product.category_id);
				formData.append('name',        this.product.name);
				formData.append('detail',      this.product.detail);
				formData.append('price',       this.product.price);
				formData.append('expire',      this.product.expire);
				
				const response = await axios.post('/api/products', formData); //商品登録APIを呼び出す
				
				if(response.status === CREATED) { //成功なら
					this.$store.commit('message/setContent', { content: '商品を登録しました！' });
					this.$router.push({ name: 'index' }); //トップページへ移動する
					
				}else if(response.status === UNPROCESSABLE_ENTITY) { //バリデーションエラーなら後続の処理を行う
					this.errors = response.data.errors; //responseエラーメッセージをプロパティに格納する
					return;                             //後続の処理を抜ける
					
				}else {
					this.$store.commit('error/setCode', response.status); //エラー情報を渡す
					return;
				}
				this.reset(); //送信が完了したら入力値をクリアする
				
			}catch (error) {
				console.error('商品出品処理に失敗しました', error);
				
			}finally {
				this.loading = false; //ローディングを非表示にする
			}
		}
	},
	watch: {
		$route: {
			async handler() {
				await this.getCategories();
			},
			immediate: true
		}
	}
}
</script>
