<?php

namespace ACP\Filtering\Model\Media;

use ACP\Filtering\Model;

class Comments extends Model {

	public function filter_by_comments( $where ) {
		global $wpdb;

		if ( 'no_comments' == $this->get_filter_value() ) {
			$where .= "AND {$wpdb->posts}.comment_count = '0'";
		} elseif ( 'has_comments' == $this->get_filter_value() ) {
			$where .= "AND {$wpdb->posts}.comment_count <> '0'";
		}

		return $where;
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'posts_where', [ $this, 'filter_by_comments' ] );

		return $vars;
	}

	public function get_filtering_data() {
		return [
			'options' => [
				'no_comments'  => __( 'No comments', 'codepress-admin-columns' ),
				'has_comments' => __( 'Has comments', 'codepress-admin-columns' ),
			],
		];
	}

}