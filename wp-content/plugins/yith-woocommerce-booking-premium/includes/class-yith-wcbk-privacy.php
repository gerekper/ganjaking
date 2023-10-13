<?php
/**
 * Class YITH_WCBK_Privacy
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Privacy' ) ) {
	/**
	 * Class YITH_WCBK_Privacy
	 */
	class YITH_WCBK_Privacy extends YITH_Privacy_Plugin_Abstract {

		/**
		 * YITH_WCBK_Privacy constructor.
		 */
		public function __construct() {
			parent::__construct( YITH_WCBK_PLUGIN_NAME );
		}

		/**
		 * Retrieve the privacy message.
		 *
		 * @param string $section The section.
		 *
		 * @return false|string
		 */
		public function get_privacy_message( $section ) {
			$section              = str_replace( '_', '-', $section );
			$privacy_content_path = YITH_WCBK_VIEWS_PATH . '/privacy/html-policy-content-' . $section . '.php';
			if ( file_exists( $privacy_content_path ) ) {
				ob_start();
				include $privacy_content_path;

				return ob_get_clean();
			}

			return '';
		}
	}
}

new YITH_WCBK_Privacy();
