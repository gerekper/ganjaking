<?php
/**
 * Load the plugin text domain for translation.
 *
 * @package  weLaunch Framework/Classes
 * @since    3.0.5
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'weLaunch_I18n', false ) ) {

	/**
	 * Class weLaunch_I18n
	 */
	class weLaunch_I18n extends weLaunch_Class {

		/**
		 * weLaunch_I18n constructor.
		 *
		 * @param object $parent weLaunchFramework pointer.
		 * @param string $file Translation file.
		 */
		public function __construct( $parent, $file ) {
			parent::__construct( $parent );

			$this->load( $file );
		}

		/**
		 * Load translations.
		 *
		 * @param string $file Path to translation files.
		 */
		private function load( $file ) {
			$domain = 'welaunch-framework';

			$core = $this->core();

			/**
			 * Locale for text domain
			 * filter 'welaunch/textdomain/basepath/{opt_name}'
			 *
			 * @param string     The locale of the blog or from the 'locale' hook
			 * @param string     'welaunch-framework'  text domain
			 */
			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			$locale = apply_filters( 'welaunch/locale', get_locale(), 'welaunch-framework' );
			$mofile = $domain . '-' . $locale . '.mo';

			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			$basepath = apply_filters( "welaunch/textdomain/basepath/{$core->args['opt_name']}", weLaunch_Core::$dir );

			$loaded = load_textdomain( $domain, weLaunch_Core::$dir . 'languages/' . $mofile );

			if ( ! $loaded ) {
				$mofile = WP_LANG_DIR . '/plugins/' . $mofile;

				$loaded = load_textdomain( $domain, $mofile );
			}
		}
	}
}
