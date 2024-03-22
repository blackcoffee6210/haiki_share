<template>
	<div class="l-main">
		<!-- プロフィール編集	-->
		<main class="l-main__2column">
			<div class="p-edit-profile">
				
				<!-- タイトル -->
				<h2 class="c-title p-edit-profile__title">プロフィール編集</h2>
				
				<div class="p-edit-profile__background">
					<!-- ローディング -->
					<Loading v-show="loading" />
					
					<form class="p-edit-profile__form" v-show="!loading" @submit.prevent="update">
						
						<!-- ユーザー画像	-->
						<div class="u-p-relative">
							<label class="p-edit-profile__label-img"
										 :class="{ 'p-edit-profile__label-img__err': errors.image,
										  				 'p-edit-profile__img--enter': isEnter
										 }"
										 @dragenter="dragEnter"
										 @dragleave="dragLeave"
										 @dragover.prevent
										 @drop.stop="dropFile">
								<span class="p-edit-profile__label-text" v-if="!preview">ドラッグ&ドロップ<br>またはファイルを選択</span>
								<input type="file"
											 class="p-edit-profile__img"
											 @change="onFileChange">
								<output class="p-edit-profile__output"
												v-if="preview">
									<img :src="preview"
											 class="p-edit-profile__output-img"
											 alt="">
								</output>
								<img :src="user.image"
										 v-if="!preview"
										 alt=""
										 class="p-edit-profile__img"
										 :class="{ 'p-edit-profile__hasImg': user.image }">
							</label>
						</div>
						<!-- エラーメッセージ	-->
						<div v-if="errors">
							<div v-for="msg in errors.image"
									 :key="msg"
									 class="p-error">{{ msg }}
							</div>
						</div>
						
						<!-- 名前	-->
						<label for="name" class="c-label p-edit-profile__label">
							<span v-show="user.group === 1">お名前</span>
							<span v-show="user.group === 2">コンビニ名</span>
						</label>
						<input type="text"
									 id="name"
									 class="c-input p-edit-profile__input"
									 :class="{ 'c-input__err': errors.name || maxCounter(user.name,50)}"
									 v-model="user.name"
									 :placeholder="placeholderText">
						<div class="u-d-flex u-space-between">
							<!-- エラーメッセージ（フロントエンド） -->
							<div v-if="maxCounter(user.name, 50) && !errors.name" class="p-error">
								<p>
									<span v-show="user.group === 1">お名前</span>
									<span v-show="user.group === 2">コンビニ名</span>
									は50文字以下で指定してください
								</p>
							</div>
							<!-- エラーメッセージ（バックエンド）	-->
							<div v-if="errors">
								<div v-for="msg in errors.name"
										 :key="msg"
										 class="p-error">
									{{ msg }}
								</div>
							</div>
							<!-- 文字数カウンター -->
							<p class="c-counter" :class="{ 'c-counter--err': maxCounter(user.name,50) }">
								{{ user.name.length }}/50
							</p>
						</div>
						
						<!-- 都道府県が変わることは考えにくいので、プロフィール編集画面には含めない -->
						
						<!-- 支店名	-->
						<div v-if="isShopUser">
							<label for="branch" class="c-label p-edit-profile__label">支店名</label>
							<input v-model="user.branch"
										 type="text"
										 id="branch"
										 class="c-input p-edit-profile__input"
										 :class="{ 'c-input__err': errors.branch || maxCounter(user.branch, 50) }"
										 placeholder="渋谷支店">
							<div class="u-d-flex u-space-between">
								<!-- エラーメッセージ（フロントエンド） -->
								<div v-if="maxCounter(user.branch, 50) && !errors.branch" class="p-error">
									<p>支店名は50文字以下で指定してください</p>
								</div>
								<!-- エラーメッセージ（バックエンド）	-->
								<div v-if="errors">
									<div v-for="msg in errors.branch"
											 :key="msg"
											 class="p-error">
										{{ msg }}
									</div>
								</div>
								<!-- 文字数カウンター -->
								<p class="c-counter" :class="{ 'c-counter--err': maxCounter(user.branch,50) }">
									{{ user.branch.length }}/50
								</p>
							</div>
						</div>
						
						<!-- 住所	-->
						<div v-if="isShopUser">
							<label for="address" class="c-label p-edit-profile__label">住所</label>
							<input v-model="user.address"
										 type="text"
										 id="address"
										 class="c-input p-edit-profile__input"
										 :class="{ 'c-input__err': errors.address || maxCounter(user.address, 255) }"
										 placeholder="渋谷１丁目">
							<div class="u-d-flex u-space-between">
								<!-- エラーメッセージ（フロントエンド） -->
								<div v-if="maxCounter(user.address, 255) && !errors.address" class="p-error">
									<p>住所は255文字以下で指定してください</p>
								</div>
								<!-- エラーメッセージ（バックエンド）	-->
								<div v-if="errors">
									<div v-for="msg in errors.address"
											 :key="msg"
											 class="p-error">
										{{ msg }}
									</div>
								</div>
								<!-- 文字数カウンター -->
								<p class="c-counter" :class="{ 'c-counter--err': maxCounter(user.address,255) }">
									{{ user.address.length }}/255
								</p>
							</div>
						</div>
						
						<!-- メール	-->
						<label for="email" class="c-label p-edit-profile__label">Eメール</label>
						<input v-model="user.email"
									 type="text"
									 id="email"
									 class="c-input p-edit-profile__input"
									 :class="{ 'c-input__err': errors.email ||
									 					 maxCounter(user.email, 255)
									 }"
									 placeholder="mail@haiki_share.com">
						<div class="u-d-flex u-space-between">
							<!-- エラーメッセージ（フロントエンド） -->
							<div v-if="maxCounter(user.email, 255) && !errors.email" class="p-error">
								<p>Eメールアドレスは255文字以下で指定してください</p>
							</div>
							<!-- エラーメッセージ（バックエンド）	-->
							<div v-if="errors">
								<div v-for="msg in errors.email"
										 :key="msg"
										 class="p-error">
									{{ msg }}
								</div>
							</div>
						</div>
						
						<!-- 自己紹介文	-->
						<label for="introduce" class="c-label p-edit-profile__label">自己紹介文</label>
						<textarea v-model="user.introduce"
											id="introduce"
											class="c-input p-edit-profile__textarea"
											:class="{ 'c-input__err': errors.introduce || maxCounter(user.introduce, 255) }"
											placeholder="こんにちは。よろしくお願いします。"></textarea>
						<div class="u-d-flex u-space-between">
							<!-- エラーメッセージ（フロントエンド） -->
							<div v-if="maxCounter(user.introduce, 255) && !errors.introduce" class="p-error">
								<p>自己紹介文は255文字以下で指定してください</p>
							</div>
							<!-- エラーメッセージ（バックエンド）	-->
							<div v-if="errors">
								<div v-for="msg in errors.introduce"
										 :key="msg"
										 class="p-error">
									{{ msg }}
								</div>
							</div>
							<!-- 文字数カウンター -->
							<p class="c-counter" :class="{ 'c-counter--err': maxCounter(user.introduce,255) }">
								{{ user.introduce.length }}/255
							</p>
						</div>
						
						<!-- ボタン -->
						<div class="p-edit-profile__btn-container">
							<a @click="$router.back()" class="c-btn c-btn--white p-edit-profile__btn--back">もどる</a>
							<button class="c-btn" type="submit">更新する</button>
						</div>
						
					</form>
				</div>
			</div>
		</main>
		
		<!-- サイドバー	-->
		<Sidebar :id="id" />
	</div>
