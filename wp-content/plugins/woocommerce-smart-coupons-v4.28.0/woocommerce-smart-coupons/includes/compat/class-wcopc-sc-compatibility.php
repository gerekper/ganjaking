<?php
/**
 * Compatibility file for WooCommerce One Page Checkout
 *
 * @author      StoreApps
 * @since       3.3.0
 * @version     1.0.0
 * @package     WooCommerce Smart Coupons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WCOPC_SC_Compatibility' ) ) {

	/**
	 * Class for handling compatibility with WooCommerce One Page Checkout
	 */
	class WCOPC_SC_Compatibility {

		/**
		 * Variable to hold instance of WCOPC_SC_Compatibility
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {

			add_filter( 'woocommerce_update_order_review_fragments', array( $this, 'update_order_review_fragments' ) );
			add_action( 'wcopc_before_display_checkout', array( $this, 'add_styles_and_scripts' ) );
			add_filter( 'wc_sc_call_for_credit_product_id', array( $this, 'call_for_credit_product_id' ), 10, 2 );

		}

		/**
		 * Get single instance of WCOPC_SC_Compatibility
		 *
		 * @return WCOPC_SC_Compatibility Singleton object of WCOPC_SC_Compatibility
		 */
		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Handle call to functions which is not available in this class
		 *
		 * @param string $function_name Function to call.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return mixed Result of function call.
		 */
		public function __call( $function_name, $arguments = array() ) {

			global $woocommerce_smart_coupon;

			if ( ! is_callable( array( $woocommerce_smart_coupon, $function_name ) ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $woocommerce_smart_coupon, $function_name ), $arguments );
			} else {
				return call_user_func( array( $woocommerce_smart_coupon, $function_name ) );
			}
		}

		/**
		 * Generate & add coupon receiver details form in update order review fragments
		 *
		 * @param  array $fragments Existing fragments.
		 * @return array $fragments
		 */
		public function update_order_review_fragments( $fragments = array() ) {

			if ( ! class_exists( 'WC_SC_Purchase_Credit' ) ) {
				include_once '../class-wc-sc-purchase-credit.php';
			}

			$wc_sc_purchase_credit = WC_SC_Purchase_Credit::get_instance();

			ob_start();
			$wc_sc_purchase_credit->gift_certificate_receiver_detail_form();
			$fragments['wc_sc_receiver_detail_form'] = ob_get_clean();

			return $fragments;
		}

		/**
		 * Add Styles And Scripts
		 */
		public function add_styles_and_scripts() {
			if ( ! class_exists( 'WC_SC_Purchase_Credit' ) ) {
				include_once '../class-wc-sc-purchase-credit.php';
			}
			$wc_sc_purchase_credit = WC_SC_Purchase_Credit::get_instance();
			$wc_sc_purchase_credit->enqueue_timepicker();
			add_action( 'wp_footer', array( $this, 'styles_and_scripts' ) );
		}

		/**
		 * Styles And Scripts
		 */
		public function styles_and_scripts() {
			if ( ! wp_script_is( 'jquery' ) ) {
				wp_enqueue_script( 'jquery' );
			}
			?>
			<script type="text/javascript">
				jQuery(function(){
					jQuery(document.body).on('updated_checkout', function( e, data ){
						if ( data.fragments.wc_sc_receiver_detail_form ) {
							if ( jQuery('div.gift-certificate.sc_info_box').length > 0 ) {
								jQuery('div.gift-certificate.sc_info_box').replaceWith( data.fragments.wc_sc_receiver_detail_form );
							} else {
								jQuery('div#customer_details').after( data.fragments.wc_sc_receiver_detail_form );
							}
						} else {
							jQuery('div.gift-certificate.sc_info_box').remove();
						}
					});
				});
			</script>
			<?php
		}

		/**
		 * Call For Credit Product Id
		 *
		 * @param  integer $product_id The product id.
		 * @param  array   $args       Additional arguments.
		 * @return integer
		 */
		public function call_for_credit_product_id( $product_id = 0, $args = array() ) {

			$action = ( ! empty( $_REQUEST['action'] ) ) ? wc_clean( wp_unslash( $_REQUEST['action'] ) ) : ''; // phpcs:ignore

			if ( 'pp_add_to_cart' === $action && empty( $product_id ) ) {
				$product_id = ( ! empty( $_REQUEST['add_to_cart'] ) ) ? absint( $_REQUEST['add_to_cart'] ) : 0; // phpcs:ignore
			}

			return $product_id;
		}

	}

}

/**
 * Initialize the Compatibility
 */
function initialize_wcopc_sc_compatibility() {
	WCOPC_SC_Compatibility::get_instance();
}
add_action( 'wcopc_loaded', 'initialize_wcopc_sc_compatibility' );
