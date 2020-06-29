<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\DataType;

class Meta extends AbstractModel {

	/**
	 * @var string
	 */
	protected $meta_key;

	public function __construct( $meta_key, DataType $data_type = null ) {
		parent::__construct( $data_type );

		$this->meta_key = $meta_key;
	}

	public function get_sorting_vars() {
		add_filter( 'posts_clauses', [ $this, 'sorting_clauses_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	public function sorting_clauses_callback( $clauses ) {
		global $wpdb;

		$join_type = $this->show_empty
			? 'LEFT'
			: 'INNER';

		$clauses['join'] .= $wpdb->prepare( "
			{$join_type} JOIN {$wpdb->postmeta} AS acsort_postmeta ON {$wpdb->posts}.ID = acsort_postmeta.post_id
			AND acsort_postmeta.meta_key = %s
		", $this->meta_key );

		if ( ! $this->show_empty ) {
			$clauses['join'] .= " AND acsort_postmeta.meta_value <> ''";
		}

		$clauses['groupby'] = "{$wpdb->posts}.ID";
		$clauses['orderby'] = $this->get_orderby();

		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		return $clauses;
	}

	/**
	 * @return string
	 */
	protected function get_orderby() {
		global $wpdb;

		$order = esc_sql( $this->get_order() );
		$cast_type = CastType::create_from_data_type( $this->data_type )->get_value();

		return sprintf( "CAST( acsort_postmeta.meta_value AS %s ) $order, {$wpdb->posts}.ID $order", $cast_type );
	}

}