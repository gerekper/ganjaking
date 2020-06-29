<?php

namespace ACP\Filtering\Model\Comment;

use ACP\Filtering\Model;

class AuthorEmail extends Model {

	public function get_filtering_vars( $vars ) {
		$vars['author_email'] = $this->get_filter_value();

		return $vars;
	}

	public function get_filtering_data() {
		$data = [];
		foreach ( $this->strategy->get_values_by_db_field( 'comment_author_email' ) as $_value ) {
			$data['options'][ $_value ] = $_value;
		}

		return $data;
	}

}