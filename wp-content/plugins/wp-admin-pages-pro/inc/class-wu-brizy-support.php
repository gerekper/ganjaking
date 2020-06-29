<?php
/**
 * Brizy Support
 *
 * Adds support to Brizy, if it is enabled
 *
 * @author      WP Admin Pages PRO
 * @category    Admin
 * @package     WP_Admin_Pages_PRO/Admin_Pages/Brizy
 * @version     1.0.1
 */

if (!defined('ABSPATH')) {
	exit;
} // end if;

/**
 * Implements Brizy Support
 *
 * @since 1.4.0 Now implements WU_Admin_Page_Content_Source_Page_Builder.
 * @since 1.3.0
 */
class WU_Admin_Pages_Brizy_Support extends WU_Admin_Page_Content_Source_Page_Builder {

	/**
	 * Initializes the Brizy Support
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
			'id'             => 'brizy',
			'post_type'      => 'brizy_template',
			'title'          => __('Brizy', 'wu-apc'),
			'selector_title' => __('Brizy Template', 'wu-apc'),
			'field'          => 'brizy_template_id',
			'read_more_link' => 'https://brizy.io/',
			'supports_modal' => array('edit_link'),
			'edit_link'      => $this->get_brizy_edit_url('TEMPLATE_ID'),
			'add_new_link'   => admin_url('post-new.php?post_type=brizy_template'),
			'see_all_link'   => admin_url('post-new.php?post_type=brizy_template'),
		);

	} // end config;

	/**
	 * Returns the edit link with the ID in place.
	 *
	 * @since 1.4.0
	 * @param mixed $id The id of the template.
	 * @return string
	 */
	public function get_brizy_edit_url($id) {

		return sprintf(admin_url('admin-post.php?action=_brizy_admin_editor_enable&post=%s'), $id);

	} // end get_brizy_edit_url;

	/**
	 * Register the necessdary scripts to run brizy page, scripts and styles
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public function register_admin_init_files() {

		if ( class_exists('Brizy_Editor') ) {

			add_action('wu_apc_load_page', function($admin_page) {

				if ($admin_page->content_type !== 'brizy') {

					return;

				} // end if;

				add_action('wu_page_enqueue_scripts_before', array($this, 'load_scripts_before'));
				add_action('admin_enqueue_scripts', array($this, 'load_scripts_before'));

				add_action('wu_page_enqueue_scripts', array($this, 'load_scripts_after'));

			});

		} // end if;

	} // end register_admin_init_files;

	/**
	 * Actually loads the scripts.
	 *
	 * This was necessary because Brizy was doing funky stuff with how it build the pages.
	 *
	 * @since 1.3.0
	 * @param WU_Admin_Page $admin_page The current admin page.
	 * @return void
	 */
	public function load_scripts_after($admin_page) {

		if ($admin_page && $admin_page->content_type !== 'brizy') {
			return;
		} // end if;

		global $post;

		$brizy_class           = Brizy_Admin_Main::instance();
		$brizy_class_templates = Brizy_Admin_Templates::_init();

		// Set the global post
		if (!is_object($post)) {
			$post = new stdClass();
		} // end if;

		$_REQUEST['post'] = $admin_page->brizy_template_id;

		WP_Ultimo_APC()->is_network_active() && switch_to_blog( get_current_site()->blog_id );

		$template = Brizy_Editor_Post::get( $admin_page->brizy_template_id );
		$this->insert_page_head($template);
		$brizy_class_templates->templateFrontEnd();

		WP_Ultimo_APC()->is_network_active() && restore_current_blog();

	}  // end load_scripts_after;

