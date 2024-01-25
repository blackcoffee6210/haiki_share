<template>
	<div class="l-main">
		<main class="l-main__2column">
			<div class="p-list">
				<h2 class="c-title p-list__title">
					<span v-show="isShopUser">投稿されたレビュー一覧</span>
					<span v-show="!isShopUser">投稿したレビュー一覧</span>
				</h2>
				
				<!-- ローディング -->
				<Loading v-show="loading" />
				
				<!-- レビューがなければ表示する -->
				<div v-if="!reviews.length"
						 class="p-list__no-review">
					<span v-show="isShopUser">投稿されたレビューはありません</span>
					<span v-show="!isShopUser">投稿したレビューはありません</span>
				</div>
				
				<div class="p-list__card-container" v-show="!loading">
					
					<!-- カード -->
					<!-- todo: レビュー投稿日を実装 -->
					<!-- Reviewコンポーネント -->
					<Review v-show="!loading"
									v-for="review in reviews"
									:key="review.id"
									:review="review" >
						<div class="p-product__btn-container" slot="btn">
									<!-- 詳細を見るボタン(コンビニユーザー) -->
									<router-link class="c-btn p-list__btn p-list__btn--detail"
															 v-show="isShopUser"
															 :to="{ name: 'review.detail',
																			params: {
															 					s_id: review.sender_id.toString(),
																				r_id: review.receiver_id.toString()
															 			  }}">詳細を見る
									</router-link>
									<!-- 編集ボタン(利用者) -->
									<router-link class="c-btn c-btn--white p-list__btn"
															 v-show="!isShopUser"
															 :to="{ name: 'review.edit',
																			params: {
																				s_id: review.sender_id.toString(),
																				r_id: review.receiver_id.toString()
																			}}">編集する
									</router-link>
						</div>
					</Review>
					
				</div>
			</div>
		</main>
		
		<!-- サイドバー	-->
		<Sidebar :id="id.toString()" />
	</div>
</template>

<script>
import Loading        from "../Loading";
import Review         from "../review/Review";
import Sidebar        from "../Sidebar";
import { OK }         from "../../util";
import { mapGetters } from 'vuex';

export default {
	name: "Reviewed",
	props: {
		id: {
			type: String,
			required: true
		}
	},
	components: {
		Loading,
		Review,
		Sidebar
	},
	data() {
		return {
			loading: false,
			reviews: {},
		}
	},
	computed: {
		...mapGetters({
			isShopUser: 'auth/isShopUser'
		})
	},
	methods: {
		async getReviewed() { //レビュー一覧取得
			this.loading = true; //ローディングを表示する
			
			const response = await axios.get(`/api/users/${this.id}/reviewed`); //API通信
			
			this.loading = false; //API通信が終わったらローディングを非表示にする
			
			if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			this.reviews = response.data; //プロパティにデータをセット
			this.s_id = response
			console.log(this.reviews);
		},
		async getWasReviewed() { //レビュ一取得
			this.loading = true; //ローディングを表示する
			
			const response = await axios.get(`/api/users/${this.id}/wasReviewed`); //API通信
			
			this.loading = false; //API通信が終わったらローディングを非表示にする
			
			if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			this.reviews = response.data; //プロパティにデータをセット
			console.log(response.data);
		},
	},
	watch: {
		$route: {
			async handler() {
				this.isShopUser ? await this.getWasReviewed() : await this.getReviewed();
			},
			immediate: true //immediateをtrueにすると、コンポーネントが生成されたタイミングでも実行する
		}
	}
}
</script>
