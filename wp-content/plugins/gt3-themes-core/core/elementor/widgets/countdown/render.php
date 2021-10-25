<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Countdown $widget */

$settings = array(
	'countdown_date' => '',
	'show_day'       => true,
	'show_seconds'   => true,
	'show_hours'     => true,
	'show_minutes'   => true,
	'size'           => '',
	'align'          => '',
);

$settings = wp_parse_args($widget->get_settings(), $settings);

$format = '';
if((bool) $settings['show_day']) {
	$format .= 'd';
}
if((bool) $settings['show_hours']) {
	$format .= 'H';
}
if((bool) $settings['show_minutes']) {
	$format .= 'M';
}
if((bool) $settings['show_seconds']) {
	$format .= 'S';
}

if(!empty($format)) {
	$format = ' data-format="'.esc_attr($format).'"';
}

$countdown_date = explode('-', $settings['countdown_date']);

if(!empty($settings['countdown_date'])) {
	$countdown_date  = explode('-', $settings['countdown_date']);
	$countdown_day   = explode(' ', $countdown_date[2]);
	$countdown_hours = explode(':', $countdown_day[1]);
	$countdown_hour  = $countdown_hours[0];
	$countdown_min   = $countdown_hours[1];
}

$countdown_year  = !empty($countdown_date) ? $countdown_date[0] : '';
$countdown_month = !empty($countdown_date) ? $countdown_date[1] : '';
$countdown_day   = !empty($countdown_day) ? $countdown_day[0] : '';
$countdown_hour  = !empty($countdown_hour) ? $countdown_hour : '';
$countdown_min   = !empty($countdown_min) ? $countdown_min : '';

$dataSettings = array(
	'format' => $format,
	'year'   => $countdown_year,
	'month'  => $countdown_month,
	'day'    => $countdown_day,
	'hours'  => $countdown_hour,
	'min'    => $countdown_min,
	'label'  => array(
		'years'   => esc_html__('Years', 'gt3_themes_core'),
		'months'  => esc_html__('Months', 'gt3_themes_core'),
		'weeks'   => esc_html__('Weeks', 'gt3_themes_core'),
		'days'    => esc_html__('Days', 'gt3_themes_core'),
		'hours'   => esc_html__('Hours', 'gt3_themes_core'),
		'minutes' => esc_html__('Minutes', 'gt3_themes_core'),
		'seconds' => esc_html__('Seconds', 'gt3_themes_core'),
	),
	'label1' => array(
		'years'   => esc_html__('Year', 'gt3_themes_core'),
		'months'  => esc_html__('Month', 'gt3_themes_core'),
		'weeks'   => esc_html__('Week', 'gt3_themes_core'),
		'days'    => esc_html__('Day', 'gt3_themes_core'),
		'hours'   => esc_html__('Hour', 'gt3_themes_core'),
		'minutes' => esc_html__('Minute', 'gt3_themes_core'),
		'seconds' => esc_html__('Second', 'gt3_themes_core'),
	)

)

?>
	<div class="gt3_countdown_wrapper">
		<div class="gt3_countdown"></div>
	</div>

<?php
$widget->print_data_settings($dataSettings);
