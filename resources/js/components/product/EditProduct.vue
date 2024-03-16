<template>
	<main class="l-main">
		<div class="p-product-form">
			<!-- タイトル -->
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
									 :class="{ 'p-product-form__label-img__err': errors.image,
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
							<img :src="product.image"
									 v-if="!preview"
									 alt=""
									 class="p-product-form__img p-product-form__img--edit">
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
								 :class="{ 'c-input__err': errors.name || maxCounter(product.name,50)}"
								 id="name"
								 v-model="product.name"
								 placeholder="商品の名前を入力してください">
					<div class="u-d-flex u-space-between">
						<!-- エラーメッセージ（フロントエンド） -->
						<div v-if="maxCounter(product.name,50) && !errors.name"
								 class="p-error">
							<p class="u-mb20">商品名は、50文字以下で指定してください。</p>
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
							<p class="u-mb20">商品の内容は、255文字以下で指定してください。</p>
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
					
					<!-- 賞味期限は変更できないようにする(不正防止)-->
					
					<!-- 金額	-->
					<label for="price"
								 class="c-label p-product-form__label u-mt0">金額
					</label>
					<div class="u-d-flex">
						<input type="text"
									 class="c-input p-product-form__input p-product-form__input--yen"
									 :class="{ 'c-input__err': errors.price || errorMessage }"
									 id="price"
									 v-model="product.price"
									 @input="validatePrice"
									 placeholder="1000">
						<div class="p-product-form__yen"
								 :class="{ 'p-product-form__yen__err': errors.price || errorMessage }">円</div>
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
						<!-- 削除ボタン	-->
						<button class="c-btn c-btn--white p-product-form__btn"
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
import Loading        from "../Loading";
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
			categories: [], //カテゴリー
			preview: null,  //画像プレビュー
			errors: {       //エラーメッセージを格納するプロパティ
				image: null,
				category_id: null,
				name: null,
				detail: null,
				price: null
			},
			isEnter: false, //画像のクラスバインドを行う
			files: [],
			errorMessage: ''
		}
	},
	computed: {
		...mapGetters({
			userId: 'auth/userId' //ユーザーID
		})
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
				alert('一度にドロップできるファイルは1つだけです。');
				return;
			}
			
			const file = event.dataTransfer.files[0];
			
			if (!file.type.match('image.*')) { // ドロップされたファイルが画像であるかを確認
				alert('ドロップされたファイルは画像ではありません。');
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
			try {
				const response = await axios.get('/api/categories'); //API通信
				this.categories = response.data; //プロパティに値をセットする
			}catch(error) {
				console.error('カテゴリーの取得に失敗しました', error);
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
		async getProduct() { //商品情報取得
			this.loading = true; //ローティングを表示する
			
			try {
				const response = await axios.get(`/api/products/${this.id}`); //API通信
				
				if(response.status === OK) { //成功なら
					this.product = response.data; //プロパティに値をセットする
				}else {
					this.handleError({ response }, '商品情報取得時にエラーが発生しました');
				}
			}catch (error) {
				this.handleError(error, '商品情報取得時にエラーが発生しました');
			}finally {
				this.loading = false; //ローディングを非表示
			}
		},
		onFileChange(event) { //フォームでファイルが選択されたら実行されるメソッド
			const file = event.target.files[0];
			
			if (!file || !file.type.match('image.*')) { //ファイルがない、または画像ではなかったら処理中断
				this.reset(); // ファイルが画像でなければリセット
				return;
			}

			const reader = new FileReader();
			reader.onload = e => {
				this.preview = e.target.result; // プレビュー用のデータURLをセット
				this.product.image = file;      // 実際に送信するファイルをセット
			};
			reader.readAsDataURL(file); // ファイルをデータURLとして読み込む
		},
		reset() { //入力欄の値とプレビュー表示をクリアするメソッド
			this.preview = null;
			this.product.image = null;
			this.$el.querySelector('input[type="file"]').value = null;
		},
		async update() { //画像更新処理
			if(this.product.is_purchased) { //この商品が購入されていたらボタンを押せなくする
				alert('ユーザーに購入された商品は編集できません');
				return;
			}
			this.loading = true; //ローティングを表示する
			
			try { //例外処理
				const formData = new FormData;
				formData.append('user_id',     this.userId);
				formData.append('image',       this.product.image);
				formData.append('category_id', this.product.category_id);
				formData.append('name',        this.product.name);
				formData.append('detail',      this.product.detail);
				formData.append('price',       this.product.price);
				
				const response = await axios.post(`/api/products/${this.product.id}`, formData); //API通信
				
				if(response.status === OK) { //成功なら
					this.$store.commit('message/setContent', { content: '商品が更新されました！'});
					this.$router.push({ name: 'product.detail',	params: { id: this.id.toString() }}); //商品詳細ページへ移動する
					
				}else if(response.status === UNPROCESSABLE_ENTITY) { //バリデーションエラーなら後続の処理を行う
					this.errors = response.data.errors;
					
				}else {
					this.$store.commit('error/setCode', response.status); //エラーコードをセット
				}
				
			}catch(error) {
				console.error('更新に失敗しました。', error);
				
			}finally {
				this.loading = false; //ローディングを非表示にする
			}
		},
		async deleteProduct() { //商品の削除
			if(!confirm('商品を削除しますか?')) return;
			
			this.loading = true; //ローティングを表示する
			
			try { //例外処理
				const response = await axios.delete(`/api/products/${this.id}`); //API通信
				
				if(response.status === OK) { //成功なら
					this.$store.commit('message/setContent', { content: '商品を削除しました' }); //メッセージ登録
					this.$router.push({ name: 'user.mypage', params: { id: this.userId.toString() }}); //マイページに移動する
					
				}else { //失敗なら
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
			}catch (error) {
				console.error('削除処理に失敗しました', error);
				
			}finally {
				this.loading = false; //ローディングを非表示にする
			}
		},
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
