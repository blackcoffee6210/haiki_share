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
						<label class="p-register-product__label-img">
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
					<select class="c-input p-register-product__input"
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
					
					<!-- 記事の名前	-->
					<input type="text"
								 class="c-input p-register-product__input"
								 id="name"
								 v-model="product.name"
								 placeholder="商品名を入力してください">
					<!-- エラーメッセージ	-->
					<div v-if="errors">
						<div v-for="msg in errors.name" :key="msg" class="p-error">{{ msg }}</div>
					</div>
					
					<!-- 商品詳細	-->
					<textarea	class="c-input p-register-product__textarea"
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
					<input type="date"
								 class="c-input p-register-product__input"
								 id="expire" v-model="product.expire">
					<!-- エラーメッセージ	-->
					<div v-if="errors">
						<div v-for="msg in errors.expire" :key="msg" class="p-error">{{ msg }}</div>
					</div>
					
					<!-- 金額	-->
					<div class="u-d-flex">
						<input type="text"
									 class="c-input p-register-product__input p-register-product__input--yen"
									 id="price"
									 v-model="product.price"
									 placeholder="1000">
						<div class="p-register-product__yen">円</div>
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
				price: '',
				expire: '',
			},
			//カテゴリーを格納するプロパティ
			categories: {},
			//データURLを格納するプロパティ
			preview: null,
			//エラーメッセージを格納するプロパティ
			errors: null,
			//ローディングを表示するかどうかを判定するプロパティ
			loading: false
		}
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
			this.article.image = event.target.files[0];
		},
		//入力欄の値とプレビュー表示をクリアするメソッド
		reset() {
			this.preview = '';
			this.product.image = null;
			this.$el.querySelector('input[type="file"]').value = null;
		},
		//商品登録メソッド
		async submit() {
			//ローティングを表示する
			this.loading = true;
			
			const formData = new FormData;
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
			
			//上のif文を抜けたら(登録成功)、メッセージを登録する
			this.$store.commit('message/setContent', {
				content: '商品を登録しました！'
			});
			
			//トップページへ移動する
			this.$router.push({ name: 'index' });
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
























