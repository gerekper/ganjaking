<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;

class LatestComment extends AbstractModel {

	const STATUS_APPROVED = '1';
	const STATUS_SPAM = 'spam';
	const STATUS_TRASH = 'trash';
	const STATUS_PENDING = '0';

	/**
	 * @var array
	 */
	private $stati;

	public function __construct( array $stati = [] ) {
		parent::__construct();

		if ( empty( $stati ) ) {
			$stati = [ self::STATUS_APPROVED, self::STATUS_PENDING ];
		}

		$this->stati = $stati;
	}

	public function get_sorting_vars() {
		add_filter( 'posts_clauses', [ $this, 'posts_fields_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	public function posts_fields_callback( $clauses ) {
		global $wpdb;

		$join_type = $this->show_empty
			? 'LEFT'
			: 'INNER';

		$clauses['fields'] .= ", max( acsort_comments.comment_date ) AS acsort_recent_comment";
		$clauses['join'] .= " 
			{$join_type} JOIN {$wpdb->comments} AS acsort_comments ON acsort_comments.comment_post_ID = {$wpdb->posts}.ID
			AND acsort_comments.comment_date != ''
		";

		if ( $this->stati ) {
			$clauses['where'] .= sprintf( " AND acsort_comments.comment_approved IN ( '%s' )", implode( "','", array_map( 'esc_sql', $this->stati ) ) );
		}

		$clauses['groupby'] = "{$wpdb->posts}.ID";
		$clauses['orderby'] = sprintf( "acsort_recent_comment %s, %s", $this->get_order(), $clauses['orderby'] );

		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		return $clauses;
	}

}