<?php
/**
 * Pages About
 *
 * Handles the addition of the About Page
 *
 * @author      WPUltimo
 * @category    Admin
 * @package     WPUltimo/Pages
 * @version     0.0.1
 */

if (!defined('ABSPATH')) {
	exit;
} // end if;

/**
 * Created the Getting Started Page
 *
 * @since 1.4.0
 */
class WAPP_Page_Getting_Started extends WAPP_Page {

	/**
	 * Adds the scripts we'll need
	 *
	 * @since 1.4.0
	 * @return void
	 */
	public function register_scripts() {

		wp_enqueue_style('wu-apc', WP_Ultimo_APC()->get_asset('wu-admin-page-creator.min.css', 'css'), false);

	} // end register_scripts;

	/**
	 * Sets the output template for this particular page
	 *
	 * @since 1.8.2
	 * @return void
	 */
	public function output() {

		WP_Ultimo_APC()->render('meta/getting-started');

	} // end output;

} // end class WAPP_Page_Getting_Started;

// new WAPP_Page_Getting_Started(WP_Ultimo_APC()->is_network_active(), array(
// 'id'         => 'wp-admin-pages-pro-getting-started',
// 'type'       => 'submenu',
// 'parent'     => 'admin-pages',
// 'title'      => __('Getting Started', 'wp-ultimo'),
// 'menu_title' => __('Getting Started', 'wp-ultimo'),
// 'capability' => WP_Ultimo_APC()->is_network_active() ? 'manage_network' : 'manage_options',
// ));
