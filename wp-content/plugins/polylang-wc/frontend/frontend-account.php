<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages the translation of the customer account.
 *
 * @since 1.0
 */
class PLLWC_Frontend_Account {

	/**
	 * Constructor.
	 * Setups actions and filters.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'woocommerce_account_content', array( $this, 'add_language_filter_before_account_orders' ), -100000 );
		add_action( 'woocommerce_account_content', array( $this, 'remove_language_filter_after_account_orders' ), 100000 );
		add_action( 'parse_query', array( $this, 'parse_query' ), 3 ); // Before Polylang (for orders).
		add_filter( 'woocommerce_order_item_name', array( $this, 'order_item_name' ), 10, 3 );
		add_filter( 'woocommerce_get_order_item_totals', array( $this, 'translate_payment_method' ), 10, 2 );
	}

	/**
	 * In frontend, forces the queries in the "My account => Orders" tab to fetch orders in all languages by adding
	 * a filter (refunds, etc). This is used when HPOS is enabled because `WP_Query` is not used in this context (so
	 * {@see PLLWC_Frontend_Account::parse_query()} has no effects).
	 * Hooked to `woocommerce_account_content` at very low priority.
	 *
	 * @since 1.9
	 *
	 * @return void
	 */
	public function add_language_filter_before_account_orders() {
		add_filter( 'woocommerce_order_query_args', array( $this, 'add_language_query_arg_in_account_orders' ) );
	}

	/**
	 * Removes the filter added by {@see PLLWC_Frontend_Account::add_language_filter_before_account_orders()}.
	 * Hooked to woocommerce_account_content` at very high priority.
	 *
	 * @since 1.9
	 *
	 * @return void
	 */
	public function remove_language_filter_after_account_orders() {
		remove_filter( 'woocommerce_order_query_args', array( $this, 'add_language_query_arg_in_account_orders' ) );
	}

	/**
	 * In frontend, forces the "My account => Orders" tab to display orders in all languages.
	 * Hooked to `woocommerce_order_query_args` in {@see PLLWC_Frontend_Account::add_language_filter_before_account_orders()}.
	 *
	 * @since 1.9
	 *
	 * @param array $query The query array.
	 * @return array
	 */
	public function add_language_query_arg_in_account_orders( $query ) {
		$query['lang'] = '';
		return $query;
	}

	/**
	 * Disables the languages filter for a customer to see all orders whatever the languages.
	 * Hooked to the action 'parse_query'.
	 *
	 * @since 0.3
	 *
	 * @param WP_Query $query WP_Query object.
	 * @return void
	 */
	public function parse_query( $query ) {
		$qvars = $query->query_vars;

		// Customers should see all their orders whatever the language.
		if ( ! isset( $qvars['lang'] ) && ( isset( $qvars['post_type'] ) && ( 'shop_order' === $qvars['post_type'] || ( is_array( $qvars['post_type'] ) && in_array( 'shop_order', $qvars['post_type'] ) ) ) ) ) {
			$query->set( 'lang', '' );
		}
	}

	/**
	 * Translates the product name in the current language.
	 * Hooked the filter 'woocommerce_order_item_name'.
	 *
	 * @since 1.0
	 *
	 * @param string                $item_name  Product name.
	 * @param WC_Order_Item_Product $item       Order item.
	 * @param bool                  $is_visible Whether the product is visible.
	 * @return string Translated product name.
	 */
	public function order_item_name( $item_name, $item, $is_visible ) {
		/** @var PLLWC_Product_Language_CPT */
		$data_store = PLLWC_Data_Store::load( 'product_language' );

		$product_id = $item->get_variation_id();
		if ( ! $product_id ) {
			$product_id = $item->get_product_id();
		}

		$tr_id = $data_store->get( $product_id );

		if ( $tr_id && $tr_id !== $product_id && $product = wc_get_product( $tr_id ) ) {
			if ( $is_visible ) {
				$item_name = sprintf( '<a href="%s">%s</a>', esc_url( $product->get_permalink() ), $product->get_name() );
			} else {
				$item_name = $product->get_name();
			}
		}

		return $item_name;
	}

	/**
	 * Translates the payment method in the order item totals.
	 * Hooked to the filter 'woocommerce_get_order_item_totals'.
	 *
	 * @since 1.0
	 *
	 * @param string[][] $rows  Order item totals.
	 * @param WC_Order   $order Order.
	 * @return string[][]
	 */
	public function translate_payment_method( $rows, $order ) {
		if ( method_exists( $order, 'get_payment_method' ) ) {
			$payment_method = $order->get_payment_method();
			$gateways       = WC_Payment_Gateways::instance()->payment_gateways();
			if ( isset( $gateways[ $payment_method ] ) && ! empty( $rows['payment_method'] ) ) {
				// Check $rows['payment_method'] for an issue (where the payment method was not set) introduced in WC 7.7.0 and fixed in 7.8 {@see https://github.com/polylang/polylang-wc/issues/629}.
				$rows['payment_method']['value'] = $gateways[ $payment_method ]->get_title();
			}
		}
		return $rows;
	}
}
