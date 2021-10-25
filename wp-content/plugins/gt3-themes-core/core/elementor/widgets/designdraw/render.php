<?php

if(!defined('ABSPATH')) {
	exit;
}

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_DesignDraw $widget */

$settings = array(
	'link'         => array( 'url' => '#', 'is_external' => false, 'nofollow' => false, ),
	'position' => 'left',
	'element_icon' => '',
);

$settings = wp_parse_args($widget->get_settings(), $settings);

$widget->add_render_attribute('wrapper', 'class', array(
	'gt3_svg_line',
	$settings['position'],
));

if(empty($settings['link']['url'])) {
	$settings['link']['url'] = '#';
}
$widget->add_render_attribute('link', 'class', 'gt3_svg_line_link');
$widget->add_render_attribute('link', 'href', esc_url($settings['link']['url']));

if($settings['link']['is_external']) {
	$widget->add_render_attribute('link', 'target', '_blank');
}

if(!empty($settings['link']['nofollow'])) {
	$widget->add_render_attribute('link', 'rel', 'nofollow');
}

?>
	<div <?php $widget->print_render_attribute_string('wrapper') ?>>
        <?php
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="44px" height="200px" viewBox="0 0 44 200" preserveAspectRatio="none">
			<g>
				<path fill="rgba(255,255,255,1)" stroke="#ffffff" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" opacity="1" d="M 45.703505156528365 200.19885384998346 C 45.73301315307617 190.79171752929688 45.365841356073965 101.6338238086374 45.662750244140625 0.25295501947402954 C 45.52727381388346 10.878458460172016 41.63068771362305 22.985551555951435 34.041473388671875 36.57423400878906 C 24.181148529052734 56.518985748291016 1.116410493850708 75.28781127929688 0.9567615985870361 100.19087982177734 C 1.1245900392532349 125.75543975830078 23.82019805908203 145.14585876464844 34.17644691467285 165.19732666015625 C 41.07911682128906 178.5620574951172 45.73301315307617 190.79171752929688 45.703505156528365 200.19885384998346 Z" transform="matrix(1 0 0 1 -0.729369 -0.220138)"></path>
			</g>
		</svg>';
        echo apply_filters('gt3/widgets/render/designdraw/svg', $svg); ?>

		<?php if ($settings['enable_icon'] == 'yes' && $settings['element_icon'] !== '') { ?>
			<span class="gt3_svg_line_icon <?php echo esc_attr($settings['element_icon']); ?>"></span>
		<?php } ?>
		<?php if ($settings['enable_link'] == 'yes') { ?>
			<a <?php $widget->print_render_attribute_string('link') ?>></a>
		<?php } ?>
	</div>
<?php
