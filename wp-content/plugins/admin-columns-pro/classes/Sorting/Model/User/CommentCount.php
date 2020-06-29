<?php

namespace ACP\Sorting\Model\User;

use ACP\Sorting\AbstractModel;
use WP_User_Query;

class CommentCount extends AbstractModel {

	const STATUS_APPROVED = '1';
	const STATUS_SPAM = 'spam';
	const STATUS_TRASH = 'trash';
	const STATUS_PENDING = '0';

	/**
	 * @var string
	 */
	private $status;

	/**
	 * @var array
	 */
	private $post_types;

	public function __construct( array $status = [], array $post_types = [] ) {
		parent::__construct();

		if ( empty( $status ) ) {
			$status = [ self::STATUS_APPROVED, self::STATUS_PENDING ];
		}

		$this->status = $status;
		$this->post_types = $post_types;
	}

	public function get_sorting_vars() {
		add_action( 'pre_user_query', [ $this, 'pre_user_query_callback' ] );

		return [];
	}

	public function pre_user_query_callback( WP_User_Query $query ) {
		global $wpdb;

		$order = $this->get_order();

		$join_type = $this->show_empty
			? 'LEFT'
			: 'INNER';

		$where_status = $this->status
			? sprintf( " AND acsort_comments.comment_approved IN ( %s )", $this->esc_sql_array( $this->status ) )
			: '';

		$query->query_fields .= ", COUNT( acsort_comments.comment_ID ) AS acsort_commentcount";
		$query->query_from .= " 
			$join_type JOIN {$wpdb->comments} AS acsort_comments ON acsort_comments.user_id = {$wpdb->users}.ID
			{$where_status}
		";

		if ( $this->post_types ) {

			$where_post_type = sprintf( " AND acsort_posts.post_type IN ( %s )", $this->esc_sql_array( $this->post_types ) );

			$query->query_from .= " 
				$join_type JOIN {$wpdb->posts} AS acsort_posts ON acsort_posts.ID = acsort_comments.comment_post_ID
				{$where_post_type}
			";
		}
		$query->query_orderby = "
			GROUP BY {$wpdb->users}.ID
			ORDER BY acsort_commentcount $order, {$wpdb->users}.ID $order
		";

		remove_action( 'pre_user_query', [ $this, __FUNCTION__ ] );
	}

	private function esc_sql_array( $array ) {
		return sprintf( "'%s'", implode( "','", array_map( 'esc_sql', $array ) ) );
	}

}