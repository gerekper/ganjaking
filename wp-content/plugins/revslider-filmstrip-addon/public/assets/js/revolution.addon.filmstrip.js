/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2019 ThemePunch
*/

;(function() {
	
	var $,
		win,
		base;
	
	window.RsFilmstripAddOn = function(_$, slider, _base, carousel) {
		
		if(!_$ || !slider) return;
		
		$ = _$;
		win = $(window);
		base = _base;
		if(base.substr(base.length - 1) === '/') base = base.slice(0, -1);
		
		// add hook to listen if the element is removed from the DOM
		$.event.special.rsStripDestroyed = {remove: function(evt) {evt.handler();}};
		
		new RsAddonStripSlider(slider, carousel);
		
	};
	
	function RsAddonStripSlider(slider, carousel) {
		
		var opt = $.fn.revolution && $.fn.revolution[slider[0].id] ? $.fn.revolution[slider[0].id] : false;
		if(!opt) return;
		
		var strips = [],
			gridWidth = opt.gridwidth,
			timeLevels = opt.visibilityLevels;
			
		if(Array.isArray(this.gridWidth)) gridWidth = gridWidth[0];
		slider.find('rs-slide[data-filmstrip]').each(function(i) {
				
			var $this = $(this).attr('data-anim', 'ei:d;eo:d;s:300ms;r:0;t:fade;sl:0;');
			if(i === 0) $this.attr('data-firstanim', 'ei:d;eo:d;s:300ms;r:0;t:fade;sl:0;');
			
			strips[strips.length] = new RsAddonStripSlide(
				
				slider[0],
				carousel,
				gridWidth,
				timeLevels,
				$this,
				JSON.parse($this.attr('data-filmstrip'))
				
			);
			
		});
		
		this.slider = slider;
		this.strips = strips;
		
		slider.one('revolution.slide.onloaded', this.onLoaded.bind(this))
			  .on('revolution.slide.onbeforeswap', this.beforeSwap.bind(this))
			  .on('revolution.slide.onafterswap', this.afterSwap.bind(this));
		
	}
	
	RsAddonStripSlider.prototype = {
		
		onLoaded: function() {
			
			if(this.checkRemoved()) return;
			
			var len = this.strips.length;
			for(var i = 0; i < len; i++) {
				
				// create new filmstrip
				this.strips[i].slide.find('rs-sbg-wrap').append(this.strips[i].strip);
				
			}
			
		},
		
		beforeSwap: function(e, data) {
			
			if(this.checkRemoved()) return;		

			jQuery("#"+data.slider).find('.rs-addon-strip-active').removeClass('rs-addon-strip-active');
			if(data.nextslide.hasClass('rs-addon-strip')) {				
				data.nextslide.addClass('rs-addon-strip-active');				
				$.data(				
					data.nextslide[0], 
					'rs-addon-strip-' + data.nextslide[0].getAttribute('data-key')					
				).start();
				
			}
			
		},
		
		afterSwap: function(e, data) {
			
			if(this.checkRemoved()) return;
			
			/*
				data.currentSlide and data.prevSlide are not correct anymore
			*/

			
			jQuery('#'+data.slider).find('.rs-addon-strip').not('.rs-addon-strip-active').each(function() {				
				$.data(				
					this, 
					'rs-addon-strip-' + this.getAttribute('data-key')					
				).onStop();
				
			});
			
		},
		
		checkRemoved: function() {
		
			// bounce if the slider has been removed from the DOM before the onloaded event fires
			if(!this.slider || !document.body.contains(this.slider[0])) {
				
				this.destroy();
				return true;
			
			}
			
			return false;
			
		},
		
		destroy: function() {			
			win.off('resize.rsaddonstrip');			
			if(this.strips) {
			
				while(this.strips.length) {
					
					this.strips[0].destroy();
					this.strips.shift();
					
				}
				
			}
			
			for(var prop in this) if(this.hasOwnProperty(prop)) delete this[prop];
			
		}
		
	};
	
	function onReset($this) {
		
		var obj = {};
		obj[$this.direction] = $this.resetPosition;
		
		punchgs.TweenLite.set($this.strip, obj);
		onTween.call($this);
		
	}
	
	function onTween() {
		
		var obj = {ease: "none", onComplete: onReset, onCompleteParams: [this]};
		obj[this.direction] = this.moveTo;
				
		if(!this.carousel) {
		
			punchgs.TweenLite.to(this.strip, this.time, obj);
		
		}
		// pause.resume tweens for carousel slides on slide change
		else {			
			if(this.tween) {
				
				this.tween.resume();
				
			}
			else {
				
				this.tween = punchgs.TweenLite.to(this.strip, this.time, obj);
				
			}
			
		}
		
	}
	
	function newImage(data) {
		
		var img = document.createElement('img');
		img.className = 'rs-addon-strip-img';
		img.setAttribute('data-lazyload', data.url);
		
		if(data.alt) img.setAttribute('alt', data.alt);
		img.src = base + '/public/assets/images/transparent.png';
		
		return img;
		
	}
	
	function RsAddonStripSlide(slider, carousel, gridWidth, levels, slide, data) {
		
		var j, 
			imgs = data.imgs,
			len = imgs.length,
			frag = document.createDocumentFragment(),
			reverse = data.direction === 'left-to-right' || data.direction === 'top-to-bottom';

		for(var i = 0; i < 2; i++) {
			
			if(!reverse) {
			
				for(j = 0; j < len; j++) frag.appendChild(newImage(imgs[j]));
				
			}
			else {
				
				j = len;
				while(j--) frag.appendChild(newImage(imgs[j]));
				
			}
			
		}
		
		var strip = document.createElement('div'),
			filter = !data.filter ? '' : ' ' + data.filter,
			direction = data.direction.search(/left|right/) !== -1 ? 'horizontal' : 'vertical';
		
		
		strip.className = 'rs-addon-strip-wrap rs-addon-strip-' + direction + filter;
		strip.appendChild(frag);
		
		slide[0].appendChild(strip);
		slide[0].className = slide[0].className + ' ' + 'rs-addon-strip';
		
		this.strip = strip;
		this.slide = slide;
		this.slider = slider;
		this.levels = levels;
		this.reverse = reverse;
		this.carousel = carousel;
		this.gridWidth = gridWidth;
		this.times = data.times.split(',');
		this.resizer = this.sizer.bind(this);
		this.direction = direction === 'horizontal' ? 'x' : 'y';
		
		
		var times = data.times.split(','),
			len = times.length;
			speeds = [];
			
		for(i = 0; i < 4; i++) {
			
			var time = i < len ? parseInt(times[i]) : 10;
			if(!time) time = 10;
			else if(time < 2) time = 2;
			speeds[i] = time;
			
		}
			
		this.times = speeds;		
		$.data(slide[0], 'rs-addon-strip-' + slide[0].getAttribute('data-key'), this);
		
	}
	
	RsAddonStripSlide.prototype = {
		
		start: function() {
			
			clearTimeout(this.timer);
			if(!this.resizeAdded) this.addResize();
			
			if(!this.carousel || (this.carousel && !this.tween)) {
			
				var obj = {};
				obj[this.direction] = this.resetPosition;
				punchgs.TweenLite.set(this.strip, obj);
				
			}
			
			this.running = true;
			this.timer = setTimeout(this.onStart.bind(this), 100);
			
		},
		
		onStart: function() {
			
			if(!this.carousel) this.strip.style.opacity = '1';
			onTween.call(this);
			
		},
		
		stop: function() {
			
			clearTimeout(this.timer);
			
			if(!this.carousel) {
			
				punchgs.TweenLite.killTweensOf(this.strip);
				
			}
			else {
				
				if(this.tween) {
					
					this.tween.pause();
					
				}
				else {
					
					punchgs.TweenLite.killTweensOf(this.strip);
					
				}
				
			}
			
		},
		
		onStop: function() {
			
			this.running = false;
			if(!this.carousel) this.strip.style.opacity = '0';
			this.stop();
			
		},
		
		addResize: function() {
			
			win.on('resize.rsaddonstrip', this.onResize.bind(this));
			if(this.direction === 'x') this.strip.style.height = this.slider.clientHeight + 'px';
			
			this.resizeAdded = true;
			this.sizer(true);
			
		},
		
		onResize: function() {
			
			clearTimeout(this.resize);
			if(this.carousel) delete this.tween;
			this.stop();
			
			if(this.direction === 'x') this.strip.style.height = this.slider.clientHeight + 'px';
			this.resize = setTimeout(this.resizer, 100);
			
		},
		
		sizer: function() {
			
			var wid = this.slider.clientWidth,
				set;
			
			for(var i = 0; i < 4; i++) {
				
				if(wid >= this.levels[i]) {
					
					this.time = this.times[i];
					set = true;
					break;
					
				}
				
			}
			
			if(!set) this.time = this.times[3];
			if(this.direction === 'x') {
				
				var stripWidth = this.strip.clientWidth;
				this.strip.style.height = this.slider.clientHeight + 'px';
				
				if(!this.reverse) {
					
					this.moveTo = -(stripWidth / 2);
					this.resetPosition = 0;
					
				}
				else {
					
					this.moveTo = -((stripWidth / 2) - wid);
					this.resetPosition = -(stripWidth - wid);
					
				}
				
			}
			else {
				
				var stripHeight = this.strip.clientHeight;
				if(!this.reverse) {
					
					this.moveTo = -(stripHeight / 2);
					this.resetPosition = 0;
					
				}
				else {
					
					var high = this.slider.clientHeight;
					this.moveTo = -((stripHeight / 2) - high);
					this.resetPosition = -(stripHeight - high);
					
				}
				
			}
			
			if(this.running) {
				
				this.start();
				
			}
			else if(this.carousel) {
				
				var obj = {};
				obj[this.direction] = this.resetPosition;
				punchgs.TweenLite.set(this.strip, obj);
				
			}
			
		},
		
		destroy: function() {
			
			clearTimeout(this.timer);
			clearTimeout(this.resize);
			
			punchgs.TweenLite.killTweensOf(this.strip);
			$.removeData(this.slide[0], 'rs-addon-strip-' + this.slide[0].getAttribute('data-key'));
			
			for(var prop in this) if(this.hasOwnProperty(prop)) delete this[prop];
			
		}
		
	};
	
})();