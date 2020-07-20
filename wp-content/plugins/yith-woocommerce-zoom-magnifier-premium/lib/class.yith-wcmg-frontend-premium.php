<?php
/**
 * Frontend class
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Zoom Magnifier
 * @version 1.1.2
 */

if ( ! defined( 'YITH_WCMG' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMG_Frontend_Premium' ) ) {
	/**
	 * Admin class.
	 * The class manage all the Frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMG_Frontend_Premium extends YITH_WCMG_Frontend {

		/**
		 * Constructor
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function __construct( ) {

			parent::__construct(  );
		}

		/**
		 * Enqueue styles and scripts
		 *
		 * @access public
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue_styles_scripts() {

						/** @var YITH_WooCommerce_Zoom_Magnifier_Premium $yith_wcmg */
			global $yith_wcmg;

			if ( $yith_wcmg->is_product_excluded() ) {
				return;
			}

			parent::enqueue_styles_scripts();

		}


		public function render() {

			/** @var YITH_WooCommerce_Zoom_Magnifier_Premium $yith_wcmg */
			global $yith_wcmg;

			//  Check if the plugin have to interact with current product
			if ( $yith_wcmg->is_product_excluded() ) {
				return;
			}

			//  Call the parent method
			parent::render();

		}
	}
}
