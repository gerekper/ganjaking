<?php
/**
 * Cart Message Template
 *
 * @class   YWCM_Cart_Message
 * @package YITH
 * @since   1.0.0
 * @author  Your Inspiration Themes
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWCM_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

$ywcm_layout2_box_bg_color          = get_option( 'ywcm_layout2_box_bg_color' );
$ywcm_layout2_box_border_color      = get_option( 'ywcm_layout2_box_border_color' );
$ywcm_layout2_box_text_color        = get_option( 'ywcm_layout2_box_text_color' );
$ywcm_layout2_icon_background_color = get_option( 'ywcm_layout2_icon_background_color' );
$ywcm_layout2_settings_icon_image   = get_option( 'ywcm_layout2_settings_icon_image' );
$ywcm_layout2_button_bg_color       = get_option( 'ywcm_layout2_button_bg_color' );
$ywcm_layout2_button_bg_color_hover = get_option( 'ywcm_layout2_button_bg_color_hover' );
$ywcm_layout2_button_color          = get_option( 'ywcm_layout2_button_color' );

$ywcm_layout3_box_bg_color          = get_option( 'ywcm_layout3_box_bg_color' );
$ywcm_layout3_box_border_color      = get_option( 'ywcm_layout3_box_border_color' );
$ywcm_layout3_box_text_color        = get_option( 'ywcm_layout3_box_text_color' );
$ywcm_layout3_settings_icon_image   = get_option( 'ywcm_layout3_settings_icon_image' );
$ywcm_layout3_button_bg_color       = get_option( 'ywcm_layout3_button_bg_color' );
$ywcm_layout3_button_bg_color_hover = get_option( 'ywcm_layout3_button_bg_color_hover' );
$ywcm_layout3_button_color          = get_option( 'ywcm_layout3_button_color' );

$ywcm_layout4_box_bg_color          = get_option( 'ywcm_layout4_box_bg_color' );
$ywcm_layout4_box_border_color      = get_option( 'ywcm_layout4_box_border_color' );
$ywcm_layout4_box_text_color        = get_option( 'ywcm_layout4_box_text_color' );
$ywcm_layout4_settings_icon_image   = get_option( 'ywcm_layout4_settings_icon_image' );
$ywcm_layout4_button_bg_color       = get_option( 'ywcm_layout4_button_bg_color' );
$ywcm_layout4_button_bg_color_hover = get_option( 'ywcm_layout4_button_bg_color_hover' );
$ywcm_layout4_button_border_color   = get_option( 'ywcm_layout4_button_border_color' );
$ywcm_layout4_button_color          = get_option( 'ywcm_layout4_button_color' );

$ywcm_layout5_box_bg_color          = get_option( 'ywcm_layout5_box_bg_color' );
$ywcm_layout5_box_text_color        = get_option( 'ywcm_layout5_box_text_color' );
$ywcm_layout5_settings_icon_image   = get_option( 'ywcm_layout5_settings_icon_image' );
$ywcm_layout5_button_bg_color       = get_option( 'ywcm_layout5_button_bg_color' );
$ywcm_layout5_button_bg_color_hover = get_option( 'ywcm_layout5_button_bg_color_hover' );
$ywcm_layout5_button_color          = get_option( 'ywcm_layout5_button_color' );

$ywcm_layout6_box_bg_color          = get_option( 'ywcm_layout6_box_bg_color' );
$ywcm_layout6_box_text_color        = get_option( 'ywcm_layout6_box_text_color' );
$ywcm_layout6_settings_icon_image   = get_option( 'ywcm_layout6_settings_icon_image' );
$ywcm_layout6_button_bg_color       = get_option( 'ywcm_layout6_button_bg_color' );
$ywcm_layout6_button_bg_color_hover = get_option( 'ywcm_layout6_button_bg_color_hover' );
$ywcm_layout6_button_color          = get_option( 'ywcm_layout6_button_color' );

return apply_filters(
	'ywcm_css_layout',
	"
.yith-cart-message-layout2{
    background-color:{$ywcm_layout2_box_bg_color};
    border-color:{$ywcm_layout2_box_border_color};
    color:{$ywcm_layout2_box_text_color}
}
.yith-cart-message-layout2 .icon-wrapper{
    background-color: {$ywcm_layout2_icon_background_color};
}
.yith-cart-message-layout2 .icon-wrapper:before{
    background-image:url('{$ywcm_layout2_settings_icon_image}');
}
.yith-cart-message-layout2 .content .button, .yith-cart-message-layout2 .content .button:hover{
    background-color: {$ywcm_layout2_button_bg_color};
    color:{$ywcm_layout2_button_color};
}
.yith-cart-message-layout2 .content .button:hover{
   background-color: {$ywcm_layout2_button_bg_color_hover};
}

.yith-cart-message-layout3{
    background-color:{$ywcm_layout3_box_bg_color};
    border-color:{$ywcm_layout3_box_border_color};
    color:{$ywcm_layout3_box_text_color}
}

.yith-cart-message-layout3 .icon-wrapper:before{
    background-image:url('{$ywcm_layout3_settings_icon_image}');
}

.yith-cart-message-layout3 .content .button, .yith-cart-message-layout3 .content .button:hover{
    background-color: {$ywcm_layout3_button_bg_color};
    color:{$ywcm_layout3_button_color};
}
.yith-cart-message-layout3 .content .button:hover{
   background-color: {$ywcm_layout3_button_bg_color_hover};
}


.yith-cart-message-layout4{
    background-color:{$ywcm_layout4_box_bg_color};
    border-color:{$ywcm_layout4_box_border_color};
    color:{$ywcm_layout4_box_text_color}
}

.yith-cart-message-layout4 .icon-wrapper:before{
    background-image:url('{$ywcm_layout4_settings_icon_image}');
}

.yith-cart-message-layout4 .content .button, .yith-cart-message-layout4 .content .button:hover{
    background-color: {$ywcm_layout4_button_bg_color};
    color:{$ywcm_layout4_button_color};
    box-shadow: 0px 2px 0px {$ywcm_layout4_button_border_color};
}
.yith-cart-message-layout4 .content .button:hover{
   background-color: {$ywcm_layout4_button_bg_color_hover};
}


.yith-cart-message-layout5{
    background-color:{$ywcm_layout5_box_bg_color};
    color:{$ywcm_layout5_box_text_color}
}

.yith-cart-message-layout5 .icon-wrapper:before{
    background-image:url('{$ywcm_layout5_settings_icon_image}');
}

.yith-cart-message-layout5 .content .button, .yith-cart-message-layout5 .content .button:hover{
    background-color: {$ywcm_layout5_button_bg_color};
    color:{$ywcm_layout5_button_color};
}

.yith-cart-message-layout5 .content .button:hover{
   background-color: {$ywcm_layout5_button_bg_color_hover};
}

.yith-cart-message-layout6{
    background-color:{$ywcm_layout6_box_bg_color};
    color:{$ywcm_layout6_box_text_color}
}

.yith-cart-message-layout6 .icon-wrapper:before{
    background-image:url('{$ywcm_layout6_settings_icon_image}');
}

.yith-cart-message-layout6 .content .button, .yith-cart-message-layout6 .content .button:hover{
    background-color: {$ywcm_layout6_button_bg_color};
    color:{$ywcm_layout6_button_color};
}

.yith-cart-message-layout6 .content .button:hover{
   background-color: {$ywcm_layout6_button_bg_color_hover};
}

"
);
