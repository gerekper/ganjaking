<?php

/**
 * Class CT_Ultimate_GDPR_Update_Legacy_Options
 */
class CT_Ultimate_GDPR_Update_Legacy_Options {

	/**
	 *
	 */
	const CT_ULTIMATE_GDPR_VERSION_OPTION_KEY = 'ct_ultimate_gdpr_version';

	/**
	 * @var
	 */
	private $plugin_version;
	/**
	 * @var
	 */
	private $database_version;
	/**
	 * @var
	 */
	private $controller_cookie;

	/**
	 * CT_Ultimate_GDPR_Update_Legacy_Options constructor.
	 */
	public function __construct() {
	}

	/**
	 * Create cookies from predefined services (if not exist already)
	 */
	public function update_cookie_manager_posts() {
		if ( function_exists( 'acf' ) ) {
			$registered_cookies = $this->get_registered_cookies_names( false );
			$registered_scripts = $this->get_registered_scripts_names();
			$services           = CT_Ultimate_GDPR_Model_Services::instance()->get_services();
			$empty_cookies      = self::get_empty_cookie_array();
			/** @var CT_Ultimate_GDPR_Service_Abstract $service */
			foreach ( $services as $service ) {
				$service_cookies = $service->cookies_to_block_filter( $empty_cookies, true );
				$service_scripts = $service->script_blacklist_filter( $empty_cookies , true );
				$service_name    = $service->get_name();
				$service_id      = $service->get_id();
				$this->create_cookie_posts( $service_cookies, $registered_cookies, $service_scripts, $registered_scripts, $service_name, $service_id );
			}
		}
	}

	/**
	 * Check version, load user defined cookies to posts, make backup
	 */
	public function run_updater() {
		if ( function_exists( 'acf' ) ) {
			$plugin_version_array   = get_file_data( dirname( dirname( __DIR__ ) ) . '/ct-ultimate-gdpr.php', array( 'Version' ), 'plugin' );
			$this->plugin_version   = $plugin_version_array[0];
			$this->database_version = $database_version = get_option( self::CT_ULTIMATE_GDPR_VERSION_OPTION_KEY );
			if ( ! $this->database_version || version_compare( $this->database_version, $this->plugin_version ) == - 1 ) {
				$this->update_database();
				update_option( self::CT_ULTIMATE_GDPR_VERSION_OPTION_KEY, $this->plugin_version );
			}
		}
	}

	/**
	 *
	 */
	private function update_database() {
		if ( version_compare( $this->plugin_version, 1.4 ) != 1 ) {
			$this->migrate_user_cookies_from_options_to_cpt();
		}
	}

	/**
	 *
	 */
	private function migrate_user_cookies_from_options_to_cpt() {
		$this->import_cookie_settings();
	}

	/**
	 *
	 */
	private function import_cookie_settings() {
		$registered_cookies   = $this->get_registered_cookies();
		$user_created_cookies = $this->get_user_created_cookies();
		$this->create_cookie_posts( $user_created_cookies, $registered_cookies );
		$this->backup_user_cookies();
		$this->clear_user_cookies();
	}

	/**
	 * @return array
	 */
	private function get_registered_cookies() {
		$this->controller_cookie = CT_Ultimate_GDPR::instance()->get_controller_by_id( CT_Ultimate_GDPR_Controller_Cookie::ID );
		$registered_cookies      = $this->get_registered_cookies_names();

		return $registered_cookies;
	}

	/**
	 * @return array
	 */
	public function get_user_created_cookies() {
		$user_created_cookies                                                  = array();
		$necessary                                                             = CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'cookie_cookies_group_2', '', CT_Ultimate_GDPR_Controller_Cookie::ID );
		$necessary                                                             = array_filter( array_map( 'trim', explode( ',', $necessary ) ) );
		$user_created_cookies[ CT_Ultimate_GDPR_Model_Group::LEVEL_NECESSARY ] = $necessary;

