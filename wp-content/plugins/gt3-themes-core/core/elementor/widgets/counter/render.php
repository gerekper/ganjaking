<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Counter $widget */

$settings = array(
	'start'               => 10,
	'end'                 => 100000,
	'description'         => '',
	'decimal'             => 0,
	'duration'            => 5000,
	'useEasing'           => '',
	'suffix'              => '',
	'prefix'              => '',
	'separator_enabled'   => true,
	'separator'           => '',
	'decimal_point'       => '.',
	'show_icon'           => false,
	'icon_image'          => 'icon',
	'icon_position'       => 'left',
	'icon'                => 'fa fa-bank',
	'text_color_gradient' => false,
);

$settings = wp_parse_args($widget->get_settings(), $settings);

if(!is_numeric($settings['duration']) || $settings['duration'] < 400) {
	$settings['duration'] = 5000;
}
if(!is_numeric($settings['start']) || $settings['start'] < 0) {
	$settings['start'] = 0;
}
if(!is_numeric($settings['end']) || $settings['end'] < $settings['start']) {
	$settings['end'] = $settings['start']*100;
}
if(!is_numeric($settings['decimal']) || $settings['decimal'] < 0) {
	$settings['decimal'] = 0;
}

$data = array(
	'start'    => intval($settings['start']),
	'end'      => intval($settings['end']),
	'decimal'  => intval($settings['decimal']),
	'duration' => round(intval($settings['duration'])/1000, 3),
);

$suffix = !empty($settings['suffix']) ? '<span class="counter_suffix">'.esc_attr($settings['suffix']).'</span>' : '';
$prefix = !empty($settings['prefix']) ? '<span class="counter_prefix">'.esc_attr($settings['prefix']).'</span>' : '';

$options = array(
	'useEasing'   => (bool) $settings['useEasing'],
	'easingFn'    => $settings['useEasing'],
	'useGrouping' => (bool) $settings['separator_enabled'],
	'separator'   => $settings['separator'],
	'decimal'     => $settings['decimal_point'],
	'prefix'      => $prefix,
	'suffix'      => $suffix,
);
$data['options'] = $options;

$widget->add_render_attribute('wrapper', 'data-settings', json_encode($data));
$widget->add_render_attribute('wrapper', 'data-options', json_encode($options));
$widget->add_render_attribute('wrapper', 'class', array(
	'counter-wrapper',
	'icon_type-'.$settings['icon_image'],
	'icon_position-'.$settings['icon_position'],
));

if ((bool) $settings['text_color_gradient']) {
	$widget->add_render_attribute('wrapper','class', 'text_gradient');
}

if(!$options['useGrouping']) {
	$settings['separator'] = '';
}

$image_style = '';
if ( $settings['image_size'] && is_array( $settings['image_size'] ) ) {
	$image_style = 'style="';
	$image_style .= $settings['image_size']['width'] ? 'width: '.(int)$settings['image_size']['width'].'px;' : '';
	$image_style .= $settings['image_size']['height'] ? 'height: '.(int)$settings['image_size']['height'].'px;' : '';
	$image_style .= '"';
}

$icon = '';
if((bool) $settings['show_icon']) {
	switch($settings['icon_image']) {
		case 'icon':
			$icon = '<div class="icon_container" '.$image_style.'><span class="gt3_icon '.esc_attr($settings['icon']).'"></span></div>';
			break;
		case 'image':
			if(isset($settings['image']['id']) && (bool) $settings['image']['id']) {
				$image = wp_get_attachment_image_src($settings['image']['id'],'full');
				if($image) {
					$image_obj = wp_prepare_attachment_for_js($settings['image']['id']);
					$icon = '<div class="icon_container" '.$image_style.'><img src="'.aq_resize($image['0'], (int)$settings['image_size']['width']*2, (int)$settings['image_size']['height']*2, true, true, true).'" alt="'.$image_obj['alt'].'" /></div>';
				} else {
					$icon = '<div class="icon_container" '.$image_style.'><img src="'.Utils::get_placeholder_image_src().'" alt="" /></div>';
				}
			} else {
				$icon = '<div class="icon_container" '.$image_style.'><img src="'.Utils::get_placeholder_image_src().'" alt="" /></div>';
			}
			break;
		default:
			break;
	}
}
?>
	<div <?php $widget->print_render_attribute_string('wrapper') ?>>
		<?php

		$counter_descr = '';
		if(!empty($settings['description'])) {
			$counter_descr = '<div class="description">'.$settings['description'].'</div>';
		}

		$counter_info = '<div class="counter_text"><div class="counter"></div><div class="hidden_end">'.$settings['prefix'].number_format($settings['end'], $settings['decimal'], $settings['decimal_point'], $settings['separator']).$suffix.'</div>'.$counter_descr.'</div>';

		switch($settings['icon_position']) {
			case 'left':
			case 'top':
				echo ''.$icon.$counter_info;
				break;
			case 'right':
			case 'bottom':
				echo ''.$counter_info.$icon;
				break;
		}
		?>
	</div>
<?php

$widget->print_data_settings($data);


