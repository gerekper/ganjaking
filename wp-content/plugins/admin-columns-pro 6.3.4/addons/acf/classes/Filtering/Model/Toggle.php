<?php

namespace ACA\ACF\Filtering\Model;

use ACP;

class Toggle extends ACP\Filtering\Model\Meta {

	public function get_filtering_vars( $vars ) {

		$value = $this->get_filter_value();

		if ( 1 == $value ) {
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
		$data = [];

		$values = $this->get_meta_values();
		$data['options'][0] = __( 'False', 'codepress-admin-columns' );
		foreach ( $values as $value ) {
			if ( 0 != $value ) {
				$data['options'][ $value ] = __( 'True', 'codepress-admin-columns' );
			}
		}

		return $data;
	}

}