"use strict";

window.Happy = window.Happy || {};

(function ($, Happy, w) {
  var $window = $(w);
  var widgets = skinWidgetToHide.widgets;
  $.each(widgets, function (i, widget) {
    $('#ha-widget-' + widget).parents('.ha-dashboard-widgets__item').hide();
  });
})(jQuery, Happy, window);