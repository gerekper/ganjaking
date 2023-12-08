<?php
//TODO: namespace need.  Note: We don't use namespace because use them easily
use Elementor\Plugin;
use ElementPack\Element_Pack_Loader;
use ElementPack\Base\Element_Pack_Base;

/**
 * You can easily add white label branding for for extended license or multi site license.
 * Don't try for regular license otherwise your license will be invalid.
 * return white label
 */
define('BDTEP_PNAME', basename(dirname(BDTEP__FILE__)));
define('BDTEP_PBNAME', plugin_basename(BDTEP__FILE__));
define('BDTEP_PATH', plugin_dir_path(BDTEP__FILE__));
define('BDTEP_URL', plugins_url('/', BDTEP__FILE__));
define('BDTEP_ADMIN_PATH', BDTEP_PATH . 'admin/');
define('BDTEP_ADMIN_URL', BDTEP_URL . 'admin/');
define('BDTEP_MODULES_PATH', BDTEP_PATH . 'modules/');
define('BDTEP_INC_PATH', BDTEP_PATH . 'includes/');
define('BDTEP_ASSETS_URL', BDTEP_URL . 'assets/');
define('BDTEP_ASSETS_PATH', BDTEP_PATH . 'assets/');
define('BDTEP_MODULES_URL', BDTEP_URL . 'modules/');


if (!defined('BDTEP')) {
	define('BDTEP', '');
} //Add prefix for all widgets <span class="bdt-widget-badge"></span>
if (!defined('BDTEP_CP')) {
	define('BDTEP_CP', '<span class="bdt-ep-widget-badge"></span>');
} //Add prefix for all widgets <span class="bdt-widget-badge"></span>
if (!defined('BDTEP_NC')) {
	define('BDTEP_NC', '<span class="bdt-ep-new-control"></span>');
} //Add prefix for all widgets <span class="bdt-widget-badge"></span>
if (!defined('BDTEP_UC')) {
	define('BDTEP_UC', '<span class="bdt-ep-updated-control"></span>');
} // if you have any custom style
if (!defined('BDTEP_SLUG')) {
	define('BDTEP_SLUG', 'element-pack');
} // set your own alias

function is_ep_pro() {
	return apply_filters('bdt_ep_init_pro', false);
}

function element_pack_is_edit() {
	return Plugin::$instance->editor->is_edit_mode();
}

function element_pack_is_preview() {
	return Plugin::$instance->preview->is_preview_mode();
}

/**
 * Show any alert by this function
 *
 * @param mixed $message [description]
 * @param string css class $type
 * @param boolean $close [description]
 *
 * @return string [description]
 */
function element_pack_alert($message, $type = 'warning', $close = true) {
?>
	<div class="bdt-alert-<?php echo esc_attr($type); ?>" data-bdt-alert>
		<?php if ($close) : ?>
			<a class="bdt-alert-close" data-bdt-close></a>
		<?php endif; ?>
		<?php echo wp_kses_post($message); ?>
	</div>
	<?php
}

function element_pack_get_alert($message, $type = 'warning', $close = true) {

	$output = '<div class="bdt-alert-' . $type . '" bdt-alert>';
	if ($close) :
		$output .= '<a class="bdt-alert-close" bdt-close></a>';
	endif;
	$output .= wp_kses_post($message);
	$output .= '</div>';

	return $output;
}

/**
 * all array css classes will output as proper space
 *
 * @param array $classes shortcode css class as array
 *
 * @return array string
 */

function element_pack_get_post_types($args = []) {

	$post_type_args = [
		'show_in_nav_menus' => true,
	];

	if (!empty($args['post_type'])) {
		$post_type_args['name'] = $args['post_type'];
	}

	$_post_types = get_post_types($post_type_args, 'objects');

	$post_types = ['0' => esc_html__('Select Type', 'bdthemes-element-pack')];

	foreach ($_post_types as $post_type => $object) {
		$post_types[$post_type] = $object->label;
	}

	return $post_types;
}

function element_pack_get_users($args = array()) {

	$users     = get_users();
	$user_list = array();

	if (empty($users)) {
		return $user_list;
	}

	foreach ($users as $user) {
		$user_list[$user->ID] = $user->display_name;
	}

	return $user_list;
}

function element_pack_get_posts() {

	$post_types = get_post_types();

	$post_list = get_posts(
		array(
			'post_type'      => $post_types,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'posts_per_page' => -1,
		)
	);

	$posts = array();

	if (!empty($post_list) && !is_wp_error($post_list)) {
		foreach ($post_list as $post) {
			$posts[$post->ID] = $post->post_title;
		}
	}

	return $posts;
}

function element_pack_allow_tags($tag = null) {
	$tag_allowed = wp_kses_allowed_html('post');

	$tag_allowed['input']  = [
		'class'   => [],
		'id'      => [],
		'name'    => [],
		'value'   => [],
		'checked' => [],
		'type'    => [],
	];
	$tag_allowed['select'] = [
		'class'    => [],
		'id'       => [],
		'name'     => [],
		'value'    => [],
		'multiple' => [],
		'type'     => [],
	];
	$tag_allowed['option'] = [
		'value'    => [],
		'selected' => [],
	];

	$tag_allowed['title'] = [
		'a'      => [
			'href'  => [],
			'title' => [],
			'class' => [],
		],
		'br'     => [],
		'em'     => [],
		'strong' => [],
		'hr'     => [],
	];

	$tag_allowed['text'] = [
		'a'      => [
			'target' => [],
			'href'   => [],
			'title'  => [],
			'class'  => [],
		],
		'br'     => [],
		'em'     => [],
		'strong' => [],
		'hr'     => [],
		'i'      => [
			'class' => [],
		],
		'span'   => [
			'class' => [],
		],
	];

	$tag_allowed['svg'] = [
		'svg'     => [
			'version'     => [],
			'xmlns'       => [],
			'viewbox'     => [],
			'xml:space'   => [],
			'xmlns:xlink' => [],
			'x'           => [],
			'y'           => [],
			'style'       => [],
		],
		'g'       => [],
		'path'    => [
			'class' => [],
			'd'     => [],
		],
		'ellipse' => [
			'class' => [],
			'cx'    => [],
			'cy'    => [],
			'rx'    => [],
			'ry'    => [],
		],
		'circle'  => [
			'class' => [],
			'cx'    => [],
			'cy'    => [],
			'r'     => [],
		],
		'rect'    => [
			'x'         => [],
			'y'         => [],
			'transform' => [],
			'height'    => [],
			'width'     => [],
			'class'     => [],
		],
		'line'    => [
			'class' => [],
			'x1'    => [],
			'x2'    => [],
			'y1'    => [],
			'y2'    => [],
		],
		'style'   => [],
	];

	if ($tag == null) {
		return $tag_allowed;
	} elseif (is_array($tag)) {
		$new_tag_allow = [];

		foreach ($tag as $_tag) {
			$new_tag_allow[$_tag] = $tag_allowed[$_tag];
		}

		return $new_tag_allow;
	} else {
		return isset($tag_allowed[$tag]) ? $tag_allowed[$tag] : [];
	}
}

/**
 * post pagination
 */
function element_pack_post_pagination($wp_query) {

	/** Stop execution if there's only 1 page */
	if ($wp_query->max_num_pages <= 1) {
		return;
	}

	if (is_front_page()) {
		$paged = (get_query_var('page')) ? get_query_var('page') : 1;
	} else {
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	}

	$max = intval($wp_query->max_num_pages);

	/** Add current page to the array */
	if ($paged >= 1) {
		$links[] = $paged;
	}

	/** Add the pages around the current page to the array */
	if ($paged >= 3) {
		$links[] = $paged - 1;
		$links[] = $paged - 2;
	}

	if (($paged + 2) <= $max) {
		$links[] = $paged + 2;
		$links[] = $paged + 1;
	}

	echo '<ul class="bdt-pagination bdt-flex-center">' . "\n";

	/** Previous Post Link */
	if (get_previous_posts_link()) {
		printf('<li>%s</li>' . "\n", get_previous_posts_link('<span data-bdt-pagination-previous></span>'));
	}

	/** Link to first page, plus ellipses if necessary */
	if (!in_array(1, $links)) {
		$class = 1 == $paged ? ' class="current"' : '';

		printf('<li%1$s><a href="%2$s">%3$s</a></li>' . "\n", $class, esc_url(get_pagenum_link(1)), '1');

		if (!in_array(2, $links)) {
			echo '<li class="bdt-pagination-dot-dot"><span>...</span></li>';
		}
	}

	/** Link to current page, plus 2 pages in either direction if necessary */
	sort($links);

	foreach ((array) $links as $link) {
		$class = $paged == $link ? ' class="bdt-active"' : '';
		printf('<li%1$s><a href="%2$s">%3$s</a></li>' . "\n", $class, esc_url(get_pagenum_link($link)), $link);
	}

	/** Link to last page, plus ellipses if necessary */
	if (!in_array($max, $links)) {
		if (!in_array($max - 1, $links)) {
			echo '<li class="bdt-pagination-dot-dot"><span>...</span></li>' . "\n";
		}

		$class = $paged == $max ? ' class="bdt-active"' : '';
		printf('<li%1$s><a href="%2$s">%3$s</a></li>' . "\n", $class, esc_url(get_pagenum_link($max)), $max);
	}

	/** Next Post Link */
	if (get_next_posts_link()) {
		printf('<li>%s</li>' . "\n", get_next_posts_link('<span data-bdt-pagination-next></span>'));
	}

	echo '</ul>' . "\n";
}

