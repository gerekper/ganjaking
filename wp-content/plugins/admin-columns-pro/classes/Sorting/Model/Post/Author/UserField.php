<?php

namespace ACP\Sorting\Model\Post\Author;

use ACP;
use ACP\Sorting\AbstractModel;

class UserField extends AbstractModel {

	/**
	 * @var string
	 */
	private $user_field;

	public function __construct( $user_field ) {
		parent::__construct();

		$this->user_field = (string) $user_field;
	}

	public function get_sorting_vars() {
		add_filter( 'posts_clauses', [ $this, 'sorting_clauses_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	public function sorting_clauses_callback( $clauses ) {
		global $wpdb;

		$order = esc_sql( $this->get_order() );

		$join_type = $this->show_empty
			? 'LEFT'
			: 'INNER';

		$clauses['join'] .= " {$join_type} JOIN {$wpdb->users} AS acsort_users ON {$wpdb->posts}.post_author = acsort_users.ID";

		if ( ! $this->show_empty ) {
			$clauses['join'] .= sprintf( " AND acsort_users.`%s` <> ''", esc_sql( $this->user_field ) );
		}

		$clauses['orderby'] = sprintf( "acsort_users.%s $order, $wpdb->posts.ID $order", esc_sql( $this->user_field ) );

		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		return $clauses;
	}

}