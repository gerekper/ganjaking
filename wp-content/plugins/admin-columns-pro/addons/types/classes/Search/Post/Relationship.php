<?php

namespace ACA\Types\Search\Post;

use AC;
use ACP;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;
use Toolset_Relationship_Definition_Repository;
use Toolset_Relationship_Table_Name;

class Relationship extends ACP\Search\Comparison
	implements Comparison\SearchableValues {

	/**
	 * @var string
	 */
	private $related_post_type;

	/**
	 * @var string
	 */
	private $relationship;

	/**
	 * @var string
	 */
	private $role;

	/**
	 * @var string
	 */
	private $return_role;

	public function __construct( $relationship, $related_post_type, $role, $return_role ) {
		$this->relationship = $relationship;
		$this->role = $role;
		$this->related_post_type = $related_post_type;
		$this->return_role = $return_role;

		parent::__construct( $this->get_default_operators() );
	}

	protected function get_default_operators() {
		return new Operators( [
			Operators::EQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );
	}

	/**
	 * @return int
	 */
	private function get_relationship_id() {
		$relationship = Toolset_Relationship_Definition_Repository::get_instance()->get_definition( $this->relationship );

		return $relationship ? $relationship->get_row_id() : 0;
	}

	protected function create_query_bindings( $operator, Value $value ) {
		global $wpdb;

		if ( in_array( $operator, [ Operators::IS_EMPTY, Operators::NOT_IS_EMPTY ] ) ) {
			return $this->get_associated_bindings( $operator );
		}

		$bindings = new ACP\Search\Query\Bindings();

		$posts = toolset_get_related_posts( $value->get_value(), $this->relationship, [ 'query_by_role' => $this->role, 'role_to_return' => $this->return_role, 'limit' => -1 ] );
		$posts = empty( $posts ) ? [ 0 ] : $posts;

		return $bindings->where( sprintf( "{$wpdb->posts}.ID IN( '%s')", implode( "','", array_map( 'esc_sql', $posts ) ) ) );
	}

	private function get_associated_bindings( $operator ) {
		global $wpdb;

		$table = ( new Toolset_Relationship_Table_Name() )->association_table();
		$column = ( 'child' === $this->role ) ? 'parent_id' : 'child_id';
		$in = ( $operator === Operators::NOT_IS_EMPTY ) ? 'IN' : 'NOT IN';

		$sql = $wpdb->prepare( "
				SELECT DISTINCT($column) 
				FROM {$table}
				WHERE relationship_id = %d", $this->get_relationship_id() );

		$bindings = new ACP\Search\Query\Bindings();

		return $bindings->where( sprintf( "{$wpdb->posts}.ID {$in}( {$sql} )" ) );
	}

	/**
	 * @param $search
	 * @param $page
	 *
	 * @return AC\Helper\Select\Options\Paginated
	 */
	public function get_values( $search, $page ) {
		$entities = new ACP\Helper\Select\Entities\Post( [
			'paged'     => $page,
			'post_type' => $this->related_post_type,
			's'         => $search,
		] );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new ACP\Helper\Select\Formatter\PostTitle( $entities )
		);
	}

}