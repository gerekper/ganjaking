<?php
/**
 * Generates dynamic css styles for only special pages or post types
 * @package Porto
 * @author P-Themes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( function_exists( 'vc_is_inline' ) && vc_is_inline() && porto_is_ajax() ) {
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

if ( ! empty( $porto_settings['skin-color'] ) ) {
	$skin_color_escaped = esc_html( $porto_settings['skin-color'] );
} else {
	$skin_color_escaped = '#0088cc';
}
if ( ! empty( $porto_settings['light-color'] ) ) {
	$light_color_escaped = esc_html( $porto_settings['light-color'] );
} else {
	$light_color_escaped = '#ffffff';
}


/* logo css */
$logo_width        = ( isset( $porto_settings['logo-width'] ) && (int) $porto_settings['logo-width'] ) ? (int) $porto_settings['logo-width'] : 170;
$logo_width_wide   = ( isset( $porto_settings['logo-width-wide'] ) && (int) $porto_settings['logo-width-wide'] ) ? (int) $porto_settings['logo-width-wide'] : 250;
$logo_width_tablet = ( isset( $porto_settings['logo-width-tablet'] ) && (int) $porto_settings['logo-width-tablet'] ) ? (int) $porto_settings['logo-width-tablet'] : 110;
$logo_width_mobile = ( isset( $porto_settings['logo-width-mobile'] ) && (int) $porto_settings['logo-width-mobile'] ) ? (int) $porto_settings['logo-width-mobile'] : 110;
$logo_width_sticky = ( isset( $porto_settings['logo-width-sticky'] ) && (int) $porto_settings['logo-width-sticky'] ) ? (int) $porto_settings['logo-width-sticky'] : 80;
?>
#header .logo,
.side-header-narrow-bar-logo { max-width: <?php echo esc_html( $logo_width ); ?>px; }
@media (min-width: <?php echo (int) $porto_settings['container-width'] + (int) $porto_settings['grid-gutter-width']; ?>px) {
	#header .logo { max-width: <?php echo esc_html( $logo_width_wide ); ?>px; }
}
@media (max-width: 991px) {
	#header .logo { max-width: <?php echo esc_html( $logo_width_tablet ); ?>px; }
}
@media (max-width: 767px) {
	#header .logo { max-width: <?php echo esc_html( $logo_width_mobile ); ?>px; }
}
<?php if ( ! empty( $porto_settings['change-header-logo'] ) ) : ?>
	#header.sticky-header .logo { max-width: <?php echo esc_html( $logo_width_sticky * 1.25 ); ?>px; }
	<?php
endif;

/* loading overlay */
$loading_overlay = porto_get_meta_value( 'loading_overlay' );
if ( 'no' !== $loading_overlay && ( 'yes' === $loading_overlay || ( 'yes' !== $loading_overlay && ! empty( $porto_settings['show-loading-overlay'] ) ) ) ) :
	?>
	/* Loading Overlay */
	/*.loading-overlay-showing { overflow-x: hidden; }*/
	.loading-overlay-showing > .loading-overlay { opacity: 1; visibility: visible; transition-delay: 0; }
	.loading-overlay { transition: visibility 0s ease-in-out 0.5s, opacity 0.5s ease-in-out; position: absolute; bottom: 0; left: 0; opacity: 0; right: 0; top: 0; visibility: hidden; }
	.loading-overlay .loader { display: inline-block; border: 2px solid transparent; width: 40px; height: 40px; -webkit-animation: spin 0.75s infinite linear; animation: spin 0.75s infinite linear; border-image: none; border-radius: 50%; vertical-align: middle; position: absolute; margin: auto; left: 0; right: 0; top: 0; bottom: 0; z-index: 2; border-top-color: <?php echo porto_filter_output( $skin_color_escaped ); ?>; }
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
	.fixed-header #header .header-main .header-center { padding-top: <?php echo (int) $porto_settings['header-main-padding']['padding-top']; ?>px; padding-bottom: <?php echo (int) $porto_settings['header-main-padding']['padding-bottom']; ?>px }
<?php endif; ?>
<?php if ( isset( $porto_settings['header-main-padding-mobile'] ) && ( '' != $porto_settings['header-main-padding-mobile']['padding-top'] || '' != $porto_settings['header-main-padding-mobile']['padding-bottom'] ) ) : ?>
	@media (max-width: 991px) {
		#header .header-main .header-left,
		#header .header-main .header-center,
		#header .header-main .header-right,
		.fixed-header #header .header-main .header-left,
		.fixed-header #header .header-main .header-right,
		.fixed-header #header .header-main .header-center { padding-top: <?php echo (int) $porto_settings['header-main-padding-mobile']['padding-top']; ?>px; padding-bottom: <?php echo (int) $porto_settings['header-main-padding-mobile']['padding-bottom']; ?>px }
	}
<?php endif; ?>

/* breadcrumb type */
<?php
	$page_header_type = porto_get_meta_value( 'porto_page_header_shortcode_type' );
	$page_header_type = $page_header_type ? $page_header_type : porto_get_meta_value( 'breadcrumbs_type' );
	$page_header_type = $page_header_type ? $page_header_type : ( $porto_settings['breadcrumbs-type'] ? $porto_settings['breadcrumbs-type'] : '1' );
?>
<?php if ( 1 === (int) $page_header_type ) : ?>
	.page-top .page-title-wrap { line-height: 0; }
	<?php if ( isset( $porto_settings['breadcrumbs-bottom-border'] ) && ! empty( $porto_settings['breadcrumbs-bottom-border']['border-top'] ) && '0px' != $porto_settings['breadcrumbs-bottom-border']['border-top'] ) : ?>
		.page-top .page-title:not(.b-none):after { content: ''; position: absolute; width: 100%; left: 0; border-bottom: <?php echo esc_html( $porto_settings['breadcrumbs-bottom-border']['border-top'] ); ?> solid <?php echo porto_filter_output( $skin_color_escaped ); ?>; bottom: -<?php echo ( isset( $porto_settings['breadcrumbs-padding'] ) ? (int) porto_config_value( $porto_settings['breadcrumbs-padding']['padding-bottom'] ) : 0 ) + (int) porto_config_value( $porto_settings['breadcrumbs-bottom-border']['border-top'] ) + 12; ?>px; }
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
		.page-top ul.breadcrumb { -webkit-justify-content: center; -ms-flex-pack: center; justify-content: center }
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
		.right-sidebar.col-lg-3 { -ms-flex: 0 0 20%; flex: 0 0 20%; max-width: 20%; }
		.main-content.col-lg-9 { -ms-flex: 0 0 80%; flex: 0 0 80%; max-width: 80%; }
		.main-content.col-lg-6 { -ms-flex: 0 0 60%; flex: 0 0 60%; max-width: 60%; }
	}
	<?php
endif;

