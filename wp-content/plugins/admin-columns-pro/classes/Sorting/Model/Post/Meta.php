<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\DataType;

class Meta extends AbstractModel {

	/**
	 * @var string
	 */
	protected $meta_key;

	public function __construct( string $meta_key, DataType $data_type = null ) {
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
		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$clauses['join'] .= $wpdb->prepare( "
			LEFT JOIN $wpdb->postmeta AS acsort_postmeta ON $wpdb->posts.ID = acsort_postmeta.post_id
			AND acsort_postmeta.meta_key = %s
		", $this->meta_key );

		$clauses['groupby'] = "$wpdb->posts.ID";
		$clauses['orderby'] = $this->get_order_by();
		$clauses['orderby'] .= sprintf( ", $wpdb->posts.ID %s", $this->get_order() );

		return $clauses;
	}

	protected function get_order_by(): string {
		return SqlOrderByFactory::create( "acsort_postmeta.`meta_value`", $this->get_order(), [ 'cast_type' => (string) CastType::create_from_data_type( $this->data_type ) ] );
	}

}