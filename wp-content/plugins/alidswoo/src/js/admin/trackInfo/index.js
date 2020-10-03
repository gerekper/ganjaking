import App from './trackInfo';
import Vue from 'vue';

const index = function () {

    return {
        init(){
            jQuery('body').append('<div id="trackInfo"/>');
            new Vue({
                el: '#trackInfo',
                ...App
            });
        }
    }
};


export default index();