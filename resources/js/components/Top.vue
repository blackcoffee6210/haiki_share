<template>
	<div class="p-top">
		<!--ランディングページには、-->
		<!--共感 →-->
		<!--ベネフィット →-->
		<!--実績・事例 →-->
		<!--お客様の声 →-->
		<!--特典による検討強化 →-->
		<!--クロージング-->
		<!--という基本の型があります。-->
		
		<!-- ファーストビュー（共感） -->
		<div class="p-top__bg">
			<transition name="hero" appear>
				<section class="p-top__section p-top__section--hero">
					<div class="p-top__title">そこのあなた！ こう思ったことはありませんか？</div>
					<p class="p-top__think p-top__think--user">
						コンビニは便利だけどスーパーより高い..<br>
						もっと安く買えたらな...
					</p>
					<p class="p-top__think p-top__think--shop">
						まだ食べられる食品を<br>
						廃棄で捨てるのはもったいないな...
					</p>
					<p class="p-top__title">
						そのお悩み、<br>
						「Haiki Share（ハイキシェア）」で解決できます！
					</p>
				</section>
			</transition>
		</div>
		
		<!-- サービス紹介 - haiki_shareとは？ -->
		<!--todo: underlineを動的にする！-->
		<div class="p-top__bg--alter">
			<section class="p-top__section">
				<h2 class="p-top__title"
						:class="{ fadeIn1: visible1 }"
						v-show="visible1">
					Haiki Share（ハイキシェア）とはどんなサービス？
				</h2>
				<p class="p-top__text">
					みなさん、コンビニでの廃棄金額がいくらかご存知ですか？
				</p>
				<p class="p-top__text">
					なんと、1店舗あたり年間
					<span class="p-top__price"
								:class="{ fadeIn2: visible2 }"
								v-show="visible2">468
					</span>
					万円！（中央値）<br>
					<span class="p-top__note">
						※公正取引委員会による全国の大手コンビニエンスストア5万7524店を対象に行った調査（1万2093店が回答）
					</span>
				</p>
				<p class="p-top__text">
					1日に約
					<!--<span class="p-top__price">13,000</span>-->
					<span class="p-top__price"
								:class="{ fadeIn2: visible2 }"
								v-show="visible2">13,000
					</span>
					円もの食品が廃棄として捨てられているんです。
				</p>
				<p class="p-top__text">
					まだまだ食べられる食品を捨てるのはもったいない！
				</p>
				<p class="p-top__text">
					そんなコンビニユーザーと、安く買いたい利用者の想いを「シェア」するサービスです。
				</p>
			</section>
		</div>
		
		<!-- 利用者のメリット（ベネフィット） -->
		<div class="p-top__bg">
			<section class="p-top__section">
				<h2 class="p-top__title"
						:class="{ fadeIn3: visible3 }"
						v-show="visible3">Haiki Share 3つのメリット!
				</h2>
				
				<div class="p-top__container p-top__container--2line">
					<div class="p-top__ornament">利用者の方</div>
					<div class="c-card p-top__panel">
						<h3 class="p-top__panel__title">
							コンビニの食品を<br>安く購入できる！
						</h3>
					</div>
					<div class="c-card p-top__panel">
						<h3 class="p-top__panel__title">
							お近くのコンビニから<br>探すことができる！
						</h3>
					</div>
					<div class="c-card p-top__panel">
						<h3 class="p-top__panel__title">
							たくさんの<br>カテゴリーから選べる！
						</h3>
					</div>
				</div>
				
				<div class="p-top__container">
					<div class="p-top__ornament">コンビニユーザー</div>
					<div class="c-card p-top__panel">
						<h3 class="p-top__panel__title">
							今まで捨てていた食品<br>で売り上げUP！
						</h3>
					</div>
					<div class="c-card p-top__panel">
						<h3 class="p-top__panel__title">
							カンタン出品！<br>
							スマホでもパソコンでも！
						</h3>
					</div>
					<div class="c-card p-top__panel">
						<h3 class="p-top__panel__title">
							賞味期限が近い、<br>過ぎている食品もOK!
						</h3>
					</div>
				</div>
			</section>
		</div>
		
		<!-- 実績・事例 -->
		<div class="p-top__bg--alter">
			<section class="p-top__section">
				<h2 class="p-top__title">実績・事例</h2>
				<div class="p-top__container">
					<div class="c-card p-top__panel">
						<div class="p-top__panel__title">
							北は北海道、南は沖縄までたくさんの方にご利用いただいてます！
						</div>
						<img src="/storage/images/japan.png"
								 class="p-top__panel__image"
								 alt="">
					</div>
					<div class="c-card p-top__panel">
						LOREM IPSUM DOLOR SIT AMET, CONSECTETUR ADIPISICING ELIT, SED DO
						EIUSMOD TEMPOR INCIDIDUNT UT LABORE
					</div>
					<div class="c-card p-top__panel">
						LOREM IPSUM DOLOR SIT AMET, CONSECTETUR ADIPISICING ELIT, SED DO
					</div>
				</div>
			</section>
		</div>
		
		<!-- 利用者の声 -->
		<div class="p-top__bg">
			<section class="p-top__section">
				<h2 class="p-top__title">利用者の声</h2>
				<div class="p-top__container">
					<Review v-show="!loading"
									class="p-top__review"
									v-for="review in reviews"
									:key="review.id"
									:review="review" />
				</div>
			</section>
		</div>
		
		<!-- こんな方におすすめ（クロージング） -->
		<div class="p-top__bg--alter">
			<section class="p-top__section">
				<h2 class="p-top__title">こんな方におすすめ！</h2>
				<p>毎日食べる食品を安く買いたいお客様！</p>
				<p>今まで捨てていた食品を売って売り上げを上げたいコンビニの皆様！</p>
				<p>美味しく食べてSDGs！ 地球に貢献!</p>
			</section>
		</div>
		
		<!-- カンタン！３ステップ （利用者）-->
		<div class="p-top__bg">
			<section class="p-top__section">
				<h2 class="p-top__title">カンタン！３ステップ</h2>
				
				<div class="p-top__container p-top__container--step p-top__container--2line">
					<div class="p-top__ornament p-top__ornament--step">利用者の方</div>
					<div class="p-top__panel p-top__panel--step">
						<div class="p-top__step">STEP<span class="p-top__step-number">1</span></div>
						<h3 class="p-top__step-title">カンタン！<br>会員登録</h3>
					</div>
					<div class="p-top__panel p-top__panel--step">
						<div class="p-top__step">STEP<span class="p-top__step-number">2</span></div>
						<h3 class="p-top__step-title">気になる商品を<br>購入する</h3>
					</div>
					<div class="p-top__panel p-top__panel--step">
						<div class="p-top__step">STEP<span class="p-top__step-number">3</span></div>
						<h3 class="p-top__step-title">コンビニで<br>商品を受け取る</h3>
					</div>
				</div>
				
				<div class="p-top__container p-top__container--step">
					<div class="p-top__ornament p-top__ornament--step">コンビニユーザー</div>
					<div class="p-top__panel p-top__panel--step">
						<div class="p-top__step">STEP<span class="p-top__step-number">1</span></div>
						<h3 class="p-top__step-title">カンタン！<br>会員登録</h3>
					</div>
					<div class="p-top__panel p-top__panel--step">
						<div class="p-top__step">STEP<span class="p-top__step-number">2</span></div>
						<h3 class="p-top__step-title">写真を撮って<br>商品を出品</h3>
					</div>
					<div class="p-top__panel p-top__panel--step">
						<div class="p-top__step">STEP<span class="p-top__step-number">3</span></div>
						<h3 class="p-top__step-title">購入された商品を<br>用意する</h3>
					</div>
				</div>
			</section>
		</div>
		
		<!-- サービス登録欄 -->
		<div class="p-top__bg--alter">
			<section class="p-top__section">
				<h2 class="p-top__title">
					廃棄食品を無駄にしないサービス<br>
					「Haiki Share」を早速利用してみる！
				</h2>
				
				<router-link class="c-btn p-top__btn"
										 :to="{ name: 'register'}">簡単！ 無料会員登録
				</router-link>
				
			</section>
		</div>
		
		<!-- よくある質問 -->
		<section class="p-top__section"></section>
		
		<!-- TOPへ	-->
		<transition name="c-go-top">
			<button class="c-go-top p-top__go-top"
							v-show="buttonActive"
							@click="returnTop">TOP</button>
		</transition>
	</div>
