import Vue from 'vue';
import VueCollapse from 'vue2-collapse';
import VTooltip from 'v-tooltip';
import VueTabs from 'vue-nav-tabs/dist/vue-tabs.js';

import Advanced from './Components/Advanced.vue';

import { __ } from './helpers.js';

Vue.use(VueCollapse);
Vue.use(VTooltip);
Vue.use(VueTabs);

Vue.filter('i18n', function (source, placeholders = []) {
	return __( source, placeholders );
})

new Vue({
	el: '#searchwp-advanced',
	render: h => h(Advanced)
});