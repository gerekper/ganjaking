/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 703:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var ReactPropTypesSecret = __webpack_require__(414);

function emptyFunction() {}
function emptyFunctionWithReset() {}
emptyFunctionWithReset.resetWarningCache = emptyFunction;

module.exports = function() {
  function shim(props, propName, componentName, location, propFullName, secret) {
    if (secret === ReactPropTypesSecret) {
      // It is still safe when called from React.
      return;
    }
    var err = new Error(
      'Calling PropTypes validators directly is not supported by the `prop-types` package. ' +
      'Use PropTypes.checkPropTypes() to call them. ' +
      'Read more at http://fb.me/use-check-prop-types'
    );
    err.name = 'Invariant Violation';
    throw err;
  };
  shim.isRequired = shim;
  function getShim() {
    return shim;
  };
  // Important!
  // Keep this list in sync with production version in `./factoryWithTypeCheckers.js`.
  var ReactPropTypes = {
    array: shim,
    bigint: shim,
    bool: shim,
    func: shim,
    number: shim,
    object: shim,
    string: shim,
    symbol: shim,

    any: shim,
    arrayOf: getShim,
    element: shim,
    elementType: shim,
    instanceOf: getShim,
    node: shim,
    objectOf: getShim,
    oneOf: getShim,
    oneOfType: getShim,
    shape: getShim,
    exact: getShim,

    checkPropTypes: emptyFunctionWithReset,
    resetWarningCache: emptyFunction
  };

  ReactPropTypes.PropTypes = ReactPropTypes;

  return ReactPropTypes;
};


/***/ }),

/***/ 697:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

if (false) { var throwOnDirectAccess, ReactIs; } else {
  // By explicitly using `prop-types` you are opting into new production behavior.
  // http://fb.me/prop-types-in-prod
  module.exports = __webpack_require__(703)();
}


/***/ }),

/***/ 414:
/***/ (function(module) {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var ReactPropTypesSecret = 'SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED';

module.exports = ReactPropTypesSecret;


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
!function() {
"use strict";

;// CONCATENATED MODULE: external ["wp","hooks"]
var external_wp_hooks_namespaceObject = window["wp"]["hooks"];
;// CONCATENATED MODULE: external ["wp","i18n"]
var external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: external "React"
var external_React_namespaceObject = window["React"];
;// CONCATENATED MODULE: external ["wp","element"]
var external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: external ["wc","components"]
var external_wc_components_namespaceObject = window["wc"]["components"];
;// CONCATENATED MODULE: external ["wc","wcSettings"]
var external_wc_wcSettings_namespaceObject = window["wc"]["wcSettings"];
;// CONCATENATED MODULE: external ["wp","compose"]
var external_wp_compose_namespaceObject = window["wp"]["compose"];
;// CONCATENATED MODULE: external ["wp","data"]
var external_wp_data_namespaceObject = window["wp"]["data"];
// EXTERNAL MODULE: ./node_modules/prop-types/index.js
var prop_types = __webpack_require__(697);
var prop_types_default = /*#__PURE__*/__webpack_require__.n(prop_types);
;// CONCATENATED MODULE: external ["wc","navigation"]
var external_wc_navigation_namespaceObject = window["wc"]["navigation"];
;// CONCATENATED MODULE: external ["wc","number"]
var external_wc_number_namespaceObject = window["wc"]["number"];
;// CONCATENATED MODULE: external ["wc","data"]
var external_wc_data_namespaceObject = window["wc"]["data"];
;// CONCATENATED MODULE: external ["wc","date"]
var external_wc_date_namespaceObject = window["wc"]["date"];
;// CONCATENATED MODULE: external ["wc","currency"]
var external_wc_currency_namespaceObject = window["wc"]["currency"];
var external_wc_currency_default = /*#__PURE__*/__webpack_require__.n(external_wc_currency_namespaceObject);
;// CONCATENATED MODULE: ./node_modules/@somewherewarm/woocommerce/packages/lib/currency-context.js
/**
 * External dependencies
 */




/**
 * Internal dependencies
 */

const appCurrency = external_wc_currency_default()(external_wc_wcSettings_namespaceObject.CURRENCY);
const getFilteredCurrencyInstance = query => {
  const config = appCurrency.getCurrencyConfig();
  const filteredConfig = applyFilters('woocommerce_admin_report_currency', config, query);
  return CurrencyFactory(filteredConfig);
};
const CurrencyContext = (0,external_wp_element_namespaceObject.createContext)(appCurrency // default value
);
;// CONCATENATED MODULE: ./node_modules/@somewherewarm/woocommerce/packages/components/report-summary/index.js

/**
 * External dependencies
 */






/**
 * WooCommerce dependencies
 */






/**
 * Internal dependencies
 */
// import ReportError from '../report-error';


/**
 * Component to render summary numbers in reports.
 */
class ReportSummary extends external_wp_element_namespaceObject.Component {
  formatVal(val, type) {
    const {
      formatAmount,
      getCurrencyConfig
    } = this.context;
    return type === 'currency' ? formatAmount(val) : (0,external_wc_number_namespaceObject.formatValue)(getCurrencyConfig(), type, val);
  }
  getValues(key, type) {
    const {
      emptySearchResults,
      summaryData
    } = this.props;
    const {
      totals
    } = summaryData;
    const primaryTotal = totals.primary ? totals.primary[key] : 0;
    const secondaryTotal = totals.secondary ? totals.secondary[key] : 0;
    const primaryValue = emptySearchResults ? 0 : primaryTotal;
    const secondaryValue = emptySearchResults ? 0 : secondaryTotal;
    return {
      delta: (0,external_wc_number_namespaceObject.calculateDelta)(primaryValue, secondaryValue),
      prevValue: this.formatVal(secondaryValue, type),
      value: this.formatVal(primaryValue, type)
    };
  }
  render() {
    const {
      charts,
      query,
      selectedChart,
      summaryData,
      endpoint,
      report,
      defaultDateRange
    } = this.props;
    const {
      isError,
      isRequesting
    } = summaryData;
    if (isError) {
      // return <ReportError isError />;
      return;
    }
    if (isRequesting) {
      return (0,external_React_namespaceObject.createElement)(external_wc_components_namespaceObject.SummaryListPlaceholder, {
        numberOfItems: charts.length
      });
    }
    const {
      compare
    } = (0,external_wc_date_namespaceObject.getDateParamsFromQuery)(query, defaultDateRange);
    const renderSummaryNumbers = ({
      onToggle
    }) => charts.map(chart => {
      const {
        key,
        order,
        orderby,
        label,
        type
      } = chart;
      const newPath = {
        chart: key
      };
      if (orderby) {
        newPath.orderby = orderby;
      }
      if (order) {
        newPath.order = order;
      }
      const href = (0,external_wc_navigation_namespaceObject.getNewPath)(newPath);
      const isSelected = selectedChart.key === key;
      const {
        delta,
        prevValue,
        value
      } = this.getValues(key, type);
      return (0,external_React_namespaceObject.createElement)(external_wc_components_namespaceObject.SummaryNumber, {
        key: key,
        delta: delta,
        href: href,
        label: label,
        prevLabel: compare === 'previous_period' ? (0,external_wp_i18n_namespaceObject.__)('Previous Period:', 'woocommerce-product-bundles') : (0,external_wp_i18n_namespaceObject.__)('Previous Year:', 'woocommerce-product-bundles'),
        prevValue: prevValue,
        selected: isSelected,
        value: value,
        onLinkClickCallback: () => {
          // Wider than a certain breakpoint, there is no dropdown so avoid calling onToggle.
          if (onToggle) {
            onToggle();
          }
        }
      });
    });
    return (0,external_React_namespaceObject.createElement)(external_wc_components_namespaceObject.SummaryList, null, renderSummaryNumbers);
  }
}
ReportSummary.propTypes = {
  /**
   * Properties of all the charts available for that report.
   */
  charts: (prop_types_default()).array.isRequired,
  /**
   * The endpoint to use in API calls to populate the Summary Numbers.
   * For example, if `taxes` is provided, data will be fetched from the report
   * `taxes` endpoint (ie: `/wc-analytics/reports/taxes/stats`). If the provided endpoint
   * doesn't exist, an error will be shown to the user with `ReportError`.
   */
  endpoint: (prop_types_default()).string.isRequired,
  /**
   * The query string represented in object form.
   */
  query: (prop_types_default()).object.isRequired,
  /**
   * Properties of the selected chart.
   */
  selectedChart: prop_types_default().shape({
    /**
     * Key of the selected chart.
     */
    key: (prop_types_default()).string.isRequired,
    /**
     * Chart label.
     */
    label: (prop_types_default()).string.isRequired,
    /**
     * Order query argument.
     */
    order: prop_types_default().oneOf(['asc', 'desc']),
    /**
     * Order by query argument.
     */
    orderby: (prop_types_default()).string,
    /**
     * Number type for formatting.
     */
    type: prop_types_default().oneOf(['average', 'number', 'currency']).isRequired
  }).isRequired,
  /**
   * Data to display in the SummaryNumbers.
   */
  summaryData: (prop_types_default()).object,
  /**
   * Report name, if different than the endpoint.
   */
  report: (prop_types_default()).string
};
ReportSummary.defaultProps = {
  summaryData: {
    totals: {
      primary: {},
      secondary: {}
    },
    isError: false
  }
};
ReportSummary.contextType = CurrencyContext;
/* harmony default export */ var report_summary = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_data_namespaceObject.withSelect)((select, props) => {
  const {
    charts,
    endpoint,
    limitProperties,
    query,
    filters,
    advancedFilters
  } = props;
  const limitBy = limitProperties || [endpoint];
  const hasLimitByParam = limitBy.some(item => query[item] && query[item].length);
  if (query.search && !hasLimitByParam) {
    return {
      emptySearchResults: true
    };
  }
  const fields = charts && charts.map(chart => chart.key);
  const {
    woocommerce_default_date_range: defaultDateRange
  } = select(external_wc_data_namespaceObject.SETTINGS_STORE_NAME).getSetting('wc_admin', 'wcAdminSettings');
  const summaryData = (0,external_wc_data_namespaceObject.getSummaryNumbers)({
    endpoint,
    query,
    select,
    limitBy,
    filters,
    advancedFilters,
    defaultDateRange,
    fields
  });
  return {
    summaryData,
    defaultDateRange
  };
}))(ReportSummary));
;// CONCATENATED MODULE: external ["wp","date"]
var external_wp_date_namespaceObject = window["wp"]["date"];
;// CONCATENATED MODULE: external "lodash"
var external_lodash_namespaceObject = window["lodash"];
;// CONCATENATED MODULE: ./node_modules/@somewherewarm/woocommerce/packages/components/report-error/index.js

