<template>
	<div class="p-emailConfirm">
		<h1 class="p-emailConfirm__title">Eメール確認中...</h1>
	</div>
</template>

<script>
import { OK } from "../../util";

export default {
	name: "EmailConfirmation",
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
					this.$router.push({ name: 'user.mypage', params: { id: this.id }}); //マイページに移動する
				}
				
			}catch(error) {
				console.error('Eメールの更新に失敗しました。もう一度試してください。');
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
