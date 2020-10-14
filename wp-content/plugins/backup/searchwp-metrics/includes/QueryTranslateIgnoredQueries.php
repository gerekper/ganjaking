<?php

namespace SearchWP_Metrics;

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class QueryTranslateIgnoredQueries
 * @package SearchWP_Metrics
 */
class QueryTranslateIgnoredQueries extends Query {

	protected $hashes;

	private $defaults = array(
		'hashes' => array(),
	);


	function __construct( $args = array() ) {
		parent::__construct( $args );

		$args = wp_parse_args( $args, $this->defaults );

		if ( is_array( $args ) ) {
			foreach ( $args as $property => $val ) {
				$this->__set( $property, $val );
			}
		}
	}

	function set_sql_fields() {
		$this->sql['select'] = "SELECT `{$this->tables['queries']}`.`query`";
	}

	function set_sql_from() {
		$this->sql['from'] = "FROM `{$this->tables['queries']}`";
	}

	function set_sql_join() {}

	function set_sql_where() {
		$ignored_queries = array_filter( array_values( $this->hashes ), function( $value ) {
			return preg_match( '/^[a-f0-9]{32}$/', $value );
		} );

		$this->sql['where'] = "WHERE MD5(`{$this->tables['queries']}`.`query`) IN ('" . implode( "','", $ignored_queries ) . "')";
	}

	function set_sql_group_by() {}

	function set_sql_having() {}

	function set_sql_order_by() {
		$this->sql['orderby'] = "ORDER BY LOWER(`{$this->tables['queries']}`.`query`) ASC";
	}
}
