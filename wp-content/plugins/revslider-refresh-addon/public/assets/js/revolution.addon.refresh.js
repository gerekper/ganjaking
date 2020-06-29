/* 
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2019 ThemePunch
*/
;(function() {
	
	window.RsRefreshAddOn = function(slider, settings) {
		
		if(!slider) return;
		settings = JSON.parse(settings);
		
		var customUrl = settings.url_enable && settings.custom_url !== 'http://' && settings.custom_url !== 'https://' ? settings.custom_url : false;
		slider.one('revolution.slide.onloaded', function() {

			switch(settings.type) {
			
				case 'time':
					
					if(isNaN(settings.minutes)) return;
					var time = parseFloat(settings.minutes) * 60000;
					if(!time) return;
					
					setTimeout(function() {
						
						if(customUrl) self.location.href = customUrl;
						else self.location.reload();
						
					}, time);
				
				break;
				
				case 'slide':
					
					var slide = parseInt(settings.slide, 10);
					if(!slide) return;
					
					slider.on('revolution.slide.onbeforeswap', function(e, data) {
						
						if(parseInt(slider.revcurrentslide(), 10) === slide) {
						
							if(customUrl) self.location.href = customUrl;
							else self.location.reload();
						
						}
						
					});
				
				break;
				
				case 'loops':
				
					var total = slider.revmaxslide(),
						loops = parseInt(settings.loops, 10);
						
					if(!loops) return;
					slider.on('revolution.slide.onbeforeswap', function(e, data) {
						
						if(parseInt(slider.revcurrentslide(), 10) === total) loops--;
						if(!loops) {
						
							if(customUrl) self.location.href = customUrl;
							else self.location.reload();
						
						}
						
					});
				
				break;
				
			}
			
		});
		
	};

})();
