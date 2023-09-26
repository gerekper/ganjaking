<?php

namespace ACA\JetEngine\Service;

use AC;
use AC\ListScreen;
use ACA\JetEngine\Column;
use ACA\JetEngine\RelationColumnFactory;
use ACA\JetEngine\Utils\Api;
use ACP;
use Jet_Engine\Relations\Relation;
use LogicException;

final class RelationalColumns implements AC\Registerable {

	public function register(): void
    {
		add_action( 'ac/column_types', [ $this, 'add_legacy_relational_columns' ] );
		add_action( 'ac/column_types', [ $this, 'add_relational_columns' ] );
	}

	public function add_legacy_relational_columns( ListScreen $list_screen ) {
		$legacy_manager = Api::Relations()->legacy;

		if ( null !== $legacy_manager && $list_screen instanceof AC\ListScreen\Post ) {
			$relations = $legacy_manager->get_relation_fields_for_post_type( $list_screen->get_post_type() );

			foreach ( $relations as $relation ) {
				$column = new Column\RelationLegacy();
				$column->set_type( $relation['name'] )
				       ->set_label( $relation['title'] )
				       ->set_config( Api::Relations()->get_relation_info( $relation['name'] ) );

				$list_screen->register_column_type( $column );
			}
		}
	}

	/**
	 * @param $post_type
	 *
	 * @return Relation[]
	 */
	private function get_relations_for_list_screen( ListScreen $list_screen ) {
		switch ( true ) {
			case $list_screen instanceof AC\ListScreen\User:
				return $this->get_relations_for_meta_type( 'mix', 'users' );
			case $list_screen instanceof AC\ListScreen\Post:
				return $this->get_relations_for_meta_type( 'posts', $list_screen->get_post_type() );
			case $list_screen instanceof ACP\ListScreen\Taxonomy:
				return $this->get_relations_for_meta_type( 'terms', $list_screen->get_taxonomy() );
			default:
				return [];
		}
	}

	public function add_relational_columns( ListScreen $list_screen ) {
		foreach ( $this->get_relations_for_list_screen( $list_screen ) as $relation ) {
			$factory = new RelationColumnFactory( $list_screen );
			$column = $factory->create( $relation );

			if ( $column ) {
				$list_screen->register_column_type( $column );
			}
		}
	}

	/**
	 * @param $type
	 *
	 * @return Relation[]
	 */
	private function get_relations_for_meta_type( $type, $value ) {
		if ( false === in_array( $type, [ 'posts', 'terms', 'mix' ] ) ) {
			throw new LogicException( 'Type is not supported' );
		}

		$relations = [];

		foreach ( Api::Relations()->get_active_relations() as $relation ) {
			if ( ! $relation instanceof Relation ) {
				continue;
			}

			$separator = sprintf( '%s::', $type );

			$child = explode( $separator, $relation->get_args( 'child_object' ) );
			$child = count( $child ) === 2 ? $child[1] : '';

			$parent = explode( $separator, $relation->get_args( 'parent_object' ) );
			$parent = count( $parent ) === 2 ? $parent[1] : '';

			if ( ! $relation->get_args( 'is_legacy' ) && in_array( $value, [ $child, $parent ] ) ) {
				$relations[] = $relation;
			}
		}

		return $relations;
	}

}