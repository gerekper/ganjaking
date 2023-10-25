"use strict";

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

/*
 * textillate.js
 * http://jschr.github.com/textillate
 * MIT licensed
 *
 * Copyright (C) 2012-2013 Jordan Schroter
 */
(function ($) {
  "use strict";

  function isInEffect(effect) {
    return /In/.test(effect) || $.inArray(effect, $.fn.textillate.defaults.inEffects) >= 0;
  }

  ;

  function isOutEffect(effect) {
    return /Out/.test(effect) || $.inArray(effect, $.fn.textillate.defaults.outEffects) >= 0;
  }

  ;

  function stringToBoolean(str) {
    if (str !== "true" && str !== "false") return str;
    return str === "true";
  }

  ; // custom get data api method

  function getData(node) {
    var attrs = node.attributes || [],
        data = {};
    if (!attrs.length) return data;
    $.each(attrs, function (i, attr) {
      var nodeName = attr.nodeName.replace(/delayscale/, 'delayScale');

      if (/^data-in-*/.test(nodeName)) {
        data.in = data.in || {};
        data.in[nodeName.replace(/data-in-/, '')] = stringToBoolean(attr.nodeValue);
      } else if (/^data-out-*/.test(nodeName)) {
        data.out = data.out || {};
        data.out[nodeName.replace(/data-out-/, '')] = stringToBoolean(attr.nodeValue);
      } else if (/^data-*/.test(nodeName)) {
        data[nodeName.replace(/data-/, '')] = stringToBoolean(attr.nodeValue);
      }
    });
    return data;
  }

  function shuffle(o) {
    for (var j, x, i = o.length; i; j = parseInt(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x) {
      ;
    }

    return o;
  }

  function animate($t, effect, cb) {
    $t.addClass('animated ' + effect).css('visibility', 'visible').show();
    $t.one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
      $t.removeClass('animated ' + effect);
      cb && cb();
    });
  }

  function animateTokens($tokens, options, cb) {
    var that = this,
        count = $tokens.length;

    if (!count) {
      cb && cb();
      return;
    }

    if (options.shuffle) $tokens = shuffle($tokens);
    if (options.reverse) $tokens = $tokens.toArray().reverse();
    $.each($tokens, function (i, t) {
      var $token = $(t);

      function complete() {
        if (isInEffect(options.effect)) {
          if ("typeOut" === options.effect) {
            $token.css("display", "inline-block");
          } else {
            $token.css('visibility', 'visible');
          }
        } else if (isOutEffect(options.effect)) {
          if ("typeOut" === options.effect) {
            $token.css("display", "none");
          } else {
            $token.css('visibility', 'hidden');
          }
        }

        count -= 1;
        if (!count && cb) cb();
      }

      var delay = options.sync ? options.delay : options.delay * i * options.delayScale;
      $token.text() ? setTimeout(function () {
        //animate($token, options.effect, complete) 
        var $el;
        var name;
        var filter;
        var strip_width;
        $el = $token;
        name = options.effect;
        filter = complete;
        strip_width = 0;

        if ("clipIn" === name) {
          $el.css("width", "auto");
          strip_width = $el.width();
          $el.css("overflow", "hidden");
          $el.css("width", "0");
          $el.css("visibility", "visible");
          $el.animate({
            width: strip_width + .3 * parseFloat($el.css("font-size"))
          }, 1200, function () {
            setTimeout(function () {
              if (filter) {
                filter();
              }
            }, 100);
          });
        } else {
          if ("clipOut" === name) {
            $el.animate({
              width: "2px"
            }, 1200, function () {
              setTimeout(function () {
                if (filter) {
                  filter();
                }
              }, 100);
            });
          } else {
            if ("typeIn" === name) {
              $el.addClass("sp-title-animated " + name).show();
            } else {
              $el.addClass("sp-title-animated " + name).css("visibility", "visible").show();
            }
          }
        }

        if (!("typeIn" !== name && "typeOut" !== name || !jQuery("html").hasClass("ua-edge") && !jQuery("html").hasClass("ua-ie"))) {
          $el.removeClass("sp-title-animated " + name).css("visibility", "visible");

          if (filter) {
            filter();
          }
        }

        $el.one("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oAnimationEnd AnimationEnd", function () {
          $el.removeClass("sp-title-animated " + name);

          if (filter) {
            filter();
          }
        });
      }, delay) : complete();
    });
  }

  ;

  var Textillate = function Textillate(element, options) {
    var base = this,
        $element = $(element);

    base.init = function () {
      base.$texts = $element.find(options.selector);

      if (!base.$texts.length) {
        base.$texts = $('<ul class="texts"><li>' + $element.html() + '</li></ul>');
        $element.html(base.$texts);
      }

      base.$texts.hide();
      base.$current = $('<span class="sp-textillate">').html(base.$texts.find(':first-child').html()).prependTo($element);

      if (isInEffect(options.in.effect)) {
        base.$current.css('visibility', 'hidden');
      } else if (isOutEffect(options.out.effect)) {
        base.$current.css('visibility', 'visible');
      }

      base.setOptions(options);
      base.timeoutRun = null;
      setTimeout(function () {
        base.options.autoStart && base.start();
      }, base.options.initialDelay);
    };

    base.setOptions = function (options) {
      base.options = options;
    };

    base.triggerEvent = function (name) {
      var e = $.Event(name + '.tlt');
      $element.trigger(e, base);
      return e;
    };

    base.in = function (index, cb) {
      index = index || 0;
      var $elem = base.$texts.find(':nth-child(' + ((index || 0) + 1) + ')'),
          options = $.extend(true, {}, base.options, $elem.length ? getData($elem[0]) : {}),
          $tokens;
      var sparklineElement = base.$current.closest(".sp-animated-texts-wrapper");
      $elem.addClass('current');
      base.triggerEvent('inAnimationBegin');
      $element.attr('data-active', $elem.data('id'));

      if ("line" == base.options.length) {
        base.$current.html($elem.html()).lettering("lines");
      } else {
        base.$current.html($elem.html()).lettering("words");
      }
      /*		
      base.$current
        .html($elem.html())
        .lettering('words');
      */
      // split words to individual characters if token type is set to 'char'


      if (base.options.type == "char") {
        base.$current.find('[class^="word"]').css({
          'display': 'inline-block',
          // fix for poor ios performance
          '-webkit-transform': 'translate3d(0,0,0)',
          '-moz-transform': 'translate3d(0,0,0)',
          '-o-transform': 'translate3d(0,0,0)',
          'transform': 'translate3d(0,0,0)'
        }).each(function () {
          $(this).lettering();
        });
      }

      $tokens = base.$current.find('[class^="' + base.options.length + '"]').css("display", "inline-block");
      /*	
         $tokens = base.$current
           .find('[class^="' + base.options.type + '"]')
           .css('display', 'inline-block');
      */

      if (isInEffect(options.in.effect)) {
        if ("typeIn" === options.in.effect) {
          $tokens.css("display", "none");
        } else {
          $tokens.css("visibility", "hidden");
        } //$tokens.css('visibility', 'hidden');

      } else if (isOutEffect(options.in.effect)) {
        $tokens.css('visibility', 'visible');
      }

      if (!("typeIn" !== options.in.effect && "clipIn" !== options.in.effect || void 0 !== sparklineElement.attr("style") && -1 !== sparklineElement.attr("style").indexOf("width"))) {
        base.$current.closest(".sp-animated-texts-wrapper").css("width", "auto");
      }

      base.currentIndex = index;
      animateTokens($tokens, options.in, function () {
        base.triggerEvent('inAnimationEnd');
        if (options.in.callback) options.in.callback();
        if (cb) cb(base);
      });
    };

    base.out = function (cb) {
      var $elem = base.$texts.find(':nth-child(' + ((base.currentIndex || 0) + 1) + ')'),
          $tokens = base.$current.find('[class^="' + base.options.length + '"]'),
          options = $.extend(true, {}, base.options, $elem.length ? getData($elem[0]) : {});
      base.triggerEvent('outAnimationBegin');
      animateTokens($tokens, options.out, function () {
        $elem.removeClass('current');
        base.triggerEvent('outAnimationEnd');
        $element.removeAttr('data-active');
        if (options.out.callback) options.out.callback();
        if (cb) cb(base);
      });
    };

    base.start = function (index) {
      setTimeout(function () {
        base.triggerEvent('start');

        (function run(index) {
          base.in(index, function () {
            var length = base.$texts.children().length;
            index += 1;

            if (!base.options.loop && index >= length) {
              if (base.options.callback) base.options.callback();
              base.triggerEvent('end');
            } else {
              index = index % length;
              base.timeoutRun = setTimeout(function () {
                base.out(function () {
                  run(index);
                });
              }, base.options.minDisplayTime);
            }
          });
        })(index || 0);
      }, base.options.initialDelay);
    };

    base.stop = function () {
      if (base.timeoutRun) {
        clearInterval(base.timeoutRun);
        base.timeoutRun = null;
      }
    };

    base.init();
  };

  $.fn.textillate = function (settings, args) {
    return this.each(function () {
      var $this = $(this),
          data = $this.data('textillate'),
          options = $.extend(true, {}, $.fn.textillate.defaults, getData(this), _typeof(settings) == 'object' && settings);

      if (!data) {
        $this.data('textillate', data = new Textillate(this, options));
      } else if (typeof settings == 'string') {
        data[settings].apply(data, [].concat(args));
      } else {
        data.setOptions.call(data, options);
      }
    });
  };

  $.fn.textillate.defaults = {
    selector: '.texts',
    loop: true,
    minDisplayTime: 2000,
    initialDelay: 0,
    in: {
      effect: 'fadeInLeftBig',
      delayScale: 1.5,
      delay: 50,
      sync: false,
      reverse: false,
      shuffle: false,
      callback: function callback() {}
    },
    out: {
      effect: 'hinge',
      delayScale: 1.5,
      delay: 50,
      sync: false,
      reverse: false,
      shuffle: false,
      callback: function callback() {}
    },
    autoStart: true,
    inEffects: [],
    outEffects: ['hinge'],
    callback: function callback() {},
    type: 'char'
  };
})(jQuery);