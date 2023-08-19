<?php

namespace ACA\GravityForms;

use ACA\GravityForms\Field\Type\Textarea;
use ACA\GravityForms\Field\Type\Unsupported;
use GF_Field;
use GFFormsModel;

final class FieldFactory {

	/**
	 * @param string $field_id
	 * @param int    $form_id
	 *
	 * @return Field\Field|null
	 */
	public function create( $field_id, $form_id ) {
		$gf_field = GFFormsModel::get_field( $form_id, $field_id );

		if ( ! $gf_field ) {
			return null;
		}

		$field = $this->get_field_for_gf_field( $gf_field, $form_id, $field_id );

		return $field instanceof Field\Container && $this->is_sub_field( $field_id )
			? $field->get_sub_field( $field_id )
			: $field;
	}

	/**
	 * @param GF_Field $gf_field
	 * @param int      $form_id
	 * @param string   $field_id
	 *
	 * @return Field\Field
	 */
	private function get_field_for_gf_field( GF_Field $gf_field, $form_id, $field_id ) {

		switch ( $gf_field->offsetGet( 'type' ) ) {
			case FieldTypes::PAGE:
				return new Unsupported( $form_id, $field_id, $gf_field );

			case FieldTypes::ADDRESS:
				return new Field\Type\Address( $form_id, $field_id, $gf_field );

			case FieldTypes::NAME:
				return new Field\Type\Name( $form_id, $field_id, $gf_field );

			case FieldTypes::NUMBER:
			case FieldTypes::TOTAL:
				return new Field\Type\Number( $form_id, $field_id, $gf_field );

			case FieldTypes::CHECKBOX:
				return new Field\Type\CheckboxGroup( $form_id, $field_id, $gf_field );

			case FieldTypes::MULTI_SELECT:
				return new Field\Type\Select( $form_id, $field_id, $gf_field, Utils\FormField::formatChoices( $gf_field->offsetGet( 'choices' ) ), true );
			case FieldTypes::SELECT:
				return new Field\Type\Select( $form_id, $field_id, $gf_field, Utils\FormField::formatChoices( $gf_field->offsetGet( 'choices' ) ), false );

			case FieldTypes::RADIO:
				return new Field\Type\Radio( $form_id, $field_id, $gf_field );

			case FieldTypes::DATE:
				return new Field\Type\Date( $form_id, $field_id, $gf_field );

			case FieldTypes::CONSENT:
				return new Field\Type\Consent( $form_id, $field_id, $gf_field );

			case FieldTypes::LISTS:
				return new Field\Type\ItemList( $form_id, $field_id, $gf_field );

			case FieldTypes::PRODUCT:
				switch ( $gf_field->get_input_type() ) {
					case 'singleproduct':
					case 'calculation':
					case 'hiddenproduct':
						return new Field\Type\Product( $form_id, $field_id, $gf_field );

					case 'select':
					case 'radio':
						return new Field\Type\ProductSelect( $form_id, $field_id, $gf_field );

					case 'price':
						return new Field\Type\Input( $form_id, $field_id, $gf_field );

					default:
						return null;
				}

			case FieldTypes::QUANTITY:
				return $gf_field->get_input_type() === 'select'
					? new Field\Type\Select( $form_id, $field_id, $gf_field, Utils\FormField::formatChoices( $gf_field->offsetGet( 'choices' ) ), false )
					: new Field\Type\Input( $form_id, $field_id, $gf_field );

			case FieldTypes::EMAIL:
				return new Field\Type\Email( $form_id, $field_id, $gf_field );

			case FieldTypes::POST_TITLE:
			case FieldTypes::HIDDEN:
			case FieldTypes::TEXT:

			case FieldTypes::WEBSITE:
			case FieldTypes::PHONE:
			case FieldTypes::TIME:
				return new Field\Type\Input( $form_id, $field_id, $gf_field );

			case FieldTypes::POST_CONTENT:
			case FieldTypes::POST_EXCERPT:
			case FieldTypes::TEXTAREA:
				return new Textarea( $form_id, $field_id, $gf_field );

		}

		return new Field\Field( $form_id, $field_id, $gf_field );
	}

	private function is_sub_field( $field_id ) {
		return strpos( $field_id, '.' ) > 0;
	}

}