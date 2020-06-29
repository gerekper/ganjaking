<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;

class Attachment extends AbstractModel {

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

		$clauses['fields'] .= ", count( acsort_attachments.ID ) AS acsort_attachment_count";
		$clauses['join'] .= "
			$join_type JOIN $wpdb->posts AS acsort_attachments ON acsort_attachments.post_parent = {$wpdb->posts}.ID
			AND acsort_attachments.post_type = 'attachment'
		";
		$clauses['groupby'] = "$wpdb->posts.ID";
		$clauses['orderby'] = "acsort_attachment_count $order, $wpdb->posts.ID $order";

		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		return $clauses;
	}

}