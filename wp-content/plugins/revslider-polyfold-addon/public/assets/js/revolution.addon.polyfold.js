/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */

;(function() {
	
	'use strict';
	
	var $,
		win,
		baseClass     = 'rs-addon-polyfold',
	    topClass      = 'rs-addon-poly-top',
		bottomClass   = 'rs-addon-poly-bottom',
	    centerClass   = 'rs-addon-poly-center',
		navLevel      = 'rs-addon-poly-nav-level',
		staticLevel   = 'rs-addon-poly-static-level';
		
	window.RsPolyfoldAddOn = function(_$, slider, options) {
		
		if(!_$ || !slider) return;
		
		$ = _$;
		win = $(window);
		
		// add hook to listen if the element is removed from the DOM
		$.event.special.polyfoldDestroyed = {remove: function(evt) {evt.handler();}};
		
		new RsPolyfold(slider, options);
		
	};
	
	function RsPolyfold(slider, options) {
		
		this.calc = false;
		this.slider = slider;
		this.scrolled = false;
		this.ids = slider[0].id;
		this.time = options.time;
		this.ease = options.ease;
		this.color = options.color;
		this.point = options.point;
		this.height = options.height;
		this.onScroll = options.scroll;
		this.inverted = options.inverted;
		this.animated = options.animated;
		this.negative = options.negative;
		this.placement = options.placement;
		this.leftWidth = options.leftWidth;
		this.rightWidth = options.rightWidth;
		this.responsive = options.responsive;
		this.range = options.range === 'slider';
		this.isTop = options.position === 'top';
		this.starter = this.calculate.bind(this);

		slider.one('revolution.slide.onbeforeswap', this.init.bind(this));
		
	}
	
	RsPolyfold.prototype = {
		
		init: function() {
			
			// bounce if the slider has been removed from the DOM before the onloaded event fires
			if(!document.body.contains(this.slider[0])) {
				
				this.destroy();
				return;
			
			}
			
			var opt = $.fn.revolution && $.fn.revolution[this.ids] ? $.fn.revolution[this.ids] : false;
			if(!opt) return;
			
			var animeClass,
			    cls = baseClass,
		        container = document.createElement('div'),
		        frag = document.createDocumentFragment();
			
			this.left = document.createElement('div');
			this.right = document.createElement('div');
			
			// poly resizing always based on the first level
			this.gridWidth = opt.gridwidth;
			if(Array.isArray(this.gridWidth)) this.gridWidth = this.gridWidth[0];
			
			if(!this.isTop) {
				
				animeClass = bottomClass;
				cls += ' ' + bottomClass;
				
			}
			else {
				
				animeClass = topClass;
				cls += ' ' + topClass;
				
			}
			
			// sets the z-index
			if(this.placement > 1) {
				
				cls += ' ';
				
				if(this.placement === 2) {
					
					cls += navLevel;
					
					// set arrow z-index to same as bullets/tabs/thumbs
					this.slider.find('.tparrows').css('z-index', 1000);
					
				}
				else {
					
					cls += staticLevel;
					
				}
				
			}
			
			// CSS3 transition
			if(this.animated) {
				
				var style = document.createElement('style');
				style.type = 'text/css';
				style.innerHTML = '#' + this.ids + '_wrapper .' + animeClass + ' div ' + 
				                  '{transition: border-width ' + this.time + 's ' + this.ease + ';}';
								  
				document.getElementsByTagName('head')[0].appendChild(style);
				
			}
			
			// draw from center or sides
			if(this.point === 'center') cls += ' ' + centerClass;
			
			frag.appendChild(this.left);
			frag.appendChild(this.right);
			
			container.className = cls;
			container.appendChild(frag);
			
			// garbage collection from custom event set above
			this.slider.one('polyfoldDestroyed', this.destroy.bind(this));
			
			// insert poly div after ".rev_slider"
			this.slider[0].parentNode.insertBefore(container, this.slider.nextSibling);
			
			// resize and scroll events
			win.on('resize.rspolyaddon' + this.ids, this.resize.bind(this));
			if(this.onScroll) win.on('scroll.rspolyaddon' + this.ids, this.draw.bind(this));
			
			// set the border colors
			this.colors();
			
			// kick it off
			this.resize(false, true);	
			
		},
		
		colors: function() {
			
			var leftColor,
				rightColor;
			
			if(!this.negative) {
					
				if(!this.isTop) {
					
					leftColor = 'transparent transparent transparent ' + this.color;
					rightColor = 'transparent transparent ' + this.color + ' transparent';
					 
				}
				else {
					
					leftColor = this.color + ' transparent transparent transparent';
					rightColor = 'transparent ' + this.color + ' transparent transparent';
					
				}
				
			}
			else {
				
				if(!this.isTop) {
					
					leftColor = 'transparent transparent ' + this.color + ' transparent';
					rightColor = 'transparent transparent transparent ' + this.color;
					 
				}
				else {
					
					leftColor = "transparent " + this.color + " transparent transparent";
					rightColor = this.color + " transparent transparent transparent";
					
				}
				
			}
			
			this.left.style.borderColor = leftColor;
			this.right.style.borderColor = rightColor;
			
		},
		
		draw: function(event) {
			
			var offset,
				leftWidth,
				rightWidth;
			
			if(this.onScroll) {
				
				if(event) this.scrolled = true;
				var slideRect = this.slider[0].getBoundingClientRect();
				
				// if slider is in viewport
				if(slideRect.top + window.pageYOffset < this.winHeight + window.pageYOffset && slideRect.bottom > 0) {
					
					if(!this.isTop) {
						
						if(!this.inverted) {
							
							offset = this.calc !== false ? slideRect.bottom - this.calc : this.left.getBoundingClientRect().top;
							this.calc = this.polyHeight * ((this.drawHeight - offset) / this.drawHeight);
							
						}
						else {
							
							offset = this.calc !== false ? slideRect.bottom : this.left.getBoundingClientRect().top;
							this.calc = this.polyHeight - (this.polyHeight * (((this.drawHeight - offset) / this.drawHeight)));
							
						}
						
					}
					else {
						
						var dif = this.range ? this.winHeight - this.sliderHeight : 0;
						if(!this.inverted) {

							offset = this.calc !== false ? slideRect.top + this.calc : this.left.getBoundingClientRect().top;
							this.calc = this.polyHeight * ((offset - dif) / this.drawHeight);
							
						}
						else {
							
							offset = this.calc !== false ? slideRect.top : this.left.getBoundingClientRect().top;
							this.calc = this.polyHeight - (this.polyHeight * ((offset - dif) / this.drawHeight));
							
						}
						
					}
					
					this.calc = Math.floor(Math.min(Math.max(this.calc, 0), this.polyHeight));
					
				}
				else {
					
					return 1;
					
				}
				
			}
			// simple fixed draw for no scroll setting
			else {
				
				this.calc = this.polyHeight;
				
			}
			
			if(!this.negative) {
				
				if(!this.isTop) {
					
					leftWidth = this.calc + 'px 0 0 ' + Math.floor(this.drawWidth * this.leftWidth) + 'px';
					rightWidth = '0 0 ' + this.calc + 'px ' + Math.floor(this.drawWidth * this.rightWidth) + 'px';
					 
				}
				else {
					
					leftWidth = this.calc + 'px ' + Math.floor(this.drawWidth * this.leftWidth) + 'px 0 0';
					rightWidth = '0 ' + Math.floor(this.drawWidth * this.rightWidth) + 'px ' + this.calc + 'px 0';
					
				}
				
			}
			else {
				
				if(!this.isTop) {
					
					leftWidth = '0 0 ' + this.calc + 'px ' + Math.floor(this.drawWidth * this.leftWidth) + 'px';
					rightWidth = this.calc + 'px 0 0 ' + Math.floor(this.drawWidth * this.rightWidth) + 'px';
					 
				}
				else {
					
					leftWidth = "0 " + Math.floor(this.drawWidth * this.leftWidth) + "px " + this.calc + 'px 0';
					rightWidth = this.calc + 'px ' + Math.floor(this.drawWidth * this.rightWidth) + 'px 0 0';
					
				}
				
			}
			
			this.left.style.borderWidth = leftWidth;
			this.right.style.borderWidth = rightWidth;
			
			if(this.onScroll) return offset;
			
		},
		
		resize: function(event, init) {
			
			this.winHeight = window.innerHeight;
			this.drawWidth = this.slider[0].clientWidth;
			this.sliderHeight = this.slider[0].clientHeight;
			this.drawHeight = this.range ? this.sliderHeight : this.winHeight;
			this.polyHeight = this.responsive ? Math.round(this.height * (this.drawWidth / this.gridWidth)) : this.height;

			// always make slider.height the max-height
			this.polyHeight = Math.min(this.polyHeight, this.sliderHeight);
			
			if(!init || !this.onScroll) {
				
				this.draw();
				
			}
			// need to draw a few times on document.ready for accuracy
			else if(this.onScroll) {

				this.oldOffset = 0;
				this.newOffset = 1;
				this.calculate();
				
			}
			
		},
		
		calculate: function() {
			
			// need to draw a few times on document.ready for accuracy
			if(!this.scrolled && this.newOffset !== this.oldOffset) {
				
				this.oldOffset = this.newOffset;
				this.newOffset = this.draw();
				window.requestAnimationFrame(this.starter);
				
			}
			else {
				
				delete this.starter;
				
			}
			
		},
		
		destroy: function() {
			
			win.off('scroll.rspolyaddon' + this.ids + ' resize.rspolyaddon' + this.ids);
			for(var prop in this) if(this.hasOwnProperty(prop)) delete this[prop];
			
		}
		
	};
	
})();