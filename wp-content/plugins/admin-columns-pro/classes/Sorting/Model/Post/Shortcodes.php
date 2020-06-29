<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\FormatValue\ShortCodeCount;
use ACP\Sorting\Type\DataType;

class Shortcodes extends FieldFormat {

	public function __construct() {
		parent::__construct( 'post_content', new ShortCodeCount(), new DataType( DataType::NUMERIC ) );
	}

	public function get_sorting_vars() {
		add_filter( 'posts_where', [ $this, 'posts_where_callback' ] );

		return parent::get_sorting_vars();
	}

	public function posts_where_callback( $where ) {
		global $wpdb;

		remove_filter( 'posts_where', [ $this, __FUNCTION__ ] );

		$where .= " AND {$wpdb->posts}.post_content LIKE '%[%' AND {$wpdb->posts}.post_content LIKE '%]%' ";

		return $where;
	}

}