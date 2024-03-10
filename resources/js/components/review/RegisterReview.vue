<template>
	<main class="l-main">
		<div class="p-review-form" v-cloak>
			
			<h2 class="c-title p-review-form__title">レビュー投稿</h2>
			
			<div class="p-review-form__background">
				<!-- ローディング -->
				<Loading color="#f96204" v-show="loading" />
				
				<!-- レビュー投稿フォーム -->
				<form class="p-review-form__form"
							v-show="!loading"
							@submit.prevent="submit">
					
					<!-- 出品者の情報 -->
					<div class="p-review-form__user-info">
						<!-- 出品者の画像 -->
						<img :src="reviewForm.shopUser.image"
								 alt=""
								 v-if="reviewForm.shopUser.image"
								 class="c-icon p-review-form__icon">
						<!-- no-img -->
						<img class="c-icon p-review-form__icon"
								 v-else
								 src="/storage/images/no-image.png"
								 alt="">
						<!-- 出品者の名前 -->
						<div class="p-review-form__user-name">{{ reviewForm.shopUser.name }}</div>
					</div>
					
					<!-- ユーザーの評価 -->
					<div>ユーザーの評価</div>
					<div class="p-review-form__recommendation__container">
						<input type="radio"
									 :id="'recommend' + recommendations[0].id"
									 class="p-review-form__recommendation__input"
									 name="recommend"
									 :value="recommendations[0].id"
									 v-model.number="reviewForm.recommendation_id">
						<label :for="'recommend' + recommendations[0].id"
									 class="p-review-form__recommendation__label">
							<font-awesome-icon class="p-review-form__fa"
																 :icon="['far', 'laugh-beam']"
																 color="#ff6f80" />
							<span class="p-review-form__text">{{ recommendations[0].name }}</span>
						</label>
						<input type="radio"
									 :id="'recommend' + recommendations[1].id"
									 class="p-review-form__recommendation__input"
									 name="recommend"
									 :value="recommendations[1].id"
									 v-model.number="reviewForm.recommendation_id">
						<label :for="'recommend' + recommendations[1].id"
									 class="p-review-form__recommendation__label">
							<font-awesome-icon class="p-review-form__fa"
																 :icon="['far', 'smile']"
																 color="#ff6f80" />
							<span class="p-review-form__text">{{ recommendations[1].name }}</span>
						</label>
						<input type="radio"
									 :id="'recommend' + recommendations[2].id"
									 class="p-review-form__recommendation__input"
									 name="recommend"
									 :value="recommendations[2].id"
									 v-model.number="reviewForm.recommendation_id">
						<label :for="'recommend' + recommendations[2].id"
									 class="p-review-form__recommendation__label">
							<font-awesome-icon class="p-review-form__fa"
																 :icon="['far', 'meh']"
																 color="#ff6f80" />
							<span class="p-review-form__text">{{ recommendations[2].name }}</span>
						</label>
					</div>
	
					<!-- エラーメッセージ	-->
					<div v-if="errors">
						<div v-for="msg in errors.recommendation_id"
								 :key="msg"
								 class="p-error">{{ msg }}
						</div>
					</div>
					
					<!-- タイトル -->
					<label for="title"
								 class="c-label p-review-form__label">レビュータイトル
					</label>
					<input type="text"
								 class="c-input p-review-form__input"
								 :class="{ 'c-input__err': errors.title ||
								  				 maxCounter(reviewForm.title, 50)
								 }"
								 id="title"
								 v-model="reviewForm.title"
								 placeholder="もっとも伝いたいポイントはなんですか？">
					<div class="u-d-flex u-space-between">
						<!-- エラーメッセージ（フロントエンド） -->
						<div v-if="maxCounter(reviewForm.title,50) && !errors.title"
								 class="p-error">
							<p class="u-mb20">レビュータイトルは50文字以下で指定してください</p>
						</div>
						<!-- エラーメッセージ（バックエンド）	-->
						<div v-if="errors">
							<div v-for="msg in errors.title"
									 :key="msg"
									 class="p-error u-mb20">
								{{ msg }}
							</div>
						</div>
						<!-- 文字数カウンター -->
						<p class="c-counter"
							 :class="{ 'c-counter--err': maxCounter(reviewForm.title,50) }">
							{{ reviewForm.title.length }}/50
						</p>
					</div>
					
					<!-- レビューの内容 -->
					<label for="detail"
								 class="c-label p-review-form__label u-mt0">レビューの内容
					</label>
					<textarea class="c-input p-review-form__textarea"
										:class="{ 'c-input__err': errors.detail ||
					 										maxCounter(reviewForm.detail, 255)
														}"
										id="detail"
										v-model="reviewForm.detail"
										placeholder="レビューの内容を入力してください"
					></textarea>
					<div class="u-d-flex u-space-between">
						<!-- エラーメッセージ（フロントエンド） -->
						<div v-if="maxCounter(reviewForm.detail,255) && !errors.detail"
								 class="p-error">
							<p class="">レビューの内容は255文字以下で指定してください</p>
						</div>
						<!-- エラーメッセージ（バックエンド）	-->
						<div v-if="errors">
							<div v-for="msg in errors.detail"
									 :key="msg"
									 class="p-error u-mb20">
								{{ msg }}
							</div>
						</div>
						<!-- 文字数カウンター -->
						<p class="c-counter"
							 :class="{ 'c-counter--err': maxCounter(reviewForm.detail,255) }">
							{{ reviewForm.detail.length }}/255
						</p>
					</div>
					
					<div class="p-review-form__btn-container">
						<!-- 投稿ボタン -->
						<button class="c-btn p-review-form__btn--post"
										type="submit">投稿する
						</button>
					</div>
				</form>
			</div>
		</div>
	</main>
