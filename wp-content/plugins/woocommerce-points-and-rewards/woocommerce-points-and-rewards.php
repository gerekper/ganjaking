<?php
/**
 * Plugin Name: WooCommerce Points and Rewards
 * Plugin URI: https://woocommerce.com/products/woocommerce-points-and-rewards/
 * Description: Reward customers for purchases and other actions with points which can be redeemed for discounts
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Version: 1.6.37
 * Text Domain: woocommerce-points-and-rewards
 * Domain Path: /languages/
 * Tested up to: 5.5
 * WC tested up to: 4.2
 * WC requires at least: 2.6
 *
 * Copyright: © 2020 WooCommerce
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Woo: 210259:1649b6cca5da8b923b01ca56b5cdd246
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce fallback notice.
 *
 * @since 1.13.0
 */
function woocommerce_points_and_rewards_missing_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Points and Rewards requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-points-and-rewards' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

function wc_points_rewards_expire_points_schedule() {
	$timestamp = wp_next_scheduled( 'wc_points_rewards_expire_points' );

	if ( false === $timestamp ) {
		wp_schedule_event( time(), 'daily', 'wc_points_rewards_expire_points_daily' );
	}
}

/**
 * Activation
 *
 * @since 1.6.3
 *
 */
function wc_points_rewards_activate() {
	wc_points_rewards_add_endpoints();
	flush_rewrite_rules();
}

/**
 * Register new endpoint to use inside My Account page.
 *
 * @since 1.6.3
 *
 * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
 */
function wc_points_rewards_add_endpoints() {
	add_rewrite_endpoint( WC_POINTS_REWARDS_ENDPOINT, EP_ROOT | EP_PAGES );
}

register_activation_hook( __FILE__, 'wc_points_rewards_expire_points_schedule' );
register_activation_hook( __FILE__, 'wc_points_rewards_activate' );

/**
 * # WooCommerce Points and Rewards Main Plugin Class
 *
 * ## Plugin Overview
 *
 * This plugin lets customers earn points for purchases and actions (such as writing a review) and then redeem those
 * points for a discount on a purchase. Points earned per monetary value spent is defined in the settings along with
 * the amount of points that provides a discount. Points earned can be overridden at both the product category, and
 * product level using either a fixed amount of points earned, or by setting a percentage which modifies the amount of points
 * that would typically be earned.
 *
 * When points are redeemed for a discount, a coupon code is dynamically created and the discount amount is set as the
 * value of the coupon. Immediately upon checkout, the points are deducted from the customer's account. Any points earned
 * for the purchases are not added to the customer's account *until* payment is received for the order. If an order where
 * points were redeemed for a discount is cancelled or refunded, the points are credited back to the customer's account.
 *
 * ## Admin Considerations
 *
 * General Settings are added to WooCommerce > Settings > Points & Rewards. A sub-menu page "Points & Rewards" is also
 * added under "WooCommerce" that displays the Manage Points list table, and the Points Log list table. Fields
 * are added to simple/variable products, and also to the create/edit/view product category view.
 *
 * ## Frontend Considerations
 *
 * Messages are displayed on the single product page (both for simple and variable products). Messages are also displayed
 * on the cart/checkout page indicating how many points the customer will earn for the purchase, and whether there is a discount
 * available to redeem points for. A "My Points" section is added to the "My Account" page that lists the current amount of points
 * the user has, as well as a listing of the 5 most recent point events
 *
 * ## Database
 *
 * ### Global Settings
 *
 * + `wc_points_rewards_default_points_earned` - the default points earned for a purchase, can be either fixed amount or percentage
 * + `wc_points_rewards_display_monetary_value` - whether to display the monetary value for points or not
 * + `wc_points_rewards_points_label` - the label for "Points" on the frontend
 * + `wc_points_rewards_single_product_message` - the call to action to display on the single product page
 * + `wc_points_rewards_earn_points_message` - the message to display on the cart/checkout page when points will be earned for the purchase
 * + `wc_points_rewards_redeem_points_message` - the message to display on the cart/checkout page when points are available for redemption
 * + `wc_points_rewards_thank_you_message` - the message to display on the thank you / order received page when points are earned
 *
 * ### Options table
 *
 * + `wc_points_rewards_version` - the current plugin version, set on install/upgrade
 *
 * ### Product meta
 *
 * + `_wc_points_earned` - the points earned when this product is purchased, this can be set at both the variable/variation level
 *
 *
 * ### User meta
 *
 * + `wc_points_balance` - the users total points balance.  This is the summation
 *   of all the user points records, and used solely for the purposes of sorting
 *   records in the manage user points table.
 *
 * ### Custom Table wc_points_rewards_user_points
 *
 * This table represents awarded user points and records are created each time
 * the balance of points for a customer increases due to them placing an order
 * or performing another point-generating event.
 *
 * + user_id: references the customer user record
 * + points: the total points earned for the associated event
 * + points_balance: the balance of points available for use (can be positive or negative)
 * + order_id: optional order identifier, if this event was associated with an order
 * + date: the date the points were awarded
 *
 * ### Custom Table wc_points_rewards_user_points_log
 *
 * This table tracks changes in the balance of points for users
 *
 * + user_id: references the customer user record
 * + points: the total change in points (can be positive or negative)
 * + type: optional slug identifying the type of event being logged; this is used for grouping events of the same type
 * + user_points_id: optional, references the wc_points_rewards_user_points table when the log entry refers to a points record
 * + order_id: optional, references the order if this event was associated with an order
 * + admin_user_id: optional, references an admin user if one was responsible for the change in points for this event
 * + data: optional serialized associative array of arbitrary data which can be provided by third party point events
 * + date: the event date
 */

