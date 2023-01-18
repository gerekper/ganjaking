<?php 

if ( ! function_exists( 'betterdocs_get_option_defaults_pro' ) ) :
	/**
	 * Set default options
	 */
	function betterdocs_get_option_defaults_pro() {
		$betterdocs_defaults_pro = array(
			'betterdocs_multikb_layout_select' => 'layout-1',
			'betterdocs_mkb_background_color' => '#ffffff',
			'betterdocs_mkb_background_image' => '',
			'betterdocs_mkb_background_property' => '',
			'betterdocs_mkb_background_size' => '',
			'betterdocs_mkb_background_repeat' => '',
			'betterdocs_mkb_background_attachment' => '',
			'betterdocs_mkb_background_position' => '',
			'betterdocs_mkb_content_padding' => '',
			'betterdocs_mkb_content_padding_top' => '50',
			'betterdocs_mkb_content_padding_right' => '0',
			'betterdocs_mkb_content_padding_bottom' => '50',
			'betterdocs_mkb_content_padding_left' => '0',
			'betterdocs_mkb_content_width' => '100',
			'betterdocs_mkb_content_max_width' => '1600',
			'betterdocs_mkb_column_settings' => '',
            'betterdocs_mkb_title_tag' => 'h2',
			'betterdocs_mkb_column_space' => '15',
			'betterdocs_mkb_column_padding' => '',
			'betterdocs_mkb_column_padding_top' => '20',
			'betterdocs_mkb_column_padding_right' => '20',
			'betterdocs_mkb_column_padding_bottom' => '20',
			'betterdocs_mkb_column_padding_left' => '20',
			'betterdocs_mkb_column_bg_color2' => '#f8f8fc',
			'betterdocs_mkb_column_hover_bg_color' => '#fff',
			'betterdocs_mkb_column_borderr' => '',
			'betterdocs_mkb_column_borderr_topleft' => '5',
			'betterdocs_mkb_column_borderr_topright' => '5',
			'betterdocs_mkb_column_borderr_bottomright' => '5',
			'betterdocs_mkb_column_borderr_bottomleft' => '5',
			'betterdocs_mkb_column_content_space' => '',
			'betterdocs_mkb_column_content_space_image' => '20',
			'betterdocs_mkb_column_content_space_title' => '15',
			'betterdocs_mkb_column_content_space_desc' => '15',
			'betterdocs_mkb_column_content_space_counter' => '0',
			'betterdocs_mkb_popular_list_settings' => '',
			'betterdocs_mkb_popular_list_bg_color' => '#ffffff',
			'betterdocs_mkb_popular_list_color' => '#15063F',
			'betterdocs_mkb_popular_list_hover_color' => '#614CFB',
			'betterdocs_mkb_popular_list_font_size' => '17',
			'betterdocs_mkb_popular_list_icon_color' => '#A6A4EF',
			'betterdocs_mkb_popular_list_icon_font_size' => '15',
			'betterdocs_mkb_popular_list_margin' => '',
			'betterdocs_mkb_popular_list_margin_top' => '0',
			'betterdocs_mkb_popular_list_margin_right' => '0',
			'betterdocs_mkb_popular_list_margin_bottom' => '0',
			'betterdocs_mkb_popular_list_margin_left' => '0',
			'betterdocs_mkb_cat_icon_size' => '80',
			'betterdocs_mkb_cat_title_font_size' => '20',
			'betterdocs_mkb_cat_title_color' => '#333333',
			'betterdocs_mkb_cat_title_hover_color' => '',
			'betterdocs_mkb_item_count_color' => '#707070',
			'betterdocs_mkb_item_count_color_hover' => '#707070',
			'betterdocs_mkb_item_count_font_size' => '15',
			'betterdocs_mkb_desc' => false,
			'betterdocs_mkb_desc_color' => '#566e8b',
			'betterdocs_doc_page_content_overlap' => '135',
			'betterdocs_doc_page_cat_icon_size_l_3_4' => '60',
			'betterdocs_doc_page_cat_title_font_size2' => '18',
			'betterdocs_doc_single_content_area_bg_color' => '', // from free
			'betterdocs_doc_single_content_area_padding_right' => '25', // from free
			'betterdocs_doc_single_content_area_padding_left' => '25', // from free
			'betterdocs_mkb_list_bg_color' => '#EDEDFF', 
			'betterdocs_mkb_tab_list_back_color_active' => '#614EFC',
			'betterdocs_mkb_list_bg_hover_color'	=> '#EDEDFF',
			'betterdocs_mkb_tab_list_font_color' => '#15063F',
			'betterdocs_mkb_tab_list_font_color_active' => '#fff',
			'betterdocs_mkb_list_font_size' => '16',
			'betterdocs_mkb_list_column_padding_top' => '10',
			'betterdocs_mkb_list_column_padding_right' => '36',
			'betterdocs_mkb_list_column_padding_bottom' => '10',
			'betterdocs_mkb_list_column_padding_left' => '36',
			'betterdocs_mkb_tab_list_margin_top' => '8',
			'betterdocs_mkb_tab_list_margin_right' => '8',
			'betterdocs_mkb_tab_list_margin_bottom' => '8',
			'betterdocs_mkb_tab_list_margin_left'	=> '8',
			'betterdocs_mkb_tab_list_border_topleft' => '5',
			'betterdocs_mkb_tab_list_border_topright' => '5',
			'betterdocs_mkb_tab_list_border_bottomright' => '5',
			'betterdocs_mkb_tab_list_border_bottomleft' => '5',
			'betterdocs_mkb_tab_list_box_content_width' => '100',
			'betterdocs_mkb_tab_list_box_gap'=> '30',
			'betterdocs_mkb_tab_list_box_content_padding_top' => '25',
			'betterdocs_mkb_tab_list_box_content_padding_right' => '25',
			'betterdocs_mkb_tab_list_box_content_padding_bottom' => '25',
			'betterdocs_mkb_tab_list_box_content_padding_left' => '0',
			'betterdocs_mkb_tab_list_box_content_margin_top' => '0',
			'betterdocs_mkb_tab_list_box_content_margin_right' => '0',
			'betterdocs_mkb_tab_list_box_content_margin_bottom' => '0',
			'betterdocs_mkb_tab_list_box_content_margin_left' => '0',
			'betterdocs_mkb_column_list_heading' => '',
			'betterdocs_mkb_column_list_color' => '#566e8b',
			'betterdocs_mkb_column_list_hover_color' => '#A6A4EF',
			'betterdocs_mkb_column_list_font_size'	 => '15',
			'betterdocs_mkb_column_list_margin_top' => '10',
			'betterdocs_mkb_column_list_margin_right' => 10,
			'betterdocs_mkb_list_margin_bottom'	=> 10,
			'betterdocs_mkb_list_margin_left' => 10,
            'betterdocs_mkb_tab_list_explore_btn' => '',
			'betterdocs_mkb_tab_list_explore_btn_bg_color' => '#ffffff',
			'betterdocs_mkb_tab_list_explore_btn_color' => '#528ffe',
			'betterdocs_mkb_tab_list_explore_btn_border_color' => '#528ffe',
			'betterdocs_mkb_tab_list_explore_btn_hover_bg_color' => '#528ffe',
			'betterdocs_mkb_tab_list_explore_btn_hover_color' => '#fff',
			'betterdocs_mkb_tab_list_explore_btn_hover_border_color' => '#528ffe',
			'betterdocs_mkb_tab_list_explore_btn_font_size' => '16',
            'betterdocs_mkb_tab_list_explore_btn_padding' => '',
			'betterdocs_mkb_tab_list_explore_btn_padding_top' => '10',
			'betterdocs_mkb_tab_list_explore_btn_padding_right' => '20',
			'betterdocs_mkb_tab_list_explore_btn_padding_bottom' => '10',
			'betterdocs_mkb_tab_list_explore_btn_padding_left' => '20',
            'betterdocs_mkb_tab_list_explore_btn_borderr' => '',
			'betterdocs_mkb_tab_list_explore_btn_borderr_topleft' => '50',
			'betterdocs_mkb_tab_list_explore_btn_borderr_topright' => '50',
			'betterdocs_mkb_tab_list_explore_btn_borderr_bottomright' => '50',
			'betterdocs_mkb_tab_list_explore_btn_borderr_bottomleft' => '50',
			'betterdocs_mkb_popular_title_font_size' => '20',
			'betterdocs_mkb_popular_docs_switch' => true,
			'betterdocs_mkb_popular_docs_padding_top' => '0',
			'betterdocs_mkb_popular_docs_padding_right' => '0',
			'betterdocs_mkb_popular_docs_padding_bottom' => '0',
			'betterdocs_mkb_popular_docs_padding_left' => '0',
			'betterdocs_mkb_popular_docs_padding' => '',
			'betterdocs_mkb_popular_title_color' => '#000000',
			'betterdocs_mkb_popular_title_color_hover' => '#000000',
            'betterdocs_category_search_toggle' => true,
            'betterdocs_search_button_toggle' => true,
            'betterdocs_popular_search_toggle' => true,
            'betterdocs_search_button_section' => '',
            'betterdocs_search_button_background_color' => '#24CC8F',
			'betterdocs_search_button_background_color_hover' => '#528ffe',
            'betterdocs_search_button_text_color' => '#FFFFFF',
            'betterdocs_search_button_borderr_radius' => '',
            'betterdocs_search_button_borderr_left_top' => '7',
            'betterdocs_search_button_borderr_right_top' => '7',
            'betterdocs_search_button_borderr_left_bottom' => '7',
            'betterdocs_search_button_borderr_right_bottom' => '7',
            'betterdocs_search_button_padding' => '',
            'betterdocs_search_button_padding_top' => '15',
            'betterdocs_search_button_padding_left' => '25',
            'betterdocs_search_button_padding_right' => '25',
            'betterdocs_search_button_padding_bottom' => '15',
            'betterdocs_new_search_button_font_size' => '16',
            'betterdocs_new_search_button_font_weight' => '500',
            'betterdocs_new_search_button_text_transform' => 'uppercase',
            'betterdocs_new_search_button_letter_spacing' => '5',
            'betterdocs_popular_search_section' => '',
            'betterdocs_popular_search_background_color' => '#FFF',
            'betterdocs_popular_keyword_text_color' => '#99A3CA',
            'betterdocs_popular_search_margin' => '',
            'betterdocs_popular_search_margin_top' => '24',
            'betterdocs_popular_search_margin_right' => '0',
            'betterdocs_popular_search_margin_bottom' => '0',
            'betterdocs_popular_search_margin_left' => '0',
            'betterdocs_popular_search_text' => esc_html__('Popular Search', 'betterdcos-pro'),
            'betterdocs_popular_search_padding' => '',
            'betterdocs_popular_search_padding_top' => '8',
            'betterdocs_popular_search_padding_right' => '25',
            'betterdocs_popular_search_padding_bottom' => '8',
            'betterdocs_popular_search_padding_left' => '25',
            'betterdocs_popular_search_font_size' => '14',
            'betterdocs_popular_search_title_text_color' => '#8588A6',
            'betterdocs_popular_search_title_font_size' => '14',
            'betterdocs_popular_search_keyword_margin' => '',
            'betterdocs_popular_search_keyword_margin_top' => '2',
            'betterdocs_popular_search_keyword_margin_right' => '2',
            'betterdocs_popular_search_keyword_margin_bottom' => '2',
            'betterdocs_popular_search_keyword_margin_left' => '2',
            'betterdocs_category_select_search_section' => '',
            'betterdocs_category_select_font_size' => '16',
            'betterdocs_category_select_font_weight' => 'normal',
            'betterdocs_category_select_text_transform' => 'none',
            'betterdocs_category_select_text_color' => '#434872',
			'betterdocs_doc_page_article_list_margin_2' => '',
			'betterdocs_doc_page_article_list_margin_top_2' => '0',
			'betterdocs_doc_page_article_list_margin_right_2' => '0',
			'betterdocs_doc_page_article_list_margin_bottom_2' => '0',
			'betterdocs_doc_page_article_list_margin_left_2' => '0',
			'betterdocs_doc_page_popular_docs_padding' => '',
			'betterdocs_doc_page_popular_docs_padding_top' => '0',
			'betterdocs_doc_page_popular_docs_padding_right' => '0',
			'betterdocs_doc_page_popular_docs_padding_bottom' => '0',
			'betterdocs_doc_page_popular_docs_padding_left' => '0',
			'betterdocs_doc_page_article_list_bg_color_2' => '#ffffff',
			'betterdocs_docs_page_popular_docs_switch' => true,
			'betterdocs_doc_page_article_list_color_2' => '#15063F',
			'betterdocs_doc_page_article_list_hover_color_2' => '#614cfb',
			'betterdocs_doc_page_article_list_font_size_2' => '17',
			'betterdocs_doc_page_article_title_font_size_2' => '20',
			'betterdocs_doc_page_article_title_color_2' => '#000000',
			'betterdocs_doc_page_article_title_color_hover_2' => '#000000',
			'betterdocs_doc_page_article_list_icon_color_2' => '#A6A4EF',
			'betterdocs_doc_page_article_list_icon_font_size_2' => '15',
			'betterdocs_doc_page_popular_title_margin' => '',
			'betterdocs_doc_page_popular_title_margin_top' => '0',
			'betterdocs_doc_page_popular_title_margin_right' => '0',
			'betterdocs_doc_page_popular_title_margin_bottom' => '0',
			'betterdocs_doc_page_popular_title_margin_left' => '0',
			'betterdocs_mkb_popular_title_margin' => '',
			'betterdocs_mkb_popular_title_margin_top' => '0',
			'betterdocs_mkb_popular_title_margin_right' => '0',
			'betterdocs_mkb_popular_title_margin_bottom' => '0',
			'betterdocs_mkb_popular_title_margin_left' => '0',
			'betterdocs_popular_search_keyword_border' => 'solid',
			'betterdocs_popular_search_keyword_border_color' => '#DDDEFF',
			'betterdocs_popular_search_keyword_border_width' => '',
			'betterdocs_popular_search_keyword_border_width_top' => '1',
			'betterdocs_popular_search_keyword_border_width_right' => '1',
			'betterdocs_popular_search_keyword_border_width_bottom' => '1',
			'betterdocs_popular_search_keyword_border_width_left' => '1',
			'betterdocs_popular_keyword_border_radius' => '',
			'betterdocs_popular_keyword_border_radius_left_top' => '0',
			'betterdocs_popular_keyword_border_radius_right_top' => '0',
			'betterdocs_popular_keyword_border_radius_left_bottom' => '0',
			'betterdocs_popular_keyword_border_radius_right_bottom' => '0',
			'betterdocs_doc_page_item_count_color_layout6' => '#FFFFFF',
			'betterdocs_doc_page_item_count_back_color_layout6'	=> '#523BE9',
			'betterdocs_doc_page_item_count_border_width_layout6' => '',
			'betterdocs_doc_page_item_count_border_width_top_layout6' => '0',
			'betterdocs_doc_page_item_count_border_width_right_layout6' => '0',
			'betterdocs_doc_page_item_count_border_width_bottom_layout6' => '0',
			'betterdocs_doc_page_item_count_border_width_left_layout6' => '0',
			'betterdocs_doc_page_item_count_border_radius_layout6' => '',
			'betterdocs_doc_page_item_count_border_radius_top_left_layout6' => '90',
			'betterdocs_doc_page_item_count_border_radius_top_right_layout6' => '90',
			'betterdocs_doc_page_item_count_border_radius_bottom_right_layout6' => '90',
			'betterdocs_doc_page_item_count_border_radius_bottom_left_layout6' => '90',
			'betterdocs_doc_page_item_count_margin_layout6' => '',
			'betterdocs_doc_page_item_count_margin_top_layout6' => '0',
			'betterdocs_doc_page_item_count_margin_right_layout6' => '0',
			'betterdocs_doc_page_item_count_margin_bottom_layout6' => '0',
			'betterdocs_doc_page_item_count_margin_left_layout6' => '18',
			'betterdocs_doc_page_item_count_border_type_layout6' =>'none',
			'betterdocs_doc_page_item_count_border_color_layout6' => '#FFF4F4',
			'betterdocs_doc_page_item_count_padding_layout6' => '',
			'betterdocs_doc_page_item_count_padding_top_layout6' => '0',
			'betterdocs_doc_page_item_count_padding_right_layout6' => '0',
			'betterdocs_doc_page_item_count_padding_bottom_layout6' => '0',
			'betterdocs_doc_page_item_count_padding_left_layout6' => '0',
			'betterdocs_doc_list_layout6_separator' => '',
			'betterdocs_doc_list_font_size_layout6' => '15',
			'betterdocs_doc_list_font_weight_layout6' => '400',
			'betterdocs_doc_list_font_line_height_layout6' => '63',
			'betterdocs_doc_list_img_switch_layout6' => true,
			'betterdocs_doc_list_img_width_layout6' => '45',
			'betterdocs_doc_page_cat_title_padding_bottom_layout6' => '0',
			'betterdocs_doc_list_desc_switch_layout6' => true,
			'betterdocs_doc_list_desc_color_layout6' => '#7B7B7B',
			'betterdocs_doc_list_desc_font_size_layout6' => '17',
			'betterdocs_doc_list_desc_font_weight_layout6' => '400',
			'betterdocs_doc_list_desc_line_height_layout6' => '27',
			'betterdocs_doc_list_desc_margin_layout6' => '',
			'betterdocs_doc_list_desc_margin_top_layout6' => '15',
			'betterdocs_doc_list_desc_margin_right_layout6' => '0',
			'betterdocs_doc_list_desc_margin_bottom_layout6' => '25',
			'betterdocs_doc_list_desc_margin_left_layout6' => '0',
			'betterdocs_doc_list_font_color_layout6' => '#15063F',
			'betterdocs_doc_list_font_color_hover_layout6' => '#15063F',
			'betterdocs_doc_list_back_color_hover_layout6' => '#F2F0FF',
			'betterdocs_doc_list_border_color_hover_layout6' => '#15063F',
			'betterdocs_doc_list_margin_layout6' => '',
			'betterdocs_doc_list_margin_top_layout6' => '0',
			'betterdocs_doc_list_margin_right_layout6' => '0',
			'betterdocs_doc_list_margin_bottom_layout6' => '0',
			'betterdocs_doc_list_margin_left_layout6' => '0',
			'betterdocs_doc_list_padding_layout6' => '',
			'betterdocs_doc_list_padding_top_layout6' => '0',
			'betterdocs_doc_list_padding_right_layout6' => '0',
			'betterdocs_doc_list_padding_bottom_layout6' => '0',
			'betterdocs_doc_list_padding_left_layout6' => '0',
			'betterdocs_doc_list_border_style_layout6' => 'solid',
			'betterdocs_doc_list_border_layout6' => '',
			'betterdocs_doc_list_border_top_layout6' => '1',
			'betterdocs_doc_list_border_right_layout6' => '1',
			'betterdocs_doc_list_border_bottom_layout6' => '1',
			'betterdocs_doc_list_border_left_layout6' => '1',
			'betterdocs_doc_list_border_hover_layout6' => '',
			'betterdocs_doc_list_border_hover_top_layout6' => '1',
			'betterdocs_doc_list_border_hover_right_layout6' => '1',
			'betterdocs_doc_list_border_hover_bottom_layout6' => '1',
			'betterdocs_doc_list_border_hover_left_layout6' => '1',
			'betterdocs_doc_list_border_color_top_layout6' => '#E8E9EB',
			'betterdocs_doc_list_border_color_right_layout6' => 'rgba(255,255,255,0)',
			'betterdocs_doc_list_border_color_bottom_layout6' => 'rgba(255,255,255,0)',
			'betterdocs_doc_list_border_color_left_layout6' => 'rgba(255,255,255,0)',
			'betterdocs_doc_list_arrow_height_layout6' => '14',
			'betterdocs_doc_list_arrow_width_layout6' => '26',
			'betterdocs_doc_list_arrow_color_layout6' => '#15063F',
			'betterdocs_doc_list_explore_more_separator' => '',
			'betterdocs_doc_list_explore_more_font_size_layout6' => '15',
			'betterdocs_doc_list_explore_more_font_color_layout6' => '#523BE9',
			'betterdocs_doc_list_explore_more_font_weight_layout6' => '600',
			'betterdocs_doc_list_explore_more_font_line_height_layout6' => '50',
			'betterdocs_doc_list_explore_more_padding_layout6' => '',
			'betterdocs_doc_list_explore_more_padding_top_layout6' => '0',
			'betterdocs_doc_list_explore_more_padding_right_layout6' => '5',
			'betterdocs_doc_list_explore_more_padding_bottom_layout6' => '0',
			'betterdocs_doc_list_explore_more_padding_left_layout6' => '0',
			'betterdocs_doc_list_explore_more_margin_layout6' => '',
			'betterdocs_doc_list_explore_more_margin_top_layout6' => '0',
			'betterdocs_doc_list_explore_more_margin_right_layout6' => '0',
			'betterdocs_doc_list_explore_more_margin_bottom_layout6' => '0',
			'betterdocs_doc_list_explore_more_margin_left_layout6' => '0',
			'betterdocs_doc_list_explore_more_arrow_height_layout6' => '14',
			'betterdocs_doc_list_explore_more_arrow_width_layout6' => '26',
			'betterdocs_doc_list_explore_more_arrow_color_layout6' => 'rgb(82, 59, 233)',
			'betterdocs_archive_title_tag_layout2'   => 'h2', 
			'betterdocs_archive_title_color_layout2' => '#15063F',
			'betterdocs_archive_title_font_size_layout2' => '30',
			'betterdocs_archive_title_margin_layout2' => '',
			'betterdocs_archive_title_margin_top_layout2' => '0',
			'betterdocs_archive_title_margin_right_layout2' => '0',
			'betterdocs_archive_title_margin_bottom_layout2' => '0',
			'betterdocs_archive_title_margin_left_layout2' => '0',
			'betterdocs_archive_description_color_layout2' => '#777777',
			'betterdocs_archive_description_font_size_layout2' => '17',
			'betterdocs_archive_description_margin_layout2' => '',
			'betterdocs_archive_description_margin_top_layout2' => '0',
			'betterdocs_archive_description_margin_right_layout2' => '0',
			'betterdocs_archive_description_margin_bottom_layout2' => '0',
			'betterdocs_archive_description_margin_left_layout2' => '0',
			'betterdocs_archive_list_item_color_layout2' => '#15063F',
			'betterdocs_archive_list_item_color_hover_layout2' => '#15063F',
			'betterdocs_archive_list_back_color_hover_layout2' => '#F2F0FF',
			'betterdocs_archive_list_border_color_hover_layout2' => '#15063F',
			'betterdocs_archive_list_border_width_hover_layout2' => '',
			'betterdocs_archive_list_border_width_top_hover_layout2' => '1',
			'betterdocs_archive_list_border_width_right_hover_layout2' => '1',
			'betterdocs_archive_list_border_width_bottom_hover_layout2' => '1',
			'betterdocs_archive_list_border_width_left_hover_layout2' => '1',
			'betterdocs_archive_list_border_style_layout2' => 'solid',
			'betterdocs_archive_list_border_width_layout2' => '',
			'betterdocs_archive_list_border_width_top_layout2' => '1',
			'betterdocs_archive_list_border_width_right_layout2' => '1',
			'betterdocs_archive_list_border_width_bottom_layout2' => '1',
			'betterdocs_archive_list_border_width_left_layout2' => '1',
			'betterdocs_archive_list_border_width_color_top_layout2' => '#E8E9EB',
			'betterdocs_archive_list_border_width_color_right_layout2' => 'rgba(255,255,255,0)',
			'betterdocs_archive_list_border_width_color_bottom_layout2' => 'rgba(255,255,255,0)',
			'betterdocs_archive_list_border_width_color_left_layout2' => 'rgba(255,255,255,0)',
			'betterdocs_archive_list_item_font_size_layout2' => '18',
			'betterdocs_archive_article_list_margin_layout2' => '',
			'betterdocs_archive_article_list_margin_top_layout2' => '0',
			'betterdocs_archive_article_list_margin_right_layout2' => '0',
			'betterdocs_archive_article_list_margin_bottom_layout2' => '0',
			'betterdocs_archive_article_list_margin_left_layout2' => '0',
			'betterdocs_archive_article_list_font_weight_layout2' => '400',
			'betterdocs_archive_list_item_line_height_layout2' => '70',
			'betterdocs_archive_list_item_arrow_height_layout2' => '14',
			'betterdocs_archive_list_item_arrow_width_layout2' => '26',
			'betterdocs_archive_list_item_arrow_color_layout2' => '#15063F',
			'betterdocs_archive_article_list_arrow_margin_layout2' => '',
			'betterdocs_archive_article_list_arrow_margin_top_layout2' => '0',
			'betterdocs_archive_article_list_arrow_margin_right_layout2' => '0',
			'betterdocs_archive_article_list_arrow_margin_bottom_layout2' => '0',
			'betterdocs_archive_article_list_arrow_margin_left_layout2' => '0',
			'betterdocs_archive_article_list_excerpt_font_weight_layout2' => '400',
			'betterdocs_archive_article_list_excerpt_font_size_layout2' => '14',
			'betterdocs_archive_article_list_excerpt_font_line_height_layout2' => '16.94',
			'betterdocs_archive_article_list_excerpt_font_color_layout2' => '#707070',
			'betterdocs_archive_article_list_excerpt_margin_layout2' => '',
			'betterdocs_archive_article_list_excerpt_margin_top_layout2' => '0',
			'betterdocs_archive_article_list_excerpt_margin_right_layout2' => '0',
			'betterdocs_archive_article_list_excerpt_margin_left_layout2' => '0',
			'betterdocs_archive_article_list_excerpt_margin_bottom_layout2' => '0',
			'betterdocs_archive_article_list_excerpt_padding_layout2' => '',
			'betterdocs_archive_article_list_excerpt_padding_top_layout2' => '0',
			'betterdocs_archive_article_list_excerpt_padding_right_layout2' => '0',
			'betterdocs_archive_article_list_excerpt_padding_bottom_layout2' => '20',
			'betterdocs_archive_article_list_excerpt_padding_left_layout2' => '0',
			'betterdocs_archive_article_list_counter_seperator_layout2' => '',
			'betterdocs_archive_article_list_counter_font_weight_layout2' => '500',
			'betterdocs_archive_article_list_counter_font_size_layout2' => '16',
			'betterdocs_archive_article_list_counter_font_line_height_layout2' => '34',
			'betterdocs_archive_article_list_counter_font_color_layout2' => '#FFFFFF',
			'betterdocs_archive_article_list_counter_border_radius_layout2' => '',
			'betterdocs_archive_article_list_counter_border_radius_top_left_layout2' => '90',
			'betterdocs_archive_article_list_counter_border_radius_top_right_layout2' => '90',
			'betterdocs_archive_article_list_counter_border_radius_bottom_right_layout2' => '90',
			'betterdocs_archive_article_list_counter_border_radius_bottom_left_layout2' => '90',
			'betterdocs_archive_article_list_counter_margin_layout2' => '',
			'betterdocs_archive_article_list_counter_margin_top_layout2' => '0',
			'betterdocs_archive_article_list_counter_margin_right_layout2' => '16',
			'betterdocs_archive_article_list_counter_margin_bottom_layout2' => '0',
			'betterdocs_archive_article_list_counter_margin_left_layout2' => '16',
			'betterdocs_archive_article_list_counter_padding_layout2' => '',
			'betterdocs_archive_article_list_counter_padding_top_layout2' => '0',
			'betterdocs_archive_article_list_counter_padding_right_layout2' => '20',
			'betterdocs_archive_article_list_counter_padding_bottom_layout2' => '0',
			'betterdocs_archive_article_list_counter_padding_left_layout2' => '20',
			'betterdocs_archive_article_list_counter_border_color_layout2' => '#591FFF',
			'betterdocs_archive_article_list_counter_back_color_layout2' => '#591FFF',
			'betterdocs_sidebar_bg_color_layout6' => 'rgba(255,255,255,0)',
			'betterdocs_sidebar_padding_layout6'  => '',
			'betterdocs_sidebar_padding_top_layout6' => '0',
			'betterdocs_sidebar_padding_right_layout6' => '0',
			'betterdocs_sidebar_padding_bottom_layout6' => '0',
			'betterdocs_sidebar_padding_left_layout6' => '0',
			'betterdocs_sidebar_margin_layout6' => '',
			'betterdocs_sidebar_margin_top_layout6' => '40',
			'betterdocs_sidebar_margin_right_layout6' => '0',
			'betterdocs_sidebar_margin_bottom_layout6' => '0',
			'betterdocs_sidebar_margin_left_layout6' => '0',
			'betterdocs_sidebar_border_radius_layout6' => '',
			'betterdocs_sidebar_border_radius_top_left_layout6' => '0',
			'betterdocs_sidebar_border_radius_top_right_layout6' => '0',
			'betterdocs_sidebar_border_radius_bottom_right_layout6' => '0',
			'betterdocs_sidebar_border_radius_bottom_left_layout6' => '0',
			'betterdocs_sidebar_title_layout6' => '',
			'betterdocs_sidebar_title_tag_layout6' => 'h2',
			'betterdocs_sidebar_title_bg_color_layout6' => 'rgba(255,255,255,0)',
			'betterdocs_sidebar_active_bg_color_layout6' => '#F2F0FF',
			'betterdocs_sidebar_active_bg_border_color_layout6' => '#000000',
			'betterdocs_sidebar_active_title_bg_color_layout6' => '#F2F0FF',
			'betterdocs_sidebar_title_color_layout6' => '#777777',
			'betterdocs_sidebar_title_hover_color_layout6' => '#777777',
			'betterdocs_sidebar_title_font_size_layout6' => '16',
			'betterdocs_sidebar_title_font_line_height_layout6' => '36',
			'betterdocs_sidebar_title_font_weight_layout6' => '500',
			'betterdocs_sidebar_title_padding_layout6' => '',
			'betterdocs_sidebar_title_padding_top_layout6' => '0',
			'betterdocs_sidebar_title_padding_right_layout6' => '0',
			'betterdocs_sidebar_title_padding_bottom_layout6' => '0',
			'betterdocs_sidebar_title_padding_left_layout6' => '0',
			'betterdocs_sidebar_title_margin_layout6' => '',
			'betterdocs_sidebar_title_margin_top_layout6' => '0',
			'betterdocs_sidebar_title_margin_right_layout6' => '0',
			'betterdocs_sidebar_title_margin_bottom_layout6' => '0',
			'betterdocs_sidebar_title_margin_left_layout6' => '0',
			'betterdocs_sidebar_term_list_border_type_layout6' => 'solid',
			'betterdocs_sidebar_term_border_width_layout6' => '',
			'betterdocs_sidebar_term_border_top_width_layout6' => '1',
			'betterdocs_sidebar_term_border_right_width_layout6' => '0',
			'betterdocs_sidebar_term_border_bottom_width_layout6' => '0',
			'betterdocs_sidebar_term_border_left_width_layout6' => '0',
			'betterdocs_sidebar_term_border_width_color_layout6' => '#E8E9EB',
			'betterdocs_sidebar_term_item_counter_seperator_layout6' => '',
			'betterdocs_sidebar_term_item_counter_font_size_layout6' => '15',
			'betterdocs_sidebar_term_item_counter_font_weight_layout6' => '500',
			'betterdocs_sidebar_term_item_counter_font_line_height_layout6' => '36',
			'betterdocs_sidebar_term_item_counter_color_layout6' => '#2B11E5',
			'betterdocs_sidebar_term_item_counter_border_type_layout6' => 'solid',
			'betterdocs_sidebar_term_item_counter_back_color_layout6' => '#fff',
			'betterdocs_sidebar_term_item_counter_border_width_layout6' => '1',
			'betterdocs_sidebar_term_item_counter_border_radius_layout6' => '',
			'betterdocs_sidebar_term_item_counter_border_radius_top_left_layout6' => '90',
			'betterdocs_sidebar_term_item_counter_border_radius_top_right_layout6' => '90',
			'betterdocs_sidebar_term_item_counter_border_radius_bottom_right_layout6' => '90',
			'betterdocs_sidebar_term_item_counter_border_radius_bottom_left_layout6' => '90',
			'betterdocs_sidebar_term_item_counter_padding_layout6' => '',
			'betterdocs_sidebar_term_item_counter_padding_top_layout6' => '0',
			'betterdocs_sidebar_term_item_counter_padding_right_layout6' => '0',
			'betterdocs_sidebar_term_item_counter_padding_bottom_layout6' => '0',
			'betterdocs_sidebar_term_item_counter_padding_left_layout6' => '0',
			'betterdocs_sidebar_term_item_counter_margin_layout6' => '',
			'betterdocs_sidebar_term_item_counter_margin_top_layout6' => '0',
			'betterdocs_sidebar_term_item_counter_margin_right_layout6' => '10',
			'betterdocs_sidebar_term_item_counter_margin_bottom_layout6' => '0',
			'betterdocs_sidebar_term_item_counter_margin_left_layout6' => '10',
			'betterdocs_archive_inner_content_back_color_layout2' => '#F2F0FF',
			'betterdocs_archive_inner_content_image_size_layout2' => '33',
			'betterdocs_archive_other_categories_image_size' => '100',
			'betterdocs_archive_inner_content_image_padding_layout2' => '',
			'betterdocs_archive_inner_content_image_padding_top_layout2' => '0',
			'betterdocs_archive_inner_content_image_padding_right_layout2' => '0',
			'betterdocs_archive_inner_content_image_padding_bottom_layout2' => '0',
			'betterdocs_archive_inner_content_image_padding_left_layout2' => '0',
			'betterdocs_archive_inner_content_image_margin_layout2' => '',
			'betterdocs_archive_inner_content_image_margin_top_layout2' => '0',
			'betterdocs_archive_inner_content_image_margin_right_layout2' => '0',
			'betterdocs_archive_inner_content_image_margin_left_layout2' => '0',
			'betterdocs_archive_inner_content_image_margin_bottom_layout2' => '-100',
			'betterdocs_sidebar_seperator_layout6' => '',
			'betterdocs_sidebar_term_item_content_seperator_layout6' => '',
			'betterdocs_sidebar_term_list_item_color_layout6' => '#464646',
			'betterdocs_sidebar_term_list_item_hover_color_layout6' => '#528ffe',
			'betterdocs_sidebar_term_list_item_font_size_layout6' => '14',
			'betterdocs_sidebar_term_list_item_icon_color_layout6' => '#000000',
			'betterdocs_sidebar_term_list_item_icon_size_layout6' => '15',
			'betterdocs_sidebar_term_list_item_padding_layout6' => '',
			'betterdocs_sidebar_term_list_item_padding_top_layout6' => '10',
			'betterdocs_sidebar_term_list_item_padding_right_layout6' => '10',
			'betterdocs_sidebar_term_list_item_padding_bottom_layout6' => '10',
			'betterdocs_sidebar_term_list_item_padding_left_layout6' => '10',
			'betterdocs_sidebar_term_list_active_item_color_layout6' => '#528ffe',
			'betterdocs_doc_page_cat_title_font_size_layout6' => '30',
			'betterdocs_doc_page_item_count_font_size_layout6' => '17',
			'betterdocs_archive_other_categories_seperator' => '',
			'betterdocs_archive_other_categories_heading_text' => 'Other Categories',
			'betterdocs_archive_other_categories_load_more_text' => 'Load More',
			'betterdocs_archive_other_categories_title_color' => '#15063F',
			'betterdocs_archive_other_categories_title_hover_color' => '#15063F',
			'betterdocs_archive_other_categories_title_font_weight' => '500',
			'betterdocs_archive_other_categories_title_font_size' => '22',
			'betterdocs_archive_other_categories_title_line_height' => '36',
			'betterdocs_archive_other_categories_title_padding' => '',
			'betterdocs_archive_other_categories_title_padding_top' => '0',
			'betterdocs_archive_other_categories_title_padding_right' => '0',
			'betterdocs_archive_other_categories_title_padding_bottom' => '0',
			'betterdocs_archive_other_categories_title_padding_left' => '0',
			'betterdocs_archive_other_categories_title_margin' => '',
			'betterdocs_archive_other_categories_title_margin_top' => '0',
			'betterdocs_archive_other_categories_title_margin_right' => '0',
			'betterdocs_archive_other_categories_title_margin_bottom' => '0',
			'betterdocs_archive_other_categories_title_margin_left' => '0',
			'betterdocs_archive_other_categories_count_color' => '#FFFFFF',
			'betterdocs_archive_other_categories_count_back_color' => '#591FFF',
			'betterdocs_archive_other_categories_count_back_color_hover' => '#591FFF',
			'betterdocs_archive_other_categories_count_line_height' => '28',
			'betterdocs_archive_other_categories_count_font_weight' => '500',
			'betterdocs_archive_other_categories_count_font_size' => '17',
			'betterdocs_archive_other_categories_count_border_radius' => '',
			'betterdocs_archive_other_categories_count_border_radius_topleft' => '90',
			'betterdocs_archive_other_categories_count_border_radius_topright' => '90',
			'betterdocs_archive_other_categories_count_border_radius_bottomright' => '90',
			'betterdocs_archive_other_categories_count_border_radius_bottomleft' => '90',
			'betterdocs_archive_other_categories_count_padding' => '',
			'betterdocs_archive_other_categories_count_padding_top' => '0',
			'betterdocs_archive_other_categories_count_padding_right' => '10',
			'betterdocs_archive_other_categories_count_padding_bottom' => '0',
			'betterdocs_archive_other_categories_count_padding_left' => '10',
			'betterdocs_archive_other_categories_count_margin' => '',
			'betterdocs_archive_other_categories_count_margin_top' => '0',
			'betterdocs_archive_other_categories_count_margin_right' => '0',
			'betterdocs_archive_other_categories_count_margin_bottom' => '0',
			'betterdocs_archive_other_categories_count_margin_left' => '10',
			'betterdocs_archive_other_categories_description_color' => '#7B7B7B',
			'betterdocs_archive_other_categories_description_font_weight' => '400',
			'betterdocs_archive_other_categories_description_font_size' => '15',
			'betterdocs_archive_other_categories_description_line_height' => '27',
			'betterdocs_archive_other_categories_description_padding' => '',
			'betterdocs_archive_other_categories_description_padding_top' => '10',
			'betterdocs_archive_other_categories_description_padding_right' => '0',
			'betterdocs_archive_other_categories_description_padding_bottom' => '0',
			'betterdocs_archive_other_categories_description_padding_left' => '0',
			'betterdocs_archive_other_categories_button_color' => '#FFFFFF',
			'betterdocs_archive_other_categories_button_back_color' => '#591FFF',
			'betterdocs_archive_other_categories_button_back_color_hover' => '#591FFF',
			'betterdocs_archive_other_categories_button_font_weight' => '500',
			'betterdocs_archive_other_categories_button_font_size' => '18',
			'betterdocs_archive_other_categories_button_font_line_height' => '36',
			'betterdocs_archive_other_categories_button_border_radius' => '',
			'betterdocs_archive_other_categories_button_border_radius_top_left' => '80',
			'betterdocs_archive_other_categories_button_border_radius_top_right' => '80',
			'betterdocs_archive_other_categories_button_border_radius_bottom_left' => '80',
			'betterdocs_archive_other_categories_button_border_radius_bottom_right' => '80',
			'betterdocs_archive_other_categories_button_padding' => '',
			'betterdocs_archive_other_categories_button_padding_top' => '12',
			'betterdocs_archive_other_categories_button_padding_right' => '30',
			'betterdocs_archive_other_categories_button_padding_bottom' => '13',
			'betterdocs_archive_other_categories_button_padding_left' => '29',
			'betterdocs_faq_switch_mkb' => true,
			'betterdocs_select_specific_faq_mkb' => 'all',
			'betterdocs_select_faq_template_mkb' => 'layout-1',
			'betterdocs_faq_title_text_mkb' => esc_html__('Frequently Asked Questions', 'betterdocs-pro'),
			'betterdocs_faq_title_margin_mkb_layout_1' => array(
				'input1'  	=> 0,
				'input2'  	=> 0,
				'input3'  	=> 0,
				'input4'  	=> 0,
			),
			'betterdocs_faq_title_color_mkb_layout_1' => '#15063F',
			'betterdocs_faq_title_font_size_mkb_layout_1' => '30',
			'betterdocs_faq_category_title_color_mkb_layout_1' => '#15063F',
			'betterdocs_faq_category_name_font_size_mkb_layout_1' => '25',
			'betterdocs_faq_category_name_padding_mkb_layout_1' => array(
				'input1'  	=> 20,
				'input2'  	=> 20,
				'input3'  	=> 20,
				'input4'  	=> 20,
			),
			'betterdocs_faq_list_color_mkb_layout_1' => '#2f3b48',
			'betterdocs_faq_list_background_color_mkb_layout_1' => '#f0f1f5',
			'betterdocs_faq_list_content_background_color_mkb_layout_1' => '#fbfcff',
			'betterdocs_faq_list_font_size_mkb_layout_1' => '17',
			'betterdocs_faq_list_padding_mkb_layout_1' => array(
				'input1'  	=> 20,
				'input2'  	=> 20,
				'input3'  	=> 20,
				'input4'  	=> 20,
			),
			'betterdocs_faq_category_title_color_mkb_layout_2' => '#15063F',
			'betterdocs_faq_category_name_font_size_mkb_layout_2' => '25',
			'betterdocs_faq_category_name_padding_mkb_layout_2' => array(
				'input1'  	=> 20,
				'input2'  	=> 20,
				'input3'  	=> 20,
				'input4'  	=> 20,
			),
			'betterdocs_faq_list_color_mkb_layout_2' => '#2f3b48',
			'betterdocs_faq_list_background_color_mkb_layout_2' => '#fff',
			'betterdocs_faq_list_content_background_color_mkb_layout_2' => '#fff',
			'betterdocs_faq_list_font_size_mkb_layout_2' => '17',
			'betterdocs_faq_list_padding_mkb_layout_2' => array(
				'input1'  	=> 20,
				'input2'  	=> 20,
				'input3'  	=> 20,
				'input4'  	=> 20
			),
			'betterdocs_faq_list_content_font_size_mkb_layout_1' => '15',
			'betterdocs_faq_list_content_font_size_mkb_layout_2' => '15',
			'betterdocs_faq_list_content_color_mkb_layout_1'  => '#7B7B7B',
			'betterdocs_faq_list_content_color_mkb_layout_2'  => '#7B7B7B',
			'betterdocs_faq_section_mkb_seperator' => ''
		);
		return apply_filters( 'betterdocs_option_defaults_pro', $betterdocs_defaults_pro );
	}
