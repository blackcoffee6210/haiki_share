import Vue  from 'vue';
import Vuex from 'vuex';

import auth    from './auth';
import error   from './error';
import message from './message';

Vue.use(Vuex); //Vuexを使う

const store = new Vuex.Store({
  modules: {
    csrf: document.querySelector('meta[name="csrf-token"]') //csrf
                  .getAttribute('content'),
    auth,
    error,
    message
  }
})

export default store;
