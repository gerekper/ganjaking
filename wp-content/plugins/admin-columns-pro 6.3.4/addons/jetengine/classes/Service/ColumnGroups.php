<?php

namespace ACA\JetEngine\Service;

use AC;

final class ColumnGroups implements AC\Registerable {

	const JET_ENGINE = 'jet_engine';
	const JET_ENGINE_RELATION = 'jet_engine_relation';

	public function register(): void
    {
		add_action( 'ac/column_groups', [ $this, 'register_column_groups' ] );
	}

	public function register_column_groups( AC\Groups $groups ) {
		$groups->add( self::JET_ENGINE, __( 'JetEngine', 'codepress-admin-columns' ), 11 );
		$groups->add( self::JET_ENGINE_RELATION, __( 'JetEngine Relationship', 'codepress-admin-columns' ), 11 );
	}

}