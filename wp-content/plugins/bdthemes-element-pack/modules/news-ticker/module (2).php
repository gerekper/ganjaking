<?php
namespace ElementPack\Modules\NewsTicker;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'news-ticker';
	}

	public function get_widgets() {

		$widgets = [
			'News_Ticker',
		];
		
		return $widgets;
	}
}
