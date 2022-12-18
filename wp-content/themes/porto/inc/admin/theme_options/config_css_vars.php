<?php
/**
 * @package Porto
 * @author P-Theme
 * @since 6.2.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $porto_is_dark, $porto_settings;
$porto_settings_backup = $porto_settings;
$b                     = porto_check_theme_options();
$porto_settings        = $porto_settings_backup;
$porto_is_dark         = ( 'dark' == $b['css-type'] );
$dark                  = $porto_is_dark;

if ( $dark ) {
	$color_dark = $b['color-dark'];
} else {
	$color_dark = $b['dark-color'];
}

require_once PORTO_LIB . '/lib/color-lib.php';
$porto_color_lib = PortoColorLib::getInstance();

global $reduxPortoSettings;
$css_vars = $reduxPortoSettings->get_css_vars();
if ( empty( $css_vars ) ) {
	return;
}

// variables
$body_mobile_font_size_scale   = ( 0 == (float) $b['body-font']['font-size'] || 0 == (float) $b['body-mobile-font']['font-size'] ) ? 1 : ( (float) $b['body-mobile-font']['font-size'] / (float) $b['body-font']['font-size'] );
$body_mobile_line_height_scale = ( 0 == (float) $b['body-font']['line-height'] || 0 == (float) $b['body-mobile-font']['line-height'] ) ? 1 : ( (float) $b['body-mobile-font']['line-height'] / (float) $b['body-font']['line-height'] );

// responsive fonts
$responsive_fonts = array(
	'body-font',
	'h1-font',
	'h2-font',
	'h3-font',
	'h4-font',
	'h5-font',
	'h6-font',
);
$mobile_vars      = '';

$container_selectors = '';
foreach ( $css_vars as $selector => $lines ) {
	$lines_mobile_vars = '';
	echo esc_html( $selector ) . '{';
	foreach ( $lines as $line ) {
		$option_name = $line[0];
		if ( isset( $line[2] ) && 'typography' == $line[2] ) {
			$var_name        = str_replace( '-font', '', $option_name );
			$typography_vars = array(
				'font-family'    => 'ff',
				'font-weight'    => 'fw',
				'font-size'      => 'fs',
				'font-style'     => 'fst',
				'line-height'    => 'lh',
				'letter-spacing' => 'ls',
				'color'          => 'color',
			);
			foreach ( $typography_vars as $tv => $ab_name ) {
				if ( ! empty( $b[ $option_name ][ $tv ] ) ) {
					$var_val = $b[ $option_name ][ $tv ];
					if ( 'ff' == $ab_name ) {
						$var_val = '"' . $var_val . '"';
					}
					echo '--porto-' . esc_html( $var_name ) . '-' . $ab_name . ':' . sanitize_text_field( $var_val ) . ';';
				}
			}
			if ( 'body-font' == $option_name ) {
				if ( empty( $b[ $option_name ]['color'] ) ) {
					echo '--porto-body-color:#777;';
				}
				echo '--porto-body-color-light-5:' . esc_html( $porto_color_lib->lighten( empty( $b['body-font']['color'] ) ? '#777' : $b['body-font']['color'], 5 ) ) . ';';
			}
		} elseif ( ! empty( $b[ $option_name ] ) ) {
			$var_name = $option_name;
			if ( 'skin-color' == $option_name || 'skin-color-inverse' == $option_name ) {
				$var_name = str_replace( 'skin-color', 'primary-color', $option_name );
			}
			if ( is_array( $b[ $option_name ] ) ) {
				foreach ( $b[ $option_name ] as $o_k => $o_v ) {
					echo '--porto-' . esc_html( $var_name . '-' . $o_k ) . ':' . esc_html( $o_v ) . ';';
				}
			} else {
				echo '--porto-' . esc_html( $var_name ) . ':' . esc_html( $b[ $option_name ] );
				if ( empty( $line[1] ) ) { // if hasn't unit
					echo ';';
				}
			}

			if ( 'container-width' == $option_name ) {
				$container_selectors = $selector;
			}
		}
		if ( ! empty( $line[1] ) ) { // if has unit
			echo esc_html( $line[1] ) . ';';
		}

		// responsive variables
		if ( in_array( $option_name, $responsive_fonts ) && ( (float) 1 !== (float) $body_mobile_font_size_scale || (float) 1 !== (float) $body_mobile_line_height_scale ) ) {
			if ( ':root' == $selector ) {
				$lines_mobile_vars .= '--porto-mobile-fs-scale:' . round( (float) $body_mobile_font_size_scale, 4 ) . ';';
			}
			if ( isset( $line[2] ) && 'typography' == $line[2] ) {
				$var_name = str_replace( '-font', '', $option_name );
				if ( ! empty( $b[ $option_name ]['font-size'] ) ) {
					$lines_mobile_vars .= '--porto-' . esc_html( $var_name ) . '-fs:' . round( (float) $b[ $option_name ]['font-size'] * $body_mobile_font_size_scale, 4 ) . 'px;';
				}
				if ( ! empty( $b[ $option_name ]['line-height'] ) ) {
					$lines_mobile_vars .= '--porto-' . esc_html( $var_name ) . '-lh:' . round( (float) $b[ $option_name ]['line-height'] * $body_mobile_line_height_scale, 4 ) . 'px;';
				}
				if ( 'body-font' == $option_name && $b['body-mobile-font']['letter-spacing'] ) {
					$lines_mobile_vars .= '--porto-body-ls:' . esc_html( $b['body-mobile-font']['letter-spacing'] ) . ';';
				}
			} elseif ( ! empty( $b[ $option_name ] ) ) {

			}
		}
	}

	if ( ':root' == $selector ) {
		echo '--porto-column-spacing:' . ( (int) $b['grid-gutter-width'] / 2 ) . 'px;';
		echo '--porto-res-spacing:' . ( (int) $b['grid-gutter-width'] / 2 ) . 'px;';
		echo '--porto-fluid-spacing:' . ( (int) $b['grid-gutter-width'] ) . 'px;';
		echo '--porto-container-spacing:' . ( (int) $b['grid-gutter-width'] / 2 ) . 'px;';
		echo '--porto-primary-dark-5:' . esc_html( $porto_color_lib->darken( $b['skin-color'], 5 ) ) . ';';
		echo '--porto-primary-dark-10:' . esc_html( $porto_color_lib->darken( $b['skin-color'], 10 ) ) . ';';
		echo '--porto-primary-dark-15:' . esc_html( $porto_color_lib->darken( $b['skin-color'], 15 ) ) . ';';
		echo '--porto-primary-dark-20:' . esc_html( $porto_color_lib->darken( $b['skin-color'], 20 ) ) . ';';
		echo '--porto-primary-light-5:' . esc_html( $porto_color_lib->lighten( $b['skin-color'], 5 ) ) . ';';
		echo '--porto-primary-light-7:' . esc_html( $porto_color_lib->lighten( $b['skin-color'], 7 ) ) . ';';
		echo '--porto-primary-light-10:' . esc_html( $porto_color_lib->lighten( $b['skin-color'], 10 ) ) . ';';
		echo '--porto-primary-inverse-dark-10:' . esc_html( $porto_color_lib->darken( $b['skin-color-inverse'], 10 ) ) . ';';

		$theme_colors = array( 'secondary', 'tertiary', 'quaternary', 'dark', 'light' );
		foreach ( $theme_colors as $theme_color ) {
			echo '--porto-' . $theme_color . '-dark-5:' . esc_html( $porto_color_lib->darken( $b[ $theme_color . '-color' ], 5 ) ) . ';';
			echo '--porto-' . $theme_color . '-dark-10:' . esc_html( $porto_color_lib->darken( $b[ $theme_color . '-color' ], 10 ) ) . ';';
			echo '--porto-' . $theme_color . '-dark-15:' . esc_html( $porto_color_lib->darken( $b[ $theme_color . '-color' ], 15 ) ) . ';';
			echo '--porto-' . $theme_color . '-dark-20:' . esc_html( $porto_color_lib->darken( $b[ $theme_color . '-color' ], 20 ) ) . ';';
			echo '--porto-' . $theme_color . '-light-5:' . esc_html( $porto_color_lib->lighten( $b[ $theme_color . '-color' ], 5 ) ) . ';';
			echo '--porto-' . $theme_color . '-light-7:' . esc_html( $porto_color_lib->lighten( $b[ $theme_color . '-color' ], 7 ) ) . ';';
			echo '--porto-' . $theme_color . '-light-10:' . esc_html( $porto_color_lib->lighten( $b[ $theme_color . '-color' ], 10 ) ) . ';';
			echo '--porto-' . $theme_color . '-inverse-dark-10:' . esc_html( $porto_color_lib->darken( $b[ $theme_color . '-color-inverse' ], 10 ) ) . ';';
		}

		if ( ! empty( $porto_settings['show-skeleton-screen'] ) ) {
			$skeleton_color = isset( $porto_settings['placeholder-color'] ) ? $porto_settings['placeholder-color'] : '#f4f4f4';
			echo '--porto-placeholder-color:' . esc_html( $skeleton_color ) . ';';
		}
		// dark / light skin mode
		if ( $dark ) {
			$color_dark_inverse = '#fff';
			$color_dark_1       = $b['color-dark'];
			$color_dark_2       = $porto_color_lib->lighten( $color_dark_1, 2 );
			$color_dark_3       = $porto_color_lib->lighten( $color_dark_1, 5 );
			$color_dark_4       = $porto_color_lib->lighten( $color_dark_1, 8 );
			$color_dark_5       = $porto_color_lib->lighten( $color_dark_1, 3 );
			$color_darken_1     = $porto_color_lib->darken( $color_dark_1, 2 );

			echo '--porto-bgc: #000;';
			echo '--porto-body-bg:' . esc_html( $color_dark_1 ) . ';';
			echo '--porto-color-price: #eee;';
			echo '--porto-widget-bgc:' . esc_html( $color_dark_3 ) . ';';
			echo '--porto-title-bgc:' . esc_html( $color_dark_4 ) . ';';
			echo '--porto-widget-bc: transparent;';
			echo '--porto-input-bc:' . esc_html( $color_dark_3 ) . ';';
			echo '--porto-slide-bgc:' . esc_html( $color_dark_1 ) . ';';
			echo '--porto-heading-color:' . $color_dark_inverse . ';';
			echo '--porto-heading-light-8:' . $porto_color_lib->darken( $color_dark_inverse, 8 ) . ';';
			echo '--porto-normal-bg:' . esc_html( $color_dark_3 ) . ';';
			echo '--porto-gray-bg:' . esc_html( $color_dark_5 ) . ';';
			echo '--porto-gray-1:' . esc_html( $color_dark_3 ) . ';';
			echo '--porto-gray-2:' . esc_html( $color_dark_4 ) . ';';
			echo '--porto-gray-3:' . esc_html( $color_dark_4 ) . ';';
			echo '--porto-gray-4: #999;';
			echo '--porto-gray-5:' . esc_html( $color_dark_3 ) . ';';
			echo '--porto-gray-6:' . esc_html( $color_dark_3 ) . ';';
			echo '--porto-gray-7:' . esc_html( $color_dark_2 ) . ';';
			echo '--porto-gray-8:' . esc_html( $color_dark_3 ) . ';';
			echo '--porto-light-1:' . esc_html( $color_dark_4 ) . ';';
			echo '--porto-light-2:' . esc_html( $color_dark_5 ) . ';';
			echo '--porto-normal-bc:rgba(255, 255, 255, .06);';
			echo '--porto-label-bg1:rgba(0, 0, 0, .9);';
		} else {
			echo '--porto-bgc: #fff;';
			echo '--porto-body-bg: #fff;';
			echo '--porto-color-price: #444;';
			echo '--porto-widget-bgc: #fbfbfb;';
			echo '--porto-title-bgc: #f5f5f5;';
			echo '--porto-widget-bc: #ddd;';
			echo '--porto-input-bc: rgba(0, 0, 0, 0.08);';
			echo '--porto-slide-bgc: #e7e7e7;';
			echo '--porto-heading-color: #222529;';
			echo '--porto-heading-light-8:' . $porto_color_lib->lighten( '#222529', 8 ) . ';';
			echo '--porto-normal-bg: #fff;';
			echo '--porto-gray-bg: #dfdfdf;';
			echo '--porto-gray-1: #f4f4f4;';
			echo '--porto-gray-2: #e7e7e7;';
			echo '--porto-gray-3: #f4f4f4;';
			echo '--porto-gray-4: #ccc;';
			echo '--porto-gray-5: #e7e7e7;';
			echo '--porto-gray-6: #999;';
			echo '--porto-gray-7: #f4f4f4;';
			echo '--porto-gray-8: #f1f1f1;';
			echo '--porto-light-1: #fff;';
			echo '--porto-light-2: #fff;';
			echo '--porto-normal-bc:rgba(0, 0, 0, .06);';
			echo '--porto-label-bg1:rgba(255, 255, 255, .9);';
		}
	} elseif ( 'li.menu-item' == $selector ) {
		if ( class_exists( 'Woocommerce' ) ) {
			echo '--porto-submenu-item-bbw: 0;';
			echo '--porto-submenu-item-lrp: 15px;';
		}
	}
	echo '}';

	if ( $lines_mobile_vars ) {
		$mobile_vars .= esc_html( $selector ) . '{' . $lines_mobile_vars . '}';
	}
}

/* custom */
if ( class_exists( 'Woocommerce' ) ) {
	echo '#header { --porto-header-top-link-fw: 600; }';
}

