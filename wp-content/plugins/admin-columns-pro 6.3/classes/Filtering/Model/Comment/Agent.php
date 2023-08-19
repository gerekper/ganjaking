<?php

namespace ACP\Filtering\Model\Comment;

use ACP\Filtering\Model;

class Agent extends Model {

	public function filter_by_agent( $comments_clauses ) {
		global $wpdb;

		$comments_clauses['where'] .= ' ' . $wpdb->prepare( "AND {$wpdb->comments}.comment_agent = %s", $this->get_filter_value() );

		return $comments_clauses;
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'comments_clauses', [ $this, 'filter_by_agent' ] );

		return $vars;
	}

	public function get_filtering_data() {
		$data = [];
		foreach ( $this->strategy->get_values_by_db_field( 'comment_agent' ) as $_value ) {
			$data['options'][ $_value ] = $_value;
		}

		return $data;
	}

}