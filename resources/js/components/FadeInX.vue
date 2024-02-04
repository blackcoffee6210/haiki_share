<template>
	<div :class="{ fadeIn }">
		<slot v-show="visible"></slot>
	</div>
</template>

<script>
export default {
	name: "FadeInX",
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
				this.visible = top < window.innerHeight + 50; //ブラウザのウィンドウの高さに100pxを足した値よりtopの値が小さくなったときにthis.visibleにtrueを入れる
			}
		}
	}
}
</script>

<style scoped>

</style>
