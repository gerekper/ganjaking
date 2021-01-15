<?php
/**
 * weLaunch ThirdParty Fixes Class
 *
 * @class weLaunch_ThirdParty_Fixes
 * @version 3.0.0
 * @package weLaunch Framework/Classes
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'weLaunch_ThirdParty_Fixes', false ) ) {

	/**
	 * Class weLaunch_ThirdParty_Fixes
	 */
	class weLaunch_ThirdParty_Fixes extends weLaunch_Class {

		/**
		 * weLaunch_ThirdParty_Fixes constructor.
		 *
		 * @param object $parent weLaunchFramework pointer.
		 */
		public function __construct( $parent ) {
			parent::__construct( $parent );

			$this->gt3_page_builder();
		}

		/**
		 * GT3 Page Buiolder fix.
		 */
		private function gt3_page_builder() {
			// Fix for the GT3 page builder: http://www.gt3themes.com/wordpress-gt3-page-builder-plugin/.
			if ( has_action( 'ecpt_field_options_' ) ) {
				global $pagenow;

				if ( 'admin.php' === $pagenow ) {
					remove_action( 'admin_init', 'pb_admin_init' );
				}
			}
		}
	}
}
