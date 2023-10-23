<?php
/**
 * Admin class
 *
 * @author  YITH <plugins@yithemes.com>
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
		 * Link to landing page on yithemes.com
		 *
		 * @since 1.0.0
		 * @var string
		 */
		protected $premium_landing_url = 'https://yithemes.com/themes/plugins/yith-woocommerce-customize-myaccount-page/';

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
			$this->load_admin_functions();

			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );
			add_action( 'yith_wcmap_admin_items_list', array( $this, 'items_list' ) );

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
		 * Load admin functions
		 *
		 * @since  3.12.0
		 * @return void
		 */
		protected function load_admin_functions() {
			include_once 'yith-wcmap-admin-functions.php';
		}

		/**
		 * Enqueue scripts
		 *
		 * @since  1.0.0
		 */
		public function enqueue_scripts() {
			// phpcs:disable WordPress.Security.NonceVerification
			if ( isset( $_GET['page'] ) && sanitize_text_field( wp_unslash( $_GET['page'] ) ) === self::PANEL_PAGE ) {

				$min = ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ) ? '.min' : '';

				wp_register_style( 'yith_wcmap', YITH_WCMAP_ASSETS_URL . '/css/ywcmap-admin' . $min . '.css', array(), YITH_WCMAP_VERSION );
				wp_register_script( 'nestable', YITH_WCMAP_ASSETS_URL . '/js/jquery.nestable' . $min . '.js', array( 'jquery' ), YITH_WCMAP_VERSION, true );
				wp_register_script( 'yith_wcmap', YITH_WCMAP_ASSETS_URL . '/js/ywcmap-admin' . $min . '.js', array( 'jquery', 'nestable', 'jquery-ui-dialog' ), YITH_WCMAP_VERSION, true );

				wp_enqueue_style( 'yith_wcmap' );
				wp_enqueue_script( 'yith_wcmap' );
				// enqueue select2 script registered by WooCommerce.
				wp_enqueue_script( 'select2' );
				if ( ! isset( $_GET['tab'] ) || in_array( sanitize_text_field( wp_unslash( $_GET['tab'] ) ), array( 'items', 'banners' ), true ) ) {
					wp_dequeue_script( 'wc-enhanced-select' );
					wp_enqueue_script( 'selectWoo' );
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
		 * Get admin panel tabs
		 *
		 * @since  3.12.0
		 * @return array
		 */
		public function get_admin_tabs() {
			/**
			 * APPLY_FILTERS: yith_wcmap_admin_tabs
			 *
			 * Filter the available tabs in the plugin panel.
			 *
			 * @param array $tabs Admin tabs.
			 *
			 * @return array
			 */
			return apply_filters(
				'yith_wcmap_admin_tabs',
				array(
					'endpoints' => array(
						'title'       => _x( 'Endpoints', 'Endpoints tab name', 'yith-woocommerce-customize-myaccount-page' ),
						'icon'        => '
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
								<path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75L2.25 12l4.179 2.25m0-4.5l5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0l4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0l-5.571 3-5.571-3" />
							</svg>
					 	',
						'description' => _x( 'An “Endpoint” is the content shown as a subtab on your customers\' My Account page. With this plugin, you can disable WooCommerce default endpoints (Dashboard, Orders, etc.), edit their content, and change the order in which they’re displayed. You can add new endpoints using the dedicated button.', 'Admin endpoints tab description ', 'yith-woocommerce-customize-myaccount-page' ),
					),
					'settings'  => array(
						'title'       => _x( 'Settings', 'Settings tab name', 'yith-woocommerce-customize-myaccount-page' ),
						'icon'        => 'settings',
					),
				)
			);
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @since    1.0
		 * @use      /Yit_Plugin_Panel class
		 * @return   void
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {

			if ( ! empty( $this->panel ) ) {
				return;
			}

			$premium_tab = array(
				'landing_page_url' => $this->get_premium_landing_uri(),
				'premium_features' => array(
					__( 'Choose between <b>3 different menu styles</b>: no borders, modern or simple', 'yith-woocommerce-customize-myaccount-page' ),
					__( 'Customize the account color scheme', 'yith-woocommerce-customize-myaccount-page' ),
					__( 'Create groups of <b>nested endpoints</b>', 'yith-woocommerce-customize-myaccount-page' ),
					__( 'Add custom URL links', 'yith-woocommerce-customize-myaccount-page' ),
					__( 'Upload custom icons to visually enhance the endpoints', 'yith-woocommerce-customize-myaccount-page' ),
					__( 'Show endpoints only to specific user roles', 'yith-woocommerce-customize-myaccount-page' ),
					__( 'Create <b>custom banners</b> to show as endpoint content', 'yith-woocommerce-customize-myaccount-page' ),
					__( 'Add Google reCAPTCHA (v2) to the register form on My Account', 'yith-woocommerce-customize-myaccount-page' ),
					__( 'Block specific email domains so users cannot create an account with those domains', 'yith-woocommerce-customize-myaccount-page' ),
					__( '<b>Allow users to upload their own custom profile pictures</b>', 'yith-woocommerce-customize-myaccount-page' ),
					__( '<b>Regular updates, translations and premium support</b>', 'yith-woocommerce-customize-myaccount-page' ),
				),
				'main_image_url'   => YITH_WCMAP_ASSETS_URL . '/images/get-premium-cma.jpg',
			);

			/**
			 * APPLY_FILTERS: yith_wcmap_admin_panel_args
			 *
			 * Filters the array with the arguments to build the plugin panel.
			 *
			 * @param array $args Array of arguments.
			 *
			 * @return array
			 */
			$args = apply_filters(
				'yith_wcmap_admin_panel_args',
				array(
					'ui_version'       => 2,
					'create_menu_page' => true,
					'parent_slug'      => '',
					'plugin_slug'      => YITH_WCMAP_SLUG,
					'page_title'       => 'YITH WooCommerce Customize My Account Page',
					'menu_title'       => 'Customize My Account Page',
					'capability'       => 'manage_options',
					'parent'           => '',
					'class'            => yith_set_wrapper_class(),
					'parent_page'      => 'yith_plugin_panel',
					'page'             => self::PANEL_PAGE,
					'admin-tabs'       => $this->get_admin_tabs(),
					'options-path'     => YITH_WCMAP_DIR . '/plugin-options',
					'premium_tab'      => $premium_tab,
					'is_premium'       => defined( 'YITH_WCMAP_PREMIUM' ),
					'is_extended'      => defined( 'YITH_WCMAP_EXTENDED' ),
				)
			);

			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once YITH_WCMAP_DIR . '/plugin-fw/lib/yit-plugin-panel-wc.php';
			}

			$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );

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
			/**
			 * APPLY_FILTERS: yith_wcmap_admin_endpoints_template
			 *
			 * Filters the array with the arguments to print the endpoints in the plugin panel.
			 *
			 * @param array Array of arguments.
			 *
			 * @return array
			 */
			$args = apply_filters(
				'yith_wcmap_admin_endpoints_template',
				array(
					'value'   => json_decode( get_option( 'yith_wcmap_endpoint', '' ), true ),
					'items'   => YITH_WCMAP()->items->get_items(),
					'actions' => array(
						'endpoint' => array(
							'label'     => __( 'Add endpoint', 'yith-woocommerce-customize-myaccount-page' ),
							'alt-label' => __( 'Close new endpoint', 'yith-woocommerce-customize-myaccount-page' ),
						),
					),
				)
			);

			extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
			include YITH_WCMAP_DIR . 'includes/admin/views/items-list.php';
			// Include items template.
			include YITH_WCMAP_DIR . 'includes/admin/views/items-template.php';
		}

		/**
		 * Handle AJAX admin requests
		 *
		 * @since  3.0.0
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
				wp_die( -1, 403 );
			}

			$res = $this->$request();
			wp_send_json_success( $res );
		}

		/**
		 * Handle AJAX item activation/deactivation
		 *
		 * @since  3.0.0
		 * @return boolean
		 */
		protected function handle_ajax_activate() {
			// phpcs:disable WordPress.Security.NonceVerification
			$item  = ! empty( $_POST['item'] ) ? sanitize_title( wp_unslash( $_POST['item'] ) ) : '';
			$value = ! empty( $_POST['value'] ) ? sanitize_text_field( wp_unslash( $_POST['value'] ) ) : '';

			if ( empty( $item ) || empty( $value ) ) {
				// Params missing or item doesn't exists.
				wp_die( -1, 403 );
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
		 * @since  3.0.0
		 * @return mixed
		 */
		protected function handle_ajax_add() {

			// phpcs:disable WordPress.Security.NonceVerification

			$type = ! empty( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : 'endpoint';
			$data = isset( $_POST['yith_wcmap_endpoint_new'] ) ? $_POST['yith_wcmap_endpoint_new'] : array(); // phpcs:ignore

			if ( empty( $data ) ) {
				// Params missing or item doesn't exists.
				wp_die( -1, 403 );
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
		 * @since  3.0.0
		 * @return mixed
		 */
		protected function handle_ajax_save() {

			// phpcs:disable WordPress.Security.NonceVerification

			$item = ! empty( $_POST['item'] ) ? sanitize_title( wp_unslash( $_POST['item'] ) ) : '';
			$type = ! empty( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : 'endpoint';

			if ( empty( $item ) ) {
				// Item doesn't exists.
				wp_die( -1, 403 );
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
		 * Handle AJAX item reset
		 *
		 * @since  3.0.0
		 * @return mixed
		 */
		protected function handle_ajax_reset() {
			// phpcs:disable WordPress.Security.NonceVerification
			$item = ! empty( $_POST['item'] ) ? sanitize_title( wp_unslash( $_POST['item'] ) ) : '';
			$type = ! empty( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : 'endpoint';

			if ( empty( $item ) ) {
				// Item doesn't exists.
				wp_die( -1, 403 );
			}

			if ( ! YITH_WCMAP()->items->reset_item( $item ) ) {
				wp_send_json_error();
			}

			// Re-init items.
			YITH_WCMAP()->items->init( true );

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
		 * @since  3.0.0
		 * @return boolean
		 */
		protected function handle_ajax_remove() {

			$item = ! empty( $_POST['item'] ) ? sanitize_title( wp_unslash( $_POST['item'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
			if ( empty( $item ) ) {
				// Params missing or item doesn't exists.
				wp_die( -1, 403 );
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
		 * @since  3.0.0
		 * @access public
		 * @param string $id   The item ID.
		 * @param string $type The item type.
		 * @return boolean True on success, false otherwise.
		 */
		protected function save_single_item( $id, $type ) {
			$raw_id = rawurldecode( $id );
			$data   = isset( $_POST[ 'yith_wcmap_endpoint_' . $raw_id ] ) ? $_POST[ 'yith_wcmap_endpoint_' . $raw_id ] : array(); // phpcs:ignore
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
		 * @param object $q The query object.
		 */
		public function filter_media_library( $q ) {
			/**
			 * APPLY_FILTERS: yith_wcmap_disable_filter_media_library
			 *
			 * Filters whether to disable filtering the media library.
			 *
			 * @param bool $disable_filter_media_library Whether to disable filtering the media library or not.
			 *
			 * @return bool
			 */
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
		 * @param object $q        The query object.
		 * @param array  $post_ids Post to filter.
		 */
		private function _filter_media_library( $q, $post_ids ) {
			$q->set( 'post__not_in', $post_ids );
		}

		/**
		 * Register menu items strings for translations
		 *
		 * @since  2.3.0
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
		 * @param string $key    The string key.
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
		 * @param array $endpoints The endpoint items.
		 * @return array
		 */
		public function custom_nav_items( $endpoints ) {
			$my_endpoints = yith_wcmap_endpoints_list();
			$endpoints    = array_merge( $endpoints, $my_endpoints );

			return $endpoints;
		}

		/**
		 * Get the premium landing uri
		 *
		 * @since   1.0.0
		 * @return  string The premium landing link
		 */
		public function get_premium_landing_uri() {
			return apply_filters( 'yith_plugin_fw_premium_landing_uri', $this->premium_landing_url, YITH_WCMAP_SLUG );
		}
	}
}
