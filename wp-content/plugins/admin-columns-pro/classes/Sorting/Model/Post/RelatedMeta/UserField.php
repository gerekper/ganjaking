<?php

namespace ACP\Sorting\Model\Post\RelatedMeta;

use ACP\Sorting\AbstractModel;

class UserField extends AbstractModel {

	/**
	 * @var string
	 */
	private $field;

	/**
	 * @var string
	 */
	private $meta_key;

	public function __construct( $field, $meta_key ) {
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
		global $wpdb;

		$order = esc_sql( $this->get_order() );

		$join_type = $this->show_empty
			? 'LEFT'
			: 'INNER';

		$clauses['fields'] .= sprintf( ", acsort_users.%s AS acsort_user_field", esc_sql( $this->field ) );

		$clauses['join'] .= $wpdb->prepare( "
			{$join_type} JOIN $wpdb->postmeta AS acsort_postmeta 
				ON $wpdb->posts.ID = acsort_postmeta.post_id
				AND acsort_postmeta.meta_key = %s
			{$join_type} JOIN $wpdb->users AS acsort_users 
				ON acsort_users.ID = acsort_postmeta.meta_value 
			", $this->meta_key );

		if ( ! $this->show_empty ) {
			$clauses['where'] .= sprintf( " AND acsort_users.%s <> ''", esc_sql( $this->field ) );
		}

		$clauses['orderby'] = "acsort_user_field $order, $wpdb->posts.ID $order";
		$clauses['groupby'] = "{$wpdb->posts}.ID";

		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		return $clauses;
	}

}