<?php

namespace ACA\WC\Export\ShopSubscription;

use ACP;

class Status implements ACP\Export\Service {

	public function get_value( $id ) {
		return wcs_get_subscription( $id )->get_status();
	}

}