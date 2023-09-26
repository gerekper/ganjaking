<?php

namespace ACA\Types\Filtering;

use ACA\Types\Column;
use ACA\Types\Filtering;

/**
 * @property Column $column
 */
class Skype extends Filtering {

	public function get_filtering_data() {
		$options = [];

		foreach ( $this->get_meta_values_filtered() as $value ) {
			$value = maybe_unserialize( $value );

			if ( isset( $value['skypename'] ) ) {
				$options[ $value['skypename'] ] = $value['skypename'];
			}
		}

		return [
			'options' => $options,
		];
	}

}