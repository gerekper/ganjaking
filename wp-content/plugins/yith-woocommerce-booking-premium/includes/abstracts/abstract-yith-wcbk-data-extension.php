<?php
/**
 * Class YITH_WCBK_Data_Extension
 * Allow extending data.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Data_Extension' ) ) {
	/**
	 * Class YITH_WCBK_Data_Extension
	 */
	abstract class YITH_WCBK_Data_Extension {
		use YITH_WCBK_Multiple_Singleton_Trait;

		/*
		|--------------------------------------------------------------------------
		| Methods to override.
		|--------------------------------------------------------------------------
		*/

		/**
		 * Get settings.
		 *
		 * @return array
		 */
		protected function get_settings(): array {
			return array();
		}

		/*
		|--------------------------------------------------------------------------
		| Settings
		|--------------------------------------------------------------------------
		*/

		/**
		 * Get single settings.
		 *
		 * @param string      $key     The key.
		 * @param mixed|false $default Default value.
		 *
		 * @return false|mixed
		 */
		final protected function get_single_settings( string $key, $default = false ) {
			$settings = $this->get_settings();

			return $settings[ $key ] ?? $default;
		}

		/**
		 * Get meta_keys_to_props.
		 *
		 * @return array
		 */
		final protected function get_meta_keys_to_props(): array {
			return $this->get_single_settings( 'meta_keys_to_props', array() );
		}

		/**
		 * Get internal_meta_keys.
		 *
		 * @return array
		 */
		final protected function get_internal_meta_keys(): array {
			return $this->get_single_settings( 'internal_meta_keys', array() );
		}
	}
}
