<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;

class PostField extends AbstractModel {

	/**
	 * @var string
	 */
	protected $field;

	public function __construct( $field ) {
		parent::__construct();

		$this->field = (string) $field;
	}

	public function get_sorting_vars() {
		add_filter( 'posts_orderby', [ $this, 'posts_orderby_callback' ] );

		if ( ! $this->show_empty ) {
			add_filter( 'posts_where', [ $this, 'posts_where_callback' ] );
		}

		return [
			'suppress_filters' => false,
		];
	}

	public function posts_orderby_callback() {
		global $wpdb;

		remove_filter( 'posts_orderby', [ $this, __FUNCTION__ ] );

		return sprintf( '%s.`%s` %s', $wpdb->posts, esc_sql( $this->field ), $this->get_order() );
	}

	public function posts_where_callback( $where ) {
		global $wpdb;

		remove_filter( 'posts_where', [ $this, __FUNCTION__ ] );

		$where .= sprintf( " AND %s.%s <> ''", $wpdb->posts, esc_sql( $this->field ) );

		return $where;
	}

}