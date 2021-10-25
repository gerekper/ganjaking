<?php

if(!defined('ABSPATH')) {
	exit;
}

if(!class_exists( 'RevSlider' )) {
	return;
}

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_RevolutionSlider $widget */

$settings = array();

$settings = wp_parse_args($widget->get_settings(), $settings);

$widget->add_render_attribute('wrapper', 'class', array(
	'gt3_revolution-slider-elementor',
));

?>
	<div <?php $widget->print_render_attribute_string('wrapper') ?>>
		<?php
			if ($settings['gt3_rs_slider_core'] !== 'none') {
				echo do_shortcode( '[rev_slider ' . $settings['gt3_rs_slider_core'] . ']' );
			} else {
				echo '<div class="gt3_revolution-slider-elementor_not_found">' . esc_html__( 'Revolution Slider Error: No sliders found', 'gt3_themes_core' ) . '</div>';
			}
		?>
	</div>
<?php




