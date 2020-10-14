import Vue from "vue";
import VueModalTor from 'vue-modaltor';
import VTooltip from 'v-tooltip';
import VueCollapse from 'vue2-collapse';
import SearchwpMetricsColors from "./Colors";
import SearchwpMetricsFormatter from "./Formatter";
import SearchwpMetrics from "./components/Metrics.vue";

Vue.use(VueModalTor);
Vue.use(VTooltip);
Vue.use(VueCollapse);
Vue.use(SearchwpMetricsColors);
Vue.use(SearchwpMetricsFormatter);

new Vue({
  el: '#searchwp-metrics',
  render: h => h(SearchwpMetrics)
});
