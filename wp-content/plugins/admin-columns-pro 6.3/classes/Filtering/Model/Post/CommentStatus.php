<?php

namespace ACP\Filtering\Model\Post;

use ACP\Filtering\Model;

class CommentStatus extends Model {

	public function filter_by_comment_status( $where ) {
		global $wpdb;

		if ( $value = $this->get_filter_value() ) {
			$where .= $wpdb->prepare( " AND {$wpdb->posts}.comment_status = %s", $value );
		}

		return $where;
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'posts_where', [ $this, 'filter_by_comment_status' ] );

		return $vars;
	}

	public function get_filtering_data() {
		$data = [];
		$data['options'] = [
			'open'   => __( 'Open' ),
			'closed' => __( 'Closed' ),
		];

		return $data;
	}

}