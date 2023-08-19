<?php

namespace ACA\ACF\Search\Comparison;

use AC\MetaType;
use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class Repeater extends Comparison {

	/**
	 * @var string
	 */
	protected $meta_type;

	/**
	 * @var string
	 */
	protected $parent_key;

	/**
	 * @var string
	 */
	protected $sub_key;

	/**
	 * @var boolean
	 */
	protected $serialized;

	public function __construct( $meta_type, $parent_key, $sub_key, $operators, $value_type = null, $serialized = false, $labels = null ) {
		$this->meta_type = (string) $meta_type;
		$this->parent_key = (string) $parent_key;
		$this->sub_key = (string) $sub_key;
		$this->serialized = (bool) $serialized;

		parent::__construct( $operators, $value_type, $labels );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb;

		if ( $this->serialized ) {
			$operator = $this->map_serialize_operators( $operator );
			$value = $this->serialize_value( $value );
		}

		$bindings = new Bindings();
		$alias = $bindings->get_unique_alias( 'metatable' );
		$meta_key = $this->parent_key . '%' . $this->sub_key;
		$comparison = ComparisonFactory::create( $alias . '.meta_value', $operator, $value )->prepare();

		switch ( $this->meta_type ) {
			case MetaType::COMMENT:
				$bindings->join( "JOIN $wpdb->commentmeta AS $alias ON {$wpdb->comments}.comment_ID = {$alias}.comment_id AND {$alias}.meta_key LIKE '{$meta_key}' AND {$comparison}" );

				break;
			case MetaType::TERM:
				$bindings->join( "JOIN $wpdb->termmeta AS $alias ON {$wpdb->terms}.term_id = {$alias}.term_id AND {$alias}.meta_key LIKE '{$meta_key}' AND {$comparison}" );
				$bindings->group_by( "{$wpdb->terms}.term_id" );

				break;
			case MetaType::USER:
				$bindings->join( "JOIN $wpdb->usermeta AS $alias ON {$wpdb->users}.ID = {$alias}.user_id AND {$alias}.meta_key LIKE '{$meta_key}' AND {$comparison}" );
				$bindings->group_by( "{$wpdb->users}.ID" );

				break;
			case MetaType::POST:
				$bindings->join( "JOIN $wpdb->postmeta AS $alias ON {$wpdb->posts}.ID = {$alias}.post_id AND {$alias}.meta_key LIKE '{$meta_key}' AND {$comparison}" );
				$bindings->group_by( "{$wpdb->posts}.ID" );
		}

		return $bindings;
	}

	protected function map_serialize_operators( $operator ) {
		$mapping = [
			Operators::EQ  => Operators::CONTAINS,
			Operators::NEQ => Operators::NOT_CONTAINS,
		];

		return array_key_exists( $operator, $mapping ) ? $mapping[ $operator ] : $operator;
	}

	protected function serialize_value( Value $value ) {
		return new Value(
			serialize( $value->get_value() ),
			$value->get_type()
		);
	}

}