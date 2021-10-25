<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_ImageProcessBar $widget */

$settings = array(
	'items' => array(
		array(
			'proc_heading'   => esc_html__('Process #1', 'gt3_themes_core'),
			'proc_descr' => esc_html__('I am item content. Click edit button to change this text.', 'gt3_themes_core'),
			'proc_number' => '1',
		),
		array(
			'proc_heading'   => esc_html__('Process #2', 'gt3_themes_core'),
			'proc_descr' => esc_html__('I am item content. Click edit button to change this text.', 'gt3_themes_core'),
			'proc_number' => '2',
		),
		array(
			'proc_heading'   => esc_html__('Process #3', 'gt3_themes_core'),
			'proc_descr' => esc_html__('I am item content. Click edit button to change this text.', 'gt3_themes_core'),
			'proc_number' => '3',
		),
		array(
			'proc_heading'   => esc_html__('Process #4', 'gt3_themes_core'),
			'proc_descr' => esc_html__('I am item content. Click edit button to change this text.', 'gt3_themes_core'),
			'proc_number' => '4',
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

$image_size_width = 1200/(int)$settings['steps'] - 90;

if (isset($settings['items']) && is_array($settings['items'])) {
	echo '<div class="gt3_process_bar_container gt3_image_process_bar_wrapper row gt3_process_bar_container--type-'.esc_attr($settings['type']).'">';

	foreach ($settings['items'] as $item) {
		echo '<div class="gt3_process_item'.(!empty($column) ? ' span'.$column : '').'">';
			
			echo '<div class="gt3_process_item__circle_container"><div class="gt3_process_item__circle_line_before"></div>';
				echo '<div class="gt3_process_item__circle_wrapp"><div class="gt3_process_item__circle">';

				if(isset($item['image']['id']) && (bool) $item['image']['id']) {
					$image = wp_get_attachment_image_src($item['image']['id'],'full');
					if($image) {
						echo '<div class="image"><img src="'.esc_url(aq_resize($image['0'], $image_size_width, $image_size_width, true, true, true)).'" alt="'.esc_attr($item['proc_heading']).'" /></div>';
					}
				} else {
					echo '<div class="image no-image"><img src="'.esc_url(Utils::get_placeholder_image_src(), $image_size_width, $image_size_width, true, true, true).'" alt="'.esc_attr($item['proc_heading']).'" /></div>';
				}


				echo '</div></div>';
			echo '<div class="gt3_process_item__circle_line_after"></div></div>';
			if ($settings['type'] == 'vertical') {
				echo '<div class="gt3_process_item__content">';
			}
			if (isset($item['proc_heading'])) {
				echo '<div class="gt3_process_item__heading">';
					if (isset($item['proc_number'])) {
						echo '<div class="gt3_process_item__number">';
							echo $item['proc_number'];
						echo '</div>';
					}
					echo '<h3>'.esc_html($item['proc_heading']).'</h3>';
				echo '</div>';
			}
			if (isset($item['proc_descr'])) {
				echo '<div class="gt3_process_item__description">';
					echo $item['proc_descr'];
				echo '</div>';
			}
			if ($settings['type'] == 'vertical') {
				echo '</div>';
			}
		echo '</div>';
	}	
	echo '</div>';
}