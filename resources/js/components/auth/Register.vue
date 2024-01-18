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
			<h2 class="c-title p-auth-form__title"
					v-show="registerForm.group === 1">新規登録（利用者）
			</h2>
			<h2 class="c-title p-auth-form__title"
					v-show="registerForm.group === 2">新規登録（お店の方）
			</h2>
			<form class="p-auth-form__form" @submit.prevent="register">
				
				<!-- name -->
				<label for="name" class="c-label p-auth-form__label">
					<span v-show="registerForm.group === 1">お名前</span>
					<span v-show="registerForm.group === 2">コンビニ名</span>
				</label>
				<input type="text"
							 id="name"
							 class="c-input p-auth-form__input"
							 :class="{ 'c-input__err': (registerErrors) ? registerErrors.name : ''}"
							 v-model="registerForm.name"
							 :placeholder="name">
				<!-- エラーメッセージ	-->
				<div v-if="registerErrors">
					<div v-for="msg in registerErrors.name"
							 :key="msg"
							 class="p-error">{{ msg }}
					</div>
				</div>
				
				<!-- 支店 -->
				<div v-show="registerForm.group === 2">
					<label for="branch" class="c-label p-auth-form__label">支店名</label>
					<input type="text"
								 id="branch"
								 class="c-input p-auth-form__input"
								 :class="{ 'c-input__err': (registerErrors) ? registerErrors.branch : ''}"
								 v-model="registerForm.branch"
								 placeholder="渋谷109前店">
					<!-- エラーメッセージ	-->
					<div v-if="registerErrors">
						<div v-for="msg in registerErrors.branch"
								 :key="msg"
								 class="p-error">{{ msg }}
						</div>
					</div>
				</div>
				
				<!-- 都道府県 -->
				<div v-show="registerForm.group === 2">
					<label for="prefecture" class="c-label p-auth-form__label">都道府県</label>
					<select id="prefecture"
									class="c-select p-auth-form__input"
									:class="{ 'c-select__err': (registerErrors) ? registerErrors.prefecture_id : ''}"
									v-model="registerForm.prefecture_id">
						<option value="" disabled>都道府県を選択してください</option>
						<option v-for="prefecture in prefectures" :value="prefecture.id" :key="prefecture.id">
							{{ prefecture.name }}
						</option>
					</select>
					<!-- エラーメッセージ	-->
					<div v-if="registerErrors">
						<div v-for="msg in registerErrors.prefecture_id"
								 :key="msg"
								 class="p-error">{{ msg }}
						</div>
					</div>
				</div>
				
				<!-- 住所 -->
				<div v-show="registerForm.group === 2">
					<label for="address"
								 class="c-label p-auth-form__label">住所
					</label>
					<input type="text"
								 id="address"
								 class="c-input p-auth-form__input"
								 :class="{ 'c-input__err': (registerErrors) ? registerErrors.address : ''}"
								 v-model="registerForm.address"
								 placeholder="渋谷区宇田川町26-4">
					<!-- エラーメッセージ	-->
					<div v-if="registerErrors">
						<div v-for="msg in registerErrors.address"
								 :key="msg"
								 class="p-error">{{ msg }}
						</div>
					</div>
				</div>
				
				<!-- email-->
				<label for="email" class="c-label p-auth-form__label">Eメール</label>
				<input type="text"
							 id="email"
							 class="c-input p-auth-form__input"
							 :class="{ 'c-input__err': (registerErrors) ? registerErrors.email : ''}"
							 v-model="registerForm.email"
							 placeholder="mail@haiki_share.com">
				<!-- エラーメッセージ	-->
				<div v-if="registerErrors">
					<div v-for="msg in registerErrors.email" :key="msg" class="p-error">{{ msg }}</div>
				</div>
				
				<!-- password-->
				<label for="password" class="c-label p-auth-form__label">パスワード</label>
				<input type="password"
							 id="password"
							 class="c-input p-auth-form__input"
							 :class="{ 'c-input__err': (registerErrors) ? registerErrors.password : ''}"
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
							 :class="{ 'c-input__err': (registerErrors) ? registerErrors.password_confirmation : ''}"
							 v-model="registerForm.password_confirmation"
							 placeholder="8文字以上の半角英数字">
				<!-- エラーメッセージ	-->
				<div v-if="registerErrors">
					<div v-for="msg in registerErrors.password_confirmation" :key="msg" class="p-error">{{ msg }}</div>
				</div>
				
				<!-- 利用規約 -->
				<div class="p-auth-form__check-container">
					<input type="checkbox"
								 id="agreement"
								 v-model="checkAgree"
								 class="p-auth-form__check">
					<router-link class="p-auth-form__agreement"
											 target="_blank"
											 :to="{ name: 'agreement'}">利用規約
					</router-link>
					<label for="agreement">に同意する</label>
				</div>
				
				<div class="u-p-relative">
					<button class="c-btn p-auth-form__btn"
									:disabled="!checkAgree"
									type="submit">登録する（無料）
					</button>
					<!--<div class="p-auth-form__btn&#45;&#45;mask">利用規約にチェックしてください</div>-->
				</div>
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
				address: '',
				email: '',
				password: '',
				password_confirmation: ''
			},
			checkAgree: false
		}
	},
	computed: {
		...mapState({
			apiStatus     : state => state.auth.apiStatus, //通信失敗の場合、つまりapiStatusがfalseの場合はインデックスへの移動を行わないように制御する
			registerErrors: state => state.auth.registerErrorMessages
		}),
		name() { //名前インプットエリアのplaceholderを利用者とお店で切り替える
			switch (this.registerForm.group) {
				case 1:
					return 'ハイキ君';
				case 2:
					return 'ファミリーストア';
			}
		}
	},
	methods: {
		async getPrefectures() { //都道府県取得
			const response = await axios.get('/api/prefectures');
			
			if(response.status !== OK) { //responseステータスがOKじゃなかったら
				this.$store.commit('error/setCode', response.status);
				return false;
			}
			this.prefectures = response.data; //プロパティに値をセットする
		},
		async register() { //ユーザー登録
			await this.$store.dispatch('auth/register', this.registerForm); //dispatchメソッドでauthストアのregisterアクションを呼び出す
			
			if(this.apiStatus) { //apiStatusがtrue(通信成功)なら後続の処理を行う
				this.$store.commit('message/setContent', { //メッセージを登録
					content: 'ユーザー登録しました！',
				});
				this.$router.push({ name: 'index' }); //トップページ(index画面)に移動する
			}
		},
		clearError() { //エラーメッセージをクリアするメソッド
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
