<?php
/**
 * Class FUE_Addon_Twitter
 */
class FUE_Addon_Twitter {

	/**
	 * Plugin version
	 */
	const VERSION = '1.1.1';

	/**
	 * Plugin file path
	 */
	const PLUGIN_FILE = __FILE__;

	/**
	 * @var array Twitter settings
	 */
	public $settings;

	/**
	 * @var string OAUth callback URL
	 */
	private $callback;

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->settings = $this->get_settings();
		$this->callback = $this->get_callback_url();

		$this->register_hooks();
		$this->include_files();

		if ( is_admin() || isset( $_REQUEST['oauth_verifier'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->twitter_admin = new FUE_Addon_Twitter_Admin( $this );
		}

		$this->twitter_tweet    = new FUE_Addon_Twitter_Tweeter( $this );
		$this->twitter_frontend = new FUE_Addon_Twitter_Frontend( $this );
		$this->twitter_scheduler = new FUE_Addon_Twitter_Scheduler( $this );
	}

	/**
	 * Register hooks
	 */
	public function register_hooks() {
		add_filter( 'fue_addons', array($this, 'register_addon') );
		add_filter( 'fue_email_types', array($this, 'register_email_types'), 20 );
	}

	/**
	 * Load the settings data from the database
	 * @return array
	 */
	public function get_settings() {
		$settings = get_option( 'fue_twitter' );
		$default    = array(
			'checkout_fields'       => 0,
			'account_fields'        => 0,
			'consumer_key'          => '',
			'consumer_secret'       => '',
			'access_token'          => ''
		);
		$settings = wp_parse_args( $settings, $default );

		return apply_filters( 'fue_twitter_settings', $settings );
	}

	/**
	 * Get the callback URL for OAuth
	 *
	 * @return string
	 */
	public function get_callback_url() {
		$url = admin_url('admin.php?page=followup-emails-settings&tab=twitter&oauth=1');

		return apply_filters( 'fue_twitter_callback_url', $url );
	}

	/**
	 * Load Twitter libraries
	 */
	public function include_files() {
		require_once FUE_INC_DIR .'/addons/twitter/class-fue-addon-twitter-admin.php';
		require_once FUE_INC_DIR .'/addons/twitter/class-fue-addon-twitter-frontend.php';
		require_once FUE_INC_DIR .'/addons/twitter/class-fue-addon-twitter-scheduler.php';
		require_once FUE_INC_DIR .'/addons/twitter/class-fue-addon-twitter-tweeter.php';

		if ( !class_exists( 'TwitterOAuth' ) ) {
			require_once FUE_INC_DIR .'/addons/twitter/lib/twitter/twitteroauth/autoload.php';
		}

	}

	/**
	 * Register as an add-on for FUE
	 * @param array $addons
	 * @return array
	 */
	public function register_addon( $addons ) {
		$addons['twitter'] = array(
			'name'          => 'Twitter',
			'installed'     => true,
			'settings'      => true,
			'url'           => '#',
			'description'   => __('Collect @twitter usernames from your customers and create followups that tweet messages to your customers.', 'follow_up_emails')
		);

		return $addons;
	}

	/**
	 * Register the Twitter email type
	 *
	 * @hook fue_email_types
	 * @param array $types
	 * @return array $types
	 */
	public function register_email_types( $types ) {
		$triggers = array();

		// copy the triggers for the storewide email type
		foreach ( $types as $type ) {
			if ( in_array( $type->id, array( 'storewide', 'subscription', 'wootickets' ) ) ) {
				$triggers += $type->triggers;
			}
		}

		if ( empty( $triggers ) ) {
			// storewide email type not found. perhaps WC is not installed
			// twitter needs WC to be installed
			return $types;
		}

		$props = array(
			'priority'              => 10,
			'label'                 => __('Twitter Messages', 'follow_up_emails'),
			'singular_label'        => __('Twitter Message', 'follow_up_emails'),
			'triggers'              => $triggers,
			'durations'             => Follow_Up_Emails::$durations,
			'long_description'      => __('Collect @twitter usernames from your customers and create followups that tweet messages to your customers.', 'follow_up_emails'),
			'short_description'     => __('Collect @twitter usernames from your customers and create followups that tweet messages to your customers.', 'follow_up_emails'),
			'list_template'         => FUE_TEMPLATES_DIR .'/email-list/twitter-list.php'
		);
		$types[] = new FUE_Email_Type( 'twitter', $props );

		return $types;

	}

	/**
	 * Get the Twitter handle stored in the given order.
	 *
	 * @param int $order_id
	 * @return string
	 */
	public function get_twitter_handle_from_order( $order_id ) {
		$handle_meta    = get_post_meta( $order_id, '_twitter_handle', true );
		$handle         = '';

		if ( $handle_meta ) {
			$handle = '@'.$handle_meta;
		}

		return $handle;
	}

}

$GLOBALS['fue_addon_twitter'] = new FUE_Addon_Twitter();
