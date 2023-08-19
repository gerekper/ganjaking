<?php

namespace ACA\GravityForms\Settings;

use AC;
use ACA\GravityForms\Column;

/**
 * @property Column\Entry $column
 */
class ChoiceDisplay extends AC\Settings\Column
	implements AC\Settings\FormatValue {

	/**
	 * @var string
	 */
	private $choice_display;

	/**
	 * @var array
	 */
	private $choices;

	public function __construct( Column\Entry $column, $choices ) {
		parent::__construct( $column );

		$this->choices = $choices;
	}

	/**
	 * @return string
	 */
	public function get_choice_display() {
		return $this->choice_display;
	}

	/**
	 * @param string $choice_display
	 */
	public function set_choice_display( $choice_display ) {
		$this->choice_display = (string) $choice_display;
	}

	protected function define_options() {
		return [
			'choice_display' => 'label',
		];
	}

	public function create_view() {
		$select = $this->create_element( 'select' )
		               ->set_options( [ '' => 'Value', 'label' => 'Labels' ] );

		return new AC\View( [
			'label'   => __( 'Display', 'codepress-admin-columns' ),
			'setting' => $select,
		] );
	}

	public function format( $value, $original_value ) {
		$value = (array) $value;

		if ( 'label' === $this->get_choice_display() ) {
			$value = $this->format_labels( $value );
		}

		return implode( ', ', $value );
	}

	/**
	 * @param array $value
	 *
	 * @return array
	 */
	public function format_labels( $value ) {
		$choices = $this->choices;
		$labels = [];

		foreach ( $value as $key ) {
			$label = $key;
			if ( isset( $choices[ $key ] ) ) {
				$label = $choices[ $key ];
			}

			$labels[] = $label;
		}

		return $labels;
	}

}