<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class CT_Ultimate_GDPR_Controller_Admin
 *
 */
class CT_Ultimate_GDPR_Controller_Admin {

	/**
	 *
	 */
	const ID = 'ct-ultimate-gdpr-admin';

	/**
	 * @var string
	 */
	private $option_name = 'ct-ultimate-gdpr';
	/**
	 * @var array
	 */
	private $view_options = array();

	/**
	 * CT_Ultimate_GDPR_Admin constructor.
	 */
	public function __construct() {

		$this->register_menu_pages();
		$this->register_option_fields();
		$this->register_styles();

		if ( is_admin() ) {
			$this->admin_actions();
		}

		//Notice if PHP version is lower than 5.6
        add_action( 'admin_notices', array( $this, 'version_controller_check') );

		// set plugin compatibility with itself
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_ct-ultimate-gdpr/ct-ultimate-gdpr.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_ct-ultimate-gdpr/ct-ultimate-gdpr.php', '__return_true' );

		//fix menu highlight when adding new service to service manager
		add_filter( 'parent_file', array( $this, 'add_new_service_select_submenu' ) );

	}

	/**
	 *
	 */
	private function admin_actions() {

		if ( $this->is_request_export_settings() ) {
			add_action( 'ct_ultimate_gdpr_after_controllers_registered', array( $this, 'export_settings' ) );
		}

		if ( $this->is_request_import_settings() ) {
			add_action( 'ct_ultimate_gdpr_after_controllers_registered', array( $this, 'import_settings' ) );
		}

		if ( $this->is_request_export_services() ) {
			add_action( 'ct_ultimate_gdpr_after_controllers_registered', array( $this, 'export_services' ) );
		}

		if ( $this->is_request_import_services() ) {
			add_action( 'ct_ultimate_gdpr_after_controllers_registered', array( $this, 'import_services' ) );
		}

	}

