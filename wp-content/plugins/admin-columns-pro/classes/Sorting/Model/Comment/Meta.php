<?php

namespace ACP\Sorting\Model\Comment;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\DataType;

class Meta extends AbstractModel {

	/**
	 * @var string
	 */
	private $meta_key;

	public function __construct( $meta_key, DataType $data_type = null ) {
		parent::__construct( $data_type );

		$this->meta_key = $meta_key;
	}

	public function get_sorting_vars() {
		add_filter( 'comments_clauses', [ $this, 'comments_clauses_callback' ] );

		return [];
	}

	public function comments_clauses_callback( $clauses ) {
		global $wpdb;

		$join_type = $this->show_empty
			? 'LEFT'
			: 'INNER';

		$clauses['join'] .= $wpdb->prepare( "
			{$join_type} JOIN {$wpdb->commentmeta} AS acsort_commentmeta ON {$wpdb->comments}.comment_ID = acsort_commentmeta.comment_id
			AND acsort_commentmeta.meta_key = %s
		", $this->meta_key );

		if ( ! $this->show_empty ) {
			$clauses['join'] .= " AND acsort_commentmeta.meta_value <> ''";
		}

		$clauses['orderby'] = $this->get_order_by();
		$clauses['groupby'] = "{$wpdb->comments}.comment_ID";

		remove_filter( 'comments_clauses', [ $this, __FUNCTION__ ] );

		return $clauses;
	}

	/**
	 * @return string
	 */
	protected function get_order_by() {
		global $wpdb;

		$order = esc_sql( $this->get_order() );
		$cast_type = CastType::create_from_data_type( $this->data_type )->get_value();

		return sprintf( "CAST( acsort_commentmeta.meta_value AS %s ) $order, {$wpdb->comments}.comment_ID $order", $cast_type );
	}

}