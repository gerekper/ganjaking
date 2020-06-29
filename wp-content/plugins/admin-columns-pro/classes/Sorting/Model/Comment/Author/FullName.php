<?php

namespace ACP\Sorting\Model\Comment\Author;

use ACP;
use ACP\Sorting\AbstractModel;

class FullName extends AbstractModel {

	public function get_sorting_vars() {
		add_filter( 'comments_clauses', [ $this, 'comments_clauses_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	public function comments_clauses_callback( $clauses ) {
		global $wpdb;

		$order = esc_sql( $this->get_order() );

		$clauses['fields'] .= ", concat( acsort_usermeta1.meta_value, acsort_usermeta2.meta_value ) AS acsort_fullname";
		$clauses['join'] .= "
			INNER JOIN {$wpdb->usermeta} AS acsort_usermeta1 ON {$wpdb->comments}.user_id = acsort_usermeta1.user_id 
				AND acsort_usermeta1.meta_key = 'first_name'
			INNER JOIN {$wpdb->usermeta} AS acsort_usermeta2 ON {$wpdb->comments}.user_id = acsort_usermeta2.user_id 
				AND acsort_usermeta2.meta_key = 'last_name'
		";
		$clauses['orderby'] = "acsort_fullname $order, {$wpdb->comments}.comment_ID $order";
		$clauses['groupby'] = "{$wpdb->comments}.comment_ID";

		remove_filter( 'comments_clauses', [ $this, __FUNCTION__ ] );

		return $clauses;
	}

}