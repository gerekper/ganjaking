<?php

namespace ACP\Sorting\Model\User;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\ComputationType;
use WP_User_Query;

class PostCount extends AbstractModel {

	/**
	 * @var array
	 */
	private $post_types;

	/**
	 * @var string
	 */
	private $post_status;

	public function __construct( array $post_types = null, array $post_status = null ) {
		parent::__construct();

		$this->post_types = $post_types;
		$this->post_status = $post_status;
	}

	public function get_sorting_vars() {
		add_action( 'pre_user_query', [ $this, 'pre_user_query_callback' ] );

		return [];
	}

	public function pre_user_query_callback( WP_User_Query $query ) {
		remove_action( "pre_user_query", [ $this, __FUNCTION__ ] );

		global $wpdb;

		$query->query_from .= " LEFT JOIN $wpdb->posts AS acsort_posts ON acsort_posts.post_author = $wpdb->users.ID";

		if ( $this->post_status ) {
			$query->query_from .= sprintf( " AND acsort_posts.post_status IN ( %s )", $this->esc_sql_array( $this->post_status ) );
		}

		if ( $this->post_types ) {
			$query->query_from .= sprintf( " AND acsort_posts.post_type IN ( %s )", $this->esc_sql_array( $this->post_types ) );
		}

		$query->query_orderby = sprintf( "
				GROUP BY $wpdb->users.ID
				ORDER BY %s, $wpdb->users.ID %s
			",
			SqlOrderByFactory::create_with_computation( new ComputationType( ComputationType::COUNT ), 'acsort_posts.ID', $this->get_order(), true ),
			esc_sql( $this->get_order() )
		);
	}

	private function esc_sql_array( $array ) {
		return sprintf( "'%s'", implode( "','", array_map( 'esc_sql', $array ) ) );
	}

}