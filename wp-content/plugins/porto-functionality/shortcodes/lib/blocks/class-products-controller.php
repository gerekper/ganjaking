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

		$is_pre_order = $request->get_param( 'pre_order' );
		if ( $is_pre_order ) {
			$args['meta_query'] = $this->add_meta_query(
				$args,
				array(
					'relation' => 'OR',
					array(
						'key'   => '_porto_pre_order',
						'value' => 'yes',
					),
					array(
						'key'   => '_porto_variation_pre_order',
						'value' => 'yes',
					),
				)
			);
		}

		return $args;
	}

	public function get_collection_params() {
		$params                    = parent::get_collection_params();
		$params['orderby']['enum'] = array_merge( $params['orderby']['enum'], array( 'price', 'popularity', 'rating' ) );

		return $params;
	}
}
