<?php

namespace ACP\Sorting\Model\User;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;
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
		remove_action( 'pre_user_query', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$query->query_from .= "\nLEFT JOIN $wpdb->comments AS acsort_comments ON acsort_comments.user_id = $wpdb->users.ID\n";

		if ( $this->status ) {
			$query->query_from .= sprintf( "\nAND acsort_comments.comment_approved IN ( %s )", $this->esc_sql_array( $this->status ) );
		}

		if ( $this->post_types ) {
			$query->query_from .= sprintf(
				"
				\nLEFT JOIN $wpdb->posts AS acsort_posts ON acsort_posts.ID = acsort_comments.comment_post_ID AND acsort_posts.post_type IN ( %s )
				",
				$this->esc_sql_array( $this->post_types )
			);
		}

		$query->query_orderby = sprintf(
			"
			GROUP BY $wpdb->users.ID
			ORDER BY %s, $wpdb->users.ID %s
			",
			SqlOrderByFactory::create_with_count( "acsort_comments.comment_ID", $this->get_order() ),
			$this->get_order()
		);
	}

	private function esc_sql_array( $array ) {
		return sprintf( "'%s'", implode( "','", array_map( 'esc_sql', $array ) ) );
	}

}