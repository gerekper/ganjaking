import Vue from 'vue';
import VueCollapse from 'vue2-collapse';
import vSelect from 'vue-select';
import VTooltip from 'v-tooltip';
import VModal from 'vue-js-modal';

import Settings from './Components/Settings.vue';

import { __ } from './helpers.js';

Vue.use(VueCollapse);
Vue.use(VTooltip);
Vue.use(VModal, {componentName: 'v-modal'});

vSelect.props.components.default = () => ({
	Deselect: {
		render: createElement => createElement('span', { class: 'dashicons dashicons-no-alt' } ),
	},
	OpenIndicator: {
		render: createElement => createElement('span', { class: 'dashicons dashicons-arrow-down-alt2' } ),
	},
});

Vue.component('v-select', vSelect);

Vue.filter('i18n', function (source, placeholders = []) {
	return __( source, placeholders );
})

new Vue({
	el: '#searchwp-settings',
	render: h => h(Settings)
});