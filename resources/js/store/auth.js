import { CREATED, OK, UNPROCESSABLE_ENTITY, TOO_MANY_REQUEST } from "../util"; //util.jsに定義したステータスコードをインポート

const state = {         //stateはデータの入れ物
  user:      null,      //ログインユーザーを保持する
  apiStatus: null,      //API呼び出しが成功したか失敗したかを表すステート(これを参照して後続の処理を行うかどうか判別する)
  loginErrorMessages: { //ログインのエラーメッセージを入れるステート
    email:    null,
    password: null
  },
  registerErrorMessages: { //ユーザー登録のエラーメッセージを入れるステート
    name:                  null,
    branch:                null,
    prefecture_id:         null,
    address:               null,
    email:                 null,
    password:              null,
    password_confirmation: null
  }
}

const getters = { //gettersはstateの内容から算出される値
  check:      state => !! state.user,                           //ログインチェックに使用する（確実に真偽値を返すために二重否定にしている）
  username:   state => state.user ? state.user.name : '',       //ログインユーザー名を取得(nullの場合に呼ばれてもエラーが発生しないように空文字を返す)
  userId:     state => state.user ? state.user.id : '',         //ログインユーザーのIDを取得
  isShopUser: state => !!(state.user && state.user.group === 2) //ログインユーザーがお店の人かどうか
}

const mutations = {      //mutationsはstateを更新するためのメソッド(mutationsを介してstateを更新する(同期処理) )
  setUser(state, user) { //第一引数は必ず「state」。第二引数には実際に渡したい値を書く
    state.user = user;   //userステートの値を更新する
  },
  setApiStatus(state, status) { //apiStatusステートの値を更新する
    state.apiStatus = status;
  },
  setLoginErrorMessages(state, messages) { //loginErrorMessagesステートの値を更新する
    state.loginErrorMessages = messages;
  },
  setRegisterErrorMessages(state, messages) { //registerErrorMessagesステートの値を更新する
    state.registerErrorMessages = messages;
  }
}

const actions = {                         //actionsはAPI通信などの非同期処理を行ったあとに、mutationsを呼び出してstateを更新する
  async register(context, data) {         //会員登録APIを呼び出すアクション
    context.commit('setApiStatus', null); //apiStatusステートに最初はnullをセットする

    try { //例外処理
      const response = await axios.post('/api/register', data); //会員登録APIを呼び出し、返却データを定数responseに渡す

      if(response.status === CREATED) {           //responseステータスがCREATED(201)なら後続の処理を行う
        context.commit('setApiStatus', true);     //通信成功なので、apiStatusにtrueをセットする
        context.commit('setUser', response.data); //setUserミューテーションを実行してuserステートにデータをセット
        return true;                              //下に書いた処理をfalseを使って抜ける(成功なので)
      }
      context.commit('setApiStatus', null); //apiStatusステートにnullをセットする

      if(response.status === UNPROCESSABLE_ENTITY) {
        context.commit('setRegisterErrorMessages', response.data.errors);
      }else {
        context.commit('error/setCode', status, { root: true });
      }

    }catch (error) {


    }
  },
  async login(context, data) { //ログインアクション
    context.commit('setApiStatus', null); //apiStatusステートに最初はnullをセットする

    try { //例外処理
      const response = await axios.post('/api/login', data); //ログインAPIを呼び出し、返却データを定数responseに渡す
      if(response.status === OK) {                //responseステータスがOK(200)なら後続の処理を行う
        context.commit('setApiStatus', true);     //通信成功(200 OK)なので、apiStatusステートにtrueをセット
        context.commit('setUser', response.data); //userステートにresponseデータをセット
        return true; //成功した場合はtrueを返す
      }
      context.commit('setApiStatus', null); //apiStatusステートにnullをセットする

      if(response.status === UNPROCESSABLE_ENTITY) {
        context.commit('setLoginErrorMessages', response.data.errors);
      }else if(response.status === TOO_MANY_REQUEST) {
        context.commit('setLoginErrorMessages', response.data.errors);
      }else {
        context.commit('error/setCode', status, { root: true });
      }

    }catch(error) {

    }
  },
  async logout(context) { //ログアウト
    context.commit('setApiStatus', null); //apiStatusステートに最初はnullをセットする

    try {
      const response = await axios.post('/api/logout');

      if(response.status === OK) {            //responseステータスがOK(200)なら後続の処理を行う
        context.commit('setApiStatus', true); //通信成功なので、apiStatusステートにtrueをセット
        context.commit('setUser', null);      //userステートにnullをセットして空にする(userデータを消す)
        return true;
      }

    }catch(error) {

    }
  },
  async currentUser(context) { //ログインユーザーチェック
    context.commit('setApiStatus', null); //apiStatusステートに最初はnullをセットする

    try {
      const response = await axios.get('/api/user'); //ユーザー情報をAPIから取得する

      if(response.status === OK) {            //responseステータスがOK(200)なら後続の処理を行う
        context.commit('setApiStatus', true); //通信成功なので、apiStatusステートにtrueをセット
        context.commit('setUser', response.data || null);      //ユーザーデータをセット。データがない(ログインしていない)場合はnullが入る
        return true;
      }

    }catch(error) {

    }
  },
}

export default { //外部ファイルで読み込めるようにエクスポートする
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}

