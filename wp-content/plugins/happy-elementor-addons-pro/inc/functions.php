<?php

/**
 * Helper functions
 *
 * @package Happy_Addons Pro
 */

use Happy_Addons_Pro\Extension\Mega_Menu;

defined('ABSPATH') || die();

/**
 * Short Number Format
 * @param $n
 * @param int $precision
 * @return string
 */
function hapro_short_number_format($n, $precision = 1) {
	if ($n < 900) {
		// 0 - 900
		$n_format = number_format($n, $precision);
		$suffix = '';
	} else if ($n < 900000) {
		// 0.9k-850k
		$n_format = number_format($n / 1000, $precision);
		$suffix = 'K';
	} else if ($n < 900000000) {
		// 0.9m-850m
		$n_format = number_format($n / 1000000, $precision);
		$suffix = 'M';
	} else if ($n < 900000000000) {
		// 0.9b-850b
		$n_format = number_format($n / 1000000000, $precision);
		$suffix = 'B';
	} else {
		// 0.9t+
		$n_format = number_format($n / 1000000000000, $precision);
		$suffix = 'T';
	}
	// Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
	// Intentionally does not affect partials, eg "1.50" -> "1.50"
	if ($precision > 0) {
		$dotzero = '.' . str_repeat('0', $precision);
		$n_format = str_replace($dotzero, '', $n_format);
	}
	return $n_format . $suffix;
}


/**
 * Escaped title html tags
 *
 * @param string $tag input string of title tag
 * @return string $default default tag will be return during no matches
 */
if (!function_exists('ha_escape_tags')) {
	function ha_escape_tags($tag, $default = 'span', $extra = []) {

		$supports = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'];

		$supports = array_merge($supports, $extra);

		if (!in_array($tag, $supports, true)) {
			return $default;
		}

		return $tag;
	}
}


/**
 * Check license validity
 *
 * @return bool
 */
function hapro_has_valid_license() {
	return \Happy_Addons_Pro\Base::$appsero->license()->is_valid();
}

/**
 * Contain masking shape list
 * @param $element
 * @return array
 */
function hapro_masking_shape_list($element) {
	$dir = HAPPY_ADDONS_PRO_ASSETS . 'imgs/masking-shape/';
	$shape_name = 'shape';
	$extension = '.svg';
	$list = [];
	if ('list' == $element) {
		for ($i = 1; $i <= 39; $i++) {
			$list[$shape_name . $i] = [
				'title' => ucwords($shape_name . ' ' . $i),
				'url' => $dir . $shape_name . $i . $extension,
			];
		}
	} elseif ('url' == $element) {
		for ($i = 1; $i <= 39; $i++) {
			$list[$shape_name . $i] = $dir . $shape_name . $i . $extension;
		}
	}
	return $list;
}


/**
 * Compare value.
 *
 * Compare two values based on Comparison operator
 *
 * @param mixed $left_value  First value to compare.
 * @param mixed $right_value  Second value to compare.
 * @param string $operator  Comparison operator.
 * @return bool
 */
function hapro_compare($left_value, $right_value, $operator) {
	switch ($operator) {
		case 'is':
			return $left_value == $right_value;
		case 'not':
			return $left_value != $right_value;
		default:
			return $left_value === $right_value;
	}
}

/**
 * Get User Browser name
 *
 * @param $user_agent
 * @return string
 */
function hapro_get_browser_name($user_agent) {

	if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'opera';
	elseif (strpos($user_agent, 'Edge')) return 'edge';
	elseif (strpos($user_agent, 'Chrome')) return 'chrome';
	elseif (strpos($user_agent, 'Safari')) return 'safari';
	elseif (strpos($user_agent, 'Firefox')) return 'firefox';
	elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'ie';
	return 'other';
}

/**
 * Get Client Site Time
 * @param string $format
 * @return string
 */
function hapro_get_local_time($format = 'Y-m-d h:i:s A') {
	$local_time_zone = isset($_COOKIE['HappyLocalTimeZone']) && !empty($_COOKIE['HappyLocalTimeZone']) ? str_replace('GMT ', 'GMT+', $_COOKIE['HappyLocalTimeZone']) : date_default_timezone_get();
	$now_date = new \DateTime('now', new \DateTimeZone($local_time_zone));
	$today = $now_date->format($format);
	return $today;
}

/**
 * Get Server Time
 * @param string $format
 * @return string
 */
function hapro_get_server_time($format = 'Y-m-d h:i:s A') {
	$today 	= date($format, strtotime("now") + (get_option('gmt_offset') * HOUR_IN_SECONDS));
	return $today;
}

/**
 * Check elementor version
 *
 * @param string $version
 * @param string $operator
 * @return bool
 */
function hapro_is_elementor_version($operator = '>=', $version = '2.8.0') {
	return defined('ELEMENTOR_VERSION') && version_compare(ELEMENTOR_VERSION, $version, $operator);
}

/**
 * Get the list of all section templates
 *
 * @return array
 */
function hapro_get_section_templates() {

	$sections = ha_elementor()->templates_manager->get_source('local')->get_items(['type' => 'section']);
	if (!empty($sections)) {
		$sections = wp_list_pluck($sections, 'title', 'template_id');
	}

	$container = ha_elementor()->templates_manager->get_source('local')->get_items(['type' => 'container']);
	if (!empty($container)) {
		$container = wp_list_pluck($container, 'title', 'template_id');
	}
	
	if (!empty($sections) || !empty($container)) {
		return ( $sections + $container );
	}

	return [];
}

