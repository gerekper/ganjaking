<?php

namespace ACA\ACF\Filtering\Model;

use ACP;

class Unformatted extends ACP\Filtering\Model\Meta {

	/**
	 * @return array
	 */
	public function get_filtering_data() {
		$options = [];

		foreach ( $this->get_meta_values() as $value ) {
			$options[ $value ] = $value;
		}

		return [
			'empty_option' => true,
			'options'      => $options,
		];
	}

}