<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;

class PostParent extends AbstractModel {

	public function get_sorting_vars() {
		add_filter( 'posts_clauses', [ $this, 'sorting_clauses_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	public function sorting_clauses_callback( $clauses ) {
		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$clauses['join'] .= "LEFT JOIN $wpdb->posts AS acsort_posts ON $wpdb->posts.post_parent = acsort_posts.ID";
		$clauses['orderby'] = SqlOrderByFactory::create( "acsort_posts.post_title", $this->get_order() );
		$clauses['orderby'] .= sprintf( ", $wpdb->posts.post_date %s", esc_sql( $this->get_order() ) );

		return $clauses;
	}

}