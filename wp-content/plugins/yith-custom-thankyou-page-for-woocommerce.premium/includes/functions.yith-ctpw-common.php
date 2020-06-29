<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH Custom ThankYou Page for Woocommerce
 */

if ( ! function_exists( 'yith_ctpw_list_all_pages' ) ) {

	/**
	 * List all WordPress pages
	 *
	 * @return array ( ID => Page_title )
	 * @since  1.0.0
	 * @author Armando Liccardo <armando.liccardo@yithemes.com>
	 */
	function yith_ctpw_list_all_pages() {

		// get woocommerce pages ids to exclude them from list
		// ADD_FILTER: yctpw_avoid_pages: remove pages from pages list: default pages removed are woocommerce pages.
		$avoid_pages = apply_filters(
			'yctpw_avoid_pages',
			array(
				get_option( 'woocommerce_checkout_page_id' ),
				get_option( 'woocommerce_cart_page_id' ),
				get_option( 'woocommerce_shop_page_id' ),
				get_option( 'woocommerce_myaccount_page_id' ),
			)
		);

		$args    = array(
			'sort_order'   => 'asc',
			'sort_column'  => 'post_title',
			'hierarchical' => 1,
			'exclude'      => $avoid_pages,
			'include'      => '',
			'authors'      => '',
			'child_of'     => 0,
			'parent'       => - 1,
			'exclude_tree' => '',
			'number'       => '',
			'offset'       => 0,
			'post_type'    => 'page',
			'post_status'  => 'publish',
		);
		$pages   = get_pages( $args );
		$l_pages = array();
		foreach ( $pages as $page ) {
			$l_pages[ $page->ID ] = $page->post_title;
		}

		return $l_pages;
	}
}


if ( ! function_exists( 'yith_ctpw_get_edit_page_url' ) ) {
	/**
	 * Add an edit link on Selected Thank you page on admin side
	 *
	 * @return void
	 * @author Armando Liccardo <armando.liccardo@yithemes.com>
	 * @since 1.0.4
	 */
	function yith_ctpw_get_edit_page_url() {
		$result = false;
		if ( isset( $_POST['ctpw_id'] ) ) { //phpcs:ignore
			if ( '' !== $_POST['ctpw_id'] && 0 !== $_POST['ctpw_id'] ) {//phpcs:ignore
				$result = get_edit_post_link( sanitize_key( $_POST['ctpw_id'] ) ); //phpcs:ignore
			}
		}
		echo $result; //phpcs:ignore
		wp_die();
	}

	/* add an edit link on Selected Thank you page on admin side */
	add_action( 'wp_ajax_yith_ctpw_get_edit_page_url', 'yith_ctpw_get_edit_page_url' );
}


if ( ! function_exists( 'yith_ctpw_get_available_order_to_preview' ) ) {
	/**
	 * Get a Random Woocommerce Completed Order to use in ThankYou Page Preview
	 *
	 * @return WC_Order/boolean
	 * @author Armando Liccardo <armando.liccardo@yithemes.com>
	 * @since 1.1.6
	 */
	function yith_ctpw_get_available_order_to_preview() {
		// APPLY_FILTERS: yctpw_get_available_order_args: change the args to get the random order to use in Preview.
		$defaults = apply_filters(
			'yctpw_get_available_order_args',
			array(
				'type'    => 'shop_order',
				'status'  => 'completed',
				'limit'   => 1,
				'orderby' => 'rand',
			)
		);

		$o = false;

		$orders = wc_get_orders( $defaults );
		if ( count( $orders ) > 0 ) {
			$o = $orders[0];
		}

		// APPLY_FILTERS: yctpw_order_to_preview: change the order object to use in Preview.
		return apply_filters( 'yctpw_order_to_preview', $o );

	}
}


if ( ! function_exists( 'yith_ctpw_get_url_order_args' ) ) {
	/**
	 * Get url args to create a view or preview url for a page
	 *
	 * @param WC_Order   $order .
	 * @param string|int $page_id the id of the page for which to create the url.
	 *
	 * @return array
	 * @author Armando Liccardo <armando.liccardo@yithemes.com>
	 * @since 1.1.8
	 */
	function yith_ctpw_get_url_order_args( $order, $page_id = '' ) {
		$url_args = false;
		if ( $order ) {

			$url_args = array(
				'order-received' => $order->get_id(),
				'key'            => $order->get_order_key(),
			);

			if ( ! empty( $page_id ) ) {
				$url_args['ctpw'] = $page_id;
			}
		}

		return $url_args;

	}
}