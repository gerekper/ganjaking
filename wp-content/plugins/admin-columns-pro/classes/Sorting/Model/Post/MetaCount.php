<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;

/**
 * Sort a user list table on the number of times the meta_key is used by a user.
 * @since 5.2
 */
class MetaCount extends AbstractModel {

	/**
	 * @var string
	 */
	protected $meta_key;

	/**
	 * @param string
	 */
	public function __construct( $meta_key ) {
		parent::__construct();

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

		$clauses['fields'] .= ", COUNT( acsort_postmeta.meta_key ) AS acsort_metacount";
		$clauses['join'] .= $wpdb->prepare( "
			{$join_type} JOIN {$wpdb->postmeta} AS acsort_postmeta ON {$wpdb->posts}.ID = acsort_postmeta.post_id
			AND acsort_postmeta.meta_key = %s
		", $this->meta_key );

		if ( ! $this->show_empty ) {
			$clauses['join'] .= " AND acsort_postmeta.meta_value <> ''";
		}

		$clauses['groupby'] = "{$wpdb->posts}.ID";
		$clauses['orderby'] = "acsort_metacount {$order}, {$wpdb->posts}.ID {$order}";

		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		return $clauses;
	}

}