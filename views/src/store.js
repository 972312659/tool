import Vue from "vue";
import Vuex from "vuex";
import createPersistedState from "vuex-persistedstate";
Vue.use(Vuex);

export default new Vuex.Store({
  plugins: [
    createPersistedState({
      storage: window.sessionStorage
    })
  ],
  state: {
    userinfo: {}
  },
  mutations: {
    Login(state, data) {
      state.userinfo = data;
    },
    Logout(state) {
      state.userinfo = {};
    }
  },
  actions: {}
});
