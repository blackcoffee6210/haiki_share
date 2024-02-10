<template>
	<div class="p-top">
		<!--ランディングページには、-->
		<!--共感 → ベネフィット → 実績・事例 → お客様の声 → 特典による検討強化 → クロージング -->
		<!--という基本の型があります。-->
		
		<!-- ファーストビュー（共感） -->
		<div class="p-top__bg">
			<transition name="hero" appear>
				<section class="p-top__section p-top__section--hero">
					<div class="p-top__title">そこのあなた！ <br class="u-br">
						こう思ったことはありませんか？
					</div>
					<p class="p-top__think p-top__think--user">
						コンビニは便利だけど<br class="p-top__br--think">
						スーパーより高い..<br>
						もっと安く買えたらな...
					</p>
					<img src="/storage/images/expensive.png"
							 class="p-top__think-img p-top__think-img--user"
							 alt="">
					<p class="p-top__think p-top__think--shop">
						まだ食べられる食品を<br>
						廃棄で捨てるのは<br class="p-top__br--think">
						もったいないな...
					</p>
					<img src="/storage/images/trash.png"
							 class="p-top__think-img p-top__think-img--shop"
							 alt="">
					<p class="p-top__title">
						そのお悩み、<br>
						「Ha<span class="c-color__main">i</span>ki Share（ハイキシェア）」で解決できます！
					</p>
					<router-link class="c-btn p-top__btn" :to="{ name: 'index' }">商品一覧を見てみる</router-link>
				</section>
			</transition>
		</div>
		
		<!-- サービス紹介 - haiki_shareとは？ -->
		<div class="p-top__bg--alter">
			<section class="p-top__section">
				<FadeIn>
					<h2 class="p-top__title">Ha<span class="c-color__main">i</span>ki Share（ハイキシェア）とはどんなサービス？</h2>
				</FadeIn>
				<p class="p-top__text">
					みなさん、コンビニでの廃棄金額がいくらかご存知ですか？
				</p>
				<p class="p-top__text">
					なんと、1店舗あたり年間
					<span class="p-top__price">468</span>
					万円！（中央値）<br>
					<span class="p-top__note">
						※公正取引委員会による全国の大手コンビニエンスストア5万7524店を<br class="u-br">
						対象に行った調査（1万2093店が回答）
					</span>
				</p>
				<p class="p-top__text">
					1日に約
					<span class="p-top__price">13,000</span>
					円もの食品が廃棄として捨てられているんです。
				</p>
				<p class="p-top__text">
					まだまだ食べられる食品を捨てるのはもったいない！
				</p>
				<p class="p-top__text">
					そんなコンビニユーザーと、安く買いたい利用者の<br class="u-br">
					想いを「シェア」するサービスです。
				</p>
			</section>
		</div>
		
		<!-- 利用者のメリット（ベネフィット） -->
		<div class="p-top__bg">
			<section class="p-top__section">
				<FadeIn>
					<h2 class="p-top__title">Ha<span class="c-color__main">i</span>ki Share 3つのメリット!</h2>
				</FadeIn>
				
				<div class="p-top__container p-top__container--2line">
					<div class="p-top__ornament">利用者の方</div>
					<div class="c-card p-top__panel">
						<h3 class="p-top__panel__title">
							コンビニの食品を<br>安く購入できる！
						</h3>
						<img src="/storage/images/Discount-amico.png"
								 class="p-top__merit-img"
								 alt="">
					</div>
					<div class="c-card p-top__panel">
						<h3 class="p-top__panel__title">
							お近くのコンビニから<br>探すことができる！
						</h3>
						<img src="/storage/images/near.png"
								 class="p-top__merit-img"
								 alt="">
					</div>
					<div class="c-card p-top__panel">
						<h3 class="p-top__panel__title">
							たくさんの<br>カテゴリーから選べる！
						</h3>
						<img src="/storage/images/bunch.png"
								 class="p-top__merit-img"
								 alt="">
					</div>
				</div>
				
				<div class="p-top__container">
					<div class="p-top__ornament">コンビニユーザー</div>
					<div class="c-card p-top__panel">
						<h3 class="p-top__panel__title">
							今まで捨てていた食品<br>で売り上げUP！
						</h3>
						<img src="/storage/images/increase.png"
								 class="p-top__merit-img"
								 alt="">
					</div>
					<div class="c-card p-top__panel">
						<h3 class="p-top__panel__title">
							カンタン出品！<br>
							スマホでもパソコンでも！
						</h3>
						<img src="/storage/images/device.png"
								 class="p-top__merit-img"
								 alt="">
					</div>
					<div class="c-card p-top__panel">
						<h3 class="p-top__panel__title">
							賞味期限が近い、<br>過ぎている食品もOK!
						</h3>
						<img src="/storage/images/time.png"
								 class="p-top__merit-img"
								 alt="">
					</div>
				</div>
			</section>
		</div>
		
		<!-- 実績・事例 -->
		<div class="p-top__bg--alter">
			<section class="p-top__section">
				<FadeIn>
					<h2 class="p-top__title">実績・事例</h2>
				</FadeIn>
				
				<div class="p-top__recommend">
					<img src="/storage/images/japan.png"
							 class="p-top__recommend__img"
							 alt="">
					<p class="p-top__recommend__text">
						北は北海道、南は沖縄までたくさんの方に<br>
						ご利用いただいてます！
					</p>
				</div>
				<div class="p-top__recommend">
					<img src="/storage/images/money.png"
							 class="p-top__recommend__img"
							 alt="">
					<p class="p-top__recommend__text">
						Haiki Shareを利用して売り上げが1.3倍に！
					</p>
				</div>
				<div class="p-top__recommend">
					<img src="/storage/images/store.png"
							 class="p-top__recommend__img"
							 alt="">
					<p class="p-top__recommend__text">
						大手コンビニチェーン「Seven Store」にサービスを<br>
						ご利用いただいております！
					</p>
				</div>
				
			</section>
		</div>
		<!--<div class="p-top__bg&#45;&#45;alter">-->
		<!--	<section class="p-top__section">-->
		<!--		<FadeIn>-->
		<!--			<h2 class="p-top__title">実績・事例</h2>-->
		<!--		</FadeIn>-->
		<!--		<div class="p-top__container">-->
		<!--			<div class="c-card p-top__panel">-->
		<!--				<div class="p-top__panel__title">-->
		<!--					北は北海道、南は沖縄までたくさんの方にご利用いただいてます！-->
		<!--				</div>-->
		<!--				<img src="/storage/images/japan.png"-->
		<!--						 class="p-top__panel__image"-->
		<!--						 alt="">-->
		<!--			</div>-->
		<!--			<div class="c-card p-top__panel">-->
		<!--				LOREM IPSUM DOLOR SIT AMET, CONSECTETUR ADIPISICING ELIT, SED DO-->
		<!--				EIUSMOD TEMPOR INCIDIDUNT UT LABORE-->
		<!--			</div>-->
		<!--			<div class="c-card p-top__panel">-->
		<!--				LOREM IPSUM DOLOR SIT AMET, CONSECTETUR ADIPISICING ELIT, SED DO-->
		<!--			</div>-->
		<!--		</div>-->
		<!--	</section>-->
		<!--</div>-->
		
		<!-- 利用者の声 -->
		<div class="p-top__bg">
			<section class="p-top__section">
				<FadeIn>
					<h2 class="p-top__title">利用者の声</h2>
				</FadeIn>
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
				<FadeIn>
					<h2 class="p-top__title">こんな方におすすめ！</h2>
				</FadeIn>
				
				<FadeIn>
					<div class="p-top__recommend">
						<img src="/storage/images/Discount-amico.png"
								 class="p-top__recommend__img"
								 alt="">
						<p class="p-top__recommend__text">
							毎日食べる食品を<br class="u-br"> 安く買いたいお客様！
						</p>
					</div>
				</FadeIn>
				<!--<div class="p-top__recommend"-->
				<!--		 :class="{ fadeIn1000ms: visibleX }"-->
				<!--		 v-show="visibleX">-->
				<!--	<img src="/storage/images/Discount-amico.png"-->
				<!--			 class="p-top__recommend__img"-->
				<!--			 alt="">-->
				<!--	<p class="p-top__recommend__text">-->
				<!--		毎日食べる食品を安く買いたいお客様！-->
				<!--	</p>-->
				<!--</div>-->
				<FadeIn>
					<div class="p-top__recommend">
						<img src="/storage/images/increase.png"
								 class="p-top__recommend__img"
								 alt="">
						<p class="p-top__recommend__text">
							捨てていた食品で売り上げを<br>上げたいコンビニオーナー！
						</p>
					</div>
				</FadeIn>
				<!--<div class="p-top__recommend"-->
				<!--		 :class="{ fadeIn1500ms: visibleX }"-->
				<!--		 v-show="visibleX">-->
				<!--	<img src="/storage/images/increase.png"-->
				<!--			 class="p-top__recommend__img"-->
				<!--			 alt="">-->
				<!--	<p class="p-top__recommend__text">-->
				<!--		&lt;!&ndash;今まで捨てていた食品を売って<br>売り上げを上げたいコンビニオーナー！&ndash;&gt;-->
				<!--		捨てていた食品で売り上げを<br>上げたいコンビニオーナー！-->
				<!--	</p>-->
				<!--</div>-->
				<FadeIn>
					<div class="p-top__recommend">
						<img src="/storage/images/enviroment.png"
								 class="p-top__recommend__img"
								 alt="">
						<p class="p-top__recommend__text">
							美味しく食べてSDGs！ <br class="u-br">地球に貢献!
						</p>
					</div>
				</FadeIn>
				<!--<div class="p-top__recommend"-->
				<!--		 :class="{ fadeIn2000ms: visibleX }"-->
				<!--		 v-show="visibleX">-->
				<!--	<img src="/storage/images/enviroment.png"-->
				<!--			 class="p-top__recommend__img"-->
				<!--			 alt="">-->
				<!--	<p class="p-top__recommend__text">-->
				<!--		美味しく食べてSDGs！ <br class="u-br">地球に貢献!-->
				<!--	</p>-->
				<!--</div>-->
			</section>
		</div>
		
		<!-- カンタン！３ステップ （利用者）-->
		<div class="p-top__bg">
			<section class="p-top__section">
				<FadeIn>
					<h2 class="p-top__title">カンタン！３ステップ</h2>
				</FadeIn>
				<div class="p-top__container p-top__container--step p-top__container--2line">
					<div class="p-top__ornament p-top__ornament--step">利用者の方</div>
					<div class="p-top__panel p-top__panel--step">
						<div class="p-top__step">
							STEP
							<span class="p-top__step-number">1</span>
						</div>
						<h3 class="p-top__step-title">
							カンタン！<br class="p-top__br">会員登録
						</h3>
					</div>
					<div class="p-top__panel p-top__panel--step">
						<div class="p-top__step">
							STEP
							<span class="p-top__step-number">2</span>
						</div>
						<h3 class="p-top__step-title">
							気になる商品を<br class="p-top__br">購入する
						</h3>
					</div>
					<div class="p-top__panel p-top__panel--step">
						<div class="p-top__step">
							STEP
							<span class="p-top__step-number">3</span>
						</div>
						<h3 class="p-top__step-title">
							コンビニで<br class="p-top__br">商品を受け取る
						</h3>
					</div>
				</div>
				
				<div class="p-top__container p-top__container--step">
					<div class="p-top__ornament p-top__ornament--step">コンビニユーザー</div>
					<div class="p-top__panel p-top__panel--step">
						<div class="p-top__step">STEP<span class="p-top__step-number">1</span></div>
						<h3 class="p-top__step-title">
							カンタン！<br class="p-top__br">会員登録
						</h3>
					</div>
					<div class="p-top__panel p-top__panel--step">
						<div class="p-top__step">STEP<span class="p-top__step-number">2</span></div>
						<h3 class="p-top__step-title">
							写真を撮って<br class="p-top__br">商品を出品
						</h3>
					</div>
					<div class="p-top__panel p-top__panel--step">
						<div class="p-top__step">STEP<span class="p-top__step-number">3</span></div>
						<h3 class="p-top__step-title">
							購入された商品を<br class="p-top__br">用意する
						</h3>
					</div>
				</div>
			</section>
		</div>
		
		<!-- サービス登録欄 -->
		<div class="p-top__bg--alter">
			<section class="p-top__section">
				<h2 class="p-top__title">
					廃棄食品を無駄にしないサービス<br>
					「Ha<span class="c-color__main">i</span>ki Share」を早速利用してみる！
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
import { OK }   from "../util";
import FadeIn   from "./FadeIn";
import Review   from "./review/Review";

