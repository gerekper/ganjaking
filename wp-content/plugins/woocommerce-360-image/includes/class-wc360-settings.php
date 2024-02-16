<?php
/**
 * WooCommerce 360° Image Settings
 *
 * @package WC_360_Image
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WC_360_Image_Settings' ) ) {
	/**
	 * WC360 Settings Class.
	 */
	class WC_360_Image_Settings {

		protected static $instance;

		private function __construct() {

			// WC360 Settings
			add_filter( 'woocommerce_get_sections_products', array( $this, 'add_section' ) );
			add_filter( 'woocommerce_get_settings_products', array( $this, 'all_settings' ), 10, 2 );
		}

		/**
		 * Start the Class when called
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( ! self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}


		/**
		 * Add 360 Settings Section to Products Tab
		 *
		 * @since 1.0.0
		 */
		public function add_section( $sections ) {

			$sections['wc360'] = __( '360 Image', 'woocommerce-360-image' );

			return $sections;
		}


		/**
		 * General Overall Settings
		 *
		 * @since 1.0.0
		 */
		public function all_settings( $settings, $current_section ) {
			/**
			 * Check the current section is what we want
			 */
			if ( 'wc360' === $current_section ) {

				$settings_360 = array();

				$settings_360[] = array(
					'name' => __( 'WooCommerce 360 Image', 'woocommerce-360-image' ),
					'type' => 'title',
					'desc' => __( 'The following options are used to configure the WooCommerce 360 Image display.', 'woocommerce-360-image' ),
					'id'   => 'woocommerce_360_image',
				);

				$settings_360[] = array(

					'name'     => __( 'Full Screen', 'woocommerce-360-image' ),
					'desc_tip' => __( 'This will add a button to your 360 Image Displays that opens up a full screen view.', 'woocommerce-360-image' ),
					'id'       => 'wc360_fullscreen_enable',
					'type'     => 'checkbox',
					'css'      => 'min-width:300px;',
					'default'  => 'no',
					'desc'     => __( 'Enable Full Screen Option', 'woocommerce-360-image' ),

				);

				$settings_360[] = array(

					'name'     => __( 'Navigation Controls', 'woocommerce-360-image' ),
					'desc_tip' => __( 'This will enable the navigation control buttons.', 'woocommerce-360-image' ),
					'id'       => 'wc360_navigation_enable',
					'type'     => 'checkbox',
					'css'      => 'min-width:300px;',
					'default'  => 'yes',
					'desc'     => __( 'Enable Navigation Controls', 'woocommerce-360-image' ),

				);

				$settings_360[] = array(
					'type' => 'sectionend',
					'id'   => 'woocommerce_360_image',
				);

				return $settings_360;

				/**
				 * If not, return the standard settings
				 */

			} else {

				return $settings;

			}
		}
	}

}
