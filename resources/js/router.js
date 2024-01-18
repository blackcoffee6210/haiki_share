import Vue             from 'vue';
import VueRouter       from 'vue-router';
import store           from './store';
import PassResetEmail  from "./components/auth/passwords/PassResetEmail"; // ↓コンポーネントをインポートする
import PassResetForm   from "./components/auth/passwords/PassResetForm";
import Register        from "./components/auth/Register";
import Login           from "./components/auth/Login";
import NotFound        from "./components/errors/NotFound";
import System          from "./components/errors/System";
import Agreement       from "./components/footer/Agreement";
import Policy          from "./components/footer/Policy";
import Tokutei         from "./components/footer/Tokutei";
import EditProduct     from "./components/product/EditProduct";
import Index           from "./components/product/Index";
import ProductDetail   from "./components/product/ProductDetail";
import RegisterProduct from "./components/product/RegisterProduct";
import EditReview      from "./components/review/EditReview";
import RegisterReview  from "./components/review/RegisterReview";
import Canceled        from "./components/user/Canceled";
import EditPassword    from "./components/user/EditPassword";
import EditProfile     from "./components/user/EditProfile";
import Liked           from "./components/user/Liked";
import MyPage          from "./components/user/MyPage";
import Posted          from "./components/user/Posted";
import Purchased       from "./components/user/Purchased";
import Reviewed        from "./components/user/Reviewed";                 // ↑ここまで


Vue.use(VueRouter); //VueRouterプラグインを利用する(<router-view>コンポーネントなどを使うことができる)

