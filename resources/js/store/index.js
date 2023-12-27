import Vue  from 'vue';
import Vuex from 'vuex';

import auth    from './auth';
import error   from './error';
import message from './message';

//Vuexを使う
Vue.use(Vuex);

const store = new Vuex.Store({
  modules: {
    //csrf
    csrf: document.querySelector('meta[name="csrf-token"]')
                  .getAttribute('content'),
    auth,
    error,
    message
  }
})

export default store;
