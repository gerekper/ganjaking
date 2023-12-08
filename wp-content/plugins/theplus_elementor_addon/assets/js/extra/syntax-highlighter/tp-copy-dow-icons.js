/*unfold*/
(function ($) {
	"use strict";
	var WidgetshHandler = function($scope, $) {
		var container = $scope.find('.tp-code-highlighter.tpcpicon'),
		data_copy = container.data("prismjs-copy"),
		data_copyed = container.data("prismjs-copy-success"),
		data_copyicon = container.data("copyicon"),
		data_copiedbtnicon = container.data("copiedbtnicon"),
		tpdowicon = $scope.find('.tp-code-highlighter.tpdowicon'),
		data_dowtext = tpdowicon.data("download-text"),
		data_dowicon = tpdowicon.data("download-iconsh");
		
		if(container.length){
			container.find('.copy-to-clipboard-button').html(data_copy + data_copyicon);
			$( ".copy-to-clipboard-button" ).on( "click", function() {
				container.find('.copy-to-clipboard-button').html(data_copyed + data_copiedbtnicon);
				setTimeout(function(){ 
					container.find('.copy-to-clipboard-button').html(data_copy + data_copyicon);
				}, 1500);
			});
		}
		
		if(tpdowicon.length){			
			tpdowicon.find('.toolbar-item a').html(data_dowtext + data_dowicon);			
		}
		
	};	
	
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-syntax-highlighter.default', WidgetshHandler);
	});
})(jQuery);