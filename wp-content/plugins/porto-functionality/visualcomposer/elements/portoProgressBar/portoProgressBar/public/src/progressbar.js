/* global vcv */
(function ($) {
  'use strict'

  var portoVCProgressBar = function( $elements ) {
    if (typeof $elements == "undefined") {
        $elements = $(".porto-vc-progressbar");
    }
    $elements.each(function() {
      if (window.theme && theme.appear) {
        theme.appear(this, function() {
          var $el = $(this).find('.progress-bar'),
            obj = $el.get(0),
            delay = ($el.attr('data-appear-animation-delay') ? $el.attr('data-appear-animation-delay') : 0);
          if (delay) {
            obj.style.transitionDelay = delay + 'ms';
          }
          obj.style.width = $el.attr('data-appear-progress-animation');
          $el.find('.progress-bar-tooltip').css('transition-delay', Number(delay) + 500 + 'ms').css('opacity', 1);
        }, {
          accX: 0,
          accY: -120
        });
      }
    });
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
