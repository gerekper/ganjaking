<?php
namespace ElementPack\Modules\CookieConsent;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'cookie-consent';
	}

	public function get_widgets() {

		$widgets = [
			'Cookie_Consent',
		];

		return $widgets;
	}
}
