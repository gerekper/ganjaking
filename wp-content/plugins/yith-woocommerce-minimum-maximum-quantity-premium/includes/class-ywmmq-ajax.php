<?php
/**
 * Ajax functions
 *
 * @package YITH\MinimumMaximumQuantity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YWMMQ_Ajax' ) ) {

	/**
	 * Implements AJAX for YWMMQ plugin
	 *
	 * @class   YWMMQ_Ajax
	 * @since   1.0.0
	 * @author  YITH <plugins@yithemes.com>
	 *
	 * @package YITH
	 */
	class YWMMQ_Ajax {

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function __construct() {

			add_action( 'wp_ajax_ywmmq_get_rules', array( $this, 'get_rules' ) );
			add_action( 'wp_ajax_nopriv_ywmmq_get_rules', array( $this, 'get_rules' ) );

		}

		/**
		 * Send a test mail from option panel
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function get_rules() {

			$response = array();
			$posted   = $_POST; //phpcs:ignore

			try {

				ob_start();
				YITH_WMMQ()->add_rules_text( $posted['product_id'], $posted['variation_id'] );

				$response['status'] = 'success';
				$response['rules']  = ob_get_clean();

				$product_id = $posted['product_id'];

				global $sitepress;
				$has_wpml = ! empty( $sitepress ) ? true : false;

				if ( $has_wpml && apply_filters( 'ywmmq_wpml_use_default_language_settings', false ) ) {
					$product_id = yit_wpml_object_id( $product_id, 'product', true, wpml_get_default_language() );
				}

				$product             = wc_get_product( $product_id );
				$set_quantity_locked = apply_filters( 'ywmmq_set_variation_quantity_locked', true );

				if ( 'yes' === $product->get_meta( '_ywmmq_product_quantity_limit_variations_override' ) && $set_quantity_locked ) {
					$response['limits'] = YITH_WMMQ()->product_limits( $posted['product_id'], $posted['variation_id'] );
				} else {
					$response['limits'] = array(
						'min'  => 0,
						'max'  => 0,
						'step' => 1,
					);
				}
			} catch ( Exception $e ) {
				if ( ! empty( $e ) ) {
					$response['status'] = 'failure';
				}
			}

			wp_send_json( $response );

			exit;

		}

	}

	new YWMMQ_Ajax();

}
