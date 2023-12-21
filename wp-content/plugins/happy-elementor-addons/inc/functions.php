<?php

/**
 * Helper functions
 *
 * @package Happy_Addons
 */
defined('ABSPATH') || die();

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
function ha_do_shortcode($tag, array $atts = [], $content = null) {
	global $shortcode_tags;
	if (!isset($shortcode_tags[$tag])) {
		return false;
	}
	return call_user_func($shortcode_tags[$tag], $atts, $content, $tag);
}

/**
 * Sanitize html class string
 *
 * @param $class
 * @return string
 */
function ha_sanitize_html_class_param($class) {
	$classes   = !empty($class) ? explode(' ', $class) : [];
	$sanitized = [];
	if (!empty($classes)) {
		$sanitized = array_map(function ($cls) {
			return sanitize_html_class($cls);
		}, $classes);
	}
	return implode(' ', $sanitized);
}

function ha_is_script_debug_enabled() {
	return (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG);
}

/**
 * @param $settings
 * @param array $field_map
 */

function ha_prepare_data_prop_settings(&$settings, $field_map = []) {
	$data = [];
	foreach ($field_map as $key => $data_key) {
		$setting_value                          = ha_get_setting_value($settings, $key);
		list($data_field_key, $data_field_type) = explode('.', $data_key);
		$validator                              = $data_field_type . 'val';

		if (is_callable($validator)) {
			$val = call_user_func($validator, $setting_value);
		} else {
			$val = $setting_value;
		}
		$data[$data_field_key] = $val;
	}
	return wp_json_encode($data);
}

/**
 * @param $settings
 * @param $keys
 * @return mixed
 */
function ha_get_setting_value(&$settings, $keys) {
	if (!is_array($keys)) {
		$keys = explode('.', $keys);
	}
	if (is_array($settings[$keys[0]])) {
		return ha_get_setting_value($settings[$keys[0]], array_slice($keys, 1));
	}
	return $settings[$keys[0]];
}

function ha_is_localhost() {
	return isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);
}

function ha_get_css_cursors() {
	return [
		'default'      => __('Default', 'happy-elementor-addons'),
		'alias'        => __('Alias', 'happy-elementor-addons'),
		'all-scroll'   => __('All scroll', 'happy-elementor-addons'),
		'auto'         => __('Auto', 'happy-elementor-addons'),
		'cell'         => __('Cell', 'happy-elementor-addons'),
		'context-menu' => __('Context menu', 'happy-elementor-addons'),
		'col-resize'   => __('Col-resize', 'happy-elementor-addons'),
		'copy'         => __('Copy', 'happy-elementor-addons'),
		'crosshair'    => __('Crosshair', 'happy-elementor-addons'),
		'e-resize'     => __('E-resize', 'happy-elementor-addons'),
		'ew-resize'    => __('EW-resize', 'happy-elementor-addons'),
		'grab'         => __('Grab', 'happy-elementor-addons'),
		'grabbing'     => __('Grabbing', 'happy-elementor-addons'),
		'help'         => __('Help', 'happy-elementor-addons'),
		'move'         => __('Move', 'happy-elementor-addons'),
		'n-resize'     => __('N-resize', 'happy-elementor-addons'),
		'ne-resize'    => __('NE-resize', 'happy-elementor-addons'),
		'nesw-resize'  => __('NESW-resize', 'happy-elementor-addons'),
		'ns-resize'    => __('NS-resize', 'happy-elementor-addons'),
		'nw-resize'    => __('NW-resize', 'happy-elementor-addons'),
		'nwse-resize'  => __('NWSE-resize', 'happy-elementor-addons'),
		'no-drop'      => __('No-drop', 'happy-elementor-addons'),
		'not-allowed'  => __('Not-allowed', 'happy-elementor-addons'),
		'pointer'      => __('Pointer', 'happy-elementor-addons'),
		'progress'     => __('Progress', 'happy-elementor-addons'),
		'row-resize'   => __('Row-resize', 'happy-elementor-addons'),
		's-resize'     => __('S-resize', 'happy-elementor-addons'),
		'se-resize'    => __('SE-resize', 'happy-elementor-addons'),
		'sw-resize'    => __('SW-resize', 'happy-elementor-addons'),
		'text'         => __('Text', 'happy-elementor-addons'),
		'url'          => __('URL', 'happy-elementor-addons'),
		'w-resize'     => __('W-resize', 'happy-elementor-addons'),
		'wait'         => __('Wait', 'happy-elementor-addons'),
		'zoom-in'      => __('Zoom-in', 'happy-elementor-addons'),
		'zoom-out'     => __('Zoom-out', 'happy-elementor-addons'),
		'none'         => __('None', 'happy-elementor-addons'),
	];
}

