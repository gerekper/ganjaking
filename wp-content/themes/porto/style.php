<?php
/**
 * @package Porto
 * @author P-Theme
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $porto_is_dark, $porto_settings, $porto_save_settings_is_rtl, $porto_settings_optimize;
$porto_settings_backup = $porto_settings;
$b                     = porto_check_theme_options();
$porto_settings        = $porto_settings_backup;
$porto_is_dark         = ( 'dark' == $b['css-type'] );
$dark                  = $porto_is_dark;

if ( ( isset( $porto_save_settings_is_rtl ) && $porto_save_settings_is_rtl ) || is_rtl() ) {
	$left  = 'right';
	$right = 'left';
	$rtl   = true;
} else {
	$left  = 'left';
	$right = 'right';
	$rtl   = false;
}

if ( ! function_exists( 'porto_if_dark' ) ) {
	function porto_if_dark( $if, $else = '' ) {
		global $porto_is_dark;
		if ( $porto_is_dark ) {
			return $if;
		}
		return $else;
	}
}
if ( ! function_exists( 'porto_if_light' ) ) {
	function porto_if_light( $if, $else = '' ) {
		global $porto_is_dark;
		if ( ! $porto_is_dark ) {
			return $if;
		}
		return $else;
	}
}
if ( ! function_exists( 'porto_calc_container_width' ) ) :
	function porto_calc_container_width( $container, $flag, $container_width, $grid_gutter_width ) {
		if ( ! $flag ) :
			?>
			@media (min-width: 768px) {
				<?php echo esc_html( $container ); ?> { max-width: <?php echo 720 + $grid_gutter_width; ?>px; }
			}
			@media (min-width: 992px) {
				<?php echo esc_html( $container ); ?> { max-width: <?php echo 960 + $grid_gutter_width; ?>px; }
			}
			@media (min-width: <?php echo esc_html( $container_width + $grid_gutter_width ); ?>px) {
				<?php echo esc_html( $container ); ?> { max-width: <?php echo esc_html( $container_width + $grid_gutter_width ); ?>px; }
			}
		<?php else : ?>
			@media (min-width: 768px) {
				<?php echo esc_html( $container ); ?> { max-width: <?php echo 720 - $grid_gutter_width; ?>px; }
			}
			@media (min-width: 992px) {
				<?php echo esc_html( $container ); ?> { max-width: <?php echo 960 - $grid_gutter_width; ?>px; }
				<?php echo esc_html( $container ); ?> .container { max-width: <?php echo 960 - $grid_gutter_width * 2; ?>px; }
			}
			@media (min-width: <?php echo esc_html( $container_width + $grid_gutter_width ); ?>px) {
				<?php echo esc_html( $container ); ?> { max-width: <?php echo esc_html( $container_width - $grid_gutter_width ); ?>px; }
				<?php echo esc_html( $container ); ?> .container { max-width: <?php echo esc_html( $container_width - $grid_gutter_width * 2 ); ?>px; }
			}
			<?php
		endif;
	}
endif;

require_once( PORTO_LIB . '/lib/color-lib.php' );
$porto_color_lib = PortoColorLib::getInstance();

if ( ! function_exists( 'porto_background_opacity' ) ) {
	function porto_background_opacity( $porto_color_lib, $bg_color, $opacity ) {
		if ( empty( $bg_color ) ) {
			return;
		}
		if ( 'transparent' == $bg_color || ! $opacity ) {
			echo 'box-shadow: none;';
		} else {
			echo 'background-color: rgba(' . $porto_color_lib->hexToRGB( $bg_color ) . ',' . $opacity . ');';
		}
	}
}

if ( $dark ) {
	$color_dark = $b['color-dark'];
} else {
	$color_dark = '#222529';
}
$color_dark_inverse = '#fff';
$color_dark_1       = $color_dark;
$color_dark_2       = $porto_color_lib->lighten( $color_dark_1, 2 );
$color_dark_3       = $porto_color_lib->lighten( $color_dark_1, 5 );
$color_dark_4       = $porto_color_lib->lighten( $color_dark_1, 8 );
$color_dark_5       = $porto_color_lib->lighten( $color_dark_1, 3 );
$color_darken_1     = $porto_color_lib->darken( $color_dark_1, 2 );

$dark_bg           = $color_dark;
$dark_default_text = '#808697';

if ( $dark ) {
	$color_price = '#eee';

	$widget_bg_color       = $color_dark_3;
	$widget_title_bg_color = $color_dark_4;
	$widget_border_color   = 'transparent';

	$input_border_color = $color_dark_3;
	$image_border_color = $color_dark_4;
	$color_widget_title = '#fff';

	$price_slide_bg_color = $color_dark;
	$panel_default_border = $color_dark_3;
} else {
	$color_price = '#444';

	$widget_bg_color       = '#fbfbfb';
	$widget_title_bg_color = '#f5f5f5';
	$widget_border_color   = '#ddd';

	$input_border_color   = 'rgba(0, 0, 0, 0.09)';
	$image_border_color   = '#ddd';
	$color_widget_title   = '#313131';
	$price_slide_bg_color = '#eee';
	$panel_default_border = '#ddd';
}

$screen_large = '(max-width: ' . ( $b['container-width'] + $b['grid-gutter-width'] - 1 ) . 'px)';
$input_lists  = 'input[type="email"],
				input[type="number"],
				input[type="password"],
				input[type="search"],
				input[type="tel"],
				input[type="text"],
				input[type="url"],
				input[type="color"],
				input[type="date"],
				input[type="datetime"],
				input[type="datetime-local"],
				input[type="month"],
				input[type="time"],
				input[type="week"]';


$body_mobile_font_size_scale   = ( 0 == (float) $b['body-font']['font-size'] || 0 == (float) $b['body-mobile-font']['font-size'] ) ? 1 : ( (float) $b['body-mobile-font']['font-size'] / (float) $b['body-font']['font-size'] );
$body_mobile_line_height_scale = ( 0 == (float) $b['body-font']['line-height'] || 0 == (float) $b['body-mobile-font']['line-height'] ) ? 1 : ( (float) $b['body-mobile-font']['line-height'] / (float) $b['body-font']['line-height'] );

$header_bg_empty     = ( empty( $b['header-bg']['background-color'] ) || 'transparent' == $b['header-bg']['background-color'] ) && ( empty( $b['header-bg']['background-image'] ) || 'none' == $b['header-bg']['background-image'] );
$breadcrumb_bg_empty = ( empty( $b['breadcrumbs-bg']['background-color'] ) || 'transparent' == $b['breadcrumbs-bg']['background-color'] ) && ( empty( $b['breadcrumbs-bg']['background-image'] ) || 'none' == $b['breadcrumbs-bg']['background-image'] );
$content_bg_empty    = ( empty( $b['content-bg']['background-color'] ) || 'transparent' == $b['content-bg']['background-color'] ) && ( empty( $b['content-bg']['background-image'] ) || 'none' == $b['content-bg']['background-image'] );
$footer_bg_empty     = ( empty( $b['footer-bg']['background-color'] ) || 'transparent' == $b['footer-bg']['background-color'] ) && ( empty( $b['footer-bg']['background-image'] ) || 'none' == $b['footer-bg']['background-image'] );
?>
/*-------------------- layout --------------------- */
body {
	font-family: <?php echo sanitize_text_field( $b['body-font']['font-family'] ); ?>, sans-serif;
	<?php if ( $b['body-font']['font-weight'] ) : ?>
		font-weight: <?php echo esc_html( $b['body-font']['font-weight'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['body-font']['font-size'] ) : ?>
		font-size: <?php echo esc_html( $b['body-font']['font-size'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['body-font']['line-height'] ) : ?>
		line-height: <?php echo esc_html( $b['body-font']['line-height'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['body-font']['letter-spacing'] ) : ?>
		letter-spacing: <?php echo esc_html( $b['body-font']['letter-spacing'] ); ?>;
	<?php endif; ?>
}
<?php if ( $b['body-font']['line-height'] ) : ?>
	li, .porto-u-sub-heading, .porto-sicon-description { line-height: <?php echo esc_html( $b['body-font']['line-height'] ); ?>; }
<?php endif; ?>

<?php if ( ! class_exists( 'Woocommerce' ) ) : ?>
	h1, h2, h3, h4, h5, h6 { letter-spacing: -0.05em; color: <?php echo porto_if_dark( '#fff', $color_dark ); ?>; }
<?php else : ?>
	h1, h2, h3, h4, h5, h6 { color: <?php echo porto_if_dark( '#fff', $color_dark ); ?>; }
<?php endif; ?>

<?php
	$fonts_settings = array(
		'h1'             => 'h1',
		'h2'             => 'h2',
		'h3'             => 'h3',
		'h4'             => 'h4',
		'h5'             => 'h5',
		'h6'             => 'h6',
		'paragraph'      => 'p, .porto-u-sub-heading',
		'footer'         => '#footer, #footer p',
		'footer-heading' => '#footer h1, #footer h2, #footer h3, #footer h4, #footer h5, #footer h6, #footer .widget-title, #footer .widget-title a, .footer-top .widget-title',
	);

	foreach ( $fonts_settings as $key => $selector ) {
		$css = array();
		if ( isset( $porto_settings[ $key . '-font' ] ) && ! empty( $porto_settings[ $key . '-font' ]['font-family'] ) ) {
			$css[] = 'font-family:' . sanitize_text_field( $porto_settings[ $key . '-font' ]['font-family'] ) . ', sans-serif';
		}
		if ( ! empty( $b[ $key . '-font' ]['font-weight'] ) ) {
			$css[] = 'font-weight:' . esc_html( $b[ $key . '-font' ]['font-weight'] );
		}
		if ( ! empty( $b[ $key . '-font' ]['font-size'] ) ) {
			$css[] = 'font-size:' . esc_html( $b[ $key . '-font' ]['font-size'] );
		}
		if ( ! empty( $b[ $key . '-font' ]['line-height'] ) ) {
			$css[] = 'line-height:' . esc_html( $b[ $key . '-font' ]['line-height'] );
		}
		if ( ! empty( $b[ $key . '-font' ]['letter-spacing'] ) ) {
			$css[] = 'letter-spacing:' . esc_html( $b[ $key . '-font' ]['letter-spacing'] );
		}
		if ( ! empty( $b[ $key . '-font' ]['color'] ) ) {
			$css[] = 'color:' . esc_html( $b[ $key . '-font' ]['color'] );
		}
		if ( ! empty( $b[ $key . '-font' ]['font-style'] ) ) {
			$css[] = 'font-style:' . esc_html( $b[ $key . '-font' ]['font-style'] );
		}

		if ( ! empty( $css ) ) {
			echo esc_html( $selector ) . '{' . implode( ';', $css ) . '}';
		}
	}
	?>
h1.big {
	<?php if ( $b['h1-font']['font-size'] ) : ?>
		font-size: <?php echo round( (float) $b['h1-font']['font-size'] * 1.6154, 4 ); ?>px;
	<?php endif; ?>
	<?php if ( $b['h1-font']['line-height'] ) : ?>
		line-height: <?php echo round( (float) $b['h1-font']['line-height'] * 1.2273, 4 ); ?>px;
	<?php endif; ?>
}
h1.small {
	<?php if ( $b['h1-font']['font-size'] ) : ?>
		font-size: <?php echo round( (float) $b['h1-font']['font-size'] * 0.8462, 4 ); ?>px;
	<?php endif; ?>
	<?php if ( $b['h1-font']['line-height'] ) : ?>
		line-height: <?php echo round( (float) $b['h1-font']['line-height'] * 0.9545, 4 ); ?>px;
	<?php endif; ?>
	font-weight: 600;
}
<?php if ( $b['h2-font']['font-weight'] && (int) $b['h2-font']['font-weight'] < 400 ) : ?>
	h2.vc_custom_heading { font-weight: 400; }
<?php endif; ?>

<?php if ( (float) 1 !== (float) $body_mobile_font_size_scale || (float) 1 !== (float) $body_mobile_line_height_scale ) : ?>
	@media (max-width: 575px) {
		html {
			font-size: <?php echo round( 16 * $body_mobile_font_size_scale, 4 ); ?>px;
		}
		body {
			<?php if ( $b['body-font']['font-size'] ) : ?>
				font-size: <?php echo round( (float) $b['body-font']['font-size'] * $body_mobile_font_size_scale, 4 ); ?>px;
			<?php endif; ?>
			<?php if ( $b['body-font']['line-height'] ) : ?>
				line-height: <?php echo round( (float) $b['body-font']['line-height'] * $body_mobile_line_height_scale, 4 ); ?>px;
			<?php endif; ?>
			<?php if ( $b['body-mobile-font']['letter-spacing'] ) : ?>
				letter-spacing: <?php echo esc_html( $b['body-mobile-font']['letter-spacing'] ); ?>;
			<?php endif; ?>
		}
		h1 {
			<?php if ( $b['h1-font']['font-size'] ) : ?>
				font-size: <?php echo round( (float) $b['h1-font']['font-size'] * $body_mobile_font_size_scale, 4 ); ?>px;
			<?php endif; ?>
			<?php if ( $b['h1-font']['line-height'] ) : ?>
				line-height: <?php echo round( (float) $b['h1-font']['line-height'] * $body_mobile_line_height_scale, 4 ); ?>px;
			<?php endif; ?>
		}
		h1.big {
			<?php if ( $b['h1-font']['font-size'] ) : ?>
				font-size: <?php echo round( (float) $b['h1-font']['font-size'] * $body_mobile_font_size_scale * 1.6154, 4 ); ?>px;
			<?php endif; ?>
			<?php if ( $b['h1-font']['line-height'] ) : ?>
				line-height: <?php echo round( (float) $b['h1-font']['line-height'] * $body_mobile_line_height_scale * 1.2273, 4 ); ?>px;
			<?php endif; ?>
		}
		<?php for ( $i = 2; $i <= 6; $i++ ) { ?>
			h<?php echo (int) $i; ?> {
				<?php if ( $b[ 'h' . $i . '-font' ]['font-size'] ) : ?>
					font-size: <?php echo round( (float) $b[ 'h' . $i . '-font' ]['font-size'] * $body_mobile_font_size_scale, 4 ); ?>px;
				<?php endif; ?>
				<?php if ( $b[ 'h' . $i . '-font' ]['line-height'] ) : ?>
					line-height: <?php echo round( (float) $b[ 'h' . $i . '-font' ]['line-height'] * $body_mobile_line_height_scale, 4 ); ?>px;
				<?php endif; ?>
			}
		<?php } ?>
	}
<?php endif; ?>

<?php if ( $b['body-font']['letter-spacing'] ) : ?>
	p { letter-spacing: <?php echo esc_html( $b['body-font']['letter-spacing'] ); ?>; }
<?php endif; ?>

<?php
	$font_family_settings = array(
		'custom1' => '.custom-font1',
		'custom2' => '.custom-font2',
		'custom3' => '.custom-font3',
	);
	foreach ( $font_family_settings as $key => $selector ) {
		if ( ! empty( $b[ $key . '-font' ]['font-family'] ) ) {
			echo esc_html( $selector ) . '{font-family:' . sanitize_text_field( $b[ $key . '-font' ]['font-family'] ) . ', sans-serif}';
		}
	}
	?>

/*-------------------- plugins -------------------- */
<?php if ( $b['border-radius'] ) : ?>
	.owl-carousel .owl-nav [class*='owl-'],
	.scrollbar-rail > .scroll-element .scroll-bar,
	.scrollbar-chrome > .scroll-element .scroll-bar { border-radius: 3px; }
	.resp-vtabs .resp-tabs-container,
	.fancybox-skin { border-radius: 4px; }
	.scrollbar-inner > .scroll-element .scroll-element_outer, .scrollbar-inner > .scroll-element .scroll-element_track, .scrollbar-inner > .scroll-element .scroll-bar,
	.scrollbar-outer > .scroll-element .scroll-element_outer, .scrollbar-outer > .scroll-element .scroll-element_track, .scrollbar-outer > .scroll-element .scroll-bar { border-radius: 8px; }
	.scrollbar-macosx > .scroll-element .scroll-bar,
	.scrollbar-dynamic > .scroll-element .scroll-bar { border-radius: 7px; }
	.scrollbar-light > .scroll-element .scroll-element_outer,
	.scrollbar-light > .scroll-element .scroll-element_size,
	.scrollbar-light > .scroll-element .scroll-bar { border-radius: 10px; }
	.scrollbar-dynamic > .scroll-element .scroll-element_outer,
	.scrollbar-dynamic > .scroll-element .scroll-element_size { border-radius: 12px; }
	.scrollbar-dynamic > .scroll-element:hover .scroll-element_outer .scroll-bar,
	.scrollbar-dynamic > .scroll-element.scroll-draggable .scroll-element_outer .scroll-bar { border-radius: 6px; }
<?php endif; ?>
<?php if ( $dark ) : ?>
	.fancybox-skin { background: <?php echo esc_html( $b['color-dark'] ); ?>; }
<?php endif; ?>
.owl-carousel.show-nav-title .owl-nav [class*="owl-"] { color: <?php echo esc_html( $color_dark ); ?>;/* skin color for old */ }
.owl-carousel.dots-light .owl-dots .owl-dot span { background: <?php echo porto_if_dark( 'rgba(0, 0, 0, 0.6)', 'rgba(255, 255, 255, 0.6)' ); ?> }
.owl-carousel.dots-light .owl-dots .owl-dot.active span, .owl-carousel.dots-light .owl-dots .owl-dot:hover span { background: <?php echo porto_if_dark( '#000', '#fff' ); ?> }

/*------------------ general ---------------------- */
.container-fluid,
.container-fluid .vc_section:not(.porto-inner-container) { padding-left: <?php echo (int) $b['grid-gutter-width']; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width']; ?>px; }
.container-fluid .container-fluid { padding-left: 0; padding-right: 0; width: auto; }
.container-fluid .vc_row, .container-fluid .vc_section { margin-left: -<?php echo (int) $b['grid-gutter-width']; ?>px; margin-right: -<?php echo (int) $b['grid-gutter-width']; ?>px; }
.container-fluid .vc_row:not(.porto-inner-container),
.vc_section.alignfull > .container,
.alignfull .porto-ibanner-desc > .container { padding-left: <?php echo (int) $b['grid-gutter-width'] / 2; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width'] / 2; ?>px; }

<?php
$max_spacing_mobile = 20;
if ( (int) $b['grid-gutter-width'] > $max_spacing_mobile ) :
	?>
	@media (max-width: 480px) {
		.container-fluid,
		.container-fluid .vc_section:not(.porto-inner-container) { padding-left: <?php echo (int) $max_spacing_mobile; ?>px; padding-right: <?php echo (int) $max_spacing_mobile; ?>px; }
		.container-fluid .vc_row, .container-fluid .vc_section { margin-left: -<?php echo (int) $max_spacing_mobile; ?>px; margin-right: -<?php echo (int) $max_spacing_mobile; ?>px; }
		.container-fluid .vc_row:not(.porto-inner-container) { padding-left: <?php echo (int) $max_spacing_mobile - (int) $b['grid-gutter-width'] / 2; ?>px; padding-right: <?php echo (int) $max_spacing_mobile - (int) $b['grid-gutter-width'] / 2; ?>px; }
	}
	<?php
	endif;

if ( defined( 'WPB_VC_VERSION' ) ) :
	?>
	.container-fluid .vc_row.vc_column-gap-1 { padding-left: <?php echo (int) $b['grid-gutter-width'] - 0.5; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width'] - 0.5; ?>px }
	.container-fluid .vc_row.vc_column-gap-2 { padding-left: <?php echo (int) $b['grid-gutter-width'] - 1; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width'] - 1; ?>px }
	.container-fluid .vc_row.vc_column-gap-3 { padding-left: <?php echo (int) $b['grid-gutter-width'] - 1.5; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width'] - 1.5; ?>px }
	.container-fluid .vc_row.vc_column-gap-4 { padding-left: <?php echo (int) $b['grid-gutter-width'] - 2; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width'] - 2; ?>px }
	.container-fluid .vc_row.vc_column-gap-5 { padding-left: <?php echo (int) $b['grid-gutter-width'] - 2.5; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width'] - 2.5; ?>px }
	.container-fluid .vc_row.vc_column-gap-10 { padding-left: <?php echo (int) $b['grid-gutter-width'] - 5; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width'] - 5; ?>px }
	.container-fluid .vc_row.vc_column-gap-15 { padding-left: <?php echo (int) $b['grid-gutter-width'] - 7.5; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width'] - 7.5; ?>px }
	.container-fluid .vc_row.vc_column-gap-20 { padding-left: <?php echo (int) $b['grid-gutter-width'] - 10; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width'] - 10; ?>px }
	.container-fluid .vc_row.vc_column-gap-25 { padding-left: <?php echo (int) $b['grid-gutter-width'] - 12.5; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width'] - 12.5; ?>px }
	.container-fluid .vc_row.vc_column-gap-30 { padding-left: <?php echo (int) $b['grid-gutter-width'] - 15; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width'] - 15; ?>px }
	<?php if ( $b['grid-gutter-width'] >= 17.5 ) : ?>
		.container-fluid .vc_row.vc_column-gap-35 { padding-left: <?php echo (int) $b['grid-gutter-width'] - 17.5; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width'] - 17.5; ?>px }
	<?php else : ?>
		.container-fluid .vc_row.vc_column-gap-35 { padding-left: 0; padding-right: 0; }
	<?php endif; ?>
	.container-fluid .vc_row .vc_row { padding-left: 0; padding-right: 0; margin-left: -<?php echo (int) $b['grid-gutter-width'] / 2; ?>px; margin-right: -<?php echo (int) $b['grid-gutter-width'] / 2; ?>px; }
	.container-fluid .vc_row .vc_row.vc_column-gap-1 { margin-left: -0.5px; margin-right: -0.5px }
	.container-fluid .vc_row .vc_row.vc_column-gap-2 { margin-left: -1px; margin-right: -1px }
	.container-fluid .vc_row .vc_row.vc_column-gap-3 { margin-left: -1.5px; margin-right: -1.5px }
	.container-fluid .vc_row .vc_row.vc_column-gap-4 { margin-left: -2px; margin-right: -2px }
	.container-fluid .vc_row .vc_row.vc_column-gap-5 { margin-left: -2.5px; margin-right: -2.5px }
	.container-fluid .vc_row .vc_row.vc_column-gap-10 { margin-left: -5px; margin-right: -5px }
	.container-fluid .vc_row .vc_row.vc_column-gap-15 { margin-left: -7.5px; margin-right: -7.5px }
	.container-fluid .vc_row .vc_row.vc_column-gap-20 { margin-left: -10px; margin-right: -10px }
	.container-fluid .vc_row .vc_row.vc_column-gap-25 { margin-left: -12.5px; margin-right: -12.5px }
	.container-fluid .vc_row .vc_row.vc_column-gap-30 { margin-left: -15px; margin-right: -15px }
	.container-fluid .vc_row .vc_row.vc_column-gap-35 { margin-left: -17.5px; margin-right: -17.5px }
	/*.container-fluid .ads-container.vc_row { margin-left: 0 !important; margin-right: 0 !important; }*/
	<?php
	endif;
?>
@media (max-width: 991px) {
	.container,
	.container-fluid .vc_row .porto-map-section .container,
	.container-fluid .vc_row.no-padding .container,
	.container-fluid .vce-row-no-paddings .container,
	.container-fluid .vc_row.vc_row-no-padding .container,
	.container-fluid .porto-inner-container .container,
	.vc_section.alignfull > .container,
	.alignfull .porto-ibanner-desc > .container { padding-left: <?php echo (int) $b['grid-gutter-width']; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width']; ?>px; }
	.container-fluid .container,
	.container-fluid .container .container { padding-left: 0; padding-right: 0 }
}

<?php if ( (int) $b['grid-gutter-width'] > $max_spacing_mobile ) : ?>
	@media (max-width: 480px) {
		.container,
		.container-fluid .vc_row .porto-map-section .container,
		.container-fluid .vc_row.no-padding .container,
		.container-fluid .vce-row-no-paddings .container,
		.container-fluid .vc_row.vc_row-no-padding .container,
		.container-fluid .porto-inner-container .container,
		.vc_section.alignfull > .container,
		.alignfull .porto-ibanner-desc > .container { padding-left: <?php echo (int) $max_spacing_mobile; ?>px; padding-right: <?php echo (int) $max_spacing_mobile; ?>px; }
		.container-fluid .container,
		.container-fluid .container .container { padding-left: 0; padding-right: 0 }
	}
<?php endif; ?>

.porto-column,
.pricing-table-classic.spaced [class*="col-lg-"],
ul.products .product-col,
.grid-creative > .post,
.vc_section { padding-left: <?php echo (int) $b['grid-gutter-width'] / 2; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width'] / 2; ?>px; }
.gallery-item { padding: <?php echo (int) $b['grid-gutter-width'] / 2; ?>px }
ul.products,
.slider-wrapper,
.vc_section,
.gallery { margin-left: -<?php echo (int) $b['grid-gutter-width'] / 2; ?>px; margin-right: -<?php echo (int) $b['grid-gutter-width'] / 2; ?>px; }
.owl-carousel.show-dots-title-right .owl-dots { <?php echo porto_filter_output( $right ); ?>: <?php echo (int) $b['grid-gutter-width'] / 2 - 2; ?>px; }

<?php if ( class_exists( 'Woocommerce' ) ) : ?>
	@media (min-width: 767px) {
		.products.gap-narrow { margin-left: -<?php echo (int) $b['grid-gutter-width'] / 4; ?>px; margin-right: -<?php echo (int) $b['grid-gutter-width'] / 4; ?>px; }
		.products.gap-narrow .product-col { padding-left: <?php echo (int) $b['grid-gutter-width'] / 4; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width'] / 4; ?>px; }
	}
<?php endif; ?>

/*------ Search Border Radius ------- */
<?php if ( $b['search-border-radius'] ) : ?>
	<?php if ( 'simple' == $b['search-layout'] ) : ?>
		#header .searchform .searchform-fields { border-radius: 20px; }
		#header .searchform input,
		#header .searchform select,
		#header .searchform .selectric .label,
		#header .searchform button { height: 36px; }
	<?php else : ?>
		#header .searchform { border-radius: 25px; line-height: 40px; }
		#header .searchform input,
		#header .searchform select,
		#header .searchform .selectric .label,
		#header .searchform button { height: 40px; }
		#header .searchform .live-search-list { <?php echo porto_filter_output( $left ); ?>: 15px; <?php echo porto_filter_output( $right ); ?>: 46px; width: auto }
	<?php endif; ?>
	#header .searchform select,
	#header .searchform .selectric .label { line-height: inherit; }
	#header .searchform input { border-radius: <?php echo porto_filter_output( $rtl ? '0 20px 20px 0' : '20px 0 0 20px' ); ?>; }
	#header .searchform button { border-radius: <?php echo porto_filter_output( $rtl ? '20px 0 0 20px' : '0 20px 20px 0' ); ?>; }
	#header .searchform .autocomplete-suggestions { left: 15px; right: 15px; }

	#header .searchform select,
	#header .searchform .selectric .label { padding: <?php echo porto_filter_output( $rtl ? '0 10px 0 15px' : '0 15px 0 10px' ); ?>; }

	#header .searchform input { padding: <?php echo porto_filter_output( $rtl ? '0 20px 0 15px' : '0 15px 0 20px' ); ?>; }

	#header .searchform button { padding: <?php echo porto_filter_output( $rtl ? '0 13px 0 16px' : '0 16px 0 13px' ); ?>; }
<?php endif; ?>

<?php
/* theme */
?>
/*------------------ header ---------------------- */
<?php if ( isset( $b['header-bottom-height'] ) && $b['header-bottom-height'] ) : ?>
.header-bottom { min-height: <?php echo esc_html( $b['header-bottom-height'] ); ?>px }
<?php endif; ?>
<?php if ( isset( $b['header-top-height'] ) ) : ?>
.header-top > .container, .header-top > .container-fluid { min-height: <?php echo esc_html( $b['header-top-height'] ); ?>px }
<?php endif; ?>

<?php
	$header_type = porto_get_header_type();
?>
/* menu */
<?php if ( ! $b['header-top-menu-hide-sep'] ) : ?>
	#header .header-top .top-links > li.menu-item:first-child > a { padding-<?php echo porto_filter_output( $left ); ?>: 0; }
<?php endif; ?>
<?php if ( 'transparent' == $b['switcher-hbg-color'] || ! $b['switcher-top-level-hover'] ) : ?>
	#header .porto-view-switcher:first-child > li.menu-item:first-child > a { padding-<?php echo porto_filter_output( $left ); ?>: 0; }
<?php endif; ?>
<?php if ( $b['switcher-top-level-hover'] ) : ?>
	#header .porto-view-switcher > li.menu-item:hover > a,
	#header .porto-view-switcher > li.menu-item > a.active { color: <?php echo esc_html( $b['switcher-link-color']['hover'] ); ?>; background: <?php echo esc_html( $b['switcher-hbg-color'] ); ?> }
<?php endif; ?>

<?php if ( $b['header-top-bottom-border']['border-top'] && '0px' != $b['header-top-bottom-border']['border-top'] && 'menu-hover-line' == $b['menu-type'] ) : ?>
	#header .header-top + .header-main {
		border-top: <?php echo esc_html( $b['header-top-bottom-border']['border-top'] ); ?> solid <?php echo esc_html( $b['header-top-bottom-border']['border-color'] ); ?>;
	}
	.header-top + .header-main .menu-hover-line > li.menu-item > a:before { height: <?php echo esc_html( $b['header-top-bottom-border']['border-top'] ); ?>; top: -<?php echo esc_html( $b['header-top-bottom-border']['border-top'] ); ?>; }
	#header.sticky-header .header-main.change-logo .header-left,
	#header.sticky-header .header-main.change-logo .header-center,
	#header.sticky-header .header-main.change-logo .header-right { padding-top: 0; padding-bottom: 0; }
<?php endif; ?>
<?php if ( 'menu-hover-line' == $b['menu-type'] && $b['header-top-border']['border-top'] && '0px' != $b['header-top-border']['border-top'] ) : ?>
	.header-main:first-child .menu-hover-line > li.menu-item > a:before { height: <?php echo esc_html( $b['header-top-border']['border-top'] ); ?>; top: -<?php echo esc_html( $b['header-top-border']['border-top'] ); ?>; }
<?php endif; ?>
<?php if ( 'menu-hover-line menu-hover-underline' == $b['menu-type'] ) : ?>
	.mega-menu.menu-hover-underline > li.menu-item > a:before {
		margin-left: <?php echo porto_config_value( $b['mainmenu-toplevel-padding1']['padding-left'] ); ?>px;
		margin-right: <?php echo porto_config_value( $b['mainmenu-toplevel-padding1']['padding-right'] ); ?>px;
	}
	@media <?php echo porto_filter_output( $screen_large ); ?> {
		.mega-menu.menu-hover-underline > li.menu-item > a:before {
			margin-left: <?php echo porto_config_value( $b['mainmenu-toplevel-padding2']['padding-left'] ); ?>px;
			margin-right: <?php echo porto_config_value( $b['mainmenu-toplevel-padding2']['padding-right'] ); ?>px;
		}
	}
<?php endif; ?>

/* search form */
<?php if ( 'simple' == $b['search-layout'] || 'large' == $b['search-layout'] ) : ?>
	#header .search-popup .search-toggle { display: inline-block; }
	#header .search-popup .searchform { border-width: 5px; display: none; position: absolute; top: 100%; margin-top: 8px; z-index: 1003; box-shadow: 0 5px 8px <?php echo porto_if_light( 'rgba(0, 0, 0, 0.1)', 'rgba(255, 255, 255, 0.08)' ); ?>; }
	@media (min-width: 992px) {
		#header .search-popup .searchform { <?php echo porto_filter_output( $left ); ?>: -25px; }
	}
	#header .header-left .searchform { <?php echo porto_filter_output( $left ); ?>: -10px; <?php echo porto_filter_output( $right ); ?>: auto; }
	#header .header-right .searchform { <?php echo porto_filter_output( $left ); ?>: auto; <?php echo porto_filter_output( $right ); ?>: -22px; }
<?php endif; ?>
<?php if ( 'simple' == $b['search-layout'] ) : ?>
	#header .searchform-popup .search-toggle { width: 1.4em; font-size: .8rem; }
	/* #header .searchform-popup .search-toggle i { position: relative; top: -1px; } */
	#header .search-popup .searchform { box-shadow: 0 10px 30px 10px <?php echo porto_if_light( 'rgba(0, 0, 0, 0.05)', 'rgba(255, 255, 255, 0.02)' ); ?>; padding: 15px 17px; border: none; z-index: 1002; top: 100%; }
	#header .searchform .searchform-fields { border: 1px solid #eee; }
	#header .searchform input { max-width: 220px; }
	#header .searchform:not(.searchform-cats) input { border: none; }
	#header .searchform button { position: relative; top: -1px; }
<?php elseif ( 'reveal' == $b['search-layout'] ) : ?>
	#header .searchform-popup { position: static; }
	#header .search-popup .search-toggle { display: inline-block; }
	#header .search-popup .searchform { display: none; position: absolute; top: 0; z-index: 1003; border-radius: 0; border: none; left: 15px; right: 15px; height: 100%; margin-top: 0; box-shadow: none; }
	#header .searchform .searchform-fields {  position: absolute; left: 0; width: 100%; height: 100%; -ms-flex-align: center; align-items: center; }
	#header .searchform input { font-size: 22px; width: 100% !important; height: 44px; border-width: 0 0 2px 0; border-style: solid; border-radius: 0; padding: 0 15px; }
	#header .searchform .text { -ms-flex: 1; flex: 1; }
	#header .searchform .selectric-cat { display: none; }
	#header .searchform .button-wrap { position: absolute; <?php echo porto_filter_output( $right ); ?>: 10px; }
	#header .searchform .btn-close-search-form { font-size: 20px;<?php echo ! $b['mainmenu-toplevel-link-color']['regular'] ? '' : 'color: ' . esc_html( $b['mainmenu-toplevel-link-color']['regular'] ); ?> }
<?php elseif ( 'overlay' == $b['search-layout'] ) : ?>
	#header .search-popup .search-toggle { display: inline-block; min-width: 25px; }
	#header .search-popup .searchform { display: none; position: fixed; top: 0; z-index: 1003; border-radius: 0; border: none; left: 0; right: 0; height: 100%; margin-top: 0; box-shadow: none; }
	#header .searchform .searchform-fields { height: 100%; -ms-flex-align: center; align-items: center; }
	#header .searchform input, #header .searchform select, #header .searchform .selectric, #header .searchform .selectric .label { border: none; height: 44px; }
	#header .searchform input { max-width: none; width: 100%; font-size: 22px; padding: 0 15px; border-radius: 0; }
	#header .searchform .selectric-cat,
	#header .searchform .text { border-bottom: 2px solid <?php echo esc_html( $b['skin-color'] ); ?>; }
	#header .searchform .text { -ms-flex: 1; flex: 1; }
	#header .searchform .button-wrap { position: absolute; <?php echo porto_filter_output( $right ); ?>: 30px; top: 20px; }
	#header .searchform .btn-close-search-form { font-size: 20px;<?php echo ! $b['mainmenu-toplevel-link-color']['regular'] ? '' : 'color: ' . esc_html( $b['searchform-text-color'] ); ?> }
<?php else : ?>
	#header .searchform-popup .search-toggle { font-size: 19px; width: 40px; height: 40px; line-height: 40px; }
	#header .searchform button { font-size: 16px; padding: 0 15px; }
	#header .searchform-popup .search-toggle i:before,
	#header .searchform button i:before { content: "\e884"; font-family: "porto"; font-weight: 600; }
	<?php if ( isset( $b['sticky-searchform-popup-border-color'] ) && $b['sticky-searchform-popup-border-color'] ) : ?>
		#header.sticky-header .searchform-popup .searchform { border-color: <?php echo esc_html( $b['sticky-searchform-popup-border-color'] ); ?>; }
		#header.sticky-header .searchform-popup .search-toggle:after { border-bottom-color: <?php echo esc_html( $b['sticky-searchform-popup-border-color'] ); ?>; }
	<?php endif; ?>
<?php endif; ?>
<?php if ( isset( $porto_settings['search-live'] ) && $porto_settings['search-live'] ) : ?>
	.searchform .live-search-list .autocomplete-suggestions { box-shadow: 0 10px 20px 5px <?php echo porto_filter_output( $porto_color_lib->isColorDark( $b['searchform-bg-color'] ) ? 'rgba(255, 255, 255, 0.03)' : 'rgba(0, 0, 0, 0.05)' ); ?>; }
	.searchform .live-search-list .autocomplete-suggestions::-webkit-scrollbar { width: 5px; }
	.searchform .live-search-list .autocomplete-suggestions::-webkit-scrollbar-thumb { border-radius: 0px; background: <?php echo porto_if_light( 'rgba(204, 204, 204, 0.5)', '#39404c' ); ?>;<?php echo porto_if_dark( 'border-color: transparent', '' ); ?> }
	.live-search-list .autocomplete-suggestion .search-price { color: <?php echo esc_html( $porto_color_lib->isColorDark( $b['searchform-bg-color'] ) ? 'rgba(255, 255, 255, .7)' : $color_dark ); ?>; font-weight: 600; }
<?php endif; ?>

@media (min-width: 768px) and <?php echo porto_filter_output( $screen_large ); ?> {
	#header .searchform input { width: 318px; }
	#header .searchform.searchform-cats input { width: 190px; }
}
<?php if ( 4 == $header_type ) : ?>
	#header .searchform input { width: 298px; }
	#header .searchform.searchform-cats input { width: 170px; }
	@media <?php echo porto_filter_output( $screen_large ); ?> {
		#header .searchform input { width: 240px; }
		#header .searchform.searchform-cats input { width: 112px; }
	}
<?php elseif ( 2 == $header_type || 8 == $header_type ) : ?>
	<?php if ( 'advanced' == $b['search-layout'] ) : ?>
		@media (min-width: 992px) {
			#header .searchform input,
			#header .searchform.searchform-cats input { width: 140px; }
		}
	<?php endif; ?>
<?php endif; ?>

/* header type */
<?php if ( 1 == $header_type || 4 == $header_type || 9 == $header_type || 13 == $header_type || 14 == $header_type || 17 == $header_type ) : ?>
	@media (min-width: 992px) {
		#header.header-loaded .header-main { transition: none; }
		#header .header-main .logo img { transition: none; -webkit-transform: scale(1); transform: scale(1); }
	}
<?php endif; ?>

/* mini cart */
<?php
	$minicart_type = porto_get_minicart_type();
?>
<?php if ( 'minicart-arrow-alt' == $minicart_type ) : ?>
	<?php if ( $header_type ) : ?>
	@media (max-width: 991px) {
		#mini-cart {
			margin-<?php echo porto_filter_output( $left ); ?>: .3125rem;
		}
	}
	<?php endif; ?>
<?php elseif ( 'simple' == $minicart_type ) : ?>
	#mini-cart .minicart-icon { font-size: 15px }
	<?php if ( empty( $b['minicart-icon'] ) ) : ?>
		#mini-cart .minicart-icon-default { border: 2px solid; border-radius: 0 0 5px 5px; width: 14px; height: 11px; position: relative; margin: 5px 3px 1px }
		#mini-cart .minicart-icon-default:before { content: ""; position: absolute; width: 8px; height: 9px; border: 2px solid; border-bottom: none; border-radius: 4px 4px 0 0; left: 1px; top: -7px; margin: 0 }
	<?php endif; ?>
	@media all and (-ms-high-contrast: none), (-ms-high-contrast: active) {
		#mini-cart .cart-head { height: 30px }
	}
	<?php if ( empty( $header_type ) ) : ?>
		#header .searchform-popup + #mini-cart { margin-<?php echo porto_filter_output( $left ); ?>: .75em; }
	<?php endif; ?>
<?php endif; ?>
#mini-cart .cart-items-text { font-size: <?php echo ! $b['body-font']['font-size'] ? '14px' : esc_html( $b['body-font']['font-size'] ); ?> }
.minicart-offcanvas .widget_shopping_cart_content { font-family: <?php echo sanitize_text_field( $b['h3-font']['font-family'] ); ?>, <?php echo sanitize_text_field( $b['body-font']['font-family'] ); ?>, sans-serif }
#mini-cart .cart_list::-webkit-scrollbar-thumb,
.minicart-offcanvas .widget_shopping_cart_content::-webkit-scrollbar-thumb { border-radius: 3px; background: <?php echo porto_if_light( 'rgba(204, 204, 204, 0.5)', '#39404c' ); ?>;<?php echo porto_if_dark( 'border-color: transparent', '' ); ?> }

/* main menu style for shop header and classic headers */
<?php if ( ( (int) $header_type >= 1 && (int) $header_type <= 9 ) || 18 == $header_type || 19 == $header_type || ( ( 'side' == $header_type || empty( $header_type ) ) && class_exists( 'Woocommerce' ) ) ) : ?>
	.sidebar-menu .wide li.sub > a,
	#header .main-menu .wide li.sub > a { font-weight: 700; }
<?php else : ?>
	.sidebar-menu .wide li.sub > a,
	#header .main-menu .wide li.sub > a { font-weight: 600; }
<?php endif; ?>

/* shop header type */
<?php if ( class_exists( 'Woocommerce' ) ) : ?>
	#header .header-top .mega-menu > li.menu-item > a,
	#header .header-top, .welcome-msg { font-weight: 600; }
	#header .header-top .top-links:last-child > li.menu-item:last-child > a { padding-<?php echo porto_filter_output( $right ); ?>: 0; }
	#header .header-top .top-links:last-child > li.menu-item:last-child:after { display: none; }

	#header .main-menu .wide li.menu-item li.menu-item > a:hover { background: none; <?php echo ! empty( $b['mainmenu-popup-text-color']['regular'] ) ? ' color: ' . $b['mainmenu-popup-text-color']['regular'] : ''; ?> }
	#header .main-menu .wide li.menu-item li.menu-item > a:hover,
	#header .main-menu .wide li.sub > a:hover { text-decoration: underline; }
	.sidebar-menu .wide li.sub,
	.mega-menu .wide li.sub { padding: 15px 10px 0; }

	#header .main-menu .popup { <?php echo porto_filter_output( $left ); ?>: -15px; }
	#header .main-menu .narrow.pos-right .popup { <?php echo porto_filter_output( $right ); ?>: -15px; left: auto; }
	.mega-menu .narrow li.menu-item > a { border-bottom: none; padding-left: 15px; padding-right: 15px; }
	.mega-menu .narrow ul.sub-menu { padding-left: 0; padding-right: 0; }
	.mega-menu .narrow li.menu-item-has-children > a:before { margin-<?php echo porto_filter_output( $right ); ?>: 0; }

	.sidebar-menu .wide .popup { border-<?php echo porto_filter_output( $left ); ?>: none; }
	.sidebar-menu .wide .popup >.inner { margin-<?php echo porto_filter_output( $left ); ?>: 0; }
	.sidebar-menu > li.menu-item > .arrow { <?php echo porto_filter_output( $right ); ?>: 28px; font-size: 15px; }
	.sidebar-menu > li.menu-item .popup:before { content: ''; position: absolute; border-<?php echo porto_filter_output( $right ); ?>: 12px solid <?php echo porto_if_dark( $color_dark, '#fff' ); ?>; border-top: 10px solid transparent; border-bottom: 10px solid transparent; <?php echo porto_filter_output( $left ); ?>: -12px; top: 13px; z-index: 112; }

	<?php if ( ! porto_header_type_is_side() && porto_header_type_is_preset() ) : ?>
		#header .header-contact { border-<?php echo porto_filter_output( $right ); ?>: 1px solid #dde0e2; padding-<?php echo porto_filter_output( $right ); ?>: 35px; margin-<?php echo porto_filter_output( $right ); ?>: 18px; line-height:22px; }
		#header .header-top .header-contact { margin-<?php echo porto_filter_output( $right ); ?>: 0; border-<?php echo porto_filter_output( $right ); ?>: none; padding-<?php echo porto_filter_output( $right ); ?>: 0; }
	<?php endif; ?>

	<?php if ( 8 == $header_type ) : ?>
		@media (max-width: 360px) {
			#header .header-contact { display: none; }
		}
	<?php elseif ( 18 != $header_type ) : ?>
		@media (max-width: 991px) {
			#header .header-contact { display: none; }
		}
	<?php endif; ?>

	#header .porto-view-switcher .narrow ul.sub-menu,
	#header .top-links .narrow ul.sub-menu { padding: 5px 0; }

	<?php if ( 6 != $header_type && 7 != $header_type && 8 != $header_type ) : ?>
		@media (max-width: 767px) {
			#header:not(.header-builder) .header-top { display: -ms-flexbox; display: flex; }
			#header:not(.header-builder) .switcher-wrap { display: inline-block; }
		}
	<?php endif; ?>

	<?php if ( (int) $header_type >= 2 ) : ?>
		.mega-menu .menu-item .popup { box-shadow: 0 6px 25px rgba(0, 0, 0, .2); }
	<?php elseif ( porto_header_type_is_side() && empty( $b['side-menu-type'] ) ) : ?>
		.sidebar-menu .menu-item .popup { box-shadow: 0 6px 25px rgba(0, 0, 0, .2); }
	<?php endif; ?>
	<?php if ( ! empty( $porto_settings['woo-show-default-page-header'] ) && $porto_settings['woo-show-default-page-header'] ) : ?>
		.page-header-8 { padding: 1.25rem 0; }
		.woocommerce-cart .main-content, .woocommerce-checkout .main-content { padding-top: 5px; }
		.page-header-8 .breadcrumb { margin-bottom: 0; justify-content: center; background: none; }
		.page-header-8 li { line-height: 3.521875rem; }
		.page-header-8 li a { color: <?php echo porto_if_light( '#222529', '#eee' ); ?>; font-family: 'Poppins', <?php echo sanitize_text_field( $b['h3-font']['font-family'] ); ?>, sans-serif; text-decoration: none;  font-size: 1.25rem; font-weight: 700; letter-spacing: -.03em; transition: opacity .3s; vertical-align: middle; }
		.page-header-8 li.disable a { pointer-events: none; }
		.page-header-8 li .delimiter.delimiter-2 { color: <?php echo porto_if_light( '#222529', '#eee' ); ?>; font-size: 1.875rem; font-weight: 700; margin: 0 1.2rem; float: <?php echo porto_filter_output( $left ); ?>; }
		.page-header-8 li.current~li a, .page-header-8 li.current~li .delimiter { opacity: .5; }
		.page-header-8 li.current a, .page-header-8 li:not(.disable) a:hover { color: var(--primary); opacity: 1; }
	<?php endif; ?>
<?php endif; ?>

<?php if ( isset( $b['submenu-arrow'] ) && $b['submenu-arrow'] ) : ?>
	.mega-menu > li.has-sub:before,
	.mega-menu > li.has-sub:after { content: ''; position: absolute; bottom: -1px; z-index: 112; opacity: 0; <?php echo porto_filter_output( $left ); ?>: 50%; border: solid transparent; height: 0; width: 0; pointer-events: none; }
	.mega-menu > li.has-sub:before { bottom: 0; }
	.mega-menu > li.sub-ready:hover:before,
	.mega-menu > li.sub-ready:hover:after { opacity: 1; }
	.mega-menu > li.has-sub:before { border-bottom-color: <?php echo ! empty( $b['mainmenu-popup-bg-color'] ) && 'transparent' != $b['mainmenu-popup-bg-color'] ? esc_html( $b['mainmenu-popup-bg-color'] ) : '#fff'; ?>; border-width: 10px; margin-<?php echo porto_filter_output( $left ); ?>: -10px; }
	.mega-menu > li.has-sub:after { border-bottom-color: <?php echo ! empty( $b['mainmenu-popup-bg-color'] ) && 'transparent' != $b['mainmenu-popup-bg-color'] ? esc_html( $b['mainmenu-popup-bg-color'] ) : '#fff'; ?>; border-width: 9px; margin-<?php echo porto_filter_output( $left ); ?>: -9px; }
	.mega-menu.show-arrow > li.has-sub:before { margin-<?php echo porto_filter_output( $left ); ?>: -14px; }
	.mega-menu.show-arrow > li.has-sub:after { margin-<?php echo porto_filter_output( $left ); ?>: -13px; }

	/* menu effect down */
	.mega-menu > li.has-sub:before,
	.mega-menu > li.has-sub:after { bottom: 3px; transition: bottom .2s ease-out; }
	.mega-menu > li.has-sub:before { bottom: 4px; }
	.mega-menu > li.has-sub:hover:before { bottom: -1px; }
	.mega-menu > li.has-sub:hover:after { bottom: -2px; }
	/* end menu effect down */

	<?php if ( ! class_exists( 'Woocommerce' ) ) : ?>
		.mega-menu .wide .popup,
		.mega-menu .narrow ul.sub-menu { box-shadow: 0 9px 30px 10px rgba(0, 0, 0, 0.1) }
	<?php endif; ?>
<?php endif; ?>

<?php if ( 1 == $header_type || 2 == $header_type || 9 == $header_type ) : ?>
	#header .header-minicart { white-space: nowrap; }
	@media (max-width: 991px) {
		#header .header-main .header-center { -ms-flex: 1; flex: 1; -ms-flex-pack: end; justify-content: flex-end; }
	}
	#header .mobile-toggle { padding-left: 11px; padding-right: 11px; }
	#header .header-top .porto-view-switcher .narrow .popup > .inner > ul.sub-menu { border: 1px solid #ccc; }
	#header .header-top .porto-view-switcher > li.has-sub:before { border-bottom-color: #ccc; }
	.mega-menu > li.menu-item { margin-<?php echo porto_filter_output( $right ); ?>: 2px; }

<?php endif; ?>

<?php if ( 1 == $header_type ) : ?>
	#header.sticky-header .main-menu { background: none; }
	.header-top .share-links a { border-radius: 50%; width: 2em; height: 2em; margin: 0; }
	.header-top .share-links a:not(:hover) { background: none; }
	#header .mobile-toggle { margin-<?php echo porto_filter_output( $right ); ?>: .25rem;}
	<?php if ( $b['header-top-link-color']['regular'] ) : ?>
		.header-top .share-links a:not(:hover) { color: <?php echo esc_html( $b['header-top-link-color']['regular'] ); ?>; }
	<?php endif; ?>

<?php elseif ( 2 == $header_type ) : ?>
	@media (min-width: 992px) {
		#header .header-main .header-center { text-align: <?php echo porto_filter_output( $left ); ?>; }
	}
	#header.sticky-header .header-main > .container { display: -ms-flexbox; display: flex; -ms-flex-align: center; align-items: center; }
	#header.sticky-header .header-main .header-center { -ms-flex: 1; flex: 1; }
	#header.sticky-header .header-main .header-right { width: auto; }
	#header .header-main .mega-menu { position: relative; padding-<?php echo porto_filter_output( $right ); ?>: 10px; margin-<?php echo porto_filter_output( $right ); ?>: .5rem; vertical-align: middle; }
	@media (min-width: 992px) {
		#header .header-main .mega-menu { display: inline-block; }
	}
	#header:not(.sticky-header) .header-main .mega-menu:after { content: ''; position: absolute; height: 24px; top: 7px; <?php echo porto_filter_output( $right ); ?>: 0; border-<?php echo porto_filter_output( $right ); ?>: 1px solid #dde0e2; }
	#header.sticky-header .header-main.change-logo .container > div { padding-top: 0; padding-bottom: 0; }
	#header .header-main .mega-menu > li.has-sub:before { border-bottom-color: #f0f0f0; }
	#header .searchform-popup .search-toggle { font-size: 15px; margin: 0 .5rem }

<?php elseif ( 3 == $header_type ) : ?>
	#nav-panel .mega-menu>li.menu-item { float: none; }
	#nav-panel .menu-custom-block { margin-top: 0; margin-bottom: 0; }
	#header .header-right .menu-custom-block { display: inline-block; }
	@media (max-width: 991px) {
		#header .header-right .menu-custom-block { display: none; }
	}
	#header .search-toggle .search-text { display: inline-block; }
	#header .header-top .searchform-popup { margin-<?php echo porto_filter_output( $right ); ?>: .5rem; }
<?php elseif ( 4 == $header_type ) : ?>
	.mega-menu > li.has-sub:before { border-bottom-color: #f0f0f0; }
	<?php if ( 'transparent' == $b['mobile-menu-toggle-bg-color'] ) : ?>
		#header .mobile-toggle { margin-<?php echo porto_filter_output( $left ); ?>: 0; }
	<?php else : ?>
		#header .mobile-toggle { margin-<?php echo porto_filter_output( $left ); ?>: .5rem; }
	<?php endif; ?>
	<?php if ( 'side' == $b['mobile-panel-type'] && $b['mobile-panel-add-search'] ) : ?>
		@media (max-width: 991px) {
			#header .searchform-popup { display: none; }
		<?php if ( 'transparent' == $b['mobile-menu-toggle-bg-color'] ) : ?>
			#header .mobile-toggle { padding-<?php echo porto_filter_output( $left ); ?>: 0 }
		<?php endif; ?>
		}
	<?php endif; ?>

<?php elseif ( 5 == $header_type ) : ?>
	@media (max-width: 991px) {
		#header .header-main .header-left,
		#header .header-main .header-center { display: inline-block; }
	}

<?php elseif ( 6 == $header_type ) : ?>
	#header { border-bottom: 1px solid rgba(138, 137, 132, 0.5) !important; }
	#header .header-main .header-right { width: 275px; white-space: nowrap; text-align: <?php echo porto_filter_output( $left ); ?>; }
	#header .header-main .header-center { width: 1%; white-space: nowrap; }
	#header .header-main .header-left,
	#header .header-main .header-right,
	#header .header-main .header-center { padding: 0 !important; border-<?php echo porto_filter_output( $right ); ?>: 1px solid rgba(138, 137, 132, 0.5); }
	#header .header-main .header-center > div { padding-left: 20px; padding-right: 20px; }
	#header .header-main .header-right-bottom { padding-left: 15px; padding-right: 15px; }
	#header .header-main .header-center-top,
	#header .header-main .header-right-top { border-bottom: 1px solid rgba(138, 137, 132, 0.5); }
	#header .header-main .header-right > div,
	#header .header-main .header-center > div { height: 50px; display: -webkit-flex; display: -ms-flexbox; display: flex; -webkit-align-items: center; -ms-flex-align: center; align-items: center; }
	#header .header-main .header-center > div { -webkit-justify-content: flex-end; -ms-flex-pack: end; justify-content: flex-end }
	#header .header-main .header-right > div { -webkit-justify-content: center; -ms-flex-pack: center; justify-content: center }
	#header.sticky-header .header-main .header-center-top,
	#header.sticky-header .header-main .header-right-top { display: none; }

	#header .header-main #main-menu { display: block; }
	#header .main-menu { text-align: inherit; white-space: nowrap; }
	.mega-menu>li.menu-item { display: inline-block; float: none; }
	#header .porto-view-switcher { margin-<?php echo porto_filter_output( $right ); ?>: 15px; }
	#header.header-6 .porto-view-switcher > li.menu-item > a { background: none; }
	#header .porto-view-switcher > li.menu-item > a { font-size: 14px; padding-top: 1px; }
	#header .header-right .porto-view-switcher > li.menu-item:hover > a { background: none; }
	#header .porto-view-switcher .narrow ul.sub-menu, #header .top-links .narrow ul.sub-menu { padding: 0; }
	#header .top-links > li.menu-item { float: none; }
	#header .top-links > li.menu-item > a { font-size: 13px; padding: 0 10px; }
	#header .mobile-toggle { margin: 0; }

	<?php if ( 'advanced' == $b['search-layout'] ) : ?>
		#header .searchform-popup .search-toggle { display: none; }
		#header.header-6 .header-right .searchform-popup .searchform { border: none; background: none; display: block; position: static; box-shadow: none; border-radius: 0; }
		#header .searchform .searchform-fields { border: none; }
		#header .searchform button i:before { font-weight: 400; }
	<?php endif; ?>
	#header .searchform input,
	#header .searchform button { font-size: 20px; }
	#header .searchform input { width: 218px !important; color: inherit; font-size: 13px; text-transform: uppercase; }
	#header .searchform input, #header .searchform select, #header .searchform .selectric { border-<?php echo porto_filter_output( $right ); ?>: none; }

	@media (max-width: 575px) {
		#header .searchform input { max-width: 140px !important; }
		#header .header-main .header-right { width: 174px; }
		#header .header-main .header-right .searchform-popup { margin-<?php echo porto_filter_output( $left ); ?>: -75px; }
		#header .header-main .header-center { border-right: none; }
	}
	@media (max-width: 767px) {
		#header .header-right { text-align: center; }
	}

<?php elseif ( 7 == $header_type ) : ?>
	@media (max-width: 991px) {
		#header .header-main .header-left { display: none; }
	}
	@media <?php echo porto_filter_output( $screen_large ); ?> {
		#header .mobile-toggle { display: inline-block; }
		#header .main-menu,
		#header.logo-center .header-main .container .header-left { display: none; }
	}
	@media (min-width: 992px) {
		#side-nav-panel .searchform { display: none; }
	}
	@media (max-width: 991px) {
		#header .header-contact { display: none; }
		#header .header-main .header-center { text-align: <?php echo porto_filter_output( $right ); ?>; }
		#header .header-main .header-right { width: 1%; }
	}
	#header .logo { padding-top: 10px; padding-bottom: 10px; }
	#header.sticky-header .logo,
	#header.sticky-header .header-main.change-logo .container>div { padding-top: 0; padding-bottom: 0; }
	.page-top ul.breadcrumb > li a { text-transform: inherit; }
	.switcher-wrap .mega-menu .popup,
	#header .currency-switcher,
	#header .view-switcher,
	#header .top-links { font-size: 13px; font-weight: 500; }
	#header .currency-switcher .popup,
	#header .view-switcher .popup,
	#header .top-links .popup { font-size: 11px; }
	#header .currency-switcher > li.menu-item > a,
	#header .view-switcher > li.menu-item > a,
	#header .top-links > li.menu-item > a { font-weight: 500; }
	#header .top-links { margin-<?php echo porto_filter_output( $left ); ?>: 15px; margin-<?php echo porto_filter_output( $right ); ?>: 20px; text-transform: uppercase; }
	@media (min-width: 576px) {
		#header .mobile-toggle { padding: 7px 10px; }
	}
	#header .header-main .header-contact { font-size: 16px; line-height: 36px; margin-<?php echo porto_filter_output( $right ); ?>: 5px; padding-<?php echo porto_filter_output( $right ); ?>: 30px; border-<?php echo porto_filter_output( $right ); ?>-color: rgba(255, 255, 255, 0.3); }
	#header .searchform-popup .search-toggle { font-size: 16px; }

<?php elseif ( 8 == $header_type ) : ?>
	.mega-menu > li.menu-item > a { padding: 9px 12px; }
	.mega-menu.show-arrow > li.has-sub > a:after { position: relative; top: -1px; }
	#header .header-main .header-contact { padding-<?php echo porto_filter_output( $right ); ?>: 0; }
	#header .mobile-toggle { padding: 7px 10px; }
	#mini-cart .minicart-icon { font-size: 30px; }
	#mini-cart .minicart-icon:before { content: '\e871'; }
	#header:not(.sticky-header) #mini-cart .cart-head { width: 60px; }
	#header:not(.sticky-header) #mini-cart .cart-head { padding-<?php echo porto_filter_output( $right ); ?>: 20px; }
	@media (max-width: 767px) {
		#header .header-top { display: block; }
	}

<?php elseif ( 9 == $header_type ) : ?>
	#header .mobile-toggle { margin-<?php echo porto_filter_output( $left ); ?>: 0; }
	#main-toggle-menu .toggle-menu-wrap { box-shadow: none; }
	#main-toggle-menu .toggle-menu-wrap > ul { border-bottom: none; }
	#main-toggle-menu .menu-title { padding-<?php echo porto_filter_output( $left ); ?>: 30px; }
	#main-toggle-menu .menu-title .toggle { position: relative; top: -1px; }
	.sidebar-menu > li.menu-item > a, .sidebar-menu .menu-custom-block a { border-top-color: #e6ebee; margin-<?php echo porto_filter_output( $left ); ?>: 16px; margin-<?php echo porto_filter_output( $right ); ?>: 18px; padding: 14px 12px; }
	.widget_sidebar_menu .widget-title { padding: 14px 28px; }
	#main-menu .menu-custom-block { text-align: <?php echo porto_filter_output( $right ); ?>; }

/* classic header type */
<?php elseif ( ( (int) $header_type >= 10 && (int) $header_type <= 17 ) || empty( $header_type ) ) : ?>
	<?php if ( ! empty( $header_type ) ) : ?>
	@media (min-width: 992px) {
		#header .header-main .header-right { padding-<?php echo porto_filter_output( $left ); ?>: <?php echo (int) $b['grid-gutter-width']; ?>px; }
		/*#header .header-main .header-right .searchform-popup { margin-<?php echo porto_filter_output( $right ); ?>: 0; }*/
	}
	<?php endif; ?>

	<?php if ( 'simple' != $b['search-layout'] && 'reveal' != $b['search-layout'] && 'overlay' != $b['search-layout'] ) : ?>
		#header .searchform { box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset; }
		#header .searchform select,
		#header .searchform button,
		#header .searchform .selectric .label { height: 34px; line-height: 34px; }
		#header .searchform input { border: none; line-height: 1.5; height: 34px; width: 200px; }
		#header .searchform select { border-<?php echo porto_filter_output( $left ); ?>: 1px solid #ccc; padding-<?php echo porto_filter_output( $left ); ?>: 8px; margin-<?php echo porto_filter_output( $right ); ?>: -3px; font-size: 13px; }
		#header .searchform .selectric { border-<?php echo porto_filter_output( $left ); ?>: 1px solid #ccc; }
		#header .searchform .selectric .label { padding-<?php echo porto_filter_output( $left ); ?>: 8px; margin-<?php echo porto_filter_output( $right ); ?>: -3px; }
		#header .searchform button { padding: 0 12px; }
	<?php endif; ?>

	#header .share-links a { width: 30px; height: 30px; border-radius: 30px; margin: 0 1px; overflow: hidden; font-size: .8rem; }
	#header .share-links a:not(:hover) { background-color: #fff; color: #333; }
<?php endif; ?>
<?php if ( 10 == $header_type ) : ?>
	#header .header-right-bottom { margin: 10px 0 5px; }
	#header .header-right-bottom > * { margin-<?php echo porto_filter_output( $left ); ?>: 1em }
	#header.sticky-header .header-right-bottom { margin-top: 5px; }
	@media (max-width: 991px) {
		#header .header-right-bottom { margin-top: 0; margin-bottom: 0; }
	}
	@media (max-width: 575px) {
		#header .share-links { display: none; }
	}
	#header .header-main .header-left,
	#header .header-main .header-center,
	#header .header-main .header-right { padding-top: 15px; padding-bottom: 15px; }
	#header .header-contact { margin: <?php echo porto_filter_output( $rtl ? '0 0 0 10px' : '0 10px 0 0' ); ?>; }
	@media (min-width: 992px) {
		#header .header-main.sticky .header-right-top { display: none; }
		#header.sticky-header .header-main.sticky .header-left,
		#header.sticky-header .header-main.sticky .header-center,
		#header.sticky-header .header-main.sticky .header-right { padding-top: 0; padding-bottom: 0; }
		#header .searchform { margin-bottom: 4px; margin-<?php echo porto_filter_output( $left ); ?>: 15px; }
	}
<?php endif; ?>
<?php if ( (int) $header_type >= 11 && (int) $header_type <= 17 ) : ?>
	#header .header-main .searchform-popup, #header .header-main #mini-cart { display: none; }
	@media (min-width: 768px) {
		#header .switcher-wrap { margin-<?php echo porto_filter_output( $right ); ?>: 5px; }
		#header .header-main .block-inline { line-height: 50px; margin-bottom: 5px; }
		#header .header-left .block-inline { margin-<?php echo porto_filter_output( $right ); ?>: 8px; }
		#header .header-left .block-inline > * { margin: <?php echo porto_filter_output( $rtl ? '0 0 0 7px' : '0 7px 0 0' ); ?>; }
		#header .header-right .block-inline { margin-<?php echo porto_filter_output( $left ); ?>: 8px; }
		#header .header-right .block-inline > * { margin: <?php echo porto_filter_output( $rtl ? '0 7px 0 0' : '0 0 0 7px' ); ?>; }
		#header .share-links { line-height: 1; }
	}
	#header .header-top .welcome-msg { font-size: 1.15em; }
	#header .header-top #mini-cart { font-size: 1em; }
	#header .header-top #mini-cart:first-child { margin-left: 0; margin-right: 0; }
	@media (max-width: 991px) {
		#header .header-top .header-left > *, #header .header-top .header-right > * { display: none; }
		#header .header-top .header-left > .block-inline, #header .header-top .header-right > .block-inline { display: block; }
		#header .header-top .searchform-popup, #header .header-top #mini-cart { display: none; }
		#header .header-main .searchform-popup, #header .header-main #mini-cart { display: inline-block; }
	}
<?php endif; ?>
<?php if ( 11 == $header_type || 12 == $header_type ) : ?>
	@media (min-width: 992px) {
		#header .header-main .header-left,
		#header .header-main .header-right,
		#header .header-main .header-center,
		.fixed-header #header .header-main .header-left,
		.fixed-header #header .header-main .header-right,
		.fixed-header #header .header-main .header-center,
		#header.sticky-header .header-main.sticky .header-left,
		#header.sticky-header .header-main.sticky .header-right,
		#header.sticky-header .header-main.sticky .header-center { padding-top: 0; padding-bottom: 0; }
		#header .main-menu > li.menu-item > a { border-radius: 0; margin-bottom: 0; }
		#header .main-menu .popup { margin-top: 0; }
		#header .main-menu .wide .popup,
		.header-wrapper #header .main-menu .wide .popup > .inner,
		#header .main-menu .narrow .popup > .inner > ul.sub-menu,
		#header .main-menu .narrow ul.sub-menu ul.sub-menu { border-radius: 0; }
		#header .main-menu > li.menu-item > a .tip { top: <?php echo (float) $b['mainmenu-toplevel-padding2']['padding-top'] - 18; ?>px; }
		#header .share-links { margin-top: <?php echo (float) $b['mainmenu-toplevel-padding2']['padding-top'] - $b['mainmenu-toplevel-padding2']['padding-bottom'] - 1; ?>px; }
		<?php if ( $b['enable-sticky-header'] && isset( $b['mainmenu-toplevel-padding3'] ) && ! empty( $b['mainmenu-toplevel-padding3']['padding-top'] ) && ! empty( $b['mainmenu-toplevel-padding3']['padding-bottom'] ) ) : ?>
			#header.sticky-header .main-menu > li.menu-item > a .tip { top: <?php echo (float) $b['mainmenu-toplevel-padding3']['padding-top'] - 18; ?>px; }
			#header.sticky-header .share-links { margin-top: <?php echo (float) $b['mainmenu-toplevel-padding3']['padding-top'] - $b['mainmenu-toplevel-padding3']['padding-bottom'] - 1; ?>px; }
		<?php endif; ?>
	}
	@media (min-width: <?php echo (int) ( $b['container-width'] + $b['grid-gutter-width'] ); ?>px) {
		#header .main-menu > li.menu-item > a .tip { top: <?php echo (float) $b['mainmenu-toplevel-padding1']['padding-top'] - 18; ?>px; }
		#header .share-links { margin-top: <?php echo (float) $b['mainmenu-toplevel-padding1']['padding-top'] - $b['mainmenu-toplevel-padding1']['padding-bottom'] - 1; ?>px; }
	}
<?php endif; ?>
<?php if ( 11 == $header_type || 15 == $header_type || 16 == $header_type ) : ?>
	#header .searchform { margin-<?php echo porto_filter_output( $left ); ?>: 15px; }
	@media (max-width: 991px) {
		#header .share-links { display: none; }
	}
<?php endif; ?>
<?php if ( 11 == $header_type ) : ?>
	@media (min-width: 992px) {
		#header .header-main.change-logo .main-menu > li.menu-item,
		#header .header-main.change-logo .main-menu > li.menu-item.active,
		#header .header-main.change-logo .main-menu > li.menu-item:hover { margin-top: 0; }
		#header .header-main.change-logo .main-menu > li.menu-item > a,
		#header .header-main.change-logo .main-menu > li.menu-item.active > a,
		#header .header-main.change-logo .main-menu > li.menu-item:hover > a { border-width: 0; }
		#header .show-header-top .main-menu > li.menu-item,
		#header .show-header-top .main-menu > li.menu-item.active,
		#header .show-header-top .main-menu > li.menu-item:hover { margin-top: 0; }
		#header .show-header-top .main-menu > li.menu-item > a,
		#header .show-header-top .main-menu > li.menu-item.active > a,
		#header .show-header-top .main-menu > li.menu-item:hover > a { border-width: 0; }
	}
<?php elseif ( 12 == $header_type ) : ?>
	#header .share-links a { box-shadow: none; }
	#header .share-links a:not(:hover) { background: none; 
	<?php
	if ( $b['header-link-color']['regular'] ) {
		echo 'color: ' . esc_html( $b['header-link-color']['regular'] ); }
	?>
	}
	@media (max-width: 991px) {
		#header .header-main .share-links { margin-<?php echo porto_filter_output( $left ); ?>: 0; }
	}
	@media (max-width: 575px) {
		#header .header-main .share-links { display: none; }
	}
<?php elseif ( 14 == $header_type ) : ?>
	#header .main-menu > li.menu-item { margin-<?php echo porto_filter_output( $right ); ?>: 2px; }

<?php elseif ( 18 == $header_type ) : ?>
	#header .header-contact { border-<?php echo porto_filter_output( $right ); ?>: none; padding-<?php echo porto_filter_output( $right ); ?>: 30px; }

	#header #mini-cart .cart-items { font-size: 11px; font-weight: 400; }
	#mini-cart .minicart-icon { font-size: 24px; }
	#mini-cart .cart-items { background-color: #b7597c; width: 14px; height: 14px; line-height: 14px; font-size: 9px; }
	#mini-cart .cart-head:after { top: -1px; font-size: 16px; }
	#header .searchform-popup .search-toggle { font-size: 20px; line-height: 39px; }
	#header .searchform-popup .search-toggle i:before { content: "\e884"; font-family: "porto"; font-weight: 400; }

	.mega-menu > li.has-sub:before { border-bottom-color: #f0f0f0; }

	@media (min-width: 992px) {
		#header .header-main .header-right .searchform-popup { margin-left: 18px; margin-right: 18px; }
		.fixed-header #header .header-main .header-left { padding: 25px 0; }
	}

<?php elseif ( 19 == $header_type ) : ?>
	@media (min-width: 992px) {
		#header .header-main .header-center { padding-top: 30px; padding-bottom: 30px; }
		#header.logo-center .header-main .header-left,
		#header.logo-center .header-main .header-right { -ms-flex-pack: center; justify-content: center; }
	}
	#header .header-main #main-menu { display: -ms-flexbox; display: flex; }
	#header.header-19 .searchform-popup .search-toggle { color: <?php echo esc_html( $b['header-top-link-color']['regular'] ); ?>; }
	#header.header-19 .searchform-popup .search-toggle:hover { color: <?php echo esc_html( $b['header-top-link-color']['hover'] ); ?>; }
	#header .searchform { line-height: 36px; font-family: <?php echo sanitize_text_field( $b['body-font']['font-family'] ); ?>, sans-serif }
	#header .searchform input,
	#header .searchform.searchform-cats input { width: 200px; }
	<?php if ( $b['search-border-radius'] ) : ?>
		#header.header-19 .searchform { border-radius: 4px; border: none; }
		#header .searchform .live-search-list { <?php echo porto_filter_output( $right ); ?>: 0; width: 100%; <?php echo porto_filter_output( $left ); ?>: auto; }
	<?php endif; ?>
	#header .searchform input, #header .searchform select, #header .searchform .selectric .label, #header .searchform button { height: 36px; }
	<?php if ( 'none' == $b['minicart-type'] || ! class_exists( 'Woocommerce' ) ) : ?>
		#header .header-top { padding: 5px 0; }
	<?php endif; ?>
	#header .header-top .share-links { margin: 5px; }
	#header .searchform input { padding: 0 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
	#header .searchform button { padding: 0 12px; font-size: 12px; }
	#header .header-right > *:not(:last-child),
	#header .top-links>li.menu-item { margin-<?php echo porto_filter_output( $right ); ?>: 15px; }
	#header .header-main .header-left { text-align: <?php echo porto_filter_output( $right ); ?> }
	#header .searchform:not(.searchform-cats) input { border-<?php echo porto_filter_output( $right ); ?>: none; }
	#header .header-main { border-bottom: 1px solid rgba(0, 0, 0, 0.075); }
	<?php if ( 'none' != $b['minicart-type'] && class_exists( 'Woocommerce' ) ) : ?>
		#mini-cart { padding: <?php echo porto_filter_output( $rtl ? '5px 25px 5px 0' : '5px 0 5px 25px' ); ?>; line-height: 38px; border-<?php echo porto_filter_output( $left ); ?>: 1px solid rgba(255, 255, 255, 0.15) }
		#mini-cart .cart-items,
		#mini-cart .cart-head:after { display: none; }
		#mini-cart .cart-items-text { display: inline; text-transform: uppercase; font-weight: 300; vertical-align: middle; }
		#mini-cart .minicart-icon { border-bottom: 18px solid; border-top: none; border-left: 1px solid transparent; border-right: 1px solid transparent; width: 18px; position: relative; top: 2px; margin-right: 6px; border-radius: 0; }
		#mini-cart .minicart-icon:before { content: ''; position: absolute; bottom: 100%; top: auto; left: 50%; margin-left: -5px; border-width: 2px 2px 0 2px; width: 10px; height: 5px; border-radius: 3px 3px 0 0; border-style: solid; }
	<?php endif; ?>

<?php elseif ( 'side' == $header_type ) : ?>
	.header-wrapper #header .side-top { display: block; text-align: <?php echo porto_filter_output( $left ); ?>; }
	.header-wrapper #header .side-top > .container { display: -ms-flexbox; display: flex; -ms-flex-align: center; align-items: center; min-height: 0 !important; position: static; }
	.header-wrapper #header .share-links { margin: 0 0 16px; }
	#header .share-links a { width: 30px; height: 30px; margin: 1px; box-shadow: none; }

	@media (min-width: 992px) {
		.admin-bar .header-wrapper #header { min-height: calc(100vh - 32px); }
		.page-wrapper { display: -ms-flexbox; display: flex; -ms-flex-wrap: wrap; flex-wrap: wrap; }
		.header-wrapper { -ms-flex: 0 0 256px; flex: 0 0 256px; max-width: 256px; }
		.header-wrapper #header { min-height: 100vh; padding: 15px 15px 160px; position: relative; }
		.page-wrapper > .content-wrapper { -ms-flex: 1; flex: 1; max-width: calc(100% - 256px); }
		.header-wrapper #header .side-top > .container { padding: 0; width: 100%; }
		.header-wrapper #header .header-main > .container { position: static; padding: 0; width: 100%; display: block; }
		.header-wrapper #header .header-main > .container > * { position: static; display: block; padding: 0; }
		#header .logo { text-align: center; margin: 30px auto; }
		<?php if ( 'advanced' == $b['search-layout'] ) : ?>
			.header-wrapper #header .searchform { margin-bottom: 20px; }
			.header-wrapper #header .searchform input { padding: 0 10px; border-width: 0; width: 190px; }
			.header-wrapper #header .searchform.searchform-cats input { width: 95px; }
			.header-wrapper #header .searchform button { padding: <?php echo porto_filter_output( $rtl ? '0 8px 0 9px' : '0 9px 0 8px' ); ?>; }
			.header-wrapper #header .searchform select { border-width: 0;  padding: 0 5px; width: 93px; }
			.header-wrapper #header .searchform .selectric-cat { width: 93px; }
			.header-wrapper #header .searchform .selectric { border-width: 0; }
			.header-wrapper #header .searchform .selectric .label { padding: 0 5px; }
			.header-wrapper #header .searchform .autocomplete-suggestions { left: -1px; right: -1px; }
		<?php endif; ?>
		.header-wrapper #header .top-links { display: block; font-size: .8em; margin-bottom: 20px; }
		.header-wrapper #header .top-links li.menu-item { display: block; float: none; margin: 0; }
		.header-wrapper #header .top-links li.menu-item:after { display: none; }
		.header-wrapper #header .top-links li.menu-item > a { margin: 0; padding-top: 7px; padding-bottom: 7px; }
		.header-wrapper #header .top-links li.menu-item:first-child > a { border-top-width: 0; }
		.header-wrapper #header .header-contact { margin: 5px 0 20px; white-space: normal; }
		.header-wrapper #header .header-copyright { font-size: .9em; }
		.header-wrapper #mini-cart .cart-popup { <?php echo porto_filter_output( $left ); ?>: 0; <?php echo porto_filter_output( $right ); ?>: auto; }
		.header-wrapper .side-bottom { text-align: center; position: absolute; bottom: 0; left: 0; right: 0; margin: 20px 10px; }
		.page-wrapper.side-nav .page-top.fixed-pos { position: fixed; z-index: 1001; width: 100%; box-shadow: 0 1px 0 0 rgba(0, 0, 0, .1); }
		.page-wrapper.side-nav:not(.side-nav-right) #mini-cart .cart-popup { <?php echo porto_filter_output( $left ); ?>: -32px; }

		<?php if ( $b['side-menu-type'] && 'columns' != $b['side-menu-type'] ) : ?>
			<?php if ( $b['menu-popup-font']['line-height'] ) : ?>
				#header .sidebar-menu li.menu-item > .arrow { top: <?php echo ( 16 + (float) $b['menu-popup-font']['line-height'] - 30 ) / 2; ?>px }
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( 'slide' == $b['side-menu-type'] ) : ?>
			#header .header-main .header-center { overflow: hidden }
		<?php endif; ?>
	}
	@media (min-width: 1440px) {
		.header-wrapper { -ms-flex: 0 0 300px; flex: 0 0 300px; max-width: 300px; }
		.header-wrapper #header { padding-left: 30px; padding-right: 30px; }
		.page-wrapper > .content-wrapper { max-width: calc(100% - 300px); }
		.header-wrapper #header .searchform input { width: 204px; }
		#header .header-copyright { clear: both; }
		.header-wrapper .side-bottom { display: -ms-flexbox; display: flex; -ms-flex-align: center; align-items: center; margin-left: 30px; -ms-flex-pack: center; justify-content: center; -ms-flex-wrap: wrap; flex-wrap: wrap; margin-right: 30px; }
		.side-bottom .header-contact { -ms-flex-order: 2; order: 2; margin-<?php echo porto_filter_output( $left ); ?>: auto; }
	}

	<?php if ( empty( $b['side-menu-type'] ) ) : ?>
		.sidebar-menu > li.menu-item .popup { margin-<?php echo porto_filter_output( $left ); ?>: 30px; }
		.page-wrapper.side-nav-right .sidebar-menu > li.menu-item .popup { margin-<?php echo porto_filter_output( $right ); ?>: 30px; }
		@media (max-width: 1439px) {
			.sidebar-menu > li.menu-item .popup { margin-<?php echo porto_filter_output( $left ); ?>: 15px; }
			.page-wrapper.side-nav-right .sidebar-menu > li.menu-item .popup { margin-<?php echo porto_filter_output( $right ); ?>: 15px; }
		}
	<?php endif; ?>
	@media (max-width: 1439px) {
		.header-wrapper #header .header-contact { margin-<?php echo porto_filter_output( $right ); ?>: 5px; }
	}

	.sidebar-menu { margin-bottom: 20px; }
	.sidebar-menu > li.menu-item > a, .sidebar-menu .menu-custom-block a { border-top: none; }
	.sidebar-menu > li.menu-item > a { margin-left: 0; margin-right: 0; }
	.sidebar-menu > li.menu-item > .arrow { <?php echo porto_filter_output( $right ); ?>: 5px; }
	.sidebar-menu > li.menu-item:last-child:hover { border-radius: 0; }
	<?php if ( 'columns' != $b['side-menu-type'] ) : ?>
		.sidebar-menu > li.has-sub:before { content: ''; position: absolute; top: 0; bottom: 0; <?php echo porto_filter_output( $left ); ?>: 100%; width: 30px; }
	<?php else : ?>
		.sidebar-menu > li.has-sub > a:before { content: ''; position: absolute; top: 0; bottom: 0; <?php echo porto_filter_output( $left ); ?>: 100%; width: 30px; }
	<?php endif; ?>
	.sidebar-menu .menu-custom-block a { margin-left: 0; margin-right: 0; padding-left: 5px; padding-right: 5px; }
	.sidebar-menu .menu-custom-block .fas,
	.sidebar-menu .menu-custom-block .fab
	.sidebar-menu .menu-custom-block .far { width: 18px; text-align: center; }
	.sidebar-menu .menu-custom-block .fas,
	.sidebar-menu .menu-custom-block .fab,
	.sidebar-menu .menu-custom-block .far,
	.sidebar-menu .menu-custom-block .avatar { margin-<?php echo porto_filter_output( $right ); ?>: 5px; }
	.sidebar-menu .menu-custom-block > .avatar img { margin-top: -5px; }

	@media (max-width: 991px) {
		.header-wrapper #header .side-top { padding: 10px 0 0; }
		.header-wrapper #header .side-top .porto-view-switcher { float: <?php echo porto_filter_output( $left ); ?>; }
		.header-wrapper #header .side-top .mini-cart { float: <?php echo porto_filter_output( $right ); ?>; }
		.header-wrapper #header .logo { margin-bottom: 5px; }
		.header-wrapper #header .sidebar-menu { display: none; }
		.header-wrapper #header .share-links { margin: <?php echo porto_filter_output( $rtl ? '0 10px 0 0' : '0 0 0 10px' ); ?>; }
		.header-wrapper #header .share-links a:last-child { margin-<?php echo porto_filter_output( $right ); ?>: 0; }
		.header-wrapper #header .header-copyright { display: none; }

		.header-wrapper #header .side-top { padding-top: 0; }
		.header-wrapper #header .side-top > .container > * { display: none !important; }
		.header-wrapper #header .side-top > .container > .mini-cart { display: block !important; position: absolute !important; top: 50%; bottom: 50%; height: 26px; margin-top: -12px; <?php echo porto_filter_output( $right ); ?>: 15px; z-index: 1001; }
		.header-wrapper #header .logo { margin: 0; }
		.header-wrapper #header .share-links { display: none; }
		.header-wrapper #header .show-minicart .header-contact { margin-<?php echo porto_filter_output( $right ); ?>: 80px; }
	}

	@media (min-width: 992px) {
		body.boxed.body-side .header-wrapper { <?php echo porto_filter_output( $left ); ?>: -276px; margin-top: -30px; }
		body.boxed.body-side .page-wrapper.side-nav { max-width: 100%; }
		body.boxed.body-side .page-wrapper.side-nav > * { padding-<?php echo porto_filter_output( $left ); ?> : 0; }
		body.boxed.body-side .page-wrapper.side-nav .page-top.fixed-pos { width: auto; }
	}

	<?php if ( class_exists( 'Woocommerce' ) ) : ?>
		.header-wrapper a { color: inherit }
		#header .header-minicart > a { display: inline-block; font-size: 20px; min-width: 32px; text-align: center; margin-<?php echo porto_filter_output( $right ); ?>: .5rem; }
		#mini-cart .minicart-icon { font-size: 20px; }
		#mini-cart .cart-head { padding: 0; text-transform: uppercase; }
		#mini-cart .cart-subtotal { font-size: 13px; font-weight: 500; margin-<?php echo porto_filter_output( $left ); ?>: 1px; }
		#header .side-top .header-minicart { margin-<?php echo porto_filter_output( $left ); ?>: auto; }
	<?php endif; ?>
	#header .searchform-popup .search-toggle { font-size: 16px; }
	@media (max-width: 991px) {
		#header .header-right { width: 1%; white-space: nowrap; }
		#header .header-main .header-center { text-align: <?php echo porto_filter_output( $right ); ?> }
		#header .mobile-toggle {
			margin-<?php echo porto_filter_output( $left ); ?>: 0;
		}
		.fixed-header #header .header-main { padding-top: 5px; padding-bottom: 5px; }
		#mini-cart .minicart-icon { font-size: 22px; }
		#mini-cart .cart-head:after { display: none; }
	}

/* header builder side type */
	<?php
elseif ( empty( $header_type ) && porto_header_type_is_side() ) :
	$current_layout      = porto_header_builder_layout();
	$side_position       = empty( $porto_settings['header-side-position'] ) ? 'left' : 'right';
	$side_mobile_visible = isset( $current_layout['side_header_toggle'] ) && $current_layout['side_header_toggle'];
	?>
	<?php if ( ! $side_mobile_visible ) : ?>
		@media (min-width: 992px) {
	<?php endif; ?>
		.header-wrapper #header { display: -ms-flexbox; display: flex; -ms-flex-direction: column; flex-direction: column; position: fixed; z-index: 1110; top: 0; <?php echo porto_filter_output( $side_position ); ?>: 0; <?php echo 'left' === $side_position ? 'right' : 'left'; ?>: auto; width: <?php echo isset( $current_layout['side_header_width'] ) ? $current_layout['side_header_width'] : '255'; ?>px; box-shadow: 0 0 30px <?php echo porto_if_light( 'rgba(0, 0, 0, 0.05)', 'rgba(255, 255, 255, 0.03)' ); ?> }
		#header .header-main { display: -ms-flexbox; display: flex; height: 100%; }
		#header .header-main .header-row { height: 100%; -ms-flex-align: start; align-items: flex-start; }
		#header .header-main .header-row > div { -ms-flex-align: start; align-items: flex-start; }
		#header .header-center { -ms-flex: 1; flex: 1; text-align: center; }
		.header-row .header-col > * { -ms-flex: 0 0 100%; flex: 0 0 100%; margin-bottom: 1em; }
		.header-bottom { -ms-flex-wrap: wrap; flex-wrap: wrap; padding: 15px 0 30px; }
		.header-copyright { text-align: center; width: 100%; font-size: .8em; }
		.page-wrapper.side-nav .page-top.fixed-pos { position: fixed; z-index: 1001; width: 100%; box-shadow: 0 1px 0 0 rgba(0, 0, 0, .1); }
		#header.sticky-header .header-main.sticky { position: static; }

		.header-wrapper { z-index: 1002; }
		<?php if ( ! isset( $current_layout['side_header_toggle'] ) || ! $current_layout['side_header_toggle'] ) : ?>
			.page-wrapper.side-nav > * { padding-<?php echo porto_filter_output( $side_position ); ?>: <?php echo isset( $current_layout['side_header_width'] ) ? $current_layout['side_header_width'] : '255'; ?>px; padding-<?php echo porto_filter_output( 'right' == $side_position ? 'left' : 'right' ); ?>: 0; }
		<?php endif; ?>
		#header .logo { padding: 7vh 0 5vh; }

		<?php
		if ( $b['mainmenu-popup-bg-color'] && 'transparent' != $b['mainmenu-popup-bg-color'] ) :
			$main_menu_popup_bg_arr = $porto_color_lib->hexToRGB( $b['mainmenu-popup-bg-color'], false );
			?>
			<?php if ( ! $side_mobile_visible ) : ?>
				#header .sidebar-menu > li > a
			<?php else : ?>
				#header .sidebar-menu li > a
			<?php endif; ?>
				{ <?php if ( ( (int) $main_menu_popup_bg_arr[0] * 256 + (int) $main_menu_popup_bg_arr[1] * 16 + (int) $main_menu_popup_bg_arr[2] ) < ( 79 * 256 + 255 * 16 + 255 ) ) : ?>
					border-bottom: 1px solid <?php echo esc_html( $porto_color_lib->lighten( $b['mainmenu-popup-bg-color'], 5 ) ); ?>;
				<?php else : ?>
					border-bottom: 1px solid <?php echo esc_html( $porto_color_lib->darken( $b['mainmenu-popup-bg-color'], 5 ) ); ?>;
				<?php endif; ?> }
		<?php endif; ?>
		#header .sidebar-menu li:last-child > a { border-bottom: none; }

		<?php if ( $b['side-menu-type'] && 'columns' != $b['side-menu-type'] ) : ?>
			.header-wrapper #header { height: 100vh; -ms-flex-pack: center; justify-content: center; }
			.admin-bar #header { height: calc(100vh - 32px); top: 32px; }
			#header .header-main { overflow-x: hidden; overflow-y: auto; -webkit-overflow-scrolling: touch; scroll-behavior: smooth; }
			#header .header-main::-webkit-scrollbar,
			#header .side-menu-slide ul.sub-menu::-webkit-scrollbar { width: 5px; }
			#header .header-main::-webkit-scrollbar-thumb,
			#header .side-menu-slide ul.sub-menu::-webkit-scrollbar-thumb { border-radius: 0px; background: <?php echo porto_if_light( 'rgba(204, 204, 204, 0.5)', '#39404c' ); ?>;<?php echo porto_if_dark( 'border-color: transparent', '' ); ?> }
			<?php if ( $b['menu-popup-font']['line-height'] ) : ?>
				#header .sidebar-menu li.menu-item > .arrow { top: <?php echo ( 14 + (float) $b['menu-popup-font']['line-height'] - 30 ) / 2; ?>px }
			<?php endif; ?>
			<?php if ( class_exists( 'Woocommerce' ) ) : ?>
				#header .sidebar-menu li.menu-item > .arrow { <?php echo porto_filter_output( $right ); ?>: 10px }
			<?php endif; ?>
			#header .sidebar-menu li.menu-item:hover > .arrow { <?php echo porto_filter_output( $right ); ?>: 5px }
		<?php elseif ( 'columns' == $b['side-menu-type'] ) : ?>
			.header-wrapper #header { height: 100vh; -ms-flex-pack: center; justify-content: center; }
			.admin-bar #header { height: calc(100vh - 32px); top: 32px; }
			#header .header-main .header-row,
			#header .header-main .header-row > div { -ms-flex-align: center; align-items: center; height: 100%; }
		<?php else : ?>
			.header-wrapper #header { min-height: 100vh; }
			.admin-bar #header { min-height: calc(100vh - 32px); top: 0; }
			#header .header-main { -ms-flex: 1; flex: 1; }
			#header.initialize { position: absolute; }
		<?php endif; ?>


		#header .sidebar-menu li.menu-item > .arrow { transition: <?php echo porto_filter_output( $right ); ?> .25s; font-size: .8em; }
		#header .sidebar-menu li.menu-item li.menu-item > a { font-size: inherit; text-transform: inherit; font-weight: inherit; }
		.side-header-overlay { width: 100%; height: 0; top: 0; bottom: 0; left: 0; right: 0; background: rgba(0, 0, 0, 0.8); position: fixed; z-index: 10; opacity: 0; }
		<?php if ( 'slide' == $b['side-menu-type'] ) : ?>
			#header .header-main .header-row { padding: 0; margin: 0 15px; height: 100%; -ms-flex-align: center; align-items: center; overflow: hidden; }
			#header .header-main .header-row > div { -ms-flex-align: center; align-items: center; height: 100%; }
			<?php if ( $b['mainmenu-popup-text-hbg-color'] ) : ?>
				#header .sidebar-menu .narrow li.menu-item:hover > a { background: none; }
				#header .sidebar-menu li.menu-item:not(:last-child) > a { border-bottom: 1px solid <?php echo esc_html( $b['mainmenu-popup-text-hbg-color'] ); ?>; }
			<?php endif; ?>
		<?php endif; ?>
	<?php if ( ! $side_mobile_visible ) : ?>
		}
	<?php endif; ?>

	<?php if ( ! $side_mobile_visible ) : // default side layout ?>
		@media (min-width: 992px) {
			#header.fixed-bottom { position: fixed; bottom: 0; top: auto; }
			.forcefullwidth_wrapper_tp_banner .rev_slider_wrapper { width: 100% !important; left: auto !important; }
		}
		@media (max-width: 991px) {
			#header .sidebar-menu,
			#header .header-copyright { display: none; }
		}
		<?php
	else : // fixed
		$side_narrow_bar_bg = $b['header-bg']['background-color'] && 'transparent' != $b['header-bg']['background-color'] ? $b['header-bg']['background-color'] : '#fff';
		?>
		#header .mobile-toggle { display: none; }
		.header-wrapper #header { <?php echo porto_filter_output( $side_position ); ?>: -<?php echo isset( $current_layout['side_header_width'] ) ? (int) $current_layout['side_header_width'] + 10 : '265'; ?>px; <?php echo porto_filter_output( 'right' == $side_position ? 'left' : 'right' ); ?>: auto; transition: <?php echo porto_filter_output( $side_position ); ?> .4s cubic-bezier(0.55, 0, 0.1, 1); background: <?php echo esc_html( $b['header-bg']['background-color'] ); ?> }
		#header.side-header-visible { <?php echo porto_filter_output( $side_position ); ?>: 90px; }
		.forcefullwidth_wrapper_tp_banner .rev_slider_wrapper { width: 100% !important; left: auto !important; }
		<?php if ( 'side' == $current_layout['side_header_toggle'] ) : ?>
			.page-wrapper.side-nav > * { padding-<?php echo porto_filter_output( $side_position ); ?>: 90px; padding-<?php echo porto_filter_output( 'right' == $side_position ? 'left' : 'right' ); ?>: 0; }
		<?php elseif ( 'top' == $current_layout['side_header_toggle'] ) : ?>
			.page-wrapper.side-nav>* { padding-left: 0; padding-right: 0; }
			.header-wrapper #header { z-index: 1112; }
		<?php endif; ?>
			.side-header-narrow-bar { display: -ms-flexbox; display: flex; -ms-flex-direction: column; flex-direction: column; background-color: <?php echo esc_html( $side_narrow_bar_bg ); ?>; width: 90px; position: <?php echo 'side' == $current_layout['side_header_toggle'] ? 'fixed' : 'absolute'; ?>; top: 0; <?php echo porto_filter_output( $side_position ); ?>: 0; height: 100%; z-index: 1111; text-align: center; }
			.side-header-narrow-bar.side-header-narrow-bar-sticky { position: fixed; }
			@media (min-width: 601px) {
			<?php if ( 'top' == $current_layout['side_header_toggle'] ) : ?>
				.admin-bar .side-header-narrow-bar.side-header-narrow-bar-sticky { top: 32px; }
			<?php else : ?>
				.admin-bar .side-header-narrow-bar { top: 32px; height: calc(100% - 32px); }
			<?php endif; ?>
			}
			.side-header-narrow-bar:after { content: ""; width: 1px; height: 100%; top: 0; bottom: 0; left: auto; right: 0; background: <?php echo esc_html( $porto_color_lib->isColorDark( $side_narrow_bar_bg ) ? 'rgba(255, 255, 255, 0.04)' : 'rgba(0, 0, 0, 0.06)' ); ?>; position: absolute; }
			.side-header-narrow-bar-logo > a { display: inline-block; margin: 1em; max-width: 120px; }
			.side-header-narrow-bar-content { display: -ms-flexbox; display: flex; height: 100%; -ms-flex-align: center; align-items: center; -ms-flex: 1; flex: 1; -ms-flex-pack: center; justify-content: center; }
			.side-header-narrow-bar-content-vertical { transform: rotate(-90deg); white-space: nowrap; text-transform: uppercase; }
			.side-header-narrow-bar-side .side-header-narrow-bar-toggle { padding-bottom: 15px; }

			.side-header-narrow-bar-top { height: auto; width: 100%; -ms-flex-direction: row; flex-direction: row; -ms-flex-align: center;  align-items: center; padding: 0 <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; }
			.side-header-narrow-bar-top.side-header-narrow-bar-sticky { border-bottom: 1px solid <?php echo porto_filter_output( $porto_color_lib->isColorDark( $side_narrow_bar_bg ) ? 'rgba(255, 255, 255, 0.04)' : 'rgba(0, 0, 0, 0.06)' ); ?>; }
			.side-header-narrow-bar-top + #header.side-header-visible { <?php echo porto_filter_output( $side_position ); ?>: 0; }
			.side-header-narrow-bar-top .side-header-narrow-bar-logo > a { margin: 1.8em 0; }

		.side-header-visible + .side-header-overlay { height: 100vh; opacity: 1; }

	<?php endif; ?>
<?php endif; ?>

/* menu type */
<?php if ( porto_header_type_is_side() && 'columns' == $b['side-menu-type'] ) : ?>
	.header-side-nav .sidebar-menu .wide .popup > .inner:before,
	.header-side-nav .sidebar-menu .narrow ul.sub-menu:before,
	.header-side-nav .sidebar-menu .wide .popup > .inner:after,
	.header-side-nav .sidebar-menu .narrow ul.sub-menu:after { content: ''; position: absolute; bottom: 100%; height: 50vh; width: calc(100% + 2px); left: -1px; background-color: inherit; border-width: 0 1px 0 1px; border-style: solid; border-color: inherit; }
	.header-side-nav .sidebar-menu .wide .popup > .inner:after,
	.header-side-nav .sidebar-menu .narrow ul.sub-menu:after { bottom: auto; top: 100%; }
	.header-side-nav .sidebar-menu .wide .popup > .inner,
	.header-side-nav .sidebar-menu .narrow ul.sub-menu { border-color: rgba(0, 0, 0, .03); border-width: 0 1px 0 1px; border-style: solid; position: relative; }
<?php endif; ?>
<?php if ( 'slide' == $b['side-menu-type'] ) : ?>
	.header-side-nav, .main-sidebar-menu .sidebar-menu-wrap { overflow: hidden }
<?php elseif ( 'columns' == $b['side-menu-type'] ) : ?>
	.header-side-nav .side-menu-columns, .main-sidebar-menu .side-menu-columns { position: relative }
<?php endif; ?>

<?php if ( ! isset( $b['show-sticky-contact-info'] ) || ! $b['show-sticky-contact-info'] ) : ?>
	#header.sticky-header .header-contact { display: none; }
<?php endif; ?>

/*------------------ footer ---------------------- */
<?php if ( $b['border-radius'] || ( (int) $header_type >= 10 && (int) $header_type <= 17 ) || empty( $header_type ) ) : ?>
	#footer .widget_wysija_cont .wysija-input { border-radius: <?php echo porto_filter_output( $rtl ? '0 30px 30px 0' : '30px 0 0 30px' ); ?>; padding-<?php echo porto_filter_output( $left ); ?>: 1rem; }
	#footer .widget_wysija_cont .wysija-submit { border-radius: <?php echo porto_filter_output( $rtl ? '30px 0 0 30px' : '0 30px 30px 0' ); ?> }
<?php endif; ?>


/*------------------ theme ---------------------- */
/*------ Grid Gutter Width ------- */
.col-1-5, .col-sm-1-5, .col-md-1-5, .col-lg-1-5, .col-xl-1-5, .col-2-5, .col-sm-2-5, .col-md-2-5, .col-lg-2-5, .col-xl-2-5, .col-3-5, .col-sm-3-5, .col-md-3-5, .col-lg-3-5, .col-xl-3-5, .col-4-5, .col-sm-4-5, .col-md-4-5, .col-lg-4-5, .col-xl-4-5 { padding-left: <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; padding-right: <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; }

/* footer */
#footer .logo { margin-<?php echo porto_filter_output( $right ); ?>: <?php echo (int) $b['grid-gutter-width'] / 2 + 10; ?>px; }

/* header side */
@media (min-width: 992px) {
	#footer .footer-bottom .footer-left .widget { margin-<?php echo porto_filter_output( $right ); ?>: <?php echo (int) $b['grid-gutter-width'] / 2 + 5; ?>px; }
	#footer .footer-bottom .footer-right .widget { margin-<?php echo porto_filter_output( $left ); ?>: <?php echo (int) $b['grid-gutter-width'] / 2 + 5; ?>px; }
	body.boxed.body-side { padding-<?php echo porto_filter_output( $left ); ?>: <?php echo (int) $b['grid-gutter-width'] + 256; ?>px; padding-<?php echo porto_filter_output( $right ); ?>: <?php echo (int) $b['grid-gutter-width']; ?>px; }
	body.boxed.body-side.modal-open { padding-<?php echo porto_filter_output( $left ); ?>: <?php echo (int) $b['grid-gutter-width'] + 256; ?>px !important; padding-<?php echo porto_filter_output( $right ); ?>: <?php echo (int) $b['grid-gutter-width']; ?>px !important; }
	body.boxed.body-side .page-wrapper.side-nav .container { padding-<?php echo porto_filter_output( $left ); ?>: <?php echo (int) $b['grid-gutter-width']; ?>px; padding-<?php echo porto_filter_output( $right ); ?>: <?php echo (int) $b['grid-gutter-width']; ?>px; }
	body.boxed.body-side .page-wrapper.side-nav .page-top.fixed-pos { <?php echo porto_filter_output( $left ); ?>: <?php echo (int) $b['grid-gutter-width'] + 256; ?>px; <?php echo porto_filter_output( $right ); ?>: <?php echo (int) $b['grid-gutter-width']; ?>px; }
}

/* header */
@media (min-width: 768px) {
	#header-boxed #header.sticky-header .header-main.sticky { max-width: <?php echo (int) $b['grid-gutter-width'] + 720; ?>px; }
}
@media (min-width: 992px) {
	#header-boxed #header.sticky-header .header-main.sticky,
	#header-boxed #header.sticky-header .main-menu-wrap { max-width: <?php echo (int) $b['grid-gutter-width'] + 960; ?>px; }
}

/* page top */
.page-top .sort-source { <?php echo porto_filter_output( $right ); ?>: <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; }

/* post */
.post-carousel .post-item,
.widget .row .post-item-small,
.elementor-widget .row .post-item-small { margin: 0 <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; }
@media (min-width: 992px) {
    .single-post .post-medium .post-media { margin-<?php echo porto_filter_output( $right ); ?>: <?php echo esc_html( $b['grid-gutter-width'] ); ?>px; }
}
article.post-medium .post-image.single { margin-bottom: <?php echo esc_html( $b['grid-gutter-width'] ); ?>px; }

/* carousel */
.owl-carousel.show-nav-title.post-carousel .owl-nav,
.owl-carousel.show-nav-title.portfolio-carousel .owl-nav,
.owl-carousel.show-nav-title.member-carousel .owl-nav { <?php echo porto_filter_output( $right ); ?>: <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; }

/* featured box */
.featured-box .box-content { padding: 30px <?php echo (int) $b['grid-gutter-width']; ?>px 10px <?php echo (int) $b['grid-gutter-width']; ?>px; border-top-color: <?php echo porto_if_dark( $color_dark_5, '#dfdfdf' ); ?>; }
@media (max-width: 767px) {
	.featured-box .box-content { padding: 25px <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px 5px <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; }
}

/* navs */
.sticky-nav-wrapper { margin: 0 <?php echo esc_html( -( $b['grid-gutter-width'] / 2 ) ); ?>px; }

/* pricing table */
.pricing-table { padding: 0 <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; }

/* sections */
/*.vc_row.section { margin-left: -<?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; margin-right: -<?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; }*/
.col-half-section { padding-left: <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; padding-right: <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; max-width: <?php echo (float) ( ( $b['container-width'] / 2 ) - ( $b['grid-gutter-width'] / 2 ) ); ?>px; }
.vc_column_container.section,
.row.no-padding > .vc_column_container.section { padding-left: <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; padding-right: <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; }

@media (min-width: 992px) and <?php echo porto_filter_output( $screen_large ); ?> {
	.col-half-section { max-width: <?php echo ( 960 / 2 ) - ( $b['grid-gutter-width'] / 2 ); ?>px; }
}
@media (max-width: 991px) {
	.col-half-section { max-width: <?php echo ( 720 / 2 ) - ( $b['grid-gutter-width'] / 2 ); ?>px; }
	.col-half-section.col-fullwidth-md { max-width: 720px; float: none !important; margin-left: auto !important; margin-right: auto !important; -webkit-align-self: auto; -ms-flex-item-align: auto; align-self: auto; }
}
@media (max-width: 767px) {
	.col-half-section { max-width: 540px; float: none !important; margin-left: auto !important; margin-right: auto !important; -webkit-align-self: auto; -ms-flex-item-align: auto; align-self: auto; }
}
@media (max-width: 575px) {
	.col-half-section { padding-left: 0; padding-right: 0; }
}

/* shortcodes */
.blog-posts-hover_info article.post,
.blog-posts-hover_info2 article.post,
.blog-posts .post-item-list,
.porto-image-frame { margin-bottom: <?php echo (int) $b['grid-gutter-width']; ?>px; }
.grid-creative article.post { margin-bottom: 0; padding-bottom: <?php echo (int) $b['grid-gutter-width']; ?>px }

/* siders */
body.boxed #revolutionSliderCarouselContainer,
#main.main-boxed #revolutionSliderCarouselContainer { margin-left: -<?php echo (int) $b['grid-gutter-width']; ?>px; margin-right: -<?php echo (int) $b['grid-gutter-width']; ?>px; }
@media (max-width: 767px) {
	body.boxed #revolutionSliderCarouselContainer,
	#main.main-boxed #revolutionSliderCarouselContainer { margin-left: -<?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; margin-right: -<?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; }
}

/* toggles */
.toggle > .toggle-content { padding-<?php echo porto_filter_output( $left ); ?>: <?php echo (int) $b['grid-gutter-width'] / 2 + 5; ?>px; }

/* visual composer */
.vc_row.wpb_row.vc_row-no-padding .vc_column_container.section { padding-left: <?php echo (int) $b['grid-gutter-width']; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width']; ?>px; }
@media (max-width: 767px) {
	.vc_row.wpb_row.vc_row-no-padding .vc_column_container.section { padding-left: <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; padding-right: <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; }
}
body.vc_row { margin-left: -<?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; margin-right: -<?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; }

/* layouts boxed */
body.boxed .porto-container.container,
#main.main-boxed .porto-container.container { margin-left: -<?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; margin-right: -<?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; }
body.boxed .vc_row[data-vc-stretch-content].section,
#main.main-boxed .vc_row[data-vc-stretch-content].section { padding-left: <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; padding-right: <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; }
@media (min-width: 768px) {
	body.boxed .vc_row[data-vc-stretch-content],
	#main.main-boxed .vc_row[data-vc-stretch-content] { margin-left: -<?php echo (int) $b['grid-gutter-width']; ?>px !important; margin-right: -<?php echo (int) $b['grid-gutter-width']; ?>px !important; max-width: <?php echo 720 + (int) $b['grid-gutter-width']; ?>px; }
}
@media (min-width: 992px) {
	body.boxed .vc_row[data-vc-stretch-content],
	#main.main-boxed .vc_row[data-vc-stretch-content] { max-width: <?php echo 960 + (int) $b['grid-gutter-width']; ?>px; }
}
body.boxed #main.wide .vc_row[data-vc-stretch-content] .porto-wrap-container { padding-left: <?php echo (int) $b['grid-gutter-width']; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width']; ?>px; }
@media (max-width: 767px) {
	body.boxed #main.wide .vc_row[data-vc-stretch-content] .porto-wrap-container { padding-left: <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; padding-right: <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; }
}
body.boxed #main.wide .container .vc_row { margin-left: -<?php echo (int) $b['grid-gutter-width']; ?>px; margin-right: -<?php echo (int) $b['grid-gutter-width']; ?>px; padding-left: <?php echo (int) $b['grid-gutter-width'] / 2; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width'] / 2; ?>px; }
body.boxed #main.wide .container .vc_row.porto-inner-container { padding-left: <?php echo (int) $b['grid-gutter-width']; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width']; ?>px; }
body.boxed #main.wide .container .vc_row .vc_row { margin-left: -<?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; margin-right: -<?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; }
@media (min-width: 768px) {
	body.boxed #header.sticky-header .header-main.sticky { max-width: <?php echo 720 + $b['grid-gutter-width']; ?>px; }
}
@media (min-width: 992px) {
	body.boxed #header.sticky-header .header-main.sticky,
	body.boxed #header.sticky-header .main-menu-wrap { max-width: <?php echo 960 + $b['grid-gutter-width']; ?>px; }
}
#breadcrumbs-boxed .page-top { padding-left: <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; padding-right: <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; }

/* layouts defaults */
body.wide .container:not(.inner-container) { padding-left: <?php echo (int) $b['grid-gutter-width']; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width']; ?>px; }
body.wide .container .container { padding-left: 0; padding-right: 0; }
.container-fluid .container .vc_row,
#main.wide > .container > .row { margin-left: -<?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; margin-right: -<?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; }

/* member */
.member-row .member { padding: 0 <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px 1px; margin-bottom: <?php echo (int) $b['grid-gutter-width']; ?>px; width: 100% }
.member-row-advanced .member { padding: 0; width: 100% }

/* home */
body .menu-ads-container { margin-left: -<?php echo 20 + $b['grid-gutter-width'] / 2; ?>px; margin-right: -<?php echo 20 + $b['grid-gutter-width'] / 2; ?>px; }
body .ads-container-blue,
body.boxed .ads-container-full,
#main.main-boxed .ads-container-full,
body.boxed #main.wide .ads-container-full { margin-left: -<?php echo (int) $b['grid-gutter-width']; ?>px !important; margin-right: -<?php echo (int) $b['grid-gutter-width']; ?>px !important; }
@media (max-width: 767px) {
	body.boxed .ads-container-full,
	#main.main-boxed .ads-container-full,
	body.boxed #main.wide .ads-container-full { margin-left: -<?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px !important; margin-right: -<?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px !important; }
}

/* portfolio */
.popup-inline-content hr.solid,
.mfp-content .ajax-container hr.solid,
.portfolio .portfolio-image.wide,
body.boxed .portfolio hr.solid,
body.boxed #portfolioAjaxBox .portfolio-image.wide,
body.boxed #portfolioAjaxBox hr.solid,
#main.main-boxed .portfolio .portfolio-image.wide,
#main.main-boxed .portfolio hr.solid,
#main.main-boxed #portfolioAjaxBox .portfolio-image.wide,
#main.main-boxed #portfolioAjaxBox hr.solid,
body.boxed .portfolio-row.full,
#main.wide .portfolio .portfolio-image.wide,
#main.wide .page-image.single { margin-left: -<?php echo (int) $b['grid-gutter-width']; ?>px; margin-right: -<?php echo (int) $b['grid-gutter-width']; ?>px; }
.popup-inline-content .portfolio-image.wide { margin-left: -<?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; margin-right: -<?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; }

.portfolio-carousel .portfolio-item { margin-left: <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; margin-right: <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; }
.portfolio-row { margin-left: -<?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; margin-right: -<?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; }
.portfolio-row .portfolio { padding-left: <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; padding-right: <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; padding-bottom: <?php echo (int) $b['grid-gutter-width']; ?>px; }
.portfolio-modal .vc_row[data-vc-full-width],
body.boxed .portfolio-modal .vc_row[data-vc-full-width],
#main.main-boxed .portfolio-modal .vc_row[data-vc-full-width],
.portfolio-modal .vc_row[data-vc-stretch-content],
body.boxed .portfolio-modal .vc_row[data-vc-stretch-content],
#main.main-boxed .portfolio-modal .vc_row[data-vc-stretch-content],
.portfolio-ajax-modal .vc_row[data-vc-full-width],
body.boxed .portfolio-ajax-modal .vc_row[data-vc-full-width],
#main.main-boxed .portfolio-ajax-modal .vc_row[data-vc-full-width],
.portfolio-ajax-modal .vc_row[data-vc-stretch-content],
body.boxed .portfolio-ajax-modal .vc_row[data-vc-stretch-content],
#main.main-boxed .portfolio-ajax-modal .vc_row[data-vc-stretch-content] { padding-left: <?php echo (int) $b['grid-gutter-width']; ?>px !important; padding-right: <?php echo (int) $b['grid-gutter-width']; ?>px !important; }

/* shop */
.cross-sells .slider-wrapper .products .product { padding-left: <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; padding-right: <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; }
.col2-set { margin-left: -<?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; margin-right: -<?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; }
.col2-set .col-1, .col2-set .col-2 { padding-left: <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; padding-right: <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; }
.single-product .variations:after { <?php echo porto_filter_output( $left ); ?>: <?php echo esc_html( $b['grid-gutter-width'] / 2 ); ?>px; width: calc(100% - <?php echo (int) $b['grid-gutter-width']; ?>px); }

/*------ Screen Large Variable ------- */
@media (min-width: <?php echo (int) ( $b['container-width'] + $b['grid-gutter-width'] ); ?>px) {
	.ccols-xl-2 > * {
		flex: 0 0 50%;
		max-width: 50%;
	}
	.ccols-xl-3 > * {
		flex: 0 0 33.3333%;
		max-width: 33.3333%;
	}
	.ccols-xl-4 > * {
		flex: 0 0 25%;
		max-width: 25%;
	}
	.ccols-xl-5 > * {
		flex: 0 0 20%;
		max-width: 20%;
	}
	.ccols-xl-6 > * {
		flex: 0 0 16.6666%;
		max-width: 16.6666%;
	}
	.ccols-xl-7 > * {
		flex: 0 0 14.2857%;
		max-width: 14.2857%;
	}
	.ccols-xl-8 > * {
		flex: 0 0 12.5%;
		max-width: 12.5%;
	}
	.ccols-xl-9 > * {
		flex: 0 0 11.1111%;
		max-width: 11.1111%;
	}
	.ccols-xl-10 > * {
		flex: 0 0 10%;
		max-width: 10%;
	}
	.shop_table.wishlist_table .add-links {
		flex-direction: row;
	}
	.shop_table.wishlist_table .quickview {
		margin-<?php echo porto_filter_output( $right ); ?>: 10px;
		margin-bottom: 0;
	}
}
@media (min-width: 1400px) {
	.ccols-sl-10 > * {
		max-width: 10%; flex: 0 0 10%;
	}
	.ccols-sl-9 > * {
		max-width: 11.1111%; flex: 0 0 11.1111%;
	}
	.ccols-sl-8 > * {
		max-width: 12.5%; flex: 0 0 12.5%;
	}
	.ccols-sl-7 > * {
		max-width: 14.2857%; flex: 0 0 14.2857%;
	}
}

@media <?php echo porto_filter_output( $screen_large ); ?> {
	/*#header .header-top .porto-view-switcher > li.menu-item > a,
	#header .header-top .top-links > li.menu-item > a { padding-top: 3px !important; padding-bottom: 3px !important; }*/

	.mega-menu > li.menu-item > a { padding: 9px 9px 8px; }

	.widget_sidebar_menu .widget-title { font-size: 0.8571em; line-height: 13px; padding: 10px 15px; }

	.sidebar-menu > li.menu-item > a { font-size: 0.9286em; line-height: 17px; padding: 9px 5px; }
	.sidebar-menu .menu-custom-block a { font-size: 0.9286em; line-height: 16px; padding: 9px 5px; }
	.sidebar-menu > li.menu-item .popup:before { top: 11px; }

	.porto-links-block { font-size: 13px; }
	/*.porto-links-block .links-title { padding: 8px 12px 6px; }
	.porto-links-block li.porto-links-item > a,
	.porto-links-block li.porto-links-item > span { padding: 7px 5px; line-height: 19px; margin: 0 7px -1px; }*/

	body .sidebar-menu .menu-ads-container .vc_column_container .porto-sicon-box.left-icon { padding: 15px 0; }
	body .sidebar-menu .menu-ads-container .vc_column_container .left-icon .porto-sicon-left { display: block; }
	body .sidebar-menu .menu-ads-container .vc_column_container .left-icon .porto-sicon-left .porto-icon { font-size: 25px !important; margin-bottom: 10px; }
	body .sidebar-menu .menu-ads-container .vc_column_container .left-icon .porto-sicon-body { display: block; text-align: center; }


<?php if ( class_exists( 'Woocommerce' ) ) : ?>
	ul.pcols-md-6 li.product-col { max-width: 16.6666%; flex: 0 0 16.6666%; }
	ul.pwidth-md-6 .product-image { font-size: 0.8em; }
	ul.pwidth-md-6 .add-links { font-size: 0.85em; }
	ul.pcols-md-5 li.product-col { max-width: 20%; flex: 0 0 20%; }
	ul.pwidth-md-5 .product-image { font-size: 0.9em; }
	ul.pwidth-md-5 .add-links { font-size: 0.95em; }
	ul.pcols-md-4 li.product-col { max-width: 25%; flex: 0 0 25%; }
	ul.pwidth-md-4 .product-image { font-size: 1em; }
	ul.pwidth-md-4 .add-links { font-size: 1em; }
	ul.pcols-md-3 li.product-col { max-width: 33.3333%; flex: 0 0 33.3333%; }
	ul.pwidth-md-3 .product-image { font-size: 1.15em; }
	ul.pwidth-md-3 .add-links { font-size: 1em; }
	ul.pcols-md-2 li.product-col { max-width: 50%; flex: 0 0 50%; }
	ul.pwidth-md-2 .product-image { font-size: 1.4em; }
	ul.pwidth-md-2 .add-links { font-size: 1em; }
	ul.pcols-md-1 li.product-col { max-width: none; flex: 0 0 100%; }
<?php endif; ?>
}

@media (min-width: 992px) and <?php echo porto_filter_output( $screen_large ); ?> {
	.portfolio-row .portfolio-col-6 { width: 20%; }
	.portfolio-row .portfolio-col-6.w2 { width: 40%; }

<?php if ( class_exists( 'Woocommerce' ) ) : ?>
	.column2 ul.pwidth-md-5 .product-image { font-size: 0.75em; }
	.column2 ul.pwidth-md-5 .add-links { font-size: 0.8em; }
	.column2 ul.pwidth-md-4 .product-image { font-size: 0.8em; }
	.column2 ul.pwidth-md-4 .add-links { font-size: 0.9em; }
	.column2 ul.pwidth-md-3 .product-image { font-size: 0.9em; }
	.column2 ul.pwidth-md-3 .add-links { font-size: 1em; }
	.column2 ul.pwidth-md-2 .product-image { font-size: 1.1em; }
	.column2 ul.pwidth-md-2 .add-links { font-size: 1em; }

	.column2 .shop-loop-before .woocommerce-pagination ul { margin-<?php echo porto_filter_output( $left ); ?>: -5px; }

	.quickview-wrap { width: 720px; }
	ul.product_list_widget li .product-image { width: 70px; -ms-flex: 0 0 70px; flex: 0 0 70px; margin-<?php echo porto_filter_output( $right ); ?>: 15px }
	ul.product_list_widget li .product-details { max-width: calc(100% - 85px); }
<?php endif; ?>
}

@media (min-width: 768px) and <?php echo porto_filter_output( $screen_large ); ?> {
	.column2 .portfolio-row .portfolio-col-4 { width: 33.33333333%; }
	.column2 .portfolio-row .portfolio-col-4.w2 { width: 66.66666666%; }
	.column2 .portfolio-row .portfolio-col-5,
	.column2 .portfolio-row .portfolio-col-6 { width: 25%; }
	.column2 .portfolio-row .portfolio-col-5.w2,
	.column2 .portfolio-row .portfolio-col-6.w2 { width: 50%; }
}

<?php if ( class_exists( 'Woocommerce' ) ) : ?>
	@media (min-width: 768px) and (max-width: 991px) {
		ul.pcols-sm-4 li.product-col { max-width: 25%; flex: 0 0 25%; }
		ul.pcols-sm-3 li.product-col { max-width: 33.3333%; flex: 0 0 33.3333%; }
		ul.pcols-sm-2 li.product-col { max-width: 50%; flex: 0 0 50%; }
		ul.pcols-sm-1 li.product-col { max-width: none; flex: 0 0 100%; }
	}
	@media (max-width: 767px) {
		ul.pcols-xs-4 li.product-col { max-width: 25%; flex: 0 0 25%; }
		ul.pcols-xs-3 li.product-col { max-width: 33.3333%; flex: 0 0 33.3333%; }
		ul.pwidth-xs-3 .product-image { font-size: .85em; }
		ul.pwidth-xs-3 .add-links { font-size: .85em; }
		ul.pcols-xs-2 li.product-col { max-width: 50%; flex: 0 0 50%; }
		ul.pwidth-xs-2 .product-image { font-size: 1em; }
		ul.pwidth-xs-2 .add-links { font-size: 1em; }
		ul.pcols-xs-1 li.product-col { max-width: none; flex: 0 0 100%; }
		ul.pwidth-xs-1 .product-image { font-size: 1.2em; }
		ul.pwidth-xs-1 .add-links { font-size: 1em; }
	}
	@media (max-width: 575px) {
		ul.pcols-ls-2 li.product-col { max-width: 50%; flex: 0 0 50%; }
		ul.pwidth-ls-2 .product-image { font-size: .8em; }
		ul.pwidth-ls-2 .add-links { font-size: .85em; }
		ul.pcols-ls-1 li.product-col { max-width: none; flex: 0 0 100%; }
		ul.pwidth-ls-1 .product-image { font-size: 1.1em; }
		ul.pwidth-ls-1 .add-links { font-size: 1em; }
	}
	@media (min-width: 576px) {
		ul.list li.product { -ms-flex: 0 0 100%; flex: 0 0 100%; max-width: none; }
	}
<?php endif; ?>

/*------ Border Radius ------- */
<?php if ( $b['border-radius'] ) : ?>
	.wcvashopswatchlabel { border-radius: 1px; }

	.accordion-menu .tip,
	#header .searchform .autocomplete-suggestion span.yith_wcas_result_on_sale,
	#header .searchform .autocomplete-suggestion span.yith_wcas_result_featured,
	#header .menu-custom-block .tip,
	.mega-menu .tip,
	#nav-panel .menu-custom-block .tip,
	#side-nav-panel .menu-custom-block .tip,
	.sidebar-menu .tip,
	article.post .post-date .sticky,
	.post-item .post-date .sticky,
	article.post .post-date .format,
	.post-item .post-date .format,
	.thumb-info .thumb-info-type,
	.wcvaswatchinput.active .wcvashopswatchlabel { border-radius: 2px; }
	article.post .post-date .month,
	.post-item .post-date .month { border-radius: 0 0 2px 2px; }
	article.post .post-date .day,
	.post-item .post-date .day { border-radius: 2px 2px 0 0; }
	.pricing-table h3 { border-radius: 2px 2px 0 0; }

	.accordion-menu .arrow,
	#footer .thumbnail img,
	#footer .img-thumbnail img,
	.widget_sidebar_menu,
	.widget_sidebar_menu .widget-title .toggle,
	<?php if ( ( class_exists( 'bbPress' ) && is_bbpress() ) || ( class_exists( 'BuddyPress' ) && is_buddypress() ) ) : ?>
		.bbp-pagination-links a,
		.bbp-pagination-links span.current,
		.bbp-topic-pagination a,
		#bbpress-forums #bbp-your-profile fieldset input,
		#bbpress-forums #bbp-your-profile fieldset textarea,
		#bbpress-forums p.bbp-topic-meta img.avatar,
		#bbpress-forums ul.bbp-reply-revision-log img.avatar,
		#bbpress-forums ul.bbp-topic-revision-log img.avatar,
		#bbpress-forums div.bbp-template-notice img.avatar,
		.widget_display_topics img.avatar,
		.widget_display_replies img.avatar,
		#buddypress div.pagination .pagination-links a,
		#buddypress div.pagination .pagination-links span.current,
		#buddypress form#whats-new-form textarea,
		#buddypress .activity-list .activity-content .activity-header img.avatar,
		#buddypress div.activity-comments form .ac-textarea,
	<?php endif; ?>
	.accordion .card-header,
	.progress-bar-tooltip,
	<?php echo porto_filter_output( $input_lists ); ?>,
	textarea,
	select,
	input[type="submit"],
	.thumb-info img,
	.toggle-simple .toggle > label:after,
	body .btn-sm,
	body .btn-group-sm > .btn,
	body .btn-xs,
	body .btn-group-xs > .btn,
	.tm-collapse .tm-section-label,
	body .ads-container,
	body .ads-container-light,
	body .ads-container-blue,
	.chosen-container-single .chosen-single,
	.woocommerce-checkout .form-row .chosen-container-single .chosen-single,
	.select2-container .select2-choice,
	.product-nav .product-popup .product-image img,
	div.quantity .minus,
	div.quantity .plus,
	.gridlist-toggle > a,
	.wcvaswatchlabel,
	.widget .widget-title .toggle,
	ul.product_list_widget li .product-image img,
	.woocommerce-password-strength { border-radius: 3px; }
	.pagination > a:first-child,
	.pagination > span:first-child,
	.page-links > a:first-child,
	.page-links > span:first-child { border-top-<?php echo porto_filter_output( $left ); ?>-radius: .25rem; border-bottom-<?php echo porto_filter_output( $left ); ?>-radius: .25rem; }
	.pagination > a:last-child,
	.pagination > span:last-child,
	.page-links > a:last-child,
	.page-links > span:last-child { border-top-<?php echo porto_filter_output( $right ); ?>-radius: .25rem; border-bottom-<?php echo porto_filter_output( $right ); ?>-radius: .25rem; }
	.widget_sidebar_menu .widget-title,
	.member-item.member-item-3 .thumb-info-wrapper img { border-radius: 3px 3px 0 0; }
	body .menu-ads-container { border-radius: 0 0 3px 3px; }

	#header .porto-view-switcher > li.menu-item > a,
	#header .top-links > li.menu-item > a,
	#header .searchform .autocomplete-suggestion img,
	#mini-cart .widget_shopping_cart_content,
	#header .mobile-toggle,
	.mega-menu li.menu-item > a > .thumb-info-preview .thumb-info-wrapper,
	.mega-menu > li.menu-item.active > a,
	.mega-menu > li.menu-item:hover > a,
	.mega-menu .wide .popup,
	.mega-menu .wide li.sub li.menu-item > a,
	.mega-menu .narrow ul.sub-menu ul.sub-menu,
	#nav-panel .accordion-menu li > a,
	.sidebar-menu li.menu-item > a > .thumb-info-preview .thumb-info-wrapper,
	.sidebar-menu .wide li.menu-item li.menu-item > a,
	#bbpress-forums div.bbp-forum-author img.avatar,
	#bbpress-forums div.bbp-topic-author img.avatar,
	#bbpress-forums div.bbp-reply-author img.avatar,
	div.bbp-template-notice,
	div.indicator-hint,
	<?php if ( ( class_exists( 'bbPress' ) && is_bbpress() ) || ( class_exists( 'BuddyPress' ) && is_buddypress() ) ) : ?>
		#buddypress .activity-list li.load-more,
		#buddypress .activity-list li.load-newest,
		#buddypress .standard-form textarea,
		#buddypress .standard-form input[type=text],
		#buddypress .standard-form input[type=color],
		#buddypress .standard-form input[type=date],
		#buddypress .standard-form input[type=datetime],
		#buddypress .standard-form input[type=datetime-local],
		#buddypress .standard-form input[type=email],
		#buddypress .standard-form input[type=month],
		#buddypress .standard-form input[type=number],
		#buddypress .standard-form input[type=range],
		#buddypress .standard-form input[type=search],
		#buddypress .standard-form input[type=tel],
		#buddypress .standard-form input[type=time],
		#buddypress .standard-form input[type=url],
		#buddypress .standard-form input[type=week],
		#buddypress .standard-form select,
		#buddypress .standard-form input[type=password],
		#buddypress .dir-search input[type=search],
		#buddypress .dir-search input[type=text],
		#buddypress .groups-members-search input[type=search],
		#buddypress .groups-members-search input[type=text],
		#buddypress button,
		#buddypress a.button,
		#buddypress input[type=submit],
		#buddypress input[type=button],
		#buddypress input[type=reset],
		#buddypress ul.button-nav li a,
		#buddypress div.generic-button a,
		#buddypress .comment-reply-link,
		a.bp-title-button,
		#buddypress div.item-list-tabs ul li.selected a,
		#buddypress div.item-list-tabs ul li.current a,
	<?php endif; ?>
	.blog-posts-padding .grid-box,
	.img-rounded, .rounded,
	.img-thumbnail,
	.img-thumbnail img,
	.img-thumbnail .inner,
	.page-wrapper .fdm-item-image,
	.share-links a,
	.tabs,
	.testimonial.testimonial-style-2 blockquote,
	.testimonial.testimonial-style-3 blockquote,
	.testimonial.testimonial-style-4 blockquote,
	.testimonial.testimonial-style-5 blockquote,
	.testimonial.testimonial-style-6 blockquote,
	.thumb-info,
	.thumb-info .thumb-info-wrapper,
	.thumb-info .thumb-info-wrapper:after,
	section.timeline .timeline-date,
	section.timeline .timeline-box,
	body .btn,
	body .btn-md,
	body .btn-group-md > .btn,
	div.wpb_single_image .vc_single_image-wrapper.vc_box_rounded,
	div.wpb_single_image .vc_single_image-wrapper.vc_box_shadow,
	div.wpb_single_image .vc_single_image-wrapper.vc_box_rounded img,
	div.wpb_single_image .vc_single_image-wrapper.vc_box_shadow img,
	div.wpb_single_image .vc_single_image-wrapper.vc_box_border,
	div.wpb_single_image .vc_single_image-wrapper.vc_box_outline,
	div.wpb_single_image .vc_single_image-wrapper.vc_box_shadow_border,
	div.wpb_single_image .vc_single_image-wrapper.vc_box_border img,
	div.wpb_single_image .vc_single_image-wrapper.vc_box_outline img,
	div.wpb_single_image .vc_single_image-wrapper.vc_box_shadow_border img,
	div.wpb_single_image .vc_single_image-wrapper.vc_box_shadow_3d img,
	div.wpb_single_image .porto-vc-zoom.porto-vc-zoom-hover-icon:before,
	div.wpb_single_image.vc_box_border,
	div.wpb_single_image.vc_box_outline,
	div.wpb_single_image.vc_box_shadow_border,
	div.wpb_single_image.vc_box_border img,
	div.wpb_single_image.vc_box_outline img,
	div.wpb_single_image.vc_box_shadow_border img,
	.flickr_badge_image,
	.wpb_content_element .flickr_badge_image,
	.tm-collapse,
	.tm-box,
	div.wpcf7-response-output,
	.success-message-container button,
	#header .header-contact .nav-top a,
	#header .header-contact .nav-top span,
	article .comment-respond input[type="submit"] { border-radius: 4px; }
	<?php if ( class_exists( 'Woocommerce' ) ) : ?>
		.product-image .labels .onhot,
		.product-image .labels .onsale,
		.yith-wcbm-badge,
		.summary-before .labels .onhot,
		.summary-before .labels .onsale,
		.woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li a,
		.woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li a:hover,
		.woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li span,
		.woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li span:hover,
		.woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li a,
		.woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li a:hover,
		.woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li span,
		.woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li span:hover,
		.woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen a,
		.woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen a:hover,
		.woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen span,
		.woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen span:hover,
		.woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen a,
		.woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen a:hover,
		.woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen span,
		.woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen span:hover,
		.woocommerce .yith-woo-ajax-navigation ul.yith-wcan-label li a,
		.woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-label li a,
		.woocommerce .yith-woo-ajax-navigation ul.yith-wcan-label li.chosen a,
		.woocommerce .yith-woo-ajax-navigation ul.yith-wcan-label li a:hover,
		.woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-label li.chosen a,
		.woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-label li a:hover,
		.shop_table.wishlist_table .add_to_cart { border-radius: 4px; }
	<?php endif; ?>
	#header .porto-view-switcher > li.menu-item:hover > a,
	#header .top-links > li.menu-item:hover > a,
	.mega-menu > li.menu-item.has-sub:hover > a,
	html #topcontrol,
	.tabs.tabs-bottom .tab-content,
	.member-item.member-item-3 .thumb-info,
	.member-item.member-item-3 .thumb-info-wrapper { border-radius: 4px 4px 0 0; }
	.mega-menu .wide .popup > .inner,
	.resp-tab-content,
	.tab-content { border-radius: 0 0 4px 4px; }
	.mega-menu .wide.pos-left .popup,
	.mega-menu .narrow.pos-left .popup > .inner > ul.sub-menu { border-radius: <?php echo porto_filter_output( $rtl ? '4px 0 4px 4px' : '0 4px 4px 4px' ); ?>; }
	.mega-menu .wide.pos-right .popup,
	.mega-menu .narrow.pos-right .popup > .inner > ul.sub-menu { border-radius: <?php echo porto_filter_output( $rtl ? '0 4px 4px 4px' : '4px 0 4px 4px' ); ?>; }
	.mega-menu .narrow .popup > .inner > ul.sub-menu { border-radius: <?php echo porto_filter_output( $rtl ? '4px 0 4px 4px' : '0 4px 4px 4px' ); ?>; }
	.owl-carousel.full-width .owl-nav .owl-prev,
	.owl-carousel.big-nav .owl-nav .owl-prev,
	.resp-vtabs .resp-tabs-container { border-radius: <?php echo porto_filter_output( $rtl ? '4px 0 0 4px' : '0 4px 4px 0' ); ?>; }
	.owl-carousel.full-width .owl-nav .owl-next,
	.owl-carousel.big-nav .owl-nav .owl-next { border-radius: <?php echo porto_filter_output( $rtl ? '0 4px 4px 0' : '4px 0 0 4px' ); ?>; }

	@media (min-width: 992px) {
		.header-wrapper.header-side-nav #header .searchform { border-radius: 5px; }
		.header-wrapper.header-side-nav #header .searchform input { border-radius: <?php echo porto_filter_output( $rtl ? '0 5px 5px 0' : '5px 0 0 5px' ); ?>; }
		.header-wrapper.header-side-nav #header .searchform button { border-radius: <?php echo porto_filter_output( $rtl ? '5px 0 0 5px' : '0 5px 5px 0' ); ?>; }
	}
	<?php if ( ( class_exists( 'bbPress' ) && is_bbpress() ) || ( class_exists( 'BuddyPress' ) && is_buddypress() ) ) : ?>
		#buddypress form#whats-new-form #whats-new-avatar img.avatar,
		#buddypress .activity-list li.mini .activity-avatar img.avatar,
		#buddypress .activity-list li.mini .activity-avatar img.FB_profile_pic,
		#buddypress .activity-permalink .activity-list li.mini .activity-avatar img.avatar,
		#buddypress .activity-permalink .activity-list li.mini .activity-avatar img.FB_profile_pic,
		#buddypress div#message p,
		#sitewide-notice p,
		#bp-uploader-warning,
		#bp-webcam-message p.warning,
		#buddypress table.forum td img.avatar,
		#buddypress div#item-header ul img.avatar,
		#buddypress div#item-header ul.avatars img.avatar,
		#buddypress ul.item-list li img.avatar,
		#buddypress div#message-thread img.avatar,
		#buddypress #message-threads img.avatar,
		.widget.buddypress div.item-avatar img.avatar,
		.widget.buddypress ul.item-list img.avatar,
		.bp-login-widget-user-avatar img.avatar { border-radius: 5px; }
		@media only screen and (max-width: 240px) {
			#buddypress ul.item-list li img.avatar { border-radius: 5px; }
		}
	<?php endif; ?>
	@media (max-width: 767px) {
		ul.comments ul.children > li .comment-body,
		ul.comments > li .comment-body { border-radius: 5px; }
	}
	ul.comments .comment-block,
	.pricing-table .plan,
	.tabs-navigation,
	.toggle > label,
	body.boxed .page-wrapper { border-radius: 5px; }
	<?php if ( class_exists( 'Woocommerce' ) ) : ?>
		.add-links .add_to_cart_button.loading.viewcart-style-1:after,
		.add-links .quickview.loading:after,
		.commentlist li .comment-text,
		.product-image img,
		.shop_table,
		.product-nav .product-popup .product-image,
		.product-summary-wrap .yith-wcwl-add-to-wishlist a:before,
		.product-summary-wrap .yith-wcwl-add-to-wishlist span:before,
		ul.product_list_widget li .product-image,
		.widget_recent_reviews .product_list_widget li img { border-radius: 5px; }
		.shop_table thead tr:first-child th:first-child,
		.shop_table thead tr:first-child td:first-child { border-radius: <?php echo porto_filter_output( $rtl ? '0 5px 0 0' : '5px 0 0 0' ); ?>; }
		.shop_table thead tr:first-child th:last-child,
		.shop_table thead tr:first-child td:last-child { border-radius: <?php echo porto_filter_output( $rtl ? '5px 0 0 0' : '0 5px 0 0' ); ?>; }
		.shop_table thead tr:first-child th:only-child,
		.shop_table thead tr:first-child td:only-child { border-radius: 5px 5px 0 0; }
		.shop_table tfoot tr:last-child th:first-child,
		.shop_table tfoot tr:last-child td:first-child { border-radius: <?php echo porto_filter_output( $rtl ? '0 0 5px 0' : '0 0 0 5px' ); ?>; }
		.shop_table tfoot tr:last-child th:last-child,
		.shop_table tfoot tr:last-child td:last-child { border-radius: <?php echo porto_filter_output( $rtl ? '0 0 0 5px' : '0 0 5px 0' ); ?>; }
		.shop_table tfoot tr:last-child th:only-child,
		.shop_table tfoot tr:last-child td:only-child { border-radius: 0 0 5px 5px; }
		@media (max-width: 575px) {
			.commentlist li .comment_container { border-radius: 5px; }
		}
	<?php endif; ?>
	.br-normal { border-radius: 5px !important; }
	.resp-tabs-list li,
	.nav-tabs li .nav-link,
	.tabs-navigation .nav-tabs > li:first-child .nav-link { border-radius: 5px 5px 0 0; }
	.tabs.tabs-bottom .nav-tabs li .nav-link,
	.tabs-navigation .nav-tabs > li:last-child .nav-link { border-radius: 0 0 5px 5px; }
	.tabs-left .tab-content { border-radius: 0 5px 5px 5px; }
	.tabs-left .nav-tabs > li:first-child .nav-link { border-radius: 5px 0 0 0; }
	.tabs-left .nav-tabs > li:last-child .nav-link { border-radius: 0 0 0 5px; }
	.tabs-right .tab-content { border-radius: 5px 0 5px 5px; }
	.tabs-right .nav-tabs > li:first-child .nav-link { border-radius: 0 5px 0 0; }
	.tabs-right .nav-tabs > li:last-child .nav-link { border-radius: 0 0 5px 0; }
	.resp-tabs-list li:first-child,
	.nav-tabs.nav-justified li:first-child .nav-link,
	.nav-tabs.nav-justified li:first-child .nav-link:hover { border-radius: <?php echo porto_filter_output( $rtl ? '0 5px 0 0' : '5px 0 0 0' ); ?>; }
	.nav-tabs.nav-justified li:last-child .nav-link,
	.nav-tabs.nav-justified li:last-child .nav-link:hover { border-radius: <?php echo porto_filter_output( $rtl ? '5px 0 0 0' : '0 5px 0 0' ); ?>; }
	.resp-tabs-list li:last-child,
	.tabs.tabs-bottom .nav.nav-tabs.nav-justified li:first-child .nav-link { border-radius: <?php echo porto_filter_output( $rtl ? '0 0 5px 0' : '0 0 0 5px' ); ?>; }
	.tabs.tabs-bottom .nav.nav-tabs.nav-justified li:last-child .nav-link { border-radius: <?php echo porto_filter_output( $rtl ? '0 0 0 5px' : '0 0 5px 0' ); ?>; }
	@media (max-width: 575px) {
		.tabs .nav.nav-tabs.nav-justified li:first-child .nav-link,
		.tabs .nav.nav-tabs.nav-justified li:first-child .nav-link:hover { border-radius: 5px 5px 0 0; }
		.tabs.tabs-bottom .nav.nav-tabs.nav-justified li:last-child .nav-link,
		.tabs.tabs-bottom .nav.nav-tabs.nav-justified li:last-child .nav-link:hover { border-radius: 0 0 5px 5px; }
	}

	#mini-cart .cart-popup,
	#header .main-menu,
	.sidebar-menu .narrow ul.sub-menu,
	.btn-3d,
	.stats-block.counter-with-border,
	.gmap-rounded,
	.gmap-rounded .porto_google_map,
	blockquote.with-borders,
	.tparrows,
	.testimonial.testimonial-style-4,
	body .cart-actions .button,
	body .checkout-button,
	body #place_order,
	body .btn-lg,
	body .btn-group-lg > .btn,
	body input.submit.btn-lg,
	body input.btn.btn-lg[type="submit"], 
	body input.button.btn-lg[type="submit"],
	body .return-to-shop .button { border-radius: 6px; }
	#header .porto-view-switcher .narrow .popup > .inner > ul.sub-menu,
	#header .top-links .narrow .popup > .inner > ul.sub-menu { border-radius: 0 0 6px 6px; }
	.mobile-sidebar .sidebar-toggle { border-radius: <?php echo porto_filter_output( $rtl ? '6px 0 0 6px' : '0 6px 6px 0' ); ?>; }
	.sidebar-menu .wide .popup,
	.sidebar-menu .wide .popup > .inner,
	.sidebar-menu .narrow .popup > .inner > ul.sub-menu { border-radius: <?php echo porto_filter_output( $rtl ? '6px 0 6px 6px' : '0 6px 6px 6px' ); ?>; }
	.right-sidebar .sidebar-menu .wide .popup,
	.right-sidebar .sidebar-menu .wide .popup > .inner,
	.right-sidebar .sidebar-menu .narrow .popup > .inner > ul.sub-menu { border-radius: <?php echo porto_filter_output( $rtl ? '0 6px 6px 6px' : '6px 0 6px 6px' ); ?>; }
	<?php if ( class_exists( 'Woocommerce' ) ) : ?>
		.widget_product_categories .widget-title,
		.widget_price_filter .widget-title,
		.widget_layered_nav .widget-title,
		.widget_layered_nav_filters .widget-title,
		.widget_rating_filter .widget-title { border-radius: 6px 6px 0 0; }
		.category-image,
		.widget_product_categories.closed .widget-title,
		.widget_price_filter.closed .widget-title,
		.widget_layered_nav.closed .widget-title,
		.widget_layered_nav_filters.closed .widget-title,
		.widget_rating_filter.closed .widget-title { border-radius: 6px; }
		.shop_table.responsive.cart-total tbody tr:first-child th,
		.shop_table.shop_table_responsive.cart-total tbody tr:first-child th { border-radius: <?php echo porto_filter_output( $rtl ? '0 6px 0 0' : '6px 0 0 0' ); ?>; }
		.shop_table.responsive.cart-total tbody tr:last-child th,
		.shop_table.shop_table_responsive.cart-total tbody tr:last-child th { border-radius: <?php echo porto_filter_output( $rtl ? '0 0 6px 0' : '0 0 0 6px' ); ?>; }
	<?php endif; ?>

	.widget_sidebar_menu.closed .widget-title,
	.img-opacity-effect a img,
	#content .master-slider,
	#content-inner-top .master-slider,
	#content-inner-bottom .master-slider,
	#content .master-slider .ms-slide .ms-slide-bgcont,
	#content-inner-top .master-slider .ms-slide .ms-slide-bgcont,
	#content-inner-bottom .master-slider .ms-slide .ms-slide-bgcont,
	#content .master-slider .ms-slide .ms-slide-bgvideocont,
	#content-inner-top .master-slider .ms-slide .ms-slide-bgvideocont,
	#content-inner-bottom .master-slider .ms-slide .ms-slide-bgvideocont,
	#content .rev_slider_wrapper,
	#content-inner-top .rev_slider_wrapper,
	#content-inner-bottom .rev_slider_wrapper,
	#content .rev_slider_wrapper li.tp-revslider-slidesli,
	#content-inner-top .rev_slider_wrapper li.tp-revslider-slidesli,
	#content-inner-bottom .rev_slider_wrapper li.tp-revslider-slidesli,
	.porto-links-block { border-radius: 7px; }
	.sidebar-menu > li.menu-item:last-child:hover,
	.sidebar-menu .menu-custom-block a:last-child:hover { border-radius: 0 0 7px 7px; }
	.porto-links-block .links-title { border-radius: 7px 7px 0 0; }
	.sidebar-menu > li.menu-item:last-child.menu-item-has-children:hover { border-radius: <?php echo porto_filter_output( $rtl ? '0 0 7px 0' : '0 0 0 7px' ); ?>; }
	.right-sidebar .sidebar-menu > li.menu-item:last-child.menu-item-has-children:hover { border-radius: <?php echo porto_filter_output( $rtl ? '0 0 0 7px' : '0 0 7px 0' ); ?>; }
	.br-thick { border-radius: 7px !important; }
	<?php if ( class_exists( 'Woocommerce' ) ) : ?>
		.product-image,
		.widget_product_categories,
		.widget_price_filter,
		.widget_layered_nav,
		.widget_layered_nav_filters,
		.widget_rating_filter,
		.widget_layered_nav .yith-wcan-select-wrapper { border-radius: 7px; }
	<?php endif; ?>

	.featured-box,
	.featured-box .box-content,
	.testimonial blockquote { border-radius: 8px; }

	<?php if ( ( class_exists( 'bbPress' ) && is_bbpress() ) || ( class_exists( 'BuddyPress' ) && is_buddypress() ) ) : ?>
		#bbpress-forums #bbp-single-user-details #bbp-user-avatar img.avatar,
		#buddypress div#item-header img.avatar { border-radius: 16px; }
	<?php endif; ?>

<?php else : ?>
	<?php if ( class_exists( 'Woocommerce' ) ) : ?>
		.wishlist_table .add_to_cart.button,
		.yith-wcwl-add-to-wishlist a.add_to_wishlist,
		.yith-wcwl-popup-button a.add_to_wishlist,
		.wishlist_table a.ask-an-estimate-button,
		.wishlist-title a.show-title-form,
		.hidden-title-form a.hide-title-form,
		.woocommerce .yith-wcwl-wishlist-new button,
		.wishlist_manage_table a.create-new-wishlist,
		.wishlist_manage_table button.submit-wishlist-changes,
		.yith-wcwl-wishlist-search-form button.wishlist-search-button { border-radius: 0; }
	<?php endif; ?>
<?php endif; ?>

/*------ Thumb Padding ------- */
<?php if ( $b['thumb-padding'] ) : ?>
	.mega-menu li.menu-item > a > .thumb-info-preview .thumb-info-wrapper,
	.sidebar-menu li.menu-item > a > .thumb-info-preview .thumb-info-wrapper,
	.page-wrapper .fdm-item-image,
	.thumb-info-side-image .thumb-info-side-image-wrapper,
	.flickr_badge_image,
	.wpb_content_element .flickr_badge_image { padding: 4px; }
	.img-thumbnail .zoom { <?php echo porto_filter_output( $right ); ?>: 8px; bottom: 8px; }
	.thumb-info .thumb-info-wrapper { margin: 4px; }
	.thumb-info .thumb-info-wrapper:after { bottom: -4px; top: -4px; left: -4px; right: -4px; }

	.flickr_badge_image,
	.wpb_content_element .flickr_badge_image { border: 1px solid <?php echo esc_html( $dark ? $color_dark_3 : '#ddd' ); ?>; }

	.owl-carousel .img-thumbnail,
	.owl-carousel .owl-nav,
	.owl-carousel .thumb-info { max-width: 99.8%; }
	.thumb-info { background-color: <?php echo porto_if_light( '#fff', $color_dark_3 ); ?>; border-color: <?php echo porto_if_light( '#ddd', $color_dark_3 ); ?>; }
	.thumb-info-social-icons { border-top: <?php echo porto_if_light( '1px dotted #ddd', '1px solid ' . $porto_color_lib->lighten( $dark_bg, 12 ) ); ?>; }

	<?php if ( class_exists( 'Woocommerce' ) ) : ?>
		.yith-wcbm-badge { margin: 5px; }
		.yith-wcbm-badge img { margin: -5px !important; }
		.product-images .zoom { <?php echo porto_filter_output( $right ); ?>: 8px; bottom: 8px; }

		.product-image-slider.owl-carousel .img-thumbnail,
		.product-thumbs-slider.owl-carousel .img-thumbnail { width: 99.8%; padding: 3px; }

		.product-image { padding: 0.2381em; }

		.widget_recent_reviews .product_list_widget li img { border: 1px solid <?php echo esc_html( $dark ? $color_dark_3 : '#ddd' ); ?>; padding: 3px; }

		.product-nav .product-popup .product-image,
		ul.product_list_widget li .product-image { padding: 3px; }
	<?php endif; ?>
<?php else : ?>
	.page-wrapper .fdm-item-image,
	.thumb-info { border-width: 0; background: none; }
	.thumb-info-caption .thumb-info-caption-text { padding: 15px 0; margin-bottom: 0; }
	.thumb-info-social-icons { padding: 0; }
	.thumb-info-social-icons:first-child { padding: 10px 0; }
	.thumb-info .share-links a { background: <?php echo esc_html( $b['skin-color'] ); ?> }
	.thumb-info .share-links a:hover { opacity: .9 }
<?php endif; ?>
.post-share-advance .share-links a { background: none; }
.thumb-info .thumb-info-wrapper:after { background: <?php echo porto_if_light( 'rgba(33, 37, 41, 0.8)', 'rgba(' . $porto_color_lib->hexToRGB( $color_dark ) . ', 0.9)' ); ?>; }

/*------ Dark version ------- */
<?php if ( ( class_exists( 'bbPress' ) && is_bbpress() ) || ( class_exists( 'BuddyPress' ) && is_buddypress() ) ) : ?>
	.bbp-pagination-links a:hover,
	.bbp-pagination-links span.current:hover { background: <?php echo porto_if_dark( $color_dark_3, '#eee' ); ?>; border: 1px solid <?php echo porto_if_dark( $color_dark_4, '#ddd' ); ?>; }
	#bbpress-forums div.wp-editor-container { border: 1px solid <?php echo porto_if_dark( $color_dark_3, '#dedede' ); ?>; }
	#bbpress-forums #bbp-single-user-details #bbp-user-navigation li.current a { background: <?php echo porto_if_dark( $color_dark_3, '#eee' ); ?>; }
	#buddypress div.pagination .pagination-links a:hover,
	#buddypress div.pagination .pagination-links span.current:hover { background: <?php echo porto_if_dark( $color_dark_3, '#eee' ); ?>; border: 1px solid <?php echo porto_if_dark( $color_dark_4, '#ddd' ); ?>; }
	#buddypress form#whats-new-form textarea { background: <?php echo porto_if_dark( $color_dark_3, '#fff' ); ?>; color: <?php echo porto_if_dark( '#999', '#777' ); ?>; }
	#buddypress .activity-list li.load-more a:hover,
	#buddypress .activity-list li.load-newest a:hover { color: <?php echo porto_if_dark( '#999', '#333' ); ?>; }
	#buddypress a.bp-primary-action span,
	#buddypress #reply-title small a span { background: <?php echo porto_if_dark( '#555', '#fff' ); ?>; <?php echo porto_if_dark( '', 'color: #999;' ); ?> }
	#buddypress a.bp-primary-action:hover span,
	#buddypress #reply-title small a:hover span { background: <?php echo porto_if_dark( '#777', '#fff' ); ?>; <?php echo porto_if_dark( '', 'color: #999;' ); ?> }
	#buddypress div.activity-comments ul li { border-top: 1px solid <?php echo porto_if_dark( $color_dark_3, '#eee' ); ?>; }
	#buddypress div.activity-comments form .ac-textarea { background: <?php echo porto_if_dark( $color_dark_3, '#fff' ); ?>; color: <?php echo porto_if_dark( '#999', '#777' ); ?>; border: 1px solid <?php echo porto_if_dark( $color_dark_3, '#ccc' ); ?>; }
	#buddypress div.activity-comments form textarea { color: <?php echo porto_if_dark( '#999', '#777' ); ?>; }
	#buddypress #pass-strength-result { border-color: <?php echo porto_if_dark( $color_dark_4, '#ddd' ); ?>; }
	#buddypress .standard-form textarea,
	#buddypress .standard-form input[type=text],
	#buddypress .standard-form input[type=color],
	#buddypress .standard-form input[type=date],
	#buddypress .standard-form input[type=datetime],
	#buddypress .standard-form input[type=datetime-local],
	#buddypress .standard-form input[type=email],
	#buddypress .standard-form input[type=month],
	#buddypress .standard-form input[type=number],
	#buddypress .standard-form input[type=range],
	#buddypress .standard-form input[type=search],
	#buddypress .standard-form input[type=tel],
	#buddypress .standard-form input[type=time],
	#buddypress .standard-form input[type=url],
	#buddypress .standard-form input[type=week],
	#buddypress .standard-form select,
	#buddypress .standard-form input[type=password],
	#buddypress .dir-search input[type=search],
	#buddypress .dir-search input[type=text],
	#buddypress .groups-members-search input[type=search],
	#buddypress .groups-members-search input[type=text] {
	<?php if ( $dark ) : ?>
		border: 1px solid <?php echo esc_html( $color_dark_3 ); ?>;
		background: <?php echo esc_html( $color_dark_3 ); ?>;
		color: #999;
	<?php endif; ?>
		color: <?php echo porto_if_dark( '#999', '#777' ); ?>;
	}
	#buddypress .standard-form input:focus,
	#buddypress .standard-form textarea:focus,
	#buddypress .standard-form select:focus {
	<?php if ( $dark ) : ?>
		background: $color-dark-3;
	<?php endif; ?>
		color: <?php echo porto_if_dark( '#999', '#777' ); ?>;
	}
	#buddypress table.forum tr td.label {
	<?php if ( $dark ) : ?>
		border-<?php echo porto_filter_output( $right ); ?>-width: 0;
	<?php endif; ?>
		color: <?php echo porto_if_dark( '#fff', '#777' ); ?>;
	}
	#buddypress div.item-list-tabs ul li.selected a span,
	#buddypress div.item-list-tabs ul li.current a span {
		border-color: <?php echo porto_if_dark( $color_dark_3, '#fff' ); ?>;
	<?php if ( $dark ) : ?>
		background-color: <?php echo esc_html( $color_dark_3 ); ?>;
	<?php endif; ?>
	}
	<?php if ( $dark ) : ?>
		#buddypress div.pagination .pagination-links a,
		#buddypress div.pagination .pagination-links span.current { background: <?php echo esc_html( $color_dark_3 ); ?>; }
		#buddypress div.pagination .pagination-links a.dots,
		#buddypress div.pagination .pagination-links span.current.dots { background: transparent; }
		#buddypress .activity-list li.load-more a,
		#buddypress .activity-list li.load-newest a { color: #777; }
		#buddypress .activity-list li.new_forum_post .activity-content .activity-inner,
		#buddypress .activity-list li.new_forum_topic .activity-content .activity-inner { border-<?php echo porto_filter_output( $left ); ?>: 2px solid <?php echo esc_html( $color_dark_3 ); ?>; }
		#buddypress .activity-list .activity-content img.thumbnail { border: 2px solid <?php echo esc_html( $color_dark_3 ); ?>; }
		#buddypress .activity-list li.load-more,
		#buddypress .activity-list li.load-newest { background: <?php echo esc_html( $color_dark_3 ); ?>; }
		#buddypress div.ac-reply-avatar img { border: 1px solid <?php echo esc_html( $color_dark_3 ); ?>; }
		#buddypress #pass-strength-result { background-color: <?php echo esc_html( $color_dark_3 ); ?>; }
		#buddypress div#invite-list { background: transparent; }
		#buddypress table.notifications tr.alt td,
		#buddypress table.notifications-settings tr.alt td,
		#buddypress table.profile-settings tr.alt td,
		#buddypress table.profile-fields tr.alt td,
		#buddypress table.wp-profile-fields tr.alt td,
		#buddypress table.messages-notices tr.alt td,
		#buddypress table.forum tr.alt td { background: transparent; }
		#buddypress ul.item-list { border-top: 1px solid <?php echo esc_html( $color_dark_3 ); ?>; }
		#buddypress ul.item-list li { border-bottom: 1px solid <?php echo esc_html( $color_dark_3 ); ?> }
		#buddypress div.item-list-tabs ul li a span { background: <?php echo esc_html( $color_dark_3 ); ?>; border: 1px solid <?php echo esc_html( $color_dark_3 ); ?>; }
		#buddypress div.item-list-tabs ul li.selected a span,
		#buddypress div.item-list-tabs ul li.current a span,
		#buddypress div.item-list-tabs ul li a:hover span { background-color: <?php echo esc_html( $color_dark_3 ); ?>; border-color: <?php echo esc_html( $color_dark_3 ); ?>; }
		#buddypress div#message-thread div.alt { background: <?php echo esc_html( $color_dark_3 ); ?>; }
		.bp-avatar-nav ul.avatar-nav-items li.current { border-color: <?php echo esc_html( $color_dark_4 ); ?>; }
		.bp-avatar-nav ul { border-color: <?php echo esc_html( $color_dark_4 ); ?>; }
		#drag-drop-area { border-color: <?php echo esc_html( $color_dark_4 ); ?>; }
		#buddypress input[type="submit"].pending:hover,
		#buddypress input[type="button"].pending:hover,
		#buddypress input[type="reset"].pending:hover,
		#buddypress button.pending:hover,
		#buddypress div.pending a:hover,
		#buddypress a.disabled:hover { border-color: <?php echo esc_html( $color_dark_3 ); ?>; }
		#buddypress ul#topic-post-list li.alt { background: transparent; }
		#buddypress table.notifications thead tr,
		#buddypress table.notifications-settings thead tr,
		#buddypress table.profile-settings thead tr,
		#buddypress table.profile-fields thead tr,
		#buddypress table.wp-profile-fields thead tr,
		#buddypress table.messages-notices thead tr,
		#buddypress table.forum thead tr { background: <?php echo esc_html( $color_dark_3 ); ?>; }

		#bbpress-forums div.even, #bbpress-forums ul.even { background-color: <?php echo esc_html( $color_dark ); ?>; }
		#bbpress-forums div.odd, #bbpress-forums ul.odd { background-color: <?php echo esc_html( $color_dark_2 ); ?>; }
		#bbpress-forums div.bbp-forum-header,
		#bbpress-forums div.bbp-topic-header,
		#bbpress-forums div.bbp-reply-header { background-color: <?php echo esc_html( $color_dark_5 ); ?>; }
		#bbpress-forums .status-trash.even, #bbpress-forums .status-spam.even { background-color: <?php echo esc_html( $color_dark ); ?>; }
		#bbpress-forums .status-trash.odd, #bbpress-forums .status-spam.odd { background-color: <?php echo esc_html( $color_dark_2 ); ?>; }
		#bbpress-forums ul.bbp-lead-topic,
		#bbpress-forums ul.bbp-topics,
		#bbpress-forums ul.bbp-forums,
		#bbpress-forums ul.bbp-replies,
		#bbpress-forums ul.bbp-search-results { border: 1px solid <?php echo esc_html( $color_dark_3 ); ?>; }
		#bbpress-forums li.bbp-header,
		#bbpress-forums li.bbp-footer { background: <?php echo esc_html( $color_dark_3 ); ?>; border-top: <?php echo esc_html( $color_dark_3 ); ?>; }
		#bbpress-forums li.bbp-header { background: <?php echo esc_html( $color_dark_4 ); ?>; }
		#bbpress-forums .bbp-forums-list { border-<?php echo porto_filter_output( $left ); ?>: 1px solid <?php echo esc_html( $color_dark_4 ); ?>; }
		#bbpress-forums li.bbp-body ul.forum,
		#bbpress-forums li.bbp-body ul.topic { border-top: 1px solid <?php echo esc_html( $color_dark_3 ); ?>; }
		div.bbp-forum-header,
		div.bbp-topic-header,
		div.bbp-reply-header { border-top: 1px solid <?php echo esc_html( $color_dark_4 ); ?>; }
		#bbpress-forums div.bbp-topic-content code,
		#bbpress-forums div.bbp-topic-content pre,
		#bbpress-forums div.bbp-reply-content code,
		#bbpress-forums div.bbp-reply-content pre  { background-color: <?php echo esc_html( $color_dark_4 ); ?>; border: 1px solid <?php echo esc_html( $color_dark_4 ); ?>; }
		.bbp-pagination-links a,
		.bbp-pagination-links span.current,
		.bbp-topic-pagination a { background: <?php echo esc_html( $color_dark_3 ); ?>; }
		.bbp-pagination-links a.dots,
		.bbp-pagination-links span.current.dots,
		.bbp-topic-pagination a.dots { background: transparent; }
		#bbpress-forums fieldset.bbp-form { border: 1px solid <?php echo esc_html( $color_dark_3 ); ?>; }
		body.topic-edit .bbp-topic-form div.avatar img,
		body.reply-edit .bbp-reply-form div.avatar img,
		body.single-forum .bbp-topic-form div.avatar img,
		body.single-reply .bbp-reply-form div.avatar img { border: 1px solid <?php echo esc_html( $color_dark_4 ); ?>; background-color: <?php echo esc_html( $color_dark_4 ); ?>; }
		#bbpress-forums  div.bbp-the-content-wrapper div.quicktags-toolbar { background: <?php echo esc_html( $color_dark_4 ); ?>; border-bottom-color: <?php echo esc_html( $color_dark_3 ); ?>; }
		#bbpress-forums #bbp-your-profile fieldset input,
		#bbpress-forums #bbp-your-profile fieldset textarea { background: <?php echo esc_html( $color_dark_3 ); ?>; border: 1px solid <?php echo esc_html( $color_dark_3 ); ?>; }
		#bbpress-forums #bbp-your-profile fieldset input:focus,
		#bbpress-forums #bbp-your-profile fieldset textarea:focus { border: 1px solid <?php echo esc_html( $color_dark_3 ); ?>; }
		#bbpress-forums #bbp-your-profile fieldset span.description { border: transparent 1px solid; background-color: transparent; }
		.bbp-topics-front ul.super-sticky,
		.bbp-topics ul.super-sticky,
		.bbp-topics ul.sticky,
		.bbp-forum-content ul.sticky { background-color: <?php echo esc_html( $color_dark_3 ); ?> !important; }
		#bbpress-forums .bbp-topic-content ul.bbp-topic-revision-log,
		#bbpress-forums .bbp-reply-content ul.bbp-topic-revision-log,
		#bbpress-forums .bbp-reply-content ul.bbp-reply-revision-log { border-top: 1px dotted <?php echo esc_html( $color_dark_4 ); ?>; }
		.activity-list li.bbp_topic_create .activity-content .activity-inner,
		.activity-list li.bbp_reply_create .activity-content .activity-inner { border-<?php echo porto_filter_output( $left ); ?>: 2px solid <?php echo esc_html( $color_dark_4 ); ?>; }
	<?php endif; ?>
<?php endif; ?>
<?php if ( $dark ) : ?>
	.pagination > a,
	.pagination > span,
	.page-links > a,
	.page-links > span,
	.pricing-table.pricing-table-classic .plan-price { background: <?php echo esc_html( $color_dark_3 ); ?>; }
	.pagination > a.dots,
	.pagination > span.dots,
	.page-links > a.dots,
	.page-links > span.dots { background: transparent; }
	.dir-arrow { background: transparent url(<?php echo PORTO_URI; ?>/images/arrows-dark.png) no-repeat 0 0; }
	.dir-arrow.arrow-light { background: transparent url(<?php echo PORTO_URI; ?>/images/arrows.png) no-repeat 0 0; }
	hr, .divider { background: rgba(255, 255, 255, 0.06); }
	hr.light { background: rgba(0, 0, 0, 0.06); }
	.featured-boxes-style-6 .featured-box .icon-featured { background: <?php echo esc_html( $color_dark_3 ); ?>; }
	.featured-boxes-style-7 .featured-box .icon-featured { background: <?php echo esc_html( $color_dark_3 ); ?>; }
	.featured-boxes-style-7 .featured-box .icon-featured:after { box-shadow: 3px 3px <?php echo esc_html( $porto_color_lib->darken( $color_dark, 3 ) ); ?>; }
	.porto-concept { background-image: url(<?php echo PORTO_URI; ?>/images/concept-dark.png); }
	.porto-concept .process-image { background-image: url(<?php echo PORTO_URI; ?>/images/concept-item-dark.png); }
	.porto-concept .project-image { background-image: url(<?php echo PORTO_URI; ?>/images/concept-item-dark.png); }
	.porto-concept .sun { background-image: url(<?php echo PORTO_URI; ?>/images/concept-icons-dark.png); }
	.porto-concept .cloud { background-image: url(<?php echo PORTO_URI; ?>/images/concept-icons-dark.png); }
	.porto-map-section { background-image: url(<?php echo PORTO_URI; ?>/images/map-dark.png); }
	.slider-title .line, .section-title .line { background-image:linear-gradient(to <?php echo porto_filter_output( $right ); ?>, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0.15) 70%, rgba(255, 255, 255, 0) 100%); }
	.porto-radio .porto-control-label::after { border-width: 0; }
	@media (max-width: 767px) {
		.resp-tab-content:last-child,
		.resp-vtabs .resp-tab-content:last-child { border-bottom: 1px solid <?php echo esc_html( $color_dark_4 ); ?> !important; }
	}
	.resp-easy-accordion h2.resp-tab-active { background: <?php echo esc_html( $color_dark_4 ); ?> !important; }
	.vc_separator .vc_sep_holder.vc_sep_holder_l .vc_sep_line,
	.vc_separator .vc_sep_holder.vc_sep_holder_r .vc_sep_line { background: rgba(255, 255, 255, 0.06); }
	.card > .card-header { background-color: <?php echo esc_html( $color_dark_4 ); ?>; }
	.btn-default { background-color: <?php echo esc_html( $color_dark_2 ); ?> !important; border-color: <?php echo esc_html( $color_dark_2 ); ?> !important; }
	.porto-history .thumb { background: transparent url(<?php echo PORTO_URI; ?>/images/history-thumb-dark.png) no-repeat 0 <?php echo porto_filter_output( $rtl ? '-200px' : '0' ); ?>; }
	.accordion.panel-modern .card,
	.woocommerce-page.archive .sidebar-content,
	.woocommerce-page.archive .sidebar-content aside.widget,
	.woocommerce-pagination ul li a, .woocommerce-pagination ul li span { border-color: <?php echo esc_html( $color_dark_2 ); ?> }
	.accordion.panel-modern .card-header,
	.comment-form,
	.widget_sidebar_menu { background-color: <?php echo esc_html( $color_dark_2 ); ?> }
	select { background-image: url("<?php echo PORTO_URI; ?>/images/select-bg-light.svg"); }
	.comment-form label,
	.share-links-block .share-links a:not(:hover),
	.owl-carousel.nav-style-4 .owl-nav [class*="owl-"] { color: <?php echo esc_html( $b['light-color'] ); ?>; }
<?php else : ?>
	.dir-arrow { background: transparent url(<?php echo PORTO_URI; ?>/images/arrows.png) no-repeat 0 0; }
	.dir-arrow.arrow-light { background: transparent url(<?php echo PORTO_URI; ?>/images/arrows-dark.png) no-repeat 0 0; }
	hr, .divider,
	.slider-title .line,
	.section-title .line,
	.vc_separator .vc_sep_holder.vc_sep_holder_l .vc_sep_line,
	.vc_separator .vc_sep_holder.vc_sep_holder_r .vc_sep_line { background: rgba(0, 0, 0, 0.08); }
	hr.light { background: rgba(255, 255, 255, 0.06); }
	.porto-history .thumb { background: transparent url(<?php echo PORTO_URI; ?>/images/history-thumb.png) no-repeat 0 <?php echo porto_filter_output( $rtl ? '-200px' : '0' ); ?>; }
<?php endif; ?>
<?php if ( class_exists( 'Woocommerce' ) ) : ?>
	<?php if ( $dark ) : ?>
		.add-links .add_to_cart_button.loading.viewcart-style-1:after,
		.add-links .quickview.loading:after,
		.wcml-switcher li.loading,
		ul.product_list_widget li .ajax-loading,
		.variations .reset_variations { background-color: <?php echo esc_html( $color_dark_3 ); ?>; }
		.select2-drop, .select2-drop-active,
		.woocommerce-pagination ul li a,
		.woocommerce-pagination ul li span { background: <?php echo esc_html( $color_dark_3 ); ?>; }
		.select2-drop input, .select2-drop-active input,
		.select2-drop .select2-results .select2-highlighted,
		.select2-drop-active .select2-results .select2-highlighted { background: <?php echo esc_html( $color_dark_2 ); ?>; }
		.woocommerce-pagination ul li a.dots,
		.woocommerce-pagination ul li span.dots { background: transparent; }
		.woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li a,
		.woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li a:hover,
		.woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li span,
		.woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li span:hover,
		.woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li a,
		.woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li a:hover,
		.woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li span,
		.woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li span:hover,
		.woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen a,
		.woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen a:hover,
		.woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen span,
		.woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen span:hover,
		.woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen a,
		.woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen a:hover,
		.woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen span,
		.woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen span:hover { border-color: #ccc; }
		.porto-products.title-border-bottom > .section-title,
		.related.products .slider-title,
		.porto-products.title-border-middle > .section-title .inline-title:before { border-bottom: 1px solid rgba(255, 255, 255, .06); }
		.product-summary-wrap .product_meta,
		.single-product .entry-summary .quantity .minus,
		.single-product .entry-summary .quantity .plus,
		.single-product .entry-summary .quantity .qty,
		.woocommerce-tabs .tab-content,
		.cart-v2 .card.card-default .card-body tbody tr { border-color: <?php echo esc_html( $color_dark_4 ); ?>; background: none; }
		.woocommerce-ordering select,
		.woocommerce-viewing select,
		.shop-loop-after,
		.woocommerce-widget-layered-nav-list a:not(.filter-color),
		.filter-item-list .filter-item,
		.cart-v2 .card.card-default,
		.woocommerce-account .featured-boxes, .woocommerce-thankyou .featured-boxes, .woocommerce-checkout .featured-boxes, #login-form-popup .featured-boxes { border-color: <?php echo esc_html( $color_dark_3 ); ?>; }
		.single-product .entry-summary .add_to_wishlist:before,
		.single-product .entry-summary .yith-wcwl-wishlistexistsbrowse a:before,
		.single-product .entry-summary .yith-wcwl-wishlistaddedbrowse a:before,
		.single_variation_wrap, .single-product .cart:not(.variations_form) { border-color: <?php echo esc_html( $color_dark_4 ); ?> !important; }
		.single_variation_wrap, .single-product .cart:not(.variations_form),
		.single-product .product-summary-wrap .quantity .minus, .single-product .product-summary-wrap .quantity .plus, .shop_table.cart-table .quantity .minus, .shop_table.cart-table .quantity .plus,
		.single-product .product-summary-wrap .quantity .qty, .shop_table.cart-table .quantity .qty { border-color: <?php echo esc_html( $color_dark_4 ); ?> }
		.quantity .qty,
		.gridlist-toggle > a:not(.active),
		.gridlist-toggle > a:hover,
		.single-product .product_title,
		.single-product .entry-summary .quantity .minus, .single-product .entry-summary .quantity .plus,
		.shop_table.cart-table .quantity .plus, .shop_table.cart-table .quantity .minus,
		.woocommerce-tabs h2.resp-accordion,
		.cart-v2 .card.card-default .card-body tbody tr td:last-child,
		.cart_totals .order-total .amount, .review-order.shop_table .order-total .amount { color: <?php echo esc_html( $b['light-color'] ); ?>; }
		.woocommerce-tabs .resp-tabs-list li.resp-tab-active { color: #fff !important }
		#mini-cart .total-count,
		#mini-cart .cart-icon:after { border-color: <?php echo esc_html( $color_dark_4 ); ?>; }
		#mini-cart li:not(.empty) { border-bottom-color: <?php echo esc_html( $color_dark_4 ); ?> !important; }
		#mini-cart .total-count a,
		.product-nav a,
		.product-nav a:hover { color: <?php echo esc_html( $b['body-font']['color'] ); ?>; }
		.cart-v2 .card.card-default .card-body, .card { background: <?php echo esc_html( $color_dark_5 ); ?> }
		.cart-v2 .card.card-default { border-radius: 0 0 6.99px 6.99px }
		.blockUI { background: <?php echo esc_html( $color_dark_2 ); ?> !important }
		.woocommerce-message, .woocommerce-error { background-color: transparent; }
		@media (max-width: 991px) {
			.shop-loop-before { background: <?php echo esc_html( $color_dark_2 ); ?> }
			a.porto-product-filters-toggle { background: <?php echo esc_html( $color_dark_3 ); ?>; border: none; }
			.shop-loop-before.sticky { border-bottom-color: rgba(255, 255, 255, .04) }
		}
	<?php else : ?>
		.add-links .add_to_cart_button.loading.viewcart-style-1:after,
		.add-links .quickview.loading:after,
		.wcml-switcher li.loading,
		ul.product_list_widget li .ajax-loading { background-color: #fff; }
		.porto-products.title-border-bottom > .section-title,
		.related.products .slider-title,
		.porto-products.title-border-middle > .section-title .inline-title:before { border-bottom: 1px solid rgba(0, 0, 0, .08); }
	<?php endif; ?>
<?php endif; ?>

#header.sticky-header .header-main.sticky,
#header.sticky-header .main-menu-wrap,
.fixed-header #header.sticky-header .main-menu-wrap { box-shadow: 0 1px 0 0 <?php echo porto_if_dark( 'rgba(255, 255, 255, 0.06)', 'rgba(0, 0, 0, 0.06)' ); ?>; }
#mini-cart .widget_shopping_cart_content { background: <?php echo porto_if_dark( $color_dark_3, '#fff' ); ?>; }
<?php if ( $dark ) : ?>
	#mini-cart .cart-icon:after { background-color: <?php echo esc_html( $color_dark_3 ); ?> }
<?php endif; ?>
.mega-menu li.menu-item > a > .thumb-info-preview .thumb-info-wrapper,
.sidebar-menu li.menu-item > a > .thumb-info-preview .thumb-info-wrapper { background: <?php echo porto_if_dark( $color_dark_4, '#fff' ); ?>; }
.mega-menu .wide .popup > .inner,
.sidebar-menu .wide .popup > .inner { background: <?php echo porto_if_dark( $color_dark_3, '#fff' ); ?>; }
.mega-menu .wide li.sub > a,
.sidebar-menu .wide li.sub > a { color: <?php echo porto_if_dark( '#fff', '#333' ); ?>; }
.mega-menu .wide li.menu-item li.menu-item > a:hover { background: <?php echo porto_if_dark( $porto_color_lib->lighten( $color_dark_3, 5 ), '#f4f4f4' ); ?>; }
@media (max-width: 991px) {
	.mobile-sidebar,
	.mobile-sidebar .sidebar-toggle { background: <?php echo porto_if_dark( $dark_bg, '#fff' ); ?>; }
	.mobile-sidebar .sidebar-content::-webkit-scrollbar { width: 5px; }
	.mobile-sidebar .sidebar-content::-webkit-scrollbar-thumb { border-radius: 0px; background: <?php echo porto_if_light( 'rgba(204, 204, 204, 0.5)', '#39404c' ); ?>;<?php echo porto_if_dark( 'border-color: transparent', '' ); ?> }
}
.widget_sidebar_menu .widget-title .toggle { color: <?php echo porto_if_dark( '#999', '#ccc' ); ?>; background: <?php echo porto_if_dark( $color_dark, '#fff' ); ?>; border: 1px solid <?php echo esc_html( $dark ? $color_dark_3 : '#ccc' ); ?>; }
.sidebar-menu > li.menu-item > a,
.sidebar-menu .menu-custom-block a { border-top: 1px solid <?php echo porto_if_dark( $porto_color_lib->lighten( $dark_bg, 12 ), '#ddd' ); ?>; }
.blog-posts article,
.member-row-advanced .member:not(:last-child) { border-bottom: 1px solid <?php echo porto_if_dark( 'rgba(255, 255, 255, 0.06)', 'rgba(0, 0, 0, 0.06)' ); ?>; }
.blog-posts-padding .grid-box { border: 1px solid <?php echo porto_if_dark( $color_dark_3, '#e5e5e5' ); ?>; background: <?php echo porto_if_dark( $color_dark_3, '#fff' ); ?>; }
article.post .post-date .day,
.post-item .post-date .day,
ul.comments .comment-block { background: <?php echo porto_if_dark( $color_dark_3, '#f4f4f4' ); ?>; }
.post-item-small { border-top: 1px dotted <?php echo porto_if_dark( $color_dark_3, '#ececec' ); ?>; }
article.portfolio .comment-respond { border-top: 1px solid <?php echo porto_if_dark( $color_dark_3, 'rgba(0, 0, 0, 0.06)' ); ?>; }
ul.comments .comment-arrow { border-<?php echo porto_filter_output( $right ); ?>: 15px solid <?php echo porto_if_dark( $color_dark_3, '#f4f4f4' ); ?>; }
@media (max-width: 767px) {
	ul.comments li { border-<?php echo porto_filter_output( $left ); ?>: 8px solid <?php echo porto_if_dark( $color_dark_3, '#ddd' ); ?>; padding-<?php echo porto_filter_output( $left ); ?>: 10px; }
}
.vc_progress_bar .vc_single_bar.progress,
.progress { background: <?php echo porto_if_dark( $color_dark_4, '#fafafa' ); ?>; }
.section-dark .vc_progress_bar .vc_single_bar.progress { background: <?php echo esc_html( $color_dark_4 ); ?>; }
.btn-default { color: <?php echo porto_if_dark( '#777', '#666' ); ?>; }
[type="submit"].btn-default { color: <?php echo porto_if_dark( '#666', '#333' ); ?>; }
.btn-default.btn:hover { color: <?php echo porto_if_dark( '#777', '#333' ); ?>; }
.owl-carousel.top-border { border-top: 1px solid <?php echo porto_if_dark( '#3F4247', '#dbdbdb' ); ?>; }
.slick-slider .slick-dots li i { color: <?php echo porto_if_dark( $color_dark_4 . '!important', '#d6d6d6' ); ?>; }
.porto-ajax-loading:after { background-color: <?php echo porto_if_dark( $dark_bg, '#fff' ); ?>; }
hr.solid,
.divider.divider-solid,
.vc_separator .vc_sep_holder.vc_sep_holder_l .vc_sep_line.solid,
.vc_separator .vc_sep_holder.vc_sep_holder_r .vc_sep_line.solid { background: <?php echo porto_if_dark( 'rgba(255, 255, 255, 0.06)', 'rgba(0, 0, 0, 0.06)' ); ?>; }
.divider i { background: <?php echo porto_if_dark( $color_dark, '#fff' ); ?>; }
.divider.divider-style-2 i { background: <?php echo porto_if_dark( $color_dark_2, '#f4f4f4' ); ?>; }
.divider.divider-style-3 i,
.divider.divider-style-4 i { border: 1px solid <?php echo porto_if_dark( '#3f4247', '#cecece' ); ?>; }
.divider.divider-style-4 i:after { border: 3px solid <?php echo porto_if_dark( $color_dark_2, '#f4f4f4' ); ?>; }
.divider.divider-small hr { background: <?php echo porto_if_dark( '#3f4247', '#555' ); ?>; }
.divider.divider-small.divider-light hr { background: <?php echo porto_if_dark( '#3f4247', '#ddd' ); ?>; }
hr.dashed:after,
.divider.dashed:after,
.vc_separator .vc_sep_holder.vc_sep_holder_l .vc_sep_line.dashed:after,
.vc_separator .vc_sep_holder.vc_sep_holder_r .vc_sep_line.dashed:after { border: 1px dashed <?php echo porto_if_dark( 'rgba(255, 255, 255, 0.06)', 'rgba(0, 0, 0, 0.06)' ); ?>; }
.stats-block.counter-with-border,
blockquote.with-borders,
.vc_general.vc_cta3.vc_cta3-style-custom { border-top: 1px solid <?php echo porto_if_dark( $color_dark_4, '#dfdfdf' ); ?>; border-bottom: 1px solid <?php echo porto_if_dark( $color_dark_4, '#dfdfdf' ); ?>; border-left: 1px solid <?php echo porto_if_dark( $color_dark_3, '#ececec' ); ?>; border-right: 1px solid <?php echo porto_if_dark( $color_dark_3, '#ececec' ); ?>; }
.featured-box { background: <?php echo porto_if_dark( $color_dark_4, '#fff' ); ?>; border-bottom: 1px solid <?php echo porto_if_dark( $color_dark_4, '#dfdfdf' ); ?>; border-left: 1px solid <?php echo porto_if_dark( $color_dark_4, '#ececec' ); ?>; border-right: 1px solid <?php echo porto_if_dark( $color_dark_4, '#ececec' ); ?>; }
.resp-tab-content { border: 1px solid <?php echo esc_html( $dark ? $color_dark_4 : '#eee' ); ?>; }
.featured-boxes-style-6 .featured-box .icon-featured,
.feature-box.feature-box-style-6 .feature-box-icon,
.porto-sicon-box.featured-icon .porto-icon { border: 1px solid <?php echo esc_html( $dark ? $color_dark_4 : '#cecece' ); ?>; }
.featured-boxes-style-6 .featured-box .icon-featured:after { border: 5px solid <?php echo esc_html( $dark ? $color_dark_4 : '#f4f4f4' ); ?>; }
.featured-boxes-flat .featured-box .box-content,
.featured-boxes-style-8 .featured-box .icon-featured { background: <?php echo porto_if_dark( $color_dark_4, '#fff' ); ?>; }
.featured-boxes-style-3 .featured-box .icon-featured,
body #wp-link-wrap { background: <?php echo porto_if_dark( $color_dark, '#fff' ); ?>; }
.featured-boxes-style-5 .featured-box .box-content h4,
.featured-boxes-style-6 .featured-box .box-content h4,
.featured-boxes-style-7 .featured-box .box-content h4 { color: <?php echo porto_if_dark( '#fff', $color_dark_4 ); ?>; }
.featured-boxes-style-5 .featured-box .icon-featured,
.featured-boxes-style-6 .featured-box .icon-featured,
.featured-boxes-style-7 .featured-box .icon-featured { background: <?php echo porto_if_dark( $color_dark_3, '#fff' ); ?>; border: 1px solid <?php echo porto_if_dark( $color_dark_4, '#dfdfdf' ); ?>; }
.featured-box-effect-1 .icon-featured:after { box-shadow: 0 0 0 3px <?php echo porto_if_dark( $color_dark_4, '#fff' ); ?>; }
.feature-box.feature-box-style-2 h4,
.feature-box.feature-box-style-3 h4,
.feature-box.feature-box-style-4 h4,
.widget.twitter-tweets .fa-twitter { color: <?php echo porto_if_dark( '#fff', $color_dark ); ?>; }
.feature-box.feature-box-style-6 .feature-box-icon:after,
.porto-sicon-box.featured-icon .porto-icon:after { border: 3px solid <?php echo esc_html( $dark ? $color_dark_4 : '#f4f4f4' ); ?>; }
<?php echo porto_filter_output( $input_lists ); ?>,
textarea,
.form-control,
select { background-color: <?php echo porto_if_dark( $color_dark_3, '#fff' ); ?>; color: <?php echo porto_if_dark( '#999', '#777' ); ?>; border-color: <?php echo esc_html( $input_border_color ); ?>; }
.form-control:focus,
.btn-default.btn { border-color: <?php echo esc_html( $input_border_color ); ?>; }
<?php if ( ! $dark ) : ?>
	.btn-default.btn { border-bottom-color: rgba(0, 0, 0, .2) }
<?php endif; ?>
body #wp-link-wrap #link-modal-title { background: <?php echo porto_if_dark( $color_dark_4, '#fcfcfc' ); ?>; border-bottom: 1px solid <?php echo porto_if_dark( $color_dark_4, '#dfdfdf' ); ?>; }
body #wp-link-wrap .submitbox { background: <?php echo porto_if_dark( $color_dark_4, '#fcfcfc' ); ?>; border-top: 1px solid <?php echo porto_if_dark( $color_dark_4, '#dfdfdf' ); ?>; }
.heading.heading-bottom-border h1 { border-bottom: 5px solid; padding-bottom: 10px; }
.heading.heading-bottom-border h2,
.heading.heading-bottom-border h3 { border-bottom: 2px solid; padding-bottom: 10px; }
.heading.heading-bottom-border h4,
.heading.heading-bottom-border h5,
.heading.heading-bottom-border h6 { border-bottom: 1px solid; padding-bottom: 5px; }
.heading.heading-bottom-double-border .heading-tag { border-bottom: 3px double; padding-bottom: 10px; }
.heading.heading-bottom-double-border h4,
.heading.heading-bottom-double-border h5,
.heading.heading-bottom-double-border h6 { padding-bottom: 5px; }
.heading.heading-border .heading-tag { border-top-color: <?php echo porto_if_dark( '#3f4247', '#dbdbdb' ); ?>; border-bottom-color: <?php echo porto_if_dark( '#3f4247', '#dbdbdb' ); ?>; }
.dialog { background: <?php echo porto_if_dark( $color_dark, '#fff' ); ?>; }
.popup-inline-content,
.mfp-content .ajax-container,
.loading-overlay { background: <?php echo porto_if_dark( $dark_bg, '#fff' ); ?>; }
.fontawesome-icon-list > div,
.sample-icon-list > div { color: <?php echo porto_if_dark( '#ddd', '#222' ); ?>; }
.content-grid .content-grid-item:before { border-left: 1px solid <?php echo porto_if_dark( $color_dark_4, '#dadada' ); ?>; }
.content-grid .content-grid-item:after { border-bottom: 1px solid <?php echo porto_if_dark( $color_dark_4, '#dadada' ); ?>; }
.content-grid.content-grid-dashed .content-grid-item:before { border-left: 1px dashed <?php echo porto_if_dark( $color_dark_4, '#dadada' ); ?>; }
.content-grid.content-grid-dashed .content-grid-item:after { border-bottom: 1px dashed <?php echo porto_if_dark( $color_dark_4, '#dadada' ); ?>; }
ul.nav-list li a, ul[class^="wsp-"] li a { border-bottom: 1px solid <?php echo porto_if_dark( $color_dark_3, 'rgba(0, 0, 0, 0.06)' ); ?>; }
ul.nav-list.show-bg-active .active > a,
ul.nav-list.show-bg-active a.active,
ul[class^="wsp-"].show-bg-active .active > a,
ul[class^="wsp-"].show-bg-active a.active { background-color: <?php echo porto_if_light( '#f5f5f5', $color_dark_4 ); ?>; }
ul.nav-list.show-bg-active .active > a:hover,
ul.nav-list.show-bg-active a.active:hover,
ul[class^="wsp-"].show-bg-active .active > a:hover,
ul[class^="wsp-"].show-bg-active a.active:hover { background-color: <?php echo porto_if_light( '#eee', $color_dark_3 ); ?>; }
.page-wrapper .fdm-item-image { background-color: <?php echo porto_if_light( '#fff', $color_dark_3 ); ?>; border: 1px solid <?php echo porto_if_light( '#ddd', $color_dark_3 ); ?>; padding: 0; }
.pricing-table li { border-top: 1px solid <?php echo porto_if_light( '#ddd', $color_dark_2 ); ?>; }
.pricing-table h3 { background-color: <?php echo porto_if_light( '#eee', $color_dark_2 ); ?>; }
.pricing-table .plan-price { background: <?php echo porto_if_light( '#fff', $color_dark_3 ); ?>; border: 5px solid <?php echo porto_if_light( '#fff', $color_dark_5 ); ?>; box-shadow: 0 5px 20px <?php echo porto_if_light( '#ddd', $color_dark_5 ); ?> inset, 0 3px 0 <?php echo porto_if_light( '#999', $color_dark_3 ); ?> inset; }
.pricing-table .most-popular { border: 3px solid <?php echo porto_if_light( '#ccc', $color_dark_3 ); ?>; }
.pricing-table .most-popular h3 { background-color: <?php echo porto_if_light( '#666', $color_dark_3 ); ?>; text-shadow: <?php echo porto_if_light( '0 1px #555', 'none' ); ?>; }
.pricing-table .plan-ribbon { background-color: <?php echo porto_if_light( '#bfdc7a', $color_dark_3 ); ?>; }
.pricing-table .plan { background: <?php echo porto_if_light( '#fff', $color_dark_3 ); ?>; border: 1px solid <?php echo porto_if_light( '#ddd', $color_dark_3 ); ?>; }
.pricing-table-sm .plan-price { border: 3px solid <?php echo porto_if_light( '#fff', $color_dark_5 ); ?>; box-shadow: 0 5px 20px <?php echo porto_if_light( '#ddd', $color_dark_5 ); ?> inset, 0 3px 0 <?php echo porto_if_light( '#999', $color_dark_3 ); ?> inset; }
.pricing-table-flat .plan-btn-bottom li:last-child { border-bottom: 1px solid <?php echo porto_if_light( '#ddd', $color_dark_2 ); ?>; }
.section { background-color: <?php echo porto_if_light( '#f4f4f4', $color_dark_2 ); ?>; border-top: 5px solid <?php echo porto_if_light( '#f1f1f1', $color_dark_3 ); ?>; }
.porto-map-section .map-content { background-color: <?php echo porto_if_light( 'rgba(247, 247, 247, 0.5)', 'rgba(33, 38, 45, 0.5)' ); ?>; }
@media (max-width: 767px) {
	.resp-tab-content,
	.resp-vtabs .resp-tab-content { border-color: <?php echo porto_if_light( '#ddd', $color_dark_4 ); ?>; }
}
.resp-tabs-list { border-bottom: 1px solid <?php echo porto_if_light( '#eee', $color_dark_4 ); ?>; }
.resp-tabs-list li,
.resp-tabs-list li:hover,
.nav-tabs li .nav-link,
.nav-tabs li .nav-link:hover,
.nav-tabs li .nav-link:focus { background: <?php echo porto_if_light( '#f4f4f4', $color_dark_3 ); ?>; border-left: 1px solid <?php echo porto_if_light( '#eee', $color_dark_3 ); ?>; border-right: 1px solid <?php echo porto_if_light( '#eee', $color_dark_3 ); ?>; border-top: 3px solid <?php echo porto_if_light( '#eee', $color_dark_3 ); ?>; }
.resp-tabs-list li.resp-tab-active { background: <?php echo porto_if_light( '#fff', $color_dark_4 ); ?>; border-left: 1px solid <?php echo porto_if_light( '#eee', $color_dark_4 ); ?>; border-right: 1px solid <?php echo porto_if_light( '#eee', $color_dark_4 ); ?>; }
.resp-vtabs .resp-tabs-container { border: 1px solid <?php echo porto_if_light( '#eee', $color_dark_4 ); ?>; background: <?php echo porto_if_light( '#fff', $color_dark_4 ); ?>; }
.resp-vtabs .resp-tabs-list li:first-child { border-top: 1px solid <?php echo porto_if_light( '#eee', $color_dark_4 ); ?> !important; }
.resp-vtabs .resp-tabs-list li:last-child { border-bottom: 1px solid <?php echo porto_if_light( '#eee', $color_dark_4 ); ?> !important; }
.resp-vtabs .resp-tabs-list li,
.resp-vtabs .resp-tabs-list li:hover { border-<?php echo porto_filter_output( $left ); ?>: 3px solid <?php echo porto_if_light( '#eee', $color_dark_4 ); ?>; }
.resp-vtabs .resp-tabs-list li.resp-tab-active { background: <?php echo porto_if_light( '#fff', $color_dark_4 ); ?>; }
h2.resp-accordion { background: <?php echo porto_if_light( '#f5f5f5', $color_dark_3 ); ?> !important; border-color: <?php echo porto_if_light( '#ddd', $color_dark_4 ); ?>; }
h2.resp-accordion:first-child { border-top-color: <?php echo porto_if_light( '#ddd', $color_dark_4 ); ?> !important; }
h2.resp-tab-active { background: <?php echo porto_if_light( '#f5f5f5', $color_dark_3 ); ?> !important; border-bottom: 1px solid <?php echo porto_if_light( '#ddd', $color_dark_3 ); ?> !important; }
.resp-easy-accordion .resp-tab-content { border-color: <?php echo porto_if_light( '#ddd', $color_dark_4 ); ?>; background: <?php echo porto_if_light( '#fff', $color_dark_4 ); ?>; }
.resp-easy-accordion .resp-tab-content:last-child { border-color: <?php echo porto_if_light( '#ddd', $color_dark_3 ); ?> !important; }
.nav-tabs { border-bottom-color: <?php echo porto_if_light( '#eee', $color_dark_3 ); ?>; }
.nav-tabs li .nav-link:hover { border-top-color: <?php echo porto_if_light( '#ccc', '#808697' ); ?>; }
.nav-tabs li.active .nav-link { background: <?php echo porto_if_light( '#fff', $color_dark_4 ); ?>; border-left-color: <?php echo porto_if_light( '#eee', $color_dark_4 ); ?>; border-right-color: <?php echo porto_if_light( '#eee', $color_dark_4 ); ?>; border-top: 3px solid <?php echo porto_if_light( '#ccc', '#808697' ); ?>; }
.tab-content { background: <?php echo porto_if_light( '#fff', $color_dark_4 ); ?>; border-color: <?php echo porto_if_light( '#eee', $color_dark_4 ); ?>; }
.tabs.tabs-bottom .tab-content,
.tabs.tabs-bottom .nav-tabs { border-bottom: none; border-top: 1px solid <?php echo porto_if_light( '#eee', $color_dark_4 ); ?>; }
.tabs.tabs-bottom .nav-tabs li .nav-link { border-bottom-color: <?php echo porto_if_light( '#eee', $color_dark_3 ); ?>; border-top: 1px solid <?php echo porto_if_light( '#eee', $color_dark_4 ); ?> !important; }
.tabs-vertical { border-top-color: <?php echo porto_if_light( '#eee', $color_dark_4 ); ?>; }
.tabs-left .nav-tabs > li:last-child .nav-link,
.tabs-right .nav-tabs > li:last-child .nav-link,
.nav-tabs.nav-justified li .nav-link,
.nav-tabs.nav-justified li .nav-link:hover { border-bottom: 1px solid <?php echo porto_if_light( '#eee', $color_dark_3 ); ?>; }
.tabs-left .nav-tabs > li .nav-link { border-<?php echo porto_filter_output( $right ); ?>: 1px solid <?php echo porto_if_light( '#eee', $color_dark_3 ); ?>; border-<?php echo porto_filter_output( $left ); ?>: 3px solid <?php echo porto_if_light( '#eee', $color_dark_3 ); ?>; }
.tabs-left .nav-tabs > li.active .nav-link { border-<?php echo porto_filter_output( $right ); ?>-color: transparent; }
.tabs-right .nav-tabs > li .nav-link { border-<?php echo porto_filter_output( $right ); ?>: 3px solid <?php echo porto_if_light( '#eee', $color_dark_3 ); ?>; border-<?php echo porto_filter_output( $left ); ?>: 1px solid <?php echo porto_if_light( '#eee', $color_dark_3 ); ?>; }
.tabs-right .nav-tabs > li.active .nav-link { border-<?php echo porto_filter_output( $left ); ?>-color: transparent; }
.nav-tabs.nav-justified li.active .nav-link { border-bottom: 1px solid transparent; }
.tabs-navigation .nav-tabs > li:first-child .nav-link { border-top: 1px solid <?php echo porto_if_light( '#eee', $color_dark_4 ); ?> !important; }
.tabs-navigation .nav-tabs > li.active .nav-link { border-left-color: <?php echo porto_if_light( '#eee', $color_dark_4 ); ?>; border-right-color: <?php echo porto_if_light( '#eee', $color_dark_4 ); ?>; }
.tabs.tabs-simple .nav-tabs > li .nav-link { border-bottom-color: <?php echo porto_if_light( '#eee', $color_dark_4 ); ?>; }
.testimonial .testimonial-author strong { color: <?php echo porto_if_light( '#111', '#fff' ); ?>; }
.testimonial.testimonial-style-3 blockquote { background: <?php echo porto_if_light( '#f2f2f2', $color_dark_4 ); ?>; }
.testimonial.testimonial-style-3 .testimonial-arrow-down { border-top: 10px solid <?php echo porto_if_light( '#f2f2f2', $color_dark_4 ); ?> !important; }
.testimonial.testimonial-style-4 { border-top-color: <?php echo porto_if_light( '#dfdfdf', $color_dark_4 ); ?>; border-bottom-color: <?php echo porto_if_light( '#dfdfdf', $color_dark_4 ); ?>; border-left-color: <?php echo porto_if_light( '#ececec', $color_dark_4 ); ?>; border-right-color: <?php echo porto_if_light( '#ececec', $color_dark_4 ); ?>; }
.testimonial.testimonial-style-5 .testimonial-author { border-top: 1px solid <?php echo porto_if_light( '#f2f2f2', $color_dark_4 ); ?>; }
.thumb-info.thumb-info-bottom-info:not(.thumb-info-bottom-info-dark) .thumb-info-title { background: <?php echo porto_if_light( '#fff', $color_dark ); ?>;  }
.thumb-info.thumb-info-bottom-info:not(.thumb-info-bottom-info-dark) .thumb-info-inner { color: <?php echo porto_if_light( $color_dark, '#fff' ); ?>; }
.thumb-info-side-image { border: 1px solid <?php echo porto_if_light( '#ddd', $color_dark_3 ); ?>; }
section.timeline .timeline-date { border: 1px solid <?php echo porto_if_light( '#e5e5e5', $color_dark_3 ); ?>; background: <?php echo porto_if_light( '#fff', $color_dark_3 ); ?>; text-shadow: <?php echo porto_if_light( '0 1px 1px #fff', 'none' ); ?>; }
section.timeline .timeline-title { background: <?php echo porto_if_light( '#f4f4f4', $color_dark_3 ); ?>; }
section.timeline .timeline-box { border: 1px solid <?php echo porto_if_light( '#e5e5e5', $color_dark_3 ); ?>; background: <?php echo porto_if_light( '#fff', $color_dark_3 ); ?>; }
section.timeline .timeline-box.left:after { background: <?php echo porto_if_light( '#fff', $color_dark_3 ); ?>; border-<?php echo porto_filter_output( $right ); ?>: 1px solid <?php echo porto_if_light( '#e5e5e5', $color_dark_3 ); ?>; border-top: 1px solid <?php echo porto_if_light( '#e5e5e5', $color_dark_3 ); ?>; }
section.timeline .timeline-box.right:after { background: <?php echo porto_if_light( '#fff', $color_dark_3 ); ?>; border-<?php echo porto_filter_output( $left ); ?>: 1px solid <?php echo porto_if_light( '#e5e5e5', $color_dark_3 ); ?>; border-bottom: 1px solid <?php echo porto_if_light( '#e5e5e5', $color_dark_3 ); ?>; }
section.exp-timeline .timeline-box.right:after { border: none; }
.toggle > label { background: <?php echo porto_if_light( '#f4f4f4', $color_dark_4 ); ?>; }
.toggle > label:hover { background: <?php echo porto_if_light( '#f5f5f5', $color_dark_3 ); ?>; }
.toggle.active > label { background: <?php echo porto_if_light( '#f4f4f4', $color_dark_3 ); ?>; }
.toggle-simple .toggle > label,
.toggle-simple .toggle.active > label { color: <?php echo porto_if_light( $color_dark, '#fff' ); ?>; }
div.wpb_single_image .vc_single_image-wrapper.vc_box_shadow_border,
div.wpb_single_image .vc_single_image-wrapper.vc_box_shadow_border_circle,
.product-image,
.product-image .viewcart,
.product-image .stock { background: <?php echo porto_if_light( '#fff', $color_dark_3 ); ?>; }
div.wpb_single_image .vc_single_image-wrapper.vc_box_outline.vc_box_border_grey,
div.wpb_single_image .vc_single_image-wrapper.vc_box_outline_circle.vc_box_border_grey { background: <?php echo porto_if_light( '#fff', $color_dark_3 ); ?>; border-color: <?php echo porto_if_light( '#ddd', $color_dark_3 ); ?>; }
.toggle-simple .toggle.active > label { color: <?php echo porto_if_light( $color_dark, '#fff' ); ?>; }
.porto-links-block .links-title { color: <?php echo porto_if_light( '#465157', '#fff' ); ?>; }
.porto-links-block li.porto-links-item > a,
.porto-links-block li.porto-links-item > span { border-top: 1px solid <?php echo porto_if_light( '#ddd', $porto_color_lib->lighten( $dark_bg, 12 ) ); ?>; }
.widget > div > ul,
.widget > ul { border-bottom-color: <?php echo porto_if_light( 'rgba(0, 0, 0, 0.06)', $color_dark_3 ); ?>; }
.widget > div > ul li,
.widget > ul li { border-top-color: <?php echo porto_if_light( 'rgba(0, 0, 0, 0.06)', $color_dark_3 ); ?>; }
.widget .tagcloud a,
.skill-list a,
.skill-list a:hover { background: <?php echo porto_if_light( $color_dark, '#fff' ); ?>; color: <?php echo porto_if_light( '#fff', $color_dark ); ?>; }
.flickr_badge_image,
.wpb_content_element .flickr_badge_image { background: <?php echo porto_if_light( '#fff', $color_dark_3 ); ?>; }
.sidebar-content .widget.widget_wysija, .sidebar-content .wpcf7-form .widget_wysija { background: <?php echo porto_if_light( '#f4f4f4', $color_dark_4 ); ?>; }
.tm-collapse .tm-section-label { background: <?php echo porto_if_light( '#f5f5f5', $color_dark_3 ); ?>; }
.tm-box { border: 1px solid <?php echo porto_if_light( '#ddd', $color_dark_3 ); ?>; }
body.boxed .page-wrapper,
#content-top,
#content-bottom,
.member-item.member-item-3 .thumb-info-caption { background: <?php echo porto_if_light( '#fff', $color_dark ); ?>; }
body { background: <?php echo porto_if_light( '#fff', '#000' ); ?>; }
<?php if ( ! empty( $b['footer-reveal'] ) ) : ?>
	.page-wrapper { background-color: <?php echo porto_if_light( '#fff', '#000' ); ?>; }
<?php endif; ?>
#main { background: <?php echo porto_if_light( '#fff', $dark_bg ); ?>; }
body .menu-ads-container { background: <?php echo porto_if_light( '#f6f6f6', $color_dark_4 ); ?>; border: 2px solid <?php echo porto_if_light( '#fff', $color_dark_3 ); ?>; }
body .menu-ads-container .vc_column_container { border-<?php echo porto_filter_output( $left ); ?>: 2px solid <?php echo porto_if_light( '#fff', $color_dark_3 ); ?>; }
.portfolio-info ul li { border-<?php echo porto_filter_output( $right ); ?>: 1px solid <?php echo porto_if_light( '#e6e6e6', $color_dark_4 ); ?>; }
<?php if ( class_exists( 'Woocommerce' ) ) : ?>
	@media (max-width: 575px) {
		.commentlist li .comment_container { background: <?php echo porto_if_light( '#f5f7f7', $color_dark_3 ); ?>; }
	}
	.commentlist li .comment-text { background: <?php echo porto_if_light( '#f5f7f7', $color_dark_3 ); ?>; }
	.product-image .stock { background: <?php echo porto_if_light( 'rgba(255, 255, 255, .9)', 'rgba(0, 0, 0, .9)' ); ?>; }
	.login-more.heading-tag { color: #999; }
	.woocommerce-privacy-policy-text p { font-size: 12px; line-height: 23px; color: #8a8b8e; }
	.porto-social-login-section.false-modal a { padding: 17px 12px; margin-bottom: 20px; }
	.porto-social-login-section.false-modal i { margin-right: 5px; }
	.porto-social-login-section.false-modal span { letter-spacing: -.03em; }
	.porto-social-login-section.false-modal i.fa-facebook-f { width: 25px; height: 25px; line-height: 35px; vertical-align: middle; color: #3a589d; background: #fff; border-radius: 50%; }
	#login-form-popup .register-btn { margin: 20px 0 0; color: <?php echo porto_if_light( '#222529', '#fff' ); ?>; letter-spacing: -.025em; outline: none; }
	#login-form-popup .register-btn:hover { color: var(--primary); }
	.porto-social-login-section .social-button:hover i.fa-facebook-f { color: var(--primary); }
	.wc-action-btn.button, .wc-action-btn.button:disabled, .wishlist_table .add-links .quickview, .order-again .button { background-color: <?php echo porto_if_light( '#f4f4f4', $b['skin-color'] ); ?>; color: <?php echo porto_if_light( '#222529', '#fff' ); ?>; border: none; }
	.wc-action-btn.button:disabled { opacity: .8; }
	.wc-action-btn.button:disabled:hover { color: #fff; }
	.order_details .product-name a { color: <?php echo porto_if_light( '#222529', '#fff' ); ?>; }
	.order_details tfoot tr:last-child .amount { color: <?php echo porto_if_light( '#222529', '#fff' ); ?>; }
	.shop_table thead span { color: <?php echo porto_if_light( '#222529', '#fff' ); ?>; text-transform: uppercase; }
	.shop_table thead tr, .shop_table tr:not(:last-child) { border-bottom: 1px solid <?php echo porto_if_light( '#e7e7e7', '#313438' ); ?>; }
	@media (max-width: 767px) {
		.shop_table.shop_table_responsive tr,
		.shop_table.responsive tr,
		.shop_table.shop_table_responsive tfoot tr:first-child,
		.shop_table.responsive tfoot tr:first-child { border-top: 1px solid <?php echo porto_if_light( '#ddd', $color_dark_3 ); ?>; }
	}
	.featured-box .shop_table .quantity input.qty { border-color: <?php echo porto_if_light( '#c8bfc6', 'transparent' ); ?>; }
	.featured-box .shop_table .quantity .minus,
	.featured-box .shop_table .quantity .plus { background: <?php echo porto_if_light( '#f4f4f4', $color_dark_2 ); ?>; border-color: <?php echo porto_if_light( '#c8bfc6', 'transparent' ); ?>; }
	.chosen-container-single .chosen-single,
	.woocommerce-checkout .form-row .chosen-container-single .chosen-single,
	.select2-container .select2-choice { background: <?php echo porto_if_light( '#fff', $color_dark_3 ); ?>; border-color: <?php echo porto_if_light( '#ccc', $color_dark_3 ); ?>; }
	.chosen-container-active.chosen-with-drop .chosen-single,
	.select2-container-active .select2-choice,
	.select2-drop,
	.select2-drop-active { border-color: <?php echo porto_if_light( '#ccc', $color_dark_3 ); ?>; }
	.select2-drop .select2-results,
	.select2-drop-active .select2-results,
	.form-row input[type="email"], .form-row input[type="number"], .form-row input[type="password"], .form-row input[type="search"], .form-row input[type="tel"], .form-row input[type="text"], .form-row input[type="url"], .form-row input[type="color"], .form-row input[type="date"], .form-row input[type="datetime"], .form-row input[type="datetime-local"], .form-row input[type="month"], .form-row input[type="time"], .form-row input[type="week"], .form-row select, .form-row textarea { background-color: <?php echo porto_if_light( '#fff', $color_dark_3 ); ?>; }
	.woocommerce-account .woocommerce-MyAccount-navigation ul li:not(:last-child) a { border-bottom: 1px solid <?php echo porto_if_light( '#ededde', $color_dark_3 ); ?>; }
	.woocommerce-account .woocommerce-MyAccount-navigation ul li.is-active>a, .account-text-user { color: <?php echo porto_if_light( '#222529', '#fff' ); ?>; }
	.order-info mark { display: block; color: <?php echo porto_if_light( '#222529', '#fff' ); ?>; }
	#yith-wcwl-popup-message { background: <?php echo porto_if_light( '#fff', $dark_bg ); ?>; }
	.product_title,
	.product_title a { color: <?php echo porto_if_light( '#555', '#fff' ); ?>; }
	#reviews .commentlist li .comment-text:before { border-<?php echo porto_filter_output( $right ); ?>: 15px solid <?php echo porto_if_light( '#f5f7f7', $color_dark_3 ); ?>; }
	div.quantity .minus,
	div.quantity .plus { background: <?php echo porto_if_light( 'transparent', $color_dark_3 ); ?>; border-color: <?php echo porto_if_light( $input_border_color, $color_dark_3 ); ?>; color: <?php echo porto_if_light( $color_dark, $b['light-color'] ); ?>; }
	.star-rating:before { color: <?php echo porto_if_light( 'rgba(0,0,0,0.16)', 'rgba(255,255,255,0.13)' ); ?>; }
	.wcvashopswatchlabel { border: 1px solid <?php echo porto_if_light( '#fff', $color_dark_3 ); ?>; box-shadow: 0 0 0 1px <?php echo porto_if_light( '#ccc', '#444' ); ?>; }
	.wcvaswatchinput.active .wcvashopswatchlabel { border: 1px solid <?php echo porto_if_light( '#000', '#ccc' ); ?>; }
	.wcvaswatchlabel { border: 2px solid <?php echo porto_if_light( '#fff', $color_dark_3 ); ?>; box-shadow: 0 0 0 1px <?php echo porto_if_light( '#ccc', '#444' ); ?>; }
	.wcvaswatch input:checked + .wcvaswatchlabel { border: 2px solid <?php echo porto_if_light( '#000', '#ccc' ); ?>; box-shadow: 0 0 0 0 <?php echo porto_if_light( '#000', '#ccc' ); ?>; }
	.widget .widget-title .toggle { color: <?php echo porto_if_light( $input_border_color, '#999' ); ?>; }
	.woocommerce .yith-woo-ajax-navigation ul.yith-wcan-label li a,
	.woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-label li a { border: 1px solid <?php echo porto_if_light( '#e9e9e9', $color_dark ); ?>; background: <?php echo porto_if_light( '#fff', $color_dark ); ?>; }
	.widget_recent_reviews .product_list_widget li img { background: <?php echo porto_if_light( '#fff', $color_dark_3 ); ?>; }
	.woocommerce table.shop_table.wishlist_table tbody td,
	.woocommerce table.shop_table.wishlist_table tfoot td { border-color: <?php echo porto_if_light( '#ddd', $color_dark_3 ); ?>; }

	<?php if ( isset( $b['woo-show-product-border'] ) && $b['woo-show-product-border'] ) : ?>
		.product-image { border: 1px solid <?php echo esc_html( $dark ? $color_dark_3 : '#f4f4f4' ); ?>; /*width: 99.9999%;*/ }
	<?php endif; ?>
	<?php if ( ! $b['thumb-padding'] ) : ?>
		.product-images .product-image-slider.owl-carousel .img-thumbnail { padding-right: 1px; padding-left: 1px; }
		.product-images .img-thumbnail .inner { border: 1px solid <?php echo esc_html( $dark ? $color_dark_3 : '#f4f4f4' ); ?>; }
	<?php endif; ?>
	.product-thumbs-slider.owl-carousel .img-thumbnail { border-color: <?php echo esc_html( $dark ? $color_dark_3 : '#f4f4f4' ); ?>; }
<?php endif; ?>

.mobile-sidebar .sidebar-toggle:hover,
.feature-box.feature-box-style-5 h4,
.feature-box.feature-box-style-6 h4,
h1.dark,
h2.dark,
h3.dark,
h4.dark,
h5.dark { color: <?php echo esc_html( $color_dark ); ?>; }
article.post .read-more, article.post .read-more-block, .post-item .read-more, .post-item .read-more-block { color: <?php echo porto_if_light( $color_dark, '#fff' ); ?>; }
.text-dark,
.text-dark.wpb_text_column p { color: <?php echo esc_html( $color_dark ); ?> !important; }
.alert.alert-dark { background-color: <?php echo esc_html( $porto_color_lib->lighten( $color_dark, 10 ) ); ?>; border-color: <?php echo esc_html( $porto_color_lib->darken( $color_dark, 10 ) ); ?>; color: <?php echo esc_html( $porto_color_lib->lighten( $color_dark, 70 ) ); ?>; }
.alert.alert-dark .alert-link { color: <?php echo esc_html( $porto_color_lib->lighten( $color_dark, 85 ) ); ?>; }
.section.section-text-dark,
.section.section-text-dark h1,
.section.section-text-dark h2,
.section.section-text-dark h3,
.section.section-text-dark h4,
.section.section-text-dark h5,
.section.section-text-dark h6,
.vc_general.vc_cta3 h2,
.vc_general.vc_cta3 h4,
.vc_general.vc_cta3.vc_cta3-style-flat .vc_cta3-content-header h2,
.vc_general.vc_cta3.vc_cta3-style-flat .vc_cta3-content-header h4 { color: <?php echo esc_html( $color_dark ); ?>; }
.section.section-text-dark p { color: <?php echo esc_html( $porto_color_lib->lighten( $color_dark, 10 ) ); ?>; }
body.boxed .page-wrapper { border-bottom-color: <?php echo esc_html( $color_dark ); ?>; }

html.dark .text-muted { color: <?php echo esc_html( $porto_color_lib->darken( $dark_default_text, 20 ) ); ?> !important; }
.text-v-dark { color: <?php echo porto_if_light( '#222529', '#eee' ); ?> !important; }
.woocommerce-message, .cart-popup .total, .shop_table.cart-table .product-subtotal .amount, .cart_totals .order-total .amount, .review-order .order-total .amount { color: <?php echo porto_if_light( '#222529', '#fff' ); ?>; }
html .btn-v-dark, .cart-popup .button.checkout, .wishlist_table .add_to_cart.button { color: #fff; background-color: <?php echo porto_if_light( '#222529', esc_html( $porto_color_lib->lighten( $color_dark, 8 ) ) ); ?>; border: none }
html .btn-v-dark:focus, .cart-popup .button.checkout:focus, .wishlist_table .add_to_cart.button:focus { outline: none; box-shadow: none; color: #fff; }
html .btn-v-dark:hover, .cart-popup .button.checkout:hover, .wishlist_table .add_to_cart.button:hover {
	color: #ffffff;
	background-color: <?php echo porto_if_light( '#31373d', esc_html( $porto_color_lib->lighten( $color_dark, 10 ) ) ); ?>
}
<?php if ( class_exists( 'Woocommerce' ) ) : ?>
	.price,
	td.product-subtotal,
	td.product-total,
	td.order-total,
	tr.cart-subtotal,
	.product-nav .product-popup .product-details .amount,
	ul.product_list_widget li .product-details .amount { color: <?php echo esc_html( $color_price ); ?>; }

	.widget_price_filter .price_slider { background: <?php echo esc_html( $price_slide_bg_color ); ?>; }
<?php endif; ?>

.porto-links-block { border-color: <?php echo esc_html( $widget_border_color ); ?>; background: <?php echo esc_html( $widget_bg_color ); ?>; }

.widget_sidebar_menu .widget-title,
.porto-links-block .links-title { background: <?php echo esc_html( $widget_title_bg_color ); ?>; border-bottom-color: <?php echo esc_html( $widget_border_color ); ?>; }

.widget_sidebar_menu,
.tm-collapse,
.widget_layered_nav .yith-wcan-select-wrapper { border-color: <?php echo esc_html( $widget_border_color ); ?>; }

.mobile-sidebar .sidebar-toggle { border-color: <?php echo esc_html( $input_border_color ); ?>; }
.pagination > a,
.pagination > span,
.page-links > a,
.page-links > span { border-color: rgba(0, 0, 0, 0.06); }
<?php if ( ( class_exists( 'bbPress' ) && is_bbpress() ) || ( class_exists( 'BuddyPress' ) && is_buddypress() ) ) : ?>
	.bbp-pagination-links a,
	.bbp-pagination-links span.current,
	.bbp-topic-pagination a,
	#buddypress div.pagination .pagination-links a,
	#buddypress div.pagination .pagination-links span.current { border-color: <?php echo esc_html( $input_border_color ); ?>; }
	#buddypress #whats-new:focus { border-color: <?php echo esc_html( $input_border_color ); ?> !important; outline-color: <?php echo esc_html( $input_border_color ); ?>; }
<?php endif; ?>

.section-title,
.slider-title,
.widget .widget-title,
.widget .widget-title a,
.widget_calendar caption { color: <?php echo esc_html( $color_widget_title ); ?>; }

.accordion.without-borders .card { border-bottom-color: <?php echo esc_html( $panel_default_border ); ?>; }

/*------------------ header type 17 ---------------------- */
<?php if ( 17 == $header_type ) : ?>
	#header .main-menu-wrap .menu-right { position: relative; top: auto; padding-<?php echo porto_filter_output( $left ); ?>: 0; display: table-cell; vertical-align: middle; }
	#header .main-menu-wrap #mini-cart { display: inline-block; }
	#header .main-menu-wrap .searchform-popup { display: inline-block; }
	<?php if ( 'advanced' == $b['search-layout'] ) : ?>
		#header .main-menu-wrap .searchform-popup .search-toggle { display: none; }
		#header .main-menu-wrap .searchform-popup .searchform { margin-top: 0; position: static; display: block; border-width: 0; box-shadow: none; background: rgba(0, 0, 0, 0.07); }
		#header .main-menu-wrap .menu-right .searchform-popup .searchform { border-radius: 0; }
		#header .main-menu-wrap .searchform-popup .searchform fieldset { margin-<?php echo porto_filter_output( $right ); ?>: 0; }
		#header .main-menu-wrap .searchform-popup .searchform input,
		#header .main-menu-wrap .searchform-popup .searchform select,
		#header .main-menu-wrap .searchform-popup .searchform button { border-radius: 0; color: #fff; height: 60px; }
		#header .main-menu-wrap .searchform-popup .searchform input::-webkit-input-placeholder,
		#header .main-menu-wrap .searchform-popup .searchform select::-webkit-input-placeholder,
		#header .main-menu-wrap .searchform-popup .searchform button::-webkit-input-placeholder { color: #fff; opacity: 0.4; text-transform: uppercase; }
		#header .main-menu-wrap .searchform-popup .searchform input:-ms-input-placeholder,
		#header .main-menu-wrap .searchform-popup .searchform select:-ms-input-placeholder,
		#header .main-menu-wrap .searchform-popup .searchform button:-ms-input-placeholder { color: #fff; opacity: 0.4; text-transform: uppercase; }
		#header .main-menu-wrap .searchform-popup .searchform .selectric .label { height: 60px; line-height: 62px; }
		#header .main-menu-wrap .searchform-popup .searchform input { font-weight: 700; width: 200px; padding: <?php echo porto_filter_output( $rtl ? '6px 22px 6px 12px' : '6px 12px 6px 22px' ); ?>; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset; }
		@media <?php echo porto_filter_output( $screen_large ); ?> {
			#header .main-menu-wrap .searchform-popup .searchform input,
			#header .main-menu-wrap .searchform-popup .searchform select,
			#header .main-menu-wrap .searchform-popup .searchform button { height: 50px; }
			#header .main-menu-wrap .searchform-popup .searchform .selectric .label { height: 50px; line-height: 52px; }
			#header .main-menu-wrap .searchform-popup .searchform input { width: 198px; }
		}
		#header .main-menu-wrap .searchform-popup .searchform select { font-weight: 700; width: 120px; padding: <?php echo porto_filter_output( $rtl ? '6px 22px 6px 12px' : '6px 12px 6px 22px' ); ?>; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset; }
		#header .main-menu-wrap .searchform-popup .searchform .selectric-cat { width: 120px; }
		#header .main-menu-wrap .searchform-popup .searchform .selectric .label { font-weight: 700; padding: <?php echo porto_filter_output( $rtl ? '6px 22px 6px 12px' : '6px 12px 6px 22px' ); ?>; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset; }
		#header .main-menu-wrap .searchform-popup .searchform button { margin-<?php echo porto_filter_output( $left ); ?>: -1px; font-size: 20px; padding: 6px 15px; color: #fff; opacity: 0.4; }
		#header .main-menu-wrap .searchform-popup .searchform button:hover { color: #000; }
		#header .main-menu-wrap .searchform-popup .searchform button .fa-search { font-family: "Simple-Line-Icons"; vertical-align: middle; }
		#header .main-menu-wrap .searchform-popup .searchform button .fa-search:before { content: "\e090"; font-family: inherit; }
	<?php endif; ?>
	#header .searchform .live-search-list { left: 0; right: 0; }
	@media (min-width: 768px) {
		#header .header-main .header-left,
		#header .header-main .header-center,
		#header .header-main .header-right { padding-top: 0; padding-bottom: 0; }
	}
	#header .feature-box .feature-box-icon,
	#header .feature-box .feature-box-info { float: <?php echo porto_filter_output( $left ); ?>; padding-<?php echo porto_filter_output( $left ); ?>: 0; }
	#header .feature-box .feature-box-icon { height: auto; top: 0; margin-<?php echo porto_filter_output( $right ); ?>: 0; }
	#header .feature-box .feature-box-icon > i { margin: 0; }
	#header .feature-box .feature-box-info > h4 { line-height: 110px; margin: 0; }
	#header .header-contact { margin: 0; }
	#header .header-extra-info li { padding-<?php echo porto_filter_output( $right ); ?>: 32px; margin-<?php echo porto_filter_output( $left ); ?>: 22px; border-<?php echo porto_filter_output( $right ); ?>: 1px solid #e9e9e9; }
	@media <?php echo porto_filter_output( $screen_large ); ?> {
		#header .header-extra-info li { padding-<?php echo porto_filter_output( $right ); ?>: 30px; margin-<?php echo porto_filter_output( $left ); ?>: 20px; }
	}
	#header .header-extra-info li:first-child { margin-<?php echo porto_filter_output( $left ); ?>: 0; }
	#header .header-extra-info li:last-child { padding-<?php echo porto_filter_output( $right ); ?>: 0; border-<?php echo porto_filter_output( $right ); ?>: medium none; }
	@media (max-width: 991px) {
		#header .header-extra-info li { padding-<?php echo porto_filter_output( $right ); ?>: 15px; margin-<?php echo porto_filter_output( $left ); ?>: 0; border-<?php echo porto_filter_output( $right ); ?>: medium none; }
		#header .header-extra-info li:last-child { padding-<?php echo porto_filter_output( $right ); ?>: 15px; }
	}
	#header.sticky-header .mobile-toggle { margin: 0; }
<?php endif; ?>

/*------------------ Skin ---------------------- */
<?php
	$body_color = $b['body-font']['color'];
	$skin_color = $b['skin-color'];

	$bg_arr = array(
		array( 'body', 'body-bg', 'body-bg-gcolor' ),
		array( '.header-wrapper', 'header-wrap-bg', 'header-wrap-bg-gcolor' ),
		array( '#header .header-main', 'header-bg', 'header-bg-gcolor' ),
		array( '#main', 'content-bg', 'content-bg-gcolor' ),
		array( '#main .content-bottom-wrapper', 'content-bottom-bg', 'content-bottom-bg-gcolor' ),
		array( '.page-top', 'breadcrumbs-bg', 'breadcrumbs-bg-gcolor' ),
		array( '#footer', 'footer-bg', 'footer-bg-gcolor' ),
		array( '#footer .footer-main', 'footer-main-bg', 'footer-main-bg-gcolor' ),
		array( '.footer-top', 'footer-top-bg', 'footer-top-bg-gcolor' ),
		array( '#footer .footer-bottom', 'footer-bottom-bg', 'footer-bottom-bg-gcolor' ),
	);
	foreach ( $bg_arr as $element_bg ) {
		$css = array();

		if ( ! empty( $b[ $element_bg[1] ] ) ) {
			$bg_vars = $b[ $element_bg[1] ];
			if ( isset( $bg_vars['background-color'] ) && $bg_vars['background-color'] ) {
				$css[] = 'background-color:' . esc_html( $bg_vars['background-color'] );
			}
			if ( ! empty( $bg_vars['background-image'] ) ) {
				$css[] = 'background-image: url(' . str_replace( array( 'http://', 'https://' ), array( '//', '//' ), esc_url( $bg_vars['background-image'] ) ) . ')';
				if ( ! empty( $bg_vars['background-repeat'] ) ) {
					$css[] = 'background-repeat:' . esc_html( $bg_vars['background-repeat'] );
				}
				if ( ! empty( $bg_vars['background-size'] ) ) {
					$css[] = 'background-size:' . esc_html( $bg_vars['background-size'] );
				}
				if ( ! empty( $bg_vars['background-attachment'] ) ) {
					$css[] = 'background-attachment:' . esc_html( $bg_vars['background-attachment'] );
				}
				if ( ! empty( $bg_vars['background-position'] ) ) {
					$css[] = 'background-position:' . esc_html( $bg_vars['background-position'] );
				}
			}
		}

		if ( isset( $b[ $element_bg[1] . '-gradient' ] ) && $b[ $element_bg[1] . '-gradient' ] && $b[ $element_bg[2] ]['from'] && $b[ $element_bg[2] ]['to'] ) {
			$css[] = 'background-image: -webkit-linear-gradient(top, ' . esc_html( $b[ $element_bg[2] ]['from'] ) . ',' . esc_html( $b[ $element_bg[2] ]['to'] ) . ')';
			$css[] = 'background-image: linear-gradient(to bottom, ' . esc_html( $b[ $element_bg[2] ]['from'] ) . ',' . esc_html( $b[ $element_bg[2] ]['to'] ) . ')';
			$css[] = 'background-repeat: repeat-x';
		}

		if ( ! empty( $css ) ) {
			echo porto_filter_output( $element_bg[0] ) . '{' . implode( ';', $css ) . '}';
		}
	}
	?>
@media (min-width: 992px) {
	.header-wrapper.header-side-nav:not(.fixed-header) #header {
		background-color: <?php echo esc_html( $b['header-bg']['background-color'] ); ?>;
		<?php if ( ! empty( $b['header-bg']['background-repeat'] ) ) { ?>
			background-repeat: <?php echo esc_html( $b['header-bg']['background-repeat'] ); ?>;
		<?php } ?>
		<?php if ( ! empty( $b['header-bg']['background-size'] ) ) { ?>
			background-size: <?php echo esc_html( $b['header-bg']['background-size'] ); ?>;
		<?php } ?>
		<?php if ( ! empty( $b['header-bg']['background-attachment'] ) ) { ?>
			background-attachment: <?php echo esc_html( $b['header-bg']['background-attachment'] ); ?>;
		<?php } ?>
		<?php if ( ! empty( $b['header-bg']['background-position'] ) ) { ?>
			background-position: <?php echo esc_html( $b['header-bg']['background-position'] ); ?>;
		<?php } ?>
		<?php if ( ! empty( $b['header-bg']['background-image'] ) ) { ?>
			background-image: url(<?php echo str_replace( array( 'http://', 'https://' ), array( '//', '//' ), esc_url( $b['header-bg']['background-image'] ) ); ?>);
		<?php } ?>
	<?php if ( $b['header-bg-gradient'] && $b['header-bg-gcolor']['from'] && $b['header-bg-gcolor']['to'] ) : ?>
		background-image: -webkit-linear-gradient(top, <?php echo esc_html( $b['header-bg-gcolor']['from'] ); ?>, <?php echo esc_html( $b['header-bg-gcolor']['to'] ); ?>);
		background-image: linear-gradient(to bottom, <?php echo esc_html( $b['header-bg-gcolor']['from'] ); ?>, <?php echo esc_html( $b['header-bg-gcolor']['to'] ); ?>);
		background-repeat: repeat-x;
	<?php endif; ?>
	}
}

<?php if ( isset( $b['content-bottom-padding'] ) && ( ! empty( $b['content-bottom-padding']['padding-top'] ) || ! empty( $b['content-bottom-padding']['padding-bottom'] ) ) ) : ?>
	#main .content-bottom-wrapper {
		<?php
		if ( ! empty( $b['content-bottom-padding']['padding-top'] ) ) :
			?>
		padding-top: <?php echo esc_html( $b['content-bottom-padding']['padding-top'] ); ?>px; <?php endif; ?>
		<?php
		if ( ! empty( $b['content-bottom-padding']['padding-bottom'] ) ) :
			?>
		padding-bottom: <?php echo esc_html( $b['content-bottom-padding']['padding-bottom'] ); ?>px; <?php endif; ?>
	}
<?php endif; ?>
<?php if ( isset( $b['footer-top-padding'] ) && ( ! empty( $b['footer-top-padding']['padding-top'] ) || ! empty( $b['footer-top-padding']['padding-bottom'] ) ) ) : ?>
	.footer-top {
		<?php
		if ( ! empty( $b['footer-top-padding']['padding-top'] ) ) :
			?>
		padding-top: <?php echo esc_html( $b['footer-top-padding']['padding-top'] ); ?>px; <?php endif; ?>
		<?php
		if ( ! empty( $b['footer-top-padding']['padding-bottom'] ) ) :
			?>
		padding-bottom: <?php echo esc_html( $b['footer-top-padding']['padding-bottom'] ); ?>px; <?php endif; ?>
	}
<?php endif; ?>

/* layout */
<?php porto_calc_container_width( '#banner-wrapper.banner-wrapper-boxed', ( $header_bg_empty && ! $content_bg_empty ), $b['container-width'], $b['grid-gutter-width'] ); ?>
<?php porto_calc_container_width( '#main.main-boxed', ( $header_bg_empty && ! $content_bg_empty ), $b['container-width'], $b['grid-gutter-width'] ); ?>
<?php porto_calc_container_width( 'body.boxed .page-wrapper', false, $b['container-width'], $b['grid-gutter-width'] ); ?>
<?php porto_calc_container_width( '#main.main-boxed .vc_row[data-vc-stretch-content]', ( $header_bg_empty && ! $content_bg_empty ), $b['container-width'], $b['grid-gutter-width'] ); ?>
@media (min-width: <?php echo (int) ( $b['container-width'] + $b['grid-gutter-width'] ); ?>px) {
	body.boxed .vc_row[data-vc-stretch-content],
	body.boxed #header.sticky-header .header-main.sticky,
	body.boxed #header.sticky-header .main-menu-wrap,
	body.boxed #header.sticky-header .header-main.sticky,
	#header-boxed #header.sticky-header .header-main.sticky,
	body.boxed #header.sticky-header .main-menu-wrap,
	#header-boxed #header.sticky-header .main-menu-wrap {
		max-width: <?php echo (int) ( $b['container-width'] + $b['grid-gutter-width'] ); ?>px;
	}

	.col-xl-1-5 { -ms-flex: 0 0 20%; flex: 0 0 20%; width: 20%; }
	.col-xl-2-5 { -ms-flex: 0 0 40%; flex: 0 0 40%; width: 40%; }
	.col-xl-3-5 { -ms-flex: 0 0 60%; flex: 0 0 60%; width: 60%; }
	.col-xl-4-5 { -ms-flex: 0 0 80%; flex: 0 0 80%; width: 80%; }
}

/* header */
<?php if ( isset( $b['header-top-font-size'] ) && $b['header-top-font-size'] ) : ?>
	#header .header-top { font-size: <?php echo esc_attr( $b['header-top-font-size'] ); ?>px; }
<?php endif; ?>
#header .separator { border-left: 1px solid <?php echo porto_filter_output( $porto_color_lib->isColorDark( $b['header-link-color']['regular'] ) ? 'rgba(0, 0, 0, .04)' : 'rgba(255, 255, 255, .09)' ); ?> }
#header .header-top .separator { border-left-color: <?php echo porto_filter_output( $porto_color_lib->isColorDark( $b['header-top-link-color']['regular'] ) ? 'rgba(0, 0, 0, .04)' : 'rgba(255, 255, 255, .09)' ); ?> }
<?php if ( 'transparent' == $b['header-top-bg-color'] || ! $porto_color_lib->isColorDark( $b['header-top-bg-color'] ) ) : ?>
	<?php if ( ( (int) $header_type >= 10 && (int) $header_type <= 17 ) || empty( $header_type ) ) : ?>
		#header .header-top .share-links > a:not(:hover) { background: none; }
	<?php endif; ?>
<?php endif; ?>

<?php
	$header_bg_color = $b['header-bg']['background-color'];
	$header_opacity  = ( isset( $b['header-opacity'] ) && (int) $b['header-opacity'] ) ? (int) $b['header-opacity'] : 80;
	$header_opacity  = (float) $header_opacity / 100;

	$searchform_opacity = ( (int) $b['searchform-opacity'] ) ? (int) $b['searchform-opacity'] : 50;
	$searchform_opacity = (float) $searchform_opacity / 100;
	$menuwrap_opacity   = ( (int) $b['menuwrap-opacity'] ) ? (int) $b['menuwrap-opacity'] : 30;
	$menuwrap_opacity   = (float) $menuwrap_opacity / 100;
	$menu_opacity       = ( (int) $b['menu-opacity'] ) ? (int) $b['menu-opacity'] : 30;
	$menu_opacity       = (float) $menu_opacity / 100;

	$footer_opacity = ( (int) $b['footer-opacity'] ) ? (int) $b['footer-opacity'] : 80;
	$footer_opacity = (float) $footer_opacity / 100;
?>
.fixed-header #header .header-main {
<?php if ( 'transparent' == $header_bg_color ) : ?>
	box-shadow: none;
<?php elseif ( $header_bg_color ) : ?>
	background-color: rgba(<?php echo esc_html( $porto_color_lib->hexToRGB( $header_bg_color ) ); ?>, <?php echo esc_html( $header_opacity ); ?>);
<?php endif; ?>
}
<?php if ( 'transparent' != $b['header-top-bg-color'] && $b['header-top-bg-color'] ) : ?>
	.fixed-header #header .header-top {
		background-color: rgba(<?php echo esc_html( $porto_color_lib->hexToRGB( $b['header-top-bg-color'] ) ); ?>, <?php echo esc_html( $header_opacity ); ?>);
	}
<?php endif; ?>
@media (min-width: 992px) {
	.header-wrapper.header-side-nav.fixed-header #header {
		<?php porto_background_opacity( $porto_color_lib, $header_bg_color, $header_opacity ); ?>
	}
}

<?php
if ( $b['mainmenu-wrap-bg-color-sticky'] && 'transparent' != $b['mainmenu-wrap-bg-color-sticky'] ) {
	$sticky_menu_bg_color = $b['mainmenu-wrap-bg-color-sticky'];
} elseif ( $b['mainmenu-bg-color'] && 'transparent' != $b['mainmenu-bg-color'] ) {
	$sticky_menu_bg_color = $b['mainmenu-bg-color'];
} elseif ( $b['mainmenu-wrap-bg-color'] && 'transparent' != $b['mainmenu-wrap-bg-color'] ) {
	$sticky_menu_bg_color = $b['mainmenu-wrap-bg-color'];
} else {
	$sticky_menu_bg_color = $b['sticky-header-bg']['background-color'];
}
?>
#header.sticky-header .header-main,
.fixed-header #header.sticky-header .header-main
<?php echo 'transparent' == $sticky_menu_bg_color ? ', #header.sticky-header .main-menu-wrap, .fixed-header #header.sticky-header .main-menu-wrap' : ''; ?> {
	<?php if ( ! empty( $b['sticky-header-bg']['background-color'] ) && 'transparent' != $b['sticky-header-bg']['background-color'] ) { ?>
		background-color: rgba(<?php echo esc_html( $porto_color_lib->hexToRGB( $b['sticky-header-bg']['background-color'] ) ); ?>, <?php echo (float) str_replace( '%', '', $b['sticky-header-opacity'] ) / 100; ?>);
	<?php } ?>
	<?php if ( isset( $b['sticky-header-bg']['background-repeat'] ) && ! empty( $b['sticky-header-bg']['background-repeat'] ) ) { ?>
		background-repeat: <?php echo esc_html( $b['sticky-header-bg']['background-repeat'] ); ?>;
	<?php } ?>
	<?php if ( isset( $b['sticky-header-bg']['background-size'] ) && ! empty( $b['sticky-header-bg']['background-size'] ) ) { ?>
		background-size: <?php echo esc_html( $b['sticky-header-bg']['background-size'] ); ?>;
	<?php } ?>
	<?php if ( isset( $b['sticky-header-bg']['background-attachment'] ) && ! empty( $b['sticky-header-bg']['background-attachment'] ) ) { ?>
		background-attachment: <?php echo esc_html( $b['sticky-header-bg']['background-attachment'] ); ?>;
	<?php } ?>
	<?php if ( isset( $b['sticky-header-bg']['background-position'] ) && ! empty( $b['sticky-header-bg']['background-position'] ) ) { ?>
		background-position: <?php echo esc_html( $b['sticky-header-bg']['background-position'] ); ?>;
	<?php } ?>
	<?php if ( isset( $b['sticky-header-bg']['background-image'] ) && ! empty( $b['sticky-header-bg']['background-image'] ) ) { ?>
		background-image: url(<?php echo str_replace( array( 'http://', 'https://' ), array( '//', '//' ), $b['sticky-header-bg']['background-image'] ); ?>);
	<?php } ?>
<?php if ( $b['sticky-header-bg-gradient'] && $b['sticky-header-bg-gcolor']['from'] && $b['sticky-header-bg-gcolor']['to'] && 'transparent' != $b['sticky-header-bg-gcolor']['from'] && 'transparent' != $b['sticky-header-bg-gcolor']['to'] ) : ?>
	background-image: -webkit-linear-gradient(top, rgba(<?php echo esc_html( $porto_color_lib->hexToRGB( $b['sticky-header-bg-gcolor']['from'] ) ); ?>, <?php echo (float) str_replace( '%', '', $b['sticky-header-opacity'] ) / 100; ?>), rgba(<?php echo esc_html( $porto_color_lib->hexToRGB( $b['sticky-header-bg-gcolor']['to'] ) ); ?>, <?php echo (float) str_replace( '%', '', $b['sticky-header-opacity'] ) / 100; ?>));
	background-image: linear-gradient(to bottom, rgba(<?php echo esc_html( $porto_color_lib->hexToRGB( $b['sticky-header-bg-gcolor']['from'] ) ); ?>, <?php echo (float) str_replace( '%', '', $b['sticky-header-opacity'] ) / 100; ?>), rgba(<?php echo esc_html( $porto_color_lib->hexToRGB( $b['sticky-header-bg-gcolor']['to'] ) ); ?>, <?php echo (float) str_replace( '%', '', $b['sticky-header-opacity'] ) / 100; ?>));
	background-repeat: repeat-x;
<?php endif; ?>
}
<?php if ( 'transparent' != $sticky_menu_bg_color ) : ?>
#header.sticky-header .main-menu-wrap,
.fixed-header #header.sticky-header .main-menu-wrap {
	background-color: rgba(<?php echo esc_html( $porto_color_lib->hexToRGB( $sticky_menu_bg_color ) ); ?>, <?php echo (float) str_replace( '%', '', $b['sticky-header-opacity'] ) / 100; ?>);
}
<?php endif; ?>
<?php if ( ! empty( $b['sticky-header-bg']['background-image'] ) ) { ?>
	#header.header-loaded .header-main { transition: none; }
<?php } ?>

.fixed-header #header .searchform {
	<?php porto_background_opacity( $porto_color_lib, $b['searchform-bg-color'], $searchform_opacity ); ?>
	<?php if ( 'transparent' != $b['searchform-border-color'] && $searchform_opacity ) : ?>
		border-color: rgba(<?php echo esc_html( $porto_color_lib->hexToRGB( $b['searchform-border-color'] ) ); ?>, <?php echo esc_html( $searchform_opacity ); ?>);
	<?php endif; ?>
}
@media (max-width: 991px) {
	.fixed-header #header .searchform {
		<?php porto_background_opacity( $porto_color_lib, $b['searchform-bg-color'], 1 ); ?>
	}
}
.fixed-header #header .searchform-popup .searchform {
	<?php porto_background_opacity( $porto_color_lib, $b['searchform-bg-color'], 1 ); ?>
}
.fixed-header #header .main-menu-wrap {
	<?php porto_background_opacity( $porto_color_lib, $b['mainmenu-wrap-bg-color'], $menuwrap_opacity ); ?>
}
.fixed-header #header .main-menu {
	<?php porto_background_opacity( $porto_color_lib, $b['mainmenu-bg-color'], $menu_opacity ); ?>
}
#header .searchform,
.fixed-header #header.sticky-header .searchform {
	<?php if ( $b['searchform-bg-color'] ) : ?>
		background: <?php echo esc_html( 'overlay' == $b['search-layout'] ? 'rgba(' . $porto_color_lib->hexToRGB( $b['searchform-bg-color'] ) . ', .95)' : $b['searchform-bg-color'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['searchform-border-color'] ) : ?>
		border-color: <?php echo esc_html( $b['searchform-border-color'] ); ?>;
	<?php endif; ?>
}
<?php if ( 'simple' != $b['search-layout'] ) : ?>
	.fixed-header #header.sticky-header .searchform {
		border-radius: <?php echo porto_filter_output( $b['search-border-radius'] ? '20px' : '0' ); ?>;
	}
<?php endif; ?>
<?php if ( $b['mainmenu-bg-color'] && 'transparent' != $b['mainmenu-bg-color'] ) : ?>
	.fixed-header #header.sticky-header .main-menu,
	#header .main-menu,
	#main-toggle-menu .toggle-menu-wrap {
		background-color: <?php echo esc_html( $b['mainmenu-bg-color'] ); ?>;
	}
<?php endif; ?>
<?php if ( ! empty( $b['header-bottom-bg-color'] ) ) : ?>
	.header-bottom { background-color: <?php echo esc_html( $b['header-bottom-bg-color'] ); ?> }
<?php endif; ?>
<?php if ( ! empty( $b['header-bottom-container-bg-color'] ) ) : ?>
	.header-bottom > .container { background-color: <?php echo esc_html( $b['header-bottom-container-bg-color'] ); ?> }
<?php endif; ?>

<?php if ( ! empty( $b['header-text-color'] ) ) : ?>
	#header,
	#header .header-main .header-contact .nav-top > li > a,
	#header .top-links > li.menu-item:before { color: <?php echo esc_html( $b['header-text-color'] ); ?> }
<?php endif; ?>
<?php if ( $b['header-link-color']['regular'] ) : ?>
	.header-main .header-contact a,
	#header .tooltip-icon,
	#header .top-links > li.menu-item > a,
	#header .searchform-popup .search-toggle,
	.header-wrapper .custom-html a:not(.btn),
	#header .my-account,
	#header .my-wishlist {
		color: <?php echo esc_html( $b['header-link-color']['regular'] ); ?>;
	}
	#header .tooltip-icon { border-color: <?php echo esc_html( $b['header-link-color']['regular'] ); ?>; }
<?php endif; ?>
<?php if ( $b['header-link-color']['hover'] ) : ?>
	.header-main .header-contact a:hover,
	#header .top-links > li.menu-item:hover > a,
	#header .top-links > li.menu-item > a.active,
	#header .top-links > li.menu-item > a.focus,
	#header .top-links > li.menu-item.has-sub:hover > a,
	#header .searchform-popup .search-toggle:hover,
	.header-wrapper .custom-html a:not(.btn):hover,
	#header .my-account,
	#header .my-wishlist {
		color: <?php echo esc_html( $b['header-link-color']['hover'] ); ?>;
	}
<?php endif; ?>
<?php if ( ! empty( $b['header-top-text-color'] ) ) : ?>
	#header .header-top,
	.header-top .top-links > li.menu-item:after { color: <?php echo esc_html( $b['header-top-text-color'] ); ?> }
<?php endif; ?>
<?php if ( $b['header-top-link-color']['regular'] ) : ?>
	.header-top .header-contact a,
	.header-top .custom-html a:not(.btn),
	#header .header-top .top-links > li.menu-item > a,
	.header-top .welcome-msg a {
		color: <?php echo esc_html( $b['header-top-link-color']['regular'] ); ?>;
	}
<?php endif; ?>
<?php if ( $b['header-top-link-color']['hover'] ) : ?>
	.header-top .header-contact a:hover,
	.header-top .custom-html a:not(.btn):hover,
	#header .header-top .top-links > li.menu-item.active > a,
	#header .header-top .top-links > li.menu-item:hover > a,
	#header .header-top .top-links > li.menu-item > a.active,
	#header .header-top .top-links > li.menu-item.has-sub:hover > a,
	.header-top .welcome-msg a:hover {
		color: <?php echo esc_html( $b['header-top-link-color']['hover'] ); ?>;
	}
<?php endif; ?>
<?php if ( ! empty( $b['header-bottom-text-color'] ) ) : ?>
	#header .header-bottom { color: <?php echo esc_html( $b['header-bottom-text-color'] ); ?> }
<?php endif; ?>
<?php if ( ! empty( $b['header-bottom-link-color']['regular'] ) ) : ?>
	#header .header-bottom a:not(.btn) { color: <?php echo esc_html( $b['header-bottom-link-color']['regular'] ); ?> }
<?php endif; ?>
<?php if ( ! empty( $b['header-bottom-link-color']['hover'] ) ) : ?>
	#header .header-bottom a:not(.btn):hover { color: <?php echo esc_html( $b['header-bottom-link-color']['hover'] ); ?> }
<?php endif; ?>

<?php if ( $b['mainmenu-toplevel-hbg-color'] ) : ?>
	#header .header-main .top-links > li.menu-item.has-sub:hover > a,
	#header .header-bottom .top-links > li.menu-item.has-sub:hover > a {
		background-color: <?php echo esc_html( $b['mainmenu-toplevel-hbg-color'] ); ?>;
	}
<?php endif; ?>
<?php if ( $b['mainmenu-popup-bg-color'] ) : ?>
	#header .top-links .narrow ul.sub-menu,
	#header .main-menu .wide .popup > .inner,
	.side-nav-wrap .sidebar-menu .wide .popup > .inner,
	.sidebar-menu .narrow ul.sub-menu {
		background-color: <?php echo esc_html( $b['mainmenu-popup-bg-color'] ); ?>;
	}
	<?php if ( isset( $b['submenu-arrow'] ) && $b['submenu-arrow'] ) : ?>
		#header .top-links > li.has-sub:before,
		#header .top-links > li.has-sub:after {
			border-bottom-color: <?php echo esc_html( $b['mainmenu-popup-bg-color'] ); ?>;
		}
	<?php endif; ?>
	.sidebar-menu .menu-custom-block a:hover,
	.sidebar-menu .menu-custom-block a:hover + a {
		border-top-color: <?php echo esc_html( $b['mainmenu-popup-bg-color'] ); ?>;
	}
<?php endif; ?>
<?php if ( $b['mainmenu-popup-text-color']['regular'] ) : ?>
	#header .top-links .narrow li.menu-item > a,
	#header .main-menu .wide li.sub li.menu-item > a,
	.side-nav-wrap .sidebar-menu .wide li.menu-item li.menu-item > a,
	.sidebar-menu .wide li.sub li.menu-item > a,
	.sidebar-menu .narrow li.menu-item > a,
	.porto-popup-menu .sub-menu a {
		color: <?php echo esc_html( $b['mainmenu-popup-text-color']['regular'] ); ?>;
	}
<?php endif; ?>
<?php if ( $b['mainmenu-popup-text-color']['hover'] ) : ?>
	#header .top-links .narrow li.menu-item:hover > a,
	.porto-popup-menu .sub-menu a:hover {
		color: <?php echo esc_html( $b['mainmenu-popup-text-color']['hover'] ); ?>;
	}
<?php endif; ?>
<?php if ( $b['mainmenu-popup-text-hbg-color'] ) : ?>
	#header .top-links .narrow li.menu-item:hover > a,
	#header .sidebar-menu .narrow .menu-item:hover > a,
	.main-sidebar-menu .sidebar-menu .narrow .menu-item:hover > a,
	.main-menu .wide li.menu-item li.menu-item > a:hover { background-color: <?php echo esc_html( $b['mainmenu-popup-text-hbg-color'] ); ?>; }
<?php endif; ?>
<?php if ( 'slide' != $b['side-menu-type'] ) : ?>
	.side-nav-wrap .sidebar-menu .wide li.menu-item li.menu-item > a:hover {
		<?php if ( $b['mainmenu-popup-text-hbg-color'] ) : ?>
			background-color: <?php echo esc_html( $b['mainmenu-popup-text-hbg-color'] ); ?>;
		<?php endif; ?>
		<?php if ( $b['mainmenu-popup-text-color']['hover'] ) : ?>
			color: <?php echo esc_html( $b['mainmenu-popup-text-color']['hover'] ); ?>;
		<?php endif; ?>
	}
<?php endif; ?>
<?php porto_calc_container_width( '#header-boxed', false, $b['container-width'], $b['grid-gutter-width'] ); ?>

<?php if ( $b['header-top-menu-padding']['padding-top'] || $b['header-top-menu-padding']['padding-bottom'] || $b['header-top-menu-padding']['padding-left'] || $b['header-top-menu-padding']['padding-right'] ) : ?>
	#header .header-top .top-links > li.menu-item > a {
		<?php if ( $b['header-top-menu-padding']['padding-top'] ) : ?>
			padding-top: <?php echo esc_html( $b['header-top-menu-padding']['padding-top'] ); ?>px;
		<?php endif; ?>
		<?php if ( $b['header-top-menu-padding']['padding-bottom'] ) : ?>
			padding-bottom: <?php echo esc_html( $b['header-top-menu-padding']['padding-bottom'] ); ?>px;
		<?php endif; ?>
		<?php if ( $b['header-top-menu-padding']['padding-left'] ) : ?>
			padding-left: <?php echo esc_html( $b['header-top-menu-padding']['padding-left'] ); ?>px;
		<?php endif; ?>
		<?php if ( $b['header-top-menu-padding']['padding-right'] ) : ?>
			padding-right: <?php echo esc_html( $b['header-top-menu-padding']['padding-right'] ); ?>px;
		<?php endif; ?>
	}
<?php endif; ?>
#header .header-top .top-links .narrow li.menu-item:hover > a { text-decoration: none; }
<?php if ( $b['header-top-menu-hide-sep'] ) : ?>
	#header .top-links > li.menu-item:after { content: none; }
	#header .header-top .gap { visibility: hidden; }
<?php endif; ?>

.header-top {
	<?php if ( $b['header-top-bottom-border']['border-top'] && '0px' != $b['header-top-bottom-border']['border-top'] && 'menu-hover-line' != $b['menu-type'] ) : ?>
		border-bottom: <?php echo esc_html( $b['header-top-bottom-border']['border-top'] ); ?> solid <?php echo esc_html( $b['header-top-bottom-border']['border-color'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['header-top-bg-color'] ) : ?>
		background-color: <?php echo esc_html( $b['header-top-bg-color'] ); ?>;
	<?php endif; ?>
}

.main-menu-wrap {
	<?php if ( $b['mainmenu-wrap-bg-color'] ) : ?>
		background-color: <?php echo esc_html( $b['mainmenu-wrap-bg-color'] ); ?>;
	<?php endif; ?>
	padding: <?php echo porto_config_value( $b['mainmenu-wrap-padding']['padding-top'] ); ?>px <?php echo porto_config_value( $b['mainmenu-wrap-padding'][ 'padding-' . $right ] ); ?>px <?php echo porto_config_value( $b['mainmenu-wrap-padding']['padding-bottom'] ); ?>px <?php echo porto_config_value( $b['mainmenu-wrap-padding'][ 'padding-' . $left ] ); ?>px;
}

<?php if ( '' != $b['mainmenu-wrap-padding-sticky']['padding-top'] || '' != $b['mainmenu-wrap-padding-sticky']['padding-bottom'] || '' != $b['mainmenu-wrap-padding-sticky']['padding-left'] || '' != $b['mainmenu-wrap-padding-sticky']['padding-right'] ) : ?>
	#header.sticky-header .main-menu-wrap,
	#header.sticky-header .header-main.sticky .header-left,
	#header.sticky-header .header-main.sticky .header-right {
		<?php if ( '' != $b['mainmenu-wrap-padding-sticky']['padding-top'] ) : ?>
			padding-top: <?php echo porto_config_value( $b['mainmenu-wrap-padding-sticky']['padding-top'] ); ?>px;
		<?php endif; ?>
		<?php if ( '' != $b['mainmenu-wrap-padding-sticky']['padding-bottom'] ) : ?>
			padding-bottom: <?php echo porto_config_value( $b['mainmenu-wrap-padding-sticky']['padding-bottom'] ); ?>px;
		<?php endif; ?>
		<?php if ( '' != $b['mainmenu-wrap-padding-sticky']['padding-left'] ) : ?>
			padding-left: <?php echo porto_config_value( $b['mainmenu-wrap-padding-sticky']['padding-left'] ); ?>px;
		<?php endif; ?>
		<?php if ( '' != $b['mainmenu-wrap-padding-sticky']['padding-right'] ) : ?>
			padding-right: <?php echo porto_config_value( $b['mainmenu-wrap-padding-sticky']['padding-right'] ); ?>px;
		<?php endif; ?>
	}
<?php endif; ?>
#header.sticky-header .header-main.sticky .header-center {
	padding-top: <?php echo porto_config_value( $b['mainmenu-wrap-padding-sticky']['padding-top'] ); ?>px;
	padding-bottom: <?php echo porto_config_value( $b['mainmenu-wrap-padding-sticky']['padding-bottom'] ); ?>px
}
<?php if ( $b['border-radius'] && ( empty( $b['mainmenu-bg-color'] ) || 'transparent' == $b['mainmenu-bg-color'] ) && ( empty( $b['mainmenu-toplevel-hbg-color'] ) || 'transparent' == $b['mainmenu-toplevel-hbg-color'] ) ) : ?>
	.main-menu-wrap .main-menu .wide .popup,
	.main-menu-wrap .main-menu .wide .popup > .inner,
	.main-menu-wrap .main-menu .wide.pos-left .popup,
	.main-menu-wrap .main-menu .wide.pos-right .popup,
	.main-menu-wrap .main-menu .wide.pos-left .popup > .inner,
	.main-menu-wrap .main-menu .wide.pos-right .popup > .inner,
	.main-menu-wrap .main-menu .narrow .popup > .inner > ul.sub-menu,
	.main-menu-wrap .main-menu .narrow.pos-left .popup > .inner > ul.sub-menu,
	.main-menu-wrap .main-menu .narrow.pos-right .popup > .inner > ul.sub-menu { border-radius: 0 0 2px 2px; }
<?php endif; ?>
.main-menu-wrap .main-menu > li.menu-item > a .tip {
	<?php echo porto_filter_output( $right ); ?>: <?php echo porto_config_value( $b['mainmenu-toplevel-padding1'][ 'padding-' . $right ] ); ?>px;
	top: <?php echo (int) porto_config_value( $b['mainmenu-toplevel-padding1']['padding-top'] ) - 15; ?>px;
}
#header .main-menu-wrap .menu-custom-block a,
#header .main-menu-wrap .menu-custom-block span {
	padding: <?php echo porto_config_value( $b['mainmenu-toplevel-padding1']['padding-top'] ); ?>px <?php echo porto_config_value( $b['mainmenu-toplevel-padding1'][ 'padding-' . $right ] ); ?>px <?php echo porto_config_value( $b['mainmenu-toplevel-padding1']['padding-bottom'] ); ?>px <?php echo porto_config_value( $b['mainmenu-toplevel-padding1'][ 'padding-' . $left ] ); ?>px;
}
<?php if ( $b['mainmenu-toplevel-padding1']['padding-top'] ) : ?>
	#header .main-menu-wrap .menu-custom-block {
		padding-top: 0;
		padding-bottom: 0
	}
<?php endif; ?>
@media <?php echo porto_filter_output( $screen_large ); ?> {
	.main-menu-wrap .main-menu > li.menu-item > a .tip {
		<?php echo porto_filter_output( $right ); ?>: <?php echo porto_config_value( $b['mainmenu-toplevel-padding2'][ 'padding-' . $right ] ); ?>px;
		top: <?php echo (int) porto_config_value( $b['mainmenu-toplevel-padding2']['padding-top'] ) - 15; ?>px;
	}
	#header .main-menu-wrap .menu-custom-block a,
	#header .main-menu-wrap .menu-custom-block span {
		padding: <?php echo porto_config_value( $b['mainmenu-toplevel-padding2']['padding-top'] ); ?>px <?php echo porto_config_value( $b['mainmenu-toplevel-padding2'][ 'padding-' . $right ] ); ?>px <?php echo porto_config_value( $b['mainmenu-toplevel-padding2']['padding-bottom'] ); ?>px <?php echo porto_config_value( $b['mainmenu-toplevel-padding2'][ 'padding-' . $left ] ); ?>px;
	}
}
<?php if ( $b['enable-sticky-header'] ) : ?>
	<?php if ( isset( $b['mainmenu-toplevel-padding3'] ) && ! empty( $b['mainmenu-toplevel-padding3'][ 'padding-' . $right ] ) ) : ?>
		.sticky-header .main-menu-wrap .main-menu > li.menu-item > a .tip,
		#header.sticky-header .main-menu-wrap .main-menu .menu-custom-block .tip {
			<?php echo porto_filter_output( $right ); ?>: <?php echo porto_config_value( $b['mainmenu-toplevel-padding3'][ 'padding-' . $right ] ); ?>px;
		}
	<?php endif; ?>
	<?php if ( isset( $b['mainmenu-toplevel-padding3'] ) && ! empty( $b['mainmenu-toplevel-padding3']['padding-top'] ) ) : ?>
		.sticky-header .main-menu-wrap .main-menu > li.menu-item > a .tip,
		#header.sticky-header .main-menu-wrap .main-menu .menu-custom-block .tip {
			top: <?php echo porto_config_value( $b['mainmenu-toplevel-padding3']['padding-top'] ) - 15; ?>px;
		}
	<?php endif; ?>
	<?php if ( isset( $b['mainmenu-toplevel-padding3'] ) && ( ! empty( $b['mainmenu-toplevel-padding3']['padding-top'] ) || ! empty( $b['mainmenu-toplevel-padding3']['padding-bottom'] ) ) || ! empty( $b['mainmenu-toplevel-padding3']['padding-left'] ) || ! empty( $b['mainmenu-toplevel-padding3']['padding-right'] ) ) : ?>
		@media (min-width: 992px) {
			#header.sticky-header .main-menu > li.menu-item > a,
			#header.sticky-header .menu-custom-block span,
			#header.sticky-header .menu-custom-block a,
			#header.sticky-header .main-menu-wrap .main-menu .menu-custom-block a,
			#header.sticky-header .main-menu-wrap .main-menu .menu-custom-block span {
				<?php if ( ! empty( $b['mainmenu-toplevel-padding3']['padding-top'] ) ) : ?>
					padding-top: <?php echo porto_config_value( $b['mainmenu-toplevel-padding3']['padding-top'] ); ?>px;
				<?php endif; ?>
				<?php if ( ! empty( $b['mainmenu-toplevel-padding3']['padding-bottom'] ) ) : ?>
					padding-bottom: <?php echo porto_config_value( $b['mainmenu-toplevel-padding3']['padding-bottom'] ); ?>px;
				<?php endif; ?>
				<?php if ( ! empty( $b['mainmenu-toplevel-padding3'][ 'padding-' . $left ] ) ) : ?>
					padding-<?php echo porto_filter_output( $left ); ?>: <?php echo porto_config_value( $b['mainmenu-toplevel-padding3'][ 'padding-' . $left ] ); ?>px;
				<?php endif; ?>
				<?php if ( ! empty( $b['mainmenu-toplevel-padding3'][ 'padding-' . $right ] ) ) : ?>
					padding-<?php echo porto_filter_output( $right ); ?>: <?php echo porto_config_value( $b['mainmenu-toplevel-padding3'][ 'padding-' . $right ] ); ?>px;
				<?php endif; ?>
			}
		}
	<?php endif; ?>
	<?php if ( 'menu-hover-line menu-hover-underline' == $b['menu-type'] && isset( $b['mainmenu-toplevel-padding3'] ) && ! empty( $b['mainmenu-toplevel-padding3']['padding-left'] ) && ! empty( $b['mainmenu-toplevel-padding3']['padding-right'] ) ) : ?>
		.sticky-header .mega-menu.menu-hover-underline > li.menu-item > a:before {
			margin-left: <?php echo porto_config_value( $b['mainmenu-toplevel-padding3']['padding-left'] ); ?>px;
			margin-right: <?php echo porto_config_value( $b['mainmenu-toplevel-padding3']['padding-right'] ); ?>px;
		}
	<?php endif; ?>
<?php endif; ?>

#header .main-menu-wrap .menu-custom-block .tip {
	<?php echo porto_filter_output( $right ); ?>: <?php echo porto_config_value( $b['mainmenu-toplevel-padding1'][ 'padding-' . $right ] ) - 5; ?>px;
	top: <?php echo porto_config_value( $b['mainmenu-toplevel-padding1']['padding-top'] ) - 15; ?>px;
}

#header .main-menu > li.menu-item > a {
	<?php if ( $b['menu-font']['font-family'] ) : ?>
		font-family: <?php echo sanitize_text_field( $b['menu-font']['font-family'] ); ?>, sans-serif;
	<?php endif; ?>
	<?php if ( $b['menu-font']['font-size'] ) : ?>
		font-size: <?php echo esc_html( $b['menu-font']['font-size'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['menu-font']['font-weight'] ) : ?>
		font-weight: <?php echo esc_html( $b['menu-font']['font-weight'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['menu-font']['line-height'] ) : ?>
		line-height: <?php echo esc_html( $b['menu-font']['line-height'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['menu-font']['letter-spacing'] ) : ?>
		letter-spacing: <?php echo esc_html( $b['menu-font']['letter-spacing'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['mainmenu-toplevel-link-color']['regular'] ) : ?>
		color: <?php echo esc_html( $b['mainmenu-toplevel-link-color']['regular'] ); ?>;
	<?php endif; ?>
	padding: <?php echo porto_config_value( $b['mainmenu-toplevel-padding1']['padding-top'] ); ?>px <?php echo porto_config_value( $b['mainmenu-toplevel-padding1'][ 'padding-' . $right ] ); ?>px <?php echo porto_config_value( $b['mainmenu-toplevel-padding1']['padding-bottom'] ); ?>px <?php echo porto_config_value( $b['mainmenu-toplevel-padding1'][ 'padding-' . $left ] ); ?>px;
}
<?php
	$main_menu_level1_abg_color    = $b['mainmenu-toplevel-config-active'] ? $b['mainmenu-toplevel-abg-color'] : $b['mainmenu-toplevel-hbg-color'];
	$main_menu_level1_active_color = $b['mainmenu-toplevel-config-active'] ? $b['mainmenu-toplevel-alink-color'] : $b['mainmenu-toplevel-link-color']['hover'];
?>
#header .main-menu > li.menu-item.active > a {
	<?php if ( $main_menu_level1_abg_color ) : ?>
		background-color: <?php echo esc_html( $main_menu_level1_abg_color ); ?>;
	<?php endif; ?>
	<?php if ( $main_menu_level1_active_color ) : ?>
		color: <?php echo esc_html( $main_menu_level1_active_color ); ?>;
	<?php endif; ?>
}
#header .main-menu > li.menu-item.active:hover > a,
#header .main-menu > li.menu-item:hover > a {
	<?php if ( $b['mainmenu-toplevel-hbg-color'] ) : ?>
		background-color: <?php echo esc_html( $b['mainmenu-toplevel-hbg-color'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['mainmenu-toplevel-link-color']['hover'] ) : ?>
		color: <?php echo esc_html( $b['mainmenu-toplevel-link-color']['hover'] ); ?>;
	<?php endif; ?>
}
#header .main-menu .popup a,
.side-nav-wrap .sidebar-menu .popup,
.main-sidebar-menu .sidebar-menu .popup,
.porto-popup-menu .sub-menu {
	<?php if ( $b['menu-popup-font']['font-family'] ) : ?>
		font-family: <?php echo sanitize_text_field( $b['menu-popup-font']['font-family'] ); ?>, sans-serif;
	<?php endif; ?>
	<?php if ( $b['menu-popup-font']['font-size'] ) : ?>
		font-size: <?php echo esc_html( $b['menu-popup-font']['font-size'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['menu-popup-font']['font-weight'] ) : ?>
		font-weight: <?php echo esc_html( $b['menu-popup-font']['font-weight'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['menu-popup-font']['line-height'] ) : ?>
		line-height: <?php echo esc_html( $b['menu-popup-font']['line-height'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['menu-popup-font']['letter-spacing'] ) : ?>
		letter-spacing: <?php echo esc_html( $b['menu-popup-font']['letter-spacing'] ); ?>;
	<?php endif; ?>
}

<?php
if ( isset( $b['mainmenu-popup-top-border'] ) && isset( $b['mainmenu-popup-top-border']['border-top'] ) ) {
	if ( '0px' == $b['mainmenu-popup-top-border']['border-top'] ) {
		$b['mainmenu-popup-border'] = false;
	} else {
		$b['mainmenu-popup-border']       = $b['mainmenu-popup-top-border']['border-top'];
		$b['mainmenu-popup-border-color'] = $b['mainmenu-popup-top-border']['border-color'];
	}
} elseif ( $b['mainmenu-popup-border'] ) {
	$b['mainmenu-popup-border'] = '3px';
} else {
	$b['mainmenu-popup-border'] = false;
}
?>
<?php if ( $b['mainmenu-popup-border'] ) : ?>
	#header .main-menu .wide .popup { border-top: <?php echo esc_attr( $b['mainmenu-popup-border'] ); ?> solid <?php echo esc_attr( $b['mainmenu-popup-border-color'] ); ?>; }
	#header .sidebar-menu .wide .popup { border-<?php echo empty( $porto_settings['header-side-position'] ) ? 'left' : 'right'; ?>: <?php echo esc_attr( $b['mainmenu-popup-border'] ); ?> solid <?php echo esc_attr( $b['mainmenu-popup-border-color'] ); ?>; }
<?php endif; ?>
<?php if ( ! $b['mainmenu-popup-border'] ) : ?>
	#header .main-menu .wide .popup,
	#header .sidebar-menu .wide .popup {
		border-width: 0;
	}
	<?php if ( $b['border-radius'] ) : ?>
		#header .main-menu .wide .popup > .inner { border-radius: 2px; }
		#header .main-menu .wide.pos-left .popup > .inner { border-radius: 0 2px 2px 2px; }
		#header .main-menu .wide.pos-right .popup > .inner { border-radius: 2px 0 2px 2px; }
	<?php endif; ?>
<?php endif; ?>
<?php if ( $b['mainmenu-popup-heading-color'] ) : ?>
	#header .main-menu .wide li.sub > a,
	.side-nav-wrap .sidebar-menu .wide li.sub > a {
		color: <?php echo esc_html( $b['mainmenu-popup-heading-color'] ); ?>;
	}
<?php endif; ?>
<?php if ( isset( $b['mainmenu-popup-narrow-type'] ) && $b['mainmenu-popup-narrow-type'] && $b['mainmenu-toplevel-hbg-color'] && 'transparent' != $b['mainmenu-toplevel-hbg-color'] ) : ?>
	#header .main-menu .narrow ul.sub-menu {
		background: <?php echo esc_html( $b['mainmenu-toplevel-hbg-color'] ); ?>;
	}
	#header .main-menu .narrow li.menu-item > a {
		color: <?php echo esc_html( $b['mainmenu-toplevel-link-color']['hover'] ); ?>;
		background-color: <?php echo esc_html( $b['mainmenu-toplevel-hbg-color'] ); ?>;
	}
	#header .main-menu .narrow li.menu-item:hover > a {
		background: <?php echo esc_html( $porto_color_lib->lighten( $b['mainmenu-toplevel-hbg-color'], 5 ) ); ?>
	}
	<?php if ( isset( $b['submenu-arrow'] ) && $b['submenu-arrow'] ) : ?>
		#header .main-menu .narrow.has-sub:before,
		#header .main-menu .narrow.has-sub:after { content: none }
	<?php endif; ?>
<?php else : ?>
	#header .main-menu .narrow ul.sub-menu {
		<?php if ( $b['mainmenu-popup-bg-color'] ) : ?>
			background-color: <?php echo esc_html( $b['mainmenu-popup-bg-color'] ); ?>;
		<?php endif; ?>
		<?php if ( $b['mainmenu-popup-border'] ) : ?>
			border-top: <?php echo esc_attr( $b['mainmenu-popup-border'] ); ?> solid <?php echo esc_attr( $b['mainmenu-popup-border-color'] ); ?>;
		<?php endif; ?>
	}
	<?php if ( $b['mainmenu-popup-border'] ) : ?>
		#header .main-menu .narrow li.menu-item:hover > ul.sub-menu { top: <?php echo - 5 - (int) str_replace( 'px', '', $b['mainmenu-popup-border'] ); ?>px; }
	<?php endif; ?>
	#header .main-menu .narrow li.menu-item > a {
		<?php if ( $b['mainmenu-popup-text-color']['regular'] ) : ?>
			color: <?php echo esc_html( $b['mainmenu-popup-text-color']['regular'] ); ?>;
		<?php endif; ?>
		<?php
		if ( $b['mainmenu-popup-bg-color'] && 'transparent' != $b['mainmenu-popup-bg-color'] ) :
			$main_menu_popup_bg_arr = $porto_color_lib->hexToRGB( $b['mainmenu-popup-bg-color'], false );
			if ( ( $main_menu_popup_bg_arr[0] * 256 + $main_menu_popup_bg_arr[1] * 16 + $main_menu_popup_bg_arr[2] ) < ( 79 * 256 + 255 * 16 + 255 ) ) :
				?>
				border-bottom-color: <?php echo esc_html( $porto_color_lib->lighten( $b['mainmenu-popup-bg-color'], 5 ) ); ?>;
			<?php else : ?>
				border-bottom-color: <?php echo esc_html( $porto_color_lib->darken( $b['mainmenu-popup-bg-color'], 5 ) ); ?>;
			<?php endif; ?>
		<?php endif; ?>
	}
	#header .main-menu .narrow li.menu-item:hover > a {
		<?php if ( $b['mainmenu-popup-text-color']['hover'] ) : ?>
			color: <?php echo esc_html( $b['mainmenu-popup-text-color']['hover'] ); ?>;
		<?php endif; ?>
		<?php if ( $b['mainmenu-popup-text-hbg-color'] ) : ?>
			background-color: <?php echo esc_html( $b['mainmenu-popup-text-hbg-color'] ); ?>;
		<?php endif; ?>
	}
<?php endif; ?>

<?php if ( $b['menu-custom-text-color'] ) : ?>
	#header .menu-custom-block,
	#header .menu-custom-block span { color: <?php echo esc_html( $b['menu-custom-text-color'] ); ?>; }
<?php endif; ?>
#header .menu-custom-block span,
#header .menu-custom-block a {
	<?php if ( $b['menu-font']['font-family'] ) : ?>
		font-family: <?php echo sanitize_text_field( $b['menu-font']['font-family'] ); ?>, sans-serif;
	<?php endif; ?>
	<?php if ( $b['menu-font']['font-size'] ) : ?>
		font-size: <?php echo esc_html( $b['menu-font']['font-size'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['menu-font']['font-weight'] ) : ?>
		font-weight: <?php echo esc_html( $b['menu-font']['font-weight'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['menu-font']['line-height'] ) : ?>
		line-height: <?php echo esc_html( $b['menu-font']['line-height'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['menu-font']['letter-spacing'] ) : ?>
		letter-spacing: <?php echo esc_html( $b['menu-font']['letter-spacing'] ); ?>;
	<?php endif; ?>
}
#header .menu-custom-block a {
	<?php if ( $b['menu-text-transform'] ) : ?>
		text-transform: <?php echo esc_html( $b['menu-text-transform'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['menu-custom-link']['regular'] ) : ?>
		color: <?php echo esc_html( $b['menu-custom-link']['regular'] ); ?>;
	<?php endif; ?>
}
<?php if ( $b['menu-custom-link']['hover'] ) : ?>
	#header .menu-custom-block a:hover { color: <?php echo esc_html( $b['menu-custom-link']['hover'] ); ?>; }
<?php endif; ?>

<?php if ( $b['switcher-link-color']['regular'] ) : ?>
	#header .porto-view-switcher > li.menu-item:before,
	#header .porto-view-switcher > li.menu-item > a { color: <?php echo esc_html( $b['switcher-link-color']['regular'] ); ?>; }
<?php endif; ?>
<?php if ( $b['switcher-bg-color'] ) : ?>
	#header .porto-view-switcher > li.menu-item > a { background-color: <?php echo esc_html( $b['switcher-bg-color'] ); ?>; }
<?php endif; ?>
<?php if ( $b['switcher-hbg-color'] ) : ?>
	#header .porto-view-switcher .narrow ul.sub-menu { background: <?php echo esc_html( $b['switcher-hbg-color'] ); ?>; }
<?php endif; ?>
<?php if ( $b['switcher-link-color']['hover'] ) : ?>
	#header .porto-view-switcher .narrow li.menu-item > a { color: <?php echo esc_html( $b['switcher-link-color']['hover'] ); ?>; }
<?php endif; ?>
#header .porto-view-switcher .narrow li.menu-item > a.active,
#header .porto-view-switcher .narrow li.menu-item:hover > a {
	<?php if ( $b['switcher-link-color']['hover'] ) : ?>
		color: <?php echo esc_html( $b['switcher-link-color']['hover'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['switcher-hbg-color'] ) : ?>
		background: <?php echo esc_html( $porto_color_lib->darken( $b['switcher-hbg-color'], 5 ) ); ?>;
	<?php endif; ?>
}
<?php if ( isset( $b['submenu-arrow'] ) && $b['submenu-arrow'] && $b['switcher-hbg-color'] && 'transparent' != $b['switcher-hbg-color'] ) : ?>
	#header .porto-view-switcher > li.has-sub:before,
	#header .porto-view-switcher > li.has-sub:after { border-bottom-color: <?php echo esc_html( $b['switcher-hbg-color'] ); ?>; }
<?php endif; ?>
<?php if ( $b['searchform-text-color'] ) : ?>
	#header .searchform input,
	#header .searchform select,
	#header .searchform button,
	#header .searchform .selectric .label,
	#header .searchform .selectric-items li,
	#header .searchform .selectric-items li:hover,
	#header .searchform .selectric-items li.selected,
	#header .searchform .autocomplete-suggestion .yith_wcas_result_content .title { color: <?php echo esc_html( $b['searchform-text-color'] ); ?> }
	#header .searchform input:-ms-input-placeholder { color: <?php echo esc_html( $b['searchform-text-color'] ); ?> }
	#header .searchform input::-ms-input-placeholder { color: <?php echo esc_html( $b['searchform-text-color'] ); ?> }
	#header .searchform input::placeholder { color: <?php echo esc_html( $b['searchform-text-color'] ); ?> }
<?php endif; ?>
<?php if ( $b['searchform-border-color'] ) : ?>
	<?php if ( 'simple' == $b['search-layout'] ) : ?>
		#header .searchform .searchform-fields,
	<?php endif; ?>
	#header .searchform input,
	#header .searchform select,
	#header .searchform .selectric,
	#header .searchform .selectric-hover .selectric,
	#header .searchform .selectric-open .selectric,
	#header .searchform .autocomplete-suggestions,
	#header .searchform .selectric-items { border-color: <?php echo esc_html( $b['searchform-border-color'] ); ?> }
<?php endif; ?>
<?php if ( $b['searchform-hover-color'] ) : ?>
	#header .searchform button {
		color: <?php echo esc_html( $b['searchform-hover-color'] ); ?>;
	}
<?php endif; ?>
#header .searchform select option,
#header .searchform .autocomplete-suggestion,
#header .searchform .autocomplete-suggestions,
#header .searchform .selectric-items {
	<?php if ( $b['searchform-text-color'] ) : ?>
		color: <?php echo esc_html( $b['searchform-text-color'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['searchform-bg-color'] ) : ?>
		background-color: <?php echo esc_html( $b['searchform-bg-color'] ); ?>;
	<?php endif; ?>
}
<?php if ( $b['searchform-bg-color'] ) : ?>
	#header .searchform .selectric-items li:hover,
	#header .searchform .selectric-items li.selected { background-color: <?php echo esc_html( $porto_color_lib->darken( $b['searchform-bg-color'], 10 ) ); ?> }
	#header .searchform .autocomplete-selected,
	#header .searchform .autocomplete-suggestion:hover { background-color: <?php echo esc_html( $porto_color_lib->darken( $b['searchform-bg-color'], 3 ) ); ?> }
<?php endif; ?>
<?php if ( $b['searchform-popup-border-color'] && 'simple' != $b['search-layout'] ) : ?>
	#header .searchform-popup .search-toggle:after { border-bottom-color: <?php echo esc_html( $b['searchform-popup-border-color'] ); ?>; }
	#header .search-popup .searchform { border-color: <?php echo esc_html( $b['searchform-popup-border-color'] ); ?>; }
	@media (max-width: 991px) {
		#header .searchform { border-color: <?php echo esc_html( $b['searchform-popup-border-color'] ); ?>; }
	}
<?php elseif ( $b['searchform-bg-color'] && 'simple' == $b['search-layout'] ) : ?>
	#header .searchform-popup .search-toggle:after { border-bottom-color: <?php echo esc_html( $b['searchform-bg-color'] ); ?> }
<?php endif; ?>
<?php if ( ! empty( $b['sticky-searchform-toggle-text-color'] ) ) : ?>
	#header.sticky-header .searchform-popup .search-toggle { color: <?php echo esc_html( $b['sticky-searchform-toggle-text-color'] ); ?> }
<?php endif; ?>
<?php if ( ! empty( $b['sticky-searchform-toggle-hover-color'] ) ) : ?>
	#header.sticky-header .searchform-popup .search-toggle:hover { color: <?php echo esc_html( $b['sticky-searchform-toggle-hover-color'] ); ?> }
<?php endif; ?>

#header .mobile-toggle {
	<?php if ( $b['mobile-menu-toggle-text-color'] ) : ?>
		color: <?php echo esc_html( $b['mobile-menu-toggle-text-color'] ); ?>;
	<?php endif; ?>
	background-color: <?php echo empty( $b['mobile-menu-toggle-bg-color'] ) ? $b['skin-color'] : $b['mobile-menu-toggle-bg-color']; ?>;
	<?php if ( 'transparent' == $b['mobile-menu-toggle-bg-color'] ) : ?>
		font-size: 20px;
	<?php elseif ( class_exists( 'Woocommerce' ) ) : ?>
		margin-<?php echo porto_filter_output( $right ); ?>: .5rem
	<?php endif; ?>
}
<?php if ( 'transparent' == $b['mobile-menu-toggle-bg-color'] ) : ?>
	#header .mobile-toggle:first-child { padding-<?php echo porto_filter_output( $left ); ?>: 1px }
<?php endif; ?>

@media <?php echo porto_filter_output( $screen_large ); ?> {
	#header .main-menu-wrap .menu-custom-block .tip {
		<?php echo porto_filter_output( $right ); ?>: <?php echo porto_config_value( $b['mainmenu-toplevel-padding2'][ 'padding-' . $right ] ) - 5; ?>px;
		top: <?php echo porto_config_value( $b['mainmenu-toplevel-padding2']['padding-top'] ) - 15; ?>px;
	}
	#header .main-menu > li.menu-item > a/*,
	#header .menu-custom-block span,
	#header .menu-custom-block a*/ {
		padding: <?php echo porto_config_value( $b['mainmenu-toplevel-padding2']['padding-top'] ); ?>px <?php echo porto_config_value( $b['mainmenu-toplevel-padding2'][ 'padding-' . $right ] ); ?>px <?php echo porto_config_value( $b['mainmenu-toplevel-padding2']['padding-bottom'] ); ?>px <?php echo porto_config_value( $b['mainmenu-toplevel-padding2'][ 'padding-' . $left ] ); ?>px;
	}
<?php if ( $b['menu-block'] ) : ?>
	#header .menu-custom-block span,
	#header .menu-custom-block a {
		padding-right: <?php echo porto_config_value( $b['mainmenu-toplevel-padding2'][ 'padding-' . $right ] ); ?>px;
		padding-left: <?php echo porto_config_value( $b['mainmenu-toplevel-padding2'][ 'padding-' . $left ] ); ?>px;
	}
<?php endif; ?>
}

/* side header */
<?php if ( 'side' == $header_type ) : ?>
	@media (min-width: 992px) {
		.page-wrapper.side-nav:not(.side-nav-right) #mini-cart .cart-popup { <?php echo porto_filter_output( $left ); ?>: 0; <?php echo porto_filter_output( $right ); ?>: auto; }
		.page-wrapper.side-nav.side-nav-right > .header-wrapper { -ms-flex-order: 2; order: 2; }
		.page-wrapper.side-nav.side-nav-right > .header-wrapper.header-side-nav #header { <?php echo porto_filter_output( $left ); ?>: auto; <?php echo porto_filter_output( $right ); ?>: 0; }
		.page-wrapper.side-nav-right > .header-wrapper.header-side-nav #header.initialize { position: fixed; }
		.page-wrapper.side-nav-right .sidebar-menu > li.has-sub:before { <?php echo porto_filter_output( $left ); ?>: auto; <?php echo porto_filter_output( $right ); ?>: 100%; }
		.page-wrapper.side-nav-right .sidebar-menu li.menu-item > a,
		.page-wrapper.side-nav-right .accordion-menu a { text-align: <?php echo porto_filter_output( $right ); ?>; }
		.page-wrapper.side-nav-right .sidebar-menu li.menu-item > a > .thumb-info-preview { <?php echo porto_filter_output( $left ); ?>: auto; }
		.page-wrapper.side-nav-right .sidebar-menu > li.menu-item .popup:before { border-<?php echo porto_filter_output( $left ); ?>: 12px solid #fff; border-<?php echo porto_filter_output( $right ); ?>: none; right: -12px; left: auto; }
		.page-wrapper.side-nav.side-nav-right .sidebar-menu li.menu-item > .arrow { <?php echo porto_filter_output( $right ); ?>: auto; <?php echo porto_filter_output( $left ); ?>: 5px; }
		.page-wrapper.side-nav.side-nav-right .sidebar-menu li.menu-item:hover > .arrow:before { color: #fff; }
		.page-wrapper.side-nav.side-nav-right .sidebar-menu .popup,
		.page-wrapper.side-nav.side-nav-right .sidebar-menu .narrow ul.sub-menu ul.sub-menu { <?php echo porto_filter_output( $left ); ?>: auto; <?php echo porto_filter_output( $right ); ?>: 100%; }
		.page-wrapper.side-nav.side-nav-right .sidebar-menu.subeffect-fadein-<?php echo porto_filter_output( $left ); ?> > li.menu-item .popup,
		.page-wrapper.side-nav.side-nav-right .sidebar-menu.subeffect-fadein-<?php echo porto_filter_output( $left ); ?> .narrow ul.sub-menu li.menu-item > ul.sub-menu { animation: menuFadeInRight 0.2s ease-out; }
		.page-wrapper.side-nav.side-nav-right .sidebar-menu.subeffect-fadein-<?php echo porto_filter_output( $right ); ?> > li.menu-item .popup,
		.page-wrapper.side-nav.side-nav-right .sidebar-menu.subeffect-fadein-<?php echo porto_filter_output( $right ); ?> > .narrow ul.sub-menu li.menu-item > ul.sub-menu { animation: menuFadeInLeft 0.2s ease-out; }
		.page-wrapper.side-nav.side-nav-right #mini-cart .cart-popup { <?php echo porto_filter_output( $left ); ?>: auto; <?php echo porto_filter_output( $right ); ?>: 0; }
	}
<?php endif; ?>

/* sticky header */
<?php if ( isset( $porto_settings['show-sticky-logo'] ) && ! $porto_settings['show-sticky-logo'] ) : ?>
	#header.sticky-header .logo { display: none !important; }
<?php endif; ?>
<?php if ( ! isset( $porto_settings['show-sticky-searchform'] ) || ! $porto_settings['show-sticky-searchform'] ) : ?>
	#header.sticky-header .searchform-popup { display: none !important; }
<?php endif; ?>
<?php if ( ! isset( $porto_settings['show-sticky-minicart'] ) || ! $porto_settings['show-sticky-minicart'] ) : ?>
	html:not(.minicart-opened) .sticky-header #mini-cart { display: none !important; }
<?php endif; ?>
<?php if ( isset( $porto_settings['show-sticky-menu-custom-content'] ) && ! $porto_settings['show-sticky-menu-custom-content'] ) : ?>
	#header.sticky-header .menu-custom-content { display: none !important; }
<?php endif; ?>

/* header type */
<?php if ( (int) $header_type >= 11 && (int) $header_type <= 17 ) : ?>
	@media (min-width: 992px) {
		#header .searchform button { color: <?php echo esc_html( $porto_color_lib->lighten( $b['searchform-text-color'], 43 ) ); ?>; }
	}
<?php endif; ?>
<?php if ( 9 == $header_type ) : ?>
	#header.sticky-header .main-menu-wrap,
	.fixed-header #header.sticky-header .main-menu-wrap { background-color: <?php echo esc_html( $b['mainmenu-wrap-bg-color'] ); ?>; }
<?php elseif ( 11 == $header_type && $b['header-top-border']['border-top'] && '0px' != $b['header-top-border']['border-top'] ) : ?>
	<?php
		$main_menu_level1_abg_color    = $b['mainmenu-toplevel-config-active'] ? $b['mainmenu-toplevel-abg-color'] : $b['mainmenu-toplevel-hbg-color'];
		$main_menu_level1_active_color = $b['mainmenu-toplevel-config-active'] ? $b['mainmenu-toplevel-alink-color'] : $b['mainmenu-toplevel-link-color']['hover'];
	?>
	@media (min-width: 992px) {
		#header .main-menu > li.menu-item { margin-top: -<?php echo esc_html( $b['header-top-border']['border-top'] ); ?>; }
		#header .main-menu > li.menu-item > a { border-top: <?php echo esc_html( $b['header-top-border']['border-top'] ); ?> solid transparent; }
		#header .main-menu > li.menu-item.active > a {
			border-top: <?php echo esc_html( $b['header-top-border']['border-top'] ); ?> solid <?php echo 'transparent' == $main_menu_level1_abg_color ? $main_menu_level1_active_color : $main_menu_level1_abg_color; ?>;
		}
		#header .main-menu > li.menu-item:hover > a {
			border-top: <?php echo esc_html( $b['header-top-border']['border-top'] ); ?> solid <?php echo 'transparent' == $b['mainmenu-toplevel-hbg-color'] ? $b['mainmenu-toplevel-link-color']['hover'] : $b['mainmenu-toplevel-hbg-color']; ?>;
		}
	}
<?php elseif ( 18 == $header_type ) : ?>
	#header .searchform-popup .search-toggle { color: <?php echo esc_html( $skin_color ); ?>; }
	#header .mobile-toggle { margin-<?php echo porto_filter_output( $right ); ?>: .5rem }

<?php elseif ( porto_header_type_is_side() ) : ?>
	<?php if ( ! $b['side-social-bg-color'] || 'transparent' == $b['side-social-bg-color'] ) : ?>
		#header .share-links a { background-color: transparent; color: <?php echo esc_html( $b['side-social-color'] ); ?>; }
	<?php else : ?>
		#header .share-links a:not(:hover) { background-color: <?php echo esc_html( $b['side-social-bg-color'] ); ?>; color: <?php echo esc_html( $b['side-social-color'] ); ?>; }
	<?php endif; ?>
	.header-wrapper #header .header-copyright { color: <?php echo esc_html( $b['side-copyright-color'] ); ?>; }

	@media (min-width: 992px) {
		.header-wrapper #header .header-main { position: static; background: transparent; }
		<?php if ( $b['mainmenu-toplevel-hbg-color'] ) : ?>
		.header-wrapper #header .top-links li.menu-item > a { border-top-color: <?php echo esc_html( $porto_color_lib->lighten( $b['mainmenu-toplevel-hbg-color'], 5 ) ); ?>; }
		<?php endif; ?>
		.header-wrapper #header .top-links li.menu-item > a,
		.header-wrapper #header .top-links li.menu-item.active > a { color: <?php echo esc_html( $b['mainmenu-toplevel-link-color']['regular'] ); ?>; }
		.header-wrapper #header .top-links li.menu-item:hover,
		.header-wrapper #header .top-links li.menu-item.active:hover { background: <?php echo esc_html( $b['mainmenu-toplevel-hbg-color'] ); ?>; }
		.header-wrapper #header .top-links li.menu-item:hover > a,
		.header-wrapper #header .top-links li.menu-item.active:hover > a { color: <?php echo esc_html( $b['mainmenu-toplevel-link-color']['hover'] ); ?>; }
	}
<?php endif; ?>

/* mini cart */
.cart-popup .quantity, .cart-popup .quantity .amount { color: #696969 !important; }
<?php if ( isset( $b['minicart-icon-font-size'] ) && $b['minicart-icon-font-size'] ) : ?>
	<?php
		$unit = trim( preg_replace( '/[0-9.]/', '', $b['minicart-icon-font-size'] ) );
	if ( ! $unit ) {
		$b['minicart-icon-font-size'] .= 'px';
	}
	?>
	#mini-cart .minicart-icon { font-size: <?php echo esc_html( $b['minicart-icon-font-size'] ); ?> }
<?php endif; ?>
<?php if ( isset( $b['minicart-popup-border-color'] ) && $b['minicart-popup-border-color'] ) : ?>
	<?php if ( 'transparent' != $b['minicart-popup-border-color'] ) : ?>
		#mini-cart .cart-popup { border: 1px solid <?php echo esc_html( $b['minicart-popup-border-color'] ); ?>; }
	<?php endif; ?>
	#mini-cart .cart-icon:after { border-color: <?php echo esc_html( $b['minicart-popup-border-color'] ); ?>; }
<?php endif; ?>
<?php if ( isset( $b['sticky-minicart-popup-border-color'] ) && $b['sticky-minicart-popup-border-color'] ) : ?>
	<?php if ( 'transparent' != $b['sticky-minicart-popup-border-color'] ) : ?>
		.sticky-header #mini-cart .cart-popup { border: 1px solid <?php echo esc_html( $b['sticky-minicart-popup-border-color'] ); ?>; }
	<?php endif; ?>
	.sticky-header #mini-cart .cart-icon:after { border-color: <?php echo esc_html( $b['sticky-minicart-popup-border-color'] ); ?>; }
<?php endif; ?>
<?php if ( isset( $b['minicart-bg-color'] ) && $b['minicart-bg-color'] ) : ?>
	#mini-cart {
		background: <?php echo esc_html( $b['minicart-bg-color'] ); ?>;
	<?php if ( $b['border-radius'] ) : ?>
		border-radius: 4px
	<?php endif; ?>
	}
<?php endif; ?>
<?php if ( isset( $b['sticky-minicart-bg-color'] ) && $b['sticky-minicart-bg-color'] ) : ?>
	.sticky-header #mini-cart {
		background: <?php echo esc_html( $b['sticky-minicart-bg-color'] ); ?>;
	<?php if ( $b['border-radius'] ) : ?>
		border-radius: 4px
	<?php endif; ?>
	}
<?php endif; ?>
<?php if ( ! empty( $b['minicart-icon-color'] ) ) : ?>
	#mini-cart .cart-subtotal, #mini-cart .minicart-icon { color: <?php echo esc_html( $b['minicart-icon-color'] ); ?> }
<?php endif; ?>
<?php if ( ! empty( $b['minicart-item-color'] ) ) : ?>
	#mini-cart .cart-items, #mini-cart .cart-items-text { color: <?php echo esc_html( $b['minicart-item-color'] ); ?> }
<?php endif; ?>
<?php if ( ! empty( $b['sticky-minicart-icon-color'] ) ) : ?>
	.sticky-header #mini-cart .cart-subtotal,
	.sticky-header #mini-cart .minicart-icon,
	.sticky-header #mini-cart.minicart-arrow-alt .cart-head:after { color: <?php echo esc_html( $b['sticky-minicart-icon-color'] ); ?> }
<?php endif; ?>
<?php if ( ! empty( $b['sticky-minicart-item-color'] ) ) : ?>
	.sticky-header #mini-cart .cart-items,
	.sticky-header #mini-cart .cart-items-text { color: <?php echo esc_html( $b['sticky-minicart-item-color'] ); ?> }
<?php endif; ?>

/* mobile panel */
<?php
	$panel_link_color = empty( $b['panel-link-color']['regular'] ) ? ( 'side' == $b['mobile-panel-type'] ? '#fff' : '#333' ) : $b['panel-link-color']['regular'];
?>
<?php if ( ! empty( $b['panel-bg-color'] ) ) : ?>
	<?php
	if ( 'side' == $b['mobile-panel-type'] ) :
		echo '#side-nav-panel';
	else :
		echo '#nav-panel .mobile-nav-wrap';
	endif;
	?> { background-color: <?php echo esc_html( $b['panel-bg-color'] ); ?>; }
	<?php
	if ( 'side' == $b['mobile-panel-type'] ) :
		echo '#side-nav-panel .accordion-menu li.menu-item.active > a,';
		echo '#side-nav-panel .menu-custom-block a:hover';
	else :
		echo '#nav-panel .menu-custom-block a:hover';
	endif;
	?> { background-color: <?php echo esc_html( $porto_color_lib->lighten( $b['panel-bg-color'], 5 ) ); ?>; }
<?php endif; ?>
<?php if ( ! empty( $b['panel-text-color'] ) ) : ?>
	<?php
	if ( 'side' == $b['mobile-panel-type'] ) :
		echo '#side-nav-panel, #side-nav-panel .welcome-msg, #side-nav-panel .accordion-menu, #side-nav-panel .menu-custom-block, #side-nav-panel .menu-custom-block span';
	else :
		echo '#nav-panel, #nav-panel .welcome-msg, #nav-panel .accordion-menu, #nav-panel .menu-custom-block, #nav-panel .menu-custom-block span';
	endif;
	?> { color: <?php echo esc_html( $b['panel-text-color'] ); ?>; }
<?php endif; ?>
<?php if ( $b['panel-border-color'] ) : ?>
	<?php
	if ( 'side' == $b['mobile-panel-type'] ) :
		echo '#side-nav-panel .accordion-menu li:not(:last-child)';
	else :
		echo '#nav-panel .accordion-menu li';
	endif;
	?>
	{ border-bottom-color: <?php echo esc_html( $b['panel-border-color'] ); ?>; }
<?php endif; ?>
<?php if ( empty( $b['mobile-panel-type'] ) ) : ?>
	#nav-panel .accordion-menu .sub-menu li:not(.active):hover > a { background: <?php echo esc_html( $b['panel-link-hbgcolor'] ); ?>; }
	#nav-panel .accordion-menu li.menu-item > a,
	#nav-panel .accordion-menu .arrow,
	#nav-panel .menu-custom-block a { color: <?php echo esc_html( $panel_link_color ); ?>; }
	#nav-panel .accordion-menu > li.menu-item > a,
	#nav-panel .accordion-menu > li.menu-item > .arrow { color: <?php echo esc_html( $skin_color ); ?>; }
	#nav-panel .accordion-menu li.menu-item.active > a { background-color: <?php echo esc_html( $skin_color ); ?> }
	#nav-panel .mobile-nav-wrap::-webkit-scrollbar-thumb { background: <?php echo porto_if_light( 'rgba(204, 204, 204, 0.5)', '#39404c' ); ?>;<?php echo porto_if_dark( 'border-color: transparent', '' ); ?> }

	<?php if ( ! empty( $porto_settings_optimize['lazyload_menu'] ) ) : ?>
		#nav-panel .mobile-nav-wrap:empty { min-height: 200px; }
	<?php endif; ?>
<?php elseif ( 'side' == $b['mobile-panel-type'] ) : ?>
	#side-nav-panel .accordion-menu li.menu-item > a,
	#side-nav-panel .menu-custom-block a { color: <?php echo esc_html( $panel_link_color ); ?>; }
	#side-nav-panel::-webkit-scrollbar { width: 5px; }
	#side-nav-panel::-webkit-scrollbar-thumb { border-radius: 0px; background: <?php echo porto_if_light( 'rgba(204, 204, 204, 0.5)', '#39404c' ); ?>;<?php echo porto_if_dark( 'border-color: transparent', '' ); ?> }
	<?php
		$mobile_panel_pos = isset( $b['mobile-panel-pos'] ) && $b['mobile-panel-pos'] ? ( $rtl ? ( 'panel-left' == $b['mobile-panel-pos'] ? 'right' : 'left' ) : str_replace( 'panel-', '', $b['mobile-panel-pos'] ) ) : $left;
	?>
	.page-wrapper,
	#header.sticky-header .header-main.sticky { transition: <?php echo porto_filter_output( $mobile_panel_pos ); ?> .3s }
	html.panel-opened .page-wrapper,
	html.panel-opened #header.sticky-header .header-main.sticky,
	html.sidebar-opened #header.sticky-header .header-main.sticky,
	.filter-sidebar-opened #header.sticky-header .header-main.sticky { <?php echo porto_filter_output( $mobile_panel_pos ); ?>: 260px; <?php echo 'left' == $mobile_panel_pos ? 'right' : 'left'; ?>: auto }

	<?php if ( isset( $b['mobile-panel-add-search'] ) && $b['mobile-panel-add-search'] ) : ?>
		@media (max-width: 991px) {
			#side-nav-panel .searchform select { display: none; }
			#side-nav-panel .searchform { padding: 0 16px; }
			#side-nav-panel .searchform-fields {
				display: -ms-flexbox; display: flex; -ms-flex-align: center; align-items: center;
				background: <?php echo esc_html( $porto_color_lib->lighten( $b['panel-bg-color'], 5 ) ); ?>;
			}
			#side-nav-panel .searchform .text { -ms-flex: 1; flex: 1; }
			#side-nav-panel .searchform input[type="text"] { width: 100%; height: 38px; background: none; border: none; font-size: inherit; }
			#side-nav-panel .searchform .btn { background: none; border: none; box-shadow: none; padding: .625rem .875rem; color: <?php echo esc_html( $panel_link_color ); ?> }
		}
		@media (max-width: 575px) {
			#header .searchform-popup { display: none; }
		}
	<?php endif; ?>
<?php endif; ?>

<?php if ( ! empty( $b['panel-link-color']['hover'] ) ) : ?>
	<?php if ( empty( $b['mobile-panel-type'] ) ) : ?>
		#nav-panel .accordion-menu li.menu-item:hover > a,
		#nav-panel .accordion-menu .arrow:hover,
		#nav-panel .menu-custom-block a:hover { color: <?php echo esc_html( $b['panel-link-color']['hover'] ); ?>; }
	<?php else : ?>
		#side-nav-panel .accordion-menu li.menu-item.active > a,
		#side-nav-panel .menu-custom-block a:hover { color: <?php echo esc_html( $b['panel-link-color']['hover'] ); ?>; }
	<?php endif; ?>
<?php endif; ?>
.fixed-header #nav-panel .mobile-nav-wrap { padding: 15px !important; }

/* portfolio */
.single-portfolio .related-portfolios { background-color: <?php echo porto_if_light( '#f7f7f7', $color_dark_2 ); ?> }

/* footer */
.footer-wrapper.fixed #footer .footer-bottom {
<?php if ( empty( $b['footer-bottom-bg']['background-color'] ) ) : ?>
<?php elseif ( 'transparent' == $b['footer-bottom-bg']['background-color'] ) : ?>
	box-shadow: none;
<?php else : ?>
	background-color: rgba(<?php echo esc_html( $porto_color_lib->hexToRGB( $b['footer-bottom-bg']['background-color'] ) ); ?>, <?php echo esc_html( $footer_opacity ); ?>);
<?php endif; ?>
}
<?php if ( class_exists( 'Woocommerce' ) ) : ?>
	#footer .footer-main > .container { padding-top: 4rem; padding-bottom: 1.5rem; }
<?php endif; ?>
<?php if ( ! empty( $b['footer-label-color'] ) ) : ?>
	#footer .contact-details strong { color: <?php echo esc_html( $b['footer-label-color'] ); ?> }
<?php endif; ?>
<?php if ( ! empty( $b['footer-bottom-text-color'] ) ) : ?>
	#footer .footer-bottom,
	#footer .footer-bottom p,
	#footer .footer-bottom .widget > div > ul li,
	#footer .footer-bottom .widget > ul li { color: <?php echo esc_html( $b['footer-bottom-text-color'] ); ?> }
<?php endif; ?>

/* skin color */
<?php
	// Color Variables
	$theme_colors         = array(
		'primary'    => $skin_color,
		'secondary'  => $b['secondary-color'],
		'tertiary'   => $b['tertiary-color'],
		'quaternary' => $b['quaternary-color'],
		'dark'       => $b['dark-color'],
		'light'      => $b['light-color'],
	);
	$theme_colors_inverse = array(
		'primary'    => $b['skin-color-inverse'],
		'secondary'  => $b['secondary-color-inverse'],
		'tertiary'   => $b['tertiary-color-inverse'],
		'quaternary' => $b['quaternary-color-inverse'],
		'dark'       => $b['dark-color-inverse'],
		'light'      => $b['light-color-inverse'],
	);

	?>
body,
ul.list.icons li a,
.pricing-table li,
.pricing-table h3 .desc,
.pricing-table .price,
.pricing-table .plan,
.home-intro .get-started a:not(.btn),
.color-body,
.color-body a,
.color-body a:hover,
.color-body a:focus,
.mobile-sidebar .sidebar-toggle,
.page-top .product-nav .product-popup,
.thumb-info-bottom-info .thumb-info-title,
.thumb-info-bottom-info .thumb-info-title a,
.thumb-info-bottom-info .thumb-info-title a:hover,
.tabs.tabs-simple .nav-tabs > li .nav-link,
.porto-links-block li.porto-links-item > a,
.porto-links-block li.porto-links-item > span,
.vc_general.vc_cta3.vc_cta3-color-white.vc_cta3-style-flat,
.mega-menu .wide .popup,
.mega-menu .wide li.menu-item li.menu-item > a,
.sidebar-menu .popup,
.testimonial.testimonial-style-2 blockquote,
.testimonial.testimonial-style-3 blockquote,
.testimonial.testimonial-style-4 blockquote,
.testimonial.testimonial-style-5 blockquote,
.testimonial.testimonial-style-6 blockquote,
.testimonial.testimonial-with-quotes blockquote,
.sort-source-style-3 > li > a,
.widget_shopping_cart {
	color: <?php echo esc_html( $body_color ); ?>;
}

.widget_recent_entries li,
.widget_recent_comments li,
.widget_pages li,
.widget_meta li,
.widget_nav_menu li,
.widget_archive li,
.widget_categories li,
.widget_rss li {
	color: <?php echo esc_html( $porto_color_lib->darken( $body_color, 6.67 ) ); ?>;
}

.widget .rss-date,
.widget .post-date,
.widget .comment-author-link {
	color: <?php echo esc_html( $porto_color_lib->lighten( $body_color, 6.67 ) ); ?>;
}

.alternative-font,
a,
.color-primary,
article.post .post-title,
ul.list.icons li i,
ul.list.icons li a:hover,
.list.list-icons li i,
.list.list-ordened li:before,
ul[class^="wsp-"] li:before,
.fontawesome-icon-list > div:hover,
.sample-icon-list > div:hover,
.fontawesome-icon-list > div:hover .text-muted,
.sample-icon-list > div:hover .text-muted,
.accordion .card-header a,
.accordion .card-header a i,
section.toggle label,
.porto-concept strong,
.fc-slideshow nav .fc-left i,
.fc-slideshow nav .fc-right i,
.circular-bar.only-icon .fas,
.circular-bar.only-icon .fab,
.circular-bar.only-icon .far,
.home-intro p em,
.home-intro.light p,
.featured-box .porto-sicon-header h3.porto-sicon-title,
.featured-box .wpb_heading,
.feature-box[class*="feature-box-style-"] .feature-box-icon i,
.mobile-sidebar .sidebar-toggle:hover,
.page-top .sort-source > li.active > a,
.owl-carousel.nav-style-1 .owl-nav [class*="owl-"],
.master-slider .ms-container .ms-nav-prev,
.master-slider .ms-container .ms-nav-next,
.master-slider .ms-container .ms-slide-vpbtn,
.master-slider .ms-container .ms-video-btn,
.resp-tabs-list li,
h2.resp-accordion,
.tabs ul.nav-tabs .nav-link,
.wpb_wrapper .porto-sicon-read,
.vc_custom_heading em,
.widget .widget-title a:hover,
.widget .widgettitle a:hover,
.widget li > a:hover,
.widget li.active > a,
.widget_wysija_cont .showerrors,
.sidebar-menu > li.menu-item.active > a,
article.post .post-date .day,
.post-item .post-date .day,
section.timeline .timeline-date h3,
.post-carousel .post-item.style-5 .cat-names,
.post-grid .post-item.style-5 .cat-names,
.post-timeline .post-item.style-5 .cat-names,
.post-carousel .post-item.style-5 .post-meta .post-views-icon.dashicons,
.post-grid .post-item.style-5 .post-meta .post-views-icon.dashicons,
.post-timeline .post-item.style-5 .post-meta .post-views-icon.dashicons,
.portfolio-info ul li a:hover,
article.member .member-role,
.tm-extra-product-options .tm-epo-field-label,
.tm-extra-product-options-totals .amount.final,
html #topcontrol:hover,
.single-post .entry-title,
.sort-source-style-3 > li.active > a,
ul.portfolio-details h5,
.page-not-found h4,
article.post .sticky-post,
.products-slider.owl-carousel .owl-dot:hover span, .products-slider.owl-carousel .owl-dot.active span,
.owl-carousel.dots-style-1 .owl-dot.active span,
.owl-carousel.dots-style-1 .owl-dot:hover span,
.label-pre-order {
	color: <?php echo esc_html( $skin_color ); ?>;
}

a:hover,
.wpb_wrapper .porto-sicon-read:hover { color: <?php echo esc_html( $porto_color_lib->lighten( $skin_color, 5 ) ); ?>; }

a:active, a:focus { color: <?php echo esc_html( $porto_color_lib->darken( $skin_color, 5 ) ); ?>; }

.slick-slider .slick-dots li.slick-active i,
.slick-slider .slick-dots li:hover i {
	color: <?php echo esc_html( $porto_color_lib->darken( $skin_color, 6 ) ); ?> !important;
}

.list.list-icons li i,
.list.list-ordened li:before,
<?php if ( ! $dark ) : ?>
	.pricing-table .most-popular,
<?php endif; ?>
section.toggle.active > label,
.timeline-balloon .balloon-time .time-dot:before,
.featured-box .icon-featured:after,
.feature-box.feature-box-style-3 .feature-box-icon,
.owl-carousel.dots-color-primary .owl-dots .owl-dot,
.master-slider .ms-slide .ms-slide-loading:before,
.widget_sidebar_menu .widget-title .toggle:hover,
.pagination span.current,
.page-links span.current,
.porto-process .process-step-circle {
	border-color: <?php echo esc_html( $skin_color ); ?>;
}

.products-slider.owl-carousel .owl-dot span {
	color: rgba(<?php echo esc_html( $porto_color_lib->hexToRGB( $porto_color_lib->darken( $skin_color, 20 ) ) ); ?>, .4);
}

section.toggle label,
.resp-vtabs .resp-tabs-list li:hover,
.resp-vtabs .resp-tabs-list li:focus,
.resp-vtabs .resp-tabs-list li.resp-tab-active,
.sidebar-menu .wide .popup,
.wp-block-pullquote blockquote {
	border-<?php echo porto_filter_output( $left ); ?>-color: <?php echo esc_html( $skin_color ); ?>;
}

.tabs.tabs-vertical.tabs-left li .nav-link:hover,
.tabs.tabs-vertical.tabs-left li.active .nav-link {
	border-left-color: <?php echo esc_html( $skin_color ); ?>;
}

.thumb-info-ribbon:before,
.right-sidebar .sidebar-menu .wide .popup {
	border-<?php echo porto_filter_output( $right ); ?>-color: <?php echo esc_html( $porto_color_lib->darken( $skin_color, 15 ) ); ?>;
}

.tabs.tabs-vertical.tabs-right li .nav-link:hover,
.tabs.tabs-vertical.tabs-right li.active .nav-link {
	border-right-color: <?php echo esc_html( $skin_color ); ?>;
}

.porto-history .featured-box .box-content,
body.boxed .page-wrapper,
.master-slider .ms-loading-container .ms-loading:before,
.master-slider .ms-slide .ms-slide-loading:before,
#fancybox-loading:before,
#fancybox-loading:after,
.slick-slider .slick-loading .slick-list:before,
.fullscreen-carousel > .owl-carousel:before,
.fullscreen-carousel > .owl-carousel:after,
.porto-loading-icon,
.resp-tabs-list li:hover,
.resp-tabs-list li:focus,
.resp-tabs-list li.resp-tab-active,
.tabs ul.nav-tabs .nav-link:hover,
.tabs ul.nav-tabs li.active .nav-link,
.sidebar-content .widget.widget_wysija .box-content,
.mega-menu .wide .popup,
.sidebar-menu > li.menu-item:hover > a,
.sort-source-style-2 > li.active > a:after {
	border-top-color: <?php echo esc_html( $skin_color ); ?>;
}

.testimonial .testimonial-arrow-down {
	border-top-color: <?php echo esc_html( $porto_color_lib->lighten( $skin_color, 5 ) ); ?>;
}

.tabs.tabs-bottom .nav-tabs li .nav-link:hover { border-bottom-color: <?php echo esc_html( $skin_color ); ?>; }
.tabs.tabs-bottom .nav-tabs li.active .nav-link { border-bottom: 3px solid <?php echo esc_html( $skin_color ); ?>; border-top-color: transparent !important; }

.page-top .product-nav .product-popup:before,
.tabs.tabs-simple .nav-tabs > li .nav-link:hover,
.tabs.tabs-simple .nav-tabs > li .nav-link:focus,
.tabs.tabs-simple .nav-tabs > li.active .nav-link,
.sort-source-style-3 > li.active > a {
	border-bottom-color: <?php echo esc_html( $skin_color ); ?>;
}

article.post .post-date .month,
article.post .post-date .format,
.post-item .post-date .month,
.post-item .post-date .format,
.list.list-icons.list-icons-style-3 li i,
.list.list-ordened.list-ordened-style-3 li:before,
html .list-primary.list-ordened.list-ordened-style-3 li:before,
html .list-secondary.list-ordened.list-ordened-style-3 li:before,
html .list-tertiary.list-ordened.list-ordened-style-3 li:before,
html .list-quaternary.list-ordened.list-ordened-style-3 li:before,
html .list-dark.list-ordened.list-ordened-style-3 li:before,
html .list-light.list-ordened.list-ordened-style-3 li:before,
ul.nav-pills > li.active > a,
ul.nav-pills > li.active > a:hover,
ul.nav-pills > li.active > a:focus,
section.toggle.active > label,
.toggle-simple section.toggle > label:after,
div.wpb_single_image .porto-vc-zoom .zoom-icon,
.img-thumbnail .zoom,
.thumb-info .zoom,
.img-thumbnail .link,
.thumb-info .link,
.pricing-table .most-popular h3,
.pricing-table-flat .plan h3,
.pricing-table-classic .most-popular h3 strong,
.timeline-balloon .balloon-time .time-dot:after,
section.exp-timeline .timeline-box.right:after,
.floating-menu .floating-menu-btn-collapse-nav,
.icon-featured,
.featured-box .icon-featured,
.featured-box-effect-3:hover .icon-featured,
.feature-box .feature-box-icon,
.inverted,
.master-slider .ms-container .ms-bullet,
.share-links a,
.thumb-info .thumb-info-type,
.thumb-info .thumb-info-action-icon,
.thumb-info-ribbon,
.thumb-info-social-icons a,
.widget_sidebar_menu .widget-title .toggle:hover,
.mega-menu:not(:hover) > li.menu-item.active > a,
.mega-menu:not(:hover) > li.menu-item:hover > a,
.mega-menu .narrow ul.sub-menu,
.sidebar-menu > li.menu-item:hover,
.sidebar-menu .menu-custom-block a:hover,
.pagination span.current,
.page-links span.current,
.member-item.member-item-3 .thumb-info:hover .thumb-info-caption,
.sort-source-style-2,
.mega-menu.menu-hover-line > li.menu-item > a:before,
.blog-posts-hover_info2 .thumb-info-type a
<?php if ( class_exists( 'WeDevs_Dokan' ) ) : ?>
,.dokan-dashboard .dokan-dash-sidebar ul.dokan-dashboard-menu li.active
,.dokan-dashboard .dokan-dash-sidebar ul.dokan-dashboard-menu li:hover
<?php endif; ?>
<?php if ( defined( 'ELEMENTOR_VERSION' ) ) : ?>
	,.swiper-pagination-bullet-active
<?php endif; ?>
{
	background-color: <?php echo esc_html( $skin_color ); ?>;
}

div.wpb_single_image .porto-vc-zoom .zoom-icon:hover,
.img-thumbnail .zoom:hover,
.thumb-info .zoom:hover,
.img-thumbnail .link:hover,
.thumb-info .link:hover,
.mega-menu .narrow li.menu-item:hover > a,
.testimonial blockquote {
	background-color: <?php echo esc_html( $porto_color_lib->lighten( $skin_color, 5 ) ); ?>;
}

.owl-carousel .owl-dots .owl-dot.active span,
.owl-carousel .owl-dots .owl-dot:hover span {
	background-color: <?php echo esc_html( $porto_color_lib->darken( $skin_color, 6 ) ); ?>;
}
<?php if ( class_exists( 'Woocommerce' ) ) : ?>
.products-slider.owl-carousel .owl-dot:hover span, .products-slider.owl-carousel .owl-dot.active span {
	background: none;
}
<?php endif; ?>

.featured-box-effect-2 .icon-featured:after { box-shadow: 0 0 0 3px <?php echo esc_html( $skin_color ); ?>; }
.featured-box-effect-3 .icon-featured:after { box-shadow: 0 0 0 10px <?php echo esc_html( $skin_color ); ?>; }

section.toggle.active > label,
.pricing-table .most-popular h3,
.pricing-table .most-popular h3 .desc,
.pricing-table-flat .plan h3,
.pricing-table-flat .plan h3 .desc,
.pricing-table-flat .price,
ul.nav-pills > li.active > a,
ul.nav-pills > li.active > a:hover,
ul.nav-pills > li.active > a:focus,
.tparrows.tparrows-carousel.tp-leftarrow:before,
.tparrows.tparrows-carousel.tp-rightarrow:before,
.thumb-info .thumb-info-action-icon i,
.thumb-info-ribbon,
.thumb-info-social-icons a i,
.portfolio-item .thumb-info .thumb-info-type .portfolio-like i,
.portfolio-item .thumb-info .thumb-info-type .portfolio-liked i,
.member-item.member-item-3 .thumb-info:hover .thumb-info-caption,
.member-item.member-item-3 .thumb-info:hover .thumb-info-caption * {
	color: <?php echo esc_html( $b['skin-color-inverse'] ); ?>;
}

.member-item.member-item-3 .thumb-info:hover .thumb-info-social-icons {
	border-color: <?php echo esc_html( $b['skin-color-inverse'] ); ?>;
}

.member-item.member-item-3 .thumb-info:hover .share-links a {
	background-color: <?php echo esc_html( $b['skin-color-inverse'] ); ?>;
	color: <?php echo esc_html( $skin_color ); ?>;
}

@media (min-width: 992px) {
	.floating-menu .floating-menu-nav-main nav > ul > li > a:after { background-color: <?php echo esc_html( $skin_color ); ?>; }
}

/* secondary color */
<?php if ( $b['secondary-color'] ) : ?>
	.post-carousel .post-item.style-5 .post-meta a,
	.post-grid .post-item.style-5 .post-meta a,
	.post-timeline .post-item.style-5 .post-meta a {
		color: <?php echo esc_html( $b['secondary-color'] ); ?>;
	}
<?php endif; ?>

/* quaternary color */
<?php if ( $b['quaternary-color'] ) : ?>
	.post-share-advance-bg,
	.post-share-advance .fa-share {
		background: <?php echo esc_html( $b['quaternary-color'] ); ?>;
	}
<?php endif; ?>

/* dark color */
<?php if ( $b['dark-color'] ) : ?>
	section.exp-timeline .timeline-bar,
	.blog-posts .blog-post-item .meta-cats a { background-color: <?php echo esc_html( $b['dark-color'] ); ?>; }

	section.exp-timeline .timeline-box.right:before {
		background-color: <?php echo esc_html( $b['dark-color'] ); ?> !important;
		box-shadow: 0 0 0 3px #ecf1f7, 0 0 0 6px <?php echo esc_html( $b['dark-color'] ); ?> !important;
	}
<?php endif; ?>
.pricing-table-classic .price,
.product-summary-wrap .price,
.product-summary-wrap .product_meta span span, .product-summary-wrap .product_meta span a { color: <?php echo porto_if_light( $color_dark, '#fff' ); ?>; }

.product-thumbs-slider.owl-carousel .owl-item.selected .img-thumbnail,
html:not(.touch) .product-thumbs-slider .owl-item:hover .img-thumbnail {
	border: 2px solid <?php echo porto_if_light( $color_dark, '#fff' ); ?>;
}

/* misc */
.section-primary .read-more, .section-primary .read-more-block { color: <?php echo esc_html( $b['skin-color-inverse'] ); ?>; }
<?php foreach ( $theme_colors as $key => $theme_color ) : ?>
	html .list-<?php echo porto_filter_output( $key ); ?>.list-icons li i,
	html .list-<?php echo porto_filter_output( $key ); ?>.list-ordened li:before,
	html ul.nav-pills-<?php echo porto_filter_output( $key ); ?> a,
	html .toggle-<?php echo porto_filter_output( $key ); ?> .toggle label,
	html .divider.divider-<?php echo porto_filter_output( $key ); ?> i,
	.featured-box-<?php echo porto_filter_output( $key ); ?> h4,
	.featured-box-effect-7.featured-box-<?php echo porto_filter_output( $key ); ?> .icon-featured:before,
	.has-<?php echo porto_filter_output( $key ); ?>-color { color: <?php echo esc_html( $theme_color ); ?>; }

	html .heading-<?php echo porto_filter_output( $key ); ?>,
	html .lnk-<?php echo porto_filter_output( $key ); ?>,
	html .text-color-<?php echo porto_filter_output( $key ); ?> { color: <?php echo esc_html( $theme_color ); ?> !important; }

	html ul.nav-pills-<?php echo porto_filter_output( $key ); ?> a:hover,
	html ul.nav-pills-<?php echo porto_filter_output( $key ); ?> a:focus { color: <?php echo esc_html( $porto_color_lib->lighten( $theme_color, 5 ) ); ?>; }
	html ul.nav-pills-<?php echo porto_filter_output( $key ); ?> a:active { color: <?php echo esc_html( $porto_color_lib->darken( $theme_color, 5 ) ); ?>; }

	html .list-<?php echo porto_filter_output( $key ); ?>.list-icons.list-icons-style-3 li i,
	html ul.nav-pills-<?php echo porto_filter_output( $key ); ?> > li.active > a,
	html ul.nav-pills-<?php echo porto_filter_output( $key ); ?> > li.active > a:hover,
	html ul.nav-pills-<?php echo porto_filter_output( $key ); ?> > li.active > a:focus,
	html .toggle-<?php echo porto_filter_output( $key ); ?> .toggle.active > label,
	html .toggle-<?php echo porto_filter_output( $key ); ?>.toggle-simple .toggle > label:after,
	html .label-<?php echo porto_filter_output( $key ); ?>,
	html .alert-<?php echo porto_filter_output( $key ); ?>,
	html .divider.divider-<?php echo porto_filter_output( $key ); ?>.divider-small hr,
	html .divider.divider-style-2.divider-<?php echo porto_filter_output( $key ); ?> i,
	.pricing-table .plan-<?php echo porto_filter_output( $key ); ?> h3,
	.pricing-table-classic .plan-<?php echo porto_filter_output( $key ); ?> h3 strong,
	html .home-intro-<?php echo porto_filter_output( $key ); ?>,
	.feature-box .feature-box-icon-<?php echo porto_filter_output( $key ); ?>,
	.featured-box-<?php echo porto_filter_output( $key ); ?> .icon-featured,
	html .inverted-<?php echo porto_filter_output( $key ); ?>,
	html .thumb-info .thumb-info-action-icon-<?php echo porto_filter_output( $key ); ?>,
	html .thumb-info .thumb-info-action-icon-<?php echo porto_filter_output( $key ); ?>:hover,
	html .thumb-info-ribbon-<?php echo porto_filter_output( $key ); ?>,
	html .thumb-info-social-icons a.thumb-info-social-links-<?php echo porto_filter_output( $key ); ?>,
	.has-<?php echo porto_filter_output( $key ); ?>-background-color { background-color: <?php echo esc_html( $theme_color ); ?>; }

	html .accordion.accordion-<?php echo porto_filter_output( $key ); ?> .card-header,
	html .section.section-<?php echo porto_filter_output( $key ); ?>,
	html .popover-<?php echo porto_filter_output( $key ); ?> .popover-title,
	html .background-color-<?php echo porto_filter_output( $key ); ?>,
	.featured-box-effect-3.featured-box-<?php echo porto_filter_output( $key ); ?>:hover .icon-featured { background-color: <?php echo esc_html( $theme_color ); ?> !important; }
	html .section.section-<?php echo porto_filter_output( $key ); ?>-scale-2 { background-color: <?php echo esc_html( $porto_color_lib->darken( $theme_color, 10 ) ); ?> !important; }

	html .list-<?php echo porto_filter_output( $key ); ?>.list-icons.list-icons-style-3 li i,
	html .list-<?php echo porto_filter_output( $key ); ?>.list-ordened.list-ordened-style-3 li:before,
	html .accordion.accordion-<?php echo porto_filter_output( $key ); ?> .card-header a,
	html .toggle-<?php echo porto_filter_output( $key ); ?> .toggle.active > label,
	html .alert-<?php echo porto_filter_output( $key ); ?>,
	html .alert-<?php echo porto_filter_output( $key ); ?> .alert-link,
	html .section.section-<?php echo porto_filter_output( $key ); ?>,
	html .section.section-<?php echo porto_filter_output( $key ); ?>:not([class*=" section-text-"]) h1,
	html .section.section-<?php echo porto_filter_output( $key ); ?>:not([class*=" section-text-"]) h2,
	html .section.section-<?php echo porto_filter_output( $key ); ?>:not([class*=" section-text-"]) h3,
	html .section.section-<?php echo porto_filter_output( $key ); ?>:not([class*=" section-text-"]) h4,
	html .section.section-<?php echo porto_filter_output( $key ); ?>:not([class*=" section-text-"]) h5,
	html .section.section-<?php echo porto_filter_output( $key ); ?>:not([class*=" section-text-"]) h6,
	html .section.section-<?php echo porto_filter_output( $key ); ?>-scale-2 .sort-source-style-2 > li > a,
	html .section.section-<?php echo porto_filter_output( $key ); ?>-scale-2 .sort-source-style-2 > li > a:focus,
	html .section.section-<?php echo porto_filter_output( $key ); ?>-scale-2 .sort-source-style-2 > li > a:hover,
	html .divider.divider-style-2.divider-<?php echo porto_filter_output( $key ); ?> i,
	.pricing-table .plan-<?php echo porto_filter_output( $key ); ?> h3,
	.pricing-table .plan-<?php echo porto_filter_output( $key ); ?> h3 .desc,
	.pricing-table-flat .plan-<?php echo porto_filter_output( $key ); ?> h3,
	.pricing-table-flat .plan-<?php echo porto_filter_output( $key ); ?> h3 .desc,
	.pricing-table-flat .plan-<?php echo porto_filter_output( $key ); ?> .price,
	.pricing-table-classic .plan-<?php echo porto_filter_output( $key ); ?> h3 strong,
	html .home-intro-<?php echo porto_filter_output( $key ); ?>,
	html .home-intro-<?php echo porto_filter_output( $key ); ?> .get-started a:not(.btn),
	html .home-intro-<?php echo porto_filter_output( $key ); ?> p,
	html .home-intro-<?php echo porto_filter_output( $key ); ?> p em,
	html .home-intro-<?php echo porto_filter_output( $key ); ?>.light p,
	html .thumb-info .thumb-info-action-icon-<?php echo porto_filter_output( $key ); ?> i,
	html .thumb-info-ribbon-<?php echo porto_filter_output( $key ); ?>,
	html .thumb-info-social-icons a.thumb-info-social-links-<?php echo porto_filter_output( $key ); ?> i { color: <?php echo esc_html( $theme_colors_inverse[ $key ] ); ?>; }

	html .section.section-<?php echo porto_filter_output( $key ); ?>:not([class*=" section-text-"]) p { color: <?php echo esc_html( $porto_color_lib->darken( $theme_colors_inverse[ $key ], 10 ) ); ?>; }
	html .popover-<?php echo porto_filter_output( $key ); ?> .popover-title { color: <?php echo esc_html( $theme_colors_inverse[ $key ] ); ?> !important; }

	html .list-<?php echo porto_filter_output( $key ); ?>.list-icons li i,
	html .toggle-<?php echo porto_filter_output( $key ); ?> .toggle.active > label,
	html .label-<?php echo porto_filter_output( $key ); ?>,
	<?php if ( ! $dark ) : ?>
		.pricing-table .plan-<?php echo porto_filter_output( $key ); ?>,
	<?php endif; ?>
	html .divider.divider-style-3.divider-<?php echo porto_filter_output( $key ); ?> i,
	.featured-box-<?php echo porto_filter_output( $key ); ?> .icon-featured:after,
	html .heading.heading-<?php echo porto_filter_output( $key ); ?> .heading-tag { border-color: <?php echo esc_html( $theme_color ); ?>; }

	.border-color-<?php echo porto_filter_output( $key ); ?>,
	html .blockquote-<?php echo porto_filter_output( $key ); ?> { border-color: <?php echo esc_html( $theme_color ); ?> !important; }

	.featured-box-<?php echo porto_filter_output( $key ); ?> .box-content { border-top-color: <?php echo esc_html( $theme_color ); ?>; }

	html .toggle-<?php echo porto_filter_output( $key ); ?> .toggle label { border-left-color: <?php echo esc_html( $theme_color ); ?>; border-right-color: <?php echo esc_html( $theme_color ); ?>; }
	html .alert-<?php echo porto_filter_output( $key ); ?> { border-color: <?php echo esc_html( $porto_color_lib->darken( $theme_color, 3 ) ); ?>; }
	html .section.section-<?php echo porto_filter_output( $key ); ?> { border-color: <?php echo esc_html( $porto_color_lib->darken( $theme_color, 5 ) ); ?> !important; }
	html .section.section-<?php echo porto_filter_output( $key ); ?>-scale-2 { border-color: <?php echo esc_html( $porto_color_lib->darken( $theme_color, 15 ) ); ?> !important; }
	html .section.section-<?php echo porto_filter_output( $key ); ?>-scale-2 .sort-source-style-2 > li.active > a:after { border-top-color: <?php echo esc_html( $porto_color_lib->darken( $theme_color, 10 ) ); ?>; }

	html .thumb-info-ribbon-<?php echo porto_filter_output( $key ); ?>:before {
		border-<?php echo porto_filter_output( $right ); ?>-color: <?php echo esc_html( $porto_color_lib->darken( $theme_color, 15 ) ); ?>;
	}

	.featured-box-effect-2.featured-box-<?php echo porto_filter_output( $key ); ?> .icon-featured:after { box-shadow: 0 0 0 3px <?php echo esc_html( $theme_color ); ?>; }
	.featured-box-effect-3.featured-box-<?php echo porto_filter_output( $key ); ?> .icon-featured:after { box-shadow: 0 0 0 10px <?php echo esc_html( $theme_color ); ?>; }

	html .toggle-<?php echo porto_filter_output( $key ); ?>.toggle-simple .toggle > label {
		background: transparent;
		<?php if ( $b['h3-font']['color'] ) : ?>
			color: <?php echo esc_html( $b['h3-font']['color'] ); ?>;
		<?php endif; ?>
	}

<?php endforeach; ?>

<?php
if ( $dark ) {
	$color_background = '#1d2127';
	$function_name    = 'lighten';
} else {
	$color_background = '#f4f4f4';
	$function_name    = 'darken';
}

	$color_background_scale = array( $porto_color_lib->$function_name( $color_background, 10 ), $porto_color_lib->$function_name( $color_background, 20 ), $porto_color_lib->$function_name( $color_background, 30 ), $porto_color_lib->$function_name( $color_background, 40 ), $porto_color_lib->$function_name( $color_background, 50 ), $porto_color_lib->$function_name( $color_background, 60 ), $porto_color_lib->$function_name( $color_background, 70 ), $porto_color_lib->$function_name( $color_background, 80 ), $porto_color_lib->$function_name( $color_background, 90 ) );
?>
<?php foreach ( $color_background_scale as $index => $value ) : ?>
	html .section.section-default-scale-<?php echo porto_filter_output( $index + 1 ); ?> {
		background-color: <?php echo esc_html( $value ); ?> !important;
		border-top-color: <?php echo esc_html( $porto_color_lib->darken( $value, 3 ) ); ?> !important;
	}
<?php endforeach; ?>

<?php if ( $b['h2-font']['color'] ) : ?>
	.post-item.post-title-simple .post-title,
	.post-item.post-title-simple .post-title h2,
	.post-item.post-title-simple .entry-title,
	article.post.post-title-simple .entry-title a,
	.post-item.post-title-simple .entry-title a { color: <?php echo esc_html( $b['h2-font']['color'] ); ?>; }
<?php endif; ?>
<?php if ( $b['h4-font']['color'] ) : ?>
	.member-item.member-item-3 .view-more,
	.fdm-item-panel .fdm-item-title { color: <?php echo esc_html( $b['h4-font']['color'] ); ?>; }
<?php endif; ?>

/* side menu */
.side-nav-wrap .sidebar-menu > li.menu-item > a,
.main-sidebar-menu .sidebar-menu > li.menu-item > a,
.side-nav-wrap .sidebar-menu .menu-custom-block span,
.main-sidebar-menu .sidebar-menu .menu-custom-block span,
.side-nav-wrap .sidebar-menu .menu-custom-block a,
.main-sidebar-menu .sidebar-menu .menu-custom-block a {
	<?php if ( $b['menu-side-font']['font-family'] ) : ?>
		font-family: <?php echo sanitize_text_field( $b['menu-side-font']['font-family'] ); ?>, sans-serif;
	<?php endif; ?>
	<?php if ( $b['menu-side-font']['font-size'] ) : ?>
		font-size: <?php echo esc_html( $b['menu-side-font']['font-size'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['menu-side-font']['font-weight'] ) : ?>
		font-weight: <?php echo esc_html( $b['menu-side-font']['font-weight'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['menu-side-font']['line-height'] ) : ?>
		line-height: <?php echo esc_html( $b['menu-side-font']['line-height'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['menu-side-font']['letter-spacing'] ) : ?>
		letter-spacing: <?php echo esc_html( $b['menu-side-font']['letter-spacing'] ); ?>;
	<?php endif; ?>
}
<?php if ( class_exists( 'Woocommerce' ) && $b['menu-side-font']['line-height'] && empty( $b['side-menu-type'] ) ) : ?>
	.side-nav-wrap .sidebar-menu > li.menu-item .popup:before,
	.main-sidebar-menu .sidebar-menu > li.menu-item .popup:before {
		top: <?php echo  ( 23 + (float) $b['menu-side-font']['line-height'] - 20 ) / 2 + 1; ?>px
	}
	@media <?php echo porto_filter_output( $screen_large ); ?> {
		.side-nav-wrap .sidebar-menu > li.menu-item .popup:before,
		.main-sidebar-menu .sidebar-menu > li.menu-item .popup:before {
			top: <?php echo  ( 18 + (float) $b['menu-side-font']['line-height'] - 20 ) / 2 + 1; ?>px
		}
	}
<?php endif; ?>
<?php if ( $b['menu-side-font']['line-height'] && 'accordion' == $b['side-menu-type'] ) : ?>
	.header-side-nav .sidebar-menu > li.menu-item > .arrow,
	.main-sidebar-menu .sidebar-menu > li.menu-item > .arrow {
		top: <?php echo ( 18 + (float) $b['menu-side-font']['line-height'] - 30 ) / 2; ?>px
	}
	@media (min-width: <?php echo (int) ( $b['container-width'] + $b['grid-gutter-width'] ); ?>px) {
		.header-side-nav .sidebar-menu > li.menu-item > .arrow,
		.main-sidebar-menu .sidebar-menu > li.menu-item > .arrow { top: <?php echo ( 22 + (float) $b['menu-side-font']['line-height'] - 30 ) / 2; ?>px }
	}
<?php endif; ?>

<?php if ( $b['mainmenu-toplevel-link-color']['regular'] ) : ?>
	.side-nav-wrap .sidebar-menu > li.menu-item > a,
	.side-nav-wrap .sidebar-menu > li.menu-item > .arrow:before,
	.side-nav-wrap .sidebar-menu .menu-custom-block a {
		color: <?php echo esc_html( $b['mainmenu-toplevel-link-color']['regular'] ); ?>
	}
<?php endif; ?>
<?php if ( $b['mainmenu-toplevel-hbg-color'] ) : ?>
	.side-nav-wrap .sidebar-menu > li.menu-item:hover,
	.side-nav-wrap .sidebar-menu > li.menu-item.active,
	.side-nav-wrap .sidebar-menu .menu-custom-block a:hover {
		background-color: <?php echo esc_html( $b['mainmenu-toplevel-hbg-color'] ); ?>;
	}
	<?php if ( 'transparent' != $b['mainmenu-toplevel-hbg-color'] ) : ?>
		.side-nav-wrap .sidebar-menu > li.menu-item > a,
		.side-nav-wrap .sidebar-menu > li.menu-item:hover > a,
		.side-nav-wrap .sidebar-menu > li.menu-item.active > a,
		.side-nav-wrap .sidebar-menu .menu-custom-block a,
		.side-nav-wrap .sidebar-menu .menu-custom-block a:hover,
		.side-nav-wrap .sidebar-menu .menu-custom-block a:hover + a {
			border-top-color: <?php echo esc_html( $porto_color_lib->lighten( $b['mainmenu-toplevel-hbg-color'], 5 ) ); ?>;
		}
		.side-nav-wrap .sidebar-menu > li.menu-item.active + li.menu-item > a {
			border-top: none;
		}
		.sidebar-menu > li.menu-item:hover + li.menu-item > a {
			border-top-color: transparent;
		}
	<?php else : ?>
		.side-nav-wrap .sidebar-menu > li.menu-item > a { border-top-color: transparent; }
	<?php endif; ?>
<?php endif; ?>
<?php if ( $b['mainmenu-toplevel-link-color']['hover'] ) : ?>
	.side-nav-wrap .sidebar-menu > li.menu-item:hover > a,
	.side-nav-wrap .sidebar-menu > li.menu-item.active > a,
	.side-nav-wrap .sidebar-menu > li.menu-item.active > .arrow:before,
	.side-nav-wrap .sidebar-menu > li.menu-item:hover > .arrow:before,
	.side-nav-wrap .sidebar-menu .menu-custom-block a:hover {
		color: <?php echo esc_html( $b['mainmenu-toplevel-link-color']['hover'] ); ?>;
	}
<?php endif; ?>
.toggle-menu-wrap .sidebar-menu > li.menu-item > a { border-top-color: rgba(0, 0, 0, .125) }

/* breadcrumb */
<?php
$css = array();
if ( $b['breadcrumbs-top-border']['border-top'] && '0px' != $b['breadcrumbs-top-border']['border-top'] ) {
	$css[] = 'border-top:' . esc_html( $b['breadcrumbs-top-border']['border-top'] ) . ' solid ' . esc_html( $b['breadcrumbs-top-border']['border-color'] );
}
if ( $b['breadcrumbs-bottom-border']['border-top'] && '0px' != $b['breadcrumbs-bottom-border']['border-top'] ) {
	$css[] = 'border-bottom:' . esc_html( $b['breadcrumbs-bottom-border']['border-top'] ) . ' solid ' . esc_html( $b['breadcrumbs-bottom-border']['border-color'] );
}
if ( ! empty( $css ) ) {
	echo '.page-top {' . implode( ';', $css ) . '}';
}
?>
.page-top > .container {
	padding-top: <?php echo porto_config_value( $b['breadcrumbs-padding']['padding-top'] ); ?>px;
	padding-bottom: <?php echo porto_config_value( $b['breadcrumbs-padding']['padding-bottom'] ); ?>px;
}
<?php if ( $b['breadcrumbs-text-color'] ) : ?>
	.page-top .yoast-breadcrumbs,
	.page-top .breadcrumbs-wrap {
		color: <?php echo esc_html( $b['breadcrumbs-text-color'] ); ?>;
	}
<?php endif; ?>
<?php if ( $b['breadcrumbs-link-color'] ) : ?>
	.page-top .yoast-breadcrumbs a,
	.page-top .breadcrumbs-wrap a,
	.page-top .product-nav .product-link {
		color: <?php echo esc_html( $b['breadcrumbs-link-color'] ); ?>;
	}
<?php endif; ?>
.page-top .page-title {
	<?php if ( $b['breadcrumbs-title-color'] ) : ?>
		color: <?php echo esc_html( $b['breadcrumbs-title-color'] ); ?>;
	<?php endif; ?>
	<?php if ( isset( $porto_settings['h1-font'] ) && ! empty( $porto_settings['h1-font']['font-family'] ) ) : ?>
		font-family: <?php echo sanitize_text_field( $porto_settings['h1-font']['font-family'] ); ?>, sans-serif;
	<?php endif; ?>
}
.page-top .page-sub-title {
	<?php if ( $b['breadcrumbs-subtitle-color'] ) : ?>
		color: <?php echo esc_html( $b['breadcrumbs-subtitle-color'] ); ?>;
	<?php endif; ?>
	margin: <?php echo porto_config_value( $b['breadcrumbs-subtitle-margin']['margin-top'] ); ?>px <?php echo porto_config_value( $b['breadcrumbs-subtitle-margin'][ 'margin-' . $right ] ); ?>px <?php echo porto_config_value( $b['breadcrumbs-subtitle-margin']['margin-bottom'] ); ?>px <?php echo porto_config_value( $b['breadcrumbs-subtitle-margin'][ 'margin-' . $left ] ); ?>px;
}
<?php if ( $b['breadcrumbs-title-color'] ) : ?>
	.page-top .sort-source > li > a { color: <?php echo esc_html( $b['breadcrumbs-title-color'] ); ?>; }
<?php endif; ?>
@media (max-width: 767px) {
	.page-top .sort-source {
		<?php if ( $b['breadcrumbs-bg']['background-color'] ) : ?>
			background: <?php echo esc_html( $b['breadcrumbs-bg']['background-color'] ); ?>;
		<?php endif; ?>
		<?php if ( $b['breadcrumbs-bottom-border']['border-top'] && '0px' != $b['breadcrumbs-bottom-border']['border-top'] ) : ?>
			border-top: <?php echo esc_html( $b['breadcrumbs-bottom-border']['border-top'] ); ?> solid <?php echo esc_html( $b['breadcrumbs-bottom-border']['border-color'] ); ?>;
		<?php endif; ?>
		<?php if ( $b['breadcrumbs-bottom-border']['border-top'] ) : ?>
			margin-bottom: -<?php echo esc_html( $b['breadcrumbs-bottom-border']['border-top'] ); ?>;
			bottom: -<?php echo (int) porto_config_value( $b['breadcrumbs-bottom-border']['border-top'] ) + 1; ?>px;
		<?php endif; ?>
	}
}
<?php porto_calc_container_width( '#breadcrumbs-boxed', ( $header_bg_empty && ! $breadcrumb_bg_empty ), $b['container-width'], $b['grid-gutter-width'] ); ?>

.owl-carousel .owl-nav .owl-prev,
.owl-carousel .owl-nav .owl-next,
.tparrows.tparrows-carousel.tp-leftarrow,
.tparrows.tparrows-carousel.tp-rightarrow,
.button,
input.submit {
	color: <?php echo esc_html( $b['skin-color-inverse'] ); ?>; 
	background-color: <?php echo esc_html( $skin_color ); ?>;
	border-color: <?php echo esc_html( $skin_color ); ?>;
}
.owl-carousel .owl-nav [class*="owl-"]:hover,
.owl-carousel .owl-nav [class*="owl-"]:active,
.owl-carousel .owl-nav [class*="owl-"]:focus,
.tparrows.tparrows-carousel.tp-leftarrow:hover,
.tparrows.tparrows-carousel.tp-rightarrow:hover,
.tparrows.tparrows-carousel.tp-leftarrow:active,
.tparrows.tparrows-carousel.tp-rightarrow:active,
.tparrows.tparrows-carousel.tp-leftarrow:focus,
.tparrows.tparrows-carousel.tp-rightarrow:focus {
	background-color: <?php echo esc_html( $porto_color_lib->darken( $skin_color, 5 ) ); ?>;
	border-color: <?php echo esc_html( $porto_color_lib->darken( $skin_color, 5 ) ); ?>;
}

.widget.follow-us .share-links a:not(:hover) {
	color: #525252;
	background-color: #fff;
}
<?php if ( $b['social-color'] ) : ?>
	#main .share-links a {
		background-color: <?php echo esc_html( $skin_color ); ?> !important;
		color: <?php echo esc_html( $b['skin-color-inverse'] ); ?> !important;
	}
	#main .share-links a:hover {
		background-color: <?php echo esc_html( $porto_color_lib->lighten( $skin_color, 5 ) ); ?> !important;
	}
<?php endif; ?>


/* button */
.btn-primary:hover,
.button:hover,
input.submit:hover,
.btn-primary:active,
.button:active,
input.submit:active,
.btn-primary:focus,
.button:focus,
input.submit:focus {
	border-color: <?php echo esc_html( $porto_color_lib->darken( $skin_color, 5 ) ); ?>;
	background-color: <?php echo esc_html( $porto_color_lib->darken( $skin_color, 5 ) ); ?>;
	color: <?php echo esc_html( $b['skin-color-inverse'] ); ?>;
}
.btn-primary[disabled],
.button[disabled],
input.submit[disabled],
fieldset[disabled] .btn-primary,
fieldset[disabled] .button,
fieldset[disabled] input.submit,
.btn-primary[disabled]:hover,
.button[disabled]:hover,
input.submit[disabled]:hover,
fieldset[disabled] .btn-primary:hover,
fieldset[disabled] .button:hover,
fieldset[disabled] input.submit:hover,
.btn-primary[disabled]:focus,
.button[disabled]:focus,
input.submit[disabled]:focus,
fieldset[disabled] .btn-primary:focus,
fieldset[disabled] .button:focus,
fieldset[disabled] input.submit:focus,
.btn-primary[disabled].focus,
.button[disabled].focus,
input.submit[disabled].focus,
fieldset[disabled] .btn-primary.focus,
fieldset[disabled] .button.focus,
fieldset[disabled] input.submit.focus,
.btn-primary[disabled]:active,
.button[disabled]:active,
input.submit[disabled]:active,
fieldset[disabled] .btn-primary:active,
fieldset[disabled] .button:active,
fieldset[disabled] input.submit:active,
.btn-primary[disabled].active,
.button[disabled].active,
input.submit[disabled].active,
fieldset[disabled] .btn-primary.active,
fieldset[disabled] .button.active,
fieldset[disabled] input.submit.active,
[type="submit"],
.geodir-search [type="button"],
.geodir-search [type="submit"],
#geodir-wrapper [type="button"],
#geodir-wrapper [type="submit"]
<?php if ( class_exists( 'WeDevs_Dokan' ) ) : ?>
, input[type="submit"].dokan-btn-theme, a.dokan-btn-theme, .dokan-btn-theme
<?php endif; ?>
{
	background-color: <?php echo esc_html( $skin_color ); ?>;
	border-color: <?php echo esc_html( $skin_color ); ?>;
}
[type="submit"]:hover,
.geodir-search [type="button"]:hover,
.geodir-search [type="submit"]:hover,
#geodir-wrapper [type="button"]:hover,
#geodir-wrapper [type="submit"]:hover,
[type="submit"]:active,
.geodir-search [type="button"]:active,
.geodir-search [type="submit"]:active,
#geodir-wrapper [type="button"]:active,
#geodir-wrapper [type="submit"]:active
<?php if ( class_exists( 'WeDevs_Dokan' ) ) : ?>
,input[type="submit"].dokan-btn-theme:hover, a.dokan-btn-theme:hover, .dokan-btn-theme:hover, input[type="submit"].dokan-btn-theme:focus, a.dokan-btn-theme:focus, .dokan-btn-theme:focus, input[type="submit"].dokan-btn-theme:active, a.dokan-btn-theme:active, .dokan-btn-theme:active, input[type="submit"].dokan-btn-theme.active, a.dokan-btn-theme.active, .dokan-btn-theme.active, .open .dropdown-toggleinput[type="submit"].dokan-btn-theme, .open .dropdown-togglea.dokan-btn-theme, .open .dropdown-toggle.dokan-btn-theme
<?php endif; ?>
{
	border-color: <?php echo esc_html( $porto_color_lib->darken( $skin_color, 5 ) ); ?>;
	background-color: <?php echo esc_html( $porto_color_lib->darken( $skin_color, 5 ) ); ?>;
}
[type="submit"][disabled],
.geodir-search [type="button"][disabled],
.geodir-search [type="submit"][disabled],
#geodir-wrapper [type="button"][disabled],
#geodir-wrapper [type="submit"][disabled],
fieldset[disabled] [type="submit"],
[type="submit"][disabled]:hover,
.geodir-search [type="button"][disabled]:hover,
.geodir-search [type="submit"][disabled]:hover,
#geodir-wrapper [type="button"][disabled]:hover,
#geodir-wrapper [type="submit"][disabled]:hover,
fieldset[disabled] [type="submit"]:hover,
[type="submit"][disabled]:focus,
.geodir-search [type="button"][disabled]:focus,
.geodir-search [type="submit"][disabled]:focus,
#geodir-wrapper [type="button"][disabled]:focus,
#geodir-wrapper [type="submit"][disabled]:focus,
fieldset[disabled] [type="submit"]:focus,
[type="submit"][disabled]:active,
.geodir-search [type="button"][disabled]:active,
.geodir-search [type="submit"][disabled]:active,
#geodir-wrapper [type="button"][disabled]:active,
#geodir-wrapper [type="submit"][disabled]:active,
fieldset[disabled] [type="submit"]:active {
	background-color: <?php echo esc_html( $skin_color ); ?>;
	border-color: <?php echo esc_html( $skin_color ); ?>;
}
<?php foreach ( $theme_colors as $key => $theme_color ) : ?>
	html .btn-<?php echo porto_filter_output( $key ); ?> {
		color: <?php echo esc_html( $theme_colors_inverse[ $key ] ); ?>;
		background-color: <?php echo esc_html( $theme_color ); ?>;
		border-color: <?php echo esc_html( $theme_color ); ?> <?php echo esc_html( $theme_color ); ?> <?php echo esc_html( $porto_color_lib->darken( $theme_color, 10 ) ); ?>;
	}
	html .btn-<?php echo porto_filter_output( $key ); ?>:hover,
	html .btn-<?php echo porto_filter_output( $key ); ?>:focus,
	html .btn-<?php echo porto_filter_output( $key ); ?>:active {
		color: <?php echo esc_html( $theme_colors_inverse[ $key ] ); ?>;
		background-color: <?php echo esc_html( $porto_color_lib->lighten( $theme_color, 7 ) ); ?>;
		border-color: <?php echo esc_html( $porto_color_lib->lighten( $theme_color, 10 ) ); ?> <?php echo esc_html( $porto_color_lib->lighten( $theme_color, 10 ) ); ?> <?php echo esc_html( $theme_color ); ?>;
	}
	html .btn-<?php echo porto_filter_output( $key ); ?>-scale-2 {
		color: <?php echo esc_html( $theme_colors_inverse[ $key ] ); ?>;
		text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
		background-color: <?php echo esc_html( $porto_color_lib->darken( $theme_color, 10 ) ); ?>;
		border-color: <?php echo esc_html( $porto_color_lib->darken( $theme_color, 10 ) ); ?> <?php echo esc_html( $porto_color_lib->darken( $theme_color, 10 ) ); ?> <?php echo esc_html( $porto_color_lib->darken( $theme_color, 20 ) ); ?>;
	}
	html .btn-<?php echo porto_filter_output( $key ); ?>-scale-2:hover,
	html .btn-<?php echo porto_filter_output( $key ); ?>-scale-2:active,
	html .btn-<?php echo porto_filter_output( $key ); ?>-scale-2:focus {
		border-color: <?php echo esc_html( $porto_color_lib->darken( $theme_color, 15 ) ); ?>;
		background-color: <?php echo esc_html( $porto_color_lib->darken( $theme_color, 15 ) ); ?>;
	}

	html .btn-borders.btn-<?php echo porto_filter_output( $key ); ?> {
		background: transparent;
		border-color: <?php echo esc_html( $theme_color ); ?>;
		color: <?php echo esc_html( $theme_color ); ?>;
	}
	html .btn-arrow.btn-<?php echo porto_filter_output( $key ); ?>,
	html .btn-arrow.btn-<?php echo porto_filter_output( $key ); ?>:hover,
	html .btn-arrow.btn-<?php echo porto_filter_output( $key ); ?>:active,
	html .btn-arrow.btn-<?php echo porto_filter_output( $key ); ?>:not(:disabled):active {
		color: <?php echo esc_html( $theme_color ); ?>;
	}
	html .btn-arrow.btn-<?php echo porto_filter_output( $key ); ?> .icon-wrapper {
		background-color: <?php echo esc_html( $theme_color ); ?>;
		box-shadow: 2px 3px 18px -3px <?php echo esc_html( $theme_color ); ?>;
	}


	html .btn-borders.btn-<?php echo porto_filter_output( $key ); ?>:hover,
	html .btn-borders.btn-<?php echo porto_filter_output( $key ); ?>:focus,
	html .btn-borders.btn-<?php echo porto_filter_output( $key ); ?>:active {
		background-color: <?php echo esc_html( $theme_color ); ?>;
		border-color: <?php echo esc_html( $theme_color ); ?> !important;
		color: <?php echo esc_html( $theme_colors_inverse[ $key ] ); ?>;
	}

	<?php if ( 'primary' != $key ) : ?>
		.tabs-<?php echo porto_filter_output( $key ); ?> ul.nav-tabs .nav-link {
			color: <?php echo esc_html( $theme_color ); ?>;
		}
		.tabs-<?php echo porto_filter_output( $key ); ?> ul.nav-tabs li.active .nav-link,
		.tabs-<?php echo porto_filter_output( $key ); ?> ul.nav-tabs li .nav-link:hover {
			border-top-color: <?php echo esc_html( $theme_color ); ?>;
		}
		.tabs-<?php echo porto_filter_output( $key ); ?>.tabs-bottom .nav-tabs li .nav-link:hover,
		.tabs-<?php echo porto_filter_output( $key ); ?>.tabs-bottom .nav-tabs li.active .nav-link {
			border-bottom-color: <?php echo esc_html( $theme_color ); ?>;
		}
		.tabs-<?php echo porto_filter_output( $key ); ?>.tabs-vertical.tabs-left li .nav-link:hover,
		.tabs-<?php echo porto_filter_output( $key ); ?>.tabs-vertical.tabs-left li.active .nav-link {
			border-left-color: <?php echo esc_html( $theme_color ); ?>;
		}
		.tabs-<?php echo porto_filter_output( $key ); ?>.tabs-vertical.tabs-right li .nav-link:hover ,
		.tabs-<?php echo porto_filter_output( $key ); ?>.tabs-vertical.tabs-right li.active .nav-link {
			border-right-color: <?php echo esc_html( $theme_color ); ?>;
		}
	<?php endif; ?>

	html .stats-block.counter-<?php echo porto_filter_output( $key ); ?> .stats-number,
	html .stats-block.counter-<?php echo porto_filter_output( $key ); ?> div.counter_prefix,
	html .stats-block.counter-<?php echo porto_filter_output( $key ); ?> div.counter_suffix {
		color: <?php echo esc_html( $theme_color ); ?>;
	}

	html .testimonial-<?php echo porto_filter_output( $key ); ?> blockquote {
		background: <?php echo esc_html( $porto_color_lib->lighten( $theme_color, 5 ) ); ?>;
	}
	html .testimonial-<?php echo porto_filter_output( $key ); ?> .testimonial-arrow-down {
		border-top-color: <?php echo esc_html( $porto_color_lib->lighten( $theme_color, 5 ) ); ?>;
	}
<?php endforeach; ?>
<?php if ( 'btn-borders' == porto_get_button_style() ) : ?>
	.btn, .button, input.submit,
	[type="submit"].btn-primary,
	[type="submit"] {
		background: transparent;
		border-color: <?php echo esc_html( $skin_color ); ?>;
		color: <?php echo esc_html( $skin_color ); ?>;
		text-shadow: none;
		border-width: 3px;
		padding: 4px 10px;
	}
	.btn.btn-lg, .button.btn-lg, input.submit.btn-lg,
	[type="submit"].btn-primary.btn-lg,
	[type="submit"].btn-lg,
	.btn-group-lg > .btn, .btn-group-lg > .button, .btn-group-lg > input.submit,
	.btn-group-lg > [type="submit"].btn-primary,
	.btn-group-lg > [type="submit"] {
		padding: 8px 14px;
	}
	.btn.btn-sm, .button.btn-sm, input.submit.btn-sm,
	[type="submit"].btn-primary.btn-sm,
	[type="submit"].btn-sm,
	.btn-group-sm > .btn, .btn-group-sm > .button, .btn-group-sm > input.submit,
	.btn-group-sm > [type="submit"].btn-primary,
	.btn-group-sm > [type="submit"] {
		border-width: 2px;
		padding: 4px 10px;
	}
	.btn.btn-xs, .button.btn-xs, input.submit.btn-xs,
	[type="submit"].btn-primary.btn-xs,
	[type="submit"].btn-xs,
	.btn-group-xs > .btn, .btn-group-xs > .button, .btn-group-xs > input.submit,
	.btn-group-xs > [type="submit"].btn-primary,
	.btn-group-xs > [type="submit"] {
		padding: 1px 5px;
		border-width: 1px;
	}
	.btn:hover, .button:hover, input.submit:hover,
	[type="submit"].btn-primary:hover,
	[type="submit"]:hover,
	.btn:focus, .button:focus, input.submit:focus,
	[type="submit"].btn-primary:focus,
	[type="submit"]:focus,
	.btn:active, .button:active, input.submit:active,
	[type="submit"].btn-primary:active,
	[type="submit"]:active {
		background-color: <?php echo esc_html( $porto_color_lib->darken( $skin_color, 5 ) ); ?>;
		border-color: <?php echo esc_html( $skin_color ); ?> !important;
		color: <?php echo esc_html( $b['skin-color-inverse'] ); ?>;
	}

	.btn-default,
	[type="submit"].btn-default {
		border-color: #ccc;
		color: #333;
	}
	.btn-default:hover,
	[type="submit"].btn-default:hover,
	.btn-default:focus,
	[type="submit"].btn-default:focus,
	.btn-default:active,
	[type="submit"].btn-default:active {
		background-color: #e6e6e6;
		border-color: #adadad !important;
		color: #333;
	}

	body .cart-actions .button,
	body .checkout-button,
	body #place_order {
		padding: 8px 14px;
	}
	<?php foreach ( $theme_colors as $key => $theme_color ) : ?>
		.btn-<?php echo porto_filter_output( $key ); ?>,
		[type="submit"].btn-<?php echo porto_filter_output( $key ); ?> {
			background: transparent;
			border-color: <?php echo esc_html( $theme_color ); ?>;
			color: <?php echo esc_html( $theme_color ); ?>;
			text-shadow: none;
			border-width: 3px;
		}
		.btn-<?php echo porto_filter_output( $key ); ?>:hover,
		[type="submit"].btn-<?php echo porto_filter_output( $key ); ?>:hover,
		.btn-<?php echo porto_filter_output( $key ); ?>:focus,
		[type="submit"].btn-<?php echo porto_filter_output( $key ); ?>:focus,
		.btn-<?php echo porto_filter_output( $key ); ?>:active,
		[type="submit"].btn-<?php echo porto_filter_output( $key ); ?>:active {
			background-color: <?php echo esc_html( $porto_color_lib->darken( $theme_color, 5 ) ); ?>;
			border-color: <?php echo esc_html( $theme_color ); ?> !important;
			color: <?php echo esc_html( $theme_colors_inverse[ $key ] ); ?>;
		}

	<?php endforeach; ?>
<?php endif; ?>

<?php if ( $b['tertiary-color'] ) : ?>
	.portfolio-item:hover .thumb-info-icons .thumb-info-icon {
		background-color: <?php echo esc_html( $b['tertiary-color'] ); ?> !important;
	}
<?php endif; ?>

.widget_sidebar_menu .widget-title,
.sidebar-menu > li.menu-item > a,
.sidebar-menu .menu-custom-block a {
	color: #444444;
}

.sidebar-menu > li.menu-item > .arrow:before {
	color: #838b90;
}

.mega-menu > li.menu-item > a,
.mega-menu .wide li.sub > a,
.header-side .sidebar-menu > li.menu-item > a,
.main-sidebar-menu .sidebar-menu > li.menu-item > a,
.main-sidebar-menu .sidebar-menu .menu-custom-item a,
.sidebar-menu .wide li.sub > a,
.porto-view-switcher .narrow li.menu-item > a {
	text-transform: <?php echo esc_html( $b['menu-text-transform'] ); ?>;
}

.popup .sub-menu,
.header-side-nav .narrow .popup {
	text-transform: <?php echo esc_html( $b['menu-popup-text-transform'] ); ?>;
}

<?php if ( $b['mainmenu-tip-bg-color'] ) : ?>
	.mega-menu .tip,
	.sidebar-menu .tip,
	.accordion-menu .tip,
	.menu-custom-block .tip {
		background: <?php echo esc_html( $b['mainmenu-tip-bg-color'] ); ?>;
		border-color: <?php echo esc_html( $b['mainmenu-tip-bg-color'] ); ?>;
	}
<?php endif; ?>

section.timeline .timeline-box.left:before,
section.timeline .timeline-box.right:before {
	background: <?php echo esc_html( $skin_color ); ?>;
	box-shadow: 0 0 0 3px #ffffff, 0 0 0 6px <?php echo esc_html( $skin_color ); ?>;
}

.porto-lazyload:not(img):not(.lazy-load-loaded) { background-color: <?php echo esc_html( $color_background ); ?> }
.porto-lazyload:not(.lazy-load-loaded)[data-oi$=".png"] { opacity: 0; }


article.post .post-date .sticky,
.post-item .post-date .sticky {
	<?php if ( $b['hot-color'] ) : ?>
		background: <?php echo esc_html( $b['hot-color'] ); ?>;
	<?php endif; ?>
	<?php if ( $b['hot-color-inverse'] ) : ?>
		color: <?php echo esc_html( $b['hot-color-inverse'] ); ?>;
	<?php endif; ?>
}

/* shortcodes */
.porto-vc-testimonial blockquote,
.testimonial blockquote,
.testimonial blockquote p {
	font-family: <?php echo isset( $b['shortcode-testimonial-font']['font-family'] ) && $b['shortcode-testimonial-font']['font-family'] ? sanitize_text_field( $b['shortcode-testimonial-font']['font-family'] ) . ',' : ''; ?>Georgia, serif;
	<?php if ( isset( $b['shortcode-testimonial-font']['letter-spacing'] ) && $b['shortcode-testimonial-font']['letter-spacing'] ) : ?>
		letter-spacing: <?php echo esc_html( $b['shortcode-testimonial-font']['letter-spacing'] ); ?>;
	<?php endif; ?>
}

<?php if ( isset( $b['color-dark'] ) ) : ?>
	ul.category-color-dark li.product-category .thumb-info-title { color: <?php echo esc_html( $b['color-dark'] ); ?> }
<?php endif; ?>

/* footer */
<?php if ( $b['footer-text-color'] ) : ?>
	#footer,
	#footer p,
	#footer .widget > div > ul li,
	#footer .widget > ul li  {
		color: <?php echo esc_html( $b['footer-text-color'] ); ?>;
	}
	<?php if ( 'transparent' != $b['footer-text-color'] ) : ?>
		#footer .widget > div > ul,
		#footer .widget > ul,
		#footer .widget > div > ul li,
		#footer .widget > ul li,
		#footer .post-item-small {
			border-color: rgba(<?php echo esc_html( $porto_color_lib->hexToRGB( $b['footer-text-color'] ) ); ?>, 0.3);
		}
	<?php endif; ?>
<?php endif; ?>
<?php if ( $b['footer-link-color']['regular'] ) : ?>
	#footer a:not(.btn),
	#footer .tooltip-icon {
		color: <?php echo esc_html( $b['footer-link-color']['regular'] ); ?>;
	}
	#footer .tooltip-icon {
		border-color: <?php echo esc_html( $b['footer-link-color']['regular'] ); ?>;
	}
<?php endif; ?>
<?php if ( $b['footer-link-color']['hover'] ) : ?>
	#footer a:hover {
		color: <?php echo esc_html( $b['footer-link-color']['hover'] ); ?>;
	}
<?php endif; ?>
<?php if ( $b['footer-heading-color'] ) : ?>
	#footer h1,
	#footer h2,
	#footer h3,
	#footer h4,
	#footer h5,
	#footer h6,
	#footer .widget-title,
	#footer .widgettitle,
	#footer h1 a,
	#footer h2 a,
	#footer h3 a,
	#footer h4 a,
	#footer h5 a,
	#footer h6 a,
	#footer .widget-title a,
	#footer .widgettitle a,
	#footer .widget.twitter-tweets .fa-twitter {
		color: <?php echo esc_html( $b['footer-heading-color'] ); ?>;
	}
<?php endif; ?>
<?php if ( $b['footer-ribbon-bg-color'] ) : ?>
	#footer .footer-ribbon {
		background-color: <?php echo esc_html( $b['footer-ribbon-bg-color'] ); ?>;
	}
	#footer .footer-ribbon:before {
		border-<?php echo porto_filter_output( $right ); ?>-color: <?php echo esc_html( $porto_color_lib->darken( $b['footer-ribbon-bg-color'], 15 ) ); ?>;
	}
<?php endif; ?>
<?php if ( $b['footer-ribbon-text-color'] ) : ?>
	#footer .footer-ribbon,
	#footer .footer-ribbon a,
	#footer .footer-ribbon a:hover,
	#footer .footer-ribbon a:focus {
		color: <?php echo esc_html( $b['footer-ribbon-text-color'] ); ?>;
	}
<?php endif; ?>
<?php if ( $b['footer-bottom-link-color']['regular'] ) : ?>
	#footer .footer-bottom a,
	#footer .footer-bottom .widget_nav_menu ul li:before {
		color: <?php echo esc_html( $b['footer-bottom-link-color']['regular'] ); ?>;
	}
<?php endif; ?>
<?php if ( $b['footer-bottom-link-color']['hover'] ) : ?>
	#footer .footer-bottom a:hover {
		color: <?php echo esc_html( $b['footer-bottom-link-color']['hover'] ); ?>;
	}
<?php endif; ?>
<?php if ( 'transparent' == $b['footer-social-bg-color'] ) : ?>
	#footer .share-links a:not(:hover),
	.footer-top .share-links a:not(:hover) {
		background: none;
		color: <?php echo esc_html( $b['footer-social-link-color'] ? $b['footer-social-link-color'] : '#525252' ); ?>;
	}
<?php else : ?>
	#footer .share-links a:not(:hover),
	.footer-top .share-links a:not(:hover) {
		<?php if ( $b['footer-social-bg-color'] ) : ?>
			background: <?php echo esc_html( $b['footer-social-bg-color'] ); ?>;
		<?php endif; ?>
		color: <?php echo esc_html( $b['footer-social-link-color'] ? $b['footer-social-link-color'] : '#525252' ); ?>;
	}
<?php endif; ?>
<?php porto_calc_container_width( '#footer-boxed', ( $header_bg_empty && ! $footer_bg_empty ), $b['container-width'], $b['grid-gutter-width'] ); ?>

/*------------------ Fonts ---------------------- */
<?php if ( $b['alt-font']['font-family'] ) : ?>
	.porto-concept strong,
	.home-intro p em,
	.alternative-font,
	.thumb-info-ribbon span,
	.stats-block.counter-alternative .stats-number,
	.vc_custom_heading em,
	#footer .footer-ribbon { font-family: <?php echo sanitize_text_field( $b['alt-font']['font-family'] ); ?>, sans-serif; }
<?php endif; ?>
<?php if ( $b['alt-font']['font-weight'] ) : ?>
	.alternative-font,
	#footer .footer-ribbon { font-weight: <?php echo esc_html( $b['alt-font']['font-weight'] ); ?>; }
<?php endif; ?>
.pricing-table-flat .plan-price,
.testimonial.testimonial-style-3 blockquote p,
.testimonial.testimonial-style-4 blockquote p,
.testimonial.testimonial-style-5 blockquote p,
.searchform .live-search-list .autocomplete-suggestion { font-family: <?php echo sanitize_text_field( $b['body-font']['font-family'] ); ?>, sans-serif; }
<?php if ( class_exists( 'Woocommerce' ) && isset( $b['add-to-cart-font'] ) && ! empty( $b['add-to-cart-font']['font-family'] ) ) : ?>
	#mini-cart .buttons a,
	.quantity .qty,
	.single_add_to_cart_button,
	.shop_table.wishlist_table .add_to_cart.button,
	.woocommerce table.wishlist_table .add_to_cart.button,
	ul.products li.product-col .add_to_cart_button,
	ul.products li.product-col .add_to_cart_read_more,
	ul.products li.product-col .quickview,
	.single-product .product-summary-wrap .price,
	.single-product .yith-wcwl-add-to-wishlist { font-family: <?php echo sanitize_text_field( $b['add-to-cart-font']['font-family'] ); ?>, sans-serif; }
<?php endif; ?>

.owl-carousel.dots-color-primary .owl-dots .owl-dot span { background-color: #43a6a3; }
.master-slider { direction: ltr; }

/*------------------ bbPress ---------------------- */
<?php if ( ( class_exists( 'bbPress' ) && is_bbpress() ) || ( class_exists( 'BuddyPress' ) && is_buddypress() ) ) : ?>
	#bbpress-forums .bbp-topic-pagination a:hover,
	#bbpress-forums .bbp-topic-pagination a:focus,
	#bbpress-forums .bbp-topic-pagination span.current,
	#bbpress-forums .bbp-pagination a:hover,
	#bbpress-forums .bbp-pagination a:focus,
	#bbpress-forums .bbp-pagination span.current,
	#buddypress button,
	#buddypress a.button,
	#buddypress input[type="submit"],
	#buddypress input[type="button"],
	#buddypress input[type="reset"],
	#buddypress ul.button-nav li a,
	#buddypress div.generic-button a,
	#buddypress .comment-reply-link,
	a.bp-title-button {
		background-color: <?php echo esc_html( $skin_color ); ?>;
		border-color: <?php echo esc_html( $skin_color ); ?>;
	}

	#buddypress div.item-list-tabs ul li.selected a,
	#buddypress div.item-list-tabs ul li.current a {
		background-color: <?php echo esc_html( $skin_color ); ?>;
		color: <?php echo esc_html( $b['skin-color-inverse'] ); ?>;
	}

	#buddypress button:hover,
	#buddypress a.button:hover,
	#buddypress input[type="submit"]:hover,
	#buddypress input[type="button"]:hover,
	#buddypress input[type="reset"]:hover,
	#buddypress ul.button-nav li a:hover,
	#buddypress div.generic-button a:hover,
	#buddypress .comment-reply-link:hover,
	a.bp-title-button:hover,
	#buddypress button:focus,
	#buddypress a.button:focus,
	#buddypress input[type="submit"]:focus,
	#buddypress input[type="button"]:focus,
	#buddypress input[type="reset"]:focus,
	#buddypress ul.button-nav li a:focus,
	#buddypress div.generic-button a:focus,
	#buddypress .comment-reply-link:focus,
	a.bp-title-button:focus,
	#buddypress .comment-reply-link:hover,
	#buddypress a.button:focus,
	#buddypress a.button:hover,
	#buddypress button:hover,
	#buddypress div.generic-button a:hover
	#buddypress input[type="button"]:hover,
	#buddypress input[type="reset"]:hover,
	#buddypress input[type="submit"]:hover,
	#buddypress ul.button-nav li a:hover,
	#buddypress ul.button-nav li.current a,
	#buddypress input.pending[type="submit"],
	#buddypress input.pending[type="button"],
	#buddypress input.pending[type="reset"],
	#buddypress input[type="submit"][disabled="disabled"],
	#buddypress button.pending,
	#buddypress div.pending a,
	#buddypress a.disabled {
		background: <?php echo esc_html( $porto_color_lib->darken( $skin_color, 5 ) ); ?>;
		border-color: <?php echo esc_html( $porto_color_lib->darken( $skin_color, 5 ) ); ?>;
	}

	@keyframes loader-pulsate {
		from {
			background: <?php echo esc_html( $skin_color ); ?>;
			border-color: <?php echo esc_html( $skin_color ); ?>;
			box-shadow: none;
		}
		to {
			background: <?php echo esc_html( $porto_color_lib->darken( $skin_color, 5 ) ); ?>;
			border-color: <?php echo esc_html( $porto_color_lib->darken( $skin_color, 5 ) ); ?>;
			box-shadow: none;
		}
	}
	#buddypress div.pagination .pagination-links a:hover,
	#buddypress div.pagination .pagination-links a:focus,
	#buddypress div.pagination .pagination-links a:active,
	#buddypress div.pagination .pagination-links span.current,
	#buddypress div.pagination .pagination-links span.current:hover {
		background: <?php echo esc_html( $skin_color ); ?>;
		border-color: <?php echo esc_html( $skin_color ); ?>;
	}
<?php endif; ?>

/*------------------ Theme Dokan --------------------- */
<?php if ( class_exists( 'WeDevs_Dokan' ) ) : ?>
	/*.dokan-dashboard .dokan-dash-sidebar,
	.dokan-dashboard .dokan-dash-sidebar ul.dokan-dashboard-menu { background: #f4f4f4; }
	.dokan-dashboard .dokan-dash-sidebar ul.dokan-dashboard-menu li,
	.dokan-dashboard .dokan-dash-sidebar ul.dokan-dashboard-menu li.dokan-common-links a { border-color: #e1e1e1; }
	.dokan-dashboard .dokan-dash-sidebar ul.dokan-dashboard-menu li a { color: <?php echo esc_html( $b['dark-color'] ); ?>; }
	.dokan-dashboard .dokan-dash-sidebar ul.dokan-dashboard-menu li.active a,
	.dokan-dashboard .dokan-dash-sidebar ul.dokan-dashboard-menu li:hover a { color: <?php echo esc_html( $b['skin-color-inverse'] ); ?> }*/
	.dokan-form-group label.error { display: block; margin-top: 0; }
	.dokan-single-store .profile-info li { line-height: 24px; }
	.dokan-dashboard .dokan-dash-sidebar ul.dokan-dashboard-menu li.active:after { <?php echo porto_filter_output( $left ); ?>: auto; <?php echo porto_filter_output( $right ); ?>: 0; }
	input[type="submit"].dokan-btn, a.dokan-btn, .dokan-btn { font-weight: 600; font-size: .9em; padding: 0.5rem 1rem; }
	.dokan-store-sidebar .widget>div>ul { border-bottom: none; }
	.dokan-store-sidebar .widget>div>ul li { padding: 0; border-top: none; }
	.dokan-store-sidebar .widget>div>ul li > ul { margin-top: 0; margin-bottom: 0; }
	.dokan-store-sidebar .widget>div>ul li > a { display: block; padding: 5px 0; }
	.dokan-store-sidebar .widget .caret-icon { float: <?php echo porto_filter_output( $right ); ?>; padding: 0 10px; }

	.dokan-store-widget .product_list_widget li { display: block }
<?php endif; ?>

/*------------------ Theme Shop ---------------------- */
<?php if ( class_exists( 'Woocommerce' ) ) : ?>
	.woocommerce-account .woocommerce > .row { margin-left: <?php echo 10 - (int) $b['grid-gutter-width'] / 2; ?>px; margin-right: <?php echo 10 - (int) $b['grid-gutter-width'] / 2; ?>px }
	.product-layout-grid .product-images .img-thumbnail,
	ul.list li.product { margin-bottom: <?php echo (int) $b['grid-gutter-width']; ?>px; }
	ul.products.grid-creative li.product-col { padding-bottom: <?php echo (int) $b['grid-gutter-width']; ?>px; }
	.summary-before .labels { margin-<?php echo porto_filter_output( $left ); ?>: <?php echo (int) $b['grid-gutter-width'] / 2; ?>px; }
	.shop-loop-before.sticky { padding-left: <?php echo (int) $b['grid-gutter-width']; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width'] - 10; ?>px; }
	<?php if ( (int) $b['grid-gutter-width'] > $max_spacing_mobile ) : ?>
		@media (max-width: 480px) {
			.shop-loop-before.sticky { padding-<?php echo porto_filter_output( $left ); ?>: <?php echo (int) $max_spacing_mobile; ?>px; padding-<?php echo porto_filter_output( $right ); ?>: <?php echo (int) $max_spacing_mobile - 10; ?>
			}
		}
	<?php endif; ?>

	/* product layout */
	.divider-line.grid > .product-col { border-right: 1px solid <?php echo esc_html( $input_border_color ); ?>; border-bottom: 1px solid <?php echo esc_html( $input_border_color ); ?>; }
	li.product-onimage .product-content { background: <?php echo porto_if_dark( $color_dark_3, '#fff' ); ?>; border-top: 1px solid <?php echo esc_html( $input_border_color ); ?>; }
	li.product-onimage .product-content .add-links { border-top: 1px solid <?php echo esc_html( $input_border_color ); ?>; }
	@media (min-width: <?php echo (int) ( $b['container-width'] + $b['grid-gutter-width'] ); ?>px) {
		.divider-line.pcols-lg-6 > .product-col:nth-child(6n),
		.divider-line.pcols-lg-5 > .product-col:nth-child(5n),
		.divider-line.pcols-lg-4 > .product-col:nth-child(4n),
		.divider-line.pcols-lg-3 > .product-col:nth-child(3n),
		.divider-line.pcols-lg-2 > .product-col:nth-child(2n) { border-right-width: 0; }
	}
	@media (max-width: 992px) {
		.shop-loop-before { margin-bottom: <?php echo (int) $b['grid-gutter-width']; ?>px; }
	}
	@media (min-width: 768px) and <?php echo porto_filter_output( $screen_large ); ?> {
		.divider-line.pcols-md-5 > .product-col:nth-child(5n),
		.divider-line.pcols-md-4 > .product-col:nth-child(4n),
		.divider-line.pcols-md-3 > .product-col:nth-child(3n),
		.divider-line.pcols-md-2 > .product-col:nth-child(2n) { border-right-width: 0; }
	}
	@media (min-width: 576px) and (max-width: 767px) {
		.divider-line.pcols-xs-3 > .product-col:nth-child(3n),
		.divider-line.pcols-xs-2 > .product-col:nth-child(2n) { border-right-width: 0; }
	}
	@media (max-width: 575px) {
		.divider-line.pcols-ls-2 > .product-col:nth-child(2n) { border-right-width: 0; }
	}

	/* theme colors */
	li.product-col.product-default h3,
	#yith-wcwl-popup-message,
	.widget_product_categories ul li > a,
	.widget_price_filter ul li > a,
	.widget_layered_nav ul li > a,
	.widget_layered_nav_filters ul li > a,
	.widget_rating_filter ul li > a,
	.widget_price_filter ol li > a,
	.widget_layered_nav_filters ol li > a,
	.widget_rating_filter ol li > a,
	.woocommerce .widget_layered_nav ul.yith-wcan-label li a,
	.woocommerce-page .widget_layered_nav ul.yith-wcan-label li a,
	.widget_recent_reviews .product_list_widget li a,
	.shop_table dl.variation,
	.select2-container .select2-choice,
	.select2-drop,
	.select2-drop-active,
	.form-row input[type="email"],
	.form-row input[type="number"],
	.form-row input[type="password"],
	.form-row input[type="search"],
	.form-row input[type="tel"],
	.form-row input[type="text"],
	.form-row input[type="url"],
	.form-row input[type="color"],
	.form-row input[type="date"],
	.form-row input[type="datetime"],
	.form-row input[type="datetime-local"],
	.form-row input[type="month"],
	.form-row input[type="time"],
	.form-row input[type="week"],
	.form-row select,
	.form-row textarea { color: <?php echo esc_html( $body_color ); ?> }

	@media (max-width: 992px) {
		.shop-loop-before, .shop-loop-before select { color: <?php echo porto_if_dark( '#fff', esc_html( $b['dark-color'] ) ); ?> }
		a.porto-product-filters-toggle svg { stroke: <?php echo porto_if_dark( '#fff', esc_html( $b['dark-color'] ) ); ?> }
	}

	ul.products li.cat-has-icon .thumb-info > i { color: <?php echo esc_html( $b['dark-color'] ); ?> }

	.quantity .minus:hover,
	.quantity .plus:hover,
	.stock,
	.product-image .viewcart,
	.widget_product_categories ul li > a:hover,
	.widget_layered_nav ul li > a:hover,
	.widget_layered_nav_filters ul li > a:hover,
	.widget_rating_filter ul li > a:hover,
	.widget_product_categories ul li > a:focus,
	.widget_layered_nav ul li > a:focus,
	.widget_layered_nav_filters ul li > a:focus,
	.widget_rating_filter ul li > a:focus,
	.widget_product_categories ul li .toggle,
	.widget_layered_nav ul li .toggle,
	.widget_layered_nav_filters ul li .toggle,
	.widget_rating_filter ul li .toggle,
	.widget_product_categories ul li.current > a,
	.widget_layered_nav ul li.current > a,
	.widget_layered_nav_filters ul li.current > a,
	.widget_rating_filter ul li.current > a,
	.widget_product_categories ul li.chosen > a,
	.widget_layered_nav_filters ul li.chosen > a,
	.widget_rating_filter ul li.chosen > a,
	.widget_layered_nav_filters ul li a:before,
	.widget_layered_nav .yith-wcan-select-wrapper ul.yith-wcan-select.yith-wcan li:hover a,
	.widget_layered_nav .yith-wcan-select-wrapper ul.yith-wcan-select.yith-wcan li.chosen a,
	ul.cart_list li .product-details a:hover,
	ul.product_list_widget li .product-details a:hover,
	ul.cart_list li a:hover,
	ul.product_list_widget li a:hover,
	.widget_shopping_cart .total .amount,
	.shipping_calculator h2,
	.cart_totals h2,
	.review-order.shop_table h2,
	.shipping_calculator h2 a,
	.cart_totals h2 a,
	.review-order.shop_table h2 a,
	.shop_table td.product-name,
	.product-subtotal .woocommerce-Price-amount,
	.woocommerce .featured-box h2,
	.woocommerce-page .featured-box h2,
	.woocommerce .featured-box h3,
	.woocommerce-page .featured-box h3,
	.woocommerce .featured-box h4,
	.woocommerce-page .featured-box h4,
	.product-thumbs-slider.owl-carousel .thumb-nav .thumb-next,
	.product-thumbs-slider.owl-carousel .thumb-nav .thumb-prev,
	ul.products li.cat-has-icon:hover .thumb-info > i { color: <?php echo esc_html( $skin_color ); ?> }

	.variations .reset_variations:hover {
		background-color: <?php echo esc_html( $skin_color ); ?>;
		color: <?php echo $b['skin-color-inverse']; ?>;
	}

	.product-image .viewcart:hover,
	.widget_price_filter .ui-slider .ui-slider-handle,
	li.product-outimage_aq_onimage .add-links .quickview,
	li.product-onimage .product-content .quickview,
	li.product-onimage2 .quickview,
	li.product-wq_onimage .links-on-image .quickview { background-color: <?php echo esc_html( $skin_color ); ?> }
	li.product-outimage_aq_onimage .add-links .quickview { color: <?php echo esc_html( $b['skin-color-inverse'] ); ?> }
	li.product-default:hover .add-links .add_to_cart_button,
	li.product-default:hover .add-links .add_to_cart_read_more,
	li.product-wq_onimage .add-links .button:hover,
	li.product-wq_onimage .yith-wcwl-add-to-wishlist a:hover,
	li.product-awq_onimage .add-links .button:hover,
	li.product-awq_onimage .add-links .quickview:hover,
	li.product-awq_onimage .add-links .yith-wcwl-add-to-wishlist a:hover,
	ul.list li.product .add_to_cart_button,
	ul.list li.product .add_to_cart_read_more { background-color: <?php echo esc_html( $skin_color ); ?>; border-color: <?php echo esc_html( $skin_color ); ?>; color: <?php echo esc_html( $b['skin-color-inverse'] ); ?> }

	.sidebar #yith-ajaxsearchform .btn { background: <?php echo esc_html( $skin_color ); ?>; }

	#yith-wcwl-popup-message,
	.woocommerce-cart .cart-form form,
	.product-layout-full_width .product-thumbnails-inner .img-thumbnail.selected,
	.product-layout-centered_vertical_zoom .product-thumbnails-inner .img-thumbnail.selected { border-color: <?php echo esc_html( $skin_color ); ?>; }

	.loader-container i.porto-ajax-loader { border-top-color: <?php echo esc_html( $skin_color ); ?>; }

	.summary-before .ms-lightbox-btn,
	.product-images .zoom { background-color: <?php echo esc_html( $skin_color ); ?>; }
	.summary-before .ms-lightbox-btn:hover { background-color: <?php echo esc_html( $porto_color_lib->lighten( $skin_color, 5 ) ); ?>; }
	.summary-before .ms-nav-next:before,
	.summary-before .ms-nav-prev:before,
	.summary-before .ms-thumblist-fwd:before,
	.summary-before .ms-thumblist-bwd:before { color: <?php echo esc_html( $skin_color ); ?>; }

	.add-links .add_to_cart_button,
	.add-links .add_to_cart_read_more,
	.add-links .quickview,
	.yith-wcwl-add-to-wishlist a,
	.yith-wcwl-add-to-wishlist a:hover,
	.yith-wcwl-add-to-wishlist span { background-color: <?php echo esc_html( $b['shop-add-links-bg-color'] ); ?>; border: 1px solid <?php echo esc_html( $b['shop-add-links-border-color'] ); ?>; color: <?php echo esc_html( $b['shop-add-links-color'] ); ?>; }
	ul.products li.product-outimage .add-links .quickview:hover,
	li.product-outimage .add-links .yith-wcwl-add-to-wishlist .add_to_wishlist:hover,
	ul.products li.product-outimage:hover .add-links .button,
	ul.products li.product-outimage .add-links .button:focus,
	.add-links .button:hover { background-color: <?php echo esc_html( $b['skin-color'] ); ?>; border-color: <?php echo esc_html( $b['skin-color'] ); ?>; color: <?php echo esc_html( $b['skin-color-inverse'] ); ?>; }

	.product-summary-wrap .yith-wcwl-add-to-wishlist a,
	.product-summary-wrap .yith-wcwl-add-to-wishlist a:hover,
	.product-summary-wrap .yith-wcwl-add-to-wishlist span { background: none; color: inherit; border: none; }
	.product-summary-wrap .yith-wcwl-add-to-wishlist a:before,
	.product-summary-wrap .yith-wcwl-add-to-wishlist span:before { color: <?php echo esc_html( $b['wishlist-color'] ); ?>; }
	<?php if ( $b['wishlist-color-inverse'] ) : ?>
	.product-summary-wrap .yith-wcwl-add-to-wishlist a:hover:before,
	.product-summary-wrap .yith-wcwl-add-to-wishlist span:hover:before { color: <?php echo esc_html( $b['wishlist-color-inverse'] ); ?>; }
	<?php endif; ?>

	.woocommerce-pagination a:hover,
	.woocommerce-pagination a:focus,
	.woocommerce-pagination span.current { border-color: <?php echo esc_html( $skin_color ); ?>; }

	ul.products li.product-col .product-loop-title:hover,
	ul.products li.product-col .product-loop-title:focus,
	ul.products li.product-col .product-loop-title:hover h3,
	ul.products li.product-col .product-loop-title:focus h3 { color: <?php echo esc_html( $skin_color ); ?>; }
	ul.products li.product-onimage3 .product-loop-title:hover h3,
	ul.products li.product-onimage3 .product-loop-title:focus h3 { color: #fff; }

	.woocommerce .widget_layered_nav ul.yith-wcan-label li a:hover,
	.woocommerce-page .widget_layered_nav ul.yith-wcan-label li a:hover,
	.woocommerce .widget_layered_nav ul.yith-wcan-label li.chosen a,
	.woocommerce-page .widget_layered_nav ul.yith-wcan-label li.chosen a,
	.filter-item-list .filter-item:not(.disabled):hover { background-color: <?php echo esc_html( $skin_color ); ?>; border-color: <?php echo esc_html( $skin_color ); ?>; }

	.product_title a:hover,
	.product_title a:focus { color: <?php echo esc_html( $skin_color ); ?>; }

	.widget_product_categories ul li .toggle:hover,
	.widget_price_filter ul li .toggle:hover,
	.widget_layered_nav ul li .toggle:hover,
	.widget_layered_nav_filters ul li .toggle:hover,
	.widget_rating_filter ul li .toggle:hover,
	.widget_price_filter ol li .toggle:hover,
	.widget_layered_nav_filters ol li .toggle:hover,
	.widget_rating_filter ol li .toggle:hover { color: <?php echo esc_html( $porto_color_lib->lighten( $skin_color, 5 ) ); ?>; }
	.widget_layered_nav ul li .count,
	.widget_product_categories ul li .count,
	.widget_rating_filter .wc-layered-nav-rating a { color: <?php echo porto_if_light( $porto_color_lib->lighten( $body_color, 5 ), $porto_color_lib->darken( $body_color, 5 ) ); ?>; }
	.widget_layered_nav_filters ul li a:hover:before { color: <?php echo esc_html( $porto_color_lib->lighten( $skin_color, 5 ) ); ?>; }
	.woocommerce .widget_layered_nav ul.yith-wcan-label li.chosen a:hover,
	.woocommerce-page .widget_layered_nav ul.yith-wcan-label li.chosen a:hover { background-color: <?php echo esc_html( $porto_color_lib->lighten( $skin_color, 5 ) ); ?>; border-color: <?php echo esc_html( $porto_color_lib->lighten( $skin_color, 5 ) ); ?>; }
	.woocommerce #content table.shop_table.wishlist_table.cart a.remove:hover, .shop_table.cart a.remove:hover { color: <?php echo esc_html( $skin_color ); ?>; }
	.woocommerce #content table.shop_table.wishlist_table.cart a.remove:active, .shop_table.cart a.remove:active { color: <?php echo esc_html( $porto_color_lib->darken( $skin_color, 5 ) ); ?>; }
	.wishlist_table tr td { background-color: <?php echo porto_if_light( '#fff', $dark_bg ); ?>; }
	.product-image .labels .onhot,
	.summary-before .labels .onhot { background: <?php echo esc_html( $b['hot-color'] ); ?>; color: <?php echo esc_html( $b['hot-color-inverse'] ); ?>; }
	.product-image .labels .onsale,
	.summary-before .labels .onsale { background: <?php echo esc_html( $b['sale-color'] ); ?>; color: <?php echo esc_html( $b['sale-color-inverse'] ); ?>; }
	.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a:before,
	.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a:before,
	.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a:hover:before,
	.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a:hover:before { color: <?php echo esc_html( $b['sale-color'] ); ?> }
	.success-message-container.style-2 { border-top: 4px solid <?php echo esc_html( $skin_color ); ?>; }

	.woocommerce-tabs h2.resp-tab-active { border-bottom-color: <?php echo esc_html( $skin_color ); ?> !important; }
	.resp-vtabs.style-2 .resp-tabs-list li.resp-tab-active { border-bottom: 2px solid <?php echo esc_html( $skin_color ); ?> !important; }
	.featured-box.porto-user-box { border-top-color: <?php echo esc_html( $skin_color ); ?>; }
	.woocommerce-widget-layered-nav-list .chosen a:not(.filter-color),
	.filter-item-list .active .filter-item { background-color: <?php echo esc_html( $skin_color ); ?>; color: #fff; border-color: <?php echo esc_html( $skin_color ); ?>; }

	.woocommerce-tabs .resp-tabs-list li.resp-tab-active,
	.woocommerce-tabs .resp-tabs-list li:hover { border-color: <?php echo porto_if_light( esc_html( $color_dark ), '#fff' ); ?> !important; }
	.single_add_to_cart_button { background: <?php echo porto_if_light( esc_html( $color_dark ), $color_dark_3 ); ?>; }
	.single-product .product-summary-wrap .yith-wcwl-add-to-wishlist { color: <?php echo porto_if_light( esc_html( $color_dark ), '#fff' ); ?> }
	.single-product .product-summary-wrap .share-links a:not(:hover) { background: none; color: <?php echo porto_if_light( esc_html( $color_dark ), '#fff' ); ?>; border: 2px solid #e7e7e7 }

	<?php if ( isset( $b['product_variation_display_mode'] ) && 'button' == $b['product_variation_display_mode'] ) : ?>
		.single-product .variations .label label { line-height: 32px; }
	<?php endif; ?>

	/* WC Marketplace Compatibility */
	<?php if ( class_exists( 'WC_Dependencies_Product_Vendor' ) ) : ?>
		.wcmp-wrapper .nav { display: block; }
		.wcmp-wrapper .top-user-nav>li a.dropdown-toggle:after { display: none; }
		.wcmp-wrapper select,
		.wcmp-wrapper select.form-control { background-size: auto; }
		.wcmp-wrapper .row-actions span.divider { margin: 0; background-image: none; }
		.wcmp-wrapper .content-padding:not(.dashboard) .dataTables_wrapper.dt-bootstrap .dataTables_paginate li { text-indent: 0; width: auto; }
		.wcmp-wrapper .input-group-addon { width: auto; line-height: inherit; }
	<?php endif; ?>


	<?php if ( porto_is_product() ) : ?>
		.sidebar-content { padding-bottom: 15px; }
	<?php endif; ?>

	/* daily sale */
	.sale-product-daily-deal .daily-deal-title,
	.sale-product-daily-deal .porto_countdown { font-family: 'Oswald', <?php echo sanitize_text_field( $b['h3-font']['font-family'] ); ?>, sans-serif; text-transform: uppercase; }
	.entry-summary .sale-product-daily-deal { margin-top: 10px; }
	.entry-summary .sale-product-daily-deal .porto_countdown { margin-bottom: 5px; }
	.entry-summary .sale-product-daily-deal .porto_countdown-section { background-color: <?php echo esc_html( $b['skin-color'] ); ?>; color: #fff; margin-left: 1px; margin-right: 1px; display: block; float: <?php echo porto_filter_output( $left ); ?>; max-width: calc(25% - 2px); min-width: 64px; padding: 12px 10px; }
	.entry-summary .sale-product-daily-deal .porto_countdown .porto_countdown-amount { display: block; font-size: 18px; font-weight: 700; }
	.entry-summary .sale-product-daily-deal .porto_countdown-period { font-size: 10px; }
	.entry-summary .sale-product-daily-deal:after { content: ''; display: table; clear: both; }
	.entry-summary .sale-product-daily-deal .daily-deal-title { text-transform: uppercase; }

	.products .sale-product-daily-deal { position: absolute; left: 10px; right: 10px; bottom: 10px; color: #fff; padding: 5px 0; text-align: center; }
	.products .sale-product-daily-deal:before { content: ''; position: absolute; left: 0; width: 100%; top: 0; height: 100%; background: <?php echo esc_html( $b['skin-color'] ); ?>; opacity: 0.7; }
	.products .sale-product-daily-deal > h5,
	.products .sale-product-daily-deal > div { position: relative; z-index: 1; }
	.products .sale-product-daily-deal .daily-deal-title { display: inline-block; color: #fff; font-size: 11px; font-weight: 400; margin-bottom: 0; margin-<?php echo porto_filter_output( $right ); ?>: 1px; }
	.products .sale-product-daily-deal .porto_countdown { float: none; display: inline-block; text-transform: uppercase; margin-bottom: 0; width: auto; }
	.products .sale-product-daily-deal .porto_countdown-section { padding: 0; margin-bottom: 0; }
	.products .sale-product-daily-deal .porto_countdown-section:first-child:after { content: ','; margin-<?php echo porto_filter_output( $right ); ?>: 2px; }
	.products .sale-product-daily-deal .porto_countdown-amount,
	.products .sale-product-daily-deal .porto_countdown-period { font-size: 13px; font-weight: 500; padding: 0 1px; }
	.products .sale-product-daily-deal .porto_countdown-section:last-child .porto_countdown-period { padding: 0; }
	.products .sale-product-daily-deal:after { content: ''; display: table; clear: both; }

	<?php if ( isset( $porto_settings['product-attr-desc'] ) && $porto_settings['product-attr-desc'] ) : ?>
		.product-attr-description { padding: 1rem 0 .125rem; }
		.product-attr-description > div { display: none; }
		.product-attr-description > a { font-size: .8571em; font-style: italic; color: inherit; text-decoration: none; display: none; }
		.product-attr-description.active > a { display: inline-block; }
		.product-attr-description .attr-desc { padding: .5rem 0 0; display: none; }
		.product-attr-description .attr-desc.active { display: block; }
	<?php endif; ?>

<?php endif; ?>

/*---------------- Skeleton screen ---------------- */
<?php
	$skeleton_color  = isset( $b['placeholder-color'] ) ? $b['placeholder-color'] : '#f4f4f4';
	$animation_color = $dark ? $porto_color_lib->hexToRGB( $color_dark ) : '255, 255, 255';
?>
<?php if ( ! empty( $b['show-skeleton-screen'] ) ) : ?>
	.skeleton-loading, .skeleton-loading-wrap { height: 0 !important; overflow: hidden !important; visibility: hidden; margin-top: 0 !important; margin-bottom: 0 !important; padding: 0 !important; min-height: 0 !important; }
	@keyframes skeletonloading {
		to {
			background-position: 200% 0;
		}
	}
	.skeleton-body.product .entry-summary,
	.products.skeleton-body li,
	.skeleton-body.product .tab-content,
	.skeleton-body .post { overflow: hidden; position: relative; }
	.skeleton-body.product .entry-summary:after,
	.products.skeleton-body li:after,
	.sidebar-content.skeleton-body aside:after,
	.skeleton-body.product .tab-content:after,
	.skeleton-body .post:after {
		content: ''; position: absolute; top: -50%; left: -50%; right: -50%; bottom: -50%; transform: rotate(45deg);
		background-image: linear-gradient(90deg, rgba(<?php echo esc_html( $animation_color ); ?>, 0) 20%, rgba(<?php echo esc_html( $animation_color ); ?>, 0.8) 50%, rgba(<?php echo esc_html( $animation_color ); ?>, 0) 80%);
		background-size: 60% 100%;
		background-position: -100% 0;
		background-repeat: no-repeat;
		animation: skeletonloading 1.5s infinite .2s;
	}
	.skeleton-body.product .tab-content:after { background-image: linear-gradient(135deg, rgba(<?php echo esc_html( $animation_color ); ?>, 0) 20%, rgba(<?php echo esc_html( $animation_color ); ?>, 0.8) 50%, rgba(<?php echo esc_html( $animation_color ); ?>, 0) 80%); transform: none; animation-duration: 2s; }
	<?php
	if ( class_exists( 'Woocommerce' ) ) {
		$cropping = get_option( 'woocommerce_thumbnail_cropping', '1:1' );
		if ( 'custom' === $cropping ) {
			$width  = (float) max( 1, get_option( 'woocommerce_thumbnail_cropping_custom_width', '4' ) );
			$height = (float) max( 1, get_option( 'woocommerce_thumbnail_cropping_custom_height', '3' ) );
		}
	}
	?>
	<?php if ( class_exists( 'Woocommerce' ) && ( in_array( 'product', $b['show-skeleton-screen'] ) || in_array( 'quickview', $b['show-skeleton-screen'] ) ) ) : ?>
		.skeleton-body.product { position: relative; z-index: 1; }
		.skeleton-body.product-layout-transparent .product-summary-wrap:before,
		.skeleton-body.product-layout-transparent .product-summary-wrap:after { content: none; }
		.skeleton-body.product .summary-before { order: initial; }

		/* quickview */
		.skeleton-body.product .summary-before { padding-top: 59%; }
		.skeleton-body.product .entry-summary { min-height: 500px; overflow: hidden; }
		.skeleton-body.product > .row { align-items: flex-start; }
		.skeleton-body.product > .row > div:before { content: ''; display: block; position: absolute; top: 0; bottom: 0; left: 0; right: 0; margin: 0 <?php echo (int) $b['grid-gutter-width'] / 2; ?>px; background-repeat: no-repeat; }
		.skeleton-body.product .summary-before:before {
			background-image: linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 100%, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 100%, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 100%, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 100%, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 100%, transparent 0);
			background-size: 100% calc(80% - 4px), calc(25% - 6px) calc(20% - 4px), calc(25% - 6px) calc(20% - 4px), calc(25% - 6px) calc(20% - 4px), calc(25% - 6px) calc(20% - 4px);
			background-position: center top, left bottom, 33.3333% bottom, 66.6666% bottom, right bottom;
		}
		.skeleton-body.product .entry-summary:before {
			background-image: linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 25px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 18px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 16px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 16px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 1px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 25px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 35px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 25px, transparent 0);
			background-size: 55% 25px, 70% 14px, 40% 18px, 100% 14px, 100% 14px, 100% 14px, 100% 14px, 40% 16px, 40% 16px, 100% 1px, 70% 25px, 60% 35px, 60% 25px;
			background-position: <?php echo porto_filter_output( $left ); ?> top, <?php echo porto_filter_output( $left ); ?> 34px, <?php echo porto_filter_output( $left ); ?> 75px, <?php echo porto_filter_output( $left ); ?> 120px, <?php echo porto_filter_output( $left ); ?> 147px, <?php echo porto_filter_output( $left ); ?> 174px, <?php echo porto_filter_output( $left ); ?> 201px, <?php echo porto_filter_output( $left ); ?> 240px, <?php echo porto_filter_output( $left ); ?> 270px, <?php echo porto_filter_output( $left ); ?> 320px, <?php echo porto_filter_output( $left ); ?> 350px, <?php echo porto_filter_output( $left ); ?> 395px, <?php echo porto_filter_output( $left ); ?> 455px;
		}
		@media (max-width: 991px) {
			.skeleton-body.product .summary-before { margin-bottom: 2em; padding-top: 100%; width: 640px; max-width: 100%; }
		}
	<?php endif; ?>
	<?php if ( class_exists( 'Woocommerce' ) && in_array( 'product', $b['show-skeleton-screen'] ) ) : ?>
		@media (min-width: 768px) {
			.skeleton-body.product-layout-default .summary-before.col-md-5,
			.skeleton-body.product-layout-builder .summary-before.col-md-5 { padding-top: 50% }
		}
		.skeleton-body.product .tab-content { min-height: 180px; position: relative; margin-top: 3rem; padding-top: 0 !important; }
		.skeleton-body.product .tab-content:before {
			background-image: linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 40px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 40px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 40px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0);
			background-size: 100px 40px, 100px 40px, 100px 40px, 100% 14px, 100% 14px, 100% 14px, 100% 14px;
			background-position: 0 0, 120px 0, 240px 0, 0 70px, 0 100px, 0 130px, 0 160px;
		}
	<?php endif; ?>
	<?php if ( class_exists( 'Woocommerce' ) && in_array( 'shop', $b['show-skeleton-screen'] ) ) : ?>
		<?php
		if ( 'custom' === $cropping ) {
			$product_h = round( ( $height / $width - 1 ) * 50 + 100 );
			$thumb_h   = round( $product_h * 0.62 );
		} else {
			$product_h = 100;
			$thumb_h   = 62;
		}
		?>
		.products.skeleton-body li:before {
			content: ''; display: block; padding-top: calc(<?php echo (int) $product_h; ?>% + 110px); background-repeat: no-repeat;
			background-image: linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 100%, transparent 0),
			linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 12px, transparent 0),
			linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 16px, transparent 0),
			linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 12px, transparent 0),
			linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 16px, transparent 0);
			background-size: 100% <?php echo (int) $thumb_h; ?>%, 50% 12px, 70% 16px, 50% 12px, 40% 16px;
			background-position: <?php echo porto_filter_output( $left ); ?> 0, <?php echo porto_filter_output( $left ); ?> calc(<?php echo (int) $thumb_h; ?>% + 20px), <?php echo porto_filter_output( $left ); ?> calc(<?php echo (int) $thumb_h; ?>% + 44px), <?php echo porto_filter_output( $left ); ?> calc(<?php echo (int) $thumb_h; ?>% + 66px), <?php echo porto_filter_output( $left ); ?> calc(<?php echo (int) $thumb_h; ?>% + 92px);
		}
		.grid.skeleton-body .product-default:before,
		.grid.skeleton-body .product-wq_onimage:before { background-position-x: center, center, center, center, center }
		<?php $bar_bg_pos = $rtl ? 'calc(100% - 270px)' : '270px'; ?>
		@media (min-width: 576px) {
			.list.skeleton-body li:before {
				padding-top: 250px;
				background-image: linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 250px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 12px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 16px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 12px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 16px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 20px, transparent 0);
				background-size: 250px 100%, 150px 12px, 250px 16px, 150px 12px, 100% 14px, 100% 14px, 120px 16px, 200px 20px;
				background-position: <?php echo porto_filter_output( $left ); ?> 0, <?php echo porto_filter_output( $bar_bg_pos ); ?> 20px, <?php echo porto_filter_output( $bar_bg_pos ); ?> 44px, <?php echo porto_filter_output( $bar_bg_pos ); ?> 70px, <?php echo porto_filter_output( $bar_bg_pos ); ?> 105px, <?php echo porto_filter_output( $bar_bg_pos ); ?> 130px, <?php echo porto_filter_output( $bar_bg_pos ); ?> 170px, <?php echo porto_filter_output( $bar_bg_pos ); ?> 205px;
			}
			.list.skeleton-body li:after { transform: none; background-image: linear-gradient(135deg, rgba(<?php echo esc_html( $animation_color ); ?>, 0) 40%, rgba(<?php echo esc_html( $animation_color ); ?>, 0.8) 50%, rgba(<?php echo esc_html( $animation_color ); ?>, 0) 60%); animation: skeletonloading 2.5s infinite .2s; }
		}
	<?php endif; ?>
	<?php if ( in_array( 'blog', $b['show-skeleton-screen'] ) ) : ?>
		.skeleton-body .post { border-bottom: none; margin-bottom: 30px; padding-bottom: 0; }
		.skeleton-body .post:before {
			content: ''; display: block; padding-top: calc(60% + 165px); background-repeat: no-repeat;
			background-image: linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 100%, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 16px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
				linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 20px, transparent 0);
			background-size: 100% calc(100% - 165px), 50% 16px, 100% 14px, 100% 14px, 100% 14px, 70% 20px;
			background-position: 0 0, <?php echo porto_filter_output( $left ); ?> calc(100% - 130px), <?php echo porto_filter_output( $left ); ?> calc(100% - 100px), <?php echo porto_filter_output( $left ); ?> calc(100% - 75px), <?php echo porto_filter_output( $left ); ?> calc(100% - 50px), <?php echo porto_filter_output( $left ); ?> calc(100% - 10px);
		}
		@media (min-width: 992px) {
			.posts-medium.skeleton-body .post:before,
			.posts-medium-alt.skeleton-body .post:before {
				padding-top: 25%; min-height: 180px;
				background-image: linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 100%, transparent 0),
					linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 16px, transparent 0),
					linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
					linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
					linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
					linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
					linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 20px, transparent 0);
				background-size: 40% 100%, 30% 16px, 58% 14px, 58% 14px, 58% 14px, 58% 14px, 58% 20px;
				background-position: <?php echo ! $rtl ? '0' : '100%'; ?> 0, <?php echo ! $rtl ? '60%' : '40%'; ?> 10px, <?php echo ! $rtl ? '100%' : '0'; ?> 45px, <?php echo ! $rtl ? '100%' : '0'; ?> 70px, <?php echo ! $rtl ? '100%' : '0'; ?> 95px, <?php echo ! $rtl ? '100%' : '0'; ?> 120px, <?php echo ! $rtl ? '100%' : '0'; ?> 155px;
			}
			.posts-medium.skeleton-body .post:after,
			.posts-medium-alt.skeleton-body .post:after { transform: none; background-image: linear-gradient(135deg, rgba(<?php echo esc_html( $animation_color ); ?>, 0) 40%, rgba(<?php echo esc_html( $animation_color ); ?>, 0.8) 50%, rgba(<?php echo esc_html( $animation_color ); ?>, 0) 60%); animation: skeletonloading 2s infinite .2s; }
		}
	<?php endif; ?>

	<?php $bar_bg_pos = $rtl ? 'calc(100% - 45px)' : '45px'; ?>
	.sidebar-content.skeleton-body aside { overflow: hidden; position: relative; }
	.sidebar-content.skeleton-body aside:before {
		content: ''; display: block; height: 320px; background-repeat: no-repeat;
		background-image: linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 20px, transparent 0),
			linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 30px, transparent 0), linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
			linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 30px, transparent 0), linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
			linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 30px, transparent 0), linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
			linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 30px, transparent 0), linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
			linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 30px, transparent 0), linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0),
			linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 30px, transparent 0), linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 14px, transparent 0);
		background-size: 55% 20px, 30px 30px, 100% 14px, 30px 30px, 60% 14px, 30px 30px, 80% 14px, 30px 30px, 50% 14px, 30px 30px, 100% 14px, 30px 30px, 70% 14px;
		background-position: <?php echo porto_filter_output( $left ); ?> 5px, <?php echo porto_filter_output( $left ); ?> 50px, <?php echo porto_filter_output( $bar_bg_pos ); ?> 58px, <?php echo porto_filter_output( $left ); ?> 95px, <?php echo porto_filter_output( $bar_bg_pos ); ?> 103px, <?php echo porto_filter_output( $left ); ?> 140px, <?php echo porto_filter_output( $bar_bg_pos ); ?> 148px, <?php echo porto_filter_output( $left ); ?> 185px, <?php echo porto_filter_output( $bar_bg_pos ); ?> 193px, <?php echo porto_filter_output( $left ); ?> 230px, <?php echo porto_filter_output( $bar_bg_pos ); ?> 238px, <?php echo porto_filter_output( $left ); ?> 275px, <?php echo porto_filter_output( $bar_bg_pos ); ?> 283px;

	}
<?php endif; ?>

<?php if ( ! empty( $porto_settings_optimize['lazyload_menu'] ) ) : ?>
	<?php
		$nav_loading_color = $b['panel-bg-color'] ? $porto_color_lib->hexToRGB( $b['panel-bg-color'] ) : ( $dark ? $porto_color_lib->hexToRGB( $color_dark ) : '255, 255, 255' );
	if ( empty( $b['mobile-panel-type'] ) ) {
		$nav_skeleton_color = isset( $b['placeholder-color'] ) ? $b['placeholder-color'] : '#f4f4f4';
		$nav_selector       = '#nav-panel';
	} else {
		$nav_skeleton_color = $porto_color_lib->lighten( $b['panel-bg-color'], 15 );
		$nav_selector       = '#side-nav-panel';
	}
	?>
	@keyframes side_nav_loading {
		to {
			background-position: 200% 0;
		}
	}

	<?php if ( empty( $b['mobile-panel-type'] ) ) : ?>
		#nav-panel .skeleton-body { position: relative; overflow: hidden; }
		#nav-panel .skeleton-body:after {
			content: ''; position: absolute; top: -50%; left: -50%; right: -50%; bottom: -50%;
			transform: rotate(45deg);
			background-image: linear-gradient(90deg, rgba(<?php echo esc_html( $nav_loading_color ); ?>, 0) 20%, rgba(<?php echo esc_html( $nav_loading_color ); ?>, 0.8) 50%, rgba(<?php echo esc_html( $nav_loading_color ); ?>, 0) 80%);
			background-size: 60% 100%;
			background-position: -100% 0;
			background-repeat: no-repeat;
			animation: side_nav_loading 1.5s infinite .2s;
		}
		@media (min-width: 480px) {
			#nav-panel .skeleton-body:after {
				transform: none;
				background-image: linear-gradient(135deg, rgba(<?php echo esc_html( $nav_loading_color ); ?>, 0) 20%, rgba(<?php echo esc_html( $nav_loading_color ); ?>, 0.8) 50%, rgba(<?php echo esc_html( $nav_loading_color ); ?>, 0) 80%);
				animation-duration: 2s;
			}
		}
		<?php echo porto_filter_output( $nav_selector ); ?> .skeleton-body:before {
			content: ''; display: block; min-height: 200px; background-repeat: no-repeat;
			background-image: linear-gradient(<?php echo esc_html( $nav_skeleton_color ); ?> 20px, transparent 0),
				linear-gradient(<?php echo esc_html( $nav_skeleton_color ); ?> 20px, transparent 0),
				linear-gradient(<?php echo esc_html( $nav_skeleton_color ); ?> 20px, transparent 0),
				linear-gradient(<?php echo esc_html( $nav_skeleton_color ); ?> 20px, transparent 0),
				linear-gradient(<?php echo esc_html( $nav_skeleton_color ); ?> 20px, transparent 0);
			background-size: 100% 20px, 100% 20px, 100% 20px, 100% 20px, 100% 20px;
			background-position: 0 0, 0 40px, 0 80px, 0 120px, 0 160px;
		}
	<?php endif; ?>

	.menu-item.narrow .sub-menu.skeleton-body,
	.menu-item.wide .sub-menu.skeleton-body li { overflow: hidden; position: relative; }
	.menu-item.narrow .sub-menu.skeleton-body {  }
	.menu-item.wide .sub-menu.skeleton-body li { -ms-flex: 1; flex: 1; }
	.menu-item.narrow .sub-menu.skeleton-body:after,
	.menu-item.wide .sub-menu.skeleton-body li:after {
		content: ''; position: absolute; top: -50%; left: -50%; right: -50%; bottom: -50%; background-repeat: no-repeat;
		transform: rotate(45deg);
		background-image: linear-gradient(90deg, rgba(<?php echo esc_html( $animation_color ); ?>, 0) 20%, rgba(<?php echo esc_html( $animation_color ); ?>, 0.8) 50%, rgba(<?php echo esc_html( $animation_color ); ?>, 0) 80%);
		background-size: 60% 100%;
		background-position: -100% 0;
		background-repeat: no-repeat;
	}
	.menu-item.narrow .sub-menu.skeleton-body:before,
	.menu-item.wide .sub-menu.skeleton-body li:before {
		content: ''; display: block; background-repeat: no-repeat; min-height: 180px; margin: 15px 10px 0;
		background-image: linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 18px, transparent 0),
			linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 18px, transparent 0),
			linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 18px, transparent 0),
			linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 18px, transparent 0),
			linear-gradient(<?php echo esc_html( $skeleton_color ); ?> 18px, transparent 0);
		background-size: 100% 18px, 100% 18px, 100% 18px, 100% 18px, 100% 18px;
		background-position: 0 0, 0 36px, 0 72px, 0 108px, 0 144px;
	}
	.mega-menu > li.menu-item:hover .skeleton-body:after,
	.mega-menu > li.menu-item:hover .skeleton-body li:after {
		animation: side_nav_loading 1.5s infinite .2s;
	}
<?php endif; ?>

/*---------------- Elementor Styles ---------------- */
<?php if ( defined( 'ELEMENTOR_VERSION' ) ) : ?>
	/* shop builder */
	.shop-loop-before.elementor-section { display: block }
	.shop-loop-before > .elementor-container { margin-left: auto; margin-right: auto }
	.shop-loop-before .elementor-widget-wrap { align-items: center }
	.shop-loop-before .elementor-widget-wrap > .elementor-element:not(.elementor-section) { width: auto; margin-<?php echo porto_filter_output( $right ); ?>: 10px; margin-bottom: 0; max-width: 100% }
	.elementor-editor-active .shop-loop-before .elementor-widget-wrap > .elementor-element:not(.elementor-section) { min-width: 100px }

	.elementor-section { display: -ms-flexbox; display: flex; -ms-flex-pack: center; justify-content: center }
	.elementor-section .elementor-container { width: 100%; margin-left: 0; margin-right: 0 }
	.elementor-section.elementor-section-boxed > .elementor-container { max-width: <?php echo (int) $b['container-width']; ?>px }
	.container-fluid .elementor-top-section { margin-left: -<?php echo (int) $b['grid-gutter-width']; ?>px; margin-right: -<?php echo (int) $b['grid-gutter-width']; ?>px; padding-left: <?php echo (int) $b['grid-gutter-width']; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width']; ?>px }
	.container-fluid .elementor-top-section.elementor-section-full_width > .elementor-column-gap-no { margin-left: -<?php echo (int) $b['grid-gutter-width']; ?>px; margin-right: -<?php echo (int) $b['grid-gutter-width']; ?>px; width: calc(100% + <?php echo (int) $b['grid-gutter-width'] * 2; ?>px) }
	.elementor .pricing-table { padding-left: 0; padding-right: 0 }

	.elementor-container > .porto-carousel { width: 100%; }
	.porto-carousel .elementor-section.elementor-section-boxed > .elementor-container { margin: 0; max-width: 100%; }

	.elementor-section > .elementor-column-gap-default { margin-left: -<?php echo (int) $b['grid-gutter-width'] / 2; ?>px; margin-right: -<?php echo (int) $b['grid-gutter-width'] / 2; ?>px; width: calc(100% + <?php echo (int) $b['grid-gutter-width']; ?>px) }
	.elementor-section > .elementor-column-gap-narrow { margin-left: -5px; margin-right: -5px; width: calc(100% + 10px) }
	.elementor-section > .elementor-column-gap-extended { margin-left: -15px; margin-right: -15px; width: calc(100% + 30px) }
	.elementor-section > .elementor-column-gap-wide { margin-left: -20px; margin-right: -20px; width: calc(100% + 40px) }
	.elementor-section > .elementor-column-gap-wider { margin-left: -30px; margin-right: -30px; width: calc(100% + 60px) }
	.elementor-column-gap-default > .elementor-row > .elementor-column > .elementor-element-populated,
	.elementor-column-gap-default > .elementor-column > .elementor-element-populated { padding: <?php echo (int) $b['grid-gutter-width'] / 2; ?>px }
	.elementor-section.elementor-section-boxed > .elementor-column-gap-no { max-width: <?php echo (int) $b['container-width'] - (int) $b['grid-gutter-width']; ?>px }
	.elementor-section.elementor-section-boxed > .elementor-column-gap-narrow { max-width: <?php echo (int) $b['container-width'] - (int) $b['grid-gutter-width'] + 10; ?>px }
	.elementor-section.elementor-section-boxed > .elementor-column-gap-extended { max-width: <?php echo (int) $b['container-width'] - (int) $b['grid-gutter-width'] + 30; ?>px }
	.elementor-section.elementor-section-boxed > .elementor-column-gap-wide { max-width: <?php echo (int) $b['container-width'] - (int) $b['grid-gutter-width'] + 40; ?>px }
	.elementor-section.elementor-section-boxed > .elementor-column-gap-wider { max-width: <?php echo (int) $b['container-width'] - (int) $b['grid-gutter-width'] + 60; ?>px }

	@media (min-width: 992px) and (max-width: <?php echo (int) $b['container-width'] + $b['grid-gutter-width'] - 1; ?>px) {
		.elementor-section.elementor-section-boxed > .elementor-container { max-width: 960px }
		.elementor-section.elementor-section-boxed > .elementor-column-gap-no { max-width: <?php echo 960 - (int) $b['grid-gutter-width']; ?>px }
		.elementor-section.elementor-section-boxed > .elementor-column-gap-narrow { max-width: <?php echo 960 - (int) $b['grid-gutter-width'] + 10; ?>px }
		.elementor-section.elementor-section-boxed > .elementor-column-gap-extended { max-width: <?php echo 960 - (int) $b['grid-gutter-width'] + 30; ?>px }
		.elementor-section.elementor-section-boxed > .elementor-column-gap-wide { max-width: <?php echo 960 - (int) $b['grid-gutter-width'] + 40; ?>px }
		.elementor-section.elementor-section-boxed > .elementor-column-gap-wider { max-width: <?php echo 960 - (int) $b['grid-gutter-width'] + 60; ?>px }
	}

	<?php if ( (int) $b['grid-gutter-width'] > $max_spacing_mobile ) : ?>
		@media (max-width: 480px) {
			.container-fluid .elementor-top-section { margin-left: -<?php echo (int) $max_spacing_mobile; ?>px; margin-right: -<?php echo (int) $max_spacing_mobile; ?>px; padding-left: <?php echo (int) $max_spacing_mobile; ?>px; padding-right: <?php echo (int) $max_spacing_mobile; ?>px }
			.container-fluid .elementor-top-section.elementor-section-full_width > .elementor-column-gap-no { margin-left: -<?php echo (int) $max_spacing_mobile; ?>px; margin-right: -<?php echo (int) $max_spacing_mobile; ?>px; width: calc(100% + <?php echo (int) $max_spacing_mobile * 2; ?>px) }
		}
	<?php endif; ?>

	.porto-carousel.elementor-row,
	.elementor-section > .porto-ibanner,
	.porto-carousel,
	.elementor-widget-wrap .porto-sicon-box,
	.elementor-widget-wrap .porto-u-heading,
	.porto-u-heading p:last-child,
	.elementor-widget-wrap .circular-bar { margin-bottom: 0 }
	.porto-carousel .elementor-column,
	.elementor-column-wrap > .porto-carousel,
	.elementor-column > .porto-carousel { width: 100% }

	.porto-ibanner > .elementor-row { position: absolute; top: 0; left: 0; right: 0; height: 100%; }
	.porto-ibanner-layer { width: auto }
	.elementor-editor-active .elementor-element-empty.porto-ibanner-layer { width: 100% }
	.porto-ibanner > .elementor-row > .elementor-column,
	.porto-ibanner > .elementor-column { position: absolute; top: 0; left: 0; right: 0; height: 100%; width: 100% }
	.container > .porto-ibanner-layer { margin-left: <?php echo (int) $b['grid-gutter-width'] / 2; ?>px; margin-right: <?php echo (int) $b['grid-gutter-width'] / 2; ?>px }
	.porto-ibanner .porto-ibanner-layer > .elementor-widget-wrap,
	.porto-ibanner .porto-ibanner-layer.elementor-widget-wrap { display: block }
	@media (max-width: 991px) {
		.elementor-section.elementor-section-stretched > .elementor-container { padding-left: <?php echo (int) $b['grid-gutter-width']; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width']; ?>px }
		.container > .porto-ibanner-layer { margin-left: <?php echo (int) $b['grid-gutter-width']; ?>px; margin-right: <?php echo (int) $b['grid-gutter-width']; ?>px }
	}
	<?php if ( (int) $b['grid-gutter-width'] > $max_spacing_mobile ) : ?>
		@media (max-width: 480px) {
			.elementor-section.elementor-section-stretched > .elementor-container { padding-left: <?php echo (int) $max_spacing_mobile; ?>px; padding-right: <?php echo (int) $max_spacing_mobile; ?>px }
			.container > .porto-ibanner-layer { margin-left: <?php echo (int) $max_spacing_mobile; ?>px; margin-right: <?php echo (int) $max_spacing_mobile; ?>px }
		}
	<?php endif; ?>

	.elementor-row[data-plugin-masonry], .elementor-container[data-plugin-masonry], .elementor-widget-wrap.owl-carousel.owl-loaded { display: block; }
	.elementor-row[data-plugin-masonry] > .elementor-column,
	.elementor-container[data-plugin-masonry] > .elementor-column { float: <?php echo porto_filter_output( $left ); ?> }

	.elementor-widget-container > .btn { position: relative }
	.elementor-widget-container > .btn .dir-arrow { position: absolute; top: -35px; <?php echo porto_filter_output( $left ); ?>: 85% }

	.fullscreen-carousel .elementor-section,
	.fullscreen-carousel .elementor-section > * { height: 100% }

	.elementor-element[data-plugin-parallax], .elementor-element > [data-plugin-parallax] { background-attachment: scroll !important }

	/* header builder */
	.elementor-section.header-main { -ms-flex-wrap: wrap; flex-wrap: wrap }
	.header-builder-p .elementor-widget-wrap > .elementor-element:not(.elementor-section) { width: auto; margin-<?php echo porto_filter_output( $right ); ?>: .875rem; margin-bottom: 0; max-width: 100% }
	.header-builder-p .elementor-widget-wrap > .elementor-element:last-child { margin-<?php echo porto_filter_output( $right ); ?>: 0 }

	<?php if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) : ?>
		.elementor-products-grid .products li.product-col { max-width: none; padding: 0; margin-bottom: 0 }
		.elementor-products-grid .products li.product-col .woocommerce-loop-product__title,
		.elementor-products-grid .products .category-list { white-space: normal }
		.elementor-products-grid nav.woocommerce-pagination { margin-top: 0 }
	<?php endif; ?>
<?php endif; ?>

/*---------------- Visual Composer Styles ---------------- */
<?php if ( defined( 'VCV_VERSION' ) ) : ?>
	.container-fluid .vce-row-container { margin-left: -<?php echo (int) $b['grid-gutter-width']; ?>px; margin-right: -<?php echo (int) $b['grid-gutter-width']; ?>px; width: calc(100% + <?php echo (int) $b['grid-gutter-width'] * 2; ?>px) }
	.container-fluid .no-inner-container,
	.vce-row[data-vce-stretch-content="true"] { padding-left: <?php echo (int) $b['grid-gutter-width']; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width']; ?>px }
	<?php if ( 30 !== (int) $b['grid-gutter-width'] ) : ?>
		.vce-row.vce-element--has-background { padding-left: <?php echo (int) $b['grid-gutter-width']; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width']; ?>px; padding-top: <?php echo (int) $b['grid-gutter-width']; ?>px }
	<?php endif; ?>
	<?php if ( (int) $b['grid-gutter-width'] > $max_spacing_mobile ) : ?>
		@media (max-width: 480px) {
			.container-fluid .vce-row-container { margin-left: -<?php echo (int) $max_spacing_mobile; ?>px; margin-right: -<?php echo (int) $max_spacing_mobile; ?>px; width: calc(100% + <?php echo (int) $max_spacing_mobile * 2; ?>px) }
			.vce-row.vce-element--has-background,
			.container-fluid .no-inner-container,
			.vce-row[data-vce-stretch-content="true"] { padding-left: <?php echo (int) $max_spacing_mobile; ?>px; padding-right: <?php echo (int) $max_spacing_mobile; ?>px; padding-top: <?php echo (int) $max_spacing_mobile; ?>px }
		}
	<?php endif; ?>
	.container-fluid .vce-row .no-inner-container { padding-left: 0; padding-right: 0 }

	.vce-row--col-gap-<?php echo (int) $b['grid-gutter-width']; ?> > .vce-row-content > .vce-col { margin-<?php echo porto_filter_output( $right ); ?>: <?php echo (int) $b['grid-gutter-width']; ?>px }
	.vce-row--col-gap-<?php echo (int) $b['grid-gutter-width']; ?> > .vce-row-content > .vce-column-resizer .vce-column-resizer-handler { width: <?php echo (int) $b['grid-gutter-width']; ?>px }
	.container-fluid .vce-row-container .vce-row-container { margin-left: 0; margin-right: 0; width: 100% }
	.vcwb .porto-ibanner { margin-bottom: 0 }
	.fullscreen-carousel .vce-element-porto-banner-wrapper, .fullscreen-carousel .vce-element-porto-banner-wrapper .porto-ibanner, .fullscreen-carousel .vce-element-porto-banner-wrapper img  { height: 100%; }
	.vc-porto-overlay { opacity: .3 }
	.vcvhelper[data-vcvs-html^="[porto_"] { transition: opacity .2s }

	a.porto-heading-wrap { display: inline-block }

	/* header builder */
	.vce-row.header-main { display: block }
	.header-builder-p [class^="vce-porto-hb-"] { display: inline-block; vertical-align: middle; margin-<?php echo porto_filter_output( $right ); ?>: .875rem; }
	.header-builder-p [class^="vce-porto-hb-"]:last-child { margin-<?php echo porto_filter_output( $right ); ?>: 0 }
	.header-builder-p .vce-col-content > *:not(.vce-row-container) { margin-<?php echo porto_filter_output( $right ); ?>: .875rem; margin-bottom: 0; max-width: 100% }
	.header-builder-p .vce-col-content > *:last-child { margin-<?php echo porto_filter_output( $right ); ?>: 0 }

	.header-builder-p .vce-col-content { display: flex; align-items: center; flex-wrap: wrap }
	.header-builder-p .vce-col-content > .vce-row-container { flex: 0 0 100%; max-width: 100% }
	.header-builder-p .porto-block .porto-block .vce-col-content { display: block }
	.header-builder-p .vce-col-content > *:last-child,
	.header-builder-p .porto-block .porto-block .vce-col-content > * { margin-<?php echo porto_filter_output( $right ); ?>: 0 }
<?php endif; ?>

/*---------------- WPBakery Styles ---------------- */
<?php if ( defined( 'WPB_VC_VERSION' ) ) : ?>
	.compose-mode .vc_element[class*="vc_porto_hb_"] { display: inline-block; vertical-align: middle; margin-<?php echo porto_filter_output( $right ); ?>: .875rem }
	.compose-mode .vc_element[class*="vc_porto_hb_"]:last-child { margin-<?php echo porto_filter_output( $right ); ?>: 0 }
	
	.header-builder-p .vc_column-inner { display: flex; align-items: center; flex-wrap: wrap }
	.header-builder-p .vc_row-o-content-middle>.vc_column_container>.vc_column-inner { flex-direction: row; justify-content: flex-start }
	.header-builder-p .vc_column-inner > *:not(.vc_row) { margin-<?php echo porto_filter_output( $right ); ?>: .875rem }
	.header-builder-p .vc_column-inner > *:last-child { margin-<?php echo porto_filter_output( $right ); ?>: 0 }
	.header-builder-p .vc_column-inner > .vc_row,
	.header-builder-p .vc_column-inner > .vc_vc_row_inner { width: calc(100% + <?php echo (int) $b['grid-gutter-width']; ?>px) }
	.header-builder-p .porto-block .porto-block .vc_column-inner { display: block }
	.header-builder-p .porto-block .porto-block .vc_column-inner > * { margin-<?php echo porto_filter_output( $right ); ?>: 0 }
	#header.header-builder-p .header-main .container > div { display: flex }
<?php endif; ?>

/*---------------- Gutenberg Styles ---------------- */
.wp-block-columns, .page-content > .wp-block-columns.alignwide { margin-left: -<?php echo (int) $b['grid-gutter-width'] / 2; ?>px; margin-right: -<?php echo (int) $b['grid-gutter-width'] / 2; ?>px; flex-wrap: wrap }
@media (min-width: 768px) {
	.wp-block-columns.alignwide { max-width: none; width: auto }
}
.wp-block-column { padding-left: <?php echo (int) $b['grid-gutter-width'] / 2; ?>px; padding-right: <?php echo (int) $b['grid-gutter-width'] / 2; ?>px }
.wp-block-column:not[class*=" col-"] { flex-basis: 50%; }
.wp-block-columns .wp-block-column { margin-left: 0; margin-right: 0 }
@media (max-width: 781px) and (min-width: 600px) {
	.wp-block-column {
		flex-basis: 50%!important;
		flex-grow: 0;
	}
}
.btn.btn-block { display: block; padding-left: 0; padding-right: 0 }

/*---------------- Sticky Icon Bar ---------------- */
<?php if ( ! empty( $porto_settings['show-icon-menus-mobile'] ) || is_customize_preview() ) : ?>
	.porto-sticky-navbar:not(.fixed) { display: none }
	.porto-sticky-navbar > div { text-align: center; padding: 10px 0 5px;}
	.porto-sticky-navbar .sticky-icon + .sticky-icon { border-<?php echo porto_filter_output( $left ); ?>: 1px solid <?php echo porto_if_dark( '#333', '#f1f1f1' ); ?>; }
	.porto-sticky-navbar a, .porto-sticky-navbar .label { color: <?php echo porto_if_dark( '#fff', '#222529' ); ?>; }
	.porto-sticky-navbar i { font-size: 27px; display: block; line-height: 27px; margin-bottom: 4px }
	.porto-sticky-navbar .porto-icon-bars { font-size: 22px; }
	.porto-sticky-navbar .label { display: block; font-family: <?php echo sanitize_text_field( $b['h4-font']['font-family'] ); ?>, sans-serif; text-transform: uppercase; font-size: 9px; letter-spacing: -.5px; font-weight: 600; }
	.porto-sticky-navbar .cart-icon { display: inline-block; position: relative; }
	.porto-sticky-navbar .cart-items { display: inline-block; position: absolute; width: 15px; height: 15px; top: -2px; right: -6px; background-color: #ed5348; color: #fff; line-height: 15px; font-size: 9px; font-weight: 600; text-align: center; border-radius: 8px; box-shadow: -1px 1px 2px 0 rgba(0,0,0,0.3); overflow: hidden; }
	@media (max-width: 575px) {
		.porto-sticky-navbar.fixed { 
			display: flex;
			position: fixed;
			bottom: 0;
			<?php echo porto_filter_output( $left ); ?>: 0;
			width: 100%;
			background-color: <?php echo porto_if_dark( $color_dark_1, '#fff' ); ?>; 
			border-top: 1px solid <?php echo porto_if_dark( '#333', '#e7e7e7' ); ?>;
			z-index: 1040;
			animation: .3s linear menuFadeInUp;
			transition: <?php echo porto_filter_output( $left ); ?> .3s;
		}
		.sidebar-opened .porto-sticky-navbar.fixed, .panel-opened .porto-sticky-navbar.fixed {
			<?php echo porto_filter_output( $left ); ?>: 260px;
		}
		#footer { margin-bottom: 3.75rem }
	}
<?php endif; ?>
