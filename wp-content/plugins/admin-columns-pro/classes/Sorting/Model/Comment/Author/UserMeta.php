<?php

namespace ACP\Sorting\Model\Comment\Author;

use ACP;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\DataType;

class UserMeta extends ACP\Sorting\AbstractModel {

	/**
	 * @var string
	 */
	private $meta_field;

	public function __construct( $meta_field, DataType $data_type = null ) {
		parent::__construct( $data_type );

		$this->meta_field = $meta_field;
	}

	public function get_sorting_vars() {
		add_filter( 'comments_clauses', [ $this, 'comments_clauses_callback' ] );

		return [];
	}

	public function comments_clauses_callback( $clauses ) {
		remove_filter( 'comments_clauses', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$clauses['join'] .= $wpdb->prepare( " LEFT JOIN $wpdb->usermeta AS acsort_usermeta ON $wpdb->comments.user_id = acsort_usermeta.user_id AND acsort_usermeta.meta_key = %s", $this->meta_field );
		$clauses['orderby'] = SqlOrderByFactory::create( "acsort_usermeta.meta_value", $this->get_order() );
		$clauses['orderby'] .= sprintf( ", $wpdb->comments.comment_ID %s", esc_sql( $this->get_order() ) );

		return $clauses;
	}

}