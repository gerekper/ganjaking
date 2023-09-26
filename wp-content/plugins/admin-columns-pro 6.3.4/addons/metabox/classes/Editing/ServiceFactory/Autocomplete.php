<?php

namespace ACA\MetaBox\Editing\ServiceFactory;

use ACA\MetaBox\Column;
use ACA\MetaBox\Editing\StorageFactory;
use ACP\Editing\Service\Basic;
use ACP\Editing\View\AdvancedSelect;

final class Autocomplete {

	public function create( Column $column ) {
		if ( $column->is_clonable() ) {
			return false;
		}

		if ( $column instanceof Column\Autocomplete && $column->is_ajax() ) {
			return false;
		}

		return new Basic(
			( new AdvancedSelect( $column->get_field_setting( 'options' ) ) )->set_multiple( true )->set_clear_button( true ),
			( new StorageFactory() )->create( $column )
		);
	}

}