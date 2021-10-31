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

require_once( PORTO_LIB . '/lib/color-lib.php' );
$porto_color_lib = PortoColorLib::getInstance();

global $reduxPortoSettings;
$css_vars = $reduxPortoSettings->get_css_vars();
if ( empty( $css_vars ) ) {
	return;
}

$container_selectors = '';
foreach ( $css_vars as $selector => $lines ) {
	echo esc_html( $selector ) . '{';
	foreach ( $lines as $line ) {
		$option_name = $line[0];
		if ( isset( $line[2] ) && 'typography' == $line[2] ) {
			$var_name        = str_replace( '-font', '', $option_name );
			$typography_vars = array( 'font-family' => 'ff', 'font-weight' => 'fw', 'font-size' => 'fs', 'line-height' => 'lh', 'letter-spacing' => 'ls', 'color' => 'color' );
			foreach ( $typography_vars as $tv => $ab_name ) {
				if ( ! empty( $b[ $option_name ][ $tv ] ) ) {
					$var_val = $b[ $option_name ][ $tv ];
					if ( 'ff' == $ab_name ) {
						$var_val = '"' . $var_val . '"';
					}
					echo '--porto-' . esc_html( $var_name ) . '-' . $ab_name . ':' . sanitize_text_field( $var_val ) . ';';
				}
			}
			if ( 'body-font' == $option_name && empty( $b[ $option_name ]['color'] ) ) {
				echo '--porto-body-color:#777;';
			}
		} elseif ( ! empty( $b[ $option_name ] ) ) {
			echo '--porto-' . esc_html( $option_name ) . ':' . esc_html( $b[ $option_name ] );

			if ( 'container-width' == $option_name ) {
				$container_selectors = $selector;
			}
		}
		if ( ! empty( $line[1] ) ) {
			echo esc_html( $line[1] );
		}
		echo ';';
	}

	if ( ':root' == $selector ) {
		echo '--porto-column-spacing:' . ( (int) $b['grid-gutter-width'] / 2 ) . 'px;';
		echo '--porto-res-spacing:' . ( (int) $b['grid-gutter-width'] / 2 ) . 'px;';
		echo '--porto-skin-dark-5:' . esc_html( $porto_color_lib->darken( $b['skin-color'], 5 ) ) . ';';
		echo '--porto-skin-dark-10:' . esc_html( $porto_color_lib->darken( $b['skin-color'], 10 ) ) . ';';
		echo '--porto-skin-dark-20:' . esc_html( $porto_color_lib->darken( $b['skin-color'], 20 ) ) . ';';
		echo '--porto-skin-light-5:' . esc_html( $porto_color_lib->lighten( $b['skin-color'], 5 ) ) . ';';
	}
	echo '}';
}

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

/* bootstrap row */
.row > * {
	width: 100%
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
		}
	}
	<?php
}
