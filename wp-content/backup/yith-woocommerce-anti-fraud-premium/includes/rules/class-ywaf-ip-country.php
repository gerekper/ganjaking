<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YWAF_IP_Country' ) ) {

	/**
	 * IP Country rules class
	 *
	 * @class   YWAF_IP_Country
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWAF_IP_Country extends YWAF_Rules {

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$message = __( 'Customer IP address does not match the given billing country.', 'yith-woocommerce-anti-fraud' );
			$points  = get_option( 'ywaf_rules_ip_country_weight', 10 );

			parent::__construct( $message, $points );

		}

		/**
		 * Check if IP Address matches the order billing country.
		 *
		 * @since   1.0.0
		 *
		 * @param   $order WC_Order
		 *
		 * @return  boolean
		 * @author  Alberto Ruggiero
		 */
		public function get_fraud_risk( $order ) {

			$fraud_risk = false;
			$ip_address = $order->get_customer_ip_address();
			$request    = wp_remote_get( 'http://ip-api.com/json/' . $ip_address );
			$response   = json_decode( wp_remote_retrieve_body( $request ) );

			if ( ! $response || $response->status == 'fail' ) {

				$wc_geo_ip    = WC_Geolocation::geolocate_ip( $ip_address );
				$country_code = $wc_geo_ip['country'];

			} else {

				$country_code = $response->countryCode;

			}

			if ( $country_code != $order->get_billing_country() ) {

				$fraud_risk = true;

			}

			return $fraud_risk;

		}

	}

}