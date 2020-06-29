<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * WPBakery Page Builder Shortcodes settings Lazy mapping
 *
 * @package VPBakeryVisualComposer
 *
 */
$vc_config_path = vc_path_dir( 'CONFIG_DIR' );
vc_lean_map( 'vc_row', null, $vc_config_path . '/containers/shortcode-vc-row.php' );
vc_lean_map( 'vc_row_inner', null, $vc_config_path . '/containers/shortcode-vc-row-inner.php' );
vc_lean_map( 'vc_column', null, $vc_config_path . '/containers/shortcode-vc-column.php' );
vc_lean_map( 'vc_column_inner', null, $vc_config_path . '/containers/shortcode-vc-column-inner.php' );
vc_lean_map( 'vc_column_text', null, $vc_config_path . '/content/shortcode-vc-column-text.php' );
vc_lean_map( 'vc_section', null, $vc_config_path . '/containers/shortcode-vc-section.php' );
vc_lean_map( 'vc_icon', null, $vc_config_path . '/content/shortcode-vc-icon.php' );
vc_lean_map( 'vc_separator', null, $vc_config_path . '/content/shortcode-vc-separator.php' );
vc_lean_map( 'vc_zigzag', null, $vc_config_path . '/content/shortcode-vc-zigzag.php' );
vc_lean_map( 'vc_text_separator', null, $vc_config_path . '/content/shortcode-vc-text-separator.php' );
vc_lean_map( 'vc_message', null, $vc_config_path . '/content/shortcode-vc-message.php' );
vc_lean_map( 'vc_hoverbox', null, $vc_config_path . '/content/shortcode-vc-hoverbox.php' );

vc_lean_map( 'vc_facebook', null, $vc_config_path . '/social/shortcode-vc-facebook.php' );
vc_lean_map( 'vc_tweetmeme', null, $vc_config_path . '/social/shortcode-vc-tweetmeme.php' );
vc_lean_map( 'vc_googleplus', null, $vc_config_path . '/deprecated/shortcode-vc-googleplus.php' );
vc_lean_map( 'vc_pinterest', null, $vc_config_path . '/social/shortcode-vc-pinterest.php' );

vc_lean_map( 'vc_toggle', null, $vc_config_path . '/content/shortcode-vc-toggle.php' );
vc_lean_map( 'vc_single_image', null, $vc_config_path . '/content/shortcode-vc-single-image.php' );
vc_lean_map( 'vc_gallery', null, $vc_config_path . '/content/shortcode-vc-gallery.php' );
vc_lean_map( 'vc_images_carousel', null, $vc_config_path . '/content/shortcode-vc-images-carousel.php' );

vc_lean_map( 'vc_tta_tabs', null, $vc_config_path . '/tta/shortcode-vc-tta-tabs.php' );
vc_lean_map( 'vc_tta_tour', null, $vc_config_path . '/tta/shortcode-vc-tta-tour.php' );
vc_lean_map( 'vc_tta_accordion', null, $vc_config_path . '/tta/shortcode-vc-tta-accordion.php' );
vc_lean_map( 'vc_tta_pageable', null, $vc_config_path . '/tta/shortcode-vc-tta-pageable.php' );
vc_lean_map( 'vc_tta_section', null, $vc_config_path . '/tta/shortcode-vc-tta-section.php' );

vc_lean_map( 'vc_custom_heading', null, $vc_config_path . '/content/shortcode-vc-custom-heading.php' );

vc_lean_map( 'vc_btn', null, $vc_config_path . '/buttons/shortcode-vc-btn.php' );
vc_lean_map( 'vc_cta', null, $vc_config_path . '/buttons/shortcode-vc-cta.php' );

vc_lean_map( 'vc_widget_sidebar', null, $vc_config_path . '/structure/shortcode-vc-widget-sidebar.php' );
vc_lean_map( 'vc_posts_slider', null, $vc_config_path . '/content/shortcode-vc-posts-slider.php' );
vc_lean_map( 'vc_video', null, $vc_config_path . '/content/shortcode-vc-video.php' );
vc_lean_map( 'vc_gmaps', null, $vc_config_path . '/content/shortcode-vc-gmaps.php' );
vc_lean_map( 'vc_raw_html', null, $vc_config_path . '/structure/shortcode-vc-raw-html.php' );
vc_lean_map( 'vc_raw_js', null, $vc_config_path . '/structure/shortcode-vc-raw-js.php' );
vc_lean_map( 'vc_flickr', null, $vc_config_path . '/content/shortcode-vc-flickr.php' );
vc_lean_map( 'vc_progress_bar', null, $vc_config_path . '/content/shortcode-vc-progress-bar.php' );
vc_lean_map( 'vc_pie', null, $vc_config_path . '/content/shortcode-vc-pie.php' );
vc_lean_map( 'vc_round_chart', null, $vc_config_path . '/content/shortcode-vc-round-chart.php' );
vc_lean_map( 'vc_line_chart', null, $vc_config_path . '/content/shortcode-vc-line-chart.php' );

