<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_FlipBox $widget */

$settings = array(
	'flip_effect'  => 'left',
	'flip_type'    => 'type1',
	'flip_style'   => true,
	'link'         => array( 'url' => '#', 'is_external' => false, 'nofollow' => false, ),
	'link_title'   => '',
	'index_number' => '',
	'image'        => array( 'url' => Utils::get_placeholder_image_src(), ),
	'title'        => '',
	'subtitle'     => '',
	'content_text' => '',
);

$settings = wp_parse_args($widget->get_settings(), $settings);

$widget->add_render_attribute('wrapper', 'class', array(
	'gt3_services_box',
	(bool) $settings['flip_style'] ? 'to-'.$settings['flip_effect'] : 'without_flip',
	!(bool) $settings['flip_style'] ? $settings['flip_type'] : '',
));

$widget->add_render_attribute('img_bg', 'class', array(
	'gt3_services_img_bg',
	'services_box-front',
	!empty($settings['index_number']) ? $settings['index_number'] : '',
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

$large_space_class = '';
if($settings['module_height']['size'] > 400) {
	$large_space_class = 'large_space_box';
}

?>

	<div <?php $widget->print_render_attribute_string('wrapper') ?>>
		<div <?php $widget->print_render_attribute_string('img_bg') ?>>
			<?php
			echo wp_kses_post( apply_filters( 'gt3/core/render/flipbox/block_wrap_start', '' ) );
			if(!empty($settings['index_number'])) {
				echo '<div class="index_number '.esc_attr($large_space_class).'">'.esc_html($settings['index_number']).'</div>';
			}

			if(!empty($settings['title']) || !empty($settings['subtitle'])) {
				echo '<div class="gt3_services_box_title '.esc_attr($large_space_class).'">';
				if (!(bool)$settings['flip_style'] && $settings['flip_type'] == 'type1') {
					if(!empty($settings['title'])) {
						echo '<div class="box_title" >'.esc_html($settings['title']).'</div>';
					}
					if(!empty($settings['subtitle'])) {
						echo '<div class="box_subtitle">'.esc_html($settings['subtitle']).'</div>';
					}
				
				} else {
					if(!empty($settings['subtitle'])) {
						echo '<div class="box_subtitle">'.esc_html($settings['subtitle']).'</div>';
					}
					if(!empty($settings['title'])) {
						echo '<div class="box_title" >'.esc_html($settings['title']).'</div>';
					}
				}
				echo '</div>';
			}
			echo wp_kses_post( apply_filters( 'gt3/core/render/flipbox/block_wrap_end', '' ) );
			?>
		</div>
		<div class="gt3_services_box_content services_box-back">
			<?php
			if(!empty($settings['content_text'])) {
				echo '<div class="text_wrap">'.$settings['content_text'].'</div>';
			}
			?>
			<div class="fake_space"></div>
		</div>
		<?php
		if(empty($settings['link']['url'])) {
			$settings['link']['url'] = '#';
		}
		$widget->add_render_attribute('link', 'class', 'gt3_services_box_link');
		$widget->add_render_attribute('link', 'href', esc_url($settings['link']['url']));
		$widget->add_render_attribute('link', 'title', $settings['link_title']);

		if($settings['link']['is_external']) {
			$widget->add_render_attribute('link', 'target', '_blank');
		}

		if(!empty($settings['link']['nofollow'])) {
			$widget->add_render_attribute('link', 'rel', 'nofollow');
		}

		?>
		<a <?php $widget->print_render_attribute_string('link') ?>>
			<?php echo esc_html($settings['link_title']); ?>
		</a>
	</div>
<?php




