<?php

namespace ACA\GravityForms\Field\Type;

use ACA\GravityForms;
use ACA\GravityForms\Field;
use GF_Field;

class Select extends GravityForms\Field\Field implements Field\Options, Field\Multiple {

	/**
	 * @var array
	 */
	private $choices;

	/**
	 * @var bool
	 */
	private $multiple;

	public function __construct( $form_id, $field_id, GF_Field $gf_field, array $choices, $multiple ) {
		parent::__construct( $form_id, $field_id, $gf_field );

		$this->choices = $choices;
		$this->multiple = (bool) $multiple;
	}

	public function get_options() {
		return $this->choices;
	}

	public function is_multiple() {
		return $this->multiple;
	}

}