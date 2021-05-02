<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;

class CommentCount extends AbstractModel {

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

		$clauses['fields'] .= ", COUNT( acsort_comments.comment_ID ) AS acsort_commentcount";
		$clauses['join'] .= " {$join_type} JOIN {$wpdb->comments} AS acsort_comments ON acsort_comments.comment_post_ID = {$wpdb->posts}.ID";

		if ( $this->stati ) {
			$clauses['join'] .= sprintf( " AND acsort_comments.comment_approved IN ( '%s' )", implode( "','", array_map( 'esc_sql', $this->stati ) ) );
		}

		$clauses['groupby'] = "{$wpdb->posts}.ID";
		$clauses['orderby'] = sprintf( "acsort_commentcount %s, %s", $this->get_order(), $clauses['orderby'] );

		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		return $clauses;
	}

}