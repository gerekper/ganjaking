/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2019 ThemePunch
 * @version   2.1.0
 */

;(function() {

	window.RsRevealerAddOn = function($, slider, spinner) {
		
		if(!$) return;
		
		var opt = $.fn.revolution && $.fn.revolution[slider[0].id] ? $.fn.revolution[slider[0].id] : false;
		if(!opt) return;
		
		var options;
			
		if(!window.hasOwnProperty('RsAddonRevealerCustom'))  options = opt.revealer;
					
		else {

			options = window.RsAddonRevealerCustom;
			var hash = document.URL.split('?');
			if(hash.length === 2 && window.RsAddonRevealerCustom.hasOwnProperty(hash[1]) && hash[1] !== 'itm_1') {
				
				options = window.RsAddonRevealerCustom[hash[1]];
				//if(options.hasOwnProperty('spinner')) spinner = options.spinnerHTML;
				
			}
			else {
				
				options = opt.revealer;
				
			}
			
		}
		
		var direction = options.direction,
			delay = options.delay,
			preloader,
			finished,
			timer;
			
		/*if(options.spinner !== 'default') {
		
			if(!isFalse(opt.spinner)) window.requestAnimationFrame(checkSpinner);							
			else {				
				opt.spinner = 'on';
				setSpinner();				
			}		
		}*/
		delay = Math.max(200,delay);		
		
		if(direction === 'none') {
				
			slider.one('revolution.slide.onloaded', function() {
				
				if(preloader && preloader.length) opt.loader = preloader;
				
			});	
			
			return;
			
		}
		
		slider.addClass('rs_addon_reveal').find('rs-slide').first().attr('data-firstanim', 't:notransition;s:300;sl:0').data('firstanim', 't:notransition;s:300;sl:0');
		
		var wrap = $('<div class="rs_addon_revealer" />'),
			opens = direction.search('open') !== -1,
			corner = direction.search('corner') !== -1,
			ease = options.easing;
		
		ease = ease===undefined ? 'power3.inOut' : ease;

		var special = opt.sliderLayout === 'fullwidth' && direction.search('skew') !== -1,
			optionsOne = {ease: ease, onComplete: onFinish},
			optionsTwo = {ease: ease},
			calcNeeded = /skew|shrink/.test(direction),
			duration = options.duration,
			color = options.color,
			callback = onReveal,
			sideOne = '',
			sideTwo = '',
			delayStart,
			overlay,
			hasClip,
			abort,
			tw;
				
		if(isNaN(duration)) duration = '300';
		duration = parseInt(duration, 10) * 0.001;
		
		if(isNaN(delay)) delay = 0;
		delay = parseInt(delay, 10);
		
		if(!corner) {
			sideOne = '<div style="background: ' + color + '; ';
			if(opens) sideTwo = '<div style="background: ' + color + '; ';
		}
		else {
			
			var defs;
			color = processColor(color);
			
			if(!color[1]) {				
				color = color[0];
				defs = '';				
			}
			else {
				var gradient = drawFill(color);
				color = gradient[0];
				defs = gradient[1];				
			}
			
			sideOne = '<svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="none">' + defs;
			
		}
				
		if(!calcNeeded) {			
			if(delay) {				
				//opt.waitForInit = true;
				slider.height('100%');				
			}
			onReady();
			
		} 
		else {			
			window.addEventListener('resize', onResize);			
			if(!special) slider.one('revolution.slide.onloaded', onReady);	
			else slider.addClass('rs_addon_revealer_special').one('revolution.slide.onafterswap', onReady);			
			
		}
		
		function onReady() {
			
			if(abort) return;
			
			var skew;
			switch(direction) {
				
				case 'open_horizontal':
				
					sideOne += 'width: 50%; height: 100%; top: 0; left: 0';
					sideTwo += 'width: 50%; height: 100%; top: 0; left: 50%';
					
					optionsOne.width = '0%';
					optionsTwo.left = '100%';
					
				break;
				
				case 'open_vertical':
				
					sideOne += 'width: 100%; height: 50%; top: 0; left: 0';
					sideTwo += 'width: 100%; height: 50%; top: 50%; left: 0';
					
					optionsOne.height = '0%';
					optionsTwo.top = '100%';
					
				break;
				
				case 'split_left_corner':

					sideOne += '<polygon class="rs_addon_point1" points="0,0 500,0 500,500" style="fill:' + color + '; stroke:' + color + '; stroke-width: 1" />' + 
							   '<polygon class="rs_addon_point2" points="0,0 0,500 500,500" style="fill:' + color + '; stroke:' + color + '; stroke-width: 1" />';
							   
					callback = onSvg;
					optionsOne.x = 500;
					optionsTwo.x = -500;
				
				break;
				
				case 'split_right_corner':
					
					sideOne += '<polygon class="rs_addon_point1" points="0,0 500,0 0,500" style="fill:' + color + '; stroke:' + color + '; stroke-width: 1" />' + 
							   '<polygon class="rs_addon_point2" points="500,0 500,500 0,500" style="fill:' + color + '; stroke:' + color + '; stroke-width: 1" />';
							   
					callback = onSvg;
					optionsOne.x = -500;
					optionsTwo.x = 500;
					
				break;
				
				case 'shrink_circle':
					
					var size = (Math.max(slider.width(), slider.height())) * 2;
					sideOne += 'width: ' + size + 'px; height: ' + size + 'px; top: 50%; left: 50%; transform: translate(-50%, -50%); border-radius: 50%';
					
					optionsOne.width = '0';
					optionsOne.height = '0';
					
				break;
				
				case 'expand_circle':
					
					hasClip = true;
					callback = animateClip;
					slider.css('clip-path', 'circle(0% at 50% 50%)');
				
				break;
				
				case 'left_to_right':
				
					sideOne += 'width: 100%; height: 100%; top: 0; left: 0';
					optionsOne.left = '100%';
				
				break;
				
				case 'right_to_left':
				
					sideOne += 'width: 100%; height: 100%; top: 0; left: 0';
					optionsOne.width = '0%';
				
				break;
				
				case 'top_to_bottom':
				
					sideOne += 'width: 100%; height: 100%; top: 0; left: 0';
					optionsOne.top = '100%';
				
				break;
				
				case 'bottom_to_top':
				
					sideOne += 'width: 100%; height: 100%; top: 0; left: 0';
					optionsOne.height = '0%';
				
				break;
				
				case 'tlbr_skew':
					
					skew = Math.atan2(slider.width(), slider.height());
					sideOne += 'width: 200%; height: 200%; top: 0%; left: -100%; transform: skew(-' + skew + 'rad)';
					optionsOne.left = '100%';
					
				break;
				
				case 'trbl_skew':
				
					skew = Math.atan2(slider.width(), slider.height());
					sideOne += 'width: 200%; height: 200%; top: 0%; right: -100%; transform: skew(' + skew + 'rad)';
					optionsOne.right = '100%';
				
				break;
				
				case 'bltr_skew':
				
					skew = Math.atan2(slider.width(), slider.height());
					sideOne += 'width: 200%; height: 200%; bottom: -100%; left: 0%; transform: skew(' + skew + 'rad)';
					optionsOne.bottom = '100%';
				
				break;
				
				case 'brtl_skew':
				
					skew = Math.atan2(slider.width(), slider.height());
					sideOne += 'width: 200%; height: 200%; bottom: -100%; right: 0; transform: skew(-' + skew + 'rad)';
					optionsOne.bottom = '100%';
				
				break;
				
			}
			
			if(options.overlay_enabled) overlay = $('<div class="rsaddon-revealer-overlay" style="background: ' + options.overlay_color + '" />').appendTo(wrap);
			
			sideOne += !corner ? '" />' : '</svg>';
			sideOne = $(sideOne).appendTo(wrap);
			
			if(hasClip && !slider.css('clip-path')) return;
			if(opens) sideTwo = $(sideTwo + '" />').appendTo(wrap);
			
			wrap.appendTo(slider);
						
			
			if(!special) slider.one('revolution.slide.onafterswap',onStart);
			if(preloader && preloader.length) opt.loader = preloader;

			//Wait for InitEnded before Slider can Start ! Need a Start Trigger, other way it will wait endless
			if (opt.initEnded!==true) {				
				slider.one('revolution.slide.waitingforinit',function() {										
					onStart();					
				});
			} else {				
				if(special) {				
					slider.removeClass('rs_addon_revealer_special');
					onStart();					
				}
			}
			
		}
		
		function onStart() {			
			if(abort) return;
			if(isFalse(opt.stopLoop)) slider.revpause();
			if(!preloader || !preloader.length) preloader = slider.find('rs-loader');
			if(preloader.length) {				
				opt.loader = preloader;				
				var obj = {opacity: 0, ease: 'power3.inOut', onComplete: callback};
				if(delay) obj.delay = delay * 0.001;				
				tpGS.gsap.to(preloader, 0.3, obj);				
			}
			else if(delay) timer = setTimeout(callback, delay); else callback();			
		}
			
		function animateClip() {
			
			if(abort) return;
			if(overlay) animateOverlay();
			
			optionsOne.point = 100;
				
			var start = {point: 0};
			optionsOne.onUpdate = function() {	slider.css('clip-path', 'circle(' + start.point + '% at 50% 50%)');}
			
			tw = tpGS.gsap.to(start, duration, optionsOne);
						
			
		}
		
		function onSvg() {
			
			if(abort) return;
			if(overlay) animateOverlay();
			
			tpGS.gsap.to(wrap.find('.rs_addon_point1'), duration, optionsOne);
			tpGS.gsap.to(wrap.find('.rs_addon_point2'), duration, optionsTwo);
			
		}
		
		function onReveal() {
			
			if(abort) return;
			if(overlay) animateOverlay();
			//if (opt.sliderisrunning===undefined) jQuery(slider).revstart();
			
			tpGS.gsap.to(sideOne, duration, optionsOne);
			if(opens) tpGS.gsap.to(sideTwo, duration, optionsTwo);
			
		}
		
		function animateOverlay() {
			
			var dur = options.overlay_duration,
				easing = options.overlay_easing,
				del = options.overlay_delay;
			easing = easing===undefined ? "power3.inOut" : easing;

			if(isNaN(del)) del = 0;
			del = parseInt(del, 10) * 0.001;
			
			if(isNaN(dur)) dur = '300';
			dur = parseInt(dur, 10) * 0.001;
			
			tpGS.gsap.to(overlay, dur, {opacity: 0, ease: easing, delay: del, onComplete: onFinish});
			
		}
		
		function complete() {
			
			slider.removeClass('rs_addon_reveal rs_addon_revealer_special');
			slider.find('rs-loader').css('opacity', 1);
			
			if(wrap) wrap.remove();
			if(isFalse(opt.stopLoop)) slider.revresume();
			
		}
		
		function onFinish() {
			
			if(!overlay || finished) complete();
			finished = true;
			
		}
		
		function onResize() {
			
			window.removeEventListener('resize', onResize);
			clearTimeout(timer);
			abort = true;
			
			slider.off('revolution.slide.onloaded', onReady).off('revolution.slide.onafterswap', onStart);
			tpGS.gsap.killTweensOf($('.rs_addon_revealer').find('*'));
			
			if(tw) {
				tw.eventCallback('onUpdate', null);
				tw.kill();
				tw = null;
			}
			
			complete();
			
		}
		
		/*function checkSpinner() {
			
			var preloader = slider.find('rs-loader');
			if(preloader.length) {				
				preloader.remove();
				setSpinner();				
			}
			else {				
				window.requestAnimationFrame(checkSpinner);				
			}
			
		}
		
		function setSpinner() {
			
			preloader = $('<rs-loader />').appendTo(slider);
			preloader.html(spinner.replace(/{{color}}/g, options.spinnerColor));
			opt.loader = preloader;
			
		}*/
		
	};
	
	function isFalse(val) {
			
		return typeof val === undefined || val === false || val === 0 || val === '0' || val === 'false' || val === 'off' || false;
	
	}
	
	function radialGradient(colors) {
		
		var len = colors.length,
			gradient,
			color;
			
		var id = 'rsaddonrevealer' + Math.floor(Math.random() * 10000),
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
			
		var id = 'rsaddonrevealer' + Math.floor(Math.random() * 10000),
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
	
})();