/**
 * External dependencies
 */






/**
 * Component to render when there is an error in a report component due to data
 * not being loaded or being invalid.
 */
class report_error_ReportError extends external_wp_element_namespaceObject.Component {
  render() {
    const {
      className,
      isError,
      isEmpty
    } = this.props;
    let title, actionLabel, actionURL, actionCallback;
    if (isError) {
      title = (0,external_wp_i18n_namespaceObject.__)('There was an error getting your stats. Please try again.', 'woocommerce-product-bundles');
      actionLabel = (0,external_wp_i18n_namespaceObject.__)('Reload', 'woocommerce-product-bundles');
      actionCallback = () => {
        window.location.reload();
      };
    } else if (isEmpty) {
      title = (0,external_wp_i18n_namespaceObject.__)('No results could be found for this date range.', 'woocommerce-product-bundles');
      actionLabel = (0,external_wp_i18n_namespaceObject.__)('View Orders', 'woocommerce-product-bundles');
      actionURL = (0,external_wc_wcSettings_namespaceObject.getAdminLink)('edit.php?post_type=shop_order');
    }
    return (0,external_React_namespaceObject.createElement)(external_wc_components_namespaceObject.EmptyContent, {
      className: className,
      title: title,
      actionLabel: actionLabel,
      actionURL: actionURL,
      actionCallback: actionCallback
    });
  }
}
report_error_ReportError.propTypes = {
  /**
   * Additional class name to style the component.
   */
  className: (prop_types_default()).string,
  /**
   * Boolean representing whether there was an error.
   */
  isError: (prop_types_default()).bool,
  /**
   * Boolean representing whether the issue is that there is no data.
   */
  isEmpty: (prop_types_default()).bool
};
report_error_ReportError.defaultProps = {
  className: ''
};
/* harmony default export */ var report_error = (report_error_ReportError);
;// CONCATENATED MODULE: ./node_modules/@somewherewarm/woocommerce/packages/components/report-chart/utils.js
/**
 * External dependencies
 */


const DEFAULT_FILTER = 'all';
function getSelectedFilter(filters, query, selectedFilterArgs = {}) {
  if (!filters || filters.length === 0) {
    return null;
  }
  const clonedFilters = filters.slice(0);
  const filterConfig = clonedFilters.pop();
  if (filterConfig.showFilters(query, selectedFilterArgs)) {
    const allFilters = (0,external_wc_navigation_namespaceObject.flattenFilters)(filterConfig.filters);
    const value = query[filterConfig.param] || filterConfig.defaultValue || DEFAULT_FILTER;
    return (0,external_lodash_namespaceObject.find)(allFilters, {
      value
    });
  }
  return getSelectedFilter(clonedFilters, query, selectedFilterArgs);
}
function getChartMode(selectedFilter, query) {
  if (selectedFilter && query) {
    const selectedFilterParam = (0,external_lodash_namespaceObject.get)(selectedFilter, ['settings', 'param']);
    if (!selectedFilterParam || Object.keys(query).includes(selectedFilterParam)) {
      return (0,external_lodash_namespaceObject.get)(selectedFilter, ['chartMode']);
    }
  }
  return null;
}
;// CONCATENATED MODULE: ./node_modules/@somewherewarm/woocommerce/packages/components/report-chart/index.js

/**
 * External dependencies
 */








/**
 * WooCommerce dependencies
 */




/**
 * Internal dependencies
 */




/**
 * Component that renders the chart in reports.
 */
