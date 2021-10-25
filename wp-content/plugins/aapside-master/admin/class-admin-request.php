<?php

/**
 * Package Appside
 * Author Ir Tech
 * @since 2.0.0
 * */

if (!defined('ABSPATH')){
	exit(); //exit if access directly
}

if (!class_exists('Appside_Admin_Request')){
	class Appside_Admin_Request{

		private static $instance;
		
		public function __construct() {
			add_action('admin_post_appside_license_verify',array($this,'license_verify'));
		}
		/**
		 * get Instance
		 * @since 1.0.0
		 * */
		public static function getInstance(){
			if (null == self::$instance){
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * License verify
		 * @since 2.0.0
		 * */
		public function license_verify(){
			if (isset($_POST['appside_license_verify']) && wp_verify_nonce($_POST['appside_license_verify'],'appside_license_verify')){
				return;
			}

			$endpoint = 'https://irtech.biz/api/license/new';
			$secret_code = 'IcCaCjGDhZzwBWzo2D0YuS7GsTDojwSp';
			$response = wp_remote_post($endpoint,array(
				'sslverify' => false,
				'body' => [
					'purchase_code' => trim($_POST['appside_purchase_code']),
					'site_url' => home_url('/'),
					'item_unique_key' => $secret_code
				]
			));
			if (!is_wp_error($response)){ 

				$licnese_response = json_decode($response['body']);
				update_option('appside_purchase_code',trim($_POST['appside_purchase_code']));
				update_option('appside_secret_code',$secret_code);
				update_option('appside_license_status',$licnese_response->license_status);
				update_option('appside_license_msg',$licnese_response->msg);
			}

			wp_safe_redirect(admin_url('/').'/admin.php?page=theme-license');
			die();
		}


	}//end class
	if ( class_exists('Appside_Admin_Request')){
		Appside_Admin_Request::getInstance();
	}
}