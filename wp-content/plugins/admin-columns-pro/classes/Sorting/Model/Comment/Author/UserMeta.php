<?php

namespace ACP\Sorting\Model\Comment\Author;

use ACP;
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

		return [
			'suppress_filters' => false,
		];
	}

	public function comments_clauses_callback( $clauses ) {
		global $wpdb;

		$order = esc_sql( $this->get_order() );

		$join_type = $this->show_empty
			? 'LEFT'
			: 'INNER';

		$clauses['join'] .= " {$join_type} JOIN {$wpdb->usermeta} AS acsort_usermeta ON {$wpdb->comments}.user_id = acsort_usermeta.user_id";
		$clauses['where'] .= $wpdb->prepare( " AND acsort_usermeta.meta_key = %s", $this->meta_field );
		$clauses['orderby'] = "acsort_usermeta.meta_value $order, {$wpdb->comments}.comment_ID $order";

		if ( ! $this->show_empty ) {
			$clauses['where'] .= " AND acsort_usermeta.meta_value <> ''";
		}

		remove_filter( 'comments_clauses', [ $this, __FUNCTION__ ] );

		return $clauses;
	}

}