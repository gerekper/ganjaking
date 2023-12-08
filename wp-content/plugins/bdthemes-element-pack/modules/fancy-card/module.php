<?php
namespace ElementPack\Modules\FancyCard;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'fancy-card';
	}

	public function get_widgets() {

		$widgets = [
			'Fancy_Card',
		];

		return $widgets;
	}
}
