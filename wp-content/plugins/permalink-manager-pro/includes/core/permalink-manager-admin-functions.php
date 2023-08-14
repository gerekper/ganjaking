<?php

/**
 * Additional functions related to WordPress Admin Dashboard UI
 */
class Permalink_Manager_Admin_Functions {

	public $menu_name, $sections, $active_section, $active_subsection;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_bar_menu', array( $this, 'fix_customize_url' ), 41 );

		add_action( 'admin_notices', array( $this, 'display_plugin_notices' ) );
		add_action( 'admin_notices', array( $this, 'display_global_notices' ) );
	}

	/**
	 * Hooks that should be triggered with "admin_init"
	 */
	public function init() {
		// Additional links in "Plugins" page
		add_filter( "plugin_action_links_" . PERMALINK_MANAGER_BASENAME, array( $this, "plugins_page_links" ) );
		add_filter( "plugin_row_meta", array( $this, "plugins_page_meta" ), 10, 2 );

		// Detect current section
		$this->sections = apply_filters( 'permalink_manager_sections', array() );
		$this->get_current_section();
	}

	/**
	 * Use the native URL for "Customize" button in the admin bar
	 *
	 * @param WP_Admin_Bar $wp_admin_bar
	 */
	public function fix_customize_url( $wp_admin_bar ) {
		global $permalink_manager_ignore_permalink_filters;

		$object    = get_queried_object();
		$customize = $wp_admin_bar->get_node( 'customize' );

		if ( empty( $customize->href ) ) {
			return;
		}

		$permalink_manager_ignore_permalink_filters = true;
		if ( ! empty( $object->ID ) ) {
			$new_url = get_permalink( $object->ID );
		}
		$permalink_manager_ignore_permalink_filters = false;

		if ( ! empty( $new_url ) ) {
			$new_url       = urlencode_deep( $new_url );
			$customize_url = preg_replace( '/url=([^&]+)/', "url={$new_url}", $customize->href );

			$wp_admin_bar->add_node( array(
				'id'   => 'customize',
				'href' => $customize_url,
			) );
		}
	}

	/**
	 * Get current section of Permalink Manager admin panel
	 */
	public function get_current_section() {
		global $active_section, $active_subsection, $current_admin_tax;

		// 1. Get current section
		if ( isset( $_GET['page'] ) && $_GET['page'] == PERMALINK_MANAGER_PLUGIN_SLUG ) {
			if ( isset( $_POST['section'] ) ) {
				$this->active_section = sanitize_title_with_dashes( $_POST['section'] );
			} else if ( isset( $_GET['section'] ) ) {
				$this->active_section = sanitize_title_with_dashes( $_GET['section'] );
			} else {
				$sections_names       = array_keys( $this->sections );
				$this->active_section = $sections_names[0];
			}
		}

		// 2. Get current subsection
		if ( $this->active_section && isset( $this->sections[ $this->active_section ]['subsections'] ) ) {
			if ( isset( $_POST['subsection'] ) ) {
				$this->active_subsection = sanitize_title_with_dashes( $_POST['subsection'] );
			} else if ( isset( $_GET['subsection'] ) ) {
				$this->active_subsection = sanitize_title_with_dashes( $_GET['subsection'] );
			} else {
				$subsections_names       = array_keys( $this->sections[ $this->active_section ]['subsections'] );
				$this->active_subsection = $subsections_names[0];
			}
		}

		// 3. Check if current admin page is related to taxonomies
		if ( ! empty( $this->active_subsection ) && substr( $this->active_subsection, 0, 4 ) == 'tax_' ) {
			$current_admin_tax = substr( $this->active_subsection, 4, strlen( $this->active_subsection ) );
		} else {
			$current_admin_tax = false;
		}

		// Set globals
		$active_section    = $this->active_section;
		$active_subsection = $this->active_subsection;
	}

	/**
	 * Add "Tools -> Permalink Manager" to the admin sidebar menu
	 */
	public function add_menu_page() {
		$this->menu_name = add_management_page( __( 'Permalink Manager', 'permalink-manager' ), __( 'Permalink Manager', 'permalink-manager' ), 'manage_options', PERMALINK_MANAGER_PLUGIN_SLUG, array( $this, 'display_section' ) );

		add_action( 'admin_init', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_init', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Display the plugin sections
	 */
	public function display_section() {
		echo Permalink_Manager_UI_Elements::get_plugin_sections_html( $this->sections, $this->active_section, $this->active_subsection );
	}

	/**
	 * Register the CSS files for the plugin's dashboard
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'permalink-manager-plugins', PERMALINK_MANAGER_URL . '/out/permalink-manager-plugins.css', array(), PERMALINK_MANAGER_VERSION );
		wp_enqueue_style( 'permalink-manager', PERMALINK_MANAGER_URL . '/out/permalink-manager-admin.css', array( 'permalink-manager-plugins' ), PERMALINK_MANAGER_VERSION );
	}

	/**
	 * Register the JavaScript files for the plugin's dashboard.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'permalink-manager-plugins', PERMALINK_MANAGER_URL . '/out/permalink-manager-plugins.js', array( 'jquery', ), PERMALINK_MANAGER_VERSION );
		wp_enqueue_script( 'permalink-manager', PERMALINK_MANAGER_URL . '/out/permalink-manager-admin.js', array( 'jquery', 'permalink-manager-plugins' ), PERMALINK_MANAGER_VERSION );

		wp_localize_script( 'permalink-manager', 'permalink_manager', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'url'      => PERMALINK_MANAGER_URL,
			'confirm'  => __( 'Are you sure? This action cannot be undone!', 'permalink-manager' ),
			'spinners' => admin_url( 'images' )
		) );

	}

	/**
	 * Get the URL of the plugin's dashboard
	 *
	 * @param string $append
	 *
	 * @return string
	 */
	public static function get_admin_url( $append = '' ) {
		//return menu_page_url(PERMALINK_MANAGER_PLUGIN_SLUG, false) . $append;
		$admin_page = sprintf( "tools.php?page=%s", PERMALINK_MANAGER_PLUGIN_SLUG . $append );

		return admin_url( $admin_page );
	}

	/**
	 * Add shortcut links for Permalink Manager on "Plugins" page
	 *
	 * @param array $links
	 *
	 * @return array
	 */
	public function plugins_page_links( $links ) {
		$new_links = array(
			sprintf( '<a href="%s">%s</a>', $this->get_admin_url(), __( 'URI Editor', 'permalink-manager' ) ),
			sprintf( '<a href="%s">%s</a>', $this->get_admin_url( '&section=settings' ), __( 'Settings', 'permalink-manager' ) ),
		);

		return array_merge( $links, $new_links );
	}

	/**
	 * Add shortcut meta links for Permalink Manager on "Plugins" page
	 *
	 * @param array $links
	 * @param string $file
	 *
	 * @return array
	 */
	public function plugins_page_meta( $links, $file ) {
		if ( $file == PERMALINK_MANAGER_BASENAME ) {
			$new_links = array(
				'doc' => sprintf( '<a href="%s?utm_source=plugin_admin_page" target="_blank">%s</a>', 'https://permalinkmanager.pro/docs/', __( 'Documentation', 'permalink-manager' ) )
			);

			if ( ! defined( 'PERMALINK_MANAGER_PRO' ) ) {
				$new_links['upgrade'] = sprintf( '<a href="%s?utm_source=plugin_admin_page" target="_blank"><strong>%s</strong></a>', PERMALINK_MANAGER_WEBSITE, __( 'Buy Permalink Manager Pro', 'permalink-manager' ) );
			}

			$links = array_merge( $links, $new_links );
		}

		return $links;
	}

	/**
	 * Check if URI Editor should be displayed for current user
	 *
	 * @return bool
	 */
	public static function current_user_can_edit_uris() {
		global $permalink_manager_options;

		$edit_uris_cap = ( ! empty( $permalink_manager_options['general']['edit_uris_cap'] ) ) ? $permalink_manager_options['general']['edit_uris_cap'] : 'publish_posts';

		return current_user_can( $edit_uris_cap );
	}

	/**
	 * Display global notices (throughout wp-admin dashboard)
	 */
	function display_global_notices() {
		global $permalink_manager_alerts, $active_section;

		$html = "";
		if ( ! empty( $permalink_manager_alerts ) && is_array( $permalink_manager_alerts ) ) {
			foreach ( $permalink_manager_alerts as $alert_id => $alert ) {
				$dismissed_transient_name = sprintf( 'permalink-manager-notice_%s', sanitize_title( $alert_id ) );
				$dismissed                = get_transient( $dismissed_transient_name );

				// Check if alert was dismissed
				if ( empty( $dismissed ) ) {
					// Display the notice only on the plugin pages
					if ( empty( $active_section ) && ! empty( $alert['plugin_only'] ) ) {
						continue;
					}

					// Check if the notice did not expire
					if ( isset( $alert['until'] ) && ( time() > strtotime( $alert['until'] ) ) ) {
						continue;
					}

					$html .= Permalink_Manager_UI_Elements::get_alert_message( $alert['txt'], $alert['type'], true, $alert_id );
				}
			}
		}

		echo $html;
	}

	/**
	 * Display notices generated by Permalink Manager tools
	 */
	function display_plugin_notices() {
		global $permalink_manager_before_sections_html;

		echo $permalink_manager_before_sections_html;
	}

	/**
	 * Get the list of all duplicated redirects and custom permalinks
	 *
	 * @param bool $include_custom_uris
	 *
	 * @return array
	 */
	public static function get_all_duplicates( $include_custom_uris = true ) {
		global $permalink_manager_uris, $permalink_manager_redirects;

		// Make sure that both variables are arrays
		$all_uris                    = ( $include_custom_uris && is_array( $permalink_manager_uris ) ) ? $permalink_manager_uris : array();
		$permalink_manager_redirects = ( is_array( $permalink_manager_redirects ) ) ? $permalink_manager_redirects : array();

		// Convert redirects list, so it can be merged with $permalink_manager_uris
		foreach ( $permalink_manager_redirects as $element_id => $redirects ) {
			if ( is_array( $redirects ) ) {
				foreach ( $redirects as $index => $uri ) {
					$all_uris["redirect-{$index}_{$element_id}"] = $uri;
				}
			}
		}

		// Count duplicates
		$duplicates_groups = array();
		$duplicates_list   = array_count_values( $all_uris );
		$duplicates_list   = array_filter( $duplicates_list, function ( $x ) {
			return $x >= 2;
		} );

		// Assign keys to duplicates (group them)
		if ( count( $duplicates_list ) > 0 ) {
			foreach ( $duplicates_list as $duplicated_uri => $count ) {
				$duplicated_ids = array_keys( $all_uris, $duplicated_uri );

				// Ignore duplicates in different langauges
				if ( self::is_uri_duplicated( $duplicated_uri, $duplicated_ids[0], $duplicated_ids ) ) {
					$duplicates_groups[ $duplicated_uri ] = $duplicated_ids;
				}
			}
		}

		return $duplicates_groups;
	}

	/**
	 * Check if a single URI is duplicated
	 *
	 * @param string $uri
	 * @param int $element_id
	 * @param array $duplicated_ids
	 *
	 * @return bool
	 */
	public static function is_uri_duplicated( $uri, $element_id, $duplicated_ids = array() ) {
		global $permalink_manager_uris;

		if ( empty( $uri ) || empty( $element_id ) || empty( $permalink_manager_uris ) ) {
			return false;
		}

		$uri        = trim( trim( sanitize_text_field( $uri ) ), "/" );
		$element_id = sanitize_text_field( $element_id );

		// Keep the URIs in a separate array just here
		if ( ! empty( $duplicated_ids ) ) {
			$all_duplicates = $duplicated_ids;
		} else if ( in_array( $uri, $permalink_manager_uris ) ) {
			$all_duplicates = array_keys( $permalink_manager_uris, $uri );
		}

		if ( ! empty( $all_duplicates ) ) {
			// Get the language code of current element
			$this_uri_lang = apply_filters( 'permalink_manager_get_language_code', '', $element_id );

			foreach ( $all_duplicates as $key => $duplicated_id ) {
				// Ignore custom redirects
				if ( strpos( $key, 'redirect-' ) !== false ) {
					unset( $all_duplicates[ $key ] );
					continue;
				}

				if ( $this_uri_lang ) {
					$duplicated_uri_lang = apply_filters( 'permalink_manager_get_language_code', '', $duplicated_id );
				}

				// Ignore the URI for requested element and other elements in other languages to prevent the false alert
				if ( ( ! empty( $duplicated_uri_lang ) && $duplicated_uri_lang !== $this_uri_lang ) || $element_id == $duplicated_id ) {
					unset( $all_duplicates[ $key ] );
				}
			}

			return ( count( $all_duplicates ) > 0 ) ? true : false;
		} else {
			return false;
		}
	}

	/**
	 * Allow to use custom permalinks in search queries in Bulk URI Editor
	 *
	 * @param string $search_query
	 * @param string $content_type
	 *
	 * @return array
	 */
	public static function search_uri( $search_query, $content_type = null ) {
		global $permalink_manager_uris;

		$found        = array();
		$search_query = preg_quote( $search_query, '/' );

		foreach ( $permalink_manager_uris as $id => $uri ) {
			if ( preg_match( "/\b$search_query\b/i", $uri ) ) {
				if ( $content_type && $content_type == 'taxonomies' && ( strpos( $id, 'tax-' ) !== false ) ) {
					$found[] = (int) abs( filter_var( $id, FILTER_SANITIZE_NUMBER_INT ) );
				} else if ( $content_type && $content_type == 'posts' && is_numeric( $id ) ) {
					$found[] = (int) filter_var( $id, FILTER_SANITIZE_NUMBER_INT );
				} else {
					$found[] = $id;
				}
			}
		}

		return $found;
	}

	/**
	 * Check if Permalink Manager Pro is active
	 *
	 * @return bool
	 */
	public static function is_pro_active() {
		if ( defined( 'PERMALINK_MANAGER_PRO' ) && class_exists( 'Permalink_Manager_Pro_Functions' ) ) {
			// Check if license is active
			$exp_date = Permalink_Manager_Pro_Functions::get_expiration_date( true );

			$is_pro = ( $exp_date > 2 ) ? false : true;
		} else {
			$is_pro = false;
		}

		return $is_pro;
	}
}
