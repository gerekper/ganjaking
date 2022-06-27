<?php
/**
 * The template for displaying the product element add button
 *
 * @author  ThemeComplete
 * @package WooCommerce Extra Product Options/Templates/Products
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;
$button_class = ' alt';
$text_add     = ! empty( THEMECOMPLETE_EPO()->tm_epo_add_button_text_associated_products ) ? THEMECOMPLETE_EPO()->tm_epo_add_button_text_associated_products : esc_html__( 'Add', 'woocommerce-tm-extra-product-options' );
$text_remove  = ! empty( THEMECOMPLETE_EPO()->tm_epo_remove_button_text_associated_products ) ? THEMECOMPLETE_EPO()->tm_epo_remove_button_text_associated_products : esc_html__( 'Remove', 'woocommerce-tm-extra-product-options' );
$button_text  = $text_add;
if ( $input_value > 0 ) {
	$button_class = '';
	$button_text  = $text_remove;
}
?><button data-add="<?php echo esc_attr( $text_add ); ?>" data-remove="<?php echo esc_attr( $text_remove ); ?>" type="button" class="single_add_to_cart_product button<?php echo esc_attr( $button_class ); ?>"><?php echo esc_html( $button_text ); ?></button>
