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
					
					<form class="p-edit-profile__form"
								v-show="!loading"
								@submit.prevent="update">
						
						<!-- ユーザー画像	-->
						<div class="u-p-relative">
							<label class="p-edit-profile__label-img"
										 :class="{ 'p-edit-profile__label-img__err': errors.image }">
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
							<div class="p-edit-profile__img-text"
									 v-if="!preview">プロフィール画像を設定する
							</div>
						</div>
						<!-- エラーメッセージ	-->
						<div v-if="errors">
							<div v-for="msg in errors.image"
									 :key="msg"
									 class="p-error">{{ msg }}
							</div>
						</div>
						
						<!-- 名前	-->
						<label for="name"
									 class="c-label p-edit-profile__label">
							<span v-show="user.group === 1">お名前</span>
							<span v-show="user.group === 2">コンビニ名</span>
						</label>
						<input type="text"
									 id="name"
									 class="c-input p-edit-profile__input"
									 :class="{ 'c-input__err': errors.name }"
									 v-model="user.name"
									 :placeholder="name">
						<!-- エラーメッセージ	-->
						<div v-if="errors">
							<div v-for="msg in errors.name"
									 :key="msg"
									 class="p-error">{{ msg }}
							</div>
						</div>
						
						<!-- 都道府県が変わることは考えにくいので、プロフィール編集画面には含めない -->
						
						<!-- 支店名	-->
						<div v-if="isShopUser">
							<label for="branch"
										 class="c-label p-edit-profile__label">支店名</label>
							<input v-model="user.branch"
										 type="text"
										 id="branch"
										 class="c-input p-edit-profile__input"
										 :class="{ 'c-input__err': errors.branch }"
										 placeholder="渋谷支店">
							<!-- エラーメッセージ	-->
							<div v-if="errors">
								<div v-for="msg in errors.branch"
										 :key="msg"
										 class="p-error">{{ msg }}
								</div>
							</div>
						</div>
						
						<!-- 住所	-->
						<div v-if="isShopUser">
							<label for="address"
										 class="c-label p-edit-profile__label">住所</label>
							<input v-model="user.address"
										 type="text"
										 id="address"
										 class="c-input p-edit-profile__input"
										 :class="{ 'c-input__err': errors.address }"
										 placeholder="渋谷１丁目">
							<!-- エラーメッセージ	-->
							<div v-if="errors">
								<div v-for="msg in errors.address"
										 :key="msg"
										 class="p-error">{{ msg }}
								</div>
							</div>
						</div>
						
						<!-- メール	-->
						<label for="email"
									 class="c-label p-edit-profile__label">Eメール</label>
						<input v-model="user.email"
									 type="text"
									 id="email"
									 class="c-input p-edit-profile__input"
									 :class="{ 'c-input__err': errors.email }"
									 placeholder="mail@haiki_share.com">
						<!-- エラーメッセージ	-->
						<div v-if="errors">
							<div v-for="msg in errors.email"
									 :key="msg"
									 class="p-error">{{ msg }}
							</div>
						</div>
						
						<!-- 自己紹介文	-->
						<label for="introduce"
									 class="c-label p-edit-profile__label">自己紹介文</label>
						<input v-model="user.introduce"
									 type="text"
									 id="introduce"
									 class="c-input p-edit-profile__input"
									 :class="{ 'c-input__err': errors.introduce }"
									 placeholder="こんにちは。よろしくお願いします。">
						<!-- エラーメッセージ	-->
						<div v-if="errors">
							<div v-for="msg in errors.introduce"
									 :key="msg"
									 class="p-error">{{ msg }}
							</div>
						</div>
						
						<!-- ボタン -->
						<div class="p-edit-profile__btn-container">
							<a @click="$router.back()"
								 class="c-btn c-btn--white p-edit-profile__btn--back">もどる
							</a>
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
			user: {         //ユーザーの都道府県が変わることは考えにくいので編集項目に入れない
				image: '',    //プロフィール画像
				group: '',    //コンビニユーザーか利用者を判別
				name: '',     //ユーザー名またはコンビニ名
				branch: '',   //支店名（コンビニユーザー）
				address: '',  //住所（コンビニユーザー）
				email: '',    //Eメール
				introduce: '' //自己紹介文
			},
			loading: false, //ローディング
			preview: null,  //画像プレビュー
			errors: {       //エラーメッセージ
				image: null,
				name: null,
				branch: null,
				address: null,
				email: null,
				introduce: null
			},
		}
	},
	computed: {
		...mapGetters({
			isShopUser: 'auth/isShopUser', //コンビニユーザーならtrueを返す
		}),
		name() { //名前インプットエリアのplaceholderを利用者とお店で切り替える
			switch (this.user.group) {
				case 1: //利用者なら「ハイキ君」を返す
					return 'ハイキ君';
				case 2: //コンビニユーザーなら「ファミリーストア」を返す
					return 'ファミリーストア';
			}
		}
	},
	methods: {
		async getUser() { //ユーザー情報取得
			const response = await axios.get(`/api/users/${this.id}`); //API接続
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			this.user = response.data; //responseデータをuserプロパティに代入
		},
		onFileChange(event) { //フォームでファイルが選択されたら実行される
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
			this.user.image = event.target.files[0]; //データに入力値のファイルを代入
		},
		reset() { //入力欄の値とプレビュー表示をクリアするメソッド
			this.preview = '';
			this.user.image = null;
			this.$el.querySelector('input[type="file"]').value = null;
		},
		async update() { //プロフィール更新処理
			this.loading = true; //ローディングを表示する
			
			const formData = new FormData;
			formData.append('image',     this.user.image);
			formData.append('name',      this.user.name);
			formData.append('branch',    this.user.branch);
			formData.append('address',   this.user.address);
			formData.append('email',     this.user.email);
			formData.append('introduce', this.user.introduce);
			
			const response = await axios.post(`/api/users/${this.id}/updateProfile`, formData); //API通信
			
			this.loading = false; //API通信が終わったらローディングを非表示にする
			
			if(response.status === UNPROCESSABLE_ENTITY) { //responseステータスがバリデーションエラーならエラーメッセージをセット
				this.errors = response.data.errors;          //レスポンスのエラーメッセージを格納する
				return false;                                //処理を抜ける
			}
			this.reset(); //送信が完了したら入力値をクリアする
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			
			this.$store.commit('message/setContent', { //メッセージ登録
				content: 'プロフィールを更新しました！',
			})
			
			this.$router.push({name: 'user.mypage', params: { id: this.id }}); //マイページに移動する
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
