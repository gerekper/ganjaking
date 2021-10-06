<?php
/**
 * Separator Support
 *
 * Adds support to Separator, if it is enabled
 *
 * @author      WP Admin Pages PRO
 * @category    Admin
 * @package     WP_Admin_Pages_PRO/Admin_Pages/Separator
 * @version     1.0.1
 */

if (!defined('ABSPATH')) {
	exit;
} // end if;

class WU_Admin_Pages_Separator_Support {

	/**
	 * Initializes the Separator Support
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public function __construct() {

		add_action('wu_admin_pages_editors', array($this, 'add_separator_fields'));

		add_action('wu_save_admin_page', array($this, 'save_separator_before_options'));

		add_action('wu_save_admin_page', array($this, 'save_separator_after_options'));

	}  // end __construct;

	/**
	 * Add the External_link template options to the supported meta fields of the admin page
	 *
	 * @since  1.1.0
	 * @param  array $meta_fields The list of current meta fields supported.
	 * @return array
	 */
	public function add_separator_fields($meta_fields) {

		$meta_fields[] = 'separator_before';
		$meta_fields[] = 'separator_after';

		return $meta_fields;

	} // end add_separator_fields;

	/**
	 * Save separator_before meta fields on save
	 *
	 * @since  1.1.0
	 * @param  WU_Admin_Page $admin_page The current admin page being edited and saved.
	 * @return void
	 */
	public function save_separator_before_options($admin_page) {

		if (isset($_POST['separator_before'])) {

			$admin_page->separator_before = $_POST['separator_before'];

			$admin_page->save();

		} // end if;

	} // end save_separator_before_options;

	/**
	 * Save separator_after meta fields on save
	 *
	 * @since  1.1.0
	 * @param  WU_Admin_Page $admin_page The current admin page being edited and saved.
	 * @return void
	 */
	public function save_separator_after_options($admin_page) {

		if (isset($_POST['separator_after'])) {

			$admin_page->separator_after = $_POST['separator_after'];

			$admin_page->save();

		} // end if;

	} // end save_separator_after_options;

}  // end class WU_Admin_Pages_Separator_Support;

/**
 * Conditionally load the support, if Separator is Active
 *
 * @since 1.1.0
 * @return void
 */
function wu_admin_pages_add_separator_support() {

	new WU_Admin_Pages_Separator_Support();

} // end wu_admin_pages_add_separator_support;


/**
 * Load the Separator Support
 *
 * @since 1.1.0
 */
add_action('plugins_loaded', 'wu_admin_pages_add_separator_support', 11);