if (!function_exists('ha_get_section_icon')) {
	/**
	 * Get happy addons icon for panel section heading
	 *
	 * @return string
	 */
	function ha_get_section_icon() {
		return '<i style="float: right" class="hm hm-happyaddons"></i>';
	}
}


/**
 * Contain Devider shape list
 * @param $shape
 * @return array
 */
function hapro_devider_shape($shape) {

	if (empty($shape) || $shape === 'none') {
		return;
	}

	$shape_svg_list = [
		'clouds' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 61.7" preserveAspectRatio="none"><path class="st0" opacity="0.2" d="M399.9,61.7V25.3c-1.7-0.6-3.6-0.9-5.5-0.9c-1.9,0-3.7,0.3-5.4,0.8c-2.8-6.1-8.9-10.4-16.1-10.4c-3.3,0-6.3,0.9-8.9,2.4c-5.3-8.2-14.5-13.6-25-13.6c-12.3,0-22.9,7.5-27.4,18.3C308.2,11,298.1,3.1,286.1,3.1c-7,0-13.3,2.7-18.1,7c-1.6-0.5-3.2-0.8-4.9-0.8c-4.4,0-8.4,1.8-11.4,4.6c-3.6-4.9-9.4-8.1-15.9-8.1c-4.6,0-8.9,1.6-12.3,4.3c-3.4-2.7-7.6-4.3-12.3-4.3c-9.7,0-17.8,7-19.5,16.3c-1.4-0.6-3-1-4.7-1c-3.8,0-7.2,1.8-9.4,4.5c-3-7-9.9-11.9-18-11.9c-3.9,0-7.5,1.2-10.6,3.1c-4.9-7-13-11.6-22.2-11.6c-7.3,0-13.9,2.9-18.8,7.6c-4-3-9-4.8-14.5-4.8c-8.4,0-15.8,4.3-20.2,10.8c-1.5-0.7-3.3-1.1-5.1-1.1c-2.6,0-5.1,0.8-7,2.3c-3.8-3-8.6-4.8-13.8-4.8c-7.2,0-13.5,3.4-17.6,8.7c-2.1-1-4.4-1.6-6.8-1.6c-5.1,0-9.6,2.5-12.4,6.4C7.3,27.3,3.8,26.3,0,25.9v35.8H399.9z"/><path class="st0" opacity="0.2" d="M399.9,25.1c-1.6-0.3-3.3-0.5-5-0.5c-6.6,0-12.6,2.5-17.1,6.5c-3.1-10.7-13.1-18.6-24.8-18.6c-8.3,0-15.6,3.9-20.3,9.9c-4.7-6-12.1-9.9-20.3-9.9c-14.3,0-25.8,11.6-25.8,25.8c0,1,0.1,2,0.2,3c-4.7-5.7-11.9-9.4-19.9-9.4c-8,0-15.1,3.6-19.8,9.2c-4.9-4.5-11.4-7.2-18.5-7.2c-8.4,0-15.9,3.8-20.9,9.7c-4.4-9.7-14.2-16.4-25.5-16.4c-5.4,0-10.5,1.5-14.8,4.2c-3.5-10.1-13.1-17.3-24.4-17.3c-9,0-16.9,4.6-21.5,11.5c-3.9-5.3-10.2-8.7-17.3-8.7c-4.8,0-9.2,1.6-12.8,4.2c-3.7-6.4-10.6-10.6-18.5-10.6c-7.2,0-13.5,3.5-17.4,8.9c-2.4-4.2-7-7.1-12.2-7.1c-7.1,0-12.9,5.2-13.9,12c-0.5,0-1,0-1.4,0c-14.1,0-25.6,11.4-25.6,25.6c0,1.2,0.1,2.5,0.3,3.7c-0.8,0.1-1.7,0.3-2.5,0.5v7.6h400L399.9,25.1z"/><path class="st1" d="M399.9,61v-4.9c-2.3-1-4.8-1.5-7.5-1.5c-0.4,0-0.8,0-1.2,0c-2.7-7.3-9.8-12.6-18.1-12.6c-6,0-11.3,2.7-14.8,6.9c-4.8-7.1-12.8-11.7-22-11.7c-8.9,0-16.8,4.4-21.6,11.2C312.6,40.2,305,34,296,34c-6.5,0-12.2,3.2-15.7,8.1c-1.9-0.7-4-1-6.2-1c-5.4,0-10.3,2.2-13.8,5.9c-2.7-7.4-9.8-12.8-18.2-12.8c-6.5,0-12.2,3.2-15.7,8.1c-3.7-2.7-8.2-4.2-13.1-4.2c-9.1,0-16.9,5.4-20.4,13.2c-1.4-0.7-3-1.1-4.7-1.1c-3.2,0-6.1,1.3-8.1,3.5c-3.4-5.4-9.5-9-16.3-9c-3.9,0-7.6,1.2-10.6,3.2c-3.8-8.1-12.1-13.7-21.6-13.7c-6.9,0-13.2,2.9-17.5,7.7c-4.3-4.2-10.2-6.7-16.7-6.7c-9.2,0-17.2,5.1-21.2,12.7c-1.9-1.1-4-1.8-6.4-1.8c-3.9,0-7.3,1.8-9.6,4.7c-3.4-4.4-8.7-7.2-14.7-7.2c-7.3,0-13.6,4.1-16.7,10.2c-2.5-2.4-5.9-3.8-9.7-3.8c-5.2,0-9.7,2.8-12.2,6.9c-1.9-1.4-4.3-2.4-6.8-2.6V61v0.7h399.9V61L399.9,61L399.9,61z"/></svg>',

		'corner' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 45" preserveAspectRatio="none"><polygon class="st0" points="0,38.7 200,0 400,38.7 400,45 0,45 "/></svg>',

		'cross-line' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 59.7" preserveAspectRatio="none"><path class="st0" d="M0,59.7V19.8C0,17.7,1.7,16,3.8,16h0c2.1,0,3.8,1.7,3.8,3.8v14.2c0,2.1,1.7,3.8,3.8,3.8c2.1,0,3.8-1.7,3.8-3.8  V27c0-2.1,1.7-3.8,3.8-3.8h0c2.1,0,3.8,1.7,3.8,3.8v16.4c0,2.1,1.7,3.8,3.8,3.8c2.1,0,3.8-1.7,3.8-3.8V16.2c0-2.1,1.7-3.8,3.8-3.8h0  c2.1,0,3.8,1.7,3.8,3.8v17.7c0,2,1.7,3.7,3.7,3.7h0.1c2,0,3.7-1.7,3.7-3.7V19.8c0-2.1,1.7-3.8,3.8-3.8h0c2.1,0,3.8,1.7,3.8,3.8v27.8  c0,2,1.7,3.7,3.7,3.7h0.3c2,0,3.7-1.7,3.7-3.7V3.8c0-2.1,1.7-3.8,3.8-3.8c2.1,0,3.8,1.7,3.8,3.8v34.7c0,2,1.7,3.7,3.7,3.7l0,0  c2,0,3.7-1.7,3.7-3.7V23.4c0-2.1,1.7-3.8,3.8-3.8h0c2.1,0,3.8,1.7,3.8,3.8v6.7c0,2,1.7,3.7,3.7,3.7h0.1c2,0,3.7-1.7,3.7-3.7V8.9  c0-2.1,1.7-3.8,3.8-3.8h0c2.1,0,3.8,1.7,3.8,3.8v31.9c0,2,1.7,3.7,3.7,3.7h0.1c2,0,3.7-1.7,3.7-3.7V19.8c0-2.1,1.7-3.8,3.8-3.8h0  c2.1,0,3.8,1.7,3.8,3.8v5.7c0,2,1.7,3.7,3.7,3.7h0.1c2,0,3.7-1.7,3.7-3.7v-10c0-2.1,1.7-3.8,3.8-3.8h0c2.1,0,3.8,1.7,3.8,3.8v16.1  c0,2,1.7,3.7,3.7,3.7h0.1c2,0,3.7-1.7,3.7-3.7V19.8c0-2.1,1.7-3.8,3.8-3.8h0c2.1,0,3.8,1.7,3.8,3.8v27.3c0,2.1,1.7,3.8,3.8,3.8h0  c2.1,0,3.7-1.7,3.7-3.8V5.3c0-2.1,1.7-3.8,3.8-3.8h0c2.1,0,3.8,1.7,3.8,3.8v32.9c0,2.1,1.7,3.8,3.8,3.8s3.8-1.7,3.8-3.8v-13  c0-2.1,1.7-3.8,3.8-3.8h0c2.1,0,3.8,1.7,3.8,3.8V52c0,2.1,1.7,3.8,3.8,3.8h0.1c2.1,0,3.8-1.7,3.8-3.8l-0.1-41.3  c0-2.1,1.7-3.8,3.8-3.8h0c2.1,0,3.8,1.7,3.8,3.8v29.2c0,2.1,1.7,3.8,3.8,3.8h0c2.1,0,3.8-1.7,3.8-3.8V19.8c0-2.1,1.7-3.8,3.8-3.8h0  c2.1,0,3.8,1.7,3.8,3.8v4.9c0,2.1,1.7,3.8,3.8,3.8c2.1,0,3.8-1.7,3.8-3.8v-9.6c0-2.1,1.7-3.8,3.8-3.8h0c2.1,0,3.8,1.7,3.8,3.8v15.8  c0,2.1,1.7,3.8,3.8,3.8c2.1,0,3.8-1.7,3.8-3.8V19.8c0-2.1,1.7-3.8,3.8-3.8h0c2.1,0,3.8,1.7,3.8,3.8v14.2c0,2.1,1.7,3.8,3.8,3.8  c2.1,0,3.8-1.7,3.8-3.8V27c0-2.1,1.7-3.8,3.8-3.8h0c2.1,0,3.8,1.7,3.8,3.8v16.4c0,2.1,1.7,3.8,3.8,3.8c2.1,0,3.8-1.7,3.8-3.8V16.2  c0-2.1,1.7-3.8,3.8-3.8h0c2.1,0,3.8,1.7,3.8,3.8v17.7c0,2,1.7,3.7,3.7,3.7h0.1c2,0,3.7-1.7,3.7-3.7V19.8c0-2.1,1.7-3.8,3.8-3.8h0  c2.1,0,3.8,1.7,3.8,3.8v27.8c0,2,1.7,3.7,3.7,3.7h0.3c2,0,3.7-1.7,3.7-3.7V3.8c0-2.1,1.7-3.8,3.8-3.8s3.8,1.7,3.8,3.8v34.7  c0,2,1.7,3.7,3.7,3.7l0,0c2,0,3.7-1.7,3.7-3.7V23.4c0-2.1,1.7-3.8,3.8-3.8h0c2.1,0,3.8,1.7,3.8,3.8v6.7c0,2,1.7,3.7,3.7,3.7h0.1  c2,0,3.7-1.7,3.7-3.7V8.9c0-2.1,1.7-3.8,3.8-3.8h0c2.1,0,3.8,1.7,3.8,3.8v31.9c0,2,1.7,3.7,3.7,3.7h0.1c2,0,3.7-1.7,3.7-3.7V19.8  c0-2.1,1.7-3.8,3.8-3.8h0c2.1,0,3.8,1.7,3.8,3.8v5.7c0,2,1.7,3.7,3.7,3.7h0.1c2,0,3.7-1.7,3.7-3.7v-10c0-2.1,1.7-3.8,3.8-3.8h0  c2.1,0,3.8,1.7,3.8,3.8v16.1c0,2,1.7,3.7,3.7,3.7h0.1c2,0,3.7-1.7,3.7-3.7V19.8c0-2.1,1.7-3.8,3.8-3.8h0c2.1,0,3.8,1.7,3.8,3.8v27.3  c0,2.1,1.7,3.8,3.8,3.8h0c2.1,0,3.7-1.7,3.7-3.8V5.3c0-2.1,1.7-3.8,3.8-3.8h0c2.1,0,3.8,1.7,3.8,3.8v32.9c0,2.1,1.7,3.8,3.8,3.8  s3.8-1.7,3.8-3.8v-13c0-2.1,1.7-3.8,3.8-3.8h0c2.1,0,3.8,1.7,3.8,3.8v34.5H0z"/></svg>',

		'curve' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 35" preserveAspectRatio="none"><path class="st0" d="M0,33.6C63.8,11.8,130.8,0.2,200,0.2s136.2,11.6,200,33.4v1.2H0V33.6z"/></svg>',

		'drops' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 70.8" preserveAspectRatio="none"><path class="st0" d="M400,68c0,0-7.1-0.8-7.1-8.5s6.4-22.1,0-26s-7.3,2.6-6,6.5c1.3,3.9,6.2,14.5-2.6,14.8  c-6.3,0.2-0.8-8.3-7.4-10.2c-3.7-1.1-2,4.6-6.3,4.8c-6.3,0.4-7.8-10.1-7.8-12.4c0-2.3,1.9-28.7,0-32.9c-1.9-4.2-3.6-4.5-5.3-3.7  c-1.7,0.7-4.6,2.5-2,11.2c2.7,8.7,3.4,26,3.8,29.5c0.4,3.6,0.2,8.9-5.3,8.3c-5.6-0.6-0.9-16.1-6.6-15.7c-5.7,0.4-0.6,9-6.5,11.2  s-7.6,0.2-8-4.2c-0.3-4.4-5.9-5-8.6,6c-2.7,11-10.5,9-11.4,0.4s3.6-22.9-4.3-21.9s-3.6,11.9-2.2,14.4s6.3,22.2-6,27.3  c-12.3,5.1-1.7-33.5-10.4-32.9c-8.7,0.7,2.7,24.4-7.5,27.2c-10.2,2.8,0-15.1-7.1-17.6c-7.1-2.5,3.7,10.7-4.1,13.4  c-7.9,2.7-6.6-26.4-6.6-26.4s2.9-14.9-3.2-13.9c-6.1,1-1.7,14.7-0.7,18.6c0.9,3.9,1.7,22.2-7.4,22.5c-12.2,0.4-2.4-23.9-12.2-23.1  c-9.8,0.7,0.2,11.1-8.5,15.2c-2.5,1.2-5.6-5.9-8.7-4.9c-3.2,1-4.2,10.9-9.2,10.1c-5-0.7-4.4-11.5-4.3-18.7c0.1-7.1-3.9-7.9-3.7-2.4  c0.2,5,2.5,16.2-0.2,20.6c-0.5,0.7-1.4,1-2.2,0.5c-1.4-0.8-2.1-1.6-1.9,2.6c0.2,4.9-1.5,7.4-3.7,8c-0.3,0.1-0.6,0.1-0.9,0.1l0,0  c-0.6,0-1.2-0.1-1.8-0.3c-1.4-0.5-3.5-2-3.4-6c0.2-7.6,6.4-22.1,0-26c-6.4-3.9-7.3,2.6-6,6.5s6.2,14.5-2.6,14.8  c-6.3,0.2-0.8-8.3-7.4-10.2c-3.7-1.1-2,4.6-6.3,4.8c-6.3,0.4-7.8-10.1-7.8-12.4c0-2.3,1.9-28.7,0-32.9s-3.6-4.5-5.3-3.7  s-4.6,2.5-2,11.2c2.7,8.7,3.4,26,3.8,29.5c0.4,3.6,0.2,8.9-5.3,8.3c-5.6-0.6-0.9-16.1-6.6-15.7c-5.7,0.4-0.6,9-6.5,11.2  c-5.9,2.2-7.6,0.2-8-4.2s-5.9-5-8.6,6c-2.7,11-10.5,9-11.4,0.4c-0.8-8.6,3.6-22.9-4.3-21.9s-2.7,11.6-2.2,14.4  c0.9,4.8,2.7,25.8-5.4,24.6c-13.1-2-2.2-30.8-11-30.2c-8.7,0.7,2.7,24.4-7.5,27.2C72.5,64,83,41.9,75.6,43.6  C69.9,45,79.4,54.3,71.5,57c-7.9,2.7-6.6-26.4-6.6-26.4s2.9-14.9-3.2-13.9S60.1,31.4,61,35.3s1.7,22.2-7.4,22.5  c-12.2,0.4-2.4-23.9-12.2-23.1c-9.8,0.7,0.8,14.8-8.4,17.4c-7.9,2.3-3.6-10.5-9.5-10.1c-4,0.3,2.9,13.9-8.5,13  c-5-0.4-4.4-11.5-4.3-18.7c0.1-7.1-3.9-7.9-3.7-2.4c0.2,5.5,3,18.5-1.2,21.7c-2.2-0.6-3.4-3.2-3.2,2.1S2,67,0,68v2.8h400V68z"/></svg>',

		'mountains' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 86.4" preserveAspectRatio="none"><path class="st0" opacity="0.2" d="M0,69.3c0,0,76.2-89.2,215-32.8s185,32.8,185,32.8v17H0V69.3z"/><path class="st0" opacity="0.2" d="M0,69.3v17h400v-17c0,0-7.7-93.8-145.8-59.1S89.7,119,0,69.3z"/><path class="st1" d="M0,69.3c0,0,50.3-63.1,197.3-14.2S400,69.3,400,69.3v17H0V69.3z"/></svg>',

		'pyramids' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 45" preserveAspectRatio="none"><polygon class="st0" points="0.5,40.1 49.9,21.2 138.1,40.1 276.4,-0.2 400.5,40.1 400.5,45 0.5,45 "/></svg>',

		'splash' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 50" preserveAspectRatio="none"><g><path class="st0" d="M158.3,19.5c2.6,0.3,1.2-4.2-0.5-2C157.1,18.3,157.4,19.4,158.3,19.5z"/><path class="st0" d="M157.2,8.9c-0.8-0.7-1.8,1.1-1.7,1.4C156.2,11.4,158,9.6,157.2,8.9z"/><path class="st0" d="M171.8,19.1c2.2-0.6,1.1-3.2-0.6-1.3C170.7,18.6,170.9,19.4,171.8,19.1z"/><path class="st0" d="M154,23.1c0.9-0.1,1.2-1.2,0.5-2C152.8,18.9,151.4,23.4,154,23.1z"/><path class="st0" d="M140.5,22.7c0.8,0.2,1.1-0.6,0.6-1.3C139.4,19.6,138.3,22.1,140.5,22.7z"/><path class="st0" d="M140.5,26.9c-1.8,1.9,3.9,2,1.6,0.3C141.7,26.9,140.9,26.5,140.5,26.9z"/><path class="st0" d="M350.3,37.3c-0.1-0.1-0.1-0.2-0.2-0.3c-1-1.3-0.3-3-3.6-3.5c-1.9-0.4-5.3,0.3-7.2,0.7c-3,1-4.3,2.5-7.7,3   c-3.2,0.5-7,0.7-10.3,0.9c-2.8,0.1-5.4,0.1-7.4-1.2c-1.9-1.1-1.7-2.5-4-3.3c-9.3-3.1-15.9,2.9-23.9,4.9c-1.9-1.1-0.2-2.4-0.1-3.6   c-0.3-5-7.3-0.3-10.6,1c-1.7,0.7-3,1-4,1c-0.5-0.1-0.9-0.2-1.4-0.2c-1.7-0.6-2.4-2.3-3.6-3.7c1-0.4,2.1-0.7,2.9-1.4   c1-0.9,0.8-2.2-0.7-2.2c-1.1,0-2.6,1.4-3.3,2c-0.1,0.1-0.2,0.2-0.3,0.4c-2-1.2-5.1-1.4-8.2-0.6c0.5-1.1,1-2.2,0.4-2.8   c-1.1-1.1-3.2,0.6-4.4-0.2c-1.7-1.1,4.7-4.4-0.6-4.7c-1.1,0-2.8,0.3-2.6-1.2c0.1-1.1,2-1,2.2-2.3c0.1-1.4-2.2-1.1-1.1-3   c0.7-1,2.7-1.4,3-2.6c0.3-1.7-1.4-1-2.2-0.5c-1.2,0.8-1.9,2.7-3.3,3.4c-1.1,0.7-2.3,0.2-3,1.8c-0.3,0.8,0.1,3-1,3.4   c-2.4,0.8,0.8-6.1-2.5-4.7c-1.8,0.9-1,4.7-1.7,6.1c-0.5,0.9-1.4,2.6-2.7,2c-0.3-0.2-0.4-0.4-0.5-0.6c0-0.1,0-0.3,0-0.4   c0.1-0.7,0.5-1.5-0.2-1.9c-0.7-0.5-1.8,0.4-2.5,1.2c-0.1,0.1-0.2,0.1-0.2,0.2c-0.1,0.2-0.2,0.3-0.3,0.4c-1.4,1.7-1.1,4.9-3.1,6.8   c-0.6,0.6-2.5,2.1-3.5,1.4c-0.9,2.1-1.6,4.3-2.2,6.6c-0.4,0.3-0.9,0.6-1.4,0.9c-0.9-0.8-1.5-1.9-1.6-3   c-0.4-5.6,11.4-12.7,11.2-16.2c0,0-0.2-0.8-0.2-1.4c-0.3-4.7,6.6-8.2,6.9-9.7c0.1-0.5-0.6-1.2-2.1-1.1c-7.1,0.5-10.1,13-12.6,13.2   c-0.7,0.1-1.7-0.8-1.8-2.2c-0.1-1.4,0.7-2.8,0.6-4.3c-0.1-1.1-0.7-1.1-1.3-1.1c-3.2,0.2-4,13.6-11.1,14.1c-8.7,0.6-5-14-9.5-13.7   c-2.3,0.2-5.4,4-6.1,4.1c-1.9,0.1-0.6-4.3-5.5-4c-0.8,0.1-1.8,0.3-1.8,0.3c-1.8,0.1-0.8-2.9-2.5-2.8c-0.5,0-0.9,0.4-1.4,0.5   c-1.3,0.1-3.6-3-5.8-2.8c-0.9,0.1-2.3,0.8-2.2,2.2c0.2,3.1,5.3,0.9,7.2,5.5c0.7,1.8,0.3,4.4,2.1,5.8c2.2,1.7,10.8,9.5,11.1,13.1   c0.1,1.6-0.1,3.1-0.6,4.2c-0.2,0-0.3,0-0.5,0c-0.2-0.1-0.5-0.2-0.7-0.3c-1.2-2.8-2.5-5.5-4-8.1c-0.9,0.8-3-0.4-3.7-0.9   c-2.2-1.6-2.4-4.8-4-6.3c-0.5-0.6-2.5-2.2-3.3-1.4c-0.9,0.8,0.9,2.2-0.3,3c-1.3,0.8-2.4-0.8-3-1.6c-0.9-1.4-0.6-5.2-2.5-5.8   c-3.5-1,0.6,5.4-1.8,5c-0.3-0.1-0.5-0.3-0.7-0.6c0.6-0.2,0.9-0.7,0.3-1.4c-0.2-0.2-0.5-0.2-0.7-0.1c-0.1-0.5-0.2-0.9-0.3-1.2   c-0.9-1.5-2-0.9-3.2-1.4c-1.4-0.5-2.4-2.3-3.7-2.9c-0.9-0.5-2.6-0.9-2.1,0.8c0.4,1.2,2.5,1.3,3.3,2.2c1.3,1.7-1,1.7-0.7,3.1   c0.3,1.2,2.2,0.8,2.5,1.9c0.4,1.5-1.3,1.4-2.4,1.6c-5.1,1,1.6,3.3,0.1,4.7c-0.6,0.5-1.4,0.4-2.2,0.3c-0.3-0.2-0.8-0.4-1.3-0.6   c-3.7-1.2-7.6-0.9-9.6,0.7c-1,0.8-1.6,2-2.3,2.9c0,0-0.1,0-0.1,0c-0.9,0.1-1.3,0.7-1.1,1.2c-0.2,0.1-0.5,0.3-0.7,0.4   c-0.5,0.1-0.9,0.1-1.4,0.2c-1,0-2.3-0.3-4-1c-3.3-1.3-10.3-6-10.6-1c0.2,1.2,1.8,2.5-0.1,3.6c-8-2-14.7-8-23.9-4.9   c-2.4,0.9-2.1,2.3-4,3.3c-2,1.3-4.6,1.3-7.4,1.2c-3.3-0.2-7.1-0.4-10.3-0.9c-3.4-0.5-4.7-2-7.7-3c-1.9-0.5-5.3-1.1-7.2-0.7   c-3.3,0.5-2.6,2.2-3.6,3.5c-0.3,0.3-0.5,0.7-0.9,0.9c-43.7-2.6-66-5.7-66-5.7V50h400V32.3C400,32.3,385.5,34.9,350.3,37.3z"/><path class="st0" d="M237.9,15c-0.3,0-0.4,0.3-0.4,0.6c0,0.5,0.7,0.9,1.2,0.8c0.4,0,0.6-0.3,0.6-0.7C239.4,15.2,238.5,15,237.9,15z   "/><path class="st0" d="M232.2,9.1c0.7-0.6,1-1.1,1.8-1.4c2.4-1,2.9-2.5,2.8-3c0-0.6-0.5-0.7-1.4-0.6c-2.3,0.2-4,2.2-4.3,3.9   C230.9,9.1,231.4,9.8,232.2,9.1z"/><path class="st0" d="M228.5,5.5c0.4,0,1.5-1.1,1.4-2.5c-0.1-0.7-0.4-1.4-1.1-1.4c-0.6,0-1.6,0.7-1.5,1.7   C227.4,4.4,227.9,5.6,228.5,5.5z"/><path class="st0" d="M222.7,11.8c0.8-0.1,1.6-1.4,1.6-2.2c-0.1-0.8-0.6-1.2-1-1.2c-0.5,0-1.3,0.6-1.2,2   C222,10.7,222.2,11.8,222.7,11.8z"/><path class="st0" d="M196.8,13c0.2-0.2,0.3-0.5,0.2-0.8c0-0.3-1.8-5.6-2.5-5.5c-0.9,0.1-1.3,0.9-1.2,1.8   C193.4,9.4,196.4,13.5,196.8,13z"/><path class="st0" d="M184,11.2c0.3,0,1.1-1,1-2c-0.1-1-0.8-1.6-1.4-1.5c-0.5,0-0.8,1.3-0.7,2C183,10.7,183.6,11.2,184,11.2z"/><path class="st0" d="M178.5,8.7c0.5,0,0.9-0.1,0.9-0.7c-0.1-0.9-0.8-1.9-1.6-1.8c-0.6,0-0.7,0.3-0.7,0.8   C177.1,7.9,177.6,8.8,178.5,8.7z"/><path class="st0" d="M174.4,14.3c0.3-0.1,0.2-0.4,0.2-0.7c0-0.6-0.6-1-1.1-1C172.3,12.8,173.4,14.6,174.4,14.3z"/><path class="st0" d="M202.1,10.7c0.7,0.2,0.7-1.1,0.6-1.6c-0.1-1.3-1.7-2.8-1.6-1.2C201.2,8.6,201.3,10.6,202.1,10.7z"/><path class="st0" d="M203.1,4.1c0,0,0.8,0,0.7-0.7c0-0.4-0.7-1.1-1.2-1.1c-0.3,0-0.5,0.3-0.5,0.7C202.1,3.5,202.5,4,203.1,4.1z"/><path class="st0" d="M227.2,15.6c0.7,0.1,1.6-1.1,1.5-2.3c0-0.5-0.2-0.5-0.5-0.5c-0.8,0.1-1.4,0.8-1.4,1.5   C226.8,14.6,227,15.6,227.2,15.6z"/></g></svg>',

		'split' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 50" preserveAspectRatio="none"><path class="st0" d="M247.4,2.6C221.2,2.6,200,23.8,200,50c0-26.2-21.2-47.4-47.4-47.4H0V50h200h200V2.6H247.4z"/></svg>',

		'tilt' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 55" preserveAspectRatio="none"><polygon class="st0" points="0,55 400,55 0,0 "/></svg>',

		'torn-paper' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 61.7" preserveAspectRatio="none"><path class="st0" d="M400,61.7V17.9c-0.1-1.1-0.4-3.5-0.4-3.5c0-0.4-4-1.2-4,1.2c0,1.3-3.9,1.7-3.9,3.1c0-1-5.1-0.5-5.1,0  c0,0.1-4,2.6-4,2.7c0-0.6-4.8,2.1-4.8,0.9c0-1.3-5,0-5,0.2c0,0.8-3.8,2.6-3.8,3.3c0,0.3-4.7,0.5-4.7,1c0-1.1-3.9,3.4-3.9,2.9  c0-0.2-5.1-0.4-5.1,0.1c0-1.1-4.4,1.1-4.4,1.6c0,2.3-4.3,2.6-4.3,3.4c0,0.8-5.3-2.8-5.3-2.1c0-1.2-5.3-0.6-5.3,0.2  c0-0.4-5.1,2.8-5.1,1.8c0,0.6-4.7-0.1-4.7,0.6c0,0.9-3.4,3-3.4,2.9c0,0.2-3.4,2.4-3.4,2.8c0,1.2-4.8,0.3-4.8,0.1  c0-0.5-4.5,0.5-4.5,0.4c0-0.3-4.6,2.7-4.6,3c0-0.5-4.7-1.4-4.7-2c0,1-4.6,2-4.6,0.9c0,1-4.7,0.8-4.7-0.7c0,0-4.7-0.8-4.7,0.1  c0-0.2-4.6,2.1-4.6,2.2c0-0.1-4.6-0.7-4.6,0.5c0,0.3-4.3-1-4.3-0.6c0-0.1-3.9-2.8-3.9-1.9c0-0.5-3.9-2.1-3.9-2.7c0,0.8-3-3.4-3-3.7  c0,0.1-3.9-1.1-3.9-2.7c0-0.2-4.6-0.2-4.6-0.9c0-0.4-5.1-0.5-5.1-0.7c0-0.2-3.7,3.5-3.7,3.8c0-1.4-3.9,3.1-3.9,2.8  c0-1-4.7,0.5-4.7,1.7c0,0.5-4,0.5-4,1.7c0,0.1-3.6,3.3-3.6,2.9c0-1.3-4.4-0.2-4.4,0.9c0,0.7-4.6-1.2-4.6-0.1c0-1.2-4.1,1.4-4.1,1.7  c0,0.9-4.5-1.6-4.5-0.9c0,1.1-4.1,3.2-4.1,2.7c0-0.1-4.4,1.1-4.4,0.7c0,0.6-4.4,0.1-4.4-0.9c0,1.6-4.4-2.1-4.4-1.8  c0,1.2-4.3,3.9-4.3,2.9c0,0.1-4.3-1.3-4.3-0.6c0,2.1-4.4,0.8-4.4,0.4c0-0.7-3.8-4-3.8-3.4c0-1.7-3.6-2.5-3.6-2.1  c0-0.3-4.2-1.4-4.2-1c0-0.4-4.1,0.4-4.1-1.5c0-1.1-4.6,0.8-4.6-0.6c0,0.2-3.1-3.9-3.1-4c0,0.7-4.4-2.2-4.4-1.6c0,1-4.4-1.6-4.4-1.6  c0-0.4-4.9,0.8-4.9,0.3c0,0.7-4.6-0.2-4.6-0.3c0,0.3-4.4-0.6-4.4-1.4c0-0.4-4.6-1.7-4.6-1.8c0,0.4-4.7,0.6-4.7,0.9  c0,0-4.5,3-4.5,1.4c0,1.9-4.4,0.9-4.4,1.4c0-0.2-5.2-2-5.2-1.8c0,0.6-4.2,2.5-4.2,2.2c0,1.2-3.7,3.9-3.7,3.5c0-0.2-4.4,0.9-4.4,1.6  c0,0.8-5.1-0.2-5.1-0.8c0,0.4-4.5,1.5-4.5,1.5c0,0.7-4.7-0.9-4.7-0.8c0,0.3-4.4-0.7-4.4-1.4c0-0.8-4.7-0.9-4.7-0.1  c0-0.1-4.4-2.5-4.4-1.4c0-0.3-5.1,2-5.1,0.9c0-1.3-4.1-2.5-4.1-2.5c0,0.1-4.5-1.3-4.5-2.5c0-0.6-4.8,3.5-4.8,2.2  c0-0.9-4.3,2.5-4.3,2.4c0-0.6-5.4-1.1-5.4-1.8c0-1.3-4.1,4.2-4.1,3c0-0.2-5.1-0.5-5.1-0.7c0-0.5-3.9,1.2-3.9,1.3  c0-0.3-4.1-0.6-4.1-0.3c0-0.4-4.3,1.3-4.3,1c0,0.2-4.6,0.8-4.6-0.2c0-0.1-2.8-2.7-2.8-3.8c0-2-4.3-0.8-4.3-2c0,0.6-4.5,0.8-4.5-1.6  c0,0.2-2.6-5.2-2.6-4.7c0,1-1.9,0.1-3.2-0.5l0,33.9H400z"/></svg>',

		'triangle' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 45" preserveAspectRatio="none"><polygon class="st0" points="0,39.2 272.4,3.5 400,39.2 400,45 0,45 "/></svg>',

		'wave' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 35" preserveAspectRatio="none"><path class="st0" d="M0,35h400V24.9c0,0-43.8-25.4-114.9-3.4s-107.7,4.1-142.2-9S34.1-6.7,0,12.6V35z"/></svg>',

		'zigzag' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 12.4 400 35" preserveAspectRatio="none"><polygon class="st0" points="400,47.3 400,14.2 375,30.7 350,14.2 325,30.7 300,14.2 275,30.7 250,14.2 225,30.7 200,14.2 175,30.7   150,14.2 125,30.7 100,14.2 75,30.7 50,14.2 25,30.7 0,14.2 0,47.3 "/></svg>',
	];

	return $shape_svg_list[$shape];
}

