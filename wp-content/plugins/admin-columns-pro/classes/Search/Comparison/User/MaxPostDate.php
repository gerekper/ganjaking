<?php

namespace ACP\Search\Comparison\User;

use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class MaxPostDate extends Comparison {

	/**
	 * @var string
	 */
	private $post_type;

	/**
	 * @var array
	 */
	private $post_stati;

	/**
	 * @var bool
	 */
	private $oldest_post;

	/**
	 * @param string $post_type ;
	 */
	public function __construct( $post_type, array $post_stati = [], $oldest_post = false ) {
		$operators = new Operators( [
			Operators::BETWEEN,
			Operators::GT,
			Operators::LT,
		] );

		$this->post_type = (string) $post_type;
		$this->post_stati = $post_stati;
		$this->oldest_post = (bool) $oldest_post;

		parent::__construct( $operators, Value::DATE, new Labels\Date() );
	}

	public function create_query_bindings( $operator, Value $value ) {
		global $wpdb;
		$alias = uniqid( 'acs', false );
		$comparison = ComparisonFactory::create( "$alias.date", $operator, $value )->prepare();

		$min_or_max = $this->oldest_post
			? 'MIN'
			: 'MAX';

		$sub_query = "SELECT post_author FROM (
						SELECT post_author, {$min_or_max}(post_date) AS date
						FROM {$wpdb->posts}
						WHERE post_type = '" . esc_sql( $this->post_type ) . "'
						AND post_status IN( " . $this->esc_sql_array( $this->post_stati ) . " )
						GROUP BY post_author
					) as $alias
					WHERE $comparison
		";

		$bindings = new Bindings();
		$bindings->where( sprintf( "{$wpdb->users}.ID IN( %s)", $sub_query ) );

		return $bindings;
	}

	private function esc_sql_array( $array ) {
		return sprintf( "'%s'", implode( "','", array_map( 'esc_sql', $array ) ) );
	}

}