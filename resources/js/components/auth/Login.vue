<template>
	<div class="l-main">
		<div class="p-auth-form">
			<!-- 利用者とお店の方の切り替えをするタグ -->
			<ul class="p-auth-form__group">
				<li class="p-auth-form__group__item"
						:class="{ 'p-auth-form__group__item--active': loginForm.group === 1 }"
						@click="loginForm.group = 1">利用者の方
				</li>
				<li class="p-auth-form__group__item"
						:class="{ 'p-auth-form__group__item--active': loginForm.group === 2 }"
						@click="loginForm.group = 2">お店の方
				</li>
			</ul>
			<h2 class="c-title p-auth-form__title" v-show="loginForm.group === 1">ログイン（利用者）</h2>
			<h2 class="c-title p-auth-form__title" v-show="loginForm.group === 2">ログイン（お店の方）</h2>
			<!-- preventをつけてデフォルトのフォーム送信の挙動をキャンセルする -->
			<form class="p-auth-form__form" @submit.prevent="login">
				
				<!-- Email	-->
				<label for="email"
							 class="c-label p-auth-form__label">Eメール
				</label>
				<input type="text"
							 v-model="loginForm.email"
							 class="c-input p-auth-form__input"
							 :class="{'c-input__err': (loginErrors) ? loginErrors.email : ''}"
							 id="email"
							 placeholder="mail@haiki_share.com">
				<!-- Emailエラーメッセージ	-->
				<div v-if="loginErrors">
					<div v-for="msg in loginErrors.email"
							 :key="msg"
							 class="p-error">{{ msg }}
					</div>
				</div>
				
				<!-- パスワード -->
				<label for="password"
							 class="c-label p-auth-form__label">パスワード(半角英数字)
				</label>
				<input type="password"
							 v-model="loginForm.password"
							 class="c-input p-auth-form__input"
							 :class="{ 'c-input__err': (loginErrors) ? loginErrors.password : ''}"
							 id="password"
							 placeholder="*********">
				<!-- パスワード エラーメッセージ	-->
				<div v-if="loginErrors">
					<div v-for="msg in loginErrors.password"
							 :key="msg"
							 class="p-error">{{ msg }}
					</div>
				</div>
				
				<!-- ログイン保持 -->
				<div class="p-auth-form__check-container">
					<input type="checkbox"
								 id="remember"
								 class="p-auth-form__check"
								 v-model="loginForm.remember">
					<label for="remember"
								 class="p-auth-form__label--check">ログイン保持
					</label>
				</div>
				
				<!-- ログインボタン -->
				<button class="c-btn p-auth-form__btn" type="submit">ログイン</button>
			</form>
			
			<!-- パスワードリマインダー	-->
			<router-link :to="{ name: 'password.email' }"
									 class="c-link">パスワードをお忘れの方はこちら</router-link>
			<hr class="p-auth-form__u-line">
			<!-- 会員登録へ遷移	-->
			<router-link :to="{ name: 'register' }"
									 class="c-link">会員登録はこちら</router-link>
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
				group: 1,
				email: '',
				password: '',
				remember: false
			}
		}
	},
	computed: {
		...mapState({
			//通信失敗の場合、つまりapiStatusがfalseの場合はインデックスへの移動を行わないように制御する
			//trueまたはfalseが入る
			apiStatus: state   => state.auth.apiStatus,
			//loginErrorMessageを参照する
			loginErrors: state => state.auth.loginErrorMessages
		}),
		// ↑ 簡略化した書き方 ↑
		// ===================================================================
		// apiStatus() {
		// 	//通信失敗の場合、つまりapiStatusがfalseの場合はインデックスへの移動を行わないように制御する
		// 	return this.$store.state.auth.apiStatus;
		// },
		// loginErrors() {
		// 	return this.$store.state.auth.loginErrorMessages
		// }
		// ====================================================================
	},
	methods: {
		//ログイン
		async login() {
			//authストアのloginアクションを呼び出す
			await this.$store.dispatch('auth/login', this.loginForm);
			
			//apiStatusが成功(true)だった場合のみindexへ移動する
			if(this.apiStatus) {
				//メッセージ登録
				this.$store.commit('message/setContent', {
					content: 'ログインしました！',
				})
				
				//トップページに移動する
				// this.$router.push({name: 'index'});
				//元々アクセスしたかったページにリダイレクトする
				this.$router.push(this.$router.go(-1));
			}
		},
		//エラーメッセージをクリアするメソッド
		//これがないと別のページに行って戻ってくると以前のエラーが表示されたままになる
		clearError() {
			this.$store.commit('auth/setLoginErrorMessages', null);
		},
	},
	created() {
		//ログインページを表示するタイミング、つまりcreatedライフサイクルフックでエラーをクリアする
		this.clearError();
	}
}
</script>
