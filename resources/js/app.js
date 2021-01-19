import Vue from 'vue';
import FlashMessage from '@smartweb/vue-flash-message';
import VueRouter from 'vue-router';
import Vuex from 'vuex';
window.axios = require('axios');
import moment from 'moment';

Vue.use(VueRouter)
Vue.use(FlashMessage)
Vue.use(Vuex);
Vue.use(moment);
moment.locale('ru');
//Vue.prototype.$moment = moment;

import App from './views/App.vue'
import Shows from './views/Shows.vue'
import Events from './views/Events.vue'
import Places from './views/Places.vue'

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'shows',
            component: Shows
        },
        {
            path: '/events/:eventId/places',
            name: 'event.eventId',
            component: Places
        },
        {
            path: '/shows/:showId/events',
            name: 'shows.eventId',
            component: Events
        },
    ]
})

const store = new Vuex.Store({
    state: {
        protocol: 'https',
        portal: 'leadbook.ru/test-task-api',
    },
    getters: {
        protocol: state => {
            return state.protocol
        },
        portal: state => {
            return state.portal
        }
    }
})

const app = new Vue({
    el: '#app',
    components: { App },
    router,
    store
  });