export default {
	name: "Top",
	components: {
		FadeIn,
		Review
	},
	data() {
		return {
			loading: false,
			reviews: [],         //レビューをランダムで3件
			buttonActive: false, //TOPボタンを表示する
			scroll: 0,           //scroll
			visibleX: false
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
		handleScrollX() {
			if(!this.visibleX) {
				var top = this.$el.getBoundingClientRect().top; //ブラウザの表示領域の左上を(0, 0)として、そこから要素の上端までの相対位置の値が返ってくる
				this.visibleX = top < window.innerHeight + 50; //ブラウザのウィンドウの高さに100pxを足した値よりtopの値が小さくなったときにthis.visibleにtrueを入れる
			}
		}
	},
	created() {
		window.addEventListener("scroll", this.scrollWindow);
		window.addEventListener("scroll", this.handleScrollX);
	},
	destroyed() {
		window.removeEventListener("scroll", this.handleScrollX);
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
.fadeIn1000ms {
	animation: fadeIn1000ms 1.5s;
	/*animation-name: fadeIn1000ms; !* アニメーション名 *!*/
	/*animation-duration: 1500ms; !* 開始から終了までの時間 *!*/
	/*animation-fill-mode: forwards; !* 再生前、再生後に適用するスタイル *!*/
}
@keyframes fadeIn1000ms {
	0% {
		opacity: 0;
		transform: translatex(-300px);
	}
	100% {
		opacity: 1;
		transform: translateX(0);
	}
}

.fadeIn1500ms {
	animation: fadeIn1500ms 1.5s;
	/*animation-name: fadeIn1500ms;*/
	/*animation-duration: 1750ms;*/
	/*animation-fill-mode: forwards;*/
}
@keyframes fadeIn1500ms {
	0% {
		opacity: 0;
		transform: translatex(-300px);
	}
	100% {
		opacity: 1;
		transform: translateX(0);
	}
}

.fadeIn2000ms {
	animation: fadeIn2000ms 1.5s;
	/*animation-name: fadeIn2000ms;*/
	/*animation-duration: 2000ms; !* 開始から終了までの時間 *!*/
	/*animation-fill-mode: forwards;*/
}
@keyframes fadeIn2000ms {
	0% {
		opacity: 0;
		transform: translatex(-300px);
	}
	100% {
		opacity: 1;
		transform: translateX(0);
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
