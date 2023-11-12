<?php

/**
 * SearchWP OptionsView.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Admin;

use SearchWP\Admin\Views\AboutUsView;
use SearchWP\Admin\Views\GeneralSettingsView;
use SearchWP\Admin\Views\GettingStartedView;
use SearchWP\Admin\Views\GlobalRulesView;
use SearchWP\Admin\Views\ImportExportView;
use SearchWP\Admin\Views\MiscSettingsView;
use SearchWP\Admin\Views\ResultsPageView;
use SearchWP\Admin\Views\SystemInfoView;
use SearchWP\Utils;
use SearchWP\Settings;
use SearchWP\Statistics;
use SearchWP\Admin\Views\SearchFormsView;
use SearchWP\Admin\Views\EnginesView;
use SearchWP\Admin\Views\StatisticsView;
use SearchWP\Admin\Views\ExtensionsView;

/**
 * Class OptionsView is responsible for implementing the options screen into the WordPress Admin area.
 *
 * @since 4.0
 */
class OptionsView {

	/**
	 * Slug for this view.
	 *
	 * @since 4.0
     *
	 * @var string
	 */
	private static $slug;

	/**
	 * Extensions registry.
	 *
	 * @since 4.0
     *
	 * @var array
	 */
	private $extensions;

	/**
	 * OptionsView constructor.
	 *
	 * @since 4.0
	 */
	public function __construct() {

		self::$slug = Utils::$slug;

		self::hooks();
		$this->option_views();

		do_action( 'searchwp\settings\init' );
	}

