//コンポーネントを跨いでエラー情報を扱うモジュール
const state = {
  //エラーのステータスコードを表すcodeステート
  code: null
}

const mutations = {
  //codeステータスの値を更新する
  setCode(state, code) {
    state.code = code;
  }
}


export default {
  namespaced: true,
  state,
  mutations
}
