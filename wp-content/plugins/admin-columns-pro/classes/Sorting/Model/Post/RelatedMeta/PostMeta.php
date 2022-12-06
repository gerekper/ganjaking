<?php

namespace ACP\Sorting\Model\Post\RelatedMeta;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;

class PostMeta extends AbstractModel {

	/**
	 * @var string
	 */
	private $meta_field;

	/**
	 * @var string
	 */
	private $meta_key;

	public function __construct( string $meta_field, string $meta_key ) {
		parent::__construct();

		$this->meta_field = $meta_field;
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
			LEFT JOIN $wpdb->postmeta AS acsort_pm ON $wpdb->posts.ID = acsort_pm.post_id
				AND acsort_pm.meta_key = %s 
			LEFT JOIN $wpdb->postmeta AS acsort_pm2 ON acsort_pm.meta_value = acsort_pm2.post_id
				AND acsort_pm2.meta_key = %s 
			", $this->meta_key, $this->meta_field );

		$clauses['groupby'] = "$wpdb->posts.ID";
		$clauses['orderby'] = SqlOrderByFactory::create( "acsort_pm2.meta_value", $this->get_order() );
		$clauses['orderby'] .= sprintf( ", $wpdb->posts.ID %s", esc_sql( $this->get_order() ) );

		return $clauses;
	}

}