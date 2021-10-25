<?php

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	} // Exit if accessed directly

	add_filter('image_size_names_choose1', function ($names){
		$my_name = array(
			'thumbnail' => __('Thumbnail','gt3pg'),
			'medium' => __('Medium','gt3pg'),
			'medium_large' => __('Medium-large','gt3pg'),
			'large' => __('Large','gt3pg'),
		);

		foreach ($my_name as $key=>$value) {
			if (!isset($names[$key])) {
				$names[$key] = $value;
			}
		}

		return $names;
	});