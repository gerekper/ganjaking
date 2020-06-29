<?php
/**
 * Admin class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Deposits and Down Payments
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCDP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCDP_Admin' ) ) {
	/**
	 * WooCommerce Deposits and Down Payments Admin
	 *
	 * @since 1.0.0
	 */
	class YITH_WCDP_Admin {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCDP_Admin
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Docs url
		 *
		 * @var string Official documentation url
		 * @since 1.0.0
		 */
		public $doc_url = 'https://yithemes.com/docs-plugins/yith-woocommerce-deposits-and-down-payments/';

		/**
		 * Premium landing url
		 *
		 * @var string Premium landing url
		 * @since 1.0.0
		 */
		public $premium_landing_url = 'https://yithemes.com/themes/plugins/yith-woocommerce-deposits-and-down-payments/';

		/**
		 * Live demo url
		 * @var string Live demo url
		 * @since 1.0.0
		 */
		public $live_demo_url = 'https://plugins.yithemes.com/yith-woocommerce-deposits-and-down-payments/';

		/**
		 * List of available tab for deposit panel
		 *
		 * @var array
		 * @access public
		 * @since  1.0.0
		 */
		public $available_tabs = array();

		/**
		 * Constructor method
		 *
		 * @return \YITH_WCDP_Admin
		 * @since 1.0.0
		 */
		public function __construct() {
			// sets available tab
			$this->available_tabs = apply_filters( 'yith_wcdp_available_admin_tabs', array(
				'settings' => __( 'Settings', 'yith-woocommerce-deposits-and-down-payments' ),
				'premium'  => __( 'Premium Version', 'yith-woocommerce-deposits-and-down-payments' )
			) );

			// register plugin panel
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
			add_action( 'yith_wcdp_premium_tab', array( $this, 'print_premium_tab' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

			// register product tabs
			add_filter( 'woocommerce_product_data_tabs', array( $this, 'register_product_tabs' ) );
			add_action( 'woocommerce_product_data_panels', array( $this, 'print_product_deposit_tabs' ), 10 );

			// register quick edit / bulk edit
			add_action( 'quick_edit_custom_box', array( $this, 'print_bulk_editing_fields' ), 10, 2 );
			add_action( 'bulk_edit_custom_box', array( $this, 'print_bulk_editing_fields' ), 10, 2 );
			add_action( 'save_post', array( $this, 'save_bulk_editing_fields' ), 10, 2 );

			// save tabs options
			add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_deposit_tabs' ), 10, 1 );

			// admin order view handling
			add_filter( 'request', array( $this, 'filter_order_list' ), 10, 1 );
			add_filter( 'wp_count_posts', array( $this, 'filter_order_counts' ), 10, 3 );
			add_filter( 'manage_shop_order_posts_columns', array( $this, 'shop_order_columns' ), 15 );
			add_action( 'manage_shop_order_posts_custom_column', array( $this, 'render_shop_order_columns' ) );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
			add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hide_order_item_meta' ) );
			add_action( 'woocommerce_before_order_itemmeta', array(
				$this,
				'print_full_payment_order_itemmeta'
			), 10, 2 );

			add_filter( 'woocommerce_order_item_get_name', array( $this, 'filter_order_items' ), 10, 2 );
			add_filter( 'woocommerce_order_get_items', array( $this, 'filter_order_items' ), 10, 2 );

			// filter woocomerce reports
			add_filter( 'woocommerce_reports_get_order_report_data_args', array( $this, 'filter_sales_report' ) );

			// register plugin links & meta row
			add_filter( 'plugin_action_links_' . YITH_WCDP_INIT, array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'add_plugin_meta' ), 10, 5 );
		}

		/* === HELPER METHODS === */

		/**
		 * Return array of screen ids for affiliate plugin
		 *
		 * @return mixed Array of available screens
		 * @since 1.0.0
		 */
		public function get_screen_ids() {
			$base = sanitize_title( __( 'YITH Plugins', 'yith-plugin-fw' ) );

			$screen_ids = array(
				$base . '_page_yith_wcdp_panel'
			);

			return apply_filters( 'yith_wcdp_screen_ids', $screen_ids );
		}

		/* === PANEL METHODS === */

		/**
		 * Register panel
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_panel() {
			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => __( 'Deposits and Down Payments', 'yith-woocommerce-deposits-and-down-payments' ),
				'menu_title'       => __( 'Deposits and Down Payments', 'yith-woocommerce-deposits-and-down-payments' ),
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => 'yith_wcdp_panel',
				'admin-tabs'       => $this->available_tabs,
				'options-path'     => YITH_WCDP_DIR . 'plugin-options'
			);

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once( YITH_WCDP_DIR . 'plugin-fw/lib/yit-plugin-panel-wc.php' );
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * Print premium tab
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_premium_tab() {
			include( YITH_WCDP_DIR . 'templates/admin/deposits-premium-panel.php' );
		}

		/**
		 * Enqueue admin side scripts
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue() {
			// enqueue scripts
			$screen = get_current_screen();
			$path   = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? 'unminified/' : '';
			$suffix = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '' : '.min';

			$scripts_screens = array_merge(
				$this->get_screen_ids(),
				array(
					'edit-shop_order',
					'shop_order',
					'product'
				)
			);

			if ( in_array( $screen->id, $scripts_screens ) ) {
				wp_register_script( 'yith-wcdp', YITH_WCDP_URL . 'assets/js/admin/' . $path . 'yith-wcdp' . $suffix . '.js', array(
					'jquery',
					'jquery-ui-datepicker',
					'wc-admin-meta-boxes'
				), false, true );
				do_action( 'yith_wcdp_before_admin_script_enqueue' );
				wp_enqueue_script( 'yith-wcdp' );

				wp_register_style( 'yith-wcdp', YITH_WCDP_URL . 'assets/css/admin/yith-wcdp.css' );
				do_action( 'yith_wcdp_before_admin_style_enqueue' );
				wp_enqueue_style( 'yith-wcdp' );
			}
		}

		/* === BULK PRODUCT EDITING === */

		/**
		 * Print Quick / Bulk editing fields
		 *
		 * @param $column_name string Current column Name
		 * @param $post_type   string Current post type
		 *
		 * @return void
		 * @todo  review code when WC switches to custom table
		 * @since 1.0.2
		 */
		public function print_bulk_editing_fields( $column_name, $post_type ) {
			global $post;

			if ( $post_type != 'product' || $column_name != 'product_type' || ! $product = wc_get_product( $post->ID ) ) {
				return;
			}

			// define variables to use in template
			$enable_deposit = 'default';
			$force_deposit  = 'default';

			if ( $post ) {
				$enable_deposit = $product->get_meta( '_enable_deposit', true );
				$enable_deposit = ! empty( $enable_deposit ) ? $enable_deposit : 'default';

				$force_deposit = $product->get_meta( '_force_deposit', true );
				$force_deposit = ! empty( $force_deposit ) ? $force_deposit : 'default';
			}

			include( YITH_WCDP_DIR . 'templates/admin/product-deposit-bulk-edit.php' );
		}

		/**
		 * Save Quick / Bulk editing fields
		 *
		 * @param $post_id int Post id
		 *
		 * @return void
		 * @since 1.0.2
		 */
		public function save_bulk_editing_fields( $post_id, $post ) {
			// If this is an autosave, our form has not been submitted, so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			// Don't save revisions and autosaves.
			if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) || 'product' !== $post->post_type || ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			// Check nonce.
			if ( ! isset( $_REQUEST['woocommerce_quick_edit_nonce'] ) || ! wp_verify_nonce( $_REQUEST['woocommerce_quick_edit_nonce'], 'woocommerce_quick_edit_nonce' ) ) { // WPCS: input var ok, sanitization ok.
				return;
			}

			$post_ids       = ( ! empty( $_REQUEST['post'] ) ) ? (array) $_REQUEST['post'] : array();
			$enable_deposit = isset( $_REQUEST['_enable_deposit'] ) ? trim( $_REQUEST['_enable_deposit'] ) : 'default';
			$force_deposit  = isset( $_REQUEST['_force_deposit'] ) ? trim( $_REQUEST['_force_deposit'] ) : 'default';

			if ( empty( $post_ids ) ) {
				$post_ids = array( $post_id );
			}

			// if everything is in order
			if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {
				foreach ( $post_ids as $post_id ) {
					$product = wc_get_product( $post_id );

					if ( ! $product ) {
						continue;
					}

					if ( ! current_user_can( 'edit_post', $post_id ) ) {
						continue;
					}

					yit_save_prop( $product, array(
						'_enable_deposit' => $enable_deposit,
						'_force_deposit'  => $force_deposit
					) );
				}
			}
		}

		/* == PRODUCT TABS METHODS === */

		/**
		 * Register product tabs for deposit plugin
		 *
		 * @param $tabs array Registered tabs
		 *
		 * @return array Filtered array of registered tabs
		 * @since 1.0.0
		 */
		public function register_product_tabs( $tabs ) {
			$tabs = array_merge(
				$tabs,
				array(
					'deposit' => array(
						'label'  => __( 'Deposit', 'yith-woocommerce-deposits-and-down-payments' ),
						'target' => 'yith_wcdp_deposit_tab',
						'class'  => array( 'hide_if_grouped', 'hide_if_external' )
					)
				)
			);

			return $tabs;
		}

		/**
		 * Print product tab for deposit plugin
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_product_deposit_tabs() {
			global $post;

			$product = wc_get_product( $post->ID );

			// define variables to use in template
			$enable_deposit = yit_get_prop( $product, '_enable_deposit', true );
			$enable_deposit = ! empty( $enable_deposit ) ? $enable_deposit : 'default';

			$force_deposit = yit_get_prop( $product, '_force_deposit', true );
			$force_deposit = ! empty( $force_deposit ) ? $force_deposit : 'default';

			include( YITH_WCDP_DIR . 'templates/admin/product-deposit-tab.php' );
		}

		/**
		 * Save deposit tab options
		 *
		 * @param $post_id int Current product id
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function save_product_deposit_tabs( $post_id ) {
			$product = wc_get_product( $post_id );

			$enable_deposit = isset( $_POST['_enable_deposit'] ) ? trim( $_POST['_enable_deposit'] ) : 'default';
			$force_deposit  = isset( $_POST['_force_deposit'] ) ? trim( $_POST['_force_deposit'] ) : 'default';

			yit_save_prop( $product, array(
				'_enable_deposit' => $enable_deposit,
				'_force_deposit'  => $force_deposit
			) );
		}

		/* === PLUGIN LINK METHODS === */

		/**
		 * Get the premium landing uri
		 *
		 * @return  string The premium landing link
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since   1.0.0
		 */
		public function get_premium_landing_uri() {
			return $this->premium_landing_url;
		}

		/**
		 * Add plugin action links
		 *
		 * @param mixed $links Plugins links array
		 *
		 * @return array Filtered link array
		 * @since 1.0.0
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, 'yith_wcdp_panel', defined( 'YITH_WCDP_PREMIUM_INIT' ) );

			return $links;
		}

		/**
		 * Adds plugin row meta
		 *
		 * @param $plugin_meta array Array of unfiltered plugin meta
		 * @param $plugin_file string Plugin base file path
		 *
		 * @return array Filtered array of plugin meta
		 * @since 1.0.0
		 */
		public function add_plugin_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCDP_INIT' ) {
			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug'] = 'yith-woocommerce-deposits-and-down-payments';
			}

			if ( defined( 'YITH_WCDP_PREMIUM_INIT' ) ) {
				$new_row_meta_args['is_premium'] = true;

			}

			return $new_row_meta_args;
		}

		/* === ORDER VIEW METHODS === */

		/**
		 * Only show parent orders
		 *
		 * @param array $request Current request
		 *
		 * @return array          Modified request
		 * @todo   review code when WC switches to custom tables
		 *
		 * @since  1.0.0
		 */
		public function filter_order_list( $query ) {
			global $typenow;

			if ( 'shop_order' == $typenow ) {
				if ( ! isset( $query['meta_query'] ) ) {
					$query['meta_query'] = array();
				}

				$query['meta_query']['relation'] = 'OR';
				$query['meta_query'][]           = array(
					'key'     => '_created_via',
					'value'   => 'yith_wcdp_balance_order',
					'compare' => 'NOT IN'
				);

				$query['meta_query'][] = array(
					'key'     => '_created_via',
					'compare' => 'NOT EXISTS'
				);

				// $query['post_parent'] = 0;
				$query = apply_filters( "yith_wcdp_{$typenow}_request", $query );
			}

			return $query;
		}

		/**
		 * Filter views count for admin views, to count only parent orders
		 *
		 * @param array  $counts Array of post stati count
		 * @param string $type   Current post type
		 * @param string $perm   The permission to determine if the posts are 'readable' by the current user.
		 *
		 * @return array filtered array of counts
		 * @todo  review code when WC switches to custom tables
		 *
		 * @since 1.1.1
		 */
		public function filter_order_counts( $counts, $type, $perm ) {
			global $wpdb;

			if ( 'shop_order' == $type ) {
				$query = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s AND ID NOT IN ( SELECT post_ID FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s )";

				if ( 'readable' == $perm && is_user_logged_in() ) {
					$post_type_object = get_post_type_object( $type );
					if ( ! current_user_can( $post_type_object->cap->read_private_posts ) ) {
						$query .= $wpdb->prepare( " AND (post_status != 'private' OR ( post_author = %d AND post_status = 'private' ))",
							get_current_user_id()
						);
					}
				}
				$query .= ' GROUP BY post_status';

				$results = (array) $wpdb->get_results( $wpdb->prepare( $query, $type, '_created_via', 'yith_wcdp_balance_order' ), ARRAY_A );

				foreach ( $results as $row ) {
					if ( ! isset( $counts->{$row['post_status']} ) ) {
						continue;
					}

					$counts->{$row['post_status']} = $row['num_posts'];
				}
			}

			return $counts;
		}

		/**
		 * Add and reorder order table column
		 *
		 * @param $order_columns array The order table column
		 *
		 * @return string The label value
		 * @todo  review code when WC switches to custom tables
		 *
		 * @since 1.0.0
		 */
		public function shop_order_columns( $order_columns ) {
			$suborder      = array( 'balance' => _x( 'Balances', 'Admin: column heading in "Orders" table', 'yith-woocommerce-deposits-and-down-payments' ) );
			$ref_pos       = array_search( 'order_status', array_keys( $order_columns ) );
			$order_columns = array_slice( $order_columns, 0, $ref_pos + 1, true ) + $suborder + array_slice( $order_columns, $ref_pos + 1, count( $order_columns ) - 1, true );

			return $order_columns;
		}

		/**
		 * Output custom columns for coupons
		 *
		 * @param string $column
		 */
		public function render_shop_order_columns( $column ) {
			global $post, $the_order;

			if ( empty( $the_order ) || yit_get_prop( $the_order, 'id' ) != $post->ID ) {
				$_the_order = wc_get_order( $post->ID );
			} else {
				$_the_order = $the_order;
			}

			$order_id = yit_get_prop( $_the_order, 'id' );

			$suborder_ids = YITH_WCDP_Suborders()->get_suborder( $order_id );

			switch ( $column ) {
				case 'balance' :
					if ( $suborder_ids ) {
						foreach ( $suborder_ids as $suborder_id ) {
							$suborder        = wc_get_order( $suborder_id );
							$items           = $suborder->get_items( 'line_item' );
							$order_uri       = esc_url( 'post.php?post=' . absint( $suborder_id ) . '&action=edit' );
							$items_to_string = array();

							if ( ! empty( $items ) ) {
								foreach ( $items as $item ) {
									$product_id        = method_exists( $item, 'get_product_id' ) ? $item->get_product_id() : $item['product_id'];
									$product_uri       = get_edit_post_link( $product_id );
									$items_to_string[] = sprintf( '<a href="%s">%s</a> (%s)', $product_uri, $item['name'], wc_price( $suborder->get_item_total( $item ) ) );
								}
							}

							$items_to_string  = implode( '', $items_to_string );
							$additional_class = version_compare( WC()->version, '3.2.0', '>=' ) ? 'new-style' : '';

							printf(
								'<div class="suborder-details %s">
                                    <mark class="order-status tips status-%s " data-tip="%s"><span>%s</span></mark>
                                    <a href="#" class="order-preview" data-order-id="%d" title="%6$s">%6$s</a>
                                </div>',
								$additional_class,
								sanitize_title( $suborder->get_status() ),
								wc_get_order_status_name( $suborder->get_status() ),
								wc_get_order_status_name( $suborder->get_status() ),
								$suborder_id,
								__( 'Preview', 'yith-woocommerce-deposits-and-down-payments' )
							);
						}
					} else {
						echo '<span class="na">&ndash;</span>';
					}

					break;
				case 'order_status' :

					$column = '';

					if ( $suborder_ids ) {
						$count_uncompleted = 0;
						foreach ( $suborder_ids as $suborder_id ) {

							$suborder = wc_get_order( $suborder_id );

							if ( ! in_array( $suborder->get_status(), array(
								'completed',
								'processing',
								'cancelled',
								'refunded'
							) ) ) {
								$count_uncompleted ++;
							}
						}

						if ( $count_uncompleted ) {
							$column .= '<span class="pending-count">' . $count_uncompleted . '</span>';
						}
					}

					echo $column;

					break;
			}
		}

		/**
		 * Add suborder metaboxes for Deposit order
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function add_meta_boxes() {
			global $post;

			if ( ! $post ) {
				return;
			}

			$order = wc_get_order( $post->ID );

			if ( ! $order ) {
				return;
			}

			$has_suborder = YITH_WCDP_Suborders()->get_suborder( absint( $post->ID ) );
			$is_suborder  = YITH_WCDP_Suborders()->is_suborder( $post->ID );

			if ( $has_suborder ) {
				$metabox_suborder_description = _x( 'Balance Orders', 'Admin: Single order page. Suborder details box', 'yith-woocommerce-deposits-and-down-payments' ) . ' <span class="tips" data-tip="' . esc_attr__( 'Note: from this box you can monitor the status of suborders concerning full payments.', 'yith-woocommerce-deposits-and-down-payments' ) . '">[?]</span>';
				add_meta_box( 'yith-wcdp-woocommerce-suborders', $metabox_suborder_description, array(
					$this,
					'render_metabox_output'
				), 'shop_order', 'side', 'core', array( 'metabox' => 'suborders' ) );
			} elseif ( $is_suborder ) {
				$metabox_parent_order_description = _x( 'Deposit order', 'Admin: Single order page. Info box with parent order details', 'yith-woocommerce-deposits-and-down-payments' );
				add_meta_box( 'yith-wcdp-woocommerce-parent-order', $metabox_parent_order_description, array(
					$this,
					'render_metabox_output'
				), 'shop_order', 'side', 'high', array( 'metabox' => 'parent-order' ) );
			}
		}

		/**
		 * Output the suborder metaboxes
		 *
		 * @param $post     \WP_Post The post object
		 * @param $param    mixed Callback args
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function render_metabox_output( $post, $param ) {
			$order = wc_get_order( $post->ID );

			switch ( $param['args']['metabox'] ) {
				case 'suborders':
					$suborder_ids = YITH_WCDP_Suborders()->get_suborder( absint( $post->ID ) );
					echo '<ul class="suborders-list single-orders">';
					foreach ( $suborder_ids as $suborder_id ) {
						$suborder        = wc_get_order( absint( $suborder_id ) );
						$items           = $suborder->get_items( 'line_item' );
						$suborder_uri    = esc_url( 'post.php?post=' . absint( $suborder_id ) . '&action=edit' );
						$items_to_string = array();

						if ( ! empty( $items ) ) {
							foreach ( $items as $item ) {
								$items_to_string[] = $item['name'];
							}
						}

						$items_to_string = implode( ' | ', $items_to_string );

						echo '<li class="suborder-info">';
						printf( '<mark class="%s tips" data-tip="%s">%s</mark> <strong><a href="%s">#%s</a></strong> <small>(%s)</small><br/>',
							sanitize_title( $suborder->get_status() ),
							wc_get_order_status_name( $suborder->get_status() ),
							wc_get_order_status_name( $suborder->get_status() ),
							$suborder_uri,
							$suborder_id,
							$items_to_string
						);
						echo '<li>';
					}
					echo '</ul>';
					break;

				case 'parent-order':
					$parent_order_id  = yit_get_prop( $order, 'parent_id' );
					$parent_order_uri = esc_url( 'post.php?post=' . absint( $parent_order_id ) . '&action=edit' );
					printf( '<a href="%s">&#8592; %s</a>', $parent_order_uri, _x( 'Back to main order', 'Admin: single order page. Link to parent order', 'yith-woocommerce-deposits-and-down-payments' ) );
					break;
			}
		}

		/**
		 * Filter order items to add label for deposit orders
		 *
		 * @param $arg1 mixed Order items array
		 *
		 * @return mixed Filtered array of order items
		 * @since 1.0.0
		 */
		public function filter_order_items( $arg1, $arg2 ) {
			global $pagenow;

			// apply this filter only when in single post page
			if ( $pagenow != 'post.php' ) {
				return $arg1;
			}

			$deposit_prefix = apply_filters( 'yith_wcdp_deposit_label', __( 'Deposit', 'yith-woocommerce-deposits-and-down-payments' ) ) . ': ';
			$balance_prefix = apply_filters( 'yith_wcdp_full_payment_label', __( 'Balance', 'yith-woocommerce-deposits-and-down-payments' ) ) . ': ';

			if ( version_compare( WC()->version, '2.7.0', '>=' ) ) {

				if ( is_string( $arg1 ) ) {

					if ( wc_get_order_item_meta( $arg2->get_id(), '_deposit' ) && strpos( $arg1, $deposit_prefix ) === false ) {
						$arg1 = $deposit_prefix . $arg1;
					} elseif ( wc_get_order_item_meta( $arg2->get_id(), '_full_payment' ) && strpos( $arg1, $balance_prefix ) === false ) {
						$arg1 = $balance_prefix . $arg1;
					}
				}

				return $arg1;
			} else {
				/*
				 * Using temp array to avoid to change internal $items pointer, used by WooCommerce template on backend (includes/admin/meta-boxes/views/html-order-items.php)
				 */
				$tmp = $arg1;

				if ( ! empty( $tmp ) ) {
					foreach ( $tmp as $key => $elem ) {
						if ( isset( $elem['deposit'] ) && $elem['deposit'] && strpos( $arg1[ $key ]['name'], $deposit_prefix ) === false ) {
							$arg1[ $key ]['name'] = apply_filters( 'yith_wcdp_deposit_label', __( 'Deposit', 'yith-woocommerce-deposits-and-down-payments' ) ) . ': ' . $elem['name'];
						} elseif ( isset( $elem['full_payment'] ) && $elem['full_payment'] && strpos( $arg1[ $key ]['name'], $balance_prefix ) === false ) {
							$arg1[ $key ]['name'] = apply_filters( 'yith_wcdp_full_payment_label', __( 'Balance', 'yith-woocommerce-deposits-and-down-payments' ) ) . ': ' . $elem['name'];
						}
					}
				}

				return $arg1;
			}
		}

		/**
		 * Hide plugin item meta, when not in debug mode
		 *
		 * @param $hidden_items mixed Array of meta to hide on admin side
		 *
		 * @return mixed Filtered array of meta to hide
		 * @since 1.0.0
		 */
		public function hide_order_item_meta( $hidden_items ) {
			if ( ! ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) {
				$hidden_items = array_merge(
					$hidden_items,
					array(
						'_deposit',
						'_deposit_type',
						'_deposit_amount',
						'_deposit_rate',
						'_deposit_value',
						'_deposit_balance',
						'_deposit_shipping_method',
						'_full_payment',
						'_full_payment_id',
						'_deposit_id'
					)
				);
			}

			return $hidden_items;
		}

		/**
		 * Print Full Payment link to before order item meta section of order edit admin page
		 *
		 * @param $item_id int Current order item id
		 * @param $item    mixed Current item data
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_full_payment_order_itemmeta( $item_id, $item ) {
			if ( isset( $item['deposit'] ) && $item['deposit'] ) {
				$suborder = wc_get_order( $item['full_payment_id'] );

				if ( ! $suborder ) {
					return;
				}

				$suborder_id = yit_get_prop( $suborder, 'id' );
				?>
				<div class="yith-wcdp-full-payment">
					<small><a href="<?php echo get_edit_post_link( $suborder_id ) ?>"><?php printf( '%s: #%d', __( 'Full payment order', 'yith-woocommerce-deposits-and-down-payments' ), $suborder_id ) ?></a></small>
				</div>
				<?php
			}
		}

		/* === WOOCOMMERCE REPORT === */

		/**
		 * Filters report data, to remove balance from items sold count
		 *
		 * @param $args array Report args
		 *
		 * @return array Filtered array of arguments
		 */
		public function filter_sales_report( $args ) {
			if ( isset( $args['data'] ) && isset( $args['data']['_qty'] ) && 'order_item_meta' == $args['data']['_qty']['type'] ) {

				$args['where'][] = array(
					'key'      => 'order_items.order_id',
					'value'    => YITH_WCDP_Suborders()->get_all_balances_ids(),
					'operator' => 'not in',
				);
			}

			return $args;
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCDP_Admin
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}
	}
}

/**
 * Unique access to instance of YITH_WCDP_Admin class
 *
 * @return \YITH_WCDP_Admin
 * @since 1.0.0
 */
function YITH_WCDP_Admin() {
	return YITH_WCDP_Admin::get_instance();
}
