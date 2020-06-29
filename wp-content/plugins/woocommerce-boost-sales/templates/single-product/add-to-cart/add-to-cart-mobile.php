<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

echo sprintf(
	'<button class="%s" %s>%s</button>',
	esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
	isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
	esc_html__( 'Add to cart', 'woocommerce-boost-sales' )
);
?>
    <input type="hidden" name="add-to-cart" value="<?php echo $product->get_id() ?>"/>
<?php
if ( $product->supports( 'ajax_add_to_cart' ) ) { ?>
    <input type="hidden" name="product_id" value="<?php echo $product->get_id() ?>"/>
	<?php
}