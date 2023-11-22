<?php
namespace ElementPack\Modules\SocialProof;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'social-proof';
	}

	public function get_widgets() {
		$widgets = [
			'Social_Proof',
		];

		return $widgets;
	}
}