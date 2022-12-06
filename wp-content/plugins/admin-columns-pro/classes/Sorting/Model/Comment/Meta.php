<?php

namespace ACP\Sorting\Model\Comment;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\DataType;

class Meta extends AbstractModel {

	/**
	 * @var string
	 */
	private $meta_key;

	public function __construct( $meta_key, DataType $data_type = null ) {
		parent::__construct( $data_type );

		$this->meta_key = (string) $meta_key;
	}

	public function get_sorting_vars() {
		add_filter( 'comments_clauses', [ $this, 'comments_clauses_callback' ] );

		return [];
	}

	public function comments_clauses_callback( $clauses ) {
		remove_filter( 'comments_clauses', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$clauses['join'] .= $wpdb->prepare( "LEFT JOIN $wpdb->commentmeta AS acsort_commentmeta ON $wpdb->comments.comment_ID = acsort_commentmeta.comment_id AND acsort_commentmeta.meta_key = %s", $this->meta_key );
		$clauses['groupby'] = "$wpdb->comments.comment_ID";
		$clauses['orderby'] = $this->get_order_by();
		$clauses['orderby'] .= sprintf( ", $wpdb->comments.comment_ID %s", $this->get_order() );

		return $clauses;
	}

	protected function get_order_by(): string {
		return SqlOrderByFactory::create( "acsort_commentmeta.meta_value", $this->get_order(), [ 'cast_type' => (string) CastType::create_from_data_type( $this->data_type ) ] );
	}

}