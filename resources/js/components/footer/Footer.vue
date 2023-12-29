<template>
	<footer class="l-footer">
		<div class="p-footer">
			<div class="p-footer__left">
				<!-- ログインしていれば「ログアウト」ボタンを表示する -->
				<button v-if="isLogin"
								class="p-footer__link"
								@click="logout">ログアウト
				</button>
				<!-- ログインしていなければ「ログイン」ボタンを表示する -->
				<router-link v-else
										 :to="{ name: 'login' }"
										 class="p-footer__link">ログイン
				</router-link>
			</div>
			<div class="p-footer__right">
				<router-link class="p-footer__link"
										 :to="{ name: 'agreement' }">利用規約
				</router-link>
				<router-link class="p-footer__link"
										 :to="{ name: 'policy' }">プライバシーポリシー
				</router-link>
				<router-link class="p-footer__link"
										 :to="{ name: 'tokutei' }">特定商取引法に基づく表示
				</router-link>
				<p class="p-footer__copy">Copyright&copy; Haiki Share. All Rights Reserved.</p>
			</div>
		</div>
	</footer>
</template>

<script>
import { mapState, mapGetters } from 'vuex';
export default {
	name: "Footer",
	computed: {
		...mapState({
			apiStatus: state => state.auth.apiStatus
		}),
		...mapGetters({
			isLogin: 'auth/check'
		}),
	},
	methods: {
		//ログアウト
		async logout() {
			//dispatchメソッドでauthストアのlogoutアクションを呼び出す
			await this.$store.dispatch('auth/logout');
			//通信成功なら下記の処理を実行
			if(this.apiStatus) {
				//メッセージを登録
				this.$store.commit('message/setContent', {
					content: 'ログアウトしました'
				});
				//記事一覧画面へ移動する
				this.$router.push({ name: 'index' }).catch(() => {});
			}
		}
	}
}
</script>
