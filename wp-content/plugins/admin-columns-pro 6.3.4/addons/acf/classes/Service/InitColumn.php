<?php

namespace ACA\ACF\Service;

use AC;
use AC\Registerable;
use ACA\ACF\Column;
use ACA\ACF\ColumnInstantiator;

class InitColumn implements Registerable {

	/**
	 * @var ColumnInstantiator
	 */
	private $column_initiator;

	public function __construct( ColumnInstantiator $column_initiator ) {
		$this->column_initiator = $column_initiator;
	}

	public function register(): void
    {
		add_action( 'ac/list_screen/column_created', [ $this, 'initiate_column' ] );
	}

	public function initiate_column( AC\Column $column ) {
		if ( $column instanceof Column ) {
			$this->column_initiator->initiate( $column );
		}
	}

}