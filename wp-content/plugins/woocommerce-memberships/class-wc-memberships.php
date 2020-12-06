<?php
/**
 * WooCommerce Memberships
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;
use SkyVerge\WooCommerce\Memberships\Profile_Fields;

defined( 'ABSPATH' ) or exit;

/**
 * WooCommerce Memberships Main Plugin Class.
 *
 * @since 1.0.0
 *
 * @method \SkyVerge\WooCommerce\Memberships\Admin\Setup_Wizard get_setup_wizard_handler()
 */
class WC_Memberships extends Framework\SV_WC_Plugin  {


	/** plugin version number */
	const VERSION = '1.19.2';

	/** @var \WC_Memberships single instance of this plugin */
	protected static $instance;

	/** plugin id */
	const PLUGIN_ID = 'memberships';

	/** @var \WC_Memberships_Admin instance */
	protected $admin;

	/** @var \WC_Memberships_AJAX instance */
	protected $ajax;

	/** @var \WC_Memberships_Capabilities instance */
	protected $capabilities;

	/** @var \WC_Memberships_Emails instance */
	protected $emails;

	/** @var \WC_Memberships_Frontend instance */
	protected $frontend;

	/** @var WC_Memberships_Integrations instance */
	protected $integrations;

	/** @var \WC_Memberships_Member_Discounts instance */
	protected $member_discounts;

	/** @var \WC_Memberships_Membership_Plans instance */
	protected $plans;

	/** @var \WC_Memberships_Restrictions instance */
	protected $restrictions;

	/** @var \WC_Memberships_Shipping instance */
	private $shipping;

	/** @var \WC_Memberships_Rules instance */
	protected $rules;

	/** @var \WC_Memberships_User_Memberships instance */
	protected $user_memberships;

	/** @var \WC_Memberships_Utilities instance */
	private $utilities;

	/** @var \SkyVerge\WooCommerce\Memberships\REST_API instance */
	private $rest_api;

	/** @var \SkyVerge\WooCommerce\Memberships\API\Webhooks instance */
	private $webhooks;

	/** @var \SkyVerge\WooCommerce\Memberships\Blocks instance */
	private $blocks;


	/**
	 * Initializes the plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			[
				'text_domain'  => 'woocommerce-memberships',
				'dependencies' => [
					'php_extensions' => [
						'dom',
						'mbstring',
					],
				],
			]
		);

		add_action( 'init', array( $this, 'init_post_types' ) );

		// initializes the REST API handler and the Webhooks handler
		if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.6.0' ) ) {
			add_action( 'rest_api_init',           [ $this, 'init_rest_api' ], 5 );
		} else {
			add_action( 'before_woocommerce_init', [ $this, 'init_rest_api' ] );
		}

		add_action( 'before_woocommerce_init', [ $this, 'init_webhooks' ] );

		// add query vars for rewrite endpoints
		add_action( 'init',                       array( $this, 'add_rewrite_endpoints' ), 0 );
		add_filter( 'query_vars',                 array( $this, 'add_query_vars' ), 0 );
		add_filter( 'woocommerce_get_query_vars', array( $this, 'add_query_vars' ), 0 );

		/** set submitted profile field values on the newly created membership @see Profile_Fields::set_member_profile_fields_from_purchase() */
		add_action( 'wc_memberships_grant_membership_access_from_purchase', [ Profile_Fields::class, 'set_member_profile_fields_from_purchase' ], 10, 2 );
		/** clears the profile fields data from the session on checkout @see Profile_Fields::clear_profile_fields_session_data() */
		add_action( 'woocommerce_checkout_order_processed', [ Profile_Fields::class, 'clear_profile_fields_session_data' ], 999 );
		/** deletes attachments that have not been set to profile fields when clearing session data @see Profile_Fields::remove_uploaded_profile_field_file_from_session() */
		add_action( 'woocommerce_cleanup_sessions', [ Profile_Fields::class, 'remove_uploaded_profile_field_files_from_session' ], 1 );

		// make sure template files are searched for in our plugin
		add_filter( 'woocommerce_locate_template',      array( $this, 'locate_template' ), 20, 3 );
		add_filter( 'woocommerce_locate_core_template', array( $this, 'locate_template' ), 20, 3 );

