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
 * Implements the YITH_YWRAQ_Gateway class.
 *
 * @class   YITH_YWRAQ_Gateway
 * @package YITH
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_YWRAQ_Gateway' ) ) {
	/**
	 * Class YITH_YWRAQ_Gateway
	 */
	class YITH_YWRAQ_Gateway extends WC_Payment_Gateway {
		/**
		 * Constructor for the gateway.
		 */
		public function __construct() {
			$this->id                 = 'yith-request-a-quote';
			$this->has_fields         = false;
			$this->title              = apply_filters( 'ywraq_payment_method_label', esc_html__( 'YITH Request a Quote', 'yith-woocommerce-request-a-quote' ) );
			$this->method_title       = apply_filters( 'ywraq_payment_method_label', esc_html__( 'YITH Request a Quote', 'yith-woocommerce-request-a-quote' ) );
			$this->method_description = esc_html__( 'Allows to request a quote at checkout.', 'yith-woocommerce-request-a-quote' );

			$this->enabled = 'yes';

		}
	}
}