/*
 * ================== js/jquery.appearl.js ===================
 */

;( function( $, window, document, undefined ) {

  "use strict";

  var pluginName = "appearl",
      defaults = {
        offset: 0,
        insetOffset: '50%'
      },
      attributesMap = {
        'offset': 'offset',
        'inset-offset': 'insetOffset'
    },
    $window = $(window);

  // The actual plugin constructor
  function Plugin ( element, options ) {
      this.element   = element;
      this.$element  = $(element);
      this.settings  = $.extend( {}, defaults, options );

      // read attributes
      for ( var key in attributesMap ) {
        var value = attributesMap[ key ],
            dataAttr = this.$element.data( key );

        if ( dataAttr === undefined ) {
            continue;
        }

        this.settings[ value ] = dataAttr;
      }

      this.init();
  }

  // Avoid Plugin.prototype conflicts
  $.extend( Plugin.prototype, {
      init: function() {
        if ( typeof this.settings.offset === 'object' ) {
          this._offsetTop = this.settings.offset.top;
          this._offsetBottom = this.settings.offset.bottom;
        } else {
          this._offsetTop = this._offsetBottom = this.settings.offset;
        }

        // To check if the element is on viewport and set the offset 0 for them
        if ( this._isOnViewPort( this.$element) ) {
            this._offsetTop = this._offsetBottom = 0
        }

        this._appeared = false;
        this._lastScroll = 0;

        $window.on( 'scroll resize', this.update.bind( this ) );
        setTimeout( this.update.bind(this) );
      },

      update: function( event ) {
        var rect = this.element.getBoundingClientRect(),
        areaTop = this._parseOffset( this._offsetTop ),
        areaBottom = window.innerHeight - this._parseOffset( this._offsetBottom ),
        insetOffset = this._parseOffset( this.settings.insetOffset, true );

        if ( rect.top + insetOffset <= areaBottom && rect.bottom - insetOffset >= areaTop ) {
          !this._appeared && this.$element.trigger( 'appear', [{ from: ( this._lastScroll <= $window.scrollTop() ? 'bottom' : 'top' ) }] );
          this._appeared = true;
        } else if ( this._appeared ) {
          this.$element.trigger( 'disappear', [{ from: ( rect.top < areaTop ? 'top' : 'bottom' ) }] );
          this._appeared = false;
        }

        this._lastScroll = $window.scrollTop();
      },

      _parseOffset: function( value, inset ) {
        var percentage = typeof value === 'string' && value.indexOf( '%' ) !== -1;
        value = parseInt( value );

        return !percentage ? value : ( inset ? this.element.offsetHeight : window.innerHeight ) * value / 100;
      },

      _isOnViewPort: function( element ) {
        var bottomOffset = this.element.getBoundingClientRect().bottom;
        return bottomOffset <  window.innerHeight
      },
  } );

  $.fn[ pluginName ] = function( options ) {
      return this.each( function() {
          if ( !$.data( this, "plugin_" + pluginName ) ) {
              $.data( this, "plugin_" +
                  pluginName, new Plugin( this, options ) );
          }
      } );
  };




  /*!
   *
   * ================== js/plugins/tilt.jquery.js ===================
   **/
  (function (factory) {
      if (typeof define === 'function' && define.amd) {
          // AMD. Register as an anonymous module.
          define(['jquery'], factory);
      } else if (typeof module === 'object' && module.exports) {
          // Node/CommonJS
          module.exports = function( root, jQuery ) {
              if ( jQuery === undefined ) {
                  // require('jQuery') returns a factory that requires window to
                  // build a jQuery instance, we normalize how we use modules
                  // that require this pattern but the window provided is a noop
                  // if it's defined (how jquery works)
                  if ( typeof window !== 'undefined' ) {
                      jQuery = require('jquery');
                  }
                  else {
                      jQuery = require('jquery')(root);
                  }
              }
              factory(jQuery);
              return jQuery;
          };
      } else {
          // Browser globals
          factory(jQuery);
      }
  }(function ($) {
      $.fn.tilt = function (options) {

          /**
           * RequestAnimationFrame
           */
          const requestTick = function() {
              if (this.ticking) return;
              requestAnimationFrame(updateTransforms.bind(this));
              this.ticking = true;
          };

          /**
           * Bind mouse movement evens on instance
           */
          const bindEvents = function() {
              const _this = this;
              $(this).on('mousemove', mouseMove);
              $(this).on('mouseenter', mouseEnter);
              if (this.settings.reset) $(this).on('mouseleave', mouseLeave);
              if (this.settings.glare) $(window).on('resize', updateGlareSize.bind(_this));
          };

          /**
           * Set transition only on mouse leave and mouse enter so it doesn't influence mouse move transforms
           */
          const setTransition = function() {
              if (this.timeout !== undefined) clearTimeout(this.timeout);
              $(this).css({'transition': `${this.settings.speed}ms ${this.settings.easing}`});
              if(this.settings.glare) this.glareElement.css({'transition': `opacity ${this.settings.speed}ms ${this.settings.easing}`});
              this.timeout = setTimeout(() => {
                  $(this).css({'transition': ''});
                  if(this.settings.glare) this.glareElement.css({'transition': ''});
              }, this.settings.speed);
          };

          /**
           * When user mouse enters tilt element
           */
          const mouseEnter = function(event) {
              this.ticking = false;
              $(this).css({'will-change': 'transform'});
              setTransition.call(this);

              // Trigger change event
              $(this).trigger("tilt.mouseEnter");
          };

          /**
           * Return the x,y position of the mouse on the tilt element
           * @returns {{x: *, y: *}}
           */
          const getMousePositions = function(event) {
              if (typeof(event) === "undefined") {
                  event = {
                      pageX: $(this).offset().left + $(this).outerWidth() / 2,
                      pageY: $(this).offset().top + $(this).outerHeight() / 2
                  };
              }
              return {x: event.pageX, y: event.pageY};
          };

          /**
           * When user mouse moves over the tilt element
           */
          const mouseMove = function(event) {
              this.mousePositions = getMousePositions(event);
              requestTick.call(this);
          };

          /**
           * When user mouse leaves tilt element
           */
          const mouseLeave = function() {
              setTransition.call(this);
              this.reset = true;
              requestTick.call(this);

              // Trigger change event
              $(this).trigger("tilt.mouseLeave");
          };

          /**
           * Get tilt values
           *
           * @returns {{x: tilt value, y: tilt value}}
           */
          const getValues = function() {
              const width = $(this).outerWidth();
              const height = $(this).outerHeight();
              const left = $(this).offset().left;
              const top = $(this).offset().top;
              const percentageX = (this.mousePositions.x - left) / width;
              const percentageY = (this.mousePositions.y - top) / height;
              // x or y position inside instance / width of instance = percentage of position inside instance * the max tilt value
              const tiltX = ((this.settings.maxTilt / 2) - ((percentageX) * this.settings.maxTilt)).toFixed(2);
              const tiltY = (((percentageY) * this.settings.maxTilt) - (this.settings.maxTilt / 2)).toFixed(2);
              // angle
              const angle = Math.atan2(this.mousePositions.x - (left+width/2),- (this.mousePositions.y - (top+height/2)) )*(180/Math.PI);
              // Return x & y tilt values
              return {tiltX, tiltY, 'percentageX': percentageX * 100, 'percentageY': percentageY * 100, angle};
          };

          /**
           * Update tilt transforms on mousemove
           */
          const updateTransforms = function() {
              this.transforms = getValues.call(this);

              if (this.reset) {
                  this.reset = false;
                  $(this).css('transform', `perspective(${this.settings.perspective}px) rotateX(0deg) rotateY(0deg)`);

                  // Rotate glare if enabled
                  if (this.settings.glare){
                      this.glareElement.css('transform', `rotate(180deg) translate(-50%, -50%)`);
                      this.glareElement.css('opacity', `0`);
                  }

                  return;
              } else {
                  $(this).css('transform', `perspective(${this.settings.perspective}px) rotateX(${this.settings.disableAxis === 'x' ? 0 : this.transforms.tiltY}deg) rotateY(${this.settings.disableAxis === 'y' ? 0 : this.transforms.tiltX}deg) scale3d(${this.settings.scale},${this.settings.scale},${this.settings.scale})`);

                  // Rotate glare if enabled
                  if (this.settings.glare){
                      this.glareElement.css('transform', `rotate(${this.transforms.angle}deg) translate(-50%, -50%)`);
                      this.glareElement.css('opacity', `${this.transforms.percentageY * this.settings.maxGlare / 100}`);
                  }
              }

              // Trigger change event
              $(this).trigger("change", [this.transforms]);

              this.ticking = false;
          };

          /**
           * Prepare elements
           */
          const prepareGlare = function () {
              const glarePrerender = this.settings.glarePrerender;

              // If option pre-render is enabled we assume all html/css is present for an optimal glare effect.
              if (!glarePrerender)
              // Create glare element
                  $(this).append('<div class="js-tilt-glare"><div class="js-tilt-glare-inner"></div></div>');

              // Store glare selector if glare is enabled
              this.glareElementWrapper = $(this).find(".js-tilt-glare");
              this.glareElement = $(this).find(".js-tilt-glare-inner");

              // Remember? We assume all css is already set, so just return
              if (glarePrerender) return;

              // Abstracted re-usable glare styles
              const stretch = {
                  'position': 'absolute',
                  'top': '0',
                  'left': '0',
                  'width': '100%',
                  'height': '100%',
              };

              // Style glare wrapper
              this.glareElementWrapper.css(stretch).css({
                  'overflow': 'hidden',
                  'pointer-events': 'none',
              });

              // Style glare element
              this.glareElement.css({
                  'position': 'absolute',
                  'top': '50%',
                  'left': '50%',
                  'background-image': `linear-gradient(0deg, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 100%)`,
                  'width': `${$(this).outerWidth()*2}`,
                  'height': `${$(this).outerWidth()*2}`,
                  'transform': 'rotate(180deg) translate(-50%, -50%)',
                  'transform-origin': '0% 0%',
                  'opacity': '0',
              });

          };

          /**
           * Update glare on resize
           */
          const updateGlareSize = function () {
              this.glareElement.css({
                  'width': `${$(this).outerWidth()*2}`,
                  'height': `${$(this).outerWidth()*2}`,
              });
          };

          /**
           * Public methods
           */
          $.fn.tilt.destroy = function() {
              $(this).each(function () {
                  $(this).find('.js-tilt-glare').remove();
                  $(this).css({'will-change': '', 'transform': ''});
                  $(this).off('mousemove mouseenter mouseleave');
              });
          };

          $.fn.tilt.getValues = function() {
              const results = [];
              $(this).each(function () {
                  this.mousePositions = getMousePositions.call(this);
                  results.push(getValues.call(this));
              });
              return results;
          };

          $.fn.tilt.reset = function() {
              $(this).each(function () {
                  this.mousePositions = getMousePositions.call(this);
                  this.settings = $(this).data('settings');
                  mouseLeave.call(this);
                  setTimeout(() => {
                      this.reset = false;
                  }, this.settings.transition);
              });
          };

          /**
           * Loop every instance
           */
          return this.each(function () {

              /**
               * Default settings merged with user settings
               * Can be set trough data attributes or as parameter.
               * @type {*}
               */
              this.settings = $.extend({
                  maxTilt: $(this).is('[data-tilt-max]') ? $(this).data('tilt-max') : 20,
                  perspective: $(this).is('[data-tilt-perspective]') ? $(this).data('tilt-perspective') : 300,
                  easing: $(this).is('[data-tilt-easing]') ? $(this).data('tilt-easing') : 'cubic-bezier(.03,.98,.52,.99)',
                  scale: $(this).is('[data-tilt-scale]') ? $(this).data('tilt-scale') : '1',
                  speed: $(this).is('[data-tilt-speed]') ? $(this).data('tilt-speed') : '400',
                  transition: $(this).is('[data-tilt-transition]') ? $(this).data('tilt-transition') : true,
                  disableAxis: $(this).is('[data-tilt-disable-axis]') ? $(this).data('tilt-disable-axis') : null,
                  axis: $(this).is('[data-tilt-axis]') ? $(this).data('tilt-axis') : null,
                  reset: $(this).is('[data-tilt-reset]') ? $(this).data('tilt-reset') : true,
                  glare: $(this).is('[data-tilt-glare]') ? $(this).data('tilt-glare') : false,
                  maxGlare: $(this).is('[data-tilt-maxglare]') ? $(this).data('tilt-maxglare') : 1,
              }, options);

              // Add deprecation warning & set disableAxis to deprecated axis setting
              if(this.settings.axis !== null){
                  console.warn('Tilt.js: the axis setting has been renamed to disableAxis. See https://github.com/gijsroge/tilt.js/pull/26 for more information');
                  this.settings.disableAxis = this.settings.axis;
              }

              this.init = () => {
                  // Store settings
                  $(this).data('settings', this.settings);

                  // Prepare element
                  if(this.settings.glare) prepareGlare.call(this);

                  // Bind events
                  bindEvents.call(this);
              };

              // Init
              this.init();

          });
      };

      /**
       * Auto load
       */
      $('[data-tilt]').tilt();

      return true;
  }));





  if( typeof Object.create !== 'function' ){ Object.create = function (obj){ function F(){} F.prototype = obj; return new F();}; }

  // config for lazysizes
  window.lazySizesConfig = window.lazySizesConfig || {};
  window.lazySizesConfig.lazyClass    = 'jltma-preload';
  window.lazySizesConfig.loadingClass = 'jltma-preloading';
  window.lazySizesConfig.loadedClass  = 'jltma-preloaded';

  // On Loading
  // an event right before of the "unveil" transformation of lazyload
  document.addEventListener('lazybeforeunveil', function( e ){
      var color = e.target.getAttribute( 'data-bg-color' );
      if( color ){
          e.target.style.backgroundColor = color;
      }
  });

  document.addEventListener('lazyloaded', function( e ){
      if( e.target.getAttribute('data-bg-color') ){
        e.target.style.backgroundColor = 'initial';
      }
      if( e.target.classList.contains('jltma-has-preload-height') ){
          e.target.classList.remove('jltma-has-preload-height');
          e.target.style.height = 'auto';
      }

      // Lazyload videos
      if( e.target.nodeName === "VIDEO" ){
          var video = e.target;

          for (var source in video.children) {
              var videoSource = video.children[source];
              if ( videoSource.tagName === "SOURCE" && videoSource.getAttribute('data-src') ) {
                  videoSource.src = videoSource.getAttribute('data-src');
              }
          }
          video.load();

          // autoPlay video
          if( video.classList.contains('jltma-autoplay') ){
              video.play();
          }
      }
  });

  (function($, window, document, undefined){
      "use strict";

      var resposiveNotLoadedImages = function(){
          var width, height, lazysizeImages = document.querySelectorAll('.jltma-preload');

          Array.prototype.forEach.call(lazysizeImages, function(el, i){
              if( ( width = el.getAttribute('width') ) && ( height = el.getAttribute('height') ) ){
                  el.style.height = el.clientWidth/(width/height) + 'px';
                  el.classList.add('jltma-has-preload-height');
              }
          });
      };

      window.addEventListener("orientationchange", resposiveNotLoadedImages);
      window.addEventListener('resize', resposiveNotLoadedImages);
      $(resposiveNotLoadedImages);

  })(jQuery, window, document);






/*!
 * ================== js/libs/plugins/lazysizes.js ===================
 **/

(function(window, factory) {
  var lazySizes = factory(window, window.document);
  window.lazySizes = lazySizes;
  if(typeof module == 'object' && module.exports){
    module.exports = lazySizes;
  }
}(window, function l(window, document) {
  'use strict';
  /*jshint eqnull:true */
  if(!document.getElementsByClassName){return;}

  var lazysizes, lazySizesConfig;

  var docElem = document.documentElement;

  var Date = window.Date;

  var supportPicture = window.HTMLPictureElement;

  var _addEventListener = 'addEventListener';

  var _getAttribute = 'getAttribute';

  var addEventListener = window[_addEventListener];

  var setTimeout = window.setTimeout;

  var requestAnimationFrame = window.requestAnimationFrame || setTimeout;

  var requestIdleCallback = window.requestIdleCallback;

  var regPicture = /^picture$/i;

  var loadEvents = ['load', 'error', 'lazyincluded', '_lazyloaded'];

  var regClassCache = {};

  var forEach = Array.prototype.forEach;

  var hasClass = function(ele, cls) {
    if(!regClassCache[cls]){
      regClassCache[cls] = new RegExp('(\\s|^)'+cls+'(\\s|$)');
    }
    return regClassCache[cls].test(ele[_getAttribute]('class') || '') && regClassCache[cls];
  };

  var addClass = function(ele, cls) {
    if (!hasClass(ele, cls)){
      ele.setAttribute('class', (ele[_getAttribute]('class') || '').trim() + ' ' + cls);
    }
  };

  var removeClass = function(ele, cls) {
    var reg;
    if ((reg = hasClass(ele,cls))) {
      ele.setAttribute('class', (ele[_getAttribute]('class') || '').replace(reg, ' '));
    }
  };

  var addRemoveLoadEvents = function(dom, fn, add){
    var action = add ? _addEventListener : 'removeEventListener';
    if(add){
      addRemoveLoadEvents(dom, fn);
    }
    loadEvents.forEach(function(evt){
      dom[action](evt, fn);
    });
  };

  var triggerEvent = function(elem, name, detail, noBubbles, noCancelable){
    var event = document.createEvent('CustomEvent');

    if(!detail){
      detail = {};
    }

    detail.instance = lazysizes;

    event.initCustomEvent(name, !noBubbles, !noCancelable, detail);

    elem.dispatchEvent(event);
    return event;
  };

  var updatePolyfill = function (el, full){
    var polyfill;
    if( !supportPicture && ( polyfill = (window.picturefill || lazySizesConfig.pf) ) ){
      polyfill({reevaluate: true, elements: [el]});
    } else if(full && full.src){
      el.src = full.src;
    }
  };

  var getCSS = function (elem, style){
    return (getComputedStyle(elem, null) || {})[style];
  };

  var getWidth = function(elem, parent, width){
    width = width || elem.offsetWidth;

    while(width < lazySizesConfig.minSize && parent && !elem._lazysizesWidth){
      width =  parent.offsetWidth;
      parent = parent.parentNode;
    }

    return width;
  };

  var rAF = (function(){
    var running, waiting;
    var firstFns = [];
    var secondFns = [];
    var fns = firstFns;

    var run = function(){
      var runFns = fns;

      fns = firstFns.length ? secondFns : firstFns;

      running = true;
      waiting = false;

      while(runFns.length){
        runFns.shift()();
      }

      running = false;
    };

    var rafBatch = function(fn, queue){
      if(running && !queue){
        fn.apply(this, arguments);
      } else {
        fns.push(fn);

        if(!waiting){
          waiting = true;
          (document.hidden ? setTimeout : requestAnimationFrame)(run);
        }
      }
    };

    rafBatch._lsFlush = run;

    return rafBatch;
  })();

  var rAFIt = function(fn, simple){
    return simple ?
      function() {
        rAF(fn);
      } :
      function(){
        var that = this;
        var args = arguments;
        rAF(function(){
          fn.apply(that, args);
        });
      }
    ;
  };

  var throttle = function(fn){
    var running;
    var lastTime = 0;
    var gDelay = 125;
    var rICTimeout = lazySizesConfig.ricTimeout;
    var run = function(){
      running = false;
      lastTime = Date.now();
      fn();
    };
    var idleCallback = requestIdleCallback && lazySizesConfig.ricTimeout ?
      function(){
        requestIdleCallback(run, {timeout: rICTimeout});

        if(rICTimeout !== lazySizesConfig.ricTimeout){
          rICTimeout = lazySizesConfig.ricTimeout;
        }
      } :
      rAFIt(function(){
        setTimeout(run);
      }, true)
    ;

    return function(isPriority){
      var delay;

      if((isPriority = isPriority === true)){
        rICTimeout = 33;
      }

      if(running){
        return;
      }

      running =  true;

      delay = gDelay - (Date.now() - lastTime);

      if(delay < 0){
        delay = 0;
      }

      if(isPriority || (delay < 9 && requestIdleCallback)){
        idleCallback();
      } else {
        setTimeout(idleCallback, delay);
      }
    };
  };

  //based on http://modernjavascript.blogspot.de/2013/08/building-better-debounce.html
  var debounce = function(func) {
    var timeout, timestamp;
    var wait = 99;
    var run = function(){
      timeout = null;
      func();
    };
    var later = function() {
      var last = Date.now() - timestamp;

      if (last < wait) {
        setTimeout(later, wait - last);
      } else {
        (requestIdleCallback || run)(run);
      }
    };

    return function() {
      timestamp = Date.now();

      if (!timeout) {
        timeout = setTimeout(later, wait);
      }
    };
  };

  (function(){
    var prop;

    var lazySizesDefaults = {
      lazyClass: 'lazyload',
      loadedClass: 'lazyloaded',
      loadingClass: 'lazyloading',
      preloadClass: 'lazypreload',
      errorClass: 'lazyerror',
      //strictClass: 'lazystrict',
      autosizesClass: 'lazyautosizes',
      srcAttr: 'data-src',
      srcsetAttr: 'data-srcset',
      sizesAttr: 'data-sizes',
      //preloadAfterLoad: false,
      minSize: 40,
      customMedia: {},
      init: true,
      expFactor: 1.5,
      hFac: 0.8,
      loadMode: 2,
      loadHidden: true,
      ricTimeout: 300,
    };

    lazySizesConfig = window.lazySizesConfig || window.lazysizesConfig || {};

    for(prop in lazySizesDefaults){
      if(!(prop in lazySizesConfig)){
        lazySizesConfig[prop] = lazySizesDefaults[prop];
      }
    }

    window.lazySizesConfig = lazySizesConfig;

    setTimeout(function(){
      if(lazySizesConfig.init){
        init();
      }
    });
  })();

  var loader = (function(){
    var preloadElems, isCompleted, resetPreloadingTimer, loadMode, started;

    var eLvW, elvH, eLtop, eLleft, eLright, eLbottom;

    var defaultExpand, preloadExpand, hFac;

    var regImg = /^img$/i;
    var regIframe = /^iframe$/i;

    var supportScroll = ('onscroll' in window) && !(/glebot/.test(navigator.userAgent));

    var shrinkExpand = 0;
    var currentExpand = 0;

    var isLoading = 0;
    var lowRuns = -1;

    var resetPreloading = function(e){
      isLoading--;
      if(e && e.target){
        addRemoveLoadEvents(e.target, resetPreloading);
      }

      if(!e || isLoading < 0 || !e.target){
        isLoading = 0;
      }
    };

    var isNestedVisible = function(elem, elemExpand){
      var outerRect;
      var parent = elem;
      var visible = getCSS(document.body, 'visibility') == 'hidden' || getCSS(elem, 'visibility') != 'hidden';

      eLtop -= elemExpand;
      eLbottom += elemExpand;
      eLleft -= elemExpand;
      eLright += elemExpand;

      while(visible && (parent = parent.offsetParent) && parent != document.body && parent != docElem){
        visible = ((getCSS(parent, 'opacity') || 1) > 0);

        if(visible && getCSS(parent, 'overflow') != 'visible'){
          outerRect = parent.getBoundingClientRect();
          visible = eLright > outerRect.left &&
            eLleft < outerRect.right &&
            eLbottom > outerRect.top - 1 &&
            eLtop < outerRect.bottom + 1
          ;
        }
      }

      return visible;
    };

    var checkElements = function() {
      var eLlen, i, rect, autoLoadElem, loadedSomething, elemExpand, elemNegativeExpand, elemExpandVal, beforeExpandVal;

      var lazyloadElems = lazysizes.elements;

      if((loadMode = lazySizesConfig.loadMode) && isLoading < 8 && (eLlen = lazyloadElems.length)){

        i = 0;

        lowRuns++;

        if(preloadExpand == null){
          if(!('expand' in lazySizesConfig)){
            lazySizesConfig.expand = docElem.clientHeight > 500 && docElem.clientWidth > 500 ? 500 : 370;
          }

          defaultExpand = lazySizesConfig.expand;
          preloadExpand = defaultExpand * lazySizesConfig.expFactor;
        }

        if(currentExpand < preloadExpand && isLoading < 1 && lowRuns > 2 && loadMode > 2 && !document.hidden){
          currentExpand = preloadExpand;
          lowRuns = 0;
        } else if(loadMode > 1 && lowRuns > 1 && isLoading < 6){
          currentExpand = defaultExpand;
        } else {
          currentExpand = shrinkExpand;
        }

        for(; i < eLlen; i++){

          if(!lazyloadElems[i] || lazyloadElems[i]._lazyRace){continue;}

          if(!supportScroll){unveilElement(lazyloadElems[i]);continue;}

          if(!(elemExpandVal = lazyloadElems[i][_getAttribute]('data-expand')) || !(elemExpand = elemExpandVal * 1)){
            elemExpand = currentExpand;
          }

          if(beforeExpandVal !== elemExpand){
            eLvW = innerWidth + (elemExpand * hFac);
            elvH = innerHeight + elemExpand;
            elemNegativeExpand = elemExpand * -1;
            beforeExpandVal = elemExpand;
          }

          rect = lazyloadElems[i].getBoundingClientRect();

          if ((eLbottom = rect.bottom) >= elemNegativeExpand &&
            (eLtop = rect.top) <= elvH &&
            (eLright = rect.right) >= elemNegativeExpand * hFac &&
            (eLleft = rect.left) <= eLvW &&
            (eLbottom || eLright || eLleft || eLtop) &&
            (lazySizesConfig.loadHidden || getCSS(lazyloadElems[i], 'visibility') != 'hidden') &&
            ((isCompleted && isLoading < 3 && !elemExpandVal && (loadMode < 3 || lowRuns < 4)) || isNestedVisible(lazyloadElems[i], elemExpand))){
            unveilElement(lazyloadElems[i]);
            loadedSomething = true;
            if(isLoading > 9){break;}
          } else if(!loadedSomething && isCompleted && !autoLoadElem &&
            isLoading < 4 && lowRuns < 4 && loadMode > 2 &&
            (preloadElems[0] || lazySizesConfig.preloadAfterLoad) &&
            (preloadElems[0] || (!elemExpandVal && ((eLbottom || eLright || eLleft || eLtop) || lazyloadElems[i][_getAttribute](lazySizesConfig.sizesAttr) != 'auto')))){
            autoLoadElem = preloadElems[0] || lazyloadElems[i];
          }
        }

        if(autoLoadElem && !loadedSomething){
          unveilElement(autoLoadElem);
        }
      }
    };

    var throttledCheckElements = throttle(checkElements);

    var switchLoadingClass = function(e){
      addClass(e.target, lazySizesConfig.loadedClass);
      removeClass(e.target, lazySizesConfig.loadingClass);
      addRemoveLoadEvents(e.target, rafSwitchLoadingClass);
      triggerEvent(e.target, 'lazyloaded');
    };
    var rafedSwitchLoadingClass = rAFIt(switchLoadingClass);
    var rafSwitchLoadingClass = function(e){
      rafedSwitchLoadingClass({target: e.target});
    };

    var changeIframeSrc = function(elem, src){
      try {
        elem.contentWindow.location.replace(src);
      } catch(e){
        elem.src = src;
      }
    };

    var handleSources = function(source){
      var customMedia;

      var sourceSrcset = source[_getAttribute](lazySizesConfig.srcsetAttr);

      if( (customMedia = lazySizesConfig.customMedia[source[_getAttribute]('data-media') || source[_getAttribute]('media')]) ){
        source.setAttribute('media', customMedia);
      }

      if(sourceSrcset){
        source.setAttribute('srcset', sourceSrcset);
      }
    };

    var lazyUnveil = rAFIt(function (elem, detail, isAuto, sizes, isImg){
      var src, srcset, parent, isPicture, event, firesLoad;

      if(!(event = triggerEvent(elem, 'lazybeforeunveil', detail)).defaultPrevented){

        if(sizes){
          if(isAuto){
            addClass(elem, lazySizesConfig.autosizesClass);
          } else {
            elem.setAttribute('sizes', sizes);
          }
        }

        srcset = elem[_getAttribute](lazySizesConfig.srcsetAttr);
        src = elem[_getAttribute](lazySizesConfig.srcAttr);

        if(isImg) {
          parent = elem.parentNode;
          isPicture = parent && regPicture.test(parent.nodeName || '');
        }

        firesLoad = detail.firesLoad || (('src' in elem) && (srcset || src || isPicture));

        event = {target: elem};

        if(firesLoad){
          addRemoveLoadEvents(elem, resetPreloading, true);
          clearTimeout(resetPreloadingTimer);
          resetPreloadingTimer = setTimeout(resetPreloading, 2500);

          addClass(elem, lazySizesConfig.loadingClass);
          addRemoveLoadEvents(elem, rafSwitchLoadingClass, true);
        }

        if(isPicture){
          forEach.call(parent.getElementsByTagName('source'), handleSources);
        }

        if(srcset){
          elem.setAttribute('srcset', srcset);
        } else if(src && !isPicture){
          if(regIframe.test(elem.nodeName)){
            changeIframeSrc(elem, src);
          } else {
            elem.src = src;
          }
        }

        if(isImg && (srcset || isPicture)){
          updatePolyfill(elem, {src: src});
        }
      }

      if(elem._lazyRace){
        delete elem._lazyRace;
      }
      removeClass(elem, lazySizesConfig.lazyClass);

      rAF(function(){
        if( !firesLoad || (elem.complete && elem.naturalWidth > 1)){
          if(firesLoad){
            resetPreloading(event);
          } else {
            isLoading--;
          }
          switchLoadingClass(event);
        }
      }, true);
    });

    var unveilElement = function (elem){
      var detail;

      var isImg = regImg.test(elem.nodeName);

      //allow using sizes="auto", but don't use. it's invalid. Use data-sizes="auto" or a valid value for sizes instead (i.e.: sizes="80vw")
      var sizes = isImg && (elem[_getAttribute](lazySizesConfig.sizesAttr) || elem[_getAttribute]('sizes'));
      var isAuto = sizes == 'auto';

      if( (isAuto || !isCompleted) && isImg && (elem[_getAttribute]('src') || elem.srcset) && !elem.complete && !hasClass(elem, lazySizesConfig.errorClass) && hasClass(elem, lazySizesConfig.lazyClass)){return;}

      detail = triggerEvent(elem, 'lazyunveilread').detail;

      if(isAuto){
         autoSizer.updateElem(elem, true, elem.offsetWidth);
      }

      elem._lazyRace = true;
      isLoading++;

      lazyUnveil(elem, detail, isAuto, sizes, isImg);
    };

    var onload = function(){
      if(isCompleted){return;}
      if(Date.now() - started < 999){
        setTimeout(onload, 999);
        return;
      }
      var afterScroll = debounce(function(){
        lazySizesConfig.loadMode = 3;
        throttledCheckElements();
      });

      isCompleted = true;

      lazySizesConfig.loadMode = 3;

      throttledCheckElements();

      addEventListener('scroll', function(){
        if(lazySizesConfig.loadMode == 3){
          lazySizesConfig.loadMode = 2;
        }
        afterScroll();
      }, true);
    };

    return {
      _: function(){
        started = Date.now();

        lazysizes.elements = document.getElementsByClassName(lazySizesConfig.lazyClass);
        preloadElems = document.getElementsByClassName(lazySizesConfig.lazyClass + ' ' + lazySizesConfig.preloadClass);
        hFac = lazySizesConfig.hFac;

        addEventListener('scroll', throttledCheckElements, true);

        addEventListener('resize', throttledCheckElements, true);

        if(window.MutationObserver){
          new MutationObserver( throttledCheckElements ).observe( docElem, {childList: true, subtree: true, attributes: true} );
        } else {
          docElem[_addEventListener]('DOMNodeInserted', throttledCheckElements, true);
          docElem[_addEventListener]('DOMAttrModified', throttledCheckElements, true);
          setInterval(throttledCheckElements, 999);
        }

        addEventListener('hashchange', throttledCheckElements, true);

        //, 'fullscreenchange'
        ['focus', 'mouseover', 'click', 'load', 'transitionend', 'animationend', 'webkitAnimationEnd'].forEach(function(name){
          document[_addEventListener](name, throttledCheckElements, true);
        });

        if((/d$|^c/.test(document.readyState))){
          onload();
        } else {
          addEventListener('load', onload);
          document[_addEventListener]('DOMContentLoaded', throttledCheckElements);
          setTimeout(onload, 20000);
        }

        if(lazysizes.elements.length){
          checkElements();
          rAF._lsFlush();
        } else {
          throttledCheckElements();
        }
      },
      checkElems: throttledCheckElements,
      unveil: unveilElement
    };
  })();


  var autoSizer = (function(){
    var autosizesElems;

    var sizeElement = rAFIt(function(elem, parent, event, width){
      var sources, i, len;
      elem._lazysizesWidth = width;
      width += 'px';

      elem.setAttribute('sizes', width);

      if(regPicture.test(parent.nodeName || '')){
        sources = parent.getElementsByTagName('source');
        for(i = 0, len = sources.length; i < len; i++){
          sources[i].setAttribute('sizes', width);
        }
      }

      if(!event.detail.dataAttr){
        updatePolyfill(elem, event.detail);
      }
    });
    var getSizeElement = function (elem, dataAttr, width){
      var event;
      var parent = elem.parentNode;

      if(parent){
        width = getWidth(elem, parent, width);
        event = triggerEvent(elem, 'lazybeforesizes', {width: width, dataAttr: !!dataAttr});

        if(!event.defaultPrevented){
          width = event.detail.width;

          if(width && width !== elem._lazysizesWidth){
            sizeElement(elem, parent, event, width);
          }
        }
      }
    };

    var updateElementsSizes = function(){
      var i;
      var len = autosizesElems.length;
      if(len){
        i = 0;

        for(; i < len; i++){
          getSizeElement(autosizesElems[i]);
        }
      }
    };

    var debouncedUpdateElementsSizes = debounce(updateElementsSizes);

    return {
      _: function(){
        autosizesElems = document.getElementsByClassName(lazySizesConfig.autosizesClass);
        addEventListener('resize', debouncedUpdateElementsSizes);
      },
      checkElems: debouncedUpdateElementsSizes,
      updateElem: getSizeElement
    };
  })();

  var init = function(){
    if(!init.i){
      init.i = true;
      autoSizer._();
      loader._();
    }
  };

  lazysizes = {
    cfg: lazySizesConfig,
    autoSizer: autoSizer,
    loader: loader,
    init: init,
    uP: updatePolyfill,
    aC: addClass,
    rC: removeClass,
    hC: hasClass,
    fire: triggerEvent,
    gW: getWidth,
    rAF: rAF,
  };

  return lazysizes;
}
));


} )( jQuery, window, document );
