<?php
/**
 * Content Source Support
 *
 * Adds support to Content Sources, this is a generic class that Content Source implementations must inherit from.
 *
 * @author      WP Admin Pages PRO
 * @category    Admin
 * @package     WP_Admin_Pages_PRO/Admin_Pages/Content_Source
 * @version     1.0.1
 */

if (!defined('ABSPATH')) {
	exit;
} // end if;

/**
 * Parent class of Content Source support
 *
 * @since 1.4.0
 */
class WU_Admin_Page_Content_Source {

	/**
	 * Content Source configuration array.
	 *
	 * @since 1.4.0
	 * @var array
	 */
	public $config;

	/**
	 * Initiates the Content Source support class.
	 *
	 * @since 1.4.0
	 * @return void
	 */
	public function __construct() {

		$this->config = wp_parse_args($this->config(), $this->default_config());

		add_filter('wu_admin_pages_get_editor_options', array($this, 'add_option'));

		add_action('wu_admin_pages_editors', array($this, 'add_template_selector'));

		add_action('wu_admin_pages_display_content', array($this, 'display_content'));

		add_filter('wu_admin_page_meta_fields', array($this, 'add_meta_fields'));

		add_action('wu_save_admin_page', array($this, 'save_options'));

		add_action('wu_apc_localize_content_sources', array($this, 'localize_content_source'));

		$this->init();

	} // end __construct;

	/**
	 * Set the default configuration for all content sources.
	 *
	 * @since 1.4.0
	 * @return array
	 */
	public function default_config() {

		return array(
			'id'             => 'id',
			'title'          => __('Content Source', 'wu-apc'),
			'selector_title' => __('Content Source', 'wu-apc'),
		);

	} // end default_config;

	/**
	 * Allows the content source to declare it's configurations.
	 *
	 * @since 1.4.0
	 * @return array
	 */
	public function config() {

		return array();

	} // end config;

	/**
	 * Allows Content Sources to add other hooks
	 *
	 * @since 1.4.0
	 * @return void
	 */
	public function init() { } // end init;

	/**
	 * Allowes the content source to localize the main script with settings.
	 *
	 * @since 1.4.0
	 * @param WU_Admin_Page $admin_page The admin page object.
	 * @return void
	 */
	public function localize_content_source($admin_page) { } // end localize_content_source;

	/**
	 * Adds this Content Source as an option.
	 *
	 * @since  1.1.0
	 * @param  array $options List of the supported options.
	 * @return array
	 */
	public static function add_option($options) {

		return $options;

	} // end add_option;

	/**
	 * Allow the Content Sources to add additional meta fields to the Model.
	 *
	 * @since  1.1.0
	 * @param  array $meta_fields The list of current meta fields supported.
	 * @return array
	 */
	public function add_meta_fields($meta_fields) {

		return $meta_fields;

	}  // end add_meta_fields;

	/**
	 * Allows Content Sources to save options when a Admin Page is saved on the admin.
	 *
	 * @since 1.4.0
	 * @param WU_Admin_Page $admin_page The admin page object.
	 * @return void
	 */
	public function save_options($admin_page) { } // end save_options;

	/**
	 * Display the form to select different template options.
	 *
	 * @since 1.4.0
	 * @param WU_Admin_Page $admin_page The admin page object.
	 * @return void
	 */
	public function add_template_selector($admin_page) { } // end add_template_selector;

	/**
	 * Actually displays the content on the rendered page.
	 *
	 * @since 1.4.0
	 * @param WU_Admin_Page $admin_page The admin page object.
	 * @return void
	 */
	public function display_content($admin_page) { }  // end display_content;

	public function run_private_method($object, $method) {

		$reflector = new ReflectionObject($object);

		$method = $reflector->getMethod($method);

		$method->setAccessible(true);

		return $method->invoke($object);
		
	} // end run_private_method;

}  // end class WU_Admin_Page_Content_Source;
