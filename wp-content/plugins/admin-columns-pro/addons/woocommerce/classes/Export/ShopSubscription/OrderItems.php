<?php

namespace ACA\WC\Export\ShopSubscription;

use ACP;

/**
 * @since 3.4
 */
class OrderItems extends ACP\Export\Model {

	public function get_value( $id ) {
		return wcs_get_subscription( $id )->get_status();
	}

}