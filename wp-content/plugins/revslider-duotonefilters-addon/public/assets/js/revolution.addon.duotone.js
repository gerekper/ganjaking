;var RsAddonDuotone = function($, api, simplified, easing, timing) {
	
	if(!$ || typeof api === 'undefined' || !api.length) return;
	var supports = "CSS" in window && "supports" in window.CSS;
	
	if(supports) {
		
		var blends = ["luminosity", "hue", "darken", "lighten", "hard-light", "soft-light", "color-dodge", "color", "screen"],
			len = blends.length;
			
		for(var i = 0; i < len; i++) {
			
			if(!window.CSS.supports("mix-blend-mode", blends[i])) {	
				supports = false;
				break;
				
			}
		}
		
	}
	
	if(supports) {
		
		var duotones = api.find('rs-slide[data-duotonefilter]');
		if(duotones.length) {
			
			api.addClass('duotone_active');
			if(simplified) {

				if(!isNaN(timing)) timing = parseInt(timing, 10);
				else timing = 750;
				timing = Math.max(100, Math.min(5000, timing));
				
				duotones.each(function() {
					this.setAttribute('data-anim', 'ei:Linear.easeInOut;eo:Linear.easeInOut;s:' + timing + 'ms;r:0;t:fade;sl:d;');
				});
				
				if(!easing) easing = 'ease-in';
				timing *= 0.001;
				
				api.addClass('rs-duotone-simplified');
				$('<style type="text/css">').html('#' + api[0].id + '.rs-duotone-simplified rs-slide {transition: opacity ' + timing + 's ' + easing + '}').appendTo(jQuery('head'));
				
			}
			
			api.one('revolution.slide.onloaded', function() {
				
				duotones.each(function() {
					
					var $this = tpj(this),
					slotholder = $this.find('rs-sbg-wrap');
					slotholder.wrap('<div data-duotone="' + $this.attr("data-duotonefilter") + '" />');
					
				});
				
			});
			
			if(simplified) {
				
				api.on('revolution.slide.onbeforeswap', function(e, data) {
					
					$('.rs-duotone-slide').removeClass('rs-duotone-slide');
					data.currentslide.addClass('rs-duotone-slide');
					
				}).on('revolution.slide.onafterswap', function(e, data) {
				   
					$('.rs-duotone-slide').removeClass('rs-duotone-slide');
					
				});
				
			}
			
		}
		
	}
	
};