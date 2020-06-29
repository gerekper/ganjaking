<?php

namespace ACP\Filtering\Model\Comment;

use ACP\Filtering\Model;

class User extends Model {

	public function get_filtering_vars( $vars ) {
		$vars['user_id'] = $this->get_filter_value();

		return $vars;
	}

	public function get_filtering_data() {
		$data = [];
		foreach ( $this->strategy->get_values_by_db_field( 'user_id' ) as $_value ) {
			$data['options'][ $_value ] = ac_helper()->user->get_display_name( $_value );
		}

		return $data;
	}

}