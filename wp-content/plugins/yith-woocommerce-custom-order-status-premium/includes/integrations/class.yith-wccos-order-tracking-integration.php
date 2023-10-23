<?php
/**
 * Order tracking integration.
 *
 * @package YITH\CustomOrderStatus
 */

defined( 'YITH_WCCOS' ) || exit; // Exit if accessed directly.

/**
 * YITH Order Traking Compatibility Class
 *
 * @class   YITH_WCCOS_Order_Tracking_Integration
 * @since   1.1.14
 */
class YITH_WCCOS_Order_Tracking_Integration {

	/**
	 * Single instance of the class.
	 *
	 * @var \YITH_WCCOS_Order_Tracking_Integration
	 */
	private static $instance;

	/**
	 * Singleton implementation.
	 *
	 * @return YITH_WCCOS_Order_Tracking_Integration
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * YITH_WCCOS_Order_Tracking_Integration constructor.
	 */
	private function __construct() {
		if ( $this->is_active() ) {
			add_filter( 'yith_wccos_email_placeholders', array( $this, 'add_order_tracking_placeholders' ), 10, 2 );
		}
	}

	/**
	 * Add placeholders for order tracking.
	 *
	 * @param array    $placeholders The placeholders.
	 * @param WC_Order $order        The order.
	 *
	 * @return array
	 */
	public function add_order_tracking_placeholders( $placeholders, $order ) {
		// phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

		/**
		 * The Order Tracking instance.
		 *
		 * @var YITH_WooCommerce_Order_Tracking_Premium $YWOT_Instance
		 */
		global $YWOT_Instance;

		if ( class_exists( 'YITH_Tracking_Data' ) && is_callable( 'YITH_Tracking_Data::get' ) ) {
			$data = YITH_Tracking_Data::get( $order );

			$carrier_id                        = $data->get_carrier_id();
			$placeholders['{tracking_number}'] = $data->get_tracking_code();
			$placeholders['{shipping_date}']   = date_i18n( get_option( 'date_format' ), strtotime( $data->get_pickup_date() ) );
			$placeholders['{carrier_name}']    = $this->get_carrier_name( $carrier_id );

			// YITH_Tracking_Data::get_estimated_delivery_date is available since Order Tracking 2.0.0.
			if ( is_callable( array( $data, 'get_estimated_delivery_date' ) ) ) {
				$placeholders['{estimated_delivery}'] = $data->get_estimated_delivery_date();
			}
		}

		if ( $YWOT_Instance && is_callable( array( $YWOT_Instance, 'get_track_url' ) ) ) {
			$placeholders['{tracking_url}'] = $YWOT_Instance->get_track_url( $order );
		}

		// phpcs:enable

		return $placeholders;
	}

	/**
	 * Get carrier name.
	 *
	 * @param string $carrier_id The carrier string ID.
	 *
	 * @return string
	 * @since 1.8.1
	 */
	private function get_carrier_name( $carrier_id ) {
		static $carriers_list = null;

		$carrier_name = '';

		if ( ! ! $carrier_id ) {
			if ( is_null( $carriers_list ) ) {
				if ( class_exists( 'Carriers' ) && is_callable( 'Carriers::get_instance' ) ) {
					$carriers = Carriers::get_instance();

					/**
					 * The 'get_selected_carriers' method is available since Order Tracking 1.7.1:
					 * it's preferred since the 'get_carrier_list' requires an active license
					 * to retrieve the carrier list since Order Tracking 2.0.0.
					 */
					if ( is_callable( array( $carriers, 'get_selected_carriers' ) ) ) {
						$carriers_list = $carriers->get_selected_carriers();
					} elseif ( is_callable( array( $carriers, 'get_carrier_list' ) ) ) {
						$carriers_list = $carriers->get_carrier_list();
					}
				}
			}

			if ( ! is_null( $carriers_list ) ) {
				$carrier_name = $carriers_list[ $carrier_id ]['name'] ?? '';
			}
		}

		return $carrier_name;
	}

	/**
	 * Is the plugin active?
	 *
	 * @return bool
	 */
	public function is_active() {
		$min_version = apply_filters( 'yith_wccos_order_tracking_integration_min_version', '1.5.7' );

		return defined( 'YITH_YWOT_PREMIUM' ) && defined( 'YITH_YWOT_VERSION' ) && version_compare( YITH_YWOT_VERSION, $min_version, '>=' );
	}
}

return YITH_WCCOS_Order_Tracking_Integration::get_instance();
