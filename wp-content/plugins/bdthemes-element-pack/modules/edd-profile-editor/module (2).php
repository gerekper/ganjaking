<?php

namespace ElementPack\Modules\EddProfileEditor;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'easy-digital-profile-editor';
	}

	public function get_widgets() {

		$widgets = [
			'EDD_Profile_Editor',
		];

		return $widgets;
	}
}
