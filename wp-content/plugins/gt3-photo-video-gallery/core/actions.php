<?php
if(!defined('ABSPATH')) {
	exit;
}

require_once __DIR__.'/actions/gt3_admin_mix_tabs_controls.php';
require_once __DIR__.'/actions/gt3pg_gallery_shortcode.php';
require_once __DIR__.'/actions/gt3_before_admin_panel_tabs_controls.php';
require_once __DIR__.'/actions/gt3pg_attachment_field_credit.php';
require_once __DIR__.'/actions/gt3pg_attachment_field_credit_save.php';
require_once __DIR__.'/actions/gt3pg_enqueue_scripts.php';
require_once __DIR__.'/actions/gt3pg_slug_filter_the_content.php';
require_once __DIR__.'/actions/gt3pg_wp_head.php';
require_once __DIR__.'/actions/image_size_names_choose.php';
require_once __DIR__.'/actions/gt3pg_before_render_thumb_type.php';

require_once(GT3PG_PLUGINPATH.'core/actions/render/file.php');
require_once(GT3PG_PLUGINPATH.'core/actions/render/lightbox.php');
require_once(GT3PG_PLUGINPATH.'core/actions/render/none.php');
require_once(GT3PG_PLUGINPATH.'core/actions/render/post.php');

add_action('wp_head', 'gt3pg_wp_head');

remove_shortcode('gallery');
add_shortcode('gallery', 'gt3pg_gallery_shortcode');

add_filter('the_content', 'gt3pg_slug_filter_the_content');

add_filter('attachment_fields_to_save', 'gt3pg_attachment_field_credit_save', 10, 2);

add_filter('attachment_fields_to_edit', 'gt3pg_attachment_field_credit', 10, 2);

add_action('wp_footer', function(){
	if(isset($GLOBALS['gt3pg_footer'])) {
		echo $GLOBALS['gt3pg_footer'];
	}
});

add_filter('block_categories', function($categories, $post){
	array_splice($categories, 3, 0, array(
		array(
			'slug'  => 'gt3-photo-video-gallery',
			'title' => __('GT3 Photo & Video Gallery', 'gt3pg'),
		)
	));

	return $categories;
}, 10, 2);



