<?php

namespace GT3\PhotoVideoGallery\Elementor\Widgets;
defined('ABSPATH') OR exit;

use Elementor\Controls_Manager;
use GT3\PhotoVideoGallery\Elementor\Controls\Gallery as Gallery_control;

abstract class Basic extends Widget_Base {

	/**
	 * array $params Options
	 *        boolean ['withCategories']        Enable Categories in query
	 *        boolean ['withCustomVideoLink']   Enable Custom Video Link
	 *        boolean ['withCustomLink']        Enable Custom Link
	 *
	 * @param array $params (See above)
	 *
	 * @return void
	 **/
	protected function imagesControls(array $params = array()){
		$this->add_control(
			'ids',
			array(
				'type' => Gallery_control::TYPE,
			)
		);
	}
}

