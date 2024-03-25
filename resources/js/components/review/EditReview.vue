<template>
	<main class="l-main">
		<div class="p-reviewForm" v-cloak>
			
			<h2 class="c-title p-reviewForm__title">レビュー編集</h2>
			
			<div class="p-reviewForm__background">
				<!-- ローディング -->
				<Loading color="#f96204" v-show="loading" />
				
				<!-- レビュー編集フォーム -->
				<form class="p-reviewForm__form" v-show="!loading" @submit.prevent="update">
					
					<!-- 出品者の情報 -->
					<div class="p-reviewForm__userInfo">
						<!-- 出品者の画像 -->
						<img :src="reviewForm.receiver_image"
								 alt=""
								 v-if="reviewForm.receiver_image"
								 class="c-icon p-reviewForm__icon">
						<img class="c-icon p-reviewForm__icon"
								 v-else
								 src="/storage/images/no-image.png"
								 alt="">
						<!-- 出品者の名前 -->
						<div class="p-reviewForm__userName">{{ reviewForm.receiver_name }}</div>
					</div>
					
					<!-- ユーザーの評価 -->
					<div>ユーザーの評価</div>
					<div class="p-reviewForm__recommendation__container" v-if="recommendations && recommendations.length > 0">
						<input type="radio"
									 :id="'recommend' + recommendations[0].id"
									 class="p-reviewForm__recommendation__input"
									 name="recommend"
									 :value="recommendations[0].id"
									 v-model.number="reviewForm.recommendation_id">
						<label :for="'recommend' + recommendations[0].id"
									 class="p-reviewForm__recommendation__label">
							<font-awesome-icon class="p-reviewForm__fa"
																 :icon="['far', 'laugh-beam']"
																 color="#ff6f80" />
							<span class="p-reviewForm__text">{{ recommendations[0].name }}</span>
						</label>
						<input type="radio"
									 :id="'recommend' + recommendations[1].id"
									 class="p-reviewForm__recommendation__input"
									 name="recommend"
									 :value="recommendations[1].id"
									 v-model.number="reviewForm.recommendation_id">
						<label :for="'recommend' + recommendations[1].id"
									 class="p-reviewForm__recommendation__label">
							<font-awesome-icon class="p-reviewForm__fa"
																 :icon="['far', 'smile']"
																 color="#ff6f80" />
							<span class="p-reviewForm__text">{{ recommendations[1].name }}</span>
						</label>
						<input type="radio"
									 :id="'recommend' + recommendations[2].id"
									 class="p-reviewForm__recommendation__input"
									 name="recommend"
									 :value="recommendations[2].id"
									 v-model.number="reviewForm.recommendation_id">
						<label :for="'recommend' + recommendations[2].id"
									 class="p-reviewForm__recommendation__label">
							<font-awesome-icon class="p-reviewForm__fa"
																 :icon="['far', 'meh']"
																 color="#ff6f80" />
							<span class="p-reviewForm__text">{{ recommendations[2].name }}</span>
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
					<label for="title" class="c-label p-reviewForm__label">レビュータイトル</label>
					<input type="text"
								 class="c-input p-reviewForm__input"
								 :class="{ 'c-input--err': errors.title ||
								 					 maxCounter(reviewForm.title, 50)
								 }"
								 id="title"
								 v-model="reviewForm.title"
								 placeholder="もっとも伝いたいポイントはなんですか？">
					<div class="u-d-flex u-space-between">
						<!-- エラーメッセージ（フロントエンド） -->
						<div v-if="maxCounter(reviewForm.title,50) && !errors.title" class="p-error">
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
					<label for="detail" class="c-label p-reviewForm__label u-mt0">レビューの内容
					</label>
					<textarea class="c-input p-reviewForm__textarea"
										:class="{ 'c-input--err': errors.detail ||
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
									 class="p-error">
								{{ msg }}
							</div>
						</div>
						<!-- 文字数カウンター -->
						<p class="c-counter"
							 :class="{ 'c-counter--err': maxCounter(reviewForm.detail,255) }">
							{{ reviewForm.detail.length }}/255
						</p>
					</div>
					
					<div class="p-reviewForm__btnContainer">
						<!-- ボタン -->
						<a @click="$router.back()" class="c-btn c-btn--white p-reviewForm__btn">もどる</a>
						<!-- 削除ボタン	-->
						<button class="c-btn c-btn--white p-reviewForm__btn"
										@click="deleteReview"
										type="button">削除する
						</button>
						
						<!-- 更新ボタン -->
						<button class="c-btn p-reviewForm__btn" type="submit">更新する</button>
					</div>
				</form>
			</div>
		</div>
	</main>
