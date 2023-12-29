import './bootstrap';
import Vue from 'vue';

import store      from './store'; //グローバルコンポーネント
import router     from './router'; //ルーティングの定義をインポートする
import App        from './App.vue'; //ルートコンポーネントをインポートする
import moment     from 'moment';

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
