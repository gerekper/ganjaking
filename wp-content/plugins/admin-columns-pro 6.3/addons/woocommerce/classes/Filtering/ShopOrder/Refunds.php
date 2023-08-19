<?php

namespace ACA\WC\Filtering\ShopOrder;

use ACP;
use WP_Query;

class Refunds extends ACP\Filtering\Model {

	public function get_filtering_data() {
		return [
			'empty_option' => [
				sprintf( __( 'Without %s', 'codepress-admin-columns' ), __( 'Refunds', 'codepress-admin-columns' ) ),
				sprintf( __( 'Has %s', 'codepress-admin-columns' ), __( 'Refunds', 'codepress-admin-columns' ) ),
			],
		];
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'posts_where', [ $this, 'filter_by_found_refunds' ], 10, 2 );

		return $vars;
	}

	public function filter_by_found_refunds( $where, WP_Query $query ) {
		global $wpdb;

		if ( $query->is_main_query() ) {
			$compare = 'cpac_nonempty' === $this->get_filter_value() ? 'IN' : 'NOT IN';

			$sub_query = "
				SELECT DISTINCT(post_parent)
				FROM {$wpdb->posts}
				WHERE post_type = 'shop_order_refund'
			";

			$where .= " AND {$wpdb->posts}.ID {$compare}( {$sub_query} )";
		}

		return $where;
	}

}