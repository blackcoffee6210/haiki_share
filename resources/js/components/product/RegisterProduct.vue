<template>
	<main class="l-main">
		<div class="p-product-form">
			
			<h2 class="c-title p-product-form__title">商品出品</h2>
			
			<div class="p-product-form__background">
				<!-- ローディング -->
				<Loading color="#f96204" v-show="loading"/>
				
				<!-- フォームエリア	-->
				<form class="p-product-form__form"
							v-show="!loading"
							@submit.prevent="submit">
					
					<!-- 商品画像	-->
					<div class="u-p-relative">
						<label class="p-product-form__label-img"
									 :class="{
											'p-product-form__label-img__err': errors.image,
											'p-product-form__img--enter': isEnter
									 }"
									 @dragenter="dragEnter"
									 @dragleave="dragLeave"
									 @dragover.prevent
									 @drop.stop="dropFile">
							<span class="p-product-form__label-text"
										v-if="!preview">ドラッグ&ドロップ<br>またはファイルを選択
							</span>
							<input type="file"
										 class="p-product-form__img"
										 @change="onFileChange">
							<output class="p-product-form__output"
											v-if="preview">
								<img :src="preview"
										 class="p-product-form__output-img"
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
					<label for="category_id"
								 class="c-label p-product-form__label">カテゴリー
					</label>
					<select class="c-select p-product-form__input"
									:class="{ 'c-select__err': errors.category_id }"
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
					<label for="name"
								 class="c-label p-product-form__label">商品名
					</label>
					<input type="text"
								 class="c-input p-product-form__input"
								 :class="{ 'c-input__err': errors.name || maxCounter(product.name,50) }"
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
					<label for="detail"
								 class="c-label p-product-form__label u-mt0">商品の内容
					</label>
					<textarea	class="c-input p-product-form__textarea"
										 :class="{ 'c-input__err': errors.detail || maxCounter(product.detail, 255) }"
										 id="detail"
										 v-model="product.detail"
										 placeholder="商品の内容を入力してください"
					></textarea>
					<div class="u-d-flex u-space-between">
						<!-- エラーメッセージ（フロントエンド） -->
						<div v-if="maxCounter(product.detail, 255) && !errors.detail"
								 class="p-error">
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
						<p class="c-counter"
							 :class="{ 'c-counter--err': maxCounter(product.detail,255) }">
							{{ product.detail.length }}/255
						</p>
					</div>
					
					<!-- 賞味期限 -->
					<label for="expire_date"
								 class="c-label p-product-form__label u-mt0">賞味期限
					</label>
					<input type="text"
								 onfocusin="this.type='date'"
								 onfocusout="this.type='text'"
								 class="c-input p-product-form__input"
								 :class="{ 'c-input__err': errors.expire }"
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
					<label for="price"
								 class="c-label p-product-form__label">金額
					</label>
					<div class="u-d-flex">
						<input type="text"
									 class="c-input p-product-form__input p-product-form__input--yen"
									 :class="{ 'c-input__err': errors.price || errorMessage }"
									 id="price"
									 v-model="product.price"
									 @input="validatePrice"
									 placeholder="50〜10000円の間で入力してください">
						<div class="p-product-form__yen"
								 :class="{'p-product-form__yen__err': errors.price || errorMessage }">円
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
					
					
					<div class="p-product-form__btn-container">
						<!-- ボタン -->
						<a @click="$router.back()"
							 class="c-btn c-btn--white p-product-form__btn p-product-form__btn--back">もどる
						</a>
						<!-- ボタン	-->
						<button class="c-btn p-product-form__btn"
										type="submit">出品する
						</button>
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
			
			// FileReaderを使用して画像を読み込み、プレビューとして表示
			const reader = new FileReader();
			reader.onload = e => {
				this.preview = e.target.result; // プレビュー用のデータURLをセット
				this.product.image = file; // 実際に送信するファイルをセット
			};
			reader.readAsDataURL(file); // ファイルをデータURLとして読み込む
		},
		maxCounter(content, maxValue) { //カウンターの文字数上限
			return content.length > maxValue;
		},
		async getCategories() { //カテゴリー取得
			const response = await axios.get('/api/categories');
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったら
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			this.categories = response.data; //プロパティに値をセットする
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
			this.preview = '';
			this.product.image = null;
			this.$el.querySelector('input[type="file"]').value = null;
		},
		async submit() { //商品登録メソッド
			this.loading = true; //ローティングを表示する
			
			const formData = new FormData;
			formData.append('image',       this.product.image);
			formData.append('category_id', this.product.category_id);
			formData.append('name',        this.product.name);
			formData.append('detail',      this.product.detail);
			formData.append('price',       this.product.price);
			formData.append('expire',      this.product.expire);
			
			const response = await axios.post('/api/products', formData); //商品登録APIを呼び出す
			
			this.loading = false; //API通信が終わったらローディングを非表示にする
			
			if(response.status === UNPROCESSABLE_ENTITY) { //responseステータスがバリデーションエラーなら後続の処理を行う
				this.errors = response.data.errors;          //responseエラーメッセージをプロパティに格納する
				return false;                                //後続の処理を抜ける
			}
			this.reset(); //送信が完了したら入力値をクリアする
			
			if(response.status !== CREATED) { //responseステータスがCREATEDじゃなかったら(商品登録できなかったら)後続の処理を行う
				this.$store.commit('error/setCode', response.status); //エラー情報を渡す
				return false;                                               //後続の処理を抜ける
			}
			this.$store.commit('message/setContent', { //上のif文を抜けたら登録成功なので、メッセージを登録する
				content: '商品を登録しました！'
			});
			
			this.$router.push({ name: 'index' }); //トップページへ移動する
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
























