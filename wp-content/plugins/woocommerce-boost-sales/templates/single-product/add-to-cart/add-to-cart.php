<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
?>
    <button class="<?php esc_attr_e( isset( $args['class'] ) ? $args['class'] : 'button' ) ?>" <?php ( isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '' ) ?>><?php esc_html_e( 'Add to cart', 'woocommerce-boost-sales' ) ?></button>
    <input type="hidden" name="add-to-cart" value="<?php echo $product->get_id() ?>"/>
<?php
if ( $product->supports( 'ajax_add_to_cart' ) ) {
	?>
    <input type="hidden" name="product_id" value="<?php echo $product->get_id() ?>"/>
	<?php
}