	/**
	 * @return bool
	 */
	private function is_request_export_settings() {

		if ( ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-export', $_POST ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	private function is_request_import_settings() {

		if ( ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-import', $_POST ) ) {
			return true;
		}

		return false;

	}

	/**
	 * @return bool
	 */
	private function is_request_export_services() {

		if ( ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-export-services', $_POST ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	private function is_request_import_services() {

		if ( ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-import-services', $_POST ) ) {
			return true;
		}

		return false;

	}

	/**
	 *
	 */
	public function version_controller_check(){
	    $notice = "";

        if ( function_exists('version_compare')
            && version_compare(PHP_VERSION, '5.6', '<') ){

			$version            = explode( '.', PHP_VERSION );
			$host_version       = $version[0] .".". $version[1];
			$getPlugins         = get_plugins();
			$ct_gdpr_info       = array();
			$required_version   = "5.6";
			$ct_gdpr_info       = $getPlugins['ct-ultimate-gdpr/ct-ultimate-gdpr.php'];

			echo "<div class = 'update-nag imgedit-thumbnail-preview-caption notice'><p>";
			printf(
				esc_html__( "Your server is running PHP version %s but Ultimate GDPR %s requires at least %s", 'ct-ultimate-gdpr' ),
				$host_version,
				$ct_gdpr_info['Version'],
				$required_version
			);
			echo "</p></div>";

        }
    }

	/**
	 *
	 */
	public function export_settings() {

		$controllers = CT_Ultimate_GDPR::instance()->get_controllers();

		$settings = array();

		/** @var CT_Ultimate_GDPR_Controller_Abstract $controller */
		foreach ( $controllers as $controller ) {

			$options = $controller->get_options_to_export();
			if ( $options ) {
				$settings[ $controller->get_id() ] = $options;
			}

		}

		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=ct-ultimate-gdpr-settings-export-' . date( 'm-d-Y' ) . '.json' );
		header( "Expires: 0" );

		echo json_encode( $settings );
		exit;

	}

	/**
	 *
	 */
	public function export_services() {

		$services = array();

		$posts = get_posts( array(
				'post_type'   => 'ct_ugdpr_service',
				'numberposts' => - 1
			)
		);

		/**
		 * @var int $key
		 * @var WP_Post $post
		 */
		foreach ( $posts as $key => $post ) {
			$services[ $key ]['post_title'] = $post->post_title;
			$fields                         = get_fields( $post->ID );
			$services[ $key ]['fields']     = $fields;
		}

		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=ct-ultimate-gdpr-services-export-' . date( 'm-d-Y' ) . '.json' );
		header( "Expires: 0" );

		echo json_encode( $services );
		exit;

	}

	/**
	 * Import from json file
	 */
	public function import_settings() {

		$import_file = isset( $_FILES['ct-ultimate-gdpr-settings-file']['tmp_name'] ) ? $_FILES['ct-ultimate-gdpr-settings-file']['tmp_name'] : '';

		if ( empty( $import_file ) ) {
			$this->view_options['notices'] = array( esc_html__( 'Please upload a file to import', 'ct-ultimate-gdpr' ) );

			return;
		}

		// Retrieve the settings from the file and convert the json object to an array.
		$settings = (array) json_decode( file_get_contents( $import_file ), true );

		if ( empty( $settings ) ) {
			$this->view_options['notices'] = array( esc_html__( 'No options were imported', 'ct-ultimate-gdpr' ) );

			return;
		}

		$updated = false;

		foreach ( $settings as $id => $options ) {

			$check_id = CT_Ultimate_GDPR::instance()->get_controller_by_id( $id );

			if ( $check_id ) {

				// update controller options
				$updated = $updated || update_option( $id, $options );

			}

		}

		$this->view_options['notices'] = $updated ?
			array( esc_html__( 'Settings imported successfully', 'ct-ultimate-gdpr' ) ) :
			array( esc_html__( 'Settings were not imported. Please check the import file.', 'ct-ultimate-gdpr' ) );

	}

	/**
	 * Import from json file
	 */
	public function import_services() {

		$import_file = isset( $_FILES['ct-ultimate-gdpr-services-file']['tmp_name'] ) ? $_FILES['ct-ultimate-gdpr-services-file']['tmp_name'] : '';

		if ( empty( $import_file ) ) {
			$this->view_options['notices'] = array( esc_html__( 'Please upload a file to import', 'ct-ultimate-gdpr' ) );

			return;
		}

		// Retrieve the settings from the file and convert the json object to an array.
		$services = (array) json_decode( file_get_contents( $import_file ), true );

		if ( empty( $services ) ) {
			$this->view_options['notices'] = array( esc_html__( 'No options were imported', 'ct-ultimate-gdpr' ) );

			return;
		}

		$is_inserted = false;
		add_filter( 'wp_insert_post_empty_content', '__return_false' );

		foreach ( $services as $id => $service ) {

			if ( ! isset( $service['post_title'] ) ) {
				return $this->view_options['notices'] = array( esc_html__( 'Wrong file format for services.', 'ct-ultimate-gdpr' ) );
			}

			$post_id     = wp_insert_post(
				array_merge(
					$service,
					array(
						'post_status' => 'publish',
						'post_type'   => 'ct_ugdpr_service'
					)
				)
			);
			$is_inserted = $is_inserted || is_int( $post_id );

			// update controller options
			if ( is_int( $post_id ) && isset( $service['fields'] ) && is_array( $service['fields'] ) ) {

				foreach ( $service['fields'] as $field_key => $field_value ) {

					if ( $field_key ) {
						update_field( $field_key, $field_value, $post_id );
					}

				}

			}

		}

		$this->view_options['notices'] = $is_inserted ?
			array( esc_html__( 'Services imported successfully', 'ct-ultimate-gdpr' ) ) :
			array( esc_html__( 'No service was imported', 'ct-ultimate-gdpr' ) );

		remove_filter( 'wp_insert_post_empty_content', '__return_false' );

	}

	/**
	 * @return string
	 */
	public function get_plugin_domain() {
		return CT_Ultimate_GDPR::DOMAIN;
	}

	/**
	 * register_option_fields
	 */
	private function register_option_fields() {
		add_action( 'current_screen', array( $this, 'add_option_fields' ), 5 );
		add_filter( 'whitelist_options', array( $this, 'whitelist_options_filter' ) );
	}

	/**
	 * register_menu_pages
	 */
	private function register_menu_pages() {
		add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
	}

	/**
	 * Fix WP options bug. @see https://wordpress.stackexchange.com/questions/139660/error-options-page-not-found-on-settings-page-submission-for-an-oop-plugin
	 *
	 * @param $whitelist
	 *
	 * @return mixed
	 */
	public function whitelist_options_filter( $whitelist ) {

		global $wp_settings_fields;

		foreach ( $wp_settings_fields as $field_group_name => $field_group ) {

			if ( 0 === strpos( $field_group_name, $this->get_option_name() ) ) {

				// point section settings to main page option settings
				$whitelist[ $field_group_name ] = array( $field_group_name );

			}
		}

		return $whitelist;
	}

	/**
	 * @param $method_name
	 *
	 * @return string
	 */
	public function get_field_name( $method_name ) {

		$field_name = explode( '_', $method_name );
		array_splice( $field_name, 0, 2 );
		$field_name = implode( '_', $field_name );

		return $field_name;

	}

	/**
	 * @param $field_name
	 *
	 * @return string
	 */
	public function get_field_name_prefixed( $field_name ) {
		$field_name_array    = explode( '_', $field_name );
		$option_name_postfix = array_shift( $field_name_array );

		return $this->get_option_name() . "-$option_name_postfix" . "[$field_name]";

	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 *
	 * @return array
	 */
	public function sanitize( $input ) {
		return $input;
	}

	/**
	 * @return string
	 */
	public function get_option_name() {
		return apply_filters( 'ct_ultimate_gdpr_admin_get_option_name', $this->option_name );
	}

	/**
	 * @param $option_name
	 * @param mixed $default
	 * @param string $section_id
	 * @param string $translate_type for wpml id translations
	 *
	 * @return mixed|string
	 */
	public function get_option_value( $option_name, $default = '', $section_id = '', $translate_type = '' ) {
		$section_id || $section_id = $this->get_current_section();
		$options = $this->get_options( $section_id );

		return isset( $options[ $option_name ] ) ? ( $translate_type ? ct_ultimate_gdpr_wpml_translate_id( $options[ $option_name ], $translate_type ) : $options[ $option_name ] ) : $default;
	}

	/**
	 * @param $option_name
	 * @param string $default
	 * @param string $section_id
	 * @param string $translate_type
	 *
	 * @return string
	 */
	public function get_option_value_escaped( $option_name, $default = '', $section_id = '', $translate_type = '' ) {
		return esc_attr( $this->get_option_value( $option_name, $default, $section_id, $translate_type ) );
	}

	/**
	 *
	 */
	private function register_styles() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts_action' ) );
	}

	/**
	 * Enqueue script on our menu pages only
	 *
	 * @param $hook_suffix
	 */
	public function admin_enqueue_scripts_action( $hook_suffix ) {

		wp_register_style( 'ct-ultimate-gdpr-admin-all-style', ct_ultimate_gdpr_url( '/assets/css/admin-all.min.css' ), array(), ct_ultimate_gdpr_get_plugin_version() );
		wp_enqueue_style( 'ct-ultimate-gdpr-admin-all-style' );

		if ( false !== stripos( $hook_suffix, 'ct-ultimate-gdpr' ) ) {

			wp_register_style( 'ct-ultimate-gdpr-admin-style', ct_ultimate_gdpr_url( '/assets/css/admin.min.css' ), array(), ct_ultimate_gdpr_get_plugin_version() );
			wp_enqueue_style( 'ct-ultimate-gdpr-admin-style' );

			wp_register_script( 'ct-ultimate-gdpr-bootstrap-script', ct_ultimate_gdpr_url() . '/assets/js/bootstrap/bootstrap.min.js', array( 'jquery' ), ct_ultimate_gdpr_get_plugin_version() );
			wp_enqueue_script( 'ct-ultimate-gdpr-bootstrap-script' );

			wp_register_script( 'ct-ultimate-gdpr-admin-libs', ct_ultimate_gdpr_url() . '/assets/js/admin-libs.js', array( 'jquery' ), ct_ultimate_gdpr_get_plugin_version() );
			wp_enqueue_script( 'ct-ultimate-gdpr-admin-libs' );


			wp_enqueue_script(
				'ct-ultimate-gdpr-admin',
				ct_ultimate_gdpr_url( '/assets/js/admin.min.js' ),
				array( 'jquery', 'wp-color-picker', 'ct-ultimate-gdpr-admin-libs' ),
				ct_ultimate_gdpr_get_plugin_version(),
				true
			);
			wp_localize_script( 'ct-ultimate-gdpr-admin', 'ct_ultimate_gdpr_admin_translations',
				array(
					'enabled'  => esc_html__( 'Enabled', 'ct-ultimate-gdpr' ),
					'enable'   => esc_html__( 'Enable', 'ct-ultimate-gdpr' ),
					'disabled' => esc_html__( 'Disabled', 'ct-ultimate-gdpr' ),
					'disable'  => esc_html__( 'Disable', 'ct-ultimate-gdpr' ),
				)
			);

			wp_enqueue_style( 'ct-ultimate-gdpr-bootstrap-style', ct_ultimate_gdpr_url( '/assets/css/bootstrap/bootstrap.min.css' ) );
			wp_enqueue_style( 'ct-ultimate-gdpr-font-awesome', ct_ultimate_gdpr_url( '/assets/css/fonts/font-awesome/css/font-awesome.min.css' ) );
			wp_enqueue_style( 'wp-color-picker' );
 
		}

	}

	/**
	 * Add menu pages
	 */
	public function add_menu_pages() {

		add_menu_page(
			esc_html__( 'Ultimate GDPR', 'ct-ultimate-gdpr' ),
			esc_html__( 'Ultimate GDPR', 'ct-ultimate-gdpr' ),
			'manage_options',
			'ct-ultimate-gdpr',
			array( $this, 'render_menu_page' ),
			'none'
		);

	}

	/**
	 * @param $method
	 * @param $arguments
	 */
	public function __call( $method, $arguments ) {

		/** Render menu page callbacks */
		if ( 0 === strpos( $method, 'render_menu_page' ) ) {
			$this->render_menu_page( $method );

			return;
		}

		echo "$method not found";
	}

	/**
	 * @param $method_name
	 */
	public function render_menu_page( $method_name ) {

		$method_name = str_replace( 'render_menu_page_', '', $method_name );
		if ( $method_name ) {
			$method_name = str_replace( '_', '-', strtolower( $method_name ) );
		}

		$template_name = 'admin/admin-ct-ultimate-gdpr';
		if ( $method_name ) {
			$template_name .= "-$method_name";
		}

		$template_name = apply_filters( 'ct_ultimate_gdpr_admin_template_name', $template_name, $method_name );

		ct_ultimate_gdpr_locate_template( $template_name, true, $this->view_options );

	}

	/**
	 * Add option fields to menu pages
	 */
	public function add_option_fields() {

		register_setting(
			$this->get_option_name(), // Option group
			$this->get_option_name(), // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

	}

	/**
	 * @param string $section_id
	 *
	 * @return array
	 */
	public function get_options( $section_id = '' ) {
		$option_name = $section_id ? $section_id : $this->get_option_name();
		$options = get_option( $option_name, array() );
		return ! empty( $options ) ? $options : $this->load_default_options( $option_name );
	}

	/**
	 * @return string
	 */
	private function get_current_section() {

		if ( ! function_exists( 'get_current_screen' ) || ! get_current_screen() ) {

			// get default option
			return $this->get_option_name();

		}

		$screen  = get_current_screen()->id;
		$section = explode( '_', $screen );
		$section = array_pop( $section );

		if ( $section == 'ct-ultimate-gdpr' ) {
			$section = self::ID;
		}

		return $section;
	}

	/**
	 * @param $option_name
	 *
	 * @return array
	 */
	private function load_default_options( $option_name ) {

		$controller = CT_Ultimate_GDPR::instance()->get_controller_by_id( $option_name );
		$options    = $controller ? $controller->get_default_options() : array();
		update_option( $option_name, $options );

		return $options;
	}

	/**
	 * @param $file
	 *
	 * @return string
	 */
	public function add_new_service_select_submenu( $file ) {
		global $submenu_file ;
		if( isset( $_GET['post'] ) && isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) {
			$post_id = $_GET['post'];
			$post = get_post( $post_id );
			if( $post->post_type == 'ct_ugdpr_service' ) {
				$submenu_file = 'edit.php?post_type=ct_ugdpr_service';
				$file = 'ct-ultimate-gdpr';
			}
		}
		if ( 'post-new.php?post_type=ct_ugdpr_service' == $submenu_file ) {
			$submenu_file = 'edit.php?post_type=ct_ugdpr_service';
			$file = 'ct-ultimate-gdpr';
		}
		return $file;
	}

}