<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//
// Hooks //
//

/**
 * Photography loop hooks.
 */
add_action( 'wc_photography_before_shop_loop_item', 'wc_photography_template_image', 10 );
add_action( 'wc_photography_shop_loop_item', 'wc_photography_template_sku', 10 );
add_action( 'wc_photography_shop_loop_item', 'wc_photography_template_price', 20 );
add_action( 'wc_photography_shop_loop_item', 'wc_photography_template_addons', 30 );
add_action( 'wc_photography_shop_loop_item', 'wc_photography_template_product_quantity', 50 );
add_action( 'wc_photography_shop_loop_item', 'wc_photography_template_short_description', 70 );
add_action( 'woocommerce_photography_add_to_cart', 'wc_photography_template_single_add_to_cart', 30 );
add_action( 'woocommerce_product_meta_end', 'wc_photography_template_show_collections', 10 );

/**
 * My Account page hooks.
 */
add_action( 'woocommerce_before_my_account', 'wc_photography_my_account_list', 5 );


//
// Functions //
//

if ( ! function_exists( 'wc_photography_collections_tools' ) ) {

	/**
	 * Show the photography collections tools.
	 *
	 * @return string
	 */
	function wc_photography_collections_tools() {
		wc_get_template(
			'loop/photography/collections-tools.php',
			array(),
			'woocommerce/',
			WC_Photography::get_templates_path()
		);
	}
}

if ( ! function_exists( 'wc_photography_template_image' ) ) {

	/**
	 * Show the photography image.
	 *
	 * @return string
	 */
	function wc_photography_template_image() {
		wc_get_template(
			'loop/photography/image.php',
			array(),
			'woocommerce/',
			WC_Photography::get_templates_path()
		);
	}
}

if ( ! function_exists( 'wc_photography_template_sku' ) ) {

	/**
	 * Show the photography SKU.
	 *
	 * @return string
	 */
	function wc_photography_template_sku() {
		wc_get_template(
			'loop/photography/sku.php',
			array(),
			'woocommerce/',
			WC_Photography::get_templates_path()
		);
	}
}

if ( ! function_exists( 'wc_photography_template_price' ) ) {

	/**
	 * Show the photography Price.
	 *
	 * @return string
	 */
	function wc_photography_template_price() {
		wc_get_template(
			'loop/photography/price.php',
			array(),
			'woocommerce/',
			WC_Photography::get_templates_path()
		);
	}
}

if ( ! function_exists( 'wc_photography_template_addons' ) ) {

	/**
	 * Show the photography Addons.
	 *
	 * @return string
	 */
	function wc_photography_template_addons() {
		if ( class_exists( 'WC_Product_Addons' ) ) {
			wc_get_template(
				'loop/photography/addons.php',
				array(),
				'woocommerce/',
				WC_Photography::get_templates_path()
			);
		}
	}
}

if ( ! function_exists( 'wc_photography_template_product_quantity' ) ) {

	/**
	 * Show the photography add to cart button.
	 *
	 * @return string
	 */
	function wc_photography_template_product_quantity() {
		wc_get_template(
			'loop/photography/product-quantity.php',
			array(),
			'woocommerce/',
			WC_Photography::get_templates_path()
		);
	}
}

if ( ! function_exists( 'wc_photography_template_short_description' ) ) {

	/**
	 * Show the photography excerpt.
	 *
	 * @return string
	 */
	function wc_photography_template_short_description() {
		wc_get_template(
			'loop/photography/short-description.php',
			array(),
			'woocommerce/',
			WC_Photography::get_templates_path()
		);
	}
}


if ( ! function_exists( 'wc_photography_template_single_add_to_cart' ) ) {

	/**
	 * Show the photography excerpt.
	 *
	 * @return string
	 */
	function wc_photography_template_single_add_to_cart() {
		wc_get_template(
			'single-product/add-to-cart/photography.php',
			array(),
			'woocommerce/',
			WC_Photography::get_templates_path()
		);
	}
}

if ( ! function_exists( 'wc_photography_my_account_list' ) ) {

	/**
	 * Show the user collections in "my account" page.
	 *
	 * @return string
	 */
	function wc_photography_my_account_list() {
		global $current_user;

		$collections = get_user_meta( $current_user->ID, '_wc_photography_collections', true );

		if ( $collections ) {
			wc_get_template(
				'myaccount/my-collections.php',
				array(
					'collections' => $collections,
				),
				'woocommerce/',
				WC_Photography::get_templates_path()
			);
		}
	}
}

if ( ! function_exists( 'wc_photography_get_content_template' ) ) {
	/**
	 * Shows the photography content.
	 *
	 * @see WC_Photography_Products::photography_templates
	 */
	function wc_photography_get_content_template() {
		wc_get_template_part( 'content', 'photography' );
	}
}

if ( ! function_exists( 'wc_photography_template_show_collections' ) ) {

	/**
	 * Show the collections on single page.
	 */
	function wc_photography_template_show_collections() {
		global $product;

		if ( ! $product || ! $product->is_type( 'photography' ) ) {
			return;
		}

		$collections = get_the_terms( $product->get_id(), 'images_collections' );

		if ( empty( $collections ) ) {
			return;
		}

		$label = _n( 'Collection:', 'Collections:', count( $collections ), 'woocommerce-photography' );

		the_terms( $product->get_id(), 'images_collections', '<span class="collections">' . esc_html( $label ) . ' ', ', ', '</span>' );
	}
}
