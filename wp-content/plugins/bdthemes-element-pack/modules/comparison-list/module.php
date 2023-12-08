<?php
namespace ElementPack\Modules\ComparisonList;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'comparison-list';
	}

	public function get_widgets() {
		$widgets = [
			'Comparison_List',
		];

		return $widgets;
	}
}
