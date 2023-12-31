import './bootstrap';

import Vue              from 'vue';
import store            from './store'; //グローバルコンポーネント
import router           from './router'; //ルーティングの定義をインポートする
import App              from './App.vue'; //ルートコンポーネントをインポートする
import moment           from 'moment';
import VueScrollTo      from 'vue-scrollto'; //スクロール
import VueSocialSharing from 'vue-social-sharing'; //twitterシェア
import StarRating       from 'vue-star-rating'; //レビューの星
import ReadMore         from 'vue-read-more'; //「もっと見る」表示

//======================
//FontAwesome
//======================
import { library }         from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { fas }             from '@fortawesome/free-solid-svg-icons';
import { fab }             from '@fortawesome/free-brands-svg-icons';
import { far }             from '@fortawesome/free-regular-svg-icons';
// FontAwesome Setup
// library.add(faHeart, faBookmark, faComment);
library.add(fas, fab, far);
Vue.component('font-awesome-icon', FontAwesomeIcon);
Vue.config.productionTip = false;

Vue.use(VueScrollTo); //scroll
Vue.use(VueSocialSharing); //twitterシェア
Vue.use(StarRating); //レビューの星
Vue.use(ReadMore); //もっと見る

//==============================================
// フィルター
//==============================================
//金額にカンマ(,)と¥マークをつける
Vue.filter('numberFormat', function (price) {
  return price.toLocaleString('ja-JP', { style: 'currency', currency: 'JPY'});
});
//日付を「⚪︎日前」の書式で返す
Vue.filter('moment', function(date) {
  moment.locale('ja');
  return moment(date).fromNow();
})


//アプリ起動時、Vueインスタンス生成前にauth/currentUserアクションを呼び出す
//画面をリロードしてもログイン状態を保持するための処理
const createApp = async () => {
  await store.dispatch('auth/currentUser');
  //currentUserアクションの非同期処理が終わってからVueインスタンスを生成する
  new Vue({
    el: '#app',
    store,
    router, //ルーティングの定義を読み込む
    components: { App }, //ルートコンポーネントの使用を宣言する
    template: '<App />' //ルートコンポーネントを描画する
  });
};
createApp();
