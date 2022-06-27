<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;

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

		$sql = $wpdb->prepare( "( LENGTH( $wpdb->posts.post_content ) - LENGTH( REPLACE ( $wpdb->posts.post_content, %s, '' ) ) ) / LENGTH( %s )", $string, $string );

		return $sql;
	}

	private function sql_prefix_with_href( $url ) {
		return sprintf( 'href="%s', $url );
	}

	public function sorting_clauses_callback( $clauses ) {
		global $wpdb;

		$domains = $this->domains;
		$domains = array_map( [ $this, 'sql_prefix_with_href' ], $domains );

		$sql = implode( ' + ', array_map( [ $this, 'sql_replace' ], $domains ) );

		$clauses['fields'] .= ", ROUND ( $sql ) AS acsort_link_count";

		$clauses['orderby'] = sprintf( "acsort_link_count %s, $wpdb->posts.post_date", $this->get_order() );

		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		return $clauses;
	}

}