function ha_get_css_blend_modes() {
	return [
		'normal'      => __('Normal', 'happy-elementor-addons'),
		'multiply'    => __('Multiply', 'happy-elementor-addons'),
		'screen'      => __('Screen', 'happy-elementor-addons'),
		'overlay'     => __('Overlay', 'happy-elementor-addons'),
		'darken'      => __('Darken', 'happy-elementor-addons'),
		'lighten'     => __('Lighten', 'happy-elementor-addons'),
		'color-dodge' => __('Color Dodge', 'happy-elementor-addons'),
		'color-burn'  => __('Color Burn', 'happy-elementor-addons'),
		'saturation'  => __('Saturation', 'happy-elementor-addons'),
		'difference'  => __('Difference', 'happy-elementor-addons'),
		'exclusion'   => __('Exclusion', 'happy-elementor-addons'),
		'hue'         => __('Hue', 'happy-elementor-addons'),
		'color'       => __('Color', 'happy-elementor-addons'),
		'luminosity'  => __('Luminosity', 'happy-elementor-addons'),
	];
}

/**
 * Check elementor version
 *
 * @param string $version
 * @param string $operator
 * @return bool
 */
function ha_is_elementor_version($operator = '<', $version = '2.6.0') {
	return defined('ELEMENTOR_VERSION') && version_compare(ELEMENTOR_VERSION, $version, $operator);
}

/**
 * Render icon html with backward compatibility
 *
 * @param array $settings
 * @param string $old_icon_id
 * @param string $new_icon_id
 * @param array $attributes
 */
function ha_render_icon($settings = [], $old_icon_id = 'icon', $new_icon_id = 'selected_icon', $attributes = []) {
	// Check if its already migrated
	$migrated = isset($settings['__fa4_migrated'][$new_icon_id]);
	// Check if its a new widget without previously selected icon using the old Icon control
	$is_new = empty($settings[$old_icon_id]);

	$attributes['aria-hidden'] = 'true';

	if (ha_is_elementor_version('>=', '2.6.0') && ($is_new || $migrated)) {
		\Elementor\Icons_Manager::render_icon($settings[$new_icon_id], $attributes);
	} else {
		if (empty($attributes['class'])) {
			$attributes['class'] = $settings[$old_icon_id];
		} else {
			if (is_array($attributes['class'])) {
				$attributes['class'][] = $settings[$old_icon_id];
			} else {
				$attributes['class'] .= ' ' . $settings[$old_icon_id];
			}
		}
		printf('<i %s></i>', \Elementor\Utils::render_html_attributes($attributes));
	}
}

/**
 * List of happy icons
 *
 * @return array
 */
function ha_get_happy_icons() {
	return \Happy_Addons\Elementor\Icons_Manager::get_happy_icons();
}

/**
 * Get elementor instance
 *
 * @return \Elementor\Plugin
 */
function ha_elementor() {
	return \Elementor\Plugin::instance();
}

/**
 * Escaped title html tags
 *
 * @param string $tag input string of title tag
 * @return string $default default tag will be return during no matches
 */

function ha_escape_tags($tag, $default = 'span', $extra = []) {

	$supports = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'];

	$supports = array_merge($supports, $extra);

	if (!in_array($tag, $supports, true)) {
		return $default;
	}

	return $tag;
}

/**
 * Get a list of all the allowed html tags.
 *
 * @param string $level Allowed levels are basic and intermediate
 * @return array
 */
