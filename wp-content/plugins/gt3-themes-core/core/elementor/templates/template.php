<?php
defined('ABSPATH') OR exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<?php if(!current_theme_supports('title-tag')) : ?>
		<title><?php echo wp_get_document_title(); ?></title>
	<?php endif; ?>
	<?php wp_head(); ?>
	<?php

	// Keep the following line after `wp_head()` call, to ensure it's not overridden by another templates.
	echo \Elementor\Utils::get_meta_viewport('canvas');
	?>
</head>
<body <?php body_class(); ?>>
<?php
ob_start();
?>
<style>
	#elementor-add-new-section {
		position: absolute;
		width: 100%;
		top: 100%;
		transform: translateX(-50%);
		left: 50%;
	}
</style>
<?php
Elementor\Modules\PageTemplates\Module::body_open();
/**
 * Before canvas page template content.
 *
 * Fires before the content of Elementor canvas page template.
 *
 * @since 1.0.0
 */
//do_action('elementor/page_templates/canvas/before_content');

\Elementor\Plugin::$instance->modules_manager->get_modules('page-templates')->print_content();

/**
 * After canvas page template content.
 *
 * Fires after the content of Elementor canvas page template.
 *
 * @since 1.0.0
 */
//do_action('elementor/page_templates/canvas/after_content');
wp_footer();
?>
</body>
</html>
