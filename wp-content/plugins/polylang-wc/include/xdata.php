<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages cookies transfer when switching from one domain (or subdomain) to another one.
 *
 * @since 0.2
 */
class PLLWC_Xdata {
	/**
	 * Constructor.
	 *
	 * @since 0.2
	 */
	public function __construct() {
		add_filter( 'pll_get_xdata', array( $this, 'get_xdata' ) );
		add_action( 'pll_set_xdata', array( $this, 'set_xdata' ) );
		add_filter( 'pll_xdata_session_manager', array( $this, 'get_session_manager' ) );

		if ( 3 === PLL()->options['force_lang'] ) {
			add_filter( 'woocommerce_set_cookie_options', array( $this, 'set_cookie_options' ), 10, 2 ); // Since WC 6.7.
		}
	}

	/**
	 * Get the cookies to transfer.
	 *
	 * @since 0.3
	 *
	 * @param array $data Data to transfer from one domain to the other.
	 * @return array
	 */
	public function get_xdata( $data ) {
		if ( isset( $_COOKIE['woocommerce_cart_hash'], $_COOKIE['woocommerce_items_in_cart'] ) ) {
			$data['wc'] = array(
				'hash'  => sanitize_key( $_COOKIE['woocommerce_cart_hash'] ),
				'items' => (int) $_COOKIE['woocommerce_items_in_cart'],
			);
		}

		if ( isset( $_COOKIE[ 'wp_woocommerce_session_' . COOKIEHASH ] ) ) {
			$data['wc']['session'] = sanitize_text_field( wp_unslash( $_COOKIE[ 'wp_woocommerce_session_' . COOKIEHASH ] ) );
		}

		if ( isset( $_COOKIE['woocommerce_recently_viewed'] ) ) {
			$data['wc']['views'] = sanitize_text_field( wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) );
		}

		return $data;
	}

	/**
	 * Set the transferred cookies.
	 *
	 * @since 0.3
	 *
	 * @param array $data Transferred data.
	 * @return void
	 */
	public function set_xdata( $data ) {
		if ( ! empty( $data['wc'] ) ) {
			$data = $data['wc'];

			if ( isset( $data['session'] ) ) {
				$session_expiration  = time() + intval( apply_filters( 'wc_session_expiration', 60 * 60 * 48 ) ); // 48 Hours
				$secure = apply_filters( 'wc_session_use_secure_cookie', false );
				wc_setcookie( 'wp_woocommerce_session_' . COOKIEHASH, $data['session'], $session_expiration, $secure );
			}

			if ( isset( $data['hash'] ) ) {
				wc_setcookie( 'woocommerce_cart_hash', $data['hash'] );
				wc_setcookie( 'woocommerce_items_in_cart', $data['items'] );
			}

			if ( isset( $data['views'] ) ) {
				wc_setcookie( 'woocommerce_recently_viewed', $data['views'] );
			}
		}

		// Take the opportunity to reset the shipping methods (needed since WC 2.6).
		WC()->shipping()->calculate_shipping( WC()->cart->get_shipping_packages() );
	}

	/**
	 * Get the session manager class.
	 *
	 * @since 0.3
	 *
	 * @return string Class name.
	 */
	public function get_session_manager() {
		return 'PLLWC_Xdata_Session_Manager';
	}

	/**
	 * Allows crossdomain cookies.
	 *
	 * Requires WC 6.7+, PHP 7.3+ and SSL as the cookie must be secure.
	 *
	 * @since 1.7
	 *
	 * @param array  $options Cookie options.
	 * @param string $name    Cookie name.
	 * @return array
	 */
	public function set_cookie_options( $options, $name ) {
		$cookies = array(
			'wp_woocommerce_session_' . COOKIEHASH,
			'woocommerce_cart_hash',
			'woocommerce_items_in_cart',
			'woocommerce_recently_viewed',
		);

		/**
		 * Filters whether we allow crossdomain cookies.
		 *
		 * @since 1.7
		 *
		 * @param bool   $allow True if we allow crossdomain cookies, false otherwise.
		 * @param string $name  Cookie name.
		 */
		if ( is_ssl() && in_array( $name, $cookies, true ) && apply_filters( 'pllwc_allow_cookie_xdata', true, $name ) ) {
			$options['secure']   = true;
			$options['samesite'] = 'None';
		}

		return $options;
	}
}
