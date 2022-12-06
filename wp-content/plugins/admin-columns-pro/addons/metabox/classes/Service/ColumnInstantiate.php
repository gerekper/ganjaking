<?php

namespace ACA\MetaBox\Service;

use AC;
use ACA\MetaBox\Column;
use ACA\MetaBox\RelationshipRepository;

final class ColumnInstantiate implements AC\Registerable {

	/**
	 * @var RelationshipRepository
	 */
	private $relationship_repository;

	public function __construct( RelationshipRepository $relationship_repository ) {
		$this->relationship_repository = $relationship_repository;
	}

	public function register() {
		add_action( 'ac/list_screen/column_created', [ $this, 'configure_column' ], 10 );
	}

	public function configure_column( AC\Column $column ) {
		if ( $column instanceof Column\Relation ) {
			$relationship = $this->relationship_repository->get_by_column( $column );

			if ( $relationship ) {
				$column->set_relation( $relationship );
			}
		}
	}

}