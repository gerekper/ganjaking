/* global vcv */
(function ($) {
  'use strict'

  var portoVCProgressBar = function( $elements ) {
    if (typeof $elements == "undefined") {
        $elements = $(".porto-vc-progressbar");
    }
    if (window.theme && window.theme.intObs) {
      theme.intObs(jQuery.makeArray($elements), function() {
        var obj = this.get(0).querySelector('.progress-bar');
        if (!obj) {
          return;
        }
        var delay = obj.getAttribute('data-appear-animation-delay');
        if (delay) {
          obj.style.transitionDelay = delay + 'ms';
        }
        obj.style.width = obj.getAttribute('data-appear-progress-animation');
        var tooltipObj = obj.querySelector('.progress-bar-tooltip');
        if (tooltipObj) {
          tooltipObj.style.transitionDelay = Number(delay) + 500 + 'ms';
          tooltipObj.style.opacity = 1;
        }
      }, -80);
    }
  };

  vcv.on('ready', function (action, id, options) {
    var updateAttrs = ['percentage', 'show_percent', 'animation_delay'],
      skipCounter = (action === 'merge') || (options && options.changedAttribute && updateAttrs.indexOf(options.changedAttribute) === -1);
    if (!skipCounter) {
      setTimeout(function() {
        if (id) {
          var $el = $('#el-' + id);
          if ($el.length && $el.hasClass('porto-vc-progressbar')) {
            portoVCProgressBar();
          }
        } else {
          portoVCProgressBar();
        }
      }, action ? 300 : 10);
    }
  })
})(window.jQuery)
