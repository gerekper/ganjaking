<?php

namespace ACP\Filtering\Model\Media;

use ACP\Filtering\Model;

class Author extends Model {

	public function get_filtering_vars( $vars ) {
		$vars['author'] = $this->get_filter_value();

		return $vars;
	}

	public function get_filtering_data() {
		$data = [];

		$values = $this->strategy->get_values_by_db_field( 'post_author' );

		if ( $values ) {
			foreach ( $values as $value ) {
				$user = get_user_by( 'id', $value );
				$data['options'][ $value ] = $user->display_name;
			}
		}

		return $data;
	}

}