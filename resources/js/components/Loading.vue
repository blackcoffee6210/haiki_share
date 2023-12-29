<template>
	<div class="c-loading">
		<div class="c-loading__container">
			<vue-loading type="spiningDubbles" color="#f96204"/>
		</div>
	</div>
</template>

<script>
import { VueLoading } from 'vue-loading-template';
export default {
	name: "Loading",
	components: {
		VueLoading
	}
}
</script>

<style scoped>

</style>
