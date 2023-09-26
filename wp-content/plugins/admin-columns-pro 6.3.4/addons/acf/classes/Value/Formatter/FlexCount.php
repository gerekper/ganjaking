<?php

namespace ACA\ACF\Value\Formatter;

use ACA\ACF\Column;
use ACA\ACF\Field;
use ACA\ACF\Value\Formatter;

class FlexCount extends Formatter {

	public function __construct( Column $column, Field\Type\FlexibleContent $field ) {
		parent::__construct( $column, $field );
	}

	public function format( $values, $id = null ) {
		if ( ! $values ) {
			return $this->column->get_empty_char();
		}

		if ( empty( $this->field->get_layouts() ) ) {
			return $this->column->get_empty_char();
		}

		$layouts = [];
		$labels = $this->get_layout_labels();

		foreach ( $values as $field ) {
			if ( ! isset( $layouts[ $field['acf_fc_layout'] ] ) ) {
				$layouts[ $field['acf_fc_layout'] ] = [
					'count' => 1,
					'label' => isset( $labels[ $field['acf_fc_layout'] ] ) ? $labels[ $field['acf_fc_layout'] ] : $field['acf_fc_layout'],
				];
			} else {
				$layouts[ $field['acf_fc_layout'] ]['count']++;
			}
		}

		$result = array_map( function ( $l ) {
			return ( $l['count'] > 1 )
				? sprintf( '%s <span class="ac-rounded">%s</span>', $l['label'], $l['count'] )
				: $l['label'];
		}, $layouts );

		return implode( '<br>', $result );
	}

	/**
	 * @return array
	 */
	private function get_layout_labels() {
		$labels = [];

		foreach ( $this->field->get_layouts() as $layout ) {
			$labels[ $layout['name'] ] = $layout['label'];
		}

		return $labels;
	}

}