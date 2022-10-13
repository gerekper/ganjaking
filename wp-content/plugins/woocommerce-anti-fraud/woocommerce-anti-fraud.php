<?php

/**
 * Plugin Name: WooCommerce Anti Fraud
 * Plugin URI: https://woocommerce.com/products/woocommerce-anti-fraud/
 * Description: Score each of your transactions, checking for possible fraud, using a set of advanced scoring rules.
 * Version: 4.7
 * Author: OPMC Australia Pty Ltd
 * Author URI: https://opmc.biz/
 * Text Domain: woocommerce-anti-fraud
 * Domain Path: /languages
 * License: GPL v3
 * WC tested up to: 6.9
 * WC requires at least: 2.6
 * Woo: 500217:955da0ce83ea5a44fc268eb185e46c41
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright (c) 2017 OPMC Australia Pty Ltd.
 */

/**
 * Required functions
 */

function add_the_theme_page() {
	add_menu_page(
		__( 'Anti Fraud', 'woocommerce-anti-fraud' ), 
		__( 'Anti Fraud', 'woocommerce-anti-fraud' ), 
		'manage_options', 
		'theme-options', 
		'page_content', 
		'dashicons-book-alt'
	);
}
add_action('admin_menu', 'add_the_theme_page');
function page_content() {
	require_once( plugin_dir_path( __FILE__ ) . '/templates/dashboard.php' );
}

if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . '/woo-includes/woo-functions.php' );
}
	/**
	 * Plugin updates
	 */
	woothemes_queue_update( plugin_basename( __FILE__ ), '955da0ce83ea5a44fc268eb185e46c41', '500217' );

function af_load_langauge() {


	
	$path = dirname( plugin_basename(__FILE__)) . '/languages';
	$result = load_plugin_textdomain( dirname( plugin_basename(__FILE__)), false, $path );
	// var_dump($result);die;
	// if (!$result) {
	//     $locale = apply_filters('plugin_locale', get_locale(), dirname( plugin_basename(__FILE__)));
	//     die("Could not find $path/" . dirname( plugin_basename(__FILE__)) . "-$locale.mo.");
	// }


}
add_action('init', 'af_load_langauge');


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * This function runs when WordPress completes its upgrade process
 * It iterates through each plugin updated to see if ours is included
 *
 * @param $upgrader_object Array
 * @param $options Array
 */
function wp_opmc_upgrade_completed( $upgrader_object, $options ) {
	// The path to our plugin's main file
	$our_plugin = plugin_basename( __FILE__ );
	// If an update has taken place and the updated type is plugins and the plugins element exists
	if ('update' == $options['action'] && 'plugin' == $options['type'] && isset( $options['plugins'] ) ) {
		// Iterate through the plugins being updated and check if ours is there
		foreach ( $options['plugins'] as $plugin ) {
			if ( $plugin == $our_plugin ) {
				// Set a transient to record that our plugin has just been updated
				set_transient( 'wp_opmc_updated', 1 );
				update_option('wc_af_fraud_update_state', 'yes');
			}
		}
	}
}
add_action( 'upgrader_process_complete', 'wp_opmc_upgrade_completed', 10, 2 );

/**
 * Plugin page links
 */
function wc_antifraud_plugin_links( $links ) {

	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=wc_af' ) . '">' . __( 'Settings', 'woocommerce-anti-fraud' ) . '</a>',
		'<a href="https://docs.woocommerce.com/document/woocommerce-anti-fraud/">' . __( 'Docs', 'woocommerce-anti-fraud' ) . '</a>',
	);

	return array_merge( $plugin_links, $links );
}

	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wc_antifraud_plugin_links' );


	define( 'WOOCOMMERCE_ANTI_FRAUD_VERSION', '4.4.0' );
	define( 'WOOCOMMERCE_ANTI_FRAUD_PLUGIN_URL', plugin_dir_url(__FILE__) );
	define( 'WOOCOMMERCE_ANTI_FRAUD_PLUGIN_DIR', plugin_dir_path(__FILE__) );

class WooCommerce_Anti_Fraud {

	/**
	 * Get the plugin file
	 *
	 * @static
	 * @since  1.0.0
	 *
	 * @return String
	 */
	public static function get_plugin_file() {
		return __FILE__;
	}