	/**
	 * Loads the necessary Brizy scripts for the beforeaction.
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public function load_scripts_before() {

		WP_Ultimo_APC()->is_network_active() && switch_to_blog( get_current_site()->blog_id );

		$url_builder = new Brizy_Editor_UrlBuilder( Brizy_Editor_Project::get() );
		$assets_url  = $url_builder->editor_build_url();

		wp_enqueue_style( 'brizy-preview', "${assets_url}/editor/css/preview.css", array(), BRIZY_EDITOR_VERSION, false );
		wp_register_script( 'brizy-preview-polyfill', 'https://cdn.polyfill.io/v2/polyfill.js?features=IntersectionObserver,IntersectionObserverEntry', array(), null, true );
		wp_enqueue_script( 'brizy-preview', "${assets_url}/editor/js/preview.js", array( 'brizy-preview-polyfill' ), BRIZY_EDITOR_VERSION, true );

		WP_Ultimo_APC()->is_network_active() && restore_current_blog();

	}  // end load_scripts_before;

	/**
	 * Inserts the page head for the Brizy Template.
	 *
	 * @since 1.3.0
	 * @param mixed $template Brizy Template.
	 * @return void
	 */
	public function insert_page_head($template) {

		if ( !$template ) {
			return;
		} // end if;

		$pid  = Brizy_Editor::get()->currentPostId();
		// $post = $template->get_wp_post();

		if ( $pid ) {
			$post = get_post( $pid );
		} // end if;

		$compiled_page = $template->get_compiled_page();

		$context = Brizy_Content_ContextFactory::createContext( Brizy_Editor_Project::get(), null, $post, null );

		$main_processor = new Brizy_Content_MainProcessor( $context );

		$head = $main_processor->process( $compiled_page->get_head() );

		$template->set_compiled_html_head($head);

		?>
    <!-- BRIZY HEAD -->
		<?php echo $template->get_compiled_html_head(); ?>
    <!-- END BRIZY HEAD -->
		<?php

	}  // end insert_page_head;

	/**
	 * Add Brizy as a content type option
	 *
	 * @since  1.1.0
	 * @param  array $options The list of content type options supported.
	 * @return array
	 */
	public static function add_option($options) {

		$options['brizy'] = array(
			'label'  => __('Use Brizy Template', 'wu-apc'),
			'icon'   => 'dashicons dashicons-schedule',
			'active' => class_exists('Brizy_Editor'),
			'title'  => class_exists('Brizy_Editor') ? '' : __('You need Brizy active to use this feature.', 'wu-apc'),
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
			'post_type'   => 'brizy_template',
		);

		return get_posts($args);

	} // end fetch_templates;

	/**
	 * Renders the Brizy layout
	 *
	 * @since 1.1.0
	 * @param WU_Admin_Page $admin_page The current admin page being displayed.
	 * @return void
	 */
	public function display_content($admin_page) {

		global $post;

		if ($admin_page->content_type != 'brizy') {
			return;
		} // end if;

		$bkp_post = $post;

		$post = (object) array(
			'ID' => abs($admin_page->brizy_template_id),
		);

		?>

    <div id="wu-apc-brizy-content">

		<?php

		WP_Ultimo_APC()->is_network_active() && switch_to_blog( get_current_site()->blog_id );

		// $this->load_scripts_before();
		$brizy_selected_post = Brizy_Editor_Post::get($admin_page->brizy_template_id);

		$wp_post = get_post($admin_page->brizy_template_id);

		$bpost = new Brizy_Public_Main($brizy_selected_post);

		$bpost->_action_enqueue_preview_assets();

		$this->run_private_method($bpost, 'preparePost');

		$bpost->insert_page_head();

		$content = apply_filters( 'brizy_content', $brizy_selected_post->get_compiled_page()->get_body(), Brizy_Editor_Project::get(), $wp_post, 'body');

		// $content = $bpost->insert_page_content($content);

		WP_Ultimo_APC()->is_network_active() && restore_current_blog();

		echo wu_apc_process_page_content($content);

		// $this->load_scripts_after($admin_page);
		?>

    </div>

		<?php

		/** restore post */
		$post = $bkp_post;

	}  // end display_content;

}  // end class WU_Admin_Pages_Brizy_Support;

/**
 * Brizy requires that we add some classes to the body tag, otherwise it won't apply its styles to the elements.
 *
 * @since 1.3.0
 * @param string $classes The admin body tag classes.
 * @return string
 */
function admin_brizy_body_class($classes ) {

	// Wrong: No space in the beginning/end.
	$classes .= ' brz ';
	$classes .= (function_exists( 'wp_is_mobile' ) && wp_is_mobile()) ? ' brz-is-mobile ' : '';

	return $classes;

}  // end admin_brizy_body_class;

/**
 * Conditionally load the support, if Brizy is Active.
 *
 * @since 1.1.0
 * @return void
 */
function wu_admin_pages_add_brizy_support() {

	if (class_exists('Brizy_Editor')) {

		add_filter('admin_body_class', 'admin_brizy_body_class');

		new WU_Admin_Pages_Brizy_Support();

	} else {

		add_filter('wu_admin_pages_get_editor_options', array('WU_Admin_Pages_Brizy_Support', 'add_option'));

	} // end if;

} // end wu_admin_pages_add_brizy_support;

/**
 * Load the brizy Support
 *
 * @since 1.1.0
 */
add_action('plugins_loaded', 'wu_admin_pages_add_brizy_support', 11);
