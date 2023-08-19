<?php

namespace ACA\ACF\Editing;

use AC\Helper\Select\Option;
use AC\Type\ToggleOptions;
use ACA\ACF\Editing;
use ACA\ACF\Field;
use ACA\ACF\FieldType;
use ACP;
use ACP\Editing\View;

class ModelViewFactory {

	public function create( Field $field ) {
		$view = $this->create_view_type( $field );

		if ( $field->is_required() ) {
			$view->set_required( true );
		} else {
			$view->set_clear_button( true );
		}

		if ( $view instanceof View\Placeholder && $field instanceof Field\Placeholder ) {
			$view->set_placeholder( $field->get_placeholder() );
		}

		return $view;
	}

	/**
	 * @param Field $field
	 *
	 * @return View
	 */
	private function create_view_type( Field $field ) {
		switch ( $field->get_type() ) {
			case FieldType::TYPE_BOOLEAN:
				return new View\Toggle( new ToggleOptions(
					new Option( '0', __( 'False', 'codepress-admin-columns' ) ),
					new Option( '1', __( 'True', 'codepress-admin-columns' ) )
				) );
			case FieldType::TYPE_BUTTON_GROUP:
				return new View\Select( $field instanceof Field\Choices ? $field->get_choices() : [] );

			case FieldType::TYPE_CHECKBOX:
				return new View\CheckboxList( $field instanceof Field\Choices ? $field->get_choices() : [] );

			case FieldType::TYPE_DATE_TIME_PICKER:
				return ( new View\DateTime() )->set_week_start( $field->get_first_day() );

			case FieldType::TYPE_DATE_PICKER:
				return ( new View\Date() )->set_week_start( $field->get_first_day() );

			case FieldType::TYPE_WYSIWYG:
				return new View\Wysiwyg();

			case FieldType::TYPE_TEXTAREA:
				$view = new View\TextArea();

				if ( $field instanceof Field\Textarea && $field->get_rows() ) {
					$view->set_rows( $field->get_rows() );
				}

				return $view;

			case FieldType::TYPE_EMAIL:
				return new View\Email();

			case FieldType::TYPE_COLOR_PICKER:
				return new View\Color();

			case FieldType::TYPE_PASSWORD:
				return new View\Password();

			case FieldType::TYPE_URL:
			case FieldType::TYPE_OEMBED:
				return new View\Url();

			case FieldType::TYPE_LINK:
				return new Editing\View\Link();

			case FieldType::TYPE_RANGE:
				$view = new Editing\View\Range();

				if ( $field instanceof Field\Type\Range ) {
					$view->set_step( $field->get_step() );
					$view->set_min( $field->get_min() );
					$view->set_max( $field->get_max() );
				}

				if ( $field instanceof Field\DefaultValue ) {
					$view->set_default_value( $field->get_default_value() );
				}

				return $view;

			case FieldType::TYPE_NUMBER:
				$view = new ACP\Editing\View\Number();

				if ( $field instanceof Field\Number ) {
					$view->set_step( $field->get_step() );

					if ( is_numeric( $field->get_min() ) ) {
						$view->set_min( $field->get_min() );
					}

					if ( is_numeric( $field->get_max() ) ) {
						$view->set_max( $field->get_max() );
					}
				}

				return $view;

			case FieldType::TYPE_SELECT:
			case FieldType::TYPE_RADIO:
				$view = new View\AdvancedSelect( $field instanceof Field\Choices ? $field->get_choices() : [] );

				return $view->set_multiple( $field instanceof Field\Multiple && $field->is_multiple() );

			case FieldType::TYPE_USER:
			case FieldType::TYPE_POST:
			case FieldType::TYPE_PAGE_LINK:
			case FieldType::TYPE_TAXONOMY:
				return ( new View\AjaxSelect() )->set_multiple( $field instanceof Field\Multiple && $field->is_multiple() );

			case FieldType::TYPE_RELATIONSHIP:
				return ( new View\AjaxSelect() )->set_multiple( true );

			case FieldType::TYPE_GALLERY:
				return ( new View\Image() )->set_multiple( true );

			case FieldType::TYPE_FILE:
				return new View\Media();

			case FieldType::TYPE_IMAGE:
				return ( new View\Image() )->set_upload_media_only( $field instanceof Field\Library && $field->is_upload_media_only() );
			default:
				return new View\Text();
		}

	}

}