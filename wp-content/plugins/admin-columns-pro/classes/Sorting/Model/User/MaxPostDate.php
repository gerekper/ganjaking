<?php

namespace ACP\Sorting\Model\User;

use ACP\Sorting\AbstractModel;
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
		global $wpdb;

		$order = $this->get_order();

		$min_or_max = $this->oldest_post
			? 'MIN'
			: 'MAX';

		$join_type = $this->show_empty
			? 'LEFT'
			: 'INNER';

		$query->query_fields .= ", {$min_or_max}( acsort_posts.post_date ) AS acsort_firstpost";

		$query->query_from .= $wpdb->prepare( " 
					{$join_type} JOIN {$wpdb->posts} AS acsort_posts
						ON {$wpdb->users}.ID = acsort_posts.post_author
						AND acsort_posts.post_type = %s
					", $this->post_type );

		if ( $this->post_stati ) {
			$query->query_from .= "AND acsort_posts.post_status IN (" . $this->esc_sql_array( $this->post_stati ) . ")";
		}

		$query->query_orderby = "
					GROUP BY {$wpdb->users}.ID
					ORDER BY acsort_firstpost $order
				";

		remove_action( "pre_user_query", [ $this, __FUNCTION__ ] );
	}

	private function esc_sql_array( $array ) {
		return sprintf( "'%s'", implode( "','", array_map( 'esc_sql', $array ) ) );
	}

}