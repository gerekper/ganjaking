<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Admin.
 *
 * Starts up all the admin things.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
class Admin_Controller {

	/**
	 * The instance.
	 *
	 * @var Admin_Controller
	 *
	 * @since 2.0.0
	 */
	protected static $_instance = null;


	/**
	 * If the init method has been called.
	 *
	 * @var bool
	 *
	 * @since 2.0.0
	 */
	protected $initialized = false;


	/**
	 * The main menu hook.
	 *
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public $intro_hook = '';


	/**
	 * The snippets menu hook.
	 *
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public $menu_settings_hook = '';


	/**
	 * The support menu hook.
	 *
	 * @since 2.3.0
	 *
	 * @var string
	 */
	public $menu_support_hook = '';


	/**
	 * The globalsnippets menu hook.
	 *
	 * @since 2.4.0
	 *
	 * @var string
	 */
	public $menu_globalsnippets_hook = '';


	/**
	 * The uninstall page hook.
	 *
	 * @since 2.13.1
	 *
	 * @var string
	 */
	public $menu_uninstall_hook = '';


	/**
	 * Get the singleton instance.
	 *
	 * Creates a new instance of the class if it does not exists.
	 *
	 * @return   Admin_Controller
	 *
	 * @since 2.0.0
	 */
	public static function instance() {

		if ( null === self::$_instance ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}


	/**
	 * Magic function for cloning.
	 *
	 * Disallow cloning as this is a singleton class.
	 *
	 * @since 2.0.0
	 */
	protected function __clone() {
	}


	/**
	 * Magic method for setting upt the class.
	 *
	 * Disallow external instances.
	 *
	 * @since 2.0.0
	 */
	protected function __construct() {
	}


	/**
	 * @param $name
	 * @param $arguments
	 *
	 * @return bool|mixed
	 * @since 2.0.0
	 *
	 */
	public static function __callStatic( $name, $arguments ) {

		$instance = self::instance();

		if ( method_exists( self::instance(), $name ) ) {
			return call_user_func_array( array( $instance, $name ), $arguments );
		}

		return false;
	}


	/**
	 * Initializes admin stuff
	 *
	 * @since 2.0.0
	 */
	public function init() {

		if ( $this->initialized ) {
			return;
		}

		/**
		 * Admin Controller Init Action.
		 *
		 * Allows plugins to hook into the init process of the Admin_Controller class.
		 *
		 * @hook  wpbuddy/rich_snippets/admin/init
		 *
		 * @param {Admin_Controller} $admin_controller
		 *
		 * @since 2.0.0
		 */
		do_action( 'wpbuddy/rich_snippets/admin/init', $this );


		# creates dashboard menu items
		# @note that this priority must be 9 or lower so that custom post types do not replace it.
		# @see show_in_menu arg @see https://codex.wordpress.org/Function_Reference/register_post_type
		add_action( 'admin_menu', array( self::$_instance, 'menu' ), 9 );

		# add scripts and styles to admin intro page
		add_action( 'wpbuddy/rich_snippets/admin_menu_intro', array( self::instance(), 'menu_intro_scripts' ) );

		add_action( 'admin_enqueue_scripts', array( self::instance(), 'admin_scripts' ) );

		add_action( 'load-post.php', array( self::$_instance, 'load_controllers' ) );
		add_action( 'load-plugins.php', array( self::$_instance, 'load_controllers' ) );
		add_action( 'load-post-new.php', array( self::$_instance, 'load_controllers' ) );
		add_action( 'load-edit.php', array( self::$_instance, 'load_controllers' ) );
		add_action( 'load-options.php', array( self::$_instance, 'load_controllers' ) );
		add_action( 'wpbuddy/rich_snippets/admin_menu', function () {

			$admin = Admin_Controller::instance();
			add_action( 'load-' . $admin->menu_settings_hook, array( $admin, 'load_controllers' ) );
			add_action( 'load-' . $admin->menu_support_hook, array( $admin, 'load_controllers' ) );
			add_action( 'load-' . $admin->menu_uninstall_hook, array( $admin, 'load_controllers' ) );
		} );


		add_action( 'save_post', array( self::$_instance, 'save_snippets' ), 10, 1 );

		add_action( 'plugins_loaded', array( self::instance(), 'load_translations' ) );

		add_filter( 'add_menu_classes', array( self::$_instance, 'dashboard_menu_classes' ) );

		add_action( 'admin_footer', array( self::$_instance, 'error_footer' ) );

		add_filter( 'debug_information', [ self::$_instance, 'site_health_info' ] );

		add_filter( 'site_status_tests', [ self::$_instance, 'site_status_tests' ] );

		/**
		 * Admin Controller Initialized Action.
		 *
		 * Allows plugins to hook into the Admin Controller class after it has been initialized.
		 *
		 * @hook  wpbuddy/rich_snippets/admin/initialized
		 *
		 * @param {Admin_Controller} $admin_controller
		 *
		 * @since 2.0.0
		 */
		do_action( 'wpbuddy/rich_snippets/admin/initialized', $this );

		$this->initialized = true;
	}


	/**
	 * Creates the main menu.
	 *
	 * @since 2.0.0
	 */
	public function menu() {

		/**
		 * Main Menu capability filter.
		 *
		 * Allows to change the capability for the main menu.
		 *
		 * @hook  wpbuddy/rich_snippets/capability_main_menu
		 *
		 * @param {string} $capability The capability (default: manage_options)
		 * @returns {string} The capability.
		 *
		 * @since 2.0.0
		 */
		$capability = apply_filters( 'wpbuddy/rich_snippets/capability_main_menu', 'manage_options' );

		$this->intro_hook = add_menu_page(
			_x( 'Rich Snippets', 'Main page title', 'rich-snippets-schema' ),
			_x( 'snip | Structured Data', 'Main menu title', 'rich-snippets-schema' ),
			$capability,
			'rich-snippets-schema',
			array( 'wpbuddy\rich_snippets\View', 'admin-intro' ),
			'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" width="1041" height="1013.031" viewBox="0 0 1041 1013.031"><path fill="#fff" d="M332.672,244.708q-97.679,78.74-97.667,206.327,0,127.611,80.175,184.456,80.152,56.867,236.151,71.448v35a907.63,907.63,0,0,1-175.656-16.769Q290.4,708.42,249.582,687.984V1004.4l33.528,13.12q33.516,13.125,125.364,26.25t209.912,13.12q221.564,0,310.5-71.445,88.9-71.427,88.919-191.017,0-119.544-67.053-164.77-67.067-45.18-247.813-62.7v-35q196.793,0,295.918,51.035V218.462l-33.528-13.124q-34.984-13.124-127.551-26.246-92.587-13.124-207.725-13.124Q430.317,165.968,332.672,244.708Z" transform="translate(-235 -165.969)"></path><rect fill="#fff" x="16" y="973.031" width="1025" height="40"></rect></svg>' )
		);


		/**
		 * Activation Menu capability filter.
		 *
		 * Allows to change the capability for the activation submenu.
		 *
		 * @hook  wpbuddy/rich_snippets/capability_menu_activation
		 *
		 * @param {string} $capability The capability (default: manage_options)
		 * @returns {string} The capability.
		 *
		 * @since 2.0.0
		 */
		$capability = apply_filters( 'wpbuddy/rich_snippets/capability_menu_activation', 'manage_options' );

		add_submenu_page(
			'rich-snippets-schema',
			_x( 'Let\'s start', 'Main page title', 'rich-snippets-schema' ),
			_x( 'Let\'s start', 'First menu title: Lets start', 'rich-snippets-schema' ),
			$capability,
			'rich-snippets-schema',
			array( 'wpbuddy\rich_snippets\View', 'admin-intro' )
		);

//		$hook = add_submenu_page(
//			'rich-snippets-schema',
//			'',
//			_x( 'Setup Service', 'Menu title: Setup Service', 'rich-snippets-schema' ) . '<span class="dashicons dashicons-external"></span>',
//			$capability,
//			'rich-snippets-setupservice',
//			function () {
//			}
//		);
//
//		add_action( 'load-' . $hook, function () {
//			$url = Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/setup-service/', 'plugin-submenu' );
//			wp_redirect( $url );
//		} );

		/**
		 * Admin Menu Intro action.
		 *
		 * Allows to fire code after the main menu has been added.
		 *
		 * @hook  wpbuddy/rich_snippets/admin_menu_intro
		 *
		 * @param {string} $hook The main menu hook name.
		 *
		 * @since 2.0.0
		 */
		do_action( 'wpbuddy/rich_snippets/admin_menu_intro', $this->intro_hook );


		/**
		 * Settings menu capability filter.
		 *
		 * Allows to change the capability for the settings submenu.
		 *
		 * @hook  wpbuddy/rich_snippets/capability_menu_settings
		 *
		 * @param {string} $capability The capability (default: manage_options)
		 * @returns {string} The capability.
		 *
		 * @since 2.0.0
		 */
		$capability = apply_filters( 'wpbuddy/rich_snippets/capability_menu_settings', 'manage_options' );

		$this->menu_settings_hook = add_submenu_page(
			'rich-snippets-schema',
			_x( 'Settings', 'Settings page title', 'rich-snippets-schema' ),
			_x( 'Settings', 'Settings menu title', 'rich-snippets-schema' ),
			$capability,
			'rich-snippets-settings',
			array( 'wpbuddy\rich_snippets\View', 'admin-settings' )
		);


		/**
		 * Admin Menu Settings action.
		 *
		 * Allows to fire code after the settings menu has been added.
		 *
		 * @hook  wpbuddy/rich_snippets/admin_menu_settings
		 *
		 * @param {string} $hook The settings menu hook name.
		 *
		 * @since 2.0.0
		 */
		do_action( 'wpbuddy/rich_snippets/admin_menu_settings', $this->menu_settings_hook );


		/**
		 * Settings menu capability filter.
		 *
		 * Allows to change the capability for the settings submenu.
		 *
		 * @hook  wpbuddy/rich_snippets/capability_menu_support
		 *
		 * @param {string} $capability The capability (default: manage_options)
		 * @returns {string} The capability.
		 *
		 * @since 2.0.0
		 */
		$capability = apply_filters( 'wpbuddy/rich_snippets/capability_menu_support', 'manage_options' );

		$this->menu_support_hook = add_submenu_page(
			'rich-snippets-schema',
			_x( 'Support', 'Support page title', 'rich-snippets-schema' ),
			_x( 'Support', 'Support menu title', 'rich-snippets-schema' ),
			$capability,
			'rich-snippets-support',
			array( 'wpbuddy\rich_snippets\View', 'admin-support' )
		);

		/**
		 * Admin Menu Support action.
		 *
		 * Allows to fire code after the support menu has been added.
		 *
		 * @hook  wpbuddy/rich_snippets/admin_menu_support
		 *
		 * @param {string} $hook The support menu hook name.
		 *
		 * @since 2.0.0
		 */
		do_action( 'wpbuddy/rich_snippets/admin_menu_support', $this->menu_support_hook );

		$this->menu_uninstall_hook = add_dashboard_page(
			'',
			'',
			'manage_options',
			'rich-snippets-uninstall',
			'ask_page'
		);

		/**
		 * Admin Menu action.
		 *
		 * Allows to fire code after all menu items have been added.
		 *
		 * @hook  wpbuddy/rich_snippets/admin_menu
		 *
		 * @param {Admin_Controller} $admin_controller The Admin Controller object passed by reference.
		 *
		 * @since 2.0.0
		 */
		do_action_ref_array( 'wpbuddy/rich_snippets/admin_menu', [ $this ] );
	}