function element_pack_template_edit_link($template_id) {
	if (Element_Pack_Loader::elementor()->editor->is_edit_mode()) {

		$final_url = add_query_arg(['elementor' => ''], get_permalink($template_id));

		$output = sprintf('<a class="bdt-elementor-template-edit-link" href="%1$s" title="%2$s" target="_blank"><i class="eicon-edit"></i></a>', esc_url($final_url), esc_html__('Edit Template', 'bdthemes-element-pack'));

		return $output;
	}

	return false;
}

function element_pack_template_on_modal_with_iframe($template_id, $id) {
	if (Element_Pack_Loader::elementor()->editor->is_edit_mode()) {
		$src           = add_query_arg(['elementor' => ''], get_permalink($template_id));
		$modalSelector = "bdt-template-modal-iframe-{$id}";
	?>
		<a class="bdt-template-modal-iframe-edit-link bdt-elementor-template-edit-link" data-modal-element=".<?php echo esc_attr($modalSelector) ?>" href="javascript:void(0)" title="<?php esc_html__('Edit Template', 'bdthemes-element-pack') ?>" target="_blank">
			<i class="eicon-edit"></i>
		</a>
		<div class="<?php echo esc_attr($modalSelector) ?> bdt-flex-top" bdt-modal>
			<div class="bdt-modal-dialog bdt-width-auto bdt-margin-auto-vertical">
				<button class="bdt-modal-close-outside" type="button" bdt-close></button>
				<iframe src="<?php echo esc_attr($src) ?>" width="1600" height="800" data-bdt-responsive></iframe>
			</div>
		</div>
	<?php
	}
}

/**
 * @param $currency
 * @param int $precision
 *
 * @return false|string
 */
function element_pack_currency_format($currency, $precision = 1) {

	if ($currency > 0) {
		if ($currency < 900) {
			// 0 - 900
			$currency_format = number_format($currency, $precision);
			$suffix          = '';
		} else if ($currency < 900000) {
			// 0.9k-850k
			$currency_format = number_format($currency / 1000, $precision);
			$suffix          = 'K';
		} else if ($currency < 900000000) {
			// 0.9m-850m
			$currency_format = number_format($currency / 1000000, $precision);
			$suffix          = 'M';
		} else if ($currency < 900000000000) {
			// 0.9b-850b
			$currency_format = number_format($currency / 1000000000, $precision);
			$suffix          = 'B';
		} else {
			// 0.9t+
			$currency_format = number_format($currency / 1000000000000, $precision);
			$suffix          = 'T';
		}
		// Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
		// Intentionally does not affect partials, eg "1.50" -> "1.50"
		if ($precision > 0) {
			$dotzero         = '.' . str_repeat('0', $precision);
			$currency_format = str_replace($dotzero, '', $currency_format);
		}

		return $currency_format . $suffix;
	}

	return false;
}

/**
 * @return array
 */
function element_pack_get_menu() {

	$menus = wp_get_nav_menus();
	$items = [0 => esc_html__('Select Menu', 'bdthemes-element-pack')];
	foreach ($menus as $menu) {
		$items[$menu->slug] = $menu->name;
	}

	return $items;
}

/**
 * default get_option() default value check
 *
 * @param string $option settings field name
 * @param string $section the section name this field belongs to
 * @param string $default default text if it's not found
 *
 * @return mixed
 */
function element_pack_option($option, $section, $default = '') {

	$options = get_option($section);

	if (isset($options[$option])) {
		return $options[$option];
	}

	return $default;
}

/**
 * @return array of anywhere templates
 * will be deprecated next major version
 */
function element_pack_ae_options() {

	if (post_type_exists('ae_global_templates')) {
		$anywhere = get_posts(array(
			'fields'         => 'ids', // Only get post IDs
			'posts_per_page' => -1,
			'post_type'      => 'ae_global_templates',
		));

		$anywhere_options = ['0' => esc_html__('Select Template', 'bdthemes-element-pack')];

		foreach ($anywhere as $key => $value) {
			$anywhere_options[$value] = get_the_title($value);
		}
	} else {
		$anywhere_options = ['0' => esc_html__('AE Plugin Not Installed', 'bdthemes-element-pack')];
	}

	return $anywhere_options;
}

/**
 * @return array of elementor template
 * will be deprecated next major version
 */
function element_pack_et_options() {

	$templates = Element_Pack_Loader::elementor()->templates_manager->get_source('local')->get_items();
	$types     = [];

	if (empty($templates)) {
		$template_options = ['0' => __('Template Not Found!', 'bdthemes-element-pack')];
	} else {
		$template_options = ['0' => __('Select Template', 'bdthemes-element-pack')];

		foreach ($templates as $template) {
			$template_options[$template['template_id']] = $template['title'] . ' (' . $template['type'] . ')';
			$types[$template['template_id']]            = $template['type'];
		}
	}

	return $template_options;
}

/**
 * @return array of wp default sidebars
 */
function element_pack_sidebar_options() {

	global $wp_registered_sidebars;
	$sidebar_options = [];

	if (!$wp_registered_sidebars) {
		$sidebar_options[0] = esc_html__('No sidebars were found', 'bdthemes-element-pack');
	} else {
		$sidebar_options[0] = esc_html__('Select Sidebar', 'bdthemes-element-pack');

		foreach ($wp_registered_sidebars as $sidebar_id => $sidebar) {
			$sidebar_options[$sidebar_id] = $sidebar['name'];
		}
	}

	return $sidebar_options;
}

/**
 * @param string category name
 * @return array of category
 */
function element_pack_get_terms($taxonomy = 'category') {

	$post_options = [];

	$post_categories = get_terms([
		'taxonomy'   => $taxonomy,
		'hide_empty' => false,
	]);

	if (is_wp_error($post_categories)) {
		return $post_options;
	}

	if (false !== $post_categories and is_array($post_categories)) {
		foreach ($post_categories as $category) {
			$post_options[$category->term_id] = $category->name;
		}
	}

	return $post_options;
}

/**
 * @param string parent category name
 * @return array of parent category
 */
function element_pack_get_only_parent_cats($taxonomy = 'category') {

	$parent_categories = ['none' => __('None', 'bdthemes-element-pack')];
	$args              = ['parent' => 0];
	$parent_cats       = get_terms($taxonomy, $args);

	foreach ($parent_cats as $parent_cat) {
		$parent_categories[$parent_cat->term_id] = ucfirst($parent_cat->name);
	}
	return $parent_categories;
}

/**
 * @param $post_type string any post type that you want to show category
 * @param $separator string separator for multiple category
 *
 * @return string
 */
function element_pack_get_category_list($post_type, $separator = ' ') {
	switch ($post_type) {
		case 'campaign':
			$taxonomy = 'campaign_category';
			break;
		case 'lightbox_library':
			$taxonomy = 'ngg_tag';
			break;
		case 'give_forms':
			$taxonomy = 'give_forms_category';
			break;
		case 'tribe_events':
			$taxonomy = 'tribe_events_cat';
			break;
		case 'product':
			$taxonomy = 'product_cat';
			break;
		case 'portfolio':
			$taxonomy = 'portfolio_filter';
			break;
		case 'faq':
			$taxonomy = 'faq_filter';
			break;
		case 'bdthemes-testimonial':
			$taxonomy = 'testimonial_categories';
			break;
		case 'knowledge_base':
			$taxonomy = 'knowledge-type';
			break;
		default:
			$taxonomy = 'category';
			break;
	}

	$categories  = get_the_terms(get_the_ID(), $taxonomy);
	$_categories = [];
	if ($categories) {
		foreach ($categories as $category) {
			$link                           = '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . $category->name . '</a>';
			$_categories[$category->slug] = $link;
		}
	}
	return implode(esc_attr($separator), $_categories);
}

/**
 * @param array all ajax posted array there
 *
 * @return array return all setting as array
 */
function element_pack_ajax_settings($settings) {

	$required_settings = [
		'show_date'      => true,
		'show_comment'   => true,
		'show_link'      => true,
		'show_meta'      => true,
		'show_title'     => true,
		'show_excerpt'   => true,
		'show_lightbox'  => true,
		'show_thumbnail' => true,
		'show_category'  => false,
		'show_tags'      => false,
	];

	foreach ($settings as $key => $value) {
		if (in_array($key, $required_settings)) {
			$required_settings[$key] = $value;
		}
	}

	return $required_settings;
}

/**
 * @return array of all transition names
 */
function element_pack_transition_options() {

	$transition_options = [
		''                    => esc_html__('None', 'bdthemes-element-pack'),
		'fade'                => esc_html__('Fade', 'bdthemes-element-pack'),
		'scale-up'            => esc_html__('Scale Up', 'bdthemes-element-pack'),
		'scale-down'          => esc_html__('Scale Down', 'bdthemes-element-pack'),
		'slide-top'           => esc_html__('Slide Top', 'bdthemes-element-pack'),
		'slide-bottom'        => esc_html__('Slide Bottom', 'bdthemes-element-pack'),
		'slide-left'          => esc_html__('Slide Left', 'bdthemes-element-pack'),
		'slide-right'         => esc_html__('Slide Right', 'bdthemes-element-pack'),
		'slide-top-small'     => esc_html__('Slide Top Small', 'bdthemes-element-pack'),
		'slide-bottom-small'  => esc_html__('Slide Bottom Small', 'bdthemes-element-pack'),
		'slide-left-small'    => esc_html__('Slide Left Small', 'bdthemes-element-pack'),
		'slide-right-small'   => esc_html__('Slide Right Small', 'bdthemes-element-pack'),
		'slide-top-medium'    => esc_html__('Slide Top Medium', 'bdthemes-element-pack'),
		'slide-bottom-medium' => esc_html__('Slide Bottom Medium', 'bdthemes-element-pack'),
		'slide-left-medium'   => esc_html__('Slide Left Medium', 'bdthemes-element-pack'),
		'slide-right-medium'  => esc_html__('Slide Right Medium', 'bdthemes-element-pack'),
	];

	return $transition_options;
}

