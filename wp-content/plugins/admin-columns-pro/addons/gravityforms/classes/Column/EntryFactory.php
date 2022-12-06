<?php

namespace ACA\GravityForms\Column;

use ACA\GravityForms;
use ACA\GravityForms\Column;
use ACA\GravityForms\Field;
use ACA\GravityForms\FieldFactory;
use LogicException;

final class EntryFactory {

	/**
	 * @var FieldFactory
	 */
	private $field_factory;

	/**
	 * @param FieldFactory $field_factory
	 */
	public function __construct( FieldFactory $field_factory ) {
		$this->field_factory = $field_factory;
	}

	/**
	 * @param string $field_id
	 * @param int    $form_id
	 *
	 * @return GravityForms\Field|null
	 */
	private function get_field( $field_id, $form_id ) {
		return $this->field_factory->create( $field_id, $form_id );
	}

	/**
	 * @param string $field_id
	 * @param int    $form_id
	 *
	 * @return bool
	 */
	public function has_field( $field_id, $form_id ) {
		return $this->get_field( $field_id, $form_id ) !== null;
	}

	/**
	 * @param string $field_id
	 * @param int    $form_id
	 *
	 * @return GravityForms\Column\Entry
	 */
	public function create( $field_id, $form_id ) {
		if ( ! $this->has_field( $field_id, $form_id ) ) {
			throw new LogicException( 'This column has no field defined.' );
		}

		$field = $this->get_field( $field_id, $form_id );

		switch ( true ) {
			case $field instanceof Field\Type\Address:
				return new Column\Entry\Address();

			case $field instanceof Field\Type\Name:
				return new Column\Entry\Name();

			case $field instanceof Field\Type\Product:
				return new Column\Entry\Product();

			case $field instanceof Field\Type\ProductSelect:
				return new Column\Entry\ProductSelect();

			case $field instanceof Field\Type\Select:
				return $field->is_multiple()
					? new Column\Entry\MultipleChoices()
					: new Column\Entry\Choices();

			case $field instanceof Field\Type\Radio:
				return new Column\Entry\Choices();

			case $field instanceof Field\Type\CheckboxGroup:
				return new Column\Entry\MultipleChoices();
		}

		return new Column\Entry;
	}

}