// import { CREATED, OK, UNPROCESSABLE_ENTITY, TOO_MANY_REQUEST } from "../util"; //util.jsに定義したステータスコードをインポート
//
// const state = {         //stateはデータの入れ物
//   user:      null,      //ログインユーザーを保持する
//   apiStatus: null,      //API呼び出しが成功したか失敗したかを表すステート(これを参照して後続の処理を行うかどうか判別する)
//   loginErrorMessages: { //ログインのエラーメッセージを入れるステート
//     email:    null,
//     password: null
//   },
//   registerErrorMessages: { //ユーザー登録のエラーメッセージを入れるステート
//     name:                  null,
//     branch:                null,
//     prefecture_id:         null,
//     address:               null,
//     email:                 null,
//     password:              null,
//     password_confirmation: null
//   }
// }
//
// const getters = { //gettersはstateの内容から算出される値
//   check:      state => !! state.user,                           //ログインチェックに使用する（確実に真偽値を返すために二重否定にしている）
//   username:   state => state.user ? state.user.name : '',       //ログインユーザー名を取得(nullの場合に呼ばれてもエラーが発生しないように空文字を返す)
//   userId:     state => state.user ? state.user.id : '',         //ログインユーザーのIDを取得
//   isShopUser: state => !!(state.user && state.user.group === 2) //ログインユーザーがお店の人かどうか
// }
//
// const mutations = {      //mutationsはstateを更新するためのメソッド(mutationsを介してstateを更新する(同期処理) )
//   setUser(state, user) { //第一引数は必ず「state」。第二引数には実際に渡したい値を書く
//     state.user = user;   //userステートの値を更新する
//   },
//   setApiStatus(state, status) { //apiStatusステートの値を更新する
//     state.apiStatus = status;
//   },
//   setLoginErrorMessages(state, messages) { //loginErrorMessagesステートの値を更新する
//     state.loginErrorMessages = messages;
//   },
//   setRegisterErrorMessages(state, messages) { //registerErrorMessagesステートの値を更新する
//     state.registerErrorMessages = messages;
//   }
// }
//
// const actions = {                         //actionsはAPI通信などの非同期処理を行ったあとに、mutationsを呼び出してstateを更新する
//   async register(context, data) {         //会員登録APIを呼び出すアクション
//     context.commit('setApiStatus', null); //apiStatusステートに最初はnullをセットする
//
//     try { //例外処理
//       const response = await axios.post('/api/register', data); //会員登録APIを呼び出し、返却データを定数responseに渡す
//
//       if(response.status === CREATED) {           //responseステータスがCREATED(201)なら後続の処理を行う
//         context.commit('setApiStatus', true);     //通信成功なので、apiStatusにtrueをセットする
//         context.commit('setUser', response.data); //setUserミューテーションを実行してuserステートにデータをセット
//         return true;                              //下に書いた処理をfalseを使って抜ける(成功なので)
//       }
//
//     }catch (error) {
//       handleApiError(context, error);
//       return false;
//     }
//   },
//   async login(context, data) { //ログインアクション
//     context.commit('setApiStatus', null); //apiStatusステートに最初はnullをセットする
//
//     try { //例外処理
//       const response = await axios.post('/api/login', data); //ログインAPIを呼び出し、返却データを定数responseに渡す
//       if(response.status === OK) {                //responseステータスがOK(200)なら後続の処理を行う
//         context.commit('setApiStatus', true);     //通信成功(200 OK)なので、apiStatusステートにtrueをセット
//         context.commit('setUser', response.data); //userステートにresponseデータをセット
//         return true; //成功した場合はtrueを返す
//       }
//
//     }catch(error) {
//       handleApiError(context, error);
//       return false;
//     }
//   },
//   async logout(context) { //ログアウト
//     context.commit('setApiStatus', null); //apiStatusステートに最初はnullをセットする
//
//     try {
//       const response = await axios.post('/api/logout');
//
//       if(response.status === OK) {            //responseステータスがOK(200)なら後続の処理を行う
//         context.commit('setApiStatus', true); //通信成功なので、apiStatusステートにtrueをセット
//         context.commit('setUser', null);      //userステートにnullをセットして空にする(userデータを消す)
//         return true;
//       }
//
//     }catch(error) {
//       handleApiError(context, error);
//       return false;
//     }
//   },
//   async currentUser(context) { //ログインユーザーチェック
//     context.commit('setApiStatus', null); //apiStatusステートに最初はnullをセットする
//
//     try {
//       const response = await axios.get('/api/user'); //ユーザー情報をAPIから取得する
//
//       if(response.status === OK) {            //responseステータスがOK(200)なら後続の処理を行う
//         context.commit('setApiStatus', true); //通信成功なので、apiStatusステートにtrueをセット
//         context.commit('setUser', response.data || null);      //ユーザーデータをセット。データがない(ログインしていない)場合はnullが入る
//         return true;
//       }
//
//     }catch(error) {
//       handleApiError(context, error);
//       return false;
//     }
//   },
// }
//
// function handleApiError(context, error) {
//   context.commit('setApiStatus', false);
//   if(error.response) {
//     const status = error.response.status;
//     switch(status) {
//       case UNPROCESSABLE_ENTITY:
//         context.commit('setRegisterErrorMessages', error.response.data.errors);
//         break;
//       case TOO_MANY_REQUEST:
//         context.commit('setLoginErrorMessages', { general: ['ログイン試行回数が多すぎます。しばらくしてからやり直してください。'] });
//         break;
//       default:
//         context.commit('error/setCode', status, { root: true });
//     }
//
//   }else {
//     console.error('ネットワークエラーまたはレスポンスなしのエラー', error);
//   }
// }
//
// export default { //外部ファイルで読み込めるようにエクスポートする
//   namespaced: true,
//   state,
//   getters,
//   mutations,
//   actions
// }

