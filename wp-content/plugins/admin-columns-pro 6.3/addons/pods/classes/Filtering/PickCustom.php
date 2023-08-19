<?php

namespace ACA\Pods\Filtering;

use ACA\Pods\Column;
use ACA\Pods\Field;
use ACA\Pods\Filtering;

/**
 * Class PickCustom
 * @property Column $column
 */
class PickCustom extends Filtering {

	public function get_filtering_data() {
		/** @var Field\Pick\CustomSimple $field */
		$field = $this->column->get_field();
		$meta_values = $this->get_meta_values();
		$options = [];

		$choices = $field->get_options();

		foreach ( $meta_values as $option ) {
			if ( isset( $choices[ $option ] ) ) {
				$options[ $option ] = $choices[ $option ];
			}

		}

		return [ 'options' => $options ];
	}

}