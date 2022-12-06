<?php

namespace ACA\GravityForms\Field;

use ACA\GravityForms;
use GF_Field;

class Field implements GravityForms\Field {

	/**
	 * @var int
	 */
	private $form_id;

	/**
	 * @var string
	 */
	private $field_id;

	/**
	 * @var GF_Field
	 */
	protected $gf_field;

	public function __construct( $form_id, $field_id, GF_Field $gf_field ) {
		$this->form_id = (int) $form_id;
		$this->field_id = (string) $field_id;
		$this->gf_field = $gf_field;
	}

	public function get_form_id() {
		return $this->form_id;
	}

	public function get_id() {
		return $this->field_id;
	}

	public function is_required() {
		return (bool) $this->gf_field->isRequired;
	}

}