class ReportChart extends external_wp_element_namespaceObject.Component {
  shouldComponentUpdate(nextProps) {
    if (nextProps.isRequesting !== this.props.isRequesting || nextProps.primaryData.isRequesting !== this.props.primaryData.isRequesting || nextProps.secondaryData.isRequesting !== this.props.secondaryData.isRequesting || !(0,external_lodash_namespaceObject.isEqual)(nextProps.query, this.props.query)) {
      return true;
    }
    return false;
  }
  getItemChartData() {
    const {
      primaryData,
      selectedChart
    } = this.props;
    const chartData = primaryData.data.intervals.map(function (interval) {
      const intervalData = {};
      interval.subtotals.segments.forEach(function (segment) {
        if (segment.segment_label) {
          const label = intervalData[segment.segment_label] ? segment.segment_label + ' (#' + segment.segment_id + ')' : segment.segment_label;
          intervalData[segment.segment_id] = {
            label,
            value: segment.subtotals[selectedChart.key] || 0
          };
        }
      });
      return {
        date: (0,external_wp_date_namespaceObject.format)('Y-m-d\\TH:i:s', interval.date_start),
        ...intervalData
      };
    });
    return chartData;
  }
  getTimeChartData() {
    const {
      query,
      primaryData,
      secondaryData,
      selectedChart,
      defaultDateRange
    } = this.props;
    const currentInterval = (0,external_wc_date_namespaceObject.getIntervalForQuery)(query);
    const {
      primary,
      secondary
    } = (0,external_wc_date_namespaceObject.getCurrentDates)(query, defaultDateRange);
    const chartData = primaryData.data.intervals.map(function (interval, index) {
      const secondaryDate = (0,external_wc_date_namespaceObject.getPreviousDate)(interval.date_start, primary.after, secondary.after, query.compare, currentInterval);
      const secondaryInterval = secondaryData.data.intervals[index];
      return {
        date: (0,external_wp_date_namespaceObject.format)('Y-m-d\\TH:i:s', interval.date_start),
        primary: {
          label: `${primary.label} (${primary.range})`,
          labelDate: interval.date_start,
          value: interval.subtotals[selectedChart.key] || 0
        },
        secondary: {
          label: `${secondary.label} (${secondary.range})`,
          labelDate: secondaryDate.format('YYYY-MM-DD HH:mm:ss'),
          value: secondaryInterval && secondaryInterval.subtotals[selectedChart.key] || 0
        }
      };
    });
    return chartData;
  }
  getTimeChartTotals() {
    const {
      primaryData,
      secondaryData,
      selectedChart
    } = this.props;
    return {
      primary: (0,external_lodash_namespaceObject.get)(primaryData, ['data', 'totals', selectedChart.key], null),
      secondary: (0,external_lodash_namespaceObject.get)(secondaryData, ['data', 'totals', selectedChart.key], null)
    };
  }
  renderChart(mode, isRequesting, chartData, legendTotals) {
    const {
      emptySearchResults,
      filterParam,
      interactiveLegend,
      itemsLabel,
      legendPosition,
      path,
      query,
      selectedChart,
      showHeaderControls,
      primaryData
    } = this.props;
    const currentInterval = (0,external_wc_date_namespaceObject.getIntervalForQuery)(query);
    const allowedIntervals = (0,external_wc_date_namespaceObject.getAllowedIntervalsForQuery)(query);
    const formats = (0,external_wc_date_namespaceObject.getDateFormatsForInterval)(currentInterval, primaryData.data.intervals.length);
    const emptyMessage = emptySearchResults ? (0,external_wp_i18n_namespaceObject.__)('No data for the current search', 'woocommerce-admin') : (0,external_wp_i18n_namespaceObject.__)('No data for the selected date range', 'woocommerce-admin');
    const {
      formatAmount,
      getCurrencyConfig
    } = this.context;
    return (0,external_React_namespaceObject.createElement)(external_wc_components_namespaceObject.Chart, {
      allowedIntervals: allowedIntervals,
      data: chartData,
      dateParser: '%Y-%m-%dT%H:%M:%S',
      emptyMessage: emptyMessage,
      filterParam: filterParam,
      interactiveLegend: interactiveLegend,
      interval: currentInterval,
      isRequesting: isRequesting,
      itemsLabel: itemsLabel,
      legendPosition: legendPosition,
      legendTotals: legendTotals,
      mode: mode,
      path: path,
      query: query,
      screenReaderFormat: formats.screenReaderFormat,
      showHeaderControls: showHeaderControls,
      title: selectedChart.label,
      tooltipLabelFormat: formats.tooltipLabelFormat,
      tooltipTitle: mode === 'time-comparison' && selectedChart.label || null,
      tooltipValueFormat: (0,external_wc_data_namespaceObject.getTooltipValueFormat)(selectedChart.type, formatAmount),
      chartType: (0,external_wc_date_namespaceObject.getChartTypeForQuery)(query),
      valueType: selectedChart.type,
      xFormat: formats.xFormat,
      x2Format: formats.x2Format,
      currency: getCurrencyConfig()
    });
  }
  renderItemComparison() {
    const {
      isRequesting,
      primaryData
    } = this.props;
    if (primaryData.isError) {
      return (0,external_React_namespaceObject.createElement)(report_error, {
        isError: true
      });
    }
    const isChartRequesting = isRequesting || primaryData.isRequesting;
    const chartData = this.getItemChartData();
    return this.renderChart('item-comparison', isChartRequesting, chartData);
  }
  renderTimeComparison() {
    const {
      isRequesting,
      primaryData,
      secondaryData
    } = this.props;
    if (!primaryData || primaryData.isError || secondaryData.isError) {
      return (0,external_React_namespaceObject.createElement)(report_error, {
        isError: true
      });
    }
    const isChartRequesting = isRequesting || primaryData.isRequesting || secondaryData.isRequesting;
    const chartData = this.getTimeChartData();
    const legendTotals = this.getTimeChartTotals();
    return this.renderChart('time-comparison', isChartRequesting, chartData, legendTotals);
  }
  render() {
    const {
      mode
    } = this.props;
    if (mode === 'item-comparison') {
      return this.renderItemComparison();
    }
    return this.renderTimeComparison();
  }
}
ReportChart.contextType = CurrencyContext;
ReportChart.propTypes = {
  /**
   * Filters available for that report.
   */
  filters: (prop_types_default()).array,
  /**
   * Whether there is an API call running.
   */
  isRequesting: (prop_types_default()).bool,
  /**
   * Label describing the legend items.
   */
  itemsLabel: (prop_types_default()).string,
  /**
   * Allows specifying properties different from the `endpoint` that will be used
   * to limit the items when there is an active search.
   */
  limitProperties: (prop_types_default()).array,
  /**
   * `items-comparison` (default) or `time-comparison`, this is used to generate correct
   * ARIA properties.
   */
  mode: (prop_types_default()).string,
  /**
   * Current path
   */
  path: (prop_types_default()).string.isRequired,
  /**
   * Primary data to display in the chart.
   */
  primaryData: (prop_types_default()).object,
  /**
   * The query string represented in object form.
   */
  query: (prop_types_default()).object.isRequired,
  /**
   * Secondary data to display in the chart.
   */
  secondaryData: (prop_types_default()).object,
  /**
   * Properties of the selected chart.
   */
  selectedChart: prop_types_default().shape({
    /**
     * Key of the selected chart.
     */
    key: (prop_types_default()).string.isRequired,
    /**
     * Chart label.
     */
    label: (prop_types_default()).string.isRequired,
    /**
     * Order query argument.
     */
    order: prop_types_default().oneOf(['asc', 'desc']),
    /**
     * Order by query argument.
     */
    orderby: (prop_types_default()).string,
    /**
     * Number type for formatting.
     */
    type: prop_types_default().oneOf(['average', 'number', 'currency']).isRequired
  }).isRequired
};
ReportChart.defaultProps = {
  isRequesting: false,
  primaryData: {
    data: {
      intervals: []
    },
    isError: false,
    isRequesting: false
  },
  secondaryData: {
    data: {
      intervals: []
    },
    isError: false,
    isRequesting: false
  }
};
/* harmony default export */ var report_chart = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_data_namespaceObject.withSelect)((select, props) => {
  const {
    charts,
    endpoint,
    filters,
    isRequesting,
    limitProperties,
    query,
    advancedFilters
  } = props;
  const limitBy = limitProperties || [endpoint];
  const selectedFilter = getSelectedFilter(filters, query);
  const filterParam = (0,external_lodash_namespaceObject.get)(selectedFilter, ['settings', 'param']);
  const chartMode = props.mode || getChartMode(selectedFilter, query) || 'time-comparison';
  const {
    woocommerce_default_date_range: defaultDateRange
  } = select(external_wc_data_namespaceObject.SETTINGS_STORE_NAME).getSetting('wc_admin', 'wcAdminSettings');

  /* eslint @wordpress/no-unused-vars-before-return: "off" */
  const reportStoreSelector = select(external_wc_data_namespaceObject.REPORTS_STORE_NAME);
  const newProps = {
    mode: chartMode,
    filterParam,
    defaultDateRange
  };
  if (isRequesting) {
    return newProps;
  }
  const hasLimitByParam = limitBy.some(item => query[item] && query[item].length);
  if (query.search && !hasLimitByParam) {
    return {
      ...newProps,
      emptySearchResults: true
    };
  }
  const fields = charts && charts.map(chart => chart.key);
  const primaryData = (0,external_wc_data_namespaceObject.getReportChartData)({
    endpoint,
    dataType: 'primary',
    query,
    // Hint: Leave this param for backwards compatibility WC-Admin lt 2.6.
    select,
    selector: reportStoreSelector,
    limitBy,
    filters,
    advancedFilters,
    defaultDateRange,
    fields
  });
  if (chartMode === 'item-comparison') {
    return {
      ...newProps,
      primaryData
    };
  }
  const secondaryData = (0,external_wc_data_namespaceObject.getReportChartData)({
    endpoint,
    dataType: 'secondary',
    query,
    // Hint: Leave this param for backwards compatibility WC-Admin lt 2.6.
    select,
    selector: reportStoreSelector,
    limitBy,
    filters,
    advancedFilters,
    defaultDateRange,
    fields
  });
  return {
    ...newProps,
    primaryData,
    secondaryData
  };
}))(ReportChart));
;// CONCATENATED MODULE: external ["wp","apiFetch"]
var external_wp_apiFetch_namespaceObject = window["wp"]["apiFetch"];
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_namespaceObject);
;// CONCATENATED MODULE: external ["wp","url"]
var external_wp_url_namespaceObject = window["wp"]["url"];
;// CONCATENATED MODULE: ./node_modules/@somewherewarm/woocommerce/packages/lib/index.js
/**
 * External dependencies.
 */





