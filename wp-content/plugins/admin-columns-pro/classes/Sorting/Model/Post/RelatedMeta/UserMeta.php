<?php

namespace ACP\Sorting\Model\Post\RelatedMeta;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;

class UserMeta extends AbstractModel {

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

		$this->meta_key = $meta_key;
		$this->meta_field = $meta_field;
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
			LEFT JOIN $wpdb->users AS acsort_users ON acsort_users.ID = acsort_postmeta.meta_value
			LEFT JOIN $wpdb->usermeta AS acsort_usermeta ON acsort_usermeta.user_id = acsort_users.ID
				AND acsort_usermeta.meta_key = %s
			", $this->meta_key, $this->meta_field );

		$clauses['orderby'] = SqlOrderByFactory::create( "acsort_usermeta.meta_value", $this->get_order() );
		$clauses['orderby'] .= sprintf( ", $wpdb->posts.ID %s", esc_sql( $this->get_order() ) );
		$clauses['groupby'] = "$wpdb->posts.ID";

		return $clauses;
	}

}