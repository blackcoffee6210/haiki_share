<template>
	<div class="p-emailConfirm">
		<h1 class="p-emailConfirm__title">Eメール確認中...</h1>
	</div>
</template>

<script>
import {NOT_FOUND, OK, UNPROCESSABLE_ENTITY} from "../../util";
import { mapGetters } from "vuex";

export default {
	name: "EmailConfirmation",
	computed: {
		...mapGetters({
			userId: 'auth/userId' //ユーザーID
		})
	},
	data() {
		return {
			message: '',
		}
	},
	methods: {
		async confirmEmail(token) {
			try {
				const response = await axios.get(`/api/email/confirm/${token}`);
				
				if(response.status === OK) {
					this.$store.commit('message/setContent', { content: 'Eメールを更新しました！' }); //メッセージ登録
					this.$router.push({ name: 'user.mypage', params: { id: this.userId }}); //マイページに移動する
				}
				
			}catch(error) {
				if(error.response && error.response.status === NOT_FOUND) {
					alert('リンクの有効期限が切れています。もう一度メール更新手続きを行ってください。');
					this.$router.push({ name: 'user.editEmail', params: { id: this.userId } });
				
				}else {
					console.error('Eメールの更新に失敗しました。もう一度試してください。');
				}
			}
		}
	},
	created() {
		const token = this.$route.query.token;
		if(token) {
			this.confirmEmail(token);
		}
	}
}
</script>
