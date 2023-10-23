<?php
/**
 * Admin class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes
 * @version 4.0.0
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Admin' ) ) {
	/**
	 * Admin class.
	 * This class manage all the admin features.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAN_Admin {

		/**
		 * Instance of panel object for the plugin
		 *
		 * @var YIT_Plugin_Panel_WooCommerce
		 */
		protected $panel;

		/**
		 * Panel page slug
		 *
		 * @var string
		 */
		protected $panel_page = 'yith_wcan_panel';

		/**
		 * Link to landing page on yithemes.com
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $premium_landing_url = 'https://yithemes.com/themes/plugins/yith-woocommerce-ajax-product-filter/';

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct() {
			// admin scripts.
			add_action( 'admin_init', array( $this, 'register_styles_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );

			// admin panel.
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
			add_action( 'yith_wcan_preset_details', array( $this, 'preset_edit_tab' ) );
			add_action( 'yith_wcan_terms_options', array( $this, 'filter_terms_field' ), 10, 1 );

			// ajax handling.
			add_action( 'wp_ajax_yith_wcan_search_term', array( $this, 'json_search_term' ) );

			// tools.
			add_filter( 'woocommerce_debug_tools', array( $this, 'register_tools' ) );

			// plugin action links.
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCAN_DIR . 'init.php' ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			// YITH WCAN Loaded.
			do_action( 'yith_wcan_loaded' );
		}

		/* === SCRIPT METHODS === */

		/**
		 * Register admin styles and scripts
		 *
		 * @access public
		 * @return void
		 * @since  4.0.0
		 */
		public function register_styles_scripts() {
			// register styles.
			wp_register_style( 'yith_wcan_admin', YITH_WCAN_URL . 'assets/css/admin.css', array( 'yit-plugin-style' ), YITH_WCAN_VERSION );

			// register scripts.
			wp_register_script( 'yith_wcan_admin_filters', YITH_WCAN_URL . 'assets/js/yith-wcan-admin-filters.js', array( 'jquery' ), YITH_WCAN_VERSION, true );
			wp_register_script( 'yith_wcan_admin', YITH_WCAN_URL . 'assets/js/yith-wcan-admin.js', array( 'jquery', 'wp-color-picker', 'wc-backbone-modal', 'yith_wcan_admin_filters' ), YITH_WCAN_VERSION, true );
			wp_localize_script(
				'yith_wcan_admin',
				'yith_wcan_admin',
				array(
					'nonce'             => array(
						'change_preset_status' => wp_create_nonce( 'change_preset_status' ),
						'search_term'          => wp_create_nonce( 'search_term' ),
						'save_preset_filter'   => wp_create_nonce( 'save_preset_filter' ),
						'load_more_filters'    => wp_create_nonce( 'load_more_filters' ),
						'delete_preset_filter' => wp_create_nonce( 'delete_preset_filter' ),
					),
					'messages'          => array(
						'confirm_copy'          => _x( 'Content copied to your clipboard', '[Admin] Copy confirmation message', 'yith-woocommerce-ajax-navigation' ),
						'confirm_delete'        => _x( 'Are you sure you want to delete this item?', '[Admin] Confirm filter delete message', 'yith-woocommerce-ajax-navigation' ),
						// translators: 1. Number of items that will be added.
						'confirm_add_all_terms' => _x( 'Are you sure you want to proceed? This operation will add %s items', '[Admin] Confirm add all terms message', 'yith-woocommerce-ajax-navigation' ),
						'filter_title_required' => _x( '"Filter title" is a required field', '[Admin] Error message', 'yith-woocommerce-ajax-navigation' ),
					),
					'labels'            => array(
						'no_title'      => _x( '&lt; no title &gt;', '[Admin] Message shown when filter has empty title', 'yith-woocommerce-ajax-navigation' ),
						'upload_media'  => _x( 'Select media you want to use', '[Admin] Media library title, when selecting images', 'yith-woocommerce-ajax-navigation' ),
						'confirm_media' => _x( 'Use this media', '[Admin] Media library confirm button, when selecting images', 'yith-woocommerce-ajax-navigation' ),
					),
					'yith_wccl_enabled' => defined( 'YITH_WCCL' ),
					'supported_designs' => YITH_WCAN_Filter_Factory::get_supported_designs(),
				)
			);
		}

		/**
		 * Enqueue admin styles and scripts
		 *
		 * @access public
		 * @return void
		 * @since  1.0.0
		 */
		public function enqueue_styles_scripts() {
			$screen = get_current_screen();

			if ( is_null( $screen ) ) {
				return;
			}

			$screen_id = $screen->id;

			if ( 'widgets' === $screen_id || $this->is_panel_page() ) {
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_style( 'yith_wcan_admin' );

				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_script( 'yith_wcan_admin' );

				wp_enqueue_media();
			}
		}

		/* === PANEL METHODS === */

		/**
		 * Get the premium landing uri
		 *
		 * @since   1.0.0
		 * @return  string The premium landing link
		 */
		public function get_premium_landing_uri() {
			return $this->premium_landing_url;
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0
		 * @use      /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {

			if ( ! empty( $this->panel ) ) {
				return;
			}

			$admin_tabs = array(
				'filter-preset' => array(
					'title'       => _x( 'Filter presets', '[Admin] tab name', 'yith-woocommerce-ajax-navigation' ),
					'icon'        => '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"></path>
</svg>',
					'description' => esc_html__( 'The list of all filter sets created and configured for your shop.', 'yith-woocommerce-ajax-navigation' ),
				),
				'general'       => array(
					'title'       => _x( 'General options', '[Admin] tab name', 'yith-woocommerce-ajax-navigation' ),
					'description' => _x( 'Configure the general settings of the plugin', '[Admin] tab description', 'yith-woocommerce-ajax-navigation' ),
					'icon'        => 'settings',
				),
				'seo'           => array(
					'title'       => _x( 'SEO', '[Admin] tab name', 'yith-woocommerce-ajax-navigation' ),
					'description' => _x( 'Configure options to optimize SEO indexing on any page that includes filters.', '[Admin] Tab description', 'yith-woocommerce-ajax-navigation' ),
					'icon'        => '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"></path>
</svg>',
				),
			);

			if ( isset( $_GET['tab'] ) && 'legacy' === $_GET['tab'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$admin_tabs['legacy'] = _x( 'Legacy', '[Admin] tab name', 'yith-woocommerce-ajax-navigation' );
			}

			$premium_tab = array(
				'landing_page_url' => $this->get_premium_landing_uri(),
				'premium_features' => array(
					__( '<b>100% mobile friendly:</b> Show filters in a modal view which is purposely designed for users visiting your site by smartphones or tablets', 'yith-woocommerce-ajax-navigation' ),
					__( 'Show filters in the default layout or also in an <b>horizontal toolbar above products</b> (like Zalando)', 'yith-woocommerce-ajax-navigation' ),
					__( 'Allow customers to <b>filter for price ranges</b> (unlimited ranges and the last range can show: “& above”) or using the <b>price slider</b>', 'yith-woocommerce-ajax-navigation' ),
					__( 'Allow customers to <b>filter for review</b> and <b>for brand</b> (with the support to YITH WooCommerce Brands plugin)', 'yith-woocommerce-ajax-navigation' ),
					__( 'Allow users to <b>order products</b> (by popularity, date, price, date of publishing, average rating, etc) and see only 	products in stock/featured/on sale', 'yith-woocommerce-ajax-navigation' ),
					__( '<b>Show the active filters</b> (with X to remove them) and choose their position (above products, above or under filters area)', 'yith-woocommerce-ajax-navigation' ),
					__( 'Create <b>color swatches with image support</b> (to better identify gradients, textures, patterns, etc.) and with 2 colors', 'yith-woocommerce-ajax-navigation' ),
					__( 'Show the options using <b>custom images or icons</b>', 'yith-woocommerce-ajax-navigation' ),
					__( 'Choose the <b>order of the options</b> (alphabetical, terms order, terms count, etc.), enable tooltips and show each set of filters in toggle', 'yith-woocommerce-ajax-navigation' ),
					__( 'Choose how to manage terms not availables: hide them OR shown them in grey color and not clickables ', 'yith-woocommerce-ajax-navigation' ),
					__( '<b>Regular updates, Translations and Premium Support</b>', 'yith-woocommerce-ajax-navigation' ), // phpcs:ignore
				),
				'main_image_url'   => YITH_WCAN_ASSETS . 'images/get-premium-ajax-product-filter.jpg',
			);

			$help_tab = array_merge(
				array(
					'main_video' => array(
						'desc' => _x( 'Check this video to learn how to <b>create a filter preset and show it on the shop page:</b>', '[HELP TAB] Video title', 'yith-woocommerce-ajax-navigation' ),
						'url'  => array(
							'en' => 'https://www.youtube.com/embed/o-ZhSVR4HvU',
							'it' => 'https://www.youtube.com/embed/cgQo2Cxux4M',
							'es' => 'https://www.youtube.com/embed/KGnJW_zUBRY',
						),
					),
					'playlists'  => array(
						'en' => 'https://www.youtube.com/watch?v=icXC7Ei4K7g&list=PLDriKG-6905lqqHc9JR5RJ3vhBn5ktdcj',
						'it' => 'https://www.youtube.com/watch?v=QwRkPQFeGOM&list=PL9c19edGMs08ouyniO98Q8S_pr4pHZPqb',
						'es' => 'https://www.youtube.com/watch?v=7kX7nxBD2BA&list=PL9Ka3j92PYJOyeFNJRdW9oLPkhfyrXmL1',
					),
					'hc_url'     => 'https://support.yithemes.com/hc/en-us/categories/360003474618-YITH-WOOCOMMERCE-AJAX-PRODUCT-FILTER',
				),
				defined( 'YITH_WCAN_PREMIUM_INIT' ) ? array( 'doc_url' => 'https://docs.yithemes.com/yith-woocommerce-ajax-product-filter/' ) : array()
			);

			$args = apply_filters(
				'yith_wcan_panel_args',
				array_merge(
					array(
						'create_menu_page'   => true,
						'parent_slug'        => '',
						'ui_version'         => 2,
						'page_title'         => 'YITH WooCommerce Ajax Product Filter',
						'menu_title'         => 'Ajax Product Filter',
						'plugin_description' => _x( 'It allows your users to find the product they are looking for as quickly as possible.', '[Admin] Plugin description', 'yith-woocommerce-ajax-navigation' ),
						'capability'         => apply_filters( 'yith_wcan_panel_capability', 'manage_woocommerce' ),
						'parent'             => '',
						'class'              => function_exists( 'yith_set_wrapper_class' ) ? yith_set_wrapper_class() : '',
						'parent_page'        => 'yit_plugin_panel',
						'admin-tabs'         => apply_filters( 'yith_wcan_settings_tabs', $admin_tabs ),
						'options-path'       => YITH_WCAN_DIR . '/plugin-options',
						'plugin_slug'        => YITH_WCAN_SLUG,
						'plugin-url'         => YITH_WCAN_URL,
						'is_extended'        => defined( 'YITH_WCAN_EXTENDED' ),
						'is_premium'         => defined( 'YITH_WCAN_PREMIUM' ),
						'page'               => $this->panel_page,
						'help_tab'           => $help_tab,
					),
					! defined( 'YITH_WCAN_PREMIUM' ) ? array( 'premium_tab' => $premium_tab ) : array()
				)
			);

			$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );

			do_action( 'yith_wcan_after_option_panel', $args );
		}

		/**
		 * Return url to plugin panel page
		 *
		 * @param string $tab  Tab slug.
		 * @param array  $args Array of additional arguments.
		 * @return string Panel url
		 */
		public function get_panel_url( $tab = '', $args = array() ) {
			$args = array_merge(
				$args,
				array(
					'page' => $this->panel_page,
				)
			);

			if ( ! empty( $tab ) ) {
				$args['tab'] = $tab;
			}

			return add_query_arg( $args, admin_url( 'admin.php' ) );
		}

		/**
		 * Return url to "create a new preset" page
		 *
		 * @return string "Create a new preset" url
		 */
		public function get_preset_create_page() {
			return $this->get_panel_url(
				'filter-preset',
				array(
					'action' => 'create',
				)
			);
		}

		/**
		 * Return panel page slug
		 *
		 * @return string Panel Slug.
		 */
		public function get_panel_page() {
			return $this->panel_page;
		}

		/**
		 * Return true if we're currently on plugin panel
		 *
		 * @return bool Whether current screen is panel page
		 */
		public function is_panel_page() {
			$screen = get_current_screen();

			// too soon to read screen, fallback on pagenow.
			if ( is_null( $screen ) ) {
				global $pagenow;

				return 'admin.php' === $pagenow && isset( $_GET['page'] ) && $this->panel_page === $_GET['page']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}

			// use screen id to check for current page.
			$screen_id = $screen->id;

			return 'yith-plugins_page_yith_wcan_panel' === $screen_id;
		}

		/**
		 * Return true if we're currently on preset new/edit page
		 *
		 * @return bool Whether current screen is preset new/edit page
		 */
		public function is_preset_detail_page() {
			$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( ! $action ) {
				return false;
			}

			return $this->is_panel_page() && in_array( $action, array( 'create', 'edit' ), true );
		}

		/**
		 * Shows "No items" template whenever needed
		 *
		 * @param array $args Array of arguments for the template.
		 * @return void
		 */
		public function show_empty_content( $args = array() ) {
			$args = wp_parse_args(
				$args,
				array(
					'item_name'    => _x( 'item', '[Admin] Generic item name, in "You have no x yet"', 'yith-woocommerce-ajax-navigation' ),
					'subtitle'     => _x( 'But don\'t worry, here you can create your first one!', '[Admin] Preset table empty message second line', 'yith-woocommerce-ajax-navigation' ),
					'button_label' => '',
					'button_class' => '',
					'button_url'   => '',
					'show_icon'    => true,
					'hide'         => false,
				)
			);

			extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract

			include YITH_WCAN_DIR . 'templates/admin/preset-empty-content.php';
		}

		/**
		 * Prints "Edit existing preset/Create new preset" tab
		 *
		 * @return void
		 */
		public function preset_edit_tab() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : false;
			$preset = isset( $_GET['preset'] ) ? (int) $_GET['preset'] : false;
			// phpcs:enable WordPress.Security.NonceVerification.Recommended

			if ( 'edit' === $action && $preset ) {
				$preset = YITH_WCAN_Preset_Factory::get_preset( $preset );
			} else {
				$preset = false;
			}

			include YITH_WCAN_DIR . 'templates/admin/preset-edit.php';
		}

		/**
		 * Prints "Term edit" template
		 *
		 * @param array $field Array of options for current template.
		 *
		 * @return void
		 */
		public function filter_terms_field( $field ) {
			$id       = isset( $field['index'] ) ? $field['index'] : 0;
			$terms    = isset( $field['value'] ) && $field['filter']->customize_terms() ? $field['value'] : array();
			$taxonomy = ! empty( $field['filter'] ) ? $field['filter']->get_taxonomy() : '';

			include YITH_WCAN_DIR . 'templates/admin/preset-filter-terms.php';
		}

		/**
		 * Prints single item of "Term edit" template
		 *
		 * @param int    $id Current row id.
		 * @param int    $term_id Current term id.
		 * @param string $term_name Current term name.
		 * @param string $term_options Options for current term (it may include label, tooltip, colors, and image).
		 *
		 * @return void
		 */
		public function filter_term_field( $id, $term_id, $term_name, $term_options = array() ) {
			// just include template, and provide passed terms.
			include YITH_WCAN_DIR . 'templates/admin/preset-filter-term.php';
		}

		/* === AJAX HANDLING === */

		/**
		 * Echoes a json formatted list of terms for a specific taxonomy
		 *
		 * @return void
		 */
		public function json_search_term() {
			check_ajax_referer( 'search_term', 'security' );

			$term    = isset( $_GET['term'] ) ? sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';
			$all     = isset( $_GET['all'] ) ? (bool) intval( $_GET['all'] ) : false;
			$tax     = isset( $_GET['taxonomy'] ) ? sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) ) : '';
			$exclude = isset( $_GET['selected'] ) ? array_map( 'intval', $_GET['selected'] ) : array();

			if ( ( ! $term && ! $all ) || ! $tax ) {
				wp_die();
			}

			$result = array();
			$terms  = get_terms(
				array(
					'taxonomy'   => $tax,
					'search'     => $term,
					'hide_empty' => false,
				)
			);

			if ( is_wp_error( $terms ) ) {
				wp_die();
			}

			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term_obj ) {
					if ( in_array( (int) $term_obj->term_id, $exclude, true ) ) {
						continue;
					}

					if ( ! $term_obj->parent ) {
						$result[ $term_obj->term_id ] = $term_obj->name;
					} else {
						$term_tmp  = $term_obj;
						$term_name = $term_obj->name;

						do {
							$term_tmp = get_term( $term_tmp->parent, $tax );

							if ( ! $term_tmp || is_wp_error( $term_tmp ) ) {
								break;
							}

							$term_name = "{$term_tmp->name} > {$term_name}";
						} while ( $term_tmp->parent );

						$result[ $term_obj->term_id ] = $term_name;
					}
				}
			}

			wp_send_json( apply_filters( 'yith_wcan_json_search_found_terms', $result, $term, $tax ) );
		}

		/* === TOOLS === */

		/**
		 * Register available plugin tools
		 *
		 * @param array $tools Available tools.
		 * @return array Filtered array of tools.
		 */
		public function register_tools( $tools ) {
			$additional_tools = array(
				'clear_filter_transient' => array(
					'name'     => _x( 'Clear Product Filter transients', '[ADMIN] WooCommerce Tools tab, name of the tool', 'yith-woocommerce-ajax-navigation' ),
					'button'   => _x( 'Clear', '[ADMIN] WooCommerce Tools tab, button for the tool', 'yith-woocommerce-ajax-navigation' ),
					'desc'     => _x( 'This will clear all transients related to the YITH WooCommerce AJAX Product Filter plugin. It may be useful if you changed your product\'s configuration, and filters do not display the expected results.', '[ADMIN] WooCommerce Tools tab, description of the tool', 'yith-woocommerce-ajax-navigation' ),
					'callback' => array( 'YITH_WCAN_Cache_Helper', 'delete_transients' ),
				),
				'run_widget_upgrade'     => array(
					'name'     => _x( 'Run filter widgets upgrade', '[ADMIN] WooCommerce Tools tab, name of the tool', 'yith-woocommerce-ajax-navigation' ),
					'button'   => _x( 'Run', '[ADMIN] WooCommerce Tools tab, button for the tool', 'yith-woocommerce-ajax-navigation' ),
					'desc'     => _x( 'This will create a preset for any sidebar of your shop containing filter widgets; preset will be configured to match widgets specifications', '[ADMIN] WooCommerce Tools tab, description of the tool', 'yith-woocommerce-ajax-navigation' ),
					'callback' => array( YITH_WCAN_Presets(), 'do_widget_upgrade' ),
				),
			);

			$tools = array_merge(
				$tools,
				$additional_tools
			);

			return $tools;
		}

		/* === PLUGIN META === */

		/**
		 * Add action links to plugin row in plugins.php admin page
		 *
		 * @param array $links Array of links available for the plugin.
		 *
		 * @return   mixed Array
		 * @use      plugin_action_links_{$plugin_file_name}
		 * @since    1.0
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, $this->panel_page, defined( 'YITH_WCAN_PREMIUM' ), YITH_WCAN_SLUG );

			return $links;
		}

		/**
		 * Adds meta links to plugin row in plugins.php admin page
		 *
		 * @param array  $new_row_meta_args Array of data to filter.
		 * @param array  $plugin_meta       Array of plugin meta.
		 * @param string $plugin_file       Path to init file.
		 * @param array  $plugin_data       Array of plugin data.
		 * @param string $status            Not used.
		 * @param string $init_file         Constant containing plugin int path.
		 *
		 * @return   array
		 * @since    1.0
		 * @use      plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCAN_INIT' ) {
			if ( ! defined( $init_file ) || constant( $init_file ) !== $plugin_file ) {
				return $new_row_meta_args;
			}

			$new_row_meta_args['slug']        = 'yith-woocommerce-ajax-product-filter';
			$new_row_meta_args['is_premium']  = defined( 'YITH_WCAN_PREMIUM' );
			$new_row_meta_args['is_extended'] = defined( 'YITH_WCAN_EXTENDED' );

			if ( defined( 'YITH_WCAN_FREE_INIT' ) ) {
				$new_row_meta_args['support'] = array(
					'url' => 'https://wordpress.org/support/plugin/yith-woocommerce-ajax-navigation',
				);
			}

			return $new_row_meta_args;
		}
	}
}
