<?php
/**
 * WooCommerce Pre-Orders Extend Store API.
 *
 * A class to extend the store public API with pre-order product type
 * related data for each pre-order product item.
 *
 * @package WooCommerce Pre-orders
 */

namespace WooCommerce\Pre_Orders\Blocks;

use Automattic\WooCommerce\Blocks\Package;
use Automattic\WooCommerce\Blocks\Domain\Services\ExtendRestApi;
use Automattic\WooCommerce\Blocks\StoreApi\Schemas\CartItemSchema;

class WC_Pre_Orders_Extend_Store_API {
	/**
	 * Plugin Identifier, unique to each plugin.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'preorders';

	/**
	 * Bootstraps the class and hooks required data.
	 *
	 * @since 3.1.0
	 */
	public static function init() {
		self::extend_store();
	}

	/**
	 * Registers the actual data into each endpoint.
	 */
	public static function extend_store() {
		$args = array(
			'endpoint'        => CartItemSchema::IDENTIFIER,
			'namespace'       => self::IDENTIFIER,
			'data_callback'   => array( 'WooCommerce\Pre_Orders\Blocks\WC_Pre_Orders_Extend_Store_API', 'extend_cart_item_data' ),
			'schema_callback' => array( 'WooCommerce\Pre_Orders\Blocks\WC_Pre_Orders_Extend_Store_API', 'extend_cart_item_schema' ),
			'schema_type'     => ARRAY_A,
		);

		if ( function_exists( 'woocommerce_store_api_register_endpoint_data' ) ) {
			woocommerce_store_api_register_endpoint_data( $args );
		} else {
			$extend = Package::container()->get( ExtendRestApi::class );
			$extend->register_endpoint_data( $args );
		}
	}

	/**
	 * Register pre-order product type data into cart/items endpoint.
	 *
	 * @param array $cart_item Current cart item data.
	 *
	 * @return array $item_data Registered data or empty array if condition is not satisfied.
	 */
	public static function extend_cart_item_data( $cart_item ) {
		$product   = $cart_item['data'];
		$item_data = array(
			'availability'         => null,
			'charged_upon_release' => null,
			'charged_upfront'      => null,
		);

		if ( \WC_Pre_Orders_Product::product_can_be_pre_ordered( $product->get_id() ) ) {
			$item_data = array(
				'availability'         => \WC_Pre_Orders_Product::get_localized_availability_date( $product ),
				'charged_upon_release' => \WC_Pre_Orders_Product::product_is_charged_upon_release( $product ),
				'charged_upfront'      => \WC_Pre_Orders_Product::product_is_charged_upfront( $product ),
			);
		}

		return $item_data;
	}

	/**
	 * Register pre-order product type schema into cart/items endpoint.
	 *
	 * @return array Registered schema.
	 */
	public static function extend_cart_item_schema() {
		return array(
			'availability'      => array(
				'description' => __( 'Availability date for product.', 'woocommerce-pre-orders' ),
				'type'        => array( 'string', 'null' ),
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'charged_upon_release'    => array(
				'description' => __( 'Indicates if customer is going to be charged only when product is released.', 'woocommerce-pre-orders' ),
				'type'        => array( 'boolean', 'null' ),
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'charged_upfront'    => array(
				'description' => __( 'Indicates if customer is going to be charged upfront.', 'woocommerce-pre-orders' ),
				'type'        => array( 'boolean', 'null' ),
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
		);
	}
}
