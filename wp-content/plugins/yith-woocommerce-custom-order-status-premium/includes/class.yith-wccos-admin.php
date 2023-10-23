<?php
/**
 * Admin class
 *
 * @package YITH\CustomOrderStatus
 */

defined( 'YITH_WCCOS' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCCOS_Admin' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @author   YITH <plugins@yithemes.com>
	 */
	class YITH_WCCOS_Admin {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCCOS_Admin
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * The panel.
		 *
		 * @var YIT_Plugin_Panel_WooCommerce
		 */
		protected $panel;

		/**
		 * The panel page.
		 *
		 * @var string
		 */
		protected $panel_page = 'yith_wccos_panel';


		/**
		 * Core Order Statuses
		 *
		 * @var array
		 */
		protected $core_order_statuses;

		/**
		 * Returns single instance of the class.
		 *
		 * @return YITH_WCCOS_Admin
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 */
		protected function __construct() {
			$this->core_order_statuses = wc_get_order_statuses();

			add_action( 'init', array( $this, 'add_capabilities' ) );
			add_filter( 'wc_order_statuses', array( $this, 'get_custom_statuses' ) );
			add_action( 'init', array( $this, 'register_my_new_order_statuses' ) );

			add_filter( 'woocommerce_admin_order_actions', array( $this, 'add_submit_to_order_admin_actions' ), 10, 2 );
			add_filter( 'woocommerce_admin_order_preview_actions', array( $this, 'add_admin_order_preview_actions' ), 10, 2 );

			// Prevent deleting custom order statuses if they are assigned to some orders.
			add_action( 'wp_trash_post', array( $this, 'before_trash_status' ) );
			add_action( 'before_delete_post', array( $this, 'before_trash_status' ) );

			add_action( 'woocommerce_order_status_changed', array( $this, 'woocommerce_order_status_changed' ), 10, 3 );
			add_filter( 'woocommerce_reports_order_statuses', array( $this, 'woocommerce_reports_order_statuses' ) );

			if ( is_admin() ) {
				add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

				add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCCOS_DIR . '/' . basename( YITH_WCCOS_FILE ) ), array( $this, 'action_links' ) );
				add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 3 );

				add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 100 );

				add_action( 'init', array( $this, 'post_type_register' ) );

				add_filter( 'manage_yith-wccos-ostatus_posts_columns', array( $this, 'order_status_columns' ) );
				add_filter( 'manage_yith-wccos-ostatus_posts_custom_column', array( $this, 'custom_columns' ), 10, 2 );
				if ( version_compare( WC()->version, '3.3', '<' ) ) {
					add_filter( 'manage_shop_order_posts_custom_column', array( $this, 'custom_columns' ), 10, 2 );
				}
				add_action( 'yith_wccos_how_to_tab', array( $this, 'show_how_to_tab' ) );

				add_filter( 'default_hidden_columns', array( $this, 'show_wc_actions_column_by_default' ), 0, 2 );

				/**
				 * Import Custom Order Statuses
				 *
				 * @since 1.1.4
				 */
				add_action( 'wp_loaded', array( $this, 'import_custom_statuses' ), 99 );
			}

			add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_id_to_woocommerce' ), 10, 1 );
			add_action( 'before_woocommerce_init', array( $this, 'declare_wc_features_support' ) );
		}

		/**
		 * Show actions in orders by default.
		 *
		 * @param array     $hidden Hidden columns.
		 * @param WP_Screen $screen The current screen.
		 *
		 * @return array
		 * @since
		 */
		public function show_wc_actions_column_by_default( $hidden, $screen ) {
			$order_screen_ids = array_filter(
				array(
					'edit-shop_order',
					function_exists( 'wc_get_page_screen_id' ) ? wc_get_page_screen_id( 'shop-order' ) : '',
				)
			);

			if ( isset( $screen->id ) && in_array( $screen->id, $order_screen_ids, true ) ) {
				$hidden = array_diff( $hidden, array( 'wc_actions' ) );
			}

			return $hidden;
		}

		/**
		 * Before delete a custom order status, change status of orders with this custom order status to "on-hold"
		 *
		 * @param int $post_id The post ID.
		 *
		 * @return   void
		 * @since    1.0.2
		 */
		public function before_trash_status( $post_id ) {
			if ( 'yith-wccos-ostatus' !== get_post_type( $post_id ) ) {
				return;
			}

			$status      = get_post_meta( $post_id, 'slug', true );
			$wc_statuses = array( 'pending', 'processing', 'on-hold', 'completed', 'cancelled', 'refunded', 'failed' );

			if ( in_array( $status, $wc_statuses, true ) ) {
				return;
			}

			$status      = 'wc-' . $status;
			$order_count = wc_orders_count( $status );

			if ( $order_count > 0 ) {
				$name       = get_the_title( $post_id );
				$orders_url = function_exists( 'yith_plugin_fw_is_wc_custom_orders_table_usage_enabled' ) && yith_plugin_fw_is_wc_custom_orders_table_usage_enabled() ? admin_url( 'admin.php?page=wc-orders&status=' . $status ) : admin_url( 'edit.php?post_type=shop_order&post_status=' . $status );

				$message = implode(
					'<br />',
					array(
						__( 'This status cannot be deleted or moved to trash because it is currently being used in one or more orders!', 'yith-woocommerce-custom-order-status' ),
						__( 'Before deleting it, you should change the status of the orders that have this specific status.', 'yith-woocommerce-custom-order-status' ),
					)
				);

				wp_die(
					wp_kses_post( $message ),
					esc_html__( 'Error', 'yith-woocommerce-custom-order-status' ),
					array(
						'back_link' => true,
						// translators: %s: status name.
						'link_text' => esc_html( sprintf( __( 'See orders with the "%s" status', 'yith-woocommerce-custom-order-status' ), $name ) ),
						'link_url'  => esc_url( $orders_url ),
					)
				);
			}
		}

		/**
		 * Add Icon Column in WP_List_Table of order custom statuses.
		 *
		 * @param array $columns The columns.
		 *
		 * @return   array
		 */
		public function order_status_columns( $columns ) {

			$icon_label = __( 'Icon', 'yith-woocommerce-custom-order-status' );

			$new_columns = array(
				'cb'           => $columns['cb'],
				'order_status' => "<span class='yith-wccos-status-icon-head tips' data-tip='$icon_label'>$icon_label</span>",
			);
			unset( $columns['cb'] );

			$columns = array_merge( $new_columns, $columns );

			$date = $columns['date'];
			unset( $columns['date'] );

			$options_with_icons = array(
				'can-cancel'          => __( 'User can cancel', 'yith-woocommerce-custom-order-status' ),
				'can-pay'             => __( 'User can pay', 'yith-woocommerce-custom-order-status' ),
				'is-paid'             => __( 'Order is paid', 'yith-woocommerce-custom-order-status' ),
				'downloads-permitted' => __( 'Allow Downloads', 'yith-woocommerce-custom-order-status' ),
				'display-in-reports'  => __( 'Display in Reports', 'yith-woocommerce-custom-order-status' ),
				'restore-stock'       => __( 'Restore Stock', 'yith-woocommerce-custom-order-status' ),
				'show-in-actions'     => __( 'Show always in Actions', 'yith-woocommerce-custom-order-status' ),
				'send-email-to'       => __( 'Send email to', 'yith-woocommerce-custom-order-status' ),
			);

			$new_columns = array(
				'yith-wccos-status_type' => __( 'Status Type', 'yith-woocommerce-custom-order-status' ),
				'yith-wccos-slug'        => __( 'Slug', 'yith-woocommerce-custom-order-status' ),
				'yith-wccos-nextactions' => __( 'Next Actions', 'yith-woocommerce-custom-order-status' ),
			);

			foreach ( $options_with_icons as $key => $label ) {
				$new_columns[ 'yith-wccos-' . $key ] = "<span class='yith-wccos-{$key}-head tips' data-tip='$label'>$label</span>";
			}

			$columns = array_merge( $columns, $new_columns );

			$columns['date'] = $date;

			return $columns;
		}

		/**
		 * Manage custom columns.
		 *
		 * @param string $column  The column name.
		 * @param int    $post_id The post ID.
		 */
		public function custom_columns( $column, $post_id ) {
			if ( 'order_status' === $column ) {
				$slug  = get_post_meta( $post_id, 'slug', true );
				$title = get_the_title( $post_id );
				echo '<mark class="' . esc_attr( $slug ) . '">' . esc_html( $title ) . '</mark>';
			}

			if ( strpos( $column, 'yith-wccos-' ) === 0 ) {
				$column = str_replace( 'yith-wccos-', '', $column );
				switch ( $column ) {
					case 'status_type':
						$status_types = array(
							'custom'     => _x( 'Custom Status', 'Select status type', 'yith-woocommerce-custom-order-status' ),
							'pending'    => _x( 'Pending Payment', 'Select status type', 'yith-woocommerce-custom-order-status' ),
							'processing' => _x( 'Processing', 'Select status type', 'yith-woocommerce-custom-order-status' ),
							'on-hold'    => _x( 'On Hold', 'Select status type', 'yith-woocommerce-custom-order-status' ),
							'completed'  => _x( 'Completed', 'Select status type', 'yith-woocommerce-custom-order-status' ),
							'cancelled'  => _x( 'Cancelled', 'Select status type', 'yith-woocommerce-custom-order-status' ),
							'refunded'   => _x( 'Refunded', 'Select status type', 'yith-woocommerce-custom-order-status' ),
							'failed'     => _x( 'Failed', 'Select status type', 'yith-woocommerce-custom-order-status' ),
						);
						$value        = get_post_meta( $post_id, $column, true );
						if ( ! ! $value ) {
							echo esc_html( array_key_exists( $value, $status_types ) ? $status_types[ $value ] : $value );
						}
						break;
					case 'slug':
						$value = get_post_meta( $post_id, $column, true );
						if ( ! ! $value ) {
							echo esc_html( $value );
						}
						break;
					case 'nextactions':
						$value = get_post_meta( $post_id, $column, true );
						if ( ! ! $value && is_array( $value ) ) {
							$statuses     = wc_get_order_statuses();
							$next_actions = array();
							foreach ( $value as $status_slug ) {
								$next_actions[] = array_key_exists( $status_slug, $statuses ) ? $statuses[ $status_slug ] : $status_slug;
							}
							echo esc_html( implode( ', ', $next_actions ) );
						}
						break;
					case 'can-cancel':
					case 'can-pay':
					case 'is-paid':
					case 'downloads-permitted':
					case 'display-in-reports':
					case 'restore-stock':
					case 'show-in-actions':
						$value = get_post_meta( $post_id, $column, true );
						$icon  = yith_plugin_fw_is_true( $value ) ? 'yes' : 'no';
						echo '<span class="yith-wccos-icon-check dashicons dashicons-' . esc_attr( $icon ) . '"></span>';
						break;
					case 'send-email-to':
						$recipients = yith_wccos_get_recipients( $post_id );
						$icon       = 'no';
						$label      = __( 'None', 'yith-woocommerce-custom-order-status' );
						if ( $recipients ) {
							$recipient_labels = yith_wccos_get_allowed_recipients();
							$icon             = 'email-alt';
							$labels           = array();
							foreach ( $recipients as $recipient ) {
								if ( isset( $recipient_labels[ $recipient ] ) ) {
									$labels[] = $recipient_labels[ $recipient ];
								}
							}

							$label = implode( ', ', $labels );
						}

						echo '<span class="yith-wccos-icon-mail-info dashicons dashicons-' . esc_attr( $icon ) . ' tips" data-tip="' . esc_attr( $label ) . '"></span>';
						break;
				}
			}
		}


		/**
		 * Add order actions in preview
		 *
		 * @param array    $actions The actions.
		 * @param WC_Order $order   The order.
		 *
		 * @return array
		 * @since 1.2.2
		 */
		public function add_admin_order_preview_actions( $actions, $order ) {
			$status_actions = isset( $actions['status'], $actions['status']['actions'] ) ? $actions['status']['actions'] : array();
			$status_actions = $this->add_submit_to_order_admin_actions( $status_actions, $order );

			if ( $status_actions ) {
				$actions['status'] = array(
					'group'   => __( 'Change status: ', 'woocommerce' ),
					'actions' => $status_actions,
				);
			}

			return apply_filters( 'yith_wccos_order_preview_actions', $actions );
		}

		/**
		 * Get custom statuses.
		 *
		 * @param array $statuses The WooCommerce Statuses.
		 *
		 * @return   array
		 */
		public function get_custom_statuses( $statuses ) {
			$status_ids = get_posts(
				array(
					'posts_per_page' => - 1,
					'post_type'      => 'yith-wccos-ostatus',
					'post_status'    => 'publish',
					'fields'         => 'ids',
				)
			);
			foreach ( $status_ids as $id ) {
				$title       = apply_filters( 'yith_wccos_order_status_title', get_the_title( $id ), $id );
				$status_slug = 'wc-' . get_post_meta( $id, 'slug', true );

				$statuses[ $status_slug ] = $title;
			}

			return $statuses;
		}

		/**
		 * Register custom statuses
		 */
		public function register_my_new_order_statuses() {
			$status_posts = get_posts(
				array(
					'posts_per_page' => - 1,
					'post_type'      => 'yith-wccos-ostatus',
					'post_status'    => 'publish',
				)
			);
			foreach ( $status_posts as $sp ) {
				$label = $sp->post_title;
				$slug  = 'wc-' . get_post_meta( $sp->ID, 'slug', true );

				register_post_status(
					$slug,
					array(
						'label'                     => $label,
						'public'                    => true,
						'exclude_from_search'       => false,
						'show_in_admin_all_list'    => true,
						'show_in_admin_status_list' => true,
						'label_count'               => _n_noop( $label . ' <span class="count">(%s)</span>', $label . ' <span class="count">(%s)</span>' ), // phpcs:ignore WordPress.WP.I18n
					)
				);
			}
		}

		/**
		 * Register Order Status custom post type with options metabox
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
				'tabs'     => apply_filters( 'yith_wccos_tabs_metabox', require YITH_WCCOS_DIR . '/plugin-options/metabox/custom-order-status-options.php' ),
			);
			$metabox = YIT_Metabox( 'yith-wccos-metabox' );
			$metabox->init( $args );
		}

		/**
		 * Add custom order status capabilities to Admin and Shop Manager
		 *
		 * @access public
		 * @since  1.1.7
		 */
		public function add_capabilities() {
			$singular = 'custom_order_status';
			$plural   = 'custom_order_statuses';

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
		 * @param array $links The plugin links.
		 *
		 * @return array
		 * @use      plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {
			return yith_add_action_links( $links, $this->panel_page, defined( 'YITH_WCCOS_PREMIUM' ), YITH_WCCOS_SLUG );
		}

		/**
		 * Adds row meta.
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
			$init = defined( 'YITH_WCCOS_FREE_INIT' ) ? YITH_WCCOS_FREE_INIT : YITH_WCCOS_INIT;

			if ( $init === $plugin_file ) {
				$row_meta_args['slug']       = YITH_WCCOS_SLUG;
				$row_meta_args['is_premium'] = defined( 'YITH_WCCOS_PREMIUM' );
			}

			return $row_meta_args;
		}

		/**
		 * Add a panel under YITH Plugins tab
		 */
		public function register_panel() {

			if ( ! empty( $this->panel ) ) {
				return;
			}

			$tabs = apply_filters(
				'yith_wccos_settings_admin_tabs',
				array(
					'order-statuses' => __( 'Order Statuses', 'yith-woocommerce-custom-order-status' ),
					'settings'       => __( 'Settings', 'yith-woocommerce-custom-order-status' ),
				)
			);

			if ( ! current_user_can( 'manage_options' ) ) {
				unset( $tabs['settings'] );
			}

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'class'            => yith_set_wrapper_class(),
				'page_title'       => 'YITH WooCommerce Custom Order Status',
				'menu_title'       => 'Custom Order Status',
				'capability'       => 'manage_custom_order_statuses',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->panel_page,
				'admin-tabs'       => $tabs,
				'options-path'     => YITH_WCCOS_DIR . '/plugin-options',
				'plugin_slug'      => YITH_WCCOS_SLUG,
				'is_premium'       => true,
			);

			if ( class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );
			}
		}

		/**
		 * Enqueue scripts.
		 */
		public function admin_enqueue_scripts() {
			$screen           = get_current_screen();
			$suffix           = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$metabox_js       = defined( 'YITH_WCCOS_PREMIUM' ) ? "metabox_options_premium{$suffix}.js" : "metabox_options{$suffix}.js";
			$order_screen_id  = function_exists( 'wc_get_page_screen_id' ) ? wc_get_page_screen_id( 'shop-order' ) : '';
			$admin_screen_ids = apply_filters(
				'yith_wccos_admin_screen_ids',
				array(
					'yith-wccos-ostatus',
					'edit-yith-wccos-ostatus',
					'woocommerce_page_wc-admin',
					'edit-shop_order',
					$order_screen_id,
				)
			);

			wp_register_style( 'yith-wccos-admin-styles', YITH_WCCOS_ASSETS_URL . '/css/admin.css', array(), YITH_WCCOS_VERSION );

			wp_register_script( 'yith_wccos_metabox_options', YITH_WCCOS_ASSETS_URL . '/js/' . $metabox_js, array( 'jquery', 'wp-color-picker' ), YITH_WCCOS_VERSION, true );
			wp_register_script( 'yith-wccos-admin', YITH_WCCOS_ASSETS_URL . "/js/admin{$suffix}.js", array( 'jquery', 'jquery-tiptip' ), YITH_WCCOS_VERSION, true );

			wp_localize_script(
				'yith_wccos_metabox_options',
				'yith_wccos_params',
				apply_filters(
					'yith_wccos_metabox_options_params',
					array(
						'slug_from'    => 'ãàáäâẽèéëêìíïîõòóöôùúüûñç·/_,:; ',
						'slug_to'      => 'aaaaaeeeeeiiiiooooouuuunc-------',
						'slug_allowed' => '[^a-z0-9 -]',
						'wc_statuses'  => wc_get_order_statuses(),
					)
				)
			);

			if ( in_array( $screen->id, $admin_screen_ids, true ) ) {
				wp_enqueue_style( 'font-awesome' );
				wp_enqueue_style( 'yith-wccos-admin-styles' );

				wp_add_inline_style( 'yith-wccos-admin-styles', $this->get_status_inline_css() );
			}

			if ( 'yith-wccos-ostatus' === $screen->id ) {
				wp_enqueue_script( 'yith_wccos_metabox_options' );
			}

			if ( 'edit-yith-wccos-ostatus' === $screen->id ) {
				wp_enqueue_script( 'yith-wccos-admin' );
			}

			if ( in_array( $screen->id, array( 'edit-shop_order', $order_screen_id ), true ) ) {
				wp_enqueue_script( 'yith_wccos_order_bulk_actions', YITH_WCCOS_ASSETS_URL . "/js/order_bulk_actions{$suffix}.js", array( 'jquery' ), YITH_WCCOS_VERSION, true );
				$status_ids = get_posts(
					array(
						'posts_per_page' => - 1,
						'post_type'      => 'yith-wccos-ostatus',
						'post_status'    => 'publish',
						'fields'         => 'ids',
					)
				);

				$my_custom_status = array();

				foreach ( $status_ids as $status_id ) {
					$slug                      = get_post_meta( $status_id, 'slug', true );
					$label                     = get_the_title( $status_id );
					$my_custom_status[ $slug ] = $label;
				}
				$mark_text = esc_html__( 'Mark', 'yith-woocommerce-custom-order-status' );

				wp_localize_script(
					'yith_wccos_order_bulk_actions',
					'yith_wccos_order_bulk_actions',
					array(
						'my_custom_status' => $my_custom_status,
						'mark_text'        => $mark_text,
					)
				);
			}
		}

		/**
		 * Get Status Inline CSS
		 * Return the css for custom status
		 *
		 * @return   string
		 */
		public function get_status_inline_css() {
			$css        = '';
			$status_ids = get_posts(
				array(
					'posts_per_page' => - 1,
					'post_type'      => 'yith-wccos-ostatus',
					'post_status'    => 'publish',
					'fields'         => 'ids',
				)
			);

			foreach ( $status_ids as $status_id ) {
				$name = get_post_meta( $status_id, 'slug', true );
				$meta = array(
					'label'        => get_the_title( $status_id ),
					'color'        => get_post_meta( $status_id, 'color', true ),
					'icon'         => get_post_meta( $status_id, 'icon', true ),
					'graphicstyle' => get_post_meta( $status_id, 'graphicstyle', true ),
				);

				if ( ! is_array( $meta['icon'] ) ) {
					$meta['icon'] = array();
				}

				$my_icon                = $meta['icon']['icon'] ?? 'FontAwesome:genderless';
				$meta['icon']['select'] = $meta['icon']['select'] ?? 'none';

				$icon_data = explode( ':', $my_icon, 2 );
				if ( count( $icon_data ) === 2 ) {
					$font_name = $icon_data[0];
					$icon_name = $icon_data[1];
				} else {
					$font_name = 'FontAwesome';
					$icon_name = 'genderless';
				}

				$icons     = YIT_Icons()->get_icons();
				$icon_key  = array_key_exists( $font_name, $icons ) ? array_search( $icon_name, $icons[ $font_name ], true ) : '';
				$icon_data = array(
					'icon' => $icon_key,
					'font' => $font_name,
				);

				$no_icon = 'none' === $meta['icon']['select'];

				// WooCommerce > Analytics > Orders - Status colors.
				$css .= ".woocommerce-order-status__indicator.is-{$name} {
					background: {$meta['color']};
					border-color: rgba(255,255,255,0.7);
				}
				";

				if ( 'text' === $meta['graphicstyle'] ) {
					$icon_data['icon'] = $meta['label'];
					$icon_data['font'] = 'inherit';

					if ( version_compare( WC()->version, '3.3', '>=' ) ) {
						$text_color = yith_wccos_is_light_color( $meta['color'] ) ? 'rgba(0,0,0,0.7)' : 'rgba(255,255,255,0.7)';
					} else {
						$text_color = '#ffffff';
					}
					$css .= '.post-type-yith-wccos-ostatus .widefat .column-order_status mark.' . $name . '::after{
                                content:"' . $icon_data['icon'] . '" !important;
                                color: ' . $text_color . ' !important;
                                background:' . $meta['color'] . ' !important;
                                font-family: ' . $icon_data['font'] . ' !important;
                                font-variant: normal !important;
                                text-transform: none !important;
                                line-height: 1 !important;
                                margin: 0px !important;
                                text-indent: 0px !important;
                                position: absolute !important;
                                top: 0px !important;
                                left: calc(50% - 35px) !important;
                                width: 70px !important;
                                text-align: center !important;
                                font-size:9px !important;
                                padding: 5px 3px !important;
                                box-sizing: border-box !important;
                                border-radius: 3px !important;
                                font-weight: 600;
                            }';

					if ( version_compare( WC()->version, '3.3', '>=' ) ) {
						$bg_color   = $meta['color'];
						$text_color = yith_wccos_is_light_color( $bg_color ) ? 'rgba(0,0,0,0.7)' : 'rgba(255,255,255,0.7)';

						$css .= 'mark.order-status.status-' . $name . '{
                                    background:' . $bg_color . ' !important;
                                    color: ' . $text_color . ' !important;
                        }';

						$css .= '.post-type-shop_order .wp-list-table .column-wc_actions a.wc-action-button-' . $name . '{
                                    color: ' . $bg_color . ' !important;
                                    text-indent:0;
                                    width: auto !important;
                                    padding: 0 8px !important;
                        }';

						// Multi Vendor Suborder text.
						$css .= '.post-type-shop_order .wp-list-table .column-suborder mark.' . $name . '{
                                    background:' . $bg_color . ' !important;
                                    color: ' . $text_color . ' !important;
                                    text-indent:0;
                                    width: auto !important;
                                    padding: 3px 6px !important;
                                    height: auto !important;
                                    line-height: 1 !important;
                                    font-size: 11px !important;
                                    border-radius: 3px !important;
                        }';
					}

					if ( 'completed' === $name ) {
						$name = 'complete';
					}

					$css .= ".order_actions .$name, .wc_actions .$name" . '{
                                display: block;
                                padding: 0px 7px !important;
                                color:' . $meta['color'] . ' !important;
                            }';

					$css .= ".order_actions .$name::after, .wc_actions .$name::after" . '{
                                color:' . $meta['color'] . ' !important;
                            }';
				} else {
					$wc_status = array(
						'pending',
						'processing',
						'on-hold',
						'completed',
						'cancelled',
						'refunded',
						'failed',
					);

					if ( version_compare( WC()->version, '3.3', '>=' ) ) {
						$bg_color   = $meta['color'];
						$text_color = yith_wccos_is_light_color( $bg_color ) ? 'rgba(0,0,0,0.7)' : 'rgba(255,255,255,0.7)';

						$css .= 'mark.order-status.status-' . $name . '{
                                background:' . $bg_color . ' !important;
                                color: ' . $text_color . ' !important;
                        }';
					}

					if ( $no_icon && in_array( $name, $wc_status, true ) ) {
						$css .= '.widefat .column-order_status mark.' . $name . '::after, .yith_status_icon mark.' . $name . '::after, mark.' . $name . '::after{
		                                color:' . $meta['color'] . ' !important;
		                            }';
						if ( 'completed' === $name ) {
							$name = 'complete';
						}

						$css .= ".order_actions .$name::after, .wc_actions .$name::after" . '{
		                                color: ' . $meta['color'] . ';
		                            }';
					} else {
						// 'column-suborder' for Multi Vendor suborder icons.
						$css .= '.post-type-yith-wccos-ostatus .widefat .column-order_status mark.' . $name . '::after,
                                 .post-type-shop_order .wp-list-table .column-suborder mark.' . $name . '::after{
		                               content:"' . $icon_data['icon'] . '" !important;
		                               color:' . $meta['color'] . ' !important;
		                               font-family: ' . $icon_data['font'] . ' !important;
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

						if ( 'completed' === $name ) {
							$name = 'complete';
						}

						$css .= ".order_actions .$name, .wc_actions .$name" . '{
		                               display: block;
		                               text-indent: -9999px;
		                               position: relative;
		                               padding: 0px !important;
		                               height: 2em !important;
		                               width: 2em;
		                           }';

						$css .= ".order_actions .$name::after, .wc_actions .$name::after" . '{
		                              	content:"' . $icon_data['icon'] . '" !important;
		                               color: ' . $meta['color'] . ';
		                               font-family: ' . $icon_data['font'] . ' !important;
		                               text-indent: 0px;
		                               position: absolute;
		                               width: 100%;
		                               height: 100%;
		                               font-weight: 400;
		                               text-align: center;
		                               margin: 0px;
		                               font-variant: normal;
		                               text-transform: none;
		                               top: 0px;
		                               left: 0px;
		                               line-height: 1.85;
		                           }';
					}
				}
			}

			return $css;
		}

		/**
		 * Add Custom Order Status screen id to woocommerce
		 * to include the wc-enhanced-select script
		 *
		 * @param array $screen_ids The screen IDs.
		 *
		 * @return array
		 */
		public function add_screen_id_to_woocommerce( $screen_ids ) {
			$screen_ids[] = 'yith-wccos-ostatus';
			$screen_ids[] = 'edit-yith-wccos-ostatus';

			return $screen_ids;
		}

		/**
		 * Add orders with custom statuses in Reports
		 *
		 * @param array $statuses The statuses.
		 *
		 * @return array
		 */
		public function woocommerce_reports_order_statuses( $statuses ) {
			// Fix for woocommerce refund in reports.
			if ( ! is_array( $statuses ) || ( 1 === count( $statuses ) && 'refunded' === current( $statuses ) ) ) {
				return $statuses;
			}

			$status_ids = get_posts(
				array(
					'posts_per_page' => - 1,
					'post_type'      => 'yith-wccos-ostatus',
					'post_status'    => 'publish',
					'fields'         => 'ids',
				)
			);

			$new_statuses = array();

			$display_default_statuses = array();

			foreach ( (array) $statuses as $status ) {
				$display_default_statuses[ $status ] = 1;
			}

			foreach ( $status_ids as $status_id ) {
				$display = yith_plugin_fw_is_true( get_post_meta( $status_id, 'display-in-reports', true ) );
				$slug    = get_post_meta( $status_id, 'slug', true );
				if ( $display ) {
					if ( ! in_array( $slug, (array) $statuses, true ) ) {
						$new_statuses[] = $slug;
					}
				} else {
					if ( in_array( $slug, (array) $statuses, true ) ) {
						$display_default_statuses[ $slug ] = 0;
					}
				}
			}

			foreach ( $display_default_statuses as $key => $value ) {
				if ( $value ) {
					$new_statuses[] = $key;
				}
			}

			return $new_statuses;
		}

		/**
		 * Handler for status changed; send emails for custom order statuses
		 *
		 * @param int    $order_id   The order ID.
		 * @param string $old_status Old status.
		 * @param string $new_status New status.
		 */
		public function woocommerce_order_status_changed( $order_id, $old_status, $new_status ) {
			$order = new WC_Order( $order_id );

			$custom_status = get_posts(
				array(
					'posts_per_page' => 1,
					'post_type'      => 'yith-wccos-ostatus',
					'post_status'    => 'publish',
					'meta_key'       => 'slug', // phpcs:ignore WordPress.DB.SlowDBQuery
					'meta_value'     => $new_status, // phpcs:ignore WordPress.DB.SlowDBQuery
					'fields'         => 'ids',
				)
			);

			if ( ! ! $custom_status ) {
				$status_id           = current( $custom_status );
				$recipients          = yith_wccos_get_recipients( $status_id );
				$downloads_permitted = yith_plugin_fw_is_true( get_post_meta( $status_id, 'downloads-permitted', true ) );
				$custom_recipient    = get_post_meta( $status_id, 'custom_recipient', true );
				$restore_stock       = yith_plugin_fw_is_true( get_post_meta( $status_id, 'restore-stock', true ) );
				if ( $downloads_permitted ) {
					wc_downloadable_product_permissions( $order_id );
				}
				if ( $restore_stock ) {
					$this->restore_order_stock( $order );
				}

				$mailer           = WC()->mailer();
				$email_recipients = array();

				foreach ( $recipients as $recipient ) {
					switch ( $recipient ) {
						case 'admin':
							$email_recipients[ get_option( 'admin_email' ) ] = true;
							break;
						case 'customer':
							$email_recipients[ $order->get_billing_email() ] = false;
							break;

						case 'custom-email':
							if ( $custom_recipient ) {
								$email_recipients[ $custom_recipient ] = apply_filters( 'yith_wcos_sent_to_admin_for_custom_recipient', false, $custom_recipient, $custom_status );
							}
							break;
						default:
							// Allow third-party plugins to add their own recipients.
							$extra_recipients = apply_filters( 'yith_wccos_custom_email_recipients', null, $recipients, $status_id, $order_id, $old_status, $new_status );
							if ( ! is_null( $extra_recipients ) && is_array( $extra_recipients ) ) {
								$email_recipients = array_merge( $extra_recipients, $email_recipients );
							}
					}
				}

				$email_recipients = apply_filters( 'yith_wccos_email_recipients', $email_recipients, $status_id, $order_id, $old_status, $new_status );

				if ( ! ! $email_recipients ) {

					$notification_args = array(
						'heading'              => get_post_meta( $status_id, 'mail_heading', true ),
						'subject'              => get_post_meta( $status_id, 'mail_subject', true ),
						'from_name'            => get_post_meta( $status_id, 'mail_name_from', true ),
						'from_email'           => get_post_meta( $status_id, 'mail_from', true ),
						'display_order_info'   => yith_plugin_fw_is_true( get_post_meta( $status_id, 'mail_order_info', true ) ),
						'custom_email_address' => $custom_recipient,
						'order'                => $order,
						'custom_message'       => get_post_meta( $status_id, 'mail_custom_message', true ),
					);

					foreach ( $email_recipients as $recipient => $sent_to_admin ) {
						$notification_args['recipient']     = $recipient;
						$notification_args['sent_to_admin'] = $sent_to_admin;
						do_action( 'yith_wccos_custom_order_status_notification', $notification_args );
					}
				}
			}
		}

		/**
		 * Restore stock levels for all line items in the order.
		 *
		 * @param WC_Order $order The order.
		 *
		 * @since 1.0.21
		 */
		public function restore_order_stock( $order ) {
			if ( 'yes' === get_option( 'woocommerce_manage_stock' ) && apply_filters( 'woocommerce_can_reduce_order_stock', true, $order ) && ! ! $order->get_items() ) {
				$order_id            = $order->get_id();
				$order_stock_reduced = get_post_meta( $order_id, '_order_stock_reduced', true );

				if ( in_array( $order_stock_reduced, array( '1', 'yes' ), true ) ) {
					foreach ( $order->get_items() as $item ) {
						if ( $item instanceof WC_Order_Item_Product ) {
							$product = $item->get_product();

							if ( $product && $product->exists() && $product->managing_stock() ) {
								$qty             = apply_filters( 'woocommerce_order_item_quantity', $item['qty'], $this, $item );
								$new_stock       = wc_update_product_stock( $product, $qty, 'increase' );
								$item_identifier = $product->get_sku() ? $product->get_sku() : $item['product_id'];

								if ( isset( $item['variation_id'] ) && $item['variation_id'] ) {
									// translators: 1. the item ID; 2. the variation ID; 3. the initial quantity; 4. the final quantity.
									$order->add_order_note( sprintf( __( 'Item %1$s variation #%2$s: stock increased from %3$s to %4$s.', 'yith-woocommerce-custom-order-status' ), $item_identifier, $item['variation_id'], $new_stock - $qty, $new_stock ) );
								} else {
									// translators: 1. the item ID; 2. the initial quantity; 3. the final quantity.
									$order->add_order_note( sprintf( __( 'Item %1$s: stock increased from %2$s to %3$s.', 'yith-woocommerce-custom-order-status' ), $item_identifier, $new_stock - $qty, $new_stock ) );
								}
							}
						}
					}
					delete_post_meta( $order_id, '_order_stock_reduced' );

					do_action( 'yith_wccos_restore_order_stock', $order );
				}
			}
		}

		/**
		 * Add Button Actions in Order list
		 *
		 * @param array    $actions   The actions.
		 * @param WC_Order $the_order The order.
		 *
		 * @return array
		 */
		public function add_submit_to_order_admin_actions( $actions, $the_order ) {
			$order_id     = $the_order->get_id();
			$status_posts = get_posts(
				array(
					'posts_per_page' => - 1,
					'post_type'      => 'yith-wccos-ostatus',
					'post_status'    => 'publish',
					'fields'         => 'ids',
				)
			);

			$status_slugs    = array();
			$statuses_titles = array();

			foreach ( $status_posts as $sp_id ) {
				$slug                   = get_post_meta( $sp_id, 'slug', true );
				$status_slugs[]         = $slug;
				$status_titles[ $slug ] = get_the_title( $sp_id );
			}

			// Add all status to on-hold status if 'on-hold' is not customized.
			if ( apply_filters( 'yith_wccos_add_all_custom_order_status_actions', ! in_array( 'on-hold', $status_slugs, true ) && $the_order->has_status( 'on-hold' ), $the_order ) ) {
				foreach ( $status_posts as $sp_id ) {
					$current_status = array(
						'label' => get_the_title( $sp_id ),
						'slug'  => get_post_meta( $sp_id, 'slug', true ),
					);
					$action         = $current_status['slug'];
					if ( 'completed' === $action ) {
						$actions['complete'] = array(
							'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=completed&order_id=' . $order_id ), 'woocommerce-mark-order-status' ),
							'name'   => __( 'Complete', 'woocommerce' ),
							'action' => 'complete',
						);
					} else {
						$actions[ $action ] = array(
							'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=' . $action . '&order_id=' . $order_id ), 'woocommerce-mark-order-status' ),
							'name'   => $current_status['label'],
							'action' => $action,
						);
					}
				}
			} else {
				// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				$order_status    = $the_order->get_status();
				$custom_statuses = get_posts(
					array(
						'posts_per_page' => 1,
						'post_type'      => 'yith-wccos-ostatus',
						'post_status'    => 'publish',
						'fields'         => 'ids',
						'meta_query'     => array(
							array(
								'key'   => 'slug',
								'value' => $order_status,
							),
						),
					)
				);

				$status_to_show_always = get_posts(
					array(
						'posts_per_page' => - 1,
						'post_type'      => 'yith-wccos-ostatus',
						'post_status'    => 'publish',
						'fields'         => 'ids',
						'meta_query'     => array(
							'relation' => 'OR',
							array(
								'key'   => 'show-in-actions',
								'value' => '1',
							),
							array(
								'key'   => 'show-in-actions',
								'value' => 'yes',
							),
						),
					)
				);

				// phpcs:enable

				$next_actions = array();
				if ( $custom_statuses ) {
					$custom_status_id = current( $custom_statuses );
					$next_actions     = get_post_meta( $custom_status_id, 'nextactions', true );
					$next_actions     = ! ! $next_actions && is_array( $next_actions ) ? $next_actions : array();

					unset( $actions['complete'] );
					unset( $actions['processing'] );
				}

				if ( ! ! $status_to_show_always ) {
					foreach ( $status_to_show_always as $status_id ) {
						$next_actions[] = 'wc-' . get_post_meta( $status_id, 'slug', true );
					}
				}
				$next_actions = array_unique( $next_actions );

				foreach ( $next_actions as $action ) {
					if ( ! wc_is_order_status( $action ) ) {
						continue;
					}
					$action = str_replace( 'wc-', '', $action );
					if ( 'completed' === $action ) {
						$actions['complete'] = array(
							'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=completed&order_id=' . $order_id ), 'woocommerce-mark-order-status' ),
							'name'   => __( 'Complete', 'woocommerce' ),
							'action' => 'complete',
						);
					} else {
						$actions[ $action ] = array(
							'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=' . $action . '&order_id=' . $order_id ), 'woocommerce-mark-order-status' ),
							'name'   => $status_titles[ $action ] ?? $action,
							'action' => $action,
						);
					}
				}
			}

			return $actions;
		}


		/**
		 * Import custom statuses.
		 *
		 * @since 1.1.4
		 */
		public function import_custom_statuses() {
			if (
				isset( $_REQUEST['yith-wcos-import-custom-statuses'], $_REQUEST['yith-wcos-import_nonce'] ) &&
				wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['yith-wcos-import_nonce'] ) ), 'import-custom-statuses' )
			) {
				$order_statuses        = wc_get_order_statuses();
				$yith_order_status_ids = get_posts(
					array(
						'posts_per_page' => - 1,
						'post_type'      => 'yith-wccos-ostatus',
						'post_status'    => 'publish',
						'fields'         => 'ids',
					)
				);
				$yith_order_statuses   = array();
				foreach ( $yith_order_status_ids as $id ) {
					$slug                                 = get_post_meta( $id, 'slug', true );
					$title                                = get_the_title( $id );
					$yith_order_statuses[ 'wc-' . $slug ] = $title;
				}

				$order_statuses_to_import = array_diff( array_keys( $order_statuses ), array_keys( $this->core_order_statuses ), array_keys( $yith_order_statuses ) );

				if ( ! ! $order_statuses_to_import ) {
					foreach ( $order_statuses_to_import as $slug ) {
						$title   = $order_statuses[ $slug ];
						$slug    = substr( $slug, 3 );
						$post_id = wp_insert_post(
							array(
								'post_name'   => $slug,
								'post_title'  => $title,
								'post_type'   => 'yith-wccos-ostatus',
								'post_status' => 'publish',
							)
						);

						if ( ! ! $post_id ) {
							update_post_meta( $post_id, 'slug', $slug );
							update_post_meta( $post_id, 'graphicstyle', 'text' );
							update_post_meta( $post_id, 'color', '#a36597' );
						}
					}
				}

				wp_safe_redirect( add_query_arg( array( 'post_type' => 'yith-wccos-ostatus' ), admin_url( 'edit.php' ) ) );
				exit();
			}
		}

		/**
		 * Show free how-to
		 *
		 * @return   void
		 * @since    1.0
		 */
		public function show_how_to_tab() {
			$landing = YITH_WCCOS_TEMPLATE_PATH . '/free-how-to.php';
			file_exists( $landing ) && require $landing;
		}

		/**
		 * Declare support for WooCommerce features.
		 *
		 * @since 4.4.0
		 */
		public function declare_wc_features_support() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', YITH_WCCOS_INIT, true );
			}
		}
	}
}

/**
 * Unique access to instance of YITH_WCCOS_Admin class
 *
 * @return YITH_WCCOS_Admin
 */
function yith_wccos_admin() {
	return YITH_WCCOS_Admin::get_instance();
}
