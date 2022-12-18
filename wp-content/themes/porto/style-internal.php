<?php
/**
 * Generates dynamic css styles for only special pages or post types
 * @package Porto
 * @author P-Themes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( porto_vc_is_inline() && porto_is_ajax() ) {
	return;
}

global $porto_settings, $porto_product_layout, $porto_layout;

if ( is_rtl() ) {
	$left  = 'right';
	$right = 'left';
	$rtl   = true;
} else {
	$left  = 'left';
	$right = 'right';
	$rtl   = false;
}

if ( empty( $porto_layout ) ) {
	$porto_layout = porto_meta_layout();
	$porto_layout = $porto_layout[0];
}
$body_type  = porto_get_wrapper_type();
$is_wide    = ( 'wide' == $body_type || porto_is_wide_layout() );
$is_dark    = false;
$dark_color = empty( $porto_settings['color-dark'] ) ? '#222529' : $porto_settings['color-dark'];

if ( isset( $porto_settings['css-type'] ) && 'dark' == $porto_settings['css-type'] ) {
	require_once( PORTO_LIB . '/lib/color-lib.php' );
	$porto_color_lib = PortoColorLib::getInstance();
	$color_dark_3    = $porto_color_lib->lighten( $dark_color, 5 );
	$color_dark_4    = $porto_color_lib->lighten( $dark_color, 8 );
	$is_dark         = true;
}


/* logo css */
$logo_width        = ( isset( $porto_settings['logo-width'] ) && (int) $porto_settings['logo-width'] ) ? (int) $porto_settings['logo-width'] : 170;
$logo_width_wide   = ( isset( $porto_settings['logo-width-wide'] ) && (int) $porto_settings['logo-width-wide'] ) ? (int) $porto_settings['logo-width-wide'] : 250;
$logo_width_tablet = ( isset( $porto_settings['logo-width-tablet'] ) && (int) $porto_settings['logo-width-tablet'] ) ? (int) $porto_settings['logo-width-tablet'] : 110;
$logo_width_mobile = ( isset( $porto_settings['logo-width-mobile'] ) && (int) $porto_settings['logo-width-mobile'] ) ? (int) $porto_settings['logo-width-mobile'] : 110;
$logo_width_sticky = ( isset( $porto_settings['logo-width-sticky'] ) && (int) $porto_settings['logo-width-sticky'] ) ? (int) $porto_settings['logo-width-sticky'] : 80;
?>
.side-header-narrow-bar-logo { max-width: <?php echo esc_html( $logo_width ); ?>px; }

<?php
/* loading overlay */
$loading_overlay = porto_get_meta_value( 'loading_overlay' );
if ( 'no' !== $loading_overlay && ( 'yes' === $loading_overlay || ( 'yes' !== $loading_overlay && ! empty( $porto_settings['show-loading-overlay'] ) ) ) ) :
	?>
	/* Loading Overlay */
	/*.loading-overlay-showing { overflow-x: hidden; }*/
	.loading-overlay-showing > .loading-overlay { opacity: 1; visibility: visible; transition-delay: 0; }
	.loading-overlay { transition: visibility 0s ease-in-out 0.5s, opacity 0.5s ease-in-out; position: absolute; bottom: 0; left: 0; opacity: 0; right: 0; top: 0; visibility: hidden; }
	.loading-overlay .loader { display: inline-block; border: 2px solid transparent; width: 40px; height: 40px; -webkit-animation: spin 0.75s infinite linear; animation: spin 0.75s infinite linear; border-image: none; border-radius: 50%; vertical-align: middle; position: absolute; margin: auto; left: 0; right: 0; top: 0; bottom: 0; z-index: 2; border-top-color: var(--porto-primary-color); }
	.loading-overlay .loader:before { content: ""; display: inline-block; border: inherit; width: inherit; height: inherit; -webkit-animation: spin 1.5s infinite ease; animation: spin 1.5s infinite ease; border-radius: inherit; position: absolute; left: -2px; top: -2px; border-top-color: inherit; }
	body > .loading-overlay { position: fixed; z-index: 999999; }
	<?php
endif;

/* header */
?>
<?php if ( isset( $porto_settings['header-top-border'] ) && ! empty( $porto_settings['header-top-border']['border-top'] ) && '0px' != $porto_settings['header-top-border']['border-top'] ) : ?>
	#header,
	.sticky-header .header-main.sticky { border-top: <?php echo esc_html( $porto_settings['header-top-border']['border-top'] ); ?> solid <?php echo isset( $porto_settings['header-top-border']['border-color'] ) ? esc_html( $porto_settings['header-top-border']['border-color'] ) : ''; ?> }
<?php endif; ?>
@media (min-width: 992px) {
	<?php
	if ( isset( $porto_settings['header-margin'] ) && ( ! empty( $porto_settings['header-margin']['margin-top'] ) || ! empty( $porto_settings['header-margin']['margin-bottom'] ) || ! empty( $porto_settings['header-margin']['margin-left'] ) || ! empty( $porto_settings['header-margin']['margin-right'] ) ) ) :
		if ( $rtl && ! empty( $porto_settings['header-margin']['margin-left'] ) && ! empty( $porto_settings['header-margin']['margin-right'] ) ) {
			$temp = $porto_settings['header-margin']['margin-left'];
			$porto_settings['header-margin']['margin-left']  = $porto_settings['header-margin']['margin-right'];
			$porto_settings['header-margin']['margin-right'] = $temp;
		}
		?>
		#header { margin: <?php echo porto_config_value( $porto_settings['header-margin']['margin-top'] ); ?>px <?php echo porto_config_value( $porto_settings['header-margin']['margin-right'] ); ?>px <?php echo porto_config_value( $porto_settings['header-margin']['margin-bottom'] ); ?>px <?php echo porto_config_value( $porto_settings['header-margin']['margin-left'] ); ?>px; }
	<?php endif; ?>
	<?php if ( isset( $porto_settings['header-margin'] ) && ! empty( $porto_settings['header-margin']['margin-top'] ) && isset( $porto_settings['logo-overlay'] ) && ! empty( $porto_settings['logo-overlay']['url'] ) ) : ?>
		#header.logo-overlay-header .overlay-logo { top: -<?php echo esc_html( $porto_settings['header-margin']['margin-top'] ); ?>px }
		#header.logo-overlay-header.sticky-header .overlay-logo { top: -<?php echo (int) $porto_settings['header-margin']['margin-top'] + 90; ?>px }
	<?php endif; ?>
}

