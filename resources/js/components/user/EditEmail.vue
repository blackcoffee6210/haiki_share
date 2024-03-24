<template>
	<div class="l-main">
		
		<main class="l-main__2column">
			<div class="p-editEmail">
				<!-- タイトル -->
				<h2 class="c-title p-editEmail__title">Eメール変更</h2>
				
				<div class="p-editEmail__background">
					<!-- ローディング -->
					<Loading v-show="loading" />
					
					<form class="p-editEmail__form" v-show="!loading" @submit.prevent="update">
						<!-- Eメール	-->
						<label for="email" class="c-label p-editEmail__label">新しいEメール</label>
						<input type="text"
									 id="email"
									 class="c-input p-editEmail__input"
									 :class="{ 'c-input--err': errors.email || maxCounter(email, 255) }"
									 v-model="email"
									 placeholder="mail@haiki_share.com">
						<div class="u-d-flex u-space-between">
							<!-- エラーメッセージ（フロントエンド） -->
							<div v-if="maxCounter(email, 255) && !errors.email" class="p-error">
								<p class="">Eメールは255文字以下で指定してください</p>
							</div>
							<!-- エラーメッセージ（バックエンド）	-->
							<div v-if="errors">
								<div v-for="msg in errors.email" :key="msg" class="p-error">{{ msg }}</div>
							</div>
						</div>
						
						<!-- ボタン	-->
						<div class="p-editEmail__btnContainer">
							<a @click="$router.back()" class="c-btn c-btn--white p-editEmail__btn__back">もどる</a>
							<button class="c-btn p-editEmail__btn" type="submit">メール送信</button>
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
	name: "EditEmail",
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
			loading: false,
			email: '',
			errors: {},
		}
	},
	methods: {
		maxCounter(content, maxValue) { //カウンターの文字数上限
			return content.length > maxValue;
		},
		async getEmail() { //Eメール取得
			try {
				const response = await axios.get(`/api/users/${this.id}`); //API接続
				
				if(response.status === OK) { //成功なら
					this.email = response.data.email;
					
				}else { //失敗
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
			}catch(error) {
				console.error('ユーザー情報取得中にエラーが発生しました', error);
			}
		},
		async update() { //Eメール更新処理
			this.loading = true;
			
			try {
				const response = await axios.post(`/api/users/${this.id}/updateEmail`, { email: this.email });
				
				if(response.status === OK) { //成功なら
					this.$store.commit('message/setContent',
							{ content: 'Eメールの更新リクエストを送りました。確認メールをご覧ください。' }
					); //メッセージ登録
					this.$router.push({ name: 'user.mypage', params: { id: this.id }}); //マイページに移動する
					
				}else if(response.status === UNPROCESSABLE_ENTITY) { //responseステータスがバリデーションエラーならエラーメッセージをセット
					this.errors = response.data.errors; //レスポンスのエラーメッセージを格納する
					return false;                       //処理を抜ける
				}
				
			}catch (error) {
				console.error('Eメールの更新リクエスト送信に失敗しました。', error);
				
			}finally {
				this.loading = false;
			}
		}
	},
	async created() {
		await this.getEmail();
	}
}
</script>
