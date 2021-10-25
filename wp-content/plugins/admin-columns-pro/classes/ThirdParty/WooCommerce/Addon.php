<?php

namespace ACP\ThirdParty\WooCommerce;

use AC\Column;
use AC\Registrable;

class Addon implements Registrable {

	public function register() {
		add_filter( 'acp/editing/post_statuses', [ $this, 'remove_woocommerce_statuses_for_editing' ], 10, 2 );
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