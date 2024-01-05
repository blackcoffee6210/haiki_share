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
		errorCode: {                                                 //errorモジュールのステートを監視する
			async handler(val) {
				if (val === INTERNAL_SERVER_ERROR) {                     // INTERNAL_SERVER_ERRORだった場合にはエラーページに移動する
					this.$router.push({ name: 'systemError' });            //500エラーページに遷移する
					
				}else if (val === UNAUTHORIZED) {                        // 認証切れだった場合はログインページに移動する
					await axios.get('/api/refresh-token');             //トークンをリフレッシュ
					this.$store.commit('auth/setUser', null); //ストアのuserをクリア
					this.$router.push({name: 'login'});                    //ログイン画面に遷移させる
					
				}else if (val === NOT_FOUND) {                           //404エラーだった場合は404ページに移動する
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
















