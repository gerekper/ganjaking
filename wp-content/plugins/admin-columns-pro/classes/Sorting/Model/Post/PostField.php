<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;

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

		return [
			'suppress_filters' => false,
		];
	}

	public function posts_orderby_callback() {
		remove_filter( 'posts_orderby', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$field = sprintf( '%s.`%s`', $wpdb->posts, esc_sql( $this->field ) );

		return SqlOrderByFactory::create( $field, $this->get_order() );
	}

}