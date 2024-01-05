const state = { //コンポーネントを跨いでエラー情報を扱うモジュール
  code: null    //エラーのステータスコードを表すcodeステート
}

const mutations = {
  setCode(state, code) { //codeステータスの値を更新する
    state.code = code;
  }
}

export default {
  namespaced: true,
  state,
  mutations
}
