<?php

namespace ACP\ThirdParty\WooCommerce;

use AC\Column;
use AC\ListScreen;
use AC\Registrable;
use ACP\ListScreen\Post;

class Addon implements Registrable {

	public function register() {
		add_filter( 'acp/editing/post_statuses', [ $this, 'remove_woocommerce_statuses_for_editing' ], 10, 2 );
		add_action( 'ac/table/list_screen', [ $this, 'fix_manual_product_sort' ], 12 ); // After Sorting is applied
	}

	public function fix_manual_product_sort( ListScreen $list_screen ) {
		if (
			$list_screen instanceof Post &&
			$list_screen->get_post_type() === 'product' &&
			isset( $_GET['orderby'] ) &&
			strpos( $_GET['orderby'], 'menu_order' ) !== false &&
			! filter_input( INPUT_GET, 'orderby' )
		) {
			unset( $_GET['orderby'] );
		}
	}

	/**
	 * @param array  $statuses
	 * @param Column $column
	 *
	 * @return array
	 */
	public function remove_woocommerce_statuses_for_editing( $statuses, $column ) {
		if ( function_exists( 'wc_get_order_statuses' ) && 'shop_order' !== $column->get_post_type() ) {
			$statuses = array_diff_key( $statuses, wc_get_order_statuses() );
		}

		return $statuses;
	}

}