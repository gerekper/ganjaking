<?php
/**
 * MailChimp for WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade MailChimp for WooCommerce Memberships to newer
 * versions in the future. If you wish to customize MailChimp for WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/mailchimp-for-woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2017-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\MailChimp;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * MailChimp for WooCommerce Memberships main plugin class.
 *
 * @since 1.0.0
 */
class Plugin extends Framework\SV_WC_Plugin {


	/** plugin version number */
	const VERSION = '1.4.0';

	/** @var \SkyVerge\WooCommerce\Memberships\MailChimp\Plugin single instance of this plugin */
	protected static $instance;

	/** plugin id */
	const PLUGIN_ID = 'memberships_mailchimp';

	/** @var Admin handler instance */
	private $admin;

	/** @var Frontend handler instance */
	private $frontend;

	/** @var AJAX handler instance */
	private $ajax;

	/** @var API handler instance */
	private $api;

	/** @var Membership_Plans instance */
	private $membership_plans;

	/** @var User_Memberships instance */
	private $user_memberships;

	/** @var Background_Sync instance */
	private $background_sync;

	/** @var string the key name of the option where the API key is stored */
	private $api_key_option_name;

	/** @var bool whether there's a working connection with the MailChimp API */
	private $is_connected;

	/** @var bool whether the plugin is in debug mode and the API should log data */
	private $debug_mode;

	/** @var string how members are signed up to the list (automatically or manually) */
	private $members_opt_in_mode;


