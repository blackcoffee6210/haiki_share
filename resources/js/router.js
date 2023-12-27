import Vue from 'vue';
import VueRouter from 'vue-router';
// import store     from './store';

//コンポーネントをインポートする
import Register from "./components/Register";
import Login    from "./components/Login";


//VueRouterプラグインを利用する
//これによって<router-view>コンポーネントなどを使うことができる
Vue.use(VueRouter);


const routes = [
  {
    //会員登録
    path: '/register',
    name: 'register',
    component: Register
  },
  {
    //ログイン
    path: '/login',
    name: 'login',
    component: Login
  }
];

//VueRouterインスタンスを作成する
const router = new VueRouter({
  mode: 'history',
  scrollBehavior() {
    return { x: 0, y: 0 }
  },
  routes
});

//VueRouterインスタンスをエクスポートする
//app.js でインポートするため
export default router;
