<?php

namespace ACP\Sorting\Model\User;

use ACP\Sorting\AbstractModel;
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
		global $wpdb;

		$order = $this->get_order();

		$join_type = $this->show_empty
			? 'LEFT'
			: 'INNER';

		$query->query_fields .= ", COUNT( acsort_posts.ID ) AS acsort_postcount";
		$query->query_from .= " $join_type JOIN {$wpdb->posts} AS acsort_posts ON acsort_posts.post_author = {$wpdb->users}.ID";

		if ( $this->post_status ) {
			$query->query_from .= sprintf( " AND acsort_posts.post_status IN ( %s )", $this->esc_sql_array( $this->post_status ) );
		}

		if ( $this->post_types ) {
			$query->query_from .= sprintf( " AND acsort_posts.post_type IN ( %s )", $this->esc_sql_array( $this->post_types ) );
		}

		$query->query_orderby = "
			GROUP BY {$wpdb->users}.ID
			ORDER BY acsort_postcount $order, {$wpdb->users}.ID $order
		";

		remove_action( "pre_user_query", [ $this, __FUNCTION__ ] );
	}

	private function esc_sql_array( $array ) {
		return sprintf( "'%s'", implode( "','", array_map( 'esc_sql', $array ) ) );
	}

}