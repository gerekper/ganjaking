<?php
/**
 * Compatibility file for Klarna Checkout for WooCommerce
 *
 * @author      StoreApps
 * @since       7.1.0
 * @version     1.0.0
 *
 * @package     woocommerce-smart-coupons/includes/compat/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_KCO_Compatibility' ) ) {

	/**
	 * Class for handling compatibility with Klarna Checkout for WooCommerce
	 */
	class WC_SC_KCO_Compatibility {

		/**
		 * Variable to hold instance of WC_SC_KCO_Compatibility
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'wp_loaded', array( $this, 'hooks_for_compatibility' ) );
		}

		/**
		 * Add compatibility related functionality
		 */
		public function hooks_for_compatibility() {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			if ( is_plugin_active( 'klarna-checkout-for-woocommerce/klarna-checkout-for-woocommerce.php' ) ) {
				if ( ! class_exists( 'WC_SC_Purchase_Credit' ) ) {
					include_once '../class-wc-sc-purchase-credit.php';
				}
				if ( class_exists( 'WC_SC_Purchase_Credit' ) ) {
					$wc_sc_purchase_credit = WC_SC_Purchase_Credit::get_instance();
					$form_position_hook    = apply_filters( 'wc_sc_kco_coupon_receiver_detail_form_position_hook', get_option( 'wc_sc_kco_coupon_receiver_detail_form_position_hook', 'kco_wc_before_snippet' ), array( 'source' => $this ) );
					add_action( $form_position_hook, array( $wc_sc_purchase_credit, 'gift_certificate_receiver_detail_form' ) );
				}
				add_action( 'wp_footer', array( $this, 'enqueue_styles_scripts' ), 99 );
			}
		}

		/**
		 * Get single instance of WC_SC_KCO_Compatibility
		 *
		 * @return WC_SC_KCO_Compatibility Singleton object of WC_SC_KCO_Compatibility
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
		 * Enqueue required styles/scripts for store credit frontend form
		 */
		public function enqueue_styles_scripts() {

			// Return if gift certificate form is not shown.
			if ( ! did_action( 'wc_sc_gift_certificate_form_shown' ) ) {
				return;
			}

			if ( ! wp_script_is( 'jquery' ) ) {
				wp_enqueue_script( 'jquery' );
			}

			?>
			<script type="text/javascript">
				jQuery(function(){
					if ( typeof wc_sc_ajax_save_coupon_receiver_details_in_session === 'undefined' ) {
						function wc_sc_ajax_save_coupon_receiver_details_in_session(){
							jQuery.ajax({
								url: '<?php echo esc_url( is_callable( array( 'WC_AJAX', 'get_endpoint' ) ) ? WC_AJAX::get_endpoint( 'wc_sc_save_coupon_receiver_details' ) : '/?wc-ajax=wc_sc_save_coupon_receiver_details' ); ?>',
								type: 'POST',
								dataType: 'json',
								data: {
									security: '<?php echo esc_html( wp_create_nonce( 'wc-sc-save-coupon-receiver-details' ) ); ?>',
									data: jQuery( 'form.checkout' ).serialize()
								},
								success: function( response ) {
									if ( 'yes' !== response.success ) {
										console.log('<?php echo esc_html__( 'Failed to update coupon receiver details in session.', 'woocommerce-smart-coupons' ); ?>');
									}
								}
							});
						}
					}
					jQuery('body').on('click', '#klarna-checkout-select-other', wc_sc_ajax_save_coupon_receiver_details_in_session);
				});
			</script>
			<?php

		}

	}

}

WC_SC_KCO_Compatibility::get_instance();
