/* global vcv */
(function ($) {
  var portoInitStatCounter = function($elements) {
    if (typeof $elements == "undefined") {
        $elements = $("body");
    }

    var initCounter = function(obj) {
      if (typeof obj == 'undefined') {
          obj = this;
      }
      var $obj = jQuery(obj),
        endNum = parseFloat($obj.find('.stats-number').attr('data-counter-value')),
        Num = ($obj.find('.stats-number').attr('data-counter-value'))+' ',
        speed = parseInt($obj.find('.stats-number').attr('data-speed')),
        ID = $obj.find('.stats-number').attr('data-id'),
        sep = $obj.find('.stats-number').attr('data-separator'),
        dec = $obj.find('.stats-number').attr('data-decimal'),
        dec_count = Num.split(".");
      if (dec_count[1]) {
        dec_count = dec_count[1].length-1;
      } else {
        dec_count = 0;
      }
      var grouping = true;
      if (dec == "none") {
        dec = "";
      }
      if (sep == "none") {
        grouping = false;
      } else {
        grouping = true;
      }
      var settings = {
        useEasing : false,
        useGrouping : grouping,
        separator : sep,
        decimal : dec
      }
      var counter = new vcCountUp(ID, 0, endNum, dec_count, speed, settings),
        endTrigger = function() {
          if ($('#' + ID).next('.counter_suffix').length > 0) {
            $('#' + ID).next('.counter_suffix').css('display', 'inline');
          }
        };
      setTimeout(function(){
        counter.start(endTrigger);
      }, 500);
    };
    var $stats;
    if ($elements.hasClass('stats-block')) {
      $stats = $elements;
    } else {
      $stats = $elements.find( '.stats-block' );
    }

    if (window.theme && theme.intObs) {
        theme.intObs(jQuery.makeArray($stats), initCounter, -50);
    } else {
        $stats.each(function() {
            initCounter(this);
        });
    }
  }
  vcv.on('ready', function (action, id, options) {
    var updateAttrs = ['counter_value', 'counter_sep', 'counter_decimal', 'counter_prefix', 'counter_suffix', 'speed'],
      skipCounter = !window.vcCountUp || (action === 'merge') || (options && options.changedAttribute && updateAttrs.indexOf(options.changedAttribute) === -1);
    if (!skipCounter) {
      setTimeout(function() {
        if (id) {
          portoInitStatCounter($('#el-' + id))
        } else {
          portoInitStatCounter();
        }
      }, action ? 100 : 10);
    }
  })
})(window.jQuery)
