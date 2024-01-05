<template>
	<!--todo: classエラー処理を入れる -->
	<!--todo: inputタグ上にスペースができてるので修正 -->
	<div class="p-review-form" v-cloak>
		
		<h2 class="c-title p-review-form__title">レビュー投稿</h2>
		
		<div class="p-review-form__background">
			<!-- ローディング -->
			<Loading color="#f96204" v-show="loading" />
			
			<!-- レビュー投稿フォーム -->
			<form class="p-review-form__form"
						v-show="!loading"
						@submit.prevent="submit">
				
				<!-- ユーザーの情報 -->
				<div class="p-review-form__user-info">
					<img :src="reviewForm.shopUser.image"
							 alt=""
							 class="c-icon p-review-form__icon">
					<div class="p-review-form__user-name">{{ reviewForm.shopUser.name }}</div>
				</div>
				
				<!-- ユーザーの評価 -->
				<div>ユーザーの評価</div>
				<!--<div class="p-review-form__recommendation__container">-->
				<!--	<div v-for="recommendation in recommendations"-->
				<!--			 :key="recommendation.id">-->
				<!--		<input type="radio"-->
				<!--					 :id="recommendation.id"-->
				<!--					 class="p-review-form__recommendation__input"-->
				<!--					 name="recommend"-->
				<!--					 :value="recommendation.id"-->
				<!--					 v-model.number="reviewForm.recommendation">-->
				<!--		<label :for="recommendation.id"-->
				<!--					 class="p-review-form__recommendation__label">-->
				<!--			<font-awesome-icon style="font-size: 24px;"-->
				<!--												 :icon="['far', 'laugh-beam']"-->
				<!--												 color="#ff6f80" />-->
				<!--			<span class="u-ml5">{{ recommendation.name }}</span>-->
				<!--		</label>-->
				<!--	</div>-->
				<!--</div>-->
				<div class="p-review-form__recommendation__container">
					<input type="radio"
								 :id="'recommend' + recommendations[0].id"
								 class="p-review-form__recommendation__input"
								 name="recommend"
								 :value="recommendations[0].id"
								 v-model.number="reviewForm.recommendation_id">
					<label :for="'recommend' + recommendations[0].id"
								 class="p-review-form__recommendation__label">
						<font-awesome-icon style="font-size: 24px;"
															 :icon="['far', 'laugh-beam']"
															 color="#ff6f80" />
						<span class="u-ml5">{{ recommendations[0].name }}</span>
					</label>
					<input type="radio"
								 :id="'recommend' + recommendations[1].id"
								 class="p-review-form__recommendation__input"
								 name="recommend"
								 :value="recommendations[1].id"
								 v-model.number="reviewForm.recommendation_id">
					<label :for="'recommend' + recommendations[1].id"
								 class="p-review-form__recommendation__label">
						<font-awesome-icon style="font-size: 24px;"
															 :icon="['far', 'smile']"
															 color="#ff6f80" />
						<span class="u-ml5">{{ recommendations[1].name }}</span>
					</label>
					<input type="radio"
								 :id="'recommend' + recommendations[2].id"
								 class="p-review-form__recommendation__input"
								 name="recommend"
								 :value="recommendations[2].id"
								 v-model.number="reviewForm.recommendation_id">
					<label :for="'recommend' + recommendations[2].id"
								 class="p-review-form__recommendation__label">
						<font-awesome-icon style="font-size: 24px;"
															 :icon="['far', 'meh']"
															 color="#ff6f80" />
						<span class="u-ml5">{{ recommendations[2].name }}</span>
					</label>
				</div>
				<!--<div class="p-review-form__recommendation__container">-->
				<!--	<input type="radio"-->
				<!--				 id="recommend1"-->
				<!--				 class="p-review-form__recommendation__input"-->
				<!--				 name="recommend"-->
				<!--				 value="1"-->
				<!--				 v-model.number="reviewForm.recommendation">-->
				<!--	<label for="recommend1"-->
				<!--				 class="p-review-form__recommendation__label">-->
				<!--		<font-awesome-icon style="font-size: 24px;"-->
				<!--											 :icon="['far', 'laugh-beam']"-->
				<!--											 color="#ff6f80" />-->
				<!--		<span class="u-ml5">とてもオススメ</span>-->
				<!--	</label>-->
				<!--	<input type="radio"-->
				<!--				 id="recommend2"-->
				<!--				 class="p-review-form__recommendation__input"-->
				<!--				 name="recommend"-->
				<!--				 value="2"-->
				<!--				 v-model.number="reviewForm.recommendation">-->
				<!--	<label for="recommend2"-->
				<!--				 class="p-review-form__recommendation__label">-->
				<!--		<font-awesome-icon style="font-size: 20px;"-->
				<!--											 :icon="['fas', 'smile']"-->
				<!--											 color="#ff6f80" />-->
				<!--		<span class="u-ml5">オススメ</span>-->
				<!--	</label>-->
				<!--	<input type="radio"-->
				<!--				 id="recommend3"-->
				<!--				 class="p-review-form__recommendation__input"-->
				<!--				 name="recommend"-->
				<!--				 value="3"-->
				<!--				 v-model.number="reviewForm.recommendation">-->
				<!--	<label for="recommend3"-->
				<!--				 class="p-review-form__recommendation__label">-->
				<!--		<font-awesome-icon style="font-size: 20px;"-->
				<!--											 :icon="['fas', 'meh']"-->
				<!--											 color="#ff6f80" />-->
				<!--		<span class="u-ml5">オススメしない</span>-->
				<!--	</label>-->
				<!--</div>-->
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
							 id="title"
							 v-model="reviewForm.title"
							 placeholder="もっとも伝いたいポイントはなんですか？">
				<!-- エラーメッセージ	-->
				<div v-if="errors">
					<div v-for="msg in errors.title"
							 :key="msg"
							 class="p-error">{{ msg }}
					</div>
				</div>
				
				<!-- レビューの内容 -->
				<label for="detail"
							 class="c-label p-review-form__label">レビューの内容
				</label>
				<textarea class="c-input p-review-form__textarea"
									id="detail"
									v-model="reviewForm.detail"
									placeholder="レビューの内容を入力してください"
				></textarea>
				<!-- エラーメッセージ	-->
				<div v-if="errors">
					<div v-for="msg in errors.detail"
							 :key="msg"
							 class="p-error">{{ msg }}
					</div>
				</div>
				
				<!-- 投稿ボタン -->
				<button class="c-btn p-review-form__btn"
								type="submit">投稿する
				</button>
			</form>
		</div>
	</div>
</template>

<script>
import Loading from "../Loading";
import { CREATED, OK, UNPROCESSABLE_ENTITY } from "../../util";
import { mapGetters } from 'vuex';

export default {
	name: "RegisterReview",
	components: {
		Loading
	},
	props: {
		id: String,
		required: true
	},
	data() {
		return {
			loading: false, //ローディング
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
			}
		}
	},
	computed: {
		...mapGetters({
			userId: 'auth/userId'
		})
	},
	methods: {
		async getShopUser() { //商品idを元に出品ユーザーを取得
			const response = await axios.get(`/api/users/${this.id}/shopUser`); //API通信
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			
			this.reviewForm.shopUser    = response.data[0];                 //出品者の情報
			this.reviewForm.sender_id   = this.userId;                      //送信者（レビュー投稿者）のユーザーid
			this.reviewForm.receiver_id = this.reviewForm.shopUser.user_id; //受信者（出品者）のユーザーid
			console.log(this.reviewForm);
		},
		async getRecommendation() { //ユーザー評価取得
			const response = await axios.get('/api/recommendations') //API接続
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			
			this.recommendations = response.data;
			console.log(this.recommendations);
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
				this.getShopUser();
				this.getRecommendation();
			},
			immediate: true
		}
	}
}
</script>
