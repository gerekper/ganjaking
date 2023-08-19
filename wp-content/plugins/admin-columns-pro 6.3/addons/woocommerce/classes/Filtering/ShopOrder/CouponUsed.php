<?php

namespace ACA\WC\Filtering\ShopOrder;

use ACP;

class CouponUsed extends ACP\Filtering\Model\Meta {

	public function get_filtering_data() {
		return [
			'empty_option' => [
				__( 'No' ),
				__( 'Yes' ),
			],
		];
	}

}