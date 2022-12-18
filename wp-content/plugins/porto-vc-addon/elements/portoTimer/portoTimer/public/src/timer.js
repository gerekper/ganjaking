/* global vcv */
(function ($) {
  var porto_init_countdown = function($elements) {
    if (typeof $elements == 'undefined') {
        $elements = $('body');
    }
    $elements.find('.porto_countdown-dateAndTime').each(function() {
        if (typeof $(this).data('porto_countdown_initialized') != 'undefined' && $(this).data('porto_countdown_initialized')) {
            return;
        }
        var t = new Date($(this).attr('data-terminal-date')),
            tfrmt = $(this).attr('data-countformat'),
            labels_new = $(this).attr('data-labels'),
            new_labels = labels_new.split(","),
            labels_new_2 = $(this).attr('data-labels2'),
            new_labels_2 = labels_new_2.split(","),
            server_time = function() {
                return new Date($(this).data('time-now'));
            };
        
        var ticked = function (a){
            var count_amount = $(this).find('.porto_countdown-amount'),
                count_period = $(this).find('.porto_countdown-period'),
                tick_fontfamily     = $(this).data('tick-font-family'),
                count_amount_css    = '',
                count_amount_font   = '';
        }

        if ($(this).hasClass('porto-usrtz')){
            $(this).porto_countdown({labels: new_labels, labels1: new_labels_2, until : t, format: tfrmt, padZeroes:true,onTick:ticked});
        } else {
            $(this).porto_countdown({labels: new_labels, labels1: new_labels_2, until : t, format: tfrmt, padZeroes:true,onTick:ticked , serverSync:server_time});
        }
        $(this).data('porto_countdown_initialized', true);
    });
  }

  vcv.on('ready', function (action, id, options, tag) {
    var updateAttrs = ['count_style', 'datetime', 'porto_tz', 'countdown_opts', 'string_years', 'string_years2', 'string_months', 'string_months2', 'string_weeks', 'string_weeks2', 'string_days', 'string_days2', 'string_hours', 'string_hours2', 'string_minutes', 'string_minutes2', 'string_seconds', 'string_seconds2'],
      skipCounter = !$.fn.porto_countdown || (tag && tag !== 'portoTimer') || (action === 'merge') || (options && options.changedAttribute && updateAttrs.indexOf(options.changedAttribute) === -1),
      timerTimer = null;
    if (!skipCounter) {
      if (timerTimer) {
        clearTimeout(timerTimer);
      }
      timerTimer = setTimeout(function() {
        var cdate = new Date(),
          sdate = cdate.getTime() + parseFloat( js_porto_vars ? js_porto_vars.gmt_offset : porto_vc_vars.gmt_offset ) * 3600 * 1000;
        sdate = new Date( sdate ).toISOString().replace(/(.*)(20[0-9]{2}-[0-9]{2}-[0-9]{2})T([0-9]{2}:[0-9]{2}:[0-9]{2})(.*)/, '$2 $3');
        if (id) {
          $('#el-' + id).find('.porto_countdown-dateAndTime').removeData('porto_countdown_initialized').removeData('porto_countdown').removeClass('is-porto_countdown').data('time-now', sdate);
          porto_init_countdown($('#el-' + id))
        } else {
          $('.porto_countdown-dateAndTime').removeData('porto_countdown_initialized').removeData('porto_countdown').removeClass('is-porto_countdown').data('time-now', sdate);
          porto_init_countdown();
        }
      }, action ? 100 : 10);
    }
  })
})(window.jQuery)
