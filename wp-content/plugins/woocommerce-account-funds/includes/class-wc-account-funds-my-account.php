<?php
/**
 * Class to customize the 'My Account' page.
 *
 * @package WC_Account_Funds
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Account_Funds_My_Account.
 */
class WC_Account_Funds_My_Account {

	/**
	 * Top-up deposits.
	 *
	 * @var array
	 */
	private $deposits = null;

	/**
	 * Top-up items in cart.
	 *
	 * @var array
	 */
	private $topup_in_cart = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Register the endpoint, even on the admin pages.
		add_filter( 'woocommerce_get_query_vars', array( $this, 'add_query_vars' ) );
		add_filter( 'woocommerce_endpoint_account-funds_title', array( $this, 'change_endpoint_title' ) );

		if ( ! wc_account_funds_is_request( 'frontend' ) ) {
			return;
		}

		// Adds the tab/page into the My Account page.
		add_filter( 'woocommerce_account_menu_items', array( $this, 'add_menu_items' ) );
		add_action( 'woocommerce_account_account-funds_endpoint', array( $this, 'endpoint_content' ) );

		// Account Funds tab data.
		add_action( 'woocommerce_account_funds_content', array( $this, 'account_funds_content' ) );
		add_action( 'woocommerce_account_funds_recent_deposit_items_data', array( $this, 'recent_deposit_items_data' ) );

