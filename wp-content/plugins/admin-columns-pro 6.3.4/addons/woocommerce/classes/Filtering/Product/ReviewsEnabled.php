<?php

namespace ACA\WC\Filtering\Product;

use ACP;
use WP_Query;

class ReviewsEnabled extends ACP\Filtering\Model {

	public function filter_by_wc_reviews_enabled( $where, WP_Query $query ) {
		global $wpdb;

		if ( $query->is_main_query() ) {
			$where .= $wpdb->prepare( "AND {$wpdb->posts}.comment_status = %s", $this->get_filter_value() );
		}

		return $where;
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'posts_where', [ $this, 'filter_by_wc_reviews_enabled' ], 10, 2 );

		return $vars;
	}

	public function get_filtering_data() {
		return [
			'options' => [
				'open'   => __( 'Open' ),
				'closed' => __( 'Closed' ),
			],
		];
	}

}
