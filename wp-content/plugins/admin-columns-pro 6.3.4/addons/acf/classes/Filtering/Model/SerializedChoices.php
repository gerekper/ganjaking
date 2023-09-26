<?php

namespace ACA\ACF\Filtering\Model;

use AC;
use ACA\ACF\Field;
use ACP;

class SerializedChoices extends ACP\Filtering\Model\Meta {

	/**
	 * @var Field
	 */
	private $field;

	public function __construct( AC\Column\Meta $column, Field $field ) {
		parent::__construct( $column, true );

		$this->field = $field;
	}

	public function get_filtering_data() {
		$values = $this->get_meta_values_unserialized();
		$options = [];
		$choices = $this->field instanceof Field\Choices
			? $this->field->get_choices()
			: [];

		foreach ( $values as $value ) {
			if ( $choices && isset( $choices[ $value ] ) ) {
				$options[ $value ] = $choices[ $value ];
			}
		}

		return [
			'empty_option' => true,
			'options'      => $options,
		];
	}

}