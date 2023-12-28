<template>
	<div>
		<Header />
		<Message />
		<RouterView />
		<Footer />
	</div>
</template>

<script>
import { mapState } from 'vuex'
import Header       from "./components/Header";
import Message      from "./components/Message";
import Footer       from "./components/footer/Footer";
import { INTERNAL_SERVER_ERROR, NOT_FOUND, UNAUTHORIZED } from "./util";

export default {
	name: "App",
	components: {
		Header,
		Message,
		Footer
	},
	computed: {
		...mapState({
			errorCode: state => state.error.code
		})
	},
	watch: {
		//errorモジュールのステートを監視する
		// INTERNAL_SERVER_ERRORだった場合にはエラーページに移動する
		errorCode: {
			async handler(val) {
				if (val === INTERNAL_SERVER_ERROR) {
					//500エラーページに遷移する
					this.$router.push('/500');
				}
				// 認証切れだった場合はログインページに移動する
				else if (val === UNAUTHORIZED) {
					//トークンをリフレッシュ
					await axios.get('/api/refresh-token');
					//ストアのuserをクリア
					this.$store.commit('auth/setUser', null);
					//ログイン画面に遷移させる
					this.$router.push({name: 'login'});
				}
				//404エラーだった場合は404ページに移動する
				else if (val === NOT_FOUND) {
					this.$router.push({name: 'NotFound'});
				}
			},
			immediate: true
		},
		$route() {
			this.$store.commit('error/setCode', null);
		}
	}
}
</script>
















