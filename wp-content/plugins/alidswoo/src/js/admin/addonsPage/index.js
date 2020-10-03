import App from './addonsPage.vue';
import Vue from 'vue';
import VueResource from 'vue-resource';

Vue.use(VueResource);
Vue.http.options.emulateJSON = true;

new Vue({
    el: '#adsw-page-addons',
    ...App
})