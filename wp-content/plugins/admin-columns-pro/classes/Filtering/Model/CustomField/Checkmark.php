<?php

namespace ACP\Filtering\Model\CustomField;

use ACP\Filtering\Model;

class Checkmark extends Model\CustomField {

	public function get_filtering_data() {
		return [
			'options' => [
				'1' => __( 'True', 'codepress-admin-columns' ),
				'0' => __( 'False', 'codepress-admin-columns' ),
			],
		];
	}

	public function get_filtering_vars( $vars ) {
		if ( 1 == $this->get_filter_value() ) {
			$vars['meta_query'][] = [
				'key'   => $this->column->get_meta_key(),
				'value' => [ '1', 'yes', 'true', 'on' ],
			];
		}

		if ( 0 == $this->get_filter_value() ) {

			$vars['meta_query'][] = [
				'relation' => 'OR',
				[
					'key'     => $this->column->get_meta_key(),
					'compare' => 'NOT EXISTS',
				],
				[
					'key'   => $this->column->get_meta_key(),
					'value' => [ '0', 'no', 'false', 'off', '' ],
				],
			];
		}

		return $this->get_filtering_vars_empty_nonempty( $vars );
	}

}