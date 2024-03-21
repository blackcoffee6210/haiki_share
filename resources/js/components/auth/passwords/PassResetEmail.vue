<template>
	<main class="l-main">
		<div class="p-auth-form p-auth-form--password">
			<h2 class="c-title p-auth-form__title">パスワードリセットリンク送信</h2>
			<!-- ローディング -->
			<Loading color="#f96204" v-show="loading"/>
			
			<form class="p-auth-form__form" v-show="!loading" @submit.prevent="submit">
				
				<!-- email-->
				<label for="email" class="c-label p-auth-form__label">Eメール</label>
				<input type="text"
							 id="email"
							 class="c-input p-auth-form__input"
							 :class="{ 'c-input__err': errors.email || !isEmailValid }"
							 v-model="passResetForm.email"
							 placeholder="haiki_share@gmail.com">
				<!-- エラーメッセージ -->
				<div class="u-d-flex u-space-between">
					<!-- エラーメッセージ（フロントエンド） -->
					<div v-if="!isEmailValid && !errors.email" class="p-error">
						<p>Eメールは255文字以下で指定してください</p>
					</div>
					<!-- エラーメッセージ（バックエンド）	-->
					<div v-if="errors">
						<div v-for="msg in errors.email" :key="msg" class="p-error">{{ msg }}</div>
					</div>
				</div>
				
				<!-- Email送信ボタン -->
				<button class="c-btn p-auth-form__btn" type="submit">Eメール送信</button>
				<!-- 会員登録へ遷移	-->
				<a @click="$router.back()" class="c-link">もどる</a>
			</form>
		
		</div>
	</main>
</template>

<script>
import { UNPROCESSABLE_ENTITY, OK } from "../../../util";
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
			errors: {},
			loading: false
		}
	},
	computed: {
		isEmailValid() { //Emailバリデーション
			return this.passResetForm.email.length <= 255;
		}
	},
	methods: {
		async submit() { //Email送信
			this.loading = true; //ローディングを表示する
			
			try {
				const response = await axios.post('/api/password/email', this.passResetForm); //API通信
				
				if(response.status === OK) { //responseステータスがOK(成功)ならメッセージを登録
					this.$store.commit('message/setContent', {
						content: 'パスワード再設定メールを送信しました',
					});
					this.$router.push({name: 'index'}); //インデックス画面に移動する
					
				}else if(response.status === UNPROCESSABLE_ENTITY) { //responseステータスがUNPROCESSABLE＿ENTITY(バリデーションエラー)なら後続の処理を行う
					this.errors = response.data.errors;
					return false;
				}
				
			}catch (error) {
				console.error('Email送信処理中にエラーが発生しました ', error);
				
			}finally {
				this.loading = false; //ローディングを非表示にする
			}
		}
	}
}
</script>
