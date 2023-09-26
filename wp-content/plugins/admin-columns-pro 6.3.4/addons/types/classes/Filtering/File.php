<?php

namespace ACA\Types\Filtering;

use ACA\Types\Column;
use ACP;

/**
 * @property Column $column
 */
class File extends ACP\Filtering\Model\Meta {

	public function get_filtering_data() {
		$data = parent::get_filtering_data();
		$options = [];

		foreach ( $data['options'] as $id => $option ) {
			$options[ $id ] = basename( $option );
		}
		$data['options'] = $options;

		return $data;
	}

}