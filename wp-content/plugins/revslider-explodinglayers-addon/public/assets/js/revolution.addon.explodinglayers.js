/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2019 ThemePunch
*/

;(function() {
	
	// "use strict";
	if(typeof jQuery === 'undefined') return;
	
	function getParam(st, prop) {
		
		if(!st) return false;
		
		st = st.split(prop + ':');
		if(st.length < 2) st = false;
		else st = st[1].split(';')[0];
		
		return st;
	
	}
	
	function setAttributes() {
		if(this.className && this.className.search('revaddonexplayer') !== -1) return;
		var $this = jQuery(this);
		var frameStart = $this.attr('data-frame_1'),
			frameStart_st = getParam(frameStart, 'st'),
			frameStart_sr = getParam(frameStart, 'sR');
			frameStart_sp = getParam(frameStart, 'sp');

		var frameEnd = $this.attr('data-frame_999'),
			frameEnd_st = getParam(frameEnd, 'st'),
			frameEnd_sr = getParam(frameEnd, 'sR'),
			frameEnd_sp = getParam(frameEnd, 'sp');

		frameStart = 'e:Power2.easeOut';
		if(frameStart_st !== false) frameStart += ';st:' + frameStart_st;
		if(frameStart_sr !== false) frameStart += ';sR:' + frameStart_sr;
		if(frameStart_sp !== false) frameStart += ';sp:' + frameStart_sp;
		
		frameEnd = 'o:1;e:Power2.easeOut';
		if(frameEnd_st !== false) frameEnd += ';st:' + frameEnd_st;
		if(frameEnd_sr !== false) frameEnd += ';sR:' + frameEnd_sr;
		if(frameEnd_sp !== false) frameEnd += ';sp:' + frameEnd_sp;
		
		if($this.attr('data-explodinglayersin')) {
			$this.addClass('revaddonexplayerhide');		
			$this.addClass('revaddonexplayer').attr({'data-frame_0': 'o:1', 'data-frame_1': frameStart});
		}
		
		if($this.attr('data-explodinglayersout')) {
			$this.addClass('revaddonexplayer').attr({'data-frame_999': frameEnd});
		}
		
	}
	
	jQuery('rs-layer[data-explodinglayersin], rs-layer[data-explodinglayersout], .rs-layer[data-explodinglayersin], .rs-layer[data-explodinglayersout]').each(function() {setAttributes.call(this);});
	
	var defaults = {
		
		color: '#000000',
		density: 1,
		direction: 'left',
		padding: 150,
		power: 2,
		randomsize: false,
		randomspeed: false,
		sync: false,
		size: 5,
		speed: 1,
		style: 'fill',
		type: 'circle',
		easing: 'Power3.easeInOut',
		duration: 1000
		
	};
	
	window.ExplodingLayersAddOn = function($, slider) {
		
		if(!$ || !slider) return;
		
		var levels,
			win = $(window),
			numerals = {'padding': 0, 'size': 1, 'speed': 0, 'density': 1, 'power': 0, 'duration': 300};
			
		slider.find('rs-layer[data-explodinglayersin], rs-layer[data-explodinglayersout], .rs-layer[data-explodinglayersin], .rs-layer[data-explodinglayersout]').each(function() {
			
			if(this.className && this.className.search('revaddonexplayer') === -1) setAttributes.call(this);
			
		});
		
		slider.on('revolution.slide.onloaded', function() {
			
			var opt = $.fn.revolution && $.fn.revolution[slider[0].id] ? $.fn.revolution[slider[0].id] : false;
			if(!opt) return;
			
			levels = opt.responsiveLevels;
			if(levels) {
				
				if(!Array.isArray(levels)) levels = [levels];
				while(levels.length < 4) levels[levels.length] = levels[levels.length - 1];
				for(var i = 0; i < 4; i++) levels[i] = parseInt(levels[i], 10);
				
			}
			
			slider.find('rs-layer[data-explodinglayersin], .rs-layer[data-explodinglayersin]').each(function() {
				
				var options = this.getAttribute('data-explodinglayersin');
				if(!options) return;
					
				options = JSON.parse(options);
				if(options) setOptions.apply(this, ['in', jQuery.extend(defaults, options)]);
				
			});
			
			slider.find('rs-layer[data-explodinglayersout], .rs-layer[data-explodinglayersout]').each(function() {
				
				var options = this.getAttribute('data-explodinglayersout');
				if(!options) return;
				
				options = JSON.parse(options);
				if(options) setOptions.apply(this, ['out', jQuery.extend(defaults, options)]);
				
			});
			
			slider.on('revolution.slide.afterdraw', onResize);
			
		}).on('revolution.slide.onbeforeswap revolution.slide.onafterswap', function(e, data) {
			
			if(e.namespace.search('before') !== -1) {
				if(data.nextslide && data.nextslide.length) resetEffect(data.nextslide);
			}
			else {
				if(data.prevslide && data.prevslide.length) resetEffect(data.prevslide);
			}
			
		}).on('revolution.slide.layeraction', function(e, data) {
					
			var explode,
				animation,
				effect = data.layer.data('revaddonexpeffect'),
				isStatic = data.layer.hasClass('rs-static-layers'),
				isSpecial = isStatic && !data.layer.hasClass('revaddonexpstatic');
				
			if(!effect) return;
			
			if(data.eventtype === 'enterstage' || isSpecial) {

				if(isStatic) data.layer.addClass('revaddonexpstatic');
				animation = data.layer.data('revaddonexplayerin');
				explode = false;
					
			}
			else if(data.eventtype === 'leavestage') {
				
				animation = data.layer.data('revaddonexplayerout');
				explode = true;

			}
			
			if(animation) {
				
				animation.options = $.extend({}, animation.orig);
				effect.o = animation.options;
				processOptions(animation.orig, animation.options, effect, explode);
				
			}
			
		});
		
		function onResizeReset() {
			
			resetEffect($(this), true);
			
		}
		
		function onResize() {
				
			slider.find('rs-slide').each(onResizeReset);
				
		}
		
		function resetEffect(slide, resize) {
			
			var currentSlide = slider.revcurrentslide();
			if(isNaN(currentSlide)) currentSlide = 1;
			else currentSlide = parseInt(currentSlide, 10);
			
			slide.find('rs-layer[data-explodinglayersin], rs-layer[data-explodinglayersout], .rs-layer[data-explodinglayersin], .rs-layer[data-explodinglayersout]').each(function() {
				
				var $this = $(this),
					effect = $this.data('revaddonexpeffect');
				
				if(!effect) return;
				
				var index = parseInt(slide.attr('data-originalindex'), 10),
					toResize = resize && currentSlide === index;
					
				if(toResize && effect.el && effect.rect) {
				
					var rect = effect.el.getBoundingClientRect();
					if(Math.floor(effect.rect.width) === Math.floor(rect.width) && Math.floor(effect.rect.height) === Math.floor(rect.height)) return;
				
				}
				
				var method = !toResize ? 'addClass' : effect.disintegrating ? 'addClass' : 'removeClass';
				effect.reset(toResize);
				if($this.attr('data-explodinglayersin')) $this[method]('revaddonexplayerhide');
				
			});
			
		}
		
		function setOptions(direction, options) {
			
			var val;
			for(var prop in options) {
				
				if(!options.hasOwnProperty(prop)) continue;
				
				val = options[prop];
				if(!Array.isArray(val)) options[prop] = [val, val, val, val];
				
				val = options[prop][0];
				while(options[prop].length < 4) options[prop][options[prop].length - 1] = val;
									
			}
			
			var $this = $(this);
			options.effect = $this.data('revaddonexpeffect') || new RevAddonExpBtn(this, options);

			var obj = {revaddonexpeffect: options.effect},
				orig = $.extend({}, options);
				
			obj['revaddonexplayer' + direction] = {orig: orig, options: options, direction: direction};
			$this.data(obj);
			
		}
		
		function checkValue(prop, value) {

			if(numerals.hasOwnProperty(prop)) {
			
				value = Math.max(parseFloat(value), numerals[prop]);
			
			}
			else if(prop === 'easing') {
				if (tpGS===undefined) {
					var val = value.split('.');
					if(val.length === 2) value = punchgs[val[0]][val[1]];
					else value = punchgs.hasOwnProperty(val[0]) ? punchgs[val[0]] : punchgs.Power3.easeInOut;
				}				
			}
			
			return value;
			
		}
		
		function getValue(prop, val, level) {
		
			if(!val) return false;
			if(level === 0) return checkValue(prop, val[level]);
			
			var minus = level,
				value = val[level];
				
			while(value === 'inherit') {
				
				minus--;
				if(minus > -1) value = val[minus];
				else value = val[0];
				
			}
			
			return checkValue(prop, value);
			
		}
		
		function checkRandom(tpe, options, val) {
			
			if(isTrue(options['random' + tpe])) {
				
				var min = Math.max(Math.round(val * 0.5), 1),
					max = Math.round(val * 2);
					
				options[tpe] = function() {
					
					return Math.floor(Math.random() * max) + min;
					
				};
				
			}
			
		}
		
		function processOptions(orig, options, effect, explode) {
			
			var prev,
				levl,
				level = 0,
				width = win.width();
				
			if(levels) {
				
				var len = levels.length;
				for(var i = 0; i < len; i++) {
					
					levl = levels[i];
					if(prev === levl) continue;
					if(width < levl) level = i;
					prev = levl;
					
				}
				
			}
			
			for(var prop in orig) {
				
				if(!orig.hasOwnProperty(prop) || prop === 'effect') continue;
				options[prop] = getValue(prop, options[prop], level);
				
			}
			
			checkRandom('size', options, options.size);
			checkRandom('speed', options, options.speed);
			options.sync = isTrue(options.sync);
			
			var color = processColor(options.color),	
				fill,
				defs;
			
			if(!color[1]) {
				
				fill = color[0];
				defs = '';
				
			}
			else {

				var gradient = drawFill(color);
				fill = gradient[0];
				defs = gradient[1];
				
			}
			
			var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">' + defs,
				tagStart,
				tagEnd;
				
			if(options.type !== 'circle') {
				
				options.type = checkSvg(options.type);
				tagStart = '<path ';
				tagEnd = '></path>';
				
			}
			else {
				
				tagStart = '<circle cx="12" cy="12" r="12" ';
				tagEnd = ' />';
				
			}

			if(options.style === 'fill') {
				svg += tagStart + 'fill="' + fill + '" d="' + options.type + '"' + tagEnd;
			}
			else {
				svg += tagStart + 'fill="transparent" d="' + options.type + '" stroke="' + fill + '" stroke-width="1"' + tagEnd;
			}
			
			svg += '</svg>';
			
			var img = new Image(),
				url = 'data:image/svg+xml;base64,' + btoa(svg),
				canvas = document.createElement('canvas'),
				ctx = canvas.getContext('2d');
			
			canvas.width = canvas.height = 24;
			img.onload = function() {
				
				ctx.drawImage(this, 0, 0);
				options.tpe = ctx.canvas;
				effect.run(explode);
				
			};
			
			img.src = url;
			
		}
		
	};
	
	function checkSvg(svg) {
		
		svg = jQuery.trim(svg);
		switch(svg) {
		
			case 'rectangle': return 'M4 4h16v16H4z';
			case 'triangle': return 'M12 4L4 20L20 20z';
			case 'polygon': return 'M5 4 L17 4 L22 12 L17 20 L8 20 L3 12 L8 4 Z';
			case 'star': return 'M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z';
			case 'heart_1': return 'M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z';		
			case 'star_2': return 'M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm4.24 16L12 15.45 7.77 18l1.12-4.81-3.73-3.23 4.92-.42L12 5l1.92 4.53 4.92.42-3.73 3.23L16.23 18z';			
			case 'settings': return 'M19.43 12.98c.04-.32.07-.64.07-.98s-.03-.66-.07-.98l2.11-1.65c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.3-.61-.22l-2.49 1c-.52-.4-1.08-.73-1.69-.98l-.38-2.65C14.46 2.18 14.25 2 14 2h-4c-.25 0-.46.18-.49.42l-.38 2.65c-.61.25-1.17.59-1.69.98l-2.49-1c-.23-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49.12.64l2.11 1.65c-.04.32-.07.65-.07.98s.03.66.07.98l-2.11 1.65c-.19.15-.24.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1c.52.4 1.08.73 1.69.98l.38 2.65c.03.24.24.42.49.42h4c.25 0 .46-.18.49-.42l.38-2.65c.61-.25 1.17-.59 1.69-.98l2.49 1c.23.09.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.65zM12 15.5c-1.93 0-3.5-1.57-3.5-3.5s1.57-3.5 3.5-3.5 3.5 1.57 3.5 3.5-1.57 3.5-3.5 3.5z';			
			case 'arrow_1': return 'M4 18l8.5-6L4 6v12zm9-12v12l8.5-6L13 6z';
			case 'bullseye': return 'M12 2C6.49 2 2 6.49 2 12s4.49 10 10 10 10-4.49 10-10S17.51 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3-8c0 1.66-1.34 3-3 3s-3-1.34-3-3 1.34-3 3-3 3 1.34 3 3z';
			case 'plus_1': return 'M13 7h-2v4H7v2h4v4h2v-4h4v-2h-4V7zm-1-5C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z';
			case 'triangle_2': return 'M12 7.77L18.39 18H5.61L12 7.77M12 4L2 20h20L12 4z';
			case 'smilie': return 'M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z';
			case 'star_3': return 'M22 9.24l-7.19-.62L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21 12 17.27 18.18 21l-1.63-7.03L22 9.24zM12 15.4l-3.76 2.27 1-4.28-3.32-2.88 4.38-.38L12 6.1l1.71 4.04 4.38.38-3.32 2.88 1 4.28L12 15.4z';
			case 'heart_2': return 'M16.5 3c-1.74 0-3.41.81-4.5 2.09C10.91 3.81 9.24 3 7.5 3 4.42 3 2 5.42 2 8.5c0 3.78 3.4 6.86 8.55 11.54L12 21.35l1.45-1.32C18.6 15.36 22 12.28 22 8.5 22 5.42 19.58 3 16.5 3zm-4.4 15.55l-.1.1-.1-.1C7.14 14.24 4 11.39 4 8.5 4 6.5 5.5 5 7.5 5c1.54 0 3.04.99 3.57 2.36h1.87C13.46 5.99 14.96 5 16.5 5c2 0 3.5 1.5 3.5 3.5 0 2.89-3.14 5.74-7.9 10.05z';
			case 'plus_2': return 'M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z';
			case 'close': return 'M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z';
			case 'arrow_2': return 'M22 12l-4-4v3H3v2h15v3z';
			case 'dollar': return 'M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z';
			case 'sun_1': return 'M6.76 4.84l-1.8-1.79-1.41 1.41 1.79 1.79 1.42-1.41zM4 10.5H1v2h3v-2zm9-9.95h-2V3.5h2V.55zm7.45 3.91l-1.41-1.41-1.79 1.79 1.41 1.41 1.79-1.79zm-3.21 13.7l1.79 1.8 1.41-1.41-1.8-1.79-1.4 1.4zM20 10.5v2h3v-2h-3zm-8-5c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm-1 16.95h2V19.5h-2v2.95zm-7.45-3.91l1.41 1.41 1.79-1.8-1.41-1.41-1.79 1.8z';
			case 'sun_2': return 'M7 11H1v2h6v-2zm2.17-3.24L7.05 5.64 5.64 7.05l2.12 2.12 1.41-1.41zM13 1h-2v6h2V1zm5.36 6.05l-1.41-1.41-2.12 2.12 1.41 1.41 2.12-2.12zM17 11v2h6v-2h-6zm-5-2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3zm2.83 7.24l2.12 2.12 1.41-1.41-2.12-2.12-1.41 1.41zm-9.19.71l1.41 1.41 2.12-2.12-1.41-1.41-2.12 2.12zM11 23h2v-6h-2v6z';
			case 'snowflake': return 'M22 11h-4.17l3.24-3.24-1.41-1.42L15 11h-2V9l4.66-4.66-1.42-1.41L13 6.17V2h-2v4.17L7.76 2.93 6.34 4.34 11 9v2H9L4.34 6.34 2.93 7.76 6.17 11H2v2h4.17l-3.24 3.24 1.41 1.42L9 13h2v2l-4.66 4.66 1.42 1.41L11 17.83V22h2v-4.17l3.24 3.24 1.42-1.41L13 15v-2h2l4.66 4.66 1.41-1.42L17.83 13H22z';
			case 'party': return 'M4.59 6.89c.7-.71 1.4-1.35 1.71-1.22.5.2 0 1.03-.3 1.52-.25.42-2.86 3.89-2.86 6.31 0 1.28.48 2.34 1.34 2.98.75.56 1.74.73 2.64.46 1.07-.31 1.95-1.4 3.06-2.77 1.21-1.49 2.83-3.44 4.08-3.44 1.63 0 1.65 1.01 1.76 1.79-3.78.64-5.38 3.67-5.38 5.37 0 1.7 1.44 3.09 3.21 3.09 1.63 0 4.29-1.33 4.69-6.1H21v-2.5h-2.47c-.15-1.65-1.09-4.2-4.03-4.2-2.25 0-4.18 1.91-4.94 2.84-.58.73-2.06 2.48-2.29 2.72-.25.3-.68.84-1.11.84-.45 0-.72-.83-.36-1.92.35-1.09 1.4-2.86 1.85-3.52.78-1.14 1.3-1.92 1.3-3.28C8.95 3.69 7.31 3 6.44 3 5.12 3 3.97 4 3.72 4.25c-.36.36-.66.66-.88.93l1.75 1.71zm9.29 11.66c-.31 0-.74-.26-.74-.72 0-.6.73-2.2 2.87-2.76-.3 2.69-1.43 3.48-2.13 3.48z';
			case 'flower_1': return 'M18.7 12.4c-.28-.16-.57-.29-.86-.4.29-.11.58-.24.86-.4 1.92-1.11 2.99-3.12 3-5.19-1.79-1.03-4.07-1.11-6 0-.28.16-.54.35-.78.54.05-.31.08-.63.08-.95 0-2.22-1.21-4.15-3-5.19C10.21 1.85 9 3.78 9 6c0 .32.03.64.08.95-.24-.2-.5-.39-.78-.55-1.92-1.11-4.2-1.03-6 0 0 2.07 1.07 4.08 3 5.19.28.16.57.29.86.4-.29.11-.58.24-.86.4-1.92 1.11-2.99 3.12-3 5.19 1.79 1.03 4.07 1.11 6 0 .28-.16.54-.35.78-.54-.05.32-.08.64-.08.96 0 2.22 1.21 4.15 3 5.19 1.79-1.04 3-2.97 3-5.19 0-.32-.03-.64-.08-.95.24.2.5.38.78.54 1.92 1.11 4.2 1.03 6 0-.01-2.07-1.08-4.08-3-5.19zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4z';
			case 'flower_2': return 'M12 22c4.97 0 9-4.03 9-9-4.97 0-9 4.03-9 9zM5.6 10.25c0 1.38 1.12 2.5 2.5 2.5.53 0 1.01-.16 1.42-.44l-.02.19c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5l-.02-.19c.4.28.89.44 1.42.44 1.38 0 2.5-1.12 2.5-2.5 0-1-.59-1.85-1.43-2.25.84-.4 1.43-1.25 1.43-2.25 0-1.38-1.12-2.5-2.5-2.5-.53 0-1.01.16-1.42.44l.02-.19C14.5 2.12 13.38 1 12 1S9.5 2.12 9.5 3.5l.02.19c-.4-.28-.89-.44-1.42-.44-1.38 0-2.5 1.12-2.5 2.5 0 1 .59 1.85 1.43 2.25-.84.4-1.43 1.25-1.43 2.25zM12 5.5c1.38 0 2.5 1.12 2.5 2.5s-1.12 2.5-2.5 2.5S9.5 9.38 9.5 8s1.12-2.5 2.5-2.5zM3 13c0 4.97 4.03 9 9 9 0-4.97-4.03-9-9-9z';
			case 'fire': return 'M13.5.67s.74 2.65.74 4.8c0 2.06-1.35 3.73-3.41 3.73-2.07 0-3.63-1.67-3.63-3.73l.03-.36C5.21 7.51 4 10.62 4 14c0 4.42 3.58 8 8 8s8-3.58 8-8C20 8.61 17.41 3.8 13.5.67zM11.71 19c-1.78 0-3.22-1.4-3.22-3.14 0-1.62 1.05-2.76 2.81-3.12 1.77-.36 3.6-1.21 4.62-2.58.39 1.29.59 2.65.59 4.04 0 2.65-2.15 4.8-4.8 4.8z';
			case 'pizza': return 'M12 2C8.43 2 5.23 3.54 3.01 6L12 22l8.99-16C18.78 3.55 15.57 2 12 2zM7 7c0-1.1.9-2 2-2s2 .9 2 2-.9 2-2 2-2-.9-2-2zm5 8c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z';
			
		}
		
		return svg;
		
	}
	
	/*
		COLORS PROCESSING
	*/
	function sanitizeGradient(obj) {

		var colors = obj.colors,
			len = colors.length,
			ar = [],
			prev;
			

		for(var i = 0; i < len; i++) {
			
			var cur = colors[i];
			delete cur.align;
			
			if(prev) {
				if(JSON.stringify(cur) !== JSON.stringify(prev)) ar[ar.length] = cur;
			}
			else {
				ar[ar.length] = cur;
			}
			
			prev = cur;
			
		}
		
		obj.colors = ar;
		return obj;
		
	}
	
	function processColor(clr) {
		
		if(clr.trim() === 'transparent') {
			
			return ['#ffffff', false];
			
		}
		else if(clr.search(/\[\{/) !== -1) {

			try {
				clr = JSON.parse(clr.replace(/\&/g, '"'));
				clr = sanitizeGradient(clr); 
				return [clr, true];
			}
			catch(e) {	
				return ['#ffffff', false];
			}
			
		}
		else if(clr.search('#') !== -1) {
			return [clr, false];
		}
		else if(clr.search('rgba') !== -1) {
			return [clr.replace(/\s/g, '').replace(/false/g, '1'), false];
		}
		else if(clr.search('rgb') !== -1) {
			return [clr.replace(/\s/g, ''), false];
		}
		else {
			return /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(clr) ? [clr, false] : ['#ffffff', false];
		}
		
	}
	
	function isTrue(val) {
		
		return val === true || val === 'true' || val === 'on' || val === 1 || val === '1';
		
	}
	
	function radialGradient(colors) {
		
		var len = colors.length,
			gradient,
			color;
			
		var id = 'explodinglayers' + Math.floor(Math.random() * 10000),
			st = '<defs><radialGradient id="' + id + '">',
			pos;
		
		for(var i = 0; i < len; i++) {

			color = colors[i];
			pos = parseInt(color.position, 10);
			st += '<stop offset="' + pos + '%" style="stop-color: rgb(' + color.r + ',' + color.g + ',' + color.b + '); stop-opacity: ' + color.a + '" />';

		}
		
		st += '</radialGradient></defs>';
		gradient = ['url(#' + id + ')', st];
		
		return gradient;

	}
	
	function linearGradient(colors, angle) {

		angle = parseInt(angle, 10) / 180 * Math.PI;
		
		var segment = Math.floor(angle / Math.PI * 2) + 2,
			diagonal =  (1/2 * segment + 1/4) * Math.PI,
			op = Math.cos(Math.abs(diagonal - angle)) * Math.sqrt(2),
			x = op * Math.cos(angle),
			y = op * Math.sin(angle);

		var points = [x < 0 ? 1 : 0, y < 0 ? 1 : 0, x >= 0 ? x : x + 1, y >= 0 ? y : y + 1],
			len = colors.length,
			gradient,
			color,
			pos,
			i;
			
		var id = 'explodinglayers' + Math.floor(Math.random() * 10000),
			st = '<defs><linearGradient id="' + id + '" x1="' + points[0] + '" y1="' + points[1] + '" x2="' + points[2] + '" y2="' + points[3] + '">';
		
		for(i = 0; i < len; i++) {

			color = colors[i];
			pos = parseInt(color.position, 10);
			st += '<stop offset="' + pos + '%" style="stop-color: rgb(' + color.r + ',' + color.g + ',' + color.b + '); stop-opacity: ' + color.a + '" />';

		}
		
		st += '</linearGradient></defs>';
		gradient = ['url(#' + id + ')', st];
		
		return gradient;
	
	}
	
	function drawFill(color) {

		if(color[1]) {

			color = color[0];
			if(color.type === 'radial') return radialGradient(color.colors);
			else return linearGradient(color.colors, color.angle);

		}
		else {
			return color[0];
		}

	}
	  
	/* ******************** */
	/* begin particle magic */
	/* ******************** */
    function RevAddonExpBtn(el, options) {
		
        this.el = el;
        this.o = options;
        this.init();
		
    }

    RevAddonExpBtn.prototype = {
        init: function () {
            this.particles = [];
            this.frame = null;
            this.canvas = document.createElement('canvas');
            this.ctx = this.canvas.getContext('2d');
            this.canvas.className = 'revaddon-explayer-canvas';
            this.canvas.style = 'display:none;';
            this.wrapper = document.createElement('div');
            this.wrapper.className = 'revaddon-explayer-wrapper';
            this.el.parentNode.insertBefore(this.wrapper, this.el);
            this.wrapper.appendChild(this.el);
            this.parentWrapper = document.createElement('div');
            this.parentWrapper.className = 'revaddon-explayer';
            this.wrapper.parentNode.insertBefore(this.parentWrapper, this.wrapper);
            this.parentWrapper.appendChild(this.wrapper);
            this.parentWrapper.appendChild(this.canvas);
        },
        loop: function () {
			
			if(this.o.tpe) {
			
				this.updateRevAddonExpBtn();
				this.renderRevAddonExpBtn();
				if (this.isAnimating()) {
					this.frame = requestAnimationFrame(this.loop.bind(this));
				}
				
			}
			
        },
        updateRevAddonExpBtn: function () {
			
            var p;
            for (var i = 0; i < this.particles.length; i++) {
                p = this.particles[i];
                if (p.life > p.death) {
					
					if(this.total === false) this.total = this.particles.length;
                    this.particles.splice(i, 1);
					if(this.o.sync) this.updateTransform(this.particles.length);
					
                } else {
                    p.x += p.speed;
                    p.y = this.o.power * Math.sin(p.counter * p.increase);
                    p.life++;
                    p.counter += this.disintegrating ? 1 : -1;
                }
            }
            if (!this.particles.length) {
                this.pause();
                this.canvas.style.display = 'none';
            }
        },
        renderRevAddonExpBtn: function () {
            this.ctx.clearRect(0, 0, this.width, this.height);
            var p;
            for (var i = 0; i < this.particles.length; i++) {
                p = this.particles[i];
                if (p.life < p.death) {
                    this.ctx.translate(p.startX, p.startY);
                    this.ctx.rotate(p.angle * Math.PI / 180);
                    this.ctx.globalAlpha = this.disintegrating ? 1 - p.life / p.death : p.life / p.death;
					this.ctx.drawImage(this.o.tpe, Math.round(p.x), Math.round(p.y), Math.round(p.size), Math.round(p.size));
                    this.ctx.globalAlpha = 1;
                    this.ctx.rotate(-p.angle * Math.PI / 180);
                    this.ctx.translate(-p.startX, -p.startY);
                }
            }
        },
        play: function () {
            this.frame = requestAnimationFrame(this.loop.bind(this));
        },
        pause: function () {
            cancelAnimationFrame(this.frame);
			this.ctx.clearRect(0, 0, this.width, this.height);
            this.frame = null;
        },
        addParticle: function (options) {
            var frames = this.o.duration * 60 / 1000;
            var speed = is.fnc(this.o.speed) ? this.o.speed() : this.o.speed;
			
            this.particles.push({
                startX: options.x,
                startY: options.y,
                x: this.disintegrating ? 0 : speed * -frames,
                y: 0,
                angle: rand(360),
                counter: this.disintegrating ? 0 : frames,
                increase: Math.PI * 2 / 100,
                life: 0,
                death: this.disintegrating ? (frames - 20) + Math.random() * 40 : frames,
                speed: speed,
                size: is.fnc(this.o.size) ? this.o.size() : this.o.size
            });
        },
        addRevAddonExpBtn: function (rect, progress) {
            var progressDiff = this.disintegrating ? progress - this.lastProgress : this.lastProgress - progress;
            this.lastProgress = progress;
            var x = this.o.padding;
            var y = this.o.padding;
			
            var progressValue = (this.isHorizontal() ? rect.width : rect.height) * progress + progressDiff * (this.disintegrating ? 100 : this.o.duration);
            if (this.isHorizontal()) {
                x += this.o.direction === 'left' ? progressValue : rect.width - progressValue;
            } else {
                y += this.o.direction === 'top' ? progressValue : rect.height - progressValue;
            }
            var i = Math.floor(this.o.density * (progressDiff * 100 + 1));
            if (i > 0) {
                while (i--) {					
                    this.addParticle({
                        x: x + (this.isHorizontal() ? 0 : rect.width * Math.random()),
                        y: y + (this.isHorizontal() ? rect.height * Math.random() : 0)
                    });
                }
            }
            if (!this.isAnimating()) {
                this.canvas.style.display = 'block';
                this.play();
            }
        },
		
        addTransforms: function (value) {
			
            var translateProperty = this.isHorizontal() ? 'translateX' : 'translateY';
            var translateValue = this.o.direction === 'left' || this.o.direction === 'top' ? value : -value;
            this.wrapper.style[transformString] = translateProperty + '('+ translateValue +'%)';
            this.el.style[transformString] = translateProperty + '('+ -translateValue +'%)';
			
			if(!this.changed) {
				
				this.el.className = this.el.className.replace('revaddonexplayerhide', '');
				this.wrapper.style.visibility = 'visible';
				this.changed = true;
				
			}
			
        },
		
		updateTransform: function(num) {
			
			var value = (num / this.total) * 100;
			this.addTransforms(value);
			
		},

		update: function() {
			
			var value;
			if(this.disintegrating) {
				
				value = this.tween.value;
				this.addTransforms(value);
				
			}
			else {
				
				value = 100 - this.tween.value;
				if(!this.o.sync) {
				
					var _ = this;
					this.timers[this.timers.length] = setTimeout(function() {
						
						_.addTransforms(value);
						
					}, this.o.duration);
					
				}
				
			}
			
			this.addRevAddonExpBtn(this.rect, value / 100);
			
		},
		
		run: function(explode) {

			this.reset();
			this.disintegrating = explode;
			this.lastProgress = explode ? 0 : 1;
			this.rect = this.el.getBoundingClientRect();
			this.width = this.canvas.width = this.o.width || this.rect.width + this.o.padding * 2;
			this.height = this.canvas.height = this.o.height || this.rect.height + this.o.padding * 2;
			this.changed = false;
			this.timers = [];
			this.animate(this.update.bind(this));
			
		},

		setDisplay: function(resize) {
			
			this.canvas.style.display = 'none';
			this.wrapper.style.visibility = !resize ? 'hidden' : 'visible';
			this.wrapper.style[transformString] = 'none';
			this.el.style[transformString] = 'none';
			
		},
		reset: function(resize) {

			this.pause();
			this.particles = [];
			this.total = false;
			
			if(this.tween) {
				
				punchgs.TweenLite.killTweensOf(this.tween);
				delete this.tween;
				
			}
			if(this.timers) {
				
				while(this.timers.length) {
					
					clearTimeout(this.timers[0]);
					this.timers.shift();
					
				}
				
				delete this.timers;
				
			}
			
			if(!resize) {
				this.setDisplay();
			}
			else {
				var _ = this;
				requestAnimationFrame(function() {_.setDisplay(true);});
			}
			
		},
        animate: function (update) {

            var _ = this;
			this.tween = {value: 0};
			
			return punchgs.TweenLite.to(
			
				this.tween,
				this.o.duration * 0.001,
				{
					value: 100,
					ease: this.o.easing,
					onUpdate: update,
					onComplete: function() {
						if (_.disintegrating) {
							_.wrapper.style.visibility = 'hidden';
						}
					}
					
				}
			
			);
			
        },
        isAnimating: function () {
            return !!this.frame;
        },
        isHorizontal: function () {
            return this.o.direction === 'left' || this.o.direction === 'right';
        }
    };


    // Utils

    var is = {
        arr: function (a) { return Array.isArray(a); },
        str: function (a) { return typeof a === 'string'; },
        fnc: function (a) { return typeof a === 'function'; }
    };

    function stringToHyphens(str) {
        return str.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
    }

    function getCSSValue(el, prop) {
        if (prop in el.style) {
            return getComputedStyle(el).getPropertyValue(stringToHyphens(prop)) || '0';
        }
    }

    var t = 'transform';
    var transformString = (getCSSValue(document.body, t) ? t : '-webkit-' + t);

    function rand(value) {
        return Math.random() * value - value / 2;
    }

})();









