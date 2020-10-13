<?php

/**
 * Class VI_WNOTIFICATION_Frontend_Logs
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WNOTIFICATION_Frontend_Logs {
	protected $settings;

	public function __construct() {
		$this->settings = new VI_WNOTIFICATION_Data();
		if ( $this->settings->save_logs() ) {
			add_action( 'template_redirect', array( $this, 'init' ) );
		}
	}

	/**
	 * Detect IP
	 */
	public function init() {

		if ( ! isset( $_GET['link'] ) ) {
			return false;
		}

		if ( wp_verify_nonce( $_GET['link'], 'wocommerce_notification_click' ) ) {
			$this->save_click();
		} else {
			return false;
		}
	}

	/**
	 * Save click
	 */
	private function save_click() {
		/*Check Save Logs Option*/
		if ( is_product() ) {
			$product_id = get_the_ID();
			$file_name  = mktime( 0, 0, 0, date( "m" ), date( "d" ), date( "Y" ) ) . '.txt';
			$file_path  = VI_WNOTIFICATION_CACHE . $file_name;
			if ( ! is_dir( VI_WNOTIFICATION_CACHE ) ) {
				wp_mkdir_p( VI_WNOTIFICATION_CACHE );
				file_put_contents( VI_WNOTIFICATION_CACHE . '.htaccess', '<IfModule !mod_authz_core.c>
Order deny,allow
Deny from all
</IfModule>
<IfModule mod_authz_core.c>
  <RequireAll>
    Require all denied
  </RequireAll>
</IfModule>
' );
			}
			if ( is_file( $file_path ) ) {
				file_put_contents( $file_path, ',' . $product_id, FILE_APPEND );
			} else {

				file_put_contents( $file_path, $product_id );
			}
		} else {
			return false;
		}
	}

}