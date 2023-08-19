<?php

namespace ACA\GravityForms\Field\Type;

use ACA\GravityForms;
use GF_Field;

class Checkbox extends GravityForms\Field\Field {

	/**
	 * @var string
	 */
	private $value;

	/**
	 * @var string
	 */
	private $label;

	public function __construct( $form_id, $field_id, GF_Field $field, $value, $label ) {
		parent::__construct( $form_id, $field_id, $field );

		$this->value = (string) $value;
		$this->label = (string) $label;
	}

	public function get_value() {
		return $this->value;
	}

	public function get_label() {
		return $this->label;
	}

}