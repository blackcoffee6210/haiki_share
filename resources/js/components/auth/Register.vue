<template>
	<div class="l-main">
		<div class="p-auth-form">
			<!-- 利用者とお店の方の切り替えをするタグ -->
			<ul class="p-auth-form__group">
				<li class="p-auth-form__group__item"
						:class="{ 'p-auth-form__group__item--active': registerForm.group === 1 }"
						@click="registerForm.group = 1">利用者の方
				</li>
				<li class="p-auth-form__group__item"
						:class="{ 'p-auth-form__group__item--active': registerForm.group === 2 }"
						@click="registerForm.group = 2">お店の方
				</li>
			</ul>
			<h2 class="c-title p-auth-form__title" v-show="registerForm.group === 1">新規登録（利用者）</h2>
			<h2 class="c-title p-auth-form__title" v-show="registerForm.group === 2">新規登録（お店の方）</h2>
			<form class="p-auth-form__form" @submit.prevent="register">
				
				<!-- csrf -->
				<input type="hidden" name="_token" :value="$store.state.csrf">
				
				<!-- name -->
				<label for="name" class="c-label p-auth-form__label">
					<span v-show="registerForm.group === 1">お名前</span>
					<span v-show="registerForm.group === 2">コンビニ名</span>
				</label>
				<input type="text"
							 id="name"
							 class="c-input p-auth-form__input"
							 v-model="registerForm.name"
							 :placeholder="name">
				<!-- エラーメッセージ	-->
				<div v-if="registerErrors">
					<div v-for="msg in registerErrors.name" :key="msg" class="p-error">{{ msg }}</div>
				</div>
				
				<!-- 支店 -->
				<div v-show="registerForm.group === 2" class="u-mt20 u-mb5">
					<label for="branch" class="c-label p-auth-form__label">支店名</label>
					<input type="text"
								 id="branch"
								 class="c-input p-auth-form__input"
								 v-model="registerForm.branch"
								 placeholder="渋谷109前店">
					<!-- エラーメッセージ	-->
					<div v-if="registerErrors">
						<div v-for="msg in registerErrors.branch" :key="msg" class="p-error">{{ msg }}</div>
					</div>
				</div>
				
				<!-- 都道府県 -->
				<div v-show="registerForm.group === 2" class="u-mt20 u-mb5">
					<label for="prefecture" class="c-label p-auth-form__label">都道府県</label>
					<select id="prefecture" class="c-input p-auth-form__input" v-model="registerForm.prefecture_id">
						<option value="" disabled>都道府県を選択してください</option>
						
						<option v-for="prefecture in prefectures" :value="prefecture.id" :key="prefecture.id">
							{{ prefecture.name }}
						</option>
					</select>
					<!-- エラーメッセージ	-->
					<div v-if="registerErrors">
						<div v-for="msg in registerErrors.prefecture_id" :key="msg" class="p-error">{{ msg }}</div>
					</div>
				</div>
				
				<!-- 住所 -->
				<div v-show="registerForm.group === 2" class="u-mt20 u-mb5">
					<label for="address" class="c-label p-auth-form__label">住所</label>
					<input type="text"
								 id="address"
								 class="c-input p-auth-form__input"
								 v-model="registerForm.address"
								 placeholder="渋谷区宇田川町26-4">
					<!-- エラーメッセージ	-->
					<div v-if="registerErrors">
						<div v-for="msg in registerErrors.address" :key="msg" class="p-error">{{ msg }}</div>
					</div>
				</div>
				
				<!-- email-->
				<label for="email" class="c-label p-auth-form__label">Eメール</label>
				<input type="text"
							 id="email"
							 class="c-input p-auth-form__input"
							 v-model="registerForm.email"
							 placeholder="mail@haiki_share.com">
				<!-- エラーメッセージ	-->
				<div v-if="registerErrors">
					<div v-for="msg in registerErrors.email" :key="msg" class="p-error">{{ msg }}</div>
				</div>
				
				<!-- password-->
				<label for="password" class="c-label p-auth-form__label">パスワード(半角英数字)</label>
				<input type="password"
							 id="password"
							 class="c-input p-auth-form__input"
							 v-model="registerForm.password"
							 placeholder="8文字以上の半角英数字">
				<!-- エラーメッセージ	-->
				<div v-if="registerErrors">
					<div v-for="msg in registerErrors.password" :key="msg" class="p-error">{{ msg }}</div>
				</div>
				
				<!-- password-confirmation-->
				<label for="password-confirmation"
							 class="c-label p-auth-form__label">パスワード(確認)
				</label>
				<input type="password"
							 id="password-confirmation"
							 class="c-input p-auth-form__input"
							 v-model="registerForm.password_confirmation"
							 placeholder="8文字以上の半角英数字">
				<!-- エラーメッセージ	-->
				<div v-if="registerErrors">
					<div v-for="msg in registerErrors.password_confirmation" :key="msg" class="p-error">{{ msg }}</div>
				</div>
				
				<button class="c-btn p-auth-form__btn" type="submit">登録する（無料）</button>
			</form>
			<hr class="p-auth-form__u-line">
			<!-- ログイン画面へ遷移	-->
			<router-link :to="{ name: 'login' }"
									 class="c-link">ログインはこちら</router-link>
		</div>
	</div>
</template>

<script>
import { mapState } from 'vuex';
import { OK } from "../../util";

export default {
	name: "Register",
	data() {
		return {
			prefectures: [],
			registerForm: {
				group: 1,
				name: '',
				branch: '',
				prefecture_id: '',
				email: '',
				password: '',
				password_confirmation: ''
			}
		}
	},
	computed: {
		...mapState({
			//通信失敗の場合、つまりapiStatusがfalseの場合はインデックスへの移動を行わないように制御する
			//trueまたはfalseが入る
			apiStatus     : state => state.auth.apiStatus,
			registerErrors: state => state.auth.registerErrorMessages
		}),
		//名前のplaceholderを利用者とお店で切り替える
		name() {
			if(this.registerForm.group === 1) {
				return 'ハイキ君';
			}else if(this.registerForm.group === 2) {
				return 'ファミリーストア';
			}
		}
	},
	methods: {
		//都道府県取得
		async getPrefectures() {
			const response = await axios.get('/api/prefecture');
			//responseステータスがOKじゃなかったら
			if(response.status !== OK) {
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			//プロパティに値をセットする
			this.prefectures = response.data;
		},
		//ユーザー登録
		async register() {
			//dispatchメソッドでauthストアのregisterアクションを呼び出す
			//第一引数はアクション名、第二引数はフォームの入力値を渡す
			await this.$store.dispatch('auth/register', this.registerForm);
			//apiStatusがtrue(通信成功)なら後続の処理を行う
			if(this.apiStatus) {
				//メッセージを登録
				this.$store.commit('message/setContent', {
					content: 'ユーザー登録しました！',
				});
				//トップページ(index画面)に移動する
				this.$router.push({ name: 'index' });
			}
		},
		//エラーメッセージをクリアするメソッド
		clearError() {
			this.$store.commit('auth/setRegisterErrorMessages', null);
		}
	},
	created() {
		this.clearError();
	},
	watch: {
		$route: {
			async handler() {
				await this.getPrefectures();
			},
			immediate: true
		}
	}
}
</script>
