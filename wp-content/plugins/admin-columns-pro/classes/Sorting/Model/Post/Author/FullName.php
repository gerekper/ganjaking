<?php

namespace ACP\Sorting\Model\Post\Author;

use ACP;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;

class FullName extends AbstractModel {

	public function get_sorting_vars() {
		add_filter( 'posts_clauses', [ $this, 'sorting_clauses_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	public function sorting_clauses_callback( $clauses ) {
		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$clauses['join'] .= "
			INNER JOIN $wpdb->usermeta AS acsort_usermeta1 ON $wpdb->posts.post_author = acsort_usermeta1.user_id 
				AND acsort_usermeta1.meta_key = 'first_name'
			INNER JOIN $wpdb->usermeta AS acsort_usermeta2 ON $wpdb->posts.post_author = acsort_usermeta2.user_id 
				AND acsort_usermeta2.meta_key = 'last_name'
		";
		$clauses['orderby'] = SqlOrderByFactory::create_with_concat( [ 'acsort_usermeta1.meta_value', 'acsort_usermeta2.meta_value' ], $this->get_order() );
		$clauses['orderby'] .= sprintf( "\n, $wpdb->posts.ID %s", esc_sql( $this->get_order() ) );

		return $clauses;
	}

}