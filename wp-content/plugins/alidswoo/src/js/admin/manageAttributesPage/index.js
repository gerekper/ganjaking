import App from './Page.vue';
import Vue from 'vue';
import VueResource from 'vue-resource';
import '../../../../theme/index.css';
import './global.scss';

import { Select, Input, Button, Row, Option, Notification, Tooltip, Loading } from 'element-ui';

Vue.use(VueResource);
Vue.http.options.emulateJSON = true;

Vue.component(Select.name, Select);
Vue.component(Option.name, Option);
Vue.component(Input.name, Input);
Vue.component(Button.name, Button);
Vue.component(Row.name, Row);
Vue.component(Tooltip.name, Tooltip);
Vue.use(Loading.directive);

Vue.prototype.$loading = Loading.service;

Vue.prototype.$notify = function({message, title = '', type='success'}){
    return Notification({
        title: title,
        message: message,
        type: type
    })
} ;

new Vue({
    el: '#adsw-page-manage-attributes',
    ...App
});