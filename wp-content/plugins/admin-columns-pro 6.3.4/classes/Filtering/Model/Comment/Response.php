<?php

namespace ACP\Filtering\Model\Comment;

use ACP\Filtering\Helper;
use ACP\Filtering\Model;

class Response extends Model {

	public function filter_by_post_id( $comments_clauses ) {
		global $wpdb;
		$comments_clauses['where'] .= ' ' . $wpdb->prepare( "AND {$wpdb->comments}.comment_post_ID = %s", $this->get_filter_value() );

		return $comments_clauses;
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'comments_clauses', [ $this, 'filter_by_post_id' ] );

		return $vars;
	}

	public function get_filtering_data() {
		return [
			'options' => ( new Helper() )->get_post_titles( $this->strategy->get_values_by_db_field( 'comment_post_ID' ) ),
		];
	}

}