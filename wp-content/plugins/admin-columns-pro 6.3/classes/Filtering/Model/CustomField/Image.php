<?php

namespace ACP\Filtering\Model\CustomField;

use ACP\Filtering\Model;
use ACP\Filtering\Settings;

class Image extends Model\CustomField {

	public function get_filtering_data() {
		$data = [
			'empty_option' => true,
		];

		$values = $this->get_meta_values();

		foreach ( $values as $value ) {
			$data['options'][ $value ] = basename( wp_get_attachment_url( $value ) );
		}

		return $data;
	}

	public function register_settings() {
		$this->column->add_setting( new Settings( $this->column ) );
	}

}