(function(){'use strict';var j=false;window.JQClass=function(){};JQClass.classes={};JQClass.extend=function extender(f){var g=this.prototype;j=true;var h=new this();j=false;for(var i in f){h[i]=typeof f[i]=='function'&&typeof g[i]=='function'?(function(d,e){return function(){var b=this._super;this._super=function(a){return g[d].apply(this,a)};var c=e.apply(this,arguments);this._super=b;return c}})(i,f[i]):f[i]}function JQClass(){if(!j&&this._init){this._init.apply(this,arguments)}}JQClass.prototype=h;JQClass.prototype.constructor=JQClass;JQClass.extend=extender;return JQClass}})();(function($){JQClass.classes.JQPlugin=JQClass.extend({name:'plugin',defaultOptions:{},regionalOptions:{},_getters:[],_getMarker:function(){return'is-'+this.name},_init:function(){$.extend(this.defaultOptions,(this.regionalOptions&&this.regionalOptions[''])||{});var c=camelCase(this.name);$[c]=this;$.fn[c]=function(a){var b=Array.prototype.slice.call(arguments,1);if($[c]._isNotChained(a,b)){return $[c][a].apply($[c],[this[0]].concat(b))}return this.each(function(){if(typeof a==='string'){if(a[0]==='_'||!$[c][a]){throw'Unknown method: '+a;}$[c][a].apply($[c],[this].concat(b))}else{$[c]._attach(this,a)}})}},setDefaults:function(a){$.extend(this.defaultOptions,a||{})},_isNotChained:function(a,b){if(a==='option'&&(b.length===0||(b.length===1&&typeof b[0]==='string'))){return true}return $.inArray(a,this._getters)>-1},_attach:function(a,b){a=$(a);if(a.hasClass(this._getMarker())){return}a.addClass(this._getMarker());b=$.extend({},this.defaultOptions,this._getMetadata(a),b||{});var c=$.extend({name:this.name,elem:a,options:b},this._instSettings(a,b));a.data(this.name,c);this._postAttach(a,c);this.option(a,b)},_instSettings:function(a,b){return{}},_postAttach:function(a,b){},_getMetadata:function(d){try{var f=d.data(this.name.toLowerCase())||'';f=f.replace(/'/g,'"');f=f.replace(/([a-zA-Z0-9]+):/g,function(a,b,i){var c=f.substring(0,i).match(/"/g);return(!c||c.length%2===0?'"'+b+'":':b+':')});f=$.parseJSON('{'+f+'}');for(var g in f){var h=f[g];if(typeof h==='string'&&h.match(/^new Date\((.*)\)$/)){f[g]=eval(h)}}return f}catch(e){return{}}},_getInst:function(a){return $(a).data(this.name)||{}},option:function(a,b,c){a=$(a);var d=a.data(this.name);if(!b||(typeof b==='string'&&c==null)){var e=(d||{}).options;return(e&&b?e[b]:e)}if(!a.hasClass(this._getMarker())){return}var e=b||{};if(typeof b==='string'){e={};e[b]=c}this._optionsChanged(a,d,e);$.extend(d.options,e)},_optionsChanged:function(a,b,c){},destroy:function(a){a=$(a);if(!a.hasClass(this._getMarker())){return}this._preDestroy(a,this._getInst(a));a.removeData(this.name).removeClass(this._getMarker())},_preDestroy:function(a,b){}});function camelCase(c){return c.replace(/-([a-z])/g,function(a,b){return b.toUpperCase()})}$.JQPlugin={createPlugin:function(a,b){if(typeof a==='object'){b=a;a='JQPlugin'}a=camelCase(a);var c=camelCase(b.name);JQClass.classes[c]=JQClass.classes[a].extend(b);new JQClass.classes[c]()}}})(jQuery);
/* http://keith-wood.name/countdown.html
   Countdown for jQuery v2.0.0.
   Written by Keith Wood (kbwood{at}iinet.com.au) January 2008.
   Available under the MIT (https://github.com/jquery/jquery/blob/master/MIT-LICENSE.txt) license. 
   Please attribute the author if you use it. */
