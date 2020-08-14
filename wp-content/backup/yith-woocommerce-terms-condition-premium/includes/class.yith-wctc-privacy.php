<?php
/**
 * Privacy class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Terms & Condtions Popup
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCTC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCTC_Privacy' ) ) {
	/**
	 * Privacy class to let customer export/erase personal data
	 *
	 * @since 1.2.1
	 */
	class YITH_WCTC_Privacy extends YITH_Privacy_Plugin_Abstract {

		/**
		 * Constructor.
		 *
		 * @param array $details
		 *
		 * @return \YITH_WCTC_Privacy
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct( _x( 'YITH WooCommerce Terms and Conditions Popup', 'Privacy Policy Content', 'yith-woocommerce-terms-conditions' ) );

			// hook to order exporter, to export personal data sent to MailChimp
			add_filter( 'woocommerce_privacy_export_order_personal_data_props', array(
				$this,
				'register_props_to_export_within_order'
			), 10, 2 );
			add_filter( 'woocommerce_privacy_export_order_personal_data_prop', array(
				$this,
				'retrieve_prop_to_export_within_order'
			), 10, 3 );
		}

		/**
		 * Register props to export within the order section
		 *
		 * @param $props array Array of props to export
		 * @param $order \WC_Order Current order being exported
		 *
		 * @return array Array of filtered props
		 */
		public function register_props_to_export_within_order( $props, $order ) {
			$props['yith_wctc_agreement_data'] = __( 'Terms & Conditions agreement', 'yith-woocommerce-terms-conditions' );

			return $props;
		}

		/**
		 * Retrieve props to export within the order section
		 *
		 * @param $value string Calculated value for current prop
		 * @param $prop  string Current prop
		 * @param $order \WC_Order Current order
		 *
		 * @return string Calculated prop value
		 */
		public function retrieve_prop_to_export_within_order( $value, $prop, $order ) {
			if ( 'yith_wctc_agreement_data' == $prop ) {
				$terms_type          = yit_get_prop( $order, '_yith_wctc_terms_type', true );
				$hide_checkboxes     = yit_get_prop( $order, '_yith_wctc_hide_checkboxes', true );
				$terms_accepted      = yit_get_prop( $order, '_yith_wctc_terms_accepted', true );
				$privacy_accepted    = yit_get_prop( $order, '_yith_wctc_privacy_accepted', true );
				$last_terms_update   = yit_get_prop( $order, '_yith_wctc_last_terms_update', true );
				$last_privacy_update = yit_get_prop( $order, '_yith_wctc_last_privacy_update', true );

				if ( empty( $terms_type ) ) {
					return __( 'No data registered for this order', 'yith-woocommerce-terms-conditions-premium' );
				}

				$results = array();

				if ( in_array( $terms_type, array( 'terms', 'both' ) ) ) {
					$label = __( 'Terms & Conditions accepted:', 'yith-woocommerce-terms-conditions-premium' );
					$info  = $hide_checkboxes == 'yes' || $terms_accepted == 'yes' ? __( 'yes', 'yith-woocommerce-terms-conditions-premium' ) : __( 'no', 'yith-woocommerce-terms-conditions-premium' );

					$results[] = sprintf( '%s %s', $label, $info );

					$label = __( 'T&C last update:', 'yith-woocommerce-terms-conditions-premium' );
					$info  = date_i18n( wc_date_format(), strtotime( $last_terms_update ) );

					$results[] = sprintf( '%s %s', $label, $info );
				}

				if ( in_array( $terms_type, array( 'privacy', 'both' ) ) ) {
					$label = __( 'Privacy Policy accepted:', 'yith-woocommerce-terms-conditions-premium' );
					$info  = $hide_checkboxes == 'yes' || $privacy_accepted == 'yes' ? __( 'yes', 'yith-woocommerce-terms-conditions-premium' ) : __( 'no', 'yith-woocommerce-terms-conditions-premium' );

					$results[] = sprintf( '%s %s', $label, $info );

					$label = __( 'Privacy last update:', 'yith-woocommerce-terms-conditions-premium' );
					$info  = date_i18n( wc_date_format(), strtotime( $last_privacy_update ) );

					$results[] = sprintf( '%s %s', $label, $info );
				}

				return implode( ', ', $results );
			}

			return $value;
		}
	}

	// let's register exporter/eraser
	new YITH_WCTC_Privacy();
}