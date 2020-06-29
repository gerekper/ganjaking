<?php
/**
 * Product quantity input
 *
 * Extends the WooCommerce quantity input template to include the add_to_cart data attribute.
 *
 * @package WooCommerce-One-Page-Checkout/Templates
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<button class="button add_to_cart_button checkout-quantity" id="product_<?php echo $product->get_id(); ?>" name="product_id" value="<?php echo $product->get_id(); ?>" data-add_to_cart="<?php echo $product->get_id(); ?>" data-opc_remove_text="<?php _e( 'Remove', 'wcopc' ); ?>">
	<span><?php _e( 'Add to order', 'wcopc' ); ?></span>
</button>
<a class="wc-south opc-complete-order" href="#customer_details"><?php _e( 'Modify &amp; complete order below', 'wcopc' ); ?></a>