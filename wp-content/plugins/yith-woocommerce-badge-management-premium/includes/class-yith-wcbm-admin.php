<?php
/**
 * Admin class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagement\Classes
 */

defined( 'YITH_WCBM' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBM_Admin' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 */
	class YITH_WCBM_Admin {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCBM_Admin
		 */
		protected static $instance;

		/**
		 * Plugin options
		 *
		 * @var array
		 */
		public $options = array();

		/**
		 * Plugin version
		 *
		 * @var string
		 */
		public $version = YITH_WCBM_VERSION;

		/**
		 * The plugin panel
		 *
		 * @var YIT_Plugin_Panel_WooCommerce
		 */
		protected $panel;

		/**
		 * Premium version landing link
		 *
		 * @var string
		 */
		protected $premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-badge-management/';

		/**
		 * Panel page
		 *
		 * @var string
		 */
		protected $panel_page = 'yith_wcbm_panel';

		/**
		 * Documentation URL
		 *
		 * @var string
		 */
		public $doc_url = 'https://docs.yithemes.com/yith-woocommerce-badge-management/';

		/**
		 * Demo URL
		 *
		 * @var string
		 */
		public $demo_url = 'https://plugins.yithemes.com/yith-woocommerce-badge-management';

		/**
		 * YITH Site URL
		 *
		 * @var string
		 */
		public $yith_url = 'https://www.yithemes.com';

		/**
		 * Landing URL
		 *
		 * @var string
		 */
		public $plugin_url = 'https://yithemes.com/themes/plugins/yith-woocommerce-badge-management/';

		/**
		 * Metabox IDs in which the savings will not be handled by the plugin-fw
		 *
		 * @var array
		 */
		protected $custom_savings_metabox = array( 'yith-wcbm-metabox' );

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WCBM_Admin | YITH_WCBM_Admin_Premium
		 */
		public static function get_instance() {
			$self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

			return ! is_null( $self::$instance ) ? $self::$instance : $self::$instance = new $self();
		}

		/**
		 * Constructor
		 */
		protected function __construct() {
			// Plugin Row meta in Plugins panel.
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCBM_DIR . '/' . basename( YITH_WCBM_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 3 );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

			add_action( 'admin_init', array( $this, 'add_metaboxes' ), 10 );
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );

			add_action( 'wp_ajax_yith_wcbm_toggle_enable_badge', array( $this, 'toggle_enable_badge' ) );

			// Add Capabilities to Administrator and Shop Manager.
			add_action( 'admin_init', array( $this, 'add_capabilities' ) );

			add_action( 'edit_form_top', array( $this, 'maybe_update_badge_options' ) );

			// Force screen layout to one column in Badge editing view.
			add_filter( 'get_user_option_screen_layout_' . YITH_WCBM_Post_Types::$badge, '__return_true' );

			// Handle custom fields display.
			add_action( 'yith_wcbm_print_badge_preview', array( $this, 'print_badge_preview' ) );
			add_action( 'yith_wcbm_print_badge_library_field', array( $this, 'print_badge_library_field' ) );

			add_action( 'save_post', array( $this, 'delete_badge_meta_transient_for_product' ) );
		}

		/**
		 * Action Links
		 * add the action links to plugin admin page
		 *
		 * @param array $links Action links.
		 *
		 * @use      plugin_action_links_{$plugin_file_name}
		 * @return array
		 */
		public function action_links( $links ) {
			return yith_add_action_links( $links, $this->panel_page, defined( 'YITH_WCBM_PREMIUM' ), YITH_WCBM_SLUG );
		}

		/**
		 * Adds action links to plugin admin page
		 *
		 * @param array    $row_meta_args Row meta arguments.
		 * @param string[] $plugin_meta   An array of the plugin's metadata,
		 *                                including the version, author,
		 *                                author URI, and plugin URI.
		 * @param string   $plugin_file   Path to the plugin file relative to the plugins directory.
		 *
		 * @return array
		 */
		public function plugin_row_meta( $row_meta_args, $plugin_meta, $plugin_file ) {
			$init = '';
			if ( defined( 'YITH_WCBM_FREE_INIT' ) ) {
				$init = YITH_WCBM_FREE_INIT;
			} elseif ( defined( 'YITH_WCBM_INIT' ) ) {
				$init = YITH_WCBM_INIT;
			}

			if ( $init === $plugin_file ) {
				$row_meta_args['slug'] = 'yith-woocommerce-badge-management';
				if ( ! defined( 'YITH_WCBM_PREMIUM' ) ) {
					$row_meta_args['support'] = array( 'url' => 'https://wordpress.org/support/plugin/yith-woocommerce-badges-management' );
				} else {
					$row_meta_args['is_premium'] = true;
				}
			}

			return $row_meta_args;
		}

		/**
		 * Admin enqueue scripts
		 */
		public function admin_enqueue_scripts() {
			$screen_id  = function_exists( 'get_current_screen' ) ? get_current_screen()->id ?? false : false;
			$suffix     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$metabox_js = defined( 'YITH_WCBM_PREMIUM' ) ? "metabox-options-premium{$suffix}.js" : "metabox-options{$suffix}.js";

			wp_register_style( 'yith_wcbm_admin_style', YITH_WCBM_ASSETS_URL . 'css/admin.css', array(), YITH_WCBM_VERSION );
			wp_register_style( 'yith_wcbm_admin_icons', YITH_WCBM_ASSETS_URL . 'css/icons.css', array(), YITH_WCBM_VERSION );
			wp_register_script( 'yith_wcbm_metabox_options', YITH_WCBM_ASSETS_URL . 'js/' . $metabox_js, array( 'jquery', 'jquery-blockui', 'selectWoo', 'wp-util' ), YITH_WCBM_VERSION, true );

			if ( in_array( $screen_id, array( 'yith-wcbm-badge', 'edit-yith-wcbm-badge', 'product', 'yith-plugins_page_yith_wcbm_panel' ), true ) ) {
				wp_enqueue_style( 'yith_wcbm_admin_style' );
				wp_enqueue_style( 'yith_wcbm_admin_icons' );

				if ( in_array( $screen_id, array( 'yith-wcbm-badge', 'edit-yith-wcbm-badge' ), true ) ) {
					wp_enqueue_script( 'yith_wcbm_metabox_options' );
					YITH_WCBM_Frontend()->enqueue_scripts();

					if ( 'yith-wcbm-badge' === $screen_id ) {
						wp_enqueue_editor();
						wp_enqueue_style( 'wp-color-picker' );
					}
				}
			}

			$data_to_localize = array(
				'yith_wcbm_metabox_options' => array(
					'object_name' => 'yithWcbmMetaboxOptions',
					'data'        => array(
						'screenID'      => $screen_id,
						'ajaxurl'       => admin_url( 'admin-ajax.php' ),
						'actions'       => array(
							'toggleEnableBadge' => 'yith_wcbm_toggle_enable_badge',
						),
						'security'      => array(
							'toggleEnableBadge' => wp_create_nonce( 'yith_wcbm_toggle_enable_badge' ),
						),
						'imageBadges'   => $this->get_badges_to_localize( 'image' ),
						'i18n'          => array(
							'uploadAttachment' => __( 'Choose image', 'yith-woocommerce-badges-management' ),
							'deleteBadgeModal' => array(
								'title'         => _x( 'Confirm delete', '[ADMIN] Confirm delete modal title on badges page', 'yith-woocommerce-badges-management' ),
								// translators: %s is the badge rule name.
								'message'       => _x( 'Are you sure you want to delete <b>"%s"</b>?', '[ADMIN] Confirm delete modal message on badges page', 'yith-woocommerce-badges-management' ),
								'confirmButton' => _x( 'Delete', '[ADMIN] Confirm delete modal button on badges page', 'yith-woocommerce-badges-management' ),
							),
						),
						'addBadgeModal' => array(
							'title'   => __( 'Choose badge type', 'yith-woocommerce-badges-management' ),
							'content' => yith_wcbm_get_view_html( 'add-badge-modal-content.php' ),
						),
						'editor'        => array(
							'fonts'    => yith_wcbm_get_badge_editor_fonts(),
							'fontSize' => array(
								'min' => apply_filters( 'yith_wcbm_badge_text_editor_font_size_min', 5 ),
								'max' => apply_filters( 'yith_wcbm_badge_text_editor_font_size_max', 20 ),
							),
						),
					),
				),
			);

			foreach ( $data_to_localize as $handle => $data ) {
				wp_localize_script( $handle, $data['object_name'], $data['data'] );
			}
		}

		/**
		 * Retrieve the advanced badges HTML to localize
		 *
		 * @param string $type The badge type.
		 *
		 * @return array
		 */
		protected function get_badges_to_localize( $type ) {
			$badges_html = array();
			$args        = compact( 'type' );
			$badge_list  = defined( 'YITH_WCBM_PREMIUM' ) && YITH_WCBM_PREMIUM && function_exists( 'yith_wcbm_get_badges_list' ) ? yith_wcbm_get_badges_list( $type ) : yith_wcbm_get_local_badges_list( $type );
			foreach ( $badge_list as $badge_style ) {
				$args['style']       = $badge_style;
				$key                 = str_replace( '.svg', '', $badge_style );
				$badges_html[ $key ] = yith_wcbm_get_badge_svg( $args, true );
			}

			return $badges_html;
		}

		/**
		 * Add metabox
		 */
		public function add_metaboxes() {
			$metaboxes = $this->get_metaboxes();

			foreach ( $metaboxes as $metabox_id => $metabox_args ) {
				$defaults     = array(
					'context'  => 'normal',
					'priority' => 'default',
				);
				$metabox_args = wp_parse_args( $metabox_args, $defaults );
				$metabox      = YIT_Metabox( $metabox_id );
				$metabox->init( $metabox_args );
				if ( in_array( $metabox_id, $this->custom_savings_metabox, true ) && is_callable( array( $metabox, 'save_postdata' ) ) ) {
					remove_action( 'save_post', array( $metabox, 'save_postdata' ), 10 );
				}
			}
			remove_meta_box( 'submitdiv', YITH_WCBM_Post_Types::$badge, 'side' );
		}

		/**
		 * Add Admin notices
		 */
		public function admin_notices() {
			if ( yith_wcbm_admin()->is_panel() ) {
				if ( yith_wcbm_update_is_running() ) {
					// translators: %s is the name of the plugin that is updating data in background. Don't use the full stop at the end because there will be a smile emoji.
					$text        = sprintf( __( '<b>Update of %s:</b> we are updating badges and settings. The progress is automatic and can take few minutes. Please wait', 'yith-woocommerce-badges-management' ), YITH_WCBM_PLUGIN_NAME );
					$spinner_url = YITH_WCBM_ASSETS_URL . 'images/spinner.gif';
					echo '<div class="notice notice-success is-dismissible yith-wcbm-updating-notice"><img class="yith-wcbm-updating-notice__loader" src=" ' . esc_url( $spinner_url ) . '"><p>' . wp_kses_post( $text ) . '</p></div>';
				}
			}
		}

		/**
		 * Retrieve Metaboxes
		 *
		 * @return array
		 */
		protected function get_metaboxes() {
			$metaboxes = array();
			if ( file_exists( YITH_WCBM_DIR . '/plugin-options/metaboxes/metaboxes.php' ) ) {
				$metaboxes = include YITH_WCBM_DIR . '/plugin-options/metaboxes/metaboxes.php';
			}

			return $metaboxes;
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @since    1.0
		 * @use      YIT_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {

			if ( ! empty( $this->panel ) ) {
				return;
			}

			$admin_tabs_free = array(
				'badges'   => esc_html__( 'Badges', 'yith-woocommerce-badges-management' ),
				'settings' => esc_html__( 'General Settings', 'yith-woocommerce-badges-management' ),
			);

			$admin_tabs = apply_filters( 'yith_wcbm_settings_admin_tabs', $admin_tabs_free );

			$premium_tab = array(
				'landing_page_url' => $this->get_premium_landing_uri(),
				'premium_features' => array(
					__( 'Upload a custom image/icon to use as a badge', 'yith-woocommerce-badges-management' ),
					__( 'Access a free library with <b>60 ready-to-use badges</b> for Black Friday, Christmas, End-of-season sales, Cyber Monday, etc. (and we will add more!)', 'yith-woocommerce-badges-management' ),
					__( 'Unblock additional customization options: padding, margin, flip text,position, etc.', 'yith-woocommerce-badges-management' ),
					__( 'Create dynamic badges for on-sale products and <b>automatically show the discount % and the saving</b>', 'yith-woocommerce-badges-management' ),
					__( 'Create rules to automatically show badges in <b>on-sale / recent / low stock / in stock / back to order and featured products</b>', 'yith-woocommerce-badges-management' ),
					__( 'Create rules to automatically show badges in products of <b>specific categories / tags / shipping classes</b>', 'yith-woocommerce-badges-management' ),
					__( '<b>Schedule badges</b> to set when automatically show and hide them', 'yith-woocommerce-badges-management' ),
					__( 'Choose to show badges only to specific users, user roles or members', 'yith-woocommerce-badges-management' ),
					// translators: %s is the name of out plugin YITH WooCommerce Dynamic Pricing and Discounts.
					sprintf( __( 'Assign badges to discounts and promotions created with our %s', 'yith-woocommerce-badges-management' ), 'YITH WooCommerce Dynamic Pricing and Discounts' ),
					'<b>' . __( 'Regular updates, Translations and Premium Support', 'yith-woocommerce-badges-management' ) . '</b>',
				),
				'main_image_url'   => YITH_WCBM_ASSETS_URL . 'images/badges-get-premium.jpeg',
			);

			$args = array(
				'create_menu_page' => true,
				'class'            => yith_set_wrapper_class(),
				'parent_slug'      => '',
				'plugin_slug'      => YITH_WCBM_SLUG,
				'page_title'       => YITH_WCBM_PLUGIN_NAME,
				'menu_title'       => 'Badge Management',
				'capability'       => 'yes' === get_option( 'yith-wcbm-enable-shop-manager', 'no' ) ? 'manage_woocommerce' : 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yit_plugin_panel',
				'page'             => $this->panel_page,
				'links'            => $this->get_panel_sidebar_links(),
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_WCBM_DIR . '/plugin-options',
				'premium_tab'      => $premium_tab,
			);

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once 'plugin-fw/lib/yit-plugin-panel-wc.php';
			}

			$this->panel = new YIT_Plugin_Panel_WooCommerce( apply_filters( 'yith_wcbm_settings_admin_panel_args', $args ) );
		}

		/**
		 * Add badge management capabilities to Admin and Shop Manager
		 */
		public function add_capabilities() {
			$caps = yith_wcbm_create_capabilities( array( 'badge', 'badges' ) );

			$admin        = get_role( 'administrator' );
			$shop_manager = get_role( 'shop_manager' );

			foreach ( $caps as $cap => $value ) {
				if ( $admin ) {
					$admin->add_cap( $cap );
				}

				if ( $shop_manager ) {
					$shop_manager->add_cap( $cap );
				}
			}
		}

		/**
		 * Check if a badge is being edited and if so, update it
		 *
		 * @param WP_Post $post Post.
		 *
		 * @since 2.0.0
		 */
		public function maybe_update_badge_options( $post ) {
			if ( $post instanceof WP_Post ) {
				if ( YITH_WCBM_Post_Types::$badge === $post->post_type ) {
					if ( get_post_meta( $post->ID, '_badge_meta', true ) ) {
						defined( 'YITH_WCBM_PREMIUM' ) ? yith_wcbm_update_badge_meta_premium( $post->ID ) : yith_wcbm_update_badge_meta( $post->ID );
					}
				} elseif ( 'product' === $post->post_type ) {
					if ( get_post_meta( $post->ID, '_yith_wcbm_product_meta', true ) ) {
						yith_wcbm_update_product_badge_meta( $post->ID );
					}
				}
			}
		}

		/**
		 * Handle duplicate badge actions
		 *
		 * @since       1.2.11 (free version) | 1.2.27 (premium version)
		 * @depreacted  since 2.0 | Use clone_badges() method in YITH_WCBM_Badges class
		 */
		public function admin_action_duplicate_badge() {
			yith_wcbm_badges()->clone_badge();
		}

		/**
		 * Toggle Enable Badge
		 */
		public function toggle_enable_badge() {
			$response = array( 'success' => false );
			if ( isset( $_POST['security'], $_POST['badge_id'], $_POST['badge_enable'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'yith_wcbm_toggle_enable_badge' ) ) {
				update_post_meta( absint( $_POST['badge_id'] ), '_enabled', 'yes' === $_POST['badge_enable'] ? 'yes' : 'no' );
				$response['success'] = true;
			}
			wp_send_json( $response );
			exit();
		}

		/**
		 * Print Badge Preview
		 */
		public function print_badge_preview() {
			yith_wcbm_get_view( '/metaboxes/badge-preview.php' );
		}

		/**
		 * Print Badge Library Field
		 *
		 * @param array $args Field Args.
		 */
		public function print_badge_library_field( $args = array() ) {
			$defaults        = array(
				'id'           => '',
				'library'      => array(),
				'allow_upload' => 'no',
				'url'          => '',
			);
			$args            = wp_parse_args( $args, $defaults );
			$args['library'] = array_flip( $args['library'] );

			foreach ( $args['library'] as $badge_id => &$badge_url ) {
				$badge_url = $args['url'] . $badge_id;
			}

			if ( ! empty( $args['id'] ) ) {
				yith_wcbm_get_view( '/fields/badge-library.php', compact( 'args' ) );
			}
		}

		/**
		 * Return an array of links for the YITH Sidebar
		 *
		 * @return array
		 */
		public function get_panel_sidebar_links() {
			return array(
				array(
					'url'   => $this->yith_url,
					'title' => __( 'Your Inspiration Themes', 'yith-woocommerce-badges-management' ),
				),
				array(
					'url'   => $this->doc_url,
					'title' => __( 'Plugin Documentation', 'yith-woocommerce-badges-management' ),
				),
				array(
					'url'   => $this->plugin_url,
					'title' => __( 'Plugin Site', 'yith-woocommerce-badges-management' ),
				),
				array(
					'url'   => $this->demo_url,
					'title' => __( 'Live Demo', 'yith-woocommerce-badges-management' ),
				),
			);
		}

		/**
		 * Get the premium landing uri
		 *
		 * @return  string The premium landing link
		 * @since   1.0.0
		 */
		public function get_premium_landing_uri() {
			return $this->premium_landing;
		}

		/**
		 * Retrieve panel page
		 *
		 * @return string
		 */
		public function get_panel_page() {
			return $this->panel_page;
		}

		/**
		 * Check if is a panel page
		 */
		public function is_panel() {
			$screen_id = is_callable( 'get_current_screen' ) ? get_current_screen()->id ?? false : false;

			return is_admin() && ( ( $screen_id && str_replace( 'edit-', '', $screen_id ) === 'yith-wcbm-badge' ) || ( isset( $_GET['page'] ) && $this->panel_page === $_GET['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		/**
		 * Delete badge meta transient from product
		 *
		 * @param WC_Product $product The product.
		 */
		public function delete_badge_meta_transient_for_product( $product ) {
			$product = wc_get_product( $product );

			if ( $product ) {
				delete_transient( 'yith_wcbm_badges_from_product_' . $product->get_id() . '_meta' );
				if ( $product->is_type( 'variable' ) ) {
					delete_transient( 'yith_wcbm_badges_from_product_' . $product->get_id() . '_meta_with_variations' );
				}
			}
		}

		/** ------------------------------------------
		 * Deprecated Methods
		 */

		/**
		 * Set the post status to publish when a badge is untrashed
		 *
		 * @param string $new_status New Post Status.
		 * @param int    $post_id    Post ID.
		 *
		 * @depreacted since 2.0 | The Badges haven't the trashed status anymore
		 * @return string
		 */
		public function untrash_badge_post_status( $new_status, $post_id ) {
			yith_wcbm_deprecated_function( 'YITH_WCBM_Admin::untrash_badge_post_status', '2.0.0' );

			return get_post_type( $post_id ) === YITH_WCBM_Post_Types::$badge ? 'publish' : $new_status;
		}

		/**
		 * Show premium landing tab
		 *
		 * @since      1.0
		 * @depreacted since 2.0 | Now it's handles by the Plugin FW Premium Tab
		 */
		public function show_premium_tab() {
			yith_wcbm_deprecated_function( 'YITH_WCBM_Admin::show_premium_tab', '2.0.0' );
		}
	}
}

if ( ! function_exists( 'yith_wcbm_admin' ) ) {
	/**
	 * Unique access to instance of YITH_WCBM_Admin class
	 *
	 * @return YITH_WCBM_Admin
	 */
	function yith_wcbm_admin() {
		return YITH_WCBM_Admin::get_instance();
	}
}
