<?php
/**
 * Help Scout Integration.
 *
 * @package WC_Help_Scout_Integration
 * @author  Automattic/WooCommerce
 *
 * @since 1.3.0
 */

class WC_Help_Scout_My_Account extends WC_Integration {

	/**
	 * Endpoint slug.
	 *
	 * @var string
	 */
	private $_endpoint = 'support-conversations';

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Customer "My Orders" actions.
		add_action( 'woocommerce_view_order', array( $this, 'view_order_create_conversation' ), 40 );
		add_action( 'woocommerce_my_account_my_orders_actions', array( $this, 'orders_actions' ), 10, 2 );
		add_action( 'woocommerce_after_my_account', array( $this, 'my_account_conversations_table' ), 10 );

		// Tabbed My Account in WC 2.6+.
		add_action( 'init', array( $this, 'my_support_conversations_endpoint' ) );
		add_filter( 'query_vars', array( $this, 'my_support_conversations_query_vars' ), 0 );
		add_filter( 'woocommerce_account_menu_items', array( $this, 'my_support_conversations_menu_items' ) );
		add_action( 'woocommerce_account_support-conversations_endpoint', array( $this, 'my_support_conversations_content' ) );
		add_filter( 'the_title', array( $this, 'my_support_conversations_title' ) );
	}

	/**
	 * Create conversation form.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return void
	 */
	public function view_order_create_conversation( $order_id ) {
		$vars = array(
			'order_id' => $order_id,
		);
		$default_path = WC_Help_Scout::get_instance()->plugin_path() . '/templates/';
		wc_get_template( 'order/create-conversation.php', $vars, 'woocommerce-help-scout', $default_path );
	}

	/**
	 * Added support button in order actions.
	 *
	 * @param  array    $actions Order actions.
	 * @param  WC_Order $order   Order data.
	 *
	 * @return array
	 */
	public function orders_actions( $actions, $order ) {
		$order_url = $order->get_view_order_url();

		$actions[ $this->id ] = array(
			'url'  => $order_url . '#start-conversation',
			'name' => __( 'Get Help', 'woocommerce-help-scout' )
		);

		return $actions;
	}

	/**
	 * Display a table with the user conversations in My Account page.
	 *
	 * Hooked into `woocommerce_after_my_account` to provide backward compatibility
	 * for WC < 2.6. Won't output anything if WC >= 2.6.
	 *
	 * @return string Tickets table.
	 */
	public function my_account_conversations_table() {
		if ( version_compare( WC()->version, '2.6', '<' ) ) {
			echo $this->get_my_conversations( array(
				'show_title'   => true,
				'endpoint_url' => get_permalink( wc_get_page_id( 'myaccount' ) ),
			) );
		}
	}

	/**
	 * Get my conversations table.
	 *
	 * @param array $args Arguments to alter output
	 *
	 * @return string HTML of my conversations table
	 */
	public function get_my_conversations( $args = array() ) {
		global $current_user;

		$args = wp_parse_args(
			$args,
			array(
				'show_title'   => false,
				'endpoint_url' => wc_get_endpoint_url( $this->_endpoint ),
			)
		);

		$integration = WC_Help_Scout::get_integration_instance();
		$customer_id = $integration->get_customer_id( $current_user->ID, $current_user->user_email );

		// Navigation.
		$current_page = ( isset( $_GET['support_page'] ) && $_GET['support_page'] > 0 ) ? intval( $_GET['support_page'] ) : 1;
		$next_page    = add_query_arg( array( 'support_page' => $current_page + 1 ), $args['endpoint_url'] );
		$last_page    = add_query_arg( array( 'support_page' => $current_page - 1 ), $args['endpoint_url'] );

		$vars = array_merge(
			$args,
			array(
				'conversations' => $integration->get_customer_conversations( $customer_id, $current_page ),
				'date_format'   => wc_date_format(),
				'integration'   => $integration,
				'current_page'  => $current_page,
				'next_page'     => $next_page,
				'last_page'     => $last_page,
			)
		);

		$default_path = WC_Help_Scout::get_instance()->plugin_path() . '/templates/';

		return wc_get_template_html( 'myaccount/conversations.php', $vars, 'woocommerce-help-scout', $default_path );
	}

	/**
	 * Register new endpoint to use inside My Account page.
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
	 */
	public function my_support_conversations_endpoint() {
		add_rewrite_endpoint( $this->_endpoint, EP_ROOT | EP_PAGES );
	}

	/**
	 * Add new query var.
	 *
	 * @param array $vars Query vars
	 *
	 * @return array Altered query vars
	 */
	public function my_support_conversations_query_vars( $vars ) {
		$vars[] = $this->_endpoint;

		return $vars;
	}

	/**
	 * Insert the new endpoint into the My Account menu.
	 *
	 * @param array $items Menu items
	 *
	 * @return array Altered menu items
	 */
	public function my_support_conversations_menu_items( $items ) {
		$new_items = array();
		$new_items[ $this->_endpoint ] = __( 'Support Conversations', 'woocommerce-help-scout' );

		// Add the new item after `orders`.
		return $this->_insert_menu_items_after( $items, $new_items, 'orders' );
	}

	/**
	 * Custom helper to add new items into an array after a selected item.
	 *
	 * @param array $items     Original items
	 * @param array $new_items New items to add
	 * @param string $after    Insert new items after this menu item
	 *
	 * @return array Altered menu items
	 */
	private function _insert_menu_items_after( $items, $new_items, $after ) {
		// Search for the item position and +1 since is after the selected item key.
		$position = array_search( $after, array_keys( $items ) ) + 1;

		// Insert the new item.
		$array = array_slice( $items, 0, $position, true );
		$array += $new_items;
		$array += array_slice( $items, $position, count( $items ) - $position, true );

		return $array;
	}

	/**
	 * Endpoint HTML content.
	 *
	 * @return void
	 */
	public function my_support_conversations_content() {
		echo $this->get_my_conversations();
	}

	/**
	 * Change endpoint title.
	 *
	 * @param string $title Original title
	 *
	 * @return string Altered title
	 */
	public function my_support_conversations_title( $title ) {
		global $wp_query;

		$is_endpoint = isset( $wp_query->query_vars[ $this->_endpoint ] );

		if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
			// New page title.
			$title = __( 'My Support Conversations', 'woocommerce-help-scout' );

			remove_filter( 'the_title', array( $this, 'my_support_conversations_title' ) );
		}

		return $title;
	}
}