const routes = [ //パスとコンポーネントのマッピング
  {
    path: '/register', //会員登録
    name: 'register',
    component: Register,
    beforeEnter(to, from, next) { //ログイン状態でアクセスしたらトップページを表示する
      store.getters['auth/check'] ? next({ name: 'index' }) : next();
    }
  },
  {
    path: '/login', //ログイン
    name: 'login',
    component: Login,
    beforeEnter(to, from, next) { //ログイン状態でアクセスしたらトップページを表示する
      store.getters['auth/check'] ? next({ name: 'index' }) : next();
    }
  },
  {
    path: '/password/reset', //パスワードリセットメール送信
    name: 'password.email',
    component: PassResetEmail,
    beforeEnter(to, from, next) { //ログイン状態でアクセスしたらトップページを表示する
      store.getters['auth/check'] ? next({ name: 'index' }) : next();
    }
  },
  {
    path: '/password/reset/:token/:email', //パスワードリセットリンク送信
    name: 'password.reset',
    component: PassResetForm,
    beforeEnter(to, from, next) { //ログイン状態でアクセスしたらトップページを表示する
      store.getters['auth/check'] ? next({ name: 'index' }) : next();
    }
  },
  {
    path: '/products', //インデックス(商品一覧)画面
    name: 'index',
    component: Index,
    props: route => {
      const page = route.query.page;
      return { page: /^[1-9][0-9]*$/.test(page) ? page * 1 : 1 }
    }
  },
  {
    path: '/products/:id', //商品詳細
    name: 'product.detail',
    component: ProductDetail,
    props: true //ProductDetail.vueに:idの値がpropsとして渡される
  },
  {
    path: '/products/register', //商品登録
    name: 'product.register',
    component: RegisterProduct,
    beforeEnter(to, from, next) { //ログインしている、かつコンビニユーザーの場合はページを表示、それ以外は商品一覧画面に遷移する
      (store.getters['auth/check'] && store.getters['auth/isShopUser']) ? next() : next({name: 'index'});
    }
  },
  {
    path: '/products/:id/edit', //商品編集
    name: 'product.edit',
    component: EditProduct,
    props: true,
    beforeEnter(to, from, next) { //ログイン状態かつコンビニユーザーがページにアクセスした場合(true)、そのまま移動させる
      (store.getters['auth/check'] && store.getters['auth/isShopUser']) ? next() : next({name: 'index'});
    }
  },
  {
    path: '/reviews/:id/register', //レビュー投稿(利用者)
    name: 'review.register',
    component: RegisterReview,
    props: true,
    beforeEnter(to, from, next) { //ログイン状態かつ利用者ユーザーがページにアクセスした場合(true)、そのまま移動させる
      (store.getters['auth/check'] && !store.getters['auth/isShopUser']) ? next() : next({name: 'index'} );
    }
  },
  {
    path: '/reviews/:s_id/:r_id/edit', //レビュー編集(利用者)
    name: 'review.edit',
    component: EditReview,
    props: true,
    beforeEnter(to, from, next) { //ログイン状態かつ利用者ユーザーがページにアクセスした場合(true)、そのまま移動させる
      (store.getters['auth/check'] && !store.getters['auth/isShopUser']) ? next() : next({name: 'index'} );
    }
  },
  {
    path: '/users/:id/my-page', //マイページ
    name: 'user.mypage',
    component: MyPage,
    props: true,
    beforeEnter(to, from, next) { //ログイン状態でページにアクセスがあったらそのまま移動させる
      (store.getters['auth/check']) ? next() : next({name: 'login'});
    }
  },
  {
    path: '/users/:id/edit-profile', //プロフィール編集
    name: 'user.editProfile',
    component: EditProfile,
    props: true,
    beforeEnter(to, from ,next) { //ログイン状態でページにアクセスがあった場合(true)、そのまま移動させる
      (store.getters['auth/check']) ? next() : next({name: 'login'});
    }
  },
  {
    path: '/users/:id/edit-password', //パスワード編集
    name: 'user.editPassword',
    component: EditPassword,
    props: true,
    beforeEnter(to, from ,next) { //ログイン状態でページにアクセスがあった場合(true)、そのまま移動させる
      (store.getters['auth/check']) ? next() : next({name: 'login'});
    }
  },
  {
    path: '/users/:id/posted', //出品した商品一覧（コンビニユーザー）
    name: 'user.posted',
    component: Posted,
    props: true,
    beforeEnter(to, from ,next) { //ログイン状態かつコンビニユーザーがページにアクセスした場合(true)、そのまま移動させる
      (store.getters['auth/check'] && store.getters['auth/isShopUser']) ? next() : next({name: 'index'});
    }
  },
  {
    path: '/user/:id/liked', //いいねした商品一覧(利用者)
    name: 'user.liked',
    component: Liked,
    props: true,
    beforeEnter(to, from ,next) { //ログイン状態かつ利用者ユーザーがページにアクセスした場合(true)、そのまま移動させる
      (store.getters['auth/check'] && !store.getters['auth/isShopUser']) ? next() : next({name: 'index'});
    }
  },
  {
    path: '/users/:id/purchased', //購入した商品一覧(利用者)
    name: 'user.purchased',
    component: Purchased,
    props: true,
    beforeEnter(to, from ,next) { //ログイン状態でページにアクセスした場合(true)、そのまま移動させる
      (store.getters['auth/check']) ? next() : next({name: 'index'});
    }
  },
  {
    path: '/users/:id/canceled', //キャンセルした（された）商品一覧
    name: 'user.canceled',
    component: Canceled,
    props: true,
    beforeEnter(to, from ,next) { //ログイン状態でページにアクセスした場合(true)、そのまま移動させる
      (store.getters['auth/check']) ? next() : next({name: 'index'});
    }
  },
  {
    path: '/users/:id/reviewed', //レビューした(された)ユーザー一覧(利用者)
    name: 'user.reviewed',
    component: Reviewed,
    props: true,
    beforeEnter(to, from ,next) { //ログイン状態でページにアクセスした場合(true)、そのまま移動させる
      (store.getters['auth/check']) ? next() : next({name: 'index'});
    }
  },
  {
    path: '/agreement', //利用規約
    name: 'agreement',
    component: Agreement
  },
  {
    path: '/policy', //プライバシーポリシー
    name: 'policy',
    component: Policy
  },
  {
    path: '/tokutei', //特定商取引法
    name: 'tokutei',
    component: Tokutei
  },
  {
    path: '*', //404画面
    name: 'notFound',
    component: NotFound
  },
  {
    path: '/500', //500エラー
    name: 'systemError',
    component: System
  }
];

const router = new VueRouter({ //VueRouterインスタンスを作成する
  mode: 'history',                    // #(ハッシュ)を消して本来のURLの形にする
  scrollBehavior() {
    return { x: 0, y: 0 }
  },
  routes
});

export default router; //VueRouterインスタンスをエクスポートする(app.js でインポートするため)
