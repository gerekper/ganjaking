/*!
 * WPBakery Page Builder v6.0.0 (https://wpbakery.com)
 * Copyright 2011-2024 Michael M, WPBakery
 * License: Commercial. More details: http://go.wpbakery.com/licensing
 */

// jscs:disable
// jshint ignore: start

!function($){"use strict";$.fn.vcImageZoom=function(){return this.each(function(){var $this=$(this),src=$this.data("vcZoom");$this.removeAttr("data-vc-zoom").wrap('<div class="vc-zoom-wrapper"></div>').parent().zoom({duration:500,url:src,onZoomIn:function(){$this.width()>$(this).width()&&$this.trigger("zoom.destroy").attr("data-vc-zoom","").unwrap().vcImageZoom()}})}),this},"function"!=typeof window.vc_image_zoom&&(window.vc_image_zoom=function(model_id){var selector="[data-vc-zoom]";$(selector=void 0!==model_id?'[data-model-id="'+model_id+'"] '+selector:selector).vcImageZoom()}),$(document).ready(function(){window.vc_iframe||vc_image_zoom()})}(jQuery);