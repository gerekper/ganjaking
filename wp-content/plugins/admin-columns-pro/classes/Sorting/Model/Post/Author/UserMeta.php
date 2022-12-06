<?php

namespace ACP\Sorting\Model\Post\Author;

use ACP;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\DataType;

class UserMeta extends ACP\Sorting\AbstractModel {

	/**
	 * @var string
	 */
	private $meta_key;

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
		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$clauses['join'] .= "\nLEFT JOIN $wpdb->usermeta AS acsort_usermeta ON $wpdb->posts.post_author = acsort_usermeta.user_id";
		$clauses['where'] .= $wpdb->prepare( "\nAND acsort_usermeta.meta_key = %s", $this->meta_key );
		$clauses['groupby'] = "$wpdb->posts.ID";
		$clauses['orderby'] = SqlOrderByFactory::create( "acsort_usermeta.meta_value", $this->get_order(), [ 'cast_type' => (string) CastType::create_from_data_type( $this->data_type ) ] );
		$clauses['orderby'] .= sprintf( "\n, $wpdb->posts.ID %s", esc_sql( $this->get_order() ) );

		return $clauses;
	}

}