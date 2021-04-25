import Vue from 'vue';
import FlashMessage from '@smartweb/vue-flash-message';
import Vuex from 'vuex';
import moment from 'moment';

Vue.use(FlashMessage)
Vue.use(Vuex);
Vue.use(moment);
moment.locale('ru');

import App from './views/App.vue'

const app = new Vue({
    el: '#app',
    components: { App },
  });