// BDT Blend Type
function element_pack_blend_options() {
	$blend_options = [
		'multiply'    => esc_html__('Multiply', 'bdthemes-element-pack'),
		'screen'      => esc_html__('Screen', 'bdthemes-element-pack'),
		'overlay'     => esc_html__('Overlay', 'bdthemes-element-pack'),
		'darken'      => esc_html__('Darken', 'bdthemes-element-pack'),
		'lighten'     => esc_html__('Lighten', 'bdthemes-element-pack'),
		'color-dodge' => esc_html__('Color-Dodge', 'bdthemes-element-pack'),
		'color-burn'  => esc_html__('Color-Burn', 'bdthemes-element-pack'),
		'hard-light'  => esc_html__('Hard-Light', 'bdthemes-element-pack'),
		'soft-light'  => esc_html__('Soft-Light', 'bdthemes-element-pack'),
		'difference'  => esc_html__('Difference', 'bdthemes-element-pack'),
		'exclusion'   => esc_html__('Exclusion', 'bdthemes-element-pack'),
		'hue'         => esc_html__('Hue', 'bdthemes-element-pack'),
		'saturation'  => esc_html__('Saturation', 'bdthemes-element-pack'),
		'color'       => esc_html__('Color', 'bdthemes-element-pack'),
		'luminosity'  => esc_html__('Luminosity', 'bdthemes-element-pack'),
	];

	return $blend_options;
}

// BDT Position
function element_pack_position() {
	$position_options = [
		''              => esc_html__('Default', 'bdthemes-element-pack'),
		'top-left'      => esc_html__('Top Left', 'bdthemes-element-pack'),
		'top-center'    => esc_html__('Top Center', 'bdthemes-element-pack'),
		'top-right'     => esc_html__('Top Right', 'bdthemes-element-pack'),
		'center'        => esc_html__('Center', 'bdthemes-element-pack'),
		'center-left'   => esc_html__('Center Left', 'bdthemes-element-pack'),
		'center-right'  => esc_html__('Center Right', 'bdthemes-element-pack'),
		'bottom-left'   => esc_html__('Bottom Left', 'bdthemes-element-pack'),
		'bottom-center' => esc_html__('Bottom Center', 'bdthemes-element-pack'),
		'bottom-right'  => esc_html__('Bottom Right', 'bdthemes-element-pack'),
	];

	return $position_options;
}

// BDT Thumbnavs Position
function element_pack_thumbnavs_position() {
	$position_options = [
		'top-left'      => esc_html__('Top Left', 'bdthemes-element-pack'),
		'top-center'    => esc_html__('Top Center', 'bdthemes-element-pack'),
		'top-right'     => esc_html__('Top Right', 'bdthemes-element-pack'),
		'center-left'   => esc_html__('Center Left', 'bdthemes-element-pack'),
		'center-right'  => esc_html__('Center Right', 'bdthemes-element-pack'),
		'bottom-left'   => esc_html__('Bottom Left', 'bdthemes-element-pack'),
		'bottom-center' => esc_html__('Bottom Center', 'bdthemes-element-pack'),
		'bottom-right'  => esc_html__('Bottom Right', 'bdthemes-element-pack'),
	];

	return $position_options;
}

function element_pack_navigation_position() {
	$position_options = [
		'top-left'      => esc_html__('Top Left', 'bdthemes-element-pack'),
		'top-center'    => esc_html__('Top Center', 'bdthemes-element-pack'),
		'top-right'     => esc_html__('Top Right', 'bdthemes-element-pack'),
		'center'        => esc_html__('Center', 'bdthemes-element-pack'),
		'center-left'   => esc_html__('Center Left', 'bdthemes-element-pack'),
		'center-right'  => esc_html__('Center Right', 'bdthemes-element-pack'),
		'bottom-left'   => esc_html__('Bottom Left', 'bdthemes-element-pack'),
		'bottom-center' => esc_html__('Bottom Center', 'bdthemes-element-pack'),
		'bottom-right'  => esc_html__('Bottom Right', 'bdthemes-element-pack'),
	];

	return $position_options;
}

function element_pack_pagination_position() {
	$position_options = [
		'top-left'      => esc_html__('Top Left', 'bdthemes-element-pack'),
		'top-center'    => esc_html__('Top Center', 'bdthemes-element-pack'),
		'top-right'     => esc_html__('Top Right', 'bdthemes-element-pack'),
		'center-left'   => esc_html__('Center Left', 'bdthemes-element-pack'),
		'center-right'  => esc_html__('Center Right', 'bdthemes-element-pack'),
		'bottom-left'   => esc_html__('Bottom Left', 'bdthemes-element-pack'),
		'bottom-center' => esc_html__('Bottom Center', 'bdthemes-element-pack'),
		'bottom-right'  => esc_html__('Bottom Right', 'bdthemes-element-pack'),
	];

	return $position_options;
}

// BDT Drop Position
function element_pack_drop_position() {
	$drop_position_options = [
		'bottom-left'    => esc_html__('Bottom Left', 'bdthemes-element-pack'),
		'bottom-center'  => esc_html__('Bottom Center', 'bdthemes-element-pack'),
		'bottom-right'   => esc_html__('Bottom Right', 'bdthemes-element-pack'),
		'bottom-justify' => esc_html__('Bottom Justify', 'bdthemes-element-pack'),
		'top-left'       => esc_html__('Top Left', 'bdthemes-element-pack'),
		'top-center'     => esc_html__('Top Center', 'bdthemes-element-pack'),
		'top-right'      => esc_html__('Top Right', 'bdthemes-element-pack'),
		'top-justify'    => esc_html__('Top Justify', 'bdthemes-element-pack'),
		'left-top'       => esc_html__('Left Top', 'bdthemes-element-pack'),
		'left-center'    => esc_html__('Left Center', 'bdthemes-element-pack'),
		'left-bottom'    => esc_html__('Left Bottom', 'bdthemes-element-pack'),
		'right-top'      => esc_html__('Right Top', 'bdthemes-element-pack'),
		'right-center'   => esc_html__('Right Center', 'bdthemes-element-pack'),
		'right-bottom'   => esc_html__('Right Bottom', 'bdthemes-element-pack'),
	];

	return $drop_position_options;
}

// Button Size
function element_pack_button_sizes() {
	$button_sizes = [
		'xs' => esc_html__('Extra Small', 'bdthemes-element-pack'),
		'sm' => esc_html__('Small', 'bdthemes-element-pack'),
		'md' => esc_html__('Medium', 'bdthemes-element-pack'),
		'lg' => esc_html__('Large', 'bdthemes-element-pack'),
		'xl' => esc_html__('Extra Large', 'bdthemes-element-pack'),
	];

	return $button_sizes;
}

// Button Size
function element_pack_heading_size() {
	$heading_sizes = [
		'h1' => 'H1',
		'h2' => 'H2',
		'h3' => 'H3',
		'h4' => 'H4',
		'h5' => 'H5',
		'h6' => 'H6',
	];

	return $heading_sizes;
}

// Title Tags
function element_pack_title_tags() {
	$title_tags = [
		'h1'   => 'H1',
		'h2'   => 'H2',
		'h3'   => 'H3',
		'h4'   => 'H4',
		'h5'   => 'H5',
		'h6'   => 'H6',
		'div'  => 'div',
		'span' => 'span',
		'p'    => 'p',
	];

	return $title_tags;
}

// function element_pack_mask_shapes() {
//     $path       = BDTEP_ASSETS_URL . 'images/mask/';
//     $shape_name = 'shape';
//     $extension  = '.svg';
//     $list       = [0 => esc_html__('Select Mask', 'bdthemes-element-pack')];

//     for ($i = 1; $i <= 20; $i++) {
//         $list[$path . $shape_name . '-' . $i . $extension] = ucwords($shape_name . ' ' . $i);
//     }

//     return $list;
// }

/**
 * This is a mask shape list function which return a mask shape list
 *
 * @return array list
 */
function element_pack_mask_shapes() {
	$shape_name = 'shape';
	$list       = [];

	for ($i = 1; $i <= 31; $i++) {
		$list[$shape_name . '-' . $i] = ucwords($shape_name . ' ' . $i);
	}

	return $list;
}

/**
 * This is a svg file converter function which return a svg content
 *
 * @param string file
 * @return false content
 */
function element_pack_svg_icon($icon) {

	$icon_path = BDTEP_ASSETS_PATH . "images/svg/{$icon}.svg";

	if (!file_exists($icon_path)) {
		return false;
	}

	ob_start();

	include $icon_path;

	$svg = ob_get_clean();

	return $svg;
}

/**
 * This is a svg file converter function which return a svg content
 *
 * @return false content
 */
function element_pack_load_svg($icon) {

	if (!file_exists($icon)) {
		return false;
	}

	ob_start();

	include $icon;

	$svg = ob_get_clean();

	return $svg;
}

/**
 * weather code to icon and description output
 * more info: http://www.apixu.com/doc/Apixu_weather_conditions.json
 */
