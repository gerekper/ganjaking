<?php

namespace ACP\Editing\Model\CustomField;

use ACP\Editing\Model;
use ACP\Editing\Settings;

class Text extends Model\CustomField {

	public function get_view_settings() {
		/* @var Settings\Excerpt $setting */
		$setting = $this->column->get_setting( 'edit' );

		return [
			'type' => $setting->get_editable_type(),
		];
	}

	public function register_settings() {
		$this->column->add_setting( new Settings\Excerpt( $this->column ) );
	}

}