		$convenience                                                             = CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'cookie_cookies_group_3', '', CT_Ultimate_GDPR_Controller_Cookie::ID );
		$convenience                                                             = array_filter( array_map( 'trim', explode( ',', $convenience ) ) );
		$user_created_cookies[ CT_Ultimate_GDPR_Model_Group::LEVEL_CONVENIENCE ] = $convenience;

		$statistics                                                             = CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'cookie_cookies_group_4', '', CT_Ultimate_GDPR_Controller_Cookie::ID );
		$statistics                                                             = array_filter( array_map( 'trim', explode( ',', $statistics ) ) );
		$user_created_cookies[ CT_Ultimate_GDPR_Model_Group::LEVEL_STATISTICS ] = $statistics;

		$targetting                                                             = CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'cookie_cookies_group_5', '', CT_Ultimate_GDPR_Controller_Cookie::ID );
		$targetting                                                             = array_filter( array_map( 'trim', explode( ',', $targetting ) ) );
		$user_created_cookies[ CT_Ultimate_GDPR_Model_Group::LEVEL_TARGETTING ] = $targetting;

		return $user_created_cookies;
	}

	/**
	 * @param $cookies_to_migrate
	 * @param $registered_cookies
	 * @param array $service_scripts
	 * @param array $registered_scripts
	 * @param bool $service_name
	 * @param bool $service_id
	 */
	public function create_cookie_posts( $cookies_to_migrate, $registered_cookies, $service_scripts = array(), $registered_scripts = array(), $service_name = false, $service_id = false ) {
		foreach ( $cookies_to_migrate as $cookie_type => $cookie_names ) {
			$cookies_to_insert = array();
			$scripts_to_insert = array();
			foreach ( $cookie_names as $cookie_name ) {
				if ( ! in_array( $cookie_name, $registered_cookies ) ) {
					$cookies_to_insert[] = $cookie_name;
				}
			}
			$script_names = isset( $service_scripts[ $cookie_type ] ) ? $service_scripts[ $cookie_type ] : array();
			foreach ( $script_names as $script_name ) {
				if ( ! in_array( $script_name, $registered_scripts ) ) {
					$scripts_to_insert[] = $script_name;
				}
			}
			$cookie_names_string = implode( ', ', $cookies_to_insert );
			$script_names_string = implode( ', ', $scripts_to_insert );
			if ( strlen( $cookie_names_string ) > 0 || strlen( $script_names_string ) > 0 ) {
				$post_title = $service_name ? $service_name : $cookie_names_string;
				$post_id    = wp_insert_post(
					array(
						'post_title'  => $post_title,
						'post_status' => 'publish',
						'post_type'   => 'ct_ugdpr_service'
					)
				);

				if ( $service_name ) {
					update_field( 'service_name', $service_name, $post_id );
					update_field( 'first_or_third_party', 'third_party', $post_id );
					update_field( 'can_be_blocked', true, $post_id );
				}

				if ( $service_id ) {
					update_field( 'id', $service_name, $post_id );
				} else {
					update_field( 'id', time(), $post_id );
				}

				if ( strlen( $script_names_string ) ) {
					update_field( 'script_name', $script_names_string, $post_id );
				}

				update_field( 'cookie_name', $cookie_names_string, $post_id );
				update_field( 'type_of_cookie', $cookie_type, $post_id );
				update_field( 'is_active', true, $post_id );
			}
		}
	}

	/**
	 *
	 */
	private function backup_user_cookies() {
		$options = $this->controller_cookie->get_all_options();
		if ( ! get_option( 'ct-ultimate-gdpr-cookie-1.4-backup' ) ) {
			add_option( 'ct-ultimate-gdpr-cookie-1.4-backup', $options );
		}
	}

	/**
	 *
	 */
	private function clear_user_cookies() {
		$options                           = $this->controller_cookie->get_all_options();
		$options['cookie_cookies_group_2'] = '';
		$options['cookie_cookies_group_3'] = '';
		$options['cookie_cookies_group_4'] = '';
		$options['cookie_cookies_group_5'] = '';
		update_option( 'ct-ultimate-gdpr-cookie', $options );
	}

	/**
	 * @param bool $get_services
	 *
	 * @return array
	 */
	public function get_registered_cookies_names( $get_services = true ) {
		$all_cookies   = array();
		$empty_cookies = self::get_empty_cookie_array();
		if ( $get_services ) {
			$all_cookies = $this->get_cookies_from_services( $empty_cookies );
		}

		$user_cookies = apply_filters( 'ct_ultimate_gdpr_cookie_get_cookies_to_block', $empty_cookies );

		if ( ! is_array( $user_cookies ) ) {
			$user_cookies = self::get_empty_cookie_array();
		}

		foreach ( $user_cookies as $cookie_names ) {
			foreach ( $cookie_names as $cookie_name ) {
				$all_cookies[] = $cookie_name;
			}
		}

		return $all_cookies;
	}

	/**
	 * @return array
	 */
	public function get_registered_scripts_names() {
		$all_scripts   = array();
		$empty_scripts = self::get_empty_cookie_array();
		$user_scripts  = apply_filters( 'ct_ultimate_gdpr_controller_cookie_script_blacklist', $empty_scripts );

		if ( ! is_array( $user_scripts ) ) {
			$user_scripts = self::get_empty_cookie_array();
		}

		foreach ( $user_scripts as $script_names ) {
			if ( is_array( $script_names ) ) {
				foreach($script_names as $script_name) {
					$all_scripts[] = $script_name;
				}
			}
		}

		return $all_scripts;
	}

	/**
	 * @param $empty_cookies
	 *
	 * @return array
	 */
	public function get_cookies_from_services( $empty_cookies ) {
		$services_cookies = array();
		$services         = CT_Ultimate_GDPR_Model_Services::instance()->get_services();

		foreach ( $services as $service ) {
			$cookies          = $service->cookies_to_block_filter( $empty_cookies );
			$filtered_cookies = array_filter( $cookies );
			foreach ( $filtered_cookies as $cookie_names ) {
				foreach ( $cookie_names as $cookie_name ) {
					$services_cookies[] = $cookie_name;
				}
			}
		}

		return $services_cookies;
	}

	/**
	 * @return array
	 */
	public static function get_empty_cookie_array() {
		$empty_cookies = array(
			CT_Ultimate_GDPR_Model_Group::LEVEL_BLOCK_ALL   => array(),
			CT_Ultimate_GDPR_Model_Group::LEVEL_NECESSARY   => array(),
			CT_Ultimate_GDPR_Model_Group::LEVEL_CONVENIENCE => array(),
			CT_Ultimate_GDPR_Model_Group::LEVEL_STATISTICS  => array(),
			CT_Ultimate_GDPR_Model_Group::LEVEL_TARGETTING  => array(),
		);

		return $empty_cookies;
	}

}