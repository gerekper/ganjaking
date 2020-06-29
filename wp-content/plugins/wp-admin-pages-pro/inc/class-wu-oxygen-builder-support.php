<?php
/**
 * Oxygen Builder Support
 *
 * Adds support to Oxygen Builder, if it is enabled
 *
 * @author      WP Admin Pages PRO
 * @category    Admin
 * @package     WP_Admin_Pages_PRO/Admin_Pages/Oxygen_Builder
 * @version     1.0.1
 */

if (!defined('ABSPATH')) {
	exit;
} // end if;

/**
 * Implements Oxygen Builder Support
 *
 * @since 1.4.0 Now implements WU_Admin_Page_Content_Source_Page_Builder.
 * @since 1.1.0
 */
class WU_Admin_Pages_Oxygen_Support extends WU_Admin_Page_Content_Source_Page_Builder {

	/**
	 * Initializes the Oxygen Support
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public function init() {

		add_action('admin_footer', array($this, 'display_svg'));

	} // end init;

	/**
	 * Sets the configurations we need in order for the Oxygen page builder integration to work.
	 *
	 * @since 1.4.0
	 * @return array
	 */
	public function config() {

		return array(
			'id'             => 'oxygen_builder',
			'post_type'      => 'ct_template',
			'title'          => __('Oxygen Builder', 'wu-apc'),
			'selector_title' => __('Oxygen Builder Template', 'wu-apc'),
			'field'          => 'oxygen_template_id',
			'read_more_link' => 'https://oxygenbuilder.com/learn/',
			'supports_modal' => array(),
			'edit_link'      => '',
			'see_all_link'   => admin_url('edit.php?post_type=ct_template'),
			'add_new_link'   => admin_url('post-new.php?post_type=ct_template'),
		);
	} // end config;

	/**
	 * Add Oxygen Builder as a content type option
	 *
	 * @since  1.1.0
	 * @param  array $options The list of content type options supported.
	 * @return array
	 */
	public static function add_option($options) {

		$options['oxygen_builder'] = array(
			'label'  => __('Use Oxygen Builder Template (Beta)', 'wu-apc'),
			'icon'   => 'dashicons dashicons-schedule',
			'active' => wapp_check_if_oxygen_is_available(),
			'title'  => wapp_check_if_oxygen_is_available() ? '' : __('You need Oxygen Builder active to use this feature', 'wu-apc'),
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
			'post_type'   => 'ct_template'
		);

		return get_posts($args);

	} // end fetch_templates;

	/**
	 * Display the SVG icons
	 *
	 * @since 1.5.0
	 * @return void
	 */
	public function display_svg() {

		WP_Ultimo_APC()->is_network_active() && switch_to_blog(get_current_site()->blog_id);

		$svg_sets = get_option('ct_svg_sets', array());

		foreach ($svg_sets as $set) {

			$svg = new SimpleXMLElement($set);

			// output only if it has valid defs and symbols
			if (isset($svg->defs) && isset($svg->defs->symbol)) {

				echo $set . "\n";

				// output specific icon widths for some icons based on viewBox parameter
				echo "<style class='ct_svg_sets'>";

				foreach ($svg->defs->symbol as $icon) {

					$icon 		  = (array) $icon;
					$attributes = $icon["@attributes"];
					$view_box 	= explode(" ", $attributes['viewBox']);

					if ($view_box[2] != $view_box[3]) {

						echo ".ct-".esc_attr($attributes['id']) . "{";
						echo "width:" . ($view_box[2] / $view_box[3]) . "em";	
						echo "}";

					} // end if;

				} // end foreach;

				echo "</style>";

			} // end if;

		} // end foreach;

		WP_Ultimo_APC()->is_network_active() && restore_current_blog();

	} // end display_svg;

	public function render_oxygen_shortcodes($shortcodes) {

		$count = 0; // safety switch

		while (strpos($shortcodes, '[oxygen ') !== false && $count < 9) {
			$count++;
			$shortcodes = preg_replace_callback('/(\")(url|src|map_address|alt|background-image)(\":\"[^\"]*)\[oxygen ([^\]]*)\]([^\"\[\s]*)/i', 'ct_obfuscate_oxy_url', $shortcodes);
		}
		
		$content = do_shortcode($shortcodes);

		return $content;

	} // end render_oxygen_shortcodes;

