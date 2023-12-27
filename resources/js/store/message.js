//グローバルなメッセージ管理用にmessageモジュールを作成
const state = {
  content: ''
}

const mutations = {
  setContent(state, { content, timeout }) {
    state.content = content;
    //timeoutが設定されていなければ、デフォルト値をセット(ここでは3秒とする)
    if(typeof timeout === 'undefined') {
      timeout = 3000;
    }
    //3秒後にコンテンツを空にする
    setTimeout(() => (state.content = ''), timeout);
  }
}

export default {
  namespaced: true,
  state,
  mutations
}
