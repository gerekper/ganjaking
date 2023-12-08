<?php
namespace ElementPack\Modules\BbpressTopicForm;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'bbpress-topic-form';
	}

	public function get_widgets() {

		$widgets = [
			'Bbpress_Topic_Form',
		];

		return $widgets;
	}
}
