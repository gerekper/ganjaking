/************************************************
 * REVOLUTION 2.0.3 ADDON - TypeWriter
 * @version: 2.0.3 (16.06.2020)
 * @requires Slider Revolution 6.1.2
 * @author ThemePunch
 ************************************************/

/*
	
	*****************************
	*********** [API] ***********
	*****************************
	
	-----------------
	---- Methods ----
	-----------------
	
	// pause animation
	jQuery(layer).data('typewriter').pause();
	
	// resume animation
	jQuery(layer).data('typewriter').resume();
	
	// retart the animation
	jQuery(layer).data('typewriter').restart();
	
	// stop the animation and restore the original Layer text
	jQuery(layer).data('typewriter').restore();
	
	----------------
	---- EVENTS ----
	----------------
	
	// animation begins
	revapi1.on('revolution.slide.typewriterstarted', function(e, layer) {
		
		console.log(layer);
		
	});
	
	// animation ended completely
	revapi1.on('revolution.slide.typewriterended', function(e, layer) {
		
		console.log(layer);
		
	});
	
	// sequenced line begins
	revapi1.on('revolution.slide.typewriternewline', function(e, layer, lineIndex, lineText) {
		
		console.log(layer);
		console.log('Line #' + lineIndex + ' = ' + lineText);
		
	});
	
	// sequenced starts a new loop
	revapi1.on('revolution.slide.typewriterlooped', function(e, layer) {
		
		console.log(layer);
		
	});

*/