	/**
	 * A static method that will setup the autoloader
	 *
	 * @static
	 * @since  1.0.0
	 */
	private static function setup_autoloader() {
		require_once( plugin_dir_path( self::get_plugin_file() ) . '/includes/class-wc-af-privacy.php' );
		require_once( plugin_dir_path( self::get_plugin_file() ) . '/includes/class-wc-af-autoloader.php' );

		// Core loader
		$core_autoloader = new WC_AF_Autoloader( plugin_dir_path( self::get_plugin_file() ) . 'anti-fraud-core/' );
		spl_autoload_register( array( $core_autoloader, 'load' ) );

		// Rule loader

		$rule_autoloader = new WC_AF_Autoloader( plugin_dir_path( self::get_plugin_file() ) . 'rules/' );
		spl_autoload_register( array( $rule_autoloader, 'load' ) );
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		// Check if WC is activated
		if ( $this->is_wc_active() ) {
			$this->init();
		}
		register_activation_hook( __FILE__, array($this,'save_default_settings' ) );

		register_activation_hook( __FILE__, array($this,'deactivate_events_on_active_plugin' ) );

		register_deactivation_hook( __FILE__, array($this,'deactivate_events' ) );
		add_action('plugins_loaded', array($this, 'plugin_load_td') );
		add_action( 'admin_init', array( $this, 'admin_scripts' ) );
		add_action('admin_enqueue_scripts', array( $this, 'switch_onoff') );

		add_action( 'wp_ajax_my_action', array($this, 'my_action' ));
		add_action( 'wp_ajax_nopriv_my_action', array($this, 'my_action' ) );
		add_action('init', array( $this,'paypal_verification' ) );
		add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'kia_display_order_data_in_admin' ) );

		// Ajax For whitlist email check
		add_action( 'wp_ajax_check_blacklist_whitelist', array($this,'check_blacklist_whitelist' ) );
		add_action( 'wp_ajax_nopriv_check_blacklist_whitelist', array($this,'check_blacklist_whitelist' ) );

		// For MaxMind Device Tracking Script
		add_action('admin_head', array( $this, 'get_device_tracking_script'), 100, 100);
		add_action('wp_head', array( $this, 'get_device_tracking_script'), 100, 100);

		add_action( 'wp_ajax_whitelist_email', array($this,'whitelist_email' ) );
		add_action( 'wp_ajax_nopriv_whitelist_email', array($this,'whitelist_email' ) );

		//add_action( 'woocommerce_new_order',  array( $this,'check_for_card_error'));
		//add_action( 'woocommerce_update_order',  array( $this,'check_for_card_error'));
		
		add_action('wp_enqueue_scripts', array($this, 'wc_af_captcha_script'), 9999);
		add_action('login_enqueue_scripts', array($this, 'wc_af_captcha_script'), 9999);
		add_action('wp_enqueue_scripts', array($this,'add_scripts_to_pages'), 9999);
		add_action( 'wp_ajax_my_action_geo_country', array($this, 'my_action_geo_country' ));
		add_action( 'wp_ajax_nopriv_my_action_geo_country', array($this, 'my_action_geo_country' ) );
	}

	public function my_action_geo_country() {

		if (!empty($_POST['latitude']) && !empty($_POST['longitude'])) {
			
			if (wp_verify_nonce( isset($_REQUEST['_wpnonce']), 'my-nonce' ) ) {
			return false;
			}
			
			$lat = sanitize_text_field($_POST['latitude']);
			$lng = sanitize_text_field($_POST['longitude']);
			$response = wp_remote_get('https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=' . $lat . '&longitude=' . $lng . '&localityLanguage=en');
			if ( is_wp_error( $response ) ) {
				echo 'error';
				die();
			}
			if (isset($response)) {

				$output = json_decode($response['body'], true);

				if (!empty($output['city'])) {
					$g_city = strtolower($output['city']);
				} else {
					$g_countryCode = strtolower($output['countryCode']);
				}

				$g_state = strtolower($output['principalSubdivision']);
				update_option('html_geo_loc_state', $g_state);
				update_option('html_geo_loc_city', $g_city);
				update_option('html_geo_loc_cntry', $g_countryCode);
				echo 'success';		
				die();
			}
		} else {
			delete_option('html_geo_loc_state');
			delete_option('html_geo_loc_city');
			delete_option('html_geo_loc_cntry');
		}
		die();
	}

	public function add_scripts_to_pages() {
		$maxmind_settings = get_option( 'wc_af_maxmind_type' ); // Get MaxMind enable/disable
		$wc_af_maxmind_insights_setting = get_option( 'wc_af_maxmind_insights' ); // Get MaxMind insights enable/disable
		$wc_af_maxmind_factors_setting = get_option( 'wc_af_maxmind_factors' ); // Get MaxMind factors enable/disable
		if ('yes' != $maxmind_settings && 'yes' != $wc_af_maxmind_insights_setting && 'yes' != $wc_af_maxmind_factors_setting) {
			wp_enqueue_script('ajax_operation_script', plugins_url('assets/js/geoloc.js', __FILE__ ), array(), '1.0');
			 wp_localize_script( 'ajax_operation_script', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php', 'relataive' )));  
			wp_enqueue_script( 'ajax_operation_script' );
		}
	}

	public function plugin_load_td() {
		
	}

	public function check_for_card_error( $order_id) {

		$order = wc_get_order( $order_id );
		$ip_address = $order->get_customer_ip_address();
		$orderemail = version_compare( WC_VERSION, '3.0', '<' ) ? $order->billing_email : $order->get_billing_email();
		$current_user_id = $order->get_user_id();
		$order_date = $order->get_date_created();
		$order_date = gmdate('Y-m-d', strtotime($order_date));
		$currentDate = gmdate('Y-m-d');
		$userDetails = $order->get_user();
		$userRole = $userDetails->roles[0];
		$blacklist_available = false;

		$args = array(
			'post_type' => 'shop_order',
			'post_status' => 'any',
			'posts_per_page' => 1,
			'meta_query' => array(
				array(
					'key' => '_customer_user',
					'value' => $current_user_id
				)
			),
			'orderby' => 'ID',
			'order' => 'DESC'
		);

		$loop = new WP_Query( $args );
		$orderids = [];
		while ( $loop->have_posts() ) {
$loop->the_post();
			$orderids[] = get_the_ID();
		}
		wp_reset_postdata();

		$_card_decline_times = 0;
		$cardDeclinedMsgs = array('authentication failed', 'declined');
		if ( !empty($orderids) ) {
			global $wpdb;
			$table_perfixed = $wpdb->prefix . 'comments';
			$orderids = implode(',', $orderids);
			$sql = "SELECT * FROM $table_perfixed WHERE  `comment_post_ID` IN ($orderids) AND  `comment_type` LIKE  'order_note'";
			$results = $wpdb->get_results($wpdb->prepare( '%s' , $sql));
			$ic = 1;
			foreach ( $results as $note ) {
				if ( ( strpos($note->comment_content, 'declined') !== false ) || ( strpos($note->comment_content, 'authentication failed') !== false )) { 
					Af_Logger::debug('Note ' . $ic . ' ' . $note->comment_content);
					$_card_decline_times = get_post_meta( $order_id, '_card_decline_times', true );
					if ( isset( $_card_decline_times ) && !empty( $_card_decline_times ) ) {
						++$_card_decline_times;
						update_post_meta( $order_id, '_card_decline_times', $_card_decline_times );
					} else {
						update_post_meta( $order_id, '_card_decline_times', 1 );
					}
					break;
				} $ic++;
			}
		}
		$_card_decline_times = get_post_meta( $order_id, '_card_decline_times', true );
		Af_Logger::debug('Card declined ' . $_card_decline_times . ' times');

		if ( ( $_card_decline_times >= 5 ) && ( $order_date == $currentDate ) ) {

			$is_enable_ip_blacklist = get_option('wc_settings_anti_fraudenable_automatic_ip_blacklist');
			// Blacklist ip if enabled
			if ('yes' == $is_enable_ip_blacklist) {
				Af_Logger::debug('IP Blacklist ' . $ip_address);
				$existing_blacklist_ip = get_option('wc_settings_anti_fraudblacklist_ipaddress', false);
				if ('' != $existing_blacklist_ip) {
					$auto_blacklist_ip = explode( ',', $existing_blacklist_ip );

					if (!in_array( $ip_address, $auto_blacklist_ip )) {
						$existing_blacklist_ip .= ',' . $ip_address;
						update_option('wc_settings_anti_fraudblacklist_ipaddress', $existing_blacklist_ip);
					}
				} else {
					update_option('wc_settings_anti_fraudblacklist_ipaddress', $ip_address);
				}
			}

			//Auto blacklist email with high risk
			$enable_auto_blacklist = get_option('wc_settings_anti_fraudenable_automatic_blacklist');

			if ( 'yes' == $enable_auto_blacklist ) {
				$existing_blacklist_emails = get_option('wc_settings_anti_fraudblacklist_emails', false);
				$auto_blacklist_emails = explode( ',', $existing_blacklist_emails );

				if (!in_array( $orderemail, $auto_blacklist_emails )) {
					$existing_blacklist_emails .= ',' . $orderemail;
					update_option('wc_settings_anti_fraudblacklist_emails', $existing_blacklist_emails);
				}
			}
		}
	}

	public function switch_onoff( $hookget) {

		if ( 'toplevel_page_theme-options' == get_current_screen()->id ) {
		  wp_enqueue_script('antifraud-chart-js', plugins_url('assets/js/chart.js', __FILE__ ), array(), WOOCOMMERCE_ANTI_FRAUD_VERSION);
		}

		if ( 'woocommerce_page_wc-settings' != $hookget ) {
			return;
		}

		if (!isset($_REQUEST['section']) || 'minfraud_settings' !== $_REQUEST['section']) {
			return;
		}

		wp_enqueue_style('on-off-switch', plugins_url('assets/css/on-off-switch.css', __FILE__), array(), WOOCOMMERCE_ANTI_FRAUD_VERSION);
		wp_enqueue_script('on-off-jqueryadd', plugins_url('assets/js/jquery-1.11.2.min.js', __FILE__), array(), WOOCOMMERCE_ANTI_FRAUD_VERSION);
		wp_enqueue_script('on-off-switch', plugins_url('assets/js/on-off-switch.js', __FILE__), array(), WOOCOMMERCE_ANTI_FRAUD_VERSION);
		wp_enqueue_script('on-off-switch-onload', plugins_url('assets/js/on-off-switch-onload.js', __FILE__), array(), WOOCOMMERCE_ANTI_FRAUD_VERSION);

	}

	public function deactivate_events_on_active_plugin( $hook) {

		$crons = _get_cron_array();
		if ( empty( $crons ) ) {
			return;
		}

		foreach ( $crons as $timestamp => $cron ) {

			if ( ! empty( $cron['my_hourly_event'] ) ) {
				unset( $crons[$timestamp]['my_hourly_event'] );
			}
		}
		_set_cron_array( $crons );
	}

	public function deactivate_events( $hook) {

		$crons = _get_cron_array();
		if ( empty( $crons ) ) {
			return;
		}

		foreach ( $crons as $timestamp => $cron ) {

			if ( ! empty( $cron['wc-af-check'] ) ) {
				unset( $crons[$timestamp]['wc-af-check'] );
			}
			if ( ! empty( $cron['wp_af_paypal_verification'] ) ) {
				unset( $crons[$timestamp]['wp_af_paypal_verification'] );
			}
			if ( ! empty( $cron['wp_af_my_hourly_event'] ) ) {
				unset( $crons[$timestamp]['wp_af_my_hourly_event'] );
			}
		}
		_set_cron_array( $crons );
	}

	/**
	* Check if Device tracking is active
	*
	* @since  1.0.0
	*
	* Call on header
	*/

	public function get_device_tracking_script() {

		$device_trackin_settings = get_option( 'wc_af_maxmind_device_tracking' );
		// Get Device Tracking enable/disable
		if ( 'yes' === $device_trackin_settings ) {
			$maxmind_user = get_option( 'wc_af_maxmind_user' );

			if ( !empty( $maxmind_user ) ) {
				?>
				<script type="text/javascript">
					maxmind_user_id = "<?php echo esc_html_e( $maxmind_user ); ?>";
					(function() {
						var loadDeviceJs = function() {
						var element = document.createElement('script');
						element.src = 'https://device.maxmind.com/js/device.js';
						document.body.appendChild(element);
					};
					if (window.addEventListener) {
						window.addEventListener('load', loadDeviceJs, false);
					} else if (window.attachEvent) {
						window.attachEvent('onload', loadDeviceJs);
					}
				  })();
				</script>
				<?php
			}
		}
	}

	public function check_blacklist_whitelist() {
		$blocked_email = get_option('wc_settings_anti_fraudblacklist_emails');
		$array_mail = explode(',', $blocked_email);
		if (wp_verify_nonce('test', 'wc_none')) {
			return true;
		}
		$whitelistarray = isset($_POST['whitelist']) ? sanitize_text_field( $_POST['whitelist'] ) : '' ;
		$expwhitearray = explode("\n", $whitelistarray);
		$result = array_diff($array_mail, $expwhitearray);
		$finalblocklist = implode(',', $result);

		update_option('wc_settings_anti_fraudblacklist_emails', $finalblocklist);

		echo esc_html__( $finalblocklist );
		wp_die();

	}

	public function whitelist_email() {
		$email = isset($_REQUEST['email']) ? sanitize_text_field($_REQUEST['email']) : '';
		$email_blacklist = get_option('wc_settings_anti_fraudblacklist_emails');
		if ( '' != $email_blacklist ) {
			$blacklist = explode( ',', $email_blacklist );
			if ( is_array( $blacklist ) && count( $blacklist ) > 0 && in_array( $email, $blacklist ) ) {
				foreach ( $blacklist as $key=>$val ) {
					if ( $val == $email ) {
						unset($blacklist[$key]);
					}
				}
				$blacklist = implode( ',', $blacklist );
				echo esc_html__( $blacklist );
				update_option( 'wc_settings_anti_fraudblacklist_emails', $blacklist );
			}
		}
		$email_whitelist = get_option('wc_settings_anti_fraud_whitelist');
		$email_whitelist .= isset($_REQUEST['email']) ? "\n" . sanitize_text_field($_REQUEST['email']) . " \n " : '';
		update_option( 'wc_settings_anti_fraud_whitelist', $email_whitelist );
		die();
	}

	// display the extra data in the order admin panel
	public function kia_display_order_data_in_admin( $order ) {
		$blocked_email = get_option('wc_settings_anti_fraudblacklist_emails');
		$array_mail = explode(',', $blocked_email);
		$orderemail = $order->get_billing_email();
		foreach ($array_mail as $single) {
			if ($orderemail == $single) {
				?>
				<p class="form-field form-field-wide">
					<?php echo '<h3 style="color:red;"><strong>' . esc_html__( 'This email id is blocked', 'woocommerce-anti-fraud' ) . '</strong></h3>'; ?>
				</p>
				<p class="form-field form-field-wide"><button type="button" class="button unblock-email" data-email="<?php echo esc_html__($orderemail); ?>"><?php echo esc_html__('Unblock', 'woocommerce-anti-fraud'); ?></button></p>
				<?php
			}
		}
	}


	/**
	 * Check if WooCommerce is active
	 *
	 * @since  1.0.0
	 *
	 * @return bool
	 */
	private function is_wc_active() {

		$is_active = WC_Dependencies::woocommerce_active_check();


		// Do the WC active check
		if ( false === $is_active ) {
			add_action( 'admin_notices', array( $this, 'notice_activate_wc' ) );
		}

		return $is_active;
	}

	/**
	 * Display the notice
	 *
	 * @since  1.0.0
	 *
	 */
	public function notice_activate_wc() {
		?>
		<div class="error">
			<p>
				<?php
					/* translators: 1. start of link, 2. end of link. */ 
					printf( esc_html__( 'Please install and activate %1$sWooCommerce%2$s in order for the WooCommerce Anti Fraud extension to work!', 'woocommerce-anti-fraud' ), '<a href="' . esc_url( admin_url( 'plugin-install.php?tab=search&s=WooCommerce&plugin-search-input=Search+Plugins' ) ) . '">', '</a>' ); 
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Init the plugin
	 *
	 * @since  1.0.0
	 *
	 */
	private function init() {
		require_once( dirname( __FILE__ ) . '/includes/class-wc-af-logger.php' );

		// Load plugin textdomain
		// load_plugin_textdomain( 'woocommerce-anti-fraud', false, plugin_dir_path( self::get_plugin_file() ) . 'languages/' );

		// Setup the autoloader
		self::setup_autoloader();

		// Setup the required WooCommerce hooks
		WC_AF_Hook_Manager::setup();

		// Add base rules
		$maxmind_settings = get_option( 'wc_af_maxmind_type' ); // Get MaxMind enable/disable
		$wc_af_maxmind_insights_setting = get_option( 'wc_af_maxmind_insights' ); // Get MaxMind insights enable/disable
		$wc_af_maxmind_factors_setting = get_option( 'wc_af_maxmind_factors' ); // Get MaxMind factors enable/disable
		$maxmind_user = get_option( 'wc_af_maxmind_user' );
		$maxmind_license_key = get_option( 'wc_af_maxmind_license_key' );
		// if ( $maxmind_settings == 'yes' ) {
		// 	WC_AF_Rules::get()->add_rule( new WC_AF_Rule_MinFraud() );
		// }
		// if ( $wc_af_maxmind_insights_setting == 'yes' ) {
		//     WC_AF_Rules::get()->add_rule( new WC_AF_Rule_MinFraud_Insights() );
		// }
		if ( 'yes' == $wc_af_maxmind_factors_setting ) {
			WC_AF_Rules::get()->add_rule( new WC_AF_Rule_MinFraud_Factors() );
			if (!empty($maxmind_user) && !empty($maxmind_license_key)) {
				WC_AF_Rules::get()->add_rule(new WC_AF_Rule_Ip_Location());
			}
		} elseif ( 'yes' == $wc_af_maxmind_insights_setting) {
			WC_AF_Rules::get()->add_rule(new WC_AF_Rule_MinFraud_Insights());
			if (!empty($maxmind_user) && !empty($maxmind_license_key)) {
				WC_AF_Rules::get()->add_rule(new WC_AF_Rule_Ip_Location());
			}
		} elseif ( 'yes' == $maxmind_settings ) {
			WC_AF_Rules::get()->add_rule(new WC_AF_Rule_MinFraud());
			if (!empty($maxmind_user) && !empty($maxmind_license_key)) {
				WC_AF_Rules::get()->add_rule(new WC_AF_Rule_Ip_Location());
			}
		}
		if ('yes' != $maxmind_settings && 'yes' != $wc_af_maxmind_insights_setting && 'yes' != $wc_af_maxmind_factors_setting) {

			WC_AF_Rules::get()->add_rule(new WC_AF_Rule_Geo_Location());
		}
		

		WC_AF_Rules::get()->add_rule( new WC_AF_Rule_Country() );
		WC_AF_Rules::get()->add_rule( new WC_AF_Rule_Billing_Matches_Shipping() );
		WC_AF_Rules::get()->add_rule( new WC_AF_Rule_Detect_Proxy() );
		WC_AF_Rules::get()->add_rule( new WC_AF_Rule_Temporary_Email() );
		WC_AF_Rules::get()->add_rule( new WC_AF_Rule_Free_Email() );
		WC_AF_Rules::get()->add_rule( new WC_AF_Rule_International_Order() );
		WC_AF_Rules::get()->add_rule( new WC_AF_Rule_High_Value() );
		WC_AF_Rules::get()->add_rule( new WC_AF_Rule_High_Amount() );
		// if ( !empty( $maxmind_user ) && !empty( $maxmind_license_key ) ) {
		// 	WC_AF_Rules::get()->add_rule( new WC_AF_Rule_Ip_Location() );
		// }
		WC_AF_Rules::get()->add_rule( new WC_AF_Rule_First_Order() );
		WC_AF_Rules::get()->add_rule( new WC_AF_Rule_First_Order_Processing() );
		WC_AF_Rules::get()->add_rule( new WC_AF_Rule_Ip_Multiple_Order_Details() );
		WC_AF_Rules::get()->add_rule( new WC_AF_Rule_Velocities() );
		WC_AF_Rules::get()->add_rule( new WC_AF_Rule_Billing_Phone_Matches_Billing_Country() );

		// Check if admin
		if ( is_admin() ) {
			require_once(dirname( __FILE__ ) . '/anti-fraud-core/class-wc-af-settings.php');
		}
		
		$wc_af_enable_recaptcha_checkout = get_option('wc_af_enable_recaptcha_checkout');
		
		$wc_af_recaptcha_site_key = get_option('wc_af_recaptcha_site_key');
		
		$wc_af_recaptcha_secret_key = get_option('wc_af_recaptcha_secret_key');
		
		if ( 'yes' == $wc_af_enable_recaptcha_checkout && !empty( $wc_af_recaptcha_site_key ) && !empty( $wc_af_recaptcha_secret_key ) ) {
			
			add_action('woocommerce_review_order_before_submit', array($this, 'wc_af_captcha_checkout_field'));
			add_action('woocommerce_after_checkout_validation', array($this, 'wc_af_validate_captcha'), 10, 3);
		}
	}

	//Update order on paypal verification
	public function paypal_verification() {
		if (isset($_REQUEST['order_id']) && isset($_REQUEST['paypal_verification'])) {
			$order_id = base64_decode( sanitize_text_field( $_REQUEST['order_id'] ) );
			update_post_meta($order_id, 'wc_af_paypal_verification', true);
			$order = new WC_Order($order_id);
			echo "<script type='text/javascript'>
			alert('Your Paypal Email verified Successfully')</script>";
			if ( 'completed' === $order->get_status() || 'processing' === $order->get_status() || 'cancelled' === $order->get_status() ) {
				return;
			} else {
				$order->add_order_note( __( 'PayPal Verification Done.', 'woocommerce-anti-fraud' ) );
				//this should be set by paypal plugin. We should not override this.
				// $status = $order->update_status('processing');
			}
		}
	}

	//TO Do Test
	public function my_action() {
		$help_class = new WC_AF_Score_Helper();
		if (wp_verify_nonce('test', 'wc_none')) {
			return true;
		}
		if (isset($_POST['order_id'])) {
			$help_class->do_check( sanitize_text_field( $_POST['order_id']) );
		}
		wp_die();
	}

	//TO DO
	public function admin_scripts() {
		wp_enqueue_style('opmc_af_admin_css', plugins_url( 'assets/css/app.css', __FILE__), array(), WOOCOMMERCE_ANTI_FRAUD_VERSION  );
		wp_enqueue_style('cal', plugins_url( 'assets/css/tags-input.css', __FILE__), array(), WOOCOMMERCE_ANTI_FRAUD_VERSION  );
		
		wp_enqueue_script('cal', plugins_url( 'assets/js/cal.js', __FILE__ ), array(), WOOCOMMERCE_ANTI_FRAUD_VERSION );
		wp_enqueue_script('tags_input', plugins_url( 'assets/js/tags-input.js', __FILE__), array(), WOOCOMMERCE_ANTI_FRAUD_VERSION );
		wp_register_script('knob', plugins_url( '/assets/js/jquery.knob.min.js', self::get_plugin_file() ), array( 'jquery'), WOOCOMMERCE_ANTI_FRAUD_VERSION );
		wp_register_script('edit', plugins_url( '/assets/js/edit-shop-order.js', __FILE__), array(), WOOCOMMERCE_ANTI_FRAUD_VERSION );
		wp_enqueue_script('opmc_af_admin_js', plugins_url( '/assets/js/app.js', __FILE__), array(), WOOCOMMERCE_ANTI_FRAUD_VERSION );
	}

	public function save_default_settings() {
		// For Minfraud
		update_option('wc_af_fraud_check_before_payment', 'no');
		update_option('wc_af_fraud_update_state', 'yes');

		update_option('wc_af_enable_whitelist_payment_method', 'no');
		update_option('wc_settings_anti_fraud_minfraud_order_weight', 30);
		update_option('wc_settings_anti_fraud_minfraud_risk_score', 30);

		update_option('wc_af_email_notification', 'no');
		update_option('wc_settings_anti_fraud_cancel_score', 90);
		update_option('wc_settings_anti_fraud_hold_score', 70);
		update_option('wc_settings_anti_fraud_email_score', 50);
		update_option('wc_settings_anti_fraud_email_score1', 51);
		update_option('wc_settings_anti_fraud_low_risk_threshold', 25);
		update_option('wc_settings_anti_fraud_higher_risk_threshold', 75);
		update_option('wc_af_first_order', 'yes');
		update_option('wc_settings_anti_fraud_first_order_weight', 5);
		update_option('wc_af_international_order', 'yes');
		update_option('wc_settings_anti_fraud_international_order_weight', 10);
		update_option('wc_af_ip_geolocation_order', 'yes');
		update_option('wc_af_billing_phone_number_order', 'no');
		update_option('wc_settings_anti_fraud_billing_phone_number_order_weight', 15);
		update_option('wc_settings_anti_fraud_ip_geolocation_order_weight', 50);
		update_option('wc_af_bca_order', 'yes');
		update_option('wc_settings_anti_fraud_bca_order_weight', 20);
		update_option('wc_af_proxy_order', 'yes');
		update_option('wc_settings_anti_fraud_proxy_order_weight', 50);
		update_option('wc_af_suspecius_email', 'yes');
		update_option('wc_settings_anti_fraud_suspecious_email_weight', 5);
		update_option('wc_settings_anti_fraud_suspecious_email_domains', $this->suspicious_domains());
		update_option('wc_af_unsafe_countries', 'yes');
		update_option('wc_settings_anti_fraud_unsafe_countries_weight', 25);
		update_option('wc_af_order_avg_amount_check', 'yes');
		update_option('wc_settings_anti_fraud_order_avg_amount_weight', 15);
		update_option('wc_settings_anti_fraud_avg_amount_multiplier', 2);
		update_option('wc_af_order_amount_check', 'yes');
		update_option('wc_settings_anti_fraud_order_amount_weight', 5);
		update_option('wc_settings_anti_fraud_amount_limit', 10000);
		update_option('wc_af_attempt_count_check', 'yes');
		update_option('wc_settings_anti_fraud_order_attempt_weight', 25);
		update_option('wc_settings_anti_fraud_attempt_time_span', 24);
		update_option('wc_settings_anti_fraud_max_order_attempt_time_span', 5);
		update_option('wc_af_ip_multiple_check', 'yes');
		update_option('wc_settings_anti_fraud_ip_multiple_weight', 25);
		update_option('wc_settings_anti_fraud_ip_multiple_time_span', 30);
		update_option('wc_settings_anti_fraudenable_automatic_email_blacklist', 'yes');
		update_option('wc_settings_anti_fraudenable_automatic_blacklist', 'yes');
		update_option('wc_af_paypal_verification', 'yes');
		update_option('wc_af_paypal_prevent_downloads', 'yes');
		update_option('wc_settings_anti_fraud_time_paypal_attempts', 2);
		update_option('wc_settings_anti_fraud_paypal_email_format', 'html');
		update_option('wc_settings_anti_fraud_paypal_email_subject', $this->paypal_email_subject());
		update_option('wc_settings_anti_fraud_email_body', $this->paypal_email_body());
	}

	public function suspicious_domains() {
		$email_domains = array('hotmail',
		'live',
		'gmail',
		'yahoo',
		'mail',
		'123vn',
		'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijk',
		'aaemail.com',
		'webmail.aol',
		'postmaster.info.aol',
		'personal',
		'atgratis',
		'aventuremail',
		'byke',
		'lycos',
		'computermail',
		'dodgeit',
		'thedoghousemail',
		'doramail',
		'e-mailanywhere',
		'eo.yifan',
		'earthlink',
		'emailaccount',
		'zzn',
		'everymail',
		'excite',
		'expatmail',
		'fastmail',
		'flashmail',
		'fuzzmail',
		'galacmail',
		'godmail',
		'gurlmail',
		'howlermonkey',
		'hushmail',
		'icqmail',
		'indiatimes',
		'juno',
		'katchup',
		'kukamail',
		'mail',
		'mail2web',
		'mail2world',
		'mailandnews',
		'mailinator',
		'mauimail',
		'meowmail',
		'merawalaemail',
		'muchomail',
		'MyPersonalEmail',
		'myrealbox',
		'nameplanet',
		'netaddress',
		'nz11',
		'orgoo',
		'phat.co',
		'probemail',
		'prontomail',
		'rediff',
		'returnreceipt',
		'synacor',
		'walkerware',
		'walla',
		'wongfaye',
		'xasamail',
		'zapak',
		'zappo');
		return implode(',', $email_domains);
	}

	public function paypal_email_body() {
		return 'Hi! We have received your order on ' . get_site_url() . ", but to complete it, we have to verify your PayPal email address. If you haven't made or authorized any purchase, please, contact PayPal support immediately, and email us at " . get_option('admin_email') . '.';
	}

	public function paypal_email_subject() {
			return get_bloginfo( 'name' ) . ' Confirm your PayPal email address';
	}
	
	public function wc_af_captcha_checkout_field() {
			wp_enqueue_script('jquery');
			$wc_af_recaptcha_site_key = get_option('wc_af_recaptcha_site_key');
		?>
			
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="reg_captcha"><?php echo esc_html__('Captcha', 'woocommerce-anti-fraud'); ?>&nbsp;<span class="required">*</span></label>
			<div id="wc-af-recaptcha-checkout" name="g-recaptcha" class="g-recaptcha" data-sitekey="<?php echo esc_attr($wc_af_recaptcha_site_key); ?>" data-theme="light" data-size="normal"></div>
			<!--<a href="javascript:wcAfCaptcha=grecaptcha.reset(wcAfCaptcha);">Refresh</a>-->
			</p>
			<script>
				 var wcAfCaptcha = null;    
				<?php $intval = uniqid('interval_'); ?>

				var <?php echo esc_html__($intval); ?> = setInterval(function() {
					
				if(document.readyState === 'complete') {

						clearInterval(<?php echo esc_html__($intval); ?>);
						var $n = jQuery.noConflict();
							   
							$n("#place_order").attr("title", "<?php echo esc_html__('Recaptcha is a required field.', 'woocommerce-anti-fraud'); ?>");
							
						   if (typeof (grecaptcha.render) !== 'undefined' && wcAfCaptcha === null) {

								wcAfCaptcha=grecaptcha.render('wc-af-recaptcha-checkout', {
										'sitekey': '<?php echo esc_attr($wc_af_recaptcha_site_key); ?>'
								});

							}       

							$n(document).on('updated_checkout', function () {

									if (typeof (grecaptcha.render) !== 'undefined' && wcAfCaptcha === null) {

											wcAfCaptcha=grecaptcha.render('wc-af-recaptcha-checkout', {
													'sitekey': '<?php echo esc_attr($wc_af_recaptcha_site_key); ?>'
											});

									}
							});
					
					}    
				 }, 100); 

			</script>
		<?php
	}
	
	
	public function wc_af_captcha_script() {

		$wc_af_enable_recaptcha_checkout = get_option('wc_af_enable_recaptcha_checkout');

		$wc_af_recaptcha_site_key = get_option('wc_af_recaptcha_site_key');

		$wc_af_recaptcha_secret_key = get_option('wc_af_recaptcha_secret_key');

		if ('yes' == $wc_af_enable_recaptcha_checkout && !empty($wc_af_recaptcha_site_key) && !empty($wc_af_recaptcha_secret_key) && is_checkout()) {

			wp_register_script('wc-af-re-captcha', 'https://www.google.com/recaptcha/api.js', array(), '1.0');
			wp_enqueue_script('wc-af-re-captcha');
		}
	}
	
	public function wc_af_validate_captcha( $fields, $validation_errors) {

		if ( isset($_POST['woocommerce-process-checkout-nonce']) && !empty($_POST['woocommerce-process-checkout-nonce']) ) {

			$nonce_value = '';
			if (isset($_REQUEST['woocommerce-process-checkout-nonce']) || isset($_REQUEST['_wpnonce'])) {

				if (isset($_REQUEST['woocommerce-process-checkout-nonce']) && !empty($_REQUEST['woocommerce-process-checkout-nonce'])) {
									
					$nonce_value=sanitize_text_field($_REQUEST['woocommerce-process-checkout-nonce']);
				} else if (isset($_REQUEST['_wpnonce']) && !empty($_REQUEST['_wpnonce'])) {
									
					$nonce_value=sanitize_text_field($_REQUEST['_wpnonce']);
				}
				
			}

			if (wp_verify_nonce($nonce_value, 'woocommerce-process_checkout')) {

				if ('yes'==get_transient($nonce_value)) {
					return $validation_errors;
				}

				if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
									
									   
					// Google reCAPTCHA API secret key 
					$response = sanitize_text_field($_POST['g-recaptcha-response']);
					
					$wc_af_recaptcha_secret_key = get_option('wc_af_recaptcha_secret_key');

					// Verify the reCAPTCHA response 
					$verifyResponse = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret=' . $wc_af_recaptcha_secret_key . '&response=' . $response);
						$current_user = wp_get_current_user();

					if (is_array($verifyResponse) && !is_wp_error($verifyResponse) && isset($verifyResponse['body'])) {

						// Decode json data 
						$responseData = json_decode($verifyResponse['body']);

						// If reCAPTCHA response is valid 
						if (!$responseData->success) {
	
							$validation_errors->add('g-recaptcha_error', __('Invalid recaptcha.', 'woocommerce-anti-fraud'));
														
						} else {
													
							if (0!=3) {
								
								set_transient($nonce_value, 'yes', ( 3*60 ));
							}
						}
												
					} else {
						
						$validation_errors->add('g-recaptcha_error', __('Could not get response from recaptcha server.', 'woocommerce-anti-fraud'));
					}
				} else {
					
					$validation_errors->add('g-recaptcha_error', __('Recaptcha is a required field.', 'woocommerce-anti-fraud'));
				}
			} else {
				$validation_errors->add('g-recaptcha_error', __('Could not verify request.', 'woocommerce-anti-fraud'));
			}
		}
				
		return $validation_errors;
	}
	
}
	add_action('profile_update', 'sync_woocommerce_email', 10, 2) ;

