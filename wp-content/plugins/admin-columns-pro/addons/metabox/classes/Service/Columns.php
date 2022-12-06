<?php

namespace ACA\MetaBox\Service;

use AC;
use AC\Registerable;
use ACA\MetaBox\ColumnFactory;
use ACA\MetaBox\RelationColumnFactory;
use ACA\MetaBox\RelationshipRepository;
use ACP;

class Columns implements Registerable {

	/**
	 * @var ColumnFactory
	 */
	private $column_factory;

	/**
	 * @var RelationColumnFactory
	 */
	private $relation_column_factory;

	/**
	 * @var RelationshipRepository
	 */
	private $relationship_repository;

	public function __construct( ColumnFactory $column_factory, RelationColumnFactory $relation_column_factory, RelationshipRepository $relationship_repository ) {
		$this->column_factory = $column_factory;
		$this->relation_column_factory = $relation_column_factory;
		$this->relationship_repository = $relationship_repository;
	}

	public function register() {
		add_action( 'ac/column_groups', [ $this, 'register_column_groups' ] );
		add_action( 'ac/column_types', [ $this, 'add_columns' ] );
		add_action( 'ac/column_types', [ $this, 'add_relation_columns' ] );
	}

	public function register_column_groups( AC\Groups $groups ) {
		$groups->register_group( 'metabox', 'MetaBox', 11 );
		$groups->register_group( 'metabox_relation', 'MetaBox Relation', 11 );
	}

	public function add_columns( AC\ListScreen $list_screen ) {
		$fields = $this->get_fields_by_list_screen( $list_screen );

		foreach ( $fields as $field ) {
			$column = $this->column_factory->create( $field );

			if ( $column ) {
				$list_screen->register_column_type( $column );
			}
		}
	}

	/**
	 * @return array
	 */
	private function get_fields_by_list_screen( $list_screen ) {
		switch ( true ) {
			case $list_screen instanceof ACP\ListScreen\Media:
			case $list_screen instanceof ACP\ListScreen\Post:

				return rwmb_get_object_fields( $list_screen->get_post_type() );
			case $list_screen instanceof ACP\ListScreen\User:

				return rwmb_get_object_fields( 'user', 'user' );
			case $list_screen instanceof ACP\ListScreen\Taxonomy:

				return rwmb_get_object_fields( $list_screen->get_taxonomy(), 'term' );
			case $list_screen instanceof ACP\ListScreen\Comment:

				return $this->get_comment_fields();
			default:
				return [];
		}
	}

	/**
	 * @return array
	 */
	private function get_comment_fields() {
		if ( ! class_exists( 'MB_Comment_Meta_Box', false ) ) {
			return [];
		}

		$fields = [];
		$metaboxes = rwmb_get_registry( 'meta_box' )->get_by( [ 'object_type' => 'comment' ] );

		foreach ( $metaboxes as $metabox ) {
			if ( ! $metabox instanceof MB_Comment_Meta_Box ) {
				continue;
			}

			foreach ( $metabox->fields as $field ) {
				$fields[ $field['id'] ] = $field;
			}
		}

		return $fields;
	}

	public function add_relation_columns( AC\ListScreen $list_screen ) {
		if ( ! class_exists( 'MB_Relationships_API', false ) ) {
			return;
		}

		foreach ( $this->relationship_repository->get_by_list_screen( $list_screen ) as $relationship ) {
			$column = $this->relation_column_factory->create( $relationship );

			if ( $column instanceof AC\Column ) {
				$list_screen->register_column_type( $column );
			}
		}
	}

}