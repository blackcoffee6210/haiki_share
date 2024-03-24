<template>
	<div class="l-main">
		<div class="p-authForm">
			
			<!-- 利用者とお店の方の切り替えをするタグ -->
			<ul class="p-authForm__group">
				<li class="p-authForm__group__item"
						:class="{ 'p-authForm__group__item--active': registerForm.group === 1 }"
						@click="registerForm.group = 1">利用者の方
				</li>
				<li class="p-authForm__group__item"
						:class="{ 'p-authForm__group__item--active': registerForm.group === 2 }"
						@click="registerForm.group = 2">お店の方
				</li>
			</ul>
			
			<!-- タイトル -->
			<h2 class="c-title p-authForm__title"
					v-show="registerForm.group === 1">新規登録（利用者）
			</h2>
			<h2 class="c-title p-authForm__title"
					v-show="registerForm.group === 2">新規登録（お店の方）
			</h2>
			
			<form class="p-authForm__form" @submit.prevent="register">
				
				<!-- name -->
				<label for="name" class="c-label p-authForm__label">
					<span v-show="registerForm.group === 1">お名前</span>
					<span v-show="registerForm.group === 2">コンビニ名</span>
				</label>
				<input type="text"
							 id="name"
							 class="c-input p-authForm__input"
							 :class="{ 'c-input--err': (registerErrors) ? registerErrors.name : '' ||
							 				 	 maxCounter(registerForm.name,50)
							 }"
							 v-model="registerForm.name"
							 :placeholder="name">
				<!-- エラーメッセージ -->
				<div class="u-d-flex u-space-between">
					<!-- エラーメッセージ（フロントエンド） -->
					<div v-if="maxCounter(registerForm.name,50) && !registerErrors"
							 class="p-error u-mb20">
						<p class="">
							<span v-show="registerForm.group === 1">お名前</span>
							<span v-show="registerForm.group === 2">コンビニ名</span>
							は50文字以下で指定してください
						</p>
					</div>
					<!-- エラーメッセージ（バックエンド）	-->
					<div v-if="registerErrors && registerErrors.name" class="p-error u-mb20">
						<div v-for="msg in registerErrors.name" :key="msg">
							<!--{{ msg }}-->
							{{ msg.replace('お名前', registerForm.group === 1 ? 'お名前' : 'コンビニ名') }}
						</div>
					</div>
					<div v-if="!registerErrors"></div>
					<!-- 文字数カウンター -->
					<p class="c-counter" :class="{ 'c-counter--err': maxCounter(registerForm.name,50) }">
						{{ registerForm.name.length }}/50
					</p>
				</div>
				
				<!-- 支店（コンビニユーザーなら表示） -->
				<div v-show="registerForm.group === 2">
					<label for="branch" class="c-label p-authForm__label u-mt0">支店名</label>
					<input type="text"
								 id="branch"
								 class="c-input p-authForm__input"
								 :class="{ 'c-input--err': (registerErrors) ? registerErrors.branch : '' ||
					 			 					 maxCounter(registerForm.branch,50)
								 }"
								 v-model="registerForm.branch"
								 placeholder="渋谷109前店">
					<!-- エラーメッセージ -->
					<div class="u-d-flex u-space-between">
						<!-- エラーメッセージ（フロントエンド） -->
						<div v-if="maxCounter(registerForm.branch,50) && !registerErrors"
								 class="p-error">
							<p class="u-mb20">支店名は50文字以下で指定してください</p>
						</div>
						<!-- エラーメッセージ（バックエンド）	-->
						<div v-if="registerErrors && registerErrors.branch" class="p-error u-mb20">
							<div v-for="msg in registerErrors.branch" :key="msg">{{ msg }}</div>
						</div>
						<div v-if="!registerErrors"></div>
						<!-- 文字数カウンター -->
						<p class="c-counter"
							 :class="{ 'c-counter--err': maxCounter(registerForm.branch,50) }">
							{{ registerForm.branch.length }}/50
						</p>
					</div>
				</div>
				
				<!-- 都道府県（コンビニユーザーなら表示） -->
				<div v-show="registerForm.group === 2">
					<label for="prefecture" class="c-label p-authForm__label u-mt0">都道府県</label>
					<select id="prefecture"
									class="c-select p-authForm__input"
									:class="{ 'c-select--err': (registerErrors) ? registerErrors.prefecture_id : ''}"
									v-model="registerForm.prefecture_id">
						<option value="" disabled>都道府県を選択してください</option>
						<option v-for="prefecture in prefectures" :value="prefecture.id" :key="prefecture.id">
							{{ prefecture.name }}
						</option>
					</select>
					<!-- エラーメッセージ	-->
					<div v-if="registerErrors && registerErrors.prefecture_id" class="p-error u-mb20">
						<div v-for="msg in registerErrors.prefecture_id" :key="msg">{{ msg }}</div>
					</div>
				</div>
				
				<!-- 住所（コンビニユーザーなら表示） -->
				<div v-show="registerForm.group === 2">
					<label for="address"
								 class="c-label p-authForm__label">住所
					</label>
					<input type="text"
								 id="address"
								 class="c-input p-authForm__input"
								 :class="{ 'c-input--err': (registerErrors) ? registerErrors.address : '' ||
													 maxCounter(registerForm.address,255)
								 }"
								 v-model="registerForm.address"
								 placeholder="渋谷区宇田川町26-4">
					<!-- エラーメッセージ -->
					<div class="u-d-flex u-space-between">
						<!-- エラーメッセージ（フロントエンド） -->
						<div v-if="maxCounter(registerForm.address,255) && !registerErrors"
								 class="p-error">
							<p class="u-mb20">住所は255文字以下で指定してください</p>
						</div>
						<!-- エラーメッセージ（バックエンド）	-->
						<div v-if="registerErrors && registerErrors.address" class="p-error u-mb20">
							<div v-for="msg in registerErrors.address" :key="msg">{{ msg }}</div>
						</div>
						<div v-if="!registerErrors"></div>
						<!-- 文字数カウンター -->
						<p class="c-counter"
							 :class="{ 'c-counter--err': maxCounter(registerForm.address,255) }">
							{{ registerForm.address.length }}/255
						</p>
					</div>
				</div>
				
				<!-- email-->
				<label for="email" class="c-label p-authForm__label u-mt0">Eメール</label>
				<input type="text"
							 id="email"
							 class="c-input p-authForm__input"
							 :class="{ 'c-input--err': (registerErrors) ? registerErrors.email : '' ||
							 					 maxCounter(registerForm.email,255)
							 }"
							 v-model="registerForm.email"
							 placeholder="mail@haiki_share.com">
				<!-- エラーメッセージ -->
				<div class="u-d-flex u-space-between u-mb20">
					<!-- エラーメッセージ（フロントエンド） -->
					<div v-if="maxCounter(registerForm.email,255) && !registerErrors"
							 class="p-error">
						<p class="">Eメールは255文字以下で指定してください</p>
					</div>
					<!-- エラーメッセージ（バックエンド）	-->
					<div v-if="registerErrors && registerErrors.email" class="p-error">
						<div v-for="msg in registerErrors.email" :key="msg">{{ msg }}</div>
					</div>
				</div>
				
				<!-- パスワード -->
				<label for="password" class="c-label p-authForm__label">パスワード</label>
				<input type="password"
							 id="password"
							 class="c-input p-authForm__input"
							 :class="{ 'c-input--err': (registerErrors) ? registerErrors.password : '' ||
												 maxCounter(registerForm.password,255)
							 }"
							 v-model="registerForm.password"
							 placeholder="8文字以上の半角英数字">
				<!-- エラーメッセージ -->
				<div class="u-d-flex u-space-between u-mb20">
					<!-- エラーメッセージ（フロントエンド） -->
					<div v-if="maxCounter(registerForm.password,255) && !registerErrors"
							 class="p-error">
						<p class="">パスワードは255文字以下で指定してください</p>
					</div>
					<!-- エラーメッセージ（バックエンド）	-->
					<div v-if="registerErrors && registerErrors.password" class="p-error">
						<div v-for="msg in registerErrors.password" :key="msg">{{ msg }}</div>
					</div>
				</div>
				
				<!-- パスワード(確認) -->
				<label for="password-confirmation"
							 class="c-label p-authForm__label u-mt0">パスワード(確認)
				</label>
				<input type="password"
							 id="password-confirmation"
							 class="c-input p-authForm__input"
							 :class="{ 'c-input--err': (registerErrors) ? registerErrors.password_confirmation : '' ||
												 maxCounter(registerForm.password_confirmation,255)}"
							 v-model="registerForm.password_confirmation"
							 placeholder="8文字以上の半角英数字">
				<!-- エラーメッセージ -->
				<div class="u-d-flex u-space-between">
					<!-- エラーメッセージ（フロントエンド） -->
					<div v-if="maxCounter(registerForm.password_confirmation,255) && !registerErrors"
							 class="p-error">
						<p class="">パスワード（確認）は255文字以下で指定してください</p>
					</div>
					<!-- エラーメッセージ（バックエンド）	-->
					<div v-if="registerErrors && registerErrors.password_confirmation" class="p-error">
						<div v-for="msg in registerErrors.password_confirmation" :key="msg">{{ msg }}</div>
					</div>
				</div>
				
				<!-- 利用規約 -->
				<div class="p-authForm__checkContainer">
					<input type="checkbox"
								 id="agreement"
								 v-model="checkAgree"
								 class="c-checkbox">
					<router-link class="p-authForm__agreement"
											 target="_blank"
											 :to="{ name: 'agreement'}">利用規約
					</router-link>
					<label for="agreement">に同意する</label>
				</div>
				
				<!-- ボタン -->
				<div class="u-p-relative">
					<button class="c-btn p-authForm__btn"
									:disabled="!checkAgree"
									type="submit">登録する（無料）
					</button>
				</div>
			</form>
			<hr class="p-authForm__uLine">
			<!-- ログイン画面へ遷移	-->
			<router-link :to="{ name: 'login' }"
									 class="c-link">ログインはこちら
			</router-link>
		</div>
	</div>
