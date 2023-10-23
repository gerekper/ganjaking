<?php
/**
 * Add-ons Container.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns\Templates
 * @version 3.0.4
 *
 * @var YITH_WAPO_Front $instance
 * @var WC_Product $product
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

$style_form_style = get_option( 'yith_wapo_style_form_style', 'theme' );
if ( $product instanceof WC_Product_Variable && empty( $product->get_default_attributes() ) ) {
	$product_price = 0;
} else {
	$product_price = apply_filters( 'yith_wapo_product_price' , yit_get_display_price( $product ), $product );
}

do_action( 'yith_wapo_before_main_container' );
?>

<!-- #yith-wapo-container -->
<div id="yith-wapo-container" class="yith-wapo-container yith-wapo-form-style-<?php echo esc_html( $style_form_style ) ?>" data-product-price="<?php echo esc_attr( $product_price ); ?>" data-product-id="<?php echo esc_attr( $product->get_id() )  ?>">
	<?php $instance->print_blocks(); ?>
</div>

<?php
do_action( 'yith_wapo_after_main_container' );
