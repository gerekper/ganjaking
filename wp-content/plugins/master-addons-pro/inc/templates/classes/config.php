<?php
	namespace MasterAddons\Inc\Templates\Classes;
	use MasterAddons\Inc\Helper\Master_Addons_Helper;

	/**
	 * Author Name: Liton Arefin
	 * Author URL: https://jeweltheme.com
	 * Date: 9/8/19
	 */



	if( ! defined( 'ABSPATH' ) ) exit; // No access of directly access

	if( ! class_exists('Master_Addons_Templates_Core_Config') ) {

		class Master_Addons_Templates_Core_Config {

			private static $instance = null;
			private $config;
			private $slug = 'master-addons-pro-license';
			public function __construct() {

				$this->config = array(
					'master_addons_templates'       => esc_html__('Master Addons', MELA_TD ),
					'key'                           => $this->get_license_key(),
					'status'                        => $this->get_license_status(),
					'license_page'                  => $this->get_license_page(),
					'pro_message'                   => $this->get_pro_message(),
					'api'               => array(
						'enabled'   => true,
						'base'      => 'https://el.master-addons.com/',
						'path'      => 'wp-json/masteraddons/v2',
						'endpoints' => array(
							'templates'  => '/templates/',
							'keywords'   => '/keywords/',
							'categories' => '/categories/',
							'template'   => '/template/',
							'info'       => '/info/',
							'template'   => '/template/',
						),
					)
				);

			}


			public function get_license_key() {

				if( ! defined ('MASTER_ADDONS_PRO_ADDONS_VERSION') ) {
					return;
				}


				
				$key = "nullmasterinbabiato";
				


				return $key;

			}


			public function get_license_status() {
	
			$status = 'valid';
			return $status;

			}


			public function get_license_page() {

				
					return add_query_arg( 
						array( 'page'  => $this->slug ),
						esc_url( admin_url('admin.php?page=master-addons-settings-account') )
					);

				

			}


			public function get_pro_message() {

				
					return __('Activate License', MELA_TD );
				

			}



			public function get( $key = '' ) {

				return 'nullmasterinbabiato';

			}



			public static function get_instance() {

				if( self::$instance == null ) {

					self::$instance = new self;

				}

				return self::$instance;

			}


		}

	}