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
			wp.customize( 'betterdocs_mkb_desc', function( setting ) {
				customizer_conditional_setting_return_toggle_pro(setting,'betterdocs_mkb_desc_color',true);
			});
			wp.customize( 'betterdocs_multikb_layout_select', function( setting ) {
				customizer_controls_show_pro(setting,'betterdocs_mkb_desc','layout-1,layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_desc_color','layout-1,layout-3');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_cat_icon_size_layout2','layout-1');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_item_count_color_layout2','layout-1,layout-2');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_cat_desc','layout-1');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_column_bg_color2','layout-1,layout-2');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_column_content_space','layout-1,layout-2');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_column_content_space_image','layout-1,layout-2');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_column_content_space_title','layout-1,layout-2');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_column_content_space_desc','layout-1,layout-2');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_column_content_space_counter','layout-1,layout-2');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_list_settings','layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_list_bg_color','layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_docs_switch', 'layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_title_font_size', 'layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_list_color','layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_list_hover_color','layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_list_font_size','layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_list_icon_color','layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_list_icon_font_size','layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_list_margin','layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_list_margin_top','layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_list_margin_right','layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_list_margin_bottom','layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_list_margin_left','layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_list_seprator','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_list_bg_color','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_back_color_active','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_list_bg_hover_color','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_font_color','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_font_color_active','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_list_font_size','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_list_column_padding','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_list_column_padding_top','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_list_column_padding_right','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_list_column_padding_bottom','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_list_column_padding_left','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_margin','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_margin_top','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_margin_right','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_margin_bottom','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_margin_left','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_border','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_border_topleft','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_border_topright','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_border_bottomright','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_border_bottomleft','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_box_content_width','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_box_gap','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_box_content_padding_top','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_box_content_padding_right','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_box_content_padding_bottom','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_box_content_padding_left','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_box_content_margin_top','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_box_content_margin_right','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_box_content_margin_bottom','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_box_content_margin_left','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_column_list_heading','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_column_list_color','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_column_list_hover_color','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_column_list_font_size','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_column_list_margin_top','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_column_list_margin','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_column_list_margin_right','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_list_margin_bottom','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_list_margin_left','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_explore_btn','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_explore_btn_bg_color','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_explore_btn_color','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_explore_btn_border_color','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_explore_btn_hover_bg_color','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_explore_btn_hover_color','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_explore_btn_hover_border_color','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_explore_btn_font_size','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_explore_btn_padding','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_explore_btn_padding_top','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_explore_btn_padding_right','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_explore_btn_padding_bottom','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_explore_btn_padding_left','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_explore_btn_borderr','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_explore_btn_borderr_topleft','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_explore_btn_borderr_topright','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_explore_btn_borderr_bottomright','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_tab_list_explore_btn_borderr_bottomleft','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_docs_padding','layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_docs_padding_right','layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_docs_padding_bottom','layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_docs_padding_top','layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_docs_padding_left','layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_title_color','layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_title_color_hover','layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_title_margin','layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_title_margin_top','layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_title_margin_right','layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_title_margin_bottom','layout-3');
				customizer_controls_show_pro(setting,'betterdocs_mkb_popular_title_margin_left','layout-3');
			});
			wp.customize( 'betterdocs_docs_layout_select', function( setting ) {
				customizer_controls_show_pro(setting,'betterdocs_doc_page_cat_title_font_size2','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_cat_icon_size_l_3_4','layout-3,layout-4,layout-5');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_content_overlap','layout-4');
				customizer_controls_show_pro(setting,'betterdocs_docs_page_popular_docs_switch','layout-5'); 
				customizer_controls_show_pro(setting,'betterdocs_doc_page_article_list_bg_color_2','layout-5'); 
				customizer_controls_show_pro(setting,'betterdocs_doc_page_article_list_color_2','layout-5');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_article_list_hover_color_2','layout-5');   
				customizer_controls_show_pro(setting,'betterdocs_doc_page_article_list_font_size_2','layout-5');  
				customizer_controls_show_pro(setting,'betterdocs_doc_page_article_title_font_size_2','layout-5'); 
				customizer_controls_show_pro(setting,'betterdocs_doc_page_article_title_color_2','layout-5');    
				customizer_controls_show_pro(setting,'betterdocs_doc_page_article_title_color_hover_2','layout-5');   
				customizer_controls_show_pro(setting,'betterdocs_doc_page_article_list_icon_color_2','layout-5');   
				customizer_controls_show_pro(setting,'betterdocs_doc_page_article_list_icon_font_size_2','layout-5');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_article_list_margin_2','layout-5');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_article_list_margin_top_2','layout-5');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_article_list_margin_right_2','layout-5');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_article_list_margin_bottom_2','layout-5');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_article_list_margin_left_2','layout-5');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_popular_docs_padding','layout-5');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_popular_docs_padding_top','layout-5');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_popular_docs_padding_right','layout-5');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_popular_docs_padding_bottom','layout-5');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_popular_docs_padding_left','layout-5');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_popular_title_margin','layout-5');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_popular_title_margin_top','layout-5');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_popular_title_margin_right','layout-5');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_popular_title_margin_bottom','layout-5');
				customizer_controls_show_pro(setting,'betterdocs_doc_page_popular_title_margin_left','layout-5');
			});
			wp.customize( 'betterdocs_single_layout_select', function( setting ) {
				customizer_controls_hide_pro(setting,'betterdocs_doc_single_content_area_padding_top','layout-2,layout-3');
				customizer_controls_hide_pro(setting,'betterdocs_doc_single_content_area_padding_bottom','layout-2,layout-3');
			});
			wp.customize( 'betterdocs_category_search_toggle', function( setting ){
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_category_select_search_section', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_category_select_font_size', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_category_select_font_weight', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_category_select_text_transform', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_category_select_text_color', true);
			});
			wp.customize( 'betterdocs_search_button_toggle', function(setting){
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_search_button_section', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_new_search_button_font_size', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_new_search_button_letter_spacing', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_new_search_button_font_weight', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_new_search_button_text_transform', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_search_button_text_color', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_search_button_background_color', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_search_button_background_color_hover', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_search_button_borderr_radius', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_search_button_borderr_left_top', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_search_button_borderr_right_top', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_search_button_borderr_left_bottom', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_search_button_borderr_right_bottom', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_search_button_padding', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_search_button_padding_top', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_search_button_padding_right', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_search_button_padding_bottom', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_search_button_padding_left', true);
			});
			wp.customize( 'betterdocs_popular_search_toggle', function(setting){
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_section', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_margin', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_margin_top', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_margin_right', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_margin_bottom', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_margin_left', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_title_text_color', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_title_font_size', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_font_size', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_background_color', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_keyword_text_color', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_padding', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_padding_top', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_padding_right', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_padding_bottom', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_padding_left', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_keyword_margin', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_keyword_margin_top', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_keyword_margin_right', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_keyword_margin_bottom', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_keyword_margin_left', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_keyword_border',true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_keyword_border_color',true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_keyword_border_width_top',true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_keyword_border_width_right',true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_keyword_border_width_bottom',true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_keyword_border_width_left',true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_search_keyword_border_width', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_keyword_border_radius', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_keyword_border_radius_left_top', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_keyword_border_radius_right_top', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_keyword_border_radius_left_bottom', true);
				customizer_conditional_setting_return_toggle_pro(setting, 'betterdocs_popular_keyword_border_radius_right_bottom', true);
			});
		});
	});
})(jQuery);