function ha_get_allowed_html_tags($level = 'basic') {
	$allowed_html = [
		'b'      => [
			'class' => [],
			'id'    => [],
			'style' => [],
		],
		'i'      => [
			'class' => [],
			'id'    => [],
			'style' => [],
		],
		'u'      => [
			'class' => [],
			'id'    => [],
			'style' => [],
		],
		's'      => [
			'class' => [],
			'id'    => [],
			'style' => [],
		],
		'br'     => [
			'class' => [],
			'id'    => [],
			'style' => [],
		],
		'em'     => [
			'class' => [],
			'id'    => [],
			'style' => [],
		],
		'del'    => [
			'class' => [],
			'id'    => [],
			'style' => [],
		],
		'ins'    => [
			'class' => [],
			'id'    => [],
			'style' => [],
		],
		'sub'    => [
			'class' => [],
			'id'    => [],
			'style' => [],
		],
		'sup'    => [
			'class' => [],
			'id'    => [],
			'style' => [],
		],
		'code'   => [
			'class' => [],
			'id'    => [],
			'style' => [],
		],
		'mark'   => [
			'class' => [],
			'id'    => [],
			'style' => [],
		],
		'small'  => [
			'class' => [],
			'id'    => [],
			'style' => [],
		],
		'strike' => [
			'class' => [],
			'id'    => [],
			'style' => [],
		],
		'abbr'   => [
			'title' => [],
			'class' => [],
			'id'    => [],
			'style' => [],
		],
		'span'   => [
			'class' => [],
			'id'    => [],
			'style' => [],
		],
		'strong' => [
			'class' => [],
			'id'    => [],
			'style' => [],
		],
	];

	if ('intermediate' === $level || 'all' === $level) {
		$tags = [
			'a'       => [
				'href'  => [],
				'title' => [],
				'class' => [],
				'id'    => [],
				'style' => [],
			],
			'q'       => [
				'cite'  => [],
				'class' => [],
				'id'    => [],
				'style' => [],
			],
			'img'     => [
				'src'    => [],
				'alt'    => [],
				'height' => [],
				'width'  => [],
				'class'  => [],
				'id'     => [],
				'style'  => [],
			],
			'dfn'     => [
				'title' => [],
				'class' => [],
				'id'    => [],
				'style' => [],
			],
			'time'    => [
				'datetime' => [],
				'class'    => [],
				'id'       => [],
				'style'    => [],
			],
			'cite'    => [
				'title' => [],
				'class' => [],
				'id'    => [],
				'style' => [],
			],
			'acronym' => [
				'title' => [],
				'class' => [],
				'id'    => [],
				'style' => [],
			],
			'hr'      => [
				'class' => [],
				'id'    => [],
				'style' => [],
			],
		];

		$allowed_html = array_merge($allowed_html, $tags);
	}

	return $allowed_html;
}

/**
 * Strip all the tags except allowed html tags
 *
 * The name is based on inline editing toolbar name
 *
 * @param string $string
 * @return string
 */
function ha_kses_intermediate($string = '') {
	return wp_kses($string, ha_get_allowed_html_tags('intermediate'));
}

/**
 * Strip all the tags except allowed html tags
 *
 * The name is based on inline editing toolbar name
 *
 * @param string $string
 * @return string
 */
function ha_kses_basic($string = '') {
	return wp_kses($string, ha_get_allowed_html_tags('basic'));
}

/**
 * Get a translatable string with allowed html tags.
 *
 * @param string $level Allowed levels are basic and intermediate
 * @return string
 */
function ha_get_allowed_html_desc($level = 'basic') {
	if (!in_array($level, ['basic', 'intermediate'])) {
		$level = 'basic';
	}

	$tags_str = '<' . implode('>,<', array_keys(ha_get_allowed_html_tags($level))) . '>';
	return sprintf(__('This input field has support for the following HTML tags: %1$s', 'happy-elementor-addons'), '<code>' . esc_html($tags_str) . '</code>');
}

function ha_has_pro() {
	return defined('HAPPY_ADDONS_PRO_VERSION');
}

