<?php
namespace ElementPack\Modules\VideoPlayer;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'video-player';
	}

	public function get_widgets() {
		$widgets = [
			'Video_Player',
		];

		return $widgets;
	}
}
