<?php
namespace ElementPack\Modules\SourceCode;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'source-code';
	}

	public function get_widgets() {

		$widgets = ['Source_Code'];

		return $widgets;
	}

}
