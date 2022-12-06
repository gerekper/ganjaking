<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;

class LinkCount extends AbstractModel {

	/**
	 * @var array
	 */
	private $domains;

	public function __construct( array $domains ) {
		parent::__construct();

		$this->domains = $domains;
	}

	public function get_sorting_vars() {
		add_filter( 'posts_clauses', [ $this, 'sorting_clauses_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	private function sql_replace( $string ) {
		global $wpdb;

		return $wpdb->prepare( "( LENGTH( $wpdb->posts.post_content ) - LENGTH( REPLACE ( $wpdb->posts.post_content, %s, '' ) ) ) / LENGTH( %s )", $string, $string );
	}

	private function sql_prefix_with_href( $url ) {
		return sprintf( 'href="%s', esc_sql( $url ) );
	}

	public function sorting_clauses_callback( $clauses ) {
		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$domains = array_map( [ $this, 'sql_prefix_with_href' ], $this->domains );
		$field = implode( ' + ', array_map( [ $this, 'sql_replace' ], $domains ) );
		$field = sprintf( 'ROUND( %s )', $field );

		$clauses['orderby'] = SqlOrderByFactory::create( $field, $this->get_order(), [ 'esc_sql' => false, 'empty_values' => [ 0 ] ] ); // Field has already been escaped
		$clauses['orderby'] .= sprintf( ", $wpdb->posts.post_date %s", esc_sql( $this->get_order() ) );

		return $clauses;
	}

}