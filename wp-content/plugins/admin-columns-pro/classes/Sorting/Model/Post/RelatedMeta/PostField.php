<?php

namespace ACP\Sorting\Model\Post\RelatedMeta;

use ACP\Sorting\AbstractModel;

class PostField extends AbstractModel {

	/**
	 * @var string
	 */
	private $post_field;

	/**
	 * @var string
	 */
	private $meta_key;

	public function __construct( $post_field, $meta_key ) {
		parent::__construct();

		$this->post_field = $post_field;
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

		$clauses['fields'] .= sprintf( ", acsort_posts.%s AS acsort_field", esc_sql( $this->post_field ) );

		$clauses['join'] .= $wpdb->prepare( "
			{$join_type} JOIN $wpdb->postmeta AS acsort_postmeta ON $wpdb->posts.ID = acsort_postmeta.post_id
				AND acsort_postmeta.meta_key = %s 
			", $this->meta_key );

		if ( ! $this->show_empty ) {
			$clauses['join'] .= " AND acsort_postmeta.meta_value !=''";
		}

		$clauses['join'] .= " {$join_type} JOIN $wpdb->posts AS acsort_posts ON acsort_posts.ID = acsort_postmeta.meta_value";
		$clauses['orderby'] = "acsort_field $order, $wpdb->posts.ID $order";
		$clauses['groupby'] = "{$wpdb->posts}.ID";

		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		return $clauses;
	}

}