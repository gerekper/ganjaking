/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2018 ThemePunch
 */
;(function() {
	
	var $,
		touch = 'ontouchend' in document,
		_R;

	window.RsLiquideffectAddOn = function(_$, slider, base, lazyType) {
		
		_R = jQuery.fn.revolution;
		
		
		if(!_$ || typeof PIXI === 'undefined') return;
		PIXI.utils.skipHello();
		slider = _$(slider);
		if(!slider.length) return;
  
		$ = _$;
		$.event.special.rsLiquidEffectDestroyed = {remove: function(evt) {evt.handler();}};
		
		var effects = slider.find('rs-slide[data-liquideffect]').each(function() {
			
			var url,
				$this = $(this),
				settings = JSON.parse($this.attr('data-liquideffect'));
				
			if(!settings) return; 
			settings.transcross = true;
			
			var sprite,
				img = $this.children('img');
			
			if(!img.attr('data-lazyload')) {
				
				sprite = img.attr('src');
				img.attr('data-lazyload', sprite);
				img.attr('src', base + 'public/assets/assets/dummy.png');
				
			}
			else {
				sprite = img.attr('data-lazyload');
			}
			
			
			if(!sprite) {
				
				sprite = $this.find('rs-sbg-wrap').contents().filter(function() {return this.nodeType === 8;});
				if(sprite.length) {
					
					url = sprite[0].nodeValue;
					sprite = false;
					
					if(url) {
						
						url = url.split('data-lazyload="');
						if(url.length === 2) {
							
							url = url[1].split('"');
							if(url.length > 1) sprite = url[0];
							
						}
						else {
							
							url = url.split('src="');
							if(url.length === 2) {
								
								url = url[1].split('"');
								if(url.length > 1) sprite = url[0];
								
							}
						}
					}
					
				}
				else {
					sprite = false;
				}
				
			}
			
			if(sprite) {
				
				settings.sprite = sprite; 
				if(touch && settings.mobile) settings.interactive = false;
					
				var st = 'ei:' + settings.easing + ';eo:' + settings.easing + ';s:' + settings.transtime + 'ms;r:0;t:';
				st += settings.transcross ? 'crossfade' : 'fade';
				st += ';sl:0;';
				
				var easing = settings.easing.split('.');
				settings.easing = easing.length === 2 ? punchgs[easing[0]][easing[1]] : punchgs.hasOwnProperty(easing[0]) ? punchgs[easing[0]] : punchgs.Power3.easeInOut;
				
				$this.attr('data-anim', st).removeAttr('data-panzoom').data({
					
					liquideffectsettings: settings, 
					liquideffectorig: $.extend({}, settings)
					
				});
				
				if(settings.interactive) {
					easing = settings.intereasing.split('.');
					settings.intereasing = easing.length === 2 ? punchgs[easing[0]][easing[1]] : punchgs.hasOwnProperty(easing[0]) ? punchgs[easing[0]] : punchgs.Power3.easeInOut;
				}
				
			}
			else {
				
				$this.removeData('liquideffect').removeAttr('data-liquideffect');
				
			}
			
		});
		
		effects = slider.find('rs-slide[data-liquideffect]');
		if(effects.length) return new LiquidEffect(slider, effects);
		else return false;
		
	};
	
	function LiquidEffect(slider, effects) {
		
		this.slider = slider;
		this.effects = effects;
		this.firstrun = true;
		
		slider.one('revolution.slide.onloaded', this.onLoaded.bind(this))
			  .one('rsLiquidEffectDestroyed', this.destroy.bind(this));
		
	}

	var deliverLazy = function(e,def,id) { 	
	 	return _R.gA(e,"lazyload")!==undefined ? _R.gA(e,"lazyload") : // INTERNAL LAZY LOADING
	 		   _R[id].lazyloaddata!==undefined && _R[id].lazyloaddata.length>0 && _R.gA(e,_R[id].lazyloaddata)!==undefined ? _R.gA(e,_R[id].lazyloaddata) : // CUSTOM DATA
	 		   _R.gA(e,"lazy-src")!==undefined ? _R.gA(e,"lazy-src") :  //WP ROCKET
	 		   _R.gA(e,"lazy-wpfc-original-src")!==undefined ? _R.gA(e,"lazy-wpfc-original-src") : //WP Fastes Cache Premium
	 		   _R.gA(e,"lazy")!==undefined ? _R.gA(e,"lazy") : // LAZY
	 		   def; // DEFAULT
	 }
	
	LiquidEffect.prototype = {
		
		onLoaded: function() {
			
			this.slider.on('revolution.slide.onbeforeswap', this.beforeChange.bind(this))
					   .on('revolution.slide.onchange', this.onChange.bind(this));
			
		},
		
		onChange: function(e, data) {
			
			var slide,
				canvas;
				
			if(!this.ranOnce) {
				
				slide = data.slide;
				if(slide && !(slide instanceof $)) slide = $(slide);
				if(!slide || !slide.length) slide = this.slider.find('rs-slide').eq(0);
				
				this.beforeChange(false, {nextslide: slide});
				return;
			
			}
			
			slide = data.prevSlide;
			if(slide && !(slide instanceof $)) slide = $(slide);
			if(slide && slide.length) {
				
				canvas = slide.removeClass('liquid-force-visible').data('liquideffectcanvas');
				if(canvas) {
					
					canvas.reset();
					//canvas.ticker.stop();
					//canvas.tweenOut = null;
					
				}
				
			}
			
			slide = data.currentSlide;
			if(slide && !(slide instanceof $)) slide = $(slide);
			if(!slide || !slide.length) slide = this.slider.find('rs-slide').eq(0);
			
			canvas = slide.data('liquideffectcanvas');
			// causes ticker of first slide to run continuously
			// if(canvas && !canvas.started) canvas.animateIn();
			
		},
		
		beforeChange: function(e, data) {
			
			this.ranOnce = true;
			var slides = [];
			
			if(!this.effectsCreated) {
				
				this.effectsCreated = true;
				this.effects.each(function() {
				
					var $this = $(this),
						sizes = $this.data('liquideffectsettings').imagesize.split('|');
					
					if(sizes.length === 2) {	
						$this.data('liquideffectcanvasprep', [parseInt(sizes[0], 10), parseInt(sizes[1], 10)]);
					}
					else {
						slides[slides.length] = this;
					}
					
				});
				
			}
			
			function loadImage($this, slide) {
						
				var img = new Image(),
					bgImg = slide.find('rs-sbg');
				
				img.crossOrigin = 'Anonymous';
				
				var lazy = deliverLazy(bgImg[0], undefined,$this.slider[0].id),
					src = lazy !== undefined ? lazy : _R.gA(bgImg[0],"svg_src") !=undefined ? _R.gA(bgImg[0],"svg_src") : bgImg[0].src===undefined ? bgImg.data('src') : bgImg[0].src;
				
				if (src!==undefined) img.src = src;
				else img.src = bgImg.css('background-image').slice(4, -1).replace(/"/g, '');
								
				img.onload = function() {				
					slide.data('liquideffectcanvasprep', [parseInt(img.naturalWidth, 10), parseInt(img.naturalHeight, 10)]);
					$this.imgCount++;
					if($this.imgCount === slides.length) $this.run(data);					
				};
				
			}
			
			if(!slides.length) {
			 
				this.run(data);
				
			}
			else {
			 
				this.imgCount = 0;
				for(var i = 0; i < slides.length; i++) loadImage(this, $(slides[i]));
				
			}
			
		},
		
		run: function(data) {
			
			var canvas;
			if(!this.firstrun) {
			
				if(data.currentslide) {
					canvas = data.currentslide.data('liquideffectcanvas');
					if(canvas) canvas.animateOut(data.nextslide);
				}
			
				canvas = data.nextslide.data('liquideffectcanvas');
				if(!canvas) {
					canvas = data.nextslide.data('liquideffectcanvasprep');
					if(canvas) {	
						canvas = new LiquidCanvas(data.nextslide, canvas[0], canvas[1], this.slider);
						data.nextslide.removeData('liquideffectcanvasprep');
						data.nextslide.data('liquideffectcanvas', canvas);
					}
				}
				
				if(canvas) {
			  
					data.nextslide.addClass('liquid-force-visible');
					if(canvas.settings.transcross) canvas.animateIn();
					
				}
				
			}
			else {
				
				canvas = data.nextslide.data('liquideffectcanvas');
				if(!canvas) {
					canvas = data.nextslide.data('liquideffectcanvasprep');
					if(canvas) {	
						canvas = new LiquidCanvas(data.nextslide, canvas[0], canvas[1], this.slider);
						data.nextslide.removeData('liquideffectcanvasprep');
						data.nextslide.data('liquideffectcanvas', canvas);
					}
				}
				
				if(canvas) canvas.animateIn(true);
				this.firstrun = false;
				
			}
			
		},
		
		destroy: function() {
			
			if(this.slider) this.slider.off('revolution.slide.onloaded revolution.slide.onbeforeswap revolution.slide.onafterswap');
			if(this.effects) this.effects.each(function() {$(this).removeData('liquideffectcanvas liquideffectsettings liquideffectorig');});
			for(var prop in this) if(this.hasOwnProperty(prop)) delete this[prop];
			
		}
		
	};
	
	function LiquidCanvas(slide, w, h, slider) {
		this.slide = slide;
		this.slideWidth = slide.width();
		this.slideHeight = slide.height();
		this.slideRatio = this.slideWidth / this.slideHeight;
		this.imageRatio = w / h;
		this.w = w;
		this.h = h;

		this.mouse = {
			x: 0,
			y: 0			
		}
		this.frame = undefined;
		this.settings = slide.data('liquideffectsettings');  
		this.orig = slide.data('liquideffectorig');  
		
		this.displacement = new PIXI.Sprite.fromImage(this.settings.image);
		this.displacement.texture.baseTexture.wrapMode = PIXI.WRAP_MODES.REPEAT;  
		this.displacement.scale.x = 2;
		this.displacement.scale.y = 2;
		this.displacement.anchor.set(0.5);
		
		var sprite = this.settings.sprite,
			texture = new PIXI.Texture.fromImage(sprite),
			container = new PIXI.Container();
		
		this.img = new PIXI.Sprite(texture);
		this.img.anchor.set(0.5);
		container.addChild(this.img);
		
		this.filter = new PIXI.filters.DisplacementFilter(this.displacement);
		this.filter.autoFit = true;
		
		this.stage = new PIXI.Container();
		this.stage.addChild(container);
		this.stage.addChild(this.displacement);
		this.stage.filters = [this.filter];
		this.stage.interactive = true;
		
		this.renderer = new PIXI.autoDetectRenderer(this.slideWidth, this.slideHeight, {transparent: true});
		var style = this.renderer.view.style;
		style.top = '50%';
		style.left = '50%';
		style.msTransform = 'translate(-50%, -50%) scale(1.2)';  
		style.transform = 'translate( -50%, -50% ) scale(1.2)';  
		slide.find('rs-sbg-wrap').append(this.renderer.view);
		
		if(this.settings.autoplay) {
			this.filter.scale.x = this.settings.scalex;
			this.filter.scale.y = this.settings.scaley;
		}
		else {
			this.filter.scale.x = 0;
			this.filter.scale.y = 0;
		}
		
		if(this.settings.interactive) {
			
			container.interactive = true;
			if(this.settings.event === 'mousedown') {
				
				container.buttonMode = true;
				container.pointerdown = this.onClick.bind(this);
				container.pointerup = container.pointerout = this.onReturn.bind(this);
				
			}
			else {
				
				container.pointerover = this.onMouseEnter.bind(this);
				container.pointermove = this.onMouseMove.bind(this);
				container.pointerout = this.onMouseLeave.bind(this);
				
			}
			
		}
		else {
			
			this.renderer.view.style.pointerEvents = 'none';
			
		}
		
		this.supressEvents = true;
		this.started = false;
		this.ticker = new PIXI.ticker.Ticker();
		this.ticker.add(this.tick.bind(this));
		slider.on('revolution.slide.afterdraw', this.resize.bind(this));
		this.resize.bind(this)();
	}
	
	LiquidCanvas.prototype = {
		
		tick: function(delta) {
   
			if(this.settings.autoplay) {
				
				if(this.settings.speedx) this.displacement.x += this.settings.speedx * delta;
				if(this.settings.speedy) this.displacement.y += this.settings.speedy;
				if(this.settings.rotationx) this.displacement.rotation.x += this.settings.rotationx;
				if(this.settings.rotationy) this.displacement.rotation.y += this.settings.rotationy;
				if(this.settings.rotation) this.displacement.rotation += this.settings.rotation * Math.PI / 180;
				
			}

			this.renderer.render(this.stage);
			
		},
		
		onClick: function() {
			
			if(this.supressEvents) return;
			var time = this.settings.intertime * 0.001;
			
			if(this.settings.interscalex || this.settings.interscaley) {
				
				var obj = {ease: this.settings.intereasing,overwrite: 'all'};
				
				if(this.settings.interscalex) obj.x = this.orig.scalex + this.settings.interscalex;
				if(this.settings.interscaley) obj.y = this.orig.scaley + this.settings.interscaley; 
				
				punchgs.TweenLite.to(this.filter.scale, time, obj);
				
			}
			
			punchgs.TweenLite.to(this.settings, time, {
				
				speedx: this.orig.speedx + this.settings.interspeedx, 
				speedy: this.orig.speedy + this.settings.interspeedy,
				rotation: this.orig.rotation + this.settings.interotation,
				ease: this.settings.intereasing,
				overwrite: 'all',
				
			});
			
		},
		
		onReturn: function() {
   
			if(this.supressEvents) return;			
			var time = this.settings.intertime * 0.001;
	
			punchgs.TweenLite.to(this.filter.scale, time, {
				
				x: this.orig.scalex,
				y: this.orig.scaley,  
				ease: this.settings.intereasing,
				overwrite: 'all'
				
			});
			
			punchgs.TweenLite.to(this.settings, time, {
				
				speedx: this.orig.speedx, 
				speedy: this.orig.speedy,
				rotation: this.orig.rotation,
				ease: this.settings.intereasing,
				overwrite: 'all',
				
			});
			
		},
		
		onMouseMove: function(e) {
			
			if(this.supressEvents) return;
			if(!this.entered) {
				
				this.onMouseEnter(e);
				return;
				
			}
				
			this.mouse.x = Math.round(e.data.global.x);
			this.mouse.y = Math.round(e.data.global.y);
			if(!this.frame) this.frame = window.requestAnimationFrame(this.updateDisplacement.bind(this));
		},

		updateDisplacement: function () {
			this.frame = window.cancelAnimationFrame(this.frame);
			var complete,
				distX = this.mouse.x - this.x,
				distY = this.mouse.y - this.y,
				t = Date.now(),
				distT = t - this.t,
				v = Math.sqrt(distX * distX + distY * distY) / distT,
				time = this.settings.intertime * 0.001;
			this.x = this.mouse.x;
			this.y = this.mouse.y;
			this.t = t;

			if (this.settings.interscalex || this.settings.interscaley) {

				var obj = {
					ease: this.settings.intereasing,
					overwrite: 'all',
					onComplete: this.onReturn.bind(this)
				};

				if (this.settings.interscalex) obj.x = this.settings.interscalex * v;
				if (this.settings.interscaley) obj.y = this.settings.interscaley * v;

				complete = true;
				punchgs.TweenLite.to(this.filter.scale, time, obj);

			}

			var obj2 = {

				speedx: this.orig.speedx + this.settings.interspeedx,
				speedy: this.orig.speedy + this.settings.interspeedy,
				rotation: this.orig.rotation + this.settings.interotation,
				ease: this.settings.intereasing,
				overwrite: 'all',

			};

			if (!complete) obj2.onComplete = this.onReturn.bind(this);
			punchgs.TweenLite.to(this.settings, time, obj2);

		},

		onMouseEnter: function(e) {
			
			if(this.supressEvents) return;
			
			this.entered = true;
			this.x = e.data.global.x;
			this.y = e.data.global.y;
			this.t = Date.now();
			
		},
		
		onMouseLeave: function() {
			
			this.entered = false;
			
		},
		
		eventsReady: function() {
			
			this.supressEvents = false;
			
		},

		onComplete: function() {
			
			var canvas = this.nextslide.data('liquideffectcanvas');
			if(canvas && !canvas.started) canvas.animateIn();
			this.nextslide = false;
		},
		
		onUpdateIn: function() {
			
			if(this.tweenIn) {
			
				this.displacement.rotation += this.tweenIn.progress() * 0.02;      
				this.displacement.scale.set(this.tweenIn.progress() * 3);
				
			}
			
		},
		
		onUpdateOut: function() {
			
			if(this.tweenOut) {
				
				this.displacement.rotation += this.tweenOut.progress() * 0.02;      
				this.displacement.scale.set(this.tweenOut.progress() * 3);
				
			}
			
		},
		
		transitionIn: function() {
			
			var transTime = this.settings.transtime * 0.001;
			var obj1 = {
				
				x: this.orig.scalex, 
				y: this.orig.scaley,  
				ease: this.settings.easing,
				overwrite: 'all',
				delay: this.del
				
			};
			
			var obj2 = {
				
				speedx: this.orig.speedx, 
				speedy: this.orig.speedy,
				rotationx: this.orig.rotationx, 
				rotationy: this.orig.rotationy, 
				rotation: this.orig.rotation, 
				ease: this.settings.easing,
				overwrite: 'all',
				delay: this.del
			
			};

			if(this.interactive && this.event === 'mousedown') obj1.onComplete = this.eventsReady.bind(this);
			else this.supressEvents = false;
			
			if(this.settings.transpower) obj2.onUpdate = this.onUpdateIn.bind(this);
			punchgs.TweenLite.to(this.filter.scale, transTime, obj1);
			
			this.tweenIn = punchgs.TweenLite.to(this.settings, transTime, obj2);
			punchgs.TweenLite.to(this.renderer.view, transTime * 0.5, {opacity: 1, ease: this.settings.easing, overwrite: 'all', delay: this.del});
			
			this.ticker.start(); 
   
		},
		
		animateIn: function(first) {
			
			this.reset();
			this.started = true;
			this.del = this.settings.transcross || first ? (this.settings.transtime * 0.001) * 0.5 : 0;
			this.timer = setTimeout(this.transitionIn.bind(this), this.del);
			
		},
		
		animateOut: function(nextslide) {
   
			clearTimeout(this.timer);
   
			this.tweenIn = null;
			this.supressEvents = true;
			this.started = false;
			
			var transTime = this.settings.transtime * 0.001;
			var obj = {
				
				speedx: this.orig.speedx + this.settings.transpeedx, 
				speedy: this.orig.speedy + this.settings.transpeedy,
				rotationx: this.orig.rotationx + this.settings.transrotx, 
				rotationy: this.orig.rotationy + this.settings.transroty, 
				rotation: this.orig.rotation + this.settings.transrot, 
				ease: this.settings.easing,
				overwrite: 'all',
				
			};
			
			if(this.settings.transcross && nextslide) {
				
				this.nextslide = nextslide;
				obj.onComplete = this.onComplete.bind(this);
				
			}
			
			if(this.settings.transpower) obj.onUpdate = this.onUpdateOut.bind(this);
			punchgs.TweenLite.to(this.filter.scale, transTime, {
				
				x: this.orig.scalex + this.settings.transitionx, 
				y: this.orig.scaley + this.settings.transitiony, 
				ease: this.settings.easing,
				overwrite: 'all'
				
			});	
			
			this.tweenOut = punchgs.TweenLite.to(this.settings, transTime, obj);
			punchgs.TweenLite.to(this.renderer.view, transTime, {opacity: 0, ease: this.settings.easing, delay: transTime * 0.5});
   
		},
		resize: function(){			
			this.slideWidth = this.slide.width();
			this.slideHeight = this.slide.height();
			this.slideRatio = this.slideWidth / this.slideHeight;			

			this.displacement.x = this.slideWidth / 2;
			this.displacement.y = this.slideHeight / 2; 

			this.renderer.resize(this.slideWidth, this.slideHeight);
			
			this.img.width = this.w;
			this.img.height = this.h;

			if(this.slideRatio > 1 && this.slideRatio > this.imageRatio) {
				this.img.width = this.slideWidth;
				this.img.height = this.slideWidth / this.imageRatio;
			} else {
				this.img.width = this.slideHeight * this.imageRatio;
				this.img.height = this.slideHeight;
			}

			this.img.x = this.slideWidth / 2;
			this.img.y = this.slideHeight / 2;
  
		},
		reset: function(kill) {
			
			this.tweenIn = null;
			this.tweenOut = null;
			this.ticker.stop();
			clearTimeout(this.timer);
			
			punchgs.TweenLite.killTweensOf(this.filter.scale);
			punchgs.TweenLite.killTweensOf(this.settings);
			punchgs.TweenLite.killTweensOf(this.renderer.view);
			
			if(kill) return;
			if(this.settings.power) {
				this.displacement.rotation = 0;
				this.displacement.scale.set(1);
			}
			
			this.displacement.x = this.slideWidth / 2;			
			this.displacement.y = this.slideHeight / 2;
			this.displacement.rotation.x = 0;
			this.displacement.rotation.y = 0;
			this.displacement.rotation = 0;
			this.settings.speedx = this.orig.speedx + this.settings.transpeedx;
			this.settings.speedy = this.orig.speedy + this.settings.transpeedy;
			this.settings.rotationx = this.orig.rotationx + this.settings.transrotx;
			this.settings.rotationy = this.orig.rotationy + this.settings.transroty;
			this.filter.scale.x = this.orig.scalex + this.settings.transitionx;
			this.filter.scale.y = this.orig.scaley + this.settings.transitiony;
			this.renderer.view.style.opacity = 0;
			
		},
		
		destroy: function() {
   
			if(this.ticker) {
				
				this.reset(true);
				this.container.pointerdown = null;
				this.container.pointerup = null;
				this.container.pointerover = null;
				this.container.pointerout = null;
				this.container.touchstart = null;
				this.container.touchend = null;
				
			}
			
			if(this.renderer) this.slide.remove(this.renderer.view);
			for(var prop in this) if(this.hasOwnProperty(prop)) delete this[prop];
			
		}
		
	};
	
})();