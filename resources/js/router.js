import Vue       from 'vue';
import VueRouter from 'vue-router';
// import store     from './store';

//コンポーネントをインポートする
import Register  from "./components/Register";
import Login     from "./components/Login";
import Index     from "./components/Index";
import NotFound  from "./components/NotFound";
import Agreement from "./components/Agreement";
import Policy    from "./components/Policy";
import Tokutei   from "./components/Tokutei";


//VueRouterプラグインを利用する
//これによって<router-view>コンポーネントなどを使うことができる
Vue.use(VueRouter);


const routes = [
  {
    //404画面
    path: '*',
    name: 'notFound',
    component: NotFound
  },
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
  },
  {
    //インデックス(商品一覧)画面
    path: '/products',
    name: 'index',
    component: Index
  },
  {
    //利用規約
    path: '/agreement',
    name: 'agreement',
    component: Agreement
  },
  {
    //プライバシーポリシー
    path: '/policy',
    name: 'policy',
    component: Policy
  },
  {
    //特定商取引法
    path: '/tokutei',
    name: 'tokutei',
    component: Tokutei
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