function sync_woocommerce_email( $user_id, $old_user_data ) {
	// wc_af_fraud_check_before_payment

	if ($current_user->user_email != $old_user_data->user_email) {
		wp_update_user( array ( 'ID' => $current_user->ID, 'billing_email' => $current_user->user_email ) ) ;
	}
}
	//custom code for block order if email or ip in blacklist.
	add_action( 'woocommerce_after_checkout_validation', 'misha_validate_fname_lname', 10, 2);
function misha_validate_fname_lname( $fields, $errors ) {
	$blocked_email = get_option('wc_settings_anti_fraudblacklist_emails');
	$blocked_ipaddress = get_option('wc_settings_anti_fraudblacklist_ipaddress');
	$array_mail = explode(',', $blocked_email);
	$userRole = '';
	if (wp_verify_nonce('test', 'wc_none')) {
		return true;
	}
	$user = get_user_by('email', isset($_POST['billing_email']) ? sanitize_text_field( $_POST['billing_email'] ) : '');
	if (isset($user->ID)) {
		$userRole = $user->roles[0];
	}
	// Af_Logger::debug('users '. print_r($user->roles[0], true));
	// $userRole = wp_get_current_user()->roles[0];
	if ('' != $blocked_email) {

		$email_whitelist = get_option('wc_settings_anti_fraud_whitelist');
		$is_whitelisted = '';
		$wc_af_whitelist_user_roles = get_option('wc_af_whitelist_user_roles');
		if ( empty($wc_af_whitelist_user_roles) ) {
			$wc_af_whitelist_user_roles = array();
		}

		$is_whitelisted_roles = false;

		if ('' != $email_whitelist) {
			$whitelist = explode( "\n", $email_whitelist );
			if ( is_array( $whitelist ) && count( $whitelist ) > 0 ) {
			// Trim items to be sure
				foreach ( $whitelist as $k => $v ) {
					$whitelist[$k] = trim( $v );
				}
				// Af_Logger::debug('email found : '. print_r($whitelist, true));
			}
			$is_whitelisted = array_intersect($whitelist, $array_mail);
			$is_enable_whitelist_user_roles = get_option('wc_af_enable_whitelist_user_roles');
			if ('yes' == $is_enable_whitelist_user_roles) {
				$is_whitelisted_roles = in_array($userRole, $wc_af_whitelist_user_roles);
			} else {
				$is_whitelisted_roles = false;
			}
		}
		
		if (empty($is_whitelisted) && false == $is_whitelisted_roles) {
			// Af_Logger::debug('blocked_email : '. print_r($array_mail,true));
			foreach ($array_mail as $single) {
				if ($_POST[ 'billing_email' ] == $single) {
					echo esc_html_e('This email id is blocked.', 'woocommerce-anti-fraud');
					$errors->add( 'validation', __('This email id is blocked.', 'woocommerce-anti-fraud') );
				}
			}
		}
	}
	if ('' != $blocked_ipaddress) {
		$userip = WC_Geolocation::get_ip_address();
		$array_ipaddress = explode(',', $blocked_ipaddress);
		foreach ($array_ipaddress as $singles) {
			if ($userip == $singles) {
				$errors->add( 'validation', __('This IP Address is blocked.', 'woocommerce-anti-fraud') );
			}
		}
	}
}

