/*
 2019 ThemePunch
 http://www.themepunch.com/
 @version   2.0.0
*/
;(function() { 
	
	window.RsParticlesAddOn = function(slider) {
		
		// bounce if showDoubleJqueryError
		if(!slider) return;
		
		var opt = jQuery.fn.revolution && jQuery.fn.revolution[slider[0].id] ? jQuery.fn.revolution[slider[0].id] : false;
		if(!opt) return;

		var slides = slider.find('rs-slide'),
			carousel = opt.sliderType === 'carousel',
			currentSlideId = 0;
			
		slides.each(function() {
				
			var $this = jQuery(this),
				data = $this.attr('data-rsparticles');
				
			if(data) {
				
				data = JSON.parse(data);
				data = jQuery.extend(true, getDefaults(), data);
				data = convertOptions(data);
				data.carousel = carousel;
				$this.data('particles', data);
				
			}
			
		});
		
		var id = slider[0].id;
		slides.each(function() {
		
			var $this = jQuery(this),
				options = $this.data('particles');
				
			if(!options) return;
			
			var particl = options.particles,
				lines = particl.line_linked,
				shape = particl.shape,
				color = particl.color,
				stroke = shape.stroke,
				bWidth = stroke.width,
				svg,
				src,
				i;
				
			var inter = options.interactivity,
				hover = inter.events.onhover,
				click = inter.events.onclick;
				
			if(hover.mode !== 'none' && click.mode === 'bubble') click.mode = 'none';	
			if(hover.enable && hover.mode === 'bubble' || click.enable && click.mode === 'bubble') {
			
				var bubbleSize = inter.modes.bubble.size;
				if(bubbleSize > particl.size.value) particl.size.drawSize = Math.ceil(bubbleSize * Math.PI);
			
			}
			else {
				
				particl.size.drawSize = particl.size.value * 2;
				
			}
				
			color.value = color.value.split(',');
			if(!bWidth) stroke.color = stroke.color.split(',');
			else stroke.color = toRGBA(stroke.color.split(','), stroke.opacity);
			
			if(lines.enable || (hover.enable && hover.mode === 'grab')) {
				
				var ar = lines.color = lines.color.split(',');
				i = ar.length;
					
				while(i--) ar[i] = hexToRgb(ar[i]);
				
			}
			
			if(shape.type === 'image') {
				
				shape.image.src = shape.image.src.split(',');
				
				var len = shape.image.src.length,
					tagStart,
					tagEnd,
					view;
				
				for(i = 0; i < len; i++) {
				
					svg = shape.image.src[i];
					if(svg !== 'circle') {
				
						tagStart = '<path ';
						tagEnd = ' d="' + svg + '"></path>';
						view = svg.search('::') === -1 ? 24 : svg.split('::')[1];
						
					}
					else {
						
						tagStart = '<circle cx="12" cy="12" r="12" ';
						tagEnd = ' />';
						view = 24;
						
					}
					
					src = '<svg xmlns="http://www.w3.org/2000/svg" width="' + view + '" height="' + view + '" viewBox="{{viewbox}}">' + 
							  tagStart + 'fill="#ffffff" stroke="{{stroke-color}}" stroke-width="{{stroke-width}}"' + tagEnd + '</svg>';
					
					if(!bWidth) {
						
						svg = src.replace('{{viewbox}}', '0 0 ' + view + ' ' + view).replace('{{stroke-width}}', 0);
						
					}
					else {
						
						var size = (bWidth * 2) + parseInt(view, 10);
						svg = src.replace('{{stroke-width}}', bWidth)
									 .replace('{{viewbox}}', -bWidth +  ' ' + -bWidth + ' ' + size + ' ' + size);
						
					}
					
					shape.image.src[i] = svg;
					
				}
			}
			
			$this.data('particles', options);
			
		});
		
		slider.one('revolution.slide.onchange', function(e, data) {
			
			var slide = data.currentSlide;
			if(slide && !(slide instanceof jQuery)) slide = jQuery(slide);
			if(!slide || !slide.length) slide = slider.find('rs-slide').eq(0);
			
			var ids = id + '-tp-particles-',
				linkFound;
			
			// determine where the canvas should be placed
			slider.find('rs-slide').each(function(i) {
				
				var $this = jQuery(this),
					options = $this.data('particles');
					
				if(!options) return;	
				var zIndex = options.zIndex,
					events = options.interactivity.events;
					
				events = events.onhover.enable || events.onclick.enable;
					
				if(zIndex === 'default') zIndex = 0;
				if(events) {
					
					var slideLink = $this.find('.slidelink');
					
					// particles are interactive, with no slidelink present
					if(!slideLink.length) {
						
						this.className = this.className + ' rs-particles-interactive';
						
					}
					// particles are interactive, and slide also has slidelink
					else {
						
						linkFound = true;
						if($this.data('seoz') !== 'back') zIndex = 999;
						else slideLink.closest('rs-layer-wrap').css('z-index', 1);
						this.className = this.className + ' rs-particles-slidelink';
						
					}
					
				}
				
				this.setAttribute('data-particlesid', ids + (i + 1));
				this.setAttribute('data-particlesindex', zIndex);
				
			});
			
			// for compatibility between particle interactivity and .slidelink
			if(linkFound) {
				
				jQuery('body').off('click.rsparticles')
							   .on('click.rsparticles', '.rs-particles-canvas', function() {
					
					var a = jQuery(this).prev('rs-parallax-wrap').find('rs-slide[data-link] a', 'rs-slide[data-linktoslide] a');
					if(!a.length) return;
					
					// navigate to url
					if(a[0].href) {
						
						if(a[0].target !== '_blank') window.location = a[0].href;
						else window.open(a[0].href);
						
					}
					else {
							
						// navigate to slide
						a.click();
						
					}
					
				});
				
			}

			slider.on('revolution.slide.onbeforeswap', onBeforeSwap);
			if(!carousel) slider.on('revolution.slide.onafterswap', onAfterSwap);
			else slider.on('revolution.slide.carouselchange', carouselChange);	
			
		});
		
		function carouselChange(e, data) {
			
			if(slider.revcurrentslide() !== currentSlideId) {
				
				onAfterSwap(false, data);
				
			}
			else {
					
				var slide = data.currentslide,
					pjs = slide.data('pjs');
			
				if(pjs && pjs.instance.pJS.resizeFunction && pjs.instance.pJS.sliderResized) {
					
					pjs.instance.pJS.resizeFunction();
					pjs.instance.pJS.sliderResized = false;
					
				}	
					
			}
			
		}
		
		function onBeforeSwap(e, data) {

			slider.off('.rsparticles');
			
			var slide = data.currentslide.off('.rsparticles'),	
				pjs = slide.data('pjs');
			
			if(pjs) {
				
				if(pjs.instance.pJS.resizeFunction) {
					pjs.instance.pJS.sliderResized = false;
					slider.off('revolution.slide.afterdraw', pjs.instance.pJS.resizeFunction);
				}
				
				pjs.el.off('.rsparticles');
				punchgs.TweenLite.to(pjs.el, 0.3, {opacity: 0, ease: punchgs.Linear.easeNone, onComplete: function() {
				
					pjs.instance.pJS.fn.vendors.destroypJS();
					slide.removeData('pjs').find('.rs-particles-canvas').remove();
				
				}});
				
			}
			
		}
		
		function onAfterSwap(e, data) {
			
			var slide = data.currentslide;
			if(slide && !(slide instanceof jQuery)) slide = jQuery(slide);
			if(!slide || !slide.length) slide = slider.find('rs-slide').eq(0);
			
			data = slide.data('particles');
			if(data) {
				
				data = jQuery.extend(true, {}, data);
				
				var zIndex = slide.attr('data-particlesindex'),
					pjs = particlesJSRs(slide, data, slide.attr('data-particlesid'), slider, zIndex);
					
				slide.data('pjs', pjs);
				punchgs.TweenLite.to(pjs.el, 0.5, {opacity: 1, ease: punchgs.Linear.easeNone});
				
			}
			
			currentSlideId = slider.revcurrentslide();
			
		}
		
	};
	
	var svgs = {
		
		edge: 'M4 4h16v16H4z', 
		triangle: 'M12 6L4 20L20 20z', 
		polygon: 'M5 4 L17 4 L22 12 L17 20 L8 20 L3 12 L8 4 Z', 
		star: 'M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z',
		heart_1: 'M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z',
		star_2: 'M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm4.24 16L12 15.45 7.77 18l1.12-4.81-3.73-3.23 4.92-.42L12 5l1.92 4.53 4.92.42-3.73 3.23L16.23 18z',
		settings: 'M19.43 12.98c.04-.32.07-.64.07-.98s-.03-.66-.07-.98l2.11-1.65c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.3-.61-.22l-2.49 1c-.52-.4-1.08-.73-1.69-.98l-.38-2.65C14.46 2.18 14.25 2 14 2h-4c-.25 0-.46.18-.49.42l-.38 2.65c-.61.25-1.17.59-1.69.98l-2.49-1c-.23-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49.12.64l2.11 1.65c-.04.32-.07.65-.07.98s.03.66.07.98l-2.11 1.65c-.19.15-.24.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1c.52.4 1.08.73 1.69.98l.38 2.65c.03.24.24.42.49.42h4c.25 0 .46-.18.49-.42l.38-2.65c.61-.25 1.17-.59 1.69-.98l2.49 1c.23.09.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.65zM12 15.5c-1.93 0-3.5-1.57-3.5-3.5s1.57-3.5 3.5-3.5 3.5 1.57 3.5 3.5-1.57 3.5-3.5 3.5z',
		arrow_1: 'M4 18l8.5-6L4 6v12zm9-12v12l8.5-6L13 6z',
		bullseye: 'M12 2C6.49 2 2 6.49 2 12s4.49 10 10 10 10-4.49 10-10S17.51 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3-8c0 1.66-1.34 3-3 3s-3-1.34-3-3 1.34-3 3-3 3 1.34 3 3z',
		plus_1: 'M13 7h-2v4H7v2h4v4h2v-4h4v-2h-4V7zm-1-5C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z',
		triangle_2: 'M12 7.77L18.39 18H5.61L12 7.77M12 4L2 20h20L12 4z',
		smilie: 'M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z',
		star_3: 'M22 9.24l-7.19-.62L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21 12 17.27 18.18 21l-1.63-7.03L22 9.24zM12 15.4l-3.76 2.27 1-4.28-3.32-2.88 4.38-.38L12 6.1l1.71 4.04 4.38.38-3.32 2.88 1 4.28L12 15.4z',
		heart_2: 'M16.5 3c-1.74 0-3.41.81-4.5 2.09C10.91 3.81 9.24 3 7.5 3 4.42 3 2 5.42 2 8.5c0 3.78 3.4 6.86 8.55 11.54L12 21.35l1.45-1.32C18.6 15.36 22 12.28 22 8.5 22 5.42 19.58 3 16.5 3zm-4.4 15.55l-.1.1-.1-.1C7.14 14.24 4 11.39 4 8.5 4 6.5 5.5 5 7.5 5c1.54 0 3.04.99 3.57 2.36h1.87C13.46 5.99 14.96 5 16.5 5c2 0 3.5 1.5 3.5 3.5 0 2.89-3.14 5.74-7.9 10.05z',
		plus_2: 'M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z',
		close: 'M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z',
		arrow_2: 'M22 12l-4-4v3H3v2h15v3z',
		dollar: 'M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z',
		sun_1: 'M6.76 4.84l-1.8-1.79-1.41 1.41 1.79 1.79 1.42-1.41zM4 10.5H1v2h3v-2zm9-9.95h-2V3.5h2V.55zm7.45 3.91l-1.41-1.41-1.79 1.79 1.41 1.41 1.79-1.79zm-3.21 13.7l1.79 1.8 1.41-1.41-1.8-1.79-1.4 1.4zM20 10.5v2h3v-2h-3zm-8-5c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm-1 16.95h2V19.5h-2v2.95zm-7.45-3.91l1.41 1.41 1.79-1.8-1.41-1.41-1.79 1.8z',
		sun_2: 'M7 11H1v2h6v-2zm2.17-3.24L7.05 5.64 5.64 7.05l2.12 2.12 1.41-1.41zM13 1h-2v6h2V1zm5.36 6.05l-1.41-1.41-2.12 2.12 1.41 1.41 2.12-2.12zM17 11v2h6v-2h-6zm-5-2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3zm2.83 7.24l2.12 2.12 1.41-1.41-2.12-2.12-1.41 1.41zm-9.19.71l1.41 1.41 2.12-2.12-1.41-1.41-2.12 2.12zM11 23h2v-6h-2v6z',
		snowflake: 'M22 11h-4.17l3.24-3.24-1.41-1.42L15 11h-2V9l4.66-4.66-1.42-1.41L13 6.17V2h-2v4.17L7.76 2.93 6.34 4.34 11 9v2H9L4.34 6.34 2.93 7.76 6.17 11H2v2h4.17l-3.24 3.24 1.41 1.42L9 13h2v2l-4.66 4.66 1.42 1.41L11 17.83V22h2v-4.17l3.24 3.24 1.42-1.41L13 15v-2h2l4.66 4.66 1.41-1.42L17.83 13H22z',
		party: 'M4.59 6.89c.7-.71 1.4-1.35 1.71-1.22.5.2 0 1.03-.3 1.52-.25.42-2.86 3.89-2.86 6.31 0 1.28.48 2.34 1.34 2.98.75.56 1.74.73 2.64.46 1.07-.31 1.95-1.4 3.06-2.77 1.21-1.49 2.83-3.44 4.08-3.44 1.63 0 1.65 1.01 1.76 1.79-3.78.64-5.38 3.67-5.38 5.37 0 1.7 1.44 3.09 3.21 3.09 1.63 0 4.29-1.33 4.69-6.1H21v-2.5h-2.47c-.15-1.65-1.09-4.2-4.03-4.2-2.25 0-4.18 1.91-4.94 2.84-.58.73-2.06 2.48-2.29 2.72-.25.3-.68.84-1.11.84-.45 0-.72-.83-.36-1.92.35-1.09 1.4-2.86 1.85-3.52.78-1.14 1.3-1.92 1.3-3.28C8.95 3.69 7.31 3 6.44 3 5.12 3 3.97 4 3.72 4.25c-.36.36-.66.66-.88.93l1.75 1.71zm9.29 11.66c-.31 0-.74-.26-.74-.72 0-.6.73-2.2 2.87-2.76-.3 2.69-1.43 3.48-2.13 3.48z',
		flower_1: 'M18.7 12.4c-.28-.16-.57-.29-.86-.4.29-.11.58-.24.86-.4 1.92-1.11 2.99-3.12 3-5.19-1.79-1.03-4.07-1.11-6 0-.28.16-.54.35-.78.54.05-.31.08-.63.08-.95 0-2.22-1.21-4.15-3-5.19C10.21 1.85 9 3.78 9 6c0 .32.03.64.08.95-.24-.2-.5-.39-.78-.55-1.92-1.11-4.2-1.03-6 0 0 2.07 1.07 4.08 3 5.19.28.16.57.29.86.4-.29.11-.58.24-.86.4-1.92 1.11-2.99 3.12-3 5.19 1.79 1.03 4.07 1.11 6 0 .28-.16.54-.35.78-.54-.05.32-.08.64-.08.96 0 2.22 1.21 4.15 3 5.19 1.79-1.04 3-2.97 3-5.19 0-.32-.03-.64-.08-.95.24.2.5.38.78.54 1.92 1.11 4.2 1.03 6 0-.01-2.07-1.08-4.08-3-5.19zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4z',
		flower_2: 'M12 22c4.97 0 9-4.03 9-9-4.97 0-9 4.03-9 9zM5.6 10.25c0 1.38 1.12 2.5 2.5 2.5.53 0 1.01-.16 1.42-.44l-.02.19c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5l-.02-.19c.4.28.89.44 1.42.44 1.38 0 2.5-1.12 2.5-2.5 0-1-.59-1.85-1.43-2.25.84-.4 1.43-1.25 1.43-2.25 0-1.38-1.12-2.5-2.5-2.5-.53 0-1.01.16-1.42.44l.02-.19C14.5 2.12 13.38 1 12 1S9.5 2.12 9.5 3.5l.02.19c-.4-.28-.89-.44-1.42-.44-1.38 0-2.5 1.12-2.5 2.5 0 1 .59 1.85 1.43 2.25-.84.4-1.43 1.25-1.43 2.25zM12 5.5c1.38 0 2.5 1.12 2.5 2.5s-1.12 2.5-2.5 2.5S9.5 9.38 9.5 8s1.12-2.5 2.5-2.5zM3 13c0 4.97 4.03 9 9 9 0-4.97-4.03-9-9-9z',
		fire: 'M13.5.67s.74 2.65.74 4.8c0 2.06-1.35 3.73-3.41 3.73-2.07 0-3.63-1.67-3.63-3.73l.03-.36C5.21 7.51 4 10.62 4 14c0 4.42 3.58 8 8 8s8-3.58 8-8C20 8.61 17.41 3.8 13.5.67zM11.71 19c-1.78 0-3.22-1.4-3.22-3.14 0-1.62 1.05-2.76 2.81-3.12 1.77-.36 3.6-1.21 4.62-2.58.39 1.29.59 2.65.59 4.04 0 2.65-2.15 4.8-4.8 4.8z',
		pizza: 'M12 2C8.43 2 5.23 3.54 3.01 6L12 22l8.99-16C18.78 3.55 15.57 2 12 2zM7 7c0-1.1.9-2 2-2s2 .9 2 2-.9 2-2 2-2-.9-2-2zm5 8c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z'
		
	};
	
	function getDefaults() {
	
		return {
	
			particles: {
				shape: 'circle',
				number: 80,
				size: 6,
				sizeMin: 1,
				random: true
			},
			styles: {
				border: {
					enable: false,
					color: '#ffffff',
					opacity: 100,
					size: 1
				},
				lines: {
					enable: false,
					color: '#ffffff',
					width: 1,
					opacity: 100,
					distance: 150
				},
				particle: {
					color: '#ffffff',
					opacity: 100,
					opacityMin: 25,
					opacityRandom: false,
					zIndex: 'default'
				}
			},
			movement: {
				enable: true,
				randomSpeed: true,
				speed: 1,
				speedMin: 1,
				direction: 'none',
				straight: true,
				bounce: false
			},
			interactivity: {
				hoverMode: 'none',
				clickMode: 'none'
			},
			bubble: {
				distance: 400,
				size: 40,
				opacity: 40
			},
			grab: {
				distance: 400,
				opacity: 50
			},
			repulse: {
				distance: 200,
				easing: 100
			},
			pulse: {
				size: {
					enable: false,
					speed: 40,
					min: 1,
					sync: false
				},
				opacity: {
					enable: false,
					speed: 3,
					min: 1,
					sync: false
				}
			}
			
		};
	
	}
	
	function convertSvgs(val) {
		
		val = val.split(',');
		var len = val.length,
			st = '';
		
		for(var i = 0; i < len; i++) {
			
			if(i > 0) st += ',';
			if(svgs.hasOwnProperty(val[i])) st += svgs[val[i]];
			else st += val[i];
			
		}
		
		return st;
		
	}
	
	function convertOpacity(val) {
		
		return val.toString().search(/\./g) !== -1 ? parseFloat(val) : parseInt(val, 10) * 0.01;
		
	}
	
	function trueFalse(val) {
		
		if(val === true || val === 'true' || val === 1 || val === '1' || val === 'on') return true;
		else return false;
		
	}
	
	function minMax(val, min, max) {
		
		return Math.max(Math.min(val, max), val, min);
	
	}
	
	function convertOptions(data) {
		
		var borderSize = data.styles.border.enable ? data.styles.border.size : 0,
			onHover = data.interactivity.hoverMode === 'none' ? false : true,
			onClick = data.interactivity.clickMode === 'none' ? false : true;
			
		var direction = data.movement.direction,
			moveStraight = !trueFalse(data.movement.straight),
			randomMove = trueFalse(data.movement.randomSpeed),
			outMode = data.movement.bounce ? 'bounce' : 'out';
			
		if(direction === 'none') {
				
			moveStraight = false;
			
		}
		else if(direction === 'static') {
			
			direction = 'none';
			moveStraight = true;
			randomMove = false;
			
		}
		
		return {
			
			zIndex: data.styles.particle.zIndex,
			particles: {
				number: {value: minMax(parseInt(data.particles.number, 10), 1, 500)}, color: {value: data.styles.particle.color},
				shape: {
					type: 'image', 
					stroke: {width: parseInt(borderSize, 10), color: data.styles.border.color, opacity: minMax(convertOpacity(data.styles.border.opacity), 0, 1)},
					image: {src: convertSvgs(data.particles.shape)}
				},
				opacity: {
					value: minMax(convertOpacity(data.styles.particle.opacity), 0.1, 1), 
					random: trueFalse(data.styles.particle.opacityRandom), 
					min: minMax(convertOpacity(data.styles.particle.opacityMin), 0.1, 1), 
					anim: {enable: trueFalse(data.pulse.opacity.enable), speed: parseFloat(data.pulse.opacity.speed), opacity_min: minMax(convertOpacity(data.pulse.opacity.min), 0, 1), sync: trueFalse(data.pulse.opacity.sync)}
				},
				size: {
					value: minMax(parseInt(data.particles.size, 10), 1, 250), 
					random: trueFalse(data.particles.random), 
					min: minMax(parseInt(data.particles.sizeMin, 10), 0.1, 250), 
					anim: {enable: trueFalse(data.pulse.size.enable), speed: parseFloat(data.pulse.size.speed), size_min: minMax(parseFloat(data.pulse.size.min), 0.1, 250), sync: trueFalse(data.pulse.size.sync)}
				},
				line_linked: {enable: trueFalse(data.styles.lines.enable), distance: parseInt(data.styles.lines.distance, 10), color: data.styles.lines.color, opacity: minMax(convertOpacity(data.styles.lines.opacity), 0, 1), width: parseInt(data.styles.lines.width, 10)},
				move: {enable: trueFalse(data.movement.enable), speed: minMax(parseInt(data.movement.speed, 10), 1, 50), direction: direction, random: randomMove, min_speed: minMax(parseInt(data.movement.speedMin, 10), 1, 50), straight: moveStraight, out_mode: outMode}
			},
			interactivity: {
				events: {
					onhover: {enable: onHover, mode: data.interactivity.hoverMode}, 
					onclick: {enable: onClick, mode: data.interactivity.clickMode}
				},
				modes: {
					grab: {distance: parseFloat(data.grab.distance), line_linked: {opacity: minMax(convertOpacity(data.grab.opacity), 0.1, 1)}}, 
					bubble: {distance: parseFloat(data.bubble.distance), size: parseFloat(data.bubble.size), opacity: minMax(convertOpacity(data.bubble.opacity), 0, 1)}, 
					repulse: {distance: parseFloat(data.repulse.distance), easing: parseInt(data.repulse.easing, 10)}
				}
			}
		};
		
	}
	
	function toRGBA(colors, opacity) {
		
		var hex,
			color,
			i = colors.length;
		
		while(i--) {
		
			hex = colors[i].replace('#', '');
			color = 'rgba(' + 
					    parseInt(hex.substring(0, 2), 16) + ',' + 
					    parseInt(hex.substring(2, 4), 16) + ',' + 
					    parseInt(hex.substring(4, 6), 16);
						
			if(opacity) color +=  ',' + opacity.toFixed(2) + ')';
			else color +=  ')';
			colors[i] = color;
		
		}
		
		return colors;
		
	}
	
	/* the magic, modified for RevSlider */

	/* -----------------------------------------------
	/* Author : Vincent Garreau  - vincentgarreau.com
	/* MIT license: http://opensource.org/licenses/MIT
	/* Demo / Generator : vincentgarreau.com/particles.js
	/* GitHub : github.com/VincentGarreau/particles.js
	/* How to use? : Check the GitHub README
	/* v2.0.0
	/* ----------------------------------------------- */

	var pJS = function(canvas_el, params, slider, revapi){
	  
	  /* particles.js variables with default values */
	  this.pJS = {
		canvas: {
		  el: canvas_el,
		  w: canvas_el.offsetWidth,
		  h: canvas_el.offsetHeight
		},
		particles: {
		  number: {
			value: 400,
			density: {
			  enable: true,
			  value_area: 800
			}
		  },
		  color: {
			value: '#fff'
		  },
		  shape: {
			type: 'circle',
			stroke: {
			  width: 0,
			  color: '#ff0000'
			},
			polygon: {
			  nb_sides: 5
			},
			image: {
			  src: '',
			  width: 100,
			  height: 100
			}
		  },
		  opacity: {
			value: 1,
			random: false,
			min: 0.1,
			anim: {
			  enable: false,
			  speed: 2,
			  opacity_min: 0,
			  sync: false
			}
		  },
		  size: {
			value: 20,
			drawSize: 40,
			random: false,
			min: 1,
			anim: {
			  enable: false,
			  speed: 20,
			  size_min: 0,
			  sync: false
			}
		  },
		  line_linked: {
			enable: false,
			distance: 100,
			color: '#fff',
			opacity: 1,
			width: 1
		  },
		  move: {
			enable: true,
			speed: 2,
			direction: 'none',
			random: false,
			min_speed: 1,
			straight: false,
			out_mode: 'out',
			bounce: false,
			attract: {
			  enable: false,
			  rotateX: 3000,
			  rotateY: 3000
			}
		  },
		  array: []
		},
		interactivity: {
		  detect_on: 'canvas',
		  events: {
			onhover: {
			  enable: true,
			  mode: 'grab'
			},
			onclick: {
			  enable: true,
			  mode: 'push'
			},
			resize: true
		  },
		  modes: {
			grab:{
			  distance: 100,
			  line_linked:{
				opacity: 1
			  }
			},
			bubble:{
			  distance: 200,
			  size: 80,
			  duration: 0.4
			},
			repulse:{
			  distance: 200,
			  duration: 0.4,
			  easing: 100
			},
			push:{
			  particles_nb: 4
			},
			remove:{
			  particles_nb: 2
			}
		  },
		  mouse:{}
		},
		retina_detect: false,
		offset: slider.offset(),
		fn: {
		  interact: {},
		  modes: {},
		  vendors:{}
		},
		tmp: {}
	  };

	  var pJS = this.pJS;
	  var $this = this;

	  /* params settings */
	  pJS = jQuery.extend(true, pJS, params);
	  
	  this.size_value = pJS.particles.size.value;

	  pJS.tmp.obj = {
		size_value: pJS.particles.size.value,
		size_anim_speed: pJS.particles.size.anim.speed,
		move_speed: pJS.particles.move.speed,
		line_linked_distance: pJS.particles.line_linked.distance,
		line_linked_width: pJS.particles.line_linked.width,
		mode_grab_distance: pJS.interactivity.modes.grab.distance,
		mode_bubble_distance: pJS.interactivity.modes.bubble.distance,
		mode_bubble_size: pJS.interactivity.modes.bubble.size,
		mode_repulse_distance: pJS.interactivity.modes.repulse.distance
	  };
		
	  // https://github.com/VincentGarreau/particles.js/issues/133
	  pJS.tmp.count_svg = 0;

	  pJS.fn.retinaInit = function(){
		  
		if(pJS.retina_detect && window.devicePixelRatio > 1){
		  pJS.canvas.pxratio = window.devicePixelRatio; 
		  pJS.tmp.retina = true;
		} 
		else{
		  pJS.canvas.pxratio = 1;
		  pJS.tmp.retina = false;
		}

		pJS.canvas.w = pJS.canvas.el.offsetWidth * pJS.canvas.pxratio;
		pJS.canvas.h = pJS.canvas.el.offsetHeight * pJS.canvas.pxratio;
		
		pJS.particles.size.value = pJS.tmp.obj.size_value * pJS.canvas.pxratio;
		pJS.particles.size.anim.speed = pJS.tmp.obj.size_anim_speed * pJS.canvas.pxratio;
		pJS.particles.move.speed = pJS.tmp.obj.move_speed * pJS.canvas.pxratio;
		pJS.particles.line_linked.distance = pJS.tmp.obj.line_linked_distance * pJS.canvas.pxratio;
		pJS.interactivity.modes.grab.distance = pJS.tmp.obj.mode_grab_distance * pJS.canvas.pxratio;
		pJS.interactivity.modes.bubble.distance = pJS.tmp.obj.mode_bubble_distance * pJS.canvas.pxratio;
		pJS.particles.line_linked.width = pJS.tmp.obj.line_linked_width * pJS.canvas.pxratio;
		pJS.interactivity.modes.bubble.size = pJS.tmp.obj.mode_bubble_size * pJS.canvas.pxratio;
		pJS.interactivity.modes.repulse.distance = pJS.tmp.obj.mode_repulse_distance * pJS.canvas.pxratio;

	  };



	  /* ---------- pJS functions - canvas ------------ */

	  pJS.fn.canvasInit = function(){
		pJS.canvas.ctx = pJS.canvas.el.getContext('2d');
	  };

	  pJS.fn.canvasSize = function(){

		pJS.canvas.el.width = pJS.canvas.w;
		pJS.canvas.el.height = pJS.canvas.h;
		
		if(pJS && pJS.interactivity.events.resize){
			
		  pJS.resizeFunction = function(){
			  
			  pJS.offset = slider.offset();
			  pJS.canvas.w = pJS.canvas.el.offsetWidth;
			  pJS.canvas.h = pJS.canvas.el.offsetHeight;

			  /* resize canvas */
			  if(pJS.tmp.retina){
				pJS.canvas.w *= pJS.canvas.pxratio;
				pJS.canvas.h *= pJS.canvas.pxratio;
			  }

			  pJS.canvas.el.width = pJS.canvas.w;
			  pJS.canvas.el.height = pJS.canvas.h;

			  /* repaint canvas on anim disabled */
			  if(!pJS.particles.move.enable){
				pJS.fn.particlesEmpty();
				pJS.fn.particlesCreate();
				pJS.fn.particlesDraw();
				pJS.fn.vendors.densityAutoParticles();
			  }
			  
			  pJS.sliderResized = true;

			/* density particles enabled */
			pJS.fn.vendors.densityAutoParticles();

		  };	
			
		  revapi.on('revolution.slide.afterdraw', pJS.resizeFunction);

		}

	  };


	  pJS.fn.canvasPaint = function(){
		pJS.canvas.ctx.fillRect(0, 0, pJS.canvas.w, pJS.canvas.h);
	  };

	  pJS.fn.canvasClear = function(){
		pJS.canvas.ctx.clearRect(0, 0, pJS.canvas.w, pJS.canvas.h);
	  };


	  /* --------- pJS functions - particles ----------- */

	  pJS.fn.particle = function(color, opacity, position){

		/* size */
		var rds = pJS.particles.size.value;

		if(pJS.particles.size.random) {
			var mrds = pJS.particles.size.min;
			rds = Math.random() * (rds - mrds) + mrds;
			if(rds === 0) rds = 1;
		}

		this.radius = rds;
		this.osize = rds;
		
		if(pJS.particles.size.anim.enable){
		  this.size_status = false;
		  this.vs = pJS.particles.size.anim.speed / 100;
		  if(!pJS.particles.size.anim.sync){
			this.vs = this.vs * Math.random();
		  }
		}

		/* position */
		this.x = position ? position.x : Math.random() * pJS.canvas.w;
		this.y = position ? position.y : Math.random() * pJS.canvas.h;

		/* check position  - into the canvas */
		if(this.x > pJS.canvas.w - this.radius*2) this.x = this.x - this.radius;
		else if(this.x < this.radius*2) this.x = this.x + this.radius;
		if(this.y > pJS.canvas.h - this.radius*2) this.y = this.y - this.radius;
		else if(this.y < this.radius*2) this.y = this.y + this.radius;

		/* check position - avoid overlap */
		if(pJS.particles.move.bounce){
		  pJS.fn.vendors.checkOverlap(this, position);
		}
		
		/* speed */
		var spd = pJS.particles.move.speed,
		mspd = pJS.particles.move.min_speed;
		
		if(pJS.particles.move.random) {
			spd = Math.round(Math.random() * (spd - mspd) + mspd);
			if(spd < 1) spd = 1;
		}
		
		this.spd = spd;

		/* color */
		this.color = {};
		if(typeof(color.value) === 'object'){

		  if(color.value instanceof Array){
			var color_selected = color.value[Math.floor(Math.random() * pJS.particles.color.value.length)];
			this.color.rgb = hexToRgb(color_selected);
		  }else{
			if(color.value.r != undefined && color.value.g != undefined && color.value.b != undefined){
			  this.color.rgb = {
				r: color.value.r,
				g: color.value.g,
				b: color.value.b
			  };
			}
			if(color.value.h != undefined && color.value.s != undefined && color.value.l != undefined){
			  this.color.hsl = {
				h: color.value.h,
				s: color.value.s,
				l: color.value.l
			  };
			}
		  }

		}
		else if(color.value === 'random'){
		  this.color.rgb = {
			r: (Math.floor(Math.random() * (255 - 0 + 1)) + 0),
			g: (Math.floor(Math.random() * (255 - 0 + 1)) + 0),
			b: (Math.floor(Math.random() * (255 - 0 + 1)) + 0)
		  };
		}
		else if(typeof(color.value) === 'string'){
		  this.color = color;
		  this.color.rgb = hexToRgb(this.color.value);
		}
		
		/* variable border colors */
		var bColor = pJS.particles.shape.stroke.color;
		this.strokeColor = bColor[Math.floor(Math.random() * bColor.length)];
		
		/* variable line colors */
		var lColor = pJS.particles.line_linked.color;
		this.lineColor = lColor[Math.floor(Math.random() * lColor.length)];
		
		var opacit = pJS.particles.opacity.value,
		mopc = pJS.particles.opacity.min;
		
		if(pJS.particles.opacity.random) {
			opacit = Math.random() * (opacit - mopc) + mopc;
		}
	
		this.opc = opacit;
		this.opacity = opacit;
		
		if(pJS.particles.opacity.anim.enable){
		  this.opacity_status = false;
		  this.vo = pJS.particles.opacity.anim.speed / 100;
		  if(!pJS.particles.opacity.anim.sync){
			this.vo = this.vo * Math.random();
		  }
		}

		/* animation - velocity for speed */
		var velbasex = 0, velbasey = 0;
		switch(pJS.particles.move.direction){
		  case 'top':
			velbasey = -1;
		  break;
		  case 'top-right':
			velbasex = 0.5;
			velbasey = -0.5;
		  break;
		  case 'right':
			velbasex = 1;
		  break;
		  case 'bottom-right':
			velbasex = 0.5;
			velbasey = 0.5;
		  break;
		  case 'bottom':
			velbasey = 1;
		  break;
		  case 'bottom-left':
			velbasex = -0.5;
			velbasey = 1;
		  break;
		  case 'left':
			velbasex = -1;
		  break;
		  case 'top-left':
			velbasex = -0.5;
			velbasey = -0.5;
		  break;
		}

		if(pJS.particles.move.straight){
		  this.vx = velbasex;
		  this.vy = velbasey;
		}else{
		  this.vx = velbasex + Math.random()-0.5;
		  this.vy = velbasey + Math.random()-0.5;
		}

		this.vx_i = this.vx;
		this.vy_i = this.vy;
		
		/* if shape is image */

		var shape_type = pJS.particles.shape.type;
		if(typeof(shape_type) === 'object'){
		  if(shape_type instanceof Array){
			var shape_selected = shape_type[Math.floor(Math.random() * shape_type.length)];
			this.shape = shape_selected;
		  }
		}else{
		  this.shape = shape_type;
		}

		if(this.shape === 'image'){
		  var sh = pJS.particles.shape;
		  this.img = {
			src: sh.image.src,
			ratio: sh.image.width / sh.image.height
		  };
		  if(!this.img.ratio) this.img.ratio = 1;
		  if(pJS.tmp.img_type === 'svg' && pJS.tmp.source_svg != undefined){
			
			pJS.fn.vendors.createSvgImg(this);
			if(pJS.tmp.pushing){
			  this.img.loaded = false;
			}
			
		  }
		}

		

	  };
	  
	  pJS.fn.particle.prototype.drawSVG = function(img_obj, radius) {
		  
		  pJS.canvas.ctx.drawImage(
			img_obj,
			this.x - radius,
			this.y - radius,
			radius * 2,
			radius * 2 / this.img.ratio
		  );
		  
	  };

	  pJS.fn.particle.prototype.draw = function() {
		
		var p = this, svg, radius, opacity, color_value;

		if(p.radius_bubble != undefined){
		  radius = p.radius_bubble; 
		}else{
		  radius = p.radius;
		}

		if(p.opacity_bubble != undefined){
		  opacity = p.opacity_bubble;
		}else{
		  opacity = p.opacity;
		}

		if(p.color.rgb){
		  color_value = 'rgba('+p.color.rgb.r+','+p.color.rgb.g+','+p.color.rgb.b+','+opacity+')';
		}else{
		  color_value = 'hsla('+p.color.hsl.h+','+p.color.hsl.s+'%,'+p.color.hsl.l+'%,'+opacity+')';
		}

		pJS.canvas.ctx.fillStyle = color_value;
		pJS.canvas.ctx.beginPath();
		
		/*
		switch(p.shape){

		  case 'circle':
			pJS.canvas.ctx.arc(p.x, p.y, radius, 0, Math.PI * 2, false);
		  break;

		  case 'edge':
			pJS.canvas.ctx.rect(p.x-radius, p.y-radius, radius*2, radius*2);
		  break;

		  case 'triangle':
			pJS.fn.vendors.drawShape(pJS.canvas.ctx, p.x-radius, p.y+radius / 1.66, radius*2, 3, 2);
		  break;

		  case 'polygon':
			pJS.fn.vendors.drawShape(
			  pJS.canvas.ctx,
			  p.x - radius / (pJS.particles.shape.polygon.nb_sides/3.5), // startX
			  p.y - radius / (2.66/3.5), // startY
			  radius*2.66 / (pJS.particles.shape.polygon.nb_sides/3), // sideLength
			  pJS.particles.shape.polygon.nb_sides, // sideCountNumerator
			  1 // sideCountDenominator
			);
		  break;

		  case 'star':
			pJS.fn.vendors.drawShape(
			  pJS.canvas.ctx,
			  p.x - radius*2 / (pJS.particles.shape.polygon.nb_sides/4), // startX
			  p.y - radius / (2*2.66/3.5), // startY
			  radius*2*2.66 / (pJS.particles.shape.polygon.nb_sides/3), // sideLength
			  pJS.particles.shape.polygon.nb_sides, // sideCountNumerator
			  2 // sideCountDenominator
			);
		  break;

		  case 'image':
		*/
			
		pJS.canvas.ctx.globalAlpha = opacity;
		
		var img_obj;
		if(pJS.tmp.img_type === 'svg'){
		  img_obj = p.img.obj;
		}else{
		  img_obj = pJS.tmp.img_obj;
		}

		if(img_obj){
		  p.drawSVG(img_obj, radius);
		}
		
		pJS.canvas.ctx.globalAlpha = 1.0;
		svg = true;
		
		/*
		  break;

		}
		*/

		pJS.canvas.ctx.closePath();
		
		if(!svg) {
		
			if(pJS.particles.shape.stroke.width > 0){
			  
			  pJS.canvas.ctx.strokeStyle = p.strokeColor;
			  pJS.canvas.ctx.lineWidth = pJS.particles.shape.stroke.width;
			  pJS.canvas.ctx.stroke();
			}
			
			pJS.canvas.ctx.fill();
			
		}
		
	  };


	  pJS.fn.particlesCreate = function(){
		  
		var len = pJS.particles.number.value,
			ar = pJS.particles.array;
		
		for(var i = 0; i < len; i++) {
		  ar[ar.length] = new pJS.fn.particle(pJS.particles.color, pJS.particles.opacity.value);
		}
	  };

	  pJS.fn.particlesUpdate = function(){
		
		var len = pJS.particles.array.length;
		
		for(var i = 0; i < len; i++){

		  /* the particle */
		  var p = pJS.particles.array[i];

		  /* move the particle */
		  if(pJS.particles.move.enable){
			
			var ms = p.spd/2;
			p.x += p.vx * ms;
			p.y += p.vy * ms;
			
		  }

		  /* change opacity status */
		  if(pJS.particles.opacity.anim.enable) {
			if(p.opacity_status == true) {
			  if(p.opacity >= p.opc) p.opacity_status = false;
			  p.opacity += p.vo;
			}else {
			  if(p.opacity <= pJS.particles.opacity.anim.opacity_min) p.opacity_status = true;
			  p.opacity -= p.vo;
			}
			if(p.opacity < 0) p.opacity = 0;
		  }

		  /* change size */
		  if(pJS.particles.size.anim.enable){
			if(p.size_status == true){
			  if(p.radius >= pJS.particles.size.value) p.size_status = false;
			  p.radius += p.vs;
			}else{
			  if(p.radius <= pJS.particles.size.anim.size_min) p.size_status = true;
			  p.radius -= p.vs;
			}
			if(p.radius < 0) p.radius = 0;
		  }
		  
		  var x_left, x_right, y_top, y_bottom;

		  /* change particle position if it is out of canvas */
		  if(pJS.particles.move.out_mode === 'bounce'){
			x_left = p.radius;
			x_right = pJS.canvas.w;
			y_top = p.radius;
			y_bottom = pJS.canvas.h;
		  }else{
			x_left = -p.radius;
			x_right = pJS.canvas.w + p.radius;
			y_top = -p.radius;
			y_bottom = pJS.canvas.h + p.radius;
		  }

		  if(p.x - p.radius > pJS.canvas.w){
			p.x = x_left;
			p.y = Math.random() * pJS.canvas.h;
		  }
		  else if(p.x + p.radius < 0){
			p.x = x_right;
			p.y = Math.random() * pJS.canvas.h;
		  }
		  if(p.y - p.radius > pJS.canvas.h){
			p.y = y_top;
			p.x = Math.random() * pJS.canvas.w;
		  }
		  else if(p.y + p.radius < 0){
			p.y = y_bottom;
			p.x = Math.random() * pJS.canvas.w;
		  }

		  /* out of canvas modes */
		  switch(pJS.particles.move.out_mode){
			case 'bounce':
			  if (p.x + p.radius > pJS.canvas.w) p.vx = -p.vx;
			  else if (p.x - p.radius < 0) p.vx = -p.vx;
			  if (p.y + p.radius > pJS.canvas.h) p.vy = -p.vy;
			  else if (p.y - p.radius < 0) p.vy = -p.vy;
			break;
		  }

		  /* events */
		  if(isInArray('grab', pJS.interactivity.events.onhover.mode)){
			pJS.fn.modes.grabParticle(p);
		  }

		  if(isInArray('bubble', pJS.interactivity.events.onhover.mode) || isInArray('bubble', pJS.interactivity.events.onclick.mode)){
			pJS.fn.modes.bubbleParticle(p);
		  }

		  if(isInArray('repulse', pJS.interactivity.events.onhover.mode) || isInArray('repulse', pJS.interactivity.events.onclick.mode)){
			pJS.fn.modes.repulseParticle(p);
		  }

		  /* interaction auto between particles */
		  if(pJS.particles.line_linked.enable || pJS.particles.move.attract.enable){
			  
			var leg =  pJS.particles.array.length;
			  
			for(var j = i + 1; j < leg; j++){
			  var p2 = pJS.particles.array[j];

			  /* link particles */
			  if(pJS.particles.line_linked.enable){
				pJS.fn.interact.linkParticles(p,p2);
			  }

			  /* attract particles */
			  if(pJS.particles.move.attract.enable){
				pJS.fn.interact.attractParticles(p,p2);
			  }

			  /* bounce particles */
			  if(pJS.particles.move.bounce){
				pJS.fn.interact.bounceParticles(p,p2);
			  }

			}
		  }


		}

	  };

	  pJS.fn.particlesDraw = function(){

		/* clear canvas */
		pJS.canvas.ctx.clearRect(0, 0, pJS.canvas.w, pJS.canvas.h);

		/* update each particles param */
		pJS.fn.particlesUpdate();

		/* draw each particle */
		var len = pJS.particles.array.length;
		for(var i = 0; i < len; i++){
		  var p = pJS.particles.array[i];
		  p.draw();
		}

	  };

	  pJS.fn.particlesEmpty = function(){
		pJS.particles.array = [];
	  };

	  pJS.fn.particlesRefresh = function(){

		/* init all */
		cancelAnimationFrame(pJS.fn.checkAnimFrame);
		cancelAnimationFrame(pJS.fn.drawAnimFrame);
		pJS.tmp.source_svg = undefined;
		pJS.tmp.img_obj = undefined;
		pJS.tmp.count_svg = 0;
		pJS.fn.particlesEmpty();
		pJS.fn.canvasClear();
		
		/* restart */
		pJS.fn.vendors.start();

	  };


	  /* ---------- pJS functions - particles interaction ------------ */

	  pJS.fn.interact.linkParticles = function(p1, p2){

		var dx = p1.x - p2.x,
			dy = p1.y - p2.y,
			dist = Math.sqrt(dx*dx + dy*dy);

		/* draw a line between p1 and p2 if the distance between them is under the config distance */
		if(dist <= pJS.particles.line_linked.distance){

		  var opacity_line = pJS.particles.line_linked.opacity - (dist / (1/pJS.particles.line_linked.opacity)) / pJS.particles.line_linked.distance;

		  if(opacity_line > 0){        
			
			/* style */
			var color_line = p1.lineColor;
				
			pJS.canvas.ctx.strokeStyle = 'rgba('+color_line.r+','+color_line.g+','+color_line.b+','+opacity_line+')';
			pJS.canvas.ctx.lineWidth = pJS.particles.line_linked.width;
			//pJS.canvas.ctx.lineCap = 'round'; /* performance issue */
			
			/* path */
			pJS.canvas.ctx.beginPath();
			pJS.canvas.ctx.moveTo(p1.x, p1.y);
			pJS.canvas.ctx.lineTo(p2.x, p2.y);
			pJS.canvas.ctx.stroke();
			pJS.canvas.ctx.closePath();

		  }

		}

	  };


	  pJS.fn.interact.attractParticles  = function(p1, p2){

		/* condensed particles */
		var dx = p1.x - p2.x,
			dy = p1.y - p2.y,
			dist = Math.sqrt(dx*dx + dy*dy);

		if(dist <= pJS.particles.line_linked.distance){

		  var ax = dx/(pJS.particles.move.attract.rotateX*1000),
			  ay = dy/(pJS.particles.move.attract.rotateY*1000);

		  p1.vx -= ax;
		  p1.vy -= ay;

		  p2.vx += ax;
		  p2.vy += ay;

		}
		

	  };


	  pJS.fn.interact.bounceParticles = function(p1, p2){

		var dx = p1.x - p2.x,
			dy = p1.y - p2.y,
			dist = Math.sqrt(dx*dx + dy*dy),
			dist_p = p1.radius+p2.radius;

		if(dist <= dist_p){
		  p1.vx = -p1.vx;
		  p1.vy = -p1.vy;

		  p2.vx = -p2.vx;
		  p2.vy = -p2.vy;
		}

	  };


	  /* ---------- pJS functions - modes events ------------ */

	  pJS.fn.modes.pushParticles = function(nb, pos){
		
		nb = nb | 0;
		pJS.tmp.pushing = true;
		
		for(var i = 0; i < nb; i++){
			
		  pJS.particles.array.push(
			new pJS.fn.particle(
			  pJS.particles.color,
			  pJS.particles.opacity.value,
			  {
				'x': pos ? pos.pos_x : Math.random() * pJS.canvas.w,
				'y': pos ? pos.pos_y : Math.random() * pJS.canvas.h
			  }
			)
		  );

		}
		
		if(!pJS.particles.move.enable){
		  pJS.fn.particlesDraw();
		}
		pJS.tmp.pushing = false;

	  };


	  pJS.fn.modes.removeParticles = function(nb){

		pJS.particles.array.splice(0, nb);
		if(!pJS.particles.move.enable){
		  pJS.fn.particlesDraw();
		}

	  };


	  pJS.fn.modes.bubbleParticle = function(p){
		
		var size, opacity, dx_mouse, dy_mouse, time_spent, dist_mouse;
		/* on hover event */
		if(pJS.interactivity.events.onhover.enable && isInArray('bubble', pJS.interactivity.events.onhover.mode)){

		  dx_mouse = p.x - pJS.interactivity.mouse.pos_x;
	      dy_mouse = p.y - pJS.interactivity.mouse.pos_y;
		  dist_mouse = Math.sqrt(dx_mouse*dx_mouse + dy_mouse*dy_mouse);
		  var ratio = 1 - dist_mouse / pJS.interactivity.modes.bubble.distance;


		  /* mousemove - check ratio */
		  if(dist_mouse <= pJS.interactivity.modes.bubble.distance){

			if(ratio >= 0 && pJS.interactivity.status === 'mousemove'){
			  
			  /* size */
			  if(pJS.interactivity.modes.bubble.size != p.radius){

				if(pJS.interactivity.modes.bubble.size > p.radius){
				  size = p.radius + (pJS.interactivity.modes.bubble.size*ratio);
				  if(size >= 0){
					p.radius_bubble = size;
				  }
				}else{
				  var dif = p.radius - pJS.interactivity.modes.bubble.size;
			      size = p.radius - (dif*ratio);
				  if(size > 0){
					p.radius_bubble = size;
				  }else{
					p.radius_bubble = 0;
				  }
				}

			  }

			  /* opacity */
			  if(pJS.interactivity.modes.bubble.opacity != p.opc){

				if(pJS.interactivity.modes.bubble.opacity > p.opc){
				  opacity = pJS.interactivity.modes.bubble.opacity*ratio;
				  if(opacity > p.opacity && opacity <= pJS.interactivity.modes.bubble.opacity){
					p.opacity_bubble = opacity;
				  }
				}else{
				  opacity = p.opacity - (p.opc-pJS.interactivity.modes.bubble.opacity)*ratio;
				  if(opacity < p.opacity && opacity >= pJS.interactivity.modes.bubble.opacity){
					p.opacity_bubble = opacity;
				  }
				}

			  }

			}

		  }else{
			p.opacity_bubble = p.opacity;
			p.radius_bubble = p.radius;
		  }


		  /* mouseleave */
		  if(pJS.interactivity.status === 'mouseleave'){
			p.opacity_bubble = p.opacity;
			p.radius_bubble = p.radius;
		  }
		
		}

		/* on click event */
		else if(pJS.interactivity.events.onclick.enable && isInArray('bubble', pJS.interactivity.events.onclick.mode)){


		  if(pJS.tmp.bubble_clicking){
			    dx_mouse = p.x - pJS.interactivity.mouse.click_pos_x;
				dy_mouse = p.y - pJS.interactivity.mouse.click_pos_y;
				dist_mouse = Math.sqrt(dx_mouse*dx_mouse + dy_mouse*dy_mouse);
				time_spent = (new Date().getTime() - pJS.interactivity.mouse.click_time)/1000;

			if(time_spent > pJS.interactivity.modes.bubble.duration){
			  pJS.tmp.bubble_duration_end = true;
			}

			if(time_spent > pJS.interactivity.modes.bubble.duration*2){
			  pJS.tmp.bubble_clicking = false;
			  pJS.tmp.bubble_duration_end = false;
			}
		  }

		  if(pJS.tmp.bubble_clicking){
			
			processBubble(p, dist_mouse, time_spent, pJS.interactivity.modes.bubble.size, p.osize, p.radius_bubble, p.radius, 'size');
			processBubble(p, dist_mouse, time_spent, pJS.interactivity.modes.bubble.opacity, p.opc, p.opacity_bubble, p.opacity, 'opacity');
			
		  }

		}

	  };
		
	  function processBubble(p, dist_mouse, time_spent, bubble_param, particles_param, p_obj_bubble, p_obj, id){
		
		var value;
		if(bubble_param != particles_param){

		  if(!pJS.tmp.bubble_duration_end){
			if(dist_mouse <= pJS.interactivity.modes.bubble.distance){
			  var obj;
			  if(p_obj_bubble != undefined) obj = p_obj_bubble;
			  else obj = p_obj;
			  
			  if(obj != bubble_param){
				value = p_obj - (time_spent * (p_obj - bubble_param) / pJS.interactivity.modes.bubble.duration);
				if(id === 'size') p.radius_bubble = value;
				if(id === 'opacity') p.opacity_bubble = value;
			  }
			}else{
			  if(id === 'size') p.radius_bubble = undefined;
			  if(id === 'opacity') p.opacity_bubble = undefined;
			}
		  }else{
			if(p_obj_bubble != undefined){
			  var value_tmp = p_obj - (time_spent * (p_obj - bubble_param) / pJS.interactivity.modes.bubble.duration);
			  var dif = bubble_param - value_tmp;
			  value = bubble_param + dif;
			  if(id === 'size') p.radius_bubble = value;
			  if(id === 'opacity') p.opacity_bubble = value;
			}
		  }

		}

	  }	

	  pJS.fn.modes.repulseParticle = function(p){
		
		var repulseRadius;
		if(pJS.interactivity.events.onhover.enable && isInArray('repulse', pJS.interactivity.events.onhover.mode) && pJS.interactivity.status === 'mousemove') {

		  var dx_mouse = p.x - pJS.interactivity.mouse.pos_x,
			  dy_mouse = p.y - pJS.interactivity.mouse.pos_y,
			  dist_mouse = Math.sqrt(dx_mouse*dx_mouse + dy_mouse*dy_mouse);

		  var normVecx = dx_mouse/dist_mouse,
			  normVecy = dy_mouse/dist_mouse;
		  repulseRadius = pJS.interactivity.modes.repulse.distance;
		  var velocity = 100,
			  repulseFactor = clamp((1/repulseRadius)*(-1*Math.pow(dist_mouse/repulseRadius,2)+1)*repulseRadius*velocity, 0, 50);
		  
		  var posX,
			  posY,
			  easing;
		  
		  if(pJS.interactivity.modes.repulse.easing) {
			easing = pJS.interactivity.modes.repulse.easing / 16;
			posX = p.x + (((p.x + normVecx * repulseFactor) - p.x) / easing);
			posY = p.y + (((p.y + normVecy * repulseFactor) - p.y) / easing);
		  }
		  else {
			posX = p.x + normVecx * repulseFactor;
			posY = p.y + normVecy * repulseFactor;
		  }

		  if(pJS.particles.move.out_mode === 'bounce'){
			if(posX - p.radius > 0 && posX + p.radius < pJS.canvas.w) p.x = posX;
			if(posY - p.radius > 0 && posY + p.radius < pJS.canvas.h) p.y = posY;
		  }else{
			p.x = posX;
			p.y = posY;
		  }
		
		}


		else if(pJS.interactivity.events.onclick.enable && isInArray('repulse', pJS.interactivity.events.onclick.mode)) {

		  if(!pJS.tmp.repulse_finish){
			pJS.tmp.repulse_count++;
			if(pJS.tmp.repulse_count == pJS.particles.array.length){
			  pJS.tmp.repulse_finish = true;
			}
		  }

		  if(pJS.tmp.repulse_clicking){

			repulseRadius = Math.pow(pJS.interactivity.modes.repulse.distance/6, 3);

			var dx = pJS.interactivity.mouse.click_pos_x - p.x,
				dy = pJS.interactivity.mouse.click_pos_y - p.y,
				d = dx*dx + dy*dy;

			var force = -repulseRadius / d * 1;

			// default
			if(d <= repulseRadius){
				
			  // process();
			  
			  var f = Math.atan2(dy,dx);
			  p.vx = force * Math.cos(f);
			  p.vy = force * Math.sin(f);

			  if(pJS.particles.move.out_mode === 'bounce'){
				  
				var posx = p.x + p.vx,
				    posy = p.y + p.vy;
					
				if (posx + p.radius > pJS.canvas.w) p.vx = -p.vx;
				else if (posx - p.radius < 0) p.vx = -p.vx;
				if (posy + p.radius > pJS.canvas.h) p.vy = -p.vy;
				else if (posy - p.radius < 0) p.vy = -p.vy;
			  }
			  
			}
			

		  }else{

			if(pJS.tmp.repulse_clicking == false){

			  p.vx = p.vx_i;
			  p.vy = p.vy_i;
			
			}

		  }

		}

	  };


	  pJS.fn.modes.grabParticle = function(p){

		if(pJS.interactivity.events.onhover.enable && pJS.interactivity.status === 'mousemove'){

		  var dx_mouse = p.x - pJS.interactivity.mouse.pos_x,
			  dy_mouse = p.y - pJS.interactivity.mouse.pos_y,
			  dist_mouse = Math.sqrt(dx_mouse*dx_mouse + dy_mouse*dy_mouse);

		  /* draw a line between the cursor and the particle if the distance between them is under the config distance */
		  if(dist_mouse <= pJS.interactivity.modes.grab.distance){

			var opacity_line = pJS.interactivity.modes.grab.line_linked.opacity - (dist_mouse / (1/pJS.interactivity.modes.grab.line_linked.opacity)) / pJS.interactivity.modes.grab.distance;

			if(opacity_line > 0){

			  /* style */
			  var color_line = p.lineColor;
			  pJS.canvas.ctx.strokeStyle = 'rgba('+color_line.r+','+color_line.g+','+color_line.b+','+opacity_line+')';
			  pJS.canvas.ctx.lineWidth = pJS.particles.line_linked.width;
			  //pJS.canvas.ctx.lineCap = 'round'; /* performance issue */
			  
			  /* path */
			  pJS.canvas.ctx.beginPath();
			  pJS.canvas.ctx.moveTo(p.x, p.y);
			  pJS.canvas.ctx.lineTo(pJS.interactivity.mouse.pos_x, pJS.interactivity.mouse.pos_y);
			  pJS.canvas.ctx.stroke();
			  pJS.canvas.ctx.closePath();

			}

		  }

		}

	  };



	  /* ---------- pJS functions - vendors ------------ */

	  pJS.fn.vendors.eventsListeners = function(){

		/* events target element */
		if(pJS.interactivity.detect_on === 'window'){
		  pJS.interactivity.el = window;
		}else{
		  pJS.interactivity.el = pJS.canvas.el;
		}

		/* detect mouse pos - on hover / click event */
		if(pJS.interactivity.events.onhover.enable || pJS.interactivity.events.onclick.enable){
		  
		  slider.on('mousemove.rsparticles', function(e){
			
			pJS.interactivity.mouse.pos_x = e.pageX - pJS.offset.left;
			pJS.interactivity.mouse.pos_y = e.pageY - pJS.offset.top;
			pJS.interactivity.status = 'mousemove';

		  });

		  /* el on onmouseleave */
		  slider.on('mouseleave.rsparticles', function(e){
			
			pJS.interactivity.mouse.pos_x = null;
			pJS.interactivity.mouse.pos_y = null;
			pJS.interactivity.status = 'mouseleave';

		  });

		}

		/* on click event */
		if(pJS.interactivity.events.onclick.enable){

		  slider.on('click.rsparticles', function(){

			pJS.interactivity.mouse.click_pos_x = pJS.interactivity.mouse.pos_x;
			pJS.interactivity.mouse.click_pos_y = pJS.interactivity.mouse.pos_y;
			pJS.interactivity.mouse.click_time = new Date().getTime();

			if(pJS.interactivity.events.onclick.enable){

			  switch(pJS.interactivity.events.onclick.mode){

				case 'push':
				  if(pJS.particles.move.enable){
					pJS.fn.modes.pushParticles(pJS.interactivity.modes.push.particles_nb, pJS.interactivity.mouse);
				  }else{
					if(pJS.interactivity.modes.push.particles_nb == 1){
					  pJS.fn.modes.pushParticles(pJS.interactivity.modes.push.particles_nb, pJS.interactivity.mouse);
					}
					else if(pJS.interactivity.modes.push.particles_nb > 1){
					  pJS.fn.modes.pushParticles(pJS.interactivity.modes.push.particles_nb);
					}
				  }
				break;

				case 'remove':
				  pJS.fn.modes.removeParticles(pJS.interactivity.modes.remove.particles_nb);
				break;

				case 'bubble':
				  pJS.tmp.bubble_clicking = true;
				break;

				case 'repulse':
				  pJS.tmp.repulse_clicking = true;
				  pJS.tmp.repulse_count = 0;
				  pJS.tmp.repulse_finish = false;
				  setTimeout(onRepulse, pJS.interactivity.modes.repulse.duration*1000);
				break;

			  }

			}

		  });
			
		}


	  };
	  
	  function onRepulse() {
		  
		  pJS.tmp.repulse_clicking = false;
		  
	  }

	  pJS.fn.vendors.densityAutoParticles = function(){

		if(pJS.particles.number.density.enable){

		  /* calc area */
		  var area = pJS.canvas.el.width * pJS.canvas.el.height / 1000;
		  if(pJS.tmp.retina){
			area = area/(pJS.canvas.pxratio*2);
		  }

		  /* calc number of particles based on density area */
		  var nb_particles = area * pJS.particles.number.value / pJS.particles.number.density.value_area;

		  /* add or remove X particles */
		  var missing_particles = pJS.particles.array.length - nb_particles;
		  
		  if(missing_particles < 0) {
			  pJS.fn.modes.pushParticles(Math.abs(missing_particles)); 
		  }
		  else {
			  pJS.fn.modes.removeParticles(missing_particles);
		  }

		}

	  };


	  pJS.fn.vendors.checkOverlap = function(p1, position){
		  
		var len = pJS.particles.array.length;
		for(var i = 0; i < len; i++){
		  var p2 = pJS.particles.array[i];

		  var dx = p1.x - p2.x,
			  dy = p1.y - p2.y,
			  dist = Math.sqrt(dx*dx + dy*dy);

		  if(dist <= p1.radius + p2.radius){
			p1.x = position ? position.x : Math.random() * pJS.canvas.w;
			p1.y = position ? position.y : Math.random() * pJS.canvas.h;
			pJS.fn.vendors.checkOverlap(p1);
		  }
		}
	  };

	  pJS.fn.vendors.createSvgImg = function(p){
		
		 p.img.obj = pJS.cachedSvg[Math.floor(Math.random() * pJS.cachedSvg.length)];
		 p.img.loaded = true;
		 pJS.tmp.count_svg++;

	  };

	  pJS.fn.vendors.destroypJS = function(){
		  
		cancelAnimationFrame(pJS.fn.drawAnimFrame);
		
		var prop;
		for(prop in pJS) {if(pJS.hasOwnProperty(prop)) delete pJS[prop];}
		for(prop in $this) {if($this.hasOwnProperty(prop)) delete $this[prop];}
		
		pJS = null;
		$this = null;

	  };


	  pJS.fn.vendors.drawShape = function(c, startX, startY, sideLength, sideCountNumerator, sideCountDenominator){

		// By Programming Thomas - https://programmingthomas.wordpress.com/2013/04/03/n-sided-shapes/
		var sideCount = sideCountNumerator * sideCountDenominator;
		var decimalSides = sideCountNumerator / sideCountDenominator;
		var interiorAngleDegrees = (180 * (decimalSides - 2)) / decimalSides;
		var interiorAngle = Math.PI - Math.PI * interiorAngleDegrees / 180; // convert to radians
		c.save();
		c.beginPath();
		c.translate(startX, startY);
		c.moveTo(0,0);
		for (var i = 0; i < sideCount; i++) {
		  c.lineTo(sideLength,0);
		  c.translate(sideLength,0);
		  c.rotate(interiorAngle);
		}
		//c.stroke();
		c.fill();
		c.restore();

	  };

	  pJS.fn.vendors.loadImg = function(type, svg){
		
		pJS.tmp.source_svg = svg;
		pJS.fn.vendors.checkBeforeDraw();

	  };

	  pJS.fn.vendors.draw = function(){

		if(pJS.particles.shape.type === 'image'){

		  if(pJS.tmp.img_type === 'svg'){

			if(pJS.tmp.count_svg >= pJS.particles.number.value){
			  pJS.fn.particlesDraw();
			  if(!pJS.particles.move.enable) cancelAnimationFrame(pJS.fn.drawAnimFrame);
			  else pJS.fn.drawAnimFrame = requestAnimationFrame(pJS.fn.vendors.draw);
			}else{
			  if(!pJS.tmp.img_error) pJS.fn.drawAnimFrame = requestAnimationFrame(pJS.fn.vendors.draw);
			}

		  }else{

			if(pJS.tmp.img_obj != undefined){
			  pJS.fn.particlesDraw();
			  if(!pJS.particles.move.enable) cancelAnimationFrame(pJS.fn.drawAnimFrame);
			  else pJS.fn.drawAnimFrame = requestAnimationFrame(pJS.fn.vendors.draw);
			}else{
			  if(!pJS.tmp.img_error) pJS.fn.drawAnimFrame = requestAnimationFrame(pJS.fn.vendors.draw);
			}

		  }

		}else{
		  pJS.fn.particlesDraw();
		  if(!pJS.particles.move.enable) cancelAnimationFrame(pJS.fn.drawAnimFrame);
		  else pJS.fn.drawAnimFrame = requestAnimationFrame(pJS.fn.vendors.draw);
		}

	  };


	  pJS.fn.vendors.checkBeforeDraw = function(){

		// if shape is image
		if(pJS.particles.shape.type === 'image'){

		  if(pJS.tmp.img_type === 'svg' && pJS.tmp.source_svg == undefined){
			pJS.tmp.checkAnimFrame = requestAnimationFrame(check);
		  }else{
			cancelAnimationFrame(pJS.tmp.checkAnimFrame);
			if(!pJS.tmp.img_error){
			  pJS.fn.vendors.init();
			  pJS.fn.vendors.draw();
			}
			
		  }

		}else{
		  pJS.fn.vendors.init();
		  pJS.fn.vendors.draw();
		}

	  };

	  pJS.fn.vendors.init = function(){
		
		/* init canvas + particles */
		pJS.fn.retinaInit();
		pJS.fn.canvasInit();
		pJS.fn.canvasSize();
		pJS.fn.canvasPaint();
		pJS.fn.particlesCreate();
		pJS.fn.vendors.densityAutoParticles();

	  };


	  pJS.fn.vendors.start = function(){

		if(isInArray('image', pJS.particles.shape.type)){
		  pJS.tmp.img_type = 'svg';
		  pJS.fn.vendors.loadImg(pJS.tmp.img_type, pJS.particles.shape.image.src);
		}else{
		  pJS.fn.vendors.checkBeforeDraw();
		}

	  };


	  /* ---------- pJS - start ------------ */
	  
	  pJS.cachedSvg = [];
	  var currentCached = 0,
		  totalToCache;
		  
	  function cacheTheSvg(svgXml, theFill, theStroke, size) {
		  
		/* set color to svg element */
		var rgbHex = /#([0-9A-F]{3,6})/gi,
			coloredSvgXml = svgXml.replace(rgbHex, theFill).replace('{{stroke-color}}', theStroke);

		/* create particle img obj */
		var img = new Image(),
			url = 'data:image/svg+xml;base64,' + btoa(coloredSvgXml),
			canvas = document.createElement('canvas'),
			ctx = canvas.getContext('2d');
		
		canvas.width = canvas.height = size;
		img.addEventListener('load', function(){
		
		  // ctx.mozImageSmoothingEnabled = false;
		  ctx.webkitImageSmoothingEnabled = false;
		  ctx.msImageSmoothingEnabled = false;
		  ctx.imageSmoothingEnabled = false;
		  ctx.drawImage(this, 0, 0, size, size);
		  pJS.cachedSvg[pJS.cachedSvg.length] = ctx.canvas;
		  
		  currentCached++;
		  if(currentCached === totalToCache) startItUp();
		  
		});

		img.src = url;
		  
	  }
	  
	  function startItUp() {
		  
		pJS.fn.vendors.eventsListeners();
		pJS.fn.vendors.start();
		  
	  }
	  
	  if(pJS.particles.shape.type === 'image') {
		  
		  var fills = pJS.particles.color.value,
			  strokes = pJS.particles.shape.stroke.color,
			  shapes = pJS.particles.shape.image.src,
			  len1 = shapes.length,
			  len2 = fills.length,
			  len3 = strokes.length;
			  
		  totalToCache = len1 * len2 * len3;
		  
		  for(var i = 0, j, k; i < len1; i++) {
			  for(j = 0; j < len2; j++) {
				  for(k = 0; k < len3; k++) {
					cacheTheSvg(shapes[i], fills[j], strokes[k], pJS.particles.size.drawSize);
				  }
			  }
		  }
	  }
	  else {
		 startItUp();
	  }
	  
	  

	};

	/* ---------- global functions - vendors ------------ */

	function hexToRgb(hex){
	  // By Tim Down - http://stackoverflow.com/a/5624139/3493650
	  // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
	  var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
	  hex = hex.replace(shorthandRegex, function(m, r, g, b) {
		 return r + r + g + g + b + b;
	  });
	  var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
	  return result ? {
		  r: parseInt(result[1], 16),
		  g: parseInt(result[2], 16),
		  b: parseInt(result[3], 16)
	  } : null;
	}

	function clamp(number, min, max) {
	  return Math.min(Math.max(number, min), max);
	}

	function isInArray(value, array) {
	  return array.indexOf(value) > -1;
	}


	/* ---------- particles.js functions - start ------------ */
	function particlesJSRs(slide, data, ids, slider, zIndex){

	  /* create canvas element */
	  var canvas_el = document.createElement('canvas');
	  canvas_el.className = 'rs-particles-canvas';
	  canvas_el.style.zIndex = zIndex;
	  canvas_el.id = ids;
	  
	  var container = !data.carousel ? slider : slide;
	  slide.append(jQuery(canvas_el));
	  
	  return {instance: new pJS(canvas_el, data, container, slider), el: jQuery(canvas_el)};


	}


})(); /* END CLOSURE */