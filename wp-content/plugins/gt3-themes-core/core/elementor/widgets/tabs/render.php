<?php

if(!defined('ABSPATH')) {
	exit;
}

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Tabs $widget */

$settings = array(
	'items' => array(
		array(
			'title'   => esc_html__('Tab #1', 'gt3_themes_core'),
			'content' => esc_html__('I am item content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'gt3_themes_core'),
			'icon' => '',
		),
		array(
			'title'   => esc_html__('Tab #2', 'gt3_themes_core'),
			'content' => esc_html__('Curabitur sodales ligula in libero. Sed dignissim lacinia nunc. Curabitur tortor. Pellentesque nibh. Aenean quam. In scelerisque sem at dolor. Maecenas mattis. Sed convallis tristique.', 'gt3_themes_core'),
			'icon' => '',
		),
		array(
			'title'   => esc_html__('Tab #3', 'gt3_themes_core'),
			'content' => esc_html__('Mauris ipsum. Nulla metus metus, ullamcorper vel, tincidunt sed, euismod in, nibh. Quisque volutpat condimentum velit. Class aptent taciti sociosqu ad litora torquent conubia nostra.', 'gt3_themes_core'),
			'icon' => '',
		),
	),
	'tabs_type'   => 'horizontal',
	'tabs_aligment_h'   => 'center',
	'tabs_aligment_v'   => 'center',
	'icon_position'   => 'left',
	'active_tab'   => '1',
);

$settings = wp_parse_args($widget->get_settings(), $settings);

if ($settings['tabs_type'] == 'horizontal') {
	$settings['tabs_aligment_v'] = '';
}

if ($settings['tabs_type'] == 'vertical') {
	$settings['tabs_aligment_h'] = '';
}

$aligment_tab = 'aligment_' . $settings['tabs_aligment_h'] . $settings['tabs_aligment_v'];

$widget->add_render_attribute('wrapper', 'class', array(
	'gt3_tabs_wrapper',
	$aligment_tab,
	$settings['tabs_type'].'_type',
	$settings['icon_position'].'_icon_position',
));

if(isset($settings['items']) && is_array($settings['items'])) {
	$uniqid = mt_rand(0, 9999);

	echo '<div data-active-tab="' . esc_attr($settings['active_tab']) . '" '. $widget->get_render_attribute_string('wrapper') .'>';
	echo '<ul class="gt3_tabs_nav">';
	$base_count = 1;
	$left_icon = $right_icon = '';
	foreach($settings['items'] as $item) {
		$left_icon = $right_icon = '';
		if(!empty($item['icon'])) {
			$icon = '<div class="icon '.esc_attr($item['icon']).'"></div>';
			if ($settings['icon_position'] == 'left') {
				$left_icon = $icon;
			}
			if ($settings['icon_position'] == 'right') {
				$right_icon = $icon;
			}
		}
		echo '<li><a href="#tab-' . esc_attr($uniqid) . '-' . esc_attr($base_count) . '">'. $left_icon . esc_html($item['title']). $right_icon . '</a></li>';
		$base_count++;
	}
	echo '</ul>';
	$base_count = 1;
	foreach($settings['items'] as $item) {
		echo '<div id="tab-' . esc_attr($uniqid) . '-' . esc_attr($base_count) . '">'.$item['content'].'</div>';
		$base_count++;
	}
	echo '</div>';
}