	/**
	 * Adds scripts and styles to the intro admin page.
	 *
	 * @param string $hook
	 *
	 * @since 2.0.0
	 */
	public function menu_intro_scripts( $hook ) {

		add_action( 'admin_enqueue_scripts', function ( $hook_suffix ) {

			if ( 'toplevel_page_rich-snippets-schema' !== $hook_suffix ) {
				return;
			}

			wp_enqueue_style(
				'wpb-rs-admin-intro',
				plugins_url( 'css/admin-intro.css', rich_snippets()->get_plugin_file() ),
				array( 'common' ),
				filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'css/admin-intro.css' )
			);

			wp_enqueue_script(
				'wpb-rs-confetti',
				plugins_url( 'js/confetti.browser.min.js', rich_snippets()->get_plugin_file() ),
				[],
				filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'js/confetti.browser.min.js' )
			);

			wp_enqueue_script(
				'wpb-rs-admin-intro',
				plugins_url( 'js/admin-intro.js', rich_snippets()->get_plugin_file() ),
				[ 'wpb-rs-confetti' ],
				filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'js/admin-intro.js' )
			);

			$args = call_user_func( function () {

				$o               = new \stdClass();
				$o->nonce        = wp_create_nonce( 'wp_rest' );
				$o->rest_url     = untrailingslashit( rest_url( 'wpbuddy/rich_snippets/v1' ) );
				$o->redirect_url = admin_url( 'admin.php?page=rich-snippets-schema&tab=updates' );

				$o->translations                            = new \stdClass();
				$o->translations->activated                 = __( 'Hurray! Your copy of this plugins is active! Please wait ...', 'rich-snippets-schema' );
				$o->translations->activation_no_content_err = __( 'The request did not return any data. Error code: %d.', 'rich-snippets-schema' );

				return $o;
			} );

