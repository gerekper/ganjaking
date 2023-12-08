<?php
namespace ElementPack\Modules\TestimonialCarousel;

use ElementPack\Base\Element_Pack_Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'testimonial-carousel';
	}

	public function get_widgets() {
		$widgets = ['Testimonial_Carousel'];

		return $widgets;
	}
}
