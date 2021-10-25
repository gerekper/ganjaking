/**
 * @preserve
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2019 ThemePunch
 * @version 2.1.9
 */

;(function() {
	var isFirefox = false;
	var radMin = Math.PI / 2 - 0.4;
	var radMax = Math.PI / 2 + 0.4;
	var TMBlock = {
		x: 0,
		y: 0,
		block: false
	};

	function setTMBlock(e) {
		TMBlock.x = e.clientX;
		TMBlock.y = e.clientY;
		TMBlock.block = false;
	}

	function calculateTMBlock(e) {
		var dx = TMBlock.x - e.clientX;
		var dy = TMBlock.y - e.clientY;
		var angle = Math.abs(Math.atan2(dy, dx));

		if (angle > radMin && angle < radMax) {
			TMBlock.block = 'no';
		} else {
			TMBlock.block = 'yes';
		}
	}


	jQuery('rs-slide[data-revaddonpaintbrush]').each(function() {
		
		var $this = jQuery(this),
			img = $this.children('img'),
			bgColor = img.attr('data-bg'),
			color = '';
			
		if(bgColor) {
			bgColor = bgColor.split('c:');
			if(bgColor.length > 1) color = bgColor[1].split(';')[0];
		}
		
		var bg = 'p:center center;'
		if(color) bg += 'c:' + color + ';';
			
		img.attr({'data-bg': bg, 'data-kenburns': 'off'}).removeAttr('data-panzoom');
		if($this.attr('data-revaddonpaintbrushedges')) $this.attr('data-anim', 'ei:d;eo:d;s:1000;r:0;t:fade;sl:0;');
		
	});
	
	jQuery('rs-slide[data-revaddonpaintbrushfallback]').each(function() {
		
		var $this = jQuery(this),
			img = $this.children('img'),
			fallback = $this.attr('data-revaddonpaintbrushfallback'),
			lazyload = img.attr('data-lazyload'),
			attr = lazyload ? 'data-lazyload' : 'src';

		if(!lazyload) img.attr(attr, fallback);
		else img.data('lazyload', fallback);
		
	});
	
	function getDefaults() {
	
		return {
		
			blurAmount: 10,
			fadetime: 1000,
			edgefix: 10,
			fixedges: false,
			style: 'round',
			blur: false,
			scaleblur: false,
			responsive: false,
			disappear: false,
			carousel: false
		
		};
	
	}
	
	var $,
		touch = 'ontouchend' in document;
		
	window.RevSliderPaintBrush = function(_$, api) {
		
		$ = _$;
		if(!$) return;
		
		
		var opts = $.fn.revolution && $.fn.revolution[api[0].id] ? $.fn.revolution[api[0].id] : false;
		if(!opts) return;
		isFirefox = $.fn.revolution.isFirefox();

		api.on('revolution.slide.onloaded', function() {
			
			var css = '',
				levels = opts.responsiveLevels,
				widths = opts.gridwidth;
			
			if(!Array.isArray(levels)) levels = [levels];
			if(!Array.isArray(widths)) widths = [widths];
			
			api.find('rs-slide[data-revaddonpaintbrush]').each(function() {
		
				var clas,
					edgeFix,
					fixEdges,
					scaleblur,
					img = new Image(),
					$this = $(this).addClass('revaddon-paintbrush').data('paintbrushloading', true),
					index = $this.attr('data-key'),
					slot = $this.find('rs-sbg-wrap'),
					options = JSON.parse(this.getAttribute('data-revaddonpaintbrush'));
					
				options = $.extend(true, getDefaults(), options);
				if(options.blur) {
					
					clas = 'revaddonblurfilter_' + index;

					if(!options.scaleblur) {
						css += '.' + clas + ' rs-sbg, .' + clas + ' .slot {filter: blur(' + options.blurAmount + 'px);}';
					}
					else {
						scaleblur = clas;
					}
					
					$this.addClass(clas);
					
				}
				
				if(options.fixedges && options.edgefix) {
					
					edgeFix = 1 + (options.edgefix * 0.01);
					fixEdges = edgeFix.toFixed(2);
					fixEdges = 'scale(' + fixEdges + ', ' + fixEdges + ')';
					slot.find('rs-sbg').css('transform', fixEdges);
					
					clas = 'revaddonblurfilterfix_' + index;
					css += '.' + clas + ' rs-sbg {transform: ' + fixEdges + ' !important}';
					$this.addClass(clas);
					
				}

				img.onload = function() {
					
					options.width = this.naturalWidth;
					options.height = this.naturalHeight;
		
					var brush = new Brush(api, options, $this, img, slot[0], levels, widths, fixEdges, edgeFix, scaleblur)
					$this.removeData('paintbrushloading').data('revaddonbrush', brush);
					
					if($this.data('paintbrushcurrent')) {
						
						brush.pause = false;
						$this.removeData('paintbrushcurrent');
						if(!brush.inited) brush.init();
						
					}
					
				};
				
				img.onerror = function() {
					
					console.log('PaintBrush Addon: background image could not be loaded');
					
				};
				
				img.src = options.image;
				
			});
			
			if(css) {
				
				var style = document.createElement('style');
				style.type = 'text/css';
				style.innerHTML = css;
				document.head.appendChild(style);
				
			}
			
		}).on('revolution.slide.onbeforeswap', function(e, data) {
			
			data.currentslide.removeData('paintbrushcurrent');
			var brush = data.currentslide.data('revaddonbrush');
			if(brush && brush.canvas) brush.canvas.className = 'revaddonpaintbrush swapping';
			
		}).on('revolution.slide.onafterswap', function(e, data) {
			
			/*
				data.currentSlide and data.prevSlide are not correct anymore
			*/
		
			var brush,
				imgLoading;
				
			api.find('.revaddon-paintbrush').each(function() {
			
				brush = $(this).removeData('paintbrushcurrent').data('revaddonbrush');
				if(brush) {
					
					brush.pause = true;
					brush.reset();
					if(brush.canvas) brush.canvas.className = 'revaddonpaintbrush';
					
				}
				
			});
			
			var slideIndex = api.revcurrentslide() - 1,
				currentSlide = api.find('rs-slide').eq(slideIndex);
				
			if(!currentSlide.length) currentSlide = api.find('rs-slide').eq(0);
			brush = currentSlide.data('revaddonbrush');
			
			if(!brush) {
					
				imgLoading = currentSlide.data('paintbrushloading');
				if(imgLoading) currentSlide.data('paintbrushcurrent', true);
				return;
				
			}
			
			brush.pause = false;
			brush.ready = true;
			
			if(!brush.inited) brush.init();
			
		});
		
	};
	
	function Brush(api, options, slide, img, slot, levels, widths, fixEdges, edgeFix, scaleblur) {

		this.pause = true;
		this.options = options;
		this.slide = slide;
		this.img = img;
		this.slot = slot;
		this.levels = levels;
		this.widths = widths;
		this.slider = api;
		this.fixEdges = fixEdges;
		this.edgeFix = edgeFix;
		this.frame = undefined;

		if(isFirefox){
			this.options.shadowBlur /= 2;
		}
		
		if(scaleblur) {
			
			var style = document.createElement('style');
			style.type = 'text/css';
			document.head.appendChild(style);
			
			this.blurstyle = {sheet: style, css: '.' + scaleblur + ' rs-sbg, .' + scaleblur + ' .slot {filter: blur({{blur}}px);}'};
			this.resizeBlur();
			
			api.on('revolution.slide.afterdraw', this.blurSizer.bind(this));
			
		}
		
	}
	
	Brush.prototype = {
		
		init: function() {

			this.canvas = document.createElement('canvas');
			this.brush = document.createElement('canvas');
			this.canvas.className = 'revaddonpaintbrush';
			
			this.context = this.canvas.getContext('2d');
			this.ctx = this.brush.getContext('2d');
			
			this.slot.parentNode.insertBefore(this.canvas, this.slot.nextSibling);
			this.inited = true;
			this.steps = [];
			
			if(!this.options.carousel) this.start();
			else setTimeout(this.start.bind(this), 100);
			
		},
		
		start: function() {
			
			if(!this.options.carousel) {
				this.slider.on('touchstart', this.onTouchStart.bind(this));
				this.slider.on('mousemove touchmove', this.onMove.bind(this));
			} else {
				this.slide.on('touchstart', this.onTouchStart.bind(this));
				this.slide.on('mousemove touchmove', this.onMove.bind(this));
			}
			
			this.slider.on('revolution.slide.afterdraw', this.sizer.bind(this));
			this.resize();
			
		},

		onTouchStart: function(e) {
			
			if (touch) {
				var e = e.originalEvent;
				if (e.touches) e = e.touches[0];
				setTMBlock(e);
			}
			
		},
		
		onMove: function(e) {
			
			if(this.pause) return;
			if (TMBlock.block === 'no') return;

			if (touch) {
				var te = e;
				e = e.originalEvent;
				if (e.touches) e = e.touches[0];

				if (!TMBlock.block) calculateTMBlock(e);
				if (TMBlock.block === 'yes') {
					te.preventDefault();
				}
				if (TMBlock.block === 'no') {
					return;
				}
			}
			
			var rect = this.canvas.getBoundingClientRect();
			this.steps.unshift({time: Date.now(), x: e.clientX - rect.left, y: e.clientY - rect.top});

			if(this.frame === undefined) {
				this.draw();
			}
			
		},
		
		updateSteps: function() {
			
			var time = Date.now();
			for(var i = 0; i < this.steps.length; i++) {
				
				if(time - this.steps[i].time > this.options.fade) this.steps.length = i;
				
			}
			
		},
		
		paint: function() {
			
			var total = this.steps.length,
				time = Date.now(),
				alpha,
				dif;

			for(var i = 1; i < total; i++) {
				if(this.steps[i] === undefined) continue;
				dif = (time - this.steps[i].time) / this.options.fadetime;
				alpha = Math.max(1 - dif, 0);

				this.ctx.strokeStyle = 'rgba(0, 0, 0, ' + alpha + ')';
				
				this.ctx.beginPath();
				this.ctx.moveTo(this.steps[i - 1].x, this.steps[i - 1].y);
				this.ctx.lineTo(this.steps[i].x, this.steps[i].y);
				this.ctx.stroke();
				if(alpha === 0) {
					this.steps.splice(i, 1);
				}			
			}
		},
		
		draw: function() {			
			this.frame = cancelAnimationFrame(this.frame);
			if(this.steps.length > 1) this.frame = window.requestAnimationFrame(this.draw.bind(this));
			
			this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
			if(this.options.disappear) this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
			
			this.paint();

			this.context.globalCompositeOperation = 'source-over';
			this.context.drawImage(this.img, this.cx, this.cy, this.cw, this.ch, 0, 0, this.canvas.width, this.canvas.height);
			
			
			this.context.shadowBlur = this.options.strength;
			this.context.globalCompositeOperation = 'destination-in';
			this.context.drawImage(this.brush, 0, 0);						
			this.context.shadowBlur = 0;
		},
		
		reset: function() {
			
			if(this.context) {
				
				this.frame = cancelAnimationFrame(this.frame);
				this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
				this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
				
			}
			
		},
		
		sizer: function() {
			
			if(!this.options.carousel) {
				
				this.resize();
				
			}
			else {
				
				clearTimeout(this.timer);
				this.timer = setTimeout(this.resize.bind(this), 250);
				
			}
			
		},
		
		resize: function(getPerc) {
			
			if(!getPerc) this.reset();
			
			var w = this.slide.width(),
				h = this.slide.height();
				
			if(this.edgeFix) {
				
				w *= this.edgeFix;
				h *= this.edgeFix;
				
			}
			
			var perc = Math.min(w / this.options.width, h / this.options.height);
			if(getPerc) return perc;
			
			var wid = this.options.width * perc,
				high = this.options.height * perc,
				ratio = 1;
	  
			if(wid < w) ratio = w / wid;                             
			if(Math.abs(ratio - 1) < 1e-14 && high < h) ratio = h / high;

			var dpr = window.devicePixelRatio || 1;

			
			this.cw = (this.options.width / ((wid * ratio) / w)) ;
			this.ch = (this.options.height / ((high * ratio) / h)) ;
			this.cx = (this.options.width - this.cw) * 0.5;
			this.cy = (this.options.height - this.ch) * 0.5;
									
			this.canvas.width = this.brush.width =	 w*dpr;
			this.canvas.height = this.brush.height = h*dpr;

			this.canvas.style.width = w+"px";
			this.canvas.style.height = h+"px";

			this.cw *=dpr;
			this.ch *=dpr;		
			this.canvas.getContext('2d').scale(dpr,dpr)
					
			
			if(this.options.responsive) {
				
				var len = this.levels.length,
					level = 0;
				
				for(var i = 0; i < len; i++) {

					if(w < this.levels[i]) level = i;
					
				}
				
				var scale = Math.min(w / this.widths[level], 1);
				this.options.size = this.options.origsize * scale;
			}

			this.context.shadowColor = '#000000';

			this.ctx.lineCap = this.options.style;
			this.ctx.lineWidth = this.options.size;
			
		},
		
		blurSizer: function() {
			
			if(!this.options.carousel) {
				
				this.resizeBlur();
				
			}
			else {
				
				clearTimeout(this.blurTimer);
				this.blurTimer = setTimeout(this.resizeBlur.bind(this), 250);
				
			}
		
		},
		
		resizeBlur: function() {
			
			var blurstyle = this.blurstyle;	
			blurstyle.sheet.innerHTML = blurstyle.css.replace('{{blur}}', Math.max(Math.round(this.options.blurAmount * this.resize(true)), 1));
			
		}
		
	};
	
})();