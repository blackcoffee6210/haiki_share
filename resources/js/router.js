import Vue       from 'vue';
import VueRouter from 'vue-router';
import store     from './store';

//コンポーネントをインポートする
import Register  from "./components/auth/Register";
import Login     from "./components/auth/Login";
import Index     from "./components/Index";
import NotFound  from "./components/errors/NotFound";
import Agreement from "./components/footer/Agreement";
import Policy    from "./components/footer/Policy";
import Tokutei   from "./components/footer/Tokutei";


//VueRouterプラグインを利用する
//これによって<router-view>コンポーネントなどを使うことができる
Vue.use(VueRouter);

//パスとコンポーネントのマッピング
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
  mode: 'history', // #(ハッシュ)を消して本来のURLの形にする
  scrollBehavior() {
    return { x: 0, y: 0 }
  },
  routes
});

//VueRouterインスタンスをエクスポートする
//app.js でインポートするため
export default router;
