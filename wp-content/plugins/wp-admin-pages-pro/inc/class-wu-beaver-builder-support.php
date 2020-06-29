<?php
/**
 * Beaver Builder Support
 *
 * Adds support to Beaver Builder, if it is enabled
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
 * Implements Beaver Builder Support
 *
 * @since 1.4.0 Now implements WU_Admin_Page_Content_Source_Page_Builder.
 * @since 1.1.0
 */
class WU_Admin_Pages_BB_Support extends WU_Admin_Page_Content_Source_Page_Builder {

	/**
	 * Initializes the Beaver Builder Support
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public function init() {

		add_filter('fl_builder_content_elements_data', array($this, 'remove_layouts_templates_bb'));

	} // end init;

	/**
	 * Filter and Remove layout templates of Beaver Builder if wuapc display option has disable.
	 *
	 * @param array $data Data of filter bb.
	 * @return array
	 * @since
	 */
	public function remove_layouts_templates_bb($data) {

		/**
		 * Check if we should change the visibility of things.
		 */
		if (WU_Admin_Pages()->should_display_admin_menu()) {

			return $data;

		} // end if;

		$ids_to_exclude = $this->get_posts_ids_by_meta_key('bb_template_id');

		if ($ids_to_exclude) {

			foreach ($data['template'] as $key => &$value) {

				if (isset($value['postId']) && in_array(strval($value['postId']), $ids_to_exclude) ||isset($value['id']) && in_array(strval($value['id']), $ids_to_exclude) ) {

					unset($data['template'][$key]);

				} // end if;

			} // end foreach;

		} // end if;

		return $data;

	} // end remove_layouts_templates_bb;

	/**
	 * Sets the configurations we need in order for the Brizy page builder integration to work.
	 *
	 * @since 1.4.0
	 * @return array
	 */
	public function config() {

		return array(
			'id'             => 'beaver_builder',
			'post_type'      => 'fl-builder-template',
			'title'          => __('Beaver Builder', 'wu-apc'),
			'selector_title' => __('Beaver Builder Template', 'wu-apc'),
			'field'          => 'bb_template_id',
			'read_more_link' => 'https://kb.wpbeaverbuilder.com/article/88-create-and-save-a-custom-layout-template',
			'supports_modal' => array('edit_link'),
			'edit_link'      => site_url('?post_type=fl-builder-template&p=TEMPLATE_ID&fl_builder'),
			'see_all_link'   => admin_url('edit.php?post_type=fl-builder-template&fl-builder-template-type=layout'),
			'add_new_link'   => admin_url('edit.php?post_type=fl-builder-template&page=fl-builder-add-new&fl-builder-template-type=layout'),
		);

	} // end config;

	/**
	 * Add Beaver Builder as a content type option
	 *
	 * @since  1.1.0
	 * @param  array $options The list of content type options supported.
	 * @return array
	 */
	public static function add_option($options) {

		$options['beaver_builder'] = array(
			'label'  => __('Use Beaver Builder Template', 'wu-apc'),
			'icon'   => 'dashicons dashicons-schedule',
			'active' => class_exists('FLBuilder'),
			'title'  => class_exists('FLBuilder') ? '' : __('You need Beaver Builder Standard, PRO or Agency Version active to use this feature', 'wu-apc'),
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
			'post_type'   => 'fl-builder-template'
		);

		return get_posts($args);

	} // end fetch_templates;

	/**
	 * Renders the Beaver Builder layout
	 *
	 * @since 1.1.0
	 * @param WU_Admin_Page $admin_page The current admin page being displayed.
	 * @return void
	 */
	public function display_content($admin_page) {

		global $post;

		if ($admin_page->content_type != 'beaver_builder') {
			return;
		} // end if;

		// Set the global post
		if (!is_object($post)) {
			$post = new stdClass();
		} // end if;

		$post->ID = $admin_page->bb_template_id;

		FLBuilder::register_layout_styles_scripts();

		wp_enqueue_style('jquery-bxslider');
		wp_enqueue_script('jquery-bxslider');

		?>

    <div id="wu-apc-bb-content">

		<?php

		WP_Ultimo_APC()->is_network_active() && switch_to_blog( get_current_site()->blog_id );

			/**
			 * Check if Beaver Theme is activated. Init proprieties used in shortcodes of Beaver Theme
			 * @since 1.7.4
			 */
			if (class_exists( 'FLThemeBuilderLoader' )) {

				FLPageData::init_properties();

			} // end if;

			$content = do_shortcode('[fl_builder_insert_layout id="' . $admin_page->bb_template_id . '" type="fl-builder-template"]');

		WP_Ultimo_APC()->is_network_active() && restore_current_blog();

		echo wu_apc_process_page_content($content);

		?>

    </div>

		<?php

	}  // end display_content;

} // end class WU_Admin_Pages_BB_Support;

/**
 * Conditionally load the support, if Beaver is Active
 *
 * @since 1.1.0
 * @return void
 */
function wu_admin_pages_add_bb_support() {

	if (class_exists('FLBuilder')) {

		new WU_Admin_Pages_BB_Support();

	} else {

		add_filter('wu_admin_pages_get_editor_options', array('WU_Admin_Pages_BB_Support', 'add_option'));

	}// end if;

} // end wu_admin_pages_add_bb_support;

/**
 * Load the Beaver Builder Support
 *
 * @since 1.1.0
 */
add_action('plugins_loaded', 'wu_admin_pages_add_bb_support', 11);
