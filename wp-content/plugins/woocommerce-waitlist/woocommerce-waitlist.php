<?php
/**
 * Plugin Name: WooCommerce Waitlist
 * Plugin URI: http://www.woothemes.com/products/woocommerce-waitlist/
 * Description: This plugin enables registered users to request an email notification when an out-of-stock product comes back into stock. It tallies these registrations in the admin panel for review and provides details.
 * Version: 2.4.2
 * Author: Neil Pie
 * Author URI: https://pie.co.de/
 * Developer: Neil Pie
 * Developer URI: https://pie.co.de/
 * Woo: 122144:55d9643a241ecf5ad501808c0787483f
 * WC requires at least: 3.0.0
 * WC tested up to: 7.9.0
 * Requires at least: 4.2.0
 * Tested up to: 6.2.2
 * Text Domain: woocommerce-waitlist
 * Domain Path: /assets/languages/
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Copyright: © 2015-2021 WooCommerce
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
	 * Only start us up if WC is running & declare HPOS compatibility
	 */
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ||
		( is_array( get_site_option( 'active_sitewide_plugins' ) ) && array_key_exists( 'woocommerce/woocommerce.php', get_site_option( 'active_sitewide_plugins' ) ) ) ) {
		add_action( 'plugins_loaded', 'WooCommerce_Waitlist_Plugin::instance' );
		add_action( 'before_woocommerce_init', function() {
			if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			}
		} );
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
			self::$path = plugin_dir_path( __FILE__ );
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
			add_action( 'switch_theme', array( $this, 'check_templates_after_theme_switch' ) );
			add_action( 'init', array( $this, 'set_default_localization_directory' ) );
			add_action( 'init', array( $this, 'setup_product_types' ), 15 );
			add_filter( 'woocommerce_email_classes', array( $this, 'initialise_waitlist_email_class' ) );
			add_action( 'init', array( $this, 'register_custom_endpoints' ) );
			add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_links' ) );
			// Global.
			add_action( 'woocommerce_checkout_order_processed', array( $this, 'remove_user_from_waitlist_on_product_purchase' ), 10, 3 );
			add_action( 'delete_user', array( $this, 'unregister_user_when_deleted' ) );
			add_action( 'user_register', array( $this, 'check_new_user_for_waitlist_entries' ) );
			// Mailout hooks
			add_action( 'init', array( $this, 'add_stock_update_hooks' ), 20 );
		}

		/**
		 * Hook up all mailout functions for stock updates
		 */
		public function add_stock_update_hooks() {
			// - maintain previous stock count for knowing if mailouts should be sent
			add_action( 'woocommerce_product_set_stock', array( $this, 'update_stock_status' ), 99 );
			add_action( 'woocommerce_variation_set_stock', array( $this, 'update_stock_status' ), 99 );
			// Avoid mailout hooks when processing product import
			if ( isset( $_REQUEST['action'] ) && 'woocommerce_do_ajax_product_import' === $_REQUEST['action'] &&
					 ! apply_filters( 'wcwl_allow_mailouts_during_product_import', false ) ) {
				return;
			}
			add_action( 'woocommerce_product_set_stock_status', array( $this, 'perform_api_mailout_stock_status' ), 10, 2 );
			add_action( 'woocommerce_product_set_stock', array( $this, 'perform_api_mailout_stock' ) );
			add_action( 'woocommerce_variation_set_stock_status', array( $this, 'perform_api_mailout_stock_status' ), 10, 2 );
			add_action( 'woocommerce_variation_set_stock', array( $this, 'perform_api_mailout_stock' ) );
			add_action( 'woocommerce_product_object_updated_props', array( $this, 'perform_api_mailout_bundles' ), 10, 2 );
			add_action( 'transition_post_status', array( $this, 'perform_api_mailout_on_publish' ), 10, 3 );
			// Events (ticket stock status is updated directly in postmeta so does not trigger WC hooks above).
			if ( function_exists( 'tribe_is_event' ) && 'yes' === get_option( 'woocommerce_waitlist_events' ) ) {
				add_action( 'updated_postmeta', array( $this, 'perform_mailout_if_ticket_stock_updated' ), 10, 4 );
				add_action( 'tribe_tickets_ticket_add', array( $this, 'trigger_waitlist_mailouts_for_events' ), 10, 3 );
			}
		}

		/**
		 * Setup allowed product types. Delayed on the init hook to allow for customisation
		 */
		public function setup_product_types() {
			self::$allowed_product_types = $this->get_product_types();
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
			if ( ! $product ) {
				return;
			}
			if ( 'instock' === $stock_status || $product->is_in_stock() ) {
				if ( self::is_variable( $product ) ) {
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
		 * @param WC_Product/int $product updated product/product ID.
		 */
		public function perform_api_mailout_stock( $product ) {
			$product = wc_get_product( $product );
			if ( ! $product ) {
				return;
			}
			if ( self::is_variable( $product ) && $product->managing_stock() ) {
				$this->handle_variable_mailout( $product );
			} elseif ( $product->is_in_stock() ) {
				$this->do_mailout( $product );
			}
		}

		/**
		 * Handle mailouts when variable product stock status is updated for each variation
		 *
		 * @param WC_Product $product updated variable product.
		 * @return void
		 */
		public function handle_variable_mailout( WC_Product $product ) {
			foreach ( $product->get_children() as $variation_id ) {
				$variation = wc_get_product( $variation_id );
				if ( ! $variation ) {
					continue;
				}
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
		public function perform_api_mailout_bundles( WC_Product $product, $updated_props ) {
			if ( ! $product ) {
				return;
			}
			if ( ! $product->is_type( 'bundle' ) ||
				empty( $updated_props ) ||
				'bundled_items_stock_status' !== $updated_props[0] ||
				is_null( $product->get_stock_status() ) ||
				! $product->is_in_stock() ) {
					return;
			}
			$this->do_mailout( $product );
		}

		/**
		 * Update custom product meta to keep track of whether a product was in/out of stock before the latest update
		 *
		 * @param WC_Product $product updated product.
		 */
		public function update_stock_status( WC_Product $product ) {
			if ( ! $product ) {
				return;
			}
			$stock = $product->get_stock_quantity();
			if ( ! $stock || ! in_array( get_post_status( $product->get_id() ), ['publish', 'private'] ) ) {
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
			if ( '_stock_status' !== $meta_key ) {
				return;
			}
			if ( ! function_exists( 'tribe_events_product_is_ticket' ) ) {
				return;
			}
			if ( ! tribe_events_product_is_ticket( $post_id ) ) {
				return;
			}
			$product = wc_get_product( $post_id );
			if ( $product && $product->is_in_stock() ) {
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
			if ( $ticket && $ticket->is_in_stock() ) {
				$this->do_mailout( $ticket );
			}
		}

		/**
		 * Trigger mailouts when post status is updated
		 *
		 * @param string $new_status post status updated to.
		 * @param string $old_status post status updated from.
		 * @param object $post       post object.
		 * @return void
		 *
		 * @todo figure clever way to force mailout on post transition, maybe a refactor needed to avoid stock requirements here
		 */
		public function perform_api_mailout_on_publish( $new_status, $old_status, $post ) {
			if ( ! in_array( $old_status, ['publish', 'private'] ) && in_array( $new_status, ['publish', 'private'] ) ) {
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
		public function do_mailout( WC_Product $product ) {
			if ( ! $product ) {
				return;
			}
			if ( apply_filters( 'wcwl_waitlist_should_do_mailout', true, $product ) ) {
				$stock_level = $this->get_minimum_stock_level( $product->get_id() );
				if ( $this->minimum_stock_requirement_met( $product, $stock_level ) && $this->stock_level_has_broken_threshold( $product, $stock_level ) ) {
					$waitlist = new Pie_WCWL_Waitlist( $product );
					$waitlist->waitlist_mailout();
					// Chained products
					global $wc_cp;
					if ( $wc_cp && method_exists( $wc_cp, 'get_chained_parent_ids' ) ) {
						$chained_products = $wc_cp->get_chained_parent_ids( $product->get_id() );
						if ( ! empty( $chained_products ) ) {
							wcwl_perform_mailout_for_chained_products( $chained_products );
						}
					}
					// Bundle products
					if ( function_exists( 'wc_pb_get_bundled_product_map' ) ) {
						$map = wc_pb_get_bundled_product_map( $product );
						if ( is_array( $map ) && ! empty( $map ) ) {
							wcwl_perform_mailout_for_bundle_products( $map );
						}
					}
				}
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
		 * @param WC_Product $product              product object.
		 * @param int        $stock_level_required minimum stock required to trigger waitlist mailout.
		 *
		 * @return bool
		 * @since  1.8.0
		 */
		public function minimum_stock_requirement_met( WC_Product $product, $stock_level_required ) {
			if ( ( self::is_simple( $product ) || $product->is_type( 'bundle' ) ) && ! $product->get_manage_stock() ) {
				return true;
			}
			$product_stock = $product->get_stock_quantity();
			if ( self::is_variation( $product ) && ! $product->get_manage_stock() ) {
				$parent = wc_get_product( $product->get_parent_id() );
				if ( ! $parent || ! $parent->get_manage_stock() ) {
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
		 * @param WC_Product $product WC_Product.
		 * @param int        $stock_level_required set stock threshold.
		 * @return boolean
		 */
		public function stock_level_has_broken_threshold( WC_Product $product, $stock_level_required ) {
			if ( ! $product ) {
				return false;
			}
			if ( ( self::is_simple( $product ) || $product->is_type( 'bundle' ) ) && ! $product->get_manage_stock() ) {
				return true;
			}
			if ( self::is_variation( $product ) && 'parent' === $product->get_manage_stock() ) {
				$previous_stock_level = (int) get_post_meta( $product->get_parent_id(), 'wcwl_stock_level', true );
			} else {
				$previous_stock_level = (int) get_post_meta( $product->get_id(), 'wcwl_stock_level', true );
			}
		  if ( $previous_stock_level < $stock_level_required ) {
				return true;
			} else {
				return false;
			}
		}
		
		/**
		 * Check to see if product is of type "bundle"
		 *
		 * @param WC_Product $product product object.
		 *
		 * @return bool
		 */
		public static function is_bundle( WC_Product $product ) {
			if ( ! $product ) {
				return false;
			}
			if ( $product->is_type( 'bundle' ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Check to see if product is of type "variable"
		 *
		 * @param WC_Product $product product object.
		 *
		 * @return bool
		 */
		public static function is_variable( WC_Product $product ) {
			if ( ! $product ) {
				return false;
			}
			if ( $product->is_type( 'variable' ) || $product->is_type( 'variable-subscription' ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Check to see if product is of type "variation"
		 *
		 * @param WC_Product $product product object.
		 *
		 * @return bool
		 */
		public static function is_variation( WC_Product $product ) {
			if ( ! $product ) {
				return false;
			}
			if ( $product->is_type( 'variation' ) || $product->is_type( 'subscription_variation' ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Check to see if product is of type "simple"
		 *
		 * @param WC_Product $product product object.
		 *
		 * @return bool
		 */
		public static function is_simple( WC_Product $product ) {
			if ( ! $product ) {
				return false;
			}
			if ( $product->is_type( 'simple' ) || $product->is_type( 'subscription' ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Get the user object, check which products they are on the waitlist for and unregister them from each one when deleted
		 *
		 * @param int $user_id id of the user that is being deleted.
		 *
		 * @access public
		 * @return void
		 * @since  1.3
		 */
		public function unregister_user_when_deleted( $user_id ) {
			$user      = get_user_by( 'id', $user_id );
			$waitlists = self::get_waitlist_products_for_user( $user );
			if ( $user && $waitlists ) {
				foreach ( $waitlists as $product ) {
					if ( $product ) {
						$waitlist = new Pie_WCWL_Waitlist( $product );
						$waitlist->unregister_user( $user->user_email );
					}
				}
			}
			$archives = self::get_waitlist_archives_for_user( $user );
			self::remove_user_from_archives( $archives, $user );
		}

		/**
		 * When a new user registers check waitlists for the email used and adjust this to IDs
		 *
		 * @param int $user_id
		 */
		public function check_new_user_for_waitlist_entries( $user_id ) {
			  $user = get_user_by( 'id', $user_id );
				if ( ! $user ) {
						return;
				}
			  $products = self::get_waitlist_products_for_user( $user );
				foreach ( $products as $product ) {
					if ( ! $product ) {
						continue;
					}
					$waitlist = new Pie_WCWL_Waitlist( $product );
					if ( isset( $waitlist->waitlist[ $user->user_email ] ) ) {
						$waitlist->waitlist[ $user_id ] = $waitlist->waitlist[ $user->user_email ];
						unset( $waitlist->waitlist[ $user->user_email ] );
						asort( $waitlist->waitlist );
						$waitlist->save_waitlist();
					}
				}
		}

		/**
		 * Return all the products that the user is on the waitlist for
		 *
		 * @param object $user user object.
		 *
		 * @return array
		 *
		 * @since  1.6.2
		 */
		public static function get_waitlist_products_for_user( $user ) {
			global $wpdb;
			$statement = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_key = %s AND (meta_value LIKE '%%i:%d;%%' OR meta_value LIKE '%%%s%%')", array( WCWL_SLUG, $user->ID, $user->user_email ) );
			$results = $wpdb->get_results( $statement, OBJECT );
			$results = self::return_products_user_is_registered_on( $results, $user );
			return $results;
		}

		/**
		 * Integrity check on data to ensure users are on the waitlists for the returned products
		 *
		 * @param array  $products products to check.
		 * @param object $user     user object.
		 *
		 * @return array
		 */
		public static function return_products_user_is_registered_on( $products, $user ) {
			$waitlist_products = array();
			foreach ( $products as $product ) {
				$product  = wc_get_product( $product->post_id );
				if ( ! $product ) {
					continue;
				}
				$waitlist = new Pie_WCWL_Waitlist( $product );
				if ( $waitlist->user_is_registered( $user->user_email ) ) {
					$waitlist_products[] = $product;
				}
			}

			return $waitlist_products;
		}

		/**
		 * Return all the products that the user is on a waitlist archive for
		 *
		 * @param object $user user object.
		 *
		 * @return array
		 * @since  1.6.2
		 */
		public static function get_waitlist_archives_for_user( $user ) {
			if ( ! get_option( '_' . WCWL_SLUG . '_metadata_updated' ) ) {
				return array();
			}
			global $wpdb;
			$statement = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_key = 'wcwl_waitlist_archive' AND (meta_value LIKE '%%i:%d;i:%d;%%' OR meta_value LIKE '%%%s%%')", array( $user->ID, $user->ID, $user->user_email ) );

			return $wpdb->get_results( $statement, OBJECT );
		}

		/**
		 * Remove user from all archives
		 * If an admin removes the user they are deleted, if a user removes themselves the User ID is stored as 0 so we can track it
		 *
		 * @param array  $archives archives to check.
		 * @param object $user user object.
		 */
		public static function remove_user_from_archives( $archives, $user ) {
			if ( ! $user || empty( $archives ) ) {
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
						unset( $new_archive[ $timestamp ][ $user->ID ] );
						unset( $new_archive[ $timestamp ][ $user->user_email ] );
					}
				}
				update_post_meta( $product_id, 'wcwl_waitlist_archive', $new_archive );
			}
		}

		/**
		 * Return all product posts
		 *
		 * @return array all product posts.
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
		 * Return all product posts with a waitlist entry in the database
		 *
		 * @return array all product posts.
		 * @since  1.7.0
		 */
		public static function return_all_waitlist_archive_product_ids() {
			global $wpdb;
			$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}postmeta WHERE (meta_key = '" . WCWL_SLUG . "' AND meta_value NOT LIKE '' AND meta_value NOT LIKE 'a:0:{}' ) OR (meta_key = 'wcwl_waitlist_archive' AND meta_value NOT LIKE '' AND meta_value NOT LIKE 'a:0:{}' )", OBJECT );

			$product_ids = array();
			foreach ( $results as $product ) {
				$product_ids[] = intval( $product->post_id );
			}

			return array_unique( $product_ids );
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
		 * Appends our Pie_WCWL_Waitlist_Mailout class to the array of WC_Email objects.
		 *
		 * @static
		 *
		 * @param array $emails the woocommerce array of email objects.
		 *
		 * @access public
		 * @return array         the woocommerce array of email objects with our email appended
		 */
		public static function initialise_waitlist_email_class( $emails ) {
			require_once 'classes/class-pie-wcwl-waitlist-mailout.php';
			require_once 'classes/class-pie-wcwl-waitlist-joined-email.php';
			require_once 'classes/class-pie-wcwl-waitlist-left-email.php';
			require_once 'classes/class-pie-wcwl-waitlist-signup-email.php';
			$emails['Pie_WCWL_Waitlist_Mailout']      = new Pie_WCWL_Waitlist_Mailout();
			$emails['Pie_WCWL_Waitlist_Joined_Email'] = new Pie_WCWL_Waitlist_Joined_Email();
			$emails['Pie_WCWL_Waitlist_Left_Email']   = new Pie_WCWL_Waitlist_Left_Email();
			$emails['Pie_WCWL_Waitlist_Signup_Email'] = new Pie_WCWL_Waitlist_Signup_Email();
			if ( 'yes' === get_option( WCWL_SLUG . '_double_optin' ) ) {
				require_once 'classes/class-pie-wcwl-waitlist-optin-email.php';
				$emails['Pie_WCWL_Waitlist_Optin_Email'] = new Pie_WCWL_Waitlist_Optin_Email();
			}

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
				update_option( 'woocommerce_queue_flush_rewrite_rules', 'true' );
			} else {
				if ( version_compare( $options['version'], '1.7.0' ) < 0 ) {
					update_option( 'woocommerce_queue_flush_rewrite_rules', 'true' );
				}
				if ( version_compare( $options['version'], '2.0.0' ) >= 0 && ! get_option( '_' . WCWL_SLUG . '_version_2_warning' ) ) {
					update_option( '_' . WCWL_SLUG . '_version_2_warning', true );
				}
			}
			// Run any other code required if plugin has been updated
			if ( $options['version'] !== WCWL_VERSION ) {
				$this->template_version_check( true );
			} else {
				$this->template_version_check();
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
			update_option( WCWL_SLUG . '_create_account', 'yes' );
			update_option( WCWL_SLUG . '_auto_login', 'no' );
			update_option( WCWL_SLUG . '_double_optin', 'no' );
			update_option( WCWL_SLUG . '_minimum_stock', 1 );
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
			if ( 'yes' === get_option( 'woocommerce_waitlist_registration_needed' ) ) {
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
		 * @param $product_id product ID.
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
		 * @param $product_id product ID.
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
		 * @param $product_id product ID.
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
		 * @param int    $order_id    order ID.
		 * @param array  $posted_data form data.
		 * @param object $order       order object.
		 *
		 * @access public
		 */
		public function remove_user_from_waitlist_on_product_purchase( $order_id, $posted_data, $order ) {
			foreach ( $order->get_items() as $item ) {
				$product = $item->get_product();
				if ( $product ) {
					$waitlist = new Pie_WCWL_Waitlist( $product );
					$waitlist->unregister_user( $order->get_billing_email() );
				}
			}
		}

		/**
		 * Register any required query variables. Currently, just the account tab endpoint is required
		 *
		 * @param array $vars query variables.
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
		 * @param mixed $links plugin links.
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

		/**
		 * Check waitlist templates in theme for outdated versions
		 *
		 * @return void
		 */
		public function check_templates_after_theme_switch() {
			$this->template_version_check( true );
		}

		/**
		 * Template version check
		 *
		 * @since 2.4.0
		 * @param bool $skip_notice_check "true" will run the check regardless of a missing persisting notice
		 * @return void
		 */
		public function template_version_check( $skip_notice_check = false ) {
			if ( ( ! WC_Admin_Notices::has_notice( 'wcwl_outdated_templates' ) && $skip_notice_check ) ||
					WC_Admin_Notices::has_notice( 'wcwl_outdated_templates' ) ) {
				$plugin_dir = dirname(__FILE__);

				if (!is_dir($plugin_dir)) {
					return;
				}

				$core_templates = WC_Admin_Status::scan_template_files($plugin_dir . '/templates');
				$outdated       = false;

				foreach ($core_templates as $file) {
					$theme_file = false;

					if (file_exists(get_stylesheet_directory() . '/' . $file)) {
						$theme_file = get_stylesheet_directory() . '/' . $file;
					} elseif (file_exists(get_stylesheet_directory() . '/' . WC()->template_path() . $file)) {
						$theme_file = get_stylesheet_directory() . '/' . WC()->template_path() . $file;
					} elseif (file_exists(get_template_directory() . '/' . $file)) {
						$theme_file = get_template_directory() . '/' . $file;
					} elseif (file_exists(get_template_directory() . '/' . WC()->template_path() . $file)) {
						$theme_file = get_template_directory() . '/' . WC()->template_path() . $file;
					}

					if (false !== $theme_file) {
						$core_version  = WC_Admin_Status::get_file_version($plugin_dir . '/templates/' . $file);
						$theme_version = WC_Admin_Status::get_file_version($theme_file);

						if ($core_version && $theme_version && version_compare($theme_version, $core_version, '<')) {
							$outdated = true;
							break;
						}
					}
				}

				if ( $outdated ) {
					if ( ! WC_Admin_Notices::has_notice( 'wcwl_outdated_templates' ) ) {
						WC_Admin_Notices::add_custom_notice( 'wcwl_outdated_templates', $this->template_version_html() );
					}
				} else {
					WC_Admin_Notices::remove_notice( 'wcwl_outdated_templates' );
				}
			}
		}

		/**
		 * Returns HTML for the outdated template version notice
		 *
		 * @since 2.4.0
		 * @return string
		 */
		public function template_version_html()
		{
			ob_start();
			require_once __DIR__ . '/woo-includes/html-notice-template-check.php';
			return ob_get_clean();
		}
	}
}
