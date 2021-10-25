<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;


$settings = array(
	'module_type'    => 'type1',
	'link'         => array( 'url' => '#', 'is_external' => false, 'nofollow' => false, ),
	'link_title'   => '',
	'image'        => array( 'url' => Utils::get_placeholder_image_src(), ),
	'title'        => '',
	'subtitle'     => '',
	'index_number' => '',
	'module_overlay'   => true,
);

$settings = wp_parse_args($widget->get_settings(), $settings);

$widget->add_render_attribute('wrapper', 'class', array(
	'gt3_imageinfobox',
	$settings['module_type'],
	(bool) $settings['module_overlay'] ? 'with_overlay' : 'overlay_disable',
));

$widget->add_render_attribute('img_bg', 'class', array(
	'gt3_imageinfobox_img_bg',
));

if(isset($settings['image']['id']) && (bool) $settings['image']['id']) {
	$image = wp_get_attachment_image_src($settings['image']['id'], 'large');
	if($image) {
		$widget->add_render_attribute('img_bg', 'style', 'background-image: url('.$image['0'].')');
	} else {
		$widget->add_render_attribute('img_bg', 'style', 'background-image: url('.Utils::get_placeholder_image_src().')');
	}
} else {
	$widget->add_render_attribute('img_bg', 'style', 'background-image: url('.Utils::get_placeholder_image_src().')');
}
?>
	<div <?php $widget->print_render_attribute_string('wrapper') ?>>
		<div class="gt3_imageinfobox_wrapper">
		<div <?php $widget->print_render_attribute_string('img_bg') ?>></div>
			<?php
			echo '<div class="gt3_imageinfobox_container">';
			if(!empty($settings['index_number'])) {
				echo '<div class="index_number">'.esc_html($settings['index_number']).'</div>';
			}

			if ($settings['module_type'] == 'type3') {
				echo '<div class="gt3_imageinfobox_divider"></div>';
			}

			if(!empty($settings['title']) || !empty($settings['subtitle'])) {
				echo '<div class="gt3_imageinfobox_title">';

				if(!empty($settings['title'])) {
					echo '<div class="box_title" >'.esc_html($settings['title']).'</div>';
				}
				if(!empty($settings['subtitle'])) {
					echo '<div class="box_subtitle">'.esc_html($settings['subtitle']).'</div>';
				}
				echo '</div>';
			}
			echo '</div>';

			if(empty($settings['link']['url'])) {
				$settings['link']['url'] = '#';
			}
			$widget->add_render_attribute('link', 'class', 'gt3_imageinfobox_link');
			$widget->add_render_attribute('link', 'href', esc_url($settings['link']['url']));
			$widget->add_render_attribute('link', 'title', $settings['link_title']);
			if($settings['link']['is_external']) {
				$widget->add_render_attribute('link', 'target', '_blank');
			}
			if(!empty($settings['link']['nofollow'])) {
				$widget->add_render_attribute('link', 'rel', 'nofollow');
			}
		?>
		</div>
		<a <?php $widget->print_render_attribute_string('link') ?>>
			<?php echo esc_html($settings['link_title']); ?>
		</a>
	</div>
<?php




