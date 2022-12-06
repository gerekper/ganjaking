<?php

namespace ACP\Sorting\Model\User;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\ComputationType;
use WP_User_Query;

class MaxPostDate extends AbstractModel {

	/**
	 * @var string
	 */
	private $post_type;

	/**
	 * @var array
	 */
	private $post_stati;

	/**
	 * @var bool
	 */
	private $oldest_post;

	public function __construct( $post_type, array $post_stati = [], $oldest_post = false ) {
		parent::__construct();

		$this->post_type = (string) $post_type;
		$this->post_stati = $post_stati;
		$this->oldest_post = (bool) $oldest_post;
	}

	public function get_sorting_vars() {
		add_action( 'pre_user_query', [ $this, 'pre_user_query_callback' ] );

		return [];
	}

	public function pre_user_query_callback( WP_User_Query $query ) {
		remove_action( "pre_user_query", [ $this, __FUNCTION__ ] );

		global $wpdb;

		$computation = new ComputationType( $this->oldest_post ? ComputationType::MIN : ComputationType::MAX );

		$query->query_from .= $wpdb->prepare( " 
					LEFT JOIN $wpdb->posts AS acsort_posts
						ON $wpdb->users.ID = acsort_posts.post_author
						AND acsort_posts.post_type = %s
					", $this->post_type );

		if ( $this->post_stati ) {
			$query->query_from .= "AND acsort_posts.post_status IN (" . $this->esc_sql_array( $this->post_stati ) . ")";
		}

		$query->query_orderby = sprintf(
			"
				GROUP BY $wpdb->users.ID
				ORDER BY %s, $wpdb->users.ID %s
			",
			SqlOrderByFactory::create_with_computation( $computation, 'acsort_posts.post_date', $this->get_order() ),
			esc_sql( $this->get_order() )
		);
	}

	private function esc_sql_array( $array ) {
		return sprintf( "'%s'", implode( "','", array_map( 'esc_sql', $array ) ) );
	}

}