<?php

if(!defined('ABSPATH')) {
	exit;
}

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_EmptySpace $widget */

$settings = array(
	'responsive_es' => '',
	'height' => '',
	'height_sm_desktop' => '',
	'height_tablet' => '',
	'height_mobile' => '',
);

$settings = wp_parse_args($widget->get_settings(), $settings);

$widget->add_render_attribute('wrapper', 'class', array(
	'gt3_emptyspace_block',
));

if ($settings['responsive_es'] == 'yes') {
	$widget->add_render_attribute('wrapper','class',array(
		($settings['height_sm_desktop']['size'] >= 0 && $settings['height_sm_desktop']['size'] !== '') ? ' gt3_es_sm_desktop-on' : '',
		($settings['height_tablet']['size'] >= 0 && $settings['height_tablet']['size'] !== '') ? ' gt3_es_tablet-on' : '',
		($settings['height_mobile']['size'] >= 0 && $settings['height_mobile']['size'] !== '') ? ' gt3_es_mobile-on' : ''
	));
}
?>
	<div <?php $widget->print_render_attribute_string('wrapper') ?> data-block-title="<?php echo esc_attr__('EmptySpace Widget', 'gt3_themes_core'); ?>">
		<?php
			if ($settings['height']['size'] >= 0 && $settings['height']['size'] !== '') {
				echo '<div class="gt3_es gt3_es_default"></div>';
				if ($settings['responsive_es'] == 'yes') {
					if ($settings['height_sm_desktop']['size'] >= 0 && $settings['height_sm_desktop']['size'] !== '') {
						echo '<div class="gt3_es gt3_es_sm_desktop"></div>';
					}
					if ($settings['height_tablet']['size'] >= 0 && $settings['height_tablet']['size'] !== '') {
						echo '<div class="gt3_es gt3_es_tablet"></div>';
					}
					if ($settings['height_mobile']['size'] >= 0 && $settings['height_mobile']['size'] !== '') {
						echo '<div class="gt3_es gt3_es_mobile"></div>';
					}
				}
			}
		?>
	</div>
<?php




