<?php
/**
 * Backport fix for WC 2.6
 */


class WC_Ajax_Compat extends WC_AJAX {

	/**
	 * Get WC Ajax Endpoint.
	 *
	 * @param  string $request Optional.
	 * @return string
	 */
	public static function get_endpoint( $request = '' ) {
		return esc_url_raw( apply_filters( 'woocommerce_ajax_get_endpoint', add_query_arg( 'wc-ajax', $request, remove_query_arg( array( 'remove_item', 'add-to-cart', 'added-to-cart', 'order_again', '_wpnonce' ), home_url( '/', 'relative' ) ) ), $request ) );
	}
}