</template>

<script>
import Loading        from "../Loading";
import Sidebar        from "../Sidebar";
import { mapGetters } from "vuex";
import { OK, UNPROCESSABLE_ENTITY } from "../../util";

export default {
	name: "EditProfile",
	props: {
		id: {
			type: String,
			required: true
		}
	},
	components: {
		Loading,
		Sidebar
	},
	data() {
		return {
			preview: null,  //画像プレビュー
			loading: false, //ローディング
			user: {         //ユーザーの都道府県が変わることは考えにくいので編集項目に入れない
				image: '',    //プロフィール画像
				group: '',    //コンビニユーザーか利用者を判別
				name: '',     //ユーザー名またはコンビニ名
				branch: '',   //支店名（コンビニユーザー）
				address: '',  //住所（コンビニユーザー）
				email: '',    //Eメール
				introduce: '' //自己紹介文
			},
			errors: {       //エラーメッセージ
				image: null,
				name: null,
				branch: null,
				address: null,
				email: null,
				introduce: null
			},
			isEnter: false, //画像のクラスバインドを行う
			files: [],
			errorMessage: ''
		}
	},
	computed: {
		...mapGetters({
			isShopUser: 'auth/isShopUser', //コンビニユーザーならtrueを返す
		}),
		placeholderText() {
			return this.user.group === 1 ? 'ハイキ君' : 'ファミリーストア';
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
					this.user.image = file; // 実際に送信するファイルをセット
				};
				reader.readAsDataURL(file); // ファイルをデータURLとして読み込む
				
			}catch (error) {
				console.error('ドラッグ＆ドロップ処理にエラーが発生しました。', error);
			}
		},
		maxCounter(content, maxValue) { //カウンターの文字数上限
			return (content ? content.length : 0) > maxValue;
		},
		async getUser() { //ユーザー情報取得
			try {
				const response = await axios.get(`/api/users/${this.id}`); //API接続
				
				if(response.status === OK) { //成功
					// this.user = response.data; //responseデータをuserプロパティに代入
					this.user = {
						image: response.data.image || '',
						group: parseInt(response.data.group),
						name: response.data.name || '',
						branch: response.data.branch || '',
						address: response.data.address || '',
						email: response.data.email || '',
						introduce: response.data.introduce || '',
					};
					
				}else { //失敗
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
			}catch(error) {
				console.error('ユーザー情報取得中にエラーが発生しました', error);
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
				this.user.image = file; // 実際に送信するファイルをセット
			};
			reader.readAsDataURL(file); // ファイルをデータURLとして読み込む
		},
		reset() { //入力欄の値とプレビュー表示をクリアするメソッド
			this.preview = '';
			this.user.image = null;
			this.$el.querySelector('input[type="file"]').value = null;
		},
		async update() { //プロフィール更新処理
			this.loading = true; //ローディングを表示する
			
			try {
				const formData = new FormData;
				formData.append('image',     this.user.image);
				formData.append('name',      this.user.name);
				formData.append('branch',    this.user.branch);
				formData.append('address',   this.user.address);
				formData.append('email',     this.user.email);
				formData.append('introduce', this.user.introduce);
				
				const response = await axios.post(`/api/users/${this.id}/updateProfile`, formData); //API通信
				
				if(response.status === OK) { //成功
					this.$store.commit('message/setContent', { content: 'プロフィールを更新しました！' }); //メッセージ登録
					this.$router.push({ name: 'user.mypage', params: { id: this.id }}); //マイページに移動する
					
				}else if(response.status === UNPROCESSABLE_ENTITY) { //responseステータスがバリデーションエラーならエラーメッセージをセット
					this.errors = response.data.errors; //レスポンスのエラーメッセージを格納する
					return false;                       //処理を抜ける
					
				}else {
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
			}catch (error) {
				console.error('更新に失敗しました。', error);
				
			}finally {
				this.loading = false; //ローディングを非表示にする
			}
		}
	},
	watch: { //$routeを監視してページが変わったときにgetUserが実行されるようにする
		$route: {
			async handler() {
				await this.getUser();
			},
			immediate: true //immediateをtrueにすると、コンポーネントが生成されたタイミングでも実行する
		}
	}
}
</script>
