<?php

namespace ACA\MetaBox\Setting;

use AC;
use ACP;

class SubFieldSettingFactory {

	public function create( $field_settings, AC\Column $column ): array {
		$settings = [];

		switch ( $field_settings['type'] ) {
			case 'single_image':
			case 'image':
			case 'image_advanced':
			case 'image_upload':
				$settings[] = new AC\Settings\Column\Image( $column );
				break;
			case 'post':
				$settings[] = new AC\Settings\Column\Post( $column );
				break;
			case 'taxonomy':
				$settings[] = new AC\Settings\Column\Term( $column );
				break;
			case 'user':
				$settings[] = new ACP\Settings\Column\User( $column );
				break;
		}

		return $settings;
	}

}