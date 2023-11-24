<?php

use Elementor\Controls_Manager;
use ElementorPro\Modules\DynamicTags\Tags\Base\Tag;
use ElementorPro\Modules\DynamicTags\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class UnlimitedElementsDynamicTag_TimeStamp extends Tag {
	
	public function get_name() {
		return 'uc-current-timestamp';
	}

	public function get_title() {
		return __( 'Current Time Stamp', 'unlimited-elements-for-elementor' );
	}

	public function get_group() {
		return Module::SITE_GROUP;
	}

	public function get_categories() {
		return [ Module::TEXT_CATEGORY ];
	}


	public function render() {
		
		$stamp = time();
		
		echo $stamp;
	}
	
}
