<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that handles the display of scheduled orders in My Account.
 */
class WC_Deposits_My_Account {
	/**
	 * Custom endpoint name.
	 *
	 * @var string
	 */
	public static $endpoint;

	/**
	 * Plugin actions.
	 */
	public function __construct() {
		self::$endpoint = apply_filters( 'woocoommerce_deposits_my_account_end_point', 'scheduled-orders' );

		// Actions used to insert a new endpoint in the WordPress.
		add_action( 'init', array( $this, 'add_endpoints' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );

		// Change the My Account page title.
		add_filter( 'the_title', array( $this, 'endpoint_title' ) );

		// Insering your new tab/page into the My Account page.
		add_filter( 'woocommerce_account_menu_items', array( $this, 'maybe_add_new_menu_items' ) );
		add_action( 'woocommerce_account_' . self::$endpoint .  '_endpoint', array( $this, 'endpoint_content' ) );
		add_action( 'woocommerce_order_details_after_order_table', array( $this, 'add_original_order_link' ) );
		add_action( 'woocommerce_order_details_after_order_table', array( $this, 'render_related_scheduled_orders' ) );
	}

	/**
	 * Register new endpoint to use inside My Account page.
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
	 */
	public function add_endpoints() {
		add_rewrite_endpoint( self::$endpoint, EP_ROOT | EP_PAGES );
	}

	/**
	 * Add new query var.
	 *
	 * @param array $vars
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		$vars[] = self::$endpoint;

		return $vars;
	}

	/**
	 * Set endpoint title.
	 *
	 * @param string $title
	 * @return string
	 */
	public function endpoint_title( $title ) {
		global $wp_query;

		$is_endpoint = isset( $wp_query->query_vars[ self::$endpoint ] );

		if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
			// New page title.
			$title = __( 'Scheduled Orders', 'woocommerce-deposits' );

			remove_filter( 'the_title', array( $this, 'endpoint_title' ) );
		}

		return $title;
	}

	/**
	 * If the customer has scheduled orders, add new Scheduled Orders menu to My Account menu.
	 * @since 1.4.20
	 * @param array $items
	 * @return array
	 */
	public function maybe_add_new_menu_items( $items ) {
		$customer_orders = wc_get_orders( apply_filters( 'woocommerce_deposits_my_account_query', array( 'customer' => get_current_user_id(), 'post_status' =>  array( 'wc-scheduled-payment' ) ) ) );
		if ( ! $customer_orders ) {
			return $items;
		}
		return $this->new_menu_items( $items );
	}

	/**
	 * Insert the new endpoint into the My Account menu.
	 *
	 * @param array $items
	 * @return array
	 */
	public function new_menu_items( $items ) {
		$rebuilt_menu = array();

		// Rebuilt the array to position our menu item after orders.
		foreach ( $items as $key => $value ) {
			if ( 'orders' === $key ) {
				$rebuilt_menu[ $key ] = $value;
				$rebuilt_menu[ self::$endpoint ] = __( 'Scheduled Orders', 'woocommerce-deposits' );
			} else {
				$rebuilt_menu[ $key ] = $value;
			}
		}

		return $rebuilt_menu;
	}

	/**
	 * Endpoint HTML content.
	 */
	public function endpoint_content( $current_page ) {
		$current_page    = empty( $current_page ) ? 1 : absint( $current_page );
		$customer_orders = wc_get_orders( apply_filters( 'woocommerce_deposits_my_account_query', array( 'customer' => get_current_user_id(), 'page' => $current_page, 'paginate' => true, 'post_status' =>  array( 'wc-scheduled-payment' ) ) ) );

		add_filter( 'woocommerce_get_endpoint_url', array( __CLASS__, 'set_pagination_endpoint_url' ), 10, 3 );
		wc_get_template(
			'myaccount/orders.php',
			array(
				'current_page' => absint( $current_page ),
				'customer_orders' => $customer_orders,
				'has_orders' => 0 < $customer_orders->total,
			)
		);
		remove_filter( 'woocommerce_get_endpoint_url', array( __CLASS__, 'set_pagination_endpoint_url' ), 10, 3 );
	}

	/**
	 * Adds a link to the original order from within a scheduled
	 * order.
	 * My Account > Orders > (Any scheduled order).
	 *
	 * @param WC_Order $order A scheduled order object.
	 */
	public function add_original_order_link( $order ) {

		/**
		 * Return if the page is not the /view-order/ page.
		 */
		if ( ! is_wc_endpoint_url( 'view-order' ) ) {
			return;
		}

		$original_order_id = $order->get_parent_id();

		if ( ! $original_order_id ) {
			return;
		}

		$original_order     = wc_get_order( $original_order_id );
		$original_order_url = $original_order->get_view_order_url();

		printf(
			'<div class="wc-deposits-order-details__view-original-order-link"><a href="%s">%s</a></div>',
			esc_url( $original_order_url ),
			esc_html__( 'View Original Order', 'woocommerce-deposits' )
		);
	}

	/**
	 * Renders a table of all scheduled orders for a given order.
	 * My Account > Orders > (Any original order).
	 *
	 * @param WC_Order $order The Original Order object.
	 */
	public function render_related_scheduled_orders( $order ) {

		/**
		 * Return if the page is not the /view-order/ page.
		 */
		if ( ! is_wc_endpoint_url( 'view-order' ) ) {
			return;
		}

		/**
		 * The current page of this table.
		 */
		$current_page = (int)filter_input( INPUT_GET, 'scheduled_orders_page', FILTER_SANITIZE_NUMBER_INT );

		/**
		 * Array of all custom orders except for the original order.
		 */
		$customer_orders = wc_get_orders(
			array(
				'customer'    => get_current_user_id(),
				'paginate'    => true,
				'post_parent' => $order->get_id(),
				'paged'       => $current_page,
				'order'       => 'ASC',
			)
		);

		if ( ! $customer_orders->total ) {
			return;
		}

		printf(
			'<h4 class="wc-deposits-order-details__scheduled-orders-title">%s #%s</h4>',
			esc_html__( 'Scheduled orders for order', 'woocommerce-deposits' ),
			esc_html( $order->get_id() )
		);

		/**
		 * Custom template similar to myaccount/orders.php used to
		 * render the table.
		 */
		wc_get_template(
			'scheduled-orders.php',
			array(
				'customer_orders' => $customer_orders,
				'has_orders'      => 0 < $customer_orders->total,
				'current_page'    => empty( $current_page ) ? 1 : $current_page,
			),
			'',
			WC_DEPOSITS_TEMPLATE_PATH
		);
	}

	/**
	 * Set pagination endpoint.
	 */
	static function set_pagination_endpoint_url( $url, $endpoint, $value ) {

		if ( 'orders' === $endpoint ) {
			$url = wc_get_endpoint_url( self::$endpoint, $value );
		}

		return $url;
	}

}

new WC_Deposits_My_Account();
