<?php

namespace ACP\Filtering\Model\Media;

use ACP\Filtering\Model;

class UsedAsFeaturedImage extends Model {

	public function filter_by_ids( $clauses ) {
		global $wpdb;

		$alias = $this->column->get_name();

		$sub_query = "SELECT DISTINCT( meta_value ) as ID
			FROM wp_postmeta
			WHERE meta_key = '_thumbnail_id'";

		if ( $this->get_filter_value() === 'cpac_nonempty' ) {
			$clauses['join'] .= " INNER JOIN ({$sub_query}) as {$alias} ON {$wpdb->posts}.ID = {$alias}.ID";
		} else {
			$clauses['join'] .= " LEFT JOIN ({$sub_query}) as {$alias} ON {$wpdb->posts}.ID = {$alias}.ID";
			$clauses['where'] .= " AND {$alias}.ID is NULL";
		}

		return $clauses;
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'posts_clauses', [ $this, 'filter_by_ids' ] );

		return $vars;
	}

	public function get_filtering_data() {
		$data = [];

		$data['empty_option'] = [
			__( 'Not used as Featured Image', 'codepress-admin-columns' ),
			__( 'Used as Featured Image', 'codepress-admin-columns' ),
		];

		return $data;
	}

}