<?php
/**
 * Plugin Name: WooCommerce Waitlist
 * Plugin URI: http://www.woothemes.com/products/woocommerce-waitlist/
 * Description: This plugin enables registered users to request an email notification when an out-of-stock product comes back into stock. It tallies these registrations in the admin panel for review and provides details.
 * Version: 2.1.22
 * Author: Neil Pie
 * Author URI: https://pie.co.de/
 * Developer: Neil Pie
 * Developer URI: https://pie.co.de/
 * Woo: 122144:55d9643a241ecf5ad501808c0787483f
 * WC requires at least: 3.0.0
 * WC tested up to: 4.2.0
 * Requires at least: 4.2.0
 * Tested up to: 5.4.1
 * Text Domain: woocommerce-waitlist
 * Domain Path: /assets/languages/
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Copyright: © 2015-2020 WooCommerce
 *
 * @package WooCommerce Waitlist
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once 'woo-includes/woo-functions.php';
}
/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '55d9643a241ecf5ad501808c0787483f', '122144' );
if ( ! class_exists( 'WooCommerce_Waitlist_Plugin' ) ) {
	/**
	 * Activate when WC starts
	 *
	 * Only start us up if WC is running
	 */
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ||
		array_key_exists( 'woocommerce/woocommerce.php', get_site_option( 'active_sitewide_plugins' ) ) ) {
		add_action( 'plugins_loaded', 'WooCommerce_Waitlist_Plugin::instance' );
	} else {
		add_action( 'admin_notices', array( 'WooCommerce_Waitlist_Plugin', 'output_waitlist_not_active_notice' ) );
	}

	/**
	 * Namespace class for functions non-specific to any object within the plugin
	 *
	 * @package  WooCommerce Waitlist
	 */
	class WooCommerce_Waitlist_Plugin {

		/**
		 * Main plugin class instance
		 *
		 * @var object
		 */
		protected static $instance;
		/**
		 * Path to plugin directory
		 *
		 * @var string
		 */
		public static $path;
		/**
		 * Supported product types
		 *
		 * @var array
		 */
		public static $allowed_product_types;
		/**
		 * $Pie_WCWL_Admin_Init
		 *
		 * @var object
		 */
		public static $Pie_WCWL_Admin_Init;

		/**
		 * WooCommerce_Waitlist_Plugin constructor
		 */
		public function __construct() {
			self::$path                  = plugin_dir_path( __FILE__ );
			self::$allowed_product_types = $this->get_product_types();
			require_once 'definitions.php';
			if ( ! $this->minimum_woocommerce_version_is_loaded() ) {
				return;
			}
			$this->include_files();
			$this->load_hooks();
		}

		/**
		 * Check users version of WooCommerce is high enough for our plugin
		 *
		 * @return bool
		 */
		public function minimum_woocommerce_version_is_loaded() {
			global $woocommerce;
			if ( ! version_compare( $woocommerce->version, '3.0', '<' ) ) {
				return true;
			}
			add_action( 'admin_notices', array( __CLASS__, 'output_waitlist_not_active_notice' ) );

			return false;
		}

		/**
		 * Display an admin notice notifying users their version of WooCommerce is too low
		 *
		 * @return void
		 */
		public static function output_waitlist_not_active_notice() {
			?>
			<div class="error">
				<p><?php esc_html_e( 'WooCommerce Waitlist is active but is not functional. Is WooCommerce installed and up to date (version 3.0 or higher)?', 'woocommerce-waitlist' ); ?></p>
			</div>
			<?php
		}

		/**
		 * Load required files and instantiate classes where needed
		 */
		public function include_files() {
			require_once 'wcwl-waitlist-template-functions.php';
			require_once 'classes/class-pie-wcwl-waitlist.php';
			if ( is_admin() ) {
				require_once 'classes/admin/class-pie-wcwl-admin-init.php';
				$admin = new Pie_WCWL_Admin_Init();
				$admin->init();
				require_once 'classes/frontend/class-pie-wcwl-frontend-ajax.php';
				$frontend_ajax = new Pie_WCWL_Frontend_Ajax();
				$frontend_ajax->init();
			} else {
				require_once 'classes/frontend/class-pie-wcwl-frontend-init.php';
				$frontend = new Pie_WCWL_Frontend_Init();
				$frontend->init();
			}
		}

		/**
		 * All other hooks pertinent to the main plugin class
		 *
		 * @todo factor out hooks into appropriate classes
		 */
		public function load_hooks() {
			// Initialisation.
			add_action( 'admin_init', array( $this, 'version_check' ) );
			add_action( 'init', array( $this, 'set_default_localization_directory' ) );
			add_filter( 'woocommerce_email_classes', array( $this, 'initialise_waitlist_email_class' ) );
			add_action( 'init', array( $this, 'register_custom_endpoints' ) );
			add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_links' ) );
			// Global.
			add_action( 'woocommerce_checkout_order_processed', array( $this, 'remove_user_from_waitlist_on_product_purchase' ), 10, 3 );
			add_action( 'delete_user', array( $this, 'unregister_user_when_deleted' ) );
			// Mailout hooks.
			add_action( 'woocommerce_product_set_stock_status', array( $this, 'perform_api_mailout_stock_status' ), 10, 2 );
			add_action( 'woocommerce_product_set_stock', array( $this, 'perform_api_mailout_stock' ) );
			add_action( 'woocommerce_product_set_stock', array( $this, 'update_stock_status' ), 99 );
			add_action( 'woocommerce_variation_set_stock_status', array( $this, 'perform_api_mailout_stock_status' ), 10, 2 );
			add_action( 'woocommerce_variation_set_stock', array( $this, 'perform_api_mailout_stock' ) );
			add_action( 'woocommerce_variation_set_stock', array( $this, 'update_stock_status' ), 99 );
			add_action( 'woocommerce_product_object_updated_props', array( $this, 'perform_api_mailout_bundles' ), 10, 2 );
			add_action( 'transition_post_status', array( $this, 'perform_api_mailout_on_publish' ), 10, 3 );
			// Events (ticket stock status is updated directly in postmeta so does not trigger WC hooks above).
			if ( function_exists( 'tribe_is_event' ) && 'yes' == get_option( 'woocommerce_waitlist_events' ) ) {
				add_action( 'updated_postmeta', array( $this, 'perform_mailout_if_ticket_stock_updated' ), 10, 4 );
				add_action( 'tribe_tickets_ticket_add', array( $this, 'trigger_waitlist_mailouts_for_events' ), 10, 3 );
			}
		}

		/**
		 * Define the product types we want to load waitlist into
		 *
		 * @todo add notice for deprecated hook 'woocommerce_waitlist_supported_products'
		 *
		 * @return mixed|void
		 */
		public function get_product_types() {
			$product_types = apply_filters(
				'woocommerce_waitlist_supported_products',
				array(
					'simple'                => array(
						'filepath' => 'product-types/class-pie-wcwl-frontend-simple.php',
						'class'    => 'Pie_WCWL_Frontend_Simple',
					),
					'variable'              => array(
						'filepath' => 'product-types/class-pie-wcwl-frontend-variable.php',
						'class'    => 'Pie_WCWL_Frontend_Variable',
					),
					'grouped'               => array(
						'filepath' => 'product-types/class-pie-wcwl-frontend-grouped.php',
						'class'    => 'Pie_WCWL_Frontend_Grouped',
					),
					'subscription'          => array(
						'filepath' => 'product-types/class-pie-wcwl-frontend-simple.php',
						'class'    => 'Pie_WCWL_Frontend_Simple',
					),
					'variable-subscription' => array(
						'filepath' => 'product-types/class-pie-wcwl-frontend-variable.php',
						'class'    => 'Pie_WCWL_Frontend_Variable',
					),
					'bundle'                => array(
						'filepath' => 'product-types/class-pie-wcwl-frontend-bundle.php',
						'class'    => 'Pie_WCWL_Frontend_Bundle',
					),
				)
			);

			return apply_filters( 'wcwl_supported_products', $product_types );
		}

		/**
		 * Add custom endpoint for the waitlist tab on the user account page
		 */
		public function register_custom_endpoints() {
			add_rewrite_endpoint( apply_filters( 'wcwl_waitlist_endpoint', get_option( 'woocommerce_myaccount_waitlist_endpoint', 'woocommerce-waitlist' ) ), EP_ROOT | EP_PAGES );
		}

		/**
		 * Perform mailouts when stock status is updated and product is in stock
		 * We only want to do this for variations and simple products NOT variable (parent) products
		 *
		 * @todo refactor to waitlist class
		 *
		 * @param int    $product_id updated product ID.
		 * @param string $stock_status product stock status.
		 */
		public function perform_api_mailout_stock_status( $product_id, $stock_status ) {
			$product = wc_get_product( $product_id );
			if ( ! $product || 'publish' !== get_post_status( $product->get_id() ) ) {
				return;
			}
			if ( 'instock' === $stock_status || $product->is_in_stock() ) {
				if ( self::is_variation( $product ) && 'publish' !== get_post_status( $product->get_parent_id() ) ) {
					return;
				}
				$this->do_mailout( $product );
			}
		}

		/**
		 * Perform mailouts when stock quantity is updated and product registers as in stock
		 * We only want to do this for variations and simple products NOT variable (parent) products
		 *
		 * @todo refactor to waitlist class
		 *
		 * @param WC_Product/int $product updated product/product ID
		 */
		public function perform_api_mailout_stock( $product ) {
			$product = wc_get_product( $product );
			if ( ! $product || 'publish' !== get_post_status( $product->get_id() ) ) {
				return;
			}
			if ( self::is_variable( $product ) && $product->managing_stock() ) {
				$this->handle_variable_mailout( $product );
			} elseif ( $product->is_in_stock() ) {
				if ( self::is_variation( $product ) && 'publish' !== get_post_status( $product->get_parent_id() ) ) {
					return;
				}
				$this->do_mailout( $product );
			}
		}

		/**
		 * Handle mailouts when variable product stock status is updated for each variation
		 *
		 * @param WC_Product $product updated variable product.
		 * @return void
		 */
		public function handle_variable_mailout( $product ) {
			foreach ( $product->get_available_variations() as $variation ) {
				$variation = wc_get_product( $variation['variation_id'] );
				if ( 'parent' === $variation->managing_stock() && $product->is_in_stock() ) {
					$this->do_mailout( $variation );
				}
			}
		}

		/**
		 * Perform mailouts for product bundles if the given bundled product returns in stock
		 *
		 * @param WC_Product $product updated product.
		 * @param array      $updated_props updated property array.
		 * @return void
		 */
		public function perform_api_mailout_bundles( $product, $updated_props ) {
			if ( $product->is_type( 'bundle' ) && in_array( 'bundled_items_stock_status', $updated_props, true ) && ! is_null( $product->stock_status ) && $product->is_in_stock() && 'publish' === get_post_status( $product->get_id() ) ) {
				$this->do_mailout( $product );
			}
		}

		/**
		 * Update custom product meta to keep track of whether a product was in/out of stock before the latest update
		 *
		 * @param WC_Product $product updated product.
		 */
		public function update_stock_status( $product ) {
			$stock = $product->get_stock_quantity();
			if ( ! $stock || 'publish' !== get_post_status( $product->get_id() ) || ( self::is_variation( $product ) && 'publish' !== get_post_status( $product->get_parent_id() ) ) ) {
				$stock = 0;
			}
			update_post_meta( $product->get_id(), 'wcwl_stock_level', $stock );
		}

		/**
		 * Triggers mailout when "_stock_status" postmeta for an event ticket product is updated to "instock"
		 *
		 * @param int    $meta_id meta ID.
		 * @param int    $post_id post ID.
		 * @param string $meta_key meta key.
		 * @param mixed  $meta_value meta value.
		 */
		public function perform_mailout_if_ticket_stock_updated( $meta_id, $post_id, $meta_key, $meta_value ) {
			if ( ! tribe_events_product_is_ticket( $post_id ) ) {
				return;
			}
			if ( '_stock_status' !== $meta_key ) {
				return;
			}
			$product = wc_get_product( $post_id );
			if ( $product && $product->is_in_stock() && 'publish' === get_post_status( $product->get_id() ) ) {
				$this->do_mailout( $product );
			}
		}

		/**
		 * Trigger in stock notification if required when ticket is updated
		 *
		 * @param int    $event_id event ID.
		 * @param object $ticket ticket.
		 * @param array  $data data.
		 */
		public function trigger_waitlist_mailouts_for_events( $event_id, $ticket, $data ) {
			if ( ! get_post_meta( $ticket->ID, WCWL_SLUG, true ) ) {
				return;
			}
			$ticket = wc_get_product( $ticket->ID );
			if ( $ticket && $ticket->is_in_stock() && 'publish' === get_post_status( $ticket->ID ) ) {
				$this->do_mailout( $ticket );
			}
		}

		/**
		 * Trigger mailouts when post status is updated
		 *
		 * @param string $new_status
		 * @param string $old_status
		 * @param object $post
		 * @return void
		 *
		 * @todo figure clever way to force mailout on post transition, maybe a refactor needed to avoid stock requirements here
		 */
		public function perform_api_mailout_on_publish( $new_status, $old_status, $post ) {
			if ( 'publish' !== $old_status && 'publish' === $new_status ) {
				$product = wc_get_product( $post );
				if ( $product && self::is_variable( $product ) ) {
					foreach ( $product->get_available_variations() as $variation ) {
						$variation = wc_get_product( $variation['variation_id'] );
						$this->perform_api_mailout_stock( $variation );
					}
				} else {
					$this->perform_api_mailout_stock( $product );
				}
			}
		}

		/**
		 * Fire a call to perform the mailout for the given product
		 *
		 * @param WC_Product $product updated product.
		 */
		public function do_mailout( $product ) {
			$stock_level = $this->get_minimum_stock_level( $product->get_id() );
			if ( $this->minimum_stock_requirement_met( $product, $stock_level ) && $this->stock_level_has_broken_threshold( $product, $stock_level ) ) {
				$product->waitlist = new Pie_WCWL_Waitlist( $product );
				$product->waitlist->waitlist_mailout();
			}
		}

		/**
		 * Return minimum required stock level before we email waitlist users
		 *
		 * @param int $product_id product ID.
		 *
		 * @return int
		 * @since  1.8.0
		 */
		public function get_minimum_stock_level( $product_id ) {
			$options = get_post_meta( $product_id, 'wcwl_options', true );
			if ( isset( $options['enable_stock_trigger'] ) && 'true' === $options['enable_stock_trigger'] && isset( $options['minimum_stock'] ) ) {
				return absint( $options['minimum_stock'] );
			} else {
				$minimum_stock = get_option( 'woocommerce_waitlist_minimum_stock' ) ? absint( get_option( 'woocommerce_waitlist_minimum_stock' ) ) : 1;
				return $minimum_stock;
			}
		}

		/**
		 * Check the minimum stock requirements are met for the current waitlist before processing mailouts
		 *
		 * @param $product
		 * @param $stock_level_required
		 *
		 * @return bool
		 * @since  1.8.0
		 */
		public function minimum_stock_requirement_met( $product, $stock_level_required ) {
			if ( ( self::is_simple( $product ) || $product->is_type( 'bundle' ) ) && ! $product->get_manage_stock() ) {
				return true;
			}
			$product_stock = $product->get_stock_quantity();
			if ( self::is_variation( $product ) && ! $product->get_manage_stock() ) {
				$parent = wc_get_product( $product->get_parent_id() );
				if ( ! $parent->get_manage_stock() ) {
					return true;
				} else {
					$product_stock = $parent->get_stock_quantity();
				}
			}
			if ( $product_stock && $product_stock >= $stock_level_required ) {
				return true;
			}
			return false;
		}

		/**
		 * Check the stock level update has caused the stock level to go from under->over the set threshold
		 * This check avoids sending a duplicate mailout each time the product stock increases
		 *
		 * @param object $product WC_Product.
		 * @param int    $stock_level_required set stock threshold.
		 * @return boolean
		 */
		public function stock_level_has_broken_threshold( $product, $stock_level_required ) {
			$previous_stock_level = (int) get_post_meta( $product->get_id(), 'wcwl_stock_level', true );
			if ( $previous_stock_level < $stock_level_required ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Check to see if product is of type "variable"
		 *
		 * @param $product
		 *
		 * @return bool
		 */
		public static function is_variable( $product ) {
			if ( $product->is_type( 'variable' ) || $product->is_type( 'variable-subscription' ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Check to see if product is of type "variation"
		 *
		 * @param $product
		 *
		 * @return bool
		 */
		public static function is_variation( $product ) {
			if ( $product->is_type( 'variation' ) || $product->is_type( 'subscription_variation' ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Check to see if product is of type "simple"
		 *
		 * @param $product
		 *
		 * @return bool
		 */
		public static function is_simple( $product ) {
			if ( $product->is_type( 'simple' ) || $product->is_type( 'subscription' ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Get the user object, check which products they are on the waitlist for and unregister them from each one when deleted
		 *
		 * @param  int $user_id id of the user that is being deleted
		 *
		 * @access public
		 * @return void
		 * @since  1.3
		 */
		public function unregister_user_when_deleted( $user_id ) {
			$waitlists = self::get_waitlist_products_by_user_id( $user_id );
			$user      = get_user_by( 'id', $user_id );
			if ( $user && $waitlists ) {
				foreach ( $waitlists as $product ) {
					if ( $product ) {
						$waitlist = new Pie_WCWL_Waitlist( $product );
						$waitlist->unregister_user( $user );
					}
				}
			}
			$archives = self::get_waitlist_archives_by_user_id( $user_id );
			self::remove_user_from_archives( $archives, $user_id );
		}

		/**
		 * Return all the products that the user is on the waitlist for
		 *
		 * @access public
		 * @return array
		 *
		 * @since  1.6.2
		 */
		public static function get_waitlist_products_by_user_id( $user_id ) {
			global $wpdb;
			$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_key = '" . WCWL_SLUG . "' AND meta_value LIKE '%i:{$user_id};%'", OBJECT );
			$results = self::return_products_user_is_registered_on( $results, $user_id );

			return $results;
		}

		/**
		 * Integrity check on data to ensure users are on the waitlists for the returned products
		 *
		 * @param $products
		 *
		 * @return array
		 */
		public static function return_products_user_is_registered_on( $products, $user_id ) {
			$waitlist_products = array();
			foreach ( $products as $product ) {
				$product  = wc_get_product( $product->post_id );
				$waitlist = new Pie_WCWL_Waitlist( $product );
				if ( $waitlist->user_is_registered( $user_id ) ) {
					$waitlist_products[] = $product;
				}
			}

			return $waitlist_products;
		}

		/**
		 * Return all the products that the user is on a waitlist archive for
		 *
		 * @access public
		 *
		 * @param $user_id
		 *
		 * @return array
		 * @since  1.6.2
		 */
		public static function get_waitlist_archives_by_user_id( $user_id ) {
			if ( ! get_option( '_' . WCWL_SLUG . '_metadata_updated' ) ) {
				return array();
			}
			global $wpdb;
			$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_key = 'wcwl_waitlist_archive' AND meta_value LIKE '%i:{$user_id};i:{$user_id};%'", OBJECT );

			return $results;
		}

		/**
		 * Remove user from all archives
		 *
		 * If an admin removes the user they are deleted, if a user removes themselves the User ID is stored as 0 so we can track it
		 */
		public static function remove_user_from_archives( $archives, $user_id ) {
			if ( ! $user_id || empty( $archives ) ) {
				return;
			}
			foreach ( $archives as $archive ) {
				$product_id  = $archive->post_id;
				$old_archive = unserialize( $archive->meta_value );
				$new_archive = $old_archive;
				foreach ( $old_archive as $timestamp => $users ) {
					if ( empty( $users ) ) {
						unset( $new_archive[ $timestamp ] );
					} else {
						if ( get_current_user_id() != $user_id ) {
							unset( $new_archive[ $timestamp ][ $user_id ] );
						} else {
							$new_archive[ $timestamp ][ $user_id ] = 0;
						}
					}
				}
				update_post_meta( $product_id, 'wcwl_waitlist_archive', $new_archive );
			}
		}

		/**
		 * Return all product posts
		 *
		 * @static
		 * @access public
		 * @return array all product posts
		 * @since  1.7.0
		 */
		public static function return_all_product_ids() {
			$args = array(
				'post_type'      => array( 'product', 'product_variation' ),
				'post_status'    => get_post_stati(),
				'posts_per_page' => - 1,
				'fields'         => 'ids',
			);

			return get_posts( $args );
		}

		/**
		 * Checks if user is registered, if not creates a new customer and sends welcome email
		 *
		 * This function overrides woocommerce options to ensure that the user is created when joining the waitlist,
		 * options are reset afterwards
		 *
		 * @param  string $email users email address
		 *
		 * @access public
		 * @return object $current_user the customer's user object
		 * @since  1.3
		 */
		public static function create_new_customer_from_email( $email ) {
			if ( email_exists( $email ) ) {
				$current_user = email_exists( $email );
			} else {
				add_filter( 'pre_option_woocommerce_registration_generate_password', array( self::instance(), 'return_option_setting_yes' ), 10 );
				add_filter( 'pre_option_woocommerce_registration_generate_username', array( self::instance(), 'return_option_setting_yes' ), 10 );
				$current_user = self::create_new_customer( $email );
				remove_filter( 'pre_option_woocommerce_registration_generate_password', array( self::instance(), 'return_option_setting_yes' ), 10 );
				remove_filter( 'pre_option_woocommerce_registration_generate_username', array( self::instance(), 'return_option_setting_yes' ), 10 );
			}

			return $current_user;
		}

		/**
		 * A function to easily add and remove hooks pertaining to creating a user and forcing options
		 *
		 * @return string
		 */
		public function return_option_setting_yes() {
			return 'yes';
		}

		/**
		 * Create new customer using the given email and send user a welcome email with login details
		 *
		 * This function is required before woocommerce v2.1 as handling user creation is handled differently from then
		 *
		 * @access     public
		 *
		 * @param  string $email users email address
		 *
		 * @return int $user_id current user ID
		 * @since      1.3
		 */
		private static function create_new_customer( $email ) {
			$username = sanitize_user( current( explode( '@', $email ) ) );
			// Ensure username is unique
			$append     = 1;
			$o_username = $username;
			while ( username_exists( $username ) ) {
				$username = $o_username . $append;
				$append ++;
			}
			$password = wp_generate_password();
			$userdata = array(
				'user_login' => $username,
				'user_email' => $email,
				'user_pass'  => $password,
				'role'       => 'customer',
			);
			$user_id  = wp_insert_user( $userdata );
			if ( is_wp_error( $user_id ) ) {
				return $user_id;
			}
			do_action( 'woocommerce_created_customer', $user_id, $userdata, true );

			return $user_id;
		}

		/**
		 * Appends our Pie_WCWL_Waitlist_Mailout class to the array of WC_Email objects.
		 *
		 * @static
		 *
		 * @param  array $emails the woocommerce array of email objects
		 *
		 * @access public
		 * @return array         the woocommerce array of email objects with our email appended
		 */
		public static function initialise_waitlist_email_class( $emails ) {
			require_once 'classes/class-pie-wcwl-waitlist-mailout.php';
			require_once 'classes/class-pie-wcwl-waitlist-signup-email.php';
			$emails['Pie_WCWL_Waitlist_Mailout']      = new Pie_WCWL_Waitlist_Mailout();
			$emails['Pie_WCWL_Waitlist_Signup_Email'] = new Pie_WCWL_Waitlist_Signup_Email();

			return $emails;
		}

		/**
		 * Setup localization for plugin
		 *
		 * @access public
		 * @return void
		 */
		public function set_default_localization_directory() {
			load_plugin_textdomain( 'woocommerce-waitlist', false, plugin_basename( dirname( __FILE__ ) ) . '/assets/languages/' );
		}

		/**
		 * Check plugin version in DB and call required upgrade functions
		 *
		 * @hooked action admin_init
		 * @access public
		 * @return void
		 * @since  1.0.1
		 */
		public function version_check() {
			$options = get_option( WCWL_SLUG );
			if ( ! isset( $options['version'] ) ) {
				$this->set_default_options();
			} else {
				if ( version_compare( $options['version'], '1.1.0' ) < 0 ) {
					$this->move_variable_product_waitlist_entries_to_first_out_of_stock_variation();
				}
				if ( version_compare( $options['version'], '1.7.0' ) < 0 ) {
					update_option( 'woocommerce_queue_flush_rewrite_rules', 'true' );
				}
				if ( version_compare( $options['version'], '2.0.0' ) >= 0 ) {
					update_option( '_' . WCWL_SLUG . '_version_2_warning', true );
				}
			}
			$options['version'] = WCWL_VERSION;
			update_option( WCWL_SLUG, $options );
		}

		/**
		 * Set default waitlist options
		 */
		protected function set_default_options() {
			update_option( 'woocommerce_queue_flush_rewrite_rules', 'true' );
			update_option( '_' . WCWL_SLUG . '_metadata_updated', true );
			update_option( '_' . WCWL_SLUG . '_counts_updated', true );
			update_option( '_' . WCWL_SLUG . '_version_2_warning', true );
			update_option( WCWL_SLUG . '_archive_on', 'yes' );
			update_option( WCWL_SLUG . '_registration_needed', 'no' );
			update_option( WCWL_SLUG . '_minimum_stock', 1 );
		}

		/**
		 * Moves all waitlist entries on variable products to one of their variations
		 *
		 * This function is necessary when upgrading to version 1.1.0 - Prior to 1.1.0, waitlists for variable
		 * products were tracked against the parent product, and it was not possible to register for a waitlist on
		 * a product variation. This missing feature caused problems when one variation was out of stock and another
		 * in stock.
		 *
		 * In version 1.1.0, this feature has been added. Product variations can now hold their own waitlist, and
		 * the variable product parents now hold a waitlist containing all registrations for their child products.
		 * To bridge this upgrade gap, any waitlist registrations for a variable product will be moved to the first
		 * product variation that is out of stock.
		 *
		 * @access public
		 * @return void
		 * @since  1.1.0
		 */
		public function move_variable_product_waitlist_entries_to_first_out_of_stock_variation() {
			global $wpdb;
			$products                         = $wpdb->get_col( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '" . WCWL_SLUG . "' and meta_value <> 'a:0:{}'" );
			$moved_waitlists_at_1_0_4_upgrade = array();
			foreach ( $products as $product_id ) {
				$product = wc_get_product( $product_id );
				if ( $product->is_type( 'variable' ) ) {
					$waitlist                                        = get_post_meta( $product_id, WCWL_SLUG, true );
					$moved_waitlists_at_1_0_4_upgrade[ $product_id ] = array(
						'origin'   => $product_id,
						'user_ids' => $waitlist,
						'target'   => 0,
					);
					foreach ( $product->get_children() as $variation_id ) {
						$variation = wc_get_product( $variation_id );
						if ( $variation && ! $variation->is_in_stock() ) {
							$variation->waitlist = new Pie_WCWL_Waitlist( $variation );
							foreach ( $waitlist as $user_id ) {
								$variation->waitlist->register_user( get_user_by( 'id', $user_id ) );
							}
							$moved_waitlists_at_1_0_4_upgrade[ $product_id ]['target'] = $variation_id;
							break;
						}
					}
				}
			}
			if ( ! empty( $moved_waitlists_at_1_0_4_upgrade ) ) {
				$options                                     = get_option( WCWL_SLUG );
				$options['moved_waitlists_at_1_0_4_upgrade'] = $moved_waitlists_at_1_0_4_upgrade;
				update_option( WCWL_SLUG, $options );
				add_action( 'admin_notices', self::$Pie_WCWL_Admin_Init->alert_user_of_moved_waitlists_at_1_0_4_upgrade() );
			}
		}

		/**
		 * Check if users must log in to join waitlist
		 *
		 * This function is only returning true because the registration of logged out users onto waitlists is not
		 * currently being supported but may be added in a future version.
		 *
		 * @static
		 * @access public
		 * @return bool
		 * @since  1.0.1
		 */
		public static function users_must_be_logged_in_to_join_waitlist() {
			if ( 'yes' == get_option( 'woocommerce_waitlist_registration_needed' ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Check if persistent waitlists are disabled
		 *
		 * Filterable function to switch on persistent waitlists. Persistent waitlists will prevent users from being
		 * removed from a waitlist after email is sent, instead being removed when they purchase an item.
		 *
		 * @static
		 * @access public
		 *
		 * @param $product_id
		 *
		 * @return bool
		 * @since  1.1.1
		 */
		public static function persistent_waitlists_are_disabled( $product_id ) {
			return apply_filters( 'wcwl_persistent_waitlists_are_disabled', true, $product_id );
		}

		/**
		 * Check if automatic mailouts are disabled. If so, no email will be sent to waitlisted users when a product
		 * returns to stock and as such they will remain on the waitlist.
		 *
		 * @static
		 * @access public
		 *
		 * @param $product_id
		 *
		 * @return bool
		 * @since  1.1.8
		 */
		public static function automatic_mailouts_are_disabled( $product_id ) {
			return apply_filters( 'wcwl_automatic_mailouts_are_disabled', false, $product_id );
		}

		/**
		 * Apply filter to show waitlist on products that are "in stock" but available on back order
		 *
		 * @static
		 * @access public
		 *
		 * @param $product_id
		 *
		 * @return bool
		 * @since  2.0.14
		 */
		public static function enable_waitlist_for_backorder_products( $product_id ) {
			return apply_filters( 'wcwl_enable_waitlist_for_backorder_products', false, $product_id );
		}

		/**
		 * Removes user from waitlist on purchase if persistent waitlists are enabled
		 *
		 * @param  int         $order_id
		 * @param      $posted_data
		 * @param      $order
		 *
		 * @access public
		 */
		public function remove_user_from_waitlist_on_product_purchase( $order_id, $posted_data, $order ) {
			foreach ( $order->get_items() as $item ) {
				$product = $item->get_product();
				if ( $product ) {
					$user = get_user_by( 'email', $order->get_billing_email() );
					if ( $user ) {
						$waitlist = new Pie_WCWL_Waitlist( $product );
						$waitlist->unregister_user( $user );
					}
				}
			}
		}

		/**
		 * Register any required query variables. Currently, just the account tab endpoint is required
		 *
		 * @param $vars
		 *
		 * @return array
		 */
		public function add_query_vars( $vars ) {
			$vars[] = apply_filters( 'wcwl_waitlist_endpoint', get_option( 'woocommerce_myaccount_waitlist_endpoint', 'woocommerce-waitlist' ) );

			return $vars;
		}

		/**
		 * Include links to the documentation and settings page on the plugin screen
		 *
		 * @param mixed $links
		 *
		 * @since 1.7.3
		 * @return array
		 */
		public function plugin_links( $links ) {
			$plugin_links = array(
				'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=products&section=waitlist' ) . '">' . __( 'Settings', 'woocommerce-waitlist' ) . '</a>',
				'<a href="https://docs.woocommerce.com/document/woocommerce-waitlist/">' . _x( 'Docs', 'short for documents', 'woocommerce-waitlist' ) . '</a>',
				'<a href="https://woocommerce.com/my-account/marketplace-ticket-form/">' . __( 'Support', 'woocommerce-waitlist' ) . '</a>',
			);

			return array_merge( $plugin_links, $links );
		}

		/**
		 * Waitlist main instance, ensures only one instance is loaded
		 *
		 * @since 1.5.0
		 * @return WooCommerce_Waitlist_Plugin
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}
}
