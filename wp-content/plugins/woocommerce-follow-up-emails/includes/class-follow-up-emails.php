<?php

/**
 * Class Follow_Up_Emails
 */
class Follow_Up_Emails {

	private static $instance = null;

	/**
	 * @var float The current version of the database schema and data
	 */
	public static $db_version = '20180215';

	/**
	 * @var string The post type
	 */
	public static $post_type = 'follow_up_email';

	/**
	 * @var string The plugin's text domain
	 */
	public static $text_domain = 'follow_up_emails';

	/**
	 * @var array The different triggers available
	 */
	public static $triggers = array();

	/**
	 * @var array The registered email types
	 */
	public static $email_types = null;

	/**
	 * @var array The triggers available to each email type
	 */
	public static $email_type_triggers = array();

	/**
	 * @var array The available durations (e.g. Minutes, Hours, etc)
	 */
	public static $durations = array();

	/**
	 * @var bool Flag that is set to TRUE if WooCommerce is installed
	 */
	public static $is_woocommerce = false;

	/**
	 * @var bool Flag that is set to TRUE if WooThemes Sensei is installed
	 */
	public static $is_sensei = false;

	/**
	 * @var string The scheduling system to use. Only action-scheduler is currently supported
	 */
	public static $scheduling_system = 'action-scheduler';

	/**
	 * @var array Long description of the email types. Displayed in the Emails List page
	 */
	public static $email_type_long_descriptions = array();

	/**
	 * @var array Short description of the email types. Displayed in the New/Edit Email form
	 */
	public static $email_type_short_descriptions = array();

	/**
	 * @var FUE_Logger
	 */
	public $logger;

	/**
	 * @var FUE_API
	 */
	public $api = null;

	/**
	 * @var FUE_Sending_Scheduler
	 */
	public $scheduler = null;

	/**
	 * @var FUE_Sending_Mailer
	 */
	public $mailer = null;

	/**
	 * @var FUE_Sending_Email_Variables
	 */
	public $email_vars = null;

	public $wpdb = null;

	/**
	 * @var FUE_Query
	 */
	public $query = null;

	/**
	 * @var FUE_Addon_Woocommerce
	 */
	public $fue_wc = null;

	/**
	 * @var FUE_Newsletter
	 */
	public $newsletter = null;

	public $link_meta = array();

	/**
	 * Follow_Up_Emails Class constructor
	 *
	 * Initialize the necessary data and include the required classes
	 *
	 * @param wpdb $wpdb
	 */
	public function __construct( wpdb $wpdb ) {

		$this->wpdb = $wpdb;

		self::include_files();

		$this->register_autoloader();

		// Init WC API
		$this->api = new FUE_API();

		// default trigger durations
		self::$durations = apply_filters( 'fue_default_durations', array(
			'minutes' => array( __( 'minute', 'follow_up_emails' ), __( 'minutes', 'follow_up_emails' ) ),
			'hours'   => array( __( 'hour', 'follow_up_emails' ),   __( 'hours', 'follow_up_emails' ) ),
			'days'    => array( __( 'day', 'follow_up_emails' ),    __( 'days', 'follow_up_emails' ) ),
			'weeks'   => array( __( 'week', 'follow_up_emails' ),   __( 'weeks', 'follow_up_emails' ) ),
			'months'  => array( __( 'month', 'follow_up_emails' ),  __( 'months', 'follow_up_emails' ) ),
			'years'   => array( __( 'year', 'follow_up_emails' ),   __( 'years', 'follow_up_emails' ) ),
		) );

		$log_level  = get_option( 'fue_log_level', 0 );

		$this->logger       = new FUE_Logger( $log_level, fue_get_log_path() );
		$this->query        = new FUE_Query();
		$this->scheduler    = new FUE_Sending_Scheduler( $this );
		$this->email_vars   = new FUE_Sending_Email_Variables();
		$this->mailer       = new FUE_Sending_Mailer( $this, $this->email_vars );
		$this->newsletter   = new FUE_Newsletter();

		if ( self::is_sensei_installed() ) {
			self::$is_sensei = true;
			require_once FUE_INC_DIR . '/addons/class-fue-addon-sensei.php';
		}

		// Follow_Up_Emails::$scheduling_system will always be action-scheduler
		// since support for WP-Cron has been dropped
		self::$scheduling_system = 'action-scheduler';

		do_action( 'fue_init' );

		self::$instance = $this;
	}

	/**
	 * Get an instance of Follow_Up_Emails
	 *
	 * @return Follow_Up_Emails
	 */
	public static function instance() {
		return self::$instance;
	}

	public function register_autoloader() {
		// Auto-load classes on demand
		if ( function_exists( "__autoload" ) ) {
			spl_autoload_register( "__autoload" );
		}

		spl_autoload_register( array( $this, 'autoload' ) );
	}

