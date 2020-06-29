<?php
/**
 * Template to display product selection fields in a list
 *
 * @package WooCommerce-One-Page-Checkout/Templates
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<label class="opc-radio-list-label">
	<input type="radio" id="product_<?php echo $product->get_id(); ?>" name="add_to_cart" value="<?php echo $product->get_id(); ?>" data-add_to_cart="<?php echo $product->get_id(); ?>" <?php checked( wcopc_get_products_prop( $product, 'in_cart' ) ); ?>/>
</label>
