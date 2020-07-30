<?php

class WoocommerceGpfRestApi {
	/**
	 * @var WoocommerceGpfCommon
	 */
	private $woocommerce_gpf_common;

	/**
	 * WoocommerceGpfRestApi constructor.
	 *
	 * @param WoocommerceGpfCommon $woocommerce_gpf_common
	 */
	public function __construct(
		WoocommerceGpfCommon $woocommerce_gpf_common
	) {
		$this->woocommerce_gpf_common = $woocommerce_gpf_common;
	}

	public function initialise() {
		add_filter( 'woocommerce_rest_product_schema', array( $this, 'rest_api_product_schema' ), 10 );
		add_filter( 'woocommerce_rest_prepare_product_object', array( $this, 'rest_api_output_v2' ), 10, 3 );
		add_filter( 'woocommerce_rest_prepare_product_variation_object', array( $this, 'rest_api_output_v2' ), 10, 3 );
		add_filter( 'woocommerce_rest_insert_product_object', array( $this, 'rest_api_maybe_update_v2' ), 10, 3 );
		add_filter(
			'woocommerce_rest_insert_product_variation_object',
			array( $this, 'rest_api_maybe_update_v2' ),
			10,
			3
		);
	}

	public function rest_api_product_schema( $schema ) {
		$elements           = $this->generate_element_list();
		$schema['gpf_data'] = array(
			'description' => __( 'Google product feed data', 'woocommerce_gpf' ),
			'type'        => 'object',
			'content'     => array(
				'view',
				'edit',
			),
		);
		foreach ( $elements as $key => $description ) {
			$schema['gpf_data']['items'][ $key ] = array(
				'description' => $description,
				'type'        => 'string',
				'context'     => array(
					'view',
					'edit',
				),
			);
		}

		return $schema;
	}

	/**
	 * Include MSRP prices in REST API for products/xx
	 *
	 * REST API v2 - WooCommerce 3.x
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function rest_api_output_v2( $response, $product, $request ) {
		$response->data['gpf_data'] = array();
		$meta                       = get_post_meta( $product->get_id(), '_woocommerce_gpf_data', true );
		$elements                   = $this->generate_element_list();
		foreach ( array_keys( $elements ) as $id ) {
			if ( ! empty( $meta[ $id ] ) ) {
				$response->data['gpf_data'][ $id ] = $meta[ $id ];
			} else {
				$response->data['gpf_data'][ $id ] = null;
			}
		}

		return $response;
	}

	/**
	 * Update the MSRP for a product via REST API v2.
	 *
	 * REST API v2 - WooCommerce 3.0.x
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function rest_api_maybe_update_v2( $product, $request, $creating ) {
		// Do nothing if no GPF data passed in for update.
		if ( ! isset( $request['gpf_data'] ) ) {
			return $product;
		}
		// Merge passed values over the top of existing ones.
		$meta = get_post_meta( $product->get_id(), '_woocommerce_gpf_data', true );
		if ( '' === $meta || false === $meta ) {
			$meta = array();
		}
		$meta = array_merge( $meta, $request['gpf_data'] );
		// Save the changes.
		update_post_meta( $product->get_id(), '_woocommerce_gpf_data', $meta );

		return $product;
	}

	/**
	 * Generate a list of our elements from the common field class.
	 *
	 * @return array   Array of GPF columns with appropriate keys.
	 */
	private function generate_element_list() {
		$fields = wp_list_pluck( $this->woocommerce_gpf_common->product_fields, 'desc' );
		foreach ( $fields as $key => $value ) {
			// Translators: Placeholder is the name of a specific data field.
			$fields[ $key ] = sprintf( __( 'Google product feed: %s', 'woocommerce_gpf' ), $value );
		}
		$fields['exclude_product'] = __( 'Google product feed: Hide product from feed (Y/N)', 'woocommerce_gpf' );

		return $fields;
	}
}
