<?php
/**
 * Gutenberg Support
 *
 * Adds support to Gutenberg, if it is enabled
 *
 * @author      WP Admin Pages PRO
 * @category    Admin
 * @package     WP_Admin_Pages_PRO/Admin_Pages/Beaver_Builder
 * @version     1.0.1
 */

if (!defined('ABSPATH')) {
	exit;
} // end if;

/**
 * Implements Gutenberg Support
 *
 * @since 1.4.0 Now implements WU_Admin_Page_Content_Source_Page_Builder.
 * @since 1.1.0
 */
class WU_Admin_Pages_Gutenberg_Support extends WU_Admin_Page_Content_Source_Page_Builder {

	/**
	 * Initializes the Gutenberg Support
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public function init() {

		add_action('init', array($this, 'register_admin_init_files'));

		add_action('admin_print_styles', array($this, 'guttenberg_styles_without_check_is_admin'));

	} // end init;

	/**
	 * Register the necessdary scripts to run gutenberg page, scripts and styles
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public function register_admin_init_files() {

		// unregister_post_type( 'wuapc_gutenberg_cpt' );
		if (!post_type_exists( 'wuapc_gutenberg_cpt' )) :
			$this->create_post_type();
	  endif;

	} // end register_admin_init_files;

	/**
	 * Creates new custom post type if no exists
	 * // 'show_in_rest' => false,   CHANGE TO TRUE to display gutenberg editor
	 *
	 * @return void
	 * @since
	 */
	public function create_post_type() {
		register_post_type( 'wuapc_gutenberg_cpt',
		 array(
			 'labels'             => array(
				 'name'          => __( 'Gutenberg Template' ),
				 'singular_name' => __( 'Gutenberg Templates' )
			 ),
			 'show_in_menu'       => false,
			 'public'             => false,
			 'has_archive'        => true,
			 'publicly_queryable' => false,
			 'query_var'          => true,
			 'capability_type'    => 'post',
			 'show_in_rest'       => true,
			 'hierarchical'       => false,
			 'menu_position'      => null,
			 'supports'           => array('title', 'editor'),
		 )
		);
	} // end create_post_type;

	/**
	 * Guttenberg styles without check is_admin() = refer wp_common_block_scripts_and_styles()
	 *
	 * @return void
	 * @since
	 */
	public function guttenberg_styles_without_check_is_admin() {
		wp_enqueue_style( 'wp-block-library' );
		wp_enqueue_style( 'wp-block-library-theme' );
	} // end guttenberg_styles_without_check_is_admin;

	/**
	 * Sets the configurations we need in order for the Gutenberg integration to work.
	 *
	 * @since 1.4.0
	 * @return array
	 */
	public function config() {

		return array(
			'id'             => 'gutenberg',
			'post_type'      => 'wuapc_gutenberg_cpt',
			'title'          => __('Gutenberg Template', 'wu-apc'),
			'selector_title' => __('Gutenberg Templates', 'wu-apc'),
			'field'          => 'gutenberg_template_id',
			'read_more_link' => 'https://wordpress.org/gutenberg/handbook/',
			'supports_modal' => array(),
			'edit_link'      => admin_url('post.php?post=TEMPLATE_ID&action=edit'),
			'see_all_link'   => admin_url('edit.php?post_type=wuapc_gutenberg_cpt'),
			'add_new_link'   => admin_url('post-new.php?post_type=wuapc_gutenberg_cpt'),
		);

	} // end config;

	/**
	 * Add Gutenberg as a content type option
	 *
	 * @since  1.1.0
	 * @param  array $options The list of content type options supported.
	 * @return array
	 */
	public static function add_option($options) {

		global $wp_version;

		$message = '';
		$active  = true;

		if (!version_compare( $wp_version, '5.0', '>=' ) && !function_exists( 'register_block_type' )) {

			$message = __('You need WordPress version 5.0 to use this feature', 'wu-apc');
			$active  = false;

		} elseif (is_plugin_active( 'classic-editor/classic-editor.php' )) {

			$message = __('Disable Classic Editor plugin to active this feature.', 'wu-apc');
			$active  = false;

		} // end if;

		$options['gutenberg'] = array(
			'label'  => __('Use Gutenberg Template', 'wu-apc'),
			'icon'   => 'dashicons dashicons-schedule',
			'active' => $active,
			'title'  => $message,
		);

		return $options;

	} // end add_option;

	/**
	 * Returns the templates available for this particular page builder.
	 *
	 * @since 1.4.0
	 * @return array
	 */
	public function fetch_templates() {

		$args = array(
			'numberposts' => -1,
			'post_type'   => 'wuapc_gutenberg_cpt'
		);

		return get_posts($args);

	} // end fetch_templates;

	/**
	 * Renders the Gutenberg layout
	 *
	 * @since 1.1.0
	 * @param WU_Admin_Page $admin_page The current admin page being displayed.
	 * @return void
	 */
	public function display_content($admin_page) {

		global $post;

		if ($admin_page->content_type != 'gutenberg') {
			return;
		} // end if;

		// Set the global post
		if (!is_object($post)) {
			$post = new stdClass();
		} // end if;

		?>

    <div id="wu-apc-gutenberg-content">

		<?php

		is_multisite() && switch_to_blog( get_current_site()->blog_id );

		$gutenberg_selected_post = get_post($admin_page->gutenberg_template_id);
        // echo $admin_page;
		echo $gutenberg_selected_post->post_content;

		is_multisite() && restore_current_blog();

		?>

    </div>

		<?php

	}  // end display_content;

} // end class WU_Admin_Pages_Gutenberg_Support;

/**
 * Conditionally load the support, if Beaver is Active
 *
 * @since 1.1.0
 * @return void
 */
function wu_admin_pages_add_gutenberg_support() {

	global $wp_version;

	if (version_compare( $wp_version, '5.0', '>=')) {

		new WU_Admin_Pages_Gutenberg_Support();

	} else {

		add_filter('wu_admin_pages_get_editor_options', array('WU_Admin_Pages_Gutenberg_Support', 'add_option'));

	} // end if;

} // end wu_admin_pages_add_gutenberg_support;

/**
 * Load the Beaver Builder Support
 *
 * @since 1.1.0
 */
add_action('plugins_loaded', 'wu_admin_pages_add_gutenberg_support', 11);
