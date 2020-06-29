<?php

/**
* Additional hooks for "Permalink Manager Pro"
*/
class Permalink_Manager_Gutenberg extends Permalink_Manager_Class {

	public function __construct() {
		add_action('enqueue_block_editor_assets', array($this, 'init'));
	}

	public function init() {
		// add_action('enqueue_block_editor_assets', array($this, 'pm_gutenberg_scripts'));
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
