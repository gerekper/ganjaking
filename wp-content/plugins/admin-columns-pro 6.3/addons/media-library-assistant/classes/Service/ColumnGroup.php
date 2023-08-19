<?php
declare( strict_types=1 );

namespace ACA\MLA\Service;

use AC;
use AC\Registerable;

class ColumnGroup implements Registerable {

	public const NAME = 'media-library-assistant';

	public function register(): void
    {
		add_action( 'ac/column_groups', [ $this, 'register_column_group' ] );
	}

	public function register_column_group( AC\Groups $groups ) {
		$groups->add( self::NAME, __( 'Media Library Assistant' ), 25 );
	}

}