/**
 * Exports.
 */
function getRequestByIdString(path, handleData = identity) {
  return function (queryString = '') {
    const pathString = path;
    const idList = (0,external_wc_navigation_namespaceObject.getIdsFromQuery)(queryString);
    if (idList.length < 1) {
      return Promise.resolve([]);
    }
    const payload = {
      include: idList.join(','),
      per_page: idList.length
    };
    return external_wp_apiFetch_default()({
      path: (0,external_wp_url_namespaceObject.addQueryArgs)(pathString, payload)
    }).then(data => data.map(handleData));
  };
}

/**
 * Takes a chart name returns the configuration for that chart from and array
 * of charts. If the chart is not found it will return the first chart.
 *
 * @param {string} chartName - the name of the chart to get configuration for
 * @param {Array} charts - list of charts for a particular report
 * @return {Object} - chart configuration object
 */
function getSelectedChart(chartName, charts = []) {
  const chart = (0,external_lodash_namespaceObject.find)(charts, {
    key: chartName
  });
  if (chart) {
    return chart;
  }
  return charts[0];
}
;// CONCATENATED MODULE: external ["wp","htmlEntities"]
var external_wp_htmlEntities_namespaceObject = window["wp"]["htmlEntities"];
;// CONCATENATED MODULE: external ["wp","components"]
var external_wp_components_namespaceObject = window["wp"]["components"];
;// CONCATENATED MODULE: external ["wp","dom"]
var external_wp_dom_namespaceObject = window["wp"]["dom"];
;// CONCATENATED MODULE: external ["wc","csvExport"]
var external_wc_csvExport_namespaceObject = window["wc"]["csvExport"];
;// CONCATENATED MODULE: ./node_modules/@somewherewarm/woocommerce/packages/components/report-table/download-icon.js

/* harmony default export */ var download_icon = (() => (0,external_React_namespaceObject.createElement)("svg", {
  role: "img",
  "aria-hidden": "true",
  focusable: "false",
  version: "1.1",
  xmlns: "http://www.w3.org/2000/svg",
  x: "0px",
  y: "0px",
  viewBox: "0 0 24 24"
}, (0,external_React_namespaceObject.createElement)("path", {
  d: "M18,9c-0.009,0-0.017,0.002-0.025,0.003C17.72,5.646,14.922,3,11.5,3C7.91,3,5,5.91,5,9.5c0,0.524,0.069,1.031,0.186,1.519 C5.123,11.016,5.064,11,5,11c-2.209,0-4,1.791-4,4c0,1.202,0.541,2.267,1.38,3h18.593C22.196,17.089,23,15.643,23,14 C23,11.239,20.761,9,18,9z M12,16l-4-5h3V8h2v3h3L12,16z"
})));
;// CONCATENATED MODULE: ./node_modules/@somewherewarm/woocommerce/packages/components/report-table/utils.js
/**
 * External dependencies
 */

function extendTableData(select, props, queriedTableData) {
  const {
    extendItemsMethodNames,
    extendedItemsStoreName,
    itemIdField
  } = props;
  const itemsData = queriedTableData.items.data;
  if (!Array.isArray(itemsData) || !itemsData.length || !extendItemsMethodNames || !itemIdField) {
    return queriedTableData;
  }
  const {
    [extendItemsMethodNames.getError]: getErrorMethod,
    [extendItemsMethodNames.isRequesting]: isRequestingMethod,
    [extendItemsMethodNames.load]: loadMethod
  } = select(extendedItemsStoreName);
  const extendQuery = {
    include: itemsData.map(item => item[itemIdField]).join(','),
    per_page: itemsData.length
  };
  const extendedItems = loadMethod(extendQuery);
  const isExtendedItemsRequesting = isRequestingMethod ? isRequestingMethod(extendQuery) : false;
  const isExtendedItemsError = getErrorMethod ? getErrorMethod(extendQuery) : false;
  const extendedItemsData = itemsData.map(item => {
    const extendedItemData = first(extendedItems.filter(extendedItem => item.id === extendedItem.id));
    return {
      ...item,
      ...extendedItemData
    };
  });
  const isRequesting = queriedTableData.isRequesting || isExtendedItemsRequesting;
  const isError = queriedTableData.isError || isExtendedItemsError;
  return {
    ...queriedTableData,
    isRequesting,
    isError,
    items: {
      ...queriedTableData.items,
      data: extendedItemsData
    }
  };
}
;// CONCATENATED MODULE: ./node_modules/@somewherewarm/woocommerce/packages/components/report-table/index.js

/**
 * External dependencies
 */














/**
 * Internal dependencies
 */



