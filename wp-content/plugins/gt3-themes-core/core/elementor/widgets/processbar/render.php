<?php

if(!defined('ABSPATH')) {
	exit;
}

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_ProcessBar $widget */

$settings = array(
	'items' => array(
		array(
			'proc_heading'   => esc_html__('Process #1', 'gt3_themes_core'),
			'proc_descr' => esc_html__('I am item content. Click edit button to change this text.', 'gt3_themes_core'),
			'proc_number' => '2008',
		),
		array(
			'proc_heading'   => esc_html__('Process #2', 'gt3_themes_core'),
			'proc_descr' => esc_html__('I am item content. Click edit button to change this text.', 'gt3_themes_core'),
			'proc_number' => '2011',
		),
		array(
			'proc_heading'   => esc_html__('Process #3', 'gt3_themes_core'),
			'proc_descr' => esc_html__('I am item content. Click edit button to change this text.', 'gt3_themes_core'),
			'proc_number' => '2014',
		),
		array(
			'proc_heading'   => esc_html__('Process #4', 'gt3_themes_core'),
			'proc_descr' => esc_html__('I am item content. Click edit button to change this text.', 'gt3_themes_core'),
			'proc_number' => '2018',
		),
	),
	'steps'   => '4',
	'type'   => 'horizontal',
);

$settings = wp_parse_args($widget->get_settings(), $settings);

if ($settings['steps'] == '5') {
	$column = '1-5';
}else{
	$column = 12/(int)$settings['steps'];
}

if ($settings['type'] == 'vertical') {
	$column = 12;
}

$widget->add_render_attribute('wrapper', 'class', array(
	'gt3_process_bar_wrapper',
	'gt3_process_bar--'.$column.'_step',
));

if (isset($settings['items']) && is_array($settings['items'])) {
	echo '<div class="gt3_process_bar_container row gt3_process_bar_container--type-'.esc_attr($settings['type']).'">';

	foreach ($settings['items'] as $item) {
		echo '<div class="gt3_process_item'.(!empty($column) ? ' span'.$column : '').' elementor-repeater-item-'.esc_attr($item['_id']).'">';
			if (isset($item['proc_number'])) {
				echo '<div class="gt3_process_item__number">';
					echo $item['proc_number'];
				echo '</div>';
			}
			echo '<div class="gt3_process_item__circle_wrapp">
			<div class="gt3_process_item__circle_line_before"></div>
			<div class="gt3_process_item__circle'.(!empty($item['circle_size']) ? ' gt3_process_item__circle--size_'.$item['circle_size'] : '').'"></div>
			<div class="gt3_process_item__circle_line_after"></div>
			</div>';
			if ($settings['type'] == 'vertical') {
				echo '<div class="gt3_process_item__content">';
			}
			echo '<div class="gt3_process_item__content_wrapper">';
			if (isset($item['proc_heading'])) {
				echo '<div class="gt3_process_item__heading">';
					echo '<h3>'.esc_html($item['proc_heading']).'</h3>';
				echo '</div>';
			}
			if (isset($item['proc_descr'])) {
				echo '<div class="gt3_process_item__description">';
					echo $item['proc_descr'];
				echo '</div>';
			}
			echo '</div>';
			if ($settings['type'] == 'vertical') {
				echo '</div>';
			}
		echo '</div>';
	}	
	echo '</div>';
}