function element_pack_weather_code($code = null, $condition = null) {

	$codes = apply_filters('element-pack/weather/codes', [
		"113" => [
			"desc" => esc_html_x("Clear/Sunny", "Weather String", "bdthemes-element-pack"),
			"icon" => "113",
		],
		"116" => [
			"desc" => esc_html_x("Partly cloudy", "Weather String", "bdthemes-element-pack"),
			"icon" => "116",
		],
		"119" => [
			"desc" => esc_html_x("Cloudy", "Weather String", "bdthemes-element-pack"),
			"icon" => "119",
		],
		"122" => [
			"desc" => esc_html_x("Overcast", "Weather String", "bdthemes-element-pack"),
			"icon" => "122",
		],
		"143" => [
			"desc" => esc_html_x("Mist", "Weather String", "bdthemes-element-pack"),
			"icon" => "143",
		],
		"176" => [
			"desc" => esc_html_x("Patchy rain nearby", "Weather String", "bdthemes-element-pack"),
			"icon" => "176",
		],
		"179" => [
			"desc" => esc_html_x("Patchy snow nearby", "Weather String", "bdthemes-element-pack"),
			"icon" => "179",
		],
		"182" => [
			"desc" => esc_html_x("Patchy sleet nearby", "Weather String", "bdthemes-element-pack"),
			"icon" => "182",
		],
		"185" => [
			"desc" => esc_html_x("Patchy freezing drizzle nearby", "Weather String", "bdthemes-element-pack"),
			"icon" => "185",
		],
		"200" => [
			"desc" => esc_html_x("Thundery outbreaks nearby", "Weather String", "bdthemes-element-pack"),
			"icon" => "200",
		],
		"227" => [
			"desc" => esc_html_x("Blowing snow", "Weather String", "bdthemes-element-pack"),
			"icon" => "227",
		],
		"230" => [
			"desc" => esc_html_x("Blizzard", "Weather String", "bdthemes-element-pack"),
			"icon" => "230",
		],
		"248" => [
			"desc" => esc_html_x("Fog", "Weather String", "bdthemes-element-pack"),
			"icon" => "248",
		],
		"260" => [
			"desc" => esc_html_x("Freezing fog", "Weather String", "bdthemes-element-pack"),
			"icon" => "260",
		],
		"263" => [
			"desc" => esc_html_x("Patchy light drizzle", "Weather String", "bdthemes-element-pack"),
			"icon" => "263",
		],
		"266" => [
			"desc" => esc_html_x("Light drizzle", "Weather String", "bdthemes-element-pack"),
			"icon" => "266",
		],
		"281" => [
			"desc" => esc_html_x("Freezing drizzle", "Weather String", "bdthemes-element-pack"),
			"icon" => "281",
		],
		"284" => [
			"desc" => esc_html_x("Heavy freezing drizzle", "Weather String", "bdthemes-element-pack"),
			"icon" => "284",
		],
		"293" => [
			"desc" => esc_html_x("Patchy light rain", "Weather String", "bdthemes-element-pack"),
			"icon" => "293",
		],
		"296" => [
			"desc" => esc_html_x("Light rain", "Weather String", "bdthemes-element-pack"),
			"icon" => "296",
		],
		"299" => [
			"desc" => esc_html_x("Moderate rain at times", "Weather String", "bdthemes-element-pack"),
			"icon" => "299",
		],
		"302" => [
			"desc" => esc_html_x("Moderate rain", "Weather String", "bdthemes-element-pack"),
			"icon" => "302",
		],
		"305" => [
			"desc" => esc_html_x("Heavy rain at times", "Weather String", "bdthemes-element-pack"),
			"icon" => "305",
		],
		"308" => [
			"desc" => esc_html_x("Heavy rain", "Weather String", "bdthemes-element-pack"),
			"icon" => "308",
		],
		"311" => [
			"desc" => esc_html_x("Light freezing rain", "Weather String", "bdthemes-element-pack"),
			"icon" => "311",
		],
		"314" => [
			"desc" => esc_html_x("Moderate or heavy freezing rain", "Weather String", "bdthemes-element-pack"),
			"icon" => "314",
		],
		"317" => [
			"desc" => esc_html_x("Light sleet", "Weather String", "bdthemes-element-pack"),
			"icon" => "317",
		],
		"320" => [
			"desc" => esc_html_x("Moderate or heavy sleet", "Weather String", "bdthemes-element-pack"),
			"icon" => "320",
		],
		"323" => [
			"desc" => esc_html_x("Patchy light snow", "Weather String", "bdthemes-element-pack"),
			"icon" => "323",
		],
		"326" => [
			"desc" => esc_html_x("Light snow", "Weather String", "bdthemes-element-pack"),
			"icon" => "326",
		],
		"329" => [
			"desc" => esc_html_x("Patchy moderate snow", "Weather String", "bdthemes-element-pack"),
			"icon" => "329",
		],
		"332" => [
			"desc" => esc_html_x("Moderate snow", "Weather String", "bdthemes-element-pack"),
			"icon" => "332",
		],
		"335" => [
			"desc" => esc_html_x("Patchy heavy snow", "Weather String", "bdthemes-element-pack"),
			"icon" => "335",
		],
		"338" => [
			"desc" => esc_html_x("Heavy snow", "Weather String", "bdthemes-element-pack"),
			"icon" => "338",
		],
		"350" => [
			"desc" => esc_html_x("Ice pellets", "Weather String", "bdthemes-element-pack"),
			"icon" => "350",
		],
		"353" => [
			"desc" => esc_html_x("Light rain shower", "Weather String", "bdthemes-element-pack"),
			"icon" => "353",
		],
		"356" => [
			"desc" => esc_html_x("Moderate or heavy rain shower", "Weather String", "bdthemes-element-pack"),
			"icon" => "356",
		],
		"359" => [
			"desc" => esc_html_x("Torrential rain shower", "Weather String", "bdthemes-element-pack"),
			"icon" => "359",
		],
		"362" => [
			"desc" => esc_html_x("Light sleet showers", "Weather String", "bdthemes-element-pack"),
			"icon" => "362",
		],
		"365" => [
			"desc" => esc_html_x("Moderate or heavy sleet showers", "Weather String", "bdthemes-element-pack"),
			"icon" => "365",
		],
		"368" => [
			"desc" => esc_html_x("Light snow showers", "Weather String", "bdthemes-element-pack"),
			"icon" => "368",
		],
		"371" => [
			"desc" => esc_html_x("Moderate or heavy snow showers", "Weather String", "bdthemes-element-pack"),
			"icon" => "371",
		],
		"374" => [
			"desc" => esc_html_x("Light showers of ice pellets", "Weather String", "bdthemes-element-pack"),
			"icon" => "374",
		],
		"377" => [
			"desc" => esc_html_x("Moderate or heavy showers of ice pellets", "Weather String", "bdthemes-element-pack"),
			"icon" => "377",
		],
		"386" => [
			"desc" => esc_html_x("Patchy light rain with thunder", "Weather String", "bdthemes-element-pack"),
			"icon" => "386",
		],
		"389" => [
			"desc" => esc_html_x("Moderate or heavy rain with thunder", "Weather String", "bdthemes-element-pack"),
			"icon" => "389",
		],
		"392" => [
			"desc" => esc_html_x("Patchy light snow with thunder", "Weather String", "bdthemes-element-pack"),
			"icon" => "392",
		],
		"395" => [
			"desc" => esc_html_x("Moderate or heavy snow with thunder", "Weather String", "bdthemes-element-pack"),
			"icon" => "395",
		],
	]);

	if (!$code) {
		return $codes;
	}

	$code_key = (string) $code;

	if (!isset($codes[$code_key])) {
		return false;
	}

	if ($condition && isset($codes[$code_key][$condition])) {
		return $codes[$code_key][$condition];
	}

	return $codes[$code_key];
}

function element_pack_open_weather_code($code = null, $condition = null) {

	$codes = apply_filters('element-pack/weather/codes', [
		"01d" => [
			"desc" => esc_html_x("Clear/Sunny", "Weather String", "bdthemes-element-pack"),
			"icon" => "113",
		],
		"02d" => [
			"desc" => esc_html_x("Partly cloudy", "Weather String", "bdthemes-element-pack"),
			"icon" => "116",
		],
		"03d" => [
			"desc" => esc_html_x("Partly cloudy", "Weather String", "bdthemes-element-pack"),
			"icon" => "116",
		],

		"10n" => [
			"desc" => esc_html_x("Partly cloudy", "Weather String", "bdthemes-element-pack"),
			"icon" => "116",
		],
		"04d" => [
			"desc" => esc_html_x("Overcast", "Weather String", "bdthemes-element-pack"),
			"icon" => "122",
		],
		"04n" => [
			"desc" => esc_html_x("Mist", "Weather String", "bdthemes-element-pack"),
			"icon" => "143",
		],
		"50n" => [
			"desc" => esc_html_x("Mist", "Weather String", "bdthemes-element-pack"),
			"icon" => "143",
		],
		"11d" => [
			"desc" => esc_html_x("Thundery outbreaks nearby", "Weather String", "bdthemes-element-pack"),
			"icon" => "200",
		],
		"50d" => [
			"desc" => esc_html_x("Freezing fog", "Weather String", "bdthemes-element-pack"),
			"icon" => "260",
		],
		"09d" => [
			"desc" => esc_html_x("Moderate or heavy rain shower", "Weather String", "bdthemes-element-pack"),
			"icon" => "356",
		],
		"10d" => [
			"desc" => esc_html_x("Moderate or heavy rain with thunder", "Weather String", "bdthemes-element-pack"),
			"icon" => "389",
		],
		"13d" => [
			"desc" => esc_html_x("Moderate or heavy snow with thunder", "Weather String", "bdthemes-element-pack"),
			"icon" => "395",
		],
	]);

	if (!$code) {
		return $codes;
	}

	$code_key = (string) $code;

	if (!isset($codes[$code_key])) {
		return false;
	}

	if ($condition && isset($codes[$code_key][$condition])) {
		return $codes[$code_key][$condition];
	}

	return $codes[$code_key];
}

