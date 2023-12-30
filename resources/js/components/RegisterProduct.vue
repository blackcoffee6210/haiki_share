<template>
	<main class="l-main">
		<div class="p-register-product">
			
			<h2 class="c-title p-register-product__title">商品出品</h2>
			
			<div class="p-register-product__background">
				<!-- ローディング -->
				<Loading color="#f96204" v-show="loading"/>
				
				<!-- フォームエリア	-->
				<form class="p-register-product__form"
							v-show="!loading"
							@submit.prevent="submit">
					
					<!-- 商品画像	-->
					<div class="u-p-relative">
						<label class="p-register-product__label-img"
									 :class="{ 'p-register-product__label-img__err': errors.image }">
							<input type="file"
										 class="p-register-product__img"
										 
										 @change="onFileChange">
							<output class="p-register-product__output" v-if="preview">
								<img :src="preview"
										 class="p-register-product__output-img"
										 alt="">
							</output>
						</label>
						<div class="p-register-product__img-text" v-if="!preview">
							商品画像を設定する
						</div>
					</div>
					<!-- エラーメッセージ	-->
					<div v-if="errors">
						<div v-for="msg in errors.image" :key="msg" class="p-error">{{ msg }}</div>
					</div>
					
					<!-- カテゴリー -->
					<label for="category_id"
								 class="c-label p-register-product__label">カテゴリー
					</label>
					<select class="c-select p-register-product__input"
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
						<div v-for="msg in errors.category_id" :key="msg" class="p-error">{{ msg }}</div>
					</div>
					
					<!-- 商品名	-->
					<label for="name"
								 class="c-label p-register-product__label">商品名
					</label>
					<input type="text"
								 class="c-input p-register-product__input"
								 :class="{ 'c-input__err': errors.name }"
								 id="name"
								 v-model="product.name"
								 placeholder="商品名を入力してください">
					<!-- エラーメッセージ	-->
					<div v-if="errors">
						<div v-for="msg in errors.name" :key="msg" class="p-error">{{ msg }}</div>
					</div>
					
					<!-- 商品の内容	-->
					<label for="detail"
								 class="c-label p-register-product__label">商品の内容
					</label>
					<textarea	class="c-input p-register-product__textarea"
										:class="{ 'c-input__err': errors.detail }"
										id="detail"
										v-model="product.detail"
										placeholder="商品の内容を入力してください"
					></textarea>
					<!-- エラーメッセージ	-->
					<div v-if="errors">
						<div v-for="msg in errors.detail" :key="msg" class="p-error">{{ msg }}</div>
					</div>
					
					<!-- 賞味期限 -->
					<!-- todo: カエル本を参考に実装 -->
					<label for="expire_date"
								 class="c-label p-register-product__label">賞味期限
					</label>
					<input type="text"
								 onfocusin="this.type='date'"
								 onfocusout="this.type='text'"
								 class="c-input p-register-product__input"
								 :class="{ 'c-input__err': errors.expire }"
								 id="expire_date"
								 :min="tomorrow"
								 :max="maxDate"
								 placeholder="本日以降の日付を選択してください"
								 v-model="product.expire">
					<!-- エラーメッセージ	-->
					<div v-if="errors">
						<div v-for="msg in errors.expire" :key="msg" class="p-error">{{ msg }}</div>
					</div>
					
					<!-- 金額	-->
					<label for="price"
								 class="c-label p-register-product__label">金額
					</label>
					<div class="u-d-flex">
						<input type="text"
									 class="c-input p-register-product__input p-register-product__input--yen"
									 :class="{ 'c-input__err': errors.price }"
									 id="price"
									 v-model="product.price"
									 placeholder="1000">
						<div class="p-register-product__yen"
								 :class="{'p-register-product__yen__err': errors.price }">円
						</div>
					</div>
					<!-- エラーメッセージ	-->
					<div v-if="errors">
						<div v-for="msg in errors.price" :key="msg" class="p-error">{{ msg }}</div>
					</div>
					
					<!-- ボタン	-->
					<button class="c-btn p-register-product__btn" type="submit">出品する</button>
				</form>
			</div>
		</div>
	</main>
</template>

