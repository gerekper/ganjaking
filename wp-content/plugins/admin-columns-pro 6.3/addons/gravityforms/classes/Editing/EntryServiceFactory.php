<?php

namespace ACA\GravityForms\Editing;

use AC\Helper\Select\Option;
use AC\Type\ToggleOptions;
use ACA\GravityForms\Editing;
use ACA\GravityForms\Field\Field;
use ACA\GravityForms\Field\Type;
use ACP;
use ACP\Editing\View;

class EntryServiceFactory {

	/**
	 * @param Field $field
	 *
	 * @return ACP\Editing\Service|false
	 */
	public function create( Field $field ) {
		switch ( true ) {

			case $field instanceof Type\Date:
				return new ACP\Editing\Service\Date(
					( new View\Date() )->set_clear_button( true ),
					new Editing\Storage\Entry( $field->get_id() )
				);

			case $field instanceof Type\CheckboxGroup:
				return new ACP\Editing\Service\Basic(
					$this->create_view( $field ),
					new Editing\Storage\Entry\Checkbox( $field )
				);

			case $field instanceof Type\Select:
				$storage = $field->is_multiple()
					? new Editing\Storage\Entry\MultiSelect( $field )
					: new Editing\Storage\Entry( $field->get_id() );

				return new ACP\Editing\Service\Basic( $this->create_view( $field ), $storage );

			case $field instanceof Type\Checkbox:
			case $field instanceof Type\ProductSelect:
			case $field instanceof Type\Radio:
			case $field instanceof Type\Textarea:
			case $field instanceof Type\Number:
			case $field instanceof Type\Input:
				return new ACP\Editing\Service\Basic(
					$this->create_view( $field ),
					new Editing\Storage\Entry( $field->get_id() )
				);

			default:
				return false;
		}
	}

	/**
	 * @param Field $field
	 *
	 * @return View
	 */
	private function create_view( Field $field ) {
		return $this->set_default_view_properties( $field, $this->create_basic_view( $field ) );
	}

	/**
	 * @param Field $field
	 *
	 * @return View
	 */
	private function create_basic_view( Field $field ) {
		switch ( true ) {
			case $field instanceof Type\CheckboxGroup:
				return new View\CheckboxList( $field->get_options() );

			case $field instanceof Type\Checkbox:
				return new View\Toggle( new ToggleOptions(
					new Option( '' ), new Option( $field->get_value() )
				) );

			case $field instanceof Type\Radio:
			case $field instanceof Type\ProductSelect:
				return new View\Select( $field->get_options() );

			case $field instanceof Type\Select:
				return $field->is_multiple()
					? ( new View\AdvancedSelect( $field->get_options() ) )->set_multiple( true )
					: new View\Select( $field->get_options() );

			case $field instanceof Type\Number:
				$view = ( new View\Number() );
				if ( $field->has_range_min() ) {
					$view->set_min( $field->get_range_min() );
				}
				if ( $field->has_range_max() ) {
					$view->set_max( $field->get_range_max() );
				}

				return $view;

			case $field instanceof Type\Textarea:
				return new View\TextArea();

			case $field instanceof Type\Input:
				switch ( $field->get_input_type() ) {
					case 'email':
						return new View\Email();

					case 'url':
						return new View\Url();

					default:
						return new View\Text();
				}
			default:
				return new View\Text();
		}
	}

	private function set_default_view_properties( Field $field, View $view ) {
		return $view->set_clear_button( true )
		            ->set_required( $field->is_required() );
	}

}