<?php if ( isset( $porto_settings['header-main-padding'] ) && ( '' != $porto_settings['header-main-padding']['padding-top'] || '' != $porto_settings['header-main-padding']['padding-bottom'] ) ) : ?>
	#header .header-main .header-left,
	#header .header-main .header-center,
	#header .header-main .header-right,
	.fixed-header #header .header-main .header-left,
	.fixed-header #header .header-main .header-right,
	.fixed-header #header .header-main .header-center,
	.header-builder-p .header-main { padding-top: <?php echo (int) $porto_settings['header-main-padding']['padding-top']; ?>px; padding-bottom: <?php echo (int) $porto_settings['header-main-padding']['padding-bottom']; ?>px }
<?php endif; ?>
<?php if ( isset( $porto_settings['header-main-padding-mobile'] ) && ( '' != $porto_settings['header-main-padding-mobile']['padding-top'] || '' != $porto_settings['header-main-padding-mobile']['padding-bottom'] ) ) : ?>
	@media (max-width: 991px) {
		#header .header-main .header-left,
		#header .header-main .header-center,
		#header .header-main .header-right,
		.fixed-header #header .header-main .header-left,
		.fixed-header #header .header-main .header-right,
		.fixed-header #header .header-main .header-center,
		.header-builder-p .header-main { padding-top: <?php echo (int) $porto_settings['header-main-padding-mobile']['padding-top']; ?>px; padding-bottom: <?php echo (int) $porto_settings['header-main-padding-mobile']['padding-bottom']; ?>px }
	}
<?php endif; ?>

/* breadcrumb type */
<?php
	$page_header_type = porto_get_meta_value( 'porto_page_header_shortcode_type' );
	$page_header_type = $page_header_type ? $page_header_type : porto_get_meta_value( 'breadcrumbs_type' );

	$blocks_has_breadcrumbs = porto_check_using_page_builder_block( 'breadcrumbs' );
if ( $blocks_has_breadcrumbs ) {
	$page_header_type = $blocks_has_breadcrumbs;
}

$page_header_type = apply_filters( 'porto_page_top_styles', $page_header_type );
$page_header_type = $page_header_type ? $page_header_type : ( $porto_settings['breadcrumbs-type'] ? $porto_settings['breadcrumbs-type'] : '1' );

?>
<?php if ( 1 === (int) $page_header_type ) : ?>
	.page-top .page-title-wrap { line-height: 0; }
	<?php if ( isset( $porto_settings['breadcrumbs-bottom-border'] ) && ! empty( $porto_settings['breadcrumbs-bottom-border']['border-top'] ) && '0px' != $porto_settings['breadcrumbs-bottom-border']['border-top'] ) : ?>
		.page-top .page-title:not(.b-none):after { content: ''; position: absolute; width: 100%; left: 0; border-bottom: <?php echo esc_html( $porto_settings['breadcrumbs-bottom-border']['border-top'] ); ?> solid var(--porto-primary-color); bottom: -<?php echo ( isset( $porto_settings['breadcrumbs-padding'] ) ? (int) porto_config_value( $porto_settings['breadcrumbs-padding']['padding-bottom'] ) : 0 ) + (int) porto_config_value( $porto_settings['breadcrumbs-bottom-border']['border-top'] ) + 12; ?>px; }
	<?php endif; ?>
<?php elseif ( 3 === (int) $page_header_type || 4 === (int) $page_header_type || 5 === (int) $page_header_type || 7 === (int) $page_header_type ) : ?>
	<?php if ( class_exists( 'Woocommerce' ) ) : ?>
		.page-top .product-nav { position: static; height: auto; margin-top: 0; }
		.page-top .product-nav .product-prev,
		.page-top .product-nav .product-next { float: none; position: absolute; height: 30px; top: 50%; bottom: 50%; margin-top: -15px; }
		.page-top .product-nav .product-prev { <?php echo porto_filter_output( $right ); ?>: 10px; }
		.page-top .product-nav .product-next { <?php echo porto_filter_output( $left ); ?>: 10px; }
		.page-top .product-nav .product-next .product-popup { <?php echo porto_filter_output( $right ); ?>: auto; <?php echo porto_filter_output( $left ); ?>: 0; }
		.page-top .product-nav .product-next .product-popup:before { <?php echo porto_filter_output( $right ); ?>: auto; <?php echo porto_filter_output( $left ); ?>: 6px; }
	<?php endif; ?>
	.page-top .sort-source { position: static; text-align: center; margin-top: 5px; border-width: 0; }
	<?php if ( 3 === (int) $page_header_type || 7 === (int) $page_header_type ) : ?>
		.page-top ul.breadcrumb { -ms-flex-pack: center; justify-content: center }
		.page-top .page-title { font-weight: 700; }
	<?php endif; ?>