	/**
	 * Initializes the plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->api_key_option_name = 'wc_memberships_mailchimp_sync_mailchimp_api_key';

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'text_domain'  => 'woocommerce-memberships-mailchimp',
				'dependencies' => array(
					'php_extensions' => array(
						'mbstring',
					),
				),
			)
		);

		/* @see Plugin::add_api_request_logging() */
		if ( $this->is_debug_mode_enabled() ) {
			add_action( 'wc_' . $this->get_id() . '_api_request_performed', array( $this, 'log_api_request' ), 10, 2 );
		} else {
			remove_action( 'wc_' . $this->get_id() . '_api_request_performed', array( $this, 'log_api_request' ), 10 );
		}
	}


	/**
	 * Builds the lifecycle handler instance.
	 *
	 * @since 1.0.6
	 */
	protected function init_lifecycle_handler() {

		$this->lifecycle_handler = new Lifecycle( $this );
	}


	/**
	 * Initializes the plugin's required components.
	 *
	 * @internal
	 *
	 * @since 1.0.6
	 */
	public function init_plugin() {

		// hook into user memberships and membership plans
		$this->membership_plans = new Membership_Plans();
		$this->user_memberships = new User_Memberships();
		$this->background_sync  = new Background_Sync();

		// admin includes
		if ( is_admin() ) {
			$this->admin    = new Admin();
		// frontend includes
		} else {
			$this->frontend = new Frontend();
		}

		// AJAX includes
		if ( is_ajax() ) {
			$this->ajax = new AJAX();
		}
	}


	/**
	 * Returns the main admin handler instance.
	 *
	 * @since 1.0.0
	 *
	 * @return null|Admin
	 */
	public function get_admin_instance() {

		return $this->admin;
	}


	/**
	 * Returns the main front end handler instance.
	 *
	 * @since 1.0.1
	 *
	 * @return null|Frontend
	 */
	public function get_frontend_instance() {

		return $this->frontend;
	}


	/**
	 * Returns the main AJAX handler instance.
	 *
	 * @since 1.0.0
	 *
	 * @return null|AJAX
	 */
	public function get_ajax_instance() {

		return $this->ajax;
	}


	/**
	 * Returns the API handler instance.
	 *
	 * @since 1.0.0
	 *
	 * @return API
	 */
	public function get_api_instance() {

		if ( ! $this->api instanceof API ) {

			$this->api = new API();
		}

		return $this->api;
	}


	/**
	 * Returns the membership plans events handler instance.
	 *
	 * @since 1.0.0
	 *
	 * @return Membership_Plans
	 */
	public function get_membership_plans_instance() {

		return $this->membership_plans;
	}


	/**
	 * Returns the user memberships events handler instance.
	 *
	 * @since 1.0.0
	 *
	 * @return User_Memberships
	 */
	public function get_user_memberships_instance() {

		return $this->user_memberships;
	}


	/**
	 * Gets the background sync handler instance.
	 *
	 * @since 1.0.0
	 *
	 * @return Background_Sync
	 */
	public function get_background_sync_instance() {

		return $this->background_sync;
	}


	/**
	 * Returns the
	 *
	 * @since 1.0.0
	 *
	 * @return MailChimp_Lists
	 */
	public function get_lists_instance() {

		return new MailChimp_Lists();
	}


	/**
	 * Gets the configured API key.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_api_key() {

		// ensures no cached value is used
		wp_cache_delete( $this->api_key_option_name, 'options' );

		$api_key = get_option( $this->api_key_option_name, '' );

		// ensures no cached value is used
		wp_cache_delete( $this->api_key_option_name, 'options' );

		return $api_key;
	}


	/**
	 * Determines if an API key is configured.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function has_api_key() {

		$api_key = $this->get_api_key();

		return ! empty( $api_key );
	}


	/**
	 * Updates the API key setting with a new key.
	 *
	 * @since 1.0.0
	 *
	 * @param string $api_key
	 */
	public function set_api_key( $api_key ) {

		// ensures no cached value is used
		wp_cache_delete( $this->api_key_option_name, 'options' );

		update_option( $this->api_key_option_name, is_string( $api_key ) ? $api_key : '' );

		// ensures no cached value is used
		wp_cache_delete( $this->api_key_option_name, 'options' );
	}


	/**
	 * Checks whether the plugin is successfully connected to the MailChimp API.
	 *
	 * Caches the connection status in a transient to minimize API ping service calls.
	 * Admin methods could manually check if the api key exists and is valid if necessary.
	 * Otherwise this should merely be used to enable MailChimp Sync hooks within the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $use_cache default true: use cached connection status
	 * @return bool
	 */
	public function is_connected( $use_cache = true ) {

		if ( ! $use_cache || null === $this->is_connected ) {

			$this->is_connected = $use_cache && (bool) get_transient( $this->get_connected_status_transient_key() );

			if ( ! $this->is_connected && $this->has_api_key() ) {

				$this->is_connected = $this->get_api_instance()->is_api_key_valid();
			}
		}

		return $this->is_connected;
	}


	/**
	 * Returns the connected status transient key.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_connected_status_transient_key() {

		return 'wc_memberships_mailchimp_sync_connected';
	}


	/**
	 * Checks whether debug mode is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_debug_mode_enabled() {

		if ( null === $this->debug_mode ) {

			$this->debug_mode = 'yes' === get_option( 'wc_memberships_mailchimp_sync_enable_debug_mode', 'no' );
		}

		return $this->debug_mode;
	}


	/**
	 * Returns the current opt in mode (whether to subscribe members automatically or not).
	 *
	 * @since 1.0.1
	 *
	 * @return string
	 */
	private function get_members_opt_in_mode() {

		$default = 'automatic';

		if ( null === $this->members_opt_in_mode ) {

			$this->members_opt_in_mode = get_option( 'wc_memberships_mailchimp_sync_members_opt_in', $default );
		}

		return in_array( $this->members_opt_in_mode, array( 'automatic', 'manual' ), true ) ? $this->members_opt_in_mode : $default;
	}


	/**
	 * Checks which is the current members opt in mode.
	 *
	 * @since 1.0.1
	 *
	 * @param string $which either 'automatic' or 'manual'
	 * @return bool
	 */
	public function is_members_opt_in_mode( $which ) {

		return $which === $this->get_members_opt_in_mode();
	}


	/**
	 * Checks whether a user can be synced.
	 *
	 * @since 1.0.1
	 *
	 * @param \WP_User|int $user user object or ID
	 * @return bool
	 */
	public function can_sync_user( $user ) {

		$can_sync = true;
		$user_id  = $user instanceof \WP_User ? $user->ID : $user;

		if ( is_numeric( $user_id ) && $user_id > 0 ) {

			$key   = '_wc_memberships_mailchimp_sync_opt_in';
			$value = get_user_meta( (int) $user_id, $key, true );

			if ( $this->is_members_opt_in_mode( 'manual' ) ) {
				$can_sync = 'yes' === $value;
			} elseif ( ! in_array( $value, array( 'yes', 'no' ), true ) ) {
				$can_sync = (bool) update_user_meta( $user_id, $key, 'yes' );
			}
		}

		return $can_sync;
	}


	/**
	 * Renders a notice for the user to read the docs before adding add-ons.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function add_admin_notices() {

		// show any dependency notices
		parent::add_admin_notices();

		$screen = get_current_screen();

		// only render on plugins or settings screen
		if ( ( $screen && 'plugins' === $screen->id ) || $this->is_plugin_settings() ) {

			$this->get_admin_notice_handler()->add_admin_notice(
				/* translators: the %s placeholders are meant for pairs of opening <a> and closing </a> link tags */
				sprintf( __( 'Thanks for installing MailChimp for WooCommerce Memberships! To get started, take a minute to %1$sread the documentation%2$s.', 'woocommerce-memberships-mailchimp' ),
					'<a href="https://docs.woocommerce.com/document/mailchimp-for-woocommerce-memberships/" target="_blank">',
					'</a>'
				),
				'get-started-notice',
				array( 'always_show_on_settings' => false, 'notice_class' => 'updated' )
			);
		}
	}


	/**
	 * Returns the main MailChimp for WooCommerce Memberships instance.
	 *
	 * Ensures only one instance is loaded at one time.
	 *
	 * @since 1.0.0
	 *
	 * @return Plugin
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Checks if the active WooCommerce Memberships version is equal or greater than a specified version.
	 *
	 * @since 1.0.1
	 *
	 * @param string $version a semver string
	 * @return bool
	 */
	public function is_memberships_version_gte( $version ) {

		if ( function_exists( 'wc_memberships' ) ) {
			$active = wc_memberships()->get_version();
		} else {
			$active = get_option( 'wc_memberships_version', '0' );
		}

		return version_compare( $active, $version, '>=' );
	}


	/**
	 * Returns the admin message handler instance
	 *
	 * TODO: remove this when the method gets fixed in framework {IT 2017-06-21}
	 *
	 * @since 1.0.0
	 */
	public function get_message_handler() {

		require_once( $this->get_framework_path() . '/class-sv-wp-admin-message-handler.php' );

		return parent::get_message_handler();
	}


	/**
	 * Gets the plugin documentation URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_documentation_url() {

		return 'https://docs.woocommerce.com/document/mailchimp-for-woocommerce-memberships/';
	}


	/**
	 * Gets the plugin support URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/tickets/';
	}


	/**
	 * Gets the plugin sales page URL.
	 *
	 * @since 1.0.8
	 *
	 * @return string
	 */
	public function get_sales_page_url() {

		return 'https://woocommerce.com/products/mailchimp-woocommerce-memberships/';
	}


	/**
	 * Returns the plugin settings page URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string $_ unused
	 * @return string
	 */
	public function get_settings_url( $_ = '' ) {

		return admin_url( 'admin.php?page=wc-settings&tab=memberships&section=mailchimp-sync' );
	}


	/**
	 * Checks whether we are on the plugin settings page.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_plugin_settings() {

		return isset( $_GET['page'], $_GET['tab'], $_GET['section'] )
		       && 'wc-settings'    === $_GET['page']
		       && 'memberships'    === $_GET['tab']
		       && 'mailchimp-sync' === $_GET['section'];
	}


	/**
	 * Returns the plugin name, localized.
	 *
	 * @since 1.0.0
	 *
	 * @return string the plugin name
	 */
	public function get_plugin_name() {

		return __( 'MailChimp for WooCommerce Memberships', 'woocommerce-memberships-mailchimp' );
	}


	/**
	 * Returns the full path to the plugin entry script.
	 *
	 * @since 1.0.0
	 *
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {

		return dirname( __DIR__ ) . "/woocommerce-{$this->get_id_dasherized()}.php";
	}


	/**
	 * Clears transients used by the plugin to cache API results.
	 *
	 * @since 1.0.0
	 */
	public function clear_transients() {

		$this->is_connected = null;
		$this->debug_mode   = null;

		delete_transient( $this->get_connected_status_transient_key() );
		delete_transient( MailChimp_Lists::$interest_categories_transient );
		delete_transient( MailChimp_Lists::$interests_transient );
		delete_transient( MailChimp_Lists::$merge_fields_transient );
		delete_transient( MailChimp_Lists::$plans_merge_tags_transient );
	}


}
