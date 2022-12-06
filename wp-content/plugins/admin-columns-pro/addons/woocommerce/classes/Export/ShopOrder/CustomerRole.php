<?php

namespace ACA\WC\Export\ShopOrder;

use ACP;

/**
 * WooCommerce order customer role (default column) exportability model
 * @since 2.2.1
 */
class CustomerRole extends ACP\Export\Model {

	public function get_value( $id ) {
		$user = wc_get_order( $id )->get_user();

		if ( empty( $user->roles ) ) {
			return '';
		}

		return implode( ', ', $user->roles );
	}

}