</template>

<script>
import {OK}   from "../util";
import Review from "./review/Review";

export default {
	name: "Top",
	components: {
		Review
	},
	data() {
		return {
			loading: false,
			reviews: [],         //レビューをランダムで3件
			buttonActive: false, //TOPボタンを表示する
			scroll: 0,           //scroll
			visible1: false,
			visible2: false,
			visible3: false,
		}
	},
	methods: {
		async getReviews() { //レビューをランダムで3件取得
			this.loading   = true; //ローディングを表示する
			const response = await axios.get('/api/reviews/topPageReview');
			this.loading   = false; //API通信が終わったらローディングを非表示にする
			
			if (response.status !== OK) { //responseステータスがOKじゃなかったらエラーコードをセットする
				this.$store.commit('error/setCode', response.status);
				return false; //後続の処理を抜ける
			}
			this.reviews = response.data; //responseデータをプロパティに代入
			console.log(this.reviews);
		},
		returnTop() { //「TOPにもどる」ボタンを押したときの処理
			window.scrollTo({
				top: 0,
				behavior: 'smooth'
			});
		},
		scrollWindow() { //「TOPにもどる」ボタンを表示するメソッド
			const top = 100; //ボタンを表示させたい位置
			this.scroll = window.scrollY;
			
			(top <= this.scroll) ? //scrollがtop以上になったらボタンを表示する
					this.buttonActive = true :
					this.buttonActive = false;
		},
		handleScroll1() {
			if(!this.visible1) {
				this.visible1 = window.scrollY > 200;
			}else if(window.scrollY < 190) {
				this.visible1 = !this.visible1;
			}
		},
		handleScroll2() {
			if(!this.visible2) {
				this.visible2 = window.scrollY > 200;
			}else if(window.scrollY < 190) {
				this.visible2 = !this.visible2;
			}
		},
		handleScroll3() {
			if(!this.visible3) {
				this.visible3 = window.scrollY > 200;
			}else if(window.scrollY < 190) {
				this.visible3 = !this.visible3;
			}
		}
	},
	mounted() {
		window.addEventListener('scroll', this.scrollWindow);
		window.addEventListener('scroll', this.handleScroll1);
		window.addEventListener('scroll', this.handleScroll2);
		window.addEventListener('scroll', this.handleScroll3);
	},
	destroyed() {
		window.removeEventListener("scroll", this.handleScroll1);
		window.removeEventListener("scroll", this.handleScroll2);
		window.removeEventListener("scroll", this.handleScroll3);
	},
	watch: {
		$route: { //$routerを監視してページが変わったときにメソッドが実行されるようにする
			async handler() {
				await this.getReviews();
			},
			immediate: true //immediateをtrueにすると、コンポーネントが生成されたタイミングでも実行する
		}
	}
}
</script>

<style>
.fadeIn1 {
	animation: fadeIn1 1s;
}
@keyframes fadeIn1 {
	0% {
		opacity: 0;
		transform: translateY(50px);
	}
	100% {
		opacity: 1;
		transform: translateY(0px);
	}
}

.fadeIn2 {
	animation: fadeIn2 1s;
}
@keyframes fadeIn2 {
	0% {
		opacity: 0;
		transform: translateY(300px);
	}
	100% {
		opacity: 1;
		transform: translateY(0px);
	}
}

.fadeIn3 {
	animation: fadeIn3 1s;
}
@keyframes fadeIn3 {
	0% {
		opacity: 0;
		transform: translateX(50px);
	}
	100% {
		opacity: 1;
		transform: translateX(0px);
	}
}

.hero-enter-active,
.hero-leave-active {
	transition: opacity 1s, transform 1s;
}
.hero-enter,
.hero-leave-to {
	opacity: 0;
	transform: translateY(50px);
}
</style>
