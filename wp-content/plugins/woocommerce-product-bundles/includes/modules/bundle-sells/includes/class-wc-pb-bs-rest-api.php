<?php
/**
 * WC_PB_BS_REST_API class
 *
 * @package  WooCommerce Product Bundles
 * @since    6.13.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Extend REST API to support bundle-sells.
 *
 * @class    WC_PB_BS_REST_API
 * @version  6.14.0
 */
class WC_PB_BS_REST_API {

	/**
	 * Custom REST API product field names, indicating support for getting/updating.
	 *
	 * @var array
	 */
	private static $product_fields = array(
		'bundle_sell_ids' => array( 'get', 'update' ),
	);

	/**
	 * Setup REST API bundle-sells class.
	 */
	public static function init() {

		// Register WP REST API custom product fields.
		add_action( 'rest_api_init', array( __CLASS__, 'register_product_fields' ), 0 );

	}

	/**
	 * Register custom REST API fields for product requests.
	 */
	public static function register_product_fields() {

		foreach ( self::$product_fields as $field_name => $field_supports ) {

			$args = array(
				'schema' => self::get_product_field_schema( $field_name ),
			);

			if ( in_array( 'get', $field_supports, true ) ) {
				$args[ 'get_callback' ] = array( __CLASS__, 'get_product_field_value' );
			}
			if ( in_array( 'update', $field_supports, true ) ) {
				$args[ 'update_callback' ] = array( __CLASS__, 'update_product_field_value' );
			}

			register_rest_field( 'product', $field_name, $args );
		}
	}

	/**
	 * Gets schema properties for PB product fields.
	 *
	 * @param  string  $field_name
	 * @return array
	 */
	public static function get_product_field_schema( $field_name ) {

		$extended_schema = self::get_extended_product_schema();
		$field_schema    = isset( $extended_schema[ $field_name ] ) ? $extended_schema[ $field_name ] : null;

		return $field_schema;
	}

	/**
	 * Gets extended (unprefixed) schema properties for products.
	 *
	 * @return array
	 */
	private static function get_extended_product_schema() {

		return array(
			'bundle_sell_ids' => array(
				'description' => __( 'List of bundle-sells product IDs.', 'woocommerce-product-bundles' ),
				'type'        => 'array',
				'required'    => false,
				'context'     => array( 'view', 'edit' ),
				'items'       => array(
					'type' => 'integer',
				),
			),

		);
	}

	/**
	 * Gets values for PB product fields.
	 *
	 * @param  array            $response
	 * @param  string           $field_name
	 * @param  WP_REST_Request  $request
	 * @return array
	 */
	public static function get_product_field_value( $response, $field_name, $request ) {

		$data = null;

		if ( isset( $response[ 'id' ] ) ) {
			$product = wc_get_product( $response[ 'id' ] );
			$data    = self::get_product_field( $field_name, $product );
		}

		return $data;
	}

	/**
	 * Updates values for bundle-sells product fields.
	 *
	 * @param  mixed   $field_value
	 * @param  mixed   $response
	 * @param  string  $field_name
	 *
	 * @return boolean
	 */
	public static function update_product_field_value( $field_value, $response, $field_name ) {

		$product_id = false;

		if ( $response instanceof WP_Post ) {
			$product_id = absint( $response->ID );
			$product    = wc_get_product( $product_id );
		} elseif ( $response instanceof WC_Product ) {
			$product_id = $response->get_id();
			$product    = $response;
		}

		// Bail out early.
		if ( ! $product_id ) {
			return true;
		}

		if ( 'bundle_sell_ids' === $field_name ) {

			if ( ! WC_PB_BS_Product::supports_bundle_sells( $product ) ) {

				throw new WC_REST_Exception(
					'woocommerce_rest_unsupported_bundle_sell_product_type',
					sprintf(
					/* translators: %1$s: Product ID, %2$s: Product type */
						__( 'Product is of type %1$s. Bundle sells are not supported for product types: %2$s.', 'woocommerce-product-bundles' ),
						$product->get_type(),
						implode( ', ', WC_PB_BS_Product::get_unsupported_product_types() )
					),
					400
				);

			}

			// No need to prepare the data. The API schema takes care of this (bundle_sell_ids are set as an array of integers).
			$bundle_sell_ids = ! empty( $field_value ) && is_array( $field_value )
				? array_map( 'intval', (array) $field_value )
				: array();

			if ( ! empty( $bundle_sell_ids ) ) {

				foreach ( $bundle_sell_ids as $bundle_sell_id ) {

					$bundle_sell_product = $bundle_sell_id > 0 ? wc_get_product( $bundle_sell_id ) : false;

					// Ensure product exists when updating/creating and is of supported type.
					if ( false === $bundle_sell_product ) {
						throw new WC_REST_Exception(
							'woocommerce_rest_invalid_bundle_sell_product_id',
							sprintf(
							/* translators: Product ID */
								__( 'Bundle sell product ID #%s is invalid.', 'woocommerce-product-bundles' ),
								$bundle_sell_id
							),
							400
						);
					} elseif ( $bundle_sell_product instanceof WC_Product && ! $bundle_sell_product->is_type( array( 'simple', 'subscription' ) ) ) {
						throw new WC_REST_Exception(
							'woocommerce_rest_invalid_bundle_sell_product_type',
							sprintf(
							/* translators: %1$s: Product ID, %2$s: Product type */
								__( 'Bundle sell product ID %1$s is of unsupported type %2$s. Supported product types: Simple, Simple subscription.', 'woocommerce-product-bundles' ),
								$bundle_sell_id,
								$bundle_sell_product->get_type()
							),
							400
						);
					}
				}

				$product->update_meta_data( '_wc_pb_bundle_sell_ids', $bundle_sell_ids );
			} else {
				$product->delete_meta_data( '_wc_pb_bundle_sell_ids' );
			}
			$product->save();

		}


		return true;
	}

	/**
	 * Gets bundle-sells specific product data.
	 *
	 * @param  string      $key
	 * @param  WC_Product  $product
	 *
	 * @return array
	 */
	private static function get_product_field( $key, $product ) {

		$value = false;

		switch ( $key ) {

			case 'bundle_sell_ids' :

				// No need to check the type before. get_bundle_sell_ids does the type check.
				$bundle_sell_ids = WC_PB_BS_Product::get_bundle_sell_ids( $product );
				$value           = ! empty( $bundle_sell_ids ) ? $bundle_sell_ids : array();

				break;

		}

		return $value;
	}

}

WC_PB_BS_REST_API::init();
