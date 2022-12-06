<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;

class Attachment extends AbstractModel {

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
			LEFT JOIN $wpdb->posts AS acsort_attachments ON acsort_attachments.post_parent = $wpdb->posts.ID
				AND acsort_attachments.post_type = 'attachment'
		";
		$clauses['groupby'] = "$wpdb->posts.ID";
		$clauses['orderby'] = SqlOrderByFactory::create_with_count( "acsort_attachments.ID", $this->get_order() );
		$clauses['orderby'] .= sprintf( ", $wpdb->posts.ID %s", esc_sql( $this->get_order() ) );

		return $clauses;
	}

}