/* woocommerce single product */
if ( isset( $porto_product_layout ) && $porto_product_layout ) :
	?>
	.product-images .img-thumbnail .inner,
	.product-images .img-thumbnail .inner img { -webkit-transform: none; transform: none; }
	<?php if ( 'default' === $porto_product_layout ) : ?>

	<?php elseif ( 'extended' === $porto_product_layout ) : ?>
		.main-content { padding-top: 0; }
		@media (min-width: 992px) {
			.product-layout-extended .product_title { font-size: 38px; letter-spacing: -0.01em; }
		}
		.product-image-slider .center .inner:before { content: ''; position: absolute; left: 0; top: 0; width: 100%; height: 100%; z-index: 1; background: rgba(0, 0, 0, 0.07); }
		.product-image-slider .center .zoomContainer { z-index: 2; }
		.product-images .img-thumbnail .inner { border: none; }
		.product-images .product-image-slider.owl-carousel .img-thumbnail { padding-left: 0; padding-right: 0; }
		.product-layout-extended .product_title { display: inline-block; width: auto; margin: 0; vertical-align: middle; }
		.product-layout-extended .woocommerce-product-rating { margin-bottom: 20px; }
		.product-layout-extended .product-summary-wrap .price { font-size: 30px; line-height: 1; }
		.product-layout-extended .product-summary-wrap .product-share { margin-top: 0; }
		@media (min-width: 992px) {
			.product-layout-extended .product-summary-wrap .product-share { float: <?php echo porto_filter_output( $right ); ?>; margin-<?php echo porto_filter_output( $right ); ?>: 20px; }
			.share-links a { margin-top: 0; margin-bottom: 0; }
			p.price { display: inline-block; }
			.single-product-custom-block { float: <?php echo porto_filter_output( $right ); ?>; }
			.product-layout-extended .product-summary-wrap form.cart { text-align: <?php echo porto_filter_output( $right ); ?>; justify-content: flex-end }
		}
		.single-product .product-summary-wrap .product-share label:after { content: ':'; }
		.product-layout-extended .product-summary-wrap .description { clear: both; padding-top: 25px; border-top: 1px solid <?php echo ! $is_dark ? '#e7e7e7' : esc_html( $color_dark_3 ); ?>; }
		.product-layout-extended .product-nav { position: relative; float: <?php echo porto_filter_output( $right ); ?>; margin: 5.7px 10px 0; <?php echo porto_filter_output( $right ); ?>: 0; }
		.product-layout-extended .single_variation_wrap { display: inline-block; margin: 0; padding: 0; border: none; }
		@media (min-width: 576px) {
			.product-layout-extended .single_variation_wrap { vertical-align: middle; }
		}
		.product-layout-extended .product_meta { margin-bottom: 20px; }
		.product-layout-extended .single-variation-msg { margin-bottom: 10px; line-height: 1.4; }
		.product-layout-extended .product-summary-wrap form.cart { position: relative; border-top: 1px solid <?php echo ! $is_dark ? '#e7e7e7' : esc_html( $color_dark_3 ); ?>; padding-top: 1.25rem; }
		.product-layout-extended .entry-summary .quantity { position: relative; margin-<?php echo porto_filter_output( $right ); ?>: 20px; color: #8798a1; vertical-align: middle }
		.product-layout-extended .entry-summary .quantity .minus, 
		.product-layout-extended .entry-summary .quantity .qty,
		.product-layout-extended .entry-summary .quantity .plus { height: 24px; }
		.product-layout-extended .entry-summary .quantity .minus,
		.product-layout-extended .entry-summary .quantity .plus { border: none; position: relative; z-index: 2; left: 0; }
		.product-layout-extended .entry-summary .quantity .qty { width: 36px; border-width: 1px 1px 1px 1px; font-size: 13px; background: <?php echo ! $is_dark ? '#f4f4f4' : esc_html( $color_dark_3 ); ?>; }
		.product-layout-extended .entry-summary .quantity:before { content: 'QTY:'; font-size: 15px; font-weight: 600; color: <?php echo ! $is_dark ? '#222529' : '#fff'; ?>; line-height: 23px; }
		.product-layout-extended .product-summary-wrap .summary-before { margin-bottom: 2em; }
		.product-layout-extended .product-summary-wrap .woocommerce-variation.single_variation { margin-top: 20px; }
		.product-layout-extended .product-summary-wrap .yith-wcwl-add-to-wishlist { margin: 0 .5rem 5px }
		@media (min-width: 576px) {
			.product-layout-extended .product-summary-wrap .variations { display: inline-block; vertical-align: middle; margin-bottom: 5px; }
			.product-layout-extended .product-summary-wrap .variations tr { display: inline-block; margin-<?php echo porto_filter_output( $right ); ?>: 15px; }
			.product-layout-extended .product-summary-wrap .variations tr:last-child { margin-<?php echo porto_filter_output( $right ); ?>: 0; }
			.product-layout-extended .product-summary-wrap .variations td { padding-top: 0; padding-bottom: 0; }
			.product-layout-extended .product-summary-wrap .variations .label { padding-top: 4px; padding-bottom: 4px; }
			.product-layout-extended .product-summary-wrap .variations .reset_variations { display: none !important; }
			.product-layout-extended .product-summary-wrap .variations .filter-item-list { margin-top: 0; }
		}
		@media (max-width: 991px) {
			.product-layout-extended .woocommerce-product-rating { margin-top: 15px; }
			.product-layout-extended .product-summary-wrap .price { margin-bottom: 40px; }
			.product-layout-extended .product-nav { display: none; }
		}
		@media (max-width: 575px) {
			.product-layout-extended .product-summary-wrap .single_add_to_cart_button { padding: 0 1.875rem; }
			.product-layout-extended .entry-summary .quantity { display: -webkit-flex; display: -ms-flexbox; display: flex; margin-bottom: 20px; margin-top: 10px; -webkit-flex-basis: 100%; flex-basis: 100%; -ms-flex-preferred-size: 100%; }
			.product-layout-extended .entry-summary .quantity:before { margin-<?php echo porto_filter_output( $right ); ?>: 28px; }
		}
	<?php elseif ( 'grid' === $porto_product_layout ) : ?>
		.main-content { padding-bottom: 20px; }
		.product-images:hover .zoom { opacity: 0; }
		.product-images .img-thumbnail { background: none; }
		.product-images .img-thumbnail:hover .zoom { opacity: 1; background: none; }
		.product-images .img-thumbnail .inner { border: none; }
		.product-images .img-thumbnail img { width: 100%; }
		.product-summary-wrap .description { margin-bottom: 20px; }
		.product-summary-wrap .product_meta { margin-bottom: 0; padding-bottom: 0; border-bottom: none; }
		.single-product .product-summary-wrap .price { font-size: 25px; letter-spacing: 0; line-height: 1; }
		.product-layout-grid .product-summary-wrap .variations { width: 100%; }
		.product-layout-grid .product-summary-wrap .variations tr:not(:last-child) { border-bottom: 1px solid #e7e7e7; }
		.product-layout-grid .product-summary-wrap .variations tr td { padding-top: 15px; padding-bottom: 15px; }
		.product-layout-grid .product-summary-wrap .variations .label { width: 75px; }
		.product-layout-grid .product-summary-wrap .variations + .single_variation_wrap { margin-top: 0; }
		.product-layout-grid .product-summary-wrap .filter-item-list .filter-color { width: 28px; height: 28px; }
		.product-layout-grid .woocommerce-widget-layered-nav-list a:not(.filter-color),
		.product-layout-grid .filter-item { line-height: 26px; font-size: 13px; color: <?php echo ! $is_dark ? '#222529' : '#fff'; ?>; background-color: <?php echo ! $is_dark ? '#f4f4f4' : esc_html( $color_dark_3 ); ?>; }
		.product-layout-grid .product-summary-wrap .variations .filter-item-list { margin-top: 0; }
	<?php elseif ( 'full_width' === $porto_product_layout ) : ?>
		<?php if ( isset( $porto_settings['breadcrumbs-bottom-border'] ) && ! empty( $porto_settings['breadcrumbs-bottom-border']['border-top'] ) && '0px' != $porto_settings['breadcrumbs-bottom-border']['border-top'] ) : ?>
			#main.wide.column1 .main-content { padding-top: 30px; }
		<?php endif; ?>
		@media (max-width: 991px) {
			.product-layout-full_width .summary-before { max-width: none; }
		}
		.product-layout-full_width .product-images { margin-bottom: 0; }
		.product-layout-full_width .product-summary-wrap { margin-left: -<?php echo (int) $porto_settings['grid-gutter-width']; ?>px; margin-right: -<?php echo (int) $porto_settings['grid-gutter-width']; ?>px; }
		.product-layout-full_width .product-summary-wrap { padding-<?php echo porto_filter_output( $right ); ?>: 50px; }
		@media (max-width: 1199px) {
			.product-layout-full_width .product-summary-wrap { padding-<?php echo porto_filter_output( $right ); ?>: 30px; }
		}
		.product-layout-full_width .product-media { position: relative; }
		.product-layout-full_width .product-thumbnails { position: absolute; top: 20px; <?php echo porto_filter_output( $left ); ?>: 20px; z-index: 2; bottom: 20px; overflow-x: hidden; overflow-y: auto; -webkit-overflow-scrolling: touch; scroll-behavior: smooth; }
		.product-layout-full_width .product-thumbnails::-webkit-scrollbar { width: 5px; }
		.product-layout-full_width .product-thumbnails::-webkit-scrollbar-thumb { border-radius: 0px; background: <?php echo ! $is_dark ? 'rgba(204, 204, 204, 0.5)' : '#39404c'; ?>; }
		.main-boxed .product-layout-full_width .product-thumbnails,
		.single-product.boxed .product-layout-full_width .product-thumbnails { top: 20px; <?php echo porto_filter_output( $left ); ?>: 20px; }
		.product-layout-full_width .product-thumbnails .img-thumbnail { width: 100px; border: 1px solid rgba(0, 0, 0, 0.1); cursor: pointer; margin-bottom: 10px; background-color: rgba(244, 244, 244, 0.5); }
		.product-layout-full_width .product-thumbnails .img-thumbnail:last-child { margin-bottom: 0; }
		.single-product.boxed .product-layout-full_width .product-thumbnails .img-thumbnail { width: 80px; }
		.product-layout-full_width .product-thumbnails img { opacity: 0.5; }
		@media (max-width: 1679px) {
			.product-layout-full_width .product-thumbnails { top: 20px; <?php echo porto_filter_output( $left ); ?>: 20px; }
			.product-layout-full_width .product-thumbnails .img-thumbnail { width: 80px; }
			.single-product.boxed .product-layout-full_width .product-thumbnails .img-thumbnail { width: 70px; }
		}
		@media (max-width: 991px) {
			.product-layout-full_width .product-summary-wrap { padding-<?php echo porto_filter_output( $left ); ?>: 30px; }
			.product-layout-full_width .product-thumbnails { <?php echo porto_filter_output( $left ); ?>: 15px; top: 15px; }
		}
		.product-layout-full_width .product-summary-wrap .product-share { display: block; position: absolute; top: 0; <?php echo porto_filter_output( $right ); ?>: -30px; margin-top: 0; }
		.product-layout-full_width .product-summary-wrap .product-share label { margin: 0; font-size: 9px; letter-spacing: 0.05em; color: #c6c6c6; }
		.product-layout-full_width .product-summary-wrap .share-links a { display: block; margin: 0 auto 2px; border-radius: 0; }
		.product-layout-full_width .product-nav { <?php echo porto_filter_output( $right ); ?>: 30px; }
		.single-product-custom-block { margin-bottom: 20px; }
		.product-layout-full_width .product_title { font-size: 40px; line-height: 1; }
		.main-boxed .product-layout-full_width .product_title,
		.single-product.boxed .product-layout-full_width .product_title { font-size: 28px; }

		@media (max-width: 575px) {
			.product-layout-full_width .product-thumbnails .img-thumbnail { width: 60px; }
			.product-layout-full_width .product-summary-wrap { padding-left: <?php echo (int) $porto_settings['grid-gutter-width']; ?>px; padding-right: <?php echo (int) $porto_settings['grid-gutter-width']; ?>px; }
			.product-layout-full_width .product-summary-wrap .product-share { <?php echo porto_filter_output( $right ); ?>: 0; }
		}
		@media (max-width: 1680px) {
			.product-layout-full_width .product_title { font-size: 30px; }
		}
		.product-layout-full_width .product-summary-wrap .price { font-size: 25px; line-height: 1; letter-spacing: 0; }
		@media (min-width: 576px) {
			.product-layout-full_width .product-summary-wrap .variations tr { display: inline-block; margin-<?php echo porto_filter_output( $right ); ?>: 15px; }
			.product-layout-full_width .product-summary-wrap .variations tr:last-child { margin-<?php echo porto_filter_output( $right ); ?>: 0; }
			.product-layout-full_width .product-summary-wrap .variations td { padding-top: 0; padding-bottom: 0; }
			.product-layout-full_width .product-summary-wrap .variations .label { padding-top: 4px; padding-bottom: 4px; }
			.product-layout-full_width .product-summary-wrap .variations .reset_variations { display: none !important; }
			.product-layout-full_width .product-summary-wrap .variations .filter-item-list { margin-top: 0; }
		}
		.product-layout-full_width .product-summary-wrap form.cart { margin-bottom: 40px; }
		@media (min-width: 576px) {
			.product-layout-full_width .entry-summary .add_to_wishlist:before { border: none; color: <?php echo porto_filter_output( $skin_color_escaped ); ?> !important; }
		}
		.product-layout-full_width .entry-summary .quantity { margin-<?php echo porto_filter_output( $right ); ?>: 10px; }
		.product-layout-full_width .entry-summary .quantity .plus { font-family: inherit; font-size: 20px; line-height: 25px; font-weight: 200; }
		<?php if ( $porto_settings['border-radius'] ) { ?>
			.product-layout-full_width .product-thumbnails .img-thumbnail,
			.product-layout-full_width .product-thumbnails .img-thumbnail img,
			.product-layout-full_width .product-summary-wrap .single_add_to_cart_button { border-radius: 3px; }
			.product-layout-full_width .entry-summary .quantity .minus { border-radius: <?php echo porto_filter_output( $rtl ? '0 2px 2px 0' : '2px 0 0 2px' ); ?>; }
			.product-layout-full_width .entry-summary .quantity .plus { border-radius: <?php echo porto_filter_output( $rtl ? '2px 0 0 2px' : '0 2px 2px 0' ); ?>; }
			.product-layout-full_width .product-summary-wrap .share-links a { border-radius: 2px; }
		<?php } ?>
		.product-layout-full_width .filter-item-list .filter-color { width: 28px; height: 28px; }
		.product-layout-full_width .woocommerce-widget-layered-nav-list a:not(.filter-color),
		.product-layout-full_width .filter-item-list .filter-item { line-height: 26px; font-size: 13px; color: <?php echo ! $is_dark ? '#222529' : porto_filter_output( $light_color_escaped ); ?>; background-color: <?php echo ! $is_dark ? '#f4f4f4' : esc_html( $color_dark_3 ); ?>; }
		.product-layout-full_width .product-summary-wrap .product_meta { margin-bottom: 0; }
		.product-layout-full_width .product-summary-wrap .yith-wcwl-add-to-wishlist { margin: 0 .5rem 5px }
		.product-layout-full_width .related.products { margin-top: 2.5rem; }
	<?php elseif ( 'sticky_info' === $porto_product_layout ) : ?>
		div#main { overflow: hidden; }
		.product-layout-sticky_info .product-images { margin-bottom: 0; }
		.product-images .img-thumbnail:not(:last-child) { margin-bottom: 4px; }
		.product-images .img-thumbnail .inner { cursor: resize; }
		.product-images .img-thumbnail img { width: 100%; height: auto; }
		.product-images:hover .zoom { opacity: 0; }
		.product-images .img-thumbnail:hover .zoom { opacity: 1; background: none; }
		.product-layout-sticky_info .product-summary-wrap .variations .filter-item-list { margin-top: 0; }
		.product-layout-sticky_info .filter-item-list .filter-color { width: 28px; height: 28px; }
		.product-layout-sticky_info .woocommerce-widget-layered-nav-list a:not(.filter-color),
		.product-layout-sticky_info .filter-item-list .filter-item { line-height: 26px; font-size: 13px; color: <?php echo ! $is_dark ? '#222529' : porto_filter_output( $light_color_escaped ); ?>; background-color: <?php echo ! $is_dark ? '#f4f4f4' : esc_html( $color_dark_3 ); ?>; }
		.product-nav:before { line-height: 32px; }
		.single-product .share-links a { border-radius: 20px; background: #4c4c4c; margin-<?php echo porto_filter_output( $right ); ?>: 0.2em; }
		.single-product .product-share > * { display: inline-block; }
		.product-share label { margin-<?php echo porto_filter_output( $right ); ?>: 15px; }
		.woocommerce-tabs { clear: both; background: <?php echo ! $is_dark ? '#f4f4f4' : esc_html( $color_dark_3 ); ?>; padding-top: 70px; padding-bottom: 70px; position: relative; }
		body.boxed .woocommerce-tabs,
		.main-boxed .woocommerce-tabs { background: none; padding-top: 20px; padding-bottom: 0; }
		.woocommerce-tabs .tab-content { background: none; }
		.woocommerce-tabs:before, .woocommerce-tabs:after { content: ''; position: absolute; width: 30vw; height: 100%; top: 0; background: inherit; }
		.woocommerce-tabs:before { right: 100%; }
		.woocommerce-tabs:after { left: 100%; }
		.product-layout-sticky_info .product-share { margin-bottom: 40px; }
		.single-product-custom-block { margin-bottom: 2em; }
		@media (min-width: 992px) {
			.product-layout-sticky_info .product-share { float: <?php echo porto_filter_output( $right ); ?>; }
			.single-product-custom-block { float: <?php echo porto_filter_output( $left ); ?>; }
			.single-product-custom-block { width: 50%; padding-<?php echo porto_filter_output( $right ); ?>: <?php echo (int) $porto_settings['grid-gutter-width'] / 2; ?>px; }
			.woocommerce-tabs .resp-tabs-list li { font-size: 18px; margin-<?php echo porto_filter_output( $right ); ?>: 50px; }
		}
		.woocommerce-tabs .resp-tabs-list { text-align: center; }
		.woocommerce-tabs .resp-tabs-list li { position: relative; bottom: -1px; border-bottom-color: transparent !important; }
		.product-layout-sticky_info .product-summary-wrap .yith-wcwl-add-to-wishlist { margin: 0 .5rem 5px }
		.product-layout-sticky_info .related.products { margin-top: 2.5rem; }
	<?php elseif ( 'sticky_both_info' === $porto_product_layout ) : ?>
		@media (min-width: 1200px) {
			.product-layout-sticky_both_info .product_title { font-size: 32px; }
		}
		.product-layout-sticky_both_info .product-summary-wrap .product-share { margin-top: 0; margin-bottom: 20px; }
		.product-layout-sticky_both_info .product-nav { top: 30px; }
		@media (min-width: 768px) {
			.product-layout-sticky_both_info .product_title { float: <?php echo porto_filter_output( $left ); ?>; width: auto; margin-<?php echo porto_filter_output( $right ); ?>: 20px; }
			.product-layout-sticky_both_info .product-nav { position: relative; float: <?php echo porto_filter_output( $left ); ?>; <?php echo porto_filter_output( $right ); ?>: auto; top: 2px; }
			.product-layout-sticky_both_info .product-summary-wrap .product-share { float: <?php echo porto_filter_output( $right ); ?>; margin-top: 0; margin-bottom: 0; }
		}
		.product-layout-sticky_both_info .woocommerce-product-rating { clear: both; }
		.product-layout-sticky_both_info { padding-top: 10px; }
		.product-layout-sticky_both_info .summary-before { -webkit-order: 2; order: 2; -ms-flex-order: 2; }
		.product-layout-sticky_both_info .summary:last-child { -webkit-order: 3; order: 3; -ms-flex-order: 3; }
		.product-layout-sticky_both_info .product-images .img-thumbnail { margin-bottom: 4px; }
		.product-layout-sticky_both_info .product-images .img-thumbnail img { width: 100%; height: auto; }
		.product-layout-sticky_both_info .entry-summary .quantity:before { content: 'QTY:'; font-size: 15px; font-weight: 600; color: <?php echo ! $is_dark ? '#222529' : porto_filter_output( $light_color_escaped ); ?>; margin-<?php echo porto_filter_output( $right ); ?>: 10px; line-height: 24px; }
		.product-layout-sticky_both_info .entry-summary .quantity { -ms-flex-align: center; align-items: center; margin-bottom: 20px; -webkit-flex-basis: 100%; flex-basis: 100%; -ms-flex-preferred-size: 100%; }
		.product-layout-sticky_both_info .product-summary-wrap .variations { width: 100%; }
		.product-layout-sticky_both_info .product-summary-wrap .variations tr { border-bottom: 1px solid #e7e7e7; }
		.product-layout-sticky_both_info .product-summary-wrap .variations .label { width: 75px; }
		.product-layout-sticky_both_info .product-summary-wrap .variations tr td { padding-top: 15px; padding-bottom: 15px; }
		.product-layout-sticky_both_info .product-summary-wrap .variations tr:first-child td { padding-top: 0; }
		.product-layout-sticky_both_info .product_meta { text-transform: uppercase; }
		.product-layout-sticky_both_info .product_meta span span,
		.product-layout-sticky_both_info .product_meta span a,
		.product-layout-sticky_both_info .product-summary-wrap .stock { color: #4c4c4c; font-size: 14px; font-weight: 700; }
		.product-layout-sticky_both_info .product-summary-wrap .product_meta { margin-top: 30px; padding-bottom: 0; border-bottom: none; }
		.product-layout-sticky_both_info .product-summary-wrap .variations .filter-item-list { margin-top: 0; }
		.product-layout-sticky_both_info .filter-item-list .filter-color { width: 28px; height: 28px; }
		.product-layout-sticky_both_info .woocommerce-widget-layered-nav-list a:not(.filter-color),
		.product-layout-sticky_both_info .filter-item-list .filter-item { line-height: 26px; font-size: 13px; color: <?php echo ! $is_dark ? '#222529' : porto_filter_output( $light_color_escaped ); ?>; background-color: <?php echo ! $is_dark ? '#f4f4f4' : esc_html( $color_dark_3 ); ?>; }
		@media (min-width: 992px) {
			.woocommerce-tabs .resp-tabs-list li { font-size: 18px; margin-<?php echo porto_filter_output( $right ); ?>: 50px; }
		}
		#product-tab { margin-bottom: 2em; }
		.product-layout-sticky_both_info .single_variation_wrap, .product-layout-sticky_both_info .product-summary-wrap .cart:not(.variations_form) { border: none; margin-top: 0; }
		.product-layout-sticky_both_info .product-summary-wrap .yith-wcwl-add-to-wishlist { margin-top: 5px; margin-bottom: 10px; }
	<?php elseif ( 'transparent' === $porto_product_layout ) : ?>
		div#main { overflow: hidden; }
		.product-layout-transparent .product-summary-wrap,
		.product-layout-transparent .img-thumbnail,
		.product-layout-transparent .product-summary-wrap:before,
		.product-layout-transparent .product-summary-wrap:after,
		.product-layout-transparent .product-summary-wrap .zoomContainer .zoomWindow { background-color: <?php echo ! $is_dark ? '#f4f4f4' : esc_html( $color_dark_3 ); ?>; }
		.product-layout-transparent .product-summary-wrap { position: relative; padding-top: 40px; margin-bottom: 40px; }
		.product-layout-transparent .product-summary-wrap:before,
		.product-layout-transparent .product-summary-wrap:after { content: ''; position: absolute; top: 0; width: 30vw; height: 100%; }
		.product-layout-transparent .product-summary-wrap:before { right: 100%; }
		.product-layout-transparent .product-summary-wrap:after { left: 100%; }
		.product-layout-transparent .entry-summary .quantity .qty { background: none; }
		.product-layout-transparent .summary-before { margin-bottom: 29px; }
		.product-layout-transparent .summary { margin-bottom: 40px; padding-top: 10px; }
		.product-layout-transparent .product-nav { top: 10px; }
		#main.boxed .product-layout-transparent .product-summary-wrap { padding-top: <?php echo (int) $porto_settings['grid-gutter-width']; ?>px; margin-bottom: <?php echo (int) $porto_settings['grid-gutter-width']; ?>px; }
		#main.boxed .product-layout-transparent .summary-before { margin-bottom: <?php echo (int) $porto_settings['grid-gutter-width'] - 11; ?>px; }
		#main.boxed .product-layout-transparent .summary { margin-bottom: <?php echo (int) $porto_settings['grid-gutter-width']; ?>px; }
		.product-layout-transparent .summary-before { margin-top: -5px; padding: 0 <?php echo (int) $porto_settings['grid-gutter-width'] / 2 - 5; ?>px; display: -ms-flexbox; display: flex; -ms-flex-align: center; align-items: center; align-self: flex-start; }
		.product-layout-transparent .summary-before .product-images { width: 80%; -ms-flex-order: 2; order: 2; padding: 5px; }
		.product-layout-transparent .summary-before .product-thumbnails { width: 20%; }
		body.boxed .product-layout-transparent .summary-before .product-thumbnails { padding-left: 10px; }
		.woocommerce-tabs .resp-tabs-list { display: none; }
		.woocommerce-tabs h2.resp-accordion { display: block; }
		.woocommerce-tabs h2.resp-accordion:before { font-size: 20px; font-weight: 400; position: relative; top: -5px; }
		.woocommerce-tabs .tab-content { border-top: none; }

		.product-thumbs-vertical-slider .slick-arrow { text-indent: -9999px; width: 40px; height: 30px; display: block; margin-left: auto; margin-right: auto; position: relative; text-shadow: none; background: none; font-size: 30px; color: <?php echo ! $is_dark ? '#222529' : porto_filter_output( $light_color_escaped ); ?>; cursor: pointer; }
		.product-thumbs-vertical-slider .slick-arrow:before { content: '\e81b'; font-family: Porto; text-indent: 0; position: absolute; left: 0; width: 100%; line-height: 25px; top: 0; }
		.product-thumbs-vertical-slider .slick-next:before { content: '\e81c'; }
		.product-thumbs-vertical-slider .slick-next { margin-top: 10px; }
		.product-thumbs-vertical-slider .img-thumbnail { padding: 5px; border: none; }
		.product-thumbs-vertical-slider .img-thumbnail img { width: 100%; height: auto; -webkit-transform: none; transform: none; border: 1px solid #f4f4f4; }
		.product-thumbs-vertical-slider .img-thumbnail.selected img { border-color: <?php echo porto_filter_output( $skin_color_escaped ); ?> }
		@media (max-width: 767px) {
			.product-thumbs-vertical-slider .slick-prev, .product-thumbs-vertical-slider .slick-next { display: block !important; }
		}
		.product-layout-transparent .woocommerce-tabs .tab-content { background: none; }
		#product-tab { margin-bottom: 1.25rem; }
		.product-layout-transparent .product-thumbnails .img-thumbnail { cursor: pointer; }
		.product-layout-transparent .summary-before .labels { <?php echo porto_filter_output( $left ); ?>: calc(20% + .4em); }
		.product-layout-transparent .product-summary-wrap .variations { min-width: 60%; }
	<?php elseif ( 'centered_vertical_zoom' === $porto_product_layout ) : ?>
		@media (max-width: 991px) {
			.product-layout-centered_vertical_zoom .summary-before { max-width: none; }
		}
		.product-layout-centered_vertical_zoom .product-summary-wrap { margin-top: 20px; }
		.product-layout-centered_vertical_zoom .summary-before { display: -webkit-flex; display: -ms-flexbox; display: flex; }
		.product-layout-centered_vertical_zoom .summary-before .product-images { width: calc(100% - 110px); -webkit-order: 2; order: 2; -ms-flex-order: 2; }
		.product-layout-centered_vertical_zoom .summary-before .labels { <?php echo porto_filter_output( $left ); ?>: calc(<?php echo intval( 100 + $porto_settings['grid-gutter-width'] / 2 ); ?>px + 0.8em); }
		.product-layout-centered_vertical_zoom .summary-before .product-thumbnails { width: 110px; }
		.product-layout-centered_vertical_zoom .product-thumbnails .img-thumbnail { width: 100px; border: 1px solid rgba(0, 0, 0, 0.1); cursor: pointer; margin-bottom: 10px; }
		@media (max-width: 1679px) {
			.product-layout-centered_vertical_zoom .product-thumbnails .img-thumbnail { width: 80px; }
			.product-layout-centered_vertical_zoom .summary-before .product-images { width: calc(100% - 90px); }
			.product-layout-centered_vertical_zoom .summary-before .product-thumbnails { width: 90px; }
			.product-layout-centered_vertical_zoom .summary-before .labels { <?php echo porto_filter_output( $left ); ?>: calc(<?php echo intval( 80 + $porto_settings['grid-gutter-width'] / 2 ); ?>px + 0.8em); }
		}
		@media (max-width: 575px) {
			.product-layout-centered_vertical_zoom .product-thumbnails .img-thumbnail { width: 60px; }
			.product-layout-centered_vertical_zoom .summary-before .product-images { width: calc(100% - 60px); }
			.product-layout-centered_vertical_zoom .summary-before .product-thumbnails { width: 80px; }
		}
		.product-layout-centered_vertical_zoom .summary-before { -webkit-order: 2; order: 2; -ms-flex-order: 2; }
		.product-layout-centered_vertical_zoom .summary:last-child { -webkit-order: 3; order: 3; -ms-flex-order: 3; }
		.product-layout-centered_vertical_zoom .product_title { font-size: 1.5rem; }
		.product-layout-centered_vertical_zoom .product_title.show-product-nav { width: calc(100% - 50px); }
		.product-layout-centered_vertical_zoom .product-nav { <?php echo porto_filter_output( $right ); ?>: 0; }
		.product-layout-centered_vertical_zoom .product_meta { text-transform: uppercase; padding-bottom: 0; border-bottom: none; }
		.product-layout-centered_vertical_zoom .product_meta span span,
		.product-layout-centered_vertical_zoom .product_meta span a,
		.product-layout-centered_vertical_zoom .product-summary-wrap .stock { color: <?php echo ! $is_dark ? '#4c4c4c' : '#777'; ?>; font-size: 14px; font-weight: 700; }
		.product-layout-centered_vertical_zoom .product-summary-wrap .variations .filter-item-list { margin-top: 0; }
		.product-layout-centered_vertical_zoom .filter-item-list .filter-color { width: 28px; height: 28px; }
		.product-layout-centered_vertical_zoom .woocommerce-widget-layered-nav-list a:not(.filter-color),
		.product-layout-centered_vertical_zoom .filter-item-list .filter-item { line-height: 26px; font-size: 13px; color: <?php echo ! $is_dark ? '#222529' : porto_filter_output( $light_color_escaped ); ?>; background-color: <?php echo ! $is_dark ? '#f4f4f4' : esc_html( $color_dark_3 ); ?>; }
		.product-layout-centered_vertical_zoom .product-summary-wrap .variations { width: 100%; }
		.product-layout-centered_vertical_zoom .product-summary-wrap .variations tr { border-bottom: 1px solid #e7e7e7; }
		.product-layout-centered_vertical_zoom .product-summary-wrap .variations tr td { padding-top: 15px; padding-bottom: 15px; }
		.product-layout-centered_vertical_zoom .product-summary-wrap .variations tr:first-child td { padding-top: 0; }
		.product-layout-centered_vertical_zoom .product-summary-wrap .variations .label { width: 75px; }
		.product-layout-centered_vertical_zoom .product-summary-wrap .product-share { display: block; position: fixed; top: 50%; <?php echo porto_filter_output( $right ); ?>: 15px; margin: -100px 0 0; z-index: 99; }
		.product-layout-centered_vertical_zoom .product-summary-wrap .share-links a:not(:hover) { background: <?php echo ! $is_dark ? '#fff' : esc_html( $color_dark_3 ); ?>; }
		.product-layout-centered_vertical_zoom .product-summary-wrap .product-share label { margin: 0; font-size: 9px; letter-spacing: 0.05em; color: #c6c6c6; }
		.product-layout-centered_vertical_zoom .product-summary-wrap .share-links a { display: block; margin: 0 auto 2px; border-radius: 0; }
		.product-summary-wrap .single_variation_wrap, .product-summary-wrap .cart:not(.variations_form) { border: none; margin-top: 0; }

		.product-layout-centered_vertical_zoom .entry-summary .quantity:before { content: 'QTY:'; font-size: 15px; font-weight: 600; color: <?php echo ! $is_dark ? '#222529' : porto_filter_output( $light_color_escaped ); ?>; margin-<?php echo porto_filter_output( $right ); ?>: 10px; line-height: 24px; }
		.product-layout-centered_vertical_zoom .entry-summary .quantity { display: -ms-flexbox; display: flex; -ms-flex-align: center; align-items: center; margin-bottom: 20px; flex-basis: 100%; -ms-flex-preferred-size: 100%; }
		.product-layout-centered_vertical_zoom .sticky-product .quantity { display: -ms-inline-flexbox; display: inline-flex; margin-bottom: 0; }

		.product-summary-wrap .variations_button,
		.product-summary-wrap form.cart:not(.variations_form) { flex-direction: column; align-items: flex-start; }

		@media (min-width: 768px) {
			.woocommerce-tabs .resp-tabs-list li { margin-<?php echo porto_filter_output( $right ); ?>: 0; display: block; float: <?php echo porto_filter_output( $left ); ?>; clear: both; padding: 3px 0 10px !important; margin-bottom: 13px !important; position: relative; }
			.woocommerce-tabs .resp-tabs-list li:after { content: ''; position: absolute; width: 30vw; bottom: -2px; border-bottom: 1px solid <?php echo ! $is_dark ? '#dae2e6' : esc_html( $color_dark_4 ); ?>; z-index: 0; <?php echo porto_filter_output( $left ); ?>: 0; }
			.woocommerce-tabs .resp-tabs-list li:hover:before,
			.woocommerce-tabs .resp-tabs-list .resp-tab-active:before { content: ''; position: absolute; width: 100%; bottom: -2px; border-bottom: 1px solid #dae2e6; z-index: 1; border-bottom-color: inherit; }
			.woocommerce-tabs { display: table !important; width: 100%; }
			.woocommerce-tabs .resp-tabs-list,
			.woocommerce-tabs .resp-tabs-container { display: table-cell; vertical-align: top; }
			.woocommerce-tabs .resp-tabs-list { width: 20%; overflow: hidden; }
			.woocommerce-tabs .tab-content { padding-top: 0; border-top: none; padding-<?php echo porto_filter_output( $left ); ?>: 30px; }
		}
	<?php elseif ( 'left_sidebar' === $porto_product_layout ) : ?>
		@media (min-width: 1200px) {
			.product-summary-wrap .summary-before { -webkit-flex: 0 0 54%; -ms-flex: 0 0 54%; flex: 0 0 54%; max-width: 54%; }
			.product-summary-wrap .summary { -webkit-flex: 0 0 46%; -ms-flex: 0 0 46%; flex: 0 0 46%; max-width: 46%; }
		}
		.woocommerce-tabs .resp-tabs-list { display: none; }
		.woocommerce-tabs h2.resp-accordion { display: block; }
		.woocommerce-tabs h2.resp-accordion:before { font-size: 20px; font-weight: 400; position: relative; top: -4px; }
		.woocommerce-tabs .tab-content { border-top: none; padding-<?php echo porto_filter_output( $left ); ?>: 20px; }
		.left-sidebar .widget_product_categories { border: 1px solid <?php echo ! $is_dark ? '#e7e7e7' : esc_html( $color_dark_4 ); ?>; padding: 15px 30px; }
		.left-sidebar .widget_product_categories .current > a { color: <?php echo ! $is_dark ? '#222529' : porto_filter_output( $light_color_escaped ); ?>; text-transform: uppercase; }
		.left-sidebar .widget_product_categories li .toggle { font-size: 14px; }
		.left-sidebar .widget_product_categories li > .toggle:before { font-family: 'Porto'; content: '\e81c'; font-weight: 700; }
		.left-sidebar .widget_product_categories li.current >.toggle:before,
		.left-sidebar .widget_product_categories li.open >.toggle:before { content: '\e81b'; }
		.left-sidebar .widget_product_categories li.closed > .toggle:before { content: '\e81c'; }
		.sidebar .product-categories li > a { color: #7a7d82; font-weight: 600; }
		.product-images .zoom { opacity: 1; }
		#product-tab:not(:last-child) { margin-bottom: 2rem; }
	<?php elseif ( 'builder' === $porto_product_layout ) : ?>
		.product-layout-image { position: relative; }
		.product-layout-image .summary-before { flex: 0 0 100%; max-width: 100%; }
		.product-layout-image .labels { margin: 0; }
		.product-images-block .img-thumbnail { margin-bottom: <?php echo (int) $porto_settings['grid-gutter-width']; ?>px; }
		.product-images-block .product-images:hover .zoom { opacity: 0; }
		.product-images-block .img-thumbnail:hover .zoom { opacity: 1; background: none; }

		.product-layout-full_width .product-thumbnails { position: absolute; top: 15px; <?php echo porto_filter_output( $left ); ?>: 15px; z-index: 2; bottom: 20px; overflow-x: hidden; overflow-y: auto; -webkit-overflow-scrolling: touch; scroll-behavior: smooth; }
		.product-layout-full_width .product-thumbnails::-webkit-scrollbar { width: 5px; }
		.product-layout-full_width .product-thumbnails::-webkit-scrollbar-thumb { border-radius: 0px; background: <?php echo ! $is_dark ? 'rgba(204, 204, 204, 0.5)' : '#39404c'; ?>; }
		.product-layout-full_width .product-thumbnails .img-thumbnail { width: 80px; border: 1px solid rgba(0, 0, 0, 0.1); cursor: pointer; margin-bottom: 10px; background-color: rgba(244, 244, 244, 0.5); }
		.product-layout-full_width .product-thumbnails .img-thumbnail:last-child { margin-bottom: 0; }
		.product-layout-full_width .product-thumbnails img { opacity: 0.5; }
		@media (max-width: 575px) {
			.product-layout-full_width .product-thumbnails .img-thumbnail { width: 60px; }
		}

		.product-layout-transparent { margin-top: -5px; padding: 0 5px; display: -ms-flexbox; display: flex; -ms-flex-align: center; align-items: center; align-self: flex-start; -ms-flex-wrap: wrap; flex-wrap: wrap; }
		.product-layout-transparent .product-images { width: 80%; -ms-flex-order: 2; order: 2; padding: 5px; }
		.product-layout-transparent .product-thumbnails { width: 20%; }
		body.boxed .product-layout-transparent .summary-before .product-thumbnails { padding-left: 10px; }
		.product-thumbs-vertical-slider .slick-arrow { text-indent: -9999px; width: 40px; height: 30px; display: block; margin-left: auto; margin-right: auto; position: relative; text-shadow: none; background: none; font-size: 30px; color: <?php echo ! $is_dark ? '#222529' : porto_filter_output( $light_color_escaped ); ?>; cursor: pointer; }
		.product-thumbs-vertical-slider .slick-arrow:before { content: '\e81b'; font-family: Porto; text-indent: 0; position: absolute; left: 0; width: 100%; line-height: 25px; top: 0; }
		.product-thumbs-vertical-slider .slick-next:before { content: '\e81c'; }
		.product-thumbs-vertical-slider .slick-next { margin-top: 10px; }
		.product-thumbs-vertical-slider .img-thumbnail { padding: 5px; border: none; }
		.product-thumbs-vertical-slider .img-thumbnail img { width: 100%; height: auto; transform: none; border: 1px solid #f4f4f4; }
		.product-thumbs-vertical-slider .img-thumbnail.selected img { border-color: <?php echo porto_filter_output( $skin_color_escaped ); ?> }
		@media (max-width: 767px) {
			.product-thumbs-vertical-slider .slick-prev, .product-thumbs-vertical-slider .slick-next { display: block !important; }
		}
		.product-layout-transparent .product-thumbnails .img-thumbnail { cursor: pointer; }
		.product-layout-transparent .labels { <?php echo porto_filter_output( $left ); ?>: calc(20% + 5px + .8em); top: calc(5px + .8em); }

		.product-layout-centered_vertical_zoom { display: -ms-flexbox; display: flex; -ms-flex-wrap: wrap; flex-wrap: wrap; }
		.product-layout-centered_vertical_zoom .labels { <?php echo porto_filter_output( $left ); ?>: calc(90px + 0.8em); }
		.product-layout-centered_vertical_zoom .product-images { width: calc(100% - 90px); -webkit-order: 2; order: 2; -ms-flex-order: 2; }
		.product-layout-centered_vertical_zoom .product-thumbnails { width: 90px; }
		.product-layout-centered_vertical_zoom .product-thumbnails .img-thumbnail { width: 80px; border: 1px solid rgba(0, 0, 0, 0.1); cursor: pointer; margin-bottom: 10px; }
		@media (max-width: 575px) {
			.product-layout-centered_vertical_zoom .product-thumbnails .img-thumbnail { width: 60px; }
			.product-layout-centered_vertical_zoom .product-images { width: calc(100% - 60px); }
			.product-layout-centered_vertical_zoom .product-thumbnails { width: 80px; }
		}
		<?php
	endif;

	if ( ! empty( $porto_settings['show-skeleton-screen'] ) && in_array( 'product', $porto_settings['show-skeleton-screen'] ) ) :
		$skeleton_color = isset( $porto_settings['placeholder-color'] ) ? $porto_settings['placeholder-color'] : '#f4f4f4';
		if ( 'grid' == $porto_product_layout ) :
			?>
			.skeleton-body.product-layout-grid .summary-before:before {
				background-image: linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 100%, transparent 0),
					linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 100%, transparent 0),
					linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 100%, transparent 0),
					linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 100%, transparent 0);
				background-size: calc(50% - 10px) calc(50% - 10px);
				background-position: 0 0, right 0, 0 bottom, right bottom;
			}
			@media (max-width: 575px) {
				.skeleton-body.product-layout-grid .summary-before { padding-top: 400%; }
				.skeleton-body.product-layout-grid .summary-before:before {
					background-size: 100% calc(25% - 15px);
					background-position: 0 0, 0 33.3333%, 0 66.6666%, 0 bottom;
				}
			}
			<?php
		elseif ( 'sticky_info' == $porto_product_layout ) :
			?>
			.skeleton-body.product-layout-sticky_info .summary-before { padding-top: 146%; }
			.skeleton-body.product-layout-sticky_info .summary-before:before {
				background-size: 100% calc(33.3333% - 3px);
				background-position: 0 0, 0 50%, 0 bottom;
			}
			@media (max-width: 767px) {
				.skeleton-body.product-layout-sticky_info .summary-before { padding-top: 293%; }
			}
			<?php
		elseif ( 'sticky_both_info' == $porto_product_layout ) :
			?>
			.skeleton-body.product-layout-sticky_both_info .summary-before { order: 2; padding-top: 146%; }
			.skeleton-body.product-layout-sticky_both_info .tab-content { order: 4; }
			.skeleton-body.product-layout-sticky_both_info .summary + .summary { order: 3; }
			.skeleton-body.product-layout-sticky_both_info .summary-before:before {
				background-size: 100% calc(33.3333% - 3px);
				background-position: 0 0, 0 50%, 0 bottom;
			}
			@media (max-width: 991px) {
				.skeleton-body.product-layout-sticky_both_info .summary-before { padding-top: 293%; }
			}
			<?php
		elseif ( 'centered_vertical_zoom' == $porto_product_layout ) :
			?>
			.skeleton-body.product-layout-centered_vertical_zoom .summary-before { order: 2; padding-top: 40%; }
			.skeleton-body.product-layout-centered_vertical_zoom .tab-content { order: 4; }
			.skeleton-body.product-layout-centered_vertical_zoom .summary + .summary { order: 3; }
			.skeleton-body.product-layout-centered_vertical_zoom .summary-before:before {
				background-size: 79% 100%, 19% 23%, 19% 23%, 19% 23%, 19% 23%;
				background-position: right top, 0 0, 0 33.3333%, 0 66.6666%, 0 100%;
			}
			@media (max-width: 991px) {
				.skeleton-body.product-layout-centered_vertical_zoom .summary-before { padding-top: 80%; }
			}
			<?php
		elseif ( 'extended' == $porto_product_layout ) :
			?>
			.skeleton-body.product-layout-extended .summary-before { margin-bottom: 2rem; padding-top: 33.3333%; }
			.skeleton-body.product-layout-extended .summary-before:before {
				background-image: linear-gradient(#f4f4f4 100%,transparent 0),linear-gradient(#f4f4f4 100%,transparent 0),linear-gradient(#f4f4f4 100%,transparent 0);
				background-size: 33.2% 100%,33.2% 100%,33.2% 100%;
				background-position: 0 0, 50% 0, 100% 0;
			}
			<?php
		endif;
	endif;

	if ( porto_is_product() ) :
		if ( isset( $porto_settings['product-sticky-addcart'] ) && $porto_settings['product-sticky-addcart'] ) :
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
			.sticky-product .sticky-detail .price { font-family: 'Oswald'<?php echo isset( $porto_settings['h2-font'] ) && isset( $porto_settings['h2-font']['font-family'] ) ? ',' . sanitize_text_field( $porto_settings['h2-font']['font-family'] ) : '', isset( $porto_settings['h3-font'] ) && isset( $porto_settings['h3-font']['font-family'] ) ? ',' . sanitize_text_field( $porto_settings['h3-font']['font-family'] ) : ''; ?>, sans-serif; font-weight: 400; margin-bottom: 0; font-size: 1.3em; line-height: 1.5; }
			@media (max-width: 992px) {
				.sticky-product .container { padding-left: <?php echo (int) $porto_settings['grid-gutter-width']; ?>px; padding-right: <?php echo (int) $porto_settings['grid-gutter-width']; ?>px }
			}
			@media (max-width: 767px) {
				.sticky-product { display: none; }
			}
			<?php if ( 'sticky_both_info' == $porto_product_layout ) : ?>
				.entry-summary .sticky-product .quantity { margin-bottom: 5px; }
				.entry-summary:not(:last-child) [data-plugin-sticky] { z-index: 9; }
				<?php
			endif;
		endif;
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
	#footer {
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
	#footer .footer-main {
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
	#footer .footer-bottom, .footer-wrapper.fixed #footer .footer-bottom {
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

/* post type woocommerce */
$post_layout = isset( $porto_settings['post-layout'] ) ? $porto_settings['post-layout'] : 'full';
if ( 'woocommerce' === $post_layout && ( ! class_exists( 'Woocommerce' ) || ! is_woocommerce() ) && ( is_home() || is_archive() || is_singular( 'post' ) ) ) :
	?>
	article.post-woocommerce .post-date,
	article.post-woocommerce > .read-more,
	.pagination>a, .pagination>span,
	.pagination .prev, .pagination .next,
	.sidebar-content .widget-title,
	.widget .tagcloud,
	input[type="submit"], .btn,
	.related-posts .read-more { font-family: 'Oswald'<?php echo isset( $porto_settings['h3-font'] ) && isset( $porto_settings['h3-font']['font-family'] ) ? ',' . sanitize_text_field( $porto_settings['h3-font']['font-family'] ) : ''; ?>, sans-serif }
	article.post-full > .btn,
	.pagination>.dots { color: <?php echo porto_filter_output( $skin_color_escaped ); ?> !important; }
	.pagination>a:hover, .pagination>a:focus, .pagination>span.current { background-color: <?php echo porto_filter_output( $skin_color_escaped ); ?>; color: #fff; }

	.post.format-video .mejs-container .mejs-controls { opacity: 0; transition: opacity .25s; }
	.post.format-video .img-thumbnail:hover .mejs-container .mejs-controls { opacity: 1; }
	article.post-woocommerce { margin-<?php echo porto_filter_output( $left ); ?>: 90px; }
	article.post-woocommerce:after { content: ''; display: block; clear: both; }
	article.post-woocommerce h2.entry-title { color: #222529; font-size: 22px; font-weight: 600; letter-spacing: normal; line-height: 1.2; margin-bottom: 15px; }
	article.post-woocommerce h2.entry-title a { color: inherit; }
	article.post-woocommerce .post-image,
	article.post-woocommerce .post-date { margin-<?php echo porto_filter_output( $left ); ?>: -90px; }
	article.post-woocommerce .post-date { width: 60px; }
	article.post-woocommerce .post-date .day { font-size: 1.75rem; color: #222529; font-weight: 400; border: 1px solid #e3e3e3; border-bottom: none; }
	body article.post-woocommerce .post-date .day { color: #222529; background: none; }
	article.post-woocommerce .post-date .month { font-size: 14px; text-transform: uppercase; }
	article.post-woocommerce .post-meta { display: inline-block; margin-bottom: 6px; }
	article.post-woocommerce > .read-more { font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; float: <?php echo porto_filter_output( $right ); ?>; }
	article.post-woocommerce > .read-more:after { content: '\f04b'; font-family: 'Font Awesome 5 Free'; font-weight: 900; margin-<?php echo porto_filter_output( $left ); ?>: 3px; position: relative; top: -1px; }
	article.post-woocommerce .post-content { padding-bottom: 20px; border-bottom: 1px solid rgba(0, 0, 0, 0.06); margin-bottom: 15px; }
	article.post-woocommerce .post-meta { font-size: 13px; text-transform: uppercase; font-weight: 600; letter-spacing: 0; }
	article.post-woocommerce .post-meta a { color: #7b858a; }
	article.post-woocommerce .post-meta i,
	article.post-woocommerce .post-meta .post-views-icon.dashicons { font-size: 16px !important; }
	article.post-woocommerce .post-excerpt { font-size: 15px; line-height: 27px; color: #7b858a; }
	article.post-woocommerce .owl-carousel .owl-nav [class*="owl-"] { background: none; border: none; color: #9a9996; font-size: 30px; }
	article.post-woocommerce .owl-carousel .owl-nav .owl-prev { <?php echo porto_filter_output( $left ); ?>: 20px; }
	article.post-woocommerce .owl-carousel .owl-nav .owl-next { <?php echo porto_filter_output( $right ); ?>: 20px; }
	article.post-woocommerce .owl-carousel .owl-nav .owl-prev:before { content: '\e829'; }
	article.post-woocommerce .owl-carousel .owl-nav .owl-next:before { content: '\e828'; }

	.pagination>a, .pagination>span { padding: 0; min-width: 2.6em; width: auto; height: 2.8em; background: #d1f0ff; border: none; line-height: 2.8em; font-size: 15px; padding: 0 1em; }
	.pagination-wrap .pagination>a, .pagination-wrap .pagination>span { margin: 0 4px 8px; }
	.pagination>.dots { background: none; }
	.pagination .prev,
	.pagination .next { text-indent: 0; text-transform: uppercase; background: #272723; color: #fff; width: auto; }
	.pagination .prev:before,
	.pagination .next:before { display: none; }
	.pagination .prev i,
	.pagination .next i { font-size: 18px; }
	.pagination .prev i:before { content: '\f104'; }
	.pagination .next i:before { content: '\f105'; }
	.pagination span.dots { min-width: 1.8em; font-size: 15px; }

	/* sidebar */
	.widget .tagcloud a { font-size: 14px !important; text-transform: uppercase; color: #fff; background: #272723; padding: 12px 22px; border: none; border-radius: 3px; letter-spacing: 0.05em; }
	.sidebar-content { border: 1px solid #e7e7e7; padding: 20px; }
	.sidebar-content .widget:last-child { margin-bottom: 0; }
	.sidebar-content .widget .widget-title { font-size: 17px; font-weight: 400; }
	.widget-recent-posts { line-height: 1.25; }
	.widget-recent-posts a { color: #222529; font-size: 16px; font-weight: 600; line-height: 1.25; }
	.post-item-small .post-date { margin-top: 10px; }
	.post-item-small .post-image img { width: 60px; margin-<?php echo porto_filter_output( $right ); ?>: 5px; margin-bottom: 5px; }
	.widget_categories>ul li { padding: <?php echo porto_filter_output( $rtl ? '10px 15px 10px 0' : '10px 0 10px 15px' ); ?>; }
	.widget>ul li>ul { margin-top: 10px; }
	.widget>ul { font-size: 14px; }
	.widget_categories > ul li:before { border: none; content: '\e81a'; font-family: 'porto'; font-size: 15px; color: #222529; margin-<?php echo porto_filter_output( $right ); ?>: 15px; width: auto; height: auto; position: relative; top: -1px }
	.widget>ul { border-bottom: none; }
	<?php
endif;

/* single post */
if ( is_singular( 'post' ) ) :
	$post_layout = get_post_meta( get_the_ID(), 'post_layout', true );
	$post_layout = ( 'default' == $post_layout || ! $post_layout ) ? $porto_settings['post-content-layout'] : $post_layout;
	if ( 'woocommerce' === $post_layout ) :
		?>
		article.post-woocommerce { margin-<?php echo porto_filter_output( $left ); ?>: 0; }
		article.post-woocommerce .post-image, article.post-woocommerce .post-date { margin-<?php echo porto_filter_output( $left ); ?>: 0; }
		article.post-woocommerce .post-date { margin-<?php echo porto_filter_output( $right ); ?>: 30px; }
		.single-post article.post-woocommerce .post-content { padding-bottom: 0; margin-bottom: 20px; }
		.single-post #content hr { display: none; }
		.entry-content { padding-bottom: 30px; border-bottom: 1px solid rgba(0, 0, 0, 0.06); padding-<?php echo porto_filter_output( $left ); ?>: 90px; margin-bottom: 20px; }
		@media (min-width: 1200px) {
			.entry-content { padding-<?php echo porto_filter_output( $right ); ?>: 80px; }
		}
		.post-share { margin-top: 0; padding: 0; display: inline-block; }
		.post-share > * { display: inline-block; vertical-align: middle; }
		.post-share .share-links { margin-<?php echo porto_filter_output( $left ); ?>: 3px; }
		.post-share h3 { margin: 0; }
		.post-block h3, .post-share h3, article.post .comment-respond h3, article.portfolio .comment-respond h3, .related-posts .sub-title { color: #222529; font-size: 19px; font-weight: 700; text-transform: uppercase; line-height: 1.5; margin-bottom: 15px; }
		article.post-woocommerce .share-links a { width: 22px; height: 22px; border-radius: 11px; background: #939393; color: #fff; font-size: 11px; font-weight: 400; margin: <?php echo porto_filter_output( $rtl ? '2px 0 2px 4px' : '2px 4px 2px 0' ); ?>; }
		.post-meta { padding-<?php echo porto_filter_output( $left ); ?>: 90px; }
		.post-meta > * { vertical-align: middle; }
		article.post .post-meta>span, article.post .post-meta>.post-views { padding-<?php echo porto_filter_output( $right ); ?>: 20px; }
		.post-author { padding-bottom: 20px; border-bottom: 1px solid rgba(0, 0, 0, 0.06); margin-bottom: 2rem; }
		.post-author h3 { display: none; }
		.post-author .name a { color: #222529; font-size: 18px; text-transform: uppercase; }
		.post-author p { margin-bottom: 10px; font-size: 1em; line-height: 1.8; }
		article.post .comment-respond { margin-top: 0; }
		.comment-form input[type="text"], .comment-form textarea { box-shadow: none; }
		.comment-form input[type="text"] { padding: 10px 12px; }
		input[type="submit"], .btn { background: #272723; border: none; text-transform: uppercase; }
		.related-posts h3 { font-size: 19px; color: #222529; line-height: 26px; font-weight: 600; margin-top: 5px; margin-bottom: 15px; }
		.related-posts .meta-date { color: #7b858a; font-size: 13px; text-transform: uppercase; letter-spacing: 0; }
		.related-posts .meta-date i { font-size: 18px; margin-<?php echo porto_filter_output( $right ); ?>: 5px; }
		.related-posts .read-more { text-transform: uppercase; }
		.comment-form { background: none; border-radius: 0; padding: 0; }
		<?php
	endif;
endif;

/* horizontal shop filter */
global $porto_shop_filter_layout;
if ( $porto_shop_filter_layout ) :
	if ( 'horizontal' === $porto_shop_filter_layout || 'horizontal2' === $porto_shop_filter_layout ) {
		?>
		.woocommerce-result-count { font-size: 12px; color: #7a7d82; line-height: 2; -webkit-order: 2; order: 2; }
		.shop-loop-before { background: <?php echo ! $is_dark ? '#f4f4f4' : 'rgba(255, 255, 255, .02)'; ?>; padding: 12px 12px 2px; margin-bottom: 20px; }
		a.porto-product-filters-toggle, .shop-loop-before .woocommerce-ordering select, .shop-loop-before .woocommerce-viewing select { border: none; }
		@media (max-width: 767px) {
			.shop-loop-before .woocommerce-result-count { display: none; }
			.shop-loop-before .woocommerce-ordering select { width: 140px; }
		}
		<?php
	}
	if ( 'horizontal' === $porto_shop_filter_layout ) {
		?>
		@media (min-width: 992px) {
			.porto-product-filters-toggle { display: -webkit-flex; display: -ms-flexbox; display: flex; -webkit-align-items: center; -ms-flex-align: center; align-items: center; position: relative; padding: 14px; background: #f4f4f4; margin-bottom: 20px; }
			.shop-loop-before .porto-product-filters-toggle { padding: 0; background: none; margin-bottom: 10px; }
			.porto-product-filters-toggle a { position: relative; width: 46px; height: 26px; background: #e6e6e6; border-radius: 13px; transition: .3s linear; margin-<?php echo porto_filter_output( $left ); ?>: 8px; }
			.porto-product-filters-toggle a:before { content: ''; position: absolute; left: 0; width: 42px; height: 22px; background-color: #fff; border-radius: 11px; -webkit-transform: translate3d(2px, 2px, 0) scale3d(1, 1, 1); -ms-transform: translate3d(2px, 2px, 0) scale3d(1, 1, 1); transform: translate3d(2px, 2px, 0) scale3d(1, 1, 1); transition: .3s linear; }
			.porto-product-filters-toggle a:after { content: ''; position: absolute; left: 0; width: 22px; height: 22px; background-color: #fff; border-radius: 11px; box-shadow: 0 2px 2px rgba(0, 0, 0, 0.24); -webkit-transform: translate3d(2px, 2px, 0); -ms-transform: translate3d(2px, 2px, 0); transform: translate3d(2px, 2px, 0); transition: .2s ease-in-out; }
			.porto-product-filters-toggle a:active:after { width: 28px; -webkit-transform: translate3d(2px, 2px, 0); -ms-transform: translate3d(2px, 2px, 0); transform: translate3d(2px, 2px, 0); }
			.porto-product-filters-toggle.opened a:active:after { -webkit-transform: translate3d(16px, 2px, 0); -ms-transform: translate3d(16px, 2px, 0); transform: translate3d(16px, 2px, 0); }
			.porto-product-filters-toggle.opened a { background-color: <?php echo porto_filter_output( $skin_color_escaped ); ?>; }
			.porto-product-filters-toggle.opened a:before { -webkit-transform: translate3d(18px, 2px, 0) scale3d(0, 0, 0); -ms-transform: translate3d(18px, 2px, 0) scale3d(0, 0, 0); transform: translate3d(18px, 2px, 0) scale3d(0, 0, 0); }
			.porto-product-filters-toggle.opened a:after { -webkit-transform: translate3d(22px, 2px, 0); -ms-transform: translate3d(22px, 2px, 0); transform: translate3d(22px, 2px, 0); }
			.porto-product-filters-toggle a:hover { text-decoration: none; }

			.shop-loop-before { padding-left: 20px; padding-right: 20px; }
			.woocommerce-result-count { margin-<?php echo porto_filter_output( $left ); ?>: 10px; }
			.main-content-wrap { overflow: hidden; }
			.main-content-wrap .sidebar { transition: left .3s linear, right .3s linear, visibility .3s linear, z-index .3s linear; visibility: hidden; z-index: -1; }
			.main-content-wrap .left-sidebar { <?php echo porto_filter_output( $left ); ?>: -25%; }
			.main-content-wrap .right-sidebar { <?php echo porto_filter_output( $right ); ?>: -25%; }
			.main-content-wrap .main-content { transition: all 0.3s linear 0s; }
			.main-content-wrap:not(.opened) .main-content { margin-<?php echo porto_filter_output( $left ); ?>: -25%; max-width: 100%; -webkit-flex: 0 0 100%; -ms-flex: 0 0 100%; flex: 0 0 100%; min-height: 1px; }
			.column2-right-sidebar .main-content-wrap:not(.opened) .main-content { margin-<?php echo porto_filter_output( $right ); ?>: -25%; margin-<?php echo porto_filter_output( $left ); ?>: 0; }
			.main-content-wrap.opened .sidebar { z-index: 0; visibility: visible; }
			.main-content-wrap.opened .left-sidebar { <?php echo porto_filter_output( $left ); ?>: 0; }
			.main-content-wrap.opened .right-sidebar { <?php echo porto_filter_output( $right ); ?>: 0; }
			.main-content-wrap.opened .main-content { margin-<?php echo porto_filter_output( $left ); ?>: 0; }
			.column2-right-sidebar .main-content-wrap.opened .main-content { margin-<?php echo porto_filter_output( $right ); ?>: 0; }
			ul.products li.product-col { transition: width 0.3s linear 0s; }
		}
		@media (max-width: 767px) {
			.shop-loop-before { padding-left: 10px; padding-right: 10px; }
			.porto-product-filters-toggle + .woocommerce-ordering label { display: none; }
			.woocommerce-ordering select { width: 140px; }
		}
		<?php if ( $is_wide ) : ?>
		@media (min-width: 1500px) {
			.main-content-wrap .left-sidebar { <?php echo porto_filter_output( $left ); ?>: -20%; }
			.main-content-wrap .right-sidebar{ <?php echo porto_filter_output( $right ); ?>: -20%; }
			.main-content-wrap:not(.opened) .main-content { margin-<?php echo porto_filter_output( $left ); ?>: -20%; }
		}
	<?php endif; ?>
		body.woocommerce-page.archive .sidebar-content { border-bottom: none !important; }
		body.woocommerce-page.archive .sidebar-content aside.widget.woocommerce:last-child { border-bottom: 1px solid <?php echo ! $is_dark ? '#efefef' : 'rgba(255, 255, 255, .06)'; ?>; }
		.sidebar .sidebar-content .widget:not(.woocommerce) { display: none; }
		body.woocommerce .porto-products-filter-body > .sidebar,
		body.woocommerce .porto-products-filter-body > .main-content { padding-top: 0; }
		<?php
	} elseif ( 'horizontal2' === $porto_shop_filter_layout ) {
		?>
		.porto-product-filters.style2 { margin-bottom: 0 }
		.porto_widget_price_filter .widget-title { position: relative; }
		.porto_widget_price_filter .widget-title .toggle { display: inline-block; width: 1.8571em; height: 1.8571em; line-height: 1.7572em; position: absolute; <?php echo porto_filter_output( $right ); ?>: -7px; top: 50%; margin-top: -0.9em; padding: 0; cursor: pointer; font-family: "porto"; text-align: center; transition: all 0.25s; color: #222529; font-size: 17px; }
		.porto_widget_price_filter { font-weight: 500; }
		.porto_widget_price_filter .fields { display: -ms-flexbox; display: flex; align-items: center; }
		.porto_widget_price_filter .fields > *:not(:last-child) { margin-<?php echo porto_filter_output( $right ); ?>: 5px; }
		.porto_widget_price_filter label { font-size: 12px; margin-bottom: 0; }
		.porto_widget_price_filter .form-control { box-shadow: none; padding: 6px; width: 50px; }
		.porto_widget_price_filter .widget-title .toggle:before { content: "\e81c"; }
		.porto-product-filters .widget>div>ul, .porto-product-filters .widget>ul { font-size: 12px; font-weight: 600; border-bottom: none; text-transform: uppercase; padding: 0; }
		.porto_widget_price_filter .button { text-transform: uppercase; height: 33px; margin-<?php echo porto_filter_output( $left ); ?>: 9px }
		.porto-product-filters .widget>div>ul li, .porto-product-filters .widget>ul li { border-top: none; }
		.porto-product-filters .widget_product_categories ul li>a,
		.porto-product-filters .widget_product_categories ol li>a,
		.porto-product-filters .porto_widget_price_filter ul li>a,
		.porto-product-filters .porto_widget_price_filter ol li>a,
		.porto-product-filters .widget_layered_nav ul li>a,
		.porto-product-filters .widget_layered_nav ol li>a, 
		.porto-product-filters .widget_layered_nav_filters ul li>a,
		.porto-product-filters .widget_layered_nav_filters ol li>a,
		.porto-product-filters .widget_rating_filter ul li>a,
		.porto-product-filters .widget_rating_filter ol li>a { padding: 7px 0; }
		<?php if ( isset( $porto_settings['body-font'] ) && ! empty( $porto_settings['body-font']['line-height'] ) ) : ?>
			.porto-product-filters .widget_product_categories ul li .toggle {
				top: <?php echo ( 14 + floatval( $porto_settings['body-font']['line-height'] ) - 24 ) / 2; ?>px;
			}
		<?php endif; ?>
		.widget_product_categories ul li .toggle:before { content: '\f105' !important; font-weight: 900; font-family: 'Font Awesome 5 Free' !important; }

		.woocommerce-ordering label { display: none; }
		.woocommerce-ordering select { text-transform: uppercase; }
		.porto-product-filters .widget-title { font-family: inherit; }
		.porto-product-filters .widget-title .toggle { display: none; }
		.porto-product-filters .widget { display: block; max-width: none; width: auto; flex: none; padding: 0; background: <?php echo ! $is_dark ? '#fff url("' . PORTO_URI . '/images/select-bg.svg")' : esc_html( $color_dark_3 ) . ' url("' . PORTO_URI . '/images/select-bg-light.svg")'; ?> no-repeat; background-position: <?php echo porto_filter_output( $rtl ? '4' : '96' ); ?>% -13px; background-size: 26px 60px; margin-bottom: 10px; margin-top: 0; margin-<?php echo porto_filter_output( $right ); ?>: 10px; position: relative; font-size: .9286em; color: <?php echo ! $is_dark ? '#777' : '#999'; ?> }
		.porto-product-filters .widget:last-child { margin-<?php echo porto_filter_output( $right ); ?>: 0 }
		.porto-product-filters-body { display: -webkit-inline-flex; display: -ms-inline-flexbox; display: inline-flex; vertical-align: middle; }
		@media (min-width: 992px) {
			.porto-product-filters .widget-title { background: none; font-size: inherit !important; border-bottom: none; padding: 0; color: inherit !important; font-weight: 400; cursor: default; height: 34px; line-height: 34px; padding: 0 10px; width: 150px; color: inherit; margin-bottom: 0; transition: none; }
			.woocommerce-ordering select { width: 160px; }
			.porto-product-filters .widget>div>ul, .porto-product-filters .widget>ul, .porto-product-filters .widget > form { display: none; position: absolute; padding: 10px 15px 10px; top: 100%; margin-top: 9px; <?php echo porto_filter_output( $left ); ?>: 0; min-width: 220px; background: <?php echo ! $is_dark ? '#fff' : $porto_color_lib->lighten( $dark_color, 8 ); ?>; z-index: 99; box-shadow: 0 1px 3px rgba(0,0,0,0.15); }
			.porto-product-filters .opened .widget-title:before { content: ''; position: absolute; top: 100%; border-bottom: 11px solid <?php echo ! $is_dark ? '#e8e8e8' : $porto_color_lib->lighten( $dark_color, 12 ); ?>; border-left: 11px solid transparent; border-right: 11px solid transparent; <?php echo porto_filter_output( $left ); ?>: 20px; }
			.porto-product-filters .opened .widget-title:after { content: ''; position: absolute; top: 100%; border-bottom: 10px solid <?php echo ! $is_dark ? '#fff' : $porto_color_lib->lighten( $dark_color, 8 ); ?>; border-left: 10px solid transparent; border-right: 10px solid transparent; <?php echo porto_filter_output( $left ); ?>: 21px; margin-top: 1px; z-index: 999; }
		}
		@media (min-width: 992px) and (max-width: <?php echo (int) $porto_settings['container-width'] + (int) $porto_settings['grid-gutter-width'] - 1; ?>px) {
			.porto-product-filters .widget-title,
			.woocommerce-ordering select { width: 140px; }
		}
		<?php
	}
	if ( 'horizontal' === $porto_shop_filter_layout || 'horizontal2' === $porto_shop_filter_layout ) {
		?>
		@media (max-width: 991px) {
			.porto-product-filters .sidebar-toggle { margin-top: 50px; }
			.porto-product-filters.mobile-sidebar { position: fixed; }
			.porto-product-filters .widget { float: none; margin-right: 0; background: none; margin-bottom: 20px; width: 100%; }
			.porto-product-filters .row > .widget { padding-left: 10px !important; padding-right: 10px !important; }
			.porto-product-filters .porto-product-filters-body { height: 100%; overflow-x: hidden; overflow-y: scroll; padding: 30px 20px 20px; display: block !important; top: 0; box-shadow: none; }
			.porto-product-filters .widget-title { padding: 0; background: none; border-bottom: none; background: none; pointer-events: none; margin-bottom: 15px; }
			.porto-product-filters .widget-title .toggle { display: none; }
			.porto-product-filters .widget>div>ul, .porto-product-filters .widget>ul, .porto-product-filters .widget > form { display: block !important; }
			html.sidebar-opened body .porto-product-filters { -webkit-transform: translate(-260px); transform: translate(-260px); }
		}
		html.filter-sidebar-opened body > * { z-index: 0; }
		html.filter-sidebar-opened .porto-product-filters { z-index: 9001; transition: transform 0.3s ease-in-out; -webkit-transform: translate(0px); transform: translate(0px); }
		html.filter-sidebar-opened .page-wrapper { <?php echo porto_filter_output( $left ); ?>: 260px }
		html.filter-sidebar-opened .porto-product-filters .sidebar-toggle i:before { content: '\f00d'; }
		html.sidebar-opened body .porto-product-filters .sidebar-toggle i:before { content: '\f1de'; }
		<?php
	}
endif;

if ( class_exists( 'Woocommerce' ) && ! is_checkout() && ! is_user_logged_in() && ( ! isset( $porto_settings['woo-account-login-style'] ) || ! $porto_settings['woo-account-login-style'] ) ) :
	?>
	#login-form-popup { position: relative; width: 80%; max-width: 872px; margin-left: auto; margin-right: auto; }
	#login-form-popup .featured-box { margin-bottom: 0; box-shadow: none; border: none; }
	#login-form-popup .featured-box .box-content { padding: 25px 35px; }
	#login-form-popup .featured-box h2 { text-transform: uppercase; font-size: 15px; letter-spacing: 0.05em; font-weight: 600; line-height: 2; }
	.porto-social-login-section { background: #f4f4f2; text-align: center; padding: 20px 20px 25px; }
	.porto-social-login-section p { text-transform: uppercase; font-size: 12px; <?php echo isset( $porto_settings['h4-font'] ) && ! empty( $porto_settings['h4-font']['color'] ) ? 'color:' . esc_html( $porto_settings['h4-font']['color'] ) . ';' : ''; ?> font-weight: 600; margin-bottom: 8px; }
	#login-form-popup .col2-set { margin-left: -20px; margin-right: -20px; }
	#login-form-popup .col-1, #login-form-popup .col-2 { padding-left: 20px; padding-right: 20px; }
	@media (min-width: 992px) {
		#login-form-popup .col-1 { border-<?php echo porto_filter_output( $right ); ?>: 1px solid #f5f6f6; }
	}
	#login-form-popup .input-text { box-shadow: none; padding-top: 10px; padding-bottom: 10px; border-color: #ddd; border-radius: 2px; }
	#login-form-popup form label { font-size: 12px; line-height: 1; }
	#login-form-popup .form-row { margin-bottom: 20px; }
	#login-form-popup .button { border-radius: 2px; padding: 10px 24px; text-transform: uppercase; text-shadow: none; 
	<?php
	if ( isset( $porto_settings['add-to-cart-font'] ) && ! empty( $porto_settings['add-to-cart-font']['font-family'] ) ) {
		echo 'font-family: ' . sanitize_text_field( $porto_settings['add-to-cart-font']['font-family'] ) . ', sans-serif;'; }
	?>
	font-size: 12px; letter-spacing: 0.025em; color: #fff; }
	#login-form-popup label.inline { margin-top: 15px; float: <?php echo porto_filter_output( $right ); ?>; position: relative; cursor: pointer; line-height: 1.5; }
	#login-form-popup label.inline input[type=checkbox] { opacity: 0; margin-<?php echo porto_filter_output( $right ); ?>: 8px; margin-top: 0; margin-bottom: 0; }
	#login-form-popup label.inline span:before { content: ''; position: absolute; border: 1px solid #ddd; border-radius: 1px; width: 16px; height: 16px; <?php echo porto_filter_output( $left ); ?>: 0; top: 0; text-align: center; line-height: 15px; font-family: 'Font Awesome 5 Free'; font-weight: 900; font-size: 9px; color: #aaa; }
	#login-form-popup label.inline input[type=checkbox]:checked + span:before { content: '\f00c'; }
	#login-form-popup .social-button { text-decoration: none; margin-left: 10px; margin-right: 10px; }
	#login-form-popup .social-button i { font-size: 16px; margin-<?php echo porto_filter_output( $right ); ?>: 8px; }
	<?php if ( isset( $porto_settings['h4-font'] ) && ! empty( $porto_settings['h4-font']['color'] ) ) : ?>
		#login-form-popup p.status { color: <?php echo esc_html( $porto_settings['h4-font']['color'] ); ?>; }
	<?php endif; ?>
	#login-form-popup .lost_password { margin-top: -15px; font-size: 13px; margin-bottom: 0; }
	.porto-social-login-section .google-plus { background: #dd4e31; }
	.porto-social-login-section .facebook { background: #3a589d; }
	.porto-social-login-section .twitter { background: #1aa9e1; }
	<?php if ( 'yes' !== get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>
		#login-form-popup { max-width: 480px; }
	<?php endif; ?>
	html.panel-opened body > .mfp-bg { z-index: 9042; }
	html.panel-opened body > .mfp-wrap { z-index: 9043; }
	<?php
endif;

if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) :
	?>
	.vc_vc_column, .vc_vc_column_inner,
	.row > .vc_col-sm-12,
	.row > .vc_column_container { padding-left: <?php echo intval( $porto_settings['grid-gutter-width'] ) / 2; ?>px; padding-right: <?php echo intval( $porto_settings['grid-gutter-width'] ) / 2; ?>px; }
	.vc_vc_column:not(.col-half-section), .vc_vc_column_inner:not(.col-half-section) { max-width: 100% }
	.vc_vc_column > .vc_column_container, .vc_vc_column > .vc_column_container > .vc_column-inner,
	.vc_vc_column_inner > .vc_column_container, .vc_vc_column_inner > .vc_column_container > .vc_column-inner { height: 100% }
	.content-grid .content-grid-item.vc_column_container > .vc_column-inner,
	.vc_vc_column > .align-items-center > .vc_column-inner,
	.vc_vc_column > .align-items-end > .vc_column-inner,
	.vc_vc_column > .align-items-start > .vc_column-inner,
	.vc_vc_column_inner > .align-items-center > .vc_column-inner,
	.vc_vc_column_inner > .align-items-end > .vc_column-inner,
	.vc_vc_column_inner > .align-items-start > .vc_column-inner { height: auto }
	.no-padding > .vc_vc_column,
	.no-padding > .vc_vc_column_inner,
	.vc_row-no-padding > .vc_vc_column,
	.vc_row-no-padding > .vc_vc_column_inner,
	.vc_column-gap-1 > .vc_vc_column, .vc_column-gap-1 > .vc_vc_column_inner,
	.vc_column-gap-2 > .vc_vc_column, .vc_column-gap-2 > .vc_vc_column_inner,
	.vc_column-gap-3 > .vc_vc_column, .vc_column-gap-3 > .vc_vc_column_inner,
	.vc_column-gap-4 > .vc_vc_column, .vc_column-gap-4 > .vc_vc_column_inner,
	.vc_column-gap-5 > .vc_vc_column, .vc_column-gap-5 > .vc_vc_column_inner,
	.vc_column-gap-10 > .vc_vc_column, .vc_column-gap-10 > .vc_vc_column_inner,
	.vc_column-gap-15 > .vc_vc_column, .vc_column-gap-15 > .vc_vc_column_inner,
	.vc_column-gap-20 > .vc_vc_column, .vc_column-gap-20 > .vc_vc_column_inner,
	.vc_column-gap-25 > .vc_vc_column, .vc_column-gap-25 > .vc_vc_column_inner,
	.vc_column-gap-30 > .vc_vc_column, .vc_column-gap-30 > .vc_vc_column_inner,
	.vc_column-gap-35 > .vc_vc_column, .vc_column-gap-35 > .vc_vc_column_inner,
	.content-grid > .vc_vc_column,
	.content-grid > .vc_vc_column_inner { padding-left: 0; padding-right: 0 }
	.vc_row:after { content: ''; display: table; clear: both }

	.porto-ibanner { overflow: visible; min-height: 60px }
	.porto-ibanner-layer { min-width: 20px }
	.compose-mode .vc_porto_interactive_banner:hover { z-index: 9 }

	.compose-mode .vc_element .vc_element-container[class^="porto-"]:before,
	.compose-mode .vc_element .vc_element-container[class^="porto-"]:after { content: ''; display: block; min-height: .1px; }
	.vc_empty-placeholder,
	.compose-mode .vc_element.vc_vc_row>.vc_row>.vc_vc_column>.wpb_column>.vc_element-container>.vc_vc_row_inner .vc_vc_column_inner,
	.tab-content > .vc_element:last-child > div,
	.porto-ibanner-layer > .vc_element:last-child > div,
	.accordion .card-body > .vc_element:last-child > div { margin-bottom: 0 }
	.compose-mode .wpb_column>.wpb_wrapper>.vc_element:last-child>.wpb_content_element { margin-bottom: 35px }
	.vc_row .vc_vc_column > .vc_column_container>.wpb_wrapper.vc_column-inner,
	.vc_row-has-fill+.vc_vc_row>.vc_row>.vc_vc_column>.vc_column_container>.vc_column-inner,
	.compose-mode .vc_vc_row>[data-vc-full-width=true],
	.compose-mode .vc_vc_row_inner,
	.compose-mode .vc_vc_section>[data-vc-full-width=true],
	.compose-mode .vc_vc_row>.vc_parallax,
	.compose-mode .vc_vc_section>.vc_parallax,
	.compose-mode .vc_col-sm-1 .vc_vc_accordion,
	.compose-mode .vc_col-sm-1 .vc_vc_tour,
	.compose-mode .vc_col-sm-2 .vc_vc_accordion,
	.compose-mode .vc_col-sm-2 .vc_vc_tour,
	.compose-mode .vc_col-sm-3 .vc_vc_accordion,
	.compose-mode .vc_col-sm-3 .vc_vc_tour,
	.compose-mode .vc_col-sm-4 .vc_vc_accordion,
	.compose-mode .vc_col-sm-4 .vc_vc_tour { padding-top: 0 }
	.compose-mode .vc_vc_row>[data-vc-full-width=true].section { padding-top: 50px }
	.compose-mode .vc_empty-shortcode-element.vc_vc_separator { min-height: 4px; padding-top: 1px; padding-bottom: 1px }
	.compose-mode .vc_container-block[class*=" vc_porto_"] { min-height: 60px; }
	.compose-mode .vc_container-block.vc_porto_interactive_banner_layer,
	.compose-mode .vc_element .porto-ibanner-layer { min-height: 20px }
	.compose-mode .vc_element.vc_hold-active > .vc_controls { opacity: 1; animation: fadeOut .7s; }
	.compose-mode .vc_control-btn-append:before { content: none }
	.compose-mode .vc_control-btn-append .vc_btn-content { padding: 7px; border-radius: 0 }
	.compose-mode .vc_control-btn-append { position: static }
	.compose-mode .vc_controls-column>div>.vc_active.vc_element .vc_advanced,.compose-mode .vc_controls-column>div>.vc_active.vc_parent .vc_advanced,.compose-mode .vc_controls-container>div>.vc_active.vc_element .vc_advanced,.compose-mode .vc_controls-container>div>.vc_active.vc_parent .vc_advanced,.compose-mode .vc_controls-row>div>.vc_active.vc_element .vc_advanced,.compose-mode .vc_controls-row>div>.vc_active.vc_parent .vc_advanced { width: 150px }
	.compose-mode .vc_controls-column>div>.vc_active.parent-vc_row .vc_advanced,.compose-mode .vc_controls-column>div>.vc_active.parent-vc_row_inner .vc_advanced,.compose-mode .vc_controls-container>div>.vc_active.parent-vc_row .vc_advanced,.compose-mode .vc_controls-container>div>.vc_active.parent-vc_row_inner .vc_advanced,.compose-mode .vc_controls-row>div>.vc_active.parent-vc_row .vc_advanced,.compose-mode .vc_controls-row>div>.vc_active.parent-vc_row_inner .vc_advanced { width: 180px }
	.compose-mode .vc_controls-column>div>.vc_active.element-vc_column .vc_advanced,.compose-mode .vc_controls-column>div>.vc_active.element-vc_column_inner .vc_advanced,.compose-mode .vc_controls-container>div>.vc_active.element-vc_column .vc_advanced,.compose-mode .vc_controls-container>div>.vc_active.element-vc_column_inner .vc_advanced,.compose-mode .vc_controls-row>div>.vc_active.element-vc_column .vc_advanced,.compose-mode .vc_controls-row>div>.vc_active.element-vc_column_inner .vc_advanced { width: 120px }
	.compose-mode .vc_control-btn-prepend .vc-c-icon-add:after,
	.compose-mode .vc_control-btn-append .vc-c-icon-add:after { content: "\f060"; font-family: 'Font Awesome 5 Free'; font-weight: 900; position: absolute; font-size: .5em; bottom: -3px; <?php echo porto_filter_output( $left ); ?>: -3px; }
	.compose-mode .vc_control-btn-append .vc-c-icon-add:after { content: "\f061"; <?php echo porto_filter_output( $left ); ?>: auto; <?php echo porto_filter_output( $right ); ?>: -3px; }
	.compose-mode .vc_control-btn .vc-composer-icon { display: block }

	.vc_column-inner > .vc_container-block[class*=" vc_porto_"]:first-child > .vc_controls > .vc_controls-out-tl { <?php echo porto_filter_output( $left ); ?>: 280px; z-index: 1003 }
	.vc_column-inner .vc_container-block[class*=" vc_porto_"] .vc_container-block[class*=" vc_porto_"]:first-child > .vc_controls > .vc_controls-out-tl { <?php echo porto_filter_output( $left ); ?>: 0; top: 0 }
	.vc_column-inner .vc_container-block[class*=" vc_porto_"] .vc_container-block[class*=" vc_porto_"] > .vc_controls > .vc_controls-out-tl { z-index: 1004 }
	.vc_column-inner .vc_container-block[class*=" vc_porto_"] .vc_container-block[class*=" vc_porto_"]:first-child .vc_container-block[class*=" vc_porto_"]:first-child > .vc_controls > .vc_controls-out-tl { <?php echo porto_filter_output( $left ); ?>: 3px; top: 30px }
	.compose-mode .vc_container-block.vc_porto_interactive_banner_layer > .vc_controls > .vc_controls-out-tl { top: -30px !important; <?php echo porto_filter_output( $left ); ?>: -2px !important }

	.compose-mode .vc_container-block[class*=" vc_porto_"]:hover { outline: 1px dashed rgba(0, 136, 204, .4) }
	.vc_row-has-fill > .vc_row > .vc_vc_column > .vc_column_container > .vc_column-inner > .vc_container-block[class*=" vc_porto_"],
	.vc_row-has-fill > .vc_vc_column_inner > .vc_column_container > .vc_column-inner > .vc_container-block[class*=" vc_porto_"] { margin-top: 0 }
	.compose-mode .vc_container-block.vc_porto_interactive_banner_layer,
	.compose-mode .vc_container-block.h-100[class*=" vc_porto_"] { margin-top: 0; margin-bottom: 0 }
	.compose-mode .curved-border .vc_container-block[class*=" vc_porto_"] { margin-bottom: 0 }
	.compose-mode .curved-border-top .vc_container-block[class*=" vc_porto_"] { margin-top: 0; margin-bottom: 30px }

	.wpb_tab.vc_clearfix:after { display: block; }
	.wpb_tabs .nav-tabs .nav-item { margin-bottom: -1px; }
	.tabs-vertical:before, .tabs-vertical:after { content: none }

	<?php
	$porto_banner_pos = porto_get_meta_value( 'banner_pos' );
	if ( ( 'below_header' == $porto_banner_pos || 'fixed' == $porto_banner_pos || 'fixed' == porto_get_meta_value( 'header_view' ) ) || 'fixed' == $porto_settings['header-view'] ) :
		?>
		.compose-mode .page-content > .vc_vc_row:nth-child(2):hover { z-index: 1002 }
		<?php
	endif;
endif;

/* custom css */
$custom_css = '';
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
}
if ( $custom_css ) {
	echo wp_strip_all_tags( preg_replace( '#<style[^>]*>(.*)</style>#is', '$1', $custom_css ) );
}