function element_pack_wind_code($degree) {

	$direction = '';

	if (($degree >= 0 && $degree <= 33.75) or ($degree > 348.75 && $degree <= 360)) {
		$direction = esc_html_x('north', 'Weather String', 'bdthemes-element-pack');
	} else if ($degree > 33.75 && $degree <= 78.75) {
		$direction = esc_html_x('north-east', 'Weather String', 'bdthemes-element-pack');
	} else if ($degree > 78.75 && $degree <= 123.75) {
		$direction = esc_html_x('east', 'Weather String', 'bdthemes-element-pack');
	} else if ($degree > 123.75 && $degree <= 168.75) {
		$direction = esc_html_x('south-east', 'Weather String', 'bdthemes-element-pack');
	} else if ($degree > 168.75 && $degree <= 213.75) {
		$direction = esc_html_x('south', 'Weather String', 'bdthemes-element-pack');
	} else if ($degree > 213.75 && $degree <= 258.75) {
		$direction = esc_html_x('south-west', 'Weather String', 'bdthemes-element-pack');
	} else if ($degree > 258.75 && $degree <= 303.75) {
		$direction = esc_html_x('west', 'Weather String', 'bdthemes-element-pack');
	} else if ($degree > 303.75 && $degree <= 348.75) {
		$direction = esc_html_x('north-west', 'Weather String', 'bdthemes-element-pack');
	}

	return $direction;
}

/**
 * @param array CSV file data
 * @param string $delimiter
 * @param false $header
 *
 * @return string
 */
function element_pack_parse_csv($csv, $delimiter = ';', $header = true) {

	if (!is_string($csv)) {
		return '';
	}

	if (!function_exists('str_getcsv')) {
		return $csv;
	}

	$html    = '';
	$rows    = explode(PHP_EOL, $csv);
	$headRow = 1;

	foreach ($rows as $row) {

		if ($headRow == 1 and $header) {
			$html .= '<thead><tr>';
		} else {
			$html .= '<tr>';
		}

		foreach (str_getcsv($row, $delimiter) as $cell) {

			$cell = trim($cell);

			$html .= $header
				? '<th>' . $cell . '</th>'
				: '<td>' . $cell . '</td>';
		}

		if ($headRow == 1 and $header) {
			$html .= '</tr></thead><tbody>';
		} else {
			$html .= '</tr>';
		}

		$headRow++;
		$header = false;
	}

	return '<table>' . $html . '</tbody></table>';
}

/**
 * String to ID maker for any title to relavent id
 *
 * @param  [type] string any title or string
 *
 * @return [type]         [description]
 */
function element_pack_string_id($string) {
	//Lower case everything
	$string = strtolower($string);
	//Make alphanumeric (removes all other characters)
	$string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
	//Clean up multiple dashes or whitespaces
	$string = preg_replace("/[\s-]+/", " ", $string);
	//Convert whitespaces and underscore to dash
	$string = preg_replace("/[\s_]/", "-", $string);

	//finally return here
	return $string;
}

/**
 * Ninja form array creator for get all form as
 * @return array [description]
 */
function element_pack_ninja_forms_options() {

	if (class_exists('Ninja_Forms') and function_exists('Ninja_Forms')) {
		$ninja_forms = Ninja_Forms()->form()->get_forms();
		if (!empty($ninja_forms) && !is_wp_error($ninja_forms)) {
			$form_options = ['0' => esc_html__('Select Form', 'bdthemes-element-pack')];
			foreach ($ninja_forms as $form) {
				$form_options[$form->get_id()] = $form->get_setting('title');
			}
		}
	} else {
		$form_options = ['0' => esc_html__('Form Not Found!', 'bdthemes-element-pack')];
	}

	return $form_options;
}

function element_pack_fluent_forms_options() { {

		$options = array();

		if (defined('FLUENTFORM')) {
			global $wpdb;

			$result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}fluentform_forms");

			if ($result) {
				$options[0] = esc_html__('Select Form', 'bdthemes-element-pack');
				foreach ($result as $form) {
					$options[$form->id] = $form->title;
				}
			} else {
				$options[0] = esc_html__('Form Not Found!', 'bdthemes-element-pack');
			}
		}

		return $options;
	}
}

/**
 * [element_pack_everest_forms_options description]
 * @return [type] [description]
 */
function element_pack_everest_forms_options() {
	$everest_form = array();
	$ev_form      = get_posts('post_type="everest_form"&numberposts=-1');
	if ($ev_form) {
		foreach ($ev_form as $evform) {
			$everest_form[$evform->ID] = $evform->post_title;
		}
	} else {
		$everest_form[0] = esc_html__('Form Not Found!', 'bdthemes-element-pack');
	}

	return $everest_form;
}

/**
 * [element_pack_formidable_forms_options description]
 * @return [type] [description]
 */
function element_pack_formidable_forms_options() {
	if (class_exists('FrmForm')) {
		$options = array();

		$forms = FrmForm::get_published_forms(array(), 999, 'exclude');
		if (count($forms)) {
			$i = 0;
			foreach ($forms as $form) {
				if (0 === $i) {
					$options[0] = esc_html__('Select Form', 'bdthemes-element-pack');
				}
				$options[$form->id] = $form->name;
				$i++;
			}
		}
	} else {
		$options = ['0' => esc_html__('Form Not Found!', 'bdthemes-element-pack')];
	}

	return $options;
}

/**
 * [element_pack_forminator_forms_options description]
 * @return [type] [description]
 */
function element_pack_forminator_forms_options() {
	$forminator_form = array();
	$fnr_form        = get_posts('post_type="forminator_forms"&numberposts=-1');
	if ($fnr_form) {
		foreach ($fnr_form as $fnrform) {
			$forminator_form[$fnrform->ID] = $fnrform->post_title;
		}
	} else {
		$forminator_form[0] = esc_html__('Form Not Found!', 'bdthemes-element-pack');
	}

	return $forminator_form;
}

/**
 * [element_pack_we_forms_options description]
 * @return [type] [description]
 */
function element_pack_we_forms_options() {

	if (class_exists('WeForms')) {
		$we_forms = get_posts([
			'post_type'      => 'wpuf_contact_form',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		]);
		if (!empty($we_forms) && !is_wp_error($we_forms)) {
			$form_options = ['0' => esc_html__('Select Form', 'bdthemes-element-pack')];

			foreach ($we_forms as $form) {
				$form_options[$form->ID] = $form->post_title;
			}
		}
	} else {
		$form_options = ['0' => esc_html__('Form Not Found!', 'bdthemes-element-pack')];
	}

	return $form_options;
}

/**
 * [element_pack_caldera_forms_options description]
 * @return [type] [description]
 */
function element_pack_caldera_forms_options() {

	if (class_exists('Caldera_Forms')) {
		$caldera_forms = Caldera_Forms_Forms::get_forms(true, true);
		$form_options  = ['0' => esc_html__('Select Form', 'bdthemes-element-pack')];
		$form          = [];
		if (!empty($caldera_forms) && !is_wp_error($caldera_forms)) {
			foreach ($caldera_forms as $form) {
				if (isset($form['ID']) and isset($form['name'])) {
					$form_options[$form['ID']] = $form['name'];
				}
			}
		}
	} else {
		$form_options = ['0' => esc_html__('Form Not Found!', 'bdthemes-element-pack')];
	}

	return $form_options;
}

/**
 * [element_pack_quform_options description]
 * @return [type] [description]
 */
function element_pack_quform_options() {

	if (class_exists('Quform')) {
		$quform       = Quform::getService('repository');
		$quform       = $quform->formsToSelectArray();
		$form_options = ['0' => esc_html__('Select Form', 'bdthemes-element-pack')];
		if (!empty($quform) && !is_wp_error($quform)) {
			foreach ($quform as $id => $name) {
				$form_options[esc_attr($id)] = esc_html($name);
			}
		}
	} else {
		$form_options = ['0' => esc_html__('Form Not Found!', 'bdthemes-element-pack')];
	}

	return $form_options;
}

/**
 * [element_pack_gravity_forms_options description]
 * @return [type] [description]
 */
function element_pack_gravity_forms_options() {

	if (class_exists('GFCommon')) {
		$contact_forms = RGFormsModel::get_forms(null, 'title');
		$form_options  = ['0' => esc_html__('Select Form', 'bdthemes-element-pack')];
		if (!empty($contact_forms) && !is_wp_error($contact_forms)) {
			foreach ($contact_forms as $form) {
				$form_options[$form->id] = $form->title;
			}
		}
	} else {
		$form_options = ['0' => esc_html__('Form Not Found!', 'bdthemes-element-pack')];
	}

	return $form_options;
}

/**
 * [element_pack_give_forms_options description]
 * @return [type] [description]
 */
function element_pack_give_forms_options() {
	$give_form = ['0' => esc_html__('Select Form', 'bdthemes-element-pack')];
	$gwp_form  = get_posts('post_type="give_forms"&numberposts=-1');
	if ($gwp_form) {
		foreach ($gwp_form as $gwpform) {
			$give_form[$gwpform->ID] = $gwpform->post_title;
		}
	} else {
		$give_form[0] = esc_html__('Form Not Found!', 'bdthemes-element-pack');
	}

	return $give_form;
}

/**
 * [element_pack_charitable_forms_options description]
 * @return [type] [description]
 */
function element_pack_charitable_forms_options() {
	$charitable_form = array('all' => esc_html__('All', 'bdthemes-element-pack'));
	$charity_form    = get_posts('post_type="campaign"&numberposts=-1');
	if ($charity_form) {
		foreach ($charity_form as $charityform) {
			$charitable_form[$charityform->ID] = $charityform->post_title;
		}
	} else {
		$charitable_form[0] = esc_html__('Form Not Found!', 'bdthemes-element-pack');
	}

	return $charitable_form;
}

/**
 * [element_pack_rev_slider_options description]
 * @return [type] [description]
 */
