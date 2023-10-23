<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

class WC_MS_API {

    public function __construct() {
        // Initialize API filters. Need to be called after plugins_loaded because of WC_VERSION check.
	add_action( 'plugins_loaded', array( $this, 'init_api_filters' ), 11 );
    }

    /**
     * API filters
     *
     * @since 3.3.23
     * @return void
     */
    public function init_api_filters() {
		add_filter( 'woocommerce_rest_prepare_shop_order_object', array( $this, 'add_shipping_packages' ), 10, 3 );
    }

    /**
     * Add shipping packages to data.
     *
     * @param WC_Order      $order
     * @since 3.3.23
     * @return array
     */
    private function calculate_shipping_packages( $order ) {
		$packages   = $order->get_meta( '_wcms_packages' );
        $multiship  = is_array( $packages ) && 1 < count( $packages );

        $retval = array(
            'multiple_shipping' => $multiship,
        );

        if ( !$multiship ) {
            return $retval;
        }

        $shipping_packages = array();

        foreach ( $packages as $i => $package ) {
            $package['contents'] = array_values( $package['contents'] );
            foreach ( $package['contents'] as $item_key => $item ) {
                $package['contents'][ $item_key ]['name'] = $item['data']->get_title();

                unset( $package['contents'][ $item_key ]['data'], $package['full_address'] );

                $shipping_packages[] = $package;
            }
        }

        $retval['shipping_packages'] = $shipping_packages;

        return $retval;
    }

	/**
	 * Add shipping packages to the order response array
	 *
	 * @param WP_REST_Response $response   The response object.
	 * @param WC_Data          $object      Order object.
	 * @param WP_REST_Request  $request    Request object.
	 *
	 * @return WP_REST_Response $data
	 */
	public function add_shipping_packages( $response, $object, $request ) {
		$order             = wc_get_order( $object->get_id() );
		$order_data        = $response->get_data();
		$shipping_packages = $this->calculate_shipping_packages( $order );

		$response->set_data( array_merge( $order_data, $shipping_packages ) );

		return $response;
	}
}