vc_lean_map( 'vc_wp_search', null, $vc_config_path . '/wp/shortcode-vc-wp-search.php' );
vc_lean_map( 'vc_wp_meta', null, $vc_config_path . '/wp/shortcode-vc-wp-meta.php' );
vc_lean_map( 'vc_wp_recentcomments', null, $vc_config_path . '/wp/shortcode-vc-wp-recentcomments.php' );
vc_lean_map( 'vc_wp_calendar', null, $vc_config_path . '/wp/shortcode-vc-wp-calendar.php' );
vc_lean_map( 'vc_wp_pages', null, $vc_config_path . '/wp/shortcode-vc-wp-pages.php' );
vc_lean_map( 'vc_wp_tagcloud', null, $vc_config_path . '/wp/shortcode-vc-wp-tagcloud.php' );
vc_lean_map( 'vc_wp_custommenu', null, $vc_config_path . '/wp/shortcode-vc-wp-custommenu.php' );
vc_lean_map( 'vc_wp_text', null, $vc_config_path . '/wp/shortcode-vc-wp-text.php' );
vc_lean_map( 'vc_wp_posts', null, $vc_config_path . '/wp/shortcode-vc-wp-posts.php' );
vc_lean_map( 'vc_wp_links', null, $vc_config_path . '/wp/shortcode-vc-wp-links.php' );
vc_lean_map( 'vc_wp_categories', null, $vc_config_path . '/wp/shortcode-vc-wp-categories.php' );
vc_lean_map( 'vc_wp_archives', null, $vc_config_path . '/wp/shortcode-vc-wp-archives.php' );
vc_lean_map( 'vc_wp_rss', null, $vc_config_path . '/wp/shortcode-vc-wp-rss.php' );

vc_lean_map( 'vc_empty_space', null, $vc_config_path . '/content/shortcode-vc-empty-space.php' );

vc_lean_map( 'vc_basic_grid', null, $vc_config_path . '/grids/shortcode-vc-basic-grid.php' );
vc_lean_map( 'vc_media_grid', null, $vc_config_path . '/grids/shortcode-vc-media-grid.php' );
vc_lean_map( 'vc_masonry_grid', null, $vc_config_path . '/grids/shortcode-vc-masonry-grid.php' );
vc_lean_map( 'vc_masonry_media_grid', null, $vc_config_path . '/grids/shortcode-vc-masonry-media-grid.php' );

vc_lean_map( 'vc_tabs', null, $vc_config_path . '/deprecated/shortcode-vc-tabs.php' );
vc_lean_map( 'vc_tour', null, $vc_config_path . '/deprecated/shortcode-vc-tour.php' );
vc_lean_map( 'vc_tab', null, $vc_config_path . '/deprecated/shortcode-vc-tab.php' );
vc_lean_map( 'vc_accordion', null, $vc_config_path . '/deprecated/shortcode-vc-accordion.php' );
vc_lean_map( 'vc_accordion_tab', null, $vc_config_path . '/deprecated/shortcode-vc-accordion-tab.php' );
vc_lean_map( 'vc_button', null, $vc_config_path . '/deprecated/shortcode-vc-button.php' );
vc_lean_map( 'vc_button2', null, $vc_config_path . '/deprecated/shortcode-vc-button2.php' );
vc_lean_map( 'vc_cta_button', null, $vc_config_path . '/deprecated/shortcode-vc-cta-button.php' );
vc_lean_map( 'vc_cta_button2', null, $vc_config_path . '/deprecated/shortcode-vc-cta-button2.php' );

if ( is_admin() ) {
	add_action( 'admin_print_scripts-post.php', array(
		Vc_Shortcodes_Manager::getInstance(),
		'buildShortcodesAssets',
	), 1 );
	add_action( 'admin_print_scripts-post-new.php', array(
		Vc_Shortcodes_Manager::getInstance(),
		'buildShortcodesAssets',
	), 1 );
	add_action( 'vc-render-templates-preview-template', array(
		Vc_Shortcodes_Manager::getInstance(),
		'buildShortcodesAssets',
	), 1 );
} elseif ( vc_is_page_editable() ) {
	add_action( 'wp_head', array(
		Vc_Shortcodes_Manager::getInstance(),
		'buildShortcodesAssetsForEditable',
	) ); // @todo where these icons are used in iframe?
}

/**
 * @return mixed|void
 * @deprecated 4.12
 */
function vc_add_css_animation() {
	return vc_map_add_css_animation();
}

function vc_target_param_list() {
	return array(
		esc_html__( 'Same window', 'js_composer' ) => '_self',
		esc_html__( 'New window', 'js_composer' ) => '_blank',
	);
}

