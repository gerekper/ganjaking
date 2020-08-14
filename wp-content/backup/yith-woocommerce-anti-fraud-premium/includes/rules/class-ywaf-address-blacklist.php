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

if ( ! class_exists( 'YWAF_Address_Blacklist' ) ) {

	/**
	 * Address blacklist rules class
	 *
	 * @class   YWAF_Address_Blacklist
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWAF_Address_Blacklist extends YWAF_Rules {

		private $blacklist = array();

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$value           = get_option( 'ywaf_address_blacklist_list' );
			$this->blacklist = ( $value != '' ) ? maybe_unserialize( $value ) : array();

			$message = __( 'Billing and/or shipping address is blacklisted!', 'yith-woocommerce-anti-fraud' );
			$points  = 100;

			parent::__construct( $message, $points );

		}

		/**
		 * Check if address is in blacklist.
		 *
		 * @param   $order WC_Order
		 *
		 * @return  boolean
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function get_fraud_risk( $order ) {

			$fraud_risk = false;

			foreach ( $this->blacklist as $blacklisted_address ) {

				similar_text( strtolower( $order->get_billing_address_1() ), strtolower( $blacklisted_address['address1'] ), $address1 );
				similar_text( strtolower( $order->get_billing_address_2() ), strtolower( $blacklisted_address['address2'] ), $address2 );
				$address2 = ( $order->get_billing_address_2() == '' && $blacklisted_address['address2'] == '' ) ? 100 : $address2;
				similar_text( strtolower( $order->get_billing_city() ), strtolower( $blacklisted_address['city'] ), $city );
				similar_text( strtolower( $order->get_billing_postcode() ), strtolower( $blacklisted_address['postcode'] ), $postcode );
				similar_text( strtolower( $order->get_billing_country() ), strtolower( $blacklisted_address['country'] ), $country );
				similar_text( strtolower( $order->get_billing_state() ), strtolower( $blacklisted_address['state'] ), $state );

				$check_results = ( $address1 + $address2 + $city + $postcode + $country + $state ) / 6;
				if ( $check_results >= get_option( 'ywaf_address_blacklist_similarity_percentage' ) ) {
					$fraud_risk = true;
					break;
				}

				similar_text( strtolower( $order->get_shipping_address_1() ), strtolower( $blacklisted_address['address1'] ), $address1 );
				similar_text( strtolower( $order->get_shipping_address_2() ), strtolower( $blacklisted_address['address2'] ), $address2 );
				$address2 = ( $order->get_shipping_address_2() == '' && $blacklisted_address['address2'] == '' ) ? 100 : $address2;
				similar_text( strtolower( $order->get_shipping_city() ), strtolower( $blacklisted_address['city'] ), $city );
				similar_text( strtolower( $order->get_shipping_postcode() ), strtolower( $blacklisted_address['postcode'] ), $postcode );
				similar_text( strtolower( $order->get_shipping_country() ), strtolower( $blacklisted_address['country'] ), $country );
				similar_text( strtolower( $order->get_shipping_state() ), strtolower( $blacklisted_address['state'] ), $state );

				$check_results = ( $address1 + $address2 + $city + $postcode + $country + $state ) / 6;

				if ( $check_results >= get_option( 'ywaf_address_blacklist_similarity_percentage' ) ) {
					$fraud_risk = true;
					break;
				}

			}

			return $fraud_risk;

		}

		/**
		 * Add address to blacklist.
		 *
		 * @param   $order WC_Order
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function add_to_blacklist( $order ) {

			$this->blacklist[] = array(
				'address1' => $order->get_billing_address_1(),
				'address2' => $order->get_billing_address_2(),
				'city'     => $order->get_billing_city(),
				'postcode' => $order->get_billing_postcode(),
				'country'  => $order->get_billing_country(),
				'state'    => $order->get_billing_state(),
			);

			if ( $order->get_formatted_billing_address() != $order->get_formatted_shipping_address() ) {

				$this->blacklist[] = array(
					'address1' => $order->get_shipping_address_1(),
					'address2' => $order->get_shipping_address_2(),
					'city'     => $order->get_shipping_city(),
					'postcode' => $order->get_shipping_postcode(),
					'country'  => $order->get_shipping_country(),
					'state'    => $order->get_shipping_state(),
				);

			}

			update_option( 'ywaf_address_blacklist_list', maybe_serialize( $this->blacklist ) );

		}

	}

}