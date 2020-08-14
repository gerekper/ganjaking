<?php
! defined( 'YITH_POS' ) && exit; // Exit if accessed directly


if ( ! class_exists( 'YITH_POS_Admin' ) ) {
	/**
	 * Class YITH_POS_Admin
	 * Main Backend Administrator Class
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	class YITH_POS_Admin {

		/** @var YITH_POS_Admin */
		private static $_instance;

		/** @var YIT_Plugin_Panel_WooCommerce $_panel Panel object */
		private $_panel;

		/** @var string Panel page */
		private $_panel_page = 'yith_pos_panel';

		/** @var YITH_POS_Store_Post_Type_Admin */
		public $store_post_type_admin;

		/** @var YITH_POS_Register_Post_Type_Admin */
		public $register_post_type_admin;

		/** @var YITH_POS_Receipt_Post_Type_Admin */
		public $receipt_post_type_admin;


		/**
		 * Singleton implementation
		 *
		 * @return YITH_POS_Admin
		 */
		public static function get_instance() {
			return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
		}

		/**
		 * YITH_POS_Admin constructor.
		 */
		private function __construct() {

			$this->store_post_type_admin    = YITH_POS_Store_Post_Type_Admin::get_instance();
			$this->register_post_type_admin = YITH_POS_Register_Post_Type_Admin::get_instance();
			$this->receipt_post_type_admin  = YITH_POS_Receipt_Post_Type_Admin::get_instance();

			//panel
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
			add_action( 'admin_init', array( $this, 'add_metabox' ), 10 );

			//Add action links
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_POS_DIR . '/' . basename( YITH_POS_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			//custom tabs
			add_action( 'yith_pos_dashboard_tab', array( $this, 'dashboard_tab' ), 10, 2 );

			add_filter( 'yith_plugin_fw_get_field_template_path', array( $this, 'add_custom_field_path' ), 20, 2 );

			add_action( 'admin_bar_menu', array( $this, 'admin_bar_menus' ), 32 );

			//customer VAT
			add_filter( 'woocommerce_admin_billing_fields', array( $this, 'add_billing_vat' ) );
			add_filter( 'woocommerce_customer_meta_fields', array( $this, 'add_billing_vat_meta_field' ) );

			// YITH-POS / Online filter
			add_action( 'restrict_manage_posts', array( $this, 'add_order_filters' ), 10, 1 );
			add_action( 'pre_get_posts', array( $this, 'filter_orders' ), 10, 1 );

			//Gateways POS Management
			add_filter( 'woocommerce_payment_gateways_setting_columns', array( $this, 'gateway_enabled_pos_column' ), 10, 1 );
			add_action( 'woocommerce_payment_gateways_setting_column_status_pos', array( $this, 'gateway_pos_column_content' ), 10, 1 );

			add_action( 'pre_get_posts', array( $this, 'filter_post_types_for_managers' ) );
			add_filter( 'wp_count_posts', array( $this, 'count_post_types_for_managers' ), 10, 2 );
		}

		/**
		 * Add the column Enabled on YITH POS on Gateway WooCommerce Settings.
		 *
		 * @param $default_column
		 *
		 * @return mixed
		 */
		public function gateway_enabled_pos_column( $default_column ) {
			$i = array_search( 'status', array_keys( $default_column ) );
			if ( $i ++ ) {
				$default_column = array_slice( $default_column, 0, $i, true ) + array( 'status_pos' => __( 'Enabled on YITH POS', 'yith-point-of-sale-for-woocommerce' ) ) + array_slice( $default_column, $i, count( $default_column ) - $i, true );
			} else {
				$default_column[ 'status_pos' ] = __( 'Enabled on YITH POS', 'yith-point-of-sale-for-woocommerce' );
			}

			return $default_column;
		}

		/**
		 * Add the onoff on gateways table.
		 *
		 * @param $gateway
		 */
		public function gateway_pos_column_content( $gateway ) {

			$pos_gateways      = yith_pos_get_enabled_gateways_option();
			$required_gateways = (array) yith_pos_get_required_gateways();

			$method_title = $gateway->get_method_title() ? $gateway->get_method_title() : $gateway->get_title();
			$is_required  = in_array( $gateway->id, $required_gateways );

			echo '<td class="status_pos" width="5%">';
			if ( ! $is_required ) {
				echo '<a class="yith_pos_gateway_toggle_enable" href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . strtolower( $gateway->id ) ) ) . '">';
			}

			if ( in_array( $gateway->id, $pos_gateways ) ) {
				/* Translators: %s Payment gateway name. */
				echo '<span class="woocommerce-input-toggle woocommerce-input-toggle--enabled_on_yith_pos" aria-label="' . esc_attr( sprintf( __( 'The "%s" payment method is currently enabled on YITH POS', 'yith-point-of-sale-for-woocommerce' ), $method_title ) ) . '">' . esc_attr__( 'Yes', 'yith-point-of-sale-for-woocommerce' ) . '</span>';
			} else {
				/* Translators: %s Payment gateway name. */
				echo '<span class="woocommerce-input-toggle woocommerce-input-toggle--disabled" aria-label="' . esc_attr( sprintf( __( 'The "%s" payment method is currently disabled on YITH POS', 'yith-point-of-sale-for-woocommerce' ), $method_title ) ) . '">' . esc_attr__( 'No', 'yith-point-of-sale-for-woocommerce' ) . '</span>';
			}
			if ( ! $is_required ) {
				echo '</a>';
			}

			echo '</td>';
		}

		/**
		 * Get the Panel tabs
		 *
		 * @return array
		 */
		public function get_panel_tabs() {
			$tabs_with_caps = array(
				'dashboard' => array(
					'title'      => __( 'Dashboard', 'yith-point-of-sale-for-woocommerce' ),
					'capability' => 'yith_pos_manage_pos_options',
				),
				'stores'    => array(
					'title'      => __( 'Stores', 'yith-point-of-sale-for-woocommerce' ),
					'capability' => yith_pos_get_post_capability( 'edit_posts', YITH_POS_Post_Types::$store ),
				),
				'registers' => array(
					'title'      => __( 'Registers', 'yith-point-of-sale-for-woocommerce' ),
					'capability' => yith_pos_get_post_capability( 'edit_posts', YITH_POS_Post_Types::$register ),
				),
				'receipts'  => array(
					'title'      => __( 'Receipts', 'yith-point-of-sale-for-woocommerce' ),
					'capability' => yith_pos_get_post_capability( 'edit_posts', YITH_POS_Post_Types::$receipt ),
				),
				'settings'  => array(
					'title'      => __( 'Customization', 'yith-point-of-sale-for-woocommerce' ),
					'capability' => 'yith_pos_manage_pos_options',
				),
			);

			$tabs_with_caps = apply_filters( 'yith_pos_settings_admin_tabs_with_caps', $tabs_with_caps );
			$tabs           = array();

			foreach ( $tabs_with_caps as $key => $tab ) {
				$capability = isset( $tab[ 'capability' ] ) ? $tab[ 'capability' ] : 'yith_pos_manage_pos';
				if ( current_user_can( $capability ) ) {
					$tabs[ $key ] = $tab[ 'title' ];
				}
			}

			return apply_filters( 'yith_pos_settings_admin_tabs', $tabs );
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @use      YIT_Plugin_Panel_WooCommerce class
		 * @see      plugin-fw/lib/yit-plugin-panel-woocommerce.php
		 */
		public function register_panel() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = $this->get_panel_tabs();

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => 'Point of Sale for WooCommerce',
				'menu_title'       => 'Point of Sale',
				'capability'       => 'yith_pos_manage_pos',
				'parent'           => '',
				'parent_page'      => 'yit_plugin_panel',
				'class'            => yith_set_wrapper_class(),
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_POS_DIR . '/plugin-options',
			);


			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once( '../plugin-fw/lib/yit-plugin-panel-wc.php' );
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * @param WP_Query $query
		 */
		public function filter_post_types_for_managers( $query ) {
			if ( isset( $query->query[ 'post_type' ] ) &&
			     in_array( $query->query[ 'post_type' ], array( YITH_POS_Post_Types::$store, YITH_POS_Post_Types::$register ) ) &&
			     ! current_user_can( 'yith_pos_manage_others_pos' ) ) {

				if ( YITH_POS_Post_Types::$store === $query->query[ 'post_type' ] ) {
					$query->set( 'meta_query', yith_pos_get_manager_stores_meta_query() );
				} elseif ( YITH_POS_Post_Types::$register === $query->query[ 'post_type' ] ) {
					$manager_stores = yith_pos_get_manager_stores();
					$manager_stores = ! ! $manager_stores ? $manager_stores : array( 0 );
					$query->set( 'meta_query', array( array( 'key' => '_store_id', 'value' => $manager_stores, 'compare' => 'IN' ) ) );
				}
			}
		}

		public function count_post_types_for_managers( $counts, $type ) {
			if ( in_array( $type, array( YITH_POS_Post_Types::$store, YITH_POS_Post_Types::$register ) ) &&
			     ! current_user_can( 'yith_pos_manage_others_pos' ) ) {

				$stati = get_post_stati();

				// Update count object
				foreach ( $stati as $status ) {
					if ( YITH_POS_Post_Types::$store === $type ) {
						$meta_query = yith_pos_get_manager_stores_meta_query();
					} else {
						$manager_stores = yith_pos_get_manager_stores();
						$manager_stores = ! ! $manager_stores ? $manager_stores : array( 0 );
						$meta_query     = array( array( 'key' => '_store_id', 'value' => $manager_stores, 'compare' => 'IN' ) );
					}
					$args            = array(
						'post_type'      => $type,
						'posts_per_page' => - 1,
						'fields'         => 'ids',
						'post_status'    => $status,
						'meta_query'     => $meta_query
					);
					$posts           = get_posts( $args );
					$counts->$status = count( $posts );
				}

			}

			return $counts;
		}

		/**
		 * Dashboard tab
		 *
		 * @access public
		 *
		 * @param array $options
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function dashboard_tab( $options ) {
			// close the wrap div and open the Rood div
			echo '</div><!-- /.wrap -->';
			echo "<div class='woocommerce-page' >";
			yith_pos_get_view( 'panel/dashboard.php' );
		}


		/**
		 * Add additional custom fields.
		 *
		 * @param object $field_template
		 * @param object $field
		 *
		 * @return string
		 */
		public function add_custom_field_path( $field_template, $field ) {
			$custom_type = array(
				'show-categories',
				'show-products',
				'show-cashiers',
				'presets'
			);

			if ( in_array( $field[ 'type' ], $custom_type ) ) {
				$field_template = YITH_POS_VIEWS_PATH . '/fields/' . $field[ 'type' ] . '.php';
			}

			return $field_template;
		}

		/**
		 * Add the action links to plugin admin page.
		 *
		 * @param $plugin_meta
		 * @param $plugin_file
		 * @param $plugin_data
		 * @param $status
		 *
		 * @return array
		 * @use plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_POS_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args[ 'slug' ]       = YITH_POS_SLUG;
				$new_row_meta_args[ 'is_premium' ] = true;
			}

			return $new_row_meta_args;
		}

		/**
		 * Action Links
		 * add the action links to plugin admin page
		 *
		 * @param $links | links plugin array
		 *
		 * @return mixed Array
		 * @return mixed
		 * @use    plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {

			if ( function_exists( 'yith_add_action_links' ) ) {
				$links = yith_add_action_links( $links, $this->_panel_page, false );
			}

			return $links;
		}

		/**
		 * Register plugins for activation tab
		 */
		public function register_plugin_for_activation() {
			if ( function_exists( 'YIT_Plugin_Licence' ) ) {
				YIT_Plugin_Licence()->register( YITH_POS_INIT, YITH_POS_SECRET_KEY, YITH_POS_SLUG );
			}
		}

		/**
		 * Register plugins for update tab
		 */
		public function register_plugin_for_updates() {
			if ( function_exists( 'YIT_Upgrade' ) ) {
				YIT_Upgrade()->register( YITH_POS_SLUG, YITH_POS_INIT );
			}
		}

		/**
		 * Add the "Visit POST" link in admin bar main menu.
		 *
		 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance.
		 *
		 * @since 2.4.0
		 */
		public function admin_bar_menus( $wp_admin_bar ) {
			if ( ! is_admin() || ! is_admin_bar_showing() ) {
				return;
			}

			// Show only when the user is a member of this site, or they're a super admin.
			if ( ! is_user_member_of_blog() && ! is_super_admin() ) {
				return;
			}

			// Don't display when shop page is the same of the page on front.
			if ( intval( get_option( 'page_on_front' ) ) === yith_pos_get_pos_page_id() ) {
				return;
			}

			// Add an option to visit the store.
			$wp_admin_bar->add_node( array(
				                         'parent' => 'site-name',
				                         'id'     => 'view-pos',
				                         'title'  => __( 'Visit POS', 'yith-point-of-sale-for-woocommerce' ),
				                         'href'   => yith_pos_get_pos_page_url(),
			                         ) );
		}


		/**
		 * Add VAT inside the customer billing information.
		 */
		public function add_billing_vat( $billing_fields ) {
			$billing_fields[ 'vat' ] = array(
				'label' => __( 'VAT', 'yith-point-of-sale-for-woocommerce' ),
				'show'  => false,
			);

			return $billing_fields;
		}

		/**
		 * Add VAT inside the customer billing information.
		 */
		public function add_billing_vat_meta_field( $billing_fields ) {
			$billing_fields[ 'billing' ][ 'fields' ][ 'billing_vat' ] = array(
				'label'       => __( 'VAT', 'yith-point-of-sale-for-woocommerce' ),
				'description' => '',
			);

			return $billing_fields;
		}


		/**
		 * Add filters to orders for YITH_POS orders or online orders.
		 *
		 * @param string $post_type
		 */
		public function add_order_filters( $post_type ) {
			if ( 'shop_order' === $post_type ) {
				$selected_type = isset( $_REQUEST[ 'yith_pos_order_type' ] ) ? $_REQUEST[ 'yith_pos_order_type' ] : '';

				$types = array(
					'pos'    => __( 'YITH POS', 'yith-point-of-sale-for-woocommerce' ),
					'online' => __( 'Online', 'yith-point-of-sale-for-woocommerce' )
				);

				$placeholder    = esc_attr__( 'Filter by YITH POS or online', 'yith-point-of-sale-for-woocommerce' );
				$enhanced_attrs = implode( ' ', array(
					"class='wc-enhanced-select'",
					"data-placeholder='{$placeholder}'",
					"data-allow_clear='true'",
					"aria-hidden='true'",
					"style='min-width:200px;'"
				) );
				echo "<select name='yith_pos_order_type' {$enhanced_attrs}>";
				echo "<option value='' />";
				foreach ( $types as $id => $name ) {
					echo "<option value='{$id}' " . selected( $id, $selected_type, false ) . ">$name</option>";
				}
				echo "</select>";
			}
		}

		/**
		 * Filter the the YITH_POS orders from the other online orders.
		 *
		 * @param WP_Query $query
		 */
		public function filter_orders( $query ) {
			if ( $query->is_main_query() && isset( $query->query[ 'post_type' ] ) && 'shop_order' === $query->query[ 'post_type' ] ) {
				$meta_query = ! ! $query->get( 'meta_query' ) ? $query->get( 'meta_query' ) : array();
				$changed    = false;
				if ( ! empty( $_REQUEST[ 'yith_pos_order_type' ] ) ) {
					$type = $_REQUEST[ 'yith_pos_order_type' ];
					if ( 'pos' === $type ) {
						$changed      = true;
						$meta_query[] = array( 'key' => '_yith_pos_order', 'value' => '1' );
					} elseif ( 'online' === $type ) {
						$changed      = true;
						$meta_query[] = array(
							'relation' => 'OR',
							array( 'key' => '_yith_pos_order', 'value' => '0', ),
							array( 'key' => '_yith_pos_order', 'compare' => 'NOT EXISTS' )
						);
					}
				}

				if ( $changed ) {
					$query->set( 'meta_query', $meta_query );
				}
			}
		}


		/**
		 * Add metabox in the order editor
		 *
		 * @return  void
		 * @author  Emanuela Castorina
		 * @since   1.0.0
		 */
		public function add_metabox() {

			$post_id = isset( $_REQUEST[ 'post' ] ) ? $_REQUEST[ 'post' ] : ( isset( $_REQUEST[ 'post_ID' ] ) ? $_REQUEST[ 'post_ID' ] : 0 );


			if ( ( isset( $_GET[ 'post_type' ] ) && 'shop_order' === $_GET[ 'post_type' ] ) || ( 'shop_order' === get_post_type( $post_id ) ) ) {
				$order = wc_get_order( $post_id );
				if ( ! $order ) {
					return;
				}

				$is_pos_order = $order->get_meta( '_yith_pos_order' );
				if ( empty( $is_pos_order ) || $is_pos_order === 'no' ) {
					return;
				}
				$html = $this->get_metabox_template( $order );
				$args = array(
					'label'    => __( 'POS Info', 'yith-point-of-sale-for-woocommerce' ),
					'pages'    => 'shop_order', //or array( 'post-type1', 'post-type2')
					'context'  => 'side', //('normal', 'advanced', or 'side')
					'priority' => 'high',
					'tabs'     => array(
						'settings' => array(
							'label'  => __( 'Settings', 'yith-point-of-sale-for-woocommerce' ),
							'fields' => array(
								'pos_info' => array(
									'type'  => 'html',
									'html'  => $html,
									'label' => __( '', 'yith-point-of-sale-for-woocommerce' ),
									'desc'  => __( '', 'yith-point-of-sale-for-woocommerce' ),
									'std'   => 'yes',
								)
							)
						)
					)
				);
				if ( ! function_exists( 'YIT_Metabox' ) ) {
					require_once( YITH_POS_DIR . 'plugin-fw/yit-plugin.php' );
				}
				$metabox = YIT_Metabox( 'yith-pos-order' );
				$metabox->init( $args );
			}


		}

		public function get_metabox_template( $order ) {

			$store_id    = $order->get_meta( '_yith_pos_store' );
			$register_id = $order->get_meta( '_yith_pos_register' );
			$cashier_id  = $order->get_meta( '_yith_pos_cashier' );

			$store      = yith_pos_get_store( $store_id );
			$store_name = $store->get_name();

			$register      = yith_pos_get_register( $register_id );
			$register_name = $register->get_name();

			$cashier = get_user_by( 'id', $cashier_id );

			$args = array(
				'register_name'   => $register_name,
				'store_name'      => $store_name,
				'cashier'         => $cashier->first_name . ' ' . $cashier->last_name,
				'payment_methods' => yith_pos_get_order_payment_methods( $order ),
				'currency'        => $order->get_currency()
			);

			ob_start();
			yith_pos_get_view( 'metabox/shop-order-pos-info-metabox.php', $args );

			return ob_get_clean();
		}
	}


	/**
	 * Unique access to instance of YITH_POS_Admin class
	 *
	 * @return YITH_POS_Admin
	 * @since 1.0.0
	 */
	function YITH_POS_Admin() {
		return YITH_POS_Admin::get_instance();
	}
}