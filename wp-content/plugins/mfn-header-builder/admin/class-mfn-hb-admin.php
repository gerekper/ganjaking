<?php
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Mfn_HB_Admin
{

	/**
	 * Mfn_HB_Admin constructor
	 */

	public function __construct()
	{

		// It runs after the basic admin panel menu structure is in place.
		add_action('admin_menu', array( $this, 'init' ), 13);

		// Allows you to create custom handlers for your own custom AJAX requests.
		add_action('wp_ajax_mfn_save_header', array( $this, '_ajax_save_header' ));
	}

	/**
	 * Add admin page & enqueue styles
	 */

	public function init()
	{
		$title = __('Header Builder', 'mfn-opts');

		$this->page = add_submenu_page(
			'betheme',
			$title,
			$title,
			'edit_theme_options',
			'be-header',
			array( $this, 'template' )
		);

		// Fires when styles are printed for a specific admin page based on $hook_suffix.
		add_action('admin_print_styles-'. $this->page, array( $this, 'enqueue' ));
	}

	/**
	 * Status template
	 */

	public function template()
	{
		include_once dirname(__FILE__) .'/templates/header-builder.php';
	}

	/**
	 * Enqueue styles and scripts
	 */

	public function enqueue()
	{
		// colorpicker

		wp_enqueue_style('wp-color-picker');

		// upload button

		wp_enqueue_media();

		// theme specific imports

		wp_enqueue_style('mfn-header-font', 'https://fonts.googleapis.com/css?family=Poppins:300,400,400i,500,700');
		wp_enqueue_style('mfn-opts-icons', get_theme_file_uri('/fonts/mfn-icons.css'), array(), MFN_HB_VERSION);

		// builder specific imports

		wp_enqueue_style('mfn-header-styles', plugins_url('dist/css/mfn-header-builder.css', __FILE__), array(), MFN_HB_VERSION);
		wp_enqueue_style('mfn-header-component-styles', plugins_url('dist/css/app.css', __FILE__), array(), MFN_HB_VERSION);

		wp_register_script('mfn-header-builder', plugins_url('dist/js/app.js', __FILE__), array( 'wp-color-picker', 'jquery' ), MFN_HB_VERSION, true);

		$builder = get_site_option('mfn_header_builder');

		$ajax_attr = array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('mfn-header'),
			'menu_list' => mfna_menu(),
			'fonts_list' => mfn_fonts(),
			'builder' => $builder,
		);

		wp_localize_script('mfn-header-builder', 'mfn_ajax', $ajax_attr);
		wp_enqueue_script('mfn-header-builder');
	}

	/**
	 * Ajax save header
	 */

	public function _ajax_save_header()
	{
		check_ajax_referer('mfn-header', 'nonce');

		$builder = stripslashes($_POST['builder']);

		// update_site_option

		update_site_option('mfn_header_builder', $builder);

		// get_site_option

		$new = get_site_option('mfn_header_builder');

		// ajax response error

		if (! $new) {
			wp_send_json_error();
		}

		$response = array(
			'success' => true,
			'data' => 'saved',
		);

		wp_send_json($response);

		wp_die();
	}
}
