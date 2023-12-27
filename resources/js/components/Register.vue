<template>
	<div class="l-main">
		<div class="p-auth-form">
			<h2 class="c-title p-auth-form__title">新規登録</h2>
			<form class="p-auth-form__form" @submit.prevent="register">
				
				<!-- csrf -->
				<input type="hidden" name="_token" :value="$store.state.csrf">
				
				<!-- name-->
				<label for="name" class="c-label p-auth-form__label">お名前</label>
				<input type="text"
							 id="name"
							 class="c-input p-auth-form__input"
							 v-model="registerForm.name"
							 placeholder="ハイキ君">
				<!-- エラーメッセージ	-->
				<div v-if="registerErrors">
					<div v-for="msg in registerErrors.name" :key="msg" class="p-error">{{ msg }}</div>
				</div>
				
				<!-- email-->
				<label for="email" class="c-label p-auth-form__label">Eメール</label>
				<input type="text"
							 id="email"
							 class="c-input p-auth-form__input"
							 v-model="registerForm.email"
							 placeholder="mail@haiki_share.com">
				<!-- エラーメッセージ	-->
				<div v-if="registerErrors">
					<div v-for="msg in registerErrors.email" :key="msg" class="p-error">{{ msg }}</div>
				</div>
				
				<!-- password-->
				<label for="password" class="c-label p-auth-form__label">パスワード(半角英数字)</label>
				<input type="password"
							 id="password"
							 class="c-input p-auth-form__input"
							 v-model="registerForm.password"
							 placeholder="6文字以上の半角英数字">
				<!-- エラーメッセージ	-->
				<div v-if="registerErrors">
					<div v-for="msg in registerErrors.password" :key="msg" class="p-error">{{ msg }}</div>
				</div>
				
				<!-- password-confirmation-->
				<label for="password-confirmation"
							 class="c-label p-auth-form__label">パスワード(確認)
				</label>
				<input type="password"
							 id="password-confirmation"
							 class="c-input p-auth-form__input"
							 v-model="registerForm.password_confirmation"
							 placeholder="8文字以上の半角英数字">
				<!-- エラーメッセージ	-->
				<div v-if="registerErrors">
					<div v-for="msg in registerErrors.password_confirmation" :key="msg" class="p-error">{{ msg }}</div>
				</div>
				
				<button class="c-btn p-auth-form__btn" type="submit">登録する（無料）</button>
			</form>
			<hr class="p-auth-form__u-line">
			<!-- ログイン画面へ遷移	-->
			<router-link :to="{ name: 'login' }"
									 class="c-link">ログインはこちら</router-link>
		</div>
	</div>
</template>

<script>
import { mapState } from 'vuex';

export default {
	name: "Register",
	data() {
		return {
			registerForm: {
				name: '',
				email: '',
				password: '',
				password_confirmation: ''
			}
		}
	},
	computed: {
		...mapState({
			//通信失敗の場合、つまりapiStatusがfalseの場合はインデックスへの移動を行わないように制御する
			//trueまたはfalseが入る
			apiStatus: state => state.auth.apiStatus,
			registerErrors: state => state.auth.registerErrorMessages
		})
	},
	methods: {
		async register() {
			//dispatchメソッドでauthストアのregisterアクションを呼び出す
			//第一引数はアクション名、第二引数はフォームの入力値を渡す
			await this.$store.dispatch('auth/register', this.registerForm);
			//apiStatusがtrue(通信成功)なら後続の処理を行う
			if(this.apiStatus) {
				//メッセージを登録
				this.$store.commit('message/setContent', {
					content: 'ユーザー登録しました！',
				});
				//トップページ(index画面)に移動する
				this.$router.push({ name: 'index' });
			}
		},
		//エラーメッセージをクリアするメソッド
		clearError() {
			this.$store.commit('auth/setRegisterErrorMessages', null);
		}
	},
	created() {
		this.clearError();
	}
}
</script>
