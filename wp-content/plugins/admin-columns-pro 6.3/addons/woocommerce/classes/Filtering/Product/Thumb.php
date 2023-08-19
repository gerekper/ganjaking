<?php

namespace ACA\WC\Filtering\Product;

use ACP;

class Thumb extends ACP\Filtering\Model\Meta {

	public function get_filtering_data() {
		return [
			'empty_option' => [
				sprintf( __( 'Without %s', 'codepress-admin-columns' ), __( 'Image', 'codepress-admin-columns' ) ),
				sprintf( __( 'Has %s', 'codepress-admin-columns' ), __( 'Image', 'codepress-admin-columns' ) ),
			],
		];
	}

}