function ha_get_b64_icon() {
	return 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzMiAzMiI+PGcgZmlsbD0iI0ZGRiI+PHBhdGggZD0iTTI4LjYgNy44aC44Yy41IDAgLjktLjUuOC0xIDAtLjUtLjUtLjktMS0uOC0zLjUuMy02LjgtMS45LTcuOC01LjMtLjEtLjUtLjYtLjctMS4xLS42cy0uNy42LS42IDEuMWMxLjIgMy45IDQuOSA2LjYgOC45IDYuNnoiLz48cGF0aCBkPSJNMzAgMTEuMWMtLjMtLjYtLjktMS0xLjYtMS0uOSAwLTEuOSAwLTIuOC0uMi00LS44LTctMy42LTguNC03LjEtLjMtLjYtLjktMS4xLTEuNi0xQzguMyAxLjkgMS44IDcuNC45IDE1LjEuMSAyMi4yIDQuNSAyOSAxMS4zIDMxLjIgMjAgMzQuMSAyOSAyOC43IDMwLjggMTkuOWMuNy0zLjEuMy02LjEtLjgtOC44em0tMTEuNiAxLjFjLjEtLjUuNi0uOCAxLjEtLjdsMy43LjhjLjUuMS44LjYuNyAxLjFzLS42LjgtMS4xLjdsLTMuNy0uOGMtLjQtLjEtLjgtLjYtLjctMS4xek0xMC4xIDExYy4yLTEuMSAxLjQtMS45IDIuNS0xLjYgMS4xLjIgMS45IDEuNCAxLjYgMi41LS4yIDEuMS0xLjQgMS45LTIuNSAxLjYtMS0uMi0xLjgtMS4zLTEuNi0yLjV6bTE0LjYgMTAuNkMyMi44IDI2IDE3LjggMjguNSAxMyAyN2MtMy42LTEuMi02LjItNC41LTYuNS04LjItLjEtMSAuOC0xLjcgMS43LTEuNmwxNS40IDIuNWMuOSAwIDEuNCAxIDEuMSAxLjl6Ii8+PHBhdGggZD0iTTE3LjEgMjIuOGMtMS45LS40LTMuNy4zLTQuNyAxLjctLjIuMy0uMS43LjIuOS42LjMgMS4yLjUgMS45LjcgMS44LjQgMy43LjEgNS4xLS43LjMtLjIuNC0uNi4yLS45LS43LS45LTEuNi0xLjUtMi43LTEuN3oiLz48L2c+PC9zdmc+';
}

/**
 * @param $suffix
 */
function ha_get_dashboard_link($suffix = '#home') {
	return add_query_arg(['page' => 'happy-addons' . $suffix], admin_url('admin.php'));
}

/**
 * @param $suffix
 */
function ha_get_setup_wizard_link() {
	return add_query_arg(['page' => 'happy-addons-setup-wizard'], admin_url('admin.php'));
}

/**
 * @return mixed
 */
function ha_get_current_user_display_name() {
	$user = wp_get_current_user();
	$name = 'user';
	if ($user->exists() && $user->display_name) {
		$name = $user->display_name;
	}
	return $name;
}

/**
 * Get All Post Types
 * @param array $args
 * @param array $diff_key
 * @return array|string[]|WP_Post_Type[]
 */
function ha_get_post_types($args = [], $diff_key = []) {
	$default = [
		'public'            => true,
		'show_in_nav_menus' => true,
	];
	$args       = array_merge($default, $args);
	$post_types = get_post_types($args, 'objects');
	$post_types = wp_list_pluck($post_types, 'label', 'name');

	if (!empty($diff_key)) {
		$post_types = array_diff_key($post_types, $diff_key);
	}
	return $post_types;
}

/**
 * Get All Taxonomies
 * @param array $args
 * @param string $output
 * @param bool $list
 * @param array $diff_key
 * @return array|string[]|WP_Taxonomy[]
 */
function ha_get_taxonomies($args = [], $output = 'object', $list = true, $diff_key = []) {

	$taxonomies = get_taxonomies($args, $output);
	if ('object' === $output && $list) {
		$taxonomies = wp_list_pluck($taxonomies, 'label', 'name');
	}

	if (!empty($diff_key)) {
		$taxonomies = array_diff_key($taxonomies, $diff_key);
	}

	return $taxonomies;
}

if (!function_exists('ha_get_section_icon')) {
	/**
	 * Get happy addons icon for panel section heading
	 *
	 * @return string
	 */
	function ha_get_section_icon() {
		return '<i style="float: right" class="hm hm-happyaddons ha-section-icon"></i>';
	}
}

/**
 * Render icon html with backward compatibility
 *
 * @param array $settings
 * @param string $old_icon_id
 * @param string $new_icon_id
 * @param array $attributes
 */
