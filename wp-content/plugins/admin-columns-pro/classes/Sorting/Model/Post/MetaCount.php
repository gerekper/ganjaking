<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;

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
		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$clauses['join'] .= $wpdb->prepare( "
			LEFT JOIN $wpdb->postmeta AS acsort_postmeta ON $wpdb->posts.ID = acsort_postmeta.post_id
			AND acsort_postmeta.meta_key = %s
		", $this->meta_key );

		$clauses['groupby'] = "$wpdb->posts.ID";
		$clauses['orderby'] = SqlOrderByFactory::create_with_count( "acsort_postmeta.meta_key", $this->get_order() );
		$clauses['orderby'] .= sprintf( ", $wpdb->posts.post_date %s", esc_sql( $this->get_order() ) );

		return $clauses;
	}

}