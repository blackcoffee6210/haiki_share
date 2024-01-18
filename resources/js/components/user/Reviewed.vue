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
					<div class="c-card p-list__card__review"
							 v-for="review in reviews"
							 :key="review.id">
						 <!--レビュー詳細画面のリンク-->
						<router-link class="c-card__link"
												 :to="{ name: 'review.detail',
												 				params: { id: review.id.toString() }}" />
					
						<div class="p-list__user-info__review">
							<!-- ユーザーの画像	-->
							<img class="c-icon p-list__review-icon"
									 :src="review.sender_image"
									 v-show="isShopUser"
									 alt="">
							<img class="c-icon p-list__review-icon"
									 :src="review.receiver_image"
									 v-show="!isShopUser"
									 alt="">
							<div>
								<!-- レビュー相手の名前	-->
								<div class="p-list__name">
									<span v-show="isShopUser">{{ review.sender_name }}</span>
									<span v-show="!isShopUser">{{ review.receiver_name }}</span>
								</div>
								<!-- ユーザーの評価 -->
								<div class="p-list__recommendation">{{ review.recommend }}</div>
							</div>
						</div>
						
						<!-- レビュータイトル -->
						<div class="p-list__review-title">{{ review.title }}</div>
						<!-- レビューの内容 -->
						<div class="p-list__detail">{{ review.detail }}</div>
						
						<!-- ボタン	-->
						<div class="p-list__btn-container">
							<!-- 詳細を見るボタン(コンビニユーザー) -->
							<router-link class="c-btn p-list__btn p-list__btn--detail"
													 v-show="isShopUser"
													 :to="{ name: 'review.detail',
																	params: { id: review.id.toString() }}">詳細を見る
							</router-link>
							<!-- 編集ボタン(利用者) -->
							<router-link class="c-btn p-list__btn p-list__btn--detail"
													 v-show="!isShopUser"
													 :to="{ name: 'review.edit',
																	params: {
													 					s_id: review.sender_id,
													 					r_id: review.receiver_id
																	}
													 			}">編集する
							</router-link>
						</div>
					</div>
					
				</div>
			</div>
		</main>
		
		<!-- サイドバー	-->
		<Sidebar :id="id" />
	</div>
</template>

<script>
import Loading        from "../Loading";
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
