<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WPV_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'YITH_Commissions' ) ) {
	/**
	 * Class YITH_Commissions
	 *
	 * @class      YITH_Commissions
	 * @package    Yithemes
	 * @since      Version 2.0.0
	 * @author     Your Inspiration Themes
	 */
	class YITH_Commissions {

		/**
		 * Whether or not to show order item meta added by plugin in order page
		 *
		 * @var bool Whether or not to show order item meta
		 *
		 * @since 1.0
		 */
		public $show_order_item_meta = true;

		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0
		 */
		public $version = YITH_WPV_VERSION;

		/**
		 * Commission page screen
		 *
		 * @var string
		 * @since 1.0
		 */
		protected $_screen = 'yith_vendor_commissions';

		/**
		 * Main Instance
		 *
		 * @var string
		 * @since 1.0
		 * @access protected
		 */
		protected static $_instance = null;

		/**
		 * Commission table name
		 *
		 * @var string
		 * @since 1.0
		 * @access protected
		 */
		protected static $_commissions_table_name = 'yith_vendors_commissions';

		/**
		 * Commission notes table name
		 *
		 * @var string
		 * @since 1.0
		 * @access protected
		 */
		protected static $_commissions_notes_table_name = 'yith_vendors_commissions_notes';

		/**
		 * Admin notice messages
		 *
		 * @var array
		 * @since 1.0
		 */
		protected $_messages = array();

		/**
		 * Database version
		 *
		 * @var string
		 * @var string
		 * @since 1.0
		 * @access protected
		 */
		protected static $_db_version = YITH_WPV_DB_VERSION;

		/**
		 * Status changing capabilities
		 *
		 * @var array
		 * @since 1.0
		 * @access protected
		 */
		protected $_status_capabilities = array(
			'pending'    => array( 'unpaid', 'paid', 'cancelled' ),
			'unpaid'     => array( 'pending', 'paid', 'cancelled', 'processing' ),
			'paid'       => array( 'pending', 'unpaid', 'refunded' ),
			'cancelled'  => array(),
			'refunded'   => array(),
			'processing' => array( 'paid', 'unpaid', 'cancelled' ),
		);

		/**
		 * Main plugin Instance
		 *
		 * @static
		 * @return YITH_Commissions Main instance
		 *
		 * @since  1.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Constructor
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return mixed|YITH_Commissions
		 * @since  1.0.0
		 * @access public
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'add_commissions_table_wpdb' ), 0 );
			add_action( 'switch_blog', array( $this, 'add_commissions_table_wpdb' ), 0 );

			add_action( 'yith_wcmv_checkout_order_processed', array( $this, 'register_commissions' ), 10, 1 );
			add_action( 'woocommerce_order_status_changed', array( $this, 'manage_status_changing' ), 10, 3 );
			add_action( 'woocommerce_refund_created', array( $this, 'register_commission_refund' ), 10, 2 );
			add_action( 'before_delete_post', array( $this, 'remove_refund_commission_helper' ) );
			add_action( 'deleted_post', array( $this, 'remove_refund_commission' ) );

			$this->_admin_init();
		}

		/**
		 * Admin init
		 */
		protected function _admin_init() {
			if ( ! is_admin() ) {
				return;
			}

			add_action( 'admin_menu', array( $this, 'add_menu_item' ) );
			add_filter( 'admin_title', array( $this, 'change_commission_view_page_title' ), 10, 2 );

			add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_ids' ) );

			add_action( 'current_screen', array( $this, 'add_screen_option' ) );
			add_filter( 'set-screen-option', array( $this, 'set_screen_option' ), 10, 3 );
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );

			/* == Update commission status from Commissions Page == */
			add_action( 'admin_action_yith_commission_table_actions', array( $this, 'table_update_status' ) );

			//Set messages
			$this->_messages = apply_filters( 'yith_commissions_admin_notice',
				array(
					'error'   => __( 'Commission status not updated!', 'yith-woocommerce-product-vendors' ),
					'updated' => __( 'Commission status updated!', 'yith-woocommerce-product-vendors' ),
					'pay-process' => __( 'Payment successful. In a few minutes you will receive an email with the outcome of the payment and the commission state will be changed accordingly.', 'yith-woocommerce-product-vendors' ),
					'pay-failed'  => __( 'Payment failed.', 'yith-woocommerce-product-vendors' )
				)
			);

			add_filter( 'woocommerce_attribute_label', array( $this, 'commissions_attribute_label' ), 10, 3 );
		}

		/**
		 * Return the screen id for commissions page
		 *
		 * @since 1.0
		 */
		public function get_screen() {
			return $this->_screen;
		}

		/**
		 * Define the list of status
		 *
		 * @since 1.0
		 */
		public function get_status() {

			/**
			 * WC Order Status Icon  ->  YITH Commissions Status
			 * pending               ->  pending
			 * processing            ->  pending
			 * on-hold               ->  unpaid
			 * completed             ->  paid
			 * cancelled             ->  cancelled
			 * failed                ->  cancelled
			 * refunded              ->  refunded
			 *
			 */
			return array(
				'paid'       => __( 'Paid', 'yith-woocommerce-product-vendors' ),
				'unpaid'     => __( 'Unpaid', 'yith-woocommerce-product-vendors' ),
				'pending'    => __( 'Pending', 'yith-woocommerce-product-vendors' ),
				'refunded'   => __( 'Refunded', 'yith-woocommerce-product-vendors' ),
				'cancelled'  => __( 'Cancelled', 'yith-woocommerce-product-vendors' ),
				'processing' => __( 'Processing', 'yith-woocommerce-product-vendors' ),
			);
		}

		/**
		 * Print admin notice
		 *
		 * @since  1.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 *
		 * @fire yith_commissions_admin_notice hooks
		 */
		public function admin_notice() {
			if ( ! empty( $_GET['message'] ) && ! empty( $_GET['page'] ) && $this->get_screen() == $_GET['page'] && isset( $this->_messages[ $_GET['message'] ] ) ) {
				$type = $_GET['message'];
				if ( in_array( $type, array( 'pay-process' ) ) ) {
					$type = 'update-nag';
				}

				else if( in_array( $type, array( 'pay-failed' ) ) ) {
					$type = 'error';
				}

				$text    = ! empty( $_GET['text'] ) ? urldecode( $_GET['text'] ) : '';
				$message = in_array( $type, array( 'updated', 'error' ) ) ? sprintf( "<p>%s %s</p>", $this->_messages[ $_GET['message'] ], $text ) : sprintf( "%s %s", $this->_messages[ $_GET['message'] ], $text );

				?>
				<div class="<?php echo $type ?>">
					<?php echo $message; ?>
				</div>
				<?php
			}
		}

		/**
		 * Check for status changing
		 *
		 * @param $new_status
		 * @param $old_status
		 *
		 * @return bool
		 */
		public function is_status_changing_permitted( $new_status, $old_status ) {
		    $status_capabilities = $this->get_status_capabilities();
			return $new_status != $old_status && in_array( $new_status, $status_capabilities[$old_status] );
		}

		/**
		 * Add the Commissions menu item in dashboard menu
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0
		 * @return void
		 * @fire yith_wc_product_vendors_commissions_menu_items hooks
		 * @see    wp-admin\includes\plugin.php -> add_menu_page()
		 */
		public function add_menu_item() {
			$vendor = yith_get_vendor( 'current', 'user' );
			$is_super_user = $vendor->is_super_user();

			if( apply_filters( 'yith_wcmv_show_commission_page', $is_super_user || $vendor->is_valid() && $vendor->has_limited_access() && $vendor->is_owner() ) ) {

				$args = apply_filters( 'yith_wc_product_vendors_commissions_menu_items', array(
						'page_title' => __( 'Commissions', 'yith-woocommerce-product-vendors' ),
						'menu_title' => __( 'Commissions', 'yith-woocommerce-product-vendors' ),
						'capability' => 'edit_products',
						'menu_slug'  => $this->_screen,
						'function'   => array( $this, 'commissions_details_page' ),
						'icon'       => 'dashicons-tickets',
						'position'   => 58 /* After WC Products */
					)
				);

				extract( $args );

				add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon, $position );
			}
		}

		/**
		 * Show the Commissions page
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0
		 * @return void
		 * @fire yith_vendors_commissions_template hooks
		 */
		public function commissions_details_page() {
			if ( isset( $_GET['view'] ) ) {
				$commission = YITH_Commission( absint( $_GET['view'] ) );
				$args = apply_filters( 'yith_vendors_commission_view_template', array( 'commission' => $commission ) );
				yith_wcpv_get_template( 'commission-view', $args, 'admin' );
			}
			else {
				if ( ! class_exists( 'WP_List_Table' ) ) {
					require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
				}

				$path_class = YITH_WPV_PATH . 'includes/lib/class.yith-commissions-list-table';
				$class = 'YITH_Commissions_List_Table';

				require_once( $path_class . '.php' );
				if ( file_exists( $path_class . '-premium.php' ) ) {
					require_once( $path_class . '-premium.php' );
					$class .= '_Premium';
				}

				$class = apply_filters( 'yith_wcmv_commissions_list_table_class', $class );

				/** @var YITH_Commissions_List_Table|YITH_Commissions_List_Table_Premium $commissions_table */
				$commissions_table = new $class();
				$commissions_table->prepare_items();

				$args = apply_filters( 'yith_vendors_commissions_template', array(
						'commissions_table' => $commissions_table,
						'page_title'        => sprintf( '%s %s', YITH_Vendors()->get_singular_label( 'ucfirst' ), _x( 'Commissions', '[Part of] Vendor Commissions', 'yith-woocommerce-product-vendors' ) )
					)
				);

				yith_wcpv_get_template( 'commissions', $args, 'admin' );
			}
		}

		/**
		 * Change the page title of commission detail page
		 *
		 * @param $admin_title
		 * @param $title
		 *
		 * @return string
		 * @since 1.0
		 */
		public function change_commission_view_page_title( $admin_title, $title ) {
			if ( isset( $_GET['page'] ) && $_GET['page'] == $this->_screen && ! empty( $_GET['view'] ) ) {
				$title = sprintf( __( 'Commission #%d details', 'yith-woocommerce-product-vendors' ), absint( $_GET['view'] ) );
				$admin_title = sprintf( __( '%1$s &lsaquo; %2$s &#8212; WordPress' ), $title, get_bloginfo( 'name' ) );
			}

			return $admin_title;
		}

		/**
		 * Create the {$wpdb->prefix}_yith_vendor_commissions table
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0
		 * @return void
		 * @see    dbDelta()
		 */
		public static function create_commissions_table() {

			/**
			 * If exists yith_product_vendors_commissions_table_created option return null
			 */
			if ( get_option( 'yith_product_vendors_commissions_table_created' ) && ! isset( $_GET['yith_wcmv_force_create_commissions_table'] ) ) {
				return;
			}

			/**
			 * Check if dbDelta() exists
			 */
			if ( ! function_exists( 'dbDelta' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			}

			global $wpdb;
			$charset_collate = $wpdb->get_charset_collate();

			$table_name = $wpdb->prefix . self::$_commissions_table_name;
			$create = "CREATE TABLE IF NOT EXISTS $table_name (
                        ID bigint(20) NOT NULL AUTO_INCREMENT,
                        order_id bigint(20) NOT NULL,
                        user_id bigint(20) NOT NULL,
                        vendor_id bigint(20) NOT NULL,
                        line_item_id bigint(20) NOT NULL,
                        rate decimal(5,4) NOT NULL,
                        amount double(15,4) NOT NULL,
                        status varchar(100) NOT NULL,
                        type VARCHAR(30) NOT NULL DEFAULT 'product',
                        last_edit DATETIME NOT NULL DEFAULT '000-00-00 00:00:00',
                        last_edit_gmt DATETIME NOT NULL DEFAULT '000-00-00 00:00:00',
                        PRIMARY KEY (ID)
                        ) $charset_collate;";
			dbDelta( $create );

			$table_name = $wpdb->prefix . self::$_commissions_notes_table_name;
			$create = "CREATE TABLE IF NOT EXISTS $table_name (
                        ID bigint(20) NOT NULL AUTO_INCREMENT,
                        commission_id bigint(20) NOT NULL,
                        note_date DATETIME NOT NULL DEFAULT '000-00-00 00:00:00',
                        description TEXT,
                        PRIMARY KEY (ID)
                        ) $charset_collate;";
			dbDelta( $create );

			add_option( 'yith_product_vendors_db_version', self::$_db_version );
			add_option( 'yith_product_vendors_commissions_table_created', true );
		}

		/**
		 * Commissions API - set table name
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0
		 * @return void
		 */
		public function add_commissions_table_wpdb() {
			global $wpdb;
			$wpdb->commissions           = $wpdb->prefix . self::$_commissions_table_name;
			$wpdb->tables[]              = self::$_commissions_table_name;
			$wpdb->commissions_notes     = $wpdb->prefix . self::$_commissions_notes_table_name;
			$wpdb->tables[]              = self::$_commissions_notes_table_name;
		}

		/**
		 * Get Commissions
		 *
		 * @param array $q
		 *
		 * @return array
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0
		 */
		public function get_commissions( $q = array() ) {
			global $wpdb;

			$default_args = array(
				'line_item_id' => 0,
				'product_id'   => 0,
				'order_id'     => 0,
				'user_id'      => 0,
				'vendor_id'    => 0,
				'status'       => 'unpaid',
				'm'            => false,
				'date_query'   => false,
				's'            => '',
				'number'       => '',
				'offset'       => '',
				'paged'        => '',
				'orderby'      => 'ID',
				'order'        => 'ASC',
				'fields'       => 'ids',
                'table'        => $wpdb->commissions
			);

			$q = wp_parse_args( $q, $default_args );
			$q = apply_filters( 'yith_wcmv_get_commissions_args', $q );

			$table = $q['table'];

			// Fairly insane upper bound for search string lengths.
			if ( ! is_scalar( $q['s'] ) || ( ! empty( $q['s'] ) && strlen( $q['s'] ) > 1600 ) ) {
				$q['s'] = '';
			}

			// First let's clear some variables
			$where = '';
			$limits = '';
			$join = '';
			$groupby = '';
			$orderby = '';

			// query parts initializating
			$pieces = array( 'where', 'groupby', 'join', 'orderby', 'limits' );

			// filter
			if ( ! empty( $q['line_item_id'] ) ) {
				$where .= $wpdb->prepare( " AND c.line_item_id = %d", $q['line_item_id'] );
			}
			if ( ! empty( $q['product_id'] ) ) {
				$join .= " JOIN {$wpdb->prefix}woocommerce_order_items oi ON ( oi.order_item_id = c.line_item_id AND oi.order_id = c.order_id )";
				$join .= " JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim ON ( oim.order_item_id = oi.order_item_id )";
				$where .= $wpdb->prepare( " AND oim.meta_key = %s AND oim.meta_value = %s", '_product_id', $q['product_id'] );
			}
			if ( ! empty( $q['order_id'] ) ) {
				$where .= $wpdb->prepare( " AND c.order_id = %d", $q['order_id'] );
			}
			if ( ! empty( $q['user_id'] ) ) {
				$where .= $wpdb->prepare( " AND c.user_id = %d", $q['user_id'] );
			}
			if ( ! empty( $q['vendor_id'] ) ) {
				$where .= $wpdb->prepare( " AND c.vendor_id = %d", $q['vendor_id'] );
			}
            if ( ! empty( $q['type'] ) && 'all' != $q['type'] ) {
                $where .= $wpdb->prepare( " AND c.type = %s", $q['type'] );
            }
			if ( ! empty( $q['status'] ) && 'all' != $q['status'] ) {
				if ( is_array( $q['status'] ) ) {
					$q['status'] = implode( "', '", $q['status'] );
				}
				$where .= sprintf( " AND c.status IN ( '%s' )", $q['status'] );
			}

			// The "m" parameter is meant for months but accepts datetimes of varying specificity
			if ( $q['m'] ) {
				$q['m'] = absint( preg_replace( '|[^0-9]|', '', $q['m'] ) );

				$join .= strpos( $join, "$wpdb->posts o" ) === false ? " JOIN $wpdb->posts o ON o.ID = c.order_id" : '';
				$where .= " AND o.post_type = 'shop_order'";

				$where .= " AND YEAR(o.post_date)=" . substr($q['m'], 0, 4);
				if ( strlen($q['m']) > 5 )
					$where .= " AND MONTH(o.post_date)=" . substr($q['m'], 4, 2);
				if ( strlen($q['m']) > 7 )
					$where .= " AND DAYOFMONTH(o.post_date)=" . substr($q['m'], 6, 2);
				if ( strlen($q['m']) > 9 )
					$where .= " AND HOUR(o.post_date)=" . substr($q['m'], 8, 2);
				if ( strlen($q['m']) > 11 )
					$where .= " AND MINUTE(o.post_date)=" . substr($q['m'], 10, 2);
				if ( strlen($q['m']) > 13 )
					$where .= " AND SECOND(o.post_date)=" . substr($q['m'], 12, 2);
			}

			// Handle complex date queries
			if ( ! empty( $q['date_query'] ) ) {
				$join .= strpos( $join, "$wpdb->posts o" ) === false ? " JOIN $wpdb->posts o ON o.ID = c.order_id" : '';
				$where .= " AND o.post_type = 'shop_order'";

				$date_query = new WP_Date_Query( $q['date_query'], 'o.post_date' );
				$where .= $date_query->get_sql();
			}

			// Search
			if ( $q['s'] ) {
				// added slashes screw with quote grouping when done early, so done later
				$q['s'] = stripslashes( $q['s'] );
				// there are no line breaks in <input /> fields
				$q['s'] = str_replace( array( "\r", "\n" ), '', $q['s'] );

				// order
				$join .= strpos( $join, "$wpdb->posts o" ) === false ? " JOIN $wpdb->posts o ON o.ID = c.order_id" : '';

				// product
				$join .= strpos( $join, 'woocommerce_order_items' ) === false ? " JOIN {$wpdb->prefix}woocommerce_order_items oi ON ( oi.order_item_id = c.line_item_id AND oi.order_id = c.order_id )" : '';
				$where .= " AND oi.order_item_type = 'line_item'";

				// user
				$join .= " JOIN $wpdb->users u ON u.ID = c.user_id";
				$join .= " JOIN $wpdb->usermeta um ON um.user_id = c.user_id";
				$join .= " JOIN $wpdb->usermeta um2 ON um2.user_id = c.user_id";
				$where .= " AND um.meta_key = 'first_name'";
				$where .= " AND um2.meta_key = 'last_name'";

				$s = array(
					// search by order
					$wpdb->prepare( "c.order_id = %s", $q['s'] ),
					// Search by Commission id
					$wpdb->prepare( "c.ID = %s", $q['s'] ),
					// search by product
					$wpdb->prepare( "oi.order_item_name LIKE %s", "%{$q['s']}%" ),
					// search by username
					$wpdb->prepare( "u.user_login LIKE %s", "%{$q['s']}%" ),
					$wpdb->prepare( "u.user_nicename LIKE %s", "%{$q['s']}%" ),
					$wpdb->prepare( "u.user_email LIKE %s", "%{$q['s']}%" ),
					$wpdb->prepare( "um.meta_value LIKE %s", "%{$q['s']}%" ),
					$wpdb->prepare( "um2.meta_value LIKE %s", "%{$q['s']}%" ),
				);

				$where .= ' AND ( ' . implode( ' OR ', $s ) . ' )';
			}

			// Order
			if ( ! is_string( $q['order'] ) || empty( $q['order'] ) ) {
				$q['order'] = 'DESC';
			}

			if ( 'ASC' === strtoupper( $q['order'] ) ) {
				$q['order'] = 'ASC';
			} else {
				$q['order'] = 'DESC';
			}

			// Order by.
			if ( empty( $q['orderby'] ) ) {
				/*
				 * Boolean false or empty array blanks out ORDER BY,
				 * while leaving the value unset or otherwise empty sets the default.
				 */
				if ( isset( $q['orderby'] ) && ( is_array( $q['orderby'] ) || false === $q['orderby'] ) ) {
					$orderby = '';
				} else {
					$orderby = "c.ID " . $q['order'];
				}
			} elseif ( 'none' == $q['orderby'] ) {
				$orderby = '';
			} else {
				$orderby_array = array();
				if ( is_array( $q['orderby'] ) ) {
					foreach ( $q['orderby'] as $_orderby => $order ) {
						$orderby = addslashes_gpc( urldecode( $_orderby ) );

						if ( ! is_string( $order ) || empty( $order ) ) {
							$order = 'DESC';
						}

						if ( 'ASC' === strtoupper( $order ) ) {
							$order = 'ASC';
						} else {
							$order = 'DESC';
						}

						$orderby_array[] = $orderby . ' ' . $order;
					}
					$orderby = implode( ', ', $orderby_array );

				} else {
					$q['orderby'] = urldecode( $q['orderby'] );
					$q['orderby'] = addslashes_gpc( $q['orderby'] );

					foreach ( explode( ' ', $q['orderby'] ) as $i => $orderby ) {
						$orderby_array[] = $orderby;
					}
					$orderby = implode( ' ' . $q['order'] . ', ', $orderby_array );

					if ( empty( $orderby ) ) {
						$orderby = "c.ID " . $q['order'];
					} elseif ( ! empty( $q['order'] ) ) {
						$orderby .= " {$q['order']}";
					}
				}
			}

			// Paging
			if ( ! empty($q['paged']) && ! empty($q['number']) ) {
				$page = absint($q['paged']);
				if ( !$page )
					$page = 1;

				if ( empty( $q['offset'] ) ) {
					$pgstrt = absint( ( $page - 1 ) * $q['number'] ) . ', ';
				}
				else { // we're ignoring $page and using 'offset'
					$q['offset'] = absint( $q['offset'] );
					$pgstrt      = $q['offset'] . ', ';
				}
				$limits = 'LIMIT ' . $pgstrt . $q['number'];
			}

			$clauses = compact( $pieces );

			$where   = isset( $clauses['where'] ) ? $clauses['where'] : '';
			$groupby = isset( $clauses['groupby'] ) ? $clauses['groupby'] : '';
			$join    = isset( $clauses['join'] ) ? $clauses['join'] : '';
			$orderby = isset( $clauses['orderby'] ) ? $clauses['orderby'] : '';
			$limits  = isset( $clauses['limits'] ) ? $clauses['limits'] : '';

			if ( ! empty($groupby) )
				$groupby = 'GROUP BY ' . $groupby;
			if ( !empty( $orderby ) )
				$orderby = 'ORDER BY ' . $orderby;

			$found_rows = '';
			if ( ! empty( $limits ) ) {
				$found_rows = 'SQL_CALC_FOUND_ROWS';
			}

			$fields = 'c.ID';

			if( 'count' != $q['fields'] && 'ids' != $q['fields'] ){
				if( is_array( $q['fields'] ) ){
					$fields = implode( ',', $q['fields'] );
				}

				else {
					$fields = $q['fields'];
				}
			}

			$res = $wpdb->get_col( "SELECT $found_rows DISTINCT $fields FROM $table c $join WHERE 1=1 $where $groupby $orderby $limits" );

			// return count
			if ( 'count' == $q['fields'] ) {
				return ! empty( $limits ) ? $wpdb->get_var( 'SELECT FOUND_ROWS()' ) : count( $res );
			}

			return $res;
		}

		/**
		 * Return the count of posts in base of query
		 *
		 * @param array $q
		 *
		 * @return int
		 * @since 1.0
		 */
		public function count_commissions( $q = array() ) {
			if ( 'last-query' == $q ) {
				global $wpdb;
				return $wpdb->get_var( 'SELECT FOUND_ROWS()' );
			}

			$q['fields'] = 'count';
			return $this->get_commissions( $q );
		}

		/**
		 * Register the commission linked to order
		 *
		 * @param $order_id int The order ID
		 * @param $posted   array The value request
		 *
		 * @since 1.0
		 */
		public function register_commissions( $order_id ) {
		    // Only process commissions once
			$order = wc_get_order( $order_id );
			$processed = $order->get_meta( '_commissions_processed', true );

			if ( $processed && $processed == 'yes' ) {
				return;
			}

			// check all items of order to know if there is some vendor to credit and what are the products to process
			foreach ( $order->get_items() as $item_id => $item ) {
                $_product = null;

                if( YITH_Vendors()->is_wc_2_7_or_greather && is_callable( array( $item, 'get_product' ) ) ){
                    $_product = $item->get_product();
                }

                else {
                    $_product = $order->get_product_from_item( $item );
                }

                if( $_product->is_type( 'variation' ) ){
                    $_variation = $_product;
                    $_product = wc_get_product( yit_get_base_product_id( $_variation ) );
                }


				$vendor = yith_get_vendor( $_product, 'product' );

                if ( $vendor->is_valid() ) {

					// calculate amount
					$amount = $this->calculate_commission( $vendor, $order, $item, $item_id );

					// no amount to apply
					if ( empty( $amount ) ) {
						continue;
					}

					$args = array(
						'line_item_id'  => $item_id,
						'order_id'      => $order_id,
						'user_id'       => $vendor->get_owner(),
						'vendor_id'     => $vendor->id,
						'amount'        => $amount,
						'last_edit'     => current_time( 'mysql' ),
						'last_edit_gmt' => current_time( 'mysql', 1 )
					);

					$_product_commission = yit_get_prop( $_product, '_product_commission', true );

					// get commission from product if exists
					if ( ! empty( $_product_commission ) ) {
						$args['rate'] = (float) $_product_commission / 100;
					}

					// add commission in pending
					$commission_id = YITH_Commission()->add( apply_filters( 'yith_wcmv_add_commission_args', $args ) );

	                $tax_management          = get_option( 'yith_wpv_commissions_tax_management', 'website' );
	                $commission_included_tax = $include_tax = 'website' == $tax_management || 'vendor' == $tax_management ? false : true;;
	                $commission_included_coupon = 'yes' == get_option( 'yith_wpv_include_coupon', 'no' );

					if( ! empty( $commission_id ) ){
						$msg = self::get_tax_and_coupon_management_message( $tax_management, $commission_included_coupon );

					    $commission = YITH_Commission( $commission_id );
					    $commission->add_note( apply_filters( 'yith_wcmv_new_commission_note', $msg ) );
                    }

					// add line item to retrieve simply the commission associated (parent order)
					wc_update_order_item_meta( $item_id, '_commission_id', $commission_id );

					// add commission_included_tax and _commission_included_coupon to parent and vendor order
                    $parent_item_id = wc_get_order_item_meta( $item_id, '_parent_line_item_id', true );
                    $parent_item_id = is_array( $parent_item_id ) ? array_shift ( $parent_item_id ) : $parent_item_id;
					$parent_item_id = absint( $parent_item_id );

					$item_ids = array(
                        'parent' => $parent_item_id,
                        'child'  => $item_id
                    );

                    foreach( $item_ids as $type => $id ){
                        wc_update_order_item_meta( $id, '_commission_included_tax',    $tax_management );
                        wc_update_order_item_meta( $id, '_commission_included_coupon', $commission_included_coupon ? 'yes' : 'no' );

                        do_action( 'yith_wcmv_add_extra_commission_order_item_meta', $id );
                    }

                    $this->register_commission_to_parent_order( $commission_id, $item_id, '_commission_id', $order );

                    do_action( 'yith_wcmv_after_single_register_commission', $commission_id, $item_id, '_commission_id', $order );
				}
			}

			// Mark commissions as processed
			$order->add_meta_data( '_commissions_processed', 'yes', true );
            $order->save_meta_data();

			do_action( 'yith_commissions_processed', $order_id );

			if ( apply_filters( 'yith_wcmv_force_to_trigger_new_order_email_action', false ) ) {
				WC()->mailer();
				do_action( 'yith_wcmv_new_order_email', $order_id );
			}
		}

		/**
		 * Calculate commission for an order, vendor and item
		 *
		 * @param $vendor YITH_Vendor
		 * @param $order  WC_Order
		 * @param $item   array
		 *
		 * @return mixed
		 */
		public function calculate_commission( $vendor, $order, $item, $item_id ) {

			//check for product commission
            $_product = null;

            if( YITH_Vendors()->is_wc_2_7_or_greather && is_callable( array( $item, 'get_product' ) ) ){
                $_product = $item->get_product();
            }

            else {
                $_product = $order->get_product_from_item( $item );
            }

            if( $_product->is_type( 'variation' ) ){
                $_variation = $_product;
                $_product = wc_get_product( yit_get_base_product_id( $_variation ) );
            }

            $_product_commission = yit_get_prop( $_product, '_product_commission', true );

            // Get percentage for commission
			$commission = ! empty( $_product_commission ) ? (float) $_product_commission / 100 : (float) $vendor->get_commission();
			$commission = apply_filters( 'yith_wcmv_product_commission', $commission, $vendor, $order, $item, $item_id );

			// If commission is 0% then go no further
			if ( ! $commission ) {
				return 0;
			}

			// Check
			$get_item_amount = 'yes' == get_option( 'yith_wpv_include_coupon' ) ? 'get_item_total' : 'get_item_subtotal';

			// Get item amount params
			$tax_management = get_option( 'yith_wpv_commissions_tax_management', 'website' );
			$include_tax = 'website' == $tax_management || 'vendor' == $tax_management ? false : true;

			// Retrieve the real amount of single item, with right discounts applied and without taxes

			$line_total =  $order->$get_item_amount( $item, $include_tax, false ) * $item['qty'];
            $line_total = (float) apply_filters( 'yith_wcmv_get_line_total_amount_for_commission', $line_total, $order, $item, $item_id );

			// If total is 0 after discounts then go no further
			if ( ! $line_total ) {
				return 0;
			}

			// Get total amount for commission
			$amount = (float) $line_total * $commission;

			// If commission amount is 0 then go no further
			if ( ! $amount ) {
				return 0;
			}

			if( 'vendor' == $tax_management ){
				$vendor_item_tax = wc_round_tax_total( $item->get_total_tax() );
				if( ! empty( $vendor_item_tax ) ){
					$amount = (float) $amount + $vendor_item_tax;
                }
            }

			return apply_filters( 'yith_wcmv_calculate_commission_amount', $amount, $vendor, $order, $item, $item_id );
		}

		/**
		 * Manage the status changing
		 *
		 * @param $order_id
		 * @param $old_status
		 * @param $new_status
		 *
		 * @since 1.0
		 */
		public function manage_status_changing( $order_id, $old_status, $new_status ) {
			switch ( $new_status ) {

				case 'completed' :
					$this->register_commissions_unpaid( $order_id );
					break;

				case 'refunded' :
					$this->register_commissions_refunded( $order_id );
					break;

				case 'cancelled' :
				case 'failed' :
					$this->register_commissions_cancelled( $order_id );
					break;

				case 'pending':
				case 'on-hold':
					$this->register_commissions_pending( $order_id );
					break;

			}
		}

		public function register_commissions_status( $order_id, $status ){
            // Ensure the order have commissions processed
			$order = wc_get_order( $order_id );
			$processed = $order->get_meta( '_commissions_processed', true );
            $commission_processed = false;

            if ( $processed == 'yes' ) {

                $commission_ids = array();

                foreach ( $order->get_items() as $item_id => $item ){
                    if ( ! empty( $item['commission_id'] ) ) {
                        $commission_ids[] = $item['commission_id'];
                    }
                }

                $commission_ids = apply_filters( 'yith_wcmv_register_commissions_status', $commission_ids, $order_id, $status );

                foreach ( $commission_ids as $commission_id ) {

                    $commission_processed = true;

                    // retrieve commission
                    $commission = YITH_Commission( intval( $commission_id ) );

                    $paid_by_gateway = $skip_update = false;

	                if( 'woocommerce_order_status_changed' == current_action() && 'paid' == $commission->get_status() ){
	                    $paid_by_gateway = $order->get_meta( "_commission_{$commission->id}_paid_by_gateway", true );

	                    if( 'yes' == $paid_by_gateway ){
		                    $skip_update = true;
                        }
	                }

                    if( ! $skip_update ){
	                    // set commission as unpaid, ready to be paid
	                    $commission->update_status( $status );
	                    $commission->save_data();
	                    $order->save_meta_data();
                    }
                }
            }

            return $commission_processed;
        }

		/**
		 * Register the commission as unpaid when the order is completed
		 *
		 * @param $order_id
		 *
		 * @since 1.0
		 */
		public function register_commissions_unpaid( $order_id ) {
		    $this->register_commissions_status( $order_id, 'unpaid' );
		}

		/**
		 * Register the commission as refunded when there was a refund in the order
		 *
		 * @param $order_id
		 *
		 * @since 1.0
		 */
		public function register_commissions_refunded( $order_id ) {
		    $order = wc_get_order( $order_id );
		    $refunded = $order->get_meta( '_commissions_refunded', true );

            if( ( empty( $refunded ) || $refunded != 'no' ) ){
                $processed = $this->register_commissions_status( $order_id, 'refunded' );

                if( $processed ){
                    $order->add_meta_data( '_commissions_refunded', 'yes', true );
                    $order->save_meta_data();
                }
            }
		}

		/**
		 * Register the commission as unpaid when the order is completed
		 *
		 * @param $order_id
		 *
		 * @since 1.0
		 */
		public function register_commissions_cancelled( $order_id ) {
			$order = wc_get_order( $order_id );
            $cancelled =  $order->get_meta( '_commissions_cancelled', true );

            if( ( empty( $cancelled ) || $cancelled != 'no' ) ){
                $processed = $this->register_commissions_status( $order_id, 'cancelled' );

                if( $processed ){
                    $order->add_meta_data( '_commissions_cancelled', 'yes', true );
                    $order->save_meta_data();
                }
            }
		}

		/**
		 * Register the commission as pending when the order is on-hold
		 *
		 * @param $order_id
		 *
		 * @since 1.0
		 */
		public function register_commissions_pending( $order_id ) {
            $this->register_commissions_status( $order_id, 'pending' );
		}

		/**
		 * Recalculate all refunds of the order of this refund
		 *
		 * @param $new_refund_id
		 *
		 * @since 1.0
		 */
		public function register_commission_refund( $new_refund_id, $args ) {
		    $suborder_id = $args['order_id'];
		    // Is vendor suborder ?
            $suborder = wc_get_order( $suborder_id );

            if( 'yith_wcmv_vendor_suborder' == $suborder->get_created_via() && ! empty( wp_get_post_parent_id( $suborder_id ) ) ){
                //This is a vendor suborder. Get the suborder refund
                $refund = wc_get_order( $new_refund_id );
                $items = $refund->get_items( array( 'line_item', 'shipping' ) );

                foreach( $items as $refund_item ){
	                $item_id       = $refund_item->get_meta( '_refunded_item_id' );
	                $line_item     = $suborder->get_item( $item_id );

	                $refund_amount = 0;

	                //Taxes and Coupons Management for Vendor's commissions
	                $vendor_tax_management = $vendor_coupon_management = $commission_included_coupons = false;

	                $commission_id = $line_item->get_meta( '_commission_id' );
	                $commission = YITH_Commission( $commission_id );

	                if( $commission->exists() && 'product' == $commission->type ){

		                //Tax Management for Vendors
		                $vendor_tax_management      = strtolower( $line_item->get_meta( '_commission_included_tax', true, 'edit' ) );

		                //Coupon Management for Vendors
		                $vendor_coupon_management    = strtolower( $line_item->get_meta( '_commission_included_coupon', true, 'edit' ) );
		                $commission_included_coupons = 'no' == $vendor_coupon_management ? false : true;

		                if( 'website' == $vendor_tax_management ){
			                $refund_amount = abs( $refund_item->get_total() );
		                }

		                else {
			                /**
			                 * 'split' == $vendor_tax_management || 'vendor' == $vendor_tax_management
			                 */
			                $refund_amount = abs( $refund_item->get_total() + $refund_item->get_total_tax() );
		                }
                    }

	                else {
		                /**
		                 * For Shipping Commissions
		                 */
		                $refund_amount = abs( $refund_item->get_total() + $refund_item->get_total_tax() );
                    }

                    //is line item full refunded ?
                    if( $refund_amount > 0 ){


	                    if( $commission->exists() ){
	                        $is_full_refunded = false;

	                        if( 'website' == $vendor_tax_management ){
	                            $is_full_refunded = $refund_amount == $line_item->get_total();
                            }

	                        else {
		                        /**
		                         * 'split' == $vendor_tax_management || 'vendor' == $vendor_tax_management
                                 *
                                 * OR
                                 *
                                 * Shipping Commissions
		                         */
		                        $is_full_refunded = $refund_amount == ( $line_item->get_total() + $line_item->get_total_tax() );
                            }

		                    if( $is_full_refunded ){
			                    //Full Refunded
			                    $commission->update_status( 'cancelled', '', true );
			                    $_refund_commission_amount = $commission_amount_refunded = -$commission->get_amount( 'edit' );
		                    }

		                    else {
		                        $tax_refund_amount = 0;
		                        if( $commission->exists() ){
			                        if( 'product' == $commission->type ){
				                        if ( 'vendor' == $vendor_tax_management ) {
					                        $refund_amount     = abs( $refund_item->get_total() );
					                        $tax_refund_amount = abs( $refund_item->get_total_tax() );
				                        }

				                        if( false === $commission_included_coupons ){
					                        $refund_amount = ( ( $refund_amount * $line_item->get_subtotal() ) / $line_item->get_total() );
				                        }
			                        }

			                        else {
				                        $refund_amount     = abs( $refund_item->get_total() );
				                        $tax_refund_amount = abs( $refund_item->get_total_tax() );
			                        }
                                }

			                    //Partial Refund
			                    $_refund_commission_amount = $commission_amount_refunded = round( $refund_amount, 2, PHP_ROUND_HALF_ODD ) * $commission->get_rate( 'edit' ) * - 1 + ( round( $tax_refund_amount, 2, PHP_ROUND_HALF_ODD )  * -1 );
			                    $commission_amount_refunded = $commission_amount_refunded + $commission->get_refund_amount( 'edit' );
		                    }

		                    $refund_item->add_meta_data( '_refund_commission_amount', $_refund_commission_amount, true );
		                    $refund_item->save();
		                    $commission->amount_refunded = $commission_amount_refunded;
		                    $message = sprintf( _x( 'Refunded: %s', 'Commission note', 'yith-woocommerce-product-vendors' ), wc_price( abs( $commission_amount_refunded ), array( 'currency' => $suborder->get_currency() ) ) );
                            $commission->add_note( $message );

		                    do_action( 'yith_wcmv_register_commission_refund', $refund, $refund_item, $commission );
                        }
                    }
                }
            }
		}

		/**
		 *
		 */
		public function delete_commission_refund(  $refund_id, $suborder_id, $parent_order_id  ){
			$refund      = wc_get_order( $refund_id );
			$suborder    = wc_get_order( $suborder_id );

			if( 'yith_wcmv_vendor_suborder' == $suborder->get_created_via() && ! empty( wp_get_post_parent_id( $suborder_id ) ) ){

				$items = $refund->get_items( array( 'line_item', 'shipping' ) );
				foreach ( $items as $refund_item ){
					$item_id       = $refund_item->get_meta( '_refunded_item_id' );

					$line_item     = $suborder->get_item( $item_id );
					$refund_amount = abs( $refund->get_total() );

					//is line item full refunded ?
					if( $refund_amount > 0 ){
						$commission_id = $line_item->get_meta( '_commission_id' );
						$commission = YITH_Commission( $commission_id );

						if( $commission->exists() ){
							$commission_amount_refunded  = (float) $refund_item->get_meta( '_refund_commission_amount', true, 'edit' );
							$commission->amount_refunded = (float) $commission->amount_refunded + (float) abs( $commission_amount_refunded );
							$message = sprintf( _x( 'Credited: %s', 'Commission note', 'yith-woocommerce-product-vendors' ), wc_price( abs( $commission_amount_refunded ), array( 'currency' => $suborder->get_currency() ) ) );
							$commission->add_note( $message );

							do_action( 'yith_wcmv_delete_commission_refund', $refund, $refund_item, $commission );

						}
					}
                }
			}

			return true;
        }

		/**
		 * Retrieve post meta 'refunded_commissions', before the refund will be deleted
		 *
		 * @param $refund_id
		 */
		public function remove_refund_commission_helper( $refund_id ) {
            if ( in_array( get_post_type( $refund_id ), wc_get_order_types(), true ) ) {
                $refund = new WC_Order_Refund( $refund_id );
                $this->_refunded_commissions = $refund instanceof WC_Order_Refund ? $refund->get_meta('_refunded_commissions', true ) : array();
            }
		}

		/**
		 * Restore a refund when it's deleted from order
		 *
		 * @param $refund_id
		 * @param bool $note
		 *
		 * @since 1.0
		 */
		public function remove_refund_commission( $refund_id, $note = true ) {
			if ( isset( $this->_refunded_commissions ) && $refunds = $this->_refunded_commissions ) {
				// remove post meta to delete every track of refunds
                $refund = new WC_Order_Refund( $refund_id );
				if( $refund instanceof WC_Order_Refund ){
				    $refund->delete_meta_data( '_refunded_commissions' );
				    $refund->save_meta_data();
                }

			}
		}

		/**
		 * The current credit of user
		 *
		 * @param $user_id
		 *
		 * @return float
		 * @since 1.0
		 */
		public function get_user_credit( $user_id ) {
			return floatval( get_user_meta( $user_id, '_vendor_commission_credit', true ) );
		}

		/**
		 * Increment the credit to the user
		 *
		 * @param $user_id
		 * @param $amount
		 *
		 * @since 1.0
		 */
		public function update_credit_to_user( $user_id, $amount ) {
			$current = $this->get_user_credit( $user_id );
			$current += $amount;

			update_user_meta( $user_id, '_vendor_commission_credit', $current );
		}

		/**
		 * @param $screen_ids array The WC Screen ids
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0
		 * @return array The screen ids
		 * @use woocommerce_screen_ids hooks
		 */
		public function add_screen_ids( $screen_ids ) {
			$screen_ids[] = 'toplevel_page_' . $this->_screen;
			return $screen_ids;
		}

		/**
		 * Update the commission status by Commissions page
		 *
		 * @since  1.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function table_update_status() {
			$args = array( 'page' => $this->_screen, 'message' => 'error' );

			$commission_id = ! empty( $_GET['commission_id'] ) ? $_GET['commission_id'] : 0;

			if ( isset( $_GET['view'] ) ) {
				$args['view'] = $_GET['view'];
				$commission_id = $_GET['view'];
			}

			if ( ! empty( $commission_id ) && ! empty( $_GET['new_status'] ) ) {
				$commission = YITH_Commission( $commission_id );
				if ( $commission->update_status( $_GET['new_status'] ) ) {
					$args['message'] = 'updated';
					if( apply_filters( 'yith_wcmv_send_commission_email_on_manually_update', false ) ){
						// emails
						WC()->mailer();
						do_action( "yith_vendors_commissions_{$_GET['new_status']}", $commission );
					}
				}
			}

			$url = esc_url( add_query_arg( $args, admin_url( 'admin.php' ) ) );
			wp_redirect( $url );
			exit;
		}

		/**
		 * Add Screen option
		 *
		 * @return void
		 */
		public function add_screen_option() {
			if ( 'toplevel_page_' . $this->_screen == get_current_screen()->id ) {
				add_screen_option( 'per_page', array( 'label' => __( 'Commissions', 'yith-woocommerce-product-vendors' ), 'default' => 20, 'option' => 'edit_commissions_per_page' ) );

			}
		}

		/**
		 * Save custom screen options
		 *
		 * @param $set      Filter value
		 * @param $option   Option id
		 * @param $value    The option value
		 *
		 * @return mixed
		 */
		public function set_screen_option( $set, $option, $value ){
			return 'edit_commissions_per_page' == $option ? $value : $set;
		}

		/**
		 * Change commission label value
		 *
		 * @param $attribute_label  The Label Value
		 * @param $meta_key         The Meta Key value
		 * @param $product          The Product object
		 *
		 * @return string           The label value
		 */
		public function commissions_attribute_label( $attribute_label, $meta_key, $product = false ){
			global $pagenow;

			if( $product && 'post.php' == $pagenow && isset( $_GET['post'] ) && $order = wc_get_order( $_GET['post'] ) ){
				/**
				 * remove_filter for WPML Compatibility
				 */
				remove_filter( 'woocommerce_attribute_label', array( $this, 'commissions_attribute_label' ), 10 );
				$line_items = $order->get_items( 'line_item' );
				add_filter( 'woocommerce_attribute_label', array( $this, 'commissions_attribute_label' ), 10, 3 );
				foreach( $line_items as $line_item_id => $line_item ){
					$product_id = yit_get_prop( $product, 'id' );
					if( $line_item['product_id'] == $product_id ){
						$commission_id = wc_get_order_item_meta( $line_item_id, '_commission_id', true );
						$admin_url = YITH_Commission( $commission_id )->get_view_url( 'admin' );
						$attribute_label = '_commission_id' == $meta_key ? sprintf( "<a href='%s' class='%s'>%s</a>", $admin_url, 'commission-id-label', 'commission_id' ) : $attribute_label;
					}
				}
			}
			return $attribute_label;
		}

		/**
		 * Multiple Delete Bulk commission
		 *
		 * @param $order_id array  The order ids to apply the bulk action
		 * @param $action   string Bulk action type
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since 1.8.4
		 * @return void
		 */
		public function bulk_action( $order_ids, $action = 'delete' ){
			switch( $action ){
				case 'delete':
					foreach ( $order_ids as $order_id ) {
						$commission_ids = YITH_Commissions()->get_commissions( array( 'order_id' => $order_id, 'status' => $this->get_status() ) );
						foreach ( $commission_ids as $commission_id ) {
							$commission = YITH_Commission( $commission_id );
							if ( $commission_id ) {
								$commission->remove();
							}
						}
					}
					break;
			}
		}

        /**
         * Add commission id from parent to child order
         *
         * @internal moved from YITH_Orders
         * @since WooCommerce 2.7
         */
        public function register_commission_to_parent_order ( $commission_id, $child_item_id, $key, $suborder ) {
            // add line item to retrieve simply the commission associated (child order)
            $order_class = get_class( YITH_Vendors()->orders );
            $parent_item_id = $order_class::get_parent_item_id ( $suborder, $child_item_id );
            ! empty( $parent_item_id ) && wc_update_order_item_meta ( $parent_item_id, '_child_' . $key, $commission_id );
        }

        /**
         * Return the commissions table name
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 2.0.3
         * @return string table name
         */
        public function get_commissions_table_name(){
            return self::$_commissions_table_name;
        }

		/**
         * Get the message for tax and coupon managemnet system for commission
         *
		 * @param null $commission_included_tax
		 * @param null $commission_included_coupon
		 *
		 * @return string the message to show
		 */
        public static function get_tax_and_coupon_management_message( $commission_included_tax = null, $commission_included_coupon = null ){
	        $commission_included_tax             = is_null( $commission_included_tax ) ? get_option( 'yith_wpv_commissions_tax_management', 'website' ) : $commission_included_tax;
	        $commission_included_coupon = is_null( $commission_included_coupon ) ? 'yes' == get_option( 'yith_wpv_include_coupon', 'no' ) : $commission_included_coupon;


            $tax_string = array(
                'website' => _x( 'Credit taxes to the website admin', '[Admin]: Option description', 'yith-woocommerce-product-vendors' ),
                'split'   => _x( 'Split tax by percentage between website admin and vendor', '[Admin]: Option description', 'yith-woocommerce-product-vendors' ),
                'vendor'  => _x( 'Credit taxes to the vendor', '[Admin]: Option description', 'yith-woocommerce-product-vendors' ),
            );

            //Add note to commission to know if the commission have benne calculated included or escluded tax and coupon
            $coupon = $commission_included_coupon ? _x( 'included', 'means: Vendor commission have been calculated: tax included', 'yith-woocommerce-product-vendors' ) : _x( 'excluded', 'means: Vendor commission have been calculated: tax excluded', 'yith-woocommerce-product-vendors' );

            $tax_message = sprintf( '<br>* %s: <em>%s</em>',
                _x( 'tax', 'part of: tax included or tax excluded', 'yith-woocommerce-product-vendors' ),
                strtolower( $tax_string[ $commission_included_tax ] )
            );

            $tax_message = apply_filters( 'yith_wcmv_commission_tax_message', $tax_message );

            $commission_have_been_calculated_text = _x( 'Vendor commission have been calculated', 'part of: Vendor commission have been calculated: tax included', 'yith-woocommerce-product-vendors' ) . ':';
	        $commission_have_been_calculated_text = apply_filters( 'yith_wcmv_commission_have_been_calculated_text', $commission_have_been_calculated_text );

	        $msg = sprintf( '%s<br>* %s <em>%s</em>%s',
		        $commission_have_been_calculated_text,
                _x( 'coupon', 'part of: coupon included or coupon excluded', 'yith-woocommerce-product-vendors' ),
                $coupon,
                $tax_message
            );

            return $msg;
        }

		/**
		 * Status capabilities Map
         *
		 * @return array capabilities allowd change list
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 2.5.0
		 */
        public function get_status_capabilities(){
            return apply_filters( 'yith_wcmv_get_status_capability_map', $this->_status_capabilities );
        }
	}
}

/**
 * Main instance of plugin
 *
 * @return YITH_Commissions
 * @since  1.0
 * @author Andrea Grillo <andrea.grillo@yithemes.com>
 */
if ( ! function_exists( 'YITH_Commissions' ) ) {
	function YITH_Commissions() {
		return YITH_Commissions::instance();
	}
}

YITH_Commissions();