if ( $mobile_vars ) :
	?>
	@media (max-width: 575px) {
		<?php echo porto_filter_output( $mobile_vars ); ?>
	}
	<?php
endif;

/* logo */
$logo_width        = ( isset( $porto_settings['logo-width'] ) && (int) $porto_settings['logo-width'] ) ? (int) $porto_settings['logo-width'] : 170;
$logo_width_wide   = ( isset( $porto_settings['logo-width-wide'] ) && (int) $porto_settings['logo-width-wide'] ) ? (int) $porto_settings['logo-width-wide'] : 250;
$logo_width_tablet = ( isset( $porto_settings['logo-width-tablet'] ) && (int) $porto_settings['logo-width-tablet'] ) ? (int) $porto_settings['logo-width-tablet'] : 110;
$logo_width_mobile = ( isset( $porto_settings['logo-width-mobile'] ) && (int) $porto_settings['logo-width-mobile'] ) ? (int) $porto_settings['logo-width-mobile'] : 110;
$logo_width_sticky = ( isset( $porto_settings['logo-width-sticky'] ) && (int) $porto_settings['logo-width-sticky'] ) ? (int) $porto_settings['logo-width-sticky'] : 80;
?>
#header .logo {
--porto-logo-mw: <?php echo (int) $logo_width; ?>px;
--porto-sticky-logo-mw: <?php echo empty( $porto_settings['change-header-logo'] ) ? (int) $logo_width_sticky : (int) $logo_width_sticky * 1.25; ?>px;
}
.side-header-narrow-bar-logo {
--porto-side-logo-mw: <?php echo (int) $logo_width; ?>px;
}