/**
 * Get appsero instance
 *
 * @return Appsero
 */
function hapro_get_appsero() {
	return \Happy_Addons_Pro\Base::$appsero;
}


/**
 * Call a shortcode function by tag name.
 *
 * @since  1.0.0
 *
 * @param string $tag     The shortcode whose function to call.
 * @param array  $atts    The attributes to pass to the shortcode function. Optional.
 * @param array  $content The shortcode's content. Default is null (none).
 *
 * @return string|bool False on failure, the result of the shortcode on success.
 */
function hapro_do_shortcode($tag, array $atts = array(), $content = null) {
	global $shortcode_tags;
	if (!isset($shortcode_tags[$tag])) {
		return false;
	}
	return call_user_func($shortcode_tags[$tag], $atts, $content, $tag);
}

/**
 * Check if TablePress is activated
 *
 * @return bool
 */
function hapro_is_table_press_activated() {
	return class_exists('TablePress');
}

/**
 * TablePress Tables List
 *
 * @return array
 */
function hapro_get_table_press_list() {
	$lists = [];
	if (!hapro_is_table_press_activated()) return $lists;

	$tables = TablePress::$model_table->load_all(true);
	if ($tables) {
		foreach ($tables as $table) {
			$table = TablePress::$model_table->load($table, false, false);
			$lists[$table['id']] = $table['name'];
		}
	}

	return $lists;
}

