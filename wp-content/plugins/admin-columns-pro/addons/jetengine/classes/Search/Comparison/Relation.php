<?php

namespace ACA\JetEngine\Search\Comparison;

use ACP;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;
use ACP\Search\Value;
use Jet_Engine\Relations\Relation as JetEngineRelation;
use Jet_Engine\Relations\Storage;

abstract class Relation extends ACP\Search\Comparison implements SearchableValues {

	/**
	 * @var JetEngineRelation
	 */
	private $relation;

	/**
	 * @var bool
	 */
	private $is_parent;

	public function __construct( JetEngineRelation $relation, $is_parent ) {
		parent::__construct( new Operators( [
			Operators::EQ,
		] ) );

		$this->relation = $relation;
		$this->is_parent = (bool) $is_parent;
	}

	private function get_db_id_column() {
		global $wpdb;

		$argument = $this->is_parent ? 'parent_object' : 'child_object';
		$field = explode( '::', $this->relation->get_args( $argument ) )[0];

		switch ( $field ) {
			case 'mix':
				return sprintf( '%s.%s', $wpdb->users, 'ID' );
			case 'terms':
				return 't.term_id';
			case 'posts':
				return sprintf( '%s.%s', $wpdb->posts, 'ID' );
		}

		return '';
	}

	protected function create_query_bindings( $operator, Value $value ) {
		$bindings = new ACP\Search\Query\Bindings();
		$ids = array_unique( $this->get_related_ids( $value ) );
		$in_type = $operator === Operators::EQ ? 'IN' : 'NOT IN';

		$ids = implode( "','", array_map( 'esc_sql', $ids ) );
		$column = $this->get_db_id_column();

		$bindings->where( "{$column} {$in_type}( '{$ids}' )" );

		return $bindings;
	}

	protected function get_related_ids( Value $value ) {
		$query_arg = $this->is_parent ? 'child_object_id' : 'parent_object_id';
		$field = $this->is_parent ? 'parent_object_id' : 'child_object_id';

		/** @var Storage\DB $db */
		$db = $this->relation->db;

		$results = $db->query( [
			'rel_id'   => $this->relation->get_id(),
			$query_arg => $value->get_value(),
		] );

		return wp_list_pluck( $results, $field );
	}

}