endif;

/**
*  Get default customizer option
*/
if ( ! function_exists( 'betterdocs_get_option_pro' ) ) :

	/**
	 * Get default customizer option
	 * @param string $key Option key.
	 * @return mixed Option value.
	 */
	function betterdocs_get_option_pro( $key ) {

		$default_options = betterdocs_get_option_defaults_pro();

		if ( empty( $key ) ) {
			return;
		}

		$theme_options = (array)get_theme_mods( 'theme_options' );
		$theme_options = wp_parse_args( $theme_options, $default_options );

		$value = null;

		if ( isset( $theme_options[ $key ] ) ) {
			$value = $theme_options[ $key ];
		}

		return $value;
	}

endif;


if( ! function_exists( 'betterdocs_generate_defaults_pro' ) ) : 

	function betterdocs_generate_defaults_pro(){

		$default_options = betterdocs_get_option_defaults_pro();
		$saved_options = get_theme_mods();

		$returned = [];

		if( ! $saved_options ) {
			return;
		}

		foreach( $default_options as $key => $option ) {
			if( array_key_exists( $key, $saved_options ) ) {
				$returned[ $key ] = get_theme_mod( $key );				
			} else {
				switch ( $key ) {
					default:
						$returned[ $key ] = $default_options[ $key ];
						break;
				}
			}
		}

		return $returned;

	}