	/**
	 * Run OptionsView hooks.
	 *
	 * @since 4.2.0
	 */
	private static function hooks() {

		add_action( 'admin_menu', [ __CLASS__, 'legacy_admin_pages_redirect' ] );

		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'assets' ] );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'legacy_extensions_assets' ] );

		add_action( 'admin_menu', [ __CLASS__, 'add_admin_menus' ] );
		add_action( 'admin_menu', [ __CLASS__, 'add_dashboard_stats_link' ] );

		add_action( 'network_admin_menu', [ __CLASS__, 'add_network_admin_menu' ] );

		add_filter( 'admin_body_class', [ __CLASS__, 'admin_body_class' ] );
		add_action( 'in_admin_header', [ __CLASS__, 'admin_header' ], 100 );
		add_action( 'admin_print_scripts', [ __CLASS__, 'admin_hide_unrelated_notices' ] );
	}

	/**
	 * Init option views.
	 *
	 * @since 4.2.0
	 */
	private function option_views() {

		// Add internal tabs.
		do_action( 'searchwp\settings\nav\before' );

		new SearchFormsView();

		if ( apply_filters( 'searchwp\settings\nav\engines', true ) ) {
			new EnginesView();
			do_action( 'searchwp\settings\nav\engines' );
		}

		new ImportExportView();

		new GeneralSettingsView();

		// Add Extensions Tab and callbacks.
		// TODO: Extension handling can (should) be its own class.
		$this->init_extensions();

        new MiscSettingsView();

        new ResultsPageView();

		if ( apply_filters( 'searchwp\settings\nav\statistics', true ) ) {
			new StatisticsView();
			do_action( 'searchwp\settings\nav\statistics' );
		}

		( new ExtensionsView() )->init();
		new GlobalRulesView();

        new SystemInfoView();

		new AboutUsView();
        new GettingStartedView();

		do_action( 'searchwp\settings\nav\after' );
    }

	/**
	 * Redirects options pages from legacy to updated URLs.
	 *
	 * @since 4.2.1
	 */
	public static function legacy_admin_pages_redirect() {

		global $pagenow;

		if ( $pagenow !== 'options-general.php' ) {
            return;
        }

		if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'searchwp' ) {
            return;
		}

        $url_query = wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_QUERY ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

		wp_safe_redirect( add_query_arg( [ 'page' => 'searchwp-algorithm' ], admin_url( 'admin.php?' . $url_query ) ), 301 );
		exit;
	}

	/**
	 * Enqueue the assets related to the OptionsView.
	 *
	 * @since 4.0
	 */
	public static function assets() {

		if ( ! Utils::is_swp_admin_page() ) {
			return;
		}

        self::register_framework_styles();
		self::register_framework_scripts();

        self::enqueue_framework_styles();

		wp_enqueue_style(
			self::$slug . '_admin_settings',
			SEARCHWP_PLUGIN_URL . 'assets/styles/admin/settings.css',
			[],
			SEARCHWP_VERSION
		);

		wp_enqueue_script( 'jquery' );
	}

	/**
	 * Register the styling framework styles.
	 *
	 * @since 4.3.0
	 */
    private static function register_framework_styles() {

	    $styles = [
	        'buttons',
	        'card',
	        'choicesjs',
	        'collapse-layout',
		    'color-picker',
	        'colors',
	        'draggable',
	        'header',
	        'input',
	        'layout',
	        'modal',
	        'nav-menu',
	        'pills',
			'radio-img',
	        'toggle-switch',
	        'tooltip',
	        'upload-file',
        ];

	    foreach ( $styles as $style ) {
		    wp_register_style(
			    self::$slug . $style,
			    SEARCHWP_PLUGIN_URL . 'assets/css/admin/framework/' . $style . '.css',
			    [],
			    SEARCHWP_VERSION
		    );
	    }
    }

	/**
	 * Register the styling framework scripts.
	 *
	 * @since 4.3.0
	 */
	private static function register_framework_scripts() {

		$scripts = [
            'choices',
            'collapse',
            'color-picker',
            'copy-input-text',
			'pills',
			'modal',
			'settings-toggle',
		];

		foreach ( $scripts as $script ) {
			wp_register_script(
				self::$slug . $script,
				SEARCHWP_PLUGIN_URL . 'assets/js/admin/components/' . $script . '.js',
				[ 'jquery' ],
				SEARCHWP_VERSION,
				true
			);
		}
    }

	/**
	 * Enqueue the styling framework styles.
	 *
	 * @since 4.3.0
	 */
	private static function enqueue_framework_styles() {

		wp_enqueue_style(
			self::$slug . 'page-header',
			SEARCHWP_PLUGIN_URL . 'assets/css/admin/framework/page-header.css',
			[],
			SEARCHWP_VERSION
		);

		wp_enqueue_style(
			self::$slug . 'style',
			SEARCHWP_PLUGIN_URL . 'assets/css/admin/framework/style.css',
			[
				self::$slug . 'colors',
				self::$slug . 'header',
				self::$slug . 'nav-menu',
				self::$slug . 'layout',
				self::$slug . 'buttons',
            ],
			SEARCHWP_VERSION
		);
	}

	/**
	 * Enqueue the assets of the extensions with changed parent settings page.
	 * Most of the extensions are waiting for a Settings page hook to load their assets.
	 * This method fires the hook on other SearchWP admin pages mimicking the Settings page loading.
	 *
	 * @param string $hook_suffix The current admin page.
	 *
	 * @since 4.3.0
	 */
	public static function legacy_extensions_assets( $hook_suffix ) {

		if ( $hook_suffix === 'settings_page_searchwp' ) {
			return;
		}

		if ( Utils::is_swp_admin_page( '', 'extensions' ) ) {
			do_action( 'admin_enqueue_scripts', 'settings_page_searchwp' );
		}
	}

	/**
	 * Add SearchWP admin menus.
	 *
	 * @since 4.2.0
	 */
	public static function add_admin_menus() {

		if ( ! apply_filters( 'searchwp\options\settings_screen', true ) ) {
			return;
		}

		if ( ! current_user_can( Settings::get_capability() ) ) {
			return;
		}

		$submenu_pages = self::get_submenu_pages_args();

		$page_title = esc_html__( 'SearchWP', 'searchwp' );
        $menu_page  = reset( $submenu_pages );

		// Default SearchWP top level menu item.
		add_menu_page(
			$page_title,
			$page_title,
			Settings::get_capability(),
			$menu_page['menu_slug'],
			[ __CLASS__, 'page' ],
			'data:image/svg+xml;base64,' . base64_encode( self::get_dashicon() ),
			apply_filters( 'searchwp\admin_menu\position', '58.95' )
		);

        foreach ( $submenu_pages as $submenu_page ) {
	        add_submenu_page(
		        $menu_page['menu_slug'],
		        $submenu_page['page_title'] ?? $page_title,
		        $submenu_page['menu_title'],
		        $submenu_page['capability'] ?? Settings::get_capability(),
		        $submenu_page['menu_slug'],
		        $submenu_page['function'] ?? [ __CLASS__, 'page' ]
	        );
        }
	}

	/**
	 * Get SearchWP dashicon SVG.
	 *
	 * @since 4.2.0
	 */
	private static function get_dashicon() {

		return '<svg width="50" height="61" fill="#f0f0f1" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" d="M9.57 13.259c-.959 0-1.782.68-1.946 1.625-.527 3.033-1.59 9.715-1.702 14.875-.114 5.288 1.134 13.417 1.712 16.864.16.952.984 1.636 1.95 1.636h30.683c.959 0 1.78-.675 1.945-1.619.584-3.339 1.823-11.12 1.71-16.381-.112-5.195-1.194-12.217-1.72-15.36a1.969 1.969 0 0 0-1.95-1.64zm2.728 5a.99.99 0 0 0-.986.873c-.237 2.012-.797 7.111-.89 11.127-.096 4.116.94 10.066 1.34 12.2.089.468.497.8.972.8h24.368a.983.983 0 0 0 .972-.799c.403-2.133 1.443-8.084 1.348-12.201-.094-4.016-.658-9.117-.897-11.128a.99.99 0 0 0-.987-.872z"/>
                  <path d="M34.564 36.765c.55-3.195.858-6.711.858-10.408a65.76 65.76 0 0 0-.09-3.416l-8.852 6.777zM24.92 31.013l-9.2 8.017a41.23 41.23 0 0 0 1.272 4.579c.978 2.835 2.141 3.732 3.34 4.021.636.154 1.327.149 2.105.096.215-.015.439-.034.668-.053.58-.048 1.198-.1 1.817-.1s1.237.052 1.816.1c.23.019.454.038.668.053.778.053 1.47.058 2.106-.096 1.198-.29 2.361-1.186 3.34-4.021.484-1.406.91-2.94 1.269-4.577zM23.363 29.716l-8.851-6.777c-.059 1.119-.09 2.259-.09 3.418 0 3.696.305 7.212.855 10.406zM31.53 11.759c-.405.004-.814.04-1.194.082l-.44.05c-.323.04-.623.076-.834.083a54.57 54.57 0 0 0-3.566.22l-.121.012a5.617 5.617 0 0 1-.453.031 1.34 1.34 0 0 1-.317-.05l-.213-.057-.117-.033a9.308 9.308 0 0 0-.97-.215c-.796-.13-1.91-.192-3.329.084-.312.06-.743.04-1.136.023l-.037-.002h-.008c-.434-.018-.886-.038-1.317-.005-.436.032-.8.117-1.072.273-.25.145-.438.36-.525.728-.548 2.32-.954 4.87-1.198 7.569l10.24 7.838 10.237-7.838c-.244-2.7-.65-5.248-1.197-7.569a1.311 1.311 0 0 0-.678-.902c-.351-.193-.818-.288-1.353-.314a6.888 6.888 0 0 0-.403-.008z"/>
                  <path d="M15.732 43.242h18.38a1.5 1.5 0 0 1 1.492 1.35l.6 6a1.5 1.5 0 0 1-1.492 1.65h-19.58a1.5 1.5 0 0 1-1.493-1.65l.6-6a1.5 1.5 0 0 1 1.493-1.35z"/>
                  <path d="M19.918 3.26c-1.087 0-2 .913-2 2v8.5a1.5 1.5 0 0 0 1.5 1.5h11a1.5 1.5 0 0 0 1.5-1.5v-8.5c0-1.087-.913-2-2-2zm1 3h8v6h-8z"/>
                  <path d="M17.918 8.759h14a1.5 1.5 0 0 1 1.5 1.5v4.5h-17v-4.5a1.5 1.5 0 0 1 1.5-1.5z"/>
                  <path d="M14.918 11.759h20a1.5 1.5 0 0 1 1.5 1.5v4.5h-23v-4.5a1.5 1.5 0 0 1 1.5-1.5zM11.43 50.759h26.983a1.5 1.5 0 0 1 1.442 1.088l.858 3a1.5 1.5 0 0 1-1.443 1.912H10.573a1.5 1.5 0 0 1-1.442-1.912l.857-3a1.5 1.5 0 0 1 1.442-1.088z"/>
                </svg>';
	}

	/**
     * Get arguments to populate the submenus.
     * Items are sorted by the 'position' value.
     *
     * @since 4.2.0
     *
	 * @return array
	 */
    private static function get_submenu_pages_args() {

	    $submenu_pages = [
		    'forms'  => [
			    'menu_title' => esc_html__( 'Search Forms', 'searchwp' ),
			    'menu_slug'  => 'searchwp-forms',
			    'position'   => 5,
		    ],
		    'algorithm'  => [
			    'menu_title' => esc_html__( 'Algorithm', 'searchwp' ),
			    'menu_slug'  => 'searchwp-algorithm',
			    'position'   => 10,
		    ],
		    'settings'   => [
			    'menu_title' => esc_html__( 'Settings', 'searchwp' ),
			    'menu_slug'  => 'searchwp-settings',
			    'position'   => 20,
		    ],
		    'templates'  => [
			    'menu_title' => esc_html__( 'Templates', 'searchwp' ),
			    'menu_slug'  => 'searchwp-templates',
			    'position'   => 20,
		    ],
		    'statistics' => [
			    'menu_title' => esc_html__( 'Statistics', 'searchwp' ),
			    'menu_slug'  => 'searchwp-statistics',
			    'position'   => 30,
		    ],
		    'extensions' => [
			    'menu_title' => '<span style="color:#1da867">' . esc_html__( 'Extensions', 'searchwp' ) . '</span>',
			    'menu_slug'  => 'searchwp-extensions',
			    'position'   => 40,
		    ],
		    'tools'      => [
			    'menu_title' => esc_html__( 'Tools', 'searchwp' ),
			    'menu_slug'  => 'searchwp-tools',
			    'position'   => 90,
		    ],
		    'about-us'   => [
			    'menu_title' => esc_html__( 'About Us', 'searchwp' ),
			    'menu_slug'  => 'searchwp-about-us',
			    'position'   => 100,
		    ],
	    ];

	    $submenu_pages = (array) apply_filters( 'searchwp\options\submenu_pages', $submenu_pages );

        return wp_list_sort( $submenu_pages, 'position', 'ASC', true );
    }

	/**
	 * Add Search Stats dashboard link.
	 *
	 * @since 4.2.0
	 */
	public static function add_dashboard_stats_link() {

		$user_can = current_user_can( Settings::get_capability() ) ||
		            current_user_can( Statistics::get_capability() );

		if ( ! apply_filters( 'searchwp\options\dashboard_stats_link', $user_can ) ) {
			return;
		}

		if ( current_user_can( Settings::get_capability() ) ) {
			self::add_stats_dashboard_page_options_redirect();

			return;
		}

		self::add_stats_dashboard_page_standalone();
	}

	/**
	 * Add Search Stats dashboard page and redirect it to Statistics page in plugin options menu.
	 *
	 * @since 4.2.0
	 */
	private static function add_stats_dashboard_page_options_redirect() {

		global $submenu;

		add_dashboard_page(
			__( 'Search Statistics', 'searchwp' ),
			__( 'Search Stats', 'searchwp' ),
			Statistics::get_capability(),
			self::$slug . '-stats'
		);

		if ( ! is_array( $submenu ) || ! array_key_exists( 'index.php', $submenu ) ) {
			return;
		}

		// Override the link for the Search Stats Admin Menu entry.
		foreach ( $submenu['index.php'] as $index => $dashboard_submenu ) {
			if ( $dashboard_submenu[2] !== 'searchwp-stats' ) {
				continue;
			}

			$submenu['index.php'][ $index ][2] = esc_url_raw( add_query_arg( [
				'page' => 'searchwp-statistics',
			], admin_url( 'admin.php' ) ) );

			break;
		}
	}

	/**
	 * Add Search Stats dashboard standalone page.
	 *
	 * @since 4.2.0
	 */
	private static function add_stats_dashboard_page_standalone() {

		$callback = static function() {
			wp_enqueue_style(
				self::$slug . '_admin_settings',
				SEARCHWP_PLUGIN_URL . 'assets/styles/admin/settings.css',
				false,
				SEARCHWP_VERSION
			);

			wp_enqueue_script( 'jquery' );
			?>
            <div class="searchwp-admin-wrap wrap">
                <div class="searchwp-settings-view">
                    <?php
                    do_action( 'searchwp\settings\view' );
                    do_action( 'searchwp\settings\after' );
                    ?>
                </div>
            </div>
			<?php
		};

		// Current user can view Statistics but not Settings.
		add_dashboard_page(
			__( 'Search Statistics', 'searchwp' ),
			__( 'Search Stats', 'searchwp' ),
			Statistics::get_capability(),
			self::$slug . '-stats',
			$callback
		);
	}

	/**
	 * Adds SearchWP network admin menu.
	 *
	 * @since 4.0
     *
	 * @return void
	 */
	public static function add_network_admin_menu() {

		$callback = static function() {
			do_action( 'searchwp\debug\log', 'Displaying network options page', 'settings' );
			?>
            <div class="wrap">
                <div style="max-width: 60em;">
                    <h1>SearchWP</h1>
					<?php
					echo wp_kses(
						__( '<p>Cross-site searches are possible in SearchWP. Any Engine from any site can be used for a cross-site search.</p><p><strong>Note</strong>: SearchWP\'s Engines control what is indexed on each sub-site. If the Engine you are using to perform the search has different Sources/Attributes/Rules than the Engine(s) on the sub-sites you are searching the <em>results may not be accurate</em>.</p><p>For example: if Posts have been added to the Engine you are using for the search, but a sub-site does not have an Engine with Posts enabled, <strong>that sub-site will not return Posts</strong>.</p><p>For a comprehensive cross-site search, ensure that <em>all sites</em> share a similar configuration and applicable Engine.</p><p><a href="https://searchwp.com/?p=288269" target="_blank">More information</a></p>', 'searchwp' ),
						[
							'p'      => [],
							'strong' => [],
							'em'     => [],
							'a'      => [
								'href'   => [],
								'target' => [],
							],
						]
					);
					?>
                </div>
            </div>
			<?php
		};

		add_menu_page(
			'SearchWP',
			'SearchWP',
			Settings::get_capability(),
			self::$slug,
			$callback,
			'data:image/svg+xml;base64,' . base64_encode( self::get_dashicon() )
		);
	}

	/**
	 * Add body class to SearchWP admin pages for easy reference.
	 *
	 * @since 4.2.0
	 *
	 * @param string $classes CSS classes, space separated.
	 *
	 * @return string
	 */
	public static function admin_body_class( $classes ) {

		if ( ! Utils::is_swp_admin_page() ) {
			return $classes;
		}

		return "$classes searchwp-admin-page";
	}

	/**
	 * Output the SearchWP admin header.
	 *
	 * @since 4.2.0
	 */
	public static function admin_header() {

		// Bail if we're not on a SearchWP screen or page.
		if ( ! Utils::is_swp_admin_page() ) {
			return;
		}

		if ( ! apply_filters( 'searchwp\settings\header', true ) ) {
			return;
		}

		self::header();
	}

	/**
	 * Remove non-SearchWP notices from SearchWP pages.
	 *
	 * @since 4.2.0
	 */
	public static function admin_hide_unrelated_notices() {

		if ( ! Utils::is_swp_admin_page() ) {
			return;
		}

		global $wp_filter;

		// Define rules to remove callbacks.
		$rules = [
			'user_admin_notices' => [], // remove all callbacks.
			'admin_notices'      => [],
			'all_admin_notices'  => [],
			'admin_footer'       => [
				'render_delayed_admin_notices', // remove this particular callback.
			],
		];

		$notice_types = array_keys( $rules );

		foreach ( $notice_types as $notice_type ) {
			if ( empty( $wp_filter[ $notice_type ]->callbacks ) || ! is_array( $wp_filter[ $notice_type ]->callbacks ) ) {
				continue;
			}

			$remove_all_filters = empty( $rules[ $notice_type ] );

			foreach ( $wp_filter[ $notice_type ]->callbacks as $priority => $hooks ) {
				foreach ( $hooks as $name => $arr ) {
					if ( is_object( $arr['function'] ) && is_callable( $arr['function'] ) ) {
						if ( $remove_all_filters ) {
							unset( $wp_filter[ $notice_type ]->callbacks[ $priority ][ $name ] );
						}
						continue;
					}

                    $class = '';
                    if ( ! empty( $arr['function'][0] ) && is_object( $arr['function'][0] ) ) {
                        $class = strtolower( get_class( $arr['function'][0] ) );
                    }
					if ( ! empty( $arr['function'][0] ) && is_string( $arr['function'][0] ) ) {
						$class = strtolower( $arr['function'][0] );
					}

					// Remove all callbacks except SearchWP notices.
					if ( $remove_all_filters && strpos( $class, 'searchwp' ) === false ) {
						unset( $wp_filter[ $notice_type ]->callbacks[ $priority ][ $name ] );
						continue;
					}

					$cb = is_array( $arr['function'] ) ? $arr['function'][1] : $arr['function'];

					// Remove a specific callback.
					if ( ! $remove_all_filters && in_array( $cb, $rules[ $notice_type ], true ) ) {
                        unset( $wp_filter[ $notice_type ]->callbacks[ $priority ][ $name ] );
                    }
				}
			}
		}
	}

	/**
	 * Renders the page.
	 *
	 * @since 4.2.0
	 */
	public static function page() {

		do_action( 'searchwp\settings\page' );
		do_action( 'searchwp\debug\log', 'Displaying options page', 'settings' );

		self::view();
		self::footer();
	}

	/**
	 * Renders the header logo.
	 *
	 * @since 4.2.8
	 *
	 * @return void
	 */
    private static function header_logo() {
        ?>
        <svg fill="none" height="40" viewBox="0 0 186 40" width="186" xmlns="http://www.w3.org/2000/svg"
             xmlns:xlink="http://www.w3.org/1999/xlink">
            <clipPath id="a">
                <path d="m0 0h26.2464v40h-26.2464z"/>
            </clipPath>
            <g fill="#456b47">
                <path d="m51.2968 15.3744c-.1125.2272-.225.4544-.45.568-.1126.1136-.3376.1136-.5626.1136-.2251 0-.4501-.1136-.7876-.2272-.2251-.2272-.5626-.3408-1.0127-.568s-.7876-.4544-1.3502-.568c-.4501-.2272-1.1252-.2272-1.8003-.2272s-1.1251.1136-1.5752.2272-.9001.3408-1.1252.6816c-.3375.2272-.5625.568-.6751.9088-.1125.3409-.225.7953-.225 1.2497 0 .568.1125 1.0224.4501 1.4768.3375.3408.7876.6817 1.2377 1.0225.5625.2272 1.1251.4544 1.8002.6816l2.0253.6816c.6751.2272 1.3502.568 2.0253.7953.6751.3408 1.2377.6816 1.8003 1.2496.5626.4544.9001 1.136 1.2377 1.8177.3375.6816.45 1.5904.45 2.6129 0 1.136-.225 2.1584-.5625 3.0673-.3376.9088-.9002 1.8176-1.5753 2.4993-.6751.6816-1.5752 1.2496-2.5879 1.704-1.0126.4544-2.2503.568-3.488.568-.7876 0-1.4627-.1136-2.2503-.2272s-1.4627-.3408-2.1378-.6816c-.6751-.2272-1.3502-.568-1.9128-1.0224-.1125-.1136-.2251-.2272-.4501-.2272-.6751-.5681-.9001-1.4769-.4501-2.2721l.5626-.9089c.1125-.1136.2251-.3408.4501-.3408.225-.1136.3375-.1136.5626-.1136.225 0 .5626.1136.9001.3408.3376.2272.6751.4545 1.1252.7953s.9001.568 1.5752.7952c.5626.2272 1.3502.3408 2.1378.3408 1.2377 0 2.2504-.3408 2.9255-.9088s1.0126-1.4769 1.0126-2.6129c0-.6816-.1125-1.1361-.45-1.5905-.3376-.4544-.7877-.7952-1.2377-1.0224-.5626-.2272-1.1252-.4544-1.8003-.6816s-1.3502-.3408-2.0253-.5681c-.6751-.2272-1.3502-.4544-2.0253-.7952s-1.2377-.6816-1.8003-1.2496c-.5626-.4544-.9001-1.1361-1.2377-1.9313-.3375-.7952-.45-1.7041-.45-2.8401 0-.9088.225-1.7041.5626-2.6129.3375-.7952.9001-1.5905 1.5752-2.2721s1.4627-1.136 2.4754-1.5904c1.0126-.3408 2.1378-.5681 3.3755-.5681 1.4627 0 2.7004.2273 3.9381.6817.7876.3408 1.5752.6816 2.1378 1.136.5626.3408.6751 1.1361.3375 1.7041z"/>
                <path d="m62.4361 17.7601c1.1252 0 2.0253.2272 2.9255.5681.9001.3408 1.6877.9088 2.3628 1.4768s1.1252 1.4769 1.5753 2.4993c.3375 1.0224.5625 2.0449.5625 3.2945v.7952c0 .2273-.1125.3409-.1125.4545-.1125.1136-.225.2272-.3375.2272s-.2251.1136-.4501.1136h-10.5766c.1125 1.8176.5626 3.0673 1.4627 3.8625.7877.7952 1.9128 1.2497 3.263 1.2497.6751 0 1.2377-.1136 1.6878-.2273.4501-.1136.9001-.3408 1.2377-.568.3375-.2272.6751-.3408.9001-.568.225-.1136.5626-.2272.7876-.2272.1125 0 .3376 0 .4501.1136s.225.1136.3375.3408l.2251.3408c.5626.7953.45 1.8177-.3376 2.3857-.1125 0-.1125.1136-.225.1136-.5626.3408-1.1252.6816-1.8003.9088-.5626.2273-1.2377.3409-1.9128.4545s-1.2376.1136-1.8002.1136c-1.2377 0-2.2504-.2272-3.263-.5681-1.0127-.3408-1.9128-1.0224-2.7004-1.8176s-1.3502-1.7041-1.8003-2.8401c-.4501-1.1361-.6751-2.4993-.6751-3.9762 0-1.136.225-2.272.5626-3.2945.3375-1.0224.9001-1.9312 1.5752-2.7265.6751-.7952 1.5753-1.3632 2.5879-1.8176 1.0127-.4545 2.1378-.6817 3.488-.6817zm0 2.9537c-1.2377 0-2.1378.3408-2.8129 1.0225-.6751.6816-1.1251 1.704-1.3502 2.9537h7.6512c0-.568-.1126-1.0225-.2251-1.4769s-.3375-.9088-.6751-1.2496c-.3375-.3408-.6751-.6816-1.1251-.7952-.4501-.1137-.7877-.4545-1.4628-.4545z"/>
                <path d="m85.277 33.5511c0 .9089-.7877 1.7041-1.6878 1.7041h-.225c-.3376 0-.6751-.1136-.9002-.2272-.225-.1136-.3375-.3408-.45-.6817l-.3376-1.2496c-.45.3408-.9001.6816-1.2377 1.0224-.45.3409-.9001.5681-1.2376.7953-.4501.2272-.9002.3408-1.4628.4544-.45.1136-1.0126.1136-1.6877.1136s-1.3502-.1136-2.0253-.3408c-.5626-.2272-1.1252-.4544-1.5752-.9089-.4501-.3408-.7877-.9088-1.0127-1.4768s-.3375-1.2497-.3375-2.0449c0-.6816.1125-1.2496.5625-1.9313.3376-.6816.9002-1.2496 1.6878-1.704s1.8003-.9088 3.1505-1.2497c1.3502-.3408 2.9254-.4544 4.8382-.4544v-1.0224c0-1.1361-.2251-2.0449-.6751-2.6129-.4501-.568-1.2377-.7952-2.1378-.7952-.6751 0-1.2377.1136-1.6878.2272s-.7876.3408-1.1252.568c-.3375.2272-.6751.3408-.9001.568-.3375.1136-.6751.2272-1.0126.2272-.2251 0-.5626-.1136-.6751-.2272-.2251-.1136-.3376-.3408-.4501-.568v-.1136c-.4501-.7952-.225-1.7041.4501-2.1585 1.6877-1.136 3.713-1.8177 5.9633-1.8177 1.0127 0 1.9128.1137 2.7004.4545.7877.3408 1.4628.7952 2.0253 1.3632.5626.568.9002 1.2497 1.2377 2.1585.3376.7952.4501 1.7041.4501 2.7265v9.2019zm-7.9887-.9088c.45 0 .7876 0 1.1251-.1136.3376-.1136.6751-.2272 1.0127-.3408.3375-.1136.6751-.3408.9001-.5681.3376-.2272.5626-.4544.9002-.7952v-2.8401c-1.2377 0-2.2504.1136-3.038.2272s-1.4627.3408-1.9128.568c-.45.2273-.7876.5681-1.0126.7953-.2251.3408-.3376.6816-.3376 1.0224 0 .6816.2251 1.2497.6751 1.5905.4501.3408 1.0127.4544 1.6878.4544z"/>
                <path d="m88.2024 33.8918v-13.8597c0-.9088.7876-1.704 1.6877-1.704h.7877c.45 0 .6751.1136.9001.2272.1125.1136.225.4544.3375.7952l.2251 2.0449c.5626-1.0225 1.3502-1.9313 2.1378-2.4993s1.8003-.9089 2.8129-.9089h.4501c.9001.1136 1.5752 1.0225 1.4627 1.9313l-.3375 1.7041c0 .2272-.1126.3408-.2251.4544s-.225.1136-.4501.1136c-.1125 0-.3375 0-.6751-.1136-.3375-.1136-.6751-.1136-1.1251-.1136-.9002 0-1.5753.2272-2.1378.6816-.5626.4544-1.1252 1.1361-1.5753 2.0449v9.0883c0 .9088-.7876 1.7041-1.6877 1.7041h-.9002c-.9001 0-1.6877-.6817-1.6877-1.5905z"/>
                <path d="m112.619 21.7363c-.113.1136-.225.2272-.338.3408s-.338.1136-.563.1136-.45-.1136-.562-.2272c-.225-.1136-.45-.2272-.675-.4544-.225-.1136-.563-.3408-1.013-.4544-.337-.1137-.9-.2273-1.463-.2273-.675 0-1.35.1136-1.912.3409-.563.2272-1.013.6816-1.351 1.136-.337.4544-.675 1.136-.787 1.8177-.225.6816-.225 1.4768-.225 2.3856 0 .9089.112 1.7041.337 2.4993.225.6817.45 1.3633.788 1.8177.337.4544.788.9088 1.35 1.136.563.2273 1.125.3409 1.8.3409s1.126-.1136 1.576-.2273c.45-.1136.787-.3408 1.012-.568s.563-.3408.675-.568c.225-.1136.45-.2272.675-.2272.338 0 .563.1136.788.3408l.225.3408c.563.6816.45 1.8177-.337 2.3857-.113.1136-.225.2272-.225.2272-.563.3408-1.126.6816-1.688.9088-.563.2273-1.125.3409-1.8.4545-.563.1136-1.238.1136-1.801.1136-1.012 0-2.025-.2272-2.925-.5681-.9-.3408-1.688-1.0224-2.476-1.704-.675-.7952-1.237-1.7041-1.687-2.8401-.4504-1.1361-.5629-2.3857-.5629-3.7489 0-1.2497.225-2.3857.5629-3.5218.337-1.0224.9-2.0448 1.575-2.8401.675-.7952 1.575-1.3632 2.588-1.8176 1.012-.4545 2.25-.6817 3.6-.6817 1.238 0 2.363.2272 3.376.5681.45.2272.787.3408 1.238.6816.787.568 1.012 1.5904.45 2.3857z"/>
                <path d="m115.094 33.8919v-21.5848c0-.9088.787-1.7041 1.688-1.7041h.787c.9 0 1.688.7953 1.688 1.7041v7.9523c.675-.6816 1.35-1.1361 2.138-1.5905.787-.3408 1.687-.568 2.813-.568.9 0 1.8.1136 2.475.4544s1.35.7952 1.8 1.3633c.45.568.9 1.2496 1.125 2.0448.225.7953.338 1.7041.338 2.6129v9.3156c0 .9088-.788 1.704-1.688 1.704h-.787c-.901 0-1.688-.7952-1.688-1.704v-9.3156c0-1.0224-.225-1.8176-.675-2.3857-.45-.568-1.238-.9088-2.138-.9088-.788 0-1.463.2272-2.138.568-.562.3408-1.125.7953-1.688 1.2497v10.7924c0 .9088-.787 1.704-1.687 1.704h-.788c-.788-.1136-1.575-.7952-1.575-1.704z"/>
                <path d="m132.197 12.9886c-.338-1.136.45-2.1585 1.575-2.1585h1.575c.45 0 .675.1136 1.013.2272.225.2273.45.4545.562.7953l4.163 14.8821c.113.3408.225.7952.225 1.1361.113.4544.113.9088.225 1.3632.113-.4544.225-.9088.338-1.3632.112-.4545.225-.7953.338-1.1361l4.838-14.8821c.112-.2272.225-.4544.562-.6816.225-.2273.563-.3409 1.013-.3409h1.35c.45 0 .675.1136 1.013.2272.225.2273.45.4545.562.7953l4.839 14.8821c.225.6816.45 1.5905.675 2.3857.112-.4544.112-.9088.225-1.2496.112-.4545.225-.7953.225-1.1361l4.163-14.8821c.112-.3408.225-.568.562-.6816.226-.2273.563-.3409 1.013-.3409h1.238c1.125 0 1.913 1.1361 1.575 2.1585l-6.526 21.3576c-.225.6816-.9 1.2496-1.575 1.2496h-1.688c-.788 0-1.35-.4544-1.575-1.136l-5.176-15.9046c-.112-.2272-.112-.4544-.225-.6816-.112-.2272-.112-.568-.225-.7952-.112.3408-.112.568-.225.7952s-.113.4544-.225.6816l-5.063 15.791c-.225.6816-.9 1.136-1.576 1.136h-1.687c-.788 0-1.35-.4544-1.576-1.2496z"/>
                <path d="m172.69 26.8484v7.0435c0 .9088-.788 1.704-1.688 1.704h-1.238c-.9 0-1.687-.7952-1.687-1.704v-21.4712c0-.9089.787-1.7041 1.687-1.7041h6.301c1.688 0 3.038.2272 4.276.568s2.138.9089 2.925 1.5905c.788.6816 1.351 1.4768 1.688 2.4993.338 1.0224.563 2.0449.563 3.1809 0 1.2496-.225 2.2721-.563 3.2945-.45 1.0225-1.012 1.8177-1.8 2.6129-.788.6816-1.8 1.2497-2.926 1.7041-1.237.4544-2.587.568-4.163.568h-3.375zm0-3.6353h3.375c.788 0 1.576-.1136 2.138-.3408.675-.2273 1.125-.5681 1.575-.9089s.675-.9088.901-1.4768c.225-.5681.337-1.2497.337-1.9313s-.112-1.2496-.337-1.8177c-.226-.568-.563-1.0224-.901-1.3632-.45-.3408-.9-.6816-1.575-.9089-.675-.2272-1.35-.3408-2.138-.3408h-3.375z"/>
            </g>
            <g clip-path="url(#a)">
                <g clip-rule="evenodd" fill="#456b47" fill-rule="evenodd">
                    <path d="m24.5846 16.0458c0-.7083-.6797-1.4326-1.6619-1.4326v-1.7192c1.7686 0 3.3811 1.3387 3.3811 3.1518v16.5043c0 1.8132-1.6125 3.1519-3.3811 3.1519h-2.4068v-1.7192h2.4068c.9822 0 1.6619-.7243 1.6619-1.4327z"/>
                    <path d="m.057373 16.0458c0-1.8131 1.612567-3.1518 3.381087-3.1518v1.7192c-.98219 0-1.66189.7243-1.66189 1.4326v16.5043c0 .7084.6797 1.4327 1.66189 1.4327h2.29226v1.7192h-2.29226c-1.76852 0-3.381087-1.3387-3.381087-3.1519z"/>
                    <path d="m5.94932 16.2056c.25995-.6058.52876-1.2322.83483-1.8954l1.56096.7205c-.26042.5642-.51794 1.1623-.77746 1.765-.38453.893-.77343 1.7962-1.18259 2.6145-.87996 1.7599-1.36619 3.5097-1.06846 5.1968l.00446.0254.00295.0255c.31338 2.716 1.65716 4.9094 4.10687 6.6135l-.98178 1.4113c-2.81518-1.9584-4.45017-4.5703-4.83002-7.8025-.38037-2.2007.27811-4.3385 1.22828-6.2389.40036-.8007.7427-1.5985 1.10196-2.4357z"/>
                    <path d="m21.13 17.6879c.1672.3555.3376.718.5103 1.0923l.0036.0078.0035.0078c.8235 1.8824 1.467 3.8834 1.2129 6.1702l-.0008.0075c-.376 3.1333-2.0144 5.7413-4.8125 7.8094l-1.0219-1.3825c2.473-1.8278 3.8144-4.0323 4.127-6.628.2029-1.8351-.2978-3.4985-1.0763-5.2796-.1596-.3457-.3213-.6894-.4829-1.0329-.519-1.1035-1.0373-2.2055-1.4838-3.3662l1.6046-.6172c.422 1.0971.9038 2.1216 1.4163 3.2114z"/>
                    <path d="m4.46831 17.2516c.28355-.3807.82208-.4595 1.20285-.176l16.16044 12.0344c.3808.2835.4596.8221.176 1.2028-.2835.3808-.822.4596-1.2028.1761l-16.16046-12.0344c-.38077-.2836-.45958-.8221-.17603-1.2029z"/>
                    <path d="m22.0076 17.2516c.2836.3808.2048.9193-.176 1.2029l-16.16044 12.0344c-.38077.2835-.9193.2047-1.20285-.1761-.28355-.3807-.20474-.9193.17603-1.2028l16.16046-12.0344c.3808-.2835.9193-.2047 1.2028.176z"/>
                </g>
                <path d="m18.1089 11.2321h-9.74213c-.34384 0-.57307-.2292-.57307-.5731v-1.48995c0-1.60458 1.26075-2.75072 2.7507-2.75072h5.2722c1.6046 0 2.7507 1.26075 2.7507 2.75072v1.37535c.1147.4585-.1146.6877-.4584.6877z"
                      fill="#77a872"/>
                <path clip-rule="evenodd"
                      d="m10.5444 7.27791c-1.03889 0-1.89112.7846-1.89112 1.89112v1.20347h9.05442v-1.20347c0-1.03889-.7846-1.89112-1.8911-1.89112zm-3.61032 1.89112c0-2.10264 1.66926-3.61031 3.61032-3.61031h5.2722c2.1026 0 3.6103 1.66925 3.6103 3.61031v1.28517c.0656.3566.0348.7697-.2292 1.1217-.2951.3934-.7352.5158-1.0888.5158h-9.74215c-.36726 0-.74011-.1262-1.0233-.4094-.2832-.2832-.40937-.656-.40937-1.0233z"
                      fill="#456b47" fill-rule="evenodd"/>
                <path clip-rule="evenodd"
                      d="m5.04306 12.3209c-.32755 0-.51576.1883-.51576.5158v.6304h17.192v-.6304c0-.3275-.1882-.5158-.5158-.5158zm-2.23495.5158c0-1.277.95792-2.235 2.23495-2.235h16.16044c1.2771 0 2.235.958 2.235 2.235v.9169c0 .3673-.1262.7401-.4094 1.0233s-.656.4094-1.0233.4094h-17.76503c-.36726 0-.74011-.1262-1.0233-.4094s-.40936-.656-.40936-1.0233z"
                      fill="#456b47" fill-rule="evenodd"/>
                <path clip-rule="evenodd"
                      d="m7.73657 5.61605c0-3.11085 2.44793-5.558738 5.55873-5.558738 3.1109 0 5.5587 2.447888 5.5587 5.558738 0 .46159-.1409 1.12803-.2548 1.58384l-.2599 1.0396-.9585-.47923c-.4293-.21463-.854-.3677-1.2202-.3677h-5.8452c-.43253 0-.69723.07877-.97424.28653l-1.09117.81838-.2675-1.33748c-.02047-.10234-.04319-.21143-.06586-.32022-.03398-.16313-.06783-.32558-.0937-.46359-.04491-.23951-.08636-.50482-.08636-.76013zm5.55873-3.83954c-2.1613 0-3.83953 1.67818-3.83953 3.83954 0 .03853.003.08603.00978.14544.27521-.06286.55735-.08813.84985-.08813h5.8452c.3357 0 .6605.05655.9604.1404.009-.07754.0139-.14461.0139-.19771 0-2.16136-1.6782-3.83954-3.8396-3.83954z"
                      fill="#456b47" fill-rule="evenodd"/>
                <path d="m19.9427 39.1977h-13.63894c-1.14613 0-2.06304-.9169-2.06304-2.063v-2.7508c0-1.2607 1.03152-2.2922 2.29227-2.2922h13.18051c1.2607 0 2.2923 1.0315 2.2923 2.2922v2.7508c0 1.1461-.9169 2.063-2.0631 2.063z"
                      fill="#77a872"/>
                <path clip-rule="evenodd"
                      d="m6.53297 32.9513c-.78601 0-1.43267.6467-1.43267 1.4327v2.7507c0 .6714.53205 1.2034 1.20344 1.2034h13.63896c.6714 0 1.2034-.532 1.2034-1.2034v-2.7507c0-.786-.6466-1.4327-1.4326-1.4327zm-3.15187 1.4327c0-1.7355 1.41638-3.1519 3.15187-3.1519h13.18053c1.7355 0 3.1518 1.4164 3.1518 3.1519v2.7507c0 1.6209-1.3017 2.9226-2.9226 2.9226h-13.63896c-1.62088 0-2.92264-1.3017-2.92264-2.9226z"
                      fill="#456b47" fill-rule="evenodd"/>
            </g>
        </svg>
        <?php
    }

	/**
	 * Renders the header.
	 *
	 * @since 4.0
     *
	 * @return void
	 */
    private static function header() {

        do_action( 'searchwp\settings\header\before' );

        self::header_main();
        self::header_sub();

        echo '<hr class="wp-header-end">';

		do_action( 'searchwp\settings\header\after' );
    }

	/**
	 * Renders the main header.
	 *
	 * @since 4.2.8
	 *
	 * @return void
	 */
	private static function header_main() {

		?>
        <div class="swp-header">
            <div class="swp-flex--row swp-justify-between swp-flex--align-c">
                <div class="swp-col">
                    <?php self::header_logo(); ?>
                </div>
                <div class="swp-col">
	                <div class="swp-header-menu swp-flex--row swp-flex--gap20">
                        <?php do_action( 'searchwp\settings\header\actions' ); ?>
						<?php self::header_help_action(); ?>
	                </div>
                </div>
            </div>
        </div>
		<?php
	}

	/**
	 * Renders the help action in the main header.
	 *
	 * @since 4.3.1
	 */
	private static function header_help_action() {
		?>
		<a href="https://searchwp.com/documentation/?utm_source=WordPress&utm_medium=settings&utm_campaign=plugin&utm_content=Help" class="swp-header-menu--item swp-a" target="_blank" rel="noopener noreferrer">
			<svg width="15" height="14" viewBox="0 0 15 14" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path fill-rule="evenodd" clip-rule="evenodd" d="M0.833252 6.99998C0.833252 3.31998 3.81992 0.333313 7.49992 0.333313C11.1799 0.333313 14.1666 3.31998 14.1666 6.99998C14.1666 10.68 11.1799 13.6666 7.49992 13.6666C3.81992 13.6666 0.833252 10.68 0.833252 6.99998ZM8.16659 9.66665V11H6.83325V9.66665H8.16659ZM7.49992 12.3333C4.55992 12.3333 2.16659 9.93998 2.16659 6.99998C2.16659 4.05998 4.55992 1.66665 7.49992 1.66665C10.4399 1.66665 12.8333 4.05998 12.8333 6.99998C12.8333 9.93998 10.4399 12.3333 7.49992 12.3333ZM4.83325 5.66665C4.83325 4.19331 6.02659 2.99998 7.49992 2.99998C8.97325 2.99998 10.1666 4.19331 10.1666 5.66665C10.1666 6.52192 9.6399 6.98219 9.12709 7.43034C8.6406 7.85549 8.16659 8.26973 8.16659 8.99998H6.83325C6.83325 7.7858 7.46133 7.30437 8.01355 6.8811C8.44674 6.54905 8.83325 6.25279 8.83325 5.66665C8.83325 4.93331 8.23325 4.33331 7.49992 4.33331C6.76659 4.33331 6.16659 4.93331 6.16659 5.66665H4.83325Z" fill="#0E2121" fill-opacity="0.6"/>
			</svg>
			<span><?php esc_html_e( 'Help', 'searchwp' ); ?></span>
		</a>
		<?php
	}

	/**
	 * Renders the subheader.
	 *
	 * @since 4.2.8
	 *
	 * @return void
	 */
	private static function header_sub() {

		?>
			<?php if ( has_action( 'searchwp\settings\nav\tab' ) ) : ?>
                <nav>
                    <ul class="swp-nav-menu">
                        <?php do_action( 'searchwp\settings\nav\tab' ); ?>
                    </ul>
                </nav>
			<?php else : ?>
				<?php do_action( 'searchwp\settings\page\title' ); ?>
			<?php endif; ?>
		<?php
	}

	/**
	 * Renders the main view.
	 *
	 * @since 4.0
     *
	 * @return void
	 */
	private static function view() {

		$view = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'default';

		// TODO: Deprecate `'searchwp\settings\...\\' . $view` actions after updating all the dependent code.

		do_action( 'searchwp\settings\before' );
		do_action( 'searchwp\settings\before\\' . $view );

		do_action( 'searchwp\settings\view' );
		do_action( 'searchwp\settings\view\\' . $view );

		do_action( 'searchwp\settings\after' );
		do_action( 'searchwp\settings\after\\' . $view );
	}

	/**
	 * Renders the footer.
	 *
	 * @since 4.0
     *
	 * @return void
	 */
	private static function footer() {
		do_action( 'searchwp\settings\footer' );
	}

	/**
	 * Initialize all registered Extensions.
	 *
	 * @since 4.0
     *
	 * @return void
	 */
	private function init_extensions() {

		$this->prime_extensions();

		$extensions = array_filter( (array) $this->extensions, function( $extension ) {
			return ! empty( $extension->public );
		} );

		if ( empty( $extensions ) ) {
            return;
        }

		$extension_parent_pages = [
			'custom-results-order' => 'algorithm',
			'diagnostics'          => 'tools',
		];

		foreach ( $extensions as $extension ) {
			if ( ! isset( $extension->slug, $extension->name ) || empty( $extension->public ) ) {
                continue;
			}
            $parent_page = $extension_parent_pages[ $extension->slug ] ?? 'settings';
			if ( ! Utils::is_swp_admin_page( $parent_page ) ) {
                continue;
            }
			new NavTab( [
				'page'       => $parent_page,
				'tab'        => 'extensions',
				'label'      => $extension->name,
				'query_args' => [
					'extension' => $extension->slug,
				],
			] );
		}

		add_action( 'searchwp\settings\view\extensions', [ $this, 'render_extension_view' ] );
	}

	/**
	 * Prime and prepare registered Extensions.
	 *
	 * @since 4.0
     *
	 * @return void
	 */
	private function prime_extensions() {

		$extensions = apply_filters( 'searchwp\extensions', [] );

		if ( ! is_array( $extensions ) || empty( $extensions ) ) {
			return;
		}

		foreach ( $extensions as $extension => $path ) {
			$class_name = 'SearchWP' . $extension;

			if ( ! class_exists( $class_name ) && file_exists( $path ) ) {
				include_once $path;
			}

			$this->extensions[ $extension ] = new $class_name( $this->extensions );

			// Add plugin row action.
			if ( isset( $this->extensions[ $extension ]->min_searchwp_version )
			     && version_compare( SEARCHWP_VERSION, $this->extensions[ $extension ]->min_searchwp_version, '<' ) ) {
				do_action( 'searchwp\debug\log', 'after_plugin_row_' . plugin_basename( $path ) );
				add_action( 'after_plugin_row_' . plugin_basename( $path ), [ $this, 'plugin_row' ], 11, 3 );
			}
		}
	}

	/**
	 * Renders the view for an Extension.
	 *
	 * @since 4.0
     *
	 * @return void
	 */
	public function render_extension_view() {

		if ( empty( $this->extensions ) || ! isset( $_GET['extension'] ) ) {
			return;
		}

		// Find out which extension we're working with.
		$extension = array_filter( $this->extensions, function( $attributes, $extension ) {
			return isset( $attributes->slug ) && $attributes->slug === $_GET['extension'] && method_exists( $this->extensions[ $extension ], 'view' );
		}, ARRAY_FILTER_USE_BOTH );

		if ( empty( $extension ) ) {
			return;
		}

		reset( $extension );
		$extension  = key( $extension );
		$attributes = $this->extensions[ $extension ];
		?>
		<div class="wrap" id="searchwp-<?php echo esc_attr( $attributes->slug ); ?>-wrapper">
			<div id="icon-options-general" class="icon32"><br /></div>
			<div class="<?php echo esc_attr( $attributes->slug ); ?>-container">
				<h2>SearchWP <?php echo esc_html( $attributes->name ); ?></h2>
				<?php $this->extensions[ $extension ]->view(); ?>
			</div>
		</div>
		<?php
	}
}