		// GDPR handling: remove user memberships when erasing personal data
		add_filter( 'wp_privacy_personal_data_erasers',   array( $this, 'register_personal_data_eraser' ) );
		// GDPR handling: export user memberships personal data upon request
		add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_personal_data_exporter' ), 6 );
	}


	/**
	 * Loads the plugin main classes.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 */
	public function init_plugin() {

		$this->includes();

		$this->add_milestone_hooks();
	}


	/**
	 * Build and initialize the Setup Wizard handler.
	 *
	 * @since 1.11.0
	 */
	protected function init_setup_wizard_handler() {

		parent::init_setup_wizard_handler();

		require_once( $this->get_plugin_path() . '/includes/admin/class-wc-memberships-setup-wizard.php' );

		$this->setup_wizard_handler = new \SkyVerge\WooCommerce\Memberships\Admin\Setup_Wizard( $this );
	}


	/**
	 * Initializes the REST API handler.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 */
	public function init_rest_api() {

		$this->get_rest_api_instance();
	}


	/**
	 * Initializes the Webhooks handler.
	 *
	 * @internal
	 *
	 * @since 1.13.1
	 */
	public function init_webhooks() {

		$this->get_webhooks_instance();
	}


	/**
	 * Loads and initializes the plugin's lifecycle handler.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 */
	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/includes/class-wc-memberships-upgrade.php' );

		$this->lifecycle_handler = new \WC_Memberships_Upgrade( $this );
	}


	/**
	 * Initializes post types and endpoints.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 */
	public function init_post_types() {

		\WC_Memberships_Post_Types::initialize();
	}


	/**
	 * Includes required files.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function includes() {

		// load post types
		require_once( $this->get_plugin_path() . '/includes/class-wc-memberships-post-types.php' );

		// load user messages helper
		require_once( $this->get_plugin_path() . '/includes/class-wc-memberships-user-messages.php' );

		// load helper functions
		require_once( $this->get_plugin_path() . '/includes/functions/wc-memberships-functions.php' );

		// load data stores
		require_once( $this->get_plugin_path() . '/includes/Data_Stores/Profile_Field_Definition/Option.php' );
		require_once( $this->get_plugin_path() . '/includes/Data_Stores/Profile_Field/User_Meta.php' );

		// load profile field objects
		require_once( $this->get_plugin_path() . '/includes/Profile_Fields/Exceptions/Invalid_Field.php' );
		require_once( $this->get_plugin_path() . '/includes/Profile_Fields/Profile_Field_Definition.php' );
		require_once( $this->get_plugin_path() . '/includes/Profile_Fields/Profile_Field.php' );
		require_once( $this->get_plugin_path() . '/includes/Profile_Fields.php' );

		// init general classes
		$this->rules            = $this->load_class( '/includes/class-wc-memberships-rules.php',            'WC_Memberships_Rules' );
		$this->plans            = $this->load_class( '/includes/class-wc-memberships-membership-plans.php', 'WC_Memberships_Membership_Plans' );
		$this->emails           = $this->load_class( '/includes/class-wc-memberships-emails.php',           'WC_Memberships_Emails' );
		$this->user_memberships = $this->load_class( '/includes/class-wc-memberships-user-memberships.php', 'WC_Memberships_User_Memberships' );
		$this->capabilities     = $this->load_class( '/includes/class-wc-memberships-capabilities.php',     'WC_Memberships_Capabilities' );
		$this->member_discounts = $this->load_class( '/includes/class-wc-memberships-member-discounts.php', 'WC_Memberships_Member_Discounts' );
		$this->shipping         = $this->load_class( '/includes/class-wc-memberships-shipping.php',         'WC_Memberships_Shipping' );

		require_once( $this->get_plugin_path() . '/includes/Restrictions.php' );

		$this->restrictions = new SkyVerge\WooCommerce\Memberships\Restrictions();

		/** @deprecated remove legacy class aliases when the plugin has fully migrated to namespaces */
		class_alias( \SkyVerge\WooCommerce\Memberships\Restrictions::class, 'WC_Memberships_Restrictions', false );

		// frontend includes
		if ( ! is_admin() ) {
			$this->frontend_includes();
		}

		// admin includes
		if ( is_admin() && ! is_ajax() ) {
			$this->admin_includes();
		}

		// AJAX includes
		if ( is_ajax() ) {
			$this->ajax_includes();
		}

		// load utilities
		$this->utilities = $this->load_class( '/includes/class-wc-memberships-utilities.php', 'WC_Memberships_Utilities' );

		// load integrations
		$this->integrations = $this->load_class( '/includes/integrations/class-wc-memberships-integrations.php', 'WC_Memberships_Integrations' );

		// Gutenberg blocks
		$this->blocks = $this->load_class( '/includes/Blocks.php', '\\SkyVerge\\WooCommerce\\Memberships\\Blocks' );

		// WP CLI support
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			include_once $this->get_plugin_path() . '/includes/class-wc-memberships-cli.php';
		}

		// System Status Report static handler
		require_once( $this->get_plugin_path() . '/includes/class-wc-memberships-system-status-report.php' );
	}


	/**
	 * Includes required admin classes.
	 *
	 * @since 1.0.0
	 */
	private function admin_includes() {

		$this->admin = $this->load_class( '/includes/admin/class-wc-memberships-admin.php', 'WC_Memberships_Admin' );

		// message handler
		$this->admin->message_handler = $this->get_message_handler();
	}


	/**
	 * Includes required AJAX classes.
	 *
	 * @since 1.0.0
	 */
	private function ajax_includes() {

		$this->ajax = $this->load_class( '/includes/class-wc-memberships-ajax.php', 'WC_Memberships_AJAX' );
	}


	/**
	 * Includes required frontend classes.
	 *
	 * @since 1.0.0
	 */
	private function frontend_includes() {

		// init shortcodes
		require_once( $this->get_plugin_path() . '/includes/class-wc-memberships-shortcodes.php' );

		\WC_Memberships_Shortcodes::initialize();

		// load front end
		$this->frontend = $this->load_class( '/includes/frontend/class-wc-memberships-frontend.php', 'WC_Memberships_Frontend' );
	}


	/** Getter methods ******************************************************/


	/**
	 * Returns the Memberships instance singleton.
	 *
	 * Ensures only one instance is/can be loaded.
	 * @see wc_memberships()
	 *
	 * @since 1.0.0
	 *
	 * @return \WC_Memberships
	 */
	public static function instance() {

		if ( null === self::$instance ) {

			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Returns the Admin instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Memberships_Admin
	 */
	public function get_admin_instance() {

		return $this->admin;
	}


	/**
	 * Returns the AJAX instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Memberships_AJAX
	 */
	public function get_ajax_instance() {

		return $this->ajax;
	}


	/**
	 * Returns the Capabilities instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Memberships_Capabilities
	 */
	public function get_capabilities_instance() {

		return $this->capabilities;
	}


	/**
	 * Get the Restrictions instance.
	 *
	 * @since 1.9.0
	 *
	 * @return \SkyVerge\WooCommerce\Memberships\Restrictions
	 */
	public function get_restrictions_instance() {

		return $this->restrictions;
	}


	/**
	 * Returns the Shipping handler instance.
	 *
	 * @since 1.10.0
	 *
	 * @return \WC_Memberships_Shipping
	 */
	public function get_shipping_instance() {

		return $this->shipping;
	}


	/**
	 * Returns the Frontend instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Memberships_Frontend
	 */
	public function get_frontend_instance() {

		return $this->frontend;
	}


	/**
	 * Returns the Emails instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Memberships_Emails
	 */
	public function get_emails_instance() {

		return $this->emails;
	}


	/**
	 * Returns the Integrations instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Memberships_Integrations
	 */
	public function get_integrations_instance() {

		return $this->integrations;
	}


	/**
	 * Returns the Member Discounts instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Memberships_Member_Discounts
	 */
	public function get_member_discounts_instance() {

		return $this->member_discounts;
	}


	/**
	 * Returns the Membership Plans instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Memberships_Membership_Plans
	 */
	public function get_plans_instance() {

		return $this->plans;
	}


	/**
	 * Returns the Rules instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Memberships_Rules
	 */
	public function get_rules_instance() {

		return $this->rules;
	}


	/**
	 * Returns the User Memberships instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Memberships_User_Memberships
	 */
	public function get_user_memberships_instance() {

		return $this->user_memberships;
	}


	/**
	 * Returns the utilities handler instance.
	 *
	 * @since 1.10.0
	 *
	 * @return \WC_Memberships_Utilities
	 */
	public function get_utilities_instance() {

		return $this->utilities;
	}


	/**
	 * Returns the WP REST API handler instance.
	 *
	 * @since 1.11.0
	 *
	 * @return \SkyVerge\WooCommerce\Memberships\REST_API
	 */
	public function get_rest_api_instance() {

		if ( null === $this->rest_api ) {

			require_once( $this->get_plugin_path() . '/includes/api/class-wc-memberships-rest-api.php' );

			$this->rest_api = new \SkyVerge\WooCommerce\Memberships\REST_API( $this );
		}

		return $this->rest_api;
	}


	/**
	 * Gets the webhooks handler instance.
	 *
	 * @since 1.13.1
	 *
	 * @return \SkyVerge\WooCommerce\Memberships\API\Webhooks
	 */
	public function get_webhooks_instance() {

		if ( null === $this->webhooks ) {
			$this->webhooks = $this->load_class( '/includes/api/class-wc-memberships-webhooks.php', '\SkyVerge\WooCommerce\Memberships\API\Webhooks' );
		}

		return $this->webhooks;
	}


	/**
	 * Gets the Gutenberg Blocks instance.
	 *
	 * @since 1.15.0
	 *
	 * @return \SkyVerge\WooCommerce\Memberships\Blocks
	 */
	public function get_blocks_instance() {

		return $this->blocks;
	}


	/**
	 * Returns the plugin sales page URL.
	 *
	 * @since 1.11.0
	 *
	 * @return string
	 */
	public function get_sales_page_url() {

		return 'https://woocommerce.com/products/woocommerce-memberships/';
	}


	/**
	 * Returns the plugin documentation URL.
	 *
	 * @since 1.2.0
	 *
	 * @return string URL
	 */
	public function get_documentation_url() {

		return 'https://docs.woocommerce.com/document/woocommerce-memberships/';
	}


	/**
	 * Returns the plugin support URL.
	 *
	 * @since 1.2.0
	 *
	 * @return string URL
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/tickets/';
	}


	/**
	 * Returns the plugin name, localized.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_plugin_name() {

		return __( 'WooCommerce Memberships', 'woocommerce-memberships' );
	}


	/**
	 * Returns the plugin filename path.
	 *
	 * @since 1.0.0
	 *
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {

		return __FILE__;
	}


	/**
	 * Returns the plugin configuration URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_id optional plugin identifier
	 * @return string plugin settings URL
	 */
	public function get_settings_url( $plugin_id = null ) {

		return admin_url( 'admin.php?page=wc-settings&tab=memberships' );
	}


	/** Admin methods ******************************************************/


	/**
	 * Adds milestone hooks.
	 *
	 * @since 1.11.0
	 */
	protected function add_milestone_hooks() {

		// first membership plan created
		add_action( 'wp_insert_post', function( $post_id, $post, $is_update ) {

			if ( ! $is_update && 'wc_membership_plan' === get_post_type( $post ) ) {

				wc_memberships()->get_lifecycle_handler()->trigger_milestone(
					'membership-plan-created', lcfirst( __( "You've created your first membership plan!", 'woocommerce-memberships' ) )
				);
			}

		}, 10, 3 );

		// first user membership created
		add_action( 'wc_memberships_user_membership_saved', function() {

			wc_memberships()->get_lifecycle_handler()->trigger_milestone(
				'user-membership-created', lcfirst( __( "You've created your first user membership!", 'woocommerce-memberships' ) )
			);
		} );
	}


	/**
	 * Adds admin notices upon initialization.
	 *
	 * @internal
	 *
	 * @since 1.17.1
	 */
	public function add_admin_notices() {

		parent::add_admin_notices();

		// TODO remove this after 2020-04-15 {FN 2020-03-10}
		if ( time() <= strtotime( '2020-04-15 00:00:00' ) ) {

			$message = sprintf(
				/* translators: Placeholders: %1$s - opening <a> HTML link tag, %2$s - closing </a> HTML link tag */
				__( 'Heads up! Add your voice to the WooCommerce Memberships roadmap: please tell us how you use the plugin and how it can improve. %1$sLet us know in this short survey%2$s. Thanks in advance!', 'woocommerce-memberships' ),
				'<a href="https://shopdata.typeform.com/to/kImkto" target="_blank">',
				'</a>'
			);

			$this->get_admin_notice_handler()->add_admin_notice( $message, 'wc-memberships-product-survey-q1-2020', [
				'notice_class'            => 'notice-info',
				'always_show_on_settings' => false,
			] );
		}
	}


	/**
	 * Adds any delayed admin notices.
	 *
	 * @internal
	 *
	 * @since 1.16.2
	 */
	public function add_delayed_admin_notices() {

		parent::add_delayed_admin_notices();

		if ( 'yes' === get_option( 'wc_memberships_use_as_3_0_0' ) ) {

			$message = sprintf(
				__( '%1$sHeads up!%2$s We introduced a bug in version 1.16.0 of Memberships that caused issues with scheduled actions. We\'re so sorry for this issue! The bug should be resolved in the current version, but %3$splease contact our support team immediately%4$s if you notice any further issues.', 'woocommerce-memberships' ),
				'<strong>', '</strong>',
				'<a href="' . esc_url( 'https://woocommerce.com/my-account/create-a-ticket/' ) . '">', '</a>'
			);

			$this->get_admin_notice_handler()->add_admin_notice( $message, 'wc-memberships-action-scheduler-migration', [
				'notice_class'            => 'notice-info',
				'always_show_on_settings' => false,
			] );
		}
	}


	/** Helper methods ******************************************************/


	/**
	 * Determines whether a plugin is installed (not limited to active).
	 *
	 * Note: this might not be totally foolproof, in case the plugins have strange paths, but it only provides a minor utility in the context of Memberships.
	 * Consider making this a framework method if there's more broad need instead.
	 *
	 * @since 1.7.5-dev.1
	 *
	 * @param string $plugin plugin identifier (usually the main plugin file name)
	 * @return bool
	 */
	public function is_plugin_installed( $plugin ) {

		// bail early if plugin is listed as active already
		if ( $this->is_plugin_active( $plugin ) ) {
			return true;
		}

		// ensures get_plugins() function is available
		if ( ! function_exists( 'get_plugins' ) && is_readable( ABSPATH . 'wp-admin/includes/plugin.php' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$is_installed      = false;
		$installed_plugins = function_exists( 'get_plugins' ) ? get_plugins() : null;

		if ( is_array( $installed_plugins ) ) {

			foreach ( array_keys( $installed_plugins ) as $installed_plugin ) {

				// in case we passed a plugin name including path or if the plugin is a single file plugin
				if ( $plugin === $installed_plugin ) {
					$is_installed = true;
					break;
				}

				// plugins are normally listed with full path, but we are likely to specify just the file name
				$installed_plugin = explode( '/', $installed_plugin );

				if ( $plugin === end( $installed_plugin ) ) {
					$is_installed = true;
					break;
				}
			}
		}

		return $is_installed;
	}


	/**
	 * Checks if the current is the Memberships Settings page.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_plugin_settings() {

		return isset( $_GET['page'], $_GET['tab'] )
		       // we're browsing the WooCommerce settings
		       && 'wc-settings' === $_GET['page']
		       // in the plugin's main settings tab...
		       && ( 'memberships' === $_GET['tab']
		            // ...OR the plugin's email settings pages
		            || ( 'email' === $_GET['tab'] && isset( $_GET['section'] ) && Framework\SV_WC_Helper::str_starts_with( $_GET['section'], 'wc_memberships' ) ) );
	}


	/**
	 * Adds rewrite rules endpoints.
	 *
	 * @see \WC_Memberships_Members_Area
	 * @see \WC_Memberships_Upgrade we refresh permalinks on activation and plugin update
	 * @see \WC_Query::get_query_vars()
	 * @see \WC_Query::add_endpoints()
	 *
	 * @since 1.9.0
	 */
	public function add_rewrite_endpoints() {

		$members_endpoint        = wc_memberships_get_members_area_endpoint();
		$profile_fields_endpoint = wc_memberships_get_profile_fields_area_endpoint();

		if ( $members_endpoint || $profile_fields_endpoint ) {

			$ep_mask = EP_PAGES;

			if ( 'page' === get_option( 'show_on_front' ) ) {

				$page_on_front_id   = (int) get_option( 'page_on_front', 0 );
				$my_account_page_id = (int) wc_get_page_id( 'myaccount' );

				if ( $page_on_front_id > 0 && $my_account_page_id > 0 && $page_on_front_id === $my_account_page_id ) {
					$ep_mask = EP_ROOT | EP_PAGES;
				}
			}

			// add Members Area endpoint
			if ( $members_endpoint ) {
				add_rewrite_endpoint( $members_endpoint, $ep_mask );
			}

			// add Profile Fields Area endpoint
			if ( $profile_fields_endpoint ) {
				add_rewrite_endpoint( $profile_fields_endpoint, $ep_mask );
			}
		}
	}


	/**
	 * Handles query vars for endpoints.
	 *
	 * @see \WC_Memberships_Members_Area
	 * @see \WC_Query::get_query_vars()
	 * @see \WC_Query::add_endpoints()
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param array $query_vars associative array
	 * @return array
	 */
	public function add_query_vars( $query_vars ) {

		// this may happen if some third party code triggers 'woocommerce_get_query_vars' before init, when these functions are initialized
		if ( ! function_exists( 'wc_memberships_get_members_area_query_var' ) || ! function_exists( 'wc_memberships_get_members_area_endpoint' ) ) {
			return $query_vars;
		}

		// add Members Area query var
		$query_var = wc_memberships_get_members_area_query_var();

		if ( ! isset( $query_vars[ $query_var ] ) ) {
			$query_vars[ $query_var ] = wc_memberships_get_members_area_endpoint();
		}

		// add Profile Fields Area query var
		$query_var = wc_memberships_get_profile_fields_area_query_var();

		if ( ! isset( $query_vars[ $query_var ] ) ) {
			$query_vars[ $query_var ] = wc_memberships_get_profile_fields_area_endpoint();
		}

		return $query_vars;
	}


	/**
	 * Locates the WooCommerce template files from our templates directory.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param string $template already found template
	 * @param string $template_name searchable template name
	 * @param string $template_path template path
	 * @return string search result for the template
	 */
	public function locate_template( $template, $template_name, $template_path ) {

		// only keep looking if no custom theme template was found
		// or if a default WooCommerce template was found
		if ( ! $template || Framework\SV_WC_Helper::str_starts_with( $template, WC()->plugin_path() ) ) {

			// set the path to our templates directory
			$plugin_path = $this->get_plugin_path() . '/templates/';

			// if a template is found, make it so
			if ( is_readable( $plugin_path . $template_name ) ) {
				$template = $plugin_path . $template_name;
			}
		}

		return $template;
	}


	/** Privacy methods ******************************************************/


	/**
	 * Registers a GDPR compliant personal data eraser in WordPress for handling User Memberships.
	 *
	 * @internal
	 *
	 * @since 1.10.3
	 *
	 * @param array $erasers list of WordPress personal data erasers
	 * @return array
	 */
	public function register_personal_data_eraser( array $erasers ) {

		$erasers['woocommerce-memberships-user-memberships'] = array(
			'eraser_friendly_name' => __( 'User Memberships', 'woocommerce-memberships' ),
			'callback'               => array( 'WC_Memberships', 'erase_personal_data' ),
		);

		return $erasers;
	}


	/**
	 * Erases personal data from a user membership when an erasure request is issued in WordPress.
	 *
	 * @internal
	 *
	 * @since 1.10.3
	 *
	 * @param string $email_address address of the user that issued the erasure request
	 * @return array associative array with erasure response
	 */
	public static function erase_personal_data( $email_address ) {

		$response = array(
			'items_removed'  => false,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);

		if ( is_string( $email_address ) ) {

			$user             = get_user_by( 'email', trim( $email_address ) );
			$user_memberships = $user ? wc_memberships_get_user_memberships( $user ) : null;

			if ( ! empty( $user_memberships ) ) {

				$erase = 'yes' === get_option( 'wc_memberships_privacy_erasure_request_delete_user_memberships', 'no' );

				foreach ( $user_memberships as $user_membership ) {

					/**
					 * Filters whether to erase a user membership upon a personal data erasure request.
					 *
					 * @since 1.10.3
					 *
					 * @param bool $erase whether to erase a membership (default value set by admin setting)
					 * @param \WC_Memberships_User_Membership $user_membership the membership to erase or keep
					 */
					$erase = apply_filters( 'wc_memberships_privacy_erase_user_membership', $erase, $user_membership );

					$plan_name = $user_membership->get_plan()->get_name();

					if ( $erase && (bool) wp_delete_post( $user_membership->get_id() ) ) {
						$response['messages'][]     = sprintf( esc_html__( 'Removed User Membership for "%s".', 'woocommerce-memberships' ), $plan_name );
						$response['items_removed']  = true;
					} else {
						$response['messages'][]     = sprintf( esc_html__( 'User Membership for "%s" has been retained.', 'woocommerce-memberships' ), $plan_name );
						$response['items_retained'] = true;
					}
				}
			}
		}

		return $response;
	}


	/**
	 * Registers a GDPR compliant personal data exporter in WordPress for handling User Memberships.
	 *
	 * @internal
	 *
	 * @since 1.10.3
	 *
	 * @param array $exporters list of WordPress personal data exporters
	 * @return array
	 */
	public function register_personal_data_exporter( array $exporters ) {

		$exporters['woocommerce-memberships-user-memberships'] = array(
			'exporter_friendly_name' => __( 'User Memberships', 'woocommerce-memberships' ),
			'callback'               => array( 'WC_Memberships', 'export_personal_data' ),
		);

		return $exporters;
	}


	/**
	 * Exports personal data for a user that has issued a request to access to their stored personal information in WordPress.
	 *
	 * @internal
	 *
	 * @since 1.10.3
	 *
	 * @param string $email_address address of the user that issued the export request
	 * @return array exported data in key-value pairs
	 */
	public static function export_personal_data( $email_address ) {

		$user     = get_user_by( 'email', $email_address );
		$exported = array(
			'data' => array(),
			'done' => true,
		);

		if ( $user instanceof \WP_User ) {

			$user_memberships = wc_memberships_get_user_memberships( $user );

			if ( ! empty( $user_memberships ) ) {

				foreach ( $user_memberships as $user_membership ) {

					$exported['data'][] = array(
						'group_id'    => 'woocommerce_memberships_user_memberships',
						'group_label' => __( 'Memberships', 'woocommerce-memberships' ),
						'item_id'     => "user-membership-id-{$user_membership->get_id()}",
						'data'        => self::get_user_membership_personal_data( $user_membership ),
					);
				}
			}
		}

		return $exported;
	}


	/**
	 * Gathers user membership data for personal data export.
	 *
	 * @since 1.10.3
	 *
	 * @param \WC_Memberships_User_Membership $user_membership
	 * @return array
	 */
	private static function get_user_membership_personal_data( WC_Memberships_User_Membership $user_membership ) {

		$personal_data = array(
			'membership-plan' => array(
				'name'  => __( 'Plan', 'woocommerce-memberships' ),
				'value' => $user_membership->get_plan()->get_name(),
			),
			'start-date'      => array(
				'name'  => __( 'Start Date (UTC)', 'woocommerce-memberships' ),
				'value' => $user_membership->get_start_date( 'mysql' ),
			)
		);

		if ( $end_date = $user_membership->get_end_date( 'mysql' ) ) {
			$personal_data['end-date'] = array(
				'name'  => __( 'End Date (UTC)', 'woocommerce-memberships' ),
				'value' => $end_date,
			);
		}

		if ( $order_id = $user_membership->get_order_id() ) {
			$personal_data['order'] = array(
				'name'  => __( 'Related Order', 'woocommerce-memberships' ),
				'value' => $order_id,
			);
		}

		if ( $product = $user_membership->get_product( true ) ) {
			$personal_data['product'] = array(
				'name'  => __( 'Related Product', 'woocommerce-memberships' ),
				'value' => $product->get_name(),
			);
		}

		/**
		 * Filters a User Membership's personal data to export.
		 *
		 * @since 1.10.3
		 *
		 * @param array $personal_data associative array
		 * @param \WC_Memberships_User_Membership $user_membership user membership being exported
		 */
		return (array) apply_filters( 'wc_memberships_privacy_export_user_membership_personal_data', $personal_data, $user_membership );
	}


}


/**
 * Returns the One True Instance of Memberships.
 *
 * @since 1.0.0
 *
 * @return \WC_Memberships
 */
function wc_memberships() {

	return \WC_Memberships::instance();
}
