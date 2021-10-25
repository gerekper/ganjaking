<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Divider $widget */

$settings = array(
	'align'				=> '',
	'line_left'			=> '',
	'line_right'		=> '',
	'text'		=> '',
	'color'		=> '',
	'color_line'		=> '',
	'line_height'		=> '',
	'icon_position'      => 'left',
	'divider_icon_type'	=> '',
	'divider_icon'        => '',
	'image_size'         => array(
		'size' => 32,
		'unit' => 'px',
	),
	'icon_height' => '',
	'icon_lh'	=> '',
	'image'              => array(
		'url' => Utils::get_placeholder_image_src(),
	)
);

$settings = wp_parse_args($widget->get_settings(), $settings);

$icon = '';
switch($settings['divider_icon_type']) {
	case 'icon':
		$icon = '<span class="elementor_divider_icon_container"><span class="elementor_gt3_divider_icon '.esc_attr($settings['divider_icon']).'"></span></span>';
		break;
	case 'image':
		if(isset($settings['image']['id']) && (bool) $settings['image']['id']) {
			$image = wp_get_attachment_image_src($settings['image']['id'],'full');
			if($image) {
				if (strpos($image[0],'.svg') !== false) {
					$icon = '<span class="elementor_divider_icon_container"><span class="icon_svg_btn">'.file_get_contents($image[0]).'</span></span>';
				} else {
					$image_obj = wp_prepare_attachment_for_js($settings['image']['id']);
					$icon = '<span class="elementor_divider_icon_container"><img src="'.aq_resize($image[0], (int)$settings['image_size']['size']*2, '', true, true, true).'" alt="'.$image_obj['alt'].'" style="width:'.esc_attr((int)$settings['image_size']['size']).'px;" /></span>';
				}
			}
		}
		break;
	default:
		break;
}

if (!empty($icon)) {
	if ($settings['icon_position'] == 'left') {
		$settings['text'] = $icon . '<span>' . $settings['text'] . '</span>';
	}
	if ($settings['icon_position'] == 'right') {
		$settings['text'] = '<span>' . $settings['text'] . '</span>' . $icon;
	}

}

$widget->add_render_attribute('wrapper', 'class', array(
	'gt3_divider_wrapper-elementor',
	empty($settings['text']) ? 'without_text' : '',
));

?>
	<div <?php $widget->print_render_attribute_string('wrapper') ?>>
		<?php
		if($settings['line_left']) {
			echo '<span class="gt3_divider_line gt3_divider_line--left"><span></span></span>';
		}
		if(!empty($settings['text'])) {
			echo '<h6>'.wp_kses_post($settings['text']).'</h6>';
		}
		if($settings['line_right']) {
			echo '<span class="gt3_divider_line gt3_divider_line--right"><span></span></span>';
		}
		?>
	</div>
<?php




