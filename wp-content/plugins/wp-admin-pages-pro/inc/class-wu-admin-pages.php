<?php
/**
 * Admin Pages Handler
 *
 * Handles the menu, saving function and all of that for Admin Pages
 *
 * @author      WP Admin Pages PRO
 * @category    Admin
 * @package     WP_Admin_Pages_PRO/Admin_Pages
 * @version     0.0.1
 */

if (!defined('ABSPATH')) {
	exit;
} // end if;

class WU_Admin_Pages {

	/**
	 * Instace of class
	 *
	 * @since
	 *
	 * @var WU_Admin_Pages
	 */
	public static $instance;

	/**
	 * Admin Pages WP_List_Table object
	 *
	 * @since
	 *
	 * @var array
	 */
	public $admin_pages_list;

	/**
	 * Hook action admin
	 *
	 * @since
	 *
	 * @var string
	 */
	public $admin_hook;

	/**
	 * Boolean check if user can manage
	 *
	 * @since
	 *
	 * @var boolean
	 */
	public $user_can_manage;

	/**
	 * The menu list to avoid extra calls
	 *
	 * @since 1.5.0
	 * @var array
	 */
	public $menu_list;

	/**
	 * Returns a single instance of this class
     *
	 * @since 0.0.1
	 * @return WU_Admin_Pages
	 */
	public static function get_instance() {

		if (!isset(self::$instance)) {

			self::$instance = new self();

		} // end if;

		return self::$instance;

	} // end get_instance;

	/**
	 * Construct
	 */
	public function __construct() {

		// Change hook admin if network activaded
		$this->check_admin_hook();

		// Change user can manage if network activaded
		$this->check_can_user_manage();

		// Adding the menu item
		add_action($this->admin_hook, array($this, 'plugin_menu'));

		// Handles the addition of a new plan
		add_action('admin_init', array($this, 'save_admin_page'));

		// Create our Custom post Type for Admin Pages in the Main Site
		add_action('init', array($this, 'add_admin_page_cpt'));

		// Add pages to the dashboard
		add_action('admin_menu', array($this, 'add_menu_items'));

		// We can only add submenus after all menus have been added
		add_action('admin_menu', array($this, 'add_submenu_items'), 20);

		// Add the pages the own user added as parent options to future pages
		add_action('admin_init', array($this, 'set_menu_and_submenu_json_point'));
		add_action('admin_init', array($this, 'flush_menu_and_submenus'));

		// Add the others menus
		add_filter('wu_apc_get_parent_menu_options', array($this, 'add_others_menu_as_parent_options'), 99999);
		add_filter('wu_apc_get_parent_menu_options', array($this, 'add_hidding_pages_to_menu'), 99998);

		// Add the others sub menus
		add_filter('wu_apc_get_parent_sub_menu_options', array($this, 'add_others_sub_menu_as_parent_options'), 99999);

		// Add the replace functionality
		add_action('current_screen', array($this, 'render_replace_page'), 1);

		add_action('current_screen', array($this, 'set_current_admin_page'), 0);

		add_action('wp_ajax_wu_query_sites', array($this, 'query_sites'));

		add_action('wp_ajax_wu_is_embedable', array($this, 'is_url_embedable'));

		add_action('wp_ajax_callback_target_users', array($this, 'callback_target_users'));

		add_filter('wu_apc_should_display_admin_menu', array($this, 'should_display_menu_based_on_setting'), 5);

		add_filter('admin_init', array($this, 'menu_visibility_toggle'), 10);

		add_filter('screen_settings', array($this, 'adds_menu_visibility_toggle'), 10, 2);

		add_action('admin_init', array($this, 'refactoring_all_admin_pages'));

		add_action('all_admin_notices', array($this, 'replace_dashboard'), 9999);

		add_action('wp_ajax_new_update_welcome_panel', array($this, 'wp_ajax_update_welcome_panel'), 20);

		add_action('admin_init', function() {

			if (wp_doing_ajax() && isset($_REQUEST['action']) && $_REQUEST['action'] == 'update-welcome-panel') {

				$_REQUEST['action'] = 'new_update_welcome_panel';

			} // end if;

		});

	} // end __construct;

	/**
	 * Ajax handler for updating whether to display the welcome panel.
	 *
	 * @since 3.1.0
	 */
	public function wp_ajax_update_welcome_panel() {

		check_ajax_referer('welcome-panel-nonce', 'welcomepanelnonce');

		update_user_meta(get_current_user_id(), 'show_welcome_panel', empty($_POST['visible']) ? 0 : 1 );

		wp_die(1);

	} // end wp_ajax_update_welcome_panel;

	/**
	 * Replace the Dashboard
	 *
	 * @since 1.7.1
	 * @return void
	 */
	public function replace_dashboard() {

		global $pagenow, $wuapc_has_custom_welcome_widget;

		if ($pagenow === 'index.php' && !isset($_GET['page']) && $wuapc_has_custom_welcome_widget === true) {

			WP_Ultimo_APC()->render('template/dashboard');

		} // end if;

	} // end replace_dashboard;

	/**
	 * Refactoring all WU Admin Pages with name changes
	 *
	 * @since 1.7.1
	 *
	 * @return void
	 */
	public function refactoring_all_admin_pages() {

		if (WP_Ultimo_APC()->is_network_active() && is_main_site() ) {

			return;

		} // end if;

		$admin_pages = $this->get_admin_pages();

		foreach ($admin_pages as $admin_page) {

			if ($admin_page && !empty($admin_page->menu_label)) {

				if ($admin_page->menu_type == 'replace') {

					$this->rename_menu_label($admin_page->menu_label, $admin_page->page_to_replace);

				} elseif ($admin_page->menu_type == 'replace_submenu') {

					$this->rename_sub_menu_label($admin_page->menu_label, $admin_page->page_to_replace);

				} // end if;

			} // end if;

		} // end foreach;

	} // end refactoring_all_admin_pages;

	/**
	 * Get admin page by screen
	 * The function returns null if called from the admin_init hook. It should be OK to use in a later hook such as current_screen.
	 *
	 * @since 1.7.1
	 *
	 * @return WU_Admin_Page|false
	 */
	public function get_admin_page_by_screen() {

		$screen = get_current_screen();

		if ($screen) {

			if (strpos($screen->id, 'wuapc-page-') !== false) {
				// slug_url not seted
				$admin_page = $this->get_page_from_screen_id($screen->id);
			} else {
				// slug_url seted
				$admin_page = $this->get_page_by_slug_query($screen->id);
			} // end if;

		} // end if;

		return $admin_page ? $admin_page : false;

	} // end get_admin_page_by_screen;

	/**
	 * Is URL embedable?
	 *
	 * @since 1.6.0
	 * @return void
	 */
	public function is_url_embedable() {

		check_ajax_referer('check_if_embadable');

		$results = true;

		if (current_user_can('manage_options')) {

			$url = $_REQUEST['url'];

			$header = @get_headers($url, 1);

			// URL okay?
			if (!$header || stripos($header[0], '200 ok') === false) {

				$results = false;

			} elseif (isset($header['X-Frame-Options']) && (stripos($header['X-Frame-Options'], 'SAMEORIGIN') !== false || stripos($header['X-Frame-Options'], 'deny') !== false)) {

				$results = false;

			} // end if;

		} // end if;

		wp_send_json( (int) $results );

	} // end is_url_embedable;

	/**
	 * Adds the setting to toggle the Admin Pages menu visibility.
	 *
	 * @since 1.4.0
	 * @param string    $settings The previous HTML for settings.
	 * @param WP_Screen $screen The current screen object.
	 * @return string
	 */
	public function adds_menu_visibility_toggle($settings, $screen) {

		/**
		 * Fix for Woo Membership Import
		 */
		if (isset($_GET['section']) && $_GET['section'] == 'csv_import_user_memberships') {

			return $settings;

		} // end if;

		if (!current_user_can($this->user_can_manage)) {

			return $settings;

		} // end if;

		if (WP_Ultimo_APC()->is_network_active() && !is_network_admin()) {

			return $settings;

		} // end if;

		if (!$this->should_display_admin_menu() && $this->should_display_menu_based_on_setting(1)) {

			return $settings;

		} // end if;

		ob_start();

		WP_Ultimo_APC()->render('meta/settings', array(
			'is_menu_visible' => $this->get_menu_visibility(),
		));

		$content = ob_get_clean();

		return $content;

	} // end adds_menu_visibility_toggle;

