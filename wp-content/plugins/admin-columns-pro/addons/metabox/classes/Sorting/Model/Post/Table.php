<?php

namespace ACA\MetaBox\Sorting\Model\Post;

use ACA\MetaBox\Sorting\TableOrderByFactory;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Type\DataType;

class Table extends AbstractModel {

	/**
	 * @var string
	 */
	private $table_name;

	/**
	 * @var string
	 */
	private $meta_key;

	public function __construct( $table_name, $meta_key, DataType $data_type = null ) {
		parent::__construct( $data_type );

		$this->table_name = (string) $table_name;
		$this->meta_key = (string) $meta_key;
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

		$clauses['join'] .= sprintf( "
			LEFT JOIN %s AS acsort_ct 
				ON acsort_ct.ID = $wpdb->posts.ID
			",
			esc_sql( $this->table_name )
		);
		$clauses['orderby'] = TableOrderByFactory::create( $this->meta_key, $this->data_type, $this->get_order() );

		return $clauses;
	}

}