import Vue from 'vue';
import Support from './Components/Support.vue';
import VModal from 'vue-js-modal';
import VueCollapse from 'vue2-collapse';
import { __ } from './helpers.js';
import VueClipboard from 'vue-clipboard2';

Vue.use(VueCollapse);
Vue.use(VModal, {componentName: 'v-modal'});
Vue.use(VueClipboard);

Vue.filter('i18n', function (source, placeholders = []) {
	return __( source, placeholders );
})

new Vue({
	el: '#searchwp-support',
	render: h => h(Support)
});