/**
 * Database Table List
 *
 * @return array
 */
function hapro_db_tables_list() {
	global $wpdb;

	$tables_list = [];
	$tables = $wpdb->get_results('show tables', ARRAY_N);

	if ($tables) {
		$tables = wp_list_pluck($tables, 0);

		foreach ($tables as $table) {
			$tables_list[$table] = $table;
		}
	}

	return $tables_list;
}


function ha_mini_cart_count_total_fragments($fragments) {

	$fragments['.ha-mini-cart-count'] = '<span class="ha-mini-cart-count">' . WC()->cart->get_cart_contents_count() . '</span>';
	$fragments['.ha-mini-cart-total'] = '<span class="ha-mini-cart-total">' . WC()->cart->get_cart_total() . '</span>';

	$fragments['.ha-mini-cart-popup-count'] = '<span class="ha-mini-cart-popup-count">' . WC()->cart->get_cart_contents_count() . '</span>';

	return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'ha_mini_cart_count_total_fragments', 5, 1);


/**
 * HA Options Getter Setter
 */
function ha_get_option($key, $default = '') {
	$option_key = 'ha_megamenu_options';
	$data_all = get_option($option_key);
	return (isset($data_all[$key]) && $data_all[$key] != '') ? $data_all[$key] : $default;
}

