<?php

namespace ACP\Filtering\Model\Comment;

use ACP\Filtering\Model;

class Approved extends Model {

	public function filter_by_approved( $comments_clauses ) {
		global $wpdb;

		$comments_clauses['where'] .= ' ' . $wpdb->prepare( "AND {$wpdb->comments}.comment_approved = %s", $this->get_filter_value() );

		return $comments_clauses;
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'comments_clauses', [ $this, 'filter_by_approved' ] );

		return $vars;
	}

	public function get_filtering_data() {
		return [
			'options' => [
				0 => __( 'No' ),
				1 => __( 'Yes' ),
			],
		];
	}

}