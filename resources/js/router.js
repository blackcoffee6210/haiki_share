import Vue       from 'vue';
import VueRouter from 'vue-router';
import store     from './store';

//コンポーネントをインポートする
import Register  from "./components/auth/Register";
import Login     from "./components/auth/Login";
import Index     from "./components/Index";
import Agreement from "./components/footer/Agreement";
import Policy    from "./components/footer/Policy";
import Tokutei   from "./components/footer/Tokutei";
import NotFound  from "./components/errors/NotFound";
import System    from "./components/errors/System";
import RegisterProduct from "./components/RegisterProduct";


//VueRouterプラグインを利用する
//これによって<router-view>コンポーネントなどを使うことができる
Vue.use(VueRouter);

//パスとコンポーネントのマッピング
const routes = [
  {
    //会員登録
    path: '/register',
    name: 'register',
    component: Register,
    beforeEnter(to, from, next) {
      //ログイン済みでアクセスしたらトップページを表示する
      if(store.getters['auth/check']) {
        next({ name: 'index' });
      } else {
        next();
      }
    }
  },
  {
    //ログイン
    path: '/login',
    name: 'login',
    component: Login,
    beforeEnter(to, from, next) {
      //ログイン済みでアクセスしたらトップページを表示する
      if(store.getters['auth/check']) {
        next({ name: 'index' });
      } else {
        next();
      }
    }
  },
  {
    //インデックス(商品一覧)画面
    path: '/products',
    name: 'index',
    component: Index
  },
  {
    //商品登録
    path: '/products/register',
    name: 'product.register',
    component: RegisterProduct,
    beforeEnter(to, from, next) {
      //お店の人以外がアクセスしたらトップページを表示する
      if(!store.getters['auth/isShopUser']) {
        next({ name: 'index' });
      } else {
        next();
      }
    }
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
  },
  {
    //404画面
    path: '*',
    name: 'notFound',
    component: NotFound
  },
  {
    //500エラー
    path: '/500',
    name: 'systemError',
    component: System
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
