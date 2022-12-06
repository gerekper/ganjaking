<?php

namespace ACA\Pods\Editing;

use AC\Helper\Select\Option;
use AC\Type\ToggleOptions;
use ACA\Pods\Field;
use ACP\Editing\View;
use ACP\Editing\View\Toggle;

final class ViewFactory {

	public function create_by_field( Field $field ): View {
		$view = $this->create_view_type( $field );

		$this->set_clear_button( $view );

		if ( $view instanceof View\Placeholder ) {
			$view->set_placeholder( $field->get( 'label' ) );
		}

		if ( $view instanceof View\MaxLength ) {
			foreach ( [ 'email_max_length', 'website_max_length', 'text_max_length', 'paragraph_max_length' ] as $property ) {
				$this->check_max_length_for_property( $view, $field, $property );
			}
		}

		return $view;
	}

	private function set_clear_button( View $view ) {
		switch ( true ) {
			case $view instanceof Toggle:
				return $view;
			default:
				return $view->set_clear_button( true );
		}
	}

	private function create_view_type( Field $field ) {
		switch ( true ) {
			case $field instanceof Field\Boolean:
				return new Toggle( new ToggleOptions( new Option( '0' ), new Option( '1' ) ) );
			case $field instanceof Field\Code:
			case $field instanceof Field\Paragraph:
			case $field instanceof Field\Wysiwyg:
				return new View\TextArea();
			case $field instanceof Field\Color:
				return new View\Color();
			case $field instanceof Field\Currency:
			case $field instanceof Field\Number:
				return ( new View\Number() )->set_step( 'any' );
			case $field instanceof Field\Date:
				return new View\Date();
			case $field instanceof Field\Datetime:
				return new View\DateTime();
			case $field instanceof Field\Email:
				return new View\Email();
			case $field instanceof Field\File:
			case $field instanceof Field\Pick\Media:
				$view = $this->get_media_type_view_by_field( $field );
				$view->set_multiple( 'multi' === $field->get_option( 'file_format_type' ) );

				return $view;
			case $field instanceof Field\Website:
				return new View\Url();

			case $field instanceof Field\Pick\Capability:
			case $field instanceof Field\Pick\CustomSimple:
			case $field instanceof Field\Pick\Country:
			case $field instanceof Field\Pick\DaysOfWeek:
			case $field instanceof Field\Pick\ImageSize:
			case $field instanceof Field\Pick\MonthsOfYear:
			case $field instanceof Field\Pick\NavMenu:
			case $field instanceof Field\Pick\PostStatus:
			case $field instanceof Field\Pick\PostFormat:
			case $field instanceof Field\Pick\Role:
			case $field instanceof Field\Pick\UsState:
				$view = new View\AdvancedSelect( $field->get_options() );

				return $view->set_multiple( 'multi' === $field->get_option( 'pick_format_type' ) );
			case $field instanceof Field\Password:
			case $field instanceof Field\Phone:
			case $field instanceof Field\Text:
			case $field instanceof Field\Time:
			default:
				return new View\Text();
		}
	}

	public function get_media_type_view_by_field( Field $field ) {
		switch ( $field->get_option( 'file_type' ) ) {
			case 'images':
				return new View\Image();
			case 'video':
				return new View\Video();
			case 'audio':
				return new View\Audio();
			default:
				return new View\Media();
		}
	}

	private function check_max_length_for_property( View\MaxLength $view, Field $field, $property ): void {
		if ( $field->get_option( $property ) ) {
			$view->set_max_length( $field->get_option( $property ) );
		}
	}

}