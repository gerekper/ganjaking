<?php

namespace WCML\Rest\Wrapper\Orders;

use WCML\Rest\Wrapper\Handler;
use WCML\Rest\Exceptions\InvalidLanguage;

use function WCML\functions\getId;

class Languages extends Handler {

	/**
	 * @param array            $args
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return array
	 */
	public function query( $args, $request ) {
		$lang = $request->get_param( 'lang' );

		if ( ! is_null( $lang ) && $lang !== 'all' ) {
			$args['meta_query'][] = [
				'key'   => 'wpml_language',
				'value' => strval( $lang )
			];
		}

		return $args;
	}


	/**
	 * Appends the language and translation information to the get_product response
	 *
	 * @param \WP_REST_Response        $response
	 * @param \WP_Post|\WC_Order|mixed $object
	 * @param \WP_REST_Request         $request
	 *
	 * @return \WP_REST_Response
	 */
	public function prepare( $response, $object, $request ) {
		$language      = get_query_var( 'lang' );
		$orderLanguage = get_post_meta( $this->get_id( $object ), 'wpml_language', true );

		if ( $orderLanguage !== $language ) {
			foreach ( $response->data['line_items'] as $k => $item ) {
				$translatedProductId = wpml_object_id_filter( $item['product_id'], 'product', false, $language );
				if ( $translatedProductId ) {
					$translatedProduct                                = get_post( $translatedProductId );
					$response->data['line_items'][ $k ]['product_id'] = $translatedProductId;
					if ( $translatedProduct->post_type === 'product_variation' ) {
						$postParent = get_post( $translatedProduct->post_parent );
						$postName   = $postParent->post_title;
					} else {
						$postName = $translatedProduct->post_title;
					}
					$response->data['line_items'][ $k ]['name'] = $postName;
				}
			}
		}

		return $response;
	}

	/**
	 * @param \WP_Post|\WC_Order|mixed $object
	 *
	 * @return int
	 *
	 * @throws \Exception If order has no id.
	 */
	private function get_id( $object ) {
		try {
			return getId( $object );
		} catch ( \Exception $err ) {
			throw new \Exception( 'Order has no ID set.' );
		}
	}


	/**
	 * Sets the product information according to the provided language
	 *
	 * @param object           $object
	 * @param \WP_REST_Request $request
	 * @param bool             $creating
	 *
	 * @throws InvalidLanguage
	 */
	public function insert( $object, $request, $creating ) {
		$data = $request->get_params();
		if ( isset( $data['lang'] ) ) {

			if ( ! apply_filters( 'wpml_language_is_active', false, $data['lang'] ) ) {
				throw new InvalidLanguage( $data['lang'] );
			}

			update_post_meta( $object->get_id(), 'wpml_language', $data['lang'] );
		}
	}
}
