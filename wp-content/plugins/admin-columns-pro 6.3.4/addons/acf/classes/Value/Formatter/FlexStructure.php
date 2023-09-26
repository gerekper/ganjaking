<?php

namespace ACA\ACF\Value\Formatter;

use ACA\ACF\Value\Formatter;

class FlexStructure extends Formatter {

	public function format( $values, $id = null ) {
		$results = [];
		$labels = $this->get_layout_labels();

		$i = 0;
		while ( have_rows( $this->field->get_meta_key(), $id ) ) {
			the_row();
			$title = $labels[ get_row_layout() ];
			$acf_layout = $this->get_layout_by_name( get_row_layout() );

			$title = apply_filters( 'acf/fields/flexible_content/layout_title', $title, $this->field->get_settings(), $acf_layout, $i );
			$title = apply_filters( "acf/fields/flexible_content/layout_title/key={$this->field->get_hash()}", $title, $this->field->get_settings(), $acf_layout, $i );
			$title = apply_filters( "acf/fields/flexible_content/layout_title/name={$this->field->get_meta_key()}", $title, $this->field->get_settings(), $acf_layout, $i );

			$results[] = '[ ' . $title . ' ]';
			$i++;
		}

		return empty( $results )
			? $this->column->get_empty_char()
			: implode( '<br>', $results );
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

	/**
	 * @param $name
	 *
	 * @return string|false
	 */
	private function get_layout_by_name( $name ) {
		foreach ( $this->field->get_layouts() as $layout ) {
			if ( $name === $layout['name'] ) {
				return $layout;
			}
		}

		return false;
	}

}