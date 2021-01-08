/*global $, window, CanvasLoader, jQuery, alert, requestAnimationFrame, cancelAnimationFrame */
/*jslint browser:true, devel:true */

/*!
 * 360 degree Image Slider Fullscreen plugin v1.0.0
 * http://gaurav.jassal.me/lab
 *
 * Copyright 2013, gaurav@jassal.me
 * Dual licensed under the MIT or GPL Version 3 licenses.
 *
 */


(function($) {
  'use strict';
  $.ThreeSixtyFullscreen = function(el, options) {
    var plugin = this,
      $el = el,
      opts = options,
      $button = $('<a href=\'#\'>Fullscreen</a>'),
      isFullscreen = false,
      pfx = ['webkit', 'moz', 'ms', 'o', ''];

    $.fn.redraw = function() {
      $( this ).each( function() {
        var redraw = this.offsetHeight;
      } );
    };

    function exitHandler() {
      if ( $( '.wc360.threesixty' ).hasClass( 'fullscreen' ) ) {
        $( '.wc360.threesixty' ).removeClass( 'fullscreen' );
      } else {
        $( '.wc360.threesixty' ).addClass( 'fullscreen' ).redraw();
      }
    }

    if ( document.addEventListener ) {
      document.addEventListener('fullscreenchange', exitHandler, false);
      document.addEventListener('mozfullscreenchange', exitHandler, false);
      document.addEventListener('MSFullscreenChange', exitHandler, false);
      document.addEventListener('webkitfullscreenchange', exitHandler, false);
    }

    // Attach event to the plugin
    $button.on('click', function(event) {
      plugin.onClickHandler.apply(this, event);
    });

    /**
     * Set styles for the plugin interface.
     * @return {Object} this
     */
    plugin.setStyles = function() {
      $button.css({
        'z-index': 12,
        'display': 'block',
        'position': 'absolute',
        'width': '20px',
        'height': '20px',
        'text-indent': '-99999px',
        'right': '5px',
        'bottom': '5px',
        'background-position': '0px -20px'
      });
      $button.addClass( 'fullscreen-button' );
      return this;
    };

    plugin.RunPrefixMethod = function(obj, method) {
      var p = 0,
        m, t;
      while (p < pfx.length && !obj[m]) {
        m = method;
        if (pfx[p] === '') {
          m = m.substr(0, 1).toLowerCase() + m.substr(1);
        }
        m = pfx[p] + m;
        t = typeof obj[m];
        if (t !== 'undefined') {
          pfx = [pfx[p]];
          return (t === 'function' ? obj[m]() : obj[m]);
        }
        p++;
      }
    };
    /**
     * Initilize the fullscreen plugin
     * @param  {Object} opt override options
     */
    plugin.init = function() {
      plugin.setStyles();
      $el.prepend($button);
    };

    plugin.onClickHandler = function(e) {
      var elem;
      if (typeof $el.attr('id') !== 'undefined') {
        elem = document.getElementById($el.attr('id'));
      } else if (typeof $el.parent().attr('id') !== 'undefined') {
        elem = document.getElementById($el.parent().attr('id'));
      } else {
        return false;
      }

      plugin.toggleFullscreen(elem);
    };

    plugin.toggleButton = function() {
      if (isFullscreen) {
        $button.css({
          'background-position': '0px 0px'
        });
      } else {
        $button.css({
          'background-position': '0px -20px'
        });
      }
    };

    plugin.toggleFullscreen = function(elem) {
      if (plugin.RunPrefixMethod(document, 'FullScreen') || plugin.RunPrefixMethod(document, 'IsFullScreen')) {
        plugin.RunPrefixMethod(document, 'CancelFullScreen');
      }
      else {
        plugin.RunPrefixMethod(elem, 'RequestFullScreen');
      }
      plugin.toggleButton();
    };
    plugin.init();
  };
}(jQuery));