function element_pack_rev_slider_options() {

	if (class_exists('RevSlider')) {
		$slider             = new RevSlider();
		$revolution_sliders = $slider->getArrSliders();
		$slider_options     = ['0' => esc_html__('Select Slider', 'bdthemes-element-pack')];
		if (!empty($revolution_sliders) && !is_wp_error($revolution_sliders)) {
			foreach ($revolution_sliders as $revolution_slider) {
				$alias                    = $revolution_slider->getAlias();
				$title                    = $revolution_slider->getTitle();
				$slider_options[$alias] = $title;
			}
		}
	} else {
		$slider_options = ['0' => esc_html__('No Slider Found!', 'bdthemes-element-pack')];
	}

	return $slider_options;
}

/**
 * [element_pack_download_file_list description]
 * @return [type] [description]
 */
function element_pack_download_file_list() {

	$output = [];
	if (defined('DLM_VERSION')) {
		$search_query = (!empty($_POST['dlm_search']) ? esc_attr($_POST['dlm_search']) : '');
		$limit        = 100;
		$filters      = array('post_status' => 'publish');
		if (!empty($search_query)) {
			$filters['s'] = $search_query;
		}
		$downloads = download_monitor()->service('download_repository')->retrieve($filters, $limit);
		foreach ($downloads as $download) {
			$output[absint($download->get_id())] = $download->get_title() . ' (' . $download->get_version()->get_filename() . ')';
		}
	}

	return $output;
}

/**
 * [element_pack_dashboard_link description]
 * @param  string $suffix [description]
 * @return [type]         [description]
 */
function element_pack_dashboard_link($suffix = '#welcome') {
	return add_query_arg(['page' => 'element_pack_options' . $suffix], admin_url('admin.php'));
}

/**
 * [element_pack_currency_symbol description]
 * @param  string $currency [description]
 * @return [type]           [description]
 */
function element_pack_currency_symbol($currency = '') {
	switch (strtoupper($currency)) {
		case 'AED':
			$currency_symbol = 'د.إ';
			break;
		case 'AUD':
		case 'CAD':
		case 'CLP':
		case 'COP':
		case 'HKD':
		case 'MXN':
		case 'NZD':
		case 'SGD':
		case 'USD':
			$currency_symbol = '&#36;';
			break;
		case 'BDT':
			$currency_symbol = '&#2547;&nbsp;';
			break;
		case 'BGN':
			$currency_symbol = '&#1083;&#1074;.';
			break;
		case 'BIF':
			$currency_symbol = 'FBu';
			break;
		case 'BRL':
			$currency_symbol = '&#82;&#36;';
			break;
		case 'CHF':
			$currency_symbol = '&#67;&#72;&#70;';
			break;
		case 'CNY':
		case 'JPY':
		case 'RMB':
			$currency_symbol = '&yen;';
			break;
		case 'CZK':
			$currency_symbol = '&#75;&#269;';
			break;
		case 'DJF':
			$currency_symbol = 'Fdj';
			break;
		case 'DKK':
			$currency_symbol = 'DKK';
			break;
		case 'DOP':
			$currency_symbol = 'RD&#36;';
			break;
		case 'EGP':
			$currency_symbol = 'EGP';
			break;
		case 'ETB':
			$currency_symbol = 'ETB';
			break;
		case 'EUR':
			$currency_symbol = '&euro;';
			break;
		case 'GBP':
			$currency_symbol = '&pound;';
			break;
		case 'GHS':
			$currency_symbol = 'GH₵';
			break;
		case 'HRK':
			$currency_symbol = 'Kn';
			break;
		case 'HUF':
			$currency_symbol = '&#70;&#116;';
			break;
		case 'IDR':
			$currency_symbol = 'Rp';
			break;
		case 'ILS':
			$currency_symbol = '&#8362;';
			break;
		case 'INR':
			$currency_symbol = 'Rs.';
			break;
		case 'ISK':
			$currency_symbol = 'Kr.';
			break;
		case 'IRR':
			$currency_symbol = '﷼';
			break;
		case 'KES':
			$currency_symbol = 'KSh';
			break;
		case 'KIP':
			$currency_symbol = '&#8365;';
			break;
		case 'KRW':
			$currency_symbol = '&#8361;';
			break;
		case 'MYR':
			$currency_symbol = '&#82;&#77;';
			break;
		case 'NGN':
			$currency_symbol = '&#8358;';
			break;
		case 'NOK':
			$currency_symbol = '&#107;&#114;';
			break;
		case 'NPR':
			$currency_symbol = 'Rs.';
			break;
		case 'PHP':
			$currency_symbol = '&#8369;';
			break;
		case 'PKR':
			$currency_symbol = 'Rs.';
			break;
		case 'PLN':
			$currency_symbol = '&#122;&#322;';
			break;
		case 'PYG':
			$currency_symbol = '&#8370;';
			break;
		case 'RON':
			$currency_symbol = 'lei';
			break;
		case 'RUB':
			$currency_symbol = '&#1088;&#1091;&#1073;.';
			break;
		case 'RWF':
			$currency_symbol = 'FRw';
			break;
		case 'SEK':
			$currency_symbol = '&#107;&#114;';
			break;
		case 'THB':
			$currency_symbol = '&#3647;';
			break;
		case 'TND':
			$currency_symbol = 'DT';
			break;
		case 'TRY':
			$currency_symbol = '&#8378;';
			break;
		case 'TWD':
			$currency_symbol = '&#78;&#84;&#36;';
			break;
		case 'TZS':
			$currency_symbol = 'TSh';
			break;
		case 'UAH':
			$currency_symbol = '&#8372;';
			break;
		case 'UGX':
			$currency_symbol = 'USh';
			break;
		case 'VND':
			$currency_symbol = '&#8363;';
			break;
		case 'XAF':
			$currency_symbol = 'CFA';
			break;
		case 'ZAR':
			$currency_symbol = '&#82;';
			break;
		default:
			$currency_symbol = '';
			break;
	}

	return apply_filters('element_pack_currency_symbol', $currency_symbol, $currency);
}

/**
 * [element_pack_money_format description]
 * @param  [type] $value [description]
 * @return [type]        [description]
 */
function element_pack_money_format($value) {

	if (empty($value)) {
		return;
	}

	$value = sprintf('%01.2f', $value);

	return $value;
}

/**
 * @param int $limit default limit is 25 word
 * @param bool $strip_shortcode if you want to strip shortcode from excert text
 * @param string $trail trail string default is ...
 *
 * @return string return custom limited excerpt text
 */
function element_pack_custom_excerpt($limit = 25, $strip_shortcode = false, $trail = '') {

	$output = get_the_content();

	if ($limit) {
		$output = wp_trim_words($output, $limit, $trail);
	}

	if ($strip_shortcode) {
		$output = strip_shortcodes($output);
	}

	return wpautop($output);
}

/**
 * [element_pack_total_comment description]
 * @param  string $comment_type [description]
 * @return [type]               [description]
 */
function element_pack_total_comment($comment_type = 'total') {
	$comments_count = wp_count_comments();

	if ($comment_type == 'moderated') {
		$output = $comments_count->moderated;
	} elseif ($comment_type == 'approved') {
		$output = $comments_count->approved;
	} elseif ($comment_type == 'spam') {
		$output = $comments_count->spam;
	} elseif ($comment_type == 'trash') {
		$output = $comments_count->trash;
	} elseif ($comment_type = 'total') {
		$output = $comments_count->total_comments;
	}

	return $output;
}

/**
 * [element_pack_total_post description]
 * @param  string $custom_post_type [description]
 * @param  string $post_status      [description]
 * @return [type]                   [description]
 */
function element_pack_total_post($custom_post_type = 'post', $post_status = 'publish') {
	$post_count = wp_count_posts($custom_post_type);

	if ($post_status == 'publish') {
		$output = $post_count->publish;
	} elseif ($post_status == 'draft') {
		$output = $post_count->draft;
	} elseif ($post_status == 'trash') {
		$output = $post_count->trash;
	}

	return $output;
}

/**
 * [element_pack_total_user description]
 * @param  string $user_type [description]
 * @return [type]            [description]
 */
function element_pack_total_user($user_type = 'bdt-all-users') {
	$user_count = count_users();

	if ($user_type == 'bdt-all-users') {
		$output = $user_count['total_users'];
	} else {
		if (!empty($user_count['avail_roles'][$user_type])) {
			$output = $user_count['avail_roles'][$user_type];
		} else {
			$output = 0;
		}
	}

	return $output;
}

/**
 * [element_pack_user_roles description]
 * @return [type] [description]
 */
function element_pack_user_roles() {
	global $wp_roles;

	if (!isset($wp_roles)) {
		$wp_roles = new WP_Roles();
	}
	$all_roles      = $wp_roles->roles;
	$editable_roles = apply_filters('editable_roles', $all_roles);

	$users = ['bdt-all-users' => 'All Users'];

	foreach ($editable_roles as $er => $role) {
		$users[$er] = $role['name'];
	}

	return $users;
}

/**
 * [element_pack_strip_emoji description]
 * @param  [type] $text [description]
 * @return [type]       [description]
 */
function element_pack_strip_emoji($text) {
	// four byte utf8: 11110www 10xxxxxx 10yyyyyy 10zzzzzz
	return preg_replace('/[\xF0-\xF7][\x80-\xBF]{3}/', '', $text);
}

/**
 * [element_pack_twitter_process_links description]
 * @param  [type] $tweet [description]
 * @return [type]        [description]
 */
