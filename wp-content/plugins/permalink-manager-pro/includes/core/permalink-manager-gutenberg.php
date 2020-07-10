<?php

/**
* Additional hooks for "Permalink Manager Pro"
*/
class Permalink_Manager_Gutenberg extends Permalink_Manager_Class {

	public function __construct() {
		add_action('enqueue_block_editor_assets', array($this, 'init'));
	}

	public function init() {
		global $current_screen;

		// Get displayed post type
		if(empty($current_screen->post_type)) { return; }
		$post_type = $current_screen->post_type;

		// Check if post type is disabled
		if(Permalink_Manager_Helper_Functions::is_disabled($post_type, 'post_type')) { return; }

		// Stop the hook (if needed)
		$show_uri_editor = apply_filters("permalink_manager_hide_uri_editor_post_{$post_type}", true);
		if(!$show_uri_editor) { return; }

		add_meta_box('permalink-manager', __('Permalink Manager', 'permalink-manager'), array($this, 'meta_box'), '', 'side', 'high' );
	}

	public function pm_gutenberg_scripts() {
		wp_enqueue_script('permalink-manager-gutenberg', PERMALINK_MANAGER_URL . '/out/permalink-manager-gutenberg.js', array('wp-blocks', 'wp-element', 'wp-components', 'wp-i18n'), PERMALINK_MANAGER_VERSION, true);
	}

	public function meta_box($post) {
		global $permalink_manager_uris;

		if(empty($post->ID)) {
			return '';
		}

		// Display URI Editor
		echo Permalink_Manager_Admin_Functions::display_uri_box($post, true);
	}

}

?>