if ( ! class_exists( 'WC_Points_Rewards' ) ) :
	define( 'WC_POINTS_REWARDS_VERSION', '1.6.37' ); // WRCS: DEFINED_VERSION.
	define( 'WC_POINTS_REWARDS_ENDPOINT', 'points-and-rewards' );

	class WC_Points_Rewards {

		/** plugin version number */
		const VERSION = WC_POINTS_REWARDS_VERSION;

		/**
		 * The single instance of the class.
		 *
		 * @var $_instance
		 * @since 1.6.32
		 */
		protected static $_instance = null;

		/** @var string the plugin path */
		private $plugin_path;

		/** @var string the plugin url */
		private $plugin_url;

		/** @var \WC_Logger instance */
		private $logger;

		/** @var \WC_Points_Rewards_Admin admin class */
		private $admin;

		/** @var \WC_Points_Rewards_Admin product admin class */
		private $product_admin;

		/** @var WP_Admin_Message_Handler admin message handler class */
		public $admin_message_handler;

		/** @var string the user points log database tablename */
		public $user_points_log_db_tablename;

		/** @var string the user points database tablename */
		public $user_points_db_tablename;

		/** @var WC_Points_Rewards_Actions the core actions integration */
		public $actions;

		/** @var string the endpoint page to use for frontend */
		public $endpoint = WC_POINTS_REWARDS_ENDPOINT;

		/**
		 * Initializes the plugin
		 *
		 * @since 1.0
		 */
		public function __construct() {
			global $wpdb;

			// Expire points on schedule
			add_action( 'wc_points_rewards_expire_points_daily', array( $this, 'expire_points' ) );

			// initialize the custom table names
			$this->user_points_log_db_tablename = $wpdb->prefix . 'wc_points_rewards_user_points_log';
			$this->user_points_db_tablename     = $wpdb->prefix . 'wc_points_rewards_user_points';

			// include required files
			$this->includes();

			// called just before the woocommerce template functions are included
			add_action( 'init', array( $this, 'include_template_functions' ), 25 );

			// display points on a separate tab on user's account page
			add_action( 'init', array( $this, 'add_endpoints' ) );

			// initialize user point balance on user create/update, and remove the user points record on user delete
			add_action( 'user_register',  array( $this, 'refresh_user_points_balance' ) );
			add_action( 'profile_update', array( $this, 'refresh_user_points_balance' ) );
			add_action( 'delete_user',    array( $this, 'delete_user_points' ) );

			// Set up hooks and schedules for expirying points daily
			register_deactivation_hook( __FILE__, array( $this, 'wc_points_rewards_expire_points_remove_schedule' ) );

			// Set up hooks for the P&R tab on the frontend
			register_deactivation_hook( __FILE__, 'wc_points_rewards_activate' );

			// admin
			if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
				// add a 'Configure' link to the plugin action links
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_configure_link' ) );

			}

			// run every time
			$this->install();

			add_action( 'after_switch_theme', 'wc_points_rewards_activate' );
		}

		/**
		 * Register new endpoint to use inside My Account page.
		 *
		 * @since 1.6.3
		 *
		 * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
		 */
		public function add_endpoints() {
			add_rewrite_endpoint( WC_POINTS_REWARDS_ENDPOINT, EP_ROOT | EP_PAGES );
		}

		/**
		 * Main Instance.
		 *
		 * Ensures only one instance is loaded or can be loaded.
		 *
		 * @since 1.6.32
		 * @return WC_Points_Rewards
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Insert the new endpoint into the My Account menu.
		 *
		 * @since 1.6.3
		 *
		 * @param array $menu_items
		 * @return array
		 */
		public function add_menu_items( $menu_items ) {
			// Remove logout menu item.
			$logout = $menu_items['customer-logout'];
			unset( $menu_items['customer-logout'] );

			// Insert Points & Rewards.
			$menu_items[ $this->endpoint ] = $this->get_points_label( 2 );

			// Insert back logout item.
			$menu_items['customer-logout'] = $logout;

			return $menu_items;
		}

		/**
		 * Refreshes the user points balance.  This is called on user
		 * create, as well as on user update giving the admin an (albeit simple)
		 * means to refresh a users points balance if, for instance a user
		 * was created while after the points & rewards plugin was installed, but
		 * during a time when it was disabled, or the points balance got out of
		 * whack somehow or other.
		 *
		 * @since 1.0
		 * @param int $user_id user identifier
		 */
		public function refresh_user_points_balance( $user_id ) {

			// do nothing if the identified user is not a customer
			if ( ! user_can( $user_id, 'customer' ) ) {
				return;
			}

			// refresh the points balance user meta
			update_user_meta( $user_id, 'wc_points_balance', WC_Points_Rewards_Manager::get_users_points( $user_id ) );
		}

		/**
		 * Deletes the user points for the deleted user identified by $user_id
		 *
		 * @since 1.0
		 * @param int $user_id the identifier of the user being deleted
		 */
		public function delete_user_points( $user_id ) {
			WC_Points_Rewards_Manager::delete_user_points( $user_id );
		}

		/**
		 * Include required files
		 *
		 * @since 1.0
		 */
		public function includes() {

			// product class
			require_once dirname( __FILE__ ) . '/includes/class-wc-points-rewards-product.php';
			$this->product = new WC_Points_Rewards_Product();

			// cart / checkout class
			require_once dirname( __FILE__ ) . '/includes/class-wc-points-rewards-cart-checkout.php';
			$this->cart = new WC_Points_Rewards_Cart_Checkout();

			// privacy class
			require_once dirname( __FILE__ ) . '/includes/class-wc-points-rewards-privacy.php';

			// order class
			require_once dirname( __FILE__ ) . '/includes/class-wc-points-rewards-order.php';
			$this->order = new WC_Points_Rewards_Order();

			// discount class
			if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
				require_once dirname( __FILE__ ) . '/includes/class-wc-points-rewards-legacy-discount.php';
			} else {
				require_once dirname( __FILE__ ) . '/includes/class-wc-points-rewards-discount.php';
			}
			$this->discount = new WC_Points_Rewards_Discount();

			// actions class
			require_once dirname( __FILE__ ) . '/includes/class-wc-points-rewards-actions.php';
			$this->actions = new WC_Points_Rewards_Actions();

			// manager class
			require_once dirname( __FILE__ ) . '/includes/class-wc-points-rewards-manager.php';

			// points log access class
			require_once dirname( __FILE__ ) . '/includes/class-wc-points-rewards-points-log.php';

			add_action( 'woocommerce_account_menu_items', array( $this, 'add_menu_items' ) );
			add_action( 'woocommerce_account_' . $this->endpoint . '_endpoint', 'woocommerce_points_rewards_my_points' );

			if ( is_admin() ) {
				$this->admin_includes();
			}
		}

		/**
		 * Include required admin files
		 *
		 * @since 1.0
		 */
		private function admin_includes() {

			// load admin class
			require_once dirname( __FILE__ ) . '/includes/class-wc-points-rewards-admin.php';
			$this->admin = new WC_Points_Rewards_Admin();

			// load product admin class
			require_once dirname( __FILE__ ) . '/includes/class-wc-points-rewards-product-admin.php';
			$this->product_admin = new WC_Points_Rewards_Product_Admin();

			// used to provide an admin message handling utility
			require_once dirname( __FILE__ ) . '/includes/class-wp-admin-message-handler.php';
			$this->admin_message_handler = new WP_Admin_Message_Handler();
		}

		/**
		 * Function used to init WooCommerce Points & Rewards template functions,
		 * making them pluggable by plugins and themes.
		 *
		 * @since 1.0
		 */
		public function include_template_functions() {
			require_once dirname( __FILE__ ) . '/woocommerce-points-and-rewards-template.php';
		}

		/** Admin methods ******************************************************/

		/**
		 * Return the plugin action links.  This will only be called if the plugin
		 * is active.
		 *
		 * @since 1.0
		 * @param array $actions associative array of action names to anchor tags
		 * @return array associative array of plugin action links
		 */
		public function add_plugin_configure_link( $actions ) {
			// add the link to the front of the actions list
			return ( array_merge( array( 'configure' => sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=woocommerce-points-and-rewards&tab=settings' ), __( 'Configure', 'woocommerce-points-and-rewards' ) ) ),
			$actions )
			);
		}

		/** Helper methods ******************************************************/

		/**
		 * Returns the points label, singular or plural form, based on $count
		 *
		 * @since 0.1
		 * @param int $count the count
		 * @return string the points label
		 */
		public function get_points_label( $count ) {

			list( $singular, $plural ) = explode( ':', get_option( 'wc_points_rewards_points_label' ) );

			return 1 == $count ? $singular : $plural;
		}

		/**
		 * Gets the absolute plugin path without a trailing slash, e.g.
		 * /path/to/wp-content/plugins/plugin-directory
		 *
		 * @since 1.0
		 * @return string plugin path
		 */
		public function get_plugin_path() {

			if ( $this->plugin_path ) {
				return $this->plugin_path;
			}

			$this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );

			return $this->plugin_path;
		}

		/**
		 * Gets the plugin url without a trailing slash
		 *
		 * @since 1.0
		 * @return string the plugin url
		 */
		public function get_plugin_url() {

			if ( $this->plugin_url ) {
				return $this->plugin_url;
			}

			$this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );

			return $this->plugin_url;
		}

		/**
		 * Log errors / messages to WooCommerce error log (/wp-content/woocommerce/logs/)
		 *
		 * @since 1.0
		 * @param string $message
		 */
		public function log( $message ) {
			if ( ! is_object( $this->logger ) ) {
				$this->logger = new WC_Logger();
			}
			$this->logger->add( 'points-rewards', $message );
		}

		/** Lifecycle methods ******************************************************/

		/**
		 * Run every time. Used since the activation hook is not executed when updating a plugin
		 *
		 * @since 1.0
		 * @since 1.6.15 Update versioning at the beginning.
		 */
		private function install() {
			// get current version to check for upgrade
			$installed_version = get_option( 'wc_points_rewards_version' );

			// update the installed version option
			update_option( 'wc_points_rewards_version', self::VERSION );

			// install
			if ( ! $installed_version ) {
				require_once dirname( __FILE__ ) . '/includes/class-wc-points-rewards-admin.php';

				// initial install, add the wc_points_balance user meta (of 0) to all users
				$offset           = (int) get_option( 'wc_points_rewards_install_offset', 0 );
				$records_per_page = 500;
				do {
					// grab a set of user ids
					$user_ids = get_users( array( 'fields' => 'ID', 'offset' => $offset, 'number' => $records_per_page ) );

					// iterate through the results and set the meta
					if ( is_array( $user_ids ) ) {
						foreach ( $user_ids as $user_id ) {

							$wc_points_balance = get_user_meta( $user_id, 'wc_points_balance', true );

							if ( '' === $wc_points_balance ) {
								// need to create an empty balance for this customer
								update_user_meta( $user_id, 'wc_points_balance', 0 );
							}
						}
					}

					// increment offset
					$offset += $records_per_page;
					// and keep track of how far we made it in case we hit a script timeout
					update_option( 'wc_points_rewards_install_offset', $offset );

				} while ( count( $user_ids ) == $records_per_page );  // while full set of results returned  (meaning there may be more results still to retrieve)

				// install default settings, terms, etc
				foreach ( WC_Points_Rewards_Admin::get_settings() as $setting ) {
					if ( isset( $setting['default'] ) ) {
						add_option( $setting['id'], $setting['default'] );
					}
				}
			} // End if().

			// upgrade if installed version lower than plugin version
			if ( -1 === version_compare( $installed_version, self::VERSION ) ) {
				$this->upgrade( $installed_version );
			}

			// Since 1.6.3 we've moved the frontend points page to its own tab so we need to flush endpoints.
			if ( version_compare( $installed_version, '1.6.3', '<' ) || ! $installed_version ) {
				flush_rewrite_rules();
			}

			if ( version_compare( $installed_version, '1.6.14', '<' ) ) {
				$current_option_value = get_option( 'wc_points_rewards_variable_product_message' );
				// Make sure that the customer is still using the initial defaulting string before update
				if ( 'Earn up to <strong>{points}</strong> {points_label} Points.' === $current_option_value ) {
					update_option( 'wc_points_rewards_variable_product_message', 'Earn up to <strong>{points}</strong> {points_label}.' );
				}
			}
		}

		/**
		 * Perform any version-related changes. Changes to custom db tables are handled by the migrate() method
		 *
		 * @since 1.0
		 * @param int $installed_version the currently installed version of the plugin
		 */
		private function upgrade( $installed_version ) {

			$this->migrate( $installed_version );

			wc_points_rewards_expire_points_schedule();
		}

		public function wc_points_rewards_expire_points_remove_schedule() {
			wp_clear_scheduled_hook( 'wc_points_rewards_expire_points' );
		}



		/**
		 * Perform updates to custom db tables using dbDelta()
		 *
		 * @since 1.0
		 */
		private function migrate( $installed_version ) {
			global $wpdb;

			$wpdb->hide_errors();

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			// it's important that this table be indexed-up as it can grow quite large
			$sql =
			"CREATE TABLE {$this->user_points_log_db_tablename} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			points bigint(20) NOT NULL,
			type varchar(255) DEFAULT NULL,
			user_points_id bigint(20) DEFAULT NULL,
			order_id bigint(20) DEFAULT NULL,
			admin_user_id bigint(20) DEFAULT NULL,
			data longtext DEFAULT NULL,
			date datetime NOT NULL,
			KEY idx_wc_points_rewards_user_points_log_date (date),
			KEY idx_wc_points_rewards_user_points_log_type (type),
			KEY idx_wc_points_rewards_user_points_log_points (points),
			PRIMARY KEY  (id)
			) " . $this->get_db_collation();
			dbDelta( $sql );

			$sql =
			"CREATE TABLE {$this->user_points_db_tablename} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			points bigint(20) NOT NULL,
			points_balance bigint(20) NOT NULL,
			order_id bigint(20) DEFAULT NULL,
			date datetime NOT NULL,
			KEY idx_wc_points_rewards_user_points_user_id_points_balance (user_id,points_balance),
			KEY `idx_wc_points_rewards_user_points_date_points_balance` (`date`,`points_balance`),
			PRIMARY KEY  (id)
			) " . $this->get_db_collation();
			dbDelta( $sql );
		}

		/**
		 * Returns the WordPress DB collation clause used when creating tables
		 *
		 * @since 1.0
		 * @return string db collation clause
		 */
		private function get_db_collation() {
			global $wpdb;

			$collate = '';
			if ( $wpdb->has_cap( 'collation' ) ) {
				if ( ! empty( $wpdb->charset ) ) {
					$collate .= "DEFAULT CHARACTER SET {$wpdb->charset}";
				}
				if ( ! empty( $wpdb->collate ) ) {
					$collate .= " COLLATE {$wpdb->collate}";
				}
			}

			return $collate;
		}

		/**
		 * Function expire_points()
		 * If a value is set for points expiry, then expire points based on expiry period
		 *
		 * @since 1.4.2
		 * @return string db collation clause
		 */

		public function expire_points() {
			global $wpdb;

			$expiry = get_option( 'wc_points_rewards_points_expiry', '' );
			$expire_since = get_option( 'wc_points_rewards_points_expire_points_since', null );

			if ( isset( $expiry ) && '' !== $expiry ) {

				list( $number, $period ) = explode( ':', $expiry );

				if ( is_numeric( $number ) && in_array( $period, array( 'DAY', 'WEEK', 'MONTH', 'YEAR' ) ) ) {

					$date_expire_before = date( 'Y-m-d H:i', strtotime( "- $number $period" ) );

					if ( ! $expire_since ) {
						$expiring_points = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$this->user_points_db_tablename} WHERE date < %s AND NOT points_balance = 0;", $date_expire_before ) );
					} else {
						$expiring_points = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$this->user_points_db_tablename} WHERE date < %s AND DATE >= %s AND NOT points_balance = 0;", $date_expire_before, $expire_since ) );
					}

					if ( count( $expiring_points ) > 0 ) {

						foreach ( $expiring_points as $expiring ) {

							$wpdb->update( $this->user_points_db_tablename, array( 'points_balance' => 0 ), array( 'id' => $expiring->id ) );

							$wpdb->insert( $this->user_points_log_db_tablename, array(
									'user_id'        => $expiring->user_id,
									'points'         => '-' . $expiring->points_balance,
									'type'           => 'expire',
									'user_points_id' => $expiring->id,
									'data'           => '',
									'date'           => current_time( 'mysql', 1 ),
								)
							);

						}
					}
				}
			} // End if().
		}
	}
endif;

add_action( 'plugins_loaded', 'woocommerce_points_and_rewards_init' );

/**
 * Initializes the extension.
 *
 * @since 1.6.32
 * @return Object Instance of extension.
 */
function woocommerce_points_and_rewards_init() {
	load_plugin_textdomain( 'woocommerce-points-and-rewards', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_points_and_rewards_missing_wc_notice' );
		return;
	}

	$GLOBALS['wc_points_rewards'] = WC_Points_Rewards::instance();
}
