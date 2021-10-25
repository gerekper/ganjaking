<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_InfoList $widget */

$settings = array(
	'list'          => array(),
	'icon_position' => 'left',
);
$settings = wp_parse_args($widget->get_settings(), $settings);

if(is_array($settings['list']) && !empty($settings['list'])) {
	$widget->add_render_attribute('wrapper', 'class', array(
		'infolist-wrapper',
		'position-'.$settings['icon_position'],
	));
	echo '<div '.$widget->get_render_attribute_string('wrapper').'>';
	foreach($settings['list'] as $item_raw) {
		$item = array(
			'icon_type'   => 'icon',
			'icon'        => '',
			'image'       => array( 'url' => Utils::get_placeholder_image_src() ),
			'title'       => '',
			'description' => '',
		);
		$item = wp_parse_args($item_raw, $item);

		$half_img_icon_width = $line_style = '';
		if($settings['icon_image_size']['size'] != '') {
			$half_img_icon_width = $settings['icon_image_size']['size']/2;
			$line_pos            = 'left';
			if($settings['icon_position'] == 'right') {
				$line_pos = 'right';
			}
			$line_style = ' style="'.esc_attr($line_pos).': '.esc_attr($half_img_icon_width).esc_attr($settings['icon_image_size']['unit']).';"';
		}

		?>
		<div class="timeline-item">
			<?php
			echo '<div class="icon-box_wrap"><div class="icon-wrapper">';
			if($item['icon_type'] == 'icon') {
				if(!empty($item['icon'])) {
					echo '<div class="icon '.esc_attr($item['icon']).'"></div>';
				}
			} else {
				if(isset($item['image']['id']) && (bool) $item['image']['id']) {
					$image = wp_get_attachment_image_src($item['image']['id'],'full');
					if($image) {
						echo '<div class="image"><img src="'.esc_url(aq_resize($image['0'], $settings['image_size_width']['size']*2, '', true, true, true)).'" alt="'.esc_attr($item['title']).'" /></div>';
					} else {
						echo '<div class="image no-image"><img src="'.Utils::get_placeholder_image_src().'" alt="'.esc_attr($item['title']).'" /></div>';
					}
				} else {
					echo '<div class="image no-image"><img src="'.Utils::get_placeholder_image_src().'" alt="'.esc_attr($item['title']).'" /></div>';
				}
			}
			echo '</div><div class="line" '.$line_style.'></div></div>'; // div.icon-wrapper
			echo '<div class="content_block">';
			if(!empty($item['title'])) {
				echo '<div class="title">'.esc_html($item['title']).'</div>';
			}
			if(!empty($item['description'])) {
				echo '<div class="description">'.esc_html($item['description']).'</div>';
			}
			echo '</div>';
			?>
		</div>
	<?php
	}
	echo '</div>';
}




