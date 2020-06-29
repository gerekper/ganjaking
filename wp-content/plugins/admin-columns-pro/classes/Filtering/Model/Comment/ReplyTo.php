<?php

namespace ACP\Filtering\Model\Comment;

use ACP\Filtering\Model;

class ReplyTo extends Model {

	public function get_filtering_vars( $vars ) {
		$vars['parent'] = $this->get_filter_value();

		return $vars;
	}

	public function get_filtering_data() {
		$data = [];

		foreach ( $this->strategy->get_values_by_db_field( 'comment_parent' ) as $_value ) {
			$data['options'][ $_value ] = get_comment_author( $_value ) . ' (' . $_value . ')';
		}

		return $data;
	}

}