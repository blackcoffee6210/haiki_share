const state = { //グローバルなメッセージ管理用にmessageモジュールを作成
  content: ''
}

const mutations = {
  setContent(state, { content, timeout }) {
    state.content = content;

    if(typeof timeout === 'undefined') { //timeoutが設定されていなければ、デフォルト値をセット(ここでは5秒とする)
      timeout = 5000;
    }
    setTimeout(() => (state.content = ''), timeout); //5秒後にコンテンツを空にする
  }
}

export default {
  namespaced: true,
  state,
  mutations
}
