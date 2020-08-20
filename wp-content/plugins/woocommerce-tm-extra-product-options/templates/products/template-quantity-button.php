<?php
/**
 * The template for displaying the product element add button
 *
 * @author  themeComplete
 * @package WooCommerce Extra Product Options/Templates
 * @version 5.0
 */

defined( 'ABSPATH' ) || exit;
$button_class = " alt";
$text_add = __( 'Add', 'woocommerce-tm-extra-product-options' );
$text_remove = __( 'Remove', 'woocommerce-tm-extra-product-options' );
$button_text = $text_add;
if ($input_value > 0){
	$button_class = "";
	$button_text = $text_remove;
}
?><button data-add="<?php echo esc_attr($text_add); ?>" data-remove="<?php echo esc_attr($text_remove); ?>" type="button" class="single_add_to_cart_product button<?php echo esc_attr($button_class); ?>"><?php echo esc_html($button_text); ?></button>