(function($) { // Hide scope, no $ conflict
  'use strict';
  var pluginName = 'porto_countdown';
  var Y = 0; // Years
  var O = 1; // Months
  var W = 2; // Weeks
  var D = 3; // Days
  var H = 4; // Hours
  var M = 5; // Minutes
  var S = 6; // Seconds
  $.JQPlugin.createPlugin({
    /** The name of the plugin. */
    name: pluginName,
    defaultOptions: {
      until: null,
      since: null,
      timezone: null,
      serverSync: null,
      format: 'dHMS',
      layout: '',
      compact: false,
      padZeroes: false,
      significant: 0,
      description: '',
      expiryUrl: '',
      expiryText: '',
      alwaysExpire: false,
      onExpiry: null,
      onTick: null,
      tickInterval: 1
    },
    regionalOptions: { // Available regional settings, indexed by language/country code
      '': { // Default regional settings - English/US
    labels: ['Years', 'Months', 'Weeks', 'Days', 'Hours', 'Minutes', 'Seconds'],
    labels1: ['Year', 'Month', 'Week', 'Day', 'Hour', 'Minute', 'Second'],
        compactLabels: ['y', 'm', 'w', 'd'],
        whichLabels: null,
        digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
        timeSeparator: ':',
        isRTL: false
      }
    },
    /** Names of getter methods - those that can't be chained. */
    _getters: ['getTimes'],
    /* Class name for the right-to-left marker. */
    _rtlClass: pluginName + '-rtl',
    /* Class name for the countdown section marker. */
    _sectionClass: pluginName + '-section',
    /* Class name for the period amount marker. */
    _amountClass: pluginName + '-amount',
    /* Class name for the period name marker. */
    _periodClass: pluginName + '-period',
    /* Class name for the countdown row marker. */
    _rowClass: pluginName + '-row',
    /* Class name for the holding countdown marker. */
    _holdingClass: pluginName + '-holding',
    /* Class name for the showing countdown marker. */
    _showClass: pluginName + '-show',
    /* Class name for the description marker. */
    _descrClass: pluginName + '-descr',
    /* List of currently active countdown elements. */
    _timerElems: [],
    /** Additional setup for the countdown.
      Apply default localisations.
      Create the timer. */
    _init: function() {
      var self = this;
      this._super();
  this._serverSyncs = [];
  var now = (typeof Date.now == 'function' ? Date.now :
    function() { return new Date().getTime(); });
  var perfAvail = (window.performance && typeof window.performance.now == 'function');
  // Shared timer for all countdowns
  function timerCallBack(timestamp) {
    var drawStart = (timestamp < 1e12 ? // New HTML5 high resolution timer
      (perfAvail ? (performance.now() + performance.timing.navigationStart) : now()) :
      // Integer milliseconds since unix epoch
      timestamp || now());
    if (drawStart - animationStartTime >= 1000) {
          self._updateElems();
      animationStartTime = drawStart;
    }
    requestAnimationFrame(timerCallBack);
  }
  var requestAnimationFrame = window.requestAnimationFrame ||
    window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame ||
    window.oRequestAnimationFrame || window.msRequestAnimationFrame || null;
    // This is when we expect a fall-back to setInterval as it's much more fluid
  var animationStartTime = 0;
  if (!requestAnimationFrame || $.noRequestAnimationFrame) {
    $.noRequestAnimationFrame = null;
        setInterval(function() { self._updateElems(); }, 980); // Fall back to good old setInterval
  }
  else {
    animationStartTime = window.animationStartTime ||
      window.webkitAnimationStartTime || window.mozAnimationStartTime ||
      window.oAnimationStartTime || window.msAnimationStartTime || now();
    requestAnimationFrame(timerCallBack);
  }
    },
  UTCDate: function(tz, year, month, day, hours, mins, secs, ms) {
    if (typeof year == 'object' && year.constructor == Date) {
      ms = year.getMilliseconds();
      secs = year.getSeconds();
      mins = year.getMinutes();
      hours = year.getHours();
      day = year.getDate();
      month = year.getMonth();
      year = year.getFullYear();
    }
    var d = new Date();
    d.setUTCFullYear(year);
    d.setUTCDate(1);
    d.setUTCMonth(month || 0);
    d.setUTCDate(day || 1);
    d.setUTCHours(hours || 0);
    d.setUTCMinutes((mins || 0) - (Math.abs(tz) < 30 ? tz * 60 : tz));
    d.setUTCSeconds(secs || 0);
    d.setUTCMilliseconds(ms || 0);
    return d;
  },
    /** Convert a set of periods into seconds.
     Averaged for months and years.
      @param periods {number[]} The periods per year/month/week/day/hour/minute/second.
      @return {number} The corresponding number of seconds.
      @example var secs = $.countdown.periodsToSeconds(periods) */
  periodsToSeconds: function(periods) {
    return periods[0] * 31557600 + periods[1] * 2629800 + periods[2] * 604800 +
      periods[3] * 86400 + periods[4] * 3600 + periods[5] * 60 + periods[6];
  },
    _instSettings: function(elem, options) {
      return {_periods: [0, 0, 0, 0, 0, 0, 0]};
  },
    /** Add an element to the list of active ones.
      @private
      @param elem {Element} The countdown element. */
    _addElem: function(elem) {
      if (!this._hasElem(elem)) {
        this._timerElems.push(elem);
    }
  },
    /** See if an element is in the list of active ones.
      @private
      @param elem {Element} The countdown element.
      @return {boolean} True if present, false if not. */
    _hasElem: function(elem) {
      return ($.inArray(elem, this._timerElems) > -1);
  },
    /** Remove an element from the list of active ones.
      @private
      @param elem {Element} The countdown element. */
    _removeElem: function(elem) {
      this._timerElems = $.map(this._timerElems,
        function(value) { return (value == elem ? null : value); }); // delete entry
  },
    /** Update each active timer element.
      @private */
    _updateElems: function() {
      for (var i = this._timerElems.length - 1; i >= 0; i--) {
        this._updateCountdown(this._timerElems[i]);
    }
  },
    _optionsChanged: function(elem, inst, options) {
    if (options.layout) {
      options.layout = options.layout.replace(/&lt;/g, '<').replace(/&gt;/g, '>');
    }
    this._resetExtraLabels(inst.options, options);
    var timezoneChanged = (inst.options.timezone != options.timezone);
    $.extend(inst.options, options);
      this._adjustSettings(elem, inst,
      options.until != null || options.since != null || timezoneChanged);
    var now = new Date();
    if ((inst._since && inst._since < now) || (inst._until && inst._until > now)) {
        this._addElem(elem[0]);
    }
      this._updateCountdown(elem, inst);
  },
    /** Redisplay the countdown with an updated display.
      @private
      @param elem {Element|jQuery} The containing division.
      @param inst {object} The current settings for this instance. */
    _updateCountdown: function(elem, inst) {
      elem = elem.jquery ? elem : $(elem);
      inst = inst || elem.data(this.name);
    if (!inst) {
      return;
    }
      elem.html(this._generateHTML(inst)).toggleClass(this._rtlClass, inst.options.isRTL);
    if ($.isFunction(inst.options.onTick)) {
      var periods = inst._hold != 'lap' ? inst._periods :
        this._calculatePeriods(inst, inst._show, inst.options.significant, new Date());
      if (inst.options.tickInterval == 1 ||
          this.periodsToSeconds(periods) % inst.options.tickInterval == 0) {
          inst.options.onTick.apply(elem[0], [periods]);
      }
    }
    var expired = inst._hold != 'pause' &&
      (inst._since ? inst._now.getTime() < inst._since.getTime() :
      inst._now.getTime() >= inst._until.getTime());
    if (expired && !inst._expiring) {
      inst._expiring = true;
        if (this._hasElem(elem[0]) || inst.options.alwaysExpire) {
          this._removeElem(elem[0]);
        if ($.isFunction(inst.options.onExpiry)) {
            inst.options.onExpiry.apply(elem[0], []);
        }
        if (inst.options.expiryText) {
          var layout = inst.options.layout;
          inst.options.layout = inst.options.expiryText;
            this._updateCountdown(elem[0], inst);
          inst.options.layout = layout;
        }
        if (inst.options.expiryUrl) {
          window.location = inst.options.expiryUrl;
        }
      }
      inst._expiring = false;
    }
    else if (inst._hold == 'pause') {
        this._removeElem(elem[0]);
    }
  },
    /** Reset any extra labelsn and compactLabelsn entries if changing labels.
      @private
      @param base {object} The options to be updated.
      @param options {object} The new option values. */
  _resetExtraLabels: function(base, options) {
    var changingLabels = false;
    for (var n in options) {
      if (n != 'whichLabels' && n.match(/[Ll]abels/)) {
        changingLabels = true;
        break;
      }
    }
    if (changingLabels) {
      for (var n in base) { // Remove custom numbered labels
        if (n.match(/[Ll]abels[02-9]|compactLabels1/)) {
          base[n] = null;
        }
      }
    }
  },
    /** Calculate internal settings for an instance.
      @private
      @param elem {jQuery} The containing division.
      @param inst {object} The current settings for this instance.
      @param recalc {boolean} True if until or since are set. */
    _adjustSettings: function(elem, inst, recalc) {
    var now;
    var serverOffset = 0;
    var serverEntry = null;
    for (var i = 0; i < this._serverSyncs.length; i++) {
      if (this._serverSyncs[i][0] == inst.options.serverSync) {
        serverEntry = this._serverSyncs[i][1];
        break;
      }
    }
    if (serverEntry != null) {
      serverOffset = (inst.options.serverSync ? serverEntry : 0);
      now = new Date();
    }
    else {
      var serverResult = ($.isFunction(inst.options.serverSync) ?
          inst.options.serverSync.apply(elem[0], []) : null);
      now = new Date();
      serverOffset = (serverResult ? now.getTime() - serverResult.getTime() : 0);
      this._serverSyncs.push([inst.options.serverSync, serverOffset]);
    }
    var timezone = inst.options.timezone;
    timezone = (timezone == null ? -now.getTimezoneOffset() : timezone);
    if (recalc || (!recalc && inst._until == null && inst._since == null)) {
      inst._since = inst.options.since;
      if (inst._since != null) {
        inst._since = this.UTCDate(timezone, this._determineTime(inst._since, null));
        if (inst._since && serverOffset) {
          inst._since.setMilliseconds(inst._since.getMilliseconds() + serverOffset);
        }
      }
      inst._until = this.UTCDate(timezone, this._determineTime(inst.options.until, now));
      if (serverOffset) {
        inst._until.setMilliseconds(inst._until.getMilliseconds() + serverOffset);
      }
    }
    inst._show = this._determineShow(inst);
  },
    /** Remove the countdown widget from a div.
      @param elem {jQuery} The containing division.
      @param inst {object} The current instance object. */
    _preDestroy: function(elem, inst) {
      this._removeElem(elem[0]);
      elem.empty();
  },
    /** Pause a countdown widget at the current time.
     Stop it running but remember and display the current time.
      @param elem {Element} The containing division.
      @example $(selector).countdown('pause') */
    pause: function(elem) {
      this._hold(elem, 'pause');
  },
    /** Pause a countdown widget at the current time.
     Stop the display but keep the countdown running.
      @param elem {Element} The containing division.
      @example $(selector).countdown('lap') */
    lap: function(elem) {
      this._hold(elem, 'lap');
    },
    /** Resume a paused countdown widget.
      @param elem {Element} The containing division.
      @example $(selector).countdown('resume') */
    resume: function(elem) {
      this._hold(elem, null);
    },
    /** Toggle a paused countdown widget.
      @param elem {Element} The containing division.
      @example $(selector).countdown('toggle') */
    toggle: function(elem) {
      var inst = $.data(elem, this.name) || {};
      this[!inst._hold ? 'pause' : 'resume'](elem);
    },
    /** Toggle a lapped countdown widget.
      @param elem {Element} The containing division.
      @example $(selector).countdown('toggleLap') */
    toggleLap: function(elem) {
      var inst = $.data(elem, this.name) || {};
      this[!inst._hold ? 'lap' : 'resume'](elem);
    },
    /** Pause or resume a countdown widget.
      @private
      @param elem {Element} The containing division.
      @param hold {string} The new hold setting. */
    _hold: function(elem, hold) {
      var inst = $.data(elem, this.name);
    if (inst) {
      if (inst._hold == 'pause' && !hold) {
        inst._periods = inst._savePeriods;
        var sign = (inst._since ? '-' : '+');
        inst[inst._since ? '_since' : '_until'] =
          this._determineTime(sign + inst._periods[0] + 'y' +
            sign + inst._periods[1] + 'o' + sign + inst._periods[2] + 'w' +
            sign + inst._periods[3] + 'd' + sign + inst._periods[4] + 'h' + 
            sign + inst._periods[5] + 'm' + sign + inst._periods[6] + 's');
          this._addElem(elem);
      }
      inst._hold = hold;
      inst._savePeriods = (hold == 'pause' ? inst._periods : null);
        $.data(elem, this.name, inst);
        this._updateCountdown(elem, inst);
    }
  },
    /** Return the current time periods.
      @param elem {Element} The containing division.
      @return {number[]} The current periods for the countdown.
      @example var periods = $(selector).countdown('getTimes') */
    getTimes: function(elem) {
      var inst = $.data(elem, this.name);
    return (!inst ? null : (inst._hold == 'pause' ? inst._savePeriods : (!inst._hold ? inst._periods :
      this._calculatePeriods(inst, inst._show, inst.options.significant, new Date()))));
  },
    /** A time may be specified as an exact value or a relative one.
      @private
      @param setting {string|number|Date} The date/time value as a relative or absolute value.
      @param defaultTime {Date} The date/time to use if no other is supplied.
      @return {Date} The corresponding date/time. */
  _determineTime: function(setting, defaultTime) {
      var self = this;
    var offsetNumeric = function(offset) { // e.g. +300, -2
      var time = new Date();
      time.setTime(time.getTime() + offset * 1000);
      return time;
    };
    var offsetString = function(offset) { // e.g. '+2d', '-4w', '+3h +30m'
      offset = offset.toLowerCase();
      var time = new Date();
      var year = time.getFullYear();
      var month = time.getMonth();
      var day = time.getDate();
      var hour = time.getHours();
      var minute = time.getMinutes();
      var second = time.getSeconds();
      var pattern = /([+-]?[0-9]+)\s*(s|m|h|d|w|o|y)?/g;
      var matches = pattern.exec(offset);
      while (matches) {
        switch (matches[2] || 's') {
          case 's': second += parseInt(matches[1], 10); break;
          case 'm': minute += parseInt(matches[1], 10); break;
          case 'h': hour += parseInt(matches[1], 10); break;
          case 'd': day += parseInt(matches[1], 10); break;
          case 'w': day += parseInt(matches[1], 10) * 7; break;
          case 'o':
            month += parseInt(matches[1], 10); 
              day = Math.min(day, self._getDaysInMonth(year, month));
            break;
          case 'y':
            year += parseInt(matches[1], 10);
              day = Math.min(day, self._getDaysInMonth(year, month));
            break;
        }
        matches = pattern.exec(offset);
      }
      return new Date(year, month, day, hour, minute, second, 0);
    };
    var time = (setting == null ? defaultTime :
      (typeof setting == 'string' ? offsetString(setting) :
      (typeof setting == 'number' ? offsetNumeric(setting) : setting)));
    if (time) time.setMilliseconds(0);
    return time;
  },
    /** Determine the number of days in a month.
      @private
      @param year {number} The year.
      @param month {number} The month.
      @return {number} The days in that month. */
  _getDaysInMonth: function(year, month) {
    return 32 - new Date(year, month, 32).getDate();
  },
    /** Default implementation to determine which set of labels should be used for an amount.
      Use the <code>labels</code> attribute with the same numeric suffix (if it exists).
      @private
      @param num {number} The amount to be displayed.
      @return {number} The set of labels to be used for this amount. */
  _normalLabels: function(num) {
    return num;
  },
    /** Generate the HTML to display the countdown widget.
      @private
      @param inst {object} The current settings for this instance.
      @return {string} The new HTML for the countdown display. */
  _generateHTML: function(inst) {
    var self = this;
    // Determine what to show
    inst._periods = (inst._hold ? inst._periods :
      this._calculatePeriods(inst, inst._show, inst.options.significant, new Date()));
    // Show all 'asNeeded' after first non-zero value
    var shownNonZero = false;
    var showCount = 0;
    var sigCount = inst.options.significant;
    var show = $.extend({}, inst._show);
    for (var period = Y; period <= S; period++) {
      shownNonZero |= (inst._show[period] == '?' && inst._periods[period] > 0);
      show[period] = (inst._show[period] == '?' && !shownNonZero ? null : inst._show[period]);
      showCount += (show[period] ? 1 : 0);
      sigCount -= (inst._periods[period] > 0 ? 1 : 0);
    }
    var showSignificant = [false, false, false, false, false, false, false];
    for (var period = S; period >= Y; period--) { // Determine significant periods
      if (inst._show[period]) {
        if (inst._periods[period]) {
          showSignificant[period] = true;
        }
        else {
          showSignificant[period] = sigCount > 0;
          sigCount--;
        }
      }
    }
    var labels = (inst.options.compact ? inst.options.compactLabels : inst.options.labels);
    var whichLabels = inst.options.whichLabels || this._normalLabels;
    var showCompact = function(period) {
      var labelsNum = inst.options['compactLabels' + whichLabels(inst._periods[period])];
      return (show[period] ? self._translateDigits(inst, inst._periods[period]) +
        (labelsNum ? labelsNum[period] : labels[period]) + ' ' : '');
    };
    var minDigits = (inst.options.padZeroes ? 2 : 1);
    var showFull = function(period) {
      var labelsNum = inst.options['labels' + whichLabels(inst._periods[period])];
      return ((!inst.options.significant && show[period]) ||
        (inst.options.significant && showSignificant[period]) ?
          '<span class="' + self._sectionClass + '">' +
          //SID EDIT
          '<span class="porto_time-mid">' +
          '<span class="' + self._amountClass + '">' +
        self._minDigits(inst, inst._periods[period], minDigits) + '</span>' +
        '<span class="' + self._periodClass + '">' +
        (labelsNum ? labelsNum[period] : labels[period]) + '</span></span></span>' : '');
    };
    return (inst.options.layout ? this._buildLayout(inst, show, inst.options.layout,
      inst.options.compact, inst.options.significant, showSignificant) :
      ((inst.options.compact ? // Compact version
      '<span class="' + this._rowClass + ' ' + this._amountClass +
      (inst._hold ? ' ' + this._holdingClass : '') + '">' + 
      showCompact(Y) + showCompact(O) + showCompact(W) + showCompact(D) + 
      (show[H] ? this._minDigits(inst, inst._periods[H], 2) : '') +
      (show[M] ? (show[H] ? inst.options.timeSeparator : '') +
      this._minDigits(inst, inst._periods[M], 2) : '') +
      (show[S] ? (show[H] || show[M] ? inst.options.timeSeparator : '') +
      this._minDigits(inst, inst._periods[S], 2) : '') :
      // Full version
      '<span class="' + this._rowClass + ' ' + this._showClass + (inst.options.significant || showCount) +
      (inst._hold ? ' ' + this._holdingClass : '') + '">' +
      showFull(Y) + showFull(O) + showFull(W) + showFull(D) +
      showFull(H) + showFull(M) + showFull(S)) + '</span>' +
      (inst.options.description ? '<span class="' + this._rowClass + ' ' + this._descrClass + '">' +
      inst.options.description + '</span>' : '')));
  },
    /** Construct a custom layout.
      @private
      @param inst {object} The current settings for this instance.
      @param show {boolean[]} Flags indicating which periods are requested.
      @param layout {string} The customised layout.
      @param compact {boolean} True if using compact labels.
      @param significant {number} The number of periods with values to show, zero for all.
      @param showSignificant {boolean[]} Other periods to show for significance.
      @return {string} The custom HTML. */
  _buildLayout: function(inst, show, layout, compact, significant, showSignificant) {
    var labels = inst.options[compact ? 'compactLabels' : 'labels'];
    var whichLabels = inst.options.whichLabels || this._normalLabels;
    var labelFor = function(index) {
      return (inst.options[(compact ? 'compactLabels' : 'labels') +
        whichLabels(inst._periods[index])] || labels)[index];
    };
    var digit = function(value, position) {
      return inst.options.digits[Math.floor(value / position) % 10];
    };
    var subs = {desc: inst.options.description, sep: inst.options.timeSeparator,
      yl: labelFor(Y), yn: this._minDigits(inst, inst._periods[Y], 1),
      ynn: this._minDigits(inst, inst._periods[Y], 2),
      ynnn: this._minDigits(inst, inst._periods[Y], 3), y1: digit(inst._periods[Y], 1),
      y10: digit(inst._periods[Y], 10), y100: digit(inst._periods[Y], 100),
      y1000: digit(inst._periods[Y], 1000),
      ol: labelFor(O), on: this._minDigits(inst, inst._periods[O], 1),
      onn: this._minDigits(inst, inst._periods[O], 2),
      onnn: this._minDigits(inst, inst._periods[O], 3), o1: digit(inst._periods[O], 1),
      o10: digit(inst._periods[O], 10), o100: digit(inst._periods[O], 100),
      o1000: digit(inst._periods[O], 1000),
      wl: labelFor(W), wn: this._minDigits(inst, inst._periods[W], 1),
      wnn: this._minDigits(inst, inst._periods[W], 2),
      wnnn: this._minDigits(inst, inst._periods[W], 3), w1: digit(inst._periods[W], 1),
      w10: digit(inst._periods[W], 10), w100: digit(inst._periods[W], 100),
      w1000: digit(inst._periods[W], 1000),
      dl: labelFor(D), dn: this._minDigits(inst, inst._periods[D], 1),
      dnn: this._minDigits(inst, inst._periods[D], 2),
      dnnn: this._minDigits(inst, inst._periods[D], 3), d1: digit(inst._periods[D], 1),
      d10: digit(inst._periods[D], 10), d100: digit(inst._periods[D], 100),
      d1000: digit(inst._periods[D], 1000),
      hl: labelFor(H), hn: this._minDigits(inst, inst._periods[H], 1),
      hnn: this._minDigits(inst, inst._periods[H], 2),
      hnnn: this._minDigits(inst, inst._periods[H], 3), h1: digit(inst._periods[H], 1),
      h10: digit(inst._periods[H], 10), h100: digit(inst._periods[H], 100),
      h1000: digit(inst._periods[H], 1000),
      ml: labelFor(M), mn: this._minDigits(inst, inst._periods[M], 1),
      mnn: this._minDigits(inst, inst._periods[M], 2),
      mnnn: this._minDigits(inst, inst._periods[M], 3), m1: digit(inst._periods[M], 1),
      m10: digit(inst._periods[M], 10), m100: digit(inst._periods[M], 100),
      m1000: digit(inst._periods[M], 1000),
      sl: labelFor(S), sn: this._minDigits(inst, inst._periods[S], 1),
      snn: this._minDigits(inst, inst._periods[S], 2),
      snnn: this._minDigits(inst, inst._periods[S], 3), s1: digit(inst._periods[S], 1),
      s10: digit(inst._periods[S], 10), s100: digit(inst._periods[S], 100),
      s1000: digit(inst._periods[S], 1000)};
    var html = layout;
    // Replace period containers: {p<}...{p>}
    for (var i = Y; i <= S; i++) {
      var period = 'yowdhms'.charAt(i);
      var re = new RegExp('\\{' + period + '<\\}([\\s\\S]*)\\{' + period + '>\\}', 'g');
      html = html.replace(re, ((!significant && show[i]) ||
        (significant && showSignificant[i]) ? '$1' : ''));
    }
    // Replace period values: {pn}
    $.each(subs, function(n, v) {
      var re = new RegExp('\\{' + n + '\\}', 'g');
      html = html.replace(re, v);
    });
    return html;
  },
    /** Ensure a numeric value has at least n digits for display.
      @private
      @param inst {object} The current settings for this instance.
      @param value {number} The value to display.
      @param len {number} The minimum length.
      @return {string} The display text. */
  _minDigits: function(inst, value, len) {
    value = '' + value;
    if (value.length >= len) {
      return this._translateDigits(inst, value);
    }
    value = '0000000000' + value;
    return this._translateDigits(inst, value.substr(value.length - len));
  },
    /** Translate digits into other representations.
      @private
      @param inst {object} The current settings for this instance.
      @param value {string} The text to translate.
      @return {string} The translated text. */
  _translateDigits: function(inst, value) {
    return ('' + value).replace(/[0-9]/g, function(digit) {
        return inst.options.digits[digit];
      });
  },
    /** Translate the format into flags for each period.
      @private
      @param inst {object} The current settings for this instance.
      @return {string[]} Flags indicating which periods are requested (?) or
          required (!) by year, month, week, day, hour, minute, second. */
  _determineShow: function(inst) {
    var format = inst.options.format;
    var show = [];
    show[Y] = (format.match('y') ? '?' : (format.match('Y') ? '!' : null));
    show[O] = (format.match('o') ? '?' : (format.match('O') ? '!' : null));
    show[W] = (format.match('w') ? '?' : (format.match('W') ? '!' : null));
    show[D] = (format.match('d') ? '?' : (format.match('D') ? '!' : null));
    show[H] = (format.match('h') ? '?' : (format.match('H') ? '!' : null));
    show[M] = (format.match('m') ? '?' : (format.match('M') ? '!' : null));
    show[S] = (format.match('s') ? '?' : (format.match('S') ? '!' : null));
    return show;
  },
    /** Calculate the requested periods between now and the target time.
      @private
      @param inst {object} The current settings for this instance.
      @param show {string[]} Flags indicating which periods are requested/required.
      @param significant {number} The number of periods with values to show, zero for all.
      @param now {Date} The current date and time.
      @return {number[]} The current time periods (always positive)
          by year, month, week, day, hour, minute, second. */
  _calculatePeriods: function(inst, show, significant, now) {
    // Find endpoints
    inst._now = now;
    inst._now.setMilliseconds(0);
    var until = new Date(inst._now.getTime());
    if (inst._since) {
      if (now.getTime() < inst._since.getTime()) {
        inst._now = now = until;
      }
      else {
        now = inst._since;
      }
    }
    else {
      until.setTime(inst._until.getTime());
      if (now.getTime() > inst._until.getTime()) {
        inst._now = now = until;
      }
    }
    // Calculate differences by period
    var periods = [0, 0, 0, 0, 0, 0, 0];
    if (show[Y] || show[O]) {
      // Treat end of months as the same
        var lastNow = this._getDaysInMonth(now.getFullYear(), now.getMonth());
        var lastUntil = this._getDaysInMonth(until.getFullYear(), until.getMonth());
      var sameDay = (until.getDate() == now.getDate() ||
        (until.getDate() >= Math.min(lastNow, lastUntil) &&
        now.getDate() >= Math.min(lastNow, lastUntil)));
      var getSecs = function(date) {
        return (date.getHours() * 60 + date.getMinutes()) * 60 + date.getSeconds();
      };
      var months = Math.max(0,
        (until.getFullYear() - now.getFullYear()) * 12 + until.getMonth() - now.getMonth() +
        ((until.getDate() < now.getDate() && !sameDay) ||
        (sameDay && getSecs(until) < getSecs(now)) ? -1 : 0));
      periods[Y] = (show[Y] ? Math.floor(months / 12) : 0);
      periods[O] = (show[O] ? months - periods[Y] * 12 : 0);
      // Adjust for months difference and end of month if necessary
      now = new Date(now.getTime());
      var wasLastDay = (now.getDate() == lastNow);
        var lastDay = this._getDaysInMonth(now.getFullYear() + periods[Y],
        now.getMonth() + periods[O]);
      if (now.getDate() > lastDay) {
        now.setDate(lastDay);
      }
      now.setFullYear(now.getFullYear() + periods[Y]);
      now.setMonth(now.getMonth() + periods[O]);
      if (wasLastDay) {
        now.setDate(lastDay);
      }
    }
    var diff = Math.floor((until.getTime() - now.getTime()) / 1000);
    var extractPeriod = function(period, numSecs) {
      periods[period] = (show[period] ? Math.floor(diff / numSecs) : 0);
      diff -= periods[period] * numSecs;
    };
    extractPeriod(W, 604800);
    extractPeriod(D, 86400);
    extractPeriod(H, 3600);
    extractPeriod(M, 60);
    extractPeriod(S, 1);
    
    if (diff > 0 && !inst._since) { // Round up if left overs
      var multiplier = [1, 12, 4.3482, 7, 24, 60, 60];
      var lastShown = S;
      var max = 1;
      for (var period = S; period >= Y; period--) {
        if (show[period]) {
          if (periods[lastShown] >= max) {
            periods[lastShown] = 0;
            diff = 1;
          }
          if (diff > 0) {
            periods[period]++;
            diff = 0;
            lastShown = period;
            max = 1;
          }
        }
        max *= multiplier[period];
      }
    }
    
    if (significant) { // Zero out insignificant periods
      for (var period = Y; period <= S; period++) {
        if (significant && periods[period]) {
          significant--;
        }
        else if (!significant) {
          periods[period] = 0;
        }
      }
    }
    return periods;
  }
  });
})(jQuery);