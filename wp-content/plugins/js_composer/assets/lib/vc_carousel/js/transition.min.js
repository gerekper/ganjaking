/*!
 * WPBakery Page Builder v6.0.0 (https://wpbakery.com)
 * Copyright 2011-2020 Michael M, WPBakery
 * License: Commercial. More details: http://go.wpbakery.com/licensing
 */

// jscs:disable
// jshint ignore: start

!function($){"use strict";$.fn.emulateTransitionEnd=function(duration){var called=!1,$el=this;return $(this).one($.support.transition.end,function(){called=!0}),setTimeout(function(){called||$($el).trigger($.support.transition.end)},duration),this},$(function(){$.support.transition=function(){var name,el=document.createElement("bootstrap"),transEndEventNames={WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd otransitionend",transition:"transitionend"};for(name in transEndEventNames)if(void 0!==el.style[name])return{end:transEndEventNames[name]}}()})}(window.jQuery);