function ha_save_option($key, $value = '') {
	$option_key = 'ha_megamenu_options';
	$data_all = get_option($option_key);
	$data_all[$key] = $value;
	update_option('ha_megamenu_options', $data_all);
}

/**
 * Get Menu Image Meta
 */
function ha_img_meta($id) {
	$attachment = get_post($id);
	if ($attachment == null || $attachment->post_type != 'attachment') {
		return null;
	}
	return [
		'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
		'caption' => $attachment->post_excerpt,
		'description' => $attachment->post_content,
		'href' => get_permalink($attachment->ID),
		'src' => $attachment->guid,
		'title' => $attachment->post_title
	];
}

function ha_get_registered_sidebars() {
	global $wp_registered_sidebars;
	$options = [];

	if (!$wp_registered_sidebars) {
		$options[''] = __('No sidebars were found', 'happy-addons-pro');
	} else {
		$options['---'] = __('Choose Sidebar', 'happy-addons-pro');

		foreach ($wp_registered_sidebars as $sidebar_id => $sidebar) {
			$options[$sidebar_id] = $sidebar['name'];
		}
	}
	return $options;
}


function ha_get_page_template_options($type = '') {
	$page_templates = ha_get_elementor_templates();

	$options[-1] = __('Select', 'happy-addons-pro');

	if (count($page_templates)) {
		foreach ($page_templates as $id => $name) {
			$options[$id] = $name;
		}
	} else {
		$options['no_template'] = __('No saved templates found!', 'happy-addons-pro');
	}

	return $options;
}


function ha_get_elementor_templates() {
	global $wpdb;
	$post_type = 'elementor_library';
	$where = '';
	$data = [];
	$limit = '';
	$where .= $wpdb->prepare(" AND {$wpdb->posts}.post_type = %s", esc_sql($post_type));

	$query = "select post_title,ID  from $wpdb->posts where post_status = 'publish' $where $limit";
	$results = $wpdb->get_results($query);
	if (!empty($results)) {
		foreach ($results as $row) {
			$data[$row->ID] = $row->post_title;
		}
	}
	return $data;
}

function ha_pro_sanitize_array_recursively($array) {

	foreach ($array as $key => &$value) {
		if (is_array($value)) {
			$value = ha_pro_sanitize_array_recursively($value);
		} else {
			$value = sanitize_text_field($value);
		}
	}

	return $array;
}