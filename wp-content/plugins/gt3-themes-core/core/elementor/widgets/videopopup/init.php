<?php

namespace ElementorModal\Widgets;

use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ElementorModal\Widgets\GT3_Core_Elementor_Widget_VideoPopup' ) ) {
	class GT3_Core_Elementor_Widget_VideoPopup extends \ElementorModal\Widgets\GT3_Core_Widget_Base {

		public function get_name() {
			return 'gt3-core-videopopup';
		}

		public function get_title() {
			return esc_html__( 'Video Popup', 'gt3_themes_core' );
		}

		public function get_icon() {
			return 'gt3-core-elementor-icon eicon-youtube';
		}

		protected function construct() {
			$this->add_script_depends( 'swipebox_js' );
			$this->add_style_depends( 'swipebox_style' );
		}
	}
}

