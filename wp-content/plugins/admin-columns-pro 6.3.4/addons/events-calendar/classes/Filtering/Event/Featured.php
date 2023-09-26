<?php

namespace ACA\EC\Filtering\Event;

use ACA\EC\Filtering;

class Featured extends Filtering\Toggle {

	public function get_on_value() {
		return '1';
	}

	public function get_filtering_data() {
		return [
			'options' => [
				1 => __( 'Featured', 'codepress-admin-columns' ),
				0 => __( 'Not Featured', 'codepress-admin-columns' ),
			],
		];
	}

}