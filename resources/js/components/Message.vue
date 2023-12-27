<template>
	<transition appear>
		<div class="c-message" v-show="message">
			{{ message }}
		</div>
	</transition>
</template>

<script>
import { mapState } from 'vuex';
export default {
	name: "Message",
	computed: {
		...mapState({
			message: state => state.message.content
		})
	}
}
</script>

<style scoped>
.v-enter-active,
.v-leave-active	{
	transition: .5s;
	/* 上からふわっと表示 */
	transform: translateY(10px);
}
.v-enter,
.v-leave-to {
	opacity: 0;
	transform: translateY(-10px);
}
</style>
