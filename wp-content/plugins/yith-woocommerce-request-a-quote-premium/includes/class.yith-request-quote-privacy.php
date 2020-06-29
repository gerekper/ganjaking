<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH WooCommerce Request A Quote Premium
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAQ_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Implements the YITH_Request_Quote_Privacy class.
 *
 * @class   YITH_Request_Quote_Privacy
 * @package YITH
 * @since   2.0.4
 * @author  YITH
 */
if ( ! class_exists( 'YITH_Request_Quote_Privacy' ) ) {

	/**
	 * Class YITH_Request_Quote_Privacy
	 */
	class YITH_Request_Quote_Privacy {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_Request_Quote_Privacy
		 */
		protected static $instance;

		/**
		 * Array with the list of meta to export
		 *
		 * @var array
		 */
		public $raq_exporter_data;

		/**
		 * Array with the list of meta to erase
		 *
		 * @var array
		 */
		public $raq_eraser_data;


		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_Request_Quote_Privacy
		 * @since 2.0.4
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  2.0.4
		 * @author Emanuela Castorina
		 */
		public function __construct() {
			$this->raq_eraser_data   = $this->get_privacy_eraser_personal_data_props();
			$this->raq_exporter_data = $this->get_privacy_export_personal_data_props();

			// erase.
			add_action( 'woocommerce_privacy_before_remove_order_personal_data', array( $this, 'remove_order_personal_data'	), 10, 1 );

			// export.
			add_filter( 'woocommerce_privacy_export_order_personal_data_props', array( $this, 'add_privacy_export_order_personal_data_fields' ), 10, 2 );
			add_filter( 'woocommerce_privacy_export_order_personal_data_prop', array( $this, 'export_order_personal_data_prop' ), 10, 3 );

		}

		/**
		 * Return the value of personal data.
		 *
		 * @param $value
		 * @param $prop
		 * @param $order
		 *
		 * @return mixed
		 *
		 * @since 2.0.4
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function export_order_personal_data_prop( $value, $prop, $order ) {
			if ( isset( $this->raq_exporter_data[ $prop ] ) ) {
				$value = yit_get_prop( $order, $prop );
			}

			return $value;
		}

		/**
		 * @param $props_to_export
		 * @param $order
		 *
		 * @return array
		 * @since 2.0.4
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_privacy_export_order_personal_data_fields( $props_to_export, $order ) {
			$status = yit_get_prop( $order, 'ywraq_raq_status' );

			if ( ! empty( $status ) ) {
				$props_to_export = array_merge( $props_to_export, $this->raq_exporter_data );
			}

			return $props_to_export;
		}

		/**
		 * Remove personal data.
		 *
		 * @param WC_Order $order .
		 *
		 * @return bool|void
		 * @since 2.0.4
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function remove_order_personal_data( $order ) {
			$status      = yit_get_prop( $order, 'ywraq_raq_status' );
			$meta_values = array();

			if ( empty( $status ) ) {
				return false;
			}

			$props_to_remove = $this->raq_eraser_data;
			if ( $props_to_remove ) {
				foreach ( $props_to_remove as $prop => $data_type ) {
					// Get the current value in edit context.
					$value = $order->get_meta_data( $prop );
					// If the value is empty, it does not need to be anonymized.
					if ( empty( $value ) || empty( $data_type ) ) {
						continue;
					}

					if ( function_exists( 'wp_privacy_anonymize_data' ) ) {
						$anon_value = wp_privacy_anonymize_data( $data_type, $value );
					} else {
						$anon_value = '';
					}

					$meta_values[ $prop ] = apply_filters( 'woocommerce_privacy_remove_order_personal_data_prop_value', $anon_value, $prop, $value, $data_type, $order );
				}

				is_array( $meta_values ) && yit_set_prop( $order, $meta_values );
			}
		}


		/**
		 * Return the personal data to export.
		 *
		 * @return array
		 * @since 2.0.4
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function get_privacy_export_personal_data_props() {
			return apply_filters(
				'ywraq_privacy_export_personal_data_props',
				array(
					'ywraq_customer_name'       => esc_html__( 'Request a quote - Customer Name', 'yith-woocommerce-request-a-quote' ),
					'ywraq_customer_email'      => esc_html__( 'Request a quote - Customer Email', 'yith-woocommerce-request-a-quote' ),
					'ywraq_customer_message'    => esc_html__( 'Request a quote - Customer Message', 'yith-woocommerce-request-a-quote' ),
					'ywraq_other_email_content' => esc_html__( 'Request a quote - Email Content', 'yith-woocommerce-request-a-quote' ),
				)
			);
		}

		/**
		 * Return the list of personal data to remove.
		 *
		 * @return array
		 * @since 2.0.4
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function get_privacy_eraser_personal_data_props() {
			return apply_filters(
				'ywraq_privacy_eraser_personal_data_props',
				array(
					'ywraq_customer_name'       => 'text',
					'ywraq_customer_email'      => 'email',
					'ywraq_customer_message'    => 'longtext',
					'_raq_request'              => 'longtext',
					'ywraq_other_email_fields'  => 'longtext',
					'ywraq_other_email_content' => 'longtext',
				)
			);
		}

	}
}

/**
 * Unique access to instance of YITH_Request_Quote_Privacy class
 *
 * @return \YITH_Request_Quote_Privacy
 */
function YITH_Request_Quote_Privacy() {
	return YITH_Request_Quote_Privacy::get_instance();
}

YITH_Request_Quote_Privacy();