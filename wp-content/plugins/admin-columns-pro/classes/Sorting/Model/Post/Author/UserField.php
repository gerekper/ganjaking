<?php

namespace ACP\Sorting\Model\Post\Author;

use ACP;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;

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
		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$clauses['join'] .= " LEFT JOIN $wpdb->users AS acsort_users ON $wpdb->posts.post_author = acsort_users.ID";
		$clauses['orderby'] = SqlOrderByFactory::create( sprintf( "acsort_users.%s", $this->user_field ), $this->get_order() );
		$clauses['orderby'] .= sprintf( ", $wpdb->posts.ID %s", esc_sql( $this->get_order() ) );

		return $clauses;
	}

}