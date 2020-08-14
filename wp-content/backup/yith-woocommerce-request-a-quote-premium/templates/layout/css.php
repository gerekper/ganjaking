<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAQ_VERSION' ) ) {
	exit; // Exit if accessed directly .
}

if ( defined( 'YITH_PROTEO_VERSION' ) && apply_filters( 'yith_proteo_theme_color', true ) ) {

	$ywraq_layout_button_bg_color       = 'transparent';
	$ywraq_layout_button_bg_color_hover = get_theme_mod( 'yith_proteo_main_color_shade', '#448a85' );
	$ywraq_layout_button_color          = get_theme_mod( 'yith_proteo_main_color_shade', '#448a85' );
	$ywraq_layout_button_color_hover    = '#ffffff';
} else {
	$ywraq_layout_button_bg_color       = get_option( 'ywraq_layout_button_bg_color' );
	$ywraq_layout_button_bg_color_hover = get_option( 'ywraq_layout_button_bg_color_hover' );
	$ywraq_layout_button_color          = get_option( 'ywraq_layout_button_color' );
	$ywraq_layout_button_color_hover    = get_option( 'ywraq_layout_button_color_hover' );
}

$css                          = ".woocommerce .add-request-quote-button.button, .woocommerce .add-request-quote-button-addons.button{
    background-color: {$ywraq_layout_button_bg_color}!important;
    color: {$ywraq_layout_button_color}!important;
}
.woocommerce .add-request-quote-button.button:hover,  .woocommerce .add-request-quote-button-addons.button:hover{
    background-color: {$ywraq_layout_button_bg_color_hover}!important;
    color: {$ywraq_layout_button_color_hover}!important;
}
.woocommerce a.add-request-quote-button{
    color: {$ywraq_layout_button_color}!important;
}

.woocommerce a.add-request-quote-button:hover{
    color: {$ywraq_layout_button_color_hover}!important;
}
";
$show_button_near_add_to_cart = get_option( 'ywraq_show_button_near_add_to_cart', 'no' );
if ( yith_plugin_fw_is_true( $show_button_near_add_to_cart ) ) {
	$css .= '.woocommerce.single-product button.single_add_to_cart_button.button {margin-right: 5px;}
	.woocommerce.single-product .product .yith-ywraq-add-to-quote { display: inline-block; line-height: normal; vertical-align: middle; }
	';
}

return apply_filters( 'ywraq_custom_css', $css );
