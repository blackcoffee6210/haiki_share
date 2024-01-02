import Vue       from 'vue';
import VueRouter from 'vue-router';
import store     from './store';

//コンポーネントをインポートする
import PassResetEmail  from "./components/auth/passwords/PassResetEmail";
import PassResetForm   from "./components/auth/passwords/PassResetForm";
import Register        from "./components/auth/Register";
import Login           from "./components/auth/Login";
import NotFound        from "./components/errors/NotFound";
import System          from "./components/errors/System";
import Agreement       from "./components/footer/Agreement";
import Policy          from "./components/footer/Policy";
import Tokutei         from "./components/footer/Tokutei";
import Index           from "./components/product/Index";
import ProductDetail   from "./components/product/ProductDetail";
import RegisterProduct from "./components/product/RegisterProduct";
import EditProduct     from "./components/product/EditProduct";
import EditPassword    from "./components/user/EditPassword";
import EditProfile     from "./components/user/EditProfile";
import Liked           from "./components/user/Liked";
import MyPage          from "./components/user/MyPage";
import Posted          from "./components/user/Posted";
import Purchased       from "./components/user/Purchased";

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
      //ログインユーザーかつお店の人がアクセスしたらトップページを表示する
      if(store.getters['auth/check'] && store.getters['auth/isShopUser']) {
        next();
      } else {
        next({ name: 'index' });
      }
    }
  },
  {
    //商品編集
    path: '/products/:id/edit',
    name: 'product.edit',
    component: EditProduct,
    props: true,
    beforeEnter(to, from, next) {
      //ログイン状態かつコンビニユーザーがページにアクセスした場合(true)、そのまま移動させる
      if(store.getters['auth/check'] && store.getters['auth/isShopUser']) {
        next();
        //ログインしていなければログイン画面に移動させる
      } else {
        next({name: 'login'});
      }
    }
  },
  {
    //マイページ
    path: '/users/:id/my-page',
    name: 'user.mypage',
    component: MyPage,
    props: true,
    beforeEnter(to, from, next) {
      //ログイン状態でページにアクセスがあったらそのまま移動させる
      if(store.getters['auth/check']) {
        next();
      //ログインしていなければログイン画面に移動させる
      }else {
        next({name: 'login'});
      }
    }
  },
  {
    //プロフィール編集
    path: '/users/:id/edit-profile',
    name: 'user.editProfile',
    component: EditProfile,
    props: true,
    beforeEnter(to, from ,next) {
      //ログイン状態でページにアクセスがあった場合(true)、そのまま移動させる
      if(store.getters['auth/check']) {
        next();
        //ログインしていなければログイン画面に移動させる
      } else {
        next({name: 'login'});
      }
    }
  },
  {
    //パスワード編集
    path: '/users/:id/edit-password',
    name: 'user.editPassword',
    component: EditPassword,
    props: true,
    beforeEnter(to, from, next) {
      //ログイン状態でページにアクセスがあったらそのまま移動させる
      if(store.getters['auth/check']) {
        next();
        //ログインしていなければログイン画面に移動させる
      }else {
        next({name: 'login'});
      }
    }
  },
  {
    //出品した商品一覧
    path: '/users/:id/posted',
    name: 'user.posted',
    component: Posted,
    props: true,
    beforeEnter(to, from ,next) {
      //ログイン状態かつコンビニユーザーがページにアクセスした場合(true)、そのまま移動させる
      if(store.getters['auth/check'] && store.getters['auth/isShopUser']) {
        next();
      //ログインしていなければログイン画面に移動させる
      } else {
        next({name: 'login'});
      }
    }
  },
  {
    //購入した商品一覧
    path: '/users/:id/purchased',
    name: 'user.purchased',
    component: Purchased,
    props: true,
    beforeEnter(to, from, next) {
      //todo: ユーザーによって処理を分ける
      if(store.getters['auth/check']) {
        next();
      }else {
        next({name: 'login'});
      }
    }
  },
  {
    //いいねした商品一覧
    path: '/user/:id/liked',
    name: 'user.liked',
    component: Liked,
    props: true,
    beforeEnter(to, from, next) {
      //todo: ユーザーによって処理を分ける
      if(store.getters['auth/check']) {
        next();
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
