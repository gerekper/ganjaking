<?php

namespace ACP\Filtering\Model\Comment;

use ACP\Filtering\Model;

class Type extends Model {

	public function get_filtering_vars( $vars ) {
		$vars['type'] = $this->get_filter_value();

		return $vars;
	}

	public function get_filtering_data() {
		$data = [];
		foreach ( $this->strategy->get_values_by_db_field( 'comment_type' ) as $_value ) {
			$data['options'][ $_value ] = $_value;
		}

		return $data;
	}

}