const TABLE_FILTER = 'woocommerce_admin_report_table';
const ReportTable = props => {
  const {
    getHeadersContent,
    getRowsContent,
    getSummary,
    isRequesting,
    primaryData,
    tableData,
    endpoint,
    // These props are not used in the render function, but are destructured
    // so they are not included in the `tableProps` variable.
    // eslint-disable-next-line no-unused-vars
    itemIdField,
    // eslint-disable-next-line no-unused-vars
    tableQuery,
    compareBy,
    compareParam,
    searchBy,
    labels = {},
    ...tableProps
  } = props;

  // Pull these props out separately because they need to be included in tableProps.
  const {
    query,
    columnPrefsKey
  } = props;
  const {
    items,
    query: reportQuery
  } = tableData;
  const initialSelectedRows = query[compareParam] ? (0,external_wc_navigation_namespaceObject.getIdsFromQuery)(query[compareBy]) : [];
  const [selectedRows, setSelectedRows] = (0,external_wp_element_namespaceObject.useState)(initialSelectedRows);
  const scrollPointRef = (0,external_wp_element_namespaceObject.useRef)(null);
  const {
    updateUserPreferences,
    ...userData
  } = (0,external_wc_data_namespaceObject.useUserPreferences)();

  // Bail early if we've encountered an error.
  const isError = tableData.isError || primaryData.isError;
  if (isError) {
    return (0,external_React_namespaceObject.createElement)(report_error, {
      isError: true
    });
  }
  let userPrefColumns = [];
  if (columnPrefsKey) {
    userPrefColumns = userData && userData[columnPrefsKey] ? userData[columnPrefsKey] : userPrefColumns;
  }
  const onPageChange = (newPage, source) => {
    scrollPointRef.current.scrollIntoView();
    const tableElement = scrollPointRef.current.nextSibling.querySelector('.woocommerce-table__table');
    const focusableElements = external_wp_dom_namespaceObject.focus.focusable.find(tableElement);
    if (focusableElements.length) {
      focusableElements[0].focus();
    }
  };
  const onSort = (key, direction) => {
    (0,external_wc_navigation_namespaceObject.onQueryChange)('sort')(key, direction);
    const eventProps = {
      report: endpoint,
      column: key,
      direction
    };
  };
  const filterShownHeaders = (headers, hiddenKeys) => {
    // If no user preferences, set visibilty based on column default.
    if (!hiddenKeys) {
      return headers.map(header => ({
        ...header,
        visible: header.required || !header.hiddenByDefault
      }));
    }

    // Set visibilty based on user preferences.
    return headers.map(header => ({
      ...header,
      visible: header.required || !hiddenKeys.includes(header.key)
    }));
  };
  const applyTableFilters = (data, totals, totalResults) => {
    const summary = getSummary ? getSummary(totals, totalResults) : null;

    /**
     * Filter report table for the CSV download.
     *
     * Enables manipulation of data used to create the report CSV.
     *
     * @param {Object} reportTableData - data used to create the table.
     * @param {string} reportTableData.endpoint - table api endpoint.
     * @param {Array} reportTableData.headers - table headers data.
     * @param {Array} reportTableData.rows - table rows data.
     * @param {Object} reportTableData.totals - total aggregates for request.
     * @param {Array} reportTableData.summary - summary numbers data.
     * @param {Object} reportTableData.items - response from api requerst.
     */
    return (0,external_wp_hooks_namespaceObject.applyFilters)(TABLE_FILTER, {
      endpoint,
      headers: getHeadersContent(),
      rows: getRowsContent(data),
      totals,
      summary,
      items
    });
  };
  const onClickDownload = () => {
    const {
      createNotice,
      startExport,
      title
    } = props;
    const params = Object.assign({}, query);
    const {
      data,
      totalResults
    } = items;
    let downloadType = 'browser';

    // Delete unnecessary items from filename.
    delete params.extended_info;
    if (params.search) {
      delete params[searchBy];
    }
    if (data && data.length === totalResults) {
      const {
        headers,
        rows
      } = applyTableFilters(data, totalResults);
      (0,external_wc_csvExport_namespaceObject.downloadCSVFile)((0,external_wc_csvExport_namespaceObject.generateCSVFileName)(title, params), (0,external_wc_csvExport_namespaceObject.generateCSVDataFromTable)(headers, rows));
    } else {
      downloadType = 'email';
      startExport(endpoint, reportQuery).then(() => createNotice('success', (0,external_wp_i18n_namespaceObject.sprintf)( /* translators: %s = type of report */
      (0,external_wp_i18n_namespaceObject.__)('Your %s Report will be emailed to you.', 'woocommerce-admin'), title))).catch(error => createNotice('error', error.message || (0,external_wp_i18n_namespaceObject.sprintf)( /* translators: %s = type of report */
      (0,external_wp_i18n_namespaceObject.__)('There was a problem exporting your %s Report. Please try again.', 'woocommerce-admin'), title)));
    }
  };
  const onCompare = () => {
    if (compareBy) {
      (0,external_wc_navigation_namespaceObject.onQueryChange)('compare')(compareBy, compareParam, selectedRows.join(','));
    }
  };
  const onSearchChange = values => {
    const {
      baseSearchQuery
    } = props;
    // A comma is used as a separator between search terms, so we want to escape
    // any comma they contain.
    const searchTerms = values.map(v => v.label.replace(',', '%2C'));
    if (searchTerms.length) {
      (0,external_wc_navigation_namespaceObject.updateQueryString)({
        filter: undefined,
        [compareParam]: undefined,
        [searchBy]: undefined,
        ...baseSearchQuery,
        search: (0,external_lodash_namespaceObject.uniq)(searchTerms).join(',')
      });
    } else {
      (0,external_wc_navigation_namespaceObject.updateQueryString)({
        search: undefined
      });
    }
  };
  const selectAllRows = checked => {
    const {
      ids
    } = props;
    setSelectedRows(checked ? ids : []);
  };
  const selectRow = (i, checked) => {
    const {
      ids
    } = props;
    if (checked) {
      setSelectedRows((0,external_lodash_namespaceObject.uniq)([ids[i], ...selectedRows]));
    } else {
      const index = selectedRows.indexOf(ids[i]);
      setSelectedRows([...selectedRows.slice(0, index), ...selectedRows.slice(index + 1)]);
    }
  };
  const getCheckbox = i => {
    const {
      ids = []
    } = props;
    const isChecked = selectedRows.indexOf(ids[i]) !== -1;
    return {
      display: (0,external_React_namespaceObject.createElement)(external_wp_components_namespaceObject.CheckboxControl, {
        onChange: (0,external_lodash_namespaceObject.partial)(selectRow, i),
        checked: isChecked
      }),
      value: false
    };
  };
  const getAllCheckbox = () => {
    const {
      ids = []
    } = props;
    const hasData = ids.length > 0;
    const isAllChecked = hasData && ids.length === selectedRows.length;
    return {
      cellClassName: 'is-checkbox-column',
      key: 'compare',
      label: (0,external_React_namespaceObject.createElement)(external_wp_components_namespaceObject.CheckboxControl, {
        onChange: selectAllRows,
        "aria-label": (0,external_wp_i18n_namespaceObject.__)('Select All'),
        checked: isAllChecked,
        disabled: !hasData
      }),
      required: true
    };
  };
  const isLoading = isRequesting || tableData.isRequesting || primaryData.isRequesting;
  const totals = (0,external_lodash_namespaceObject.get)(primaryData, ['data', 'totals'], {});
  const totalResults = items.totalResults || 0;
  const downloadable = totalResults > 0;
  // Search words are in the query string, not the table query.
  const searchWords = (0,external_wc_navigation_namespaceObject.getSearchWords)(query);
  const searchedLabels = searchWords.map(v => ({
    key: v,
    label: v
  }));
  const {
    data
  } = items;
  const applyTableFiltersResult = applyTableFilters(data, totals, totalResults);
  let {
    headers,
    rows
  } = applyTableFiltersResult;
  const {
    summary
  } = applyTableFiltersResult;
  const onColumnsChange = (shownColumns, toggledColumn) => {
    const columns = headers.map(header => header.key);
    const hiddenColumns = columns.filter(column => !shownColumns.includes(column));
    if (columnPrefsKey) {
      const userDataFields = {
        [columnPrefsKey]: hiddenColumns
      };
      updateUserPreferences(userDataFields);
    }
  };

  // Add in selection for comparisons.
  if (compareBy) {
    rows = rows.map((row, i) => {
      return [getCheckbox(i), ...row];
    });
    headers = [getAllCheckbox(), ...headers];
  }

  // Hide any headers based on user prefs, if loaded.
  const filteredHeaders = filterShownHeaders(headers, userPrefColumns);
  return (0,external_React_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_React_namespaceObject.createElement)("div", {
    className: "woocommerce-report-table__scroll-point",
    ref: scrollPointRef,
    "aria-hidden": true
  }), (0,external_React_namespaceObject.createElement)(external_wc_components_namespaceObject.TableCard, {
    className: ('woocommerce-report-table', 'woocommerce-report-table-' + endpoint.replace('/', '-')),
    hasSearch: !!searchBy,
    actions: [compareBy && (0,external_React_namespaceObject.createElement)(external_wc_components_namespaceObject.CompareButton, {
      key: "compare",
      className: "woocommerce-table__compare",
      count: selectedRows.length,
      helpText: labels.helpText || (0,external_wp_i18n_namespaceObject.__)('Check at least two items below to compare', 'woocommerce-admin'),
      onClick: onCompare,
      disabled: !downloadable
    }, labels.compareButton || (0,external_wp_i18n_namespaceObject.__)('Compare', 'woocommerce-admin')), searchBy && (0,external_React_namespaceObject.createElement)(external_wc_components_namespaceObject.Search, {
      allowFreeTextSearch: true,
      inlineTags: true,
      key: "search",
      onChange: onSearchChange,
      placeholder: labels.placeholder || (0,external_wp_i18n_namespaceObject.__)('Search by item name', 'woocommerce-admin'),
      selected: searchedLabels,
      showClearButton: true,
      type: searchBy,
      disabled: !downloadable
    }), downloadable && (0,external_React_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
      key: "download",
      className: "woocommerce-table__download-button",
      disabled: isLoading,
      onClick: onClickDownload
    }, (0,external_React_namespaceObject.createElement)(download_icon, null), (0,external_React_namespaceObject.createElement)("span", {
      className: "woocommerce-table__download-button__label"
    }, labels.downloadButton || (0,external_wp_i18n_namespaceObject.__)('Download', 'woocommerce-admin')))],
    headers: filteredHeaders,
    isLoading: isLoading,
    onQueryChange: external_wc_navigation_namespaceObject.onQueryChange,
    onColumnsChange: onColumnsChange,
    onSort: onSort,
    onPageChange: onPageChange,
    rows: rows,
    rowsPerPage: parseInt(reportQuery.per_page, 10) || external_wc_data_namespaceObject.QUERY_DEFAULTS.pageSize,
    summary: summary,
    totalRows: totalResults,
    ...tableProps
  }));
};
ReportTable.propTypes = {
  /**
   * Pass in query parameters to be included in the path when onSearch creates a new url.
   */
  baseSearchQuery: (prop_types_default()).object,
  /**
   * The string to use as a query parameter when comparing row items.
   */
  compareBy: (prop_types_default()).string,
  /**
   * Url query parameter compare function operates on
   */
  compareParam: (prop_types_default()).string,
  /**
   * The key for user preferences settings for column visibility.
   */
  columnPrefsKey: (prop_types_default()).string,
  /**
   * The endpoint to use in API calls to populate the table rows and summary.
   * For example, if `taxes` is provided, data will be fetched from the report
   * `taxes` endpoint (ie: `/wc-analytics/reports/taxes` and `/wc/v4/reports/taxes/stats`).
   * If the provided endpoint doesn't exist, an error will be shown to the user
   * with `ReportError`.
   */
  endpoint: (prop_types_default()).string,
  /**
   * A function that returns the headers object to build the table.
   */
  getHeadersContent: (prop_types_default()).func.isRequired,
  /**
   * A function that returns the rows array to build the table.
   */
  getRowsContent: (prop_types_default()).func.isRequired,
  /**
   * A function that returns the summary object to build the table.
   */
  getSummary: (prop_types_default()).func,
  /**
   * The name of the property in the item object which contains the id.
   */
  itemIdField: (prop_types_default()).string,
  /**
   * Custom labels for table header actions.
   */
  labels: prop_types_default().shape({
    compareButton: (prop_types_default()).string,
    downloadButton: (prop_types_default()).string,
    helpText: (prop_types_default()).string,
    placeholder: (prop_types_default()).string
  }),
  /**
   * Primary data of that report. If it's not provided, it will be automatically
   * loaded via the provided `endpoint`.
   */
  primaryData: (prop_types_default()).object,
  /**
   * The string to use as a query parameter when searching row items.
   */
  searchBy: (prop_types_default()).string,
  /**
   * List of fields used for summary numbers. (Reduces queries)
   */
  summaryFields: prop_types_default().arrayOf((prop_types_default()).string),
  /**
   * Table data of that report. If it's not provided, it will be automatically
   * loaded via the provided `endpoint`.
   */
  tableData: (prop_types_default()).object.isRequired,
  /**
   * Properties to be added to the query sent to the report table endpoint.
   */
  tableQuery: (prop_types_default()).object,
  /**
   * String to display as the title of the table.
   */
  title: (prop_types_default()).string.isRequired
};
ReportTable.defaultProps = {
  primaryData: {},
  tableData: {
    items: {
      data: [],
      totalResults: 0
    },
    query: {}
  },
  tableQuery: {},
  compareParam: 'filter',
  downloadable: false,
  onSearch: external_lodash_namespaceObject.noop,
  baseSearchQuery: {}
};
const EMPTY_ARRAY = (/* unused pure expression or super */ null && ([]));
const EMPTY_OBJECT = {};
/* harmony default export */ var report_table = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_data_namespaceObject.withSelect)((select, props) => {
  const {
    endpoint,
    getSummary,
    isRequesting,
    itemIdField,
    query,
    tableData,
    tableQuery,
    filters,
    advancedFilters,
    summaryFields,
    extendedItemsStoreName
  } = props;
  const {
    woocommerce_default_date_range: defaultDateRange
  } = select(external_wc_data_namespaceObject.SETTINGS_STORE_NAME).getSetting('wc_admin', 'wcAdminSettings');
  if (isRequesting) {
    return EMPTY_OBJECT;
  }

  /* eslint @wordpress/no-unused-vars-before-return: "off" */
  const reportStoreSelector = select(external_wc_data_namespaceObject.REPORTS_STORE_NAME);
  const extendedStoreSelector = extendedItemsStoreName ? select(extendedItemsStoreName) : null;
  const primaryData = getSummary ? (0,external_wc_data_namespaceObject.getReportChartData)({
    endpoint: endpoint,
    dataType: 'primary',
    query,
    // Hint: Leave this param for backwards compatibility WC-Admin lt 2.6.
    select,
    selector: reportStoreSelector,
    filters,
    advancedFilters,
    defaultDateRange,
    fields: summaryFields
  }) : EMPTY_OBJECT;
  const queriedTableData = tableData || (0,external_wc_data_namespaceObject.getReportTableData)({
    endpoint,
    query,
    // Hint: Leave this param for backwards compatibility WC-Admin lt 2.6.
    select,
    selector: reportStoreSelector,
    tableQuery,
    filters,
    advancedFilters,
    defaultDateRange
  });
  return {
    primaryData,
    tableData: queriedTableData,
    query
  };
}), (0,external_wp_data_namespaceObject.withDispatch)(dispatch => {
  const {
    startExport
  } = dispatch(external_wc_data_namespaceObject.EXPORT_STORE_NAME);
  const {
    createNotice
  } = dispatch('core/notices');
  return {
    createNotice,
    startExport
  };
}))(ReportTable));
;// CONCATENATED MODULE: ./resources/js/admin/analytics/report/revenue/index.js

