<?php
/**
 *
 * @package BetterDocs
 */

// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;


if ( ! function_exists( 'betterdocs_get_option_defaults' ) ) :
/**
 * Set default options
 */
function betterdocs_get_option_defaults() {

	$betterdocs_defaults = array(
		'betterdocs_docs_layout_select' => 'layout-1',
		'betterdocs_single_layout_select' => 'layout-1',
		'betterdocs_doc_page_background_color' => '#ffffff',
		'betterdocs_doc_page_background_image' => '',
		'betterdocs_doc_page_background_property' => '',
		'betterdocs_doc_page_background_size' => '',
		'betterdocs_doc_page_background_repeat' => '',
		'betterdocs_doc_page_background_attachment' => '',
		'betterdocs_doc_page_background_position' => '',
		'betterdocs_doc_page_content_padding' => '',
		'betterdocs_doc_page_content_padding_top' => '50',
		'betterdocs_doc_page_content_padding_right' => '0',
		'betterdocs_doc_page_content_padding_bottom' => '50',
		'betterdocs_doc_page_content_padding_left' => '0',
		'betterdocs_doc_page_content_width' => '100',
		'betterdocs_doc_page_content_max_width' => '1600',
		'betterdocs_doc_page_column_settings' => '',
		'betterdocs_doc_page_column_space' => '15',
		'betterdocs_doc_page_column_padding' => '',
		'betterdocs_doc_page_column_padding_top' => '20',
		'betterdocs_doc_page_column_padding_right' => '20',
		'betterdocs_doc_page_column_padding_bottom' => '20',
		'betterdocs_doc_page_column_padding_left' => '20',
		'betterdocs_doc_page_column_bg_color' => '#fff',
		'betterdocs_doc_page_column_bg_color2' => '#f8f8fc',
		'betterdocs_doc_page_column_hover_bg_color' => '#fff',
		'betterdocs_doc_page_column_borderr' => '',
		'betterdocs_doc_page_column_borderr_topleft' => '5',
		'betterdocs_doc_page_column_borderr_topright' => '5',
		'betterdocs_doc_page_column_borderr_bottomright' => '5',
		'betterdocs_doc_page_column_borderr_bottomleft' => '5',
		'betterdocs_doc_page_column_content_space' => '',
		'betterdocs_doc_page_column_content_space_image' => '20',
		'betterdocs_doc_page_column_content_space_title' => '15',
		'betterdocs_doc_page_column_content_space_desc' => '15',
		'betterdocs_doc_page_column_content_space_counter' => '0',
		'betterdocs_doc_page_cat_icon_size_layout1' => '32',
		'betterdocs_doc_page_cat_icon_size_layout2' => '80',
		'betterdocs_doc_page_cat_title_font_size' => '20',
		'betterdocs_doc_page_cat_title_color' => '#528ffe',
		'betterdocs_doc_page_cat_title_color2' => '#333333',
		'betterdocs_doc_page_cat_title_hover_color' => '',
		'betterdocs_doc_page_cat_title_border_color' => '#528ffe',
		'betterdocs_item_counter_title' => '',
		'betterdocs_doc_page_item_count_color' => '#ffffff',
		'betterdocs_doc_page_item_count_color_layout2' => '#707070',
		'betterdocs_doc_page_cat_desc' => false,
		'betterdocs_doc_page_cat_desc_color' => '#566e8b',
		'betterdocs_doc_page_item_count_bg_color' => '#528ffe',
		'betterdocs_doc_page_item_count_inner_bg_color' => 'rgba(82,143,254,0.44)',
		'betterdocs_doc_page_item_counter_size' => '30',
		'betterdocs_doc_page_item_count_font_size' => '15',
		'betterdocs_doc_page_article_list_settings' => '',
		'betterdocs_doc_page_article_list_color' => '#566e8b',
		'betterdocs_doc_page_article_list_hover_color' => '#566e8b',
		'betterdocs_doc_page_article_list_bg_color' => '#ffffff',
		'betterdocs_doc_page_article_list_font_size' => '15',
		'betterdocs_doc_page_list_icon_color' => '#566e8b',
		'betterdocs_doc_page_list_icon_font_size' => '15',
		'betterdocs_doc_page_article_list_margin' => '',
		'betterdocs_doc_page_article_list_margin_top' => '10',
		'betterdocs_doc_page_article_list_margin_right' => '10',
		'betterdocs_doc_page_article_list_margin_bottom' => '10',
		'betterdocs_doc_page_article_list_margin_left' => '10',
		'betterdocs_doc_page_article_subcategory_color' => '#566e8b',
		'betterdocs_doc_page_article_subcategory_hover_color' => '#566e8b',
		'betterdocs_doc_page_article_subcategory_font_size' => '17',
		'betterdocs_doc_page_subcategory_icon_color' => '#566e8b',
		'betterdocs_doc_page_subcategory_icon_font_size' => '15',
		'betterdocs_doc_page_explore_btn' => '',
		'betterdocs_doc_page_explore_btn_bg_color' => '#ffffff',
		'betterdocs_doc_page_explore_btn_color' => '#528ffe',
		'betterdocs_doc_page_explore_btn_border_color' => '#528ffe',
		'betterdocs_doc_page_explore_btn_font_size' => '16',
		'betterdocs_doc_page_explore_btn_padding' => '',
		'betterdocs_doc_page_explore_btn_padding_top' => '10',
		'betterdocs_doc_page_explore_btn_padding_right' => '20',
		'betterdocs_doc_page_explore_btn_padding_bottom' => '10',
		'betterdocs_doc_page_explore_btn_padding_left' => '20',
		'betterdocs_doc_page_explore_btn_borderr' => '',
		'betterdocs_doc_page_explore_btn_borderr_topleft' => '50',
		'betterdocs_doc_page_explore_btn_borderr_topright' => '50',
		'betterdocs_doc_page_explore_btn_borderr_bottomright' => '50',
		'betterdocs_doc_page_explore_btn_borderr_bottomleft' => '50',
		'betterdocs_doc_page_explore_btn_hover_bg_color' => '#528ffe',
		'betterdocs_doc_page_explore_btn_hover_color' => '#fff',
		'betterdocs_doc_page_explore_btn_hover_border_color' => '#528ffe',
		'betterdocs_doc_single_content_area_bg_color' => '',
		'betterdocs_doc_single_content_area_padding' => '',
		'betterdocs_doc_single_content_area_padding_top' => '30',
		'betterdocs_doc_single_content_area_padding_right' => '25',
		'betterdocs_doc_single_content_area_padding_bottom' => '30',
		'betterdocs_doc_single_content_area_padding_left' => '25',
		'betterdocs_doc_single_post_content_padding' => '',
		'betterdocs_doc_single_post_content_padding_top' => '20',
		'betterdocs_doc_single_post_content_padding_right' => '20',
		'betterdocs_doc_single_post_content_padding_bottom' => '20',
		'betterdocs_doc_single_post_content_padding_left' => '20',
		'betterdocs_doc_single_2_post_content_padding' => '',
		'betterdocs_doc_single_2_post_content_padding_top' => '0',
		'betterdocs_doc_single_2_post_content_padding_right' => '0',
		'betterdocs_doc_single_2_post_content_padding_bottom' => '0',
		'betterdocs_doc_single_2_post_content_padding_left' => '0',
		'betterdocs_doc_single_3_post_content_padding' => '',
		'betterdocs_doc_single_3_post_content_padding_top' => '0',
		'betterdocs_doc_single_3_post_content_padding_right' => '0',
		'betterdocs_doc_single_3_post_content_padding_bottom' => '0',
		'betterdocs_doc_single_3_post_content_padding_left' => '0',
		'betterdocs_single_doc_title' => '',
		'betterdocs_single_doc_title_font_size' => '36',
		'betterdocs_single_doc_title_color' => '#3f5876',
		'betterdocs_single_doc_breadcrumb' => '',
		'betterdocs_single_doc_breadcrumbs_font_size' => '16',
		'betterdocs_single_doc_breadcrumb_color' => '#566e8b',
		'betterdocs_single_doc_breadcrumb_hover_color' => '#566e8b',
		'betterdocs_single_doc_breadcrumb_speretor_color' => '#566e8b',
		'betterdocs_single_doc_breadcrumb_active_item_color' => '#528fff',
		'betterdocs_doc_single_toc_title' => '',
		'betterdocs_sticky_toc_width' => '320',
		'betterdocs_sticky_toc_zindex' => '2',
		'betterdocs_sticky_toc_margin_top' => '0',
		'betterdocs_toc_bg_color' => '#fff',
		'betterdocs_doc_single_toc_padding' => '',
		'betterdocs_doc_single_toc_padding_top' => '20',
		'betterdocs_doc_single_toc_padding_right' => '25',
		'betterdocs_doc_single_toc_padding_bottom' => '20',
		'betterdocs_doc_single_toc_padding_left' => '20',
		'betterdocs_toc_title_color' => '#3f5876',
		'betterdocs_toc_title_font_size' => '18',
		'betterdocs_toc_list_item_color' => '#566e8b',
		'betterdocs_toc_list_item_hover_color' => '#528fff',
		'betterdocs_toc_active_item_color' => '#528fff',
		'betterdocs_toc_list_item_font_size' => '14',
		'betterdocs_doc_single_toc_list_margin' => '',
		'betterdocs_doc_single_toc_list_margin_top' => '5',
		'betterdocs_doc_single_toc_list_margin_right' => '0',
		'betterdocs_doc_single_toc_list_margin_bottom' => '5',
		'betterdocs_doc_single_toc_list_margin_left' => '0',
		'betterdocs_toc_list_number_color' => '#566e8b',
		'betterdocs_toc_list_number_font_size' => '12',
		'betterdocs_toc_margin_bottom' => '20',
		'betterdocs_doc_single_entry_content' => '',
		'betterdocs_single_content_font_size' => '16',
		'betterdocs_single_content_font_color' => '#4d4d4d',
		'betterdocs_doc_single_entry_footer' => '',
		'betterdocs_social_share_title' => '',
		'betterdocs_post_social_share' => true,
		'betterdocs_social_sharing_text' => esc_html__('Share This Article :', 'betterdocs'),
		'betterdocs_post_social_share_text_color' => '#566e8b',
		'betterdocs_post_social_share_facebook' => true,
		'betterdocs_post_social_share_twitter' => true,
		'betterdocs_post_social_share_linkedin' => true,
		'betterdocs_post_social_share_pinterest' => true,
		'betterdocs_single_doc_feedback_icon_font_size' => '26',
		'betterdocs_single_doc_feedback_icon' => '',
		'betterdocs_single_doc_feedback_link_color' => '#566e8b',
		'betterdocs_single_doc_feedback_link_hover_color' => '#566e8b',
		'betterdocs_single_doc_feedback_link_font_size' => '15',
		'betterdocs_single_doc_navigation_color' => '#3f5876',
		'betterdocs_single_doc_navigation_hover_color' => '#3f5876',
		'betterdocs_single_doc_navigation_font_size' => '16',
		'betterdocs_single_doc_navigation_arrow_color' => '#5edf8e',
		'betterdocs_single_doc_navigation_arrow_font_size' => '16',
		'betterdocs_single_doc_lu_time_color' => '#566e8b',
		'betterdocs_single_doc_lu_time_font_size' => '14',
		'betterdocs_single_doc_powered_by_color' => '#201d3a',
		'betterdocs_single_doc_powered_by_font_size' => '14',
		'betterdocs_single_doc_powered_by_link_color' => '#528fff',
		'betterdocs_sidebar_bg_color' => '#ffffff',
		'betterdocs_sidebar_padding' => '',
		'betterdocs_sidebar_padding_top' => '0',
		'betterdocs_sidebar_padding_right' => '0',
		'betterdocs_sidebar_padding_bottom' => '0',
		'betterdocs_sidebar_padding_left' => '0',
		'betterdocs_sidebar_borderr' => '',
		'betterdocs_sidebar_borderr_topleft' => '5',
		'betterdocs_sidebar_borderr_topright' => '5',
		'betterdocs_sidebar_borderr_bottomright' => '5',
		'betterdocs_sidebar_borderr_bottomleft' => '5',
		'betterdocs_sidebar_title' => '',
		'betterdocs_sidebar_icon_size' => '24',
		'betterdocs_sidebar_title_bg_color' => '#ffffff',
		'betterdocs_sidebar_title_color' => '#3f5876',
		'betterdocs_sidebar_title_hover_color' => '#3f5876',
		'betterdocs_sidebar_active_title_color' => '#3f5876',
		'betterdocs_sidebar_active_cat_background_color' => 'rgba(90, 148, 255, .1)',
		'betterdocs_sidebar_active_cat_border_color' => '#528fff',
		'betterdocs_sidebar_title_font_size' => '16',
		'betterdocs_sidebar_title_padding' => '',
		'betterdocs_sidebar_title_padding_top' => '10',
		'betterdocs_sidebar_title_padding_right' => '15',
		'betterdocs_sidebar_title_padding_bottom' => '10',
		'betterdocs_sidebar_title_padding_left' => '15',
		'betterdocs_sidebar_title_margin' => '',
		'betterdocs_sidebar_title_margin_top' => '5',
		'betterdocs_sidebar_title_margin_right' => '0',
		'betterdocs_sidebar_title_margin_bottom' => '5',
		'betterdocs_sidebar_title_margin_left' => '0',
		'betterdocs_sidebar_content' => '',
		'betterdocs_sidbebar_item_list_bg_color' => '#ffffff',
		'betterdocs_sidebar_item_counter_title' => '',
		'betterdocs_sidbebar_item_count_bg_color' => '#528ffe',
		'betterdocs_sidbebar_item_count_inner_bg_color' => 'rgba(82, 143, 255, 0.2)',
		'betterdocs_sidebar_item_counter_size' => '30',
		'betterdocs_sidebar_item_count_color' => '#ffffff',
		'betterdocs_sidebat_item_count_font_size' => '12',
		'betterdocs_sidebar_list_item_color' => '#566e8b',
		'betterdocs_sidebar_list_item_hover_color' => '#528fff',
		'betterdocs_sidebar_active_list_item_color' => '#528fff',
		'betterdocs_sidebar_list_item_font_size' => '14',
		'betterdocs_sidebar_list_item_margin' => '',
		'betterdocs_sidebar_list_item_margin_top' => '10',
		'betterdocs_sidebar_list_item_margin_right' => '10',
		'betterdocs_sidebar_list_item_margin_bottom' => '10',
		'betterdocs_sidebar_list_item_margin_left' => '10',
		'betterdocs_sidebar_list_icon_color' => '#566e8b',
		'betterdocs_sidebar_list_icon_font_size' => '14',
		'betterdocs_archive_page_background_color' => '',
		'betterdocs_archive_page_background_image' => '',
		'betterdocs_archive_page_background_property' => '',
		'betterdocs_archive_page_background_size' => '',
		'betterdocs_archive_page_background_repeat' => '',
		'betterdocs_archive_page_background_attachment' => '',
		'betterdocs_archive_page_background_position' => '',
		'betterdocs_archive_content_area_settings' => '',
		'betterdocs_archive_content_background_color' => '#ffffff',
		'betterdocs_archive_content_margin' => '',
		'betterdocs_archive_content_margin_top' => '0',
		'betterdocs_archive_content_margin_right' => '0',
		'betterdocs_archive_content_margin_bottom' => '0',
		'betterdocs_archive_content_margin_left' => '0',
		'betterdocs_archive_content_padding' => '',
		'betterdocs_archive_content_padding_top' => '30',
		'betterdocs_archive_content_padding_right' => '30',
		'betterdocs_archive_content_padding_bottom' => '30',
		'betterdocs_archive_content_padding_left' => '30',
		'betterdocs_archive_content_border_radius' => '5',
		'betterdocs_archive_title_color' => '#566e8b',
		'betterdocs_archive_title_margin' => '',
		'betterdocs_archive_title_margin_top' => '0',
		'betterdocs_archive_title_margin_right' => '0',
		'betterdocs_archive_title_margin_bottom' => '20',
		'betterdocs_archive_title_margin_left' => '0',
		'betterdocs_archive_title_font_size' => '20',
		'betterdocs_archive_description_color' => '#566e8b',
		'betterdocs_archive_description_margin' => '',
		'betterdocs_archive_description_margin_top' => '0',
		'betterdocs_archive_description_margin_right' => '0',
		'betterdocs_archive_description_margin_bottom' => '20',
		'betterdocs_archive_description_margin_left' => '0',
		'betterdocs_archive_description_font_size' => '14',
		'betterdocs_archive_list_icon_color' => '#566e8b',
		'betterdocs_archive_list_icon_font_size' => '16',
		'betterdocs_archive_list_item_color' => '#566e8b',
		'betterdocs_archive_list_item_hover_color' => '#528ffe',
		'betterdocs_archive_list_item_font_size' => '14',
		'betterdocs_archive_article_list_margin' => '',
		'betterdocs_archive_article_list_margin_top' => '10',
		'betterdocs_archive_article_list_margin_right' => '0',
		'betterdocs_archive_article_list_margin_bottom' => '10',
		'betterdocs_archive_article_list_margin_left' => '0',
		'betterdocs_live_search_heading_switch' => false,
		'betterdocs_live_search_heading' => '',
		'betterdocs_live_search_heading_font_size' => 40,
		'betterdocs_live_search_subheading' => '',
		'betterdocs_live_search_subheading_font_size' => 16,
		'betterdocs_live_search_heading_font_color' => '#566e8b',
		'betterdocs_search_heading_margin' => '',
		'betterdocs_search_heading_margin_top' => '0',
		'betterdocs_search_heading_margin_right' => '0',
		'betterdocs_search_heading_margin_bottom' => '20',
		'betterdocs_search_heading_margin_left' => '0',
		'betterdocs_live_search_subheading_font_color' => '#566e8b',
		'betterdocs_search_subheading_margin' => '',
		'betterdocs_search_subheading_margin_top' => '0',
		'betterdocs_search_subheading_margin_right' => '0',
		'betterdocs_search_subheading_margin_bottom' => '20',
		'betterdocs_search_subheading_margin_left' => '0',
		'betterdocs_live_search_background_color' => '#f7f7f7',
		'betterdocs_live_search_background_image' => '',
		'betterdocs_live_search_background_property' => '',
		'betterdocs_live_search_background_size' => '',
		'betterdocs_live_search_background_repeat' => '',
		'betterdocs_live_search_background_attachment' => '',
		'betterdocs_live_search_background_position' => '',
		'betterdocs_live_search_padding' => '',
		'betterdocs_live_search_padding_top' => '50',
		'betterdocs_live_search_padding_right' => '20',
		'betterdocs_live_search_padding_bottom' => '50',
		'betterdocs_live_search_padding_left' => '20',
		'betterdocs_search_field_settings' => '',
		'betterdocs_search_field_background_color' => '#ffffff',
		'betterdocs_search_field_font_size' => '18',
		'betterdocs_search_field_color' => '#595959',
		'betterdocs_search_field_padding' => '',
		'betterdocs_search_field_padding_top' => '22',
		'betterdocs_search_field_padding_right' => '15',
		'betterdocs_search_field_padding_bottom' => '22',
		'betterdocs_search_field_padding_left' => '15',
		'betterdocs_search_field_border_radius' => '8',
		'betterdocs_search_icon_color' => '#444b54',
		'betterdocs_search_icon_hover_color' => '#444b54',
		'betterdocs_search_close_icon_color' => '#ff697b',
		'betterdocs_search_close_icon_border_color' => '#444b54',
		'betterdocs_search_icon_size' => '30',
		'betterdocs_search_result_settings' => '',
		'betterdocs_search_result_width' => '100',
		'betterdocs_search_result_max_width' => '800',
		'betterdocs_search_result_background_color' => '#fff',
		'betterdocs_search_result_border_color' => '#f1f1f1',
		'betterdocs_search_result_item_font_size' => '16',
		'betterdocs_search_result_item_font_color' => '#444444',
		'betterdocs_search_result_item_padding' => '',
		'betterdocs_search_result_item_padding_top' => '10',
		'betterdocs_search_result_item_padding_right' => '10',
		'betterdocs_search_result_item_padding_bottom' => '10',
		'betterdocs_search_result_item_padding_left' => '10',	
		'betterdocs_search_result_item_border_color' => '#f5f5f5',	
		'betterdocs_search_result_item_hover_font_color' => '#444444',
		'betterdocs_search_result_item_hover_background_color' => '#f5f5f5'	
	);
	
	return apply_filters( 'betterdocs_option_defaults', $betterdocs_defaults );
}
endif;


/**
*  Get default customizer option
*/
if ( ! function_exists( 'betterdocs_get_option' ) ) :

	/**
	 * Get default customizer option
	 * @param string $key Option key.
	 * @return mixed Option value.
	 */
	function betterdocs_get_option( $key ) {

		$default_options = betterdocs_get_option_defaults();

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


if( ! function_exists( 'betterdocs_generate_defaults' ) ) : 

	function betterdocs_generate_defaults(){

		$default_options = betterdocs_get_option_defaults();
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

if( ! function_exists( 'betterdocs_generate_output' ) ) : 

	function betterdocs_generate_output(){

		$default_options = betterdocs_get_option_defaults();

		$returned = [];
		
		foreach( $default_options as $key => $option ) {
			$returned[ $key ] = get_theme_mod( $key, $option );	
		}

		return $returned;

	}

endif;