endif;

if( ! function_exists( 'betterdocs_generate_output_pro' ) ) : 

	function betterdocs_generate_output_pro(){

		$default_options = betterdocs_get_option_defaults_pro();

		$returned = [];
		
		foreach( $default_options as $key => $option ) {
			$returned[ $key ] = get_theme_mod( $key, $option );	
		}

		return $returned;

	}

endif;


if( ! function_exists( 'betterdocs_dimension_generator_pro' ) ) :
    function betterdocs_dimension_generator_pro($key, $style_attr, $measure = 'px', $important = false) {
        
        $important = $important ? ' !important' : '';
        $saved_options = get_theme_mods();
        if( is_array($saved_options) && array_key_exists( $key, $saved_options ) ) {
            $valueArr = (array) json_decode(betterdocs_get_option($key));
        } else {
            $default = betterdocs_get_option_defaults_pro();
            $valueArr = isset( $default[$key] ) ? $default[$key] : '';
        }

        $dimensionArr = [];
        $dimensionAttr = '';
        $input1 = '';
        $input2 = '';
        $input3 = '';
        $input4 = '';
        $measure = isset($valueArr['data_unit']) ? $valueArr['data_unit'] : $measure;

        if ( $valueArr['input1'] !== '' ) {
            $input1 = $valueArr['input1'] . $measure;
        } else {
            $input1 = '0' . $measure;
        }
        if ( $input1 !== '' ) {
            $dimensionArr[] = $input1;
        }
        
        if ( $valueArr['input2'] !== '' ) {
            $input2 = $valueArr['input2'] . $measure;
        } else {
            $input2 = '0' . $measure;
        }
        if ( $input2 !== '' ) {
            $dimensionArr[] = $input2;
        }
        
        if ( $valueArr['input3'] !== '' ) {
            $input3 = $valueArr['input3'] . $measure;
        } else {
            $input3 = '0' . $measure;
        }
        if ( $input3 !== '' ) {
            $dimensionArr[] = $input3;
        }
        
        if ( $valueArr['input4'] !== '' ) {
            $input4 = $valueArr['input4'] . $measure;
        } else {
            $input4 = '0' . $measure;
        }
        if ( $input4 !== '' ) {
            $dimensionArr[] = $input4;
        }

        if ( count($dimensionArr) > 0 ) {
            $dimensionAttr = "{$style_attr}: " . implode(' ', $dimensionArr) . $important . ";";
        }
        
        return $dimensionAttr;
    }
endif;