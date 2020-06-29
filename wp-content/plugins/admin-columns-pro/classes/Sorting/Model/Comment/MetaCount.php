<?php

namespace ACP\Sorting\Model\Comment;

use ACP\Sorting\AbstractModel;

/**
 * Sort the comment list table on the number of times the meta_key is used by a comment.
 * @since 5.2
 */
class MetaCount extends AbstractModel {

	/**
	 * @var string
	 */
	private $meta_key;

	/**
	 * @param string $meta_key
	 */
	public function __construct( $meta_key ) {
		parent::__construct();

		$this->meta_key = $meta_key;
	}

	public function get_sorting_vars() {
		add_filter( 'comments_clauses', [ $this, 'comments_clauses_callback' ] );

		return [];
	}

	public function comments_clauses_callback( $clauses ) {
		global $wpdb;

		$order = esc_sql( $this->get_order() );

		$join_type = $this->show_empty
			? 'LEFT'
			: 'INNER';

		$clauses['fields'] .= ", COUNT( acsort_commentmeta.meta_key ) AS acsort_commentmeta";
		$clauses['join'] .= $wpdb->prepare( "
			{$join_type} JOIN {$wpdb->commentmeta} AS acsort_commentmeta ON {$wpdb->comments}.comment_ID = acsort_commentmeta.comment_id
			AND acsort_commentmeta.meta_key = %s
		", $this->meta_key );

		if ( ! $this->show_empty ) {
			$clauses['join'] .= " AND acsort_commentmeta.meta_value <> ''";
		}

		$clauses['groupby'] = "{$wpdb->comments}.comment_ID";
		$clauses['orderby'] = "acsort_commentmeta {$order}, {$wpdb->comments}.comment_ID {$order}";

		remove_filter( 'comments_clauses', [ $this, __FUNCTION__ ] );

		return $clauses;
	}

}
