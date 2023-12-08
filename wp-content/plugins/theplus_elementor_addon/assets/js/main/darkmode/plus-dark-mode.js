/*dark mode */
(function($) {	
	
    var WidgetDarkMode = function($scope, $) {
		if($("body").hasClass('darkmode--activated')){			
			$( '.darkmode-toggle' ).addClass( "darkmode-toggle--white" );	
		}
		var container = $scope.find(".tp-dark-mode-wrapper"),
			time = container.data('time'),
			dm_mixcolor = container.data('dm_mixcolor'),
			bgcolor = container.data('bgcolor'),
			save_cookies = container.data('save-cookies'),
			auto_match_os_theme = container.data('auto-match-os-theme'),
			style = container.data('style');
		var label_tag = '';
		$( "body" ).addClass( style );
		if(style=="tp_dm_style2"){
			label_tag = '<span class="tp-dark-mode-slider tp-dark-mode-round "></span>';
		}else{
			label_tag = '🌓';
		}
		var options = {
			left: 'unset',
			time: time,
			mixColor: dm_mixcolor,
			backgroundColor: bgcolor,
			buttonColorDark: '#100f2c',
			buttonColorLight: '#fff',
			saveInCookies: save_cookies,
			label: label_tag,
			autoMatchOsTheme: auto_match_os_theme
		}
		const darkmode = new Darkmode(options);
        darkmode.showWidget();
		
    };
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/tp-dark-mode.default', WidgetDarkMode);
    });
})(jQuery);
/*dark mode */