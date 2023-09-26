<?php

namespace ACP\Filtering\Model\Post;

use ACP\Filtering\Model;

class Excerpt extends Model {

	public function get_filtering_data() {
		return [
			'options' => [
				'without_excerpt' => __( 'Without Excerpt', 'codepress-admin-columns' ),
				'has_excerpt'     => __( 'Has Excerpt', 'codepress-admin-columns' ),
			],
		];
	}

	public function filter_by_excerpt( $where ) {
		global $wpdb;

		if ( $value = $this->get_filter_value() ) {
			$sql = 'has_excerpt' === $value ? " NOT LIKE ''" : " LIKE ''";

			$where .= " AND {$wpdb->posts}.post_excerpt" . $sql;
		}

		return $where;
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'posts_where', [ $this, 'filter_by_excerpt' ] );

		return $vars;
	}

}