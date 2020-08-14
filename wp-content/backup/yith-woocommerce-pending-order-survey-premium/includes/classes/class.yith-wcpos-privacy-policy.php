<?php
if( !defined( 'ABSPATH')){
	exit;
}

if( !class_exists( 'YITH_Pending_Survey_Privacy_Policy' ) && class_exists( 'YITH_Privacy_Plugin_Abstract' )  ){

	class YITH_Pending_Survey_Privacy_Policy extends  YITH_Privacy_Plugin_Abstract {

		public function __construct() {
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
			}
			$plugin_info = get_plugin_data( YITH_WCPO_SURVEY_FILE );

			$name = $plugin_info['Name'];
			parent::__construct( $name );
		}

		/**
		 * @return string
		 */
		public function get_privacy_message( $section ) {


			$privacy_content_path = YITH_WCPO_SURVEY_TEMPLATE_PATH.'privacy/html-policy-content-'.$section.'.php';

			if( file_exists( $privacy_content_path ) ){

				ob_start();
				include $privacy_content_path;
				return ob_get_clean();
			}

			return '';
		}
	}


	new YITH_Pending_Survey_Privacy_Policy();
}