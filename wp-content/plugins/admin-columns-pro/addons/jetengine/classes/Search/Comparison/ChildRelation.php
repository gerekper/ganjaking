<?php

namespace ACA\JetEngine\Search\Comparison;

use ACP;
use ACP\Helper\Select;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;
use ACP\Search\Value;
use Jet_Engine\Relations\Relation as JetEngineRelation;
use Jet_Engine\Relations\Storage;

class ChildRelation extends ACP\Search\Comparison implements SearchableValues {

	/**
	 * @var JetEngineRelation
	 */
	private $relation;

	/**
	 * @var string
	 */
	private $related_post_type;

	public function __construct( JetEngineRelation $relation, $post_type ) {
		parent::__construct( new Operators( [
			Operators::EQ,
		] ) );

		$this->relation = $relation;
		$this->related_post_type = $post_type;
	}

	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb;

		$bindings = new ACP\Search\Query\Bindings();
		$ids = array_unique( $this->get_related_ids( $value ) );
		$in_type = $operator === Operators::EQ ? 'IN' : 'NOT IN';

		$ids = implode( "','", array_map( 'esc_sql', $ids ) );

		$bindings->where( "{$wpdb->posts}.ID {$in_type}( '{$ids}' )" );

		return $bindings;
	}

	protected function get_related_ids( Value $value ) {
		/** @var Storage\DB $db */
		$db = $this->relation->db;

		$results = $db->query( [
			'rel_id'           => $this->relation->get_id(),
			'parent_object_id' => $value->get_value(),
		] );

		return wp_list_pluck( $results, 'child_object_id' );
	}

	public function get_values( $search, $page ) {
		$args = [
			'post_type'     => $this->related_post_type,
			'search_fields' => [ 'post_title', 'ID' ],
		];

		return new Select\Paginated\Posts( $search, $page, $args );
	}

}