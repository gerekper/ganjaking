<?php
/**
 * Utility functions
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Deposits and Down Payments
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCDP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'yith_wcdp_get_order_subtotal' ) ) {
	/**
	 * Return order subtotal, considering full items prices if some items are deposits
	 *
	 * @param $order_id int Order id
	 *
	 * @return float Order subtotal
	 * @since 1.0.3
	 */
	function yith_wcdp_get_order_subtotal( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return 0;
		}

		$has_deposit = yit_get_prop( $order, '_has_deposit' );

		if ( ! $has_deposit ) {
			return $order->get_subtotal();
		}

		$items = $order->get_items();
		$total = 0;

		if ( ! empty( $items ) ) {
			foreach ( $items as $item ) {
				if ( ! isset( $item['deposit'] ) || ! $item['deposit'] ) {
					$total += $order->get_item_total( $item ) * $item['qty'];
				} else {
					$total += ( $item['deposit_value'] + $item['deposit_balance'] ) * $item['qty'];
				}
			}
		}

		return $total;
	}
}

if ( ! function_exists( 'yith_wcdp_get_cart_subtotal' ) ) {
	/**
	 * Return cart subtotal, considering full items prices if some items are deposits
	 *
	 * @return float Order subtotal
	 * @since 1.0.3
	 */
	function yith_wcdp_get_cart_subtotal() {
		$items = WC()->cart->get_cart();
		$total = 0;

		if ( ! empty( $items ) ) {
			foreach ( $items as $item ) {
				if ( ! isset( $item['deposit'] ) || ! $item['deposit'] ) {
					$total += $item['line_subtotal'];
				} else {
					$total += ( $item['deposit_value'] + $item['deposit_balance'] ) * $item['quantity'];
				}
			}
		}

		return $total;
	}
}

if ( ! function_exists( 'yith_wcdp_locate_template' ) ) {
	/**
	 * Locate template for Deposit plugin
	 *
	 * @param $filename string Template name (with or without extension)
	 * @param $section  string Subdirectory where to search
	 *
	 * @return string Found template
	 */
	function yith_wcdp_locate_template( $filename, $section = '' ) {
		$ext = strpos( $filename, '.php' ) === false ? '.php' : '';

		$template_name = $section . '/' . $filename . $ext;
		$template_path = WC()->template_path() . 'yith-wcdp/';
		$default_path  = YITH_WCDP_DIR . 'templates/';

		if ( defined( 'YITH_WCDP_PREMIUM_INIT' ) ) {
			$premium_template = str_replace( '.php', '-premium.php', $template_name );
			$located_premium  = wc_locate_template( $premium_template, $template_path, $default_path );
			$template_name    = file_exists( $located_premium ) ? $premium_template : $template_name;
		}

		return wc_locate_template( $template_name, $template_path, $default_path );
	}
}

if ( ! function_exists( 'yith_wcdp_get_template' ) ) {
	/**
	 * Get template for Affiliate plugin
	 *
	 * @param $filename string Template name (with or without extension)
	 * @param $args     mixed Array of params to use in the template
	 * @param $section  string Subdirectory where to search
	 */
	function yith_wcdp_get_template( $filename, $args = array(), $section = '' ) {
		$ext = strpos( $filename, '.php' ) === false ? '.php' : '';

		$template_name = $section . '/' . $filename . $ext;
		$template_path = WC()->template_path() . 'yith-wcdp/';
		$default_path  = YITH_WCDP_DIR . 'templates/';

		if ( defined( 'YITH_WCDP_PREMIUM_INIT' ) ) {
			$premium_template = str_replace( '.php', '-premium.php', $template_name );
			$located_premium  = wc_locate_template( $premium_template, $template_path, $default_path );
			$template_name    = file_exists( $located_premium ) ? $premium_template : $template_name;
		}

		wc_get_template( $template_name, $args, $template_path, $default_path );
	}
}

if ( ! function_exists( 'yith_wcdp_get_price_to_display' ) ) {
	/**
	 * Wraps functionality of wc_get_price_to_display, for older versions of WooCommerce
	 *
	 * @param $product \WC_Product Product to use during calculation
	 * @param $args    array Array of arguments used in function; use the same logic of second param of wc_get_price_to_display
	 *
	 * @since 1.1.2
	 */
	function yith_wcdp_get_price_to_display( $product, $args = array() ) {
		$args = wp_parse_args( $args, array(
			'qty'   => 1,
			'price' => $product->get_price(),
			'order' => null
		) );

		$price = $args['price'];
		$qty   = $args['qty'];
		$order = $args['order'];

		$show_including_tax = ( 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ( is_shop() || is_product() || is_product_taxonomy() ) ) || ( 'incl' === get_option( 'woocommerce_tax_display_cart' ) && ( is_cart() || is_checkout() || $order ) );

		if ( version_compare( WC()->version, '3.0.0', '<' ) ) {
			if ( $show_including_tax ) {
				return $product->get_price_including_tax( $qty, $price );
			} else {
				return $product->get_price_excluding_tax( $qty, $price );
			}
		} else {
			if ( $show_including_tax ) {
				return wc_get_price_including_tax( $product, array( 'qty' => $qty, 'price' => $price ) );
			} else {
				return wc_get_price_excluding_tax( $product, array( 'qty' => $qty, 'price' => $price ) );
			}
		}
	}
}