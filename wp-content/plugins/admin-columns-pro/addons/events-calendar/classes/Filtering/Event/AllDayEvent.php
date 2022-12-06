<?php

namespace ACA\EC\Filtering\Event;

use ACA\EC\Filtering;

class AllDayEvent extends Filtering\Toggle {

	public function get_on_value() {
		return 'yes';
	}

	public function get_filtering_data() {
		return [
			'options' => [
				'yes' => __( 'All Day Events', 'codepress-admin-columns' ),
				0     => __( 'Not All Day Events', 'codepress-admin-columns' ),
			],
		];
	}

}