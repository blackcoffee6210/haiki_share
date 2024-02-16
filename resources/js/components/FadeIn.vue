<template>
	<div :class="{ fadeIn: visible }">
		<slot v-show="visible"></slot>
	</div>
</template>

<script>
export default {
	name: "FadeIn",
	data() {
		return {
			visible: false
		}
	},
	created() {
		window.addEventListener("scroll", this.handleScroll);
	},
	destroyed() {
		window.removeEventListener("scroll", this.handleScroll);
	},
	methods: {
		handleScroll() {
			if(!this.visible) {
				var top = this.$el.getBoundingClientRect().top; //ブラウザの表示領域の左上を(0, 0)として、そこから要素の上端までの相対位置の値が返ってくる
				this.visible = top < window.innerHeight + 50;
			}
		}
	}
}
</script>

<style scoped>
.fadeIn {
	animation: fadeIn 1.5s;
}
@keyframes fadeIn {
	0% {
		opacity: 0;
		transform: translateY(100px);
	}
	100% {
		opacity: 1;
		transform: translateY(0);
	}
}
</style>





