	/**
	 * Saves the admin's choice to display or hide the Admin Pages menu item.
	 *
	 * @since 1.4.0
	 * @return void
	 */
	public function menu_visibility_toggle() {

		if (isset($_POST['submit-display-admin-pages']) || isset($_POST['screen-options-apply'])) {

			$should_display_admin_menu = isset($_POST['display-admin-pages-menu']);

			$this->set_menu_visibility($should_display_admin_menu);

			$not_allowed_pages = array(
				WU_Admin_Pages_Standalone_Dependencies()->main_menu_slug,
				WU_Admin_Pages_Standalone_Dependencies()->edit_menu_slug,
			);

			if (isset($_GET['page']) && in_array($_GET['page'], $not_allowed_pages)) {

				wp_redirect(WP_Ultimo_APC()->is_network_active() ? network_admin_url() : admin_url());

			} else {

				wp_redirect( add_query_arg() );

			} // end if;

			exit;

		} // end if;

	} // end menu_visibility_toggle;

	/**
	 * Decides wether or not we should display the menu based on the saved settings
	 *
	 * @since 1.4.0
	 * @param bool $should_display_admin_menu If we want to display the mneu item or not.
	 * @return bool
	 */
	public function should_display_menu_based_on_setting($should_display_admin_menu) {

		return $this->get_menu_visibility() == 'yes';

	} // end should_display_menu_based_on_setting;

	/**
	 * Gets the menu visibilty setting, depending on the environment.
	 *
	 * @since 1.4.0
	 * @return bool
	 */
	public function get_menu_visibility() {

		$option_name = 'wu_apc_should_display_admin_page';

		$value = get_site_option($option_name, 'yes');

		return $value;

	} // end get_menu_visibility;

	/**
	 * Sets the visibility of the menu, depending on the enironment.
	 *
	 * @since 1.4.0
	 * @param bool $value Value to set.
	 * @return void
	 */
	public function set_menu_visibility($value) {

		$option_name = 'wu_apc_should_display_admin_page';

		update_site_option($option_name, $value ? 'yes' : 'no');

	}  // end set_menu_visibility;

	/**
	 * Change hook admin if network activaded
	 *
	 * @return void
	 */
	public function check_admin_hook() {
		$this->admin_hook = is_multisite() && defined('WPAPP_IS_NETWORK') && WPAPP_IS_NETWORK ? 'network_admin_menu' : 'admin_menu';
	}  // end check_admin_hook;

	/**
	 * Change user can manage if network activaded
	 *
	 * @return void
	 */
	public function check_can_user_manage() {
		$this->user_can_manage = is_multisite() && defined('WPAPP_IS_NETWORK') && WPAPP_IS_NETWORK ? 'manage_network' : 'manage_options';
	}  // end check_can_user_manage;

	/**
	 * Actually handles the printing of the replaced content
	 *
	 * @since 1.1.0
	 * @param WU_Admin_Page $admin_page  Object Admin Page.
	 * @param boolean       $display_wrapper Check Boolean.
	 * @param boolean       $display_footer Check Boolean.
	 * @return void
	 */
	public function render_replace_page_content($admin_page, $display_wrapper = false, $display_footer = false) {

		if (!$admin_page->show_welcome) {

			remove_action( 'welcome_panel', 'wp_welcome_panel' );

		} // end if;

		$this->enqueue_scripts_and_styles($admin_page);

		echo $display_wrapper ? '<div id="wpbody" role="main"><div id="wpbody-content" aria-label="Main content" tabindex="0">' : '';

		WP_Ultimo_APC()->render('template/page', array(
			'admin_page' => $admin_page,
		));

		if ($display_footer) {
			include(ABSPATH . 'wp-admin/admin-footer.php');
		} // end if;

		echo $display_wrapper ? '</div></div>' : '';

		if ($display_wrapper) {
			die;
		} // end if;

	} // end render_replace_page_content;

	/**
	 * Rename menu label function
	 *
	 * @since 1.7.1
	 * @param string $new_label New label to rename.
	 * @param string $page_to_replace Page to replace ex: users.php / $admin_page->page_to_replace.
	 * @return void
	 */
	public function rename_menu_label($new_label, $page_to_replace) {

		global $menu;

		if ($menu) {

			foreach ($menu as &$each_menu) {

				if ($each_menu[2] == $page_to_replace) {

					$each_menu[0] = $new_label;

				} // end if;

			} // end foreach;

		} // end if;

	} // end rename_menu_label;

	/**
	 * Rename sub menu label function
	 *
	 * @since 1.7.1
	 * @param string $new_label New label to rename.
	 * @param string $page_to_replace Page to replace ex: users.php / $admin_page->page_to_replace.
	 * @return void
	 */
	public function rename_sub_menu_label($new_label, $page_to_replace) {

		global $submenu;

		if ($submenu) {

			foreach ($submenu as &$each_submenu) {

				foreach ($each_submenu as &$each_part) {

					if ($each_part[2] == $page_to_replace) {

						$each_part[0] = $new_label;

					} // end if;

				} // end foreach;

			} // end foreach;

		} // end if;

	} // end rename_sub_menu_label;

	/**
	 * Handles the replace mode, allowing admins to replace the contents of pages that already exists;
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function render_replace_page() {

		global $pagenow;

		if (is_network_admin()) {

			return;

		} // end if;

		$search_page = $pagenow;

		if ($search_page == 'edit.php' && get_current_screen()->post_type !== 'post') {

			$search_page = 'edit.php?post_type=' . get_current_screen()->post_type;

		} elseif (isset($_GET['page']) && strpos($_GET['page'], 'wuapc') !== false ) {

			// check if page is a custom page
			$search_page = $_GET['page'];

		} // end if;

			/**
			 * TODO: DIVI, why do you do that to me??
			 */
		if (isset($_REQUEST['post_type']) && $_REQUEST['post_type'] == 'et_pb_layout') {

			return;

		} // end if;

		if (isset($_GET['page'])) {

			// $search_page = get_current_screen()->id;
			$search_page = $_GET['page'];

		} // end if;

		$admin_pages = $this->get_admin_pages(array(
			'meta_query' => array(
				'replace_type' => array(
					'key'     => 'wpu_menu_type',
					'value'   => 'replace',
					'compare' => 'LIKE',
				),
				'replace_page' => array(
					'key'     => 'wpu_page_to_replace',
					'value'   => $search_page,
					'compare' => count($_GET) > 0 ? 'LIKE' : '=',
				),

			),
		));

		if (empty($admin_pages)) {

			$admin_pages = $this->get_admin_pages(array(
				'meta_query' => array(
					'replace_type' => array(
						'key'     => 'wpu_menu_type',
						'value'   => 'replace',
						'compare' => 'LIKE',
					),
					'replace_page' => array(
						'key'     => 'wpu_page_to_replace',
						'value'   => $search_page,
						'compare' => 'LIKE',
					),

				),
			));

		} // end if;

		if (empty($admin_pages)) {

			// $pagenow = $default_pagenow;
			return;

		} // end if;

