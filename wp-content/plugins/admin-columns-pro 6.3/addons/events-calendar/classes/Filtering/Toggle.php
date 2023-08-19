<?php

namespace ACA\EC\Filtering;

use ACP;

abstract class Toggle extends ACP\Filtering\Model\Meta {

	abstract public function get_on_value();

	public function get_filtering_vars( $vars ) {

		$value = $this->get_filter_value();

		if ( $this->get_on_value() === $value ) {
			$vars['meta_query'][] = [
				'key'   => $this->column->get_meta_key(),
				'value' => $value,
			];
		} else {
			$vars['meta_query'][] = [
				'relation' => 'OR',
				[
					'key'     => $this->column->get_meta_key(),
					'compare' => 'NOT EXISTS',
				],
				[
					'key'   => $this->column->get_meta_key(),
					'value' => $value,
				],
			];
		}

		return $vars;
	}

	public function get_filtering_data() {
		return [
			'options' => [
				1 => __( 'Yes' ),
				0 => __( 'No' ),
			],
		];
	}

}