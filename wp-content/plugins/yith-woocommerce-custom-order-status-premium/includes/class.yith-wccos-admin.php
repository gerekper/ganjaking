<?php
/**
 * Admin class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Custom Order Status
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCCOS' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCCOS_Admin' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @since    1.0.0
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCCOS_Admin {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCCOS_Admin
		 * @since 1.0.0
		 */
		protected static $_instance;

		/** @var $_panel Panel Object */
		protected $_panel;

		/** @var string Premium version landing link */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-custom-order-status/';

		/**@var string Quick View panel page */
		protected $_panel_page = 'yith_wccos_panel';

		/** @var string Plugin Documentation URL */
		public $doc_url = 'https://docs.yithemes.com/yith-woocommerce-custom-order-status/';


		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WCCOS_Admin
		 * @since 1.0.0
		 */
		public static function get_instance() {
			$self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

			return ! is_null( $self::$_instance ) ? $self::$_instance : $self::$_instance = new $self;
		}

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		protected function __construct() {

			if ( is_admin() ) {
				add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

				//Add action links
				add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCCOS_DIR . '/' . basename( YITH_WCCOS_FILE ) ), array( $this, 'action_links' ) );
				add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 3 );

				add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 100 );

				add_action( 'init', array( $this, 'post_type_register' ) );

				add_filter( 'manage_yith-wccos-ostatus_posts_columns', array( $this, 'order_status_columns' ) );
				add_filter( 'manage_yith-wccos-ostatus_posts_custom_column', array( $this, 'custom_columns' ), 10, 2 );
				if ( version_compare( WC()->version, '3.3', '<' ) ) {
					add_filter( 'manage_shop_order_posts_custom_column', array( $this, 'custom_columns' ), 10, 2 );
				}

				// Premium Tabs
				add_action( 'yith_wccos_premium_tab', array( $this, 'show_premium_tab' ) );
				add_action( 'yith_wccos_how_to_tab', array( $this, 'show_how_to_tab' ) );

				add_filter( 'default_hidden_columns', array( $this, 'show_wc_actions_column_by_default' ), 99, 2 );
			}

			add_action( 'init', array( $this, 'add_capabilities' ) );

			add_filter( 'wc_order_statuses', array( $this, 'get_custom_statuses' ) );

			add_action( 'init', array( $this, 'register_my_new_order_statuses' ) );

			add_filter( 'woocommerce_admin_order_actions', array( $this, 'add_submit_to_order_admin_actions' ), 10, 3 );

			// Before delete a custom order status, change status of orders with this custom order status to "on-hold"
			add_action( 'wp_trash_post', array( $this, 'before_trash_status' ) );
		}

		/**
		 * show actions in orders by default
		 *
		 * @param array     $hidden
		 * @param WP_Screen $screen
		 *
		 * @return array
		 * @since
		 */
		public function show_wc_actions_column_by_default( $hidden, $screen ) {
			if ( isset( $screen->id ) && 'edit-shop_order' === $screen->id ) {
				$hidden = array_diff( $hidden, array( 'wc_actions' ) );
			}

			return $hidden;
		}

		/**
		 * Before delete a custom order status, change status of orders with this custom order status to "on-hold"
		 *
		 * @param int $post_id id of deleted post
		 *
		 * @return   void
		 * @since    1.0.2
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function before_trash_status( $post_id ) {
			global $post_type;
			if ( $post_type != 'yith-wccos-ostatus' ) {
				return;
			}

			$post   = get_post( $post_id );
			$status = get_post_meta( $post_id, 'slug', true );

			$wc_statuses = array(
				'pending',
				'processing',
				'on-hold',
				'completed',
				'cancelled',
				'refunded',
				'failed',
			);

			if ( in_array( $status, $wc_statuses ) ) {
				return;
			}

			$order_count = wc_orders_count( $status );

			if ( $order_count > 0 ) {
				$args      = array(
					'posts_per_page' => - 1,
					'post_type'      => 'shop_order',
					'tax_query'      => array(
						array(
							'taxonomy' => 'shop_order_status',
							'field'    => 'slug',
							'terms'    => array( $status ),
						),
					),
					'fields'         => 'ids',
				);
				$order_ids = get_posts( $args );

				foreach ( $order_ids as $order_id ) {
					$order = wc_get_order( $order_id );
					if ( $order ) {
						$order->update_status( 'on-hold', sprintf( __( 'Status changed because of the deletion of "%s" custom status', 'yith-woocommerce-custom-order-status' ), $post->post_title ) );
					}
				}
			}
		}

		/**
		 * Add Icon Column in WP_List_Table of order custom statuses
		 *
		 * @return   array
		 * @since    1.0
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function order_status_columns( $columns ) {

			$icon_label = __( 'Icon', 'yith-woocommerce-custom-order-status' );

			$new_columns = array(
				'cb'           => $columns['cb'],
				'order_status' => "<span class='yith-wccos-status-icon-head tips' data-tip='$icon_label'>$icon_label</span>",
			);
			unset( $columns['cb'] );

			return array_merge( $new_columns, $columns );
		}

		public function custom_columns( $column, $post_id ) {
			if ( $column == 'order_status' ) {
				$slug  = get_post_meta( $post_id, 'slug', true );
				$title = get_the_title( $post_id );
				echo "<mark class='$slug'>$title</mark>";
			}
		}

		/**
		 * Add Button Actions in Order list
		 *
		 * @param array    $actions
		 * @param WC_Order $the_order
		 *
		 * @return array
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 * @since    1.0
		 */
		function add_submit_to_order_admin_actions( $actions, $the_order ) {
			global $post;

			$status_posts = get_posts( array(
										   'posts_per_page' => - 1,
										   'post_type'      => 'yith-wccos-ostatus',
										   'post_status'    => 'publish',
									   ) );
			$status_names = array();

			foreach ( $status_posts as $sp ) {
				$status_names[] = get_post_meta( $sp->ID, 'slug', true );
			}

			if ( $the_order->has_status( $status_names ) ) {
				$actions['processing'] = array(
					'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=processing&order_id=' . $post->ID ), 'woocommerce-mark-order-status' ),
					'name'   => __( 'Processing', 'woocommerce' ),
					'action' => "processing",
				);
			}

			if ( $the_order->has_status( $status_names ) ) {
				$actions['complete'] = array(
					'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=completed&order_id=' . $post->ID ), 'woocommerce-mark-order-status' ),
					'name'   => __( 'Complete', 'woocommerce' ),
					'action' => "complete",
				);
			}

			foreach ( $status_posts as $sp ) {
				$meta = array(
					'label' => $sp->post_title,
					'color' => get_post_meta( $sp->ID, 'color', true ),
					'slug'  => get_post_meta( $sp->ID, 'slug', true ),
				);
				if ( $meta['slug'] == 'completed' ) {
					$actions['complete'] = array(
						'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=' . $meta['slug'] . '&order_id=' . $post->ID ), 'woocommerce-mark-order-status' ),
						'name'   => $meta['label'],
						'action' => 'complete',
					);
				} else {
					$actions[ $meta['slug'] ] = array(
						'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=' . $meta['slug'] . '&order_id=' . $post->ID ), 'woocommerce-mark-order-status' ),
						'name'   => $meta['label'],
						'action' => $meta['slug'],
					);
				}
			}

			return $actions;
		}

		/**
		 * Get custom statuses
		 *
		 * @return   array
		 * @since    1.0
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function get_custom_statuses( $statuses ) {
			$status_ids = get_posts( array(
										 'posts_per_page' => - 1,
										 'post_type'      => 'yith-wccos-ostatus',
										 'post_status'    => 'publish',
										 'fields'         => 'ids',
									 ) );
			foreach ( $status_ids as $id ) {
				$title       = apply_filters( 'yith_wccos_order_status_title', get_the_title( $id ), $id );
				$status_slug = 'wc-' . get_post_meta( $id, 'slug', true );

				$statuses[ $status_slug ] = $title;
			}

			return $statuses;
		}

		/**
		 * Register custom statuses
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		function register_my_new_order_statuses() {
			$status_posts = get_posts( array(
										   'posts_per_page' => - 1,
										   'post_type'      => 'yith-wccos-ostatus',
										   'post_status'    => 'publish',
									   ) );
			foreach ( $status_posts as $sp ) {
				$label = $sp->post_title;
				$slug  = 'wc-' . get_post_meta( $sp->ID, 'slug', true );

				register_post_status( $slug, array(
					'label'                     => $label,
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( $label . ' <span class="count">(%s)</span>', $label . ' <span class="count">(%s)</span>' ),
				) );
			}
		}

		/**
		 * Register Order Status custom post type with options metabox
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function post_type_register() {
			$labels = array(
				'name'               => __( 'Order Statuses', 'yith-woocommerce-custom-order-status' ),
				'singular_name'      => __( 'Order Status', 'yith-woocommerce-custom-order-status' ),
				'add_new'            => __( 'Add Order Status', 'yith-woocommerce-custom-order-status' ),
				'add_new_item'       => __( 'New Order Status', 'yith-woocommerce-custom-order-status' ),
				'edit_item'          => __( 'Edit Order Status', 'yith-woocommerce-custom-order-status' ),
				'view_item'          => __( 'View Order Status', 'yith-woocommerce-custom-order-status' ),
				'not_found'          => __( 'Order Status not found', 'yith-woocommerce-custom-order-status' ),
				'not_found_in_trash' => __( 'Order Status not found in trash', 'yith-woocommerce-custom-order-status' ),
			);

			$args = array(
				'labels'              => $labels,
				'public'              => false,
				'show_in_menu'        => false,
				'show_ui'             => true,
				'menu_position'       => 10,
				'exclude_from_search' => true,
				'capability_type'     => array( 'custom_order_status', 'custom_order_statuses' ),
				'map_meta_cap'        => true,
				'rewrite'             => true,
				'has_archive'         => true,
				'hierarchical'        => false,
				'show_in_nav_menus'   => false,
				'menu_icon'           => 'dashicons-pressthis',
				'supports'            => array( 'title' ),
			);

			register_post_type( 'yith-wccos-ostatus', $args );

			$args    = array(
				'label'    => __( 'Status Options', 'yith-woocommerce-custom-order-status' ),
				'class'    => yith_set_wrapper_class(),
				'pages'    => 'yith-wccos-ostatus',
				'context'  => 'normal',
				'priority' => 'high',
				'tabs'     => apply_filters( 'yith_wccos_tabs_metabox', array(
					'settings' => array( //tab
										 'label'  => __( 'Settings', 'yith-woocommerce-custom-order-status' ),
										 'fields' => array(
											 'slug'  => array(
												 'label'   => __( 'Slug', 'yith-woocommerce-custom-order-status' ),
												 'desc'    => __( 'Unique slug of your status', 'yith-woocommerce-custom-order-status' ),
												 'type'    => 'text',
												 'private' => false,
												 'std'     => '',
											 ),
											 'color' => array(
												 'label'   => __( 'Color', 'yith-woocommerce-custom-order-status' ),
												 'desc'    => __( 'Color of your status', 'yith-woocommerce-custom-order-status' ),
												 'type'    => 'colorpicker',
												 'private' => false,
												 'std'     => '#2470FF',
											 ),
										 ),
					),
				) ),
			);
			$metabox = YIT_Metabox( 'yith-wccos-metabox' );
			$metabox->init( $args );
		}

		/**
		 * Add custom order status capabilities to Admin and Shop Manager
		 *
		 * @access public
		 * @since  1.1.7
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function add_capabilities() {
			$singular = 'custom_order_status';
			$plural   = 'custom_order_statuses';

			// gets the admin and shop_manager roles
			$admin        = get_role( 'administrator' );
			$shop_manager = get_role( 'shop_manager' );

			$caps = array(
				'edit_' . $singular,
				'read_' . $singular,
				'delete_' . $singular,
				'edit_' . $plural,
				'edit_others_' . $plural,
				'publish_' . $plural,
				'read_private_' . $plural,
				'delete_' . $plural,
				'delete_private_' . $plural,
				'delete_published_' . $plural,
				'delete_others_' . $plural,
				'edit_private_' . $plural,
				'edit_published_' . $plural,
				'manage_' . $plural,
			);

			$shop_manager_enabled = 'yes' === get_option( 'yith-wccos-enable-shop-manager', 'yes' );

			foreach ( $caps as $cap ) {
				if ( $admin ) {
					$admin->add_cap( $cap );
				}

				if ( $shop_manager ) {
					if ( $shop_manager_enabled ) {
						$shop_manager->add_cap( $cap );
					} else {
						$shop_manager->remove_cap( $cap );
					}
				}
			}
		}

		/**
		 * Action Links
		 * add the action links to plugin admin page
		 *
		 * @param $links | links plugin array
		 *
		 * @return   mixed Array
		 * @return mixed
		 * @use      plugin_action_links_{$plugin_file_name}
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 * @since    1.0
		 */
		public function action_links( $links ) {
			return yith_add_action_links( $links, $this->_panel_page, defined( 'YITH_WCCOS_PREMIUM' ) );
		}

		/**
		 * plugin_row_meta
		 * add the action links to plugin admin page
		 *
		 * @param $row_meta_args
		 * @param $plugin_meta
		 * @param $plugin_file
		 *
		 * @return   array
		 * @since    1.0
		 * @use      plugin_row_meta
		 */
		public function plugin_row_meta( $row_meta_args, $plugin_meta, $plugin_file ) {
			$init = defined( 'YITH_WCCOS_FREE_INIT' ) ? YITH_WCCOS_FREE_INIT : YITH_WCCOS_INIT;

			if ( $init === $plugin_file ) {
				$row_meta_args['slug']       = YITH_WCCOS_SLUG;
				$row_meta_args['is_premium'] = defined( 'YITH_WCCOS_PREMIUM' );
			}

			return $row_meta_args;
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 * @use      /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs_free = array(
				'free-how-to' => __( 'How To', 'yith-woocommerce-custom-order-status' ),
				'premium'     => __( 'Premium Version', 'yith-woocommerce-custom-order-status' ),
			);

			$admin_tabs = apply_filters( 'yith_wccos_settings_admin_tabs', $admin_tabs_free );

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'class'            => yith_set_wrapper_class(),
				'page_title'       => 'WooCommerce Custom Order Status',
				'menu_title'       => 'Custom Order Status',
				'capability'       => 'manage_custom_order_statuses',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_WCCOS_DIR . '/plugin-options',
			);

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once( 'plugin-fw/lib/yit-plugin-panel-wc.php' );
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

			add_action( 'woocommerce_admin_field_yith_wccos_upload', array( $this->_panel, 'yit_upload' ), 10, 1 );
			add_action( 'woocommerce_update_option_yith_wccos_upload', array( $this->_panel, 'yit_upload_update' ), 10, 1 );
		}

		public function admin_enqueue_scripts() {
			wp_enqueue_style( 'yith-wccos-admin-styles', YITH_WCCOS_ASSETS_URL . '/css/admin.css', array(), YITH_WCCOS_VERSION );

			$screen     = get_current_screen();
			$metabox_js = defined( 'YITH_WCCOS_PREMIUM' ) ? 'metabox_options_premium.js' : 'metabox_options.js';

			if ( 'yith-wccos-ostatus' == $screen->id ) {
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_script( 'jquery-ui-tabs' );
				wp_enqueue_style( 'jquery-ui-style-css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/themes/smoothness/jquery-ui.css' );
				wp_enqueue_style( 'font-awesome' );

				wp_enqueue_script( 'yith_wccos_metabox_options', YITH_WCCOS_ASSETS_URL . '/js/' . $metabox_js, array( 'jquery', 'wp-color-picker' ), YITH_WCCOS_VERSION, true );

				wp_localize_script( 'yith_wccos_metabox_options', 'yith_wccos_params', apply_filters( 'yith_wccos_metabox_options_params',
																									  array(
																										  'slug_from'    => "ãàáäâẽèéëêìíïîõòóöôùúüûñç·/_,:; ",
																										  'slug_to'      => "aaaaaeeeeeiiiiooooouuuunc-------",
																										  'slug_allowed' => "[^a-z0-9 -]",
																									  ) ) );
			}

			wp_add_inline_style( 'yith-wccos-admin-styles', $this->get_status_inline_css() );

			if ( 'edit-yith-wccos-ostatus' === $screen->id ) {
				wp_enqueue_style( 'font-awesome' );
				wp_enqueue_script( 'yith-wccos-admin', YITH_WCCOS_ASSETS_URL . '/js/admin.js', array( 'jquery', 'jquery-tiptip' ), YITH_WCCOS_VERSION, true );
			}

			if ( 'edit-shop_order' == $screen->id ) {
				wp_enqueue_style( 'font-awesome' );
			}
		}

		/**
		 * Get Status Inline CSS
		 * Return the css for custom status
		 *
		 * @return   string
		 * @since    1.0
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function get_status_inline_css() {
			$css          = '';
			$status_posts = get_posts( array(
										   'posts_per_page' => - 1,
										   'post_type'      => 'yith-wccos-ostatus',
										   'post_status'    => 'publish',
										   'fields'         => 'ids',
									   ) );

			foreach ( $status_posts as $id ) {
				$name = get_post_meta( $id, 'slug', true );
				$meta = array(
					'color' => get_post_meta( $id, 'color', true ),
				);

				$css .= '.widefat .column-order_status mark.' . $name . '::after, .yith_status_icon mark.' . $name . '::after{
                            content:"\e039";
                            color:' . $meta['color'] . ';
                            font-family: WooCommerce;
                            font-weight: 400;
                            font-variant: normal;
                            text-transform: none;
                            line-height: 1;
                            margin: 0px;
                            text-indent: 0px;
                            position: absolute;
                            top: 0px;
                            left: 0px;
                            width: 100%;
                            height: 100%;
                            text-align: center;
                        }';

				$css .= '.order_actions .' . $name . '{
                            display: block;
                            text-indent: -9999px;
                            position: relative;
                            padding: 0px !important;
                            height: 2em !important;
                            width: 2em;
                        }';

				$css .= '.order_actions .' . $name . '::after {
                            content: "\e039";
                            color: ' . $meta['color'] . ';
                            text-indent: 0px;
                            position: absolute;
                            width: 100%;
                            height: 100%;
                            font-weight: 400;
                            text-align: center;
                            margin: 0px;
                            font-family: WooCommerce;
                            font-variant: normal;
                            text-transform: none;
                            top: 0px;
                            left: 0px;
                            line-height: 1.85;
                        }';
			}

			return $css;
		}

		/**
		 * Show free how-to
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function show_how_to_tab() {
			$landing = YITH_WCCOS_TEMPLATE_PATH . '/free-how-to.php';
			file_exists( $landing ) && require( $landing );
		}

		/**
		 * Show premium landing tab
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function show_premium_tab() {
			$landing = YITH_WCCOS_TEMPLATE_PATH . '/premium.php';
			file_exists( $landing ) && require( $landing );
		}

		/**
		 * Get the premium landing uri
		 *
		 * @return  string The premium landing link
		 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
		 * @since   1.0.0
		 */
		public function get_premium_landing_uri() {
			return $this->_premium_landing;
		}
	}
}

/**
 * Unique access to instance of YITH_WCCOS_Admin class
 *
 * @return YITH_WCCOS_Admin|YITH_WCCOS_Admin_Premium
 * @since 1.0.0
 */
function YITH_WCCOS_Admin() {
	return YITH_WCCOS_Admin::get_instance();
}