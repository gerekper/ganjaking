<?php
/**
 * Product Bundle single-product template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/add-to-cart/bundle.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 5.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** WC Core action. */
do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form method="post" enctype="multipart/form-data" class="cart cart_group bundle_form <?php echo esc_attr( $classes ); ?>"><?php

	/**
	 * 'woocommerce_before_bundled_items' action.
	 *
	 * @param WC_Product_Bundle $product
	 */
	do_action( 'woocommerce_before_bundled_items', $product );

	foreach ( $bundled_items as $bundled_item ) {

		/**
		 * 'woocommerce_bundled_item_details' action.
		 *
		 * @hooked wc_pb_template_bundled_item_details_wrapper_open  -   0
		 * @hooked wc_pb_template_bundled_item_thumbnail             -   5
		 * @hooked wc_pb_template_bundled_item_details_open          -  10
		 * @hooked wc_pb_template_bundled_item_title                 -  15
		 * @hooked wc_pb_template_bundled_item_description           -  20
		 * @hooked wc_pb_template_bundled_item_product_details       -  25
		 * @hooked wc_pb_template_bundled_item_details_close         -  30
		 * @hooked wc_pb_template_bundled_item_details_wrapper_close - 100
		 */
		do_action( 'woocommerce_bundled_item_details', $bundled_item, $product );
	}

	/**
	 * 'woocommerce_after_bundled_items' action.
	 *
	 * @param  WC_Product_Bundle  $product
	 */
	do_action( 'woocommerce_after_bundled_items', $product );

	/**
	 * 'woocommerce_bundles_add_to_cart_wrap' action.
	 *
	 * @since  5.5.0
	 *
	 * @param  WC_Product_Bundle  $product
	 */
	do_action( 'woocommerce_bundles_add_to_cart_wrap', $product );

?></form><?php
	/** WC Core action. */
	do_action( 'woocommerce_after_add_to_cart_form' );
?>
