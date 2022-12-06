<?php

namespace ACP\Sorting\Model\Post\RelatedMeta;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;

class PostField extends AbstractModel {

	/**
	 * @var string
	 */
	private $post_field;

	/**
	 * @var string
	 */
	private $meta_key;

	public function __construct( string $post_field, string $meta_key ) {
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
		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$clauses['join'] .= $wpdb->prepare( "
			LEFT JOIN $wpdb->postmeta AS acsort_postmeta ON $wpdb->posts.ID = acsort_postmeta.post_id
				AND acsort_postmeta.meta_key = %s 
			", $this->meta_key );

		$clauses['join'] .= "\nLEFT JOIN $wpdb->posts AS acsort_posts ON acsort_posts.ID = acsort_postmeta.meta_value";
		$clauses['groupby'] = "$wpdb->posts.ID";
		$clauses['orderby'] = SqlOrderByFactory::create( sprintf( "acsort_posts.%s", esc_sql( $this->post_field ) ), $this->get_order() );

		return $clauses;
	}

}