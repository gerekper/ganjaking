<?php

namespace ACP\Sorting\Model\Comment\Author;

use ACP;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;

class FullName extends AbstractModel {

	public function get_sorting_vars() {
		add_filter( 'comments_clauses', [ $this, 'comments_clauses_callback' ] );

		return [];
	}

	public function comments_clauses_callback( $clauses ) {
		remove_filter( 'comments_clauses', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$clauses['join'] .= "
			INNER JOIN $wpdb->usermeta AS acsort_usermeta1 ON $wpdb->comments.user_id = acsort_usermeta1.user_id 
				AND acsort_usermeta1.meta_key = 'first_name'
			INNER JOIN $wpdb->usermeta AS acsort_usermeta2 ON $wpdb->comments.user_id = acsort_usermeta2.user_id 
				AND acsort_usermeta2.meta_key = 'last_name'
		";
		$clauses['orderby'] = SqlOrderByFactory::create_with_concat( [ 'acsort_usermeta1.meta_value', 'acsort_usermeta2.meta_value' ], $this->get_order() );
		$clauses['groupby'] = "$wpdb->comments.comment_ID";

		return $clauses;
	}

}