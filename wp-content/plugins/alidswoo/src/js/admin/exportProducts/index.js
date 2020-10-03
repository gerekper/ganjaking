import App from './exportProducts.vue';
import Vue from 'vue';

const index = function () {

    return {
        init(){
            jQuery('body').append('<div id="exportProducts"/>');
            new Vue({
                el: '#exportProducts',
                ...App
            });
        }
    }
};


export default index();