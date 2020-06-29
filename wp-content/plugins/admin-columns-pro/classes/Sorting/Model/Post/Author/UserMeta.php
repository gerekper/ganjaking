<?php

namespace ACP\Sorting\Model\Post\Author;

use ACP;
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
		global $wpdb;

		$order = esc_sql( $this->get_order() );

		$join_type = $this->show_empty
			? 'LEFT'
			: 'INNER';

		$clauses['join'] .= " {$join_type} JOIN {$wpdb->usermeta} AS acsort_usermeta ON {$wpdb->posts}.post_author = acsort_usermeta.user_id";
		$clauses['where'] .= $wpdb->prepare( " AND acsort_usermeta.meta_key = %s", $this->meta_key );

		if ( ! $this->show_empty ) {
			$clauses['where'] .= " AND acsort_usermeta.meta_value <> ''";
		}

		$clauses['orderby'] = "acsort_usermeta.meta_value $order, {$wpdb->posts}.ID $order";
		$clauses['groupby'] = "{$wpdb->posts}.ID";

		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		return $clauses;
	}

}