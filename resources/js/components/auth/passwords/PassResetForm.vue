<template>
	<main class="l-main">
		<div class="p-auth-form p-auth-form--password">
			
			<!-- タイトル -->
			<h2 class="c-title p-auth-form__title">パスワードリセット</h2>
			
			<form class="c-form p-auth-form__form"
						@submit.prevent="submit">
				
				<!-- token -->
				<input type="hidden" v-model="passResetForm.token">
				
				<!-- email -->
				<input type="hidden" v-model="passResetForm.email">
				
				<!-- password-->
				<label for="password"
							 class="c-label p-auth-form__label">新しいパスワード
				</label>
				<!-- 新しいパスワード -->
				<input type="password"
							 id="password"
							 class="c-input p-auth-form__input"
							 :class="{ 'c-input__err': errors.password ||
												  maxCounter(passResetForm.password,255)
							 }"
							 v-model="passResetForm.password"
							 placeholder="********">
				<!-- エラーメッセージ -->
				<div class="u-d-flex u-space-between">
					<!-- エラーメッセージ（フロントエンド） -->
					<div v-if="maxCounter(passResetForm.password,255) && !errors.password"
							 class="p-error">
						<p class="">新しいパスワードは255文字以下で指定してください</p>
					</div>
					<!-- エラーメッセージ（バックエンド）	-->
					<div v-if="errors">
						<div v-for="msg in errors.password"
								 :key="msg"
								 class="p-error">
							{{ msg }}
						</div>
					</div>
				</div>
				<!--&lt;!&ndash; エラーメッセージ	&ndash;&gt;-->
				<!--<div v-if="errors">-->
				<!--	<div v-for="msg in errors.password"-->
				<!--			 :key="msg"-->
				<!--			 class="p-error">{{ msg }}-->
				<!--	</div>-->
				<!--</div>-->
				
				<!-- 新しいパスワード(確認) -->
				<label for="password-confirmation"
							 class="c-label p-auth-form__label">新しいパスワード(確認)
				</label>
				<input type="password"
							 id="password-confirmation"
							 class="c-input p-auth-form__input"
							 :class="{ 'c-input__err': errors.password_confirmation ||
							  				 maxCounter(passResetForm.password_confirmation,255)
							 }"
							 v-model="passResetForm.password_confirmation"
							 placeholder="8文字以上の半角英数字">
				<!-- エラーメッセージ -->
				<div class="u-d-flex u-space-between">
					<!-- エラーメッセージ（フロントエンド） -->
					<div v-if="maxCounter(passResetForm.password_confirmation,255) && !errors.password_confirmation"
							 class="p-error">
						<p class="">新しいパスワード(確認)は255文字以下で指定してください</p>
					</div>
					<!-- エラーメッセージ（バックエンド）	-->
					<div v-if="errors">
						<div v-for="msg in errors.password_confirmation"
								 :key="msg"
								 class="p-error">
							{{ msg }}
						</div>
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
		maxCounter(content, maxValue) { //カウンターの文字数上限
			return content.length > maxValue;
		},
		async submit() {
			const response = await axios.post('/api/password/reset', this.passResetForm); //API通信
			
			if(response.status === UNPROCESSABLE_ENTITY) { //responseステータスがUNPROCESSABLE_ENTITY(バリデーションエラー)なら以下の処理を行う
				this.errors = response.data.errors;
				return false;
			}
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったら後続の処理を行う
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			
			this.$store.commit('message/setContent', { //メッセージ登録
				content: 'パスワードを変更しました',
			});
			
			this.$router.push({ name: 'index' }); //インデックス画面に移動する
		}
	}
}
</script>