<?php endif; ?>
<?php if ( 4 === (int) $page_header_type || 5 === (int) $page_header_type ) : ?>
	.page-top { padding-top: 20px; padding-bottom: 20px; }
	.page-top .page-title { padding-bottom: 0; }
	@media (max-width: 991px) {
		.page-top .page-sub-title { margin-bottom: 5px; margin-top: 0; }
		.page-top .breadcrumbs-wrap { margin-bottom: 5px; }
	}
	@media (min-width: 992px) {
		.page-top .page-title { min-height: 0; line-height: 1.25; }
		.page-top .page-sub-title { line-height: 1.6; }
		<?php if ( class_exists( 'Woocommerce' ) ) : ?>
			.page-top .product-nav { display: inline-block; height: 30px; vertical-align: middle; margin-<?php echo porto_filter_output( $left ); ?>: 10px; }
			.page-top .product-nav .product-prev,
			.page-top .product-nav .product-next { position: relative; }
			.page-top .product-nav .product-prev { float: <?php echo porto_filter_output( $left ); ?>; <?php echo porto_filter_output( $left ); ?>: 0; }
			.page-top .product-nav .product-prev .product-popup { <?php echo porto_filter_output( $right ); ?>: auto; <?php echo porto_filter_output( $left ); ?>: -26px; }
			.page-top .product-nav .product-prev:before { <?php echo porto_filter_output( $right ); ?>: auto; <?php echo porto_filter_output( $left ); ?>: 32px; }
			.page-top .product-nav .product-next { float: <?php echo porto_filter_output( $left ); ?>; <?php echo porto_filter_output( $left ); ?>: 0; }
			.page-top .product-nav .product-next .product-popup { <?php echo porto_filter_output( $right ); ?>: auto; <?php echo porto_filter_output( $left ); ?>: 0; }
			.page-top .product-nav .product-next .product-popup:before { <?php echo porto_filter_output( $right ); ?>: auto; }
		<?php endif; ?>
	}
<?php endif; ?>
<?php if ( 4 === (int) $page_header_type ) : ?>
	@media (min-width: 992px) {
		<?php if ( class_exists( 'Woocommerce' ) ) : ?>
			.page-top .product-nav { height: auto; }
		<?php endif; ?>
		.page-top .breadcrumb { -webkit-justify-content: flex-end; -ms-flex-pack: end; justify-content: flex-end }
	}
<?php elseif ( 6 === (int) $page_header_type ) : ?>
	.page-top ul.breadcrumb > li.home { display: inline-block; }
	.page-top ul.breadcrumb > li.home a { position: relative; width: 14px; text-indent: -9999px; }
	.page-top ul.breadcrumb > li.home a:after { content: "\e883"; font-family: 'porto'; float: <?php echo porto_filter_output( $left ); ?>; text-indent: 0; }
<?php endif; ?>
<?php if ( class_exists( 'Woocommerce' ) && ( 1 === (int) $page_header_type || 2 === (int) $page_header_type ) ) : ?>
	body.single-product .page-top .breadcrumbs-wrap { padding-<?php echo porto_filter_output( $right ); ?>: 55px; }
<?php endif; ?>

/* sidebar width */
<?php if ( $is_wide ) : ?>
	@media (min-width: 1500px) {
		.left-sidebar.col-lg-3,
		.right-sidebar.col-lg-3 { width: 20%; }
		.main-content.col-lg-9 { width: 80%; }
		.main-content.col-lg-6 { width: 60%; }
	}
	<?php
endif;

