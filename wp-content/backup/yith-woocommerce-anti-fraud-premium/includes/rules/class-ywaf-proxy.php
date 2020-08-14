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

if ( ! class_exists( 'YWAF_Proxy' ) ) {

	/**
	 * Proxy rules class
	 *
	 * @class   YWAF_Proxy
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWAF_Proxy extends YWAF_Rules {

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$message = __( 'Order placed from behind a proxy.', 'yith-woocommerce-anti-fraud' );
			$points  = get_option( 'ywaf_rules_proxy_weight', 10 );

			parent::__construct( $message, $points );

		}

		/**
		 * Check if order comes from behind a proxy.
		 *
		 * @since   1.0.0
		 *
		 * @param   $order WC_Order
		 *
		 * @return  boolean
		 * @author  Alberto Ruggiero
		 */
		public function get_fraud_risk( $order ) {

			$ip_address = $order->get_customer_ip_address();
			$dnsbls     = apply_filters( 'ywaf_proxy_dnsbls', array(
				'dnsbl-1.uceprotect.net',
				'dnsbl-2.uceprotect.net',
				'dnsbl-3.uceprotect.net',
				'dnsbl.dronebl.org',
				'dnsbl.sorbs.net',
				'bl.spamcop.net',
				'sbl.spamhaus.org',
				'xbl.spamhaus.org',
				'zen.spamhaus.org',
				'pbl.spamhaus.org',
				'b.barracudacentral.org'
			) );

			//total number of blacklist databases
			$total_dnsbls = count( $dnsbls );

			//number of positive blacklist checks
			$ip_blacklisted = 0;

			//Threshold to consider this as a valid proxy IP address
			$blacklist_threshold = 0.3;

			$reverse_ip = implode( ".", array_reverse( explode( ".", $ip_address ) ) );

			foreach ( $dnsbls as $host ) {

				if ( checkdnsrr( $reverse_ip . "." . $host . ".", "A" ) ) {
					$ip_blacklisted ++;
				}

			}

			$fraud_risk = ( $ip_blacklisted / $total_dnsbls >= $blacklist_threshold ) ? true : false;

			return $fraud_risk;

		}

	}
}