	/**
	 * Auto-load classes
	 *
	 * @param string $class
	 */
	public function autoload( $class ) {
		$path  = null;
		$class = strtolower( $class );
		$file = 'class-' . str_replace( '_', '-', $class ) . '.php';

		if ( strpos( $class, 'fue_addon_' ) === 0 ) {
			$path = trailingslashit( FUE_INC_DIR . '/addons' );
		} elseif ( strpos( $class, 'fue_report_' ) === 0 ) {
			$path = trailingslashit( FUE_INC_DIR . '/reports' );
		} elseif ( strpos( $class, 'fue_sending_' ) === 0 ) {
			$path = trailingslashit( FUE_INC_DIR . '/sending' );
		} elseif ( strpos( $class, 'fue_api_' ) === 0 ) {
			$path = trailingslashit( FUE_INC_DIR . '/api' );
		} elseif ( strpos( $class, 'fue_' ) === 0 ) {
			$path = trailingslashit( FUE_INC_DIR );
		}

		if ( $path && is_readable( $path . $file ) ) {
			include_once( $path . $file );
			return;
		}
	}

	/**
	 * Include the required core files that are not autoloaded
	 */
	public static function include_files() {
		require_once FUE_INC_DIR . '/class-fue-compat.php';
		require_once FUE_INC_DIR . '/class-fue-query.php';
		require_once FUE_INC_DIR . '/class-fue-post-types.php';
		require_once FUE_INC_DIR . '/fue-functions.php';
		require_once FUE_INC_DIR . '/class-fue-install.php';
		require_once FUE_INC_DIR . '/class-fue-front-handler.php';
		require_once FUE_INC_DIR . '/class-fue-ajax.php';
		require_once FUE_INC_DIR . '/lib/action-scheduler/action-scheduler.php';
		require_once FUE_INC_DIR . '/plugin-hooks.php';

		require_once FUE_INC_DIR . '/shortcodes/class-fue-shortcode-subscribe.php';

		if ( is_admin() ) {
			require_once FUE_INC_DIR . '/class-fue-admin-controller.php';
			require_once FUE_INC_DIR . '/class-fue-admin-profile.php';
		}

		require_once FUE_INC_DIR . '/lib/fue-logger.php';
	}

	/**
	 * Checks if WooCommerce is installed and activated by looking at the 'active_plugins' array
	 * @return bool True if WooCommerce is installed
	 */
	public static function is_woocommerce_installed() {
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

		$slug = 'woocommerce/woocommerce.php';

		return is_plugin_active( $slug ) || is_plugin_active_for_network( $slug );
	}

	/**
	 * Checks if WooThemes Sensei is installed and activated
	 * @return bool True if Sensei is installed
	 */
	public static function is_sensei_installed() {
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

		$old_slug = 'woothemes-sensei/woothemes-sensei.php';
		$new_slug = 'sensei/woothemes-sensei.php';

		return is_plugin_active( $old_slug ) || is_plugin_active_for_network( $old_slug )
			|| is_plugin_active( $new_slug ) || is_plugin_active_for_network( $new_slug )
			|| class_exists( 'Sensei_Main' );
	}

	/**
	 * Setup the registered email types
	 */
	public static function get_email_types() {
		if ( ! is_null( self::$email_types ) ) {
			return self::$email_types;
		}

		$signup_props = apply_filters( 'fue_signup_email_properties', array(
			'label'             => __( 'Signup Emails', 'follow_up_emails' ),
			'singular_label'    => __( 'Signup Email', 'follow_up_emails' ),
			'triggers'          => array(
				'signup'      => __( 'after user signs up', 'follow_up_emails' ),
				'list_signup' => __( 'after user signs up to a list', 'follow_up_emails' ),
			),
			'supports'          => array(),
			'durations'         => self::$durations,
			'short_description' => __( 'Send a welcome email to customers who create an account on your store.', 'follow_up_emails' ),
			'long_description'  => __( 'Send a welcome email to customers who create an account on your store.', 'follow_up_emails' ),
		) );
		$signup = new FUE_Email_Type( 'signup', $signup_props );

		$manual_props = apply_filters( 'fue_manual_email_properties', array(
			'label'                 => __( 'Single Emails', 'follow_up_emails' ),
			'singular_label'        => __( 'Single Email', 'follow_up_emails' ),
			'list_template'         => FUE_TEMPLATES_DIR . '/email-list/manual-list.php',
			'triggers'              => array(),
			'supports'              => array(),
			'durations'             => array(),
			'short_description'     => __( 'Send a single, one-time email to all, some, or one customer.', 'follow_up_emails' ),
			'long_description'      => __( 'Send a single, one-time email to all, some, or one customer.', 'follow_up_emails' ),
		) );
		$manual = new FUE_Email_Type( 'manual', $manual_props );

		$email_types = apply_filters( 'fue_email_types', array(
			$signup,
			$manual,
		) );

		// Sort by priority.
		$sorted_email_types = array();
		foreach ( $email_types as $key => $type ) {
			$sorted_email_types[ $type->priority ][ $key ] = $type;
		}

		ksort( $sorted_email_types );
		$email_types = array();

		foreach ( $sorted_email_types as $priority => $types ) {
			foreach ( $types as $type ) {
				$email_types[ $type->id ] = $type;
			}
		}

		self::$email_types = $email_types;

		return self::$email_types;
	}

