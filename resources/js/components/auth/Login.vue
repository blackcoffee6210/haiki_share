<template>
	<div class="l-main">
		<div class="p-authForm">
			
			<!-- 利用者とお店の方の切り替えをするタグ -->
			<ul class="p-authForm__group">
				<li class="p-authForm__group__item"
						:class="{ 'p-authForm__group__item--active': loginForm.group === 1 }"
						@click="loginForm.group = 1">利用者の方
				</li>
				<li class="p-authForm__group__item"
						:class="{ 'p-authForm__group__item--active': loginForm.group === 2 }"
						@click="loginForm.group = 2">お店の方
				</li>
			</ul>
			<h2 class="c-title p-authForm__title" v-show="loginForm.group === 1">ログイン（利用者）</h2>
			<h2 class="c-title p-authForm__title" v-show="loginForm.group === 2">ログイン（お店の方）</h2>
			
			<!-- preventをつけてデフォルトのフォーム送信の挙動をキャンセルする -->
			<form class="p-authForm__form" @submit.prevent="login">
				
				<!-- Email	-->
				<label for="email"
							 class="c-label p-authForm__label">Eメール
				</label>
				<input type="text"
							 v-model="loginForm.email"
							 class="c-input p-authForm__input"
							 :class="{ 'c-input--err': (loginErrors) ? loginErrors.email : '' ||
							 					 maxCounter(loginForm.email,255)
							 }"
							 id="email"
							 placeholder="mail@haiki_share.com">
				<!-- エラーメッセージ -->
				<div class="u-d-flex u-space-between u-mb20">
					<!-- エラーメッセージ（フロントエンド） -->
					<div v-if="maxCounter(loginForm.email,255) && !loginErrors"
							 class="p-error">
						<p class="">Eメールは255文字以下で指定してください</p>
					</div>
					<!-- エラーメッセージ（バックエンド）	-->
					<div v-if="loginErrors && loginErrors.email" class="p-error">
						<div v-for="msg in loginErrors.email" :key="msg">{{ msg }}</div>
					</div>
				</div>
				
				<!-- パスワード -->
				<label for="password"
							 class="c-label p-authForm__label">パスワード(半角英数字)
				</label>
				<input type="password"
							 v-model="loginForm.password"
							 class="c-input p-authForm__input"
							 :class="{ 'c-input--err': (loginErrors) ? loginErrors.password : '' ||
							 					  maxCounter(loginForm.password,255)
							 }"
							 id="password"
							 placeholder="*********">
				<!-- エラーメッセージ -->
				<div class="u-d-flex u-space-between">
					<!-- エラーメッセージ（フロントエンド） -->
					<div v-if="maxCounter(loginForm.password,255) && !loginErrors"
							 class="p-error">
						<p class="">パスワードは255文字以下で指定してください</p>
					</div>
					<!-- エラーメッセージ（バックエンド）	-->
					<div v-if="loginErrors && loginErrors.password" class="p-error">
						<div v-for="msg in loginErrors.password" :key="msg">{{ msg }}</div>
					</div>
				</div>
				
				<!-- ログイン保持 -->
				<div class="p-authForm__checkContainer">
					<input type="checkbox"
								 id="remember"
								 class="p-authForm__check"
								 v-model="loginForm.remember">
					<label for="remember">ログイン保持</label>
				</div>
				
				<!-- ログインボタン -->
				<button class="c-btn p-authForm__btn" type="submit">ログイン</button>
			</form>
			
			<!-- パスワードリマインダー	-->
			<router-link :to="{ name: 'password.email' }"
									 class="c-link">パスワードをお忘れの方はこちら</router-link>
			<hr class="p-authForm__uLine">
			<!-- 会員登録へ遷移	-->
			<router-link :to="{ name: 'register' }"
									 class="c-link">会員登録はこちら
			</router-link>
		</div>
	</div>
</template>

<script>
import { mapState } from 'vuex';
export default {
	name: "Login",
	data() {
		return {
			loginForm: {
				group: 1, //利用者かコンビニユーザーかを判別する
				email: '',
				password: '',
				remember: false
			}
		}
	},
	computed: {
		...mapState({
			apiStatus: state   => state.auth.apiStatus,         //通信失敗の場合はインデックスへの移動を行わないように制御する
			loginErrors: state => state.auth.loginErrorMessages //loginErrorMessageを参照する
		})
	},
	methods: {
		maxCounter(content, maxValue) { //カウンターの文字数上限
			return content.length > maxValue;
		},
		async login() { //ログイン
			await this.$store.dispatch('auth/login', this.loginForm); //authストアのloginアクションを呼び出す
			
			if(this.apiStatus) { //成功なら
				this.$store.commit('message/setContent',{ content: 'ログインしました！'}); //メッセージ登録
				this.$router.go(-1); //元々アクセスしたかったページにリダイレクトする
			}
		},
		clearError() { //エラーメッセージをクリアするメソッド(これがないと別のページに行って戻ってくると以前のエラーが表示されたままになる)
			this.$store.commit('auth/setLoginErrorMessages', null);
		},
	},
	created() { //ログインページを表示するタイミング、つまりcreatedライフサイクルフックでエラーをクリアする
		this.clearError();
	}
}
</script>
