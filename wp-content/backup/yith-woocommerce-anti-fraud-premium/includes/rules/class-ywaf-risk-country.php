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

if ( ! class_exists( 'YWAF_Risk_Country' ) ) {

	/**
	 * Risk country rules class
	 *
	 * @class   YWAF_Risk_Country
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWAF_Risk_Country extends YWAF_Rules {

		private $risk_countries = array();

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$countries = get_option( 'ywaf_rules_risk_country_list' );

			$this->risk_countries = ( is_array( $countries ) ? $countries : explode( ',', $countries ) );

			$message = __( 'Order coming from an unsafe country.', 'yith-woocommerce-anti-fraud' );
			$points  = get_option( 'ywaf_rules_risk_country_weight', 10 );

			parent::__construct( $message, $points );

		}

		/**
		 * Check if order comes from a risk country.
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

			if ( ! empty( $this->risk_countries ) ) {

				if ( in_array( $order->get_billing_country(), $this->risk_countries ) ) {
					$fraud_risk = true;
				}

			}

			return $fraud_risk;

		}

	}
}