		foreach ($admin_pages as $admin_page) {

			if ($admin_page->active && $admin_page->should_display()) {

				$this->active_page = $admin_page;

				$this->handle_earlier_hooks($pagenow, $admin_page);

				/**
				 * Switch types
			   *
			   * @since 1.1.0
			   */
				if ($admin_page->replace_mode == 'all') {

					add_action('in_admin_header', function() use ($admin_page) {

						$this->render_replace_page_content($admin_page, true, true);

					});

					continue; // In case of replace, exit the loop

				} elseif ($admin_page->replace_mode == 'append_top') {

					add_action('all_admin_notices', function() use ($admin_page) {

						$this->render_replace_page_content($admin_page);

					}, 99999);

				} elseif ($admin_page->replace_mode == 'append_bottom') {

					add_action('admin_footer', function() use ($admin_page) {

						echo '<div id="wpcontent" style="padding-bottom: 30px;">';

						$this->render_replace_page_content($admin_page);

						echo '</div>';

					}, 5);

				} // end if;

			} // end if;

		} // end foreach;

	} // end render_replace_page;

	/**
	 * Adds the admin pages custom post type
	 *
	 * @since 0.0.1
	 * @return void
	 */
	public function add_admin_page_cpt() {

		$labels = array(
			'name'               => _x('Admin Pages', 'post type general name', 'wu-apc'),
			'singular_name'      => _x('Admin Page', 'post type singular name', 'wu-apc'),
			'menu_name'          => _x('Admin Pages', 'admin menu', 'wu-apc'),
			'name_admin_bar'     => _x('Admin Page', 'add new on admin bar', 'wu-apc'),
			'add_new'            => _x('Add New', 'Admin Page', 'wu-apc'),
			'add_new_item'       => __('Add New Admin Page', 'wu-apc'),
			'new_item'           => __('New Admin Page', 'wu-apc'),
			'edit_item'          => __('Edit Admin Page', 'wu-apc'),
			'view_item'          => __('View Admin Page', 'wu-apc'),
			'all_items'          => __('All Admin Pages', 'wu-apc'),
			'search_items'       => __('Search Admin Pages', 'wu-apc'),
			'parent_item_colon'  => __('Parent Admin Pages:', 'wu-apc'),
			'not_found'          => __('No Admin Pages found.', 'wu-apc'),
			'not_found_in_trash' => __('No Admin Pages found in Trash.', 'wu-apc')
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => false,
			'show_in_menu'       => false,
			'query_var'          => true,
			'has_archive'        => true,
			'hierarchical'       => false,
			'can_export'         => !WP_Ultimo_APC()->is_network_active(),
			'menu_position'      => null,
			'capability'         => $this->user_can_manage,
			'description'        => __('Description.', 'wu-apc'),
			'supports'           => array('title', 'editor', 'excerpt', 'custom-fields'),
			'rewrite'            => array('slug' => 'wpultimo_admin_page'),
		);

		register_post_type('wpultimo_admin_page', $args);

	} // end add_admin_page_cpt;

	/**
	 * Hide the manage pages
	 *
	 * @since 1.4.0
	 * @return bool
	 */
	public function should_display_admin_menu() {

		/**
		 * Allow admins to hide the admin menu pages, if they have finished the customization process.
		 *
		 * @since 1.4.0
		 * @param bool $should_display_admin_menu If we need to display the pages.
		 * @param bool $network_active  Check if the plugin is network admin active.
		 */
		return apply_filters('wu_apc_should_display_admin_menu', true, WP_Ultimo_APC()->is_network_active());

	} // end should_display_admin_menu;

	/**
	 * Adds the menu pages for the CPT on the Network Admin
	 *
	 * @since 0.0.1
	 * @return void
	 */
	public function plugin_menu() {

		/**
		 * If the menu is hidden, do not show it, duh!
		 */
		if ($this->should_display_admin_menu() == false) {
			return;
		} // end if;

		global $submenu, $menu;

		$menu_page_slug = WU_Admin_Pages_Standalone_Dependencies()->main_menu_slug;

		$menu[10101039] = array('', 'read', 'separator10101039', '', 'wp-menu-separator');

		// Plan Menu Page
		$hook_admin_page = '';

		if (current_user_can($this->user_can_manage)) {

			$hook_admin_page = add_menu_page(__('All Admin Pages', 'wu-apc'), __('Admin Pages', 'wu-apc'), $this->user_can_manage, $menu_page_slug, array($this, 'render_admin_pages_list_page'), 'dashicons-editor-table', 10101040);

		} // end if;

		// Adds necessary hook to make things work
		add_action("load-$hook_admin_page", array($this, 'screen_option_admin_pages'));

		// Admin Pages - Add New
		$admin_page_add_new_page = add_submenu_page($menu_page_slug, __('Add New', 'wu-apc'), __('Add New', 'wu-apc'), $this->user_can_manage, WU_Admin_Pages_Standalone_Dependencies()->edit_menu_slug, array($this, 'render_admin_pages_add_new_page'));

		add_action("load-$admin_page_add_new_page", array($this, 'load_remote_menu_list'));

		add_action("load-$hook_admin_page", array($this, 'load_required_scripts'));
		add_action("load-$admin_page_add_new_page", array($this, 'load_required_scripts'));

		// Add Pages to Branding
		$this->add_to_branding(array(
			$admin_page_add_new_page,
			$admin_page_add_new_page
		));

		/**
		 * Fix name
		 */
		$submenu[$menu_page_slug][0][0] = __('All Admin Pages', 'wu-apc');

	} // end plugin_menu;

	/**
	 * Allows us to load the scripts.
	 *
	 * @since 1.5.1
	 * @return void
	 */
	public function load_required_scripts() {

		do_action('wuapp_load_required_scripts');

	} // end load_required_scripts;

	/**
	 * If WP Ultimo is available, adds the pages to the branding
	 *
	 * @since 0.0.1
	 * @param array $pages Array of pages.
	 * @return void
	 */
	public function add_to_branding($pages = array()) {

		if (!function_exists('WP_Ultimo')) {
			return;
		} // end if;

		foreach ($pages as $page) {

			if (class_exists('WU_UI_Elements') && method_exists(WU_UI_Elements(), 'add_page_to_branding')) {

				WU_UI_Elements()->add_page_to_branding($page);

			} else {

				WP_Ultimo()->add_page_to_branding($page);

			} // end if;

		} // end foreach;

	} // end add_to_branding;

	/**
	 * Screen options
     *
	 * @since 0.0.1
	 */
	public function screen_option_admin_pages() {

		$this->admin_pages_list = new WU_Admin_Pages_List_Table();

		/** Process bulk action */
		$this->admin_pages_list->process_bulk_action();

	} // end screen_option_admin_pages;

	/**
	 * Get the admin pages WP_Query wrapper
	 *
	 * @since 0.0.1
	 * @param array $query Query args.
	 * @return array
	 */
	public function get_admin_pages($query = array()) {

		$admin_pages = array();

		WP_Ultimo_APC()->is_network_active() && switch_to_blog(1);

		$args = wp_parse_args($query, array(
			'orderby'        => 'wpu_menu_order',
			'order'          => 'ASC',
			'post_type'      => 'wpultimo_admin_page',
			'posts_per_page' => -1,
			'meta_key'       => null,
			'meta_query'     => null,
			'fields'         => null,
		));

		$menus = get_posts($args);

		foreach ($menus as $menu) {

			$admin_pages[] = wu_apc_get_admin_page($menu->ID);

		} // end foreach;

		WP_Ultimo_APC()->is_network_active() && restore_current_blog();

		return $admin_pages;

	} // end get_admin_pages;

	/**
	 * Get and add the top leval admin pages created via the plugin
	 *
	 * @since 0.0.1
	 * @return void
	 */
	public function add_menu_items() {

		$admin_pages = $this->get_admin_pages(array(
			'meta_query' => array(
				'active' => array(
					'key'   => 'wpu_active',
					'value' => true,
				),
				'menu'   => array(
					'key'   => 'wpu_menu_type',
					'value' => 'menu'
				)
			)
		)
		);

		foreach ($admin_pages as $admin_page) {

			if ($admin_page->should_display()) {

				$this->add_menu_page($admin_page);

			} // end if;

		} // end foreach;

	} // end add_menu_items;

	/**
	 * After the toplevel pages are added, we can add the sub-pages
	 *
	 * @since 0.0.1
	 * @return void
	 */
	public function add_submenu_items() {

		$admin_pages = $this->get_admin_pages(array(
			'meta_key'   => 'wpu_menu_order',
			'orderby'    => 'meta_value',
			'order'      => 'ASC',
			'meta_query' => array(
				'active' => array(
					'key'   => 'wpu_active',
					'value' => true,
				),
				'menu'   => array(
					'key'   => 'wpu_menu_type',
					'value' => 'submenu'
				)
			)
		)
		);

		foreach ($admin_pages as $admin_page) {

			if ($admin_page->should_display()) {

				$this->add_submenu_page($admin_page);

			} // end if;

		} // end foreach;

	} // end add_submenu_items;

	/**
	 * Removes admin notices if necesary
	 *
	 * @since 1.4.0
	 * @param string  $hook Name of the hook.
	 * @param WU_Page $admin_page WU_Page object of the current page.
	 * @return void
	 */
	public function handle_admin_notices($hook, $admin_page) {

		if (!$admin_page->display_admin_notices) {

			add_action("load-$hook", function () use ($admin_page) {

				remove_all_actions('admin_notices');

			});

		} // end if;

	} // end handle_admin_notices;

	/**
	 * Call action hooks earlier
	 *
	 * @since 0.0.1
	 *
	 * @param string        $hook hook action.
	 * @param WP_Admin_Page $admin_page Object Admin Page.
	 * @return void
	 */
	public function handle_earlier_hooks($hook, $admin_page) {

		add_action("load-$hook", function() use ($admin_page) {

			do_action('wu_apc_load_page', $admin_page);

		});

	} // end handle_earlier_hooks;

	/**
	 * Add separator if checked in advanced options
	 *
	 * @since 1.4.0
	 *
     * @param WU_Admin_Page $admin_page Admin Page.
	 *
	 * @return void
	 */
	public function add_menu_separator($admin_page) {

		global $menu;

		$value_before = 0;
		$value_after  = 0.1;

		// invert;
		if ($admin_page->menu_order < 0) {

			$value_before = 0.1;
			$value_after  = 0;

		} // end if;

		if ($admin_page->separator_before) {

			$order_before = sprintf('%.7f', ($admin_page->menu_order + $value_before) );

			$menu[$order_before] = array('', 'read', 'separator' . $order_before, '', 'wp-menu-separator');

		} // end if;

		if ($admin_page->separator_after) {

			$order_after = sprintf('%.7f', ($admin_page->menu_order + $value_after) );

			$menu[$order_after] = array('', 'read', 'separator' . $order_after, '', 'wp-menu-separator');

		} // end if;

		ksort($menu);

	}  // end add_menu_separator;


	/**
	 * Adds a new toplevel page to the menu
	 *
	 * @since 0.0.1
	 * @param WU_Admin_Page $admin_page Object Admin Page.
	 * @return void
	 */
	public function add_menu_page($admin_page) {

		if (!empty($admin_page->slug_url)) {

			$slug_url = $admin_page->slug_url;

		} else {

			$slug_url = "wuapc-page-$admin_page->id";

		} // end if;

		/**
		 * Allows developers to filter the URLs
		 */

		$slug_url = apply_filters('wu_add_menu_page_slug_url', $slug_url, $admin_page);

		$callback = apply_filters('wu_add_menu_page_callback', array($this, 'render_custom_pages'), $admin_page);

		$custom_page_hook = add_menu_page($admin_page->title, $admin_page->menu_label, 'read', $slug_url, $callback, $admin_page->get_icon(), $admin_page->menu_order);

		if (isset($_GET['page']) && $_GET['page'] == $slug_url) {

			$this->active_page = $admin_page;

		} // end if;

		$this->add_menu_separator($admin_page);

		$this->handle_admin_notices($custom_page_hook, $admin_page);

		$this->handle_earlier_hooks($custom_page_hook, $admin_page);

	} // end add_menu_page;

	/**
	 * Adds a new subpage page to the menu
	 *
	 * @since 0.0.1
	 * @param WU_Admin_Page $admin_page Object Admin Page.
	 * @return void
	 */
	public function add_submenu_page($admin_page) {

		if (!empty($admin_page->slug_url)) {

			$slug_url = $admin_page->slug_url;

		} else {

			$slug_url = "wuapc-page-$admin_page->id";

		} // end if;

		$admin_page_parent = wu_apc_get_admin_page(wu_apc_get_id_by_menu_parent($admin_page->menu_parent));

		if (!empty($admin_page_parent->slug_url)) {

			$menu_parent_id = $admin_page_parent->slug_url;

		} else {

			$menu_parent_id = $admin_page->menu_parent;

		} // end if;

			/**
		 * Allows developers to filter the URLs
		 */
		$slug_url = apply_filters('wu_add_menu_page_slug_url', $slug_url, $admin_page);

		$callback = apply_filters('wu_add_menu_page_callback', array($this, 'render_custom_pages'), $admin_page);

		$custom_page_hook = add_submenu_page($menu_parent_id, $admin_page->title, $admin_page->menu_label, 'read', $slug_url, $callback);

		add_filter( 'custom_menu_order', function() use ($menu_parent_id, $admin_page) {
			$this->reorder_submenu($menu_parent_id, $admin_page);
		});

		$this->handle_admin_notices($custom_page_hook, $admin_page);

		$this->handle_earlier_hooks($custom_page_hook, $admin_page);

	} // end add_submenu_page;

	/**
	 * Reorder submenu
	 *
	 * @param string        $menu_parent_id Parent menu id.
	 * @param WU_Admin_Page $admin_page
	 * @return array
	 * @since
	 */
	public function reorder_submenu($menu_parent_id, $admin_page) {

		global $submenu;

		if ($admin_page->menu_order < 0) {

			$admin_page->menu_order = 0;

		} // end if;

		$submenu_copy = $submenu[$menu_parent_id];

		$slug = $admin_page->slug_url;

		foreach ($submenu_copy as $key => $details) {

			if ($details[2] == "wuapc-page-$admin_page->id" || $details[2] == $slug) {

				unset($submenu_copy[$key]);

				$pos = $this->get_submenu_unique_pos('' . (($admin_page->menu_order * 5) + 0.1 . ''), $submenu_copy );

				$submenu_copy[$pos] = $details;

			} // end if;

		} // end foreach;

		ksort($submenu_copy);

		$submenu[$menu_parent_id] = $submenu_copy;

		return $submenu;

	} // end reorder_submenu;

	/**
	 * Recursive function checking pos.
	 *
	 * @since 1.6.1
	 * @param int   $key Key.
	 * @param array $array Array.
	 * @return int
	 */
	public function get_submenu_unique_pos($key, $array) {

		if (array_key_exists($key, $array)) {

			$key = $key + 0.1;

			return $this->get_submenu_unique_pos('' . $key . '', $array);

		} // end if;

		return $key;

	} // end get_submenu_unique_pos;

	/**
	 * Returns the WU_Admin_Page page after getting it from parsing the screen id
	 * Useful to get the parent Admin Pages of sub-pages
	 *
	 * @since 0.0.1
	 * @param string $screen_id By screen id.
	 * @return WU_Admin_Page|false
	 */
	public function get_page_from_screen_id($screen_id) {

		$id = explode('wuapc-page-', $screen_id);

		$id = isset($id[1]) ? $id[1] : 0;

		return wu_apc_get_admin_page($id);

	} // end get_page_from_screen_id;

	/**
	 * Returns the name of the parent page
	 *
	 * @since 0.0.1
	 * @param string $parent_screen_id  By screen parent id.
	 * @return string
	 */
	public function get_parent_name($parent_screen_id) {

		$this->load_remote_menu_list();

		$parent_pages = $this->menu_list;

		foreach ($parent_pages->data->serve_menu as $key => $value) {
			if ($value[2] === $parent_screen_id) {
				return $value[0];
			} // end if;
		} // end foreach;

		return $parent_screen_id;

	} // end get_parent_name;

	/**
	 * Enqueues all the scripts and styles on the pages when displaying them
	 *
	 * This function includes all the CSS and JavaScript added to the pagel advanced options
	 * It also loads all the external css and js files listed
	 *
	 * @since 0.0.1
	 * @param WU_Admin_page $admin_page Object Admin Page.
	 * @return void
	 */
	public function enqueue_scripts_and_styles($admin_page) {

		/**
		 * Adding a custom action here so we have a place to enqueue the scripts and styles as needed\
		 * and make sure they only get loaded on the right pages
		 *
		 * @since 1.3.0
		 * @param WU_Page The current admin page
		 */
		do_action('wu_page_enqueue_scripts', $admin_page);

		wp_enqueue_script('wu-admin-page-creator-template', WP_Ultimo_APC()->get_asset('wu-admin-page-creator.min.js', 'js'), array('jquery'), WP_Ultimo_APC()->version, true);

		wp_enqueue_style('wu-admin-page-creator-template', WP_Ultimo_APC()->get_asset('wu-admin-page-creator-template.min.css', 'css'));

		wp_add_inline_script('wu-admin-page-creator-template', $admin_page->js_content);

		wp_add_inline_style('wu-admin-page-creator-template', $admin_page->css_content);

		/**
		 * Enqueue External Styles
		 */
		foreach ($admin_page->get_external_styles() as $style_url) {

			wp_enqueue_style(sanitize_title_with_dashes($style_url), $style_url);

		} // end foreach;

		/**
		 * Enqueue External Scripts
		 */
		foreach ($admin_page->get_external_scripts() as $script_url) {

			wp_enqueue_script(sanitize_title_with_dashes($script_url), $script_url, array('jquery'));

		} // end foreach;

	} // end enqueue_scripts_and_styles;

	/**
	 * Sets the active error globally
	 *
	 * @since 1.2.1
	 * @param WP_Screen $screen Object WP_Screen.
	 * @return void
	 */
	public function set_current_admin_page($screen) {

		$admin_page = $this->get_page_from_screen_id($screen->id);

		if ($admin_page) {

			$this->active_page = $admin_page;

		} // end if;

	}  // end set_current_admin_page;

	/**
	 * Get page in children site by slug
	 *
	 * @since 1.4.0
	 *
	 * @param string $slug slug of page.
	 * @return WU_Admin_Page
	 */
	public function get_page_by_slug_query($slug) {

		WP_Ultimo_APC()->is_network_active() && switch_to_blog(get_current_site()->blog_id);

		$slug = explode('_page_', $slug);

		$args = array(
			'post_type'  => 'wpultimo_admin_page',
			'meta_value' => $slug[1],
			'meta_key'   => 'wpu_slug_url',
		);

		$query = new WP_Query($args);

		if (!empty($query->posts)) {
			$admin_page_id = $query->posts[0]->ID;
		} // end if;

		WP_Ultimo_APC()->is_network_active() && restore_current_blog();

		return new WU_Admin_Page($admin_page_id);

	} // end get_page_by_slug_query;

	/**
	 * Renders the Custom Page
	 *
	 * @since 0.0.1
	 * @return void
	 */
	public function render_custom_pages() {

		$admin_page = $this->get_admin_page_by_screen();

		$this->enqueue_scripts_and_styles($admin_page);

		WP_Ultimo_APC()->render('template/page', array(
			'admin_page' => $admin_page,
		));

	} // end render_custom_pages;

	/**
	 * Renders the List Admin Pages page
	 *
	 * @since 0.0.1
	 * @return void
	 */
	public function render_admin_pages_list_page() {

		wp_enqueue_style('wu-apc', WP_Ultimo_APC()->get_asset('wu-admin-page-creator.min.css', 'css'), false);

		WP_Ultimo_APC()->render('admin-pages/list', array(
			'table' => $this->admin_pages_list
		));

	} // end render_admin_pages_list_page;

	/**
	 * Returns the merged array containing the default editor (CodeMirror) settings
	 *
	 * @since 0.0.1
	 * @param array $settings Array of settings.
	 * @return array
	 */
	public function get_merged_editor_settings($settings = array()) {

		$default_settings = array(
			'indentUnit'       => 2,
			'indentWithTabs'   => true,
			'lineNumbers'      => true,
			'lineWrapping'     => true,
			'styleActiveLine'  => true,
			'continueComments' => true,
			'inputStyle'       => 'contenteditable',
			'direction'        => 'ltr', // Code is shown in LTR even in RTL languages.
			'gutters'          => array(),
			'extraKeys'        => array(
				'Ctrl-Space' => 'autocomplete',
				'Ctrl-/'     => 'toggleComment',
				'Cmd-/'      => 'toggleComment',
				'Alt-F'      => 'findPersistent',
			),
		);

		return array_merge($default_settings, $settings);

	}  // end get_merged_editor_settings;

	/**
	 * Initializes the editors for HTML, CSS and JavaScript
	 *
	 * @since 0.0.1
	 * @return void
	 */
	public function initialize_editors() {

		// Enqueue code editor and settings for manipulating HTML.
		$settings = wp_enqueue_code_editor( array( 'type' => 'text/html' ) );

		wp_enqueue_script('csslint');
		wp_enqueue_script('jshint');

		// Bail if user disabled CodeMirror.
		if ( false === $settings ) {
			return;
		} // end if;

		wp_localize_script('wu-apc', 'wu_apc_editor_css_editor_settings', array(
			'codemirror' => $this->get_merged_editor_settings(array(
				'mode'              => 'css',
				'lint'              => true,
				'autoCloseBrackets' => true,
				'matchBrackets'     => true,
			))
		));

		wp_localize_script('wu-apc', 'wu_apc_editor_js_editor_settings', array(
			'codemirror' => $this->get_merged_editor_settings(array(
				'mode'              => 'javascript',
				'lint'              => true,
				'autoCloseBrackets' => true,
				'matchBrackets'     => true,
			))
		));

		/**
		 * PHP or HTML
		 */
		$html_settings = defined('WU_APC_ALLOW_PHP_PROCESSING') && WU_APC_ALLOW_PHP_PROCESSING
		? array(
			'codemirror' => $this->get_merged_editor_settings(array(
				'mode'              => 'javascript',
				'lint'              => true,
				'autoCloseBrackets' => true,
				'matchBrackets'     => true,
			)))
		: array(
			'type' => 'text/html',
		);

		wp_localize_script('wu-apc', 'wu_apc_editor_html_editor_settings', $html_settings);

	} // end initialize_editors;

	/**
	 * Returns a list of available parent options that the admins can use for the subpages
	 *
	 * @since 0.0.1
	 * @return array
	 */
	public function get_parent_menu_options() {

		return apply_filters('wu_apc_get_parent_menu_options', array());

	} // end get_parent_menu_options;

	/**
 	 * Returns a list of available parent options that the admins can use for the subpages
 	 *
 	 * @since 0.0.1
 	 * @return array
 	 */
	public function get_parent_sub_menu_options() {

		return apply_filters('wu_apc_get_parent_sub_menu_options', array());

	}  // end get_parent_sub_menu_options;

	public function flush_menu_and_submenus() {

		global $menu, $submenu, $blog_id;

		if ( !isset($_GET['flush_menu_and_submenus'] )) {

			return;

		} // end if;

		if (!current_user_can($this->user_can_manage)) {

			return;

		} // end if;

		$data = array(
			'serve_menu'     => $menu,
			'serve_sub_menu' => $submenu,
		);

		$content = array('success' => true);
		$content['data'] = $data;

		$content = json_decode((json_encode($content)));

		set_site_transient('wu_apc_menu_response', $content, 5 * MINUTE_IN_SECONDS);

		$redirect_url = remove_query_arg('flush_menu_and_submenus', add_query_arg());

		wp_redirect($redirect_url);

		exit;

	} // end flush_menu_and_submenus;

	/**
	 * End point globals menu and submenu
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function set_menu_and_submenu_json_point() {

		global $menu, $submenu, $blog_id;

		if ( !isset($_GET['serve_menus'] )) {

			return;

		} // end if;

		if (!current_user_can($this->user_can_manage)) {

			return wp_send_json_error(array(
				'serve_sub_menu' => array(),
				'serve_menu'     => array(),
			));

		} // end if;

		wp_send_json_success(array(
			'serve_sub_menu' => $submenu,
			'serve_menu'     => $menu,
		));

	}  // end set_menu_and_submenu_json_point;

	public function load_remote_menu_list() {

		$menu_list = get_site_transient('wu_apc_menu_response');

		if ($menu_list) {

			$this->menu_list = $menu_list;

			return;

		} // end if;

		$main_site_url = add_query_arg('serve_menus', '1', get_admin_url(get_main_site_id()));

		$response = wp_remote_get($main_site_url, array(
			'cookies'   => $_COOKIE,
			'timeout'   => 45,
			'sslverify' => false,
		));

		if (is_wp_error($response)) {

			$this->menu_list = array();

		} // end if;

		$this->menu_list = json_decode(wp_remote_retrieve_body($response));

		if ($this->menu_list) {
			
			set_site_transient('wu_apc_menu_response', $this->menu_list, 5 * MINUTE_IN_SECONDS);

		} // end if;

	} // end load_remote_menu_list;

	/**
	 * Get query of Hidden Pages
	 *
	 * @since 1.7.9
	 *
	 * @return array 
	 */
	public function get_query_hide_pages() {

		return array(
			'meta_query' => array(
				'active'   => array(
					'key'   => 'wpu_active',
					'value' => true,
				),
				'content_type' => array(
					'key'   => 'wpu_content_type',
					'value' => 'hide_page',
					'compare' => '='
				)
			)
		);

	} // end get_query_hide_pages;

	/**
	 * Adds Custom pages as Parent options as well if they are toplevel
	 *
	 * @since 0.0.1
	 * @param array $pages Array of pages.
	 * @return array
	 */
	public function add_others_menu_as_parent_options($pages) {

		$menu_response = $this->menu_list;

		if (empty($menu_response)) {

			return array();

		} // end if;

		// var_dump( $menu_response->data->serve_menu);
		$pages_diff = array();

		foreach ($menu_response->data->serve_menu as $admin_page) {

			if ($admin_page[0] !== '') {

				$pages_diff[$admin_page[2]] = wu_apc_remove_html_tags_and_content($admin_page[0]);

			} // end if;

		} // end foreach;

		$pages = array_unique(array_merge($pages, $pages_diff));

		$query = array(
			'meta_query' => array(
				'active'   => array(
					'key'   => 'wpu_active',
					'value' => true,
				),
				'toplevel' => array(
					'key'   => 'wpu_menu_type',
					'value' => 'menu'
				)
			)
		);

		$pages_custom = array();
		foreach ($this->get_admin_pages($query) as $admin_page) {

			$pages_custom["wuapc-page-$admin_page->id"] = $admin_page->title;

		} // end foreach;

		return $pages + $pages_custom;

	} // end add_others_menu_as_parent_options;

	/**
	 * Get hidden pages and add again on select field.
	 *
	 * @since 1.7.9
	 *
	 * @param array $pages Array of Pages.
	 *
	 * @return array
	 */
	public function add_hidding_pages_to_menu($pages) {

		global $menu;

		$pages_hide = array();

		foreach ($this->get_admin_pages($this->get_query_hide_pages()) as $admin_page) {

			if(is_array($admin_page->page_to_replace)) {

				foreach ($admin_page->page_to_replace as $page) {

					foreach($menu as $each_menu) {

						if (array_key_exists(2, $each_menu) && $page === $each_menu[2]) {

							$pages_hide[$page] = $each_menu[0];

						} // end if;

					} // end foreach;

				} // end foreach;

			} // end if;

		} // end foreach;

		return $pages + $pages_hide;

	} // end add_hidding_pages_to_menu;

	/**
	 * Adds Custom sub pages as Parent options as well if they are toplevel
	 *
	 * @since 1.5.0
	 * @param array $pages Array of pages.
	 * @return array
	 */
	public function add_others_sub_menu_as_parent_options($pages) {

		$menu_response = $this->menu_list;

		if (empty($menu_response)) {

			return array();

		} // end if;

		$pages_diff = array();

		foreach ($menu_response->data->serve_sub_menu as $key => $admin_page) {

			$menu_name = $this->get_related_menu_slug_by_id($menu_response->data->serve_menu, $key);

			$pages_diff["wusubdiv[$menu_name]"] = $menu_name;

			foreach ($admin_page as $admin_sub_page) {

				if (!is_array($admin_sub_page) || !isset($admin_sub_page[0])) {

					continue;

				} // end if;

				if ($admin_sub_page[0] !== '') {

					$pages_diff[$admin_sub_page[2]] = wu_apc_remove_html_tags_and_content($admin_sub_page[0]);

				} // end if;

			} // end foreach;

		} // end foreach;

		$pages = array_unique(array_merge($pages, $pages_diff));

		return $pages;

	} // end add_others_sub_menu_as_parent_options;

	/**
	 * Loop in array menus searching related menu by id
	 *
	 * @since 1.4.0
	 *
	 * @param array  $menu array menus for search.
	 * @param string $id Id of menu ex: index.php .
	 * @return string
	 */
	public function get_related_menu_slug_by_id($menu, $id) {

		// fix without id params
		if ($id === 'edit.php') {
			return 'Posts';
		} // end if;

		// first try loop
		foreach ($this->get_parent_menu_options() as $key => $value) {
			if ($key === $id) {
				return $value;
			} // end if;
		} // end foreach;

		// next try loop
		foreach ($menu as $key => $value) {

			if ($value[2] === $id) {

				return strip_tags($value[0]);

			} // end if;

		} // end foreach;

		// else
		return $id;
	} // end get_related_menu_slug_by_id;


	/**
	 * Returns the list of available icons (currently we only support Dashicons)
	 *
	 * @since 0.0.1
	 * @return array
	 */
	public function get_icons_list() {

		return apply_filters('wu_apc_icons_list', array(
			'dashicons-before dashicons-admin-appearance',
			'dashicons-before dashicons-admin-collapse',
			'dashicons-before dashicons-admin-comments',
			'dashicons-before dashicons-admin-customizer',
			'dashicons-before dashicons-admin-generic',
			'dashicons-before dashicons-admin-home',
			'dashicons-before dashicons-admin-links',
			'dashicons-before dashicons-admin-media',
			'dashicons-before dashicons-admin-multisite',
			'dashicons-before dashicons-admin-network',
			'dashicons-before dashicons-admin-page',
			'dashicons-before dashicons-admin-plugins',
			'dashicons-before dashicons-admin-post',
			'dashicons-before dashicons-admin-settings',
			// 'dashicons-before dashicons-admin-site-alt',
			// 'dashicons-before dashicons-admin-site-alt2',
			// 'dashicons-before dashicons-admin-site-alt3',
			'dashicons-before dashicons-admin-site',
			'dashicons-before dashicons-admin-tools',
			'dashicons-before dashicons-admin-users',
			'dashicons-before dashicons-album',
			'dashicons-before dashicons-align-center',
			'dashicons-before dashicons-align-left',
			'dashicons-before dashicons-align-none',
			'dashicons-before dashicons-align-right',
			'dashicons-before dashicons-analytics',
			'dashicons-before dashicons-archive',
			'dashicons-before dashicons-arrow-down-alt',
			'dashicons-before dashicons-arrow-down-alt2',
			'dashicons-before dashicons-arrow-down',
			'dashicons-before dashicons-arrow-left-alt',
			'dashicons-before dashicons-arrow-left-alt2',
			'dashicons-before dashicons-arrow-left',
			'dashicons-before dashicons-arrow-right-alt',
			'dashicons-before dashicons-arrow-right-alt2',
			'dashicons-before dashicons-arrow-right',
			'dashicons-before dashicons-arrow-up-alt',
			'dashicons-before dashicons-arrow-up-alt2',
			'dashicons-before dashicons-arrow-up',
			'dashicons-before dashicons-art',
			'dashicons-before dashicons-awards',
			'dashicons-before dashicons-backup',
			'dashicons-before dashicons-book-alt',
			'dashicons-before dashicons-book',
			'dashicons-before dashicons-buddicons-activity',
			'dashicons-before dashicons-buddicons-bbpress-logo',
			'dashicons-before dashicons-buddicons-buddypress-logo',
			'dashicons-before dashicons-buddicons-community',
			'dashicons-before dashicons-buddicons-forums',
			'dashicons-before dashicons-buddicons-friends',
			'dashicons-before dashicons-buddicons-groups',
			'dashicons-before dashicons-buddicons-pm',
			'dashicons-before dashicons-buddicons-replies',
			'dashicons-before dashicons-buddicons-topics',
			'dashicons-before dashicons-buddicons-tracking',
			'dashicons-before dashicons-building',
			'dashicons-before dashicons-businessman',
			'dashicons-before dashicons-calendar-alt',
			'dashicons-before dashicons-calendar',
			'dashicons-before dashicons-camera',
			'dashicons-before dashicons-carrot',
			'dashicons-before dashicons-cart',
			'dashicons-before dashicons-category',
			'dashicons-before dashicons-chart-area',
			'dashicons-before dashicons-chart-bar',
			'dashicons-before dashicons-chart-line',
			'dashicons-before dashicons-chart-pie',
			'dashicons-before dashicons-clipboard',
			'dashicons-before dashicons-clock',
			'dashicons-before dashicons-cloud',
			'dashicons-before dashicons-controls-back',
			'dashicons-before dashicons-controls-forward',
			'dashicons-before dashicons-controls-pause',
			'dashicons-before dashicons-controls-play',
			'dashicons-before dashicons-controls-repeat',
			'dashicons-before dashicons-controls-skipback',
			'dashicons-before dashicons-controls-skipforward',
			'dashicons-before dashicons-controls-volumeoff',
			'dashicons-before dashicons-controls-volumeon',
			'dashicons-before dashicons-dashboard',
			'dashicons-before dashicons-desktop',
			'dashicons-before dashicons-dismiss',
			'dashicons-before dashicons-download',
			'dashicons-before dashicons-edit',
			'dashicons-before dashicons-editor-aligncenter',
			'dashicons-before dashicons-editor-alignleft',
			'dashicons-before dashicons-editor-alignright',
			'dashicons-before dashicons-editor-bold',
			'dashicons-before dashicons-editor-break',
			'dashicons-before dashicons-editor-code',
			'dashicons-before dashicons-editor-contract',
			'dashicons-before dashicons-editor-customchar',
			'dashicons-before dashicons-editor-expand',
			'dashicons-before dashicons-editor-help',
			'dashicons-before dashicons-editor-indent',
			'dashicons-before dashicons-editor-insertmore',
			'dashicons-before dashicons-editor-italic',
			'dashicons-before dashicons-editor-justify',
			'dashicons-before dashicons-editor-kitchensink',
			'dashicons-before dashicons-editor-ltr',
			'dashicons-before dashicons-editor-ol',
			'dashicons-before dashicons-editor-outdent',
			'dashicons-before dashicons-editor-paragraph',
			'dashicons-before dashicons-editor-paste-text',
			'dashicons-before dashicons-editor-paste-word',
			'dashicons-before dashicons-editor-quote',
			'dashicons-before dashicons-editor-removeformatting',
			'dashicons-before dashicons-editor-rtl',
			'dashicons-before dashicons-editor-spellcheck',
			'dashicons-before dashicons-editor-strikethrough',
			'dashicons-before dashicons-editor-table',
			'dashicons-before dashicons-editor-textcolor',
			'dashicons-before dashicons-editor-ul',
			'dashicons-before dashicons-editor-underline',
			'dashicons-before dashicons-editor-unlink',
			'dashicons-before dashicons-editor-video',
			'dashicons-before dashicons-email-alt',
			// 'dashicons-before dashicons-email-alt2',
			'dashicons-before dashicons-email',
			'dashicons-before dashicons-excerpt-view',
			'dashicons-before dashicons-external',
			'dashicons-before dashicons-facebook-alt',
			'dashicons-before dashicons-facebook',
			'dashicons-before dashicons-feedback',
			'dashicons-before dashicons-filter',
			'dashicons-before dashicons-flag',
			'dashicons-before dashicons-format-aside',
			'dashicons-before dashicons-format-audio',
			'dashicons-before dashicons-format-chat',
			'dashicons-before dashicons-format-gallery',
			'dashicons-before dashicons-format-image',
			'dashicons-before dashicons-format-quote',
			'dashicons-before dashicons-format-status',
			'dashicons-before dashicons-format-video',
			'dashicons-before dashicons-forms',
			'dashicons-before dashicons-googleplus',
			'dashicons-before dashicons-grid-view',
			'dashicons-before dashicons-groups',
			'dashicons-before dashicons-hammer',
			'dashicons-before dashicons-heart',
			'dashicons-before dashicons-hidden',
			'dashicons-before dashicons-id-alt',
			'dashicons-before dashicons-id',
			'dashicons-before dashicons-image-crop',
			'dashicons-before dashicons-image-filter',
			'dashicons-before dashicons-image-flip-horizontal',
			'dashicons-before dashicons-image-flip-vertical',
			'dashicons-before dashicons-image-rotate-left',
			'dashicons-before dashicons-image-rotate-right',
			'dashicons-before dashicons-image-rotate',
			'dashicons-before dashicons-images-alt',
			'dashicons-before dashicons-images-alt2',
			'dashicons-before dashicons-index-card',
			'dashicons-before dashicons-info',
			'dashicons-before dashicons-laptop',
			'dashicons-before dashicons-layout',
			'dashicons-before dashicons-leftright',
			'dashicons-before dashicons-lightbulb',
			'dashicons-before dashicons-list-view',
			'dashicons-before dashicons-location-alt',
			'dashicons-before dashicons-location',
			'dashicons-before dashicons-lock',
			'dashicons-before dashicons-marker',
			'dashicons-before dashicons-media-archive',
			'dashicons-before dashicons-media-audio',
			'dashicons-before dashicons-media-code',
			'dashicons-before dashicons-media-default',
			'dashicons-before dashicons-media-document',
			'dashicons-before dashicons-media-interactive',
			'dashicons-before dashicons-media-spreadsheet',
			'dashicons-before dashicons-media-text',
			'dashicons-before dashicons-media-video',
			'dashicons-before dashicons-megaphone',
			// 'dashicons-before dashicons-menu-alt',
			'dashicons-before dashicons-menu',
			'dashicons-before dashicons-microphone',
			'dashicons-before dashicons-migrate',
			'dashicons-before dashicons-minus',
			'dashicons-before dashicons-money',
			'dashicons-before dashicons-move',
			'dashicons-before dashicons-nametag',
			'dashicons-before dashicons-networking',
			'dashicons-before dashicons-no-alt',
			'dashicons-before dashicons-no',
			'dashicons-before dashicons-palmtree',
			'dashicons-before dashicons-paperclip',
			'dashicons-before dashicons-performance',
			'dashicons-before dashicons-phone',
			'dashicons-before dashicons-playlist-audio',
			'dashicons-before dashicons-playlist-video',
			'dashicons-before dashicons-plus-alt',
			'dashicons-before dashicons-plus-light',
			'dashicons-before dashicons-plus',
			'dashicons-before dashicons-portfolio',
			'dashicons-before dashicons-post-status',
			'dashicons-before dashicons-pressthis',
			'dashicons-before dashicons-products',
			'dashicons-before dashicons-randomize',
			'dashicons-before dashicons-redo',
			// 'dashicons-before dashicons-rest-api',
			'dashicons-before dashicons-rss',
			'dashicons-before dashicons-schedule',
			'dashicons-before dashicons-screenoptions',
			'dashicons-before dashicons-search',
			'dashicons-before dashicons-share-alt',
			'dashicons-before dashicons-share-alt2',
			'dashicons-before dashicons-share',
			'dashicons-before dashicons-shield-alt',
			'dashicons-before dashicons-shield',
			'dashicons-before dashicons-slides',
			'dashicons-before dashicons-smartphone',
			'dashicons-before dashicons-smiley',
			'dashicons-before dashicons-sort',
			'dashicons-before dashicons-sos',
			'dashicons-before dashicons-star-empty',
			'dashicons-before dashicons-star-filled',
			'dashicons-before dashicons-star-half',
			'dashicons-before dashicons-sticky',
			'dashicons-before dashicons-store',
			'dashicons-before dashicons-tablet',
			'dashicons-before dashicons-tag',
			'dashicons-before dashicons-tagcloud',
			'dashicons-before dashicons-testimonial',
			'dashicons-before dashicons-text',
			'dashicons-before dashicons-thumbs-down',
			'dashicons-before dashicons-thumbs-up',
			'dashicons-before dashicons-tickets-alt',
			'dashicons-before dashicons-tickets',
			// 'dashicons-before dashicons-tide',
			'dashicons-before dashicons-translation',
			'dashicons-before dashicons-trash',
			'dashicons-before dashicons-twitter',
			'dashicons-before dashicons-undo',
			'dashicons-before dashicons-universal-access-alt',
			'dashicons-before dashicons-universal-access',
			'dashicons-before dashicons-unlock',
			'dashicons-before dashicons-update',
			'dashicons-before dashicons-upload',
			'dashicons-before dashicons-vault',
			'dashicons-before dashicons-video-alt',
			'dashicons-before dashicons-video-alt2',
			'dashicons-before dashicons-video-alt3',
			'dashicons-before dashicons-visibility',
			'dashicons-before dashicons-warning',
			'dashicons-before dashicons-welcome-add-page',
			'dashicons-before dashicons-welcome-comments',
			'dashicons-before dashicons-welcome-learn-more',
			'dashicons-before dashicons-welcome-view-site',
			'dashicons-before dashicons-welcome-widgets-menus',
			'dashicons-before dashicons-welcome-write-blog',
			'dashicons-before dashicons-wordpress-alt',
			'dashicons-before dashicons-wordpress',
			'dashicons-before dashicons-yes-alt',
			'dashicons-before dashicons-yes',
		));

	} // end get_icons_list;

	/**
	 * Returns a simples list of roles registered on WordPress
	 *
	 * @since 0.0.1
	 * @return array
	 */
	public function get_roles_list() {

		$list = array();

		$roles = get_editable_roles();

		foreach ($roles as $role_slug => $role) {

			$list[$role_slug] = $role['name'];

		} // end foreach;

		return $list;

	} // end get_roles_list;

	/**
	 * Returns the list of available plans on WP Ultimo
	 *
	 * @since 0.0.1
	 * @return array
	 */
	public function get_plans_list() {

		if (!class_exists('WP_Ultimo')) {
			return;
		} // end if;

		$list = array();

		$plans = WU_Plans::get_plans();

		foreach ($plans as $plan) {

			$list[$plan->id] = $plan->title;

		} // end foreach;

		return $list;

	}  // end get_plans_list;

	/**
	 * Renders the edit / add new page for Admin Pages
	 *
	 * @since 0.0.1
	 * @return void
	 */
	public function render_admin_pages_add_new_page() {

		if (isset($_GET['admin_page_id'])) {

			$admin_page = new WU_Admin_Page($_GET['admin_page_id']);

		} else {

			$admin_page = new WU_Admin_Page();

			if (!empty($_POST)) {

				$admin_page->set_attributes($_POST);

			} // end if;

		} // end if;

		wp_register_script('wu-apc', WP_Ultimo_APC()->get_asset('wu-admin-page-creator-admin.min.js', 'js'), array('jquery'), WP_Ultimo_APC()->version, true);

		do_action('wu_apc_localize_content_sources', $admin_page);

		$this->initialize_editors();

		wp_localize_script('wu-apc', 'wu_apc_settings', array(
			'active'                     => $admin_page->active,
			'menu_type'                  => $admin_page->menu_type ? $admin_page->menu_type : 'menu',
			'content_type'               => $admin_page->content_type,
			'limit_access'               => $admin_page->limit_access,
			'page_to_replace'            => $admin_page->page_to_replace,
			'replace_mode'               => $admin_page->replace_mode,
			'external_link_url'          => $admin_page->external_link_url,
			'widget_priority'            => $admin_page->widget_priority,
			'widget_position'            => $admin_page->widget_position,
			'widget_welcome'             => $admin_page->widget_welcome,
			'separator_before'           => $admin_page->separator_before,
			'separator_after'            => $admin_page->separator_after,
			'widget_welcome_dismissible' => $admin_page->id ? $admin_page->widget_welcome_dismissible : true,
			'apply_multiple_pages'       => $admin_page->apply_multiple_pages,
		));

		wp_enqueue_script('wu-apc');

		wp_enqueue_style('wu-apc', WP_Ultimo_APC()->get_asset('wu-admin-page-creator.min.css', 'css'), false);

		// Enqueue our JavaScriot
		WP_Ultimo_APC()->render('admin-pages/edit-admin-page', array(
			'admin_page'       => $admin_page,
			'module'           => $this,
			'icons_list'       => $this->get_icons_list(),
			'roles_list'       => $this->get_roles_list(),
			'plans_list'       => $this->get_plans_list(),
			'menu_list'        => $this->get_parent_sub_menu_options(),
			'menu_parent_list' => $this->get_parent_menu_options(),
		));

	} // end render_admin_pages_add_new_page;

	/**
	 * Handles the saving of Admin Pages
	 *
	 * @since 0.0.1
	 * @return void
	 */
	public function save_admin_page() {

		if (!isset($_POST['save_admin_page'])) {

			return;

		} // end if;

		if (!current_user_can($this->user_can_manage) || !wp_verify_nonce($_POST['_wpultimo_nonce'], 'saving_admin_page')) {

			wp_die(__('You don\'t have permission to access this page', 'wu-apc'));

		} // end if;

		// Get the plan
		$id = isset($_POST['admin_page_id']) ? (int) $_POST['admin_page_id'] : 0;

		// Get our Admin Page
		$admin_page = new WU_Admin_Page($id);

		// Error message
		$messages = array();

		/**
		 * Validations about price, only if free
		 */
		if (!isset($_POST['title']) || !$_POST['title']) {
			$messages[] = __('You must enter a title for the page.', 'wu-apc');
		} // end if;

		if ( $_POST['menu_type'] !== 'replace_submenu' && $_POST['menu_type'] !== 'replace' && $_POST['menu_type'] !== 'widget' && (!isset($_POST['menu_label']) || !$_POST['menu_label']) ) {
			$messages[] = __('You must enter a menu label for the page.', 'wu-apc');
		} // end if;

		// Return errors
		if (!empty($messages)) {
			WAPP_Admin_Notices()->add_message(implode('<br>', $messages), 'error', WP_Ultimo_APC()->is_network_active());
			return;
		} // end if;

		// Load Info
		$admin_page->set_attributes(array(
			'active'                     => isset($_POST['active']),
			'menu_label'                 => isset($_POST['menu_label']) ? sanitize_text_field($_POST['menu_label']) : $admin_page->menu_label,
			'menu_order'                 => isset($_POST['menu_order']) ? sanitize_text_field($_POST['menu_order']) : $admin_page->menu_order,
			'title'                      => sanitize_text_field($_POST['title']),
			'menu_icon'                  => sanitize_text_field($_POST['menu_icon']),
			'menu_type'                  => sanitize_text_field($_POST['menu_type']),
			'menu_parent'                => sanitize_text_field($_POST['menu_parent']),
			'content_type'               => $_POST['content_type'],
			'content'                    => $_POST['content'],
			'html_content'               => $_POST['html-content'],
			'css_content'                => $_POST['css-content'],
			'css_scripts'                => $_POST['css-scripts'],
			'js_content'                 => $_POST['js-content'],
			'js_scripts'                 => $_POST['js-scripts'],
			'plans'                      => isset($_POST['plans']) ? array_keys($_POST['plans']) : array(),
			'roles'                      => isset($_POST['roles']) ? array_keys($_POST['roles']) : array(),
			'excludes_sites'             => isset($_POST['excludes_sites']) ? $_POST['excludes_sites'] : array(),
			'target_users'               => isset($_POST['target_users']) ? $_POST['target_users'] : array(),
			'limit_access'               => isset($_POST['limit_access']),

			'slug_url'                   => sanitize_title_with_dashes($_POST['slug_url'], '', 'save'),
			'hidden_id'                  => $_POST['admin_page_id'],

			'display_title'              => isset($_POST['display_title']),
			'add_margin'                 => isset($_POST['add_margin']),
			'display_admin_notices'      => isset($_POST['display_admin_notices']),
			'show_welcome'               => isset($_POST['show_welcome']),

			'separator_before'           => isset($_POST['separator_before']),
			'separator_after'            => isset($_POST['separator_after']),

			'page_to_replace'            => $this->get_replace_page(),
			'replace_mode'               => $_POST['replace_mode'],

			'widget_position'            => $_POST['widget_position'],
			'widget_priority'            => $_POST['widget_priority'],
			'widget_welcome'             => isset($_POST['widget_welcome']),
			'widget_welcome_dismissible' => isset($_POST['widget_welcome_dismissible']),

			'display_page_main_site'     => isset($_POST['display_page_main_site']),

			'apply_multiple_pages'       => isset($_POST['apply_multiple_pages']),
		));

		$admin_page_id = $admin_page->save();

		$edit_menu_slug = WU_Admin_Pages_Standalone_Dependencies()->edit_menu_slug;

		do_action('wu_save_admin_page', $admin_page);

		// Redirect to the edit page
		if (is_multisite() && defined('WPAPP_IS_NETWORK') && WPAPP_IS_NETWORK) {

			wp_redirect(network_admin_url('admin.php?page=' . $edit_menu_slug . '&updated=1&admin_page_id=') . $admin_page_id);

		} else {

			wp_redirect(admin_url('admin.php?page=' . $edit_menu_slug . '&updated=1&admin_page_id=') . $admin_page_id);

		} // end if;

		exit;

	} // end save_admin_page;

	/**
	 * This function checks if page_to_replace are applying to multiple pages and returns the correct value.
	 *
	 * @since 1.7.8
	 * 
	 * @return void
	 */
	public function get_replace_page() {

		if(isset($_POST['page_to_replace']) && !isset($_POST['apply_multiple_pages'])) {

			return $_POST['page_to_replace'];

		} elseif (isset($_POST['pages_to_replace'])) {

			return $_POST['pages_to_replace'];

		} else {

			return array();

		} // end if;

	} // end get_replace_page;

	/**
	 * Returns a list of the supported content types
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public function get_editor_options() {

		return apply_filters('wu_admin_pages_get_editor_options', array(
			'normal' => array(
				'label'  => __('Use Normal WordPress Editor', 'wu-apc'),
				'icon'   => 'dashicons dashicons-wordpress',
				'title'  => '',
				'active' => true,
			),
			'html'   => array(
				'label'  => __('Use HTML Editor (Advanced)', 'wu-apc'),
				'icon'   => 'dashicons dashicons-editor-code',
				'title'  => '',
				'active' => true,
			),
		));

	} // end get_editor_options;

	/**
	 * Search for sites for the targeting of messages
	 *
	 * @return void
	 */
	public function query_sites() {

		if (!current_user_can($this->user_can_manage)) {

			wp_die(__('You do not have the necessary permissions to perform this action.', 'wu-apc'));

		} // end if;

		if (isset($_GET['term_sites']) && preg_match('/\\d/', $_GET['term_sites']) > 0 ) {

			$terms         = explode(',', $_GET['term_sites']);
			$network_sites = array();

			foreach ($terms as $term) {
				array_push($network_sites, get_blog_details($term));
			} // end foreach;

		} elseif (!is_numeric($_GET['term_sites'])) {

			// is search text
			$args = array(
				'search'        => $_GET['term_sites'],
				'no_found_rows' => false,
				'number'        => 20,
			);

			$qsites        = new WP_Site_Query( $args );
			$network_sites = $qsites->sites;

			foreach ($network_sites as &$each_site) {
				$each_site             = $each_site->to_array(); // WP_site class to array pure
				$each_site['blogname'] = get_blog_details($each_site['blog_id'])->blogname;
			} // end foreach;

		} else {

			$network_sites = __('Something went wrong', 'wu-apc');

		} // end if;

		$network_sites = array_filter($network_sites);

		if (count($network_sites) > 0 ) {

			echo json_encode($network_sites);

		} else {
			return;
		} // end if;

		exit;

	}  // end query_sites;

	/**
	 * Search for users targeting
	 *
	 * @return void
	 */
	public function callback_target_users() {

		if (!current_user_can('edit_posts')) {

			wp_die(__('You do not have the necessary permissions to perform this action.', 'wu-apc'));

		} // end if;

		if (isset($_GET['term_users'])) {

			$terms = explode(',', $_GET['term_users']);
			$users = array();

			foreach ($terms as $term) {

				if (!is_numeric($term)) {

					$args = array(
						'search' => '*' . esc_attr( $term ) . '*',
					);

					$wp_user_query = new WP_User_Query($args);

					$result = $wp_user_query->get_results();

					$users = array_merge($users, $result);

				} else {

					array_push($users, get_userdata($term));

				} // end if;

			} // end foreach;

		} else {

			$users = __('Something went wrong', 'wu-apc');

		} // end if;

		$users = array_filter($users);

		if (count($users) > 0 ) {

			wp_send_json($users);

		} else {

			wp_send_json(array());

		} // end if;

	}  // end callback_target_users;

	/**
	 * Select 2, to be used when necessary
	 */
	public function enqueue_select2() {

		wp_register_style('wu-select2css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/3.4.8/select2.css', false, '1.0', 'all');
		wp_register_script('wu-select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/3.4.8/select2.js', array('jquery'), '1.0', true);

		wp_enqueue_style('wu-select2css');
		wp_enqueue_script('wu-select2');

	}  // end enqueue_select2;

	/**
	 * Set attribute disable
	 *
	 * @since 1.7.1
	 *
	 * @param WU_Admin_Page $admin_page Admin page.
	 * @param int           $bool Boolean.
	 * @return void
	 */
	public function set_is_active($admin_page, $bool) {

		$admin_page->active = $bool;
		$admin_page->set_attributes(array('active' => $bool));
		$admin_page->save();

	}  // end set_is_active;

} // end class WU_Admin_Pages;

/**
 * Returns an instance of this class
 *
 * @since 1.1.0
 * @return WU_Admin_Pages
 */
function WU_Admin_Pages() { // phpcs:ignore

	return WU_Admin_Pages::get_instance();

} // end WU_Admin_Pages;

// Run it
WU_Admin_Pages();