			wp_add_inline_script( 'wpb-rs-admin-intro', "var WPB_RS_ADMIN = " . \json_encode( $args ) . ";", 'before' );

		} );
	}


	/**
	 * Loads other controllers, if necessary.
	 *
	 * @since 2.0.0
	 */
	public function load_controllers() {

		$post_type = Helper_Model::instance()->get_current_admin_post_type();

		$post_types = (array) get_option( 'wpb_rs/setting/post_types', array( 'post', 'page' ) );

		$post_types[] = 'wpb-rs-global';

		# on all post types including wpb-rs-global
		if ( in_array( $post_type, $post_types ) ) {
			$this->init_admin_snippets_controller();
		}

		$is_settings_page = call_user_func( function () {

			if ( ! function_exists( 'get_current_screen' ) ) {
				return false;
			}

			$screen = get_current_screen();

			if ( $screen->id === 'options' ) {
				return true;
			}

			if ( $screen->id === $this->menu_settings_hook ) {
				return true;
			}

			return false;
		} );

		# only on settings page
		if ( $is_settings_page ) {
			$this->init_settings_controller();
		}

		$is_support_page = call_user_func( function () {

			if ( ! function_exists( 'get_current_screen' ) ) {
				return false;
			}

			$screen = get_current_screen();

			if ( $screen->id === 'options' ) {
				return true;
			}

			if ( $screen->id === $this->menu_support_hook ) {
				return true;
			}

			return false;
		} );

		# only on support page
		if ( $is_support_page ) {
			$this->init_admin_support_controller();
		}

		$is_plugin_page = call_user_func( function () {

			if ( ! function_exists( 'get_current_screen' ) ) {
				return false;
			}

			$screen = get_current_screen();

			return $screen->id === 'plugins';
		} );

		if ( $is_plugin_page ) {
			new Admin_Plugins_Controller();
		}

		$is_uninstall_page = call_user_func( function () {

			if ( ! function_exists( 'get_current_screen' ) ) {
				return false;
			}

			$screen = get_current_screen();

			return $screen->id === $this->menu_uninstall_hook;
		} );

		if ( $is_uninstall_page ) {
			new Admin_Uninstall_Controller();
		}

		/**
		 * Admin Load Controller.
		 *
		 * Allows plugins to hook into the Admin Controller class after more controllers have been loaded.
		 *
		 * @hook  wpbuddy/rich_snippets/admin_load_controllers
		 *
		 * @param {Admin_Controller} $admin_controller
		 *
		 * @since 2.0.0
		 */
		do_action( 'wpbuddy/rich_snippets/admin_load_controllers', $this );
	}


	/**
	 * Adds scripts and styles for all admin pages.
	 *
	 * @since 2.0.0
	 */
	public function admin_scripts() {

		wp_enqueue_style(
			'wpb-rs-admin',
			plugins_url( 'css/admin.css', rich_snippets()->get_plugin_file() ),
			array( 'admin-menu' ),
			filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'css/admin.css' )
		);
	}


	/**
	 * Saves a schema.org post.
	 *
	 * @param int $post_id
	 *
	 * @see   Admin_Snippets_Controller::save_snippets()
	 *
	 * @since 2.0.0
	 *
	 */
	public function save_snippets( $post_id ) {

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		Admin_Snippets_Controller::instance()->save_snippets( $post_id );
	}


	/**
	 * Loads translation files.
	 *
	 * @since 2.0.0
	 */
	public function load_translations() {

		$rel_path = str_replace(
			WP_PLUGIN_DIR,
			'',
			dirname( rich_snippets()->get_plugin_file() )
		);

		load_plugin_textdomain( 'rich-snippets-schema', false, $rel_path . '/languages' );
	}


	/**
	 * Adds a CSS class to the Rich Snippet menu (to modify the SVG icon)
	 *
	 * @param array $menu
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public function dashboard_menu_classes( $menu ) {

		foreach ( $menu as $order => $m ) {
			if ( ! isset( $m[2] ) ) {
				continue;
			}

			if ( 'rich-snippets-schema' === $m[2] ) {
				$menu[ $order ][4] .= ' wpb-rs-dashboard-menu ';
			}
		}

		return $menu;
	}


	/**
	 * Prints a DIV for error reporting.
	 *
	 * @since 2.0.0
	 * @since 2.2.0 Moved from Admin_Snippets_Controller class.
	 */
	public function error_footer() {

		printf( '<div class="wpb-rs-errors"></div>' );
	}


	/**
	 * Shows additional information for debugging snip in the WP Health check.
	 *
	 * @param array $info
	 *
	 * @return array
	 *
	 * @since 2.14.15
	 */
	public function site_health_info( $info ) {

		global $wpdb;

		$postmeta_row = $wpdb->get_row( "SHOW COLUMNS FROM {$wpdb->postmeta} LIKE 'meta_key'" );

		$info['snip'] = [
			'label'  => 'SNIP',
			'fields' => [
				[
					'label' => __( 'PHP Max Input Vars', 'rich-snippets-schema' ),
					'value' => function_exists( 'ini_get' ) ? ini_get( 'max_input_vars' ) : __( 'Cannot read this information.', 'rich-snippets-schema' )
				],
				[
					'label' => __( 'Post meta key type', 'rich-snippets-schema' ),
					'value' => isset( $postmeta_row->Type ) ? $postmeta_row->Type : __( 'Cannot read this information.', 'rich-snippets-schema' )
				]
			],
		];

		return $info;
	}


	/**
	 * Adds status tests to the SiteHealth page.
	 *
	 * @param array $tests
	 *
	 * @return array
	 * @since 2.14.25
	 */
	public function site_status_tests( $tests ) {

		$tests['direct']['snip_php'] = [
			'label' => __( 'SNIP: PHP Version', 'rich-snippets-schema' ),
			'test'  => function () {

				$future_php_version = '7.4.0';

				$results = [
					'actions'     => '',
					'badge'       => [
						'color' => 'blue',
						'label' => __( 'Plugin issue', 'rich-snippets-schema' ),
					],
					'description' => sprintf(
						'<p>%s</p>',
						sprintf(
							__( 'Future versions of SNIP will require a PHP version of %s. You are currently running version %s of PHP. <a href="https://wordpress.org/support/update-php/" target="_blank">Read more about updating your PHP version here.</a>', 'rich-snippets-schema' ),
							$future_php_version,
							PHP_VERSION
						)
					),
					'label'       => __( 'Your site is not ready to run future versions of SNIP.', 'rich-snippets-schema' ),
					'status'      => 'good',
					'test'        => '',
				];

				if ( version_compare( PHP_VERSION, $future_php_version, '<' ) ) {
					$results['status'] = 'critical';
				}

				return $results;
			}
		];

		$tests['direct']['snip_wp'] = [
			'label' => __( 'SNIP: WordPress Version', 'rich-snippets-schema' ),
			'test'  => function () {
				global $wp_version;

				$futuer_wp_version = '5.3.0';

				$results = [
					'actions'     => '',
					'badge'       => [
						'color' => 'blue',
						'label' => __( 'Plugin issue', 'rich-snippets-schema' ),
					],
					'description' => sprintf(
						'<p>%s</p>',
						sprintf(
							__( 'Future versions of SNIP will require a WordPress version of at least %s. You are currently running version %s of WordPress. Please update soon.', 'rich-snippets-schema' ),
							$futuer_wp_version,
							$wp_version
						)
					),
					'label'       => __( 'Your site is not ready to run future versions of SNIP.', 'rich-snippets-schema' ),
					'status'      => 'good',
					'test'        => '',
				];

				if ( version_compare( $wp_version, $futuer_wp_version, '<' ) ) {
					$results['status'] = 'critical';
				}

				return $results;
			}
		];

		return $tests;
	}

	/**
	 * Initializes the settings controller.
	 *
	 * @since 2.19.0
	 */
	public function init_settings_controller() {
		new Admin_Settings_Controller();
	}

	/**
	 * Initializes the admin support controller.
	 *
	 * @since 2.19.0
	 */
	public function init_admin_support_controller() {
		new Admin_Support_Controller();
	}

	/**
	 * Initializes the admin snippets controller.
	 *
	 * @since 2.19.0
	 */
	public function init_admin_snippets_controller() {
		Admin_Snippets_Controller::instance()->init();
	}
}
