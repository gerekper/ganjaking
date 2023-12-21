<?php
/*
* Single Blog Canvas
*/

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

\Elementor\Plugin::$instance->frontend->add_body_class('elementor-template-canvas');

get_header();

// Keep the following line after `wp_head()` call, to ensure it's not overridden by another templates.
echo \Elementor\Utils::get_meta_viewport('canvas');

/**
 * Before canvas page template content.
 *
 * Fires before the content of Elementor canvas page template.
 *
 * @since 1.0.0
 */
do_action('elementor/page_templates/canvas/before_content');

if (!\Elementor\Plugin::$instance->preview->is_preview_mode()) {
	do_action('happyaddons_theme_builder_render');
} else {
	the_content();
}

/**
 * After canvas page template content.
 *
 * Fires after the content of Elementor canvas page template.
 *
 * @since 1.0.0
 */
do_action('elementor/page_templates/canvas/after_content');

get_footer();
