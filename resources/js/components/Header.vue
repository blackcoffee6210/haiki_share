<template>
	<header class="l-header">
		<div class="p-header">
			<!-- ロゴ -->
			<div class="p-header__left">
				<!-- RouterLinkを使うとaタグのようなGETリクエストが発生せず、フロントエンドでの画面遷移、VueRouterでコンポーネントの切り替えを行う -->
				<router-link class="p-header__logo" :to="{ name: 'index' }">
					Haiki Share
					<!--Haiki<br class="p-header__logo&#45;&#45;sp"> Share-->
				</router-link>
				<div class="p-header__sub">
					食品を無駄にしない<br class="p-header__br">フードシェアリングサービス
				</div>
			</div>
			
			<!-- ナビゲーション-->
			<div class="p-header__right">
				
				<!-- ハンバーガーメニュー（スマホ用） -->
				<button @click="toggleNav" class="p-header__hamburger">
					<span class="p-header__hamburger__line" :class="{ 'v-line01': active }"></span>
					<span class="p-header__hamburger__line" :class="{ 'v-line02': active }"></span>
					<span class="p-header__hamburger__line" :class="{ 'v-line03': active }"></span>
				</button>
			
				<!-- ログイン時のメニュー -->
				<nav v-if="isLogin" class="p-header__nav" :class="{ 'v-slide': active }">
					<router-link :to="{ name: 'product.register' }"
											 class="p-header__link"
											 v-show="isShopUser"
											 @click="toggleNav">出品する
					</router-link>
					<!--<router-link :to="{ name: 'product.register' }"-->
					<!--						 class="p-header__link"-->
					<!--						 v-show="user.group === 2"-->
					<!--						 @click="toggleNav">出品する-->
					<!--</router-link>-->
					<router-link :to="{ name: 'user.mypage' }"
											 class="p-header__link"
											 @click="toggleNav">マイページ
					</router-link>
					<button class="p-header__link" @click="logout">ログアウト</button>
				</nav>
				
				<!-- ログインしていないときのメニュー -->
				<nav v-else class="p-header__nav" :class="{ 'v-slide': active }">
					<!-- ログインボタン -->
					<router-link :to="{ name: 'login' }"
											 class="p-header__link"
											 @click="toggleNav">ログイン</router-link>
					<!-- ユーザー登録ボタン -->
					<router-link :to="{ name: 'register' }"
											 class="p-header__link"
											 @click="toggleNav">新規登録</router-link>
				</nav>
			</div>
		</div>
	</header>
</template>

<script>
import { mapState, mapGetters } from 'vuex';
export default {
	name: "Header",
	data() {
		return {
			active: false,
		}
	},
	computed: {
		...mapState({
			user: state => state.auth.user,
			apiStatus: state => state.auth.apiStatus,
		}),
		...mapGetters({
			isLogin: 'auth/check', //trueまたはfalseが返ってくる(ログインしていたらtrue)
			username: 'auth/username',
			isShopUser: 'auth/isShopUser'
		}),
	},
	methods: {
		//ハンバーガーメニューをtoggleで切り替える
		toggleNav() {
			return this.active = !this.active;
		},
		//ログアウト
		async logout() {
			await this.$store.dispatch('auth/logout');
			//通信成功(true)なら以下の処理を実行
			if(this.apiStatus) {
				//「ログアウト」メッセージ登録
				this.$store.commit('message/setContent', {
					content: 'ログアウトしました'
				});
				//メニューを切り替える(スマホのハンバーガーメニューを閉じる)
				this.toggleNav();
				//商品一覧画面に移動する
				this.$router.push({ name: 'index' }).catch(() => {});
			}
		}
	},
	watch: {
		'$route'() {
			this.active = false;
		}
	}
}
</script>
