<?php
/**
 * Abstract Settings Page class
 *
 * @package WC_Account_Funds/Abstracts
 * @since   2.6.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Account_Funds_Settings_Page', false ) ) {
	return;
}

/**
 * Class WC_Account_Funds_Settings_Page
 */
abstract class WC_Account_Funds_Settings_Page extends WC_Settings_Page {

	/**
	 * The settings API instance.
	 *
	 * @since 2.6.0
	 *
	 * @var WC_Settings_API
	 */
	protected $settings_api;

	/**
	 * Constructor.
	 *
	 * @since 2.6.0
	 */
	public function __construct() {
		add_filter( "woocommerce_get_sections_{$this->id}", array( $this, 'register_sections' ), 0 );

		parent::__construct();

	}

	/**
	 * Initializes the settings API.
	 *
	 * @since 2.6.0
	 */
	public function init_settings_api() {}

	/**
	 * Registers the page sections.
	 *
	 * @since 2.6.0
	 *
	 * @return array An array with the page sections.
	 */
	public function register_sections() {
		return array();
	}

	/**
	 * Outputs the page settings.
	 *
	 * @since 2.6.0
	 */
	public function output() {
		if ( ! $this->settings_api ) {
			$this->init_settings_api();
		}

		if ( $this->settings_api instanceof WC_Settings_API ) {
			$this->settings_api->admin_options();
		} else {
			parent::output();
		}
	}

	/**
	 * Saves the page settings.
	 *
	 * @since 2.6.0
	 */
	public function save() {
		if ( ! $this->settings_api ) {
			$this->init_settings_api();
		}

		if ( $this->settings_api instanceof WC_Settings_API ) {
			$this->settings_api->process_admin_options();
		} else {
			parent::save();
		}
	}
}
