<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WAPO' ) ) {
	exit( 'Direct access forbidden.' );
}

if ( class_exists( 'YITH_Privacy_Plugin_Abstract' ) && ! class_exists( 'YITH_WAPO_Privacy' ) ) {

	class YITH_WAPO_Privacy extends YITH_Privacy_Plugin_Abstract {

		/**
		 * YITH_WAPO_Privacy constructor.
		 */
		public function __construct() {

			parent::__construct( YITH_WAPO_NAME );

		}

		/**
		 * Gets the message of the privacy to display.
		 * To be overloaded by the implementor.
		 *
		 * @return string
		 */
		public function get_privacy_message( $section ) {

			$privacy_content_path = YITH_WAPO_TEMPLATE_PATH . '/privacy/html-policy-content-' . $section . '.php';

			if ( file_exists( $privacy_content_path ) ) {

                ob_start();

                include $privacy_content_path;

                return ob_get_clean();

            }

            return '';
		}
	}

	new YITH_WAPO_Privacy();

}
