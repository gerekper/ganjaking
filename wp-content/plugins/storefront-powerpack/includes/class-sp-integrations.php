<?php
/**
 * Storefront Powerpack Integrations Class
 *
 * @author   WooThemes
 * @package  Storefront_Powerpack
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Integrations' ) ) :

	/**
	 * The integrations class
	 */
	class SP_Integrations {

		/**
		 * Setup class.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			/**
			 * Composite Products integration
			 */
			if ( class_exists( 'WooCommerce' ) && class_exists( 'WC_Composite_Products' ) ) {

				if ( function_exists( 'WC_CP' ) && version_compare( WC_CP()->version, '3.0', '>=' ) ) {

					// Add customizer options.
					include_once( 'customizer/integrations/composite-products/customizer.php' );

					// Implement customizations in the front-end.
					include_once( 'customizer/integrations/composite-products/frontend.php' );
				}
			}
		}
	}

endif;

return new SP_Integrations();
