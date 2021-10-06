<?php
/**
 * Elementor Support
 *
 * Adds support to Elementor, if it is enabled
 *
 * @author      WP Admin Pages PRO
 * @category    Admin
 * @package     WP_Admin_Pages_PRO/Admin_Pages/Elementor
 * @version     1.0.1
 */

if (!defined('ABSPATH')) {
	exit;
} // end if;

/**
 * Implements Elementor PRO Support
 *
 * @since 1.4.0 Now implements WU_Admin_Page_Content_Source_Page_Builder.
 * @since 1.3.0
 */
class WU_Admin_Pages_Elementor_Support extends WU_Admin_Page_Content_Source_Page_Builder {

	/**
	 * Initializes the Elementor Support
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public function init() {

		add_action('admin_init', array($this, 'register_admin_init_files'));

	} // end init;

	/**
	 * Sets the configurations we need in order for the Brizy page builder integration to work.
	 *
	 * @since 1.4.0
	 * @return array
	 */
	public function config() {

		return array(
			'id'             => 'elementor',
			'post_type'      => 'elementor_library',
			'title'          => __('Elementor PRO', 'wu-apc'),
			'selector_title' => __('Elementor PRO Template', 'wu-apc'),
			'field'          => 'elementor_template_id',
			'read_more_link' => 'https://docs.elementor.com/article/359-creating-a-single-page-template-with-elementor-pro',
			'supports_modal' => array('edit_link'),
			'edit_link'      => admin_url('post.php?post=TEMPLATE_ID&action=elementor'),
			'see_all_link'   => admin_url('edit.php?post_type=elementor_library'),
			'add_new_link'   => admin_url('post-new.php?post_type=elementor_library'),
		);

	} // end config;

	/**
	 * Register the necessdary scripts to run elementor page, scripts and styles
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public function register_admin_init_files() {

		if (class_exists('ElementorPro\Plugin')) {

			add_action('wu_page_enqueue_scripts', array($this, 'load_scripts'));

			add_action('admin_footer', array($this, 'load_footer_scripts'));

		} else {

			add_filter('wu_admin_pages_get_editor_options', array('WU_Admin_Pages_Elementor_Support', 'add_option'));

 		} // end if;

	} // end register_admin_init_files;

	/**
	 * Loads scripts footer from elementor frontend.
	 *
	 * Has header/footer/widget-template - enqueue all style/scripts/fonts.  (elementor\includes\preview.php::251)
	 *
	 * @since 1.7.4
	 *
	 * @return void
	 */
	public function load_footer_scripts() {

		$elementor_frontend = Elementor\Plugin::instance()->frontend;

		$elementor_frontend->wp_footer();

	} // end load_footer_scripts;

	/**
	 * Actually loads the scripts.
	 *
	 * This was necessary because Elementor was doing funky stuff with how it build the pages.
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public function load_scripts() {

		$elementor_frontend = Elementor\Plugin::instance()->frontend;

		WP_Ultimo_APC()->is_network_active() && switch_to_blog( get_current_site()->blog_id );

		$elementor_frontend->register_scripts();
		$elementor_frontend->register_styles();
		$elementor_frontend->enqueue_scripts();
		$elementor_frontend->enqueue_styles();

		// Astra theme compability
		if (class_exists( 'Astra_Enqueue_Scripts')) {

			$astra_scripts = new Astra_Enqueue_Scripts();
			$astra_scripts::theme_assets();
			$astra_scripts->add_fonts();
			$astra_scripts->enqueue_scripts();

			$theme_css_data = apply_filters( 'astra_dynamic_theme_css', '' );
			wp_add_inline_style( 'astra-theme-css', $theme_css_data );

		}

		WP_Ultimo_APC()->is_network_active() && restore_current_blog();

	} // end load_scripts;

	/**
	 * Add Elementor as a content type option
	 *
	 * @since  1.1.0
	 * @param  array $options The list of content type options supported.
	 * @return array
	 */
	public static function add_option($options) {

		$options['elementor'] = array(
			'label'  => __('Use Elementor PRO Template', 'wu-apc'),
			'icon'   => 'dashicons dashicons-schedule',
			'active' => class_exists('ElementorPro\Plugin'),
			'title'  => class_exists('ElementorPro\Plugin') ? '' : __('You need Elementor PRO active to use this feature', 'wu-apc'),
		);

		return $options;

	} // end add_option;

	/**
	 * Get a list of WP_Post objects for every Elementor template
	 *
	 * @since  1.1.0
	 * @return array
	 */
	public function fetch_templates() {

		$args = array(
			'numberposts' => -1,
			'post_type'   => 'elementor_library'
		);

		return get_posts($args);

	} // end fetch_templates;

	/**
	 * Renders the Elementor layout
	 *
	 * @since 1.1.0
	 * @param WU_Admin_Page $admin_page The current admin page being displayed.
	 * @return void
	 */
	public function display_content($admin_page) {

		global $post;

		if ($admin_page->content_type != 'elementor') {
			return;
		} // end if;

		// Set the global post
		if (!is_object($post)) {
			$post = new stdClass();
		} // end if;

		?>

    <div id="wu-apc-elementor-content">

		<?php

		WP_Ultimo_APC()->is_network_active() && switch_to_blog( get_current_site()->blog_id );

		if (class_exists('ElementorPro\Plugin') ) {

			$content = do_shortcode('[elementor-template id="' . $admin_page->elementor_template_id . '"]');

			echo wu_apc_process_page_content($content);

		} else {

			?>

			<div class="wrap">
				<div class="notice notice-error"> 
					<p><?php _e('Elementor PRO needs to be available network-wide for this page to be displayed', 'wu-apc'); ?></p>
				</div>
			</div>

			<?php

		} // end if;

		WP_Ultimo_APC()->is_network_active() && restore_current_blog();

		?>

    </div>

		<?php
	} // end display_content;

}  // end class WU_Admin_Pages_Elementor_Support;

/**
 * Conditionally load the support, if Elementor is Active
 *
 * @since 1.1.0
 * @return void
 */
function wu_admin_pages_add_elementor_support() {

	new WU_Admin_Pages_Elementor_Support();

} // end wu_admin_pages_add_elementor_support;

/**
 * Load the elementor Support
 *
 * @since 1.1.0
 */
add_action('plugins_loaded', 'wu_admin_pages_add_elementor_support', 11);
