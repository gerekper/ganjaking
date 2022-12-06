<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\ComputationType;

class LatestComment extends AbstractModel {

	public const STATUS_APPROVED = '1';
	public const STATUS_SPAM = 'spam';
	public const STATUS_TRASH = 'trash';
	public const STATUS_PENDING = '0';

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
		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$clauses['join'] .= " 
			LEFT JOIN $wpdb->comments AS acsort_comments ON acsort_comments.comment_post_ID = $wpdb->posts.ID
		";

		if ( $this->stati ) {
			$clauses['where'] .= sprintf( " AND acsort_comments.comment_approved IN ( '%s' )", implode( "','", array_map( 'esc_sql', $this->stati ) ) );
		}

		$clauses['groupby'] = "$wpdb->posts.ID";
		$clauses['orderby'] = SqlOrderByFactory::create_with_computation( new ComputationType( ComputationType::MAX ), "acsort_comments.comment_date", $this->get_order() );
		$clauses['orderby'] .= sprintf( ", $wpdb->posts.post_date %s", esc_sql( $this->get_order() ) );

		return $clauses;
	}

}