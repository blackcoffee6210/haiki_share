import './bootstrap';
import Vue              from 'vue';                //Vueをインポート
import store            from './store';            //グローバルコンポーネント
import router           from './router';           //ルーティングの定義をインポートする
import App              from './App.vue';          //ルートコンポーネントをインポートする
import moment           from 'moment';             //時刻操作や表現をしたいとき使う
import VueScrollTo      from 'vue-scrollto';       //スクロール
import VueSocialSharing from 'vue-social-sharing'; //twitterシェア
import StarRating       from 'vue-star-rating';    //レビューの星
import ReadMore         from 'vue-read-more';      //「もっと見る」表示

import VueCarousel      from 'vue-carousel';
Vue.use(VueCarousel);

//======================
//FontAwesome
//======================
import { library }         from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { fas }             from '@fortawesome/free-solid-svg-icons';
import { fab }             from '@fortawesome/free-brands-svg-icons';
import { far }             from '@fortawesome/free-regular-svg-icons';

library.add(fas, fab, far); // FontAwesome Setup
Vue.component('font-awesome-icon', FontAwesomeIcon);
Vue.config.productionTip = false;

Vue.use(VueScrollTo);      //scroll
Vue.use(VueSocialSharing); //twitterシェア
Vue.use(StarRating);       //レビューの星
Vue.use(ReadMore);         //もっと見る

//==============================================
// フィルター
//==============================================
Vue.filter('numberFormat', function (price) { //金額にカンマ(,)と¥マークをつける
  return price.toLocaleString('ja-JP', { style: 'currency', currency: 'JPY'});
});
Vue.filter('moment', function(date) { //日付を「⚪︎日前」の書式で返す
  moment.locale('ja');                   //日本語化する
  return moment(date).fromNow();
})
Vue.filter('momentDate', function (date) { //日付を「年月日」の書式で返す
  moment.locale('ja');                        //日本語化する
  return moment(date).format('YYYY-MM-DD');
})
Vue.filter('momentExpire', function(date) { //日付を「残り⚪︎日」の書式で返す
  moment.locale('ja');
  var expire = moment(date);
  var today  = moment().format('YYYY-MM-DD');
  return expire.diff(today, 'days');
})

const createApp = async () => { //アプリ起動時、Vueインスタンス生成前にauth/currentUserアクションを呼び出す(リロードしてもログイン状態を保持するため)
  await store.dispatch('auth/currentUser'); //currentUserアクションの非同期処理が終わってからVueインスタンスを生成する
  new Vue({
    el: '#app',
    store,               //ストアを読み込む
    router,              //ルーティングの定義を読み込む
    components: { App }, //ルートコンポーネントの使用を宣言する
    template: '<App />'  //ルートコンポーネントを描画する
  });
};
createApp();
