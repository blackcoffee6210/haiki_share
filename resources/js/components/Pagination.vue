<template>
	<div class="p-pagination">
		<!-- 「prev」ボタンは1ページ目以外の場合に表示する -->
		<router-link v-if="!isFirstPage"
								 :to="`/articles?page=${ currentPage - 1 }`"
								 class="p-pagination__active u-ml15">
			&laquo; prev
		</router-link>
		<!-- 「next」ボタンは最後のページ以外の場合に表示する	-->
		<router-link v-if="!isLastPage"
								 :to="`/articles?page=${ currentPage + 1 }`"
								 class="p-pagination__active">
			next &raquo;
		</router-link>
	</div>
</template>

<script>
export default {
	name: "Pagination",
	props: {
		currentPage: {
			type: Number,
			required: true
		},
		lastPage: {
			type: Number,
			required: true
		},
		linkMax: {
			type: Number,
			required: true
		}
	},
	computed: {
		//最初のページを返す算出プロパティ
		isFirstPage() {
			return this.currentPage === 1;
		},
		isLastPage() {
			//最後のページを返す算出プロパティ
			return this.currentPage === this.lastPage;
		}
	}
}
</script>
