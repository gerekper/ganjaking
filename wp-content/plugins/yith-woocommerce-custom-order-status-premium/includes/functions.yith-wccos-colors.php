<?php
/**
 * Color functions.
 *
 * @package YITH\CustomOrderStatus
 */

defined( 'YITH_WCCOS' ) || exit; // Exit if accessed directly.

if ( ! function_exists( 'yith_wccos_rgb_to_hsl' ) ) {
	/**
	 * Convert RGB to HSL.
	 *
	 * @param int $r Red.
	 * @param int $g Green.
	 * @param int $b Blue.
	 *
	 * @return array
	 */
	function yith_wccos_rgb_to_hsl( $r, $g, $b ) {
		$r /= 255;
		$g /= 255;
		$b /= 255;

		$max = max( $r, $g, $b );
		$min = min( $r, $g, $b );
		$h   = 0;
		$s   = 0;
		$l   = ( $max + $min ) / 2;
		$d   = $max - $min;
		if ( 0 === $d ) {
			$h = 0;
			$s = 0;
		} else {
			$s = $d / ( 1 - abs( 2 * $l - 1 ) );
			switch ( $max ) {
				case $r:
					$h = 60 * fmod( ( ( $g - $b ) / $d ), 6 );
					if ( $b > $g ) {
						$h += 360;
					}
					break;
				case $g:
					$h = 60 * ( ( $b - $r ) / $d + 2 );
					break;
				case $b:
					$h = 60 * ( ( $r - $g ) / $d + 4 );
					break;
			}
		}

		return array( round( $h, 2 ), round( $s, 2 ), round( $l, 2 ) );
	}
}

if ( ! function_exists( 'yith_wccos_darken_color' ) ) {
	/**
	 * Darken color.
	 *
	 * @param int $h Hue.
	 * @param int $s Saturation.
	 * @param int $l Light.
	 *
	 * @return array
	 */
	function yith_wccos_darken_color( $h, $s, $l ) {
		$r = 0;
		$g = 0;
		$b = 0;
		$c = ( 1 - abs( 2 * $l - 1 ) ) * $s;
		$x = $c * ( 1 - abs( fmod( ( $h / 60 ), 2 ) - 1 ) );
		$m = $l - ( $c / 2 );
		if ( $h < 60 ) {
			$r = $c;
			$g = $x;
			$b = 0;
		} elseif ( $h < 120 ) {
			$r = $x;
			$g = $c;
			$b = 0;
		} elseif ( $h < 180 ) {
			$r = 0;
			$g = $c;
			$b = $x;
		} elseif ( $h < 240 ) {
			$r = 0;
			$g = $x;
			$b = $c;
		} elseif ( $h < 300 ) {
			$r = $x;
			$g = 0;
			$b = $c;
		} else {
			$r = $c;
			$g = 0;
			$b = $x;
		}
		$r = ( $r + $m ) * 255;
		$g = ( $g + $m ) * 255;
		$b = ( $b + $m ) * 255;

		return array( floor( $r ), floor( $g ), floor( $b ) );
	}
}

if ( ! function_exists( 'yith_wccos_color_with_fact' ) ) {
	/**
	 * Change color with factor.
	 *
	 * @param string $col  The color.
	 * @param float  $fact The factor.
	 *
	 * @return string
	 */
	function yith_wccos_color_with_fact( $col, $fact ) {
		$col = str_replace( '#', '', $col );
		if ( $fact > 0 ) {
			// Lighter.
			$fact = $fact < 1 ? 1 / ( 1 - $fact ) : 1;
		} else {
			// Darken.
			$fact = - $fact;
			$fact = $fact < 1 ? 1 - $fact : 0;
		}

		$r    = $col[0] . $col[1];
		$g    = $col[2] . $col[3];
		$b    = $col[4] . $col[5];
		$r_d  = hexdec( $r );
		$g_d  = hexdec( $g );
		$b_d  = hexdec( $b );
		$r1_d = min( 255, $fact * $r_d + 80 * $fact );
		$g1_d = min( 255, $fact * $g_d + 80 * $fact );
		$b1_d = min( 255, $fact * $b_d + 80 * $fact );

		$r1_0 = '';
		$g1_0 = '';
		$b1_0 = '';
		if ( ( $r1_d ) < 16 ) {
			$r1_0 = '0';
		}
		if ( ( $g1_d ) < 16 ) {
			$g1_0 = '0';
		}
		if ( ( $b1_d ) < 16 ) {
			$b1_0 = '0';
		}
		$r1 = $r1_0 . dechex( $r1_d );
		$g1 = $g1_0 . dechex( $g1_d );
		$b1 = $b1_0 . dechex( $b1_d );

		$dark_color = '#' . $r1 . $g1 . $b1;

		return $dark_color;
	}
}

