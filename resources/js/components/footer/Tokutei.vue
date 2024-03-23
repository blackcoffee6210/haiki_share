<template>
	<main class="l-main">
		<div class="p-tokutei">
			<h1 class="p-tokutei__title">特定商取引法に基づく表示</h1>
			<div>
				<table class="p-tokutei__table">
					<tbody>
					<tr>
						<th>販売事業者名</th>
						<td>{{ name }}</td>
					</tr>
					<tr>
						<th>代表者</th>
						<td>5lack</td>
					</tr>
					<tr>
						<th>所在地</th>
						<td>大阪府守口市佐太東町3-101-5</td>
					</tr>
					<tr>
						<th>お問い合わせ<br>メールアドレス</th>
						<td>{{ email }}</td>
					</tr>
					<tr>
						<th>電話番号</th>
						<td>
							07099839841<br>
							(お電話での対応は行なっておりません。サービスに関するお問い合わせは、お問い合わせフォームよりお願いいたします。)
						</td>
					</tr>
					<tr>
						<th>商品代金</th>
						<td>各商品ページをご参照ください。</td>
					</tr>
					<tr>
						<th>商品代金以外の<br>必要料金</th>
						<td>「銀行振込」にてお支払い頂く際は、<br class="p-tokutei__br">別途振り込み手数料をご負担ください。</td>
					</tr>
					<tr>
						<th>販売数量</th>
						<td>特に制限はございません。</td>
					</tr>
					<tr>
						<th>商品代金の<br>支払時期</th>
						<td>
							銀行振込：お申し込み後7日以内、<br class="p-tokutei__br">
							クレジットカード決済：お申込み時
						</td>
					</tr>
					<tr>
						<th>引き渡し時期</th>
						<td>決済完了後、購入先のコンビニにて</td>
					</tr>
					<tr>
						<th>引き渡し方法</th>
						<td>購入先のコンビニにて</td>
					</tr>
					<tr>
						<th>お支払い方法</th>
						<td>購入先のコンビニにて現金、クレジット決済でお支払いください。</td>
					</tr>
					<tr>
						<th>ご返品について</th>
						<td>商品の特性上返品はお受けできません。<br class="p-tokutei__br">ご了承下さい。</td>
					</tr>
					<tr>
						<th>ご解約について</th>
						<td>
							解約（退会）は必ずログイン後に<br class="p-tokutei__br">
							<router-link :to="{ name: 'user.withdrawal',
																	params: { id: userId.toString()} }"
													 class="p-tokutei__withdraw">コチラ
							</router-link>
							から行なってください。
						</td>
					</tr>
					</tbody>
				</table>
			</div>
			
			<!-- TOPへ	-->
			<transition name="c-to-top">
				<button class="c-goTop"
								v-show="buttonActive"
								@click="returnTop">TOP</button>
			</transition>
		</div>
	</main>
</template>

<script>
import { mapState, mapGetters } from 'vuex';

export default {
	name: "Tokutei",
	data() {
		return {
			name: '株式会社 Haiki Share',    //会社名
			email: 'haikishare1@gmail.com', //Eメール
			buttonActive: false,            //TOPボタンを表示する
			scroll: 0,                      //scroll
		}
	},
	computed: {
		...mapGetters({
			isLogin: 'auth/check', //ログインしているかどうか
			userId: 'auth/userId'  //ログインユーザーのユーザーID
		})
	},
	methods: {
		returnTop() { //TOPボタンを押したらページ先頭に移動
			window.scrollTo({
				top: 0,
				behavior: 'smooth'
			})
		},
		scrollWindow() { //一定量スクロールしたら右下に「TOP」ボタンを表示
			const top = 100; //ボタンを表示させたい位置
			this.scroll = window.scrollY;
			if(top <= this.scroll) {
				this.buttonActive = true;
			} else {
				this.buttonActive = false;
			}
		},
	},
	mounted() {
		window.addEventListener('scroll', this.scrollWindow);
	}
}
</script>
