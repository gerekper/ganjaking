<?php
/**
 * Class YITH_WCBK_Google_Maps_Shortcodes
 * Handle the Google Maps shortcodes.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\GoogleMaps
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Google_Maps_Shortcodes' ) ) {
	/**
	 * YITH_WCBK_Google_Maps_Shortcodes class.
	 *
	 * @since   4.0
	 */
	class YITH_WCBK_Google_Maps_Shortcodes {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * The constructor.
		 */
		protected function __construct() {
			add_shortcode( 'booking_map', array( $this, 'booking_map' ) );
		}

		/**
		 * Render the booking_map shortcode.
		 *
		 * @param array|string $atts Attributes.
		 *
		 * @return string
		 */
		public function booking_map( $atts = array() ): string {
			$product_id = isset( $atts['id'] ) ? absint( $atts['id'] ) : false;
			$product    = yith_wcbk_get_booking_product( $product_id );
			ob_start();

			$coordinates = false;
			if ( isset( $atts['latitude'], $atts['longitude'] ) ) {
				$coordinates = array(
					'lat' => $atts['latitude'],
					'lng' => $atts['longitude'],
				);
			} elseif ( $product ) {
				$coordinates = $product->get_location_coordinates();
			}

			if ( $coordinates ) {
				$width  = $atts['width'] ?? '100%';
				$height = $atts['height'] ?? '500px';
				$zoom   = absint( $atts['zoom'] ?? 9 );
				$type   = $atts['type'] ?? 'ROADMAP';

				$width  = ( ! is_numeric( $width ) ) ? $width : $width . 'px';
				$height = ( ! is_numeric( $height ) ) ? $height : $height . 'px';

				$args = compact( 'coordinates', 'product', 'width', 'height', 'zoom', 'type' );

				yith_wcbk_get_module_template( 'google-maps', 'shortcodes/booking-map.php', $args );
			}

			return ob_get_clean();
		}
	}
}
