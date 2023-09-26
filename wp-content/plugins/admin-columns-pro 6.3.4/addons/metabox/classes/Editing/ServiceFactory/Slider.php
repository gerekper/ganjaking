<?php

namespace ACA\MetaBox\Editing\ServiceFactory;

use ACA\MetaBox\Column;
use ACA\MetaBox\Editing\StorageFactory;
use ACP\Editing\Service;
use ACP\Editing\Service\Basic;
use ACP\Editing\View;

final class Slider {

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

		$view = new View\Number();

		$options = $column->get_field_setting( 'js-options' );

		$view->set_min( $options['min'] ?? 0 );
		$view->set_max( $options['max'] ?? 100 );
		$view->set_step( $options['step'] ?? 1 );

		return $view;
	}

}