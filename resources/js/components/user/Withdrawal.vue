<template>
	<div class="l-main">
		<!--todo: リロードしたらindexに遷移される-->
		<!-- メインコンテンツ	-->
		<main class="l-main__2column">
			<div class="p-withdrawal">
				<h2 class="c-title p-withdrawal__title">退会</h2>
				
				<div class="p-withdrawal__background">
					<!-- ローディング -->
					<Loading v-show="loading" />
					
					<form class="p-withdrawal__form"
								v-show="!loading"
								@submit.prevent="submit">
						<p class="p-withdrawal__text">
							退会される方は、下記の「退会する」ボタンを<br class="u-br">
							クリックしてください。
						</p>

						<!-- ボタン	-->
						<div class="p-withdrawal__btn-container">
							<a @click="$router.back()"
								 class="c-btn c-btn--white p-withdrawal__btn p-withdrawal__btn--back">
								もどる
							</a>
							<button class="c-btn p-withdrawal__btn"
											type="submit">退会する
							</button>
						</div>
						
					</form>
				</div>
			</div>
		</main>
		<!-- サイドバー -->
		<Sidebar :id="id.toString()" />
	</div>
</template>

<script>
import Loading from "../Loading";
import Sidebar from "../Sidebar";
import { OK }  from "../../util";
import { mapState, mapGetters } from 'vuex';

export default {
	name: "Withdrawal",
	components: {
		Loading,
		Sidebar
	},
	props: {
		id: {
			type: String,
			required: true
		}
	},
	data() {
		return {
			loading: false,
		}
	},
	computed: {
		...mapState({
			apiStatus: state => state.auth.apiStatus
		}),
		// ...mapGetters({
		// 	id: 'auth/userId'
		// })
	},
	methods: {
		async submit() { //退会
			if(confirm('本当に退会しますか？(お客様の情報はすべて削除されます)')) {
				this.loading = true; //ローティングを表示する
				const response = await axios.delete(`/api/users/${this.id}`); //API接続
				this.loading = false; //API通信が終わったらローディングを非表示にする
				
				if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
					this.$store.commit('error/setCode', response.status);
					return false; //後続の処理を抜ける
				}
				this.$store.commit('message/setContent', { //メッセージ登録
					content: '退会しました'
				});
				await this.$store.dispatch('auth/logout'); //dispatchメソッドでauthストアのlogoutアクションを呼び出す
				this.$router.push({ name: 'index' }); //トップページへ移動する
			}
		}
	},
}
</script>

