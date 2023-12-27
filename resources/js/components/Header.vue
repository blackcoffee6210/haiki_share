<template>
	<header class="l-header">
		<div class="p-header">
			<!-- ロゴ -->
			<div class="p-header__left">
				<a class="p-header__logo">
					Haiki Share
				</a>
				<div class="p-header__sub">食品も作り手の思いも無駄にしないフードシェアリングサービス</div>
			</div>
			
			<!-- ナビゲーション -->
			<div class="p-header__right">
				<nav class="p-header__nav">
					<router-link :to="{ name: 'login' }">ログイン</router-link>
					<router-link :to="{ name: 'register' }">新規登録</router-link>
				</nav>
			</div>
		</div>
	</header>
</template>

<script>
export default {
	name: "Header"
}
</script>
