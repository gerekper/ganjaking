/**
 * @preserve
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */

;
(function () {

	var $;
	window.RevSliderBeforeAfter = function (_$, slider, options) {

		if (!_$ || !slider || typeof punchgs === 'undefined') return;

		$ = _$;
		$.event.special.rsBeforeAfterDestroyed = {
			remove: function (evt) {
				evt.handler();
			}
		};

		new RsAddonBeforeAfter(slider, options);

	};

	function RsAddonBeforeAfter(slider, options) {

		var opt = $.fn.revolution && $.fn.revolution[slider[0].id] ? $.fn.revolution[slider[0].id] : false;
		if (!opt) return;

		this.slides = [];
		this.slider = slider;
		this.options = options;
		this.timer = this.onTimer.bind(this);

		if (!opt.fallbacks.disableFocusListener) {
			$(window).on('focus.rsaddonbeforeafter', this.onFocus.bind(this));
		}

		slider.one('revolution.slide.onloaded', this.onLoaded.bind(this))
			.one('rsBeforeAfterDestroyed', this.destroy.bind(this));

	}

	RsAddonBeforeAfter.prototype = {

		init: function () {

			var options = $.fn.revolution && $.fn.revolution[this.slider[0].id] ? $.fn.revolution[this.slider[0].id] : false;
			if (!options) return;

			var levels = options.responsiveLevels,
				widths = options.gridwidth;

			if (!Array.isArray(levels)) levels = [levels];
			if (!Array.isArray(widths)) widths = [widths];

			this.levels = levels;
			this.widths = widths;
			this.resize = this.onResize.bind(this);
			this.slider.addClass('rs-before-after-addon').on('revolution.slide.onbeforeswap', this.beforeSwap.bind(this))
				.on('revolution.slide.onafterswap', this.afterSwap.bind(this));

		},

		onLoaded: function () {
			prepareBeforeAfterSlides();
			var $this = this,
				id = this.slider[0].id,
				placer = this.slider.find('rs-static-layers');

			this.fullVideos = [];
			this.carousel = this.options.carousel;

			if (this.carousel) this.slider.addClass('before-after-carousel');
			if (!placer.length) placer = this.slider.find('rs-slides');


			this.slider.data('before-after-placer', placer).data('beforeafter-slides').each(function () {

				var slide = $(this),
					vidSolo,
					vidBg;

				// adjust for rows/groups
				slide.find('rs-layer-wrap').each(mapLayers);
				slide.find('.rs-fsv').each(function (i) {

					$this.fullVideos[i] = $(this).closest('rs-layer-wrap').addClass('rs-addon-beforeafter-video');

				});
				var befores = slide.find('rs-layer[data-beforeafter="before"], .rs-layer[data-beforeafter="before"], rs-group[data-beforeafter="before"], .rs-group[data-beforeafter="before"]').toArray().map(function (layr) {

					layr = $(layr);
					var row = layr.closest('rs-zone'),
						group = layr.closest('rs-group-wrap');
					return !row.length ? !group.length ? layr.closest('rs-layer-wrap') : group : row;

				});

				var afters = slide.find('rs-layer[data-beforeafter="after"], .rs-layer[data-beforeafter="after"], rs-group[data-beforeafter="after"], .rs-group[data-beforeafter="after"]').toArray().map(function (layr) {

					layr = $(layr);
					var row = layr.closest('rs-zone'),
						group = layr.closest('rs-group-wrap');
					return !row.length ? !group.length ? layr.closest('rs-layer-wrap') : group : row;

				});

				var options = slide.data('beforeafter-options');
				slide.addClass('rs-addon-beforeafter rs-addon-beforeafter-' + options.direction);
				if (!$this.carousel) slide.find('*').attr('draggable', false);

				var before = $('<div class="rs-addon-beforeafter-revealer rs-addon-beforeafter-before" />').append(befores).appendTo(slide),
					after = $('<div class="rs-addon-beforeafter-revealer rs-addon-beforeafter-after" />'),
					inner = $('<div class="rs-addon-beforeafter-inner" />').append(afters)[0],
					bgInner = document.createElement('div'),
					bg = document.createElement('div'),
					bgType = options.bgType;

				if (bgType === 'image' || bgType === 'external') {
					/*var bgimg = options.bgImage;
					bgimg = bgimg.replace(/\\'/g, '\'');
					bgimg = bgimg.replace(/\\"/g, '"');
					bgimg = bgimg.replace(/\\0/g, '\0');
					bgimg = bgimg.replace(/\\\\/g, '\\');*/

					bgInner.style.backgroundImage = 'url(' + options.bgImage + ')';
					bgInner.style.backgroundPosition = options.bgPos;
					bgInner.style.backgroundRepeat = options.bgRepeat;
					bgInner.style.backgroundSize = options.bgFit;

				} else if (bgType === 'solid') {

					bgInner.style.background = options.bgColor;

				} else if (bgType !== 'trans') {

					var vid = slide.data('beforeafter-video');
					if (vid) {

						vid.closest('rs-layer-wrap').addClass('rs-video-beforeafter');
						if (!$this.carousel) {

							vidBg = vid;
							vidSolo = slide.find('rs-bgvideo').length === 0;

						}

					}

				}

				var dataLink = slide.attr('data-link') || slide.attr('data-linktoslide');
				if (dataLink && slide.attr('data-seoz') === 'back') {

					before.addClass('rs-beforeafter-pointers');
					after.addClass('rs-beforeafter-pointers');

				}

				var clas = 'rs-addon-beforeafter-bg-inner';
				if (options.filter) clas += ' ' + options.filter;

				bg.className = 'rs-addon-beforeafter-bg';
				bgInner.className = clas;

				bg.appendChild(bgInner);
				slide.find('rs-sbg-wrap').append(bg);

				after.append(inner).insertBefore(before);
				$this.slides[$this.slides.length] = new RsBeforeAfterSlide(

					id,
					bg,
					inner,
					slide,
					vidBg,
					vidSolo,
					bgInner,
					options,
					after[0],
					before[0],
					$this.slider,
					$this.options,
					$this.carousel,
					slide.attr('data-key')

				);

			});

			this.init();

		},

		beforeSwap: function (e, data) {

			if (this.checkRemoved()) return;
			this.slide = false;

			var slide;
			if (data.currentslide.length) {

				slide = $.data(data.currentslide[0], 'rs-addon-beforeafter');
				if (slide) {

					slide.removeEvents();
					slide[slide.animateOut]();

				}

			}

			slide = $.data(data.nextslide[0], 'rs-addon-beforeafter');
			if (slide && slide.setup) slide.reset();

		},

		afterSwap: function (e, data) {

			if (this.checkRemoved()) return;

			/*
				data.currentSlide and data.prevSlide are not always correct anymore inside this event
			*/
			var slideIndex = this.slider.revcurrentslide() - 1,
				currentSlide = this.slider.find('rs-slide').eq(slideIndex);

			if (!currentSlide.length) currentSlide = this.slider.find('rs-slide').eq(0);
			if (currentSlide.hasClass('rs-addon-beforeafter')) {

				this.slide = $.data(currentSlide[0], 'rs-addon-beforeafter');

				if (!this.slide.setup) {
					this.onResize(false, true);
					this.slide.onSetup();
					this.slider.on('revolution.slide.afterdraw', this.resize);
				} else
					this.onResize(false); // If Addon Used in Modal, it should reset the sizes after starting it over again


				this.slide.reset(true);
				this.slide.reveal();
				this.slide.addEvents();

			}

		},

		checkRemoved: function () {

			// bounce if the slider has been removed from the DOM before the onloaded event fires
			if (!this.slider || !document.body.contains(this.slider[0])) {

				this.destroy();
				return true;

			}

			return false;

		},

		destroy: function () {

			$(window).off('.rsaddonbeforeafter');
			if (this.slides) {

				while (this.slides.length) {

					this.slides[0].destroy();
					this.slides.shift();

				}

			}

			for (var prop in this)
				if (this.hasOwnProperty(prop)) delete this[prop];

		},

		onFocus: function () {

			clearTimeout(this.timer);

			var i = this.slides.length;
			while (i--) this.slides[i].supress = true;

			this.focusTimer = setTimeout(this.timer, 100);

		},

		onTimer: function () {

			var i = this.slides.length;
			while (i--) this.slides[i].supress = false;

		},

		onResize: function (e, fromReset) {

			if (e && this.carousel) {

				clearTimeout(this.resizeTimer);
				this.resizeTimer = setTimeout(this.resize, 250);
				return;

			}

			var slide = this.slide;
			if (!slide) return;

			var width,
				height,
				instance,
				level = 0,
				leg = this.levels.length;

			if (!this.carousel) {

				width = this.slider.width();
				height = this.slider.height();

			} else {

				width = slide.slide.width();
				height = slide.slide.height();

			}

			if (width === 0 || height === 0) return;

			for (var i = 0; i < leg; i++)
				if (width < this.levels[i]) level = i;


			if (!fromReset) {
				punchgs.TweenLite.killTweensOf(slide.bg);
				punchgs.TweenLite.killTweensOf(slide.after);
				punchgs.TweenLite.killTweensOf(slide.before);
				punchgs.TweenLite.killTweensOf(slide.revealBtn);
				if (slide.revealLine) punchgs.TweenLite.killTweensOf(slide.revealLine);

			}

			var scale = width / this.widths[level],
				len = this.slides.length;

			while (len--) {

				instance = this.slides[len];
				instance.level = level;
				instance.scale = scale;
				instance.blurred = false;
				instance.sliderWidth = width;
				instance.sliderHeight = height;
				if (instance.normal) {
					instance.bgInner.style.width = width + 'px';
					instance.inner.style.width = width + 'px';
				} else {
					instance.bgInner.style.height = height + 'px';
					instance.inner.style.height = height + 'px';

				}

			}

			if (!fromReset) {

				var x, y;
				if (slide.normal) {

					x = slide.moveto[0];
					if (x.search('%') === -1) x = (parseInt(x, 10) * scale).toFixed(0) + 'px';
					y = '50%';

				} else {

					x = '50%';
					y = slide.moveto[0];
					if (y.search('%') === -1) y = (parseInt(y, 10) * scale).toFixed(0) + 'px';

				}

				slide.resetDrag(x, y);

			}

			len = this.fullVideos.length;
			if (len) {

				for (i = 0; i < len; i++) {
					this.fullVideos[i].css('width', width);
				}

			}

		}

	};

	function checkLink(el, reg) {

		while (el.parentNode) {

			el = el.parentNode;
			if (el.tagName === 'A' || reg.test(el.className)) return true;

		}

		return false;

	}

	function RsBeforeAfterSlide(id, bg, inner, slide, vidBg, vidSolo, bgInner, options, after, before, slider, globals, carousel, index) {

		this.id = id;
		this.bg = bg;
		this.index = index;
		this.slide = slide;
		this.inner = inner;
		this.after = after;
		this.before = before;
		this.slider = slider;
		this.videoBg = vidBg;
		this.bgInner = bgInner;
		this.globals = globals;
		this.videoSolo = vidSolo;
		this.carousel = carousel;
		this.animateOut = options.out;
		this.direction = options.direction;
		this.moveto = options.moveto.split('|');
		this.timing = parseInt(options.time, 10) * 0.001;
		this.delay = parseInt(options.delay, 10) * 0.001;

		var animation = options.easing.split('.');
		this.animation = animation.length === 2 ? punchgs[animation[0]][animation[1]] : punchgs.hasOwnProperty(animation[0]) ? punchgs[animation[0]] : punchgs.Power2.easeOut;

		if (this.direction === 'horizontal') {

			this.normal = true;
			this.axis = 'left';
			this.size = 'width';

		} else {

			this.axis = 'top';
			this.size = 'height';

		}

		if (this.globals.hasOwnProperty('onClick')) {
			var easing = this.globals.onClick.easing.split('.');
			this.time = parseInt(this.globals.onClick.time, 10) * 0.001;
			this.transition = typeof tpGS === 'object' ? this.globals.onClick.easing : easing.length === 2 ? punchgs[easing[0]][easing[1]] : punchgs.hasOwnProperty(easing[0]) ? punchgs[easing[0]] : punchgs.Power2.easeOut;
		}

		this.mouseUp = this.onMouseUp.bind(this);
		this.mouseMove = this.onMouseMove.bind(this);
		this.mouseClick = this.onClick.bind(this);
		this.complete = this.onComplete.bind(this);


		if (options.hasOwnProperty('bounceArrows')) {
			this.bounceArrows = options.bounceArrows;
			this.bounceDelay = parseInt(options.bounceDelay, 10);
			this.readyArrows = this.arrowsReady.bind(this);
			if (this.bounceDelay) this.delayBounce = this.bounceReady.bind(this);
		}

		if (options.hasOwnProperty('shiftOffset')) this.shiftArrows = options.shiftOffset;
		if (this.videoBg && !this.videoSolo) this.videoPlay = this.playVideo.bind(this);

		this.createDrag();
		$.data(slide[0], 'rs-addon-beforeafter', this);

	}

	RsBeforeAfterSlide.prototype = {

		createDrag: function () {

			var globals = this.globals,
				boxShadow = globals.boxShadow,
				arrowStyles = globals.arrowStyles,
				arrowShadow = globals.arrowShadow,
				arrowBorder = globals.arrowBorder,
				lineStyles = globals.dividerStyles,
				lineShadow = globals.dividerShadow,
				spacing = parseInt(arrowStyles.spacing, 10);

			var btn = '<span class="rs-addon-beforeafter-btn rs-before-after-element rs-addon-beforeafter-btn-' + this.direction + '" style="' +
				'color: ' + arrowStyles.color + ';' +
				'font-size: ' + parseInt(arrowStyles.size, 10) + 'px;' +
				'background-color:' + arrowStyles.bgColor + ';' +
				'padding: ' + parseInt(arrowStyles.padding, 10) + 'px;' +
				'border-radius: ' + arrowStyles.borderRadius + ';' +
				'cursor: ' + globals.cursor;

			if (boxShadow) btn += '; box-shadow: 0px 0px ' + parseInt(boxShadow.blur, 10) + 'px ' + parseInt(boxShadow.strength, 10) + 'px ' + boxShadow.color + ';';
			if (arrowBorder) btn += '; border: ' + parseInt(arrowBorder.size, 10) + 'px solid ' + arrowBorder.color + ';';
			if (arrowShadow) btn += '; text-shadow: 0px 0px ' + parseInt(arrowShadow.blur, 10) + 'px ' + arrowShadow.color + ';';

			var icon1,
				icon2,
				shell,
				padding1,
				padding2,
				bounce1 = '',
				bounce2 = '',
				translate1 = '',
				translate2 = '',
				shifts = this.shiftArrows ? ' rs-' + this.id + '-' + this.index + '-rs-beforeafter-shift' : '';

			if (this.normal) {

				padding1 = 'padding-right';
				padding2 = 'padding-left';
				icon1 = arrowStyles.leftIcon;
				icon2 = arrowStyles.rightIcon;

				if (this.bounceArrows) {

					shell = ' rs-' + this.id + '-' + this.index + '-rs-beforeafter-bounce-';
					bounce1 = shell + 'left';
					bounce2 = shell + 'right';

				}

				if (this.shiftArrows) {

					translate1 = 'transform: translateX(-' + this.shiftArrows + 'px);';
					translate2 = 'transform: translateX(' + this.shiftArrows + 'px);';

				}

			} else {

				padding1 = 'margin-bottom';
				padding2 = 'margin-top';
				icon1 = arrowStyles.topIcon;
				icon2 = arrowStyles.bottomIcon;

				if (this.bounceArrows) {

					shell = ' rs-' + this.id + '-' + this.index + '-rs-beforeafter-bounce-';
					bounce1 = shell + 'top';
					bounce2 = shell + 'bottom';

				}

				if (this.shiftArrows) {

					translate1 = 'transform: translateY(-' + this.shiftArrows + 'px);';
					translate2 = 'transform: translateY(' + this.shiftArrows + 'px);';

				}

			}

			btn += '" />';

			this.btn1 = $('<i class="' + icon1 + shifts + bounce1 + '" style="' + translate1 + padding1 + ': ' + spacing + 'px">');
			this.btn2 = $('<i class="' + icon2 + shifts + bounce2 + '" style="' + translate2 + padding2 + ': ' + spacing + 'px">');

			this.btn = $(btn).on('mousedown touchstart', this.onMouseDown.bind(this));
			this.btn[0].appendChild(this.btn1[0]);
			this.btn[0].appendChild(this.btn2[0]);

			var lineSize = parseInt(lineStyles.width, 10),
				appends = [];

			if (lineSize) {

				var margin = this.normal ? 'margin-left: ' : 'margin-top: ',
					halfLine = -Math.floor(lineSize * 0.5),
					line = '<span class="rs-addon-beforeafter-line rs-before-after-element rs-beforeafter-' + this.direction + '"' +
					' style="' + this.size + ': ' + lineSize + 'px; ' + margin + halfLine + 'px; background-color: ' + lineStyles.color;

				if (lineShadow) line += '; box-shadow: 0px 0px ' + parseInt(lineShadow.blur, 10) + 'px ' + parseInt(lineShadow.strength, 10) + 'px ' + lineShadow.color + ';';
				line += '"></span>';

				this.revealLine = $(line)[0];
				this.pixel = lineSize % 2 === 0 ? 0 : 1;

				appends[0] = this.revealLine;

			}

			this.revealBtn = this.btn[0];
			appends[appends.length] = this.revealBtn;

			if (!this.carousel) {

				$(appends).insertAfter(this.slider.data('before-after-placer'));

			} else {

				this.slide.append(appends);

			}

		},

		onSetup: function () {

			var arrowWidth = this.btn.outerWidth(true),
				arrowHeight = this.btn.outerHeight(true),
				equalSize = Math.max(arrowWidth, arrowHeight),
				pixelX = 0,
				pixelY = 0;

			if (this.revealLine) {

				if (this.normal) pixelX += this.pixel;
				else pixelY += this.pixel;

			}

			var halfSize = Math.floor(equalSize * 0.5);
			this.buffer = halfSize;

			this.setup = true;
			this.btn.css({

				width: equalSize,
				height: equalSize,
				marginTop: -halfSize + pixelY,
				marginLeft: -halfSize + pixelX

			});

			delete this.btn;

		},

		addEvents: function () {

			var container = !this.carousel ? this.slider : this.slide;

			container.on('mouseup.rsaddonbeforeafter mouseleave.rsaddonbeforeafter touchend.rsaddonbeforeafter', this.mouseUp)
				.on('mousemove.rsaddonbeforeafter touchmove.rsaddonbeforeafter', this.mouseMove);

			if (this.transition) container.on('click.rsaddonbeforeafter', this.mouseClick);
			this.lerpDone = true;
			this.boundLerpHandler = this.lerpHandler.bind(this);
			this.mouse = {
				x: 0,
				y: 0
			}
		},

		removeEvents: function () {

			this.onMouseUp();
			var container = !this.carousel ? this.slider : this.slide;
			container.off('.rsaddonbeforeafter');

			if (this.shiftArrows) this.btn1.off('.rsaddonbeforeafter');
			if (this.bounceDelay) clearTimeout(this.bounceTimer);

		},

		updateDrag: function (x, y) {

			if (!this.before) return;
			if (this.rAfDrag) this.rAfDrag = cancelAnimationFrame(this.rAfDrag);

			var beforePoint,
				afterPoint;

			if (this.normal) {

				beforePoint = x;
				afterPoint = this.sliderWidth - x;
				y = Math.min(this.sliderHeight - this.buffer, Math.max(y, this.buffer));

			} else {

				beforePoint = y;
				afterPoint = this.sliderHeight - y;
				x = Math.min(this.sliderWidth - this.buffer, Math.max(x, this.buffer));

			}

			this.revealBtn.style.left = x + 'px';
			this.revealBtn.style.top = y + 'px';

			this.before.style[this.size] = beforePoint + 'px';
			this.after.style[this.size] = afterPoint + 'px';
			this.bg.style[this.size] = afterPoint + 'px';

			if (this.revealLine) this.revealLine.style[this.axis] = beforePoint + 'px';

		},

		resetDrag: function (x, y) {

			if (!this.before) return;

			var beforePoint,
				afterPoint;

			if (this.normal) {

				beforePoint = x;
				afterPoint = parseInt(x, 10);
				afterPoint = x.search('%') !== -1 ? (100 - afterPoint) + '%' : (this.sliderWidth - afterPoint) + 'px';

			} else {

				beforePoint = y;
				afterPoint = parseInt(y, 10);
				afterPoint = y.search('%') !== -1 ? (100 - afterPoint) + '%' : (this.sliderHeight - afterPoint) + 'px';

			}

			this.revealBtn.style.left = x;
			this.revealBtn.style.top = y;
			this.before.style[this.size] = beforePoint;
			this.after.style[this.size] = afterPoint;
			this.bg.style[this.size] = afterPoint;

			if (this.revealLine) this.revealLine.style[this.axis] = beforePoint;

		},

		onMouseDown: function (e) {

			this.prevent = true;
			this.canDrag = true;
			this.slider.addClass('dragging');

			if (this.shiftArrows) {

				this.btn1.off('.rsaddonbeforeafter');
				this.slider.addClass('rs-beforeafter-shift-arrows');

			}

			if (this.bounceArrows) {

				if (this.bounceDelay) clearTimeout(this.bounceTimer);
				this.slider.removeClass('rs-beforeafter-bounce-arrows');

			}

			if (this.carousel) e.stopImmediatePropagation();

			var touch = e.originalEvent.touches;
			if(touch) {
				e = touch[0];
				var container = !this.carousel ? this.slider : this.slide,
					offset = container.offset();
				this.mouse.x = e.pageX - offset.left;
				this.mouse.y = e.pageY - offset.top;
			}

		},

		onMouseMove: function (e) {

			if (!this.supress && this.canDrag) {

				var touch = e.originalEvent.touches;
				if(touch) e = touch[0];

				var container = !this.carousel ? this.slider : this.slide,
					offset = container.offset(),
					x = e.pageX - offset.left,
					y = e.pageY - offset.top;

				var check = this.normal ? x > 0 && x < this.sliderWidth : y > 0 && y < this.sliderHeight;
				if (check) {
					this.x = x;
					this.y = y;

					if (!touch) {
						if (!this.rAfDrag) this.rAfDrag = requestAnimationFrame(this.updateDrag.bind(this, x, y));
					} else if (this.lerpDone) {
						this.lerpDone = false;
						punchgs.gsap.ticker.add(this.boundLerpHandler);
					}
				}

			}

		},

		lerpHandler: function (x, y) {
			var dx = this.x - this.mouse.x;
			var dy = this.y - this.mouse.y;

			if (Math.abs(dx) > 0.01 && Math.abs(dy) > 0.01) {
				this.mouse.x += dx * 0.05;
				this.mouse.y += dy * 0.05;
				this.updateDrag(this.mouse.x, this.mouse.y);
			} else {

				this.lerpDone = true;
				punchgs.gsap.ticker.remove(this.boundLerpHandler);
			}

		},

		onMouseUp: function (e) {

			this.canDrag = false;
			this.slider.removeClass('dragging');

			if (e && this.bounceArrows === 'infinite') {

				if (!this.bounceDelay) {

					if (this.shiftArrows) this.slider.removeClass('rs-beforeafter-shift-arrows');
					this.slider.addClass('rs-beforeafter-bounce-arrows');

				} else {

					this.bounceTimer = setTimeout(this.delayBounce, this.bounceDelay);

				}

			}

		},

		onClick: function (e) {
			if (this.supress || this.blurred) return;
			if (this.prevent) {

				this.prevent = false;
				return;

			}

			var targ = e.target,
				reg = /tparrows|tp-bullet|tp-tab|tp-thumb|rs-waction/,
				exclude = e.target.tagName === 'A' || reg.test(targ.className) || checkLink(targ, reg);

			if (exclude) return;

			var container = !this.carousel ? this.slider : this.slide,
				offset = container.offset(),
				x = e.pageX - offset.left,
				y = e.pageY - offset.top,
				sliderSize,
				point;


			if (this.normal) {

				point = x;
				sliderSize = this.sliderWidth;
				y = Math.min(this.sliderHeight - this.buffer, Math.max(y, this.buffer));

			} else {

				point = y;
				sliderSize = this.sliderHeight;
				x = Math.min(this.sliderWidth - this.buffer, Math.max(x, this.buffer));

			}
			// button
			punchgs.TweenLite.to(this.revealBtn, this.time, {
				left: x,
				top: y,
				ease: this.transition
			});

			// bg
			var obj = {
				ease: this.transition
			};
			obj[this.size] = sliderSize - point;
			punchgs.TweenLite.to(this.bg, this.time, obj);

			// before
			obj = {
				ease: this.transition
			};
			obj[this.size] = point;
			punchgs.TweenLite.to(this.before, this.time, obj);

			// after
			obj = {
				ease: this.transition
			};
			obj[this.size] = sliderSize - point;
			punchgs.TweenLite.to(this.after, this.time, obj);

			// line
			obj = {
				ease: this.transition
			};
			obj[this.axis] = point;
			if (this.revealLine) punchgs.TweenLite.to(this.revealLine, this.time, obj);

		},

		bounceReady: function () {

			this.slider.removeClass('rs-beforeafter-shift-arrows').addClass('rs-beforeafter-bounce-arrows');

		},

		arrowsReady: function () {

			if (!this.bounceDelay) this.bounceReady();
			else this.bounceTimer = setTimeout(this.delayBounce, this.bounceDelay);

		},

		onComplete: function () {

			this.supress = false;
			if (this.shiftArrows) {

				if (this.bounceArrows) this.btn1.one('webkitTransitionEnd.rsaddonbeforeafter transitionend.rsaddonbeforeafter', this.readyArrows);
				this.slider.addClass('rs-beforeafter-shift-arrows');

			} else if (this.bounceArrows) {

				this.arrowsReady();

			}

		},

		fade: function () {

			punchgs.TweenLite.to(this.bg, 0.3, {
				opacity: 0,
				ease: punchgs.Power2.easeInOut
			});
			punchgs.TweenLite.to(this.revealBtn, 0.3, {
				autoAlpha: 0,
				ease: punchgs.Power2.easeInOut
			});
			if (this.revealLine) punchgs.TweenLite.to(this.revealLine, 0.3, {
				autoAlpha: 0,
				ease: punchgs.Power2.easeInOut
			});

		},

		collapse: function () {

			// bg
			obj = {
				ease: this.animation
			};
			obj[this.size] = 0;
			punchgs.TweenLite.to(this.bg, this.timing, obj);

			// before
			var obj = {
				ease: this.animation
			};
			obj[this.size] = '100%';
			punchgs.TweenLite.to(this.before, this.timing, obj);

			// after
			obj = {
				ease: this.animation
			};
			obj[this.size] = 0;
			punchgs.TweenLite.to(this.after, this.timing, obj);

			// line
			obj = {
				autoAlpha: 0,
				ease: this.animation
			};
			obj[this.axis] = '100%';
			if (this.revealLine) punchgs.TweenLite.to(this.revealLine, this.timing, obj);

			// button
			obj = {
				autoAlpha: 0,
				ease: this.animation
			};
			obj[this.axis] = '100%';
			punchgs.TweenLite.to(this.revealBtn, this.timing, obj);

		},

		reset: function (removeClasses) {

			this.supress = true;

			if (this.normal) {

				this.revealBtn.style.top = '50%';
				this.revealBtn.style.left = '100%';

			} else {

				this.revealBtn.style.top = '100%';
				this.revealBtn.style.left = '50%';

			}

			this.before.style[this.size] = '100%';
			this.after.style[this.size] = '0';
			this.bg.style[this.size] = '0';

			if (this.revealLine) this.revealLine.style[this.axis] = '100%';
			if (this.shiftArrows) this.btn1.off('.rsaddonbeforeafter');
			if (this.bounceDelay) clearTimeout(this.bounceTimer);

			if (removeClasses) {

				if (this.shiftArrows) this.slider.removeClass('rs-beforeafter-shift-arrows');
				if (this.bounceArrows) this.slider.removeClass('rs-beforeafter-bounce-arrows');

			}

		},

		playVideo: function () {

			this.videoBg.closest('rs-slide').find('.rs-beforeafter-videotrigger').click();

		},

		checkVideo: function () {

			var vid = this.slide.find('.rs-background-video-layer video');
			if (vid.length) vid.off('.rsaddonbeforeafter').on('play.rsaddonbeforeafter', this.videoPlay);
			else this.playVideo();

		},

		reveal: function () {

			if (this.videoBg) {

				if (this.videoSolo) this.playVideo();
				else this.checkVideo();

			}

			var point = this.moveto[0],
				perc = point.search('%') !== -1,
				parsed = parseInt(point, 10),
				beforePoint,
				afterPoint,
				btnX,
				btnY;

			if (this.normal) {

				if (perc) {

					btnX = beforePoint = point;
					afterPoint = (100 - parsed) + '%';

				} else {

					point = beforePoint = btnX = parsed * this.scale;
					afterPoint = this.sliderWidth - point;

				}

				btnY = '50%';

			} else {

				if (perc) {

					btnY = beforePoint = point;
					afterPoint = (100 - parsed) + '%';

				} else {

					point = beforePoint = btnY = parsed * this.scale;
					afterPoint = this.sliderHeight - point;

				}

				btnX = '50%';

			}

			// bg
			obj = {
				ease: this.animation,
				delay: this.delay
			};
			obj[this.size] = afterPoint;
			this.bg.style.opacity = '1';
			punchgs.TweenLite.to(this.bg, this.timing, obj);

			// before
			var obj = {
				ease: this.animation,
				delay: this.delay
			};
			obj[this.size] = beforePoint;
			punchgs.TweenLite.to(this.before, this.timing, obj);

			// after
			obj = {
				ease: this.animation,
				delay: this.delay
			};
			obj[this.size] = afterPoint;
			this.after.style.opacity = '1';
			punchgs.TweenLite.to(this.after, this.timing, obj);

			// line
			if (this.revealLine) {

				obj = {
					ease: this.animation,
					delay: this.delay
				};
				obj[this.axis] = point;
				punchgs.TweenLite.to(this.revealLine, 0.3, {
					autoAlpha: 1,
					ease: punchgs.Power2.easeOut
				});
				punchgs.TweenLite.to(this.revealLine, this.timing, obj);

			}

			// button
			punchgs.TweenLite.to(this.revealBtn, 0.3, {
				autoAlpha: 1,
				ease: punchgs.Power2.easeOut
			});
			punchgs.TweenLite.to(this.revealBtn, this.timing, {
				delay: this.delay,
				left: btnX,
				top: btnY,
				ease: this.animation,
				onComplete: this.complete
			});

		},

		destroy: function () {

			punchgs.TweenLite.killTweensOf(this.bg);
			punchgs.TweenLite.killTweensOf(this.after);
			punchgs.TweenLite.killTweensOf(this.before);
			punchgs.TweenLite.killTweensOf(this.revealBtn);
			if (this.revealLine) punchgs.TweenLite.killTweensOf(this.revealLine);

			$.removeData(this.slide[0], 'rs-addon-beforeafter', this);
			for (var prop in this)
				if (this.hasOwnProperty(prop)) delete this[prop];

		}

	};

	function adjustLayers(i) {

		if (i > 0) this.removeAttribute('data-beforeafter');

	}

	function mapLayers() {

		var layrs = $(this).find('rs-layer, .rs-layer');
		if (layrs.length > 1) layrs.each(adjustLayers);

	}

	function updateIndexes(i) {

		var index = i + 5;
		this.style.zIndex = index.toString();

	}

	function bounceShell(id, index, tpe, dist, halfDist, speed, axis, num, times, ease) {

		return '@-webkit-keyframes ' + id + '-' + index + '-rs-beforeafter-bounce-' + tpe + ' {' +
			'0%, 20%, 50%, 80%, 100% {' +
			'-webkit-transform: translate' + axis + '(0);' +
			'transform: translate' + axis + '(0)' +
			'}' +
			'40% {' +
			'-webkit-transform: translate' + axis + '(' + dist + 'px);' +
			'transform: translate' + axis + '(' + num + dist + 'px)' +
			'}' +
			'60% {' +
			'-webkit-transform: translate' + axis + '(' + halfDist + 'px);' +
			'transform: translate' + axis + '(' + num + halfDist + 'px)' +
			'}' +
			'}' +
			'@keyframes ' + id + '-' + index + '-rs-beforeafter-bounce-' + tpe + ' {' +
			'0%, 20%, 50%, 80%, 100% {' +
			'-webkit-transform: translate' + axis + '(0);' +
			'transform: translate' + axis + '(0)' +
			'}' +
			'40% {' +
			'-webkit-transform: translate' + axis + '(' + dist + 'px);' +
			'transform: translate' + axis + '(' + num + dist + 'px)' +
			'}' +
			'60% {' +
			'-webkit-transform: translate' + axis + '(' + halfDist + 'px);' +
			'transform: translate' + axis + '(' + num + halfDist + 'px)' +
			'}' +
			'}' +
			'.rs-beforeafter-bounce-arrows .rs-' + id + '-' + index + '-rs-beforeafter-bounce-' + tpe + ' {' +
			'-webkit-animation: ' + id + '-' + index + '-rs-beforeafter-bounce-' + tpe + ' ' + speed + 's ' + ease + ' ' + times + ';' +
			'animation: ' + id + '-' + index + '-rs-beforeafter-bounce-' + tpe + ' ' + speed + 's ' + ease + ' ' + times +
			'}';

	}

	function shiftShell(id, index, time, ease, delay) {

		return '.rs-beforeafter-shift-arrows .rs-' + id + '-' + index + '-rs-beforeafter-shift {' +
			'-webkit-transition: all ' + time + 's ' + ease + ' ' + delay + 's;' +
			'transition: all ' + time + 's ' + ease + ' ' + delay + 's' +
			'}';

	}

	function prepareBeforeAfterSlides() {
		jQuery('rs-module').each(function () {
			var css = '',
				slider = jQuery(this),
				sliderId = slider[0].id,
				slides = slider.find('rs-slide[data-beforeafter]');

			if (!slides.length) return;
			if (this.dataset.beforeAfterInited !== undefined) return;
			this.dataset.beforeAfterInited = true;
			slides.each(function () {

				var speed,
					$this = jQuery(this),
					slideIndex = $this.attr('data-key'),
					options = JSON.parse($this.attr('data-beforeafter'));

				$this.data('beforeafter-options', options);
				if (/html5|youtube|vimeo/.test(options.bgType)) {

					var index = $this.attr('data-key').replace('rs-', ''),
						addVideo;

					var shellEnd = ' style="z-index: 5"></rs-layer>',
						vId = 'slide-' + index + '-layer-999',
						shell =

						'<rs-layer class="rs-layer rs-layer-video" ' +
						'data-rsp_ch="on" ' +
						'id="' + vId + '" ' +
						'data-type="video" ' +
						'data-xy="x:0;y:0;" ' +
						'data-beforeafter="after" ' +
						'data-basealign="slide" ' +
						'data-dim="w:100%;h:100%;" ' +
						'data-border="bow:0,0,0,0;" ' +
						'data-frame_999="st:w;" ' +
						'data-frame_1="st:a;"';

					var dataVideo = ' data-video="v:mute;inl:t;sav:f;';
					switch (options.bgType) {

						case 'html5':

							var mpeg = options.videoMpeg;
							if (mpeg) {

								dataVideo += 'data-mp4="' + mpeg + '" ';
								addVideo = true;

							}

							break;

						case 'youtube':

							var id = options.videoId;
							if (id) {

								shell += ' data-ytid="' + id + '"';
								dataVideo += 'sp:' + options.videoSpeed + ';';
								shell += ' data-vatr="' + options.youtubeArgs + '"';
								//shell += 'data-videoinline="true" ';

								// if(options.muteVideo) dataVideo += 'data-volume="mute" ';
								// else dataVideo += 'data-volume="' + options.videoVolume + '" ';
								addVideo = true;

							}

							break;

						case 'vimeo':

							var ids = options.videoId;
							if (ids) {

								dataVideo += 'data-vimeoid="' + ids + '" ';
								dataVideo += 'data-vatr="' + options.vimeoArgs + '" ';

								// if(options.muteVideo) dataVideo += 'data-volume="mute" ';
								// else dataVideo += 'data-volume="' + options.videoVolume + '" ';
								addVideo = true;

							}

							break;

					}

					if (addVideo) {

						if (options.forceCover) dataVideo += 'fc:' + options.forceCover + ';';
						if (options.dottedOverlay !== 'none') dataVideo += 'do:' + options.dottedOverlay + ';';
						if (options.nextSlideOnEnd) dataVideo += 'nse:' + options.nextSlideOnEnd + ';';
						if (options.rewindOnStart) dataVideo += 'rwd:' + options.rewindOnStart + ';';
						if (options.videoStartAt) dataVideo += 'sta:' + options.videoStartAt + ';';
						if (options.videoEndAt) dataVideo += 'end:' + options.videoEndAt + ';';
						if (options.aspectRatio) dataVideo += 'ar:' + options.aspectRatio + ';';

						var v = $this.find('rs-bgvideo');
						if (v.length) v.attr('data-video', v.attr('data-video') + ';sav:f');
						if (options.poster) shell += ' data-poster="' + options.poster + '"';

						shell += dataVideo + '"';
						$this.find('rs-layer, .rs-layer').each(updateIndexes);
						var video = jQuery(shell + shellEnd).insertAfter($this.children('img'));
						$this.data('beforeafter-video', video);

						if (!options.carousel) {

							// this allows for synching before/after background videos
							jQuery('<rs-layer' +
								'class="rs-layer rs-beforeafter-videotrigger"' +
								'data-type="shape" ' +
								'data-xy="x:r;y:b;" ' +
								'data-dim="w:0px,0px,0px,0px;h:0px,0px,0px,0px;" ' +
								'data-vbility="f,f,f,f" ' +
								'data-actions="o:click;a:startlayer;layer:' + vId + ';" ' +
								'data-basealign="slide" ' +
								'data-rsp_o="off" ' +
								'data-rsp_bd="off" ' +
								'data-border="bow:0,0,0,0;" ' +
								'data-frame_999="st:w;" ' +
								'data-beforeafter="before"  ' +
								'style="z-index:6;font-family:Roboto;" ' +
								'>').insertAfter(video);

						}

					}

				}

				if (options.hasOwnProperty('bounceArrows')) {

					speed = parseInt(options.bounceSpeed, 10) * 0.001;

					var axis,
						arrows,
						times = options.bounceArrows !== 'initial' ? 'infinite' : '1',
						dist = parseInt(options.bounceAmount, 10),
						repel = options.bounceType === 'repel',
						halfDist = Math.round(dist * 0.5);

					if (options.direction === 'horizontal') {

						axis = 'X';
						arrows = ['left', 'right'];

					} else {

						axis = 'Y';
						arrows = ['top', 'bottom'];

					}

					for (var i = 0; i < 2; i++) {

						var num = repel ? i === 0 ? '-' : '' : i === 0 ? '' : '-';
						css += bounceShell(sliderId, slideIndex, arrows[i], dist, halfDist, speed.toFixed(2), axis, num, times, options.bounceEasing);

					}

				}

				if (options.hasOwnProperty('shiftOffset')) {

					speed = parseInt(options.shiftTiming, 10) * 0.001;
					var delayed = parseInt(options.shiftDelay, 10) * 0.001;

					for (var j = 0; j < 2; j++) {

						css += shiftShell(sliderId, slideIndex, speed, options.shiftEasing, delayed);

					}

				}

			});

			if (css) {

				var style = document.createElement('style');
				style.type = 'text/css';
				style.innerHTML = css;
				document.head.appendChild(style);

			}
			slider.data('beforeafter-slides', slides);
		});
	}

	if (typeof jQuery !== 'undefined') prepareBeforeAfterSlides();

})();