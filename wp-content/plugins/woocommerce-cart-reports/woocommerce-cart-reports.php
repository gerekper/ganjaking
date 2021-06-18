<?php
/**
 * WooCommerce Cart Reports
 *
 * @package           woocommerce-cart-reports
 * @author            WP BackOffice
 * @copyright         WooCommerce
 * @license           GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: Cart Reports for WooCommerce
 * Plugin URI: https://woocommerce.com/products/woocommerce-cart-reports/
 * Description: Cart Reports for WooCommerce allows site admins to keep track of Abandoned, Open, and Converted Carts.
 * Version: 1.2.10
 *
 * Developer: WP BackOffice
 * Developer URI: https://wpbackoffice.com
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 *
 * Text Domain: woocommerce-cart-reports
 * Domain Path: /languages
 *
 * Woo: 184638:3920e2541c6030c45f6ac8ccb967d9d5
 *
 * WC requires at least: 3.0.0
 * WC tested up to: 4.0.0
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once  'woo-includes/woo-functions.php' ;
}

define( 'CONVERTED', 'Converted' );
define( 'ABANDONED', 'Abandoned' );
define( 'OPEN', 'Open' );
define( 'ONEDAY', 86400 );

if ( is_woocommerce_active() ) {

	/**
	 * Localisation
	 **/
	load_plugin_textdomain( 'woocommerce_cart_reports', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	include plugin_dir_path( __FILE__ ) . '/models/AV8_Cart_Actions.php';
	include plugin_dir_path( __FILE__ ) . '/models/AV8_Cart_Receipt.php';
	include plugin_dir_path( __FILE__ ) . '/admin/cart_index_interface.php';
	include plugin_dir_path( __FILE__ ) . '/admin/cart_edit_interface.php';
	include plugin_dir_path( __FILE__ ) . '/admin/cart_reports_settings.php';
	include plugin_dir_path( __FILE__ ) . '/admin/cart_reports_dashboard.php';
	include plugin_dir_path( __FILE__ ) . '/admin/cart_reports_page.php';
	include plugin_dir_path( __FILE__ ) . '/includes/helpers.php';

	//Add our Custom Cart Post type

	add_action( 'init', 'create_custom_tax', 0 ); //Init, create custom stuff first

	/*
	* We need a custom taxonomy for "Cart Status." You'll only find 2 terms here - Open
	* and Converted. Abandoned status is determined on the fly :\ with a custom 'post_where'
	* filter hook.
	*/

	function create_custom_tax() {
		register_taxonomy( 'shop_cart_status', array( 'carts' ), array(
			'hierarchical'          => false,
			'update_count_callback' => '_update_post_term_count',
			'labels'                => array(
				'name'              => __( 'Cart statuses', 'woocommerce' ),
				'singular_name'     => __( 'Cart status', 'woocommerce' ),
				'search_items'      => __( 'Search Cart statuses', 'woocommerce' ),
				'all_items'         => __( 'All Cart statuses', 'woocommerce' ),
				'parent_item'       => __( 'Parent Cart status', 'woocommerce' ),
				'parent_item_colon' => __( 'Parent Cart status:', 'woocommerce' ),
				'edit_item'         => __( 'Edit Cart status', 'woocommerce' ),
				'update_item'       => __( 'Update Cart status', 'woocommerce' ),
				'add_new_item'      => __( 'Add New Cart status', 'woocommerce' ),
				'new_item_name'     => __( 'New Cart status Name', 'woocommerce' )
			),
			'show_in_nav_menus'     => false,
			'public'                => false,
			'show_ui'               => false,
			'query_var'             => is_admin(),
			'rewrite'               => false,
		) );

		$cart_status = array( 'open', 'converted' );

		foreach ( $cart_status as $status ) {
			if ( ! get_term_by( 'slug', sanitize_title( $status ), 'shop_cart_status' ) ) {
				wp_insert_term( $status, 'shop_cart_status' );
			}
		}
	}

	/**
	 * Register our shiny new post type for "Carts"
	 *
	 */
	function cart_add_type_init() {
		register_post_type( 'carts', array(
			'label'               => 'Carts',
			'description'         => '',
			'public'              => false,
			'show_ui'             => true,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'rewrite'             => array( 'slug' => '' ),
			'query_var'           => true,
			'supports'            => array( 'title', 'author' ),
			'labels'              => array(
				'name'               => 'Carts',
				'singular_name'      => 'Cart',
				'menu_name'          => 'Carts',
				'add_new'            => 'Add Cart',
				'add_new_item'       => '',
				'edit'               => 'Edit',
				'edit_item'          => 'Cart Details',
				'new_item'           => 'New Cart',
				'view'               => 'View Cart',
				'view_item'          => 'View Cart',
				'search_items'       => 'Search Carts',
				'not_found'          => 'No Carts Found',
				'not_found_in_trash' => 'No Carts Found in Trash',
				'parent'             => 'Parent Cart',

			),
			'exclude_from_search' => true,
			'show_in_menu'        => 'woocommerce',
			'show_in_nav_menus'   => true,
			'capabilities'        => array(
				'create_posts' => false,
			),
			'map_meta_cap'        => true,
		) );
	}

	// Styling for the custom post type icon

	add_action( 'init', 'cart_add_type_init' );

	if ( ! function_exists( 'is_woocommerce_active' ) ) {
		require_once  'woo-includes/woo-functions.php' ;
	}

	register_activation_hook( __FILE__, 'woocommerce_abandoned_carts_activate' );
	register_deactivation_hook( __FILE__, 'woocommerce_abandoned_carts_deactivate' );

	/**
	 * Activate the function with some default options
	 */
	function woocommerce_abandoned_carts_activate() {

		//Check first to see if we need to upgrade

		global $wpdb;

		$check_sql       = 'SELECT meta_value FROM ' . $wpdb->prefix . "postmeta WHERE meta_key = 'av8_cartitems'";
		$upgrade_needed  = false;
		$check_meta_vals = $wpdb->get_results( $check_sql );
		foreach ( $check_meta_vals as $check_meta_val ) :
			if ( strpos( $check_meta_val->meta_value, 'WC_Product' ) ) :
				$upgrade_needed = true;
			endif;
		endforeach;

		if ( $upgrade_needed ) {
			//Upgrade needed.
			$check_sql = 'SELECT * from ' . $wpdb->prefix . "postmeta WHERE meta_key = 'av8_cartitems'";

			$meta_vals = $wpdb->get_results( $check_sql );
			$counter   = 0;
			foreach ( $meta_vals as $meta_key ) :
				$new_meta_value = str_replace( 'O:10:"WC_Product"', 'O:8:"stdclass"', $meta_key->meta_value );
				$upgrade_sql    = 'UPDATE ' . $wpdb->prefix . "postmeta SET meta_value = '" . $new_meta_value . "'WHERE meta_id = '" . $meta_key->meta_id . "' AND meta_key = '" . $meta_key->meta_key . "'";
				$wpdb->query( $upgrade_sql );
				$counter ++;
			endforeach;
		}
	}

	/**
	 * Deactivate the plugin - cleanup the options
	 */
	function woocommerce_abandoned_carts_deactivate() {
	}


	/**
	 * Delete Cart Data
	 */
	function woocommerce_abandoned_carts_delete( $older_than_in_days = false ) {
		global $wpdb;

		$sql = 'SELECT * FROM ' . $wpdb->prefix . "posts WHERE post_type = 'carts'";

		if ( $older_than_in_days ) {
			$sql .= " AND post_date < DATE_SUB(CURDATE(),INTERVAL $older_than_in_days DAY)";
		}

		$result = $wpdb->get_results( $sql );

		foreach ( $result as $cart ) {
			$delete_meta_sql = 'DELETE FROM ' . $wpdb->prefix . "postmeta WHERE post_id = '" . $cart->ID . "'";
			$wpdb->query( $delete_meta_sql );
			$delete_sql = 'DELETE FROM ' . $wpdb->prefix . "posts WHERE ID = '" . $cart->ID . "'";
			$wpdb->query( $delete_sql );
		}
	}

	class AV8_Cart_Reports {
		public $existing_id;
		public $receipt;

		public function __construct() {
			global $wpdb;
			global $woocommerce_cart_reports_options;

			/* Timeout */

			$woocommerce_cart_reports_options['timeout'] = get_option( 'wc_cart_reports_timeout' );

			add_action( 'woocommerce_cart_reset', array( $this, 'woocommerce_scheduled_cart_reset' ) );
			/* Product Index */

			$productsindex = get_option( 'wc_cart_reports_productsindex' );
			if ( $productsindex == 'yes' ) {
				$woocommerce_cart_reports_options['productsindex'] = true;
			} else {
				$woocommerce_cart_reports_options['productsindex'] = false;
			}

			/* Tracked Roles */

			$trackedroles = get_option( 'wc_cart_reports_trackedroles' );
			if ( is_array( $trackedroles ) && ! empty( $trackedroles ) ) {
				$woocommerce_cart_reports_options['trackedroles'] = $trackedroles;
			} else {
				$woocommerce_cart_reports_options['trackedroles'] = false;
			}

			/* Log IP */

			$logips = get_option( 'wc_cart_reports_logip' );

			if ( $logips == 'yes' ) {
				$woocommerce_cart_reports_options['logip'] = true;
			} else {
				$woocommerce_cart_reports_options['logip'] = false;
			}

			/* Cart Expiration Opt-In Checkbox*/

			$wc_cart_reports_expiration_opt_in = get_option( 'wc_cart_reports_expiration_opt_in' );
			if ( $wc_cart_reports_expiration_opt_in == 'yes' ) {
				$woocommerce_cart_reports_options['wc_cart_reports_expiration_opt_in'] = true;
			} else {
				$woocommerce_cart_reports_options['wc_cart_reports_expiration_opt_in'] = false;
			}

			/* Cart Expiration Day range */

			$cart_expiration_opt_in = get_option( 'wc_cart_reports_expiration_opt_in' );
			$cart_expiration        = get_option( 'wc_cart_reports_expiration' );

			if ( $cart_expiration > 0 && $cart_expiration_opt_in != 'no' ) {
				if ( ! wp_next_scheduled( 'woocommerce_cart_reset' ) ) {
					wp_schedule_event( time(), 'hourly', 'woocommerce_cart_reset' );
				}
				$woocommerce_cart_reports_options['wc_cart_reports_expiration'] = $cart_expiration;
			} else {
				if ( wp_next_scheduled( 'woocommerce_cart_reset' ) ) {
					$timestamp = wp_next_scheduled( 'woocommerce_cart_reset' );
					wp_unschedule_event( $timestamp, 'woocommerce_cart_reset' );
				}
				$woocommerce_cart_reports_options['wc_cart_reports_expiration'] = false;
			}

			/* Dashboard Range */

			$woocommerce_cart_reports_options['dashboardrange'] = get_option( 'wc_cart_reports_dashboardrange' );
			if ( ! is_numeric( (int) $woocommerce_cart_reports_options['dashboardrange'] ) || $woocommerce_cart_reports_options['dashboardrange'] < 1 ) {
				$woocommerce_cart_reports_options['dashboardrange'] = 2;
			}
			if ( ! is_numeric( (int) $woocommerce_cart_reports_options['timeout'] ) || $woocommerce_cart_reports_options['timeout'] < 1 ) {
				$woocommerce_cart_reports_options['timeout'] = 1200;
			}

			$woocommerce_cart_reports_options['timeout'] = (int) $woocommerce_cart_reports_options['timeout'];

			if ( function_exists( 'get_product' ) && defined( 'COOKIEVALUE' ) ) {
				$session = COOKIEVALUE;
			} else {
				$session = session_id();
			}

			$this->receipt = new AV8_Cart_Receipt( $session );

			add_action( 'woocommerce_checkout_update_order_review', array( $this, 'save_from_ajax' ) );
			add_action( 'woocommerce_cart_updated', array( $this, 'save_receipt' ) );
			add_action( 'woocommerce_created_customer', array( $this, 'save_user_id' ) );
			add_action( 'woocommerce_new_order', array( $this, 'save_order_id' ) );

			if ( is_admin() ) {
				$Edit_Interface = new AV8_Edit_Interface();
				$Cart_Index     = new AV8_Cart_Index_Page();
				$Settings_Page  = new AV8_Cart_Reports_Settings();
				$Dashboard_Page = new AV8_Cart_Dashboard();
				$Reports        = new AV8_Cart_Reports_Page();
			}
		}

		/* Function to periodically clear old carts, if this is configured in the settings. */

		public function woocommerce_scheduled_cart_reset() {
			global $woocommerce_cart_reports_options;
			$expiration_days = $woocommerce_cart_reports_options['wc_cart_reports_expiration'];
			$opt_in_settings = $woocommerce_cart_reports_options['wc_cart_reports_expiration_opt_in'];
			if ( $expiration_days && $expiration_days > 0 && $opt_in_settings ) {
				woocommerce_abandoned_carts_delete( $expiration_days );
			}
		}

		/**
		 * This is the main routine that acts when the visitor makes a change to their cart.
		 * First we save the user id and useragent info (if the option is set to "on") Next we
		 * populate the receipt object with the products, owner (if exists) and session id.
		 */
		public function save_receipt() {
			global $woocommerce_cart_reports_options, $woocommerce;

			$user_agent = ! empty( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';

			if ( isset( $_SERVER ) && isset( $woocommerce_cart_reports_options['logip'] ) && $woocommerce_cart_reports_options['logip'] == 'on' ) {
				$this->ip_address = $_SERVER['REMOTE_ADDR'];
				$this->user_agent = $user_agent;
			}

			//Get current user, generate full name for use later
			$person = get_current_user_id(); //$person is '' if guest

			if ( function_exists( 'get_product' ) ) {
				$session = COOKIEVALUE;
			} else {
				$session = session_id();
			}

			// Don't save if is a search engine
			if ( ! cr_detect_search_engines( $user_agent ) && ! is_restricted_role() ) {
				$receipt = new AV8_Cart_Receipt( $session );
				$receipt->set_owner( $person );
				$receipt->set_products( $woocommerce ); //Grab products from woocommerce global object
				$receipt->save_receipt(); //Save the object to the database
			}
		}

		/**
		 * Hooks into the woo action for conversions. This function tells the model that it's now
		 * a converted cart and should act as such
		 */


		public function save_from_ajax( $data ) {
			global $woocommerce_cart_reports_options;
			global $current_user;
			global $options;
			global $woocommerce;
			if ( isset( $_SERVER ) && isset( $woocommerce_cart_reports_options['logip'] ) && $woocommerce_cart_reports_options['logip'] == 'on' ) {
				$this->ip_address = $_SERVER['SERVER_ADDR'];
				$this->user_agent = ! empty( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
			}

			parse_str( $data, $data_array );

			$billing_first_name = ( isset( $data_array['billing_first_name'] ) ) ? $data_array['billing_first_name'] : '';

			$billing_last_name = ( isset( $data_array['billing_last_name'] ) ) ? $data_array['billing_last_name'] : '';
			$billing_company   = ( isset( $data_array['billing_company'] ) ) ? $data_array['billing_company'] : '';
			$billing_address_1 = ( isset( $data_array['billing_address_1'] ) ) ? $data_array['billing_address_1'] : '';
			$billing_address_2 = ( isset( $data_array['billing_address_2'] ) ) ? $data_array['billing_address_2'] : '';
			$billing_city      = ( isset( $data_array['billing_city'] ) ) ? $data_array['billing_city'] : '';
			$billing_state     = ( isset( $data_array['billing_state'] ) ) ? $data_array['billing_state'] : '';
			$billing_zip       = ( isset( $data_array['billing_zip'] ) ) ? $data_array['billing_zip'] : '';
			$billing_phone     = ( isset( $data_array['billing_phone'] ) ) ? $data_array['billing_phone'] : '';
			$billing_email     = ( isset( $data_array['billing_email'] ) ) ? $data_array['billing_email'] : 'test@test.com';

			$save_arr = array(
				'billing_first_name' => $billing_first_name,
				'billing_last_name'  => $billing_last_name,
				'billing_company'    => $billing_company,
				'billing_address_1'  => $billing_address_1,
				'billing_address_2'  => $billing_address_2,
				'billing_city'       => $billing_city,
				'billing_state'      => $billing_state,
				'billing_zip'        => $billing_zip,
				'billing_phone'      => $billing_phone,
				'billing_email'      => $billing_email
			);


			if ( function_exists( 'get_product' ) ) {
				$session = COOKIEVALUE;
			} else {
				$session = session_id();
			}


			$receipt = new AV8_Cart_Receipt( $session );
			$id      = $receipt->get_id_from_session( $session );
			if ( $id > 0 && $id != '' ) {
				update_post_meta( $id, '_customer_data', $save_arr );
			}

			//FIND current ticket id, if it exists...
		}

		/**
		 * If the user selected "create account" on the checkout page,we send the user id info to the model for saving.
		 *
		 */

		public function save_user_id( $user_id ) {
			if ( function_exists( 'get_product' ) ) {
				$session = COOKIEVALUE;
			} else {
				$session = session_id();
			}
			if ( WP_DEBUG == true ) {
				assert( is_numeric( $user_id ) );
			}

			if ( ! is_restricted_role() ) :
				$post_id = $this->receipt->get_id_from_session( $session );
				$this->receipt->save_user_id( $user_id, $post_id );
			endif;
		}

		/**
		 *
		 * Save the order id of the newly created order in the post meta of the cart object
		 */
		public function save_order_id( $order_id ) {
			if ( function_exists( 'get_product' ) ) {
				$session = COOKIEVALUE;
			} else {
				$session = session_id();
			}
			if ( WP_DEBUG == true ) {
				assert( $order_id > 0 );
			}

			//$customer_id = get_post_meta($order_id, '_customer_user', true);

			if ( ! is_restricted_role() ) :

				$receipt = new AV8_Cart_Receipt( $session );
				$id      = $receipt->get_id_from_session( $session );

				$receipt->save_conversion();

				$receipt->save_order_id( $order_id );

			endif;
		}
	}//END CLASS

	//Functions used to compute number of items

	/**
	 * Generate at-a-glance stats for the dashboard widget. Generates both ranged-induced
	 * and lifetime values and returns an array.
	 */
	function av8_woocommerce_cart_numbers( $range = false ) {
		//meta_value is the cart type you'd like to count
		global $wpdb;

		$args = array(
			'numberposts'      => - 1,
			'offset'           => 0,
			'orderby'          => 'post_date',
			'order'            => 'DESC',
			'post_type'        => 'carts',
			'post_status'      => 'publish',
			'suppress_filters' => false,

			'tax_query' => array(
				array(
					'taxonomy' => 'shop_cart_status',
					'terms'    => 'open',
					'field'    => 'slug',
					'operator' => 'IN'
				)
			)
		);

		if ( $range ) {
			add_filter( 'posts_where', 'dashboard_stats_where_abandoned_range' );
		} else {
			add_filter( 'posts_where', 'dashboard_stats_where_abandoned_lifetime' );
		}
		$abandoned = count( get_posts( $args ) );
		if ( $range ) {
			remove_filter( 'posts_where', 'dashboard_stats_where_abandoned_range' );
		} else {
			remove_filter( 'posts_where', 'dashboard_stats_where_abandoned_lifetime' );
		}

		$args = array(
			'numberposts'      => - 1,
			'offset'           => 0,
			'orderby'          => 'post_date',
			'order'            => 'DESC',
			'post_type'        => 'carts',
			'post_status'      => 'publish',
			'suppress_filters' => false,

			'tax_query' => array(
				array(
					'taxonomy' => 'shop_cart_status',
					'terms'    => 'open',
					'field'    => 'slug',
					'operator' => 'IN'
				)
			)

		);

		add_filter( 'posts_where', 'dashboard_stats_where_open' );
		$open = count( get_posts( $args ) );
		remove_filter( 'posts_where', 'dashboard_stats_where_open' );

		$args = array(
			'numberposts'      => - 1,
			'offset'           => 0,
			'orderby'          => 'post_date',
			'order'            => 'DESC',
			'post_type'        => 'carts',
			'post_status'      => 'publish',
			'suppress_filters' => false,

			'tax_query' => array(
				array(
					'taxonomy' => 'shop_cart_status',
					'terms'    => 'converted',
					'field'    => 'slug',
					'operator' => 'IN'
				)
			)
		);

		if ( $range ) {
			add_filter( 'posts_where', 'dashboard_stats_where_converted' );
		}
		$converted = count( get_posts( $args ) );
		if ( $range ) {
			remove_filter( 'posts_where', 'dashboard_stats_where_converted' );
		}

		$vals = array( 'Converted' => $converted, 'Abandoned' => $abandoned, 'Open' => $open );

		return $vals;
	}

	/*
	*  Filter for the range-induced abandoned section on the dashboard
	*/
	function dashboard_stats_where_abandoned_range( $where ) {
		global $woocommerce_cart_reports_options;
		global $offset;
		if ( WP_DEBUG == true ) {
			assert( is_numeric( $offset ) );
		}
		$where .= " AND post_modified > '" . date(
				'Y-m-d G:i:s',
				time() + ( $offset * 3600 ) - ( $woocommerce_cart_reports_options['dashboardrange'] * 24 * 60 * 60 )
			);

		$where .= "' and post_modified < '" . date(
				'Y-m-d G:i:s',
				time() + ( $offset * 3600 ) - $woocommerce_cart_reports_options['timeout']
			) . "' ";

		return $where;
	}

	/*
	 *  Filter for the abandoned lifetime fields on the dashboard widget
	 */
	function dashboard_stats_where_abandoned_lifetime( $where ) {
		global $woocommerce_cart_reports_options;
		global $offset;
		if ( WP_DEBUG == true ) {
			assert( is_numeric( $offset ) );
		}
		$where .= " AND post_modified < '" . date(
				'Y-m-d G:i:s',
				time() + ( $offset * 3600 ) - $woocommerce_cart_reports_options['timeout']
			) . "' ";

		return $where;
	}

	/*
	 * wp filter for the open filter on the index page
	 */
	function dashboard_stats_where_open( $where ) {
		global $woocommerce_cart_reports_options;
		global $offset;
		if ( WP_DEBUG == true ) {
			assert( is_numeric( $offset ) );
		}
		$where .= " AND post_modified > '" . date(
				'Y-m-d G:i:s',
				time() + ( $offset * 3600 ) - $woocommerce_cart_reports_options['timeout']
			) . "' ";

		return $where;
	}

	/**
	 *wp filter for the converted filter on the index page
	 */
	function dashboard_stats_where_converted( $where ) {
		global $woocommerce_cart_reports_options;
		global $offset;
		if ( WP_DEBUG == true ) {
			assert( is_numeric( $offset ) );
		}
		$where .= " AND post_modified > '" . date(
				'Y-m-d G:i:s',
				time() + ( $offset * 3600 ) - ( $woocommerce_cart_reports_options['dashboardrange'] * 60 * 60 * 24 )
			) . "' ";

		return $where;
	}


	$WooCommerce_Cart_Reports = new AV8_Cart_Reports(); //Instantiate!!!
} //ENDCLASS

/**
 * Print out tool tip code, input contains desired text, requires
 */
function av8_tooltip( $text, $print = true ) {
	global $woocommerce;
	$disp = '<img class="help_tip" data-tip="' . $text . '" src="' . $woocommerce->plugin_url(
		) . '/assets/images/help.png" />';
	if ( $print ) {
		echo $disp;
	} else {
		return $disp;
	}
}

function woocommerce_json_search_customers_carts() {
	check_ajax_referer( 'search-customers', 'security' );

	$term = urldecode( stripslashes( strip_tags( $_GET['term'] ) ) );

	if ( empty( $term ) ) {
		die();
	}

	$default = isset( $_GET['default'] ) ? $_GET['default'] : __( 'Guest', 'woocommerce' );

	$found_customers = array( '' => $default );

	$customers_query = new WP_User_Query(
		array(
			'fields' => 'all',
			'orderby' => 'display_name',
			'search' => '*' . $term . '*',
			'search_columns' => array( 'ID', 'user_login', 'user_email', 'user_nicename' )
		)
	);

	$customers = $customers_query->get_results();

	if ( $customers ) {
		foreach ( $customers as $customer ) {
			$found_customers[ $customer->ID ] = $customer->display_name . ' (#' . $customer->ID . ' &ndash; ' . $customer->user_email . ')';
		}
	}

	echo json_encode( $found_customers );
	die();
}

//add_action('wp_ajax_woocommerce_json_search_customers', 'woocommerce_json_search_customers_carts');

add_action( 'init', 'load_cookie_carts', 0 );

function load_cookie_carts() {
	if ( ! defined( 'COOKIEVALUE' ) ) {
		define( 'COOKIEVALUE', get_session_cookie_carts() );
	}
}

function get_session_cookie_carts() {
	if ( class_exists( 'WC_Session' ) ) {
		if ( function_exists( 'WC' ) ) {
			$cookieid = 'wp_woocommerce_session_' . COOKIEHASH;
		} else {
			$cookieid = 'wc_session_cookie_' . COOKIEHASH;
		}

		if ( isset( $_COOKIE[ $cookieid ] ) && $_COOKIE[ $cookieid ] != false ) {
			list( $customer_id, $session_expiration, $session_expiring, $cookie_hash ) = explode(
				'||',
				$_COOKIE[ $cookieid ]
			);
			$customer_id = $customer_id;

			return $customer_id;
		}
	}

	return false;
}

function is_restricted_role() {
	global $current_user;
	global $woocommerce_cart_reports_options;

	$excluded_roles = $woocommerce_cart_reports_options['trackedroles'];


	if ( is_user_logged_in() ) :
		$user       = new WP_User( $current_user->ID );
		$user_roles = $user->roles;
		$current_u  = array_shift( $user_roles );
	else :
		$current_u = 'guest';
	endif;


	if ( isset( $excluded_roles ) && ! empty( $excluded_roles ) ) {
		foreach ( $excluded_roles as $excluded_role ) :
			if ( $current_u == $excluded_role ) {
				return true;
			}
		endforeach;
	}

	return false;
}


/* Clear Carts Settings */

$plugin = plugin_basename( __FILE__ );

add_filter( "plugin_action_links_$plugin", 'woocommerce_cart_reports_settings_link' );

function woocommerce_cart_reports_settings_link( $links ) {
	$timestamp     = time();
	$settings_link = "<a class='clear-link' " . 'onclick=" return confirm(\'Are you sure you want to delete ALL Cart Reports Data? This includes all carts in the database. Settings will not be affected.\')"
' . "href='?timestamp=" . $timestamp . '&_wpnonce=' . wp_create_nonce(
			'trash-the-carts'
		) . "&cart-action=clear'>Clear Carts</a>";
	$links[]       = $settings_link;

	return $links;
}


add_action( 'admin_init', 'clear_all_carts' );

function clear_all_carts() {
	if ( isset( $_GET['timestamp'] ) && $_GET['timestamp'] && isset( $_GET['cart-action'] ) && $_GET['cart-action'] == 'clear' && current_user_can(
			'delete_plugins'
		) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'trash-the-carts' ) ) :
		$current_stamp = time();
		if ( isset( $_GET['timestamp'] ) && $_GET['timestamp'] && $_GET['timestamp'] > $current_stamp - ( 60 * 60 * 24 ) ) {
			woocommerce_abandoned_carts_delete();
			add_action( 'admin_notices', 'clear_carts_admin_notice' );
		} else {
			add_action( 'admin_notices', 'clear_carts_timeout_notice' );
		}
	endif;
}

function clear_carts_admin_notice() {
	echo '<div class="updated">
       <p>' . __( 'Cart Data Cleared', 'woocommerce_cart_reports' ) . '</p>
    </div>';
}

function clear_carts_timeout_notice() {
	echo '<div class="updated">
       <p>' . __( 'Timeout occured, please try again.', 'woocommerce_cart_reports' ) . '</p>
    </div>';
}
