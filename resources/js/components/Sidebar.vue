<template>
	<!-- サイドバー -->
	<aside class="l-sidebar">
		<div class="p-sidebar">
			
			<!-- マイページ -->
			<router-link class="p-sidebar__link"
									 :to="{ name: 'user.mypage',
									  			params: { id: id.toString() }}">マイページ
			</router-link>
			
			<!-- プロフィール編集	-->
			<router-link class="p-sidebar__link"
									 :to="{ name: 'user.editProfile',
									  			params: { id: id.toString() }}" >プロフィール編集
			</router-link>
			
			<!-- パスワード変更 -->
			<router-link class="p-sidebar__link"
									 :to="{ name: 'user.editPassword',
									  			params: { id: id.toString() }}" >パスワード変更
			</router-link>
			
			<!-- 出品する	-->
			<router-link class="p-sidebar__link"
									 v-if="isShopUser"
									 :to="{ name: 'product.register',
									  			params: { id: id.toString() }}" >出品する
			</router-link>
			
			<!-- 出品した商品一覧 -->
			<router-link class="p-sidebar__link"
									 v-if="isShopUser"
									 :to="{ name: 'user.posted',
									  			params: { id: id.toString() }}" >出品した商品一覧
			</router-link>
			
			<!-- いいねした商品一覧 -->
			<router-link class="p-sidebar__link"
									 v-if="!isShopUser"
									 :to="{ name: 'user.liked',
									  			params: { id: id.toString() }}" >いいねした商品一覧
			</router-link>
			
			<!-- 購入した(された)商品一覧 -->
			<router-link class="p-sidebar__link"
									 :to="{ name: 'user.purchased',
									  			params: { id: id.toString() }}" >
				<span v-show="isShopUser">購入された商品一覧</span>
				<span v-show="!isShopUser">購入した商品一覧</span>
			</router-link>
			
			<!-- キャンセルした(された)商品一覧 -->
			<router-link class="p-sidebar__link"
									 :to="{ name: 'user.canceled',
									  			params: { id: id.toString() }}" >
				<span v-show="isShopUser">キャンセルされた商品一覧</span>
				<span v-show="!isShopUser">キャンセルした商品一覧</span>
			</router-link>

			<!-- レビューした(された)商品一覧 -->
			<router-link class="p-sidebar__link"
									 :to="{ name: 'user.reviewed',
									  		  params: { id: id.toString() }}" >
				<span v-show="isShopUser">レビューされたユーザー一覧</span>
				<span v-show="!isShopUser">レビューしたユーザー一覧</span>
			</router-link>

			<!-- ログアウト -->
			<button class="p-sidebar__link"
							@click="logout">ログアウト
			</button>
			
			<!-- 退会 -->
			<!--todo: 退会処理実装-->
			<a class="p-sidebar__link" href="">退会する</a>
		</div>
	</aside>
</template>

<script>
import { mapState, mapGetters } from 'vuex';
export default {
	name: "Sidebar",
	props: {
		id: {
			type: String,
			required: true
		}
	},
	computed: {
		...mapState({
			user:      state => state.auth.user,
			apiStatus: state => state.auth.apiStatus,
		}),
		...mapGetters({
			isShopUser: 'auth/isShopUser',
		})
	},
	methods: {
		async logout() {
			await this.$store.dispatch('auth/logout');
			
			if(this.apiStatus) { //通信成功ならloginページへ移動する
				this.$router.push({name: 'index'});
			}
		}
	}
}
</script>
