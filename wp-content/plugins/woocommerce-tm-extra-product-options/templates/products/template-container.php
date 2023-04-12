<?php
/**
 * The template for displaying the product element container
 *
 * Used by the Thumbnail, Radio and Dropdown layout mode
 *
 * @author  ThemeComplete
 * @package WooCommerce Extra Product Options/Templates/Products
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;
$can_show = true;
if ( 'checkbox' === $layout_mode || 'thumbnailmultiple' === $layout_mode ) {
	if ( ! isset( $product_list[ $product_id ] ) ) {
		$can_show = false;
	}
}
?>
<div class="tc-epo-element-product-container-wrap">
<?php
if ( $can_show ) {
	foreach ( $product_list as $product_id => $attributes ) {

		add_filter( 'woocommerce_product_variation_title_include_attributes', [ THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS(), 'woocommerce_product_variation_title_include_attributes' ] );
		$current_product = wc_get_product( $product_id );
		remove_filter( 'woocommerce_product_variation_title_include_attributes', [ THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS(), 'woocommerce_product_variation_title_include_attributes' ] );

		if ( $args['discount'] ) {
			// this is for simple products only
			// variable products discounts are in the files
			// /includes/fields/class-tm-epo-fields-product.php
			// /includes/classes/class-tm-epo-associated-products.php
			// depending on the situation.
			$current_price = THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS()->get_discounted_price( $current_product->get_price(), $args['discount'], $args['discount_type'] );
			$current_price = apply_filters( 'wc_epo_remove_current_currency_price', $current_price );
			$current_product->set_sale_price( $current_price );
			$current_product->set_price( $current_price );
			$discount_applied = true;
		}

		if ( ! isset( $option ) || ! isset( $option['_default_value_counter'] ) ) {
			$option['_default_value_counter'] = '0';
			$option['counter']                = '0';
		}

		include THEMECOMPLETE_EPO_TEMPLATE_PATH . 'products/template-item.php';
	}
}

?>
</div>
