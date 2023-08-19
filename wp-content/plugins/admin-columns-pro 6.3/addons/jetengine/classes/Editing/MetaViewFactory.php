<?php

namespace ACA\JetEngine\Editing;

use AC\Helper\Select\Option;
use AC\Type\ToggleOptions;
use ACA\JetEngine\Field\Field;
use ACA\JetEngine\Field\MaxLength;
use ACA\JetEngine\Field\Type;
use ACP;

final class MetaViewFactory {

	/**
	 * @param Field $field
	 *
	 * @return ACP\Editing\View|null
	 */
	public function create( Field $field ) {
		$view = $this->get_view( $field );

		if ( ! $view ) {
			return null;
		}

		if ( $field->is_required() ) {
			$view->set_required( true );
		} else {
			$view->set_clear_button( true );
		}

		return $view;
	}

	/**
	 * @param Field $field
	 *
	 * @return ACP\Editing\View|null
	 */
	private function get_view( Field $field ) {
		switch ( true ) {
			case $field instanceof Type\ColorPicker:
				return new ACP\Editing\View\Color();

			case $field instanceof Type\Textarea:
				$view = new ACP\Editing\View\TextArea();
				if ( $field instanceof MaxLength && $field->has_maxlength() ) {
					$view->set_max_length( $field->get_maxlength() );
				}

				return $view;

			case $field instanceof Type\Number:
				$view = new ACP\Editing\View\Number();

				if ( $field->has_step() ) {
					$view->set_step( $field->get_step() );
				}
				if ( $field->has_min_value() ) {
					$view->set_min( $field->get_min_value() );
				}
				if ( $field->has_max_value() ) {
					$view->set_max( $field->get_max_value() );
				}

				return $view;

			case $field instanceof Type\Text:
				$view = new ACP\Editing\View\Text();
				if ( $field instanceof MaxLength && $field->has_maxlength() ) {
					$view->set_max_length( $field->get_maxlength() );
				}

				return $view;

			case $field instanceof Type\Time:
			case $field instanceof Type\IconPicker:

				return new ACP\Editing\View\Text();

			case $field instanceof Type\Gallery:
				return ( new ACP\Editing\View\Image() )->set_multiple( true );

			case $field instanceof Type\Media:
				return new ACP\Editing\View\Media();

			case $field instanceof Type\Posts:
				$view = new ACP\Editing\View\AjaxSelect();

				return $field->is_multiple()
					? $view->set_multiple( true )
					: $view;

			case $field instanceof Type\Switcher:
				return new ACP\Editing\View\Toggle( new ToggleOptions(
					new Option( 'false' ),
					new Option( 'true' )
				) );

			case $field instanceof Type\Checkbox:
				return new ACP\Editing\View\CheckboxList( $field->get_options() );

			case $field instanceof Type\Radio:
				return new ACP\Editing\View\Select( $field->get_options() );

			case $field instanceof Type\Select:
				$view = new ACP\Editing\View\AdvancedSelect( $field->get_options() );

				return $field->is_multiple()
					? $view->set_multiple( true )
					: $view;

			case $field instanceof Type\Wysiwyg:
				return new ACP\Editing\View\Wysiwyg();

			case $field instanceof Type\Date:
				return new ACP\Editing\View\Date();

			case $field instanceof Type\DateTime:
				return new ACP\Editing\View\DateTime();
		}

		return null;
	}

}