<?php

namespace ACP\Filtering\Model\Comment;

use ACP\Filtering\Model;

class Date extends Model {

	public function filter_by_date( $comments_clauses ) {
		global $wpdb;

		$comments_clauses['where'] .= ' ' . $wpdb->prepare( "AND {$wpdb->comments}.comment_date LIKE %s", $this->get_filter_value() . '%' );

		return $comments_clauses;
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'comments_clauses', [ $this, 'filter_by_date' ] );

		return $vars;
	}

	public function get_filtering_data() {
		$options = [];

		foreach ( $this->strategy->get_values_by_db_field( 'comment_date' ) as $_value ) {
			$date = substr( $_value, 0, 7 ); // only year and month
			$options[ $date ] = ac_format_date( 'F Y', strtotime( $_value ) );
		}

		krsort( $options );

		return [
			'options' => $options,
			'order'   => false,
		];
	}

}