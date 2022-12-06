<?php

namespace ACP\Sorting\Model\Post\RelatedMeta;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;

class UserField extends AbstractModel {

	/**
	 * @var string
	 */
	private $field;

	/**
	 * @var string
	 */
	private $meta_key;

	public function __construct( string $field, string $meta_key ) {
		parent::__construct();

		$this->field = $field;
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
			LEFT JOIN $wpdb->postmeta AS acsort_postmeta 
				ON $wpdb->posts.ID = acsort_postmeta.post_id
				AND acsort_postmeta.meta_key = %s
			LEFT JOIN $wpdb->users AS acsort_users 
				ON acsort_users.ID = acsort_postmeta.meta_value 
			", $this->meta_key );

		$clauses['orderby'] = SqlOrderByFactory::create( sprintf( "acsort_users.`%s`", esc_sql( $this->field ) ), $this->get_order() );
		$clauses['orderby'] .= sprintf( ", $wpdb->posts.ID %s", esc_sql( $this->get_order() ) );
		$clauses['groupby'] = "$wpdb->posts.ID";

		return $clauses;
	}

}