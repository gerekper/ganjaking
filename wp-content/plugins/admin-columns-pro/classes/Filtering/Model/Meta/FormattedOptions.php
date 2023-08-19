<?php

namespace ACP\Filtering\Model\Meta;

use AC;
use ACP\Filtering\Model\Meta;

class FormattedOptions extends Meta {

	private $formatted_options;

	private $empty_option;

	public function __construct( AC\Column\Meta $column, array $options, bool $empty_option = true ) {
		parent::__construct( $column );

		$this->formatted_options = $options;
		$this->empty_option = $empty_option;
	}

	public function get_filtering_data() {
		$values = $this->get_meta_values();
		$options = [];

		foreach ( $values as $value ) {
			if ( array_key_exists( $value, $this->formatted_options ) ) {
				$options[ $value ] = $this->formatted_options[ $value ];
			}
		}

		return [
			'empty_option' => $this->empty_option,
			'options'      => $options,
		];
	}

}