/* 
Double Tap to Go
Author: Graffino (http://www.graffino.com)
Version: 0.3
Originally by Osvaldas Valutis, www.osvaldas.info	
Available for use under the MIT License
*/

;(function($, window, document, undefined) {
	$.fn.doubleTapToGo = function(action) {

		if (!('ontouchstart' in window) &&
			!navigator.msMaxTouchPoints &&
			!navigator.userAgent.toLowerCase().match( /windows phone os 7/i )) return false;

		if (action === 'unbind') {
			this.each(function() {
				$(this).off();
				$(document).off('click touchstart MSPointerDown', handleTouch);	
			});

		} else {
			this.each(function() {
				var curItem = false;
	
				$(this).on('click', function(e) {
					var item = $(this);
					if (item[0] != curItem[0]) {
						e.preventDefault();
						curItem = item;
					}
				});
	
				$(document).on('click touchstart MSPointerDown', handleTouch); 
				
				function handleTouch(e) {
					var resetItem = true,
						parents = $(e.target).parents();
	
					for (var i = 0; i < parents.length; i++)
						if (parents[i] == curItem[0])
							resetItem = false;
	
					if(resetItem)
						curItem = false;
				}
			});
		}
		return this;	
	};
})(jQuery, window, document);