function ha_render_button_icon($settings = [], $old_icon_id = 'icon', $new_icon_id = 'selected_icon', $attributes = []) {
	// Check if its already migrated
	$migrated = isset($settings['__fa4_migrated'][$new_icon_id]);
	// Check if its a new widget without previously selected icon using the old Icon control
	$is_new = empty($settings[$old_icon_id]);

	$attributes['aria-hidden'] = 'true';
	$is_svg                    = (isset($settings[$new_icon_id], $settings[$new_icon_id]['library']) && 'svg' === $settings[$new_icon_id]['library']);

	if (ha_is_elementor_version('>=', '2.6.0') && ($is_new || $migrated)) {
		if ($is_svg) {
			echo '<span class="ha-btn-icon ha-btn-icon--svg">';
		}
		\Elementor\Icons_Manager::render_icon($settings[$new_icon_id], $attributes);
		if ($is_svg) {
			echo '</span>';
		}
	} else {
		if (empty($attributes['class'])) {
			$attributes['class'] = $settings[$old_icon_id];
		} else {
			if (is_array($attributes['class'])) {
				$attributes['class'][] = $settings[$old_icon_id];
			} else {
				$attributes['class'] .= ' ' . $settings[$old_icon_id];
			}
		}
		printf('<i %s></i>', \Elementor\Utils::render_html_attributes($attributes));
	}
}

/**
 * Get database settings of a widget by widget id and element
 *
 * @param array $elements
 * @param string $widget_id
 * @param array $value
 */

function ha_get_ele_widget_element_settings($elements, $widget_id) {

	if (is_array($elements)) {
		foreach ($elements as $d) {
			if ($d && !empty($d['id']) && $d['id'] == $widget_id) {
				return $d;
			}
			if ($d && !empty($d['elements']) && is_array($d['elements'])) {
				$value = ha_get_ele_widget_element_settings($d['elements'], $widget_id);
				if ($value) {
					return $value;
				}
			}
		}
	}

	return false;
}

/**
 * Get database settings of a widget by widget id and post id
 *
 * @param number $post_id
 * @param string $widget_id
 * @param array
 */

function ha_get_ele_widget_settings($post_id, $widget_id) {

	$elementor_data = @json_decode(get_post_meta($post_id, '_elementor_data', true), true);

	if ($elementor_data) {
		$element = ha_get_ele_widget_element_settings($elementor_data, $widget_id);
		return isset($element['settings']) ? $element['settings'] : '';
	}

	return false;
}

/**
 * get credentials function
 *
 * @param string $key
 *
 * @return void
 * @since 1.0.0
 */
function ha_get_credentials($key = '') {
	if (!class_exists('Happy_Addons\Elementor\Credentials_Manager')) {
		include_once(HAPPY_ADDONS_DIR_PATH . 'classes/credentials-manager.php');
	}
	$creds = Happy_Addons\Elementor\Credentials_Manager::get_saved_credentials();
	if (!empty($key)) {
		return isset($creds[$key]) ? $creds[$key] : esc_html__('invalid key', 'happy-elementor-addons');
	}
	return $creds;
}

/**
 * Get plugin missing notice
 *
 * @param string $plugin
 * @return void
 */
function ha_show_plugin_missing_alert($plugin) {
	if (current_user_can('activate_plugins') && $plugin) {
		printf(
			'<div %s>%s</div>',
			'style="margin: 1rem;padding: 1rem 1.25rem;border-left: 5px solid #f5c848;color: #856404;background-color: #fff3cd;"',
			$plugin . __(' is missing! Please install and activate ', 'happy-elementor-addons') . $plugin . '.'
		);
	}
}

/**
 * Get inactive happy feature list
 *
 * @return array
 */
function ha_get_inactive_features() {
	return get_option('happyaddons_inactive_features', []);
}

/**
 * Get post date link
 *
 * @param int $post_id
 * @return string
 */
function ha_get_date_link($post_id = null) {
	if (empty($post_id)) {
		$post_id = get_the_ID();
	}

	$year = get_the_date('Y', $post_id);
	$month = get_the_time('m', $post_id);
	$day = get_the_time('d', $post_id);
	$url = get_day_link($year, $month, $day);

	return $url;
}

/**
 * Get post excerpt by length
 *
 * @param integer $length
 * @return string
 */
function ha_get_excerpt($post_id = null, $length = 15) {
	if (empty($post_id)) {
		$post_id = get_the_ID();
	}

	return wp_trim_words(get_the_excerpt($post_id), $length);
}

function ha_sanitize_array_recursively($array) {

	foreach ($array as $key => &$value) {
		if (is_array($value)) {
			$value = ha_sanitize_array_recursively($value);
		} else {
			$value = sanitize_text_field($value);
		}
	}

	return $array;
}