<?php

$position_css = "";

$pos_top    = ( is_numeric( $pos_top ) ) ? ( $pos_top . "px" ) : $pos_top;
$pos_bottom = ( is_numeric( $pos_bottom ) ) ? ( $pos_bottom . "px" ) : $pos_bottom;
$pos_left   = ( is_numeric( $pos_left ) ) ? ( $pos_left . "px" ) : $pos_left;
$pos_right  = ( is_numeric( $pos_right ) ) ? ( $pos_right . "px" ) : $pos_right;

$position_css .= "top: " . $pos_top . ";";
$position_css .= "bottom: " . $pos_bottom . ";";
$position_css .= "left: " . $pos_left . ";";
$position_css .= "right: " . $pos_right . ";";

$custom_rotation = 'rotateX(' . $rotation[ 'x' ] . 'deg) rotateY(' . $rotation[ 'y' ] . 'deg) rotateZ(' . $rotation[ 'z' ] . 'deg)';
$rotation_css    = "-ms-transform: $custom_rotation; -webkit-transform: $custom_rotation; transform: $custom_rotation;";

$flip_text_css = '';
$flip_text_css .= $flip_text_horizontally ? ' scaleX(-1)' : '';
$flip_text_css .= $flip_text_vertically ? ' scaleY(-1)' : '';

if ( $flip_text_css )
    echo ".yith-wcbm-badge-$id_badge .yith-wcbm-badge-text{-ms-transform: $flip_text_css; -webkit-transform: $flip_text_css; transform:$flip_text_css}";


/**
 * Rotation - Position - Opacity
 */
$opacity_css = 'opacity: ' . $opacity / 100 . ';';
echo ".yith-wcbm-badge-{$id_badge}{ {$position_css} {$opacity_css} }";
echo ".yith-wcbm-badge-{$id_badge} .yith-wcbm-badge__wrap{ {$rotation_css} }";


switch ( $type ) {
    case 'text':
    case 'custom':
        ?>
        .yith-wcbm-badge-<?php echo $id_badge ?> .yith-wcbm-badge__wrap
        {
        <?php

        $width = is_numeric( $width ) ? $width . 'px' : $width;
        if ( is_numeric( $height ) ) {
            $line_height = ( $line_height > -1 ) ? $line_height : $height;
            $line_height .= 'px';
        } else {
            $line_height = '100%';
        }
        $height = is_numeric( $height ) ? $height . 'px' : $height;

        ?>
        color: <?php echo $txt_color ?>;
        background-color: <?php echo $bg_color ?>;
        width: <?php echo $width ?>;
        height: <?php echo $height ?>;
        line-height: <?php echo $line_height ?>;
        border-top-left-radius: <?php echo $border_top_left_radius ?>px;
        border-bottom-left-radius: <?php echo $border_bottom_left_radius ?>px;
        border-top-right-radius: <?php echo $border_top_right_radius ?>px;
        border-bottom-right-radius: <?php echo $border_bottom_right_radius ?>px;
        padding-top: <?php echo $padding_top ?>px;
        padding-bottom: <?php echo $padding_bottom ?>px;
        padding-left: <?php echo $padding_left ?>px;
        padding-right: <?php echo $padding_right ?>px;
        font-size: <?php echo $font_size ?>px;
        }
        <?php
        break;

    case 'image':
        break;

    case 'css':
        $id_css_badge = $id_badge;
        $args         = array(
            'type'                   => 'css',
            'id_css_badge'           => $id_css_badge,
            'id_badge_style'         => $css_badge,
            'css_bg_color'           => $css_bg_color,
            'css_text_color'         => $css_text_color,
            'flip_text_horizontally' => $flip_text_horizontally,
            'flip_text_vertically'   => $flip_text_vertically
        );
        yith_wcbm_get_badge_style( $args );
        break;
    case 'advanced':
        $id_advanced_badge = $id_badge;

        $args = array(
            'type'                   => 'advanced',
            'id_advanced_badge'      => $id_advanced_badge,
            'id_badge_style'         => $advanced_badge,
            'advanced_bg_color'      => $advanced_bg_color,
            'advanced_text_color'    => $advanced_text_color,
            'flip_text_horizontally' => $flip_text_horizontally,
            'flip_text_vertically'   => $flip_text_vertically
        );
        yith_wcbm_get_badge_style( $args );
        break;
}


/**
 * Scale on Mobile
 */
if ( $scale_on_mobile != 1 ) {
    $mobile_breakpoint = get_option( 'yith-wcbm-mobile-breakpoint', '768px' );

    $transform_origin = yith_wcbm_get_transform_origin_by_positions( $pos_top, $pos_right, $pos_bottom, $pos_left );
    $transform        = "scale({$scale_on_mobile})";
    $transform_css    = "-ms-transform: $transform; -webkit-transform: $transform; transform: $transform;";
    $transform_css    .= "transform-origin: {$transform_origin};";

    echo "@media only screen and (max-width: {$mobile_breakpoint}) {
        .yith-wcbm-badge-{$id_badge}{ {$transform_css} }
    }";
}