if ( ! function_exists( 'yith_wccos_darken_color' ) ) {
	/**
	 * Dark color with factor.
	 *
	 * @param string $col  The color.
	 * @param float  $fact The factor.
	 *
	 * @return string
	 */
	function yith_wccos_darken_color( $col, $fact ) {
		$col = str_replace( '#', '', $col );

		$r   = $col[0] . $col[1];
		$g   = $col[2] . $col[3];
		$b   = $col[4] . $col[5];
		$r_d = hexdec( $r );
		$g_d = hexdec( $g );
		$b_d = hexdec( $b );

		list( $h, $s, $l ) = yith_wccos_rgb_to_hsl( $r_d, $g_d, $b_d );

		$l -= $fact;

		list( $r_d, $g_d, $b_d ) = yith_wccos_hsl_to_rgb( $h, $s, $l );

		$r_0 = '';
		$g_0 = '';
		$b_0 = '';
		if ( ( $r_d ) < 16 ) {
			$r_0 = '0';
		}
		if ( ( $g_d ) < 16 ) {
			$g_0 = '0';
		}
		if ( ( $b_d ) < 16 ) {
			$b_0 = '0';
		}

		$r1 = $r_0 . dechex( $r_d );
		$g1 = $g_0 . dechex( $g_d );
		$b1 = $b_0 . dechex( $b_d );

		return '#' . $r1 . $g1 . $b1;
	}
}

if ( ! function_exists( 'yith_wccos_lighter_color' ) ) {
	/**
	 * Light color with factor.
	 *
	 * @param string $col  The color.
	 * @param float  $fact The factor.
	 *
	 * @return string
	 */
	function yith_wccos_lighter_color( $col, $fact ) {
		$col = str_replace( '#', '', $col );

		$r   = $col[0] . $col[1];
		$g   = $col[2] . $col[3];
		$b   = $col[4] . $col[5];
		$r_d = hexdec( $r );
		$g_d = hexdec( $g );
		$b_d = hexdec( $b );

		list( $h, $s, $l ) = yith_wccos_rgb_to_hsl( $r_d, $g_d, $b_d );

		$l += $fact;

		list( $r_d, $g_d, $b_d ) = yith_wccos_hsl_to_rgb( $h, $s, $l );

		$r_0 = '';
		$g_0 = '';
		$b_0 = '';
		if ( ( $r_d ) < 16 ) {
			$r_0 = '0';
		}
		if ( ( $g_d ) < 16 ) {
			$g_0 = '0';
		}
		if ( ( $b_d ) < 16 ) {
			$b_0 = '0';
		}
		$r1 = $r_0 . dechex( $r_d );
		$g1 = $g_0 . dechex( $g_d );
		$b1 = $b_0 . dechex( $b_d );

		return '#' . $r1 . $g1 . $b1;
	}
}


if ( ! function_exists( 'yith_wccos_lightness' ) ) {
	/**
	 * Return the lightness.
	 *
	 * @param string $col The color.
	 *
	 * @return mixed
	 */
	function yith_wccos_lightness( $col ) {
		$col = str_replace( '#', '', $col );

		$r   = $col[0] . $col[1];
		$g   = $col[2] . $col[3];
		$b   = $col[4] . $col[5];
		$r_d = hexdec( $r );
		$g_d = hexdec( $g );
		$b_d = hexdec( $b );

		list( $h, $s, $l ) = yith_wccos_rgb_to_hsl( $r_d, $g_d, $b_d );

		return $l;
	}
}

if ( ! function_exists( 'yith_wccos_is_light_color' ) ) {
	/**
	 * Is this a light color?
	 *
	 * @param string $color The color.
	 *
	 * @return bool
	 */
	function yith_wccos_is_light_color( $color ) {
		$color = yith_wccos_format_hex_color( $color );

		$hex = str_replace( '#', '', $color );
		$c_r = hexdec( substr( $hex, 0, 2 ) );
		$c_g = hexdec( substr( $hex, 2, 2 ) );
		$c_b = hexdec( substr( $hex, 4, 2 ) );

		$brightness = ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000;

		return $brightness > 155;
	}
}

if ( ! function_exists( 'yith_wccos_light_or_dark' ) ) {
	/**
	 * If the color is light, return the dark color; otherwise the light one.
	 *
	 * @param string $color The color to check.
	 * @param string $dark  The dark color.
	 * @param string $light The light color.
	 *
	 * @return mixed|string
	 * @since 1.2.6
	 */
	function yith_wccos_light_or_dark( $color, $dark = '#000000', $light = '#FFFFFF' ) {
		return yith_wccos_is_light_color( $color ) ? $dark : $light;
	}
}

if ( ! function_exists( 'yith_wccos_format_hex_color' ) ) {
	/**
	 * Format an hex color
	 *
	 * @param string $color The color in hex or rgb format.
	 *
	 * @return string|null
	 */
	function yith_wccos_format_hex_color( $color ) {
		$color = trim( $color );
		if ( strpos( $color, 'rgb' ) === 0 ) {
			$color  = str_replace( array( 'rgb', '(', ')' ), '', $color );
			$colors = array_map( 'absint', explode( ',', $color ) );
			if ( count( $colors ) >= 3 ) {
				$color = sprintf( '#%02x%02x%02x', $colors[0], $colors[1], $colors[2] );
			} else {
				$color = '';
			}
		}

		$hex = trim( str_replace( '#', '', $color ) );

		if ( strlen( $hex ) === 3 ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}

		return $hex ? '#' . $hex : '';
	}
}
