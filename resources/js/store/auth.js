//util.jsに定義したステータスコードをインポートする
import { CREATED, OK, UNPROCESSABLE_ENTITY, TOO_MANY_REQUEST } from "../util";

//stateはデータの入れ物
const state = {
  //ログインユーザーを保持する
  user: null,
  //API呼び出しが成功したか失敗したかを表すステート
  //これを参照して後続の処理を行うかどうか判別する
  //値にはtrueまたはfalseが入る
  apiStatus: null,
  //エラーメッセージを入れるステート
  loginErrorMessages: null,
  registerErrorMessages: null,
}

//gettersはstateの内容から算出される値
const getters = {
  //ログインチェックに使用する（確実に真偽値を返すために二重否定にしている）
  check: state => !! state.user,
  //ログインユーザーの名前を取得
  //仮にuserがnullの場合に呼ばれてもエラーが発生しないように空文字を返すようにしている
  username: state => state.user ? state.user.name : '',
  //ログインユーザーのIDを取得
  userId: state => state.user ? state.user.id : '',
}

//mutationsはstateを更新するためのメソッド
//コンポーネントはstateを直接更新できないので、mutationsを介してstateを更新する(同期処理)
//第一引数は必ず「state」
//第二引数には実際に渡したい値を書く
const mutations = {
  //userステートの値を更新する
  setUser(state, user) {
    state.user = user;
  },
  //apiStatusステートの値を更新する
  setApiStatus(state, status) {
    state.apiStatus = status;
  },
  //loginErrorMessagesステートの値を更新する
  setLoginErrorMessages(state, messages) {
    state.loginErrorMessages = messages;
  },
  //registerErrorMessagesステートの値を更新する
  setRegisterErrorMessages(state, messages) {
    state.registerErrorMessages = messages;
  }
}

//actionsはAPI通信などの非同期処理を行ったあとに、mutationsを呼び出してstateを更新する
//データの流れ↓
//「アクション→コミットでミューテーション呼び出し→ ステート更新」という流れ
const actions = {
  //会員登録APIを呼び出すアクション
  //第一引数にはcontextオブジェクトを渡す（ミューテーションを呼び出すためのcommitメソッドなどが入っている）
  async register(context, data) {
    //apiStatusステートに最初はnullをセットする
    context.commit('setApiStatus', null);
    //会員登録APIを呼び出し、返却データを定数responseに渡す
    const response = await axios.post('/api/register', data);

    //responseステータスがCREATED(201)なら後続の処理を行う
    if(response.status === CREATED) {
      //通信成功なので、apiStatusにtrueをセットする
      context.commit('setApiStatus', true);
      //setUserミューテーションを実行してuserステートにデータをセット
      context.commit('setUser', response.data);
      //下に書いた処理をfalseを使って抜ける(成功なので)
      return false;
    }
    //通信失敗なら、apiStatusにfalseをセット
    context.commit('setApiStatus', false);
    //responseステータスがUNPROCESSABLE（バリデーションエラー）なら後続の処理を行う
    if(response.status === UNPROCESSABLE_ENTITY) {
      //registerErrorMessagesにデータをセットする
      context.commit('setRegisterErrorMessages', response.data.errors);
    }
    else {
      //あるストアモジュールから別のモジュールのミューテーションをcommitする場合
      //第三引数に { root: true } を追加する
      context.commit('error/setCode', response.status, { root: true });
    }
  },
  //ログイン
  async login(context, data) {
    //apiStatusステートに最初はnullをセットする
    context.commit('setApiStatus', null);
    //ログインAPIを呼び出し、返却データを定数responseに渡す
    const response = await axios.post('/api/login', data);

    //responseステータスがOK(200)なら後続の処理を行う
    if(response.status === OK) {
      //通信成功(200 OK)なので、apiStatusステートにtrueをセット
      context.commit('setApiStatus', true);
      //userステートにresponseデータをセット
      context.commit('setUser', response.data);
      return false;
    }
    //通信失敗なら、apiStatusにfalseをセット
    context.commit('setApiStatus', false);
    //responseステータスがUNPROCESSABLE_ENTITY(バリデーションエラー)なら後続の処理を行う
    if(response.status === UNPROCESSABLE_ENTITY) {
      context.commit('setLoginErrorMessages', response.data.errors);
    }
    //入力エラーの回数が5回を超えたらエラーメッセージをセット
    else if(response.status === TOO_MANY_REQUEST) {
      context.commit('setLoginErrorMessages', response.data.errors);
    }
    else {
      //あるストアモジュールから別のモジュールのミューテーションをcommitする場合
      //第三引数に { root: true }を追加する
      context.commit('error/setCode', response.status, { root: true });
    }
  },
  //ログアウト
  async logout(context) {
    //apiStatusステートに最初はnullをセットする
    context.commit('setApiStatus', null);
    const response = await axios.post('/api/logout');
    //responseステータスがOK(200)なら後続の処理を行う
    if(response.status === OK) {
      //通信成功なので、apiStatusステートにtrueをセット
      context.commit('setApiStatus', true);
      //userステートにnullをセットして空にする(userデータを消す)
      context.commit('setUser', null);
      return false;
    }
    //apiStatusにfalseをセット(OKじゃないので)
    context.commit('setApiStatus', false);
    //あるストアモジュールから別のモジュールのミューテーションをcommitする場合
    //第三引数に { root: true }を追加する
    context.commit('error/setCode', response.status, { root: true });
  },
  //ログインユーザーチェック
  async currentUser(context) {
    //apiStatusステートに最初はnullをセットする
    context.commit('setApiStatus', null);
    //ユーザー情報をAPIから取得する
    const response = await axios.get('/api/user');
    //データを変数にセット。ログインしていないときはuserステートの初期値をnullに揃えておく
    const user = response.data || null;
    //responseステータスがOK(200)なら後続の処理を行う
    if(response.status === OK) {
      //通信成功なので、apiStatusステートにtrueをセット
      context.commit('setApiStatus', true);
      //ユーザーデータをセット。データがない(ログインしていない)場合はnullが入る
      context.commit('setUser', user);
      return false;
    }
    context.commit('setApiStatus', false);
    context.commit('error/setCode', response.status, { root: true });
  },
}


//外部ファイルで読み込めるようにエクスポートする
export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}