/* woocommerce single product */
if ( isset( $porto_product_layout ) && $porto_product_layout ) :
	?>
	.product-images .img-thumbnail .inner,
	.product-images .img-thumbnail .inner img { -webkit-transform: none; transform: none; }
	<?php

	if ( porto_is_product() ) :
		//if ( isset( $porto_settings['product-sticky-addcart'] ) && $porto_settings['product-sticky-addcart'] ) :
		?>
			.sticky-product { position: fixed; top: 0; left: 0; width: 100%; z-index: 100; background-color: #fff; box-shadow: 0 3px 5px rgba(0,0,0,0.08); padding: 15px 0; }
			.sticky-product.pos-bottom { top: auto; bottom: 0; box-shadow: 0 -3px 5px rgba(0,0,0,0.08) }
			.sticky-product .container { display: -ms-flexbox; display: flex; -ms-flex-align: center; align-items: center; -ms-flex-wrap: wrap; flex-wrap: wrap }
			.sticky-product .sticky-image { max-width: 60px; margin-<?php echo porto_filter_output( $right ); ?>: 15px; }
			.sticky-product .add-to-cart { -ms-flex: 1; flex: 1; text-align: <?php echo porto_filter_output( $right ); ?>; margin-top: 5px; }
			.sticky-product .product-name { font-size: 16px; font-weight: 600; line-height: inherit; margin-bottom: 0; }
			.sticky-product .sticky-detail { line-height: 1.5; display: -ms-flexbox; display: flex; }
			.sticky-product .star-rating { margin: 5px 15px; font-size: 1em; }
			.sticky-product .availability { padding-top: 2px; }
			.sticky-product .sticky-detail .price { font-family: <?php echo isset( $porto_settings['h2-font'] ) && isset( $porto_settings['h2-font']['font-family'] ) ? sanitize_text_field( $porto_settings['h2-font']['font-family'] ) : '', isset( $porto_settings['h3-font'] ) && isset( $porto_settings['h3-font']['font-family'] ) ? ',' . sanitize_text_field( $porto_settings['h3-font']['font-family'] ) : ''; ?>, sans-serif; font-weight: 400; margin-bottom: 0; font-size: 1.3em; line-height: 1.5; }
			@media (max-width: 992px) {
				.sticky-product .container { padding-left: var(--porto-grid-gutter-width); padding-right: var(--porto-grid-gutter-width) }
			}
			@media (max-width: 767px) {
				.sticky-product { display: none; }
			}

			<?php
			//endif;
	endif;
endif;

/* skin options */
$body_bg_color      = porto_get_meta_value( 'body_bg_color' );
$body_bg_image      = porto_get_meta_value( 'body_bg_image' );
$body_bg_repeat     = porto_get_meta_value( 'body_bg_repeat' );
$body_bg_size       = porto_get_meta_value( 'body_bg_size' );
$body_bg_attachment = porto_get_meta_value( 'body_bg_attachment' );
$body_bg_position   = porto_get_meta_value( 'body_bg_position' );

$page_bg_color      = porto_get_meta_value( 'page_bg_color' );
$page_bg_image      = porto_get_meta_value( 'page_bg_image' );
$page_bg_repeat     = porto_get_meta_value( 'page_bg_repeat' );
$page_bg_size       = porto_get_meta_value( 'page_bg_size' );
$page_bg_attachment = porto_get_meta_value( 'page_bg_attachment' );
$page_bg_position   = porto_get_meta_value( 'page_bg_position' );

$content_bottom_bg_color      = porto_get_meta_value( 'content_bottom_bg_color' );
$content_bottom_bg_image      = porto_get_meta_value( 'content_bottom_bg_image' );
$content_bottom_bg_repeat     = porto_get_meta_value( 'content_bottom_bg_repeat' );
$content_bottom_bg_size       = porto_get_meta_value( 'content_bottom_bg_size' );
$content_bottom_bg_attachment = porto_get_meta_value( 'content_bottom_bg_attachment' );
$content_bottom_bg_position   = porto_get_meta_value( 'content_bottom_bg_position' );

$header_bg_color      = porto_get_meta_value( 'header_bg_color' );
$header_bg_image      = porto_get_meta_value( 'header_bg_image' );
$header_bg_repeat     = porto_get_meta_value( 'header_bg_repeat' );
$header_bg_size       = porto_get_meta_value( 'header_bg_size' );
$header_bg_attachment = porto_get_meta_value( 'header_bg_attachment' );
$header_bg_position   = porto_get_meta_value( 'header_bg_position' );

$sticky_header_bg_color      = porto_get_meta_value( 'sticky_header_bg_color' );
$sticky_header_bg_image      = porto_get_meta_value( 'sticky_header_bg_image' );
$sticky_header_bg_repeat     = porto_get_meta_value( 'sticky_header_bg_repeat' );
$sticky_header_bg_size       = porto_get_meta_value( 'sticky_header_bg_size' );
$sticky_header_bg_attachment = porto_get_meta_value( 'sticky_header_bg_attachment' );
$sticky_header_bg_position   = porto_get_meta_value( 'sticky_header_bg_position' );

$footer_top_bg_color      = porto_get_meta_value( 'footer_top_bg_color' );
$footer_top_bg_image      = porto_get_meta_value( 'footer_top_bg_image' );
$footer_top_bg_repeat     = porto_get_meta_value( 'footer_top_bg_repeat' );
$footer_top_bg_size       = porto_get_meta_value( 'footer_top_bg_size' );
$footer_top_bg_attachment = porto_get_meta_value( 'footer_top_bg_attachment' );
$footer_top_bg_position   = porto_get_meta_value( 'footer_top_bg_position' );

$footer_bg_color      = porto_get_meta_value( 'footer_bg_color' );
$footer_bg_image      = porto_get_meta_value( 'footer_bg_image' );
$footer_bg_repeat     = porto_get_meta_value( 'footer_bg_repeat' );
$footer_bg_size       = porto_get_meta_value( 'footer_bg_size' );
$footer_bg_attachment = porto_get_meta_value( 'footer_bg_attachment' );
$footer_bg_position   = porto_get_meta_value( 'footer_bg_position' );

$footer_main_bg_color      = porto_get_meta_value( 'footer_main_bg_color' );
$footer_main_bg_image      = porto_get_meta_value( 'footer_main_bg_image' );
$footer_main_bg_repeat     = porto_get_meta_value( 'footer_main_bg_repeat' );
$footer_main_bg_size       = porto_get_meta_value( 'footer_main_bg_size' );
$footer_main_bg_attachment = porto_get_meta_value( 'footer_main_bg_attachment' );
$footer_main_bg_position   = porto_get_meta_value( 'footer_main_bg_position' );

$footer_bottom_bg_color      = porto_get_meta_value( 'footer_bottom_bg_color' );
$footer_bottom_bg_image      = porto_get_meta_value( 'footer_bottom_bg_image' );
$footer_bottom_bg_repeat     = porto_get_meta_value( 'footer_bottom_bg_repeat' );
$footer_bottom_bg_size       = porto_get_meta_value( 'footer_bottom_bg_size' );
$footer_bottom_bg_attachment = porto_get_meta_value( 'footer_bottom_bg_attachment' );
$footer_bottom_bg_position   = porto_get_meta_value( 'footer_bottom_bg_position' );

$breadcrumbs_bg_color      = porto_get_meta_value( 'breadcrumbs_bg_color' );
$breadcrumbs_bg_image      = porto_get_meta_value( 'breadcrumbs_bg_image' );
$breadcrumbs_bg_repeat     = porto_get_meta_value( 'breadcrumbs_bg_repeat' );
$breadcrumbs_bg_size       = porto_get_meta_value( 'breadcrumbs_bg_size' );
$breadcrumbs_bg_attachment = porto_get_meta_value( 'breadcrumbs_bg_attachment' );
$breadcrumbs_bg_position   = porto_get_meta_value( 'breadcrumbs_bg_position' );

if ( $body_bg_color || $body_bg_image || $body_bg_repeat || $body_bg_size || $body_bg_attachment || $body_bg_position
	|| $page_bg_color || $page_bg_image || $page_bg_repeat || $page_bg_size || $page_bg_attachment || $page_bg_position
	|| $content_bottom_bg_color || $content_bottom_bg_image || $content_bottom_bg_repeat || $content_bottom_bg_size || $content_bottom_bg_attachment || $content_bottom_bg_position
	|| $header_bg_color || $header_bg_image || $header_bg_repeat || $header_bg_size || $header_bg_attachment || $header_bg_position
	|| $sticky_header_bg_color || $sticky_header_bg_image || $sticky_header_bg_repeat || $sticky_header_bg_size || $sticky_header_bg_attachment || $sticky_header_bg_position
	|| $footer_top_bg_color || $footer_top_bg_image || $footer_top_bg_repeat || $footer_top_bg_size || $footer_top_bg_attachment || $footer_top_bg_position
	|| $footer_bg_color || $footer_bg_image || $footer_bg_repeat || $footer_bg_size || $footer_bg_attachment || $footer_bg_position
	|| $footer_main_bg_color || $footer_main_bg_image || $footer_main_bg_repeat || $footer_main_bg_size || $footer_main_bg_attachment || $footer_main_bg_position
	|| $footer_bottom_bg_color || $footer_bottom_bg_image || $footer_bottom_bg_repeat || $footer_bottom_bg_size || $footer_bottom_bg_attachment || $footer_bottom_bg_position
	|| $breadcrumbs_bg_color || $breadcrumbs_bg_image || $breadcrumbs_bg_repeat || $breadcrumbs_bg_size || $breadcrumbs_bg_attachment || $breadcrumbs_bg_position ) :
	?>
	<?php
	if ( $body_bg_color || $body_bg_image || $body_bg_repeat || $body_bg_size || $body_bg_attachment || $body_bg_position ) :
		?>
	body {
		<?php
		if ( $body_bg_color ) :
			?>
		background-color: <?php echo esc_html( $body_bg_color ); ?> !important;
			<?php
		endif;
		if ( 'none' == $body_bg_image ) :
			echo 'background-image: none !important';
		elseif ( $body_bg_image ) :
			?>
		background-image: url('<?php echo esc_url( str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $body_bg_image ) ); ?>') !important;
			<?php
		endif;
		if ( $body_bg_repeat ) :
			?>
		background-repeat: <?php echo esc_html( $body_bg_repeat ); ?> !important;
			<?php
		endif;
		if ( $body_bg_size ) :
			?>
		background-size: <?php echo esc_html( $body_bg_size ); ?> !important;
			<?php
		endif;
		if ( $body_bg_attachment ) :
			?>
		background-attachment: <?php echo esc_html( $body_bg_attachment ); ?> !important;
			<?php
		endif;
		if ( $body_bg_position ) :
			?>
		background-position: <?php echo esc_html( $body_bg_position ); ?> !important;
			<?php
		endif;
		?>
	}
		<?php
	endif;
	if ( $page_bg_color || $page_bg_image || $page_bg_repeat || $page_bg_size || $page_bg_attachment || $page_bg_position ) :
		?>
	#main {
		<?php
		if ( $page_bg_color ) :
			?>
		background-color: <?php echo esc_html( $page_bg_color ); ?> !important;
			<?php
		endif;
		if ( 'none' == $page_bg_image ) :
			echo 'background-image: none !important';
		elseif ( $page_bg_image ) :
			?>
			background-image: url('<?php echo esc_url( str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $page_bg_image ) ); ?>') !important;
			<?php
		endif;
		if ( $page_bg_repeat ) :
			?>
		background-repeat: <?php echo esc_html( $page_bg_repeat ); ?> !important;
			<?php
		endif;
		if ( $page_bg_size ) :
			?>
		background-size: <?php echo esc_html( $page_bg_size ); ?> !important;
			<?php
		endif;
		if ( $page_bg_attachment ) :
			?>
		background-attachment: <?php echo esc_html( $page_bg_attachment ); ?> !important;
			<?php
		endif;
		if ( $page_bg_position ) :
			?>
		background-position: <?php echo esc_html( $page_bg_position ); ?> !important;
			<?php
		endif;
		?>
	}
		<?php
		if ( 'transparent' == $page_bg_color ) :
			?>
		.page-content { margin-left: -<?php echo (int) $porto_settings['grid-gutter-width']; ?>px; margin-right: -<?php echo (int) $porto_settings['grid-gutter-width']; ?>px; }
		.main-content { padding-bottom: 0 !important; }
		.left-sidebar, .right-sidebar, .wide-left-sidebar, .wide-right-sidebar { padding-top: 0 !important; padding-bottom: 0 !important; margin: 0; }
			<?php
		endif;
	endif;
	if ( $content_bottom_bg_color || $content_bottom_bg_image || $content_bottom_bg_repeat || $content_bottom_bg_size || $content_bottom_bg_attachment || $content_bottom_bg_position ) :
		?>
	#main .content-bottom-wrapper {
		<?php
		if ( $content_bottom_bg_color ) :
			?>
		background-color: <?php echo esc_html( $content_bottom_bg_color ); ?> !important;
			<?php
			endif;
		if ( 'none' == $content_bottom_bg_image ) :
			echo 'background-image: none !important';
		elseif ( $content_bottom_bg_image ) :
			?>
			background-image: url('<?php echo esc_url( str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $content_bottom_bg_image ) ); ?>') !important;
			<?php
		endif;
		if ( $content_bottom_bg_repeat ) :
			?>
		background-repeat: <?php echo esc_html( $content_bottom_bg_repeat ); ?> !important;
			<?php
		endif;
		if ( $content_bottom_bg_size ) :
			?>
		background-size: <?php echo esc_html( $content_bottom_bg_size ); ?> !important;
			<?php
		endif;
		if ( $content_bottom_bg_attachment ) :
			?>
		background-attachment: <?php echo esc_html( $content_bottom_bg_attachment ); ?> !important;
			<?php
		endif;
		if ( $content_bottom_bg_position ) :
			?>
		background-position: <?php echo esc_html( $content_bottom_bg_position ); ?> !important;
			<?php
		endif;
		?>
	}
		<?php
	endif;
	if ( $header_bg_color || $header_bg_image || $header_bg_repeat || $header_bg_size || $header_bg_attachment || $header_bg_position ) :
		?>
	#header, .fixed-header #header {
		<?php
		if ( $header_bg_color ) :
			?>
		background-color: <?php echo esc_html( $header_bg_color ); ?> !important;
			<?php
		endif;
		if ( 'none' == $header_bg_image ) :
			echo 'background-image: none !important';
		elseif ( $header_bg_image ) :
			?>
			background-image: url('<?php echo esc_url( str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $header_bg_image ) ); ?>') !important;
			<?php
		endif;
		if ( $header_bg_repeat ) :
			?>
		background-repeat: <?php echo esc_html( $header_bg_repeat ); ?> !important;
			<?php
		endif;
		if ( $header_bg_size ) :
			?>
		background-size: <?php echo esc_html( $header_bg_size ); ?> !important;
			<?php
		endif;
		if ( $header_bg_attachment ) :
			?>
		background-attachment: <?php echo esc_html( $header_bg_attachment ); ?> !important;
			<?php
		endif;
		if ( $header_bg_position ) :
			?>
		background-position: <?php echo esc_html( $header_bg_position ); ?> !important;
			<?php
		endif;
		?>
	}
		<?php
	endif;

	if ( $header_bg_color || $header_bg_image ) :
		?>
	.header-wrapper #header .header-main { background: none; }
		<?php
	endif;

	if ( $sticky_header_bg_color || $sticky_header_bg_image || $sticky_header_bg_repeat || $sticky_header_bg_size || $sticky_header_bg_attachment || $sticky_header_bg_position ) :
		?>
	#header.sticky-header, .fixed-header #header.sticky-header {
		<?php
		if ( $sticky_header_bg_color ) :
			?>
		background-color: <?php echo esc_html( $sticky_header_bg_color ); ?> !important;
			<?php
		endif;
		if ( 'none' == $sticky_header_bg_image ) :
			echo 'background-image: none !important';
		elseif ( $sticky_header_bg_image ) :
			?>
			background-image: url('<?php echo esc_url( str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $sticky_header_bg_image ) ); ?>') !important;
			<?php
		endif;
		if ( $sticky_header_bg_repeat ) :
			?>
		background-repeat: <?php echo esc_html( $sticky_header_bg_repeat ); ?> !important;
			<?php
		endif;
		if ( $sticky_header_bg_size ) :
			?>
		background-size: <?php echo esc_html( $sticky_header_bg_size ); ?> !important;
			<?php
		endif;
		if ( $sticky_header_bg_attachment ) :
			?>
		background-attachment: <?php echo esc_html( $sticky_header_bg_attachment ); ?> !important;
			<?php
		endif;
		if ( $sticky_header_bg_position ) :
			?>
		background-position: <?php echo esc_html( $sticky_header_bg_position ); ?> !important;
			<?php
		endif;
		?>
	}
		<?php
	endif;
	if ( $footer_top_bg_color || $footer_top_bg_image || $footer_top_bg_repeat || $footer_top_bg_size || $footer_top_bg_attachment || $footer_top_bg_position ) :
		?>
	.footer-top {
		<?php
		if ( $footer_top_bg_color ) :
			?>
		background-color: <?php echo esc_html( $footer_top_bg_color ); ?> !important;
			<?php
		endif;
		if ( 'none' == $footer_top_bg_image ) :
			echo 'background-image: none !important';
		elseif ( $footer_top_bg_image ) :
			?>
			background-image: url('<?php echo esc_url( str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $footer_top_bg_image ) ); ?>') !important;
			<?php
		endif;
		if ( $footer_top_bg_repeat ) :
			?>
		background-repeat: <?php echo esc_html( $footer_top_bg_repeat ); ?> !important;
			<?php
		endif;
		if ( $footer_top_bg_size ) :
			?>
		background-size: <?php echo esc_html( $footer_top_bg_size ); ?> !important;
			<?php
		endif;
		if ( $footer_top_bg_attachment ) :
			?>
		background-attachment: <?php echo esc_html( $footer_top_bg_attachment ); ?> !important;
			<?php
		endif;
		if ( $footer_top_bg_position ) :
			?>
		background-position: <?php echo esc_html( $footer_top_bg_position ); ?> !important;
			<?php
		endif;
		?>
	}
		<?php
	endif;
	if ( $footer_bg_color || $footer_bg_image || $footer_bg_repeat || $footer_bg_size || $footer_bg_attachment || $footer_bg_position ) :
		?>
	.footer {
		<?php
		if ( $footer_bg_color ) :
			?>
		background-color: <?php echo esc_html( $footer_bg_color ); ?> !important;
			<?php
		endif;
		if ( 'none' == $footer_bg_image ) :
			echo 'background-image: none !important';
		elseif ( $footer_bg_image ) :
			?>
		background-image: url('<?php echo esc_url( str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $footer_bg_image ) ); ?>') !important;
			<?php
		endif;
		if ( $footer_bg_repeat ) :
			?>
		background-repeat: <?php echo esc_html( $footer_bg_repeat ); ?> !important;
			<?php
		endif;
		if ( $footer_bg_size ) :
			?>
		background-size: <?php echo esc_html( $footer_bg_size ); ?> !important;
			<?php
		endif;
		if ( $footer_bg_attachment ) :
			?>
		background-attachment: <?php echo esc_html( $footer_bg_attachment ); ?> !important;
			<?php
		endif;
		if ( $footer_bg_position ) :
			?>
		background-position: <?php echo esc_html( $footer_bg_position ); ?> !important;
			<?php
		endif;
		?>
	}
		<?php
	endif;
	if ( $footer_main_bg_color || $footer_main_bg_image || $footer_main_bg_repeat || $footer_main_bg_size || $footer_main_bg_attachment || $footer_main_bg_position ) :
		?>
	.footer .footer-main {
		<?php
		if ( $footer_main_bg_color ) :
			?>
		background-color: <?php echo esc_html( $footer_main_bg_color ); ?> !important;
			<?php
		endif;
		if ( 'none' == $footer_main_bg_image ) :
			echo 'background-image: none !important';
		elseif ( $footer_main_bg_image ) :
			?>
		background-image: url('<?php echo esc_url( str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $footer_main_bg_image ) ); ?>') !important;
			<?php
		endif;
		if ( $footer_main_bg_repeat ) :
			?>
		background-repeat: <?php echo esc_html( $footer_main_bg_repeat ); ?> !important;
			<?php
		endif;
		if ( $footer_main_bg_size ) :
			?>
		background-size: <?php echo esc_html( $footer_main_bg_size ); ?> !important;
			<?php
		endif;
		if ( $footer_main_bg_attachment ) :
			?>
		background-attachment: <?php echo esc_html( $footer_main_bg_attachment ); ?> !important;
			<?php
		endif;
		if ( $footer_main_bg_position ) :
			?>
		background-position: <?php echo esc_html( $footer_main_bg_position ); ?> !important;
			<?php
		endif;
		?>
	}
		<?php
	endif;
	if ( $footer_bottom_bg_color || $footer_bottom_bg_image || $footer_bottom_bg_repeat || $footer_bottom_bg_size || $footer_bottom_bg_attachment || $footer_bottom_bg_position ) :
		?>
	.footer .footer-bottom, .footer-wrapper.fixed .footer .footer-bottom {
		<?php
		if ( $footer_bottom_bg_color ) :
			?>
		background-color: <?php echo esc_html( $footer_bottom_bg_color ); ?> !important;
			<?php
		endif;
		if ( 'none' == $footer_bottom_bg_image ) :
			echo 'background-image: none !important';
		elseif ( $footer_bottom_bg_image ) :
			?>
		background-image: url('<?php echo esc_url( str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $footer_bottom_bg_image ) ); ?>') !important;
			<?php
		endif;
		if ( $footer_bottom_bg_repeat ) :
			?>
		background-repeat: <?php echo esc_html( $footer_bottom_bg_repeat ); ?> !important;
			<?php
		endif;
		if ( $footer_bottom_bg_size ) :
			?>
		background-size: <?php echo esc_html( $footer_bottom_bg_size ); ?> !important;
			<?php
		endif;
		if ( $footer_bottom_bg_attachment ) :
			?>
		background-attachment: <?php echo esc_html( $footer_bottom_bg_attachment ); ?> !important;
			<?php
		endif;
		if ( $footer_bottom_bg_position ) :
			?>
		background-position: <?php echo esc_html( $footer_bottom_bg_position ); ?> !important;
			<?php
		endif;
		?>
	}
		<?php
	endif;
	if ( $breadcrumbs_bg_color || $breadcrumbs_bg_image || $breadcrumbs_bg_repeat || $breadcrumbs_bg_size || $breadcrumbs_bg_attachment || $breadcrumbs_bg_position ) :
		?>
	.page-top {
		<?php
		if ( $breadcrumbs_bg_color ) :
			?>
		background-color: <?php echo esc_html( $breadcrumbs_bg_color ); ?> !important;
			<?php
		endif;
		if ( 'none' == $breadcrumbs_bg_image ) :
			echo 'background-image: none !important';
		elseif ( $breadcrumbs_bg_image ) :
			?>
		background-image: url('<?php echo esc_url( str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $breadcrumbs_bg_image ) ); ?>') !important;
					<?php
		endif;
		if ( $breadcrumbs_bg_repeat ) :
			?>
	background-repeat: <?php echo esc_html( $breadcrumbs_bg_repeat ); ?> !important;
			<?php
		endif;
		if ( $breadcrumbs_bg_size ) :
			?>
	background-size: <?php echo esc_html( $breadcrumbs_bg_size ); ?> !important;
			<?php
		endif;
		if ( $breadcrumbs_bg_attachment ) :
			?>
	background-attachment: <?php echo esc_html( $breadcrumbs_bg_attachment ); ?> !important;
			<?php
		endif;
		if ( $breadcrumbs_bg_position ) :
			?>
	background-position: <?php echo esc_html( $breadcrumbs_bg_position ); ?> !important;
			<?php
		endif;
		?>
	}
		<?php
	endif;
