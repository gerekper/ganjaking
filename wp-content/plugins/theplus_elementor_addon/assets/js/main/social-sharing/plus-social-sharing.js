/*Social Sharing*/
(function ($) {
	"use strict";
	var WidgetSocialSharingHandler = function($scope, $) {
		var container = $scope.find('.tp-social-sharing'),
		cfm = container.find('.tp-social-list .tp-main-menu'),
		cts = container.find('.toggle-share');
		
		if(cfm.length){
			cfm.on('click',function(){
			   $(this).closest('.tp-social-sharing .tp-social-list').toggleClass('active');
			});
		}
		if(cts.length){
			cts.on('click',function(){
			  var tss = $(this).closest('.tp-social-sharing');
			  tss.find('.tp-social-list').toggleClass('active');
			  tss.find('.toggle-share').toggleClass('menu-active');
			});
		}	   
	};
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-social-sharing.default', WidgetSocialSharingHandler);
	});
})(jQuery);