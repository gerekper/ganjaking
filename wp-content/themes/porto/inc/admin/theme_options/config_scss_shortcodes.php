<?php
	global $porto_settings, $porto_settings_optimize;
	$unused_shortcode_list = ! isset( $porto_settings_optimize['shortcodes_to_remove'] ) || ! $porto_settings_optimize['shortcodes_to_remove'] ? array() : $porto_settings_optimize['shortcodes_to_remove'];
?>
$screen-lg: <?php echo (int) ( $porto_settings['container-width'] + $porto_settings['grid-gutter-width'] ); ?> !default;
$grid-gutter-space: <?php echo (int) $porto_settings['grid-gutter-width']; ?> !default;

@import "theme/shortcodes/common";

<?php if ( defined( 'WPB_VC_VERSION' ) ) : ?>
	<?php if ( ! in_array( 'vc_tabs', $unused_shortcode_list ) || ! in_array( 'vc_tour', $unused_shortcode_list ) ) : ?>
	@import "theme/shortcodes/tabs";
	<?php endif; ?>
<?php endif; ?>
<?php if ( class_exists( 'Woocommerce' ) || ( defined( 'WPB_VC_VERSION' ) && ( ! in_array( 'vc_accordion', $unused_shortcode_list ) || ! in_array( 'vc_accordion_tab', $unused_shortcode_list ) ) ) || ( ! defined( 'WPB_VC_VERSION' ) && ! defined( 'ELEMENTOR_VERSION' ) ) ) : ?>
@import "theme/shortcodes/accordion";
<?php endif; ?>

@import "theme/shortcodes/toggles";

<?php if ( ! in_array( 'porto_grid_container', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/grid_container";
<?php endif; ?>
<?php if ( ! in_array( 'porto_carousel_logo', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/carousel_logo";
<?php endif; ?>

@import "theme/shortcodes/testimonials";

<?php if ( ! in_array( 'porto_preview_image', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/preview_image";
<?php endif; ?>
<?php if ( ! in_array( 'porto_buttons', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/buttons";
<?php endif; ?>
<?php if ( ! in_array( 'porto_concept', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/concept";
<?php endif; ?>

@import "theme/shortcodes/countdown";

<?php if ( ! in_array( 'porto_diamonds', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/diamond";
<?php endif; ?>
<?php if ( ! in_array( 'porto_experience_timeline_item', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/experience_timeline";
<?php endif; ?>
<?php if ( ! in_array( 'porto_fancytext', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/fancytext";
<?php endif; ?>
<?php if ( ! in_array( 'porto_floating_menu_item', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/float_menu";
<?php endif; ?>
<?php if ( ! in_array( 'porto_google_map', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/google_map";
<?php endif; ?>
<?php if ( ! in_array( 'porto_ultimate_heading', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/heading";
<?php endif; ?>
<?php if ( ! in_array( 'porto_history', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/history";
<?php endif; ?>
<?php if ( ! in_array( 'porto_icon', $unused_shortcode_list ) || ! in_array( 'porto_info_box', $unused_shortcode_list ) || ! in_array( 'porto_stat_counter', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/icon";
<?php endif; ?>
<?php if ( ! in_array( 'porto_icons', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/icons";
<?php endif; ?>
<?php if ( ! in_array( 'porto_info_box', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/info_box";
<?php endif; ?>
<?php if ( ! in_array( 'porto_info_list', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/info_list";
<?php endif; ?>
<?php if ( ! in_array( 'porto_interactive_banner', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/interactive_banner";
<?php endif; ?>
<?php if ( ! in_array( 'porto_links_block', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/links_block";
<?php endif; ?>
<?php if ( ! in_array( 'porto_map_section', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/map_section";
<?php endif; ?>
<?php if ( ! in_array( 'porto_price_box', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/pricing_tables";
<?php endif; ?>
<?php if ( ! in_array( 'porto_schedule_timeline_item', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/schedule_timeline";
<?php endif; ?>
<?php if ( ! in_array( 'porto_stat_counter', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/stat_counter";
<?php endif; ?>
<?php if ( class_exists( 'Woocommerce' ) || ! in_array( 'porto_ultimate_carousel', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/ultimate_carousel";
<?php endif; ?>
<?php if ( ! in_array( 'porto_ultimate_content_box', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/ultimate_content_box";
<?php endif; ?>

<?php if ( defined( 'WPB_VC_VERSION' ) ) : ?>
	<?php if ( ! in_array( 'vc_btn', $unused_shortcode_list ) ) : ?>
	@import "theme/shortcodes/vc_button";
	<?php endif; ?>
	<?php if ( ! in_array( 'vc_cta', $unused_shortcode_list ) ) : ?>
	@import "theme/shortcodes/vc_cta3";
	<?php endif; ?>
	<?php if ( ! in_array( 'vc_custom_heading', $unused_shortcode_list ) ) : ?>
	@import "theme/shortcodes/vc_custom_heading";
	<?php endif; ?>
	<?php if ( ! in_array( 'vc_pie', $unused_shortcode_list ) ) : ?>
	@import "theme/shortcodes/vc_pie";
	<?php endif; ?>
	<?php if ( ! in_array( 'vc_progress_bar', $unused_shortcode_list ) ) : ?>
	@import "theme/shortcodes/vc_progress_bar";
	<?php endif; ?>
	<?php if ( ! in_array( 'vc_row', $unused_shortcode_list ) ) : ?>
	@import "theme/shortcodes/vc_row";
	<?php endif; ?>
	<?php if ( ! in_array( 'vc_separator', $unused_shortcode_list ) ) : ?>
	@import "theme/shortcodes/vc_separator";
	<?php endif; ?>
	<?php if ( ! in_array( 'vc_single_image', $unused_shortcode_list ) ) : ?>
	@import "theme/shortcodes/vc_single_image";
	<?php endif; ?>
	<?php if ( ! in_array( 'vc_column_text', $unused_shortcode_list ) ) : ?>
	@import "theme/shortcodes/vc_text_column";
	<?php endif; ?>
<?php elseif ( defined( 'ELEMENTOR_VERSION' ) ) : ?>
	<?php if ( ! in_array( 'porto_circular_bar', $unused_shortcode_list ) ) : ?>
	@import "theme/shortcodes/vc_pie";
	<?php endif; ?>
<?php endif; ?>

<?php if ( class_exists( 'Woocommerce' ) && ! in_array( 'porto_one_page_category_products', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/woo_one_page_category_products";
<?php endif; ?>
<?php if ( ! in_array( 'porto_section_scroll', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/section_scroll";
<?php endif; ?>
<?php if ( ! in_array( 'porto_360degree_image_viewer', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/360degree_image_viewer";
<?php endif; ?>
<?php if ( class_exists( 'Woocommerce' ) && ! in_array( 'porto_products_filter', $unused_shortcode_list ) ) : ?>
@import "theme/shortcodes/products_filter";
<?php endif; ?>

.inline-block { display: inline-block; }
