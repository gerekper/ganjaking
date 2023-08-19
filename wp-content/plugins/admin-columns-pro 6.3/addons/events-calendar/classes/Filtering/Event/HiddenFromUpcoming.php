<?php

namespace ACA\EC\Filtering\Event;

use ACA\EC\Filtering;

class HiddenFromUpcoming extends Filtering\Toggle {

	public function get_on_value() {
		return 'yes';
	}

	public function get_filtering_data() {
		return [
			'options' => [
				'yes' => __( 'Hidden from Event Listing', 'codepress-admin-columns' ),
				'no'  => __( 'Visible on Event Listing', 'codepress-admin-columns' ),
			],
		];
	}

}