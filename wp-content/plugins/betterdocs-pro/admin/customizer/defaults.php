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
			'betterdocs_reactions_title' => '',
			'betterdocs_post_reactions' => true,
			'betterdocs_post_reactions_text' => esc_html__('What are your feelings', 'betterdocs-pro'),
			'betterdocs_post_reactions_text_color' => '#566e8b',
			'betterdocs_post_reactions_icon_color' => '#00b88a',
			'betterdocs_post_reactions_icon_svg_color' => '#fff',
			'betterdocs_post_reactions_icon_hover_bg_color' => '#fff',
			'betterdocs_post_reactions_icon_hover_svg_color' => '#00b88a',
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
			'betterdocs_popular_keyword_border_radius_right_bottom' => '0'
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

 ?>