function vc_layout_sub_controls() {
	return array(
		array(
			'link_post',
			esc_html__( 'Link to post', 'js_composer' ),
		),
		array(
			'no_link',
			esc_html__( 'No link', 'js_composer' ),
		),
		array(
			'link_image',
			esc_html__( 'Link to bigger image', 'js_composer' ),
		),
	);
}

function vc_pixel_icons() {
	return array(
		array( 'vc_pixel_icon vc_pixel_icon-alert' => esc_html__( 'Alert', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-info' => esc_html__( 'Info', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-tick' => esc_html__( 'Tick', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-explanation' => esc_html__( 'Explanation', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-address_book' => esc_html__( 'Address book', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-alarm_clock' => esc_html__( 'Alarm clock', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-anchor' => esc_html__( 'Anchor', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-application_image' => esc_html__( 'Application Image', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-arrow' => esc_html__( 'Arrow', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-asterisk' => esc_html__( 'Asterisk', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-hammer' => esc_html__( 'Hammer', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-balloon' => esc_html__( 'Balloon', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-balloon_buzz' => esc_html__( 'Balloon Buzz', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-balloon_facebook' => esc_html__( 'Balloon Facebook', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-balloon_twitter' => esc_html__( 'Balloon Twitter', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-battery' => esc_html__( 'Battery', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-binocular' => esc_html__( 'Binocular', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-document_excel' => esc_html__( 'Document Excel', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-document_image' => esc_html__( 'Document Image', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-document_music' => esc_html__( 'Document Music', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-document_office' => esc_html__( 'Document Office', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-document_pdf' => esc_html__( 'Document PDF', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-document_powerpoint' => esc_html__( 'Document Powerpoint', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-document_word' => esc_html__( 'Document Word', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-bookmark' => esc_html__( 'Bookmark', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-camcorder' => esc_html__( 'Camcorder', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-camera' => esc_html__( 'Camera', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-chart' => esc_html__( 'Chart', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-chart_pie' => esc_html__( 'Chart pie', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-clock' => esc_html__( 'Clock', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-fire' => esc_html__( 'Fire', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-heart' => esc_html__( 'Heart', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-mail' => esc_html__( 'Mail', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-play' => esc_html__( 'Play', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-shield' => esc_html__( 'Shield', 'js_composer' ) ),
		array( 'vc_pixel_icon vc_pixel_icon-video' => esc_html__( 'Video', 'js_composer' ) ),
	);
}

function vc_colors_arr() {
	return array(
		esc_html__( 'Grey', 'js_composer' ) => 'wpb_button',
		esc_html__( 'Blue', 'js_composer' ) => 'btn-primary',
		esc_html__( 'Turquoise', 'js_composer' ) => 'btn-info',
		esc_html__( 'Green', 'js_composer' ) => 'btn-success',
		esc_html__( 'Orange', 'js_composer' ) => 'btn-warning',
		esc_html__( 'Red', 'js_composer' ) => 'btn-danger',
		esc_html__( 'Black', 'js_composer' ) => 'btn-inverse',
	);
}

function vc_size_arr() {
	return array(
		esc_html__( 'Regular', 'js_composer' ) => 'wpb_regularsize',
		esc_html__( 'Large', 'js_composer' ) => 'btn-large',
		esc_html__( 'Small', 'js_composer' ) => 'btn-small',
		esc_html__( 'Mini', 'js_composer' ) => 'btn-mini',
	);
}

function vc_icons_arr() {
	return array(
		esc_html__( 'None', 'js_composer' ) => 'none',
		esc_html__( 'Address book icon', 'js_composer' ) => 'wpb_address_book',
		esc_html__( 'Alarm clock icon', 'js_composer' ) => 'wpb_alarm_clock',
		esc_html__( 'Anchor icon', 'js_composer' ) => 'wpb_anchor',
		esc_html__( 'Application Image icon', 'js_composer' ) => 'wpb_application_image',
		esc_html__( 'Arrow icon', 'js_composer' ) => 'wpb_arrow',
		esc_html__( 'Asterisk icon', 'js_composer' ) => 'wpb_asterisk',
		esc_html__( 'Hammer icon', 'js_composer' ) => 'wpb_hammer',
		esc_html__( 'Balloon icon', 'js_composer' ) => 'wpb_balloon',
		esc_html__( 'Balloon Buzz icon', 'js_composer' ) => 'wpb_balloon_buzz',
		esc_html__( 'Balloon Facebook icon', 'js_composer' ) => 'wpb_balloon_facebook',
		esc_html__( 'Balloon Twitter icon', 'js_composer' ) => 'wpb_balloon_twitter',
		esc_html__( 'Battery icon', 'js_composer' ) => 'wpb_battery',
		esc_html__( 'Binocular icon', 'js_composer' ) => 'wpb_binocular',
		esc_html__( 'Document Excel icon', 'js_composer' ) => 'wpb_document_excel',
		esc_html__( 'Document Image icon', 'js_composer' ) => 'wpb_document_image',
		esc_html__( 'Document Music icon', 'js_composer' ) => 'wpb_document_music',
		esc_html__( 'Document Office icon', 'js_composer' ) => 'wpb_document_office',
		esc_html__( 'Document PDF icon', 'js_composer' ) => 'wpb_document_pdf',
		esc_html__( 'Document Powerpoint icon', 'js_composer' ) => 'wpb_document_powerpoint',
		esc_html__( 'Document Word icon', 'js_composer' ) => 'wpb_document_word',
		esc_html__( 'Bookmark icon', 'js_composer' ) => 'wpb_bookmark',
		esc_html__( 'Camcorder icon', 'js_composer' ) => 'wpb_camcorder',
		esc_html__( 'Camera icon', 'js_composer' ) => 'wpb_camera',
		esc_html__( 'Chart icon', 'js_composer' ) => 'wpb_chart',
		esc_html__( 'Chart pie icon', 'js_composer' ) => 'wpb_chart_pie',
		esc_html__( 'Clock icon', 'js_composer' ) => 'wpb_clock',
		esc_html__( 'Fire icon', 'js_composer' ) => 'wpb_fire',
		esc_html__( 'Heart icon', 'js_composer' ) => 'wpb_heart',
		esc_html__( 'Mail icon', 'js_composer' ) => 'wpb_mail',
		esc_html__( 'Play icon', 'js_composer' ) => 'wpb_play',
		esc_html__( 'Shield icon', 'js_composer' ) => 'wpb_shield',
		esc_html__( 'Video icon', 'js_composer' ) => 'wpb_video',
	);
}

require_once vc_path_dir( 'CONFIG_DIR', 'grids/vc-grids-functions.php' );
if ( 'vc_get_autocomplete_suggestion' === vc_request_param( 'action' ) || 'vc_edit_form' === vc_post_param( 'action' ) ) {
	add_filter( 'vc_autocomplete_vc_basic_grid_include_callback', 'vc_include_field_search', 10, 1 ); // Get suggestion(find). Must return an array
	add_filter( 'vc_autocomplete_vc_basic_grid_include_render', 'vc_include_field_render', 10, 1 ); // Render exact product. Must return an array (label,value)
	add_filter( 'vc_autocomplete_vc_masonry_grid_include_callback', 'vc_include_field_search', 10, 1 ); // Get suggestion(find). Must return an array
	add_filter( 'vc_autocomplete_vc_masonry_grid_include_render', 'vc_include_field_render', 10, 1 ); // Render exact product. Must return an array (label,value)

	// Narrow data taxonomies
	add_filter( 'vc_autocomplete_vc_basic_grid_taxonomies_callback', 'vc_autocomplete_taxonomies_field_search', 10, 1 );
	add_filter( 'vc_autocomplete_vc_basic_grid_taxonomies_render', 'vc_autocomplete_taxonomies_field_render', 10, 1 );

	add_filter( 'vc_autocomplete_vc_masonry_grid_taxonomies_callback', 'vc_autocomplete_taxonomies_field_search', 10, 1 );
	add_filter( 'vc_autocomplete_vc_masonry_grid_taxonomies_render', 'vc_autocomplete_taxonomies_field_render', 10, 1 );

	// Narrow data taxonomies for exclude_filter
	add_filter( 'vc_autocomplete_vc_basic_grid_exclude_filter_callback', 'vc_autocomplete_taxonomies_field_search', 10, 1 );
	add_filter( 'vc_autocomplete_vc_basic_grid_exclude_filter_render', 'vc_autocomplete_taxonomies_field_render', 10, 1 );

	add_filter( 'vc_autocomplete_vc_masonry_grid_exclude_filter_callback', 'vc_autocomplete_taxonomies_field_search', 10, 1 );
	add_filter( 'vc_autocomplete_vc_masonry_grid_exclude_filter_render', 'vc_autocomplete_taxonomies_field_render', 10, 1 );

	add_filter( 'vc_autocomplete_vc_basic_grid_exclude_callback', 'vc_exclude_field_search', 10, 1 ); // Get suggestion(find). Must return an array
	add_filter( 'vc_autocomplete_vc_basic_grid_exclude_render', 'vc_exclude_field_render', 10, 1 ); // Render exact product. Must return an array (label,value)
	add_filter( 'vc_autocomplete_vc_masonry_grid_exclude_callback', 'vc_exclude_field_search', 10, 1 ); // Get suggestion(find). Must return an array
	add_filter( 'vc_autocomplete_vc_masonry_grid_exclude_render', 'vc_exclude_field_render', 10, 1 ); // Render exact product. Must return an array (label,value);
}

