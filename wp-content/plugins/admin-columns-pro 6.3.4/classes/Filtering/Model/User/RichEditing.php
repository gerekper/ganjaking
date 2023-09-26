<?php

namespace ACP\Filtering\Model\User;

use ACP\Filtering\Model;

class RichEditing extends Model {

	public function get_filtering_vars( $vars ) {
		$vars['meta_query'][] = [
			[
				'key'   => 'rich_editing',
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