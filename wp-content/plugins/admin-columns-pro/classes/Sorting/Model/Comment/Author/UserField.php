<?php

namespace ACP\Sorting\Model\Comment\Author;

use ACP;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;

class UserField extends AbstractModel {

	/**
	 * @var string
	 */
	private $user_field;

	public function __construct( string $user_field ) {
		parent::__construct();

		$this->user_field = $user_field;
	}

	public function get_sorting_vars() {
		add_filter( 'comments_clauses', [ $this, 'comments_clauses_callback' ] );

		return [];
	}

	public function comments_clauses_callback( $clauses ) {
		remove_filter( 'comments_clauses', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$clauses['join'] .= "LEFT JOIN $wpdb->users AS acsort_users ON $wpdb->comments.user_id = acsort_users.ID";
		$clauses['orderby'] = SqlOrderByFactory::create( "acsort_users.$this->user_field", $this->get_order() );
		$clauses['orderby'] .= sprintf( ", $wpdb->comments.comment_ID %s", esc_sql( $this->get_order() ) );

		return $clauses;
	}

}