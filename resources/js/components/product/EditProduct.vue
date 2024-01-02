<template>
	<main class="l-main">
		<div class="p-edit-product">
			<h2 class="c-title p-edit-product__title">商品の編集</h2>
			<div class="p-edit-product__background">
				<!-- ローディング -->
				<Loading color="#f96204" v-show="loading"/>
				
				<form class="p-edit-product__form"
							v-show="!loading"
							@submit.prevent="update">
					
					<!-- 画像	-->
					<div class="u-p-relative">
						<label class="p-edit-product__label-img"
									 :class="{ 'p-edit-product__label-img__err': errors.image }">
							<input type="file"
										 class="p-edit-product__img"
										 @change="onFileChange">
							<output class="p-edit-product__output"
											v-if="preview">
								<img :src="preview"
										 class="p-edit-product__output-img"
										 alt="">
							</output>
							<img :src="product.image"
									 v-if="!preview"
									 alt=""
									 class="p-edit-product__img">
						</label>
						<div class="p-edit-product__img-text"
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
								 class="c-label p-edit-product__label">カテゴリー
					</label>
					<select class="c-input p-edit-product__input"
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
								 class="c-label p-edit-product__label">商品名
					</label>
					<input type="text"
								 class="c-input p-edit-product__input"
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
								 class="c-label p-edit-product__label">商品の内容
					</label>
					<textarea	class="c-input p-edit-product__textarea"
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
					
					<!--todo: 賞味期限は変更できないようにする(不正防止)-->
					<!--&lt;!&ndash; 賞味期限 &ndash;&gt;-->
					<!--<label for="expire_date"-->
					<!--			 class="c-label p-edit-product__label">賞味期限-->
					<!--</label>-->
					<!--<input type="text"-->
					<!--			 onfocusin="this.type='date'"-->
					<!--			 onfocusout="this.type='text'"-->
					<!--			 class="c-input p-edit-product__input"-->
					<!--			 :class="{ 'c-input__err': errors.expire }"-->
					<!--			 id="expire_date"-->
					<!--			 :min="tomorrow"-->
					<!--			 :max="maxDate"-->
					<!--			 placeholder="本日以降の日付を選択してください"-->
					<!--			 v-model="product.expire">-->
					<!--&lt;!&ndash; エラーメッセージ	&ndash;&gt;-->
					<!--<div v-if="errors">-->
					<!--	<div v-for="msg in errors.expire"-->
					<!--			 :key="msg"-->
					<!--			 class="p-error">{{ msg }}-->
					<!--	</div>-->
					<!--</div>-->
					
					<!-- 金額	-->
					<label for="price"
								 class="c-label p-edit-product__label">金額
					</label>
					<div class="u-d-flex">
						<input type="text"
									 class="c-input p-edit-product__input p-edit-product__input--yen"
									 :class="{ 'c-select__err': errors.price }"
									 id="price"
									 v-model="product.price"
									 placeholder="1000">
						<div class="p-edit-product__yen"
								 :class="{ 'c-select__err': errors.price }">円</div>
					</div>
					<!-- エラーメッセージ	-->
					<div v-if="errors">
						<div v-for="msg in errors.price"
								 :key="msg"
								 class="p-error">{{ msg }}
						</div>
					</div>
					
					<div class="p-edit-product__btn-container">
						<!-- 削除ボタン	-->
						<button class="c-btn p-edit-product__btn p-edit-product__btn--delete"
										@click="deleteProduct"
										type="button">削除する
						</button>
						<!-- 更新ボタン -->
						<button class="c-btn p-edit-product__btn p-edit-product__btn--update"
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
		id: String,
		required: true
	},
	components: {
		Loading
	},
	data() {
		return {
			product: {}, //商品情報
			categories: {}, //カテゴリー
			preview: null, //画像プレビュー
			errors: { //エラーメッセージを格納するプロパティ
				image: null,
				category_id: null,
				name: null,
				detail: null,
				price: null
			},
			loading: false //ローディングを表示するかどうかを判定するプロパティ
		}
	},
	computed: {
		...mapGetters({
			userId: 'auth/userId'
		})
	},
	methods: {
		//カテゴリー取得
		async getCategories() {
			const response = await axios.get('/api/categories');
			//responseステータスがOKじゃなかったらエラーコードをセット
			if(response.status !== OK) {
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			//プロパティに値をセットする
			this.categories = response.data;
		},
		//商品情報取得
		async getProduct() {
			//ローティングを表示する
			this.loading = true;
			//API通信
			const response = await axios.get(`/api/products/${this.id}`);
			//API通信が終わったらローディングを非表示にする
			this.loading =false;
			//responseステータスがOKじゃなかったら後続の処理を行う
			if(response.status !== OK) {
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			//プロパティに値をセットする
			this.product = response.data;
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
		//画像更新処理
		async update() {
			//この商品が購入されていたらボタンを押せなくする
			if(this.product.is_purchased) {
				alert('ユーザーに購入された商品は編集できません');
				return false;
			}
			//ローティングを表示する
			this.loading = true;
			
			const formData = new FormData;
			formData.append('user_id',     this.userId);
			formData.append('image',       this.product.image);
			formData.append('category_id', this.product.category_id);
			formData.append('name',        this.product.name);
			formData.append('detail',      this.product.detail);
			formData.append('price',       this.product.price);
			
			//API通信
			const response = await axios.post(`/api/products/${this.product.id}/update`, formData);
			
			//API通信が終わったらローディングを非表示にする
			this.loading =false;
			
			//responseステータスがUNPROCESSABLE_ENTITY(バリデーションエラー)なら後続の処理を行う
			if(response.status === UNPROCESSABLE_ENTITY) {
				//レスポンスのエラーメッセージを格納する
				this.errors = response.data.errors;
				return false;
			}
			//送信が完了したら入力値をクリアする
			this.reset();
			
			//responseステータスがOKじゃなかったらエラーコードをセット
			if(response.status !== OK) {
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			
			//メッセージ登録
			this.$store.commit('message/setContent', {
				content: '商品が更新されました！'
			})
			
			//商品詳細ページへ移動する
			this.$router.push({ name: 'product.detail',
													params: { id: this.id.toString() }});
		},
		//商品の削除
		async deleteProduct() {
			//この記事が購入されていたらボタンを押せなくする
			if(this.product.is_purchased) {
				alert('ユーザーに購入された記事は削除できません');
				return false;
			}
			
			if(confirm('商品を削除しますか?')) {
				//ローティングを表示する
				this.loading = true;
				//API通信
				const response = await axios.post(`/api/products/${this.id}`);
				//API通信が終わったらローディングを非表示にする
				this.loading =false;
				
				//responseステータスがOKじゃなかったらエラーコードをセット
				if (response.status !== OK) {
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
				//メッセージ登録
				this.$store.commit('message/setContent', {
					content: '商品を削除しました'
				});
				
				//マイページに移動する
				this.$router.push({ name: 'user.mypage',
														params: { id: this.userId.toString() }});
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