endif;

if ( isset( $porto_settings['mainmenu-toplevel-link-color-sticky'] ) && is_array( $porto_settings['mainmenu-toplevel-link-color-sticky'] ) ) :
	if ( ! empty( $porto_settings['mainmenu-toplevel-link-color-sticky']['regular'] ) ) :
		?>
		#header.sticky-header .main-menu > li.menu-item > a,
		#header.sticky-header .main-menu > li.menu-custom-content a { color: <?php echo esc_html( $porto_settings['mainmenu-toplevel-link-color-sticky']['regular'] ); ?> }
		<?php
	endif;
	if ( ! empty( $porto_settings['mainmenu-toplevel-link-color-sticky']['hover'] ) ) :
		?>
		#header.sticky-header .main-menu > li.menu-item:hover > a,
		#header.sticky-header .main-menu > li.menu-item.active:hover > a,
		#header.sticky-header .main-menu > li.menu-custom-content:hover a { color: <?php echo esc_html( $porto_settings['mainmenu-toplevel-link-color-sticky']['hover'] ); ?> }
		<?php
	endif;
	if ( ! empty( $porto_settings['mainmenu-toplevel-link-color-sticky']['active'] ) ) :
		?>
		#header.sticky-header .main-menu > li.menu-item.active > a,
		#header.sticky-header .main-menu > li.menu-custom-content.active a { color: <?php echo esc_html( $porto_settings['mainmenu-toplevel-link-color-sticky']['active'] ); ?> }
		<?php
	endif;
