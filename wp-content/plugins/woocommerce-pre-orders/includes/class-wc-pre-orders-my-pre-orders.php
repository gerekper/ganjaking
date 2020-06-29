<?php
/**
 * WooCommerce Pre-Orders
 *
 * @package   WC_Pre_Orders/My_Pre_Orders
 * @author    WooThemes
 * @copyright Copyright (c) 2015, WooThemes
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) )  {
	exit;
}

/**
 * My Pre-Orders class
 *
 * @since 1.4.4
 */
class WC_Pre_Orders_My_Pre_Orders {

	/**
	 * Adds needed hooks / filters
	 */
	public function __construct() {
		// New endpoint for pre-orders.
		add_action( 'init', array( $this, 'add_endpoint' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );

		// Change page title.
		add_filter( 'the_title', array( $this, 'endpoint_title' ) );

		// Insert Pre-Orders menu in My Account menus.
		add_filter( 'woocommerce_account_menu_items', array( $this, 'menu_items' ) );
		add_action( 'woocommerce_account_pre-orders_endpoint', array( $this, 'my_pre_orders' ) );

		// Support WC < 2.6, display pre-orders in my-account.
		add_action( 'woocommerce_before_my_account', array( $this, 'my_pre_orders_legacy' ) );
	}

	/**
	 * Register new endpoint to use inside My Account page.
	 *
	 * @since 1.4.7
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
	 */
	public function add_endpoint() {
		add_rewrite_endpoint( 'pre-orders', EP_ROOT | EP_PAGES );
	}

	/**
	 * Add pre-orders query var.
	 *
	 * @since 1.4.7
	 *
	 * @param array $vars Query vars
	 *
	 * @return array altered query vars
	 */
	public function add_query_vars( $vars  ) {
		$vars[] = 'pre-orders';
		return $vars;
	}

	/**
	 * Change title for pre-orders endpoint.
	 *
	 * @since 1.4.7
	 *
	 * @param string $title Page title
	 *
	 * @return string Page title
	 */
	public function endpoint_title( $title ) {
		if ( $this->is_pre_orders_endpoint() ) {
			$title = __( 'Pre-Orders', 'wc-pre-orders' );
			remove_filter( 'the_title', array( $this, 'endpoint_title' ) );
		}

		return $title;
	}

	/**
	 * Checks if current page is pre-orders endpoint.
	 *
	 * @since 1.4.7
	 *
	 * @return bool Returns true if current page is pre-orders endpoint
	 */
	public function is_pre_orders_endpoint() {
		global $wp_query;

		return ( isset( $wp_query->query_vars['pre-orders'] )
			&& ! is_admin()
			&& is_main_query()
			&& in_the_loop()
			&& is_account_page()
		);
	}

	/**
	 * Insert Pre-Ordres menu into My Account menus.
	 *
	 * @since 1.4.7
	 *
	 * @param array $items Menu items
	 *
	 * @return array Menu items
	 */
	public function menu_items( $items ) {
		// Insert Pre-Orders menu.
		$new_items               = array();
		$new_items['pre-orders'] = __( 'Pre-Orders', 'wc-pre-orders' );

		return $this->_insert_new_items_after( $items, $new_items, 'dashboard' );
	}

	/**
	 * Helper to add new items into an array after a selected item.
	 *
	 * @since 1.4.7
	 *
	 * @param array  $items     Menu items
	 * @param array  $new_items New menu items
	 * @param string $after     Key in items
	 *
	 * @return array Menu items
	 */
	protected function _insert_new_items_after( $items, $new_items, $after ) {
		// Search for the item position and +1 since is after the selected item key.
		$position = array_search( $after, array_keys( $items ) ) + 1;

		// Insert the new item.
		$array = array_slice( $items, 0, $position, true );
		$array += $new_items;
		$array += array_slice( $items, $position, count( $items ) - $position, true );

		return $array;
	}

	/**
	 * Output "My Pre-Orders" table in the user's My Account page
	 */
	public function my_pre_orders() {
		global $wc_pre_orders;

		$pre_orders = WC_Pre_Orders_Manager::get_users_pre_orders();
		$items      = array();
		$actions    = array();

		foreach ( $pre_orders as $order ) {
			$_actions   = array();
			$order_item = WC_Pre_Orders_Order::get_pre_order_item( $order );

			// Stop if the pre-order is complete
			if ( is_null( $order_item ) ) {
				continue;
			}

			// Set the items for the table
			$items[] = array(
				'order' => $order,
				'data'  => $order_item
			);

			// Determine the available actions (Cancel)
			if ( WC_Pre_Orders_Manager::can_pre_order_be_changed_to( 'cancelled', $order ) ) {
				$_actions['cancel'] = array(
					'url'  => WC_Pre_Orders_Manager::get_users_change_status_link( 'cancelled', $order ),
					'name' => __( 'Cancel', 'wc-pre-orders' )
				);
			}

			$actions[ version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id() ] = $_actions;
		}

		// Load the template
		wc_get_template(
			'myaccount/my-pre-orders.php',
			array(
				'show_title' => version_compare( WC()->version, '2.6', '<' ),
				'pre_orders' => $pre_orders,
				'items'      => $items,
				'actions'    => $actions,
			),
			'',
			$wc_pre_orders->get_plugin_path() . '/templates/'
		);
	}

	/**
	 * Display pre-orders in My Account page.
	 *
	 * Hooked to woocommerce_before_my_account and only affect WC < 2.6.
	 *
	 * @since 1.4.7
	 */
	public function my_pre_orders_legacy() {
		if ( version_compare( WC()->version, '2.6', '<' ) ) {
			$this->my_pre_orders();
		}
	}
}

new WC_Pre_Orders_My_Pre_Orders();
