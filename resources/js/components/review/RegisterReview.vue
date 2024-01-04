<template>
	<div class="p-register-review">
		<!-- ローディング -->
		<Loading color="#f96204" v-show="loading" />
		
		<form class="p-register-review-review__form"
					v-show="!loading"
					@submit.prevent="submit">
			<h2 class="p-register-review__title">レビュー投稿</h2>
			
			<!-- ユーザーの情報 -->
			<div class="p-register-review__user-info">
				<img :src="product.user_image"
						 alt=""
						 class="c-icon p-register-review__icon">
				<div>
					<div class="p-register-review__user-name">{{ product.user_name }}</div>
				</div>
			</div>
			
			<!-- ユーザーの評価 -->
			<div class="p-register-review__sub-title">ユーザーの評価</div>
			<!-- todo: メルカリのユーザー評価を参考にする(おすすめ、ふつう、おすすめしないの3つ) -->
			<!-- エラーメッセージ	-->
			<div v-if="errors">
				<div v-for="msg in errors.recommendation" :key="msg" class="p-error">{{ msg }}</div>
			</div>
			
			<!-- タイトル -->
			<div class="p-register-review__sub-title">レビュータイトル</div>
			<input type="text"
						 class="c-input p-register-review__input"
						 id="title"
						 v-model="reviewForm.title"
						 placeholder="もっとも伝いたいポイントはなんですか？">
			<!-- エラーメッセージ	-->
			<div v-if="errors">
				<div v-for="msg in errors.title" :key="msg" class="p-error">{{ msg }}</div>
			</div>
			
			<!-- レビューの内容 -->
			<div class="p-register-review__sub-title">レビュー内容</div>
			<textarea class="c-input p-register-review__textarea"
								id="comment"
								v-model="reviewForm.comment"
								placeholder="レビューの内容を入力してください"
			></textarea>
			<!-- エラーメッセージ	-->
			<div v-if="errors">
				<div v-for="msg in errors.comment" :key="msg" class="p-error">{{ msg }}</div>
			</div>
			
			<!-- 投稿ボタン -->
			<button class="c-btn p-register-review__btn"
							type="submit">投稿する
			</button>
		</form>
	</div>
</template>

<script>
import Loading from "../Loading";
import { OK } from "../../util";

export default {
	name: "RegisterReview",
	components: {
		Loading
	},
	data() {
		return {
			loading: false, //ローディング
			product: {},    //商品情報
			categories: {}, //カテゴリー情報
			errors: {       //エラーメッセージ
				recommendation: null,
				title: null,
				comment: null
			},
			reviewForm: {          //レビューフォーム
				product_id: this.id, //商品id
				recommendation: 0,   //ユーザー評価
				title: '',           //レビュータイトル
				comment: ''          //レビューコメント
			}
		}
	},
	methods: {
		async getCategories() { //カテゴリー取得
			const response = await axios.get('/api/categories');
			//responseステータスがOKじゃなかったら
			if(response.status !== OK) {
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			//プロパティに値をセットする
			this.categories = response.data;
		},
		async getProduct() { //商品情報取得
			this.loading = true; //ローティングを表示する
			
			const response = await axios.get(`/api/products/${this.id}`); //API通信
			
			this.loading = false; //API通信が終わったらローディングを非表示にする
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったら後続の処理を行う
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			
			this.product = response.data; //プロパティに値をセットする
		},
	},
	watch: {
		$route: {
			async handler() {
				await this.getCategories();
				await this.getProduct();
			},
			immediate: true
		}
	}
}
</script>
