<?php
/**
 * Mix and Match Product edit button template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/edit-order/edit-container-button.php.
 *
 * HOWEVER, on occasion WooCommerce Mix and Match will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce Mix and Match/Templates
 * @since   2.2.0
 * @version 2.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<button type="submit" name="update-container" value="<?php echo esc_attr( $order_item->get_id() ); ?>" class="single_add_to_cart_button mnm_add_to_cart_button button alt" data-order_id="<?php echo esc_attr( $order->get_id() ); ?>" data-product_id="<?php echo esc_attr( $container->get_id() ); ?>" data-security="<?php echo esc_attr( wp_create_nonce( 'wc_mnm_edit_container' ) ); ?>">
	<?php echo esc_html( $button_text ); ?>
</button>

