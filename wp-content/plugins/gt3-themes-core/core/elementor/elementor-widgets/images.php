<?php

use Elementor\Controls_Manager;
use GT3\ThemesCore\Lazy_Images;
use Elementor\Plugin as Elementor_Plugin;

add_action(
	'elementor/element/image/section_image/before_section_end',
	function($element, $args){
		/* @var \Elementor\Widget_Base $element */
		$element->start_injection(
			array(
				'type' => 'control',
				'at'   => 'before',
				'of'   => 'align'
			)
		);

		$element->add_control(
			'lazyLoad',
			array(
				'label' => __('Use lazyload?', 'elementor'),
				'type'  => Controls_Manager::SWITCHER,
			)
		);
	}, 20, 2
);

add_action("elementor/widget/before_render_content/image", function($widget) {
	/** @var \Elementor\Widget_Base $widget */
	if (Elementor_Plugin::instance()->editor->is_edit_mode()) return;
	$isLazy = $widget->get_settings('lazyLoad');
	if ($isLazy) {
		Lazy_Images::instance()->setup_filters();
	}
});




