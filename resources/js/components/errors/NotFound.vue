<template>
	<div class="p-404">
		<h1 class="p-404__title">Error</h1>
		<div class="p-404__text">ご指定のページが見つかりませんでした</div>
		<router-link :to="{ name: 'index' }"
								 class="c-link p-404__link">TOPへもどる
		</router-link>
	</div>
</template>

<script>
export default {
	name: "NotFound"
}
</script>

