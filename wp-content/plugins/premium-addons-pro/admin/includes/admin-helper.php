<?php

/**
 * PAPRO Admin Helper.
 */
namespace PremiumAddonsPro\Admin\Includes;

use PremiumAddonsPro\License\API;
use PremiumAddonsPro\Includes\White_Label\Helper;

// Premium Addons Classes
use PremiumAddons\Admin\Includes\PA_Rollback;
use PremiumAddons\Includes\Helper_Functions;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Admin_Helper
 */
class Admin_Helper {

	/**
	 * Class instance
	 *
	 * @var instance
	 */
	private static $instance = null;

	/**
	 * Premium Addons Settings Page Slug
	 *
	 * @var page_slug
	 */
	protected $page_slug = 'premium-addons';


	/**
	 * Current Screen ID
	 *
	 * @var current_screen
	 */
	public static $current_screen = null;

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		// Plugin Action Links
		add_filter( 'plugin_action_links_' . PREMIUM_PRO_ADDONS_BASENAME, array( $this, 'insert_action_links' ) );

		// Add PRO Admin Tabs
		add_filter( 'pa_admin_register_tabs', array( $this, 'add_admin_tab' ) );

		// Enqueue required admin scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		// Register AJAX Hooks
		add_action( 'wp_ajax_pa_wht_lbl_save_settings', array( $this, 'save_white_labeling_settings' ) );

		// PAPRO License Actions
		add_action( 'admin_init', array( $this, 'papro_register_option' ) );

		add_action( 'admin_post_papro_license_activate', array( $this, 'action_papro_license_activate' ) );

