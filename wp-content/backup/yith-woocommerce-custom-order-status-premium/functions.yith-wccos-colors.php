<?php
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

if ( !function_exists( 'yith_wccos_rgb_to_hsl' ) ) {

    function yith_wccos_rgb_to_hsl( $r, $g, $b ) {
        $r   /= 255;
        $g   /= 255;
        $b   /= 255;
        $max = max( $r, $g, $b );
        $min = min( $r, $g, $b );
        $h   = $s = 0;
        $l   = ( $max + $min ) / 2;
        $d   = $max - $min;
        if ( $d == 0 ) {
            $h = $s = 0; // achromatic
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

if ( !function_exists( 'yith_wccos_darken_color' ) ) {
    function yith_wccos_darken_color( $h, $s, $l ) {
        $r = $g = $b = 0;
        $c = ( 1 - abs( 2 * $l - 1 ) ) * $s;
        $x = $c * ( 1 - abs( fmod( ( $h / 60 ), 2 ) - 1 ) );
        $m = $l - ( $c / 2 );
        if ( $h < 60 ) {
            $r = $c;
            $g = $x;
            $b = 0;
        } else if ( $h < 120 ) {
            $r = $x;
            $g = $c;
            $b = 0;
        } else if ( $h < 180 ) {
            $r = 0;
            $g = $c;
            $b = $x;
        } else if ( $h < 240 ) {
            $r = 0;
            $g = $x;
            $b = $c;
        } else if ( $h < 300 ) {
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

if ( !function_exists( 'yith_wccos_color_with_fact' ) ) {
    function yith_wccos_color_with_fact( $col, $fact ) {
        $col = str_replace( '#', '', $col );
        if ( $fact > 0 ) {
            //lighter
            $fact = $fact < 1 ? 1 / ( 1 - $fact ) : 1;
        } else {
            //darken
            $fact = -$fact;
            $fact = $fact < 1 ? 1 - $fact : 0;
        }

        $r    = $col[ 0 ] . $col[ 1 ];
        $g    = $col[ 2 ] . $col[ 3 ];
        $b    = $col[ 4 ] . $col[ 5 ];
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

if ( !function_exists( 'yith_wccos_darken_color' ) ) {
    function yith_wccos_darken_color( $col, $fact ) {
        $col = str_replace( '#', '', $col );

        $r   = $col[ 0 ] . $col[ 1 ];
        $g   = $col[ 2 ] . $col[ 3 ];
        $b   = $col[ 4 ] . $col[ 5 ];
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

if ( !function_exists( 'yith_wccos_lighter_color' ) ) {
    function yith_wccos_lighter_color( $col, $fact ) {
        $col = str_replace( '#', '', $col );

        $r   = $col[ 0 ] . $col[ 1 ];
        $g   = $col[ 2 ] . $col[ 3 ];
        $b   = $col[ 4 ] . $col[ 5 ];
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


if ( !function_exists( 'yith_wccos_lightness' ) ) {
    function yith_wccos_lightness( $col ) {
        $col = str_replace( '#', '', $col );

        $r   = $col[ 0 ] . $col[ 1 ];
        $g   = $col[ 2 ] . $col[ 3 ];
        $b   = $col[ 4 ] . $col[ 5 ];
        $r_d = hexdec( $r );
        $g_d = hexdec( $g );
        $b_d = hexdec( $b );
        list( $h, $s, $l ) = yith_wccos_rgb_to_hsl( $r_d, $g_d, $b_d );

        return $l;
    }
}

if ( !function_exists( 'yith_wccos_is_light_color' ) ) {
    function yith_wccos_is_light_color( $col ) {
        return yith_wccos_lightness( $col ) > 0.5;
    }
}