function element_pack_twitter_process_links($tweet) {

	// Is the Tweet a ReTweet - then grab the full text of the original Tweet
	if (isset($tweet->retweeted_status)) {
		// Split it so indices count correctly for @mentions etc.
		$rt_section = current(explode(':', $tweet->text));
		$text       = $rt_section . ': ';
		// Get Text
		$text .= $tweet->retweeted_status->text;
	} else {
		// Not a retweet - get Tweet
		$text = $tweet->text;
	}

	// NEW Link Creation from clickable items in the text
	$text = preg_replace('/((http)+(s)?:\/\/[^<>\s]+)/i', '<a href="$0" target="_blank" rel="nofollow">$0</a>', $text);
	// Clickable Twitter names
	$text = preg_replace('/[@]+([A-Za-z0-9-_]+)/', '<a href="http://twitter.com/$1" target="_blank" rel="nofollow">@$1</a>', $text);
	// Clickable Twitter hash tags
	$text = preg_replace('/[#]+([A-Za-z0-9-_]+)/', '<a href="http://twitter.com/search?q=%23$1" target="_blank" rel="nofollow">$0</a>', $text);

	// END TWEET CONTENT REGEX
	return $text;
}

/**
 * [element_pack_time_diff description]
 * @param  [type] $from [description]
 * @param  string $to   [description]
 * @return [type]       [description]
 */
function element_pack_time_diff($from, $to = '') {
	$diff    = human_time_diff($from, $to);
	$replace = array(
		' hour'    => 'h',
		' hours'   => 'h',
		' day'     => 'd',
		' days'    => 'd',
		' minute'  => 'm',
		' minutes' => 'm',
		' second'  => 's',
		' seconds' => 's',
	);

	return strtr($diff, $replace);
}

/**
 * [element_pack_post_time_diff description]
 * @param  string $format [description]
 * @return [type]         [description]
 */
function element_pack_post_time_diff($format = '') {
	$displayAgo = esc_html_x('ago', 'leading space is required', 'bdthemes-element-pack');

	if ($format == 'short') {
		$output = element_pack_time_diff(strtotime(get_the_date()), current_time('timestamp'));
	} else {
		$output = human_time_diff(strtotime(get_the_date()), current_time('timestamp'));
	}

	$output = $output . ' ' . $displayAgo;

	return $output;
}

function element_pack_hide_on_class($selectors) {
	$element_hide_on = '';
	if (!empty($selectors)) {
		foreach ($selectors as $element) {

			if ($element == 'desktop') {
				$element_hide_on .= ' bdt-desktop';
			}
			if ($element == 'tablet') {
				$element_hide_on .= ' bdt-tablet';
			}
			if ($element == 'mobile') {
				$element_hide_on .= ' bdt-mobile';
			}
		}
	}
	return $element_hide_on;
}

if (!function_exists('element_pack_array_except')) {
	/**
	 * Provide access to optional objects.
	 *
	 * @param  mixed  $value
	 * @param  callable|null  $callback
	 * @return mixed
	 */
	function element_pack_array_except($array, $keys) {

		$original = &$array;

		$keys = (array) $keys;

		if (count($keys) === 0) {
			return;
		}

		foreach ($keys as $key) {
			// if the exact key exists in the top-level, remove it
			if (array_key_exists($key, $array)) {
				unset($array[$key]);

				continue;
			}

			$parts = explode('.', $key);

			// clean up before each pass
			$array = &$original;

			while (count($parts) > 1) {
				$part = array_shift($parts);

				if (isset($array[$part]) && is_array($array[$part])) {
					$array = &$array[$part];
				} else {
					continue 2;
				}
			}

			unset($array[array_shift($parts)]);
		}

		return $array;
	}
}

/**
 * License Validation
 */
if (!function_exists('bdt_license_validation')) {
	function bdt_license_validation() {

		$license_key = get_option(Element_Pack_Base::get_lic_key_param('element_pack_license_key'));

		if (isset($license_key) && !empty($license_key)) {
			return true;
		} else {
			return false;
		}
		return false;
	}
}

/**
 * Crypto Currency API
 */
if (!function_exists('ep_crypto')) {
	function ep_crypto() {
		$currency = isset($_GET['currency']) ? $_GET['currency'] : 'usd';
		$param    = [
			'page'     => isset($_GET['page']) && is_int($_GET['page']) ? $_GET['page'] : 1,
			'per_page' => isset($_GET['per_page']) && $_GET['per_page'] ? $_GET['per_page'] : 100,
			'order'    => isset($_GET['order']) ? $_GET['order'] : 'market_cap_desc',
		];
		//$data = $client->coins()->getMarkets($currency, $param); // stoped api sdk here

		$ids = !empty($_GET['ids']) && 'all' !== $_GET['ids'] ? 'ids=' . $_GET['ids'] . '&' : '';

		// $market_url = 'https://api.coingecko.com/api/v3/coins/markets?' . $ids . 'vs_currency=' . $currency . '&order=' . $param['order'] . '&per_page=' . $param['per_page'] . '&page=' . $param['page'] . '&sparkline=true&price_change_percentage=1h%2C24h%2C7d';
		$market_url = 'https://api.coingecko.com/api/v3/coins/markets?' . $ids . 'vs_currency=' . $currency . '&order=' . $param['order'] . '&page=' . $param['page'] . '&sparkline=true&price_change_percentage=1h%2C24h%2C7d';

		/**
		 * decoding data
		 */
		$url  = wp_remote_request($market_url);
		$body = wp_remote_retrieve_body($url);
		$data = json_decode($body);

		// $data = json_decode($data);
		/**
		 * sending response
		 */
		// now have to brek the data

		if (isset($data->status->error_code) && !empty($data->status->error_code)) {
			$data = get_transient('ep-bitcoin');
			// $dataset = array(
			//     "apiErrors" => true,
			//     "data" => isset($data->status->error_message) ? $data->status->error_message : 'API Errors.'
			// );
			// echo json_encode($dataset);
			// wp_die();
		}

		if (count($data) > 0) {
			set_transient('ep-bitcoin', $data, apply_filters('element-pack/bitcoin/cached-time', HOUR_IN_SECONDS));
		}

		$resultData = [];
		$count      = 0;
		foreach ($data as $row) {
			$count++;
			// if ($count >= $param['per_page']) {
			//     return;
			// }

			$resultData[] = [
				'market_cap_rank'             => $row->market_cap_rank,
				'id'                          => $row->id,
				'current_price'               => $row->current_price,
				'price_change_percentage_1h'  => $row->price_change_percentage_1h_in_currency,
				'price_change_percentage_24h' => $row->price_change_percentage_24h_in_currency,
				'price_change_percentage_7d'  => $row->price_change_percentage_7d_in_currency,
				//'total_supply' => $row->total_supply,
				'market_cap'                  => $row->market_cap,
				'total_volume'                => $row->total_volume,
				'circulating_supply'          => $row->circulating_supply,
				'image'                       => $row->image,
				'symbol'                      => $row->symbol,
				//'last_seven_days_changes' => getChartData($row->id, $currency)
				'last_seven_days_changes'     => $row->sparkline_in_7d->price,
			];
		}

		$dataset = array(
			"totalrecords" => count($data),
			//"data" => $data
			"data"         => $resultData,
		);

		echo json_encode($dataset);
		wp_die();
	}
}

if (!function_exists('ep_crypto_data')) {
	function ep_crypto_data() {
		try {
			/**
			 * initialization
			 */
			//$client = new CoinGeckoClient();
			/**
			 * setting param
			 */
			$currency = isset($_GET['currency']) ? $_GET['currency'] : 'usd';
			$param    = [
				'page'     => isset($_GET['page']) && is_int($_GET['page']) ? $_GET['page'] : 1,
				'per_page' => isset($_GET['per_page']) && $_GET['per_page'] ? $_GET['per_page'] : 250,
				'order'    => isset($_GET['order']) ? $_GET['order'] : 'market_cap_desc',
			];
			// $data = $client->coins()->getMarkets($currency, $param); // this is previous call here

			$ids = !empty($_GET['ids']) && 'all' !== $_GET['ids'] ? 'ids=' . $_GET['ids'] . '&' : '';

			// $market_url = 'https://api.coingecko.com/api/v3/coins/markets?' . $ids . 'vs_currency=' . $currency . '&order=' . $param['order'] . '&per_page=' . $param['per_page'] . '&page=' . $param['page'] . '&sparkline=true&price_change_percentage=1h%2C24h%2C7d';
			$market_url = 'https://api.coingecko.com/api/v3/coins/markets?' . $ids . 'vs_currency=' . $currency . '&order=' . $param['order'] . '&page=' . $param['page'] . '&sparkline=true&price_change_percentage=1h%2C24h%2C7d';

			$url  = wp_remote_request($market_url);
			$body = wp_remote_retrieve_body($url);
			$data = json_decode($body);

			if (isset($data->status->error_code) && !empty($data->status->error_code)) {
				// echo $data->status->error_code;
				$data = get_transient('ep-bitcoin');
				// $dataset = array(
				//     "apiErrors" => true,
				//     "data" => isset($data->status->error_message) ? $data->status->error_message : 'API Errors.'
				// );
				// echo json_encode($dataset);
				// wp_die();
				// print_r($data->status->error_code);
				// echo 'API Errors - ' . $market_url;
			}

			$resultArray = [];

			if (count($data) > 0) {
				set_transient('ep-bitcoin', $data, apply_filters('element-pack/bitcoin/cached-time', HOUR_IN_SECONDS));
				foreach ($data as $row) {
					$resultArray[] = [
						'id'            => $row->id,
						'current_price' => $row->current_price,
					];
				}
			}

			echo count($resultArray) > 0 ? json_encode($resultArray) : null;
			wp_die();
		} catch (Exception $e) {
			echo $e->getMessage();
			wp_die();
		}
	}
}


