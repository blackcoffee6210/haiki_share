<template>
	<div class="l-main">
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
							退会される方は、下記の「退会する」ボタンをクリックしてください。
						</p>
						<!--&lt;!&ndash; ボタン	&ndash;&gt;-->
						<!--<div class="p-withdrawal__btn-container">-->
						<!--	<a @click="$router.back()"-->
						<!--		 class="c-btn c-btn&#45;&#45;white p-withdrawal__btn p-withdrawal__btn&#45;&#45;back">-->
						<!--		もどる-->
						<!--	</a>-->
						<!--	<button class="c-btn p-withdrawal__btn"-->
						<!--					type="submit">退会する-->
						<!--	</button>-->
						<!--</div>-->
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
import { mapState, mapGetters } from 'vuex';

export default {
	name: "Withdrawal",
	components: {
		Loading,
		Sidebar
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
		...mapGetters({
			id: 'auth/userId'
		})
	},
	methods: {
		async submit() { //退会
			if(confirm('本当に退会しますか？(お客様の情報はすべて削除されます)')) {
					this.$store.commit('message/setContent', { //メッセージ登録
						content: '退会しました'
					});
			}
		}
	},
}
</script>

