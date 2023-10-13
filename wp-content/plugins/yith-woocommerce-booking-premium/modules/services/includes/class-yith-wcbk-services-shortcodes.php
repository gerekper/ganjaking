<?php
/**
 * Class YITH_WCBK_Services_Shortcodes
 * Handle shortcodes for the Services module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Services
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Services_Shortcodes' ) ) {
	/**
	 * YITH_WCBK_Services_Shortcodes class.
	 */
	class YITH_WCBK_Services_Shortcodes {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * YITH_WCBK_Services_Products constructor.
		 */
		protected function __construct() {
			add_shortcode( 'booking_services', array( $this, 'booking_services' ) );
		}

		/**
		 * Booking services
		 *
		 * @param string|array $atts Attributes.
		 *
		 * @return string
		 */
		public function booking_services( $atts = array() ): string {
			global $product;
			ob_start();
			/**
			 * The booking product.
			 *
			 * @var WC_Product_Booking $product
			 */
			if ( $product && yith_wcbk_is_booking_product( $product ) ) {
				$defaults        = array(
					'type'              => 'all',
					'show_title'        => 'yes',
					'show_prices'       => 'no',
					'show_descriptions' => 'yes',
				);
				$atts            = wp_parse_args( $atts, $defaults );
				$atts['product'] = $product;

				yith_wcbk_get_module_template( 'services', 'shortcodes/booking-services.php', $atts );
			}

			return ob_get_clean();
		}
	}
}
