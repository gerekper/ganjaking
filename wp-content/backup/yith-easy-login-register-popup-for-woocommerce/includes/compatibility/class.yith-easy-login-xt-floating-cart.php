<?php
/**
 * XT WooCommerce Floating Cart compatibility class
 *
 * @author  YITH
 * @package YITH Easy Login & Register Popup For WooCommerce
 * @version 1.5.0
 */

defined( 'YITH_WELRP' ) || exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_Easy_Login_Xt_Floating_Cart' ) ) {
	/**
	 * XT WooCommerce Floating Cart compatibility class
	 *
	 * @since 1.0.0
	 */
	class YITH_Easy_Login_Xt_Floating_Cart {

		/**
		 * Constructor
		 *
		 * @since 1.5.0
		 * @return void
		 */
		public function __construct() {
			add_filter( 'yith_welrp_script_main_selectors', [ $this, 'add_selectors' ], 10, 2 );
			add_filter( 'yith_welrp_init_popup', '__return_true' );
			// enqueue assets
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_custom_styles' ], 30 );
		}

		/**
		 * Add selectors to localized script
		 *
		 * @since 1.5.0
		 * @author Francesco Licandro
		 * @param $selectors
		 * @return array
		 */
		public function add_selectors( $selectors ) {
			$selectors[] = 'a.xt_woofc-checkout.xt_woofc-btn';
			return $selectors;
		}

		/**
		 * Enqueue custom style
		 *
		 * @since 1.5.0
		 * @author Francesco Licandro
		 * @return void
		 */
		public function enqueue_custom_styles() {
			wp_add_inline_style( 'yith_welrp_css', '#yith-welrp .yith-welrp-overlay,#yith-welrp .yith-welrp-popup-wrapper{z-index: 100000;}' );
		}
	}
}

new YITH_Easy_Login_Xt_Floating_Cart();
