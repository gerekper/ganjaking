<?php

namespace ACA\MetaBox\Editing\ServiceFactory;

use ACA\MetaBox\Column;
use ACA\MetaBox\Editing\StorageFactory;
use ACP\Editing\Service;
use ACP\Editing\Service\Basic;
use ACP\Editing\View;

final class Number {

	public function create( Column $column ): Service {
		return new Basic(
			$this->create_view( $column ),
			( new StorageFactory() )->create( $column )
		);
	}

	private function create_view( Column $column ): View {
		if ( $column->is_clonable() ) {
			return ( new View\MultiInput() )->set_sub_type( 'number' )->set_clear_button( true );
		}

		return ( new View\Number() )
			->set_min( $column->get_field_setting( 'min' ) )
			->set_max( $column->get_field_setting( 'max' ) )
			->set_step( $column->get_field_setting( 'step' ) )
			->set_clear_button( true );
	}

}