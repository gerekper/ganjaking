<?php
defined( 'ABSPATH' ) || die;

if ( ! class_exists( 'WC_REST_Products_Controller' ) ) {
	return;
}

/**
 * Block Controller for getting Woocommerce Products
 */
class PortoBlocksProductsController extends WC_REST_Products_Controller {

	protected $namespace = 'portowc/v1';

	protected function prepare_objects_query( $request ) {
		$args = parent::prepare_objects_query( $request );

		$orderby = $request->get_param( 'orderby' );
		$order   = $request->get_param( 'order' );

		$ordering_args   = WC()->query->get_catalog_ordering_args( $orderby, $order );
		$args['orderby'] = $ordering_args['orderby'];
		$args['order']   = $ordering_args['order'];
		if ( $ordering_args['meta_key'] ) {
			$args['meta_key'] = $ordering_args['meta_key'];
		}

		return $args;
	}

	public function get_collection_params() {
		$params                    = parent::get_collection_params();
		$params['orderby']['enum'] = array_merge( $params['orderby']['enum'], array( 'price', 'popularity', 'rating' ) );

		return $params;
	}
}