</template>

<script>
import { mapState } from 'vuex';
import {OK, UNPROCESSABLE_ENTITY} from "../../util";

export default {
	name: "Register",
	data() {
		return {
			prefectures: [], //都道府県
			registerForm: {
				group: 1,                 //利用者とコンビニユーザーを判別する
				name: '',                 //利用者名（コンビニ名）
				branch: '',               //支店名
				prefecture_id: '',        //都道府県ID
				address: '',              //住所
				email: '',                //Eメール
				password: '',             //パスワード
				password_confirmation: '' //パスワード確認
			},
			checkAgree: false,
		}
	},
	computed: {
		...mapState({
			apiStatus     : state => state.auth.apiStatus, //通信失敗の場合、つまりapiStatusがfalseの場合はインデックスへの移動を行わないように制御する
			registerErrors: state => state.auth.registerErrorMessages
		}),
		name() { //名前インプットエリアのplaceholderを利用者とお店で切り替える
			switch (this.registerForm.group) {
				case 1:
					return 'ハイキ君';
				case 2:
					return 'ファミリーストア';
			}
		}
	},
	methods: {
		maxCounter(content, maxValue) { //カウンターの文字数上限
			return content.length > maxValue;
		},
		async getPrefectures() { //都道府県取得
			try {
				const response = await axios.get('/api/prefectures'); //API接続
				
				if(response.status === OK) {
					this.prefectures = response.data; //プロパティに値をセットする
					
				}else {
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
			}catch (error) {
				console.error('都道府県取得処理中にエラーが発生しました ', error);
			}
		},
		async register() { //ユーザー登録
			await this.$store.dispatch('auth/register', this.registerForm); //dispatchメソッドでauthストアのregisterアクションを呼び出す
			
			if(this.apiStatus) { //成功なら
				this.$store.commit('message/setContent', { content: 'ユーザー登録しました！', }); //メッセージを登録
				this.$router.push({name: 'index'}); //トップページ(index画面)に移動する
			}
		},
		clearError() { //エラーメッセージをクリアするメソッド
			this.$store.commit('auth/setRegisterErrorMessages', null);
		}
	},
	created() {
		this.clearError(); //エラーをクリアする
	},
	watch: {
		$route: {
			async handler() {
				await this.getPrefectures();
			},
			immediate: true
		}
	}
}
</script>
