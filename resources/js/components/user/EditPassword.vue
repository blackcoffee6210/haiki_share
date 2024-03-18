<template>
	<div class="l-main">
		
		<main class="l-main__2column">
			<div class="p-edit-password">
				<!-- タイトル -->
				<h2 class="c-title p-edit-password__title">パスワード変更</h2>
				
				<div class="p-edit-password__background">
					<!-- ローディング -->
					<Loading v-show="loading" />
					
					<form class="p-edit-password__form" v-show="!loading" @submit.prevent="update">
						
						<!-- 現在のパスワード	-->
						<label for="current_password" class="c-label p-edit-password__label u-mt0">現在のパスワード</label>
						<input type="password"
									 id="current_password"
									 class="c-input p-edit-password__input"
									 :class="{ 'c-input__err': errors.current_password }"
									 v-model="passwordForm.current_password"
									 placeholder="*********">
						<!-- エラーメッセージ	-->
						<div v-if="errors">
							<div v-for="msg in errors.current_password"
									 :key="msg"
									 class="p-error">{{ msg }}
							</div>
						</div>
						
						<!-- 新しいパスワード	-->
						<label for="new_password" class="c-label p-edit-password__label">新しいパスワード</label>
						<input type="password"
									 id="new_password"
									 class="c-input p-edit-password__input"
									 :class="{ 'c-input__err': errors.new_password }"
									 v-model="passwordForm.new_password"
									 placeholder="8文字以上の半角英数字">
						<!-- エラーメッセージ	-->
						<div v-if="errors">
							<div v-for="msg in errors.new_password"
									 :key="msg"
									 class="p-error">{{ msg }}
							</div>
						</div>
						
						<!-- 新しいパスワード(確認)	-->
						<label for="new_password-confirmation" class="c-label p-edit-password__label">新しいパスワード(確認)</label>
						<input type="password"
									 id="new_password-confirmation"
									 class="c-input p-edit-password__input"
									 :class="{ 'c-input__err': errors.new_password_confirmation }"
									 v-model="passwordForm.new_password_confirmation"
									 placeholder="8文字以上の半角英数字">
						<!-- エラーメッセージ	-->
						<div v-if="errors">
							<div v-for="msg in errors.new_password_confirmation"
									 :key="msg"
									 class="p-error">{{ msg }}
							</div>
						</div>
						
						<!-- ボタン	-->
						<div class="p-edit-password__btn-container">
							<a @click="$router.back()" class="c-btn c-btn--white p-edit-password__btn--back">もどる</a>
							<button class="c-btn p-edit-password__btn" type="submit">更新する</button>
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
import Loading from "../Loading";
import Sidebar from "../Sidebar";
import { OK, UNPROCESSABLE_ENTITY } from "../../util";

export default {
	name: "EditPassword",
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
			loading: false,                   //ローディング
			errors: {                         //エラーメッセージを格納するプロパティ
				current_password: null,         //現在のパスワード
				new_password: null,             //新しいパスワード
				new_password_confirmation: null //新しいパスワード(確認)
			},
			passwordForm: {
				current_password: '',         //現在のパスワード
				new_password: '',             //新しいパスワード
				new_password_confirmation: '' //新しいパスワード(確認)
			}
		}
	},
	methods: {
		async update() { //パスワード更新処理
			this.loading = true; //ローディングを表示する
			
			try {
				const response = await axios.post(`/api/users/${this.id}/updatePassword`, this.passwordForm); //API通信
				
				if(response.status === OK) { //成功
					this.$store.commit('message/setContent', { content: 'パスワードを変更しました！' }); //メッセージ登録
					this.$router.push({ name: 'user.mypage' }); //マイページに移動
					
				}else if(response.status === UNPROCESSABLE_ENTITY) { //responseステータスがバリデーションエラーなら後続の処理を行う
					this.errors = response.data.errors; //レスポンスのエラーメッセージを格納する
					return false;                       //後続の処理を抜ける
					
				}else {
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
			}catch (error) {
				console.error('パスワード更新処理中にエラーが発生しました', error);
				
			}finally {
				this.loading = false; //ローディングを非表示にする
			}
		}
	}
}
</script>
