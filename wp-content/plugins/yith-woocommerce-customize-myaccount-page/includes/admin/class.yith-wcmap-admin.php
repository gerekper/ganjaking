<?php
/**
 * Admin class
 *
 * @author  YITH
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

if ( ! class_exists( 'YITH_WCMAP_Admin' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMAP_Admin {

		/**
		 * Ajax action handler
		 *
		 * @const string
		 */
		const AJAX_ACTION = 'ywcmap_items_handler_admin';

		/**
		 * Customize my account panel page
		 *
		 * @const string
		 */
		const PANEL_PAGE = 'yith_wcmap_panel';

		/**
		 * Plugin options
		 *
		 * @since  1.0.0
		 * @var array
		 * @access public
		 */
		public $options = array();

		/**
		 * Panel Object
		 *
		 * @var YIT_Plugin_Panel_WooCommerce
		 */
		protected $panel;

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct() {

			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );
			add_action( 'yith_wcmap_admin_items_list', array( $this, 'items_list' ) );

			// Add action links.
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCMAP_DIR . '/' . basename( YITH_WCMAP_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			// Handle item admin Ajax.
			add_action( 'wp_ajax_ywcmap_items_handler_admin', array( $this, 'ajax_items_handler' ) );
			add_action( 'admin_init', array( $this, 'save_items_list' ) );
			add_action( 'admin_init', array( $this, 'reset_items_default' ) );

			// Let's filter the media library.
			add_action( 'pre_get_posts', array( $this, 'filter_media_library' ), 10, 1 );
			// Add custom endpoint to menu metabox.
			add_filter( 'woocommerce_custom_nav_menu_items', array( $this, 'custom_nav_items' ), 10, 1 );

			// register strings for translation.
			add_action( 'admin_init', array( $this, 'register_strings_translations' ), 99 );
		}

		/**
		 * Enqueue scripts
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function enqueue_scripts() {
			// phpcs:disable WordPress.Security.NonceVerification
			if ( isset( $_GET['page'] ) && sanitize_text_field( wp_unslash( $_GET['page'] ) ) === self::PANEL_PAGE ) {

				$min = ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ) ? '.min' : '';

				wp_register_style( 'yith_wcmap', YITH_WCMAP_ASSETS_URL . '/css/ywcmap-admin.css', array(), YITH_WCMAP_VERSION );
				wp_register_script( 'nestable', YITH_WCMAP_ASSETS_URL . '/js/jquery.nestable' . $min . '.js', array( 'jquery' ), YITH_WCMAP_VERSION, true );
				wp_register_script( 'yith_wcmap', YITH_WCMAP_ASSETS_URL . '/js/ywcmap-admin' . $min . '.js', array( 'jquery', 'nestable', 'jquery-ui-dialog' ), YITH_WCMAP_VERSION, true );
				// font awesome.
				wp_register_style( 'font-awesome', YITH_WCMAP_ASSETS_URL . '/css/font-awesome.min.css', array(), YITH_WCMAP_VERSION );

				wp_enqueue_style( 'font-awesome' );
				wp_enqueue_style( 'yith_wcmap' );
				wp_enqueue_script( 'yith_wcmap' );

				// enqueue select2 script registered by WooCommerce.
				wp_enqueue_script( 'select2' );
				if ( ! isset( $_GET['tab'] ) || in_array( sanitize_text_field( wp_unslash( $_GET['tab'] ) ), array( 'items', 'banners' ), true ) ) {
					wp_dequeue_script( 'wc-enhanced-select' );
					wp_enqueue_script( 'selectWoo' );
					wp_localize_script( 'yith_wcmap', 'ywcmap_icons', yith_wcmap_get_icon_list() );
				}

				wp_localize_script(
					'yith_wcmap',
					'ywcmap',
					array(
						'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
						'ajaxAction'      => self::AJAX_ACTION,
						'ajaxNonce'       => wp_create_nonce( self::AJAX_ACTION ),
						'page'            => self::PANEL_PAGE,
						'bannerShortcode' => '[yith_wcmap_banner ids="{{banners}}"]',
						'removeAlert'     => __( 'Are you sure that you want to delete this endpoint?', 'yith-woocommerce-customize-myaccount-page' ),
					)
				);
			}
			// phpcs:enable WordPress.Security.NonceVerification
		}

		/**
		 * Action Links
		 * add the action links to plugin admin page
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @param array $links Links plugin array.
		 * @return mixed
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, self::PANEL_PAGE, true, YITH_WCMAP_SLUG );

			return $links;
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      /Yit_Plugin_Panel class
		 * @return   void
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {

			if ( ! empty( $this->panel ) ) {
				return;
			}

			$admin_tabs = array(
				'items'    => __( 'Endpoints', 'yith-woocommerce-customize-myaccount-page' ),
				'general'  => __( 'General Settings', 'yith-woocommerce-customize-myaccount-page' ),
				'style'    => __( 'Style Options', 'yith-woocommerce-customize-myaccount-page' ),
				'banners'  => __( 'Banners', 'yith-woocommerce-customize-myaccount-page' ),
				'security' => __( 'Security Options', 'yith-woocommerce-customize-myaccount-page' ),
			);

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => 'YITH WooCommerce Customize My Account Page',
				'menu_title'       => 'Customize My Account Page',
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => self::PANEL_PAGE,
				'admin-tabs'       => apply_filters( 'yith_wcmap_admin_tabs', $admin_tabs ),
				'options-path'     => YITH_WCMAP_DIR . '/plugin-options',
				'class'            => yith_set_wrapper_class(),
				'plugin_slug'      => YITH_WCMAP_SLUG,
				'help_tab'         => array(
					'main_video' => array(
						'desc' => _x( 'Check this video to learn how to <b>manage and customize your customer’s My Account Page</b>', '[HELP TAB] Video title', 'yith-woocommerce-customize-myaccount-page' ),
						'url'  => array(
							'en' => 'https://www.youtube.com/watch?v=ETTEuWRp00o',
							'it' => 'https://www.youtube.com/watch?v=omm1WK_AEzI',
							'es' => 'https://www.youtube.com/watch?v=5RTuT-thEjE',
						),
					),
					'playlists'  => array(
						'en' => 'https://www.youtube.com/watch?v=ETTEuWRp00o&list=PLDriKG-6905npKYDuuPK_bma2b800SfcA',
						'it' => 'https://www.youtube.com/watch?v=omm1WK_AEzI&list=PL9c19edGMs0-qC6DNHxflJ2w-3XWptwK1',
						'es' => 'https://www.youtube.com/watch?v=5RTuT-thEjE&list=PL9Ka3j92PYJOsiu5_wN2SactHEeGcojYY',
					),
					'hc_url'     => 'https://support.yithemes.com/hc/en-us/categories/360003468777-YITH-WOOCOMMERCE-CUSTOMIZE-MY-ACCOUNT-PAGE',
				),
			);

			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once YITH_WCMAP_DIR . '/plugin-fw/lib/yit-plugin-panel-wc.php';
			}

			$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );

		}

		/**
		 * Plugin row meta. Add the action links to plugin admin page
		 *
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      plugin_row_meta
		 * @param array    $new_row_meta_args An array of plugin row meta.
		 * @param string[] $plugin_meta An array of the plugin's metadata,
		 *                                    including the version, author,
		 *                                    author URI, and plugin URI.
		 * @param string   $plugin_file Path to the plugin file relative to the plugins directory.
		 * @param array    $plugin_data An array of plugin data.
		 * @param string   $status Status of the plugin. Defaults are 'All', 'Active',
		 *                                    'Inactive', 'Recently Activated', 'Upgrade', 'Must-Use',
		 *                                    'Drop-ins', 'Search', 'Paused'.
		 * @return   Array
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status ) {
			if ( defined( 'YITH_WCMAP_INIT' ) && YITH_WCMAP_INIT === $plugin_file ) {
				$new_row_meta_args['slug']       = YITH_WCMAP_SLUG;
				$new_row_meta_args['live_demo']  = array( 'url' => 'https://plugins.yithemes.com/yith-woocommerce-customize-my-account-page/' );
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;
		}

		/**
		 * Create new Woocommerce admin field
		 *
		 * @access public
		 * @since  1.0.0
		 * @return void
		 */
		public function items_list() {

			// Get icon list.
			if ( ! class_exists( 'YIT_Plugin_Common' ) ) {
				require_once YITH_WCMAP_DIR . '/plugin-fw/lib/yit-plugin-common.php';
			}

			YITH_WCMAP()->items->init();

			// Get endpoints.
			$args = apply_filters(
				'yith_wcmap_admin_endpoints_template',
				array(
					'value' => json_decode( get_option( 'yith_wcmap_endpoint', '' ), true ),
					'items' => YITH_WCMAP()->items->get_items(),
				)
			);

			extract( $args ); // phpcs:ignore
			include YITH_WCMAP_TEMPLATE_PATH . '/admin/items-list.php';

			// Include items template.
			include YITH_WCMAP_TEMPLATE_PATH . '/admin/items-template.php';
		}

		/**
		 * Handle AJAX admin requests
		 *
		 * @since 3.0.0
		 * @author Francesco Licandro
		 * @return void
		 */
		public function ajax_items_handler() {

			check_ajax_referer( self::AJAX_ACTION, 'security' );

			$request = ! empty( $_POST['request'] ) ? sanitize_text_field( wp_unslash( $_POST['request'] ) ) : '';
			if ( ! empty( $request ) ) {
				$request = 'handle_ajax_' . $request;
			}

			if ( empty( $request ) || ! is_callable( array( $this, $request ) ) ) {
				// Request missing.
				wp_die( - 1, 403 );
			}

			$res = $this->$request();
			wp_send_json_success( $res );
		}

		/**
		 * Handle AJAX item activation/deactivation
		 *
		 * @since 3.0.0
		 * @author Francesco Licandro
		 * @return boolean
		 */
		protected function handle_ajax_activate() {
			// phpcs:disable WordPress.Security.NonceVerification
			$item  = ! empty( $_POST['item'] ) ? sanitize_title( wp_unslash( $_POST['item'] ) ) : '';
			$value = ! empty( $_POST['value'] ) ? sanitize_text_field( wp_unslash( $_POST['value'] ) ) : '';

			if ( empty( $item ) || empty( $value ) ) {
				// Params missing or item doesn't exists.
				wp_die( - 1, 403 );
			}

			if ( ! YITH_WCMAP()->items->change_status_item( $item, 'yes' === $value ) ) {
				wp_send_json_error();
			};

			return true;
			// phpcs:enable WordPress.Security.NonceVerification
		}

		/**
		 * Handle AJAX item add
		 *
		 * @since 3.0.0
		 * @author Francesco Licandro
		 * @return mixed
		 */
		protected function handle_ajax_add() {

			// phpcs:disable WordPress.Security.NonceVerification

			$type = ! empty( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : 'endpoint';
			$data = isset( $_POST['yith_wcmap_endpoint_new'] ) ? $_POST['yith_wcmap_endpoint_new'] : array(); // phpcs:ignore

			if ( empty( $data ) ) {
				// Params missing or item doesn't exists.
				wp_die( - 1, 403 );
			}

			$item_key = YITH_WCMAP()->items->add_item( $type, $data );
			if ( ! $item_key ) {
				wp_send_json_error();
			};

			// Get the item html.
			ob_start();
			yith_wcmap_admin_print_single_item(
				array(
					'item_key' => $item_key,
					'type'     => $type,
					'options'  => YITH_WCMAP()->items->get_single_item( $item_key ),
				)
			);
			$html = ob_get_clean();

			// Reset options for rewrite rules.
			update_option( 'yith_wcmap_flush_rewrite_rules', 1 );

			return array(
				'id'   => $item_key,
				'html' => $html,
			);

			// phpcs:enable WordPress.Security.NonceVerification
		}

		/**
		 * Handle AJAX item save
		 *
		 * @since 3.0.0
		 * @author Francesco Licandro
		 * @return mixed
		 */
		protected function handle_ajax_save() {

			// phpcs:disable WordPress.Security.NonceVerification

			$item = ! empty( $_POST['item'] ) ? sanitize_title( wp_unslash( $_POST['item'] ) ) : '';
			$type = ! empty( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : 'endpoint';

			if ( empty( $item ) ) {
				// Item doesn't exists.
				wp_die( - 1, 403 );
			}

			if ( ! $this->save_single_item( $item, $type ) ) {
				wp_send_json_error();
			};

			// Get the item html.
			ob_start();
			yith_wcmap_admin_print_single_item(
				array(
					'item_key' => $item,
					'type'     => $type,
					'options'  => YITH_WCMAP()->items->get_single_item( $item ),
				)
			);
			$html = ob_get_clean();

			// Reset options for rewrite rules.
			update_option( 'yith_wcmap_flush_rewrite_rules', 1 );

			return array(
				'id'   => $item,
				'html' => $html,
			);

			// phpcs:enable WordPress.Security.NonceVerification
		}

		/**
		 * Handle AJAX item remove
		 *
		 * @since 3.0.0
		 * @author Francesco Licandro
		 * @return boolean
		 */
		protected function handle_ajax_remove() {

			$item = ! empty( $_POST['item'] ) ? sanitize_title( wp_unslash( $_POST['item'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
			if ( empty( $item ) ) {
				// Params missing or item doesn't exists.
				wp_die( - 1, 403 );
			}

			if ( ! YITH_WCMAP()->items->remove_item( $item ) ) {
				wp_send_json_error();
			};

			return true;
		}

		/**
		 * Save the endpoint options field
		 *
		 * @access public
		 * @since  3.0.0
		 * @return void
		 */
		public function save_items_list() {

			if ( ! isset( $_POST['yith_wcmap_items_save'] ) || ! isset( $_POST['_wpnonce'] ) || empty( $_POST['yith_wcmap_endpoint'] )
				 || ! wp_verify_nonce( $_POST['_wpnonce'], 'yith_wcmap_items_save' ) ) {
				return;
			}

			$decoded_fields = json_decode( wp_unslash( $_POST['yith_wcmap_endpoint'] ), true );
			$to_save        = array();

			foreach ( $decoded_fields as $decoded_field ) {

				if ( ! isset( $decoded_field['id'] ) ) {
					continue;
				}

				// Check for master key.
				$id                     = yith_wcmap_sanitize_item_key( $decoded_field['id'] );
				$to_save[ $id ]         = array();
				$to_save[ $id ]['type'] = $decoded_field['type'];

				$this->save_single_item( $id, $decoded_field['type'] );

				if ( ! empty( $decoded_field['children'] ) ) {
					foreach ( $decoded_field['children'] as $child ) {
						// Check for children key.
						$child_id                                        = yith_wcmap_sanitize_item_key( $child['id'] );
						$to_save[ $id ]['children'][ $child_id ]         = array();
						$to_save[ $id ]['children'][ $child_id ]['type'] = $child['type'];

						$this->save_single_item( $child_id, $child['type'] );
					}
				}
			}

			// Reset options for rewrite rules.
			update_option( 'yith_wcmap_flush_rewrite_rules', 1 );
			update_option( 'yith_wcmap_endpoint', wp_json_encode( $to_save ) );

			wp_safe_redirect( admin_url( 'admin.php?page=' . self::PANEL_PAGE ) );
			exit;
		}

		/**
		 * Save the endpoint options field
		 *
		 * @access public
		 * @since  3.0.0
		 * @return void
		 */
		public function reset_items_default() {

			if ( ! isset( $_POST['yith_wcmap_items_reset'] ) || ! isset( $_POST['_wpnonce'] )
				 || ! wp_verify_nonce( $_POST['_wpnonce'], 'yith_wcmap_items_reset' ) ) {
				return;
			}

			$items = YITH_WCMAP()->items->get_items_keys();
			foreach ( $items as $item ) {
				YITH_WCMAP()->items->remove_item( $item );
			}

			// Delete main option.
			delete_option( 'yith_wcmap_endpoint' );
			// Delete also endpoints flush option.
			delete_option( 'yith-wcmap-flush-rewrite-rules' );

			wp_safe_redirect( admin_url( 'admin.php?page=' . self::PANEL_PAGE ) );
			exit;
		}

		/**
		 * Save a single item
		 *
		 * @since 3.0.0
		 * @access public
		 * @author Francesco Licandro
		 * @param string $id The item ID.
		 * @param string $type The item type.
		 * @return boolean True on success, false otherwise.
		 */
		protected function save_single_item( $id, $type ) {
			$raw_id = rawurldecode( $id );
			$data	= isset( $_POST[ 'yith_wcmap_endpoint_' . $raw_id ] ) ? $_POST[ 'yith_wcmap_endpoint_' . $raw_id ] : array(); // phpcs:ignore
			if ( empty( $data ) ) {
				return false;
			}

			return YITH_WCMAP()->items->save_item( $id, $type, $data );
		}

		/**
		 * Filter media library query form hide users avatar
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param object $q The query object.
		 */
		public function filter_media_library( $q ) {

			if ( apply_filters( 'yith_wcmap_disable_filter_media_library', false ) ) {
				return;
			}

			$post_ids      = get_option( 'yith_wcmap_users_avatar_ids', array() );
			$is_attachment = 'attachment' === $q->get( 'post_type' );

			if ( ! $is_attachment || empty( $post_ids ) || ! is_array( $post_ids ) ) {
				return;
			}

			$this->_filter_media_library( $q, $post_ids );
		}

		/**
		 * Filter media library query
		 *
		 * @access private
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param object $q The query object.
		 * @param array  $post_ids Post to filter.
		 */
		private function _filter_media_library( $q, $post_ids ) {
			$q->set( 'post__not_in', $post_ids );
		}

		/**
		 * Register menu items strings for translations
		 *
		 * @since  2.3.0
		 * @author Francesco Licandro
		 * @param array $items The items array.
		 * @return void
		 */
		public function register_strings_translations( $items = array() ) {
			// Get items if empty.
			if ( empty( $items ) ) {
				$items = YITH_WCMAP()->items->get_items();
			}
			// First register string for translations then remove disable.
			foreach ( $items as $key => $options ) {
				if ( ! empty( $options['label'] ) ) {
					$this->register_single_string( $key, $options['label'] );
				}
				// Register also url for links.
				if ( ! empty( $options['url'] ) ) {
					$this->register_single_string( $key . '_url', $options['url'] );
				}
				if ( ! empty( $options['content'] ) ) {
					$this->register_single_string( $key . '_content', $options['content'] );
				}
				// Check if child is active.
				if ( ! empty( $options['children'] ) ) {
					$this->register_strings_translations( $options['children'] );
				}
			}
		}

		/**
		 * Register single string for translation
		 *
		 * @since  2.3.0
		 * @author Francesco Licandro
		 * @param string $key The string key.
		 * @param string $string The string value.
		 * @return void
		 */
		protected function register_single_string( $key, $string ) {
			if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
				do_action( 'wpml_register_single_string', 'yith-woocommerce-customize-myaccount-page', 'plugin_yit_wcmap_' . $key, $string );
			} elseif ( defined( 'POLYLANG_VERSION' ) && function_exists( 'pll_register_string' ) ) {
				pll_register_string( $key, $string, 'yith-woocommerce-customize-myaccount-page' );
			}
		}

		/**
		 * Add custom endpoints to menu metabox
		 *
		 * @since  2.6.0
		 * @author Francesco Licandro
		 * @param array $endpoints The endpoint items.
		 * @return array
		 */
		public function custom_nav_items( $endpoints ) {
			$my_endpoints = yith_wcmap_endpoints_list();
			$endpoints    = array_merge( $endpoints, $my_endpoints );

			return $endpoints;
		}
	}
}
