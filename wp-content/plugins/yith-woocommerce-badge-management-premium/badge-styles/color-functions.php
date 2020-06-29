<?php
if ( ! function_exists( 'yith_wcbm_create_dark_color' ) ) {
    function yith_wcbm_create_dark_color($col){
        $r = $col[0] . $col[1];
        $g = $col[2] . $col[3];
        $b = $col[4] . $col[5];
        $r_d = hexdec($r);
        $g_d = hexdec($g);
        $b_d = hexdec($b);
        $r1_d =180/255 * $r_d; 
        $g1_d =180/255 * $g_d; 
        $b1_d =180/255 * $b_d; 
        $r1_0 = '';
        $g1_0 = '';
        $b1_0 = '';
        if ( ($r1_d) < 16) {
            $r1_0 = '0';
        }
        if (($g1_d) < 16) {
            $g1_0 = '0';
        }
        if ( ($b1_d) < 16) {
            $b1_0 = '0';
        }
        $r1 = $r1_0 . dechex($r1_d);
        $g1 = $g1_0 . dechex($g1_d);
        $b1 = $b1_0 . dechex($b1_d);
        $dark_color = $r1 . $g1 . $b1;
        return $dark_color;
    }
}

if ( ! function_exists( 'yith_wcbm_color_with_factor' ) ) {
    function yith_wcbm_color_with_factor($col, $fact){
        $r = $col[0] . $col[1];
        $g = $col[2] . $col[3];
        $b = $col[4] . $col[5];
        $r_d = hexdec($r);
        $g_d = hexdec($g);
        $b_d = hexdec($b);
        $r1_d = $fact * $r_d; 
        $g1_d = $fact * $g_d; 
        $b1_d = $fact * $b_d; 
        $r1_0 = '';
        $g1_0 = '';
        $b1_0 = '';
        if ( ($r1_d) < 16) {
            $r1_0 = '0';
        }
        if (($g1_d) < 16) {
            $g1_0 = '0';
        }
        if ( ($b1_d) < 16) {
            $b1_0 = '0';
        }
        $r1 = $r1_0 . dechex($r1_d);
        $g1 = $g1_0 . dechex($g1_d);
        $b1 = $b1_0 . dechex($b1_d);
        $dark_color = $r1 . $g1 . $b1;
        return $dark_color;
    }
}

if ( ! function_exists( 'yith_wcbm_proportional_color' ) ) {
    function yith_wcbm_proportional_color($col, $col1, $col2){
        $r = $col[0] . $col[1];
        $g = $col[2] . $col[3];
        $b = $col[4] . $col[5];

        $r1 = $col1[0] . $col1[1];
        $g1 = $col1[2] . $col1[3];
        $b1 = $col1[4] . $col1[5];

        $r2 = $col2[0] . $col2[1];
        $g2 = $col2[2] . $col2[3];
        $b2 = $col2[4] . $col2[5];

        $r_d = hexdec($r);
        $g_d = hexdec($g);
        $b_d = hexdec($b);

        $r1_d = hexdec($r1);
        $g1_d = hexdec($g1);
        $b1_d = hexdec($b1);
        
        $r2_d = hexdec($r2);
        $g2_d = hexdec($g2);
        $b2_d = hexdec($b2);

        $rr_d = $r_d * $r2_d / $r1_d; 
        $gr_d = $g_d * $g2_d / $g1_d; 
        $br_d = $b_d * $b2_d / $b1_d; 
        $rr_0 = '';
        $gr_0 = '';
        $br_0 = '';
        if ( ($rr_d) < 16) {
            $rr_0 = '0';
        }
        if (($gr_d) < 16) {
            $gr_0 = '0';
        }
        if ( ($br_d) < 16) {
            $br_0 = '0';
        }

        $rr = $rr_0 . dechex($rr_d);
        $gr = $gr_0 . dechex($gr_d);
        $br = $br_0 . dechex($br_d);
        $p_color = $rr . $gr . $br;
        return $p_color;
    }
}