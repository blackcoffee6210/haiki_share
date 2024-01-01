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
import MyPage from "./components/MyPage";
import ProductDetail from "./components/ProductDetail";
import PassResetEmail from "./components/auth/passwords/PassResetEmail";
import PassResetForm from "./components/auth/passwords/PassResetForm";


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
      //ログイン状態でアクセスしたらトップページを表示する
      if(store.getters['auth/check']) {
        next({ name: 'index' });
      } else {
        next();
      }
    }
  },
  {
    //パスワードリセットメール送信
    path: '/password/reset',
    name: 'password.email',
    component: PassResetEmail,
    beforeEnter(to, from, next) {
      //ログイン状態でアクセスしたらトップページを表示する
      if(store.getters['auth/check']) {
        next({ name: 'index' });
      } else {
        next();
      }
    }
  },
  {
    //パスワードリセットリンク送信
    path: '/password/reset/:token/:email',
    name: 'password.reset',
    component: PassResetForm,
    beforeEnter(to, from, next) {
      //ログイン状態でアクセスしたらトップページを表示する
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
    component: Index,
    props: route => {
      const page = route.query.page;
      return { page: /^[1-9][0-9]*$/.test(page) ? page * 1 : 1 }
    }
  },
  {
    //商品詳細
    path: '/products/:id',
    name: 'product.detail',
    component: ProductDetail,
    props: true //ProductDetail.vueに:idの値がpropsとして渡される
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
    //マイページ
    path: '/users/:id/my-page',
    name: 'user.mypage',
    component: MyPage,
    beforeEnter(to, from, next) {
      //ログイン状態でページにアクセスがあったらマイページに移動させる
      if(store.getters['auth/check']) {
        next();
      //ログインしていなければログイン画面に移動させる
      }else {
        next({name: 'login'});
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