endif;


/* horizontal shop filter */
global $porto_shop_filter_layout;
if ( $porto_shop_filter_layout ) :

	if ( 'horizontal' === $porto_shop_filter_layout ) {
		?>
		<?php if ( $is_wide ) : ?>
		@media (min-width: 1500px) {
			.main-content-wrap .left-sidebar { <?php echo porto_filter_output( $left ); ?>: -20%; }
			.main-content-wrap .right-sidebar{ <?php echo porto_filter_output( $right ); ?>: -20%; }
			.main-content-wrap:not(.opened) .main-content { margin-<?php echo porto_filter_output( $left ); ?>: -20%; }
		}
	<?php endif; ?>

		<?php
	} elseif ( 'horizontal2' === $porto_shop_filter_layout ) {
		?>
		@media (min-width: 992px) and (max-width: <?php echo (int) $porto_settings['container-width'] + (int) $porto_settings['grid-gutter-width'] - 1; ?>px) {
			.porto-product-filters .widget-title,
			.woocommerce-ordering select { width: 140px; }
		}
		<?php
	} elseif ( 'offcanvas' === $porto_shop_filter_layout ) {
		?>
		<?php if ( in_array( $porto_layout, porto_options_both_sidebars() ) ) : ?>
			@media (min-width: 768px) {
				.main-content { flex: 0 0 auto; width: 66.6666% }
			}
			@media (min-width: 992px) {
				.main-content { flex: 0 0 auto; width: 75% }
			}
		<?php else : ?>
			.main-content { flex: 0 0 auto; width: 100% }
		<?php endif; ?>
		<?php if ( 'wide-both-sidebar' == $porto_layout ) : ?>
			@media (min-width: 1500px) {
				.main-content.col-lg-6 { width: 80% }
				.right-sidebar.col-lg-3 { width: 20% }
			}
		<?php endif; ?>
		<?php
	}
