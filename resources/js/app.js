/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import Vue from 'vue';

import store      from './store'; //グローバルコンポーネント
import router     from './router'; //ルーティングの定義をインポートする
import App        from './App.vue'; //ルートコンポーネントをインポートする
import moment     from 'moment';

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

// Vue.component('example-component', require('./components/ExampleComponent.vue').default);

//アプリ起動時、Vueインスタンス生成前にauth/currentUserアクションを呼び出す
//画面をリロードしてもログイン状態を保持するための処理
const createApp = async() => {
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
