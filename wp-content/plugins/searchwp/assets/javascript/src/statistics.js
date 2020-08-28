import Vue from 'vue';
import VueTabs from 'vue-nav-tabs/dist/vue-tabs.js';
import VModal from 'vue-js-modal';

import Statistics from './Components/Statistics.vue';

import { __ } from './helpers.js';

Vue.use(VueTabs);
Vue.use(VModal, {componentName: 'v-modal'});

Vue.filter('i18n', function (source, placeholders = []) {
	return __( source, placeholders );
})

new Vue({
	el: '#searchwp-statistics',
	render: h => h(Statistics)
});