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
									 :class="{ 'p-product-form__label-img__err': errors.image }">
							<input type="file"
										 class="p-product-form__img"
										 
										 @change="onFileChange">
							<output class="p-product-form__output" v-if="preview">
								<img :src="preview"
										 class="p-product-form__output-img"
										 alt="">
							</output>
						</label>
						<div class="p-product-form__img-text" v-if="!preview">
							商品画像を設定する
						</div>
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
								 :class="{ 'c-input__err': errors.name }"
								 id="name"
								 v-model="product.name"
								 placeholder="商品名を入力してください">
					<!-- エラーメッセージ	-->
					<div v-if="errors">
						<div v-for="msg in errors.name"
								 :key="msg"
								 class="p-error">{{ msg }}
						</div>
					</div>
					
					<!-- 商品の内容	-->
					<label for="detail"
								 class="c-label p-product-form__label">商品の内容
					</label>
					<textarea	class="c-input p-product-form__textarea"
										:class="{ 'c-input__err': errors.detail }"
										id="detail"
										v-model="product.detail"
										placeholder="商品の内容を入力してください"
					></textarea>
					<!-- エラーメッセージ	-->
					<div v-if="errors">
						<div v-for="msg in errors.detail"
								 :key="msg"
								 class="p-error">{{ msg }}
						</div>
					</div>
					
					<!-- 賞味期限 -->
					<label for="expire_date"
								 class="c-label p-product-form__label">賞味期限
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
					<!-- エラーメッセージ	-->
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
									 :class="{ 'c-input__err': errors.price }"
									 id="price"
									 v-model="product.price"
									 placeholder="1000">
						<div class="p-product-form__yen"
								 :class="{'p-product-form__yen__err': errors.price }">円
						</div>
					</div>
					<!-- エラーメッセージ	-->
					<div v-if="errors">
						<div v-for="msg in errors.price"
								 :key="msg"
								 class="p-error">{{ msg }}
						</div>
					</div>
					
					
					<div class="p-product-form__btn-container">
						<!-- ボタン -->
						<a @click="$router.back()"
							 class="c-btn c-btn--white p-product-form__btn--back">もどる
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
import { mapGetters } from 'vuex';
import Loading from "../Loading";
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
				image: '',
				category_id: '',
				name: '',
				detail: '',
				expire: '',
				price: ''
			},
			categories: {}, //カテゴリーを格納するプロパティ
			preview: null,  //データURLを格納するプロパティ
			errors: {       //エラーメッセージを格納するプロパティ
				image: null,
				category_id: null,
				name: null,
				detail: null,
				expire: null,
				price: null
			}
		}
	},
	computed: {
		...mapGetters({
			userId: 'auth/userId' //ユーザーID
		}),
		tomorrow() { //明日の日付をYYYY-MM-DDの書式で返す
			let dt = new Date();
			dt.setDate(dt.getDate() + 1);
			return this.formatDate(dt);
		},
		maxDate() { //賞味期限を登録できる最大の日付
			let dt = new Date();
			dt.setDate(dt.getDate() + 30);
			return this.formatDate(dt);
		},
	},
	methods: {
		async getCategories() { //カテゴリー取得
			const response = await axios.get('/api/categories');
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったら
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			this.categories = response.data; //プロパティに値をセットする
		},
		onFileChange(event) { //フォームでファイルが選択されたら実行されるメソッド
			if(event.target.files.length === 0) { //何も選択されていなかったら処理中断
				this.reset();
				return false;
			}
			if(!event.target.files[0].type.match('image.*')) { //ファイルが画像ではなかったら処理中断
				this.reset();
				return false;
			}
			const reader = new FileReader; //FileReaderクラスのインスタンスを取得
			
			reader.onload = e => { //ファイルを読み込み終わったタイミングで実行する処理
				//previewに読み込み結果（データURL）を代入する
				//previewに値が入ると<output>につけたv-ifがtrueと判定される
				//また<output>内部の<img>のsrc属性はpreviewの値を参照しているので
				//結果として画像が表示される
				this.preview = e.target.result;
			}
			reader.readAsDataURL(event.target.files[0]); //ファイルを読み込む(ファイルはデータURL形式で受け取れる(上記onload参照))
			this.product.image = event.target.files[0]; //データに入力値のファイルを代入
		},
		reset() { //入力欄の値とプレビュー表示をクリアするメソッド
			this.preview = '';
			this.product.image = null;
			this.$el.querySelector('input[type="file"]').value = null;
		},
		formatDate(dt) { //日付をYYYY-MM-DDの書式で返すメソッド
			let y = dt.getFullYear();
			let m = ('00' + (dt.getMonth() + 1)).slice(-2);
			let d = ('00' + dt.getDate()).slice(-2);
			return (y + '-' + m + '-' + d);
		},
		async submit() { //商品登録メソッド
			this.loading = true; //ローティングを表示する
			
			const formData = new FormData;
			formData.append('user_id',     this.userId);
			formData.append('image',       this.product.image);
			formData.append('category_id', this.product.category_id);
			formData.append('name',        this.product.name);
			formData.append('detail',      this.product.detail);
			formData.append('price',       this.product.price);
			formData.append('expire',      this.product.expire);
			
			const response = await axios.post('/api/products', formData); //商品登録APIを呼び出す
			
			this.loading = false; //API通信が終わったらローディングを非表示にする
			
			if(response.status === UNPROCESSABLE_ENTITY) { //responseステータスがバリデーションエラーなら後続の処理を行う
				this.errors = response.data.errors; //responseエラーメッセージをプロパティに格納する
				return false; //後続の処理を抜ける
			}
			this.reset(); //送信が完了したら入力値をクリアする
			
			if(response.status !== CREATED) { //responseステータスがCREATEDじゃなかったら(商品登録できなかったら)後続の処理を行う
				this.$store.commit('error/setCode', response.status); //エラー情報を渡す
				return false; //後続の処理を抜ける
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
























