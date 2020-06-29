<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCCH' ) ) {
	exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'YITH_Privacy_Plugin_Abstract' ) ){
	return false;
}

if ( ! class_exists( 'YITH_WCCH_Privacy' )  ) {

	class YITH_WCCH_Privacy extends YITH_Privacy_Plugin_Abstract {

		/**
		 * YITH_WCCH_Privacy constructor.
		 */
		public function __construct() {

			$plugin_data = get_plugin_data( YITH_WCCH_FILE );
			$plugin_name = $plugin_data['Name'];

			parent::__construct( $plugin_name );
		}

		/**
		 * Gets the message of the privacy to display.
		 * To be overloaded by the implementor.
		 *
		 * @return string
		 */
		public function get_privacy_message( $section ) {

			$privacy_content_path = YITH_WCCH_TEMPLATE_PATH . '/privacy/html-policy-content-' . $section . '.php';

			if ( file_exists( $privacy_content_path ) ) {

                ob_start();

                include $privacy_content_path;

                return ob_get_clean();

            }

            return '';
		}
	}

	new YITH_WCCH_Privacy();

}
