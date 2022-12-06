<?php

namespace ACP\Sorting\Model\Comment;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;

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
		remove_filter( 'comments_clauses', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$order = esc_sql( $this->get_order() );

		$clauses['join'] .= $wpdb->prepare( "
			LEFT JOIN $wpdb->commentmeta AS acsort_commentmeta ON $wpdb->comments.comment_ID = acsort_commentmeta.comment_id
			AND acsort_commentmeta.meta_key = %s
		", $this->meta_key );

		$clauses['groupby'] = "$wpdb->comments.comment_ID";
		$clauses['orderby'] = SqlOrderByFactory::create_with_count( "acsort_commentmeta.meta_key", $order );

		return $clauses;
	}

}
