<?php

namespace ACP\Sorting\Model\Post\RelatedMeta;

use ACP\Sorting\AbstractModel;

class PostMeta extends AbstractModel {

	/**
	 * @var string
	 */
	private $meta_field;

	/**
	 * @var string
	 */
	private $meta_key;

	public function __construct( $meta_field, $meta_key ) {
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
		global $wpdb;

		$order = esc_sql( $this->get_order() );

		$join_type = $this->show_empty
			? 'LEFT'
			: 'INNER';

		$clauses['fields'] .= ", acsort_pm2.meta_value AS acsort_field";

		$clauses['join'] .= $wpdb->prepare( "
			{$join_type} JOIN $wpdb->postmeta AS acsort_pm ON $wpdb->posts.ID = acsort_pm.post_id
				AND acsort_pm.meta_key = %s 
			{$join_type} JOIN $wpdb->postmeta AS acsort_pm2 ON acsort_pm.meta_value = acsort_pm2.post_id
				AND acsort_pm2.meta_key = %s 
			", $this->meta_key, $this->meta_field );

		if ( ! $this->show_empty ) {
			$clauses['join'] .= " AND acsort_pm2.meta_value !=''";
		}

		$clauses['orderby'] = "acsort_field $order, $wpdb->posts.ID $order";
		$clauses['groupby'] = "$wpdb->posts.ID";

		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		return $clauses;
	}

}