<?php

namespace ACA\MetaBox\Editing\ServiceFactory;

use ACA\MetaBox\Column;
use ACA\MetaBox\Editing\StorageFactory;
use ACP\Editing\Service;
use ACP\Editing\Service\Basic;
use ACP\Editing\View;

final class Input {

	public function create( Column $column ): Service {
		$view = $this->get_view( $column )->set_clear_button( true );

		if ( $view instanceof View\Placeholder ) {
			$view->set_placeholder( $column->get_field_setting( 'placeholder' ) );
		}

		return new Basic(
			$this->get_view( $column )->set_clear_button( true ),
			( new StorageFactory() )->create( $column )
		);
	}

	private function get_view( Column $column ) {
		if ( $column->is_clonable() ) {
			return ( new View\MultiInput() )->set_clear_button( true )->set_sub_type( $this->get_input_type( $column ) );
		}

		switch ( $column->get_field_setting( 'type' ) ) {
			case 'email':
				return new View\Email();
			case 'url':
				return new View\Url();
			case 'color':
				return new View\Color();
			case 'wysiwyg':
			case 'textarea':
				return new View\TextArea();
			default:
				return new View\Text();
		}
	}

	private function get_input_type( Column $column ): string {
		switch ( $column->get_field_setting( 'type' ) ) {
			case 'email':
				return 'email';
			case 'url':
				return 'url';
			case 'color':
				return 'color';
			case 'wysiwyg':
			case 'textarea':
				return 'textarea';
			default:
				return 'text';
		}
	}

}