/**
 * External dependencies
 */







/**
 * WooCommerce dependencies
 */






/**
 * SomewhereWarm dependencies
 */


const adminSettings = (0,external_wc_wcSettings_namespaceObject.getSetting)('admin', {});
const stockStatuses = typeof adminSettings === 'object' && adminSettings.length !== 1 && adminSettings.stockStatuses ? adminSettings.stockStatuses : (0,external_wc_wcSettings_namespaceObject.getSetting)('stockStatuses', {});
class BundlesReportTable extends external_wp_element_namespaceObject.Component {
  constructor() {
    super();
    this.getHeadersContent = this.getHeadersContent.bind(this);
    this.getRowsContent = this.getRowsContent.bind(this);
    this.getSummary = this.getSummary.bind(this);
  }
  getHeadersContent() {
    return [{
      label: (0,external_wp_i18n_namespaceObject.__)('Bundle Title', 'woocommerce-product-bundles'),
      key: 'product_name',
      required: true,
      isLeftAligned: true,
      isSortable: true
    }, {
      label: (0,external_wp_i18n_namespaceObject.__)('SKU', 'woocommerce-product-bundles'),
      key: 'sku',
      hiddenByDefault: true,
      isSortable: true
    }, {
      label: (0,external_wp_i18n_namespaceObject.__)('Bundles Sold', 'woocommerce-product-bundles'),
      key: 'items_sold',
      required: true,
      defaultSort: true,
      isSortable: true,
      isNumeric: true
    }, {
      label: (0,external_wp_i18n_namespaceObject.__)('Bundled Items Sold', 'woocommerce-product-bundles'),
      key: 'bundled_items_sold',
      required: true,
      defaultSort: true,
      isSortable: true,
      isNumeric: true
    }, {
      label: (0,external_wp_i18n_namespaceObject.__)('Net Sales', 'woocommerce-product-bundles'),
      screenReaderLabel: (0,external_wp_i18n_namespaceObject.__)('Net Sales', 'woocommerce-product-bundles'),
      key: 'net_revenue',
      required: true,
      isSortable: true,
      isNumeric: true
    }, {
      label: (0,external_wp_i18n_namespaceObject.__)('Orders', 'woocommerce-product-bundles'),
      key: 'orders_count',
      isSortable: true,
      isNumeric: true
    }, {
      label: (0,external_wp_i18n_namespaceObject.__)('Status', 'woocommerce-product-bundles'),
      key: 'stock_status'
    }].filter(Boolean);
  }
  getRowsContent(data = []) {
    const {
      query
    } = this.props;
    const persistedQuery = (0,external_wc_navigation_namespaceObject.getPersistedQuery)(query);
    const {
      render: renderCurrency,
      formatDecimal: getCurrencyFormatDecimal,
      getCurrencyConfig
    } = this.context;
    const currency = getCurrencyConfig();
    return (0,external_lodash_namespaceObject.map)(data, row => {
      const {
        product_id: productId,
        items_sold: itemsSold,
        bundled_items_sold: bundledItemsSold,
        net_revenue: netRevenue,
        orders_count: ordersCount
      } = row;
      const extendedInfo = row.extended_info || {};
      const {
        sku,
        stock_status: extendedInfoStockStatus
      } = extendedInfo;
      const name = (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(extendedInfo.name);
      const ordersLink = (0,external_wc_navigation_namespaceObject.getNewPath)(persistedQuery, '/analytics/orders', {
        filter: 'advanced',
        product_includes: productId
      });
      const productDetailLink = (0,external_wc_navigation_namespaceObject.getNewPath)(persistedQuery, '/analytics/products', {
        filter: 'single_product',
        products: productId
      });
      const stockStatus = 'insufficientstock' === extendedInfoStockStatus ? (0,external_wp_i18n_namespaceObject.__)('Insufficient Stock', 'woocommerce-product-bundles') : stockStatuses[extendedInfoStockStatus];
      return [{
        display: (0,external_React_namespaceObject.createElement)(external_wc_components_namespaceObject.Link, {
          href: productDetailLink,
          type: "wc-admin"
        }, name),
        value: name
      }, {
        display: sku,
        value: sku
      }, {
        display: (0,external_wc_number_namespaceObject.formatValue)(currency, 'number', itemsSold),
        value: itemsSold
      }, {
        display: (0,external_wc_number_namespaceObject.formatValue)(currency, 'number', bundledItemsSold),
        value: bundledItemsSold
      }, {
        display: renderCurrency(netRevenue),
        value: getCurrencyFormatDecimal(netRevenue)
      }, {
        display: (0,external_React_namespaceObject.createElement)(external_wc_components_namespaceObject.Link, {
          href: ordersLink,
          type: "wc-admin"
        }, ordersCount),
        value: ordersCount
      }, {
        display: stockStatus,
        value: extendedInfoStockStatus
      }].filter(Boolean);
    });
  }
  getSummary(totals) {
    const {
      products_count: productsCount = 0,
      items_sold: itemsSold = 0,
      bundled_items_sold: bundledItemsSold = 0,
      net_revenue: netRevenue = 0,
      orders_count: ordersCount = 0
    } = totals;
    const {
      formatAmount,
      getCurrencyConfig
    } = this.context;
    const currency = getCurrencyConfig();
    return [{
      label: (0,external_wp_i18n_namespaceObject._n)('bundle', 'bundles', productsCount, 'woocommerce-product-bundles'),
      value: (0,external_wc_number_namespaceObject.formatValue)(currency, 'number', productsCount)
    }, {
      label: (0,external_wp_i18n_namespaceObject._n)('sale', 'sales', itemsSold, 'woocommerce-product-bundles'),
      value: (0,external_wc_number_namespaceObject.formatValue)(currency, 'number', itemsSold)
    }, {
      label: (0,external_wp_i18n_namespaceObject._n)('bundled item sold', 'bundled items sold', bundledItemsSold, 'woocommerce-product-bundles'),
      value: (0,external_wc_number_namespaceObject.formatValue)(currency, 'number', bundledItemsSold)
    }, {
      label: (0,external_wp_i18n_namespaceObject.__)('net sales', 'woocommerce-product-bundles'),
      value: formatAmount(netRevenue)
    }, {
      label: (0,external_wp_i18n_namespaceObject._n)('order', 'orders', ordersCount, 'woocommerce-product-bundles'),
      value: (0,external_wc_number_namespaceObject.formatValue)(currency, 'number', ordersCount)
    }];
  }
  render() {
    const {
      filters,
      isRequesting,
      hideCompare,
      query
    } = this.props;
    return (0,external_React_namespaceObject.createElement)(report_table, {
      endpoint: "bundles",
      getHeadersContent: this.getHeadersContent,
      getRowsContent: this.getRowsContent,
      getSummary: this.getSummary,
      summaryFields: ['products_count', 'items_sold', 'bundled_items_sold', 'net_revenue', 'orders_count'],
      itemIdField: "product_id",
      isRequesting: isRequesting,
      query: query,
      compareBy: hideCompare ? undefined : 'bundles',
      tableQuery: {
        orderby: query.orderby || 'items_sold',
        order: query.order || 'desc',
        extended_info: true,
        segmentby: query.segmentby
      },
      title: (0,external_wp_i18n_namespaceObject.__)('Bundles', 'woocommerce-product-bundles'),
      columnPrefsKey: "bundles_report_columns",
      filters: filters
    });
  }
}
BundlesReportTable.contextType = CurrencyContext;
/* harmony default export */ var revenue = (BundlesReportTable);
;// CONCATENATED MODULE: ./resources/js/admin/analytics/report/stock/index.js

/**
 * External dependencies
 */





/**
 * WooCommerce dependencies
 */




/**
 * SomewhereWarm dependencies
 */


const stock_adminSettings = (0,external_wc_wcSettings_namespaceObject.getSetting)('admin', {});
const stock_stockStatuses = typeof stock_adminSettings === 'object' && stock_adminSettings.length !== 1 && stock_adminSettings.stockStatuses ? stock_adminSettings.stockStatuses : (0,external_wc_wcSettings_namespaceObject.getSetting)('stockStatuses', {});
class BundlesStockReportTable extends external_wp_element_namespaceObject.Component {
  constructor() {
    super();
    this.getHeadersContent = this.getHeadersContent.bind(this);
    this.getRowsContent = this.getRowsContent.bind(this);
  }
  getHeadersContent() {
    return [{
      label: (0,external_wp_i18n_namespaceObject.__)('Bundle', 'woocommerce-product-bundles'),
      key: 'bundle_name',
      required: true,
      defaultSort: true,
      isSortable: true,
      isLeftAligned: true
    }, {
      label: (0,external_wp_i18n_namespaceObject.__)('Bundled Product', 'woocommerce-product-bundles'),
      key: 'product_name',
      isSortable: true
    }, {
      label: (0,external_wp_i18n_namespaceObject.__)('Stock Status', 'woocommerce-product-bundles'),
      key: 'stock_status'
    }, {
      label: (0,external_wp_i18n_namespaceObject.__)('Remaining Stock', 'woocommerce-product-bundles'),
      key: 'stock_quantity',
      isSortable: false
    }, {
      label: (0,external_wp_i18n_namespaceObject.__)('Required Stock', 'woocommerce-product-bundles'),
      key: 'units_required',
      isSortable: true,
      required: true,
      isNumeric: true
    }].filter(Boolean);
  }
  getRowsContent(data = []) {
    const {
      query
    } = this.props;
    const {
      render: renderCurrency,
      formatDecimal: getCurrencyFormatDecimal,
      getCurrencyConfig
    } = this.context;
    const currency = getCurrencyConfig();
    return (0,external_lodash_namespaceObject.map)(data, row => {
      const {
        bundle_id: bundleId,
        product_id: productId
      } = row;
      const extendedInfo = row.extended_info || {};
      const {
        sku,
        name: extendedInfoName,
        manage_stock: extendedInfoManageStock,
        stock_quantity: extendedInfoStockQuantity,
        bundle_name: extendedInfoBundleName,
        units_required: extendedInfoUnitsRequired,
        stock_status: extendedInfoStockStatus
      } = extendedInfo;

      // Bundle.
      const name = (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(extendedInfoBundleName);
      const productDetailLink = (0,external_wc_wcSettings_namespaceObject.getAdminLink)('post.php?post=' + bundleId + '&action=edit');

      // Bundled product.
      const bundledName = (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(extendedInfoName);
      const bundledDetailsLink = (0,external_wc_wcSettings_namespaceObject.getAdminLink)('post.php?post=' + productId + '&action=edit');
      const stockStatus = stock_stockStatuses[extendedInfoStockStatus];
      return [{
        display: (0,external_React_namespaceObject.createElement)(external_wc_components_namespaceObject.Link, {
          href: productDetailLink,
          type: "wp-admin"
        }, name),
        value: name
      }, {
        display: (0,external_React_namespaceObject.createElement)(external_wc_components_namespaceObject.Link, {
          href: bundledDetailsLink,
          type: "wp-admin"
        }, bundledName),
        value: bundledName
      }, {
        display: stockStatus,
        value: stock_stockStatuses[extendedInfoStockStatus]
      }, {
        display: extendedInfoManageStock ? (0,external_wc_number_namespaceObject.formatValue)(this.context.getCurrencyConfig(), 'number', extendedInfoStockQuantity) : (0,external_wp_i18n_namespaceObject.__)('N/A', 'woocommerce-product-bundles'),
        value: extendedInfoStockQuantity
      }, {
        display: (0,external_wc_number_namespaceObject.formatValue)(currency, 'number', extendedInfoUnitsRequired),
        value: extendedInfoUnitsRequired
      }].filter(Boolean);
    });
  }
  render() {
    const {
      filters,
      isRequesting,
      hideCompare,
      query
    } = this.props;
    return (0,external_React_namespaceObject.createElement)(report_table, {
      endpoint: "bundles/stock",
      getHeadersContent: this.getHeadersContent,
      getRowsContent: this.getRowsContent,
      itemIdField: "product_id",
      isRequesting: isRequesting,
      query: query,
      compareBy: hideCompare ? undefined : 'bundles',
      tableQuery: {
        orderby: query.orderby || 'product_name',
        order: query.order || 'asc',
        extended_info: true,
        segmentby: query.segmentby
      },
      title: (0,external_wp_i18n_namespaceObject.__)('Stock', 'woocommerce-product-bundles'),
      columnPrefsKey: "bundles_stock_report_columns",
      filters: filters
    });
  }
}
BundlesStockReportTable.contextType = CurrencyContext;
/* harmony default export */ var stock = (BundlesStockReportTable);
;// CONCATENATED MODULE: ./resources/js/admin/analytics/report/revenue/config.js
/**
 * External dependencies.
 */



/**
 * WooCommerce dependencies.
 */


/**
 * SomewhereWarm dependencies.
 */

const BUNDLES_REPORT_CHARTS_FILTER = 'woocommerce_admin_bundles_report_charts';
const BUNDLES_REPORT_FILTERS_FILTER = 'woocommerce_admin_products_report_filters';
const getProductLabels = getRequestByIdString(external_wc_data_namespaceObject.NAMESPACE + '/products', product => ({
  key: product.id,
  label: product.name
}));

/**
 * Exports.
 */
const showDatePicker = true;
const charts = (0,external_wp_hooks_namespaceObject.applyFilters)(BUNDLES_REPORT_CHARTS_FILTER, [{
  key: 'items_sold',
  label: (0,external_wp_i18n_namespaceObject.__)('Bundles Sold', 'woocommerce-product-bundles'),
  order: 'desc',
  orderby: 'items_sold',
  type: 'number'
}, {
  key: 'bundled_items_sold',
  label: (0,external_wp_i18n_namespaceObject.__)('Bundled Items Sold', 'woocommerce-product-bundles'),
  order: 'desc',
  orderby: 'bundled_items_sold',
  type: 'number'
}, {
  key: 'net_revenue',
  label: (0,external_wp_i18n_namespaceObject.__)('Net Sales', 'woocommerce-product-bundles'),
  order: 'desc',
  orderby: 'net_revenue',
  type: 'currency'
}, {
  key: 'orders_count',
  label: (0,external_wp_i18n_namespaceObject.__)('Orders', 'woocommerce-product-bundles'),
  order: 'desc',
  orderby: 'orders_count',
  type: 'number'
}]);
const filters = [{
  label: (0,external_wp_i18n_namespaceObject.__)('View', 'woocommerce-product-bundles'),
  staticParams: ['filter', 'products'],
  param: 'section',
  showFilters: () => true,
  filters: [{
    label: (0,external_wp_i18n_namespaceObject.__)('Revenue', 'woocommerce-product-bundles'),
    value: 'all'
  }, {
    label: (0,external_wp_i18n_namespaceObject.__)('Stock', 'woocommerce-product-bundles'),
    value: 'stock'
  }]
}, {
  label: (0,external_wp_i18n_namespaceObject.__)('Show', 'woocommerce-product-bundles'),
  staticParams: ['section', 'paged', 'per_page'],
  param: 'filter',
  showFilters: () => true,
  filters: [{
    label: (0,external_wp_i18n_namespaceObject.__)('All Bundles', 'woocommerce-product-bundles'),
    value: 'all'
  }, {
    label: (0,external_wp_i18n_namespaceObject.__)('Single Bundle', 'woocommerce-product-bundles'),
    value: 'select_bundle',
    subFilters: [{
      component: 'Search',
      value: 'single_product',
      path: ['select_bundle'],
      settings: {
        type: 'products',
        param: 'products',
        getLabels: getProductLabels,
        labels: {
          placeholder: (0,external_wp_i18n_namespaceObject.__)('Type to search for a bundle', 'woocommerce-product-bundles'),
          button: (0,external_wp_i18n_namespaceObject.__)('Single Bundle', 'woocommerce-product-bundles')
        }
      }
    }]
  }]
}];
;// CONCATENATED MODULE: ./resources/js/admin/analytics/report/index.js

/**
 * External dependencies
 */



/**
 * WooCommerce dependencies
 */



/**
 * SomewhereWarm dependencies.
 */




/**
 * Internal dependencies.
 */




const manageStock = (0,external_wc_wcSettings_namespaceObject.getSetting)('manageStock', 'no');
class Report extends external_wp_element_namespaceObject.Component {
  getChartMeta() {
    const {
      query,
      isSingleProductView
    } = this.props;
    const isCompareView = false;
    const mode = 'time-comparison';
    const compareObject = 'bundles';
    /* translators: Number of Bundles */
    const label = (0,external_wp_i18n_namespaceObject.__)('%d bundles', 'woocommerce-product-bundles');
    return {
      itemsLabel: label,
      mode
    };
  }
  render() {
    const {
      itemsLabel,
      mode
    } = this.getChartMeta();
    const {
      path,
      query,
      isError,
      isRequesting
    } = this.props;
    if (isError) {
      return (0,external_React_namespaceObject.createElement)(ReportError, {
        isError: true
      });
    }
    const chartQuery = {
      ...query
    };
    const showDatePicker = query.section === 'stock' ? false : true;
    const main_content = query.section !== 'stock' ? (0,external_React_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_React_namespaceObject.createElement)(report_summary, {
      mode: mode,
      charts: charts,
      endpoint: "bundles",
      isRequesting: isRequesting,
      query: chartQuery,
      selectedChart: getSelectedChart(query.chart, charts),
      filters: filters
    }), (0,external_React_namespaceObject.createElement)(report_chart, {
      charts: charts,
      mode: mode,
      endpoint: "bundles",
      isRequesting: isRequesting,
      itemsLabel: itemsLabel,
      path: path,
      query: chartQuery,
      selectedChart: getSelectedChart(chartQuery.chart, charts),
      filters: filters
    }), (0,external_React_namespaceObject.createElement)(revenue, {
      isRequesting: isRequesting,
      hideCompare: true,
      query: query,
      filters: filters
    })) : (0,external_React_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_React_namespaceObject.createElement)("p", null, (0,external_wp_element_namespaceObject.createInterpolateElement)((0,external_wp_i18n_namespaceObject.__)('Use this report to identify Bundles with <strong>Insufficient Stock</strong>, and re-stock their contents.', 'woocommerce-product-bundles'), {
      strong: (0,external_React_namespaceObject.createElement)("strong", null)
    })), (0,external_React_namespaceObject.createElement)(stock, {
      isRequesting: isRequesting,
      hideCompare: true,
      query: query,
      filters: filters
    }));
    return (0,external_React_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_React_namespaceObject.createElement)(external_wc_components_namespaceObject.ReportFilters, {
      query: query,
      path: path,
      showDatePicker: false,
      filters: filters
    }), (0,external_React_namespaceObject.createElement)(external_wc_components_namespaceObject.ReportFilters, {
      query: query,
      path: path,
      showDatePicker: showDatePicker
    }), main_content);
  }
}
Report.propTypes = {
  path: (prop_types_default()).string.isRequired,
  query: (prop_types_default()).object.isRequired
};
/* harmony default export */ var report = (Report);
;// CONCATENATED MODULE: ./resources/js/admin/analytics/index.js
/**
 * External dependencies
 */




/**
 * Local imports
 */


/**
 * Use the 'woocommerce_admin_reports_list' filter to add a report page.
 */
(0,external_wp_hooks_namespaceObject.addFilter)('woocommerce_admin_reports_list', 'woocommerce-product-bundles', reports => {
  return [...reports, {
    report: 'bundles',
    title: (0,external_wp_i18n_namespaceObject._x)('Bundles', 'analytics report table', 'woocommerce-product-bundles'),
    component: report,
    navArgs: {
      id: 'wc-pb-bundles-analytics-report'
    }
  }];
});
}();
/******/ })()
;