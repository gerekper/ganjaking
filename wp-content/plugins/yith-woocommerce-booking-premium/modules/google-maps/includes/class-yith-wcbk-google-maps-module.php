<?php
/**
 * Class YITH_WCBK_Google_Maps_Module
 * Handle the Google Maps module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\GoogleMaps
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Google_Maps_Module' ) ) {
	/**
	 * YITH_WCBK_Google_Maps_Module class.
	 *
	 * @since   4.0
	 */
	class YITH_WCBK_Google_Maps_Module extends YITH_WCBK_Module {

		const KEY = 'google-maps';

		/**
		 * On load.
		 */
		public function on_load() {
			YITH_WCBK_Google_Maps_Product_Data_Extension::get_instance();
			YITH_WCBK_Google_Maps_Shortcodes::get_instance();
		}

		/**
		 * Add admin scripts.
		 *
		 * @param array  $scripts The scripts.
		 * @param string $context The context [admin or frontend].
		 *
		 * @return array
		 */
		public function filter_scripts( array $scripts, string $context ): array {
			$google_maps_key = get_option( 'yith-wcbk-google-maps-api-key', '' );
			if ( ! ! $google_maps_key ) {
				$scripts['google-maps'] = array(
					'src'           => "//maps.google.com/maps/api/js?libraries=places&key=$google_maps_key",
					'context'       => 'common',
					'use_min'       => false,
					'deps'          => array(),
					'admin_enqueue' => 'product',
				);
			}

			$scripts['yith-wcbk-google-maps-autocomplete'] = array(
				'src'           => $this->get_url( 'assets/js/autocomplete.js' ),
				'context'       => 'common',
				'deps'          => array( 'jquery', 'google-maps' ), // Not having 'google-maps' as dep, to show an error in console if Google Maps is not loaded.
				'admin_enqueue' => 'product',
			);

			$scripts['yith-wcbk-booking-map'] = array(
				'src'              => $this->get_url( 'assets/js/booking-map.js' ),
				'context'          => 'frontend',
				'deps'             => array( 'jquery', 'google-maps' ),
				'localize_globals' => array( 'bk' ),
			);

			return $scripts;
		}
	}
}
