<template>
	<main class="l-main">
		<div class="p-product-form">
			<h2 class="c-title p-product-form__title">商品の編集</h2>
			<div class="p-product-form__background">
				<!-- ローディング -->
				<Loading color="#f96204" v-show="loading"/>
				
				<form class="p-product-form__form"
							v-show="!loading"
							@submit.prevent="update">
					
					<!-- 画像	-->
					<div class="u-p-relative">
						<label class="p-product-form__label-img"
									 :class="{ 'p-product-form__label-img__err': errors.image }">
							<input type="file"
										 class="p-product-form__img"
										 @change="onFileChange">
							<output class="p-product-form__output"
											v-if="preview">
								<img :src="preview"
										 class="p-product-form__output-img"
										 alt="">
							</output>
							<img :src="product.image"
									 v-if="!preview"
									 alt=""
									 class="p-product-form__img p-product-form__img--edit">
						</label>
						<div class="p-product-form__img-text"
								 v-if="!preview">
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
					<select class="c-input p-product-form__input"
									:class="{ 'c-select__err': errors.category_id }"
									id="category_id"
									v-model="product.category_id">
						<option value="" disabled>カテゴリーを選択してください</option>
						<option :value="category.id"
										v-for="category in categories"
										:key="category.id">{{ category.name }}</option>
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
								 :class="{ 'c-select__err': errors.name }"
								 id="name"
								 v-model="product.name"
								 placeholder="商品の名前を入力してください">
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
					
					<!-- 賞味期限は変更できないようにする(不正防止)-->
					<!--&lt;!&ndash; 賞味期限 &ndash;&gt;-->
					<!--<label for="expire_date"-->
					<!--			 class="c-label p-product-form__label">賞味期限-->
					<!--</label>-->
					<!--<input type="text"-->
					<!--			 onfocusin="this.type='date'"-->
					<!--			 onfocusout="this.type='text'"-->
					<!--			 class="c-input p-product-form__input"-->
					<!--			 :class="{ 'c-input__err': errors.expire }"-->
					<!--			 id="expire_date"-->
					<!--			 readonly-->
					<!--			 :min="tomorrow"-->
					<!--			 :max="maxDate"-->
					<!--			 placeholder="本日以降の日付を選択してください"-->
					<!--			 v-model="product.expire">-->
					
					<!-- 金額	-->
					<label for="price"
								 class="c-label p-product-form__label">金額
					</label>
					<div class="u-d-flex">
						<input type="text"
									 class="c-input p-product-form__input p-product-form__input--yen"
									 :class="{ 'c-select__err': errors.price }"
									 id="price"
									 v-model="product.price"
									 placeholder="1000">
						<div class="p-product-form__yen"
								 :class="{ 'c-select__err': errors.price }">円</div>
					</div>
					<!-- エラーメッセージ	-->
					<div v-if="errors">
						<div v-for="msg in errors.price"
								 :key="msg"
								 class="p-error">{{ msg }}
						</div>
					</div>
					
					<div class="p-product-form__btn-container">
						<!-- 削除ボタン	-->
						<button class="c-btn c-btn--white p-product-form__btn--delete"
										@click="deleteProduct"
										type="button">削除する
						</button>
						<!-- 更新ボタン -->
						<button class="c-btn p-product-form__btn p-product-form__btn--update"
										type="submit">更新する
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
import { OK, UNPROCESSABLE_ENTITY } from "../../util";

export default {
	name: "EditProduct",
	props: {
		id: {
			type: String,
			required: true
		}
	},
	components: {
		Loading
	},
	data() {
		return {
			loading: false, //ローディングを表示するかどうかを判定するプロパティ
			product: {},    //商品情報
			categories: {}, //カテゴリー
			preview: null,  //画像プレビュー
			errors: {       //エラーメッセージを格納するプロパティ
				image: null,
				category_id: null,
				name: null,
				detail: null,
				price: null
			},
		}
	},
	computed: {
		...mapGetters({
			userId: 'auth/userId'
		})
	},
	methods: {
		async getCategories() { //カテゴリー取得
			const response = await axios.get('/api/categories');
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			
			this.categories = response.data; //プロパティに値をセットする
		},
		async getProduct() { //商品情報取得
			this.loading = true; //ローティングを表示する
			
			const response = await axios.get(`/api/products/${this.id}`); //API通信
			
			this.loading = false; //API通信が終わったらローディングを非表示にする
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったら後続の処理を行う
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			
			this.product = response.data; //プロパティに値をセットする
			
			if(this.product.is_purchased) { //購入されていたらマイページに遷移する
				this.$router.push({ name: 'user.mypage', params: { id: this.product.user_id.toString()}});
			}
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
		async update() { //画像更新処理
			if(this.product.is_purchased) { //この商品が購入されていたらボタンを押せなくする
				alert('ユーザーに購入された商品は編集できません');
				return false;
			}
			this.loading = true; //ローティングを表示する
			
			const formData = new FormData;
			formData.append('user_id',     this.userId);
			formData.append('image',       this.product.image);
			formData.append('category_id', this.product.category_id);
			formData.append('name',        this.product.name);
			formData.append('detail',      this.product.detail);
			formData.append('price',       this.product.price);
			
			const response = await axios.post(`/api/products/${this.product.id}`, formData); //API通信
			
			this.loading = false; //API通信が終わったらローディングを非表示にする
			
			if(response.status === UNPROCESSABLE_ENTITY) { //responseステータスがバリデーションエラーなら後続の処理を行う
				this.errors = response.data.errors; //レスポンスのエラーメッセージを格納する
				return false;
			}
			this.reset(); //送信が完了したら入力値をクリアする
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			
			// this.percent = response.data;
			// console.log('%');
			// console.log(this.percent);
			
			this.$store.commit('message/setContent', { //メッセージ登録
				content: '商品が更新されました！'
			})
			
			this.$router.push({ name: 'product.detail',
													params: { id: this.id.toString() }}); //商品詳細ページへ移動する
		},
		async deleteProduct() { //商品の削除
			if(confirm('商品を削除しますか?')) {
				this.loading = true; //ローティングを表示する
				
				const response = await axios.delete(`/api/products/${this.id}`); //API通信
				
				this.loading = false; //API通信が終わったらローディングを非表示にする
				
				if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
				this.$store.commit('message/setContent', { //メッセージ登録
					content: '商品を削除しました'
				});
				
				this.$router.push({ name: 'user.mypage',
														params: { id: this.userId.toString() }}); //マイページに移動する
			}
		}
	},
	watch: {
		$route: {
			async handler() {
				await this.getCategories();
				await this.getProduct();
			},
			immediate: true
		}
	}
}
</script>