add_action( 'woocommerce_after_checkout_validation', 'max_order_same_ip', 10, 2 );
function max_order_same_ip( $fields, $errors) {
	$is_enabled  =  get_option('wc_af_attempt_count_check');
	$time_stamp  = get_option('wc_settings_anti_fraud_attempt_time_span');
	$max_orders = get_option('wc_settings_anti_fraud_max_order_attempt_time_span');

	if ('yes' == $is_enabled) {
		// Calculate the new datetime
		$dt = new DateTime('NOW', wp_timezone(  ));
		$enddate = $dt;
		$enddate = clone $dt;
		
		$dt->modify( '-' . $time_stamp . ' hours' );

		// Set the start and send datetime strings
		$start_datetime_string = $dt->format( 'Y-m-d H:i:s' );
		$end_datetime_string   = $enddate->format( 'Y-m-d H:i:s' );
		Af_Logger::debug('Start Date : ' . $start_datetime_string);
		Af_Logger::debug('End Date : ' . $end_datetime_string);

		$ip_address = WC_Geolocation::get_ip_address();
		Af_Logger::debug('ip_address : ' . $ip_address);

		if (wp_verify_nonce('test', 'wc_none')) {
			return true;
		}
		$user = get_user_by('email', isset($_POST['billing_email']) ? sanitize_text_field($_POST['billing_email'] ) : '' );
		// if(isset($user->ID)){
		// 	$meta['key'] = '_customer_user';
		// 	$meta['value'] = $user->ID;
		// } else {
		// 	$meta['key'] = '_billing_email';
		// 	$meta['value'] = $_POST[ 'billing_email' ];
		// }
		// Af_Logger::debug('User Meta : '. print_r($meta, true));

		// Get the Same IP Orders
		$orders_count_ip = wc_get_orders(
			array(
				'limit'               => -1,
				'meta_key'            => '_billing_email',
				'meta_value'          => isset($_POST[ 'billing_email' ]) ? sanitize_text_field($_POST['billing_email'] ) : '',
				'customer_ip_address' => $ip_address,
				'type'                => wc_get_order_types( 'order-count' ),
				'date_after'          => $start_datetime_string,
				'date_before'         => $end_datetime_string,
			)
		);
		$order_count_user = wc_get_orders(
			array(
				'limit'               => -1,
				'meta_key'            => '_customer_user',
				'meta_value'          => isset($user->ID) ? $user->ID : '',
				'customer_ip_address' => $ip_address,
				'type'                => wc_get_order_types( 'order-count' ),
				'date_after'          => $start_datetime_string,
				'date_before'         => $end_datetime_string,
			)
		);
		// Af_Logger::debug('Orders : '. print_r($orders_count, true));
		Af_Logger::debug('Order Count : ' . count($orders_count_ip));
		if (count($orders_count_ip) >= $max_orders || count($order_count_user) >= $max_orders) {
			$errors->add( 'validation',
			/* translators: %s: order time span */
			 sprintf( esc_html__('You have reached maximum number of allowed orders in %d hours. Please try again later.', 'woocommerce-anti-fraud'), $time_stamp )
			 );
		}
	}
}


