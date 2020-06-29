<?php

namespace ACP\Filtering\Model\User;

use ACP\Filtering\Model;

class ShowToolbar extends Model {

	public function get_filtering_vars( $vars ) {
		$vars['meta_query'][] = [
			[
				'key'   => 'show_admin_bar_front',
				'value' => '1' === $this->get_filter_value() ? 'true' : 'false',
			],
		];

		return $vars;
	}

	public function get_filtering_data() {
		return [
			'options' => [
				0 => __( 'No' ),
				1 => __( 'Yes' ),
			],
		];
	}

}