		add_action( 'wp', array( $this, 'topup_handler' ) );
	}

	/**
	 * Adds endpoint into query vars.
	 *
	 * @since 2.2.0
	 *
	 * @param array $vars Query vars.
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		$vars['account-funds'] = 'account-funds';

		return $vars;
	}

	/**
	 * Changes the page title on account funds page.
	 *
	 * @since 2.0.12
	 *
	 * @param string $title Endpoint title.
	 * @return string
	 */
	public function change_endpoint_title( $title ) {
		return __( 'Account Funds', 'woocommerce-account-funds' );
	}

	/**
	 * Insert the new endpoint into the My Account menu.
	 *
	 * @since 2.0.12
	 *
	 * @param array $menu_items Menu items.
	 * @return array
	 */
	public function add_menu_items( $menu_items ) {
		// Try inserting after orders.
		$key_to_add   = 'account-funds';
		$value_to_add = __( 'Account Funds', 'woocommerce-account-funds' );

		$index_for_adding = array_search( 'orders', array_keys( $menu_items ), true );

		if ( false === $index_for_adding ) {
			$menu_items[ $key_to_add ] = $value_to_add;
		} else {
			$index_for_adding++;
			$menu_items = array_merge(
				array_slice( $menu_items, 0, intval( $index_for_adding ) ),
				array( $key_to_add => $value_to_add ),
				array_slice( $menu_items, $index_for_adding )
			);
		}

		return $menu_items;
	}

	/**
	 * Endpoint HTML content.
	 *
	 * @since 2.0.12
	 */
	public function endpoint_content() {
		wc_account_funds_get_template( 'myaccount/account-funds.php' );
	}

	/**
	 * Outputs the account funds content.
	 *
	 * @since 2.2.0
	 */
	public function account_funds_content() {
		if ( 'yes' === get_option( 'account_funds_enable_topup' ) ) {
			$this->my_account_topup();
		} else {
			$this->my_account_products();
		}

		$deposits = $this->get_deposits();

		if ( ! empty( $deposits ) ) {
			wc_account_funds_get_template( 'myaccount/recent-deposits.php' );
		}
	}

	/**
	 * Handle top-ups
	 */
	public function topup_handler() {
		if ( isset( $_POST['wc_account_funds_topup'] ) && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( wc_clean( wp_unslash( $_POST['_wpnonce'] ) ), 'account-funds-topup' ) ) {
			$min          = max( 1, get_option( 'account_funds_min_topup' ) );
			$max          = get_option( 'account_funds_max_topup' );
			$topup_amount = ( isset( $_POST['topup_amount'] ) ? wc_clean( wp_unslash( $_POST['topup_amount'] ) ) : 1 );

			if ( $topup_amount < $min ) {
				/* translators: %s: minimum Top-Up amount */
				wc_add_notice( sprintf( __( 'The minimum amount that can be topped up is %s', 'woocommerce-account-funds' ), wc_price( $min ) ), 'error' );
				return;
			} elseif ( $max && $topup_amount > $max ) {
				/* translators: %s: maximum Top-Up amount */
				wc_add_notice( sprintf( __( 'The maximum amount that can be topped up is %s', 'woocommerce-account-funds' ), wc_price( $max ) ), 'error' );
				return;
			}

			WC()->cart->add_to_cart( wc_get_page_id( 'myaccount' ), true, '', '', array( 'top_up_amount' => $topup_amount ) );

			if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				wp_safe_redirect( get_permalink( wc_get_page_id( 'cart' ) ) );
			}
		}
	}

	/**
	 * Show top up form
	 */
	public function my_account_topup() {
		$max_topup     = get_option( 'account_funds_max_topup' );
		$topup_in_cart = $this->get_topup_in_cart();

		if ( '' !== $max_topup && isset( $topup_in_cart['data'] ) ) {
			$vars = array( 'topup_title_in_cart' => $topup_in_cart['data']->get_title() );
			wc_account_funds_get_template( 'myaccount/account-funds/topup-in-cart-notice.php', $vars );
		} else {
			$vars = array(
				'min_topup' => max( 1, (int) get_option( 'account_funds_min_topup' ) ),
				'max_topup' => $max_topup,
			);

			wc_account_funds_get_template( 'myaccount/topup-form.php', $vars );
		}
	}

	/**
	 * Get top-up items in cart.
	 *
	 * @since 2.0.6
	 *
	 * @return array
	 */
	private function _get_topup_items_in_cart() {
		$topup_items = array();

		if ( WC()->cart instanceof WC_Cart && ! WC()->cart->is_empty() ) {
			$topup_items = array_filter( WC()->cart->get_cart(), array( $this, 'filter_topup_items' ) );
		}

		return $topup_items;
	}

	/**
	 * Cart items filter callback to filter top-up product.
	 *
	 * @since 2.0.6
	 *
	 * @param array $item Cart item.
	 * @return bool Returns true if the cart item is a top-up product. False otherwise.
	 */
	public function filter_topup_items( $item ) {
		if ( isset( $item['data'] ) && is_callable( array( $item['data'], 'get_type' ) ) ) {
			return ( 'topup' === $item['data']->get_type() );
		}

		return false;
	}

	/**
	 * Show top up products
	 */
	private function my_account_products() {
		$product_ids = wc_get_products(
			array(
				'return' => 'ids',
				'type'   => 'deposit',
			)
		);

		if ( $product_ids ) {
			echo do_shortcode( '[products ids="' . implode( ',', $product_ids ) . '"]' );
		}
	}

	/**
	 * Get deposits data
	 *
	 * @since 2.2.0
	 *
	 * @return array
	 */
	private function get_deposits() {
		if ( is_null( $this->deposits ) ) {
			$this->deposits = wc_get_orders(
				array(
					'type'        => 'shop_order',
					'limit'       => 10,
					'status'      => array( 'wc-completed', 'wc-processing', 'wc-on-hold' ),
					'customer_id' => get_current_user_id(),
					'funds_query' => array(
						array(
							'key'   => '_funds_deposited',
							'value' => '1',
						),
					),
				)
			);
		}

		return $this->deposits;
	}

	/**
	 * Get top-up in cart
	 *
	 * @since 2.2.0
	 *
	 * @return array|null
	 */
	private function get_topup_in_cart() {
		if ( count( $this->topup_in_cart ) < 1 ) {
			$items_in_cart = $this->_get_topup_items_in_cart();

			$this->topup_in_cart = array_shift( $items_in_cart );
		}

		return $this->topup_in_cart;
	}

	/**
	 * Get HTML string of recent deposits items.
	 *
	 * @since 2.2.0
	 */
	public function recent_deposit_items_data() {
		foreach ( $this->get_deposits() as $deposit ) {
			$funded = 0;

			foreach ( $deposit->get_items() as $item ) {
				$product = $item->get_product();

				if ( ! $product ) {
					continue;
				}

				if ( $product->is_type( 'deposit' ) || $product->is_type( 'topup' ) ) {
					$funded += $deposit->get_line_total( $item );
				}
			}

			$vars = array(
				'deposit' => array(
					'funded'            => $funded,
					'order_date'        => ( $deposit->get_date_created() ? gmdate( 'Y-m-d H:i:s', $deposit->get_date_created()->getOffsetTimestamp() ) : '' ),
					'order_url'         => $deposit->get_view_order_url(),
					'order_number'      => $deposit->get_order_number(),
					'order_status_name' => wc_get_order_status_name( $deposit->get_status() ),
				),
			);

			wc_account_funds_get_template( 'myaccount/account-funds/deposit-item-data.php', $vars );
		}
	}

	/**
	 * Adds endpoint breadcrumb when viewing account funds.
	 *
	 * @since 2.0.12
	 * @deprecated 2.2.0
	 *
	 * @param array $crumbs Already assembled breadcrumb data.
	 * @return array $crumbs if we're on a account funds page, then augmented breadcrumb data
	 */
	public function add_breadcrumb( $crumbs ) {
		wc_deprecated_function( __FUNCTION__, '2.2' );

		return $crumbs;
	}
}

new WC_Account_Funds_My_Account();