		add_action( 'admin_post_papro_license_deactivate', array( $this, 'action_papro_license_deactivate' ) );

	}

	/**
	 * Insert Action Links
	 *
	 * @since 2.0.7
	 * @access private
	 *
	 * @param array $links plugin action links
	 *
	 * @return void
	 */
	public function insert_action_links( $links ) {

		$settings_link = sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'admin.php?page=' . $this->page_slug . '#tab=general' ), __( 'Settings', 'premium-addons-pro' ) );

		array_push( $links, $settings_link );

		return $links;
	}


	/**
	 * Admin Enqueue Scripts
	 *
	 * Enqueue the required assets on our admin pages
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_enqueue_scripts() {

		$current_screen = self::get_current_screen();

		if ( strpos( $current_screen, $this->page_slug ) !== false ) {

			wp_enqueue_script(
				'papro-admin',
				PREMIUM_PRO_ADDONS_URL . 'admin/assets/js/admin.js',
				array( 'jquery' ),
				PREMIUM_PRO_ADDONS_VERSION,
				true
			);

			$localized_data = array(
				'settings' => array(
					'ajaxurl'  => admin_url( 'admin-ajax.php' ),
					'nonce'    => wp_create_nonce( 'papro-white-labeling' ),
					'adminurl' => admin_url(),
					'status'   => self::get_license_status(),
				),
			);

			wp_localize_script( 'papro-admin', 'premiumProAddonsSettings', $localized_data );

		}
	}

	/**
	 * Gets current screen slug
	 *
	 * @since 3.3.8
	 * @access public
	 *
	 * @return string current screen slug
	 */
	public static function get_current_screen() {

		self::$current_screen = get_current_screen()->id;

		return isset( self::$current_screen ) ? self::$current_screen : false;

	}

	/**
	 * Add admin tab
	 *
	 * Register a new tab in plugin settings page
	 *
	 * @since 3.20.9
	 * @access private
	 *
	 * @param array $tab
	 */
	public function add_admin_tab( $tabs ) {

		$slug = $this->page_slug;

		$white_label = Helper::get_white_labeling_settings();

		if ( ! $white_label['premium-wht-lbl-license'] ) {
			$tabs['license'] = array(
				'id'       => 'license',
				'slug'     => $slug . '#tab=license',
				'title'    => __( 'License', 'premium-addons-pro' ),
				'href'     => '#tab=license',
				'template' => PREMIUM_PRO_ADDONS_PATH . 'admin/includes/templates/license',
			);
		}

		// Hide White Label tab
		if ( $white_label['premium-wht-lbl-option'] ) {
			unset( $tabs['white-label'] );
		}

		// Hide General tab
		if ( $white_label['premium-wht-lbl-about'] ) {
			unset( $tabs['general'] );
		}

		// Hide Version Control tab
		if ( $white_label['premium-wht-lbl-version'] ) {
			unset( $tabs['version-control'] );
		}

		return $tabs;
	}

	/**
	 * PAPRO Register Option
	 *
	 * Register license key input field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function papro_register_option() {

		register_setting( 'papro_license', 'papro_license_key', array( $this, 'papro_sanitize_license' ) );
	}

	/**
	 * PAPRO Sanitize License
	 *
	 * @param string $new new license key
	 * @return void
	 */
	public function papro_sanitize_license( $new ) {

		$old = self::get_license_key();

		// new license has been entered, so must reactivate
		if ( $old && $old != $new ) {
			delete_option( 'papro_license_status' );
		}

		return $new;
	}

	/**
	 * Action PAPRO License Activate
	 *
	 * Sends the entered license key to activation function
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function action_papro_license_activate() {

		check_admin_referer( 'papro_nonce', 'papro_nonce' );

		$license = trim( $_POST['papro_license_key'] );

		API::papro_activate_license( $license );
	}

	/**
	 * Action PAPRO License Deactivate
	 *
	 * Sends the entered license key to deactivate function
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function action_papro_license_deactivate() {

		check_admin_referer( 'papro_nonce', 'papro_nonce' );

		$license = self::get_license_key();

		API::papro_deactivate_license( $license );
	}

	/**
	 *
	 * Get License Status
	 *
	 * Get the stored license status
	 *
	 * @since 1.1.1
	 * @access public
	 */
	public static function get_license_status() {

		$status = get_option( 'papro_license_status' );

		return ( ! $status ) ? false : $status;

	}

	/**
	 * Get License Key
	 *
	 * @since 1.1.1
	 * @access public
	 *
	 * @return boolean|string license key
	 */
	public static function get_license_key() {

		$license = get_option( 'papro_license_key' );

		return ( ! $license ) ? false : trim( $license );

	}

	/**
	 * Get Encrypted Key
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return string
	 */
	public static function get_encrypted_key() {

		$input_string = self::get_license_key();

		$status = self::get_license_status();

		if ( 'valid' !== $status ) {
			return '';
		}

		$start  = 5;
		$length = mb_strlen( $input_string ) - $start - 5;

		$mask_string  = preg_replace( '/\S/', 'X', $input_string );
		$mask_string  = mb_substr( $mask_string, $start, $length );
		$input_string = substr_replace( $input_string, $mask_string, $start, $length );

		return $input_string;
	}

	/**
	 * Save White Labeling Settings
	 *
	 * @since 2.0.7
	 * @access public
	 */
	public function save_white_labeling_settings() {

		check_ajax_referer( 'papro-white-labeling', 'security' );

		if ( ! isset( $_POST['fields'] ) ) {
			return;
		}

		$settings = array();
		parse_str( $_POST['fields'], $settings );

		$new_settings = array(
			'premium-wht-lbl-name'            => sanitize_text_field( $settings['premium-wht-lbl-name'] ),
			'premium-wht-lbl-name-pro'        => sanitize_text_field( $settings['premium-wht-lbl-name-pro'] ),
			'premium-wht-lbl-url'             => sanitize_text_field( $settings['premium-wht-lbl-url'] ),
			'premium-wht-lbl-url-pro'         => sanitize_text_field( $settings['premium-wht-lbl-url-pro'] ),
			'premium-wht-lbl-plugin-name'     => sanitize_text_field( $settings['premium-wht-lbl-plugin-name'] ),
			'premium-wht-lbl-plugin-name-pro' => sanitize_text_field( $settings['premium-wht-lbl-plugin-name-pro'] ),
			'premium-wht-lbl-short-name'      => sanitize_text_field( $settings['premium-wht-lbl-short-name'] ),
			'premium-wht-lbl-short-name-pro'  => sanitize_text_field( $settings['premium-wht-lbl-short-name-pro'] ),
			'premium-wht-lbl-desc'            => sanitize_text_field( $settings['premium-wht-lbl-desc'] ),
			'premium-wht-lbl-desc-pro'        => sanitize_text_field( $settings['premium-wht-lbl-desc-pro'] ),
			'premium-wht-lbl-prefix'          => sanitize_text_field( $settings['premium-wht-lbl-prefix'] ),
			'premium-wht-lbl-badge'           => sanitize_text_field( $settings['premium-wht-lbl-badge'] ),
			'premium-wht-lbl-row'             => intval( $settings['premium-wht-lbl-row'] ? 1 : 0 ),
			'premium-wht-lbl-changelog'       => intval( $settings['premium-wht-lbl-changelog'] ? 1 : 0 ),
			'premium-wht-lbl-option'          => intval( $settings['premium-wht-lbl-option'] ? 1 : 0 ),
			'premium-wht-lbl-rate'            => intval( $settings['premium-wht-lbl-rate'] ? 1 : 0 ),
			'premium-wht-lbl-about'           => intval( $settings['premium-wht-lbl-about'] ? 1 : 0 ),
			'premium-wht-lbl-license'         => intval( $settings['premium-wht-lbl-license'] ? 1 : 0 ),
			'premium-wht-lbl-not'             => intval( $settings['premium-wht-lbl-not'] ? 1 : 0 ),
			'premium-wht-lbl-logo'            => intval( $settings['premium-wht-lbl-logo'] ? 1 : 0 ),
			'premium-wht-lbl-version'         => intval( $settings['premium-wht-lbl-version'] ? 1 : 0 ),
		);

		update_option( 'pa_wht_lbl_save_settings', $new_settings );

		wp_send_json_success();

	}

	/**
	 * Creates and returns an instance of the class
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return object
	 */
	public static function get_instance() {

		if ( self::$instance == null ) {

			self::$instance = new self();

		}

		return self::$instance;
	}

}
