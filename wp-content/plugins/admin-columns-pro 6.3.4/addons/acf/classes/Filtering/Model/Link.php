<?php

namespace ACA\ACF\Filtering\Model;

use AC;
use ACP;

class Link extends ACP\Filtering\Model\Meta {

	public function __construct( AC\Column\Meta $column ) {
		parent::__construct( $column, true );
	}

	public function get_filtering_data() {
		$options = [];

		foreach ( $this->get_meta_values() as $value ) {
			$value = unserialize( $value, [ 'allowed_classes' => false ] );
			$options[ $value['url'] ] = $value['url'];
		}

		return [
			'empty_option' => true,
			'options'      => $options,
		];
	}

}