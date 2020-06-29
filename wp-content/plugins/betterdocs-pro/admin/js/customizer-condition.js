(function ($) {
	'use strict';
	$(document).ready(function () {
		function customizer_controls_show_pro(setting,controler_name,controler_val){
			wp.customize.control( controler_name, function( control ) { 
				var controler_array = controler_val.split(',');
				var visibility = function() {
					if ( $.inArray(setting.get(), controler_array) > -1 ) {
						control.container.slideDown( 180 );
					} else {
						control.container.slideUp( 180 );
					}
				};           
				visibility();         
				setting.bind( visibility ); 
			});	
		}

		function customizer_controls_hide_pro(setting,controler_name,controler_val){
			wp.customize.control( controler_name, function( control ) {
				var controler_array = controler_val.split(',');
				var visibility = function() {
					if ( $.inArray(setting.get(), controler_array) > -1 ) {
						control.container.slideUp( 180 );
					} else {
						control.container.slideDown( 180 );
					}
				};   
				visibility();   
				setting.bind( visibility ); 
			});	
		}

		function customizer_conditional_setting_return_toggle_pro(setting,controler_name,controler_val){
			wp.customize.control( controler_name, function( control ) { 
				var visibility = function() {
					if ( setting.get() == true ) { 
						control.container.slideDown( 180 );     
					} else {
						control.container.slideUp( 180 );
					}
				};           
				visibility();         
				setting.bind( visibility ); 
			});	
		}
		wp.customize.bind( 'ready', function() {
			wp.customize( 'betterdocs_docs_layout_select', function( setting ) {
				customizer_controls_show_pro(setting,'betterdocs_doc_page_cat_title_font_size2','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_cat_icon_size_l_3_4','layout-3,layout-4');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_content_overlap','layout-4');
			});
			wp.customize( 'betterdocs_single_layout_select', function( setting ) {
				customizer_controls_hide_pro(setting,'betterdocs_doc_single_content_area_padding_top','layout-2,layout-3');
				customizer_controls_hide_pro(setting,'betterdocs_doc_single_content_area_padding_bottom','layout-2,layout-3');
			});
		});
	});
})(jQuery);