/* responsive */
@media (min-width: 992px) and (max-width: <?php echo (int) $porto_settings['container-width'] + (int) $porto_settings['grid-gutter-width'] - 1; ?>px) {
<?php
if ( ! empty( $container_selectors ) ) {
	echo esc_html( $container_selectors ) . '{ --porto-container-width: 960px }';
}
?>
}
@media (min-width: <?php echo (int) $porto_settings['container-width'] + (int) $porto_settings['grid-gutter-width']; ?>px) {
	#header .logo {
		--porto-logo-mw: <?php echo (int) $logo_width_wide; ?>px;
	}
}

@media (max-width: 991px) {
	:root {
		--porto-res-spacing: <?php echo (int) $b['grid-gutter-width']; ?>px;
	}
	#header .logo {
		--porto-logo-mw: <?php echo (int) $logo_width_tablet; ?>px;
	}
}
@media (max-width: 767px) {
	#header .logo {
		--porto-logo-mw: <?php echo (int) $logo_width_mobile; ?>px;
	}
}
<?php
if ( (int) $b['grid-gutter-width'] > 20 ) {
	?>
	@media (max-width: 575px) {
		:root {
			--porto-res-spacing: 20px;
			--porto-fluid-spacing: 20px;
			--porto-container-spacing: <?php echo 20 - (int) $b['grid-gutter-width'] / 2; ?>px;
		}
	}
	<?php
}
