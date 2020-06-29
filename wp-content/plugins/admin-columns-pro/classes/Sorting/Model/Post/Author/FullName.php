<?php

namespace ACP\Sorting\Model\Post\Author;

use ACP;
use ACP\Sorting\AbstractModel;

class FullName extends AbstractModel {

	public function get_sorting_vars() {
		add_filter( 'posts_clauses', [ $this, 'sorting_clauses_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	public function sorting_clauses_callback( $clauses ) {
		global $wpdb;

		$order = esc_sql( $this->get_order() );

		$clauses['fields'] .= ", concat( acsort_usermeta1.meta_value, acsort_usermeta2.meta_value ) AS acsort_fullname";
		$clauses['join'] .= "
			INNER JOIN {$wpdb->usermeta} AS acsort_usermeta1 ON {$wpdb->posts}.post_author = acsort_usermeta1.user_id 
				AND acsort_usermeta1.meta_key = 'first_name'
			INNER JOIN {$wpdb->usermeta} AS acsort_usermeta2 ON {$wpdb->posts}.post_author = acsort_usermeta2.user_id 
				AND acsort_usermeta2.meta_key = 'last_name'
		";
		$clauses['orderby'] = "acsort_fullname $order, {$wpdb->posts}.ID $order";

		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		return $clauses;
	}

}