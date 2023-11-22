<?php
namespace ElementPack\Modules\AdvancedProgressBar;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'advanced-progress-bar';
	}

	public function get_widgets() {
		$widgets = [
			'Advanced_Progress_Bar',
		];

		return $widgets;
	}
}
