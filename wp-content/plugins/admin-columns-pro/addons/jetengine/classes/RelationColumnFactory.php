<?php

namespace ACA\JetEngine;

use AC\ListScreen;
use ACA\JetEngine\Column\Relation;
use ACP;
use Jet_Engine\Relations;

final class RelationColumnFactory {

	/**
	 * @var ListScreen
	 */
	private $list_screen;

	public function __construct( ListScreen $list_screen ) {
		$this->list_screen = $list_screen;
	}

	private function is_parent( Relations\Relation $relation ) {
		$list_screen = $this->list_screen;
		switch ( true ) {
			case $list_screen instanceof ListScreen\User:
				return $relation->is_parent( 'mix', 'users' );
			case $list_screen instanceof ListScreen\Post:
				return $relation->is_parent( 'posts', $list_screen->get_post_type() );
			case $list_screen instanceof ACP\ListScreen\Taxonomy:
				return $relation->is_parent( 'terms', $list_screen->get_taxonomy() );
		}

		return false;
	}

	/**
	 * @param Relations\Relation $relation
	 *
	 * @return Relation
	 */
	public function create( Relations\Relation $relation ) {
		$argument_key = $this->is_parent( $relation ) ? 'child_object' : 'parent_object';
		$meta_type = explode( '::', $relation->get_args( $argument_key ) )[0];

		switch ( $meta_type ) {
			case 'mix':
				$column = new Column\Relation\User();
				break;
			case 'posts':
				$column = new Column\Relation\Post();
				break;
			case 'terms':
				$column = new Column\Relation\Term();
				break;
			default:
				return null;
		}

		$column->set_type( $relation->get_id() )
		       ->set_label( $relation->get_relation_name() )
		       ->set_config( $relation );

		return $column;
	}

}