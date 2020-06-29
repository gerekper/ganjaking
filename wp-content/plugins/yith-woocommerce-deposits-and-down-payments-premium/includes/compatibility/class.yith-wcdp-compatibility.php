<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_WCDP_Compatibility' ) ) {

	class YITH_WCDP_Compatibility {

		protected static $_instance;

		public function __construct() {

			if ( defined( 'YITH_YWPI_INIT' ) ) {
				require_once( 'class.yith-wcdp-yith-pdf-invoice-compatibility.php' );
				YITH_WCDP_YITH_PDF_Invoice_Compatibility();
			}

			if ( defined( 'YITH_YWDPD_PREMIUM' ) ) {
				require_once( 'class.yith-wcdp-yith-dynamic-pricing-and-discounts-compatibility.php' );
				YITH_WCDP_YITH_Dynamic_Pricing_And_Discounts();
			}

			if ( defined( 'YITH_WCEVTI_INIT' ) ) {
				require_once( 'class.yith-wcdp-yith-event-tickets-compatibility.php' );
				YITH_WCDP_YITH_Event_Tickets();
			}

			if ( defined( 'YITH_WCPO_INIT' ) ) {
				require_once( 'class.yith-wcdp-yith-pre-order-compatibility.php' );
				YITH_WCDP_YITH_Pre_Order();
			}

			if ( defined( 'YITH_WCP_PREMIUM' ) ) {
				require_once( 'class.yith-wcdp-yith-composite-products-compatibility.php' );
				YITH_WCDP_YITH_Composite_Products();
			}

			if ( defined( 'YITH_WAPO_PREMIUM' ) ) {
				require_once( 'class.yith-wcdp-yith-advanced-product-options-compatibility.php' );
				YITH_WCDP_YITH_Advanced_Product_Options();
			}

			if ( defined( 'YITH_WPV_PREMIUM' ) ) {
				require_once( 'class.yith-wcdp-yith-multi-vendor.php' );
				YITH_WCDP_YITH_Multi_Vendor();
			}

			if ( defined( 'YITH_YWGC_INIT' ) ) {
				require_once( 'class.yith-wcdp-yith-gift-card.php' );
				YITH_WCDP_YITH_Gift_Cards();
			}

			if ( defined( 'YITH_WCPB_INIT' ) && version_compare( YITH_WCPB_VERSION, '1.3.0', '>=' ) ) {
				require_once( 'class.yith-wcdp-yith-products-bundle-compatibility.php' );
				YITH_WCDP_YITH_Products_Bundle();
			}
		}

		/**
		 * @return YITH_WCDP_Compatibility unique access
		 * @since  1.0.0
		 * @author YITHEMES
		 */
		public static function get_instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}
}

/**
 * @return YITH_WCDP_Compatibility
 */
function YITH_WCDP_Compatibility() {

	return YITH_WCDP_Compatibility::get_instance();
}