</template>

<script>
import Loading                      from "../Loading";
import { OK, UNPROCESSABLE_ENTITY } from "../../util";

export default {
	name: "EditReview",
	components: {
		Loading
	},
	props: {
		s_id: { //利用者のユーザーid
			type: String,
			required: true
		},
		r_id: { //コンビニユーザーid
			type: String,
			required: true
		}
	},
	data() {
		return {
			loading: false, //ローディング
			recommendations: [],
			reviewForm: {
				title: '',
				detail: ''
			},
			errors: {       //エラーメッセージ
				recommendation_id: null,
				title: null,
				detail: null
			},
		}
	},
	methods: {
		maxCounter(content, maxValue) { //カウンターの文字数上限
			return content.length > maxValue;
		},
		async getRecommendation() { //ユーザー評価取得
			try {
				const response = await axios.get('/api/recommendations') //API接続
				
				if(response.status === OK) { //成功
					this.recommendations = response.data;
					
				}else { //失敗
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
			}catch (error) {
				console.error('ユーザー評価取得中にエラーが発生しました', error);
			}
		},
		async getReview() { //レビュー取得
			this.loading = true;
			
			try {
				const response = await axios.get(`/api/reviews/${this.s_id}/${this.r_id}`);
				
				if(response.status === OK) { //成功
					this.reviewForm = response.data;
				
				}else { //失敗
					this.$store.commit('error/setCode', response.status);
				}
				
			}catch (error) {
				console.error('レビュー取得処理中にエラーが発生しました', error);
				
			}finally {
				this.loading = false;
			}
		},
		async update() { //レビュー更新
			this.loading = true; //ローディングを表示する
			
			try {
				const response = await axios.post('/api/reviews/update', this.reviewForm); //API接続
				
				if(response.status === OK) { //成功なら
					this.$store.commit('message/setContent', { //メッセージ登録
						content: 'レビューを更新しました！',
					});
					this.$router.push({name: 'user.mypage', params: {id: this.s_id.toString()}}); //マイページに遷移
					
				}else if (response.status === UNPROCESSABLE_ENTITY) { //responseステータスがバリデーションエラーなら後続の処理を行う
					this.errors = response.data.errors;
					return false;
					
				}else {
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
			}catch (error) {
				console.error('更新に失敗しました。', error);
				
			}finally {
				this.loading = false; //ローディングを非表示にする
			}
		},
		async deleteReview() { //レビュー削除
			if(!confirm('レビューを削除しますか?')) return;
			
			this.loading = true; //ローティングを表示する
			
			try {
				const response = await axios.delete(`/api/reviews/${this.s_id}/${this.r_id}`); //API通信
				
				if(response.status === OK) { //成功なら
					this.$store.commit('message/setContent', {  //メッセージ登録
						content: 'レビューを削除しました'
					});
					this.$router.push({ name: 'user.mypage', params: { id: this.s_id.toString() }}); //マイページに移動する
					
				}else { //失敗ならエラーコードをセット
					this.$store.commit('error/setCode', response.status);
					return false;
				}
				
			}catch(error) {
				console.error('レビュー削除処理に失敗しました', error);
				
			}finally {
				this.loading = false; //ローディングを非表示にする
			}
		}
	},
	watch: {
		$route: {
			async handler() {
				this.getRecommendation();
				this.getReview();
			},
			immediate: true
		}
	}
}
</script>
