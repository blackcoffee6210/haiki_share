<template>
	<footer id="footer" class="l-footer">
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
				<router-link class="p-footer__link" :to="{ name: 'agreement' }">利用規約</router-link>
				<router-link class="p-footer__link" :to="{ name: 'policy' }">プライバシーポリシー</router-link>
				<router-link class="p-footer__link" :to="{ name: 'tokutei' }">特定商取引法に基づく表示</router-link>
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
			isLogin: 'auth/check' //ログインしていたらtrueを返す
		}),
	},
	methods: {
		async logout() { //ログアウト
			try {
				await this.$store.dispatch('auth/logout'); //dispatchメソッドでauthストアのlogoutアクションを呼び出す
				
				if(this.apiStatus) { //通信成功なら下記の処理を実行
					this.$store.commit('message/setContent', { //メッセージを登録
						content: 'ログアウトしました'
					});
					this.$router.push({ name: 'index' }).catch(() => {}); //商品一覧画面へ移動する
				}
				
			}catch (error) {
				console.error('ログアウト処理に失敗しました: ', error);
				this.$store.commit('message/setContent',  //「ログアウト失敗」メッセージ登録
						{ content: 'ログアウトに失敗しました。再度お試しください。'}
				);
			}
		}
	}
}
</script>
