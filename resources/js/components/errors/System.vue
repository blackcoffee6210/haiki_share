<template>
	<div class="p-system">
		<p class="p-system__text" style="text-align: center;">システムエラーが発生しました。</p>
		<router-link :to="{ name: 'index' }" class="c-link p-system__link">TOPへもどる
		</router-link>
	</div>
</template>

<script>
export default {
	name: "System"
}
</script>