endif;

if ( class_exists( 'Woocommerce' ) && ! is_user_logged_in() && ( ! isset( $porto_settings['woo-account-login-style'] ) || ! $porto_settings['woo-account-login-style'] ) ) :
	if ( 'yes' !== get_option( 'woocommerce_enable_myaccount_registration' ) ) :
		?>
		#login-form-popup { max-width: 480px; }
		<?php
	endif;
endif;

// Gutenberg Template Style for Full Site Editing
$template_slug = get_page_template_slug();
if ( $template_slug && ! defined( 'ELEMENTOR_VERSION' ) && ! defined( 'WPB_VC_VERSION' ) ) {
	$blocks = porto_get_post_type_items(
		'wp_template',
		array(
			'post_name__in'  => $template_slug,
			'posts_per_page' => 1,
			'no_found_rows'  => true,
		),
		false
	);
	$id     = array_key_first( $blocks );
	if ( $id ) {
		$css = get_post_meta( $id, 'porto_blocks_style_options_css', true );
		if ( $css ) {
			echo wp_strip_all_tags( $css );
		}
	}
}
/* Gutenberg Template Part */
global $porto_gutenberg_tp;
if ( ! empty( $porto_gutenberg_tp ) ) {
	echo wp_strip_all_tags( $porto_gutenberg_tp );
	unset( $GLOBALS['porto_gutenberg_tp'] );
}
/* custom css */
$custom_css = '';
if ( ! defined( 'WPB_VC_VERSION' ) || ! vc_is_inline() ) {
	/* WPBakery Builder css and Gutenberg css */
	$block_ids = array();
	if ( 'header_builder_p' == $porto_settings['header-type-select'] ) {
		$hb_layout = porto_header_builder_layout();
		if ( is_array( $hb_layout ) && ! empty( $hb_layout['ID'] ) ) {
			$block_ids[] = (int) $hb_layout['ID'];
		}
	}
	$pt_id = get_the_ID();
	if ( is_singular( 'porto_builder' ) && ! in_array( $pt_id, $block_ids ) ) {
		$block_ids[] = $pt_id;
	}
	if ( ! empty( $porto_product_layout ) ) {
		if ( 'builder' == $porto_product_layout && ! empty( $porto_settings['product-single-content-builder'] ) ) {
			global $wpdb;
			$where   = is_numeric( $porto_settings['product-single-content-builder'] ) ? 'ID' : 'post_name';
			$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = 'porto_builder' AND $where = %s", sanitize_text_field( $porto_settings['product-single-content-builder'] ) ) );
			if ( $post_id && ! in_array( $post_id, $block_ids ) ) {
				$block_ids[] = (int) $post_id;
			}
		} else {
			$builder_id = porto_check_builder_condition( 'product' );
			if ( $builder_id && ! in_array( $builder_id, $block_ids ) ) {
				$block_ids[] = $builder_id;
			}
		}
	} elseif ( class_exists( 'Woocommerce' ) && ( is_shop() || is_product_category() || is_product_tag() ) ) {
		$builder_id = porto_check_builder_condition( 'shop' );
		if ( $builder_id && ! in_array( $builder_id, $block_ids ) ) {
			$block_ids[] = $builder_id;
		}
	}

	if ( ! empty( $block_ids ) ) {
		foreach ( $block_ids as $post_id ) {
			$css = get_post_meta( $post_id, 'porto_builder_css', true );
			if ( $css ) {
				echo wp_strip_all_tags( $css );
			}

			$css = get_post_meta( $post_id, 'porto_blocks_style_options_css', true );
			if ( $css ) {
				echo wp_strip_all_tags( $css );
			}
		}
	}

	/* Gutenberg style options css */
	if ( is_singular() && ! porto_is_elementor_preview() && ! in_array( $pt_id, $block_ids ) ) {
		$css = get_post_meta( $pt_id, 'porto_blocks_style_options_css', true );
		if ( $css ) {
			$custom_css .= $css;
		}
	}
}

/* header builder custom css */
if ( ! is_customize_preview() && ! porto_header_type_is_preset() ) {
	$current_layout = porto_header_builder_layout();
	if ( isset( $current_layout['custom_css'] ) && $current_layout['custom_css'] ) {
		$custom_css .= $current_layout['custom_css'];
	}
}
if ( ! empty( $porto_settings['css-code'] ) ) {
	$custom_css .= $porto_settings['css-code'];
}
if ( ! porto_is_elementor_preview() ) {
	$page_css = porto_get_meta_value( 'custom_css' );
	if ( $page_css ) {
		$custom_css .= $page_css;
	}
} else {
	?>
	.elementor-widget-porto_steps .elementor-widget-container:after { content: ''; display: table; clear: both }
	.elementor-edit-area-active .elementor-inner-section.h-100:first-child { margin-top: 0 }
	<?php
}
if ( $custom_css ) {
	echo wp_strip_all_tags( preg_replace( '#<style[^>]*>(.*)</style>#is', '$1', $custom_css ) );
}