add_action( 'woocommerce_checkout_order_processed', 'wh_pre_paymentcall', 10, 2);
function wh_pre_paymentcall( $order_id, $errors ) {

	if ( !is_numeric($order_id) ) {
		return;
	}

	$order = wc_get_order($order_id);

	$email_whitelist = get_option('wc_settings_anti_fraud_whitelist');

	$whitelist = explode("\n", $email_whitelist);

	$not_whitelisted_email = false;
	if (wp_verify_nonce('test', 'wc_none')) {
		return true;
	}

	$whitelist_array = isset($_POST['billing_email']) ? sanitize_text_field($_POST['billing_email'] ) : '';
	if (in_array($whitelist_array, $whitelist)) {
		$not_whitelisted_email = true;
		update_post_meta($order_id, 'wc_af_score', 100);
		update_post_meta($order_id, 'whitelist_action', 'user_email_whitelisted');
		$order->add_order_note(__('Order fraud checks skipped due to whitelisted email.', 'woocommerce-anti-fraud'));
	}


	$check_before_payment = get_option('wc_af_fraud_check_before_payment');
	// echo $check_before_payment;

	if ('yes' == $check_before_payment && !$not_whitelisted_email) {

		if ( null !== get_option('wc_af_pre_payment_message') ) {
			$pre_payment_block_message = get_option('wc_af_pre_payment_message');
		} else {
			$pre_payment_block_message = __( 'Website Administrator does not allow you to place this order. Please contact our support team. Sorry for any inconvenience.', 'woocommerce-anti-fraud' );
		}

		$high_risk = get_option('wc_settings_anti_fraud_higher_risk_threshold');
		$score_helper = new WC_AF_Score_Helper();
		$score_helper->schedule_fraud_check( $order_id, true );

		$score_points = get_post_meta( $order_id, 'wc_af_score', true );
		$circle_points = WC_AF_Score_Helper::invert_score( $score_points );

		if ($high_risk <= $circle_points) {

			$order->update_status( 'failed', 'Pre Payment Fraud Check: Calculated risk score is above High Risk Threshold.', true );

			$return = array('result' => 'failure', 'messages' => "<ul class='woocommerce-error' role='alert'><li>" . $pre_payment_block_message . '</li></ul>');

			wp_send_json($return);
			wp_die();
		}
	}
}

//echo get_option('wc_af_paypal_verification');die;
new WooCommerce_Anti_Fraud();
