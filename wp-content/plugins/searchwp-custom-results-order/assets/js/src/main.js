import Vue from "vue";
import Spinner from "vue-simple-spinner";
import VTooltip from "v-tooltip";
import "v-tooltip/dist/v-tooltip.css";

import Factory from "./factory";
import CustomResultsOrder from "./CustomResultsOrder";
import Result from "./Result";
import Tooltip from "./Tooltip";

Vue.use(VTooltip);
Vue.use(Factory);

Vue.component("spinner", Spinner);
Vue.component("result", Result);
Vue.component("tooltip", Tooltip);

Vue.filter("i18n", str => Vue.CustomResultsOrderI18n(str));

const store = {
  state: {
    engines: _SEARCHWP_CRO_VARS.engines,
    triggers: _SEARCHWP_CRO_VARS.settings
  }
};

new Vue({
  el: "#searchwp-cro",
  data: store.state,
  render: h => h(CustomResultsOrder)
});
