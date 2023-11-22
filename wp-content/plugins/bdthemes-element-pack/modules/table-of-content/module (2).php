<?php
namespace ElementPack\Modules\TableOfContent;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'table-of-content';
	}

	public function get_widgets() {

		$widgets = [
			'Table_Of_Content',
		];

		return $widgets;
	}
}
