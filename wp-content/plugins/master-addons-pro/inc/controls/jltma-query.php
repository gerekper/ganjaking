<?php
namespace MasterAddons\Inc\Controls;

use \Elementor\Control_Select2;

if ( ! defined( 'ABSPATH' ) ) { exit; };

class JLTMA_Control_Query extends Control_Select2 {

	public function get_type() {
		return 'jltma_query';
	}

	public function enqueue() {
		wp_enqueue_script( 'master-addons-editor', MELA_ADMIN_ASSETS . 'js/editor.js', array( 'jquery' ), MELA_VERSION, true );
	}

	protected function get_default_settings() {
		return array_merge(
			parent::get_default_settings(), [
				'query' => '',
			]
		);
	}

	public function get_default_value() {
		return parent::get_default_value();
	}

}
