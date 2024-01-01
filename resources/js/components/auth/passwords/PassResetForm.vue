<template>
	<main class="l-main">
		<div class="p-auth-form">
			<h2 class="c-title p-auth-form__title">パスワードリセット</h2>
			
			<form class="c-form p-auth-form__form"
						@submit.prevent="submit">
				
				<!-- token -->
				<input type="hidden" v-model="passResetForm.token">
				
				<!-- email -->
				<input type="hidden" v-model="passResetForm.email">
				
				<!-- password-->
				<label for="password"
							 class="c-label p-auth-form__label">
					新しいパスワード
				</label>
				<input type="password"
							 id="password"
							 class="c-input p-auth-form__input"
							 :class="{ 'c-input__err': errors.password }"
							 v-model="passResetForm.password"
							 placeholder="********">
				<!-- エラーメッセージ	-->
				<div v-if="errors">
					<div v-for="msg in errors.password"
							 :key="msg"
							 class="p-error">
						{{ msg }}
					</div>
				</div>
				
				<!-- password(confirm)-->
				<label for="password-confirmation"
							 class="c-label p-auth-form__label">
					新しいパスワード(確認)
				</label>
				<input type="password"
							 id="password-confirmation"
							 class="c-input p-auth-form__input"
							 :class="{ 'c-input__err': errors.password_confirmation }"
							 v-model="passResetForm.password_confirmation"
							 placeholder="8文字以上の半角英数字">
				<!-- エラーメッセージ	-->
				<div v-if="errors">
					<div v-for="msg in errors.password_confirmation"
							 :key="msg"
							 class="p-error">
						{{ msg }}
					</div>
				</div>
				
				<!-- パスワード変更ボタン -->
				<button class="c-btn p-auth-form__btn" type="submit">パスワードを変更する</button>
			</form>
		</div>
	</main>
</template>

<script>
import { UNPROCESSABLE_ENTITY, OK } from "../../../util";

export default {
	name: "PassResetForm",
	data() {
		return {
			passResetForm: {
				token: this.$route.params.token,
				email: this.$route.params.email,
				password: '',
				password_confirmation: ''
			},
			errors: {
				password: null,
				password_confirmation: null
			}
		}
	},
	methods: {
		async submit() {
			//API通信
			const response = await axios.post('/api/password/reset', this.passResetForm);
			//responseステータスがUNPROCESSABLE_ENTITY(バリデーションエラー)なら以下の処理を行う
			if(response.status === UNPROCESSABLE_ENTITY) {
				this.errors = response.data.errors;
				return false;
			}
			//responseステータスがOKじゃなかったら後続の処理を行う
			if(response.status !== OK) {
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			//メッセージ登録
			this.$store.commit('message/setContent', {
				content: 'パスワードを変更しました',
			});
			//インデックス画面に移動する
			this.$router.push({ name: 'index' });
		}
	}
}
</script>

<style scoped>

</style>