</template>

<script>
import Loading        from "../Loading";
import { mapGetters } from 'vuex';
import { CREATED, OK, UNPROCESSABLE_ENTITY } from "../../util";

export default {
	name: "RegisterReview",
	components: {
		Loading
	},
	props: {
		p_id: {
			type: String,
			required: true
		}
	},
	data() {
		return {
			loading: false, //ローディング
			purchasedByUser: false,
			reviewedByUser: false,
			errors: {       //エラーメッセージ
				recommendation_id: null,
				title: null,
				detail: null
			},
			recommendations: {},
			reviewForm: {              //レビューフォーム
				sender_id: null,         //送信者のユーザーid
				receiver_id: null,       //受信者のユーザーid
				recommendation_id: null, //ユーザー評価
				title: '',               //レビュータイトル
				detail: '',              //レビューコメント
				shopUser: {},            //出品ユーザーの情報
			},
			
		}
	},
	computed: {
		...mapGetters({
			userId: 'auth/userId'
		}),
	},
	methods: {
		maxCounter(content, maxValue) { //カウンターの文字数上限
			return content.length > maxValue;
		},
		async getPurchasedByUser() { //購入したユーザーを取得
			const response = await axios.get(`/api/products/${this.p_id}/purchasedByUser`);
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			response.data[0] ? this.purchasedByUser = true : this.purchasedByUser = false; //データがあればtrue、なければfalseをプロパティにセット
			
			if(!this.purchasedByUser) { //purchasedByUserがfalse(他人の購入した商品)だったら商品一覧画面に遷移する
				this.$router.push({name: 'index'});
			}
		},
		async getShopUser() { //商品idを元に出品ユーザーを取得
			const response = await axios.get(`/api/users/${this.p_id}/shopUser`); //API通信

			if(response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
				this.$store.commit('error/setCode', response.status);
				return false;
			}

			this.reviewForm.shopUser    = response.data[0];                 //出品者の情報
			this.reviewForm.sender_id   = this.userId;                      //送信者（レビュー投稿者）のユーザーid
			this.reviewForm.receiver_id = this.reviewForm.shopUser.user_id; //受信者（出品者）のユーザーid
		},
		async getReviewedByUser() { //ログインユーザーが既にレビューを投稿済みかどうか取得する
			const response = await axios.get(`/api/reviews/${this.reviewForm.receiver_id}/reviewedByUser`);
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセット
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			response.data[0] ? this.reviewedByUser = true : this.reviewedByUser = false; //ユーザーがレビューを投稿していたらtrue、そうじゃなければfalse
			
			if(this.reviewedByUser) { //既にレビューしていたら商品一覧画面に遷移する
				this.$router.push({name: 'index'});
			}
		},
		async getRecommendation() { //ユーザー評価取得
			const response = await axios.get('/api/recommendations') //API接続
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			this.recommendations = response.data;
		},
		async submit() {       //レビュー投稿
			this.loading = true; //ローディングを表示する
			
			const response = await axios.post('/api/reviews', this.reviewForm); //API接続
			
			this.loading = false; //API通信が終わったらローディングを非表示にする
			
			if(response.status === UNPROCESSABLE_ENTITY) { //responseステータスがバリデーションエラーなら後続の処理を行う
				this.errors = response.data.errors;
				return false;
			}
			
			if(response.status !== CREATED) { //responseステータスがCREATEDじゃなかったら後続の処理を行う
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			
			this.$store.commit('message/setContent', { //メッセージ登録
				content: 'レビューを投稿しました！',
			});
			
			this.$router.push({ name: 'user.mypage', params: { id: this.userId.toString() } }); //マイページに遷移
		}
	},
	watch: {
		$route: {
			async handler() {
				this.getPurchasedByUser();
				this.getShopUser();
				this.getReviewedByUser();
				this.getRecommendation();
			},
			immediate: true
		}
	}
}
</script>