;
(function () {

	var $,
		css,
		decoder,
		sliders = [],
		events = ['onbeforeswap', 'layeraction', 'beforeredraw', 'afterdraw'],
		anime = 'content: "[char]"; animation: tp-typewriter [speed]s steps(2, start) infinite';

	// called from bottom of slider init JS
	// function(tpj, revapi)
	window.RsTypewriterAddOn = function (_$, slider) {

		if (!slider) return;
		var layrs = slider.find('rs-layer[data-typewriter], .rs-layer[data-typewriter]');
		// only instantiate if slider contains at least one Typewriter Layer
		if (layrs.length) new RsTypewriter(_$, slider, layrs);

	};

	function RsTypewriter(_$, slider, layrs) {

		sliders[sliders.length] = this;

		// store local copy of "tpj"
		if (!$) {

			$ = _$;

			// garbage collection hook
			$.event.special.typewriterDestroyed = {

				remove: function (evt) {
					evt.handler();
				}

			};

			decoder = document.createElement('textarea');

		}

		css = '';
		this.layrs = [];

		var levels = $.fn.revolution && $.fn.revolution[slider[0].id] ? $.fn.revolution[slider[0].id] : false;
		if (levels && levels.hasOwnProperty('responsiveLevels')) levels = levels.responsiveLevels;
		else levels = '';
		this.levels = levels.toString().split(',');

		// add garbage collection hook
		this.slider = slider.on('typewriterDestroyed', onRemove);

		// prepare TypeWriter Layers
		layrs.each(this.writeLayerData.bind(this));

		// inline CSS for the blinking cursor animation
		if (css) {

			var head = document.head || document.body;
			if (head) {

				var style = document.createElement('style');
				style.type = 'text/css';
				style.innerHTML = css;
				head.appendChild(style);

			}

		}

		// add slider API event listeners
		var len = events.length;
		for (var i = 0; i < len; i++) {

			slider.on('revolution.slide.' + events[i], this.eventCallback.bind(this));

		}

	}

	RsTypewriter.prototype = {

		/* catch-all callback */
		eventCallback: function (event, obj) {

			// call corresponding event handler
			this[event.namespace.replace('.slide', '')](obj);

		},

		/* stop timers and then reset Layer text */
		onbeforeswap: function (data) {

			var layrs = this.layrs,
				len = layrs.length,
				itm;

			data = data.currentslide[0];
			if (!data) return;

			while (len--) {

				itm = layrs[len];
				if (itm.statc) continue;

				clearTimeout(itm.timer);
				itm.running = false;
				itm.active = false;

				if (!data.contains(itm.el)) {

					itm.el.innerHTML = itm.orig;
					$(itm.el).addClass('hide-typewriter-cursor');

				}

			}

		},

		/* start effect when the Layer is officially available */
		layeraction: function (obj) {

			if (obj.eventtype !== 'enterstage') return;

			var typewriter = $.data(obj.layer[0], 'typewriter'),
				tpe = obj.layertype === 'html' || obj.layertype === 'text';

			if (typewriter && tpe) {
				$.fn.revolution.sA(obj.layer[0], 'vary-layer-dims', true);
				typewriter.active = true;
				typewriter.start();
			}

		},

		/* before "containerResized()" */
		beforeredraw: function (obj) {

			var layrs = this.layrs,
				len = layrs.length;

			while (len--) layrs[len].onRedraw();

		},

		/* after "containerResized()" */
		afterdraw: function () {

			var layrs = this.layrs,
				len = layrs.length;

			while (len--) layrs[len].onRedraw(true);

		},

		writeLayerData: function (i, el) {

			var txt = $.trim(el.innerHTML);

			// bounce if Layer content is empty
			if (!txt) {

				el.removeAttribute('data-typewriter');
				return;

			}

			// disable whiteboard if it exists as the two just shoudn't be used together
			if (el.getAttribute('data-whiteboard')) {

				if (typeof console !== 'undefined') {

					console.log('Whiteboard is disabled for Layers with Typewriter Add-On effect enabled');

				}

				el.removeAttribute('data-whiteboard');

			}

			this.layrs[this.layrs.length] = new TypewriterLayer(el, txt, this.levels, this.slider);

		}

	};

	function cleanData(data) {

		for (var prop in data) {

			if (data.hasOwnProperty(prop) && typeof data[prop] === 'string' && data[prop].search('ms') !== -1) {

				data[prop] = data[prop].replace('ms', '');

			}

		}

	}

	function TypewriterLayer(el, txt, levels, revapi) {
		this.spans = [];
		this.textSpans = [];
		this.incompleteSpan = false;

		var data = JSON.parse(el.getAttribute('data-typewriter')),
			clean = txt.replace(/(_|\|)$/, ''),
			clas = el.className,
			lines = data.lines,
			orig = txt,
			dels = [],
			delays,
			widths,
			preLev,
			level,
			delay,
			space,
			auto,
			line,
			len,
			val,
			dim,
			d1,
			d2,
			i;

		// new to loop a single line
		if (!lines && data.sequenced && data.looped) lines = txt;

		cleanData(data);
		$(el).addClass('hide-typewriter-cursor');

		// sanitize "data-typewriter" info
		for (var prop in data) {

			if (!data.hasOwnProperty(prop)) continue;

			val = data[prop];
			prop = prop.replace(/_([a-z])/g, camelCase);

			this[prop] = val === 'on' || val === true ? 1 : val === 'off' || val === false ? 0 : isNaN(val) === false ? parseInt(val, 10) : val;

		}

		if (this.blinking) {

			// blinking cursor is a CSS animation
			css += '#' + el.id + ':after {' +
				anime.replace('[char]', this.cursorType === 'one' ? '_' : '|')
				.replace('[speed]', parseInt(this.blinkingSpeed, 10) * 0.001) + '}' +
				'#' + el.id + '.hide-typewriter-cursor:after {content: ""}';

			if (this.color && this.color !== 'transparent') {
				css += '#' + el.id + ':after {color:' + this.color + ';}';
			}

			/*
				For presentation purposes, Layers with a background and 
				also an "auto" width should have a "static" position for the cursor.
				For all other situations, an "absolute" position (the default) works best.
			*/
			if (this.background) {

				dim = el.getAttribute('data-dim');
				if (!dim) dim = 'w:auto;h:auto;';

				widths = dim.split('w:')[1];
				widths = widths.split(';')[0];
				widths = widths.replace(/[\[\]']+/g, '').split(',');

				if (levels) {

					len = widths.length;

					// check for auto width for all 4 responsive levels
					for (i = 0; i < len; i++) {

						level = levels[i];

						// prevent duplicate rules
						if (level === preLev) continue;

						auto = widths[i];
						auto = auto === 'auto' || auto === 'none';

						if (i > 0) {

							auto = auto ? 'static' : 'absolute';
							css += '@media screen and (max-width: ' + level + 'px) {' +
								'rs-layer#' + el.id + '[data-typewriter]:after {position: ' + auto + '}' +
								'#' + el.id + '.rs-layer[data-typewriter]:after {position: ' + auto + '}}';

						} else if (auto) {

							el.setAttribute('data-typewriter-blinking', 'true');

						}

						preLev = level;

					}

				} else {

					auto = widths[0];
					if (auto === 'none' || auto === 'auto') {

						el.setAttribute('data-typewriter-blinking', 'true');

					}

				}

			}

		}

		if (this.sequenced && lines) {
			var tempSanitize = sanitize(unescape(txt));
			this.textSpans = this.textSpans.concat(tempSanitize.spans);

			txt = [tempSanitize.value];
			lines = lines.split(',');
			len = lines.length;

			for (i = 0; i < len; i++) {

				line = $.trim(lines[i]);
				tempSanitize = sanitize(unescape(line));
				this.spans = this.spans.concat(tempSanitize.spans);
				if (line) txt[txt.length] = tempSanitize.value;
			}

			el.setAttribute('data-typewriter-sequenced', 'true');

		} else {

			this.looped = false;
			this.sequenced = false;
			this.newlineDelay = this.linebreakDelay;
			var tempSanitize = sanitize(txt, true);
			this.spans = this.spans.concat(tempSanitize.spans);
			lines = (tempSanitize.value).split(/\r?\n|\r/g);
			len = lines.length;
			txt = [];

			for (i = 0; i < len; i++) {

				txt[txt.length] = $.trim(lines[i]);
				txt[txt.length] = '';

			}

			while (txt[0] === '') txt.shift();
			while (txt[txt.length - 1] === '') txt.pop();

		}

		if (this.wordDelay) {

			delays = data.delays;
			if (delays) {

				delays = delays.split(',');
				len = delays.length;
				space = 0;

				for (i = 0; i < len; i++) {

					delay = unescape(delays[i]).split('|');
					d1 = delay[0];
					d2 = delay[1];

					if (isNaN(d1) === false && isNaN(d2) === false) {

						d1 = parseInt(d1);
						dels[d1 + space - 1] = parseInt(d2);
						space += d1;

					}
				}

				this.spaces = dels;

			} else {

				this.wordDelay = false;

			}

		}

		this.el = el;
		this.txt = txt;
		this.orig = orig;
		this.clean = clean;
		this.revapi = revapi;
		this.len = txt.length;
		this.statc = clas.search('rs-layer-static') !== -1;

		$.data(el, 'typewriter', this);

	}

	TypewriterLayer.prototype = {

		/* reset values and call "start delay" timer */
		start: function (delay, fromLoop) {

			clearTimeout(this.timer);
			if (this.paused) return;

			this.line = 0;
			this.step = 0;
			this.words = 0;
			this.skip = false;
			this.rewind = false;
			this.rstart = false;
			this.breaker = false;
			this.paused = false;
			this.rpaused = false;
			this.str = this.txt[this.line];
			this.len = this.str.length;

			if (!fromLoop) {

				this.el.innerHTML = '&nbsp;';
				$(this.el).removeClass('hide-typewriter-cursor');
				this.timer = setTimeout(this.onStart.bind(this), this.startDelay);

			} else {

				this.timer = setTimeout(this.onStart.bind(this), delay);
				this.revapi.trigger('revolution.slide.typewriterlooped', [this.el]);

			}

			this.running = true;
			this.revapi.trigger('revolution.slide.typewriterstarted', [this.el]);
			this.revapi.trigger('revolution.slide.typewriternewline', [this.el, this.line + 1, this.txt[this.line]]);
		},

		onStart: function () {
			this.el.innerHTML = '';
			this.animate();
		},

		/* handles all possible typing actions */
		animate: function () {

			var next,
				charc,
				words,
				space,
				str = this.str,
				txt = this.txt,
				step = this.step,
				rewind = this.rewind,
				speed = !rewind ? this.speed : this.deletionSpeed;

			if (step < this.len) {

				if (!rewind) {

					if (!this.breaker) {

						charc = str[step];
						if (charc === 'µ') {

							this.skip = true;
							this.breaker = true;
							speed = this.linebreakDelay;

						}

					} else {

						charc = '<br>';
						speed = !this.skip ? this.newlineDelay : this.linebreakDelay;

						this.breaker = false;
						this.skip = false;
						this.words = 0;
						this.step--;

					}

					if (!this.breaker) {

						space = this.el.innerHTML;
						if (space === '&nbsp;') space = '';

						if (charc === "╠" || charc === "╣" || this.incompleteSpan) {
							var count = space.match(/<span[^>]*>/g)
							count = count ? count.length : 0;

							if (charc === "╠") {
								this.incompleteSpan = true;
								charc = this.spans[count] + "</span>";
								space = space + charc;
							} else if (charc === "╣") {
								this.incompleteSpan = false;
							} else {
								space = replaceStringAt(space, space.lastIndexOf("</span>"), "</span>", charc + "</span>");
							}
						} else {
							space = space + charc;
						}

						this.el.innerHTML = space;

						if (this.wordDelay && charc === ' ') {

							words = this.words;
							space = this.spaces[words];

							if (space) {

								speed = space;
								this.words = words < this.spaces.length - 1 ? words + 1 : 0;

							} else {

								this.words += 1;

							}
						}
					}

				} else {

					str = str.slice(0, -1);
					this.str = str;

					var regex = /╠/g;
					var matches = [];
					var m;

					while (m = regex.exec(str)) {
						matches.push(m);
					}

					if (matches) {
						for (var i = matches.length - 1; i >= 0; i--) {
							var match = matches[i];
							str = replaceStringAt(str, match.index, "╣", this.spans[i])
						}

						str = str.replace(/╣/g, '</span>') || '&nbsp';
					}

					this.el.innerHTML = str.replace(/µ/g, '<br>') || '&nbsp';

				}

				this.step++;
				next = true;

			} else if (this.rstart) {

				if (!this.paused) {

					this.start(this.newlineDelay, true);

				} else {

					this.rpaused = true;
					this.lastSpeed = this.newlineDelay;

				}

				return;

			} else if (this.line < txt.length) {

				this.line++;
				this.step = 0;
				this.skip = false;

				str = txt[this.line];
				this.str = str;

				if (str === '') {

					this.line++;
					this.breaker = true;
					speed = this.newlineDelay;

				}

				if (this.line < txt.length) {

					if (!this.sequenced) {

						this.str = txt[this.line];
						this.len = this.str.length;

					} else {

						rewind = !rewind;

						if (!rewind) {

							this.str = txt[this.line];
							this.len = this.str.length;
							speed = this.newlineDelay;

						} else {

							this.line--;
							this.str = txt[this.line];
							speed = this.deletionDelay;

						}

						this.rewind = rewind;

					}

					next = true;
					this.words = 0;

					if (!this.rewind) {

						this.revapi.trigger(

							'revolution.slide.typewriternewline',
							[this.el, this.line + 1, txt[this.line]]

						);

					}

				} else if (this.looped) {

					this.rewind = true;
					this.rstart = true;
					this.words = 0;
					this.line--;
					this.str = txt[this.line];

					next = true;
					speed = this.deletionDelay;

				}

			}

			if (next) {

				if (this.paused) {

					this.lastSpeed = speed;
					return;

				}

				if (speed) this.timer = setTimeout(this.animate.bind(this), speed);
				else this.animate();

			} else {

				this.running = false;
				if (this.hideCursor) $(this.el).addClass('hide-typewriter-cursor');
				this.revapi.trigger('revolution.slide.typewriterended', [this.el]);

			}
		},

		/* swap text to its original on redraws for accurate positioning */
		onRedraw: function (restore) {

			if (!restore) {

				var pos = window.getComputedStyle(this.el, ':after').getPropertyValue('position');

				this.state = this.el.innerHTML;
				this.el.innerHTML = pos === 'absolute' ? this.orig : this.clean;

			} else {

				this.el.innerHTML = this.state;

			}

		},

		/* 
			API event 
			jQuery(layer).data('typewriter').pause();
		*/
		pause: function () {

			// bounce if Layer does not belong to the current slide
			// or if the Layer has not entered the stage yet
			if (!this.active) return;

			clearTimeout(this.timer);
			this.paused = true;

		},

		/* 
			API event 
			jQuery(layer).data('typewriter').resume();
		*/
		resume: function () {

			// bounce if Layer does not belong to the current slide
			// or if the Layer has not entered the stage yet
			if (!this.active) return;

			// if animation wasn't previously running (i.e. had already finished),
			// call api.restart() instead
			if (!this.running) {

				this.restart();
				return;

			}

			// animation is currently running but was not previously paused,
			// so no need to continue
			if (!this.paused) return;

			if (!this.rpaused) {

				this.paused = false;
				if (this.lastSpeed) this.timer = setTimeout(this.animate.bind(this), this.lastSpeed);
				else this.animate();

			} else {

				this.start(this.lastSpeed, true);

			}

		},

		/* 
			API event 
			jQuery(layer).data('typewriter').restart();
		*/
		restart: function () {

			// bounce if Layer does not belong to the current slide
			// or if the Layer has not entered the stage yet
			if (!this.active) return;

			clearTimeout(this.timer);
			this.rpaused = false;
			this.paused = false;
			this.start();

		},

		/* 
			API event - cancels animation and restores original Layer text
			jQuery(layer).data('typewriter').restore(hideBlinkingCursor = false);
		*/
		restore: function (hideCursor) {

			// bounce if Layer does not belong to the current slide
			// or if the Layer has not entered the stage yet
			if (!this.active) return;

			clearTimeout(this.timer);
			this.el.innerHTML = this.clean;
			if (hideCursor) $(this.el).addClass('hide-typewriter-cursor');

		}

	};

	function camelCase(chr) {

		return chr[1].toUpperCase();

	}

	// sanitize text/html, strips all tags and then makes sense of all possible line breaks
	function sanitize(st, deep) {
		st = st.replace(/<(?!br|span|\/span\s*\/?)[^>]+>/g, '').replace(/(_|\|)$/, '');

		if (!deep) {

			st = st.replace(/\r?\n|\r/g, '±')
				.replace(/<br[^>]*>/gi, 'µ')
				.replace(/<\/span>/gi, '╣')
				.replace(/±µ|µ±/g, 'µ')
				.replace(/^±+|±+$/g, '')
				.replace(/^µ+|µ+$/g, '');

		} else {

			st = st.replace(/\r?\n|\r/g, ' ').replace(/<br[^>]*>/gi, '\n').replace(/<\/span>/gi, '╣');

		}
		var regex = /<span[^>]*>/g;
		var spans = [];
		var match;

		while (match = regex.exec(st)) {
			spans.push(match[0]);
		}
		st = st.replace(regex, '╠');
		decoder.innerHTML = st;
		return {
			value: decoder.value,
			spans: spans
		};

	}

	function replaceStringAt(str, index, target, replacement) {
		return str.substr(0, index) + replacement + str.substr(index + target.length, str.length);
	}

	// slider removed from stage
	function onRemove() {

		var i = sliders.length,
			slider;

		while (i--) {

			slider = sliders[i];
			if (slider[0] === this) {

				destroy(slider);
				sliders = sliders.splice(i, 1);
				break;

			}

		}

	}

	// garbage collection
	function destroy(slider) {

		var layrs = slider.layrs,
			len = layrs.length;

		while (len--) {

			$.removeData(layrs[len].el, 'typewriter');

		}

		for (var prop in slider) {

			if (slider.hasOwnProperty(prop)) {

				delete slider[prop];

			}

		}

	}


})();