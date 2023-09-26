<?php

namespace ACA\WC\Filtering\ShopCoupon;

use ACP;

class FreeShipping extends ACP\Filtering\Model\Meta {

	public function get_filtering_data() {
		return [
			'options' => [
				'no'  => __( 'No' ),
				'yes' => __( 'Yes' ),
			],
		];
	}

}