	/**
	 * Renders the Oxygen Builder layout
	 *
	 * @since 1.1.0
	 * @param WU_Admin_Page $admin_page The current admin page being displayed.
	 * @return void
	 */
	public function display_content($admin_page) {

		global $post;

		if ($admin_page->content_type != 'oxygen_builder') {
			return;
		} // end if;

		// Set the global post
		if (!is_object($post)) {
			$post = new stdClass();
		} // end if;

		$post->ID            = $admin_page->oxygen_template_id;
		$_REQUEST['post_id'] = $admin_page->oxygen_template_id;
		?>

	<div id="wu-apc-oxygen-content">

		<?php

		WP_Ultimo_APC()->is_network_active() && switch_to_blog(get_current_site()->blog_id);

		$this->enqueue_assets_before($admin_page);

		// echo ct_template_output();

		$shortcodes = get_post_meta($admin_page->oxygen_template_id, 'ct_builder_shortcodes', true);

		$content = $this->render_oxygen_shortcodes($shortcodes);

		$content = wu_apc_process_page_content($content);

		echo $content;

		$this->enqueue_assets_after();

		WP_Ultimo_APC()->is_network_active() && restore_current_blog();

		?>

  </div>

		<?php

	}  // end display_content;

	/**
	 * Enqueue assets before
	 *
	 * @since 1.5.0
	 * @param WP_Admin_Page $admin_page Object admin page.
	 * @return void
	 */
	public function enqueue_assets_before($admin_page) {

		global $wpdb;

		add_web_font();
		$universal_css_url = get_option('oxygen_vsb_universal_css_url');
		$universal_css_url = add_query_arg('cache', get_option('oxygen_vsb_last_save_time'), $universal_css_url);
		wp_enqueue_style('oxygen-universal-styles', $universal_css_url);
		wp_enqueue_style("font-awesome");

		wp_enqueue_style('oxygen', CT_FW_URI . '/oxygen.css', array(), CT_VERSION );

		// CSS PER TEMPLATE
		if (get_post($admin_page->oxygen_template_id)->post_name !== 'main') {

			$has_main = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_type = 'ct_template' AND post_title LIKE '%s'", '%Main%'));

			if (count($has_main) > 0) {

				wp_enqueue_style('main-oxygen', content_url() . '/uploads/oxygen/css/main-' . $has_main[0]->ID . '.css', array());
			} // end if;

		} // end if;

		wp_enqueue_style('dinamyc-oxygen', content_url() . '/uploads/oxygen/css/' . get_post($admin_page->oxygen_template_id)->post_name . '-' . $admin_page->oxygen_template_id . '.css', array());

		$has_archive = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_type = 'ct_template' AND post_title LIKE '%s'", '%Archive%'));

		if (count($has_archive) > 0) {

			wp_enqueue_style('archive-oxygen', content_url() . '/uploads/oxygen/css/archive-' . $has_archive[0]->ID . '.css', array());
		} // end if;

	} // end enqueue_assets_before;

	/**
	 * Enqueue assets after
	 *
	 * @since 1.5.0
	 * @return void
	 */
	public function enqueue_assets_after() {

		// echo "<style type=\"text/css\" id=\"ct-footer-css\">\r\n";
		// ct_css_styles();
		// echo "</style>\r\n";
		global $oxygen_vsb_scripts, $oxygen_vsb_components;

		add_action('admin_footer', array($oxygen_vsb_components['tabs'], 'output_js') );
		add_action('admin_footer', array($oxygen_vsb_components['fancy_icon'], 'output_svg_set') );
		add_action('admin_footer', array($oxygen_vsb_components['svg_icon'], 'output_svg_set') );
		add_action('admin_footer', array($oxygen_vsb_scripts, 'frontend_scripts') );
		add_action('admin_footer', array($oxygen_vsb_scripts, 'builder_scripts') );
		// do_action('wp_footer');
	}  // end enqueue_assets_after;

}  // end class WU_Admin_Pages_Oxygen_Support;


/**
 * Oxygen requires that we add some classes to the body tag, otherwise it won't apply its styles to the elements.
 *
 * @since 1.3.0
 * @param string $classes The admin body tag classes.
 * @return string
 */
function admin_oxygen_body_class($classes) {

	// Wrong: No space in the beginning/end.
	$classes .= ' oxygen-body ';

	return $classes;

}  // end admin_oxygen_body_class;

/**
 * Conditionally load the support, if Oxygen is Active
 *
 * @since 1.1.0
 * @return void
 */
function wu_admin_pages_add_oxygen_support() {

	if (wapp_check_if_oxygen_is_available()) {

		add_filter('admin_body_class', 'admin_oxygen_body_class');

		new WU_Admin_Pages_Oxygen_Support();

	} else {

		add_filter('wu_admin_pages_get_editor_options', array('WU_Admin_Pages_Oxygen_Support', 'add_option'));

	} // end if;

} // end wu_admin_pages_add_oxygen_support;

/**
 * Check if the oxygen builder is available.
 *
 * @since 1.5.4
 * @return bool
 */
function wapp_check_if_oxygen_is_available() {

	return function_exists('oxygen_vsb_current_user_can_access');

} // end wapp_check_if_oxygen_is_available;

/**
 * Load the Oxygen Builder Support
 *
 * @since 1.1.0
 */
add_action('plugins_loaded', 'wu_admin_pages_add_oxygen_support', 11);
