<?php

namespace ACA\WC\Filtering\ShopOrder;

use ACP;
use WP_Query;

class CustomerMessage extends ACP\Filtering\Model {

	public function filter_by_excerpt( $where, WP_Query $query ) {
		global $wpdb;

		if ( $query->is_main_query() ) {
			$sql = $this->get_filter_value() ? "!= ''" : "=''";
			$where = "{$where} AND {$wpdb->posts}.post_excerpt " . $sql;
		}

		return $where;
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'posts_where', [ $this, 'filter_by_excerpt' ], 10, 2 );

		return $vars;
	}

	public function get_filtering_data() {
		return [
			'options' => [
				0 => __( 'Empty', 'codepress-admin-columns' ),
				1 => __( 'Has customer message', 'codepress-admin-columns' ),
			],
		];
	}
}