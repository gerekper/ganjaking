<?php
namespace ElementPack\Modules\StepFlow;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'step-flow';
	}

	public function get_widgets() {

		$widgets = [
			'Step_Flow',
		];

		return $widgets;
	}
}
