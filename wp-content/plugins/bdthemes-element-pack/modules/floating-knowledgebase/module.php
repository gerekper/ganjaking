<?php

namespace ElementPack\Modules\FloatingKnowledgebase;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'floating-knowledgebase';
	}

	public function get_widgets() {

		$widgets = ['Floating_Knowledgebase'];

		return $widgets;
	}
}
