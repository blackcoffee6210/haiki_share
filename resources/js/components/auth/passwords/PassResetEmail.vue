<template>
	<main class="l-main">
		<div class="p-auth-form">
			<h2 class="c-title p-auth-form__title">パスワードリセット</h2>
			<!-- ローディング -->
			<Loading color="#f96204" v-show="loading"/>
			
			<form class="p-auth-form__form"
						v-show="!loading"
						@submit.prevent="submit">
				<!-- email-->
				<label for="email"
							 class="c-label p-auth-form__label">
					Eメール
				</label>
				<input type="text"
							 id="email"
							 class="c-input p-auth-form__input"
							 :class="{ 'c-input__err': errors }"
							 v-model="passResetForm.email"
							 placeholder="haiki_share@gmail.com">
				<!-- エラーメッセージ	-->
				<div v-if="errors">
					<div v-for="msg in errors.email"
							 :key="msg"
							 class="p-error">
						{{ msg }}
					</div>
				</div>
				<button class="c-btn p-auth-form__btn" type="submit">Eメール送信</button>
			</form>
		
		</div>
	</main>
</template>

<script>
import {UNPROCESSABLE_ENTITY, OK } from "../../../util";
import Loading from "../../Loading";
export default {
	name: "PassResetEmail",
	components: {
		Loading
	},
	data() {
		return {
			passResetForm: {
				email: '',
			},
			errors: null,
			loading: false
		}
	},
	methods: {
		async submit() {
			//ローディングを表示する
			this.loading = true;
			//API通信
			const response = await axios.post('/api/password/email', this.passResetForm);
			//API通信が終わったらローディングを非表示にする
			this.loading = false;
			
			//responseステータスがUNPROCESSABLE＿ENTITY(バリデーションエラー)なら後続の処理を行う
			if(response.status === UNPROCESSABLE_ENTITY) {
				this.errors = response.data.errors;
				return false;
			}
			//responseステータスがOK(成功)ならメッセージを登録
			if(response.status === OK) {
				this.$store.commit('message/setContent', {
					content: 'パスワード再設定メールを送信しました',
					timeout: 5000
				})
				//インデックス画面に移動する
				this.$router.push({name: 'index'});
			}
		}
	}
}
</script>

<style scoped>

</style>
