<?php
namespace ElementPack\Modules\ProgressPie;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'progress-pie';
	}

	public function get_widgets() {

		$widgets = [
			'Progress_Pie',
		];

		return $widgets;
	}
}
