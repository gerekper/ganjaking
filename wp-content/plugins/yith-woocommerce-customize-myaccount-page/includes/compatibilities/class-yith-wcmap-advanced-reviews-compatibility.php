<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YITH WooCommerce Advanced Reviews Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

if ( ! class_exists( 'YITH_WCMAP_Advanced_Reviews_Compatibility' ) ) {
	/**
	 * Class YITH_WCMAP_Advanced_Reviews_Compatibility
	 *
	 * @since 3.0.0
	 */
	class YITH_WCMAP_Advanced_Reviews_Compatibility extends YITH_WCMAP_Compatibility {

		/**
		 * Constructor
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			$this->endpoint_key = 'reviews';
			$this->endpoint     = array(
				'slug'    => 'ywar-reviews',
				'label'   => __( 'Reviews', 'yith-woocommerce-customize-myaccount-page' ),
				'icon'    => 'star',
				'content' => '[yith_ywar_show_current_user_reviews]',
			);

			// Register endpoint.
			$this->register_endpoint();
		}
	}
}
