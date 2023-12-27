<template>
	<header class="l-header">
		<div class="p-header">
			<!-- ロゴ -->
			<div class="p-header__left">
				<router-link class="p-header__logo" :to="{ name: 'index' }">
					Haiki Share
				</router-link>
				<div class="p-header__sub">食品も作り手の思いも無駄にしないフードシェアリングサービス</div>
			</div>
			
			<!-- ナビゲーション-->
			<div class="p-header__right">
				<!-- ハンバーガーメニュー（スマホ） -->
				<button @click="toggleNav" class="p-header__hamburger">
					<span class="p-header__hamburger__line" :class="{ 'v-line01': active }"></span>
					<span class="p-header__hamburger__line" :class="{ 'v-line02': active }"></span>
					<span class="p-header__hamburger__line" :class="{ 'v-line03': active }"></span>
				</button>
			
				<!-- ログイン時のメニュー -->
				<nav v-if="isLogin" class="p-header__nav" :class="{ 'v-slide': active }">
					<!-- todo: routerLinkに変える -->
					<a href="" class="p-header__link" @click="toggleNav">投稿する</a>
					<a href="" class="p-header__link" @click="toggleNav">マイページ</a>
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
			//通信成功なら商品一覧ページへ移動する
			if(this.apiStatus) {
				//「ログアウト」メッセージ登録
				this.$store.commit('message/setContent', {
					content: 'ログアウトしました'
				});
				//メニューを切り替える
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