if (!function_exists('element_pack_render_mini_cart_item')) {
	function element_pack_render_mini_cart_item($cart_item_key, $cart_item) {

		$_product           = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
		$is_product_visible = ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key));

		if (!$is_product_visible) {
			return;
		}

		$product_id     = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
		$product_price  = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
		$item_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
	?>
		<div class="bdt-mini-cart-product-item bdt-flex bdt-flex-middle <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">

			<div class="bdt-mini-cart-product-thumbnail">
				<?php
				$thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);

				if (!$item_permalink) {
					echo wp_kses_post($thumbnail);
				} else {
					printf('<a href="%s">%s</a>', esc_url($item_permalink), wp_kses_post($thumbnail));
				}
				?>
			</div>

			<div class="bdt-margin-small-left">
				<div class="bdt-mini-cart-product-name bdt-margin-small-bottom">
					<?php
					if (!$item_permalink) {
						echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;');
					} else {
						echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($item_permalink), $_product->get_name()), $cart_item, $cart_item_key));
					}

					do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);

					// Meta data.
					echo wc_get_formatted_cart_item_data($cart_item); // PHPCS: XSS ok.
					?>
				</div>

				<div class="bdt-mini-cart-product-price" data-title="<?php esc_attr_e('Price', 'bdthemes-element-pack'); ?>">
					<?php echo apply_filters('woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf('%s &times; %s', $cart_item['quantity'], $product_price) . '</span>', $cart_item, $cart_item_key); ?>
				</div>
			</div>

			<div class="bdt-mini-cart-product-remove">
				<?php
				echo apply_filters('woocommerce_cart_item_remove_link', sprintf(
					'<a href="%s" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s"><svg width="14" height="14" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg" data-svg="close-icon"><line fill="none" stroke="#000" stroke-width="1.1" x1="1" y1="1" x2="13" y2="13"></line><line fill="none" stroke="#000" stroke-width="1.1" x1="13" y1="1" x2="1" y2="13"></line></svg></a>',
					esc_url(wc_get_cart_remove_url($cart_item_key)),
					__('Remove this item', 'bdthemes-element-pack'),
					esc_attr($product_id),
					esc_attr($cart_item_key),
					esc_attr($_product->get_sku())
				), $cart_item_key);
				?>
			</div>
		</div>
<?php
	}
	function element_pack_ajax_load_query_args() {
		$postSettings = $_POST['settings'];


		// setmeta args
		$args = [
			'posts_per_page'   => isset($_POST['per_page']) ? $_POST['per_page'] : 3,
			'post_status'      => 'publish',
			'suppress_filters' => false,
			'orderby'          => $postSettings['posts_orderby'],
			'order'            => $postSettings['posts_order'],
			'ignore_sticky_posts' => true,
			'paged'            => isset($_POST['paged']) ? $_POST['paged'] : 1,
			'offset'           => isset($_POST['offset']) ? $_POST['offset'] : 0
		];

		/**
		 * wc product args
		 */

		if (class_exists('WooCommerce')) {
			$product_visibility_term_ids = wc_get_product_visibility_term_ids();
			if (isset($postSettings['hide_out_stock']) && ('yes' === $postSettings['hide_out_stock'])) {
				$args['tax_query'][] = [
					[
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_term_ids['outofstock'],
						'operator' => 'NOT IN',
					],
				];
			}
		}

		/**
		 * set feature image
		 *
		 */
		if (isset($postSettings['posts_only_with_featured_image']) && $postSettings['posts_only_with_featured_image'] === 'yes') {
			$args['meta_query'] = [
				[
					'key'     => '_thumbnail_id',
					'compare' => 'EXISTS'
				]
			];
		}

		/**
		 * set date query
		 */

		$selected_date = isset($postSettings['posts_select_date']) ? $postSettings['posts_select_date'] : '';

		if (!empty($selected_date)) {
			$date_query = [];

			switch ($selected_date) {
				case 'today':
					$date_query['after'] = '-1 day';
					break;

				case 'week':
					$date_query['after'] = '-1 week';
					break;

				case 'month':
					$date_query['after'] = '-1 month';
					break;

				case 'quarter':
					$date_query['after'] = '-3 month';
					break;

				case 'year':
					$date_query['after'] = '-1 year';
					break;

				case 'exact':
					$after_date = $postSettings['posts_date_after'];
					if (!empty($after_date)) {
						$date_query['after'] = $after_date;
					}

					$before_date = $postSettings['posts_date_before'];
					if (!empty($before_date)) {
						$date_query['before'] = $before_date;
					}
					$date_query['inclusive'] = true;
					break;
			}

			if (!empty($date_query)) {
				$args['date_query'] = $date_query;
			}
		}

		$exclude_by    = isset($posts_exclude_by) ? $posts_exclude_by : [];
		$include_by    = isset($posts_include_by) ? $posts_include_by : [];
		$include_users = [];
		$exclude_users = [];
		/**
		 * ignore sticky post
		 */
		if (!empty($exclude_by) && $postSettings['posts_source'] === 'post' && $postSettings['posts_ignore_sticky_posts'] === 'yes') {
			$args['ignore_sticky_posts'] = true;
			if (in_array('current_post', $exclude_by)) {
				$args['post__not_in'] = [get_the_ID()];
			}
		}

		/**
		 * set post type
		 */

		if ($postSettings['posts_source'] === 'manual_selection') {
			/**
			 * Set Including Manually
			 */
			$selected_ids      = $postSettings['posts_selected_ids'];
			$selected_ids      = wp_parse_id_list($selected_ids);
			$args['post_type'] = 'any';
			if (!empty($selected_ids)) {
				$args['post__in'] = $selected_ids;
			}
			$args['ignore_sticky_posts'] = 1;
		} elseif ('current_query' === $postSettings['posts_source']) {
			/**
			 * Make Current Query
			 */
			$args = $GLOBALS['wp_query']->query_vars;
			// unset($args['paged']);
			$args['paged'] = isset($_POST['paged']) ? $_POST['paged'] : 1;
			$args['offset'] = isset($_POST['offset']) ? $_POST['offset'] : 0;

			$args = apply_filters('element_pack/query/get_query_args/current_query', $args);
		} elseif ('_related_post_type' === $postSettings['posts_source']) {
			/**
			 * Set Related Query
			 */
			$post_id           = get_queried_object_id();
			$related_post_id   = is_singular() && (0 !== $post_id) ? $post_id : null;
			$args['post_type'] = get_post_type($related_post_id);

			if (in_array('authors', $include_by)) {
				$args['author__in'] = wp_parse_id_list($postSettings['posts_include_author_ids']);
			} else {
				$args['author__in'] = get_post_field('post_author', $related_post_id);
			}

			if (in_array('authors', $exclude_by)) {
				$args['author__not_in'] = wp_parse_id_list($postSettings['posts_exclude_author_ids']);
			}

			if (in_array('current_post', $exclude_by)) {
				$args['post__not_in'] = [get_the_ID()];
			}

			$args['ignore_sticky_posts'] = 1;
			$args                        = apply_filters('element_pack/query/get_query_args/related_query', $args);
		} else {
			$args['post_type'] = $postSettings['posts_source'];
			$current_post      = [];


			/**
			 * Set Taxonomy && Set Authors
			 */
			$include_terms = [];
			$exclude_terms = [];
			$terms_query   = [];
			if (!empty($exclude_by)) {
				if (in_array('authors', $exclude_by)) {
					$exclude_users = wp_parse_id_list($postSettings['posts_exclude_author_ids']);
					$include_users = array_diff($include_users, $exclude_users);
				}
				if (!empty($exclude_users)) {
					$args['author__not_in'] = $exclude_users;;
				}
				if (in_array('current_post', $exclude_by) && is_singular()) {
					$current_post[] = get_the_ID();
				}
				if (in_array('manual_selection', $exclude_by)) {
					$exclude_ids          = $postSettings['posts_exclude_ids'];
					$args['post__not_in'] = array_merge($current_post, wp_parse_id_list($exclude_ids));
				}
				if (in_array('terms', $exclude_by)) {
					$exclude_terms = wp_parse_id_list($postSettings['posts_exclude_term_ids']);
					$include_terms = array_diff($include_terms, $exclude_terms);
				}
				if (!empty($exclude_terms)) {
					$tax_terms_map = element_pack_ajax_load_map_group_control_query($exclude_terms);
					foreach ($tax_terms_map as $tax => $terms) {
						$terms_query[] = [
							'taxonomy' => $tax,
							'field'    => 'term_id',
							'terms'    => $terms,
							'operator' => 'NOT IN',
						];
					}
				}
			}

			if (!empty($include_terms)) {

				if (in_array('authors', $include_by)) {
					$include_users = wp_parse_id_list($postSettings['posts_include_author_ids']);
				}
				if (!empty($include_users)) {
					$args['author__in'] = $include_users;
				}
				if (in_array('terms', $include_by)) {
					$include_terms = wp_parse_id_list($postSettings['posts_include_term_ids']);
				}
				$tax_terms_map = element_pack_ajax_load_map_group_control_query($include_terms);
				foreach ($tax_terms_map as $tax => $terms) {
					$terms_query[] = [
						'taxonomy' => $tax,
						'field'    => 'term_id',
						'terms'    => $terms,
						'operator' => 'IN',
					];
				}
			}
			if (!empty($terms_query)) {
				$args['tax_query']             = $terms_query;
				$args['tax_query']['relation'] = 'AND';
			}
		}

		$ajaxposts = new \WP_Query($args);
		return $ajaxposts;
	}

	function element_pack_ajax_load_map_group_control_query($term_ids = []) {
		$terms         = get_terms(
			[
				'term_taxonomy_id' => $term_ids,
				'hide_empty'       => false,
			]
		);
		$tax_terms_map = [];
		foreach ($terms as $term) {
			$taxonomy                     = $term->taxonomy;
			$tax_terms_map[$taxonomy][] = $term->term_id;
		}
		return $tax_terms_map;
	}
}