	/**
	 * Delete the created unsubscribe page.
	 */
	public static function delete_unsubscribe_page() {
		$page_id = fue_get_page_id( 'followup_unsubscribe' );

		if ( $page_id ) {
			wp_delete_post( $page_id, true );
		}

		delete_option( 'fue_followup_unsubscribe_page_id' );
	}

	/**
	 * Load the addons.
	 */
	public static function load_addons() {
		// Email Reporting, Tracking and Daily Summary.
		require_once FUE_INC_DIR . '/reports/class-fue-reports.php';

		if ( self::is_woocommerce_installed() ) {
			$fue = Follow_Up_Emails::instance();
			$fue->fue_wc = new FUE_Addon_Woocommerce( $fue, $fue->mailer, $fue->email_vars );

			// Twitter.
			require_once FUE_INC_DIR . '/addons/class-fue-addon-twitter.php';
		}

		if ( self::is_sensei_installed() ) {
			require_once FUE_INC_DIR . '/addons/class-fue-addon-sensei.php';
		}

		// Coming Soon Pro
		require_once FUE_INC_DIR . '/addons/class-fue-addon-coming-soon-pro.php';

		// WC Memberships
		require_once FUE_INC_DIR . '/addons/class-fue-addon-wc-memberships.php';

		do_action( 'fue_addons_loaded' );
	}

	/**
	 * Enqueue the CSS file for the frontend.
	 */
	public static function front_css() {
		wp_enqueue_style( 'follow-up-emails', FUE_TEMPLATES_URL . '/followups.css', array(), FUE_VERSION );
	}

	/**
	 * Display an message on the frontend. Checks if Sensei is installed first
	 * and it appends the message to $woothemes_sensei->frontend_messages. Otherwise, it falls
	 * back to using WooCommerce's messaging API
	 *
	 * @param string $message
	 */
	public static function show_message( $message ) {
		if ( self::is_sensei_installed() ) {
			FUE_Addon_Sensei::add_message( $message );
		} elseif ( self::is_woocommerce_installed() ) {
			FUE_Addon_Woocommerce::add_message( $message );
		} else {
			// @todo Implement a notification system in case WC and Sensei are not available
		}
	}

	/**
	 * Get the URL of the My Account page. If Sensei is installed, it's the Dashboard page.
	 * @return string
	 */
	public static function get_account_url() {
		$url = get_bloginfo( 'url' );

		if ( self::is_sensei_installed() ) {
			$url = get_permalink( get_option( 'woothemes-sensei_user_dashboard_page_id', -1 ) );
		} elseif ( self::is_woocommerce_installed() ) {
			$url = get_permalink( wc_get_page_id( 'myaccount' ) );
		}

		return apply_filters( 'fue_get_account_url', $url );
	}

	/**
	 * Get all the triggers for the given email type.
	 *
	 * @param string $email_type
	 * @return array
	 */
	public static function get_email_type_triggers( $email_type ) {

		$type       = self::get_email_type( $email_type );
		$triggers   = array();

		if ( $type ) {
			$triggers = apply_filters( 'fue_trigger_types', $type->triggers, $email_type );
		}

		return $triggers;
	}

	/**
	 * Retrieve a specific email type
	 * @param string $type The email type's unique ID string
	 * @return FUE_Email_Type|bool Returns the email type or false if it wasn't found
	 */
	public static function get_email_type( $type ) {
		$email_types = self::get_email_types();

		if ( isset( $email_types[ $type ] ) ) {
			return $email_types[ $type ];
		}

		// if $type was not found, return false
		return false;
	}

	/**
	 * Return the default durations
	 *
	 * @return array
	 */
	public static function get_durations() {
		return apply_filters( 'fue_durations', self::$durations );
	}

	/**
	 * Return the duration string in singular or plural form depending on the passed $value
	 *
	 * @param string    $duration
	 * @param int       $value
	 *
	 * @return string
	 */
	public static function get_duration( $duration, $value = 0 ) {
		$durations = self::get_durations();

		if ( isset( $durations[ $duration ] ) ) {
			$item = $durations[ $duration ];

			if ( count( $item ) == 2 ) {
				// @codingStandardsIgnoreStart
				return _n( $item[0], $item[1], $value, 'follow_up_emails' );
				// @codingStandardsIgnoreEnd
			} else {
				return $item[0];
			}
		}

		return $duration;
	}

	/**
	 * Returns a JSON-encoded version of the email types array.
	 *
	 * @return JSON
	 */
	public static function get_email_types_json() {
		return json_encode( self::get_email_types() );
	}

	/**
	 * Check is the installed version of WooCommerce is 2.1 or newer.
	 *
	 * @return bool
	 */
	public static function is_woocommerce_pre_2_1() {
		return ! function_exists( 'wc_add_notice' );
	}
}
