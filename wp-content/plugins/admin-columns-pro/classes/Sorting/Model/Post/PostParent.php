<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;

class PostParent extends AbstractModel {

	public function get_sorting_vars() {
		add_filter( 'posts_clauses', [ $this, 'sorting_clauses_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	public function sorting_clauses_callback( $clauses ) {
		global $wpdb;

		$order = esc_sql( $this->get_order() );

		$join_type = $this->show_empty
			? 'LEFT'
			: 'INNER';

		$clauses['join'] .= "{$join_type} JOIN {$wpdb->posts} AS acsort_posts ON {$wpdb->posts}.post_parent = acsort_posts.ID";
		$clauses['orderby'] = "acsort_posts.post_title $order, acsort_posts.ID $order";

		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		return $clauses;
	}

}