<script>
import { mapGetters } from 'vuex';
import Loading from "./Loading";
import { CREATED, OK, UNPROCESSABLE_ENTITY} from "../util";
export default {
	name: "RegisterProduct",
	components: {
		Loading
	},
	data() {
		return {
			product: {
				image: '',
				category_id: '',
				name: '',
				detail: '',
				expire: '',
				price: ''
			},
			//カテゴリーを格納するプロパティ
			categories: {},
			//データURLを格納するプロパティ
			preview: null,
			//エラーメッセージを格納するプロパティ
			errors: {
				image: null,
				category_id: null,
				name: null,
				detail: null,
				expire: null,
				price: null
			},
			// errors: null,
			//ローディングを表示するかどうかを判定するプロパティ
			loading: false
		}
	},
	computed: {
		...mapGetters({
			//ユーザーID
			userId: 'auth/userId'
		}),
		//明日の日付をYYYY-MM-DDの書式で返す
		tomorrow() {
			let dt = new Date();
			dt.setDate(dt.getDate() + 1);
			return this.formatDate(dt);
		},
		//賞味期限を登録できる最大の日付
		maxDate() {
			let dt = new Date();
			dt.setDate(dt.getDate() + 30);
			return this.formatDate(dt);
		},
	},
	methods: {
		//カテゴリー取得
		async getCategories() {
			const response = await axios.get('/api/category');
			//responseステータスがOKじゃなかったら
			if(response.status !== OK) {
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			//プロパティに値をセットする
			this.categories = response.data;
		},
		//フォームでファイルが選択されたら実行されるメソッド
		onFileChange(event) {
			//何も選択されていなかったら処理中断
			if(event.target.files.length === 0) {
				this.reset();
				return false;
			}
			//ファイルが画像ではなかったら処理中断
			if(!event.target.files[0].type.match('image.*')) {
				this.reset();
				return false;
			}
			//FileReaderクラスのインスタンスを取得
			const reader = new FileReader;
			
			//ファイルを読み込み終わったタイミングで実行する処理
			reader.onload = e => {
				//previewに読み込み結果（データURL）を代入する
				//previewに値が入ると<output>につけたv-ifがtrueと判定される
				//また<output>内部の<img>のsrc属性はpreviewの値を参照しているので
				//結果として画像が表示される
				this.preview = e.target.result;
			}
			
			//ファイルを読み込む
			//読み込まれたファイルはデータURL形式で受け取れる(上記onload参照)
			reader.readAsDataURL(event.target.files[0]);
			//データに入力値のファイルを代入
			this.product.image = event.target.files[0];
		},
		//入力欄の値とプレビュー表示をクリアするメソッド
		reset() {
			this.preview = '';
			this.product.image = null;
			this.$el.querySelector('input[type="file"]').value = null;
		},
		//日付をYYYY-MM-DDの書式で返すメソッド
		formatDate(dt) {
			let y = dt.getFullYear();
			let m = ('00' + (dt.getMonth() + 1)).slice(-2);
			let d = ('00' + dt.getDate()).slice(-2);
			return (y + '-' + m + '-' + d);
		},
		
		//商品登録メソッド
		async submit() {
			//ローティングを表示する
			this.loading = true;
			
			const formData = new FormData;
			formData.append('user_id', this.userId);
			formData.append('image', this.product.image);
			formData.append('category_id', this.product.category_id);
			formData.append('name', this.product.name);
			formData.append('detail', this.product.detail);
			formData.append('price', this.product.price);
			formData.append('expire', this.product.expire);
			
			//商品登録APIを呼び出す
			const response = await axios.post('/api/products', formData);
			//API通信が終わったらローディングを非表示にする
			this.loading =false;
			
			//responseステータスがUNPROCESSABLE_ENTITY(バリデーションエラー)なら後続の処理を行う
			if(response.status === UNPROCESSABLE_ENTITY) {
				//responseエラーメッセージをプロパティに格納する
				this.errors = response.data.errors;
				//後続の処理を抜ける
				return false;
			}
			//送信が完了したら入力値をクリアする
			this.reset();
			
			//responseステータスがCREATEDじゃなかったら(商品登録できなかったら)後続の処理を行う
			if(response.status !== CREATED) {
				//エラー情報を渡す
				this.$store.commit('error/setCode', response.status);
				//後続の処理を抜ける
				return false;
			}
			
			//上のif文を抜けたら登録成功なので、メッセージを登録する
			this.$store.commit('message/setContent', {
				content: '商品を登録しました！'
			});
			
			//トップページへ移動する
			this.$router.push({ name: 'index' });
		}
	},
	created() {
	
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
























