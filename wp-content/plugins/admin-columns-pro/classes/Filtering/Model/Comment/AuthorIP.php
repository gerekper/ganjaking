<?php

namespace ACP\Filtering\Model\Comment;

use ACP\Filtering\Model;

class AuthorIP extends Model {

	public function get_filtering_vars( $vars ) {
		add_filter( 'comments_clauses', [ $this, 'filter_by_author_ip' ] );

		return $vars;
	}

	public function filter_by_author_ip( $comments_clauses ) {
		global $wpdb;
		$comments_clauses['where'] .= ' ' . $wpdb->prepare( "AND {$wpdb->comments}.comment_author_IP = %s", $this->get_filter_value() );

		return $comments_clauses;
	}

	public function get_filtering_data() {
		$data = [];
		foreach ( $this->strategy->get_values_by_db_field( 'comment_author_IP' ) as $_value ) {
			$data['options'][ $_value ] = $_value;
		}

		return $data;
	}

}