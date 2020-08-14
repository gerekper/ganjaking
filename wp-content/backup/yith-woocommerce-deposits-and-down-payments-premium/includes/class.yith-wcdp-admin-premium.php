<?php
/**
 * Admin class premium
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

if ( ! class_exists( 'YITH_WCDP_Admin_Premiuim' ) ) {
	/**
	 * WooCommerce Deposits and Down Payments Admin Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCDP_Admin_Premium extends YITH_WCDP_Admin {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCDP_Admin_Premium
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Constructor method
		 *
		 * @return \YITH_WCDP_Admin_Premium
		 * @since 1.0.0
		 */
		public function __construct() {
			// add variation settings
			add_action( 'woocommerce_product_after_variable_attributes', array(
				$this,
				'print_variation_deposit_settings'
			), 10, 3 );
			add_action( 'woocommerce_save_product_variation', array(
				$this,
				'save_variation_deposits_settings'
			), 10, 2 );

			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			// filters admin options
			add_filter( 'yith_wcdp_general_settings', array( $this, 'add_premium_general_settings' ) );
			add_filter( 'yith_wcdp_available_admin_tabs', array( $this, 'add_premium_options_tab' ) );

			// add shop order bulk actions
			add_action( 'admin_footer', array( $this, 'bulk_admin_footer' ), 15 );
			add_action( 'load-edit.php', array( $this, 'bulk_action' ) );

			// add resend notification email action
			add_action( 'admin_action_yith_wcdp_send_notification_email', array( $this, 'resend_notification_email' ) );
			add_action( 'admin_notices', array( $this, 'print_resend_notification_email_notice' ), 15 );

			// add resend new deposit email action
			add_action( 'woocommerce_order_action_new_deposit', array( $this, 'resend_new_deposit_email' ), 10, 1 );

			// add order views
			add_filter( 'views_edit-shop_order', array( $this, 'add_to_refund_deposit_view' ) );
			add_action( 'pre_get_posts', array( $this, 'filter_order_for_view' ) );

			// handle ajax actions
			add_action( 'wp_ajax_json_search_roles', array( $this, 'get_roles_via_ajax' ) );

			parent::__construct();

			// print admin order notices
			add_action( 'woocommerce_before_order_itemmeta', array( $this, 'print_item_to_refund_notice' ), 10, 2 );
		}

		/**
		 * Enqueue admin side scripts
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function enqueue() {
			parent::enqueue();

			wp_localize_script( 'yith-wcdp', 'yith_wcdp', array(
				'empty_row' => sprintf( '<tr class="no-items"><td class="colspanchange" colspan="0">%s</td></tr>', __( 'No items found.', 'yith-woocommerce-deposits-and-down-payments' ) ),
				'labels'    => array(
					'max_rate_notice' => __( 'Value entered cannot exceed 100%', 'yith-woocommerce-deposits-and-down-payments' )
				)
			) );
		}

		/* === ORDERS VIEW METHODS === */

		/**
		 * Filter orders for custom plugin views
		 *
		 * @return void
		 * @todo  review code when WC switches to custom tables
		 *
		 * @since 1.0.0
		 */
		public function filter_order_for_view() {
			if ( isset( $_GET['deposit_to_refund'] ) && $_GET['deposit_to_refund'] ) {
				add_filter( 'posts_join', array( $this, 'filter_order_join_for_view' ) );
				add_filter( 'posts_where', array( $this, 'filter_order_where_for_view' ) );
			}
		}

		/**
		 * Add joins to order view query
		 *
		 * @param $join string Original join query section
		 *
		 * @return string filtered join query section
		 * @since 1.0.0
		 */
		public function filter_order_join_for_view( $join ) {
			global $wpdb;

			$join .= " LEFT JOIN {$wpdb->prefix}woocommerce_order_items as i ON {$wpdb->posts}.ID = i.order_id
                       LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS im ON i.order_item_id = im.order_item_id";

			return $join;
		}

		/**
		 * Add conditions to order view query
		 *
		 * @param $where string Original where query section
		 *
		 * @return string filtered where query section
		 * @since 1.0.0
		 */
		public function filter_order_where_for_view( $where ) {
			global $wpdb;

			$where .= $wpdb->prepare( " AND im.meta_key = %s AND im.meta_value = %d", array(
				'_deposit_needs_manual_refund',
				1
			) );

			return $where;
		}

		/**
		 * Output the suborder metaboxes
		 * Child class version adds "Resend button" to parent order metabox
		 *
		 * @param $post     \WP_Post The post object
		 * @param $param    mixed Callback args
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function render_metabox_output( $post, $param ) {
			// print base template
			parent::render_metabox_output( $post, $param );

			$order    = wc_get_order( $post );
			$order_id = yit_get_prop( $order, 'id' );

			$deposit_expire    = get_option( 'yith_wcdp_deposit_expiration_enable', 'no' );
			$notification_days = get_option( 'yith_wcdp_notify_customer_deposit_expiring_days_limit', 15 );

			$send_available = false;

			if ( $order ) {

				// check if current order has a deposit
				if ( ! yit_get_prop( $order, '_has_deposit' ) ) {
					return;
				}

				// retrieve current order suborders
				$suborders = YITH_WCDP_Suborders_Premium()->get_suborder( $order_id );

				// check if order have suborders
				if ( ! $suborders ) {
					return;
				}

				// enable "re-send notify email" only if at least one suborder is not expired, and not completed or cancelled
				foreach ( $suborders as $suborder_id ) {
					$suborder = wc_get_order( $suborder_id );

					if ( ! yit_get_prop( $suborder, '_has_expired' ) && ! in_array( $suborder->get_status(), array(
							'completed',
							'processing',
							'cancelled'
						) ) ) {
						$send_available = true;
					}
				}
			}

			switch ( $param['args']['metabox'] ) {
				case 'suborders':
					if ( apply_filters( 'yith_wcdp_change_deposit_expiration_enable', false ) || $deposit_expire == 'yes' && $notification_days && $send_available ) {
						$resend_url = esc_url( add_query_arg( array(
							'action'   => 'yith_wcdp_send_notification_email',
							'order_id' => $order_id
						), wp_nonce_url( admin_url( 'admin.php' ), 'resend_notification_email', 'resend_notification_email_nonce' ) ) );
						echo sprintf( '<a class="button" href="%s">%s</a>', $resend_url, __( 'Send notification email', 'yith-woocommerce-deposits-and-down-payments' ) );
					}
					break;
			}
		}

		/**
		 * Add extra bulk action options to mark orders as complete or processing
		 * Using Javascript until WordPress core fixes: http://core.trac.wordpress.org/ticket/16031
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function bulk_admin_footer() {
			global $post_type;

			if ( 'shop_order' == $post_type ) {
				?>
				<script type="text/javascript">
					jQuery(function () {
						jQuery('<option>').val('remind_deposit_expiring').text('<?php _e( 'Remind deposit expiring', 'yith-woocommerce-deposits-and-down-payments' )?>').appendTo('select[name="action"]');
						jQuery('<option>').val('remind_deposit_expiring').text('<?php _e( 'Remind deposit expiring', 'yith-woocommerce-deposits-and-down-payments' )?>').appendTo('select[name="action2"]');
					});
				</script>
				<?php
			}
		}

		/**
		 * Process the new bulk actions for changing order status
		 *
		 * @return void
		 * @todo  review code when WC switches to custom tables
		 *
		 * @since 1.0.0
		 */
		public function bulk_action() {
			$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
			$action        = $wp_list_table->current_action();

			// Bail out if this is not a status-changing action
			if ( $action != 'remind_deposit_expiring' ) {
				return;
			}

			$changed  = 0;
			$post_ids = array_map( 'absint', (array) $_REQUEST['post'] );

			foreach ( $post_ids as $post_id ) {
				do_action( 'yith_wcdp_deposits_expiring', $post_id, false, true );
				$changed ++;
			}

			$sendback = add_query_arg( array(
				'post_type' => 'shop_order',
				'changed'   => $changed,
				'ids'       => join( ',', $post_ids )
			), '' );

			if ( isset( $_GET['post_status'] ) ) {
				$sendback = add_query_arg( 'post_status', sanitize_text_field( $_GET['post_status'] ), $sendback );
			}

			wp_redirect( esc_url_raw( $sendback ) );
			exit();
		}

		/**
		 * Re-send notification email and to edit order page
		 *
		 * @return void
		 * @todo  review code when WC switches to custom tables
		 *
		 * @since 1.0.0
		 */
		public function resend_notification_email() {
			if ( isset( $_GET['order_id'] ) && isset( $_GET['resend_notification_email_nonce'] ) && wp_verify_nonce( $_GET['resend_notification_email_nonce'], 'resend_notification_email' ) ) {
				$order_id = intval( $_GET['order_id'] );
				do_action( 'yith_wcdp_deposits_expiring', $order_id, false, true );

				$return_url = add_query_arg( 'notification_email_sent', true, str_replace( '&amp;', '&', get_edit_post_link( $order_id ) ) );
				wp_redirect( esc_url_raw( $return_url ) );
				die();
			}
		}

		/**
		 * Re-send new deposit email for customer and to edit order page
		 *
		 * @return void
		 * @todo  review code when WC switches to custom tables
		 *
		 * @since 1.0.0
		 */
		public function resend_new_deposit_email( $order ) {

			$order_id     = $order->get_id();
			$notify_admin = get_option( 'yith_wcdp_notify_customer_deposit_created' );

			//is_enabled always enable to send it manually.
			if ( 'yes' != $notify_admin ) {
				add_filter( 'yith_wcdp_customer_deposit_created_email_enabled', '__return_true' );
			}

			do_action( 'yith_wcdp_deposits_created', $order_id );


		}

		/**
		 * Print "Notification Email Sent" notice
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_resend_notification_email_notice() {
			global $post, $pagenow;

			if ( get_post_type( $post ) == 'shop_order' && $pagenow == 'post.php' && isset( $_GET['notification_email_sent'] ) && $_GET['notification_email_sent'] ) {
				echo '<div class="updated notice notice-success is-dismissible below-h2">';
				echo '<p>' . __( 'Notification email sent', 'yith-woocommerce-deposits-and-down-payments' ) . '</p>';
				echo '</div>';
			}
		}

		/**
		 * Print item refund notice
		 *
		 * @param $item_id int Current item id
		 * @param $item    mixed Current item
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_item_to_refund_notice( $item_id, $item ) {
			global $post;

			if ( ! $post || empty( $item['full_payment_id'] ) ) {
				return;
			}

			$order    = wc_get_order( $post->ID );
			$order_id = yit_get_prop( $order, 'id' );

			$schedule        = get_option( 'yith_wcdp_deposit_expiration_enable', 'no' );
			$expiration_type = get_option( 'yith_wcdp_deposits_expiration_type', 'num_of_days' );

			if ( $expiration_type == 'num_of_days' ) {
				$expiration_days = get_option( 'yith_wcdp_deposits_expiration_duration', 30 );
			} else {
				$suborder = $item['full_payment_id'] ? wc_get_order( $item['full_payment_id'] ) : false;

				$suborder_expires    = $suborder->get_meta( '_will_suborder_expire', true );
				$suborder_expiration = $suborder->get_meta( '_suborder_expiration', true );

				if ( $suborder_expires == 'yes' && $suborder_expiration ) {
					$expiration_date = $suborder_expiration;
				}
			}

			$message = $expiration_type == 'num_of_days' ?
				sprintf( __( 'This item should be manually refunded by admin, since the %d days available to complete payment have passed and deposit has expired', 'yith-woocommerce-deposits-and-down-payments' ), $expiration_days ) :
				sprintf( __( 'This item should be manually refunded by admin, since deposit has expired on %s', 'yith-woocommerce-deposits-and-down-payments' ), ! empty( $expiration_date ) ? $expiration_date : __( 'N/A', 'yith-woocommerce-deposits-and-down-payments' ) );

			if ( isset( $item['deposit_needs_manual_refund'] ) && $item['deposit_needs_manual_refund'] && $schedule == 'yes' ) {
				$create_refund_for_item_url = esc_url( add_query_arg( array(
					'action'   => 'yith_wcdp_refund_item',
					'order_id' => $order_id,
					'item_id'  => $item_id
				), admin_url( 'admin.php' ) ) );
				$hide_notice_for_item_url   = esc_url( add_query_arg( array(
					'action'   => 'yith_wcdp_delete_refund_notice',
					'order_id' => $order_id,
					'item_id'  => $item_id
				), admin_url( 'admin.php' ) ) );

				?>
				<div class="yith-wcdp-to-refund-notice error-notice">
					<p>
						<small>
							<?php echo $message ?>
							<a href="<?php echo $create_refund_for_item_url ?>"><?php _e( 'Create refund', 'yith-woocommerce-deposits-and-down-payments' ) ?></a>
							|
							<a href="<?php echo $hide_notice_for_item_url ?>"><?php _e( 'Hide this notice', 'yith-woocommerce-deposits-and-down-payments' ) ?></a>
						</small>
					</p>
				</div>
				<?php
			} elseif ( isset( $item['deposit_refunded_after_expiration'] ) && $item['deposit_refunded_after_expiration'] ) {
				$refund_id = $item['deposit_refunded_after_expiration'];
				?>
				<div class="yith-wcdp-to-refund-notice info-notice">
					<p>
						<small>
							<?php echo sprintf( __( 'This item has been refunded, due to deposit expiration (refund #%d)', 'yith-woocommerce-deposits-and-down-payments' ), $refund_id ) ?>
						</small>
					</p>
				</div>
				<?php
			}
		}

		/**
		 * Add a view o default order status view, to filter orders that needs manual refund
		 *
		 * @param $views mixed Current order views
		 *
		 * @return mixed Filtered array of views
		 * @todo  review code when WC switches to custom tables
		 *
		 * @since 1.0.0
		 */
		public function add_to_refund_deposit_view( $views ) {
			$order_to_refund_count = YITH_WCDP_Suborders_Premium()->count_deposit_to_refund();

			if ( $order_to_refund_count ) {
				$filter_url   = esc_url( add_query_arg( array(
					'post_type'         => 'shop_order',
					'deposit_to_refund' => true
				), admin_url( 'edit.php' ) ) );
				$filter_class = isset( $_GET['deposit_to_refund'] ) ? 'current' : '';

				$views['deposit_to_refund'] = sprintf( '<a href="%s" class="%s">%s <span class="count">(%d)</span></a>', $filter_url, $filter_class, __( 'Deposit to Refund', 'yith-woocommerce-deposits-and-down-payments' ), $order_to_refund_count );
			}

			return $views;
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
			$hidden_items = parent::hide_order_item_meta( $hidden_items );

			if ( ! ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) {
				$hidden_items = array_merge(
					$hidden_items,
					array(
						'_deposit_refunded_after_expiration',
						'_deposit_needs_manual_refund'
					)
				);
			}

			return $hidden_items;
		}

		/* === BULK PRODUCT EDITING === */

		/**
		 * Print Quick / Bulk editing fields
		 *
		 * @param $column_name string Current column Name
		 * @param $post_type   string Current post type
		 *
		 * @return void
		 * @since 1.0.2
		 */
		public function print_bulk_editing_fields( $column_name, $post_type ) {
			global $post;

			if ( $post_type != 'product' || $column_name != 'product_tag' ) {
				return;
			}

			$product = wc_get_product( $post->ID );

			// define variables to use in template
			$enable_deposit        = 'default';
			$deposit_default       = 'default';
			$force_deposit         = 'default';
			$create_balance_orders = 'default';
			$product_note          = '';

			if ( $post ) {
				$enable_deposit = yit_get_prop( $product, '_enable_deposit', true );
				$enable_deposit = ! empty( $enable_deposit ) ? $enable_deposit : 'default';

				$deposit_default = yit_get_prop( $product, '_deposit_default', true );
				$deposit_default = ! empty( $enable_deposit ) ? $deposit_default : 'default';

				$force_deposit = yit_get_prop( $product, '_force_deposit', true );
				$force_deposit = ! empty( $force_deposit ) ? $force_deposit : 'default';

				$create_balance_orders = yit_get_prop( $product, '_create_balance_orders', true );
				$create_balance_orders = ! empty( $create_balance_orders ) ? $create_balance_orders : 'default';

				$product_note = yit_get_prop( $product, '_product_note', true );
			}

			include( YITH_WCDP_DIR . 'templates/admin/product-deposit-bulk-edit-premium.php' );
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

			$post_ids              = ( ! empty( $_REQUEST['post'] ) ) ? (array) $_REQUEST['post'] : array();
			$enable_deposit        = isset( $_REQUEST['_enable_deposit'] ) ? trim( $_REQUEST['_enable_deposit'] ) : 'default';
			$deposit_default       = isset( $_REQUEST['_deposit_default'] ) ? trim( $_REQUEST['_deposit_default'] ) : 'default';
			$force_deposit         = isset( $_REQUEST['_force_deposit'] ) ? trim( $_REQUEST['_force_deposit'] ) : 'default';
			$create_balance_orders = isset( $_REQUEST['_create_balance_orders'] ) ? trim( $_REQUEST['_create_balance_orders'] ) : 'default';
			$product_note          = isset( $_REQUEST['_product_note'] ) ? sanitize_text_field( trim( $_REQUEST['_product_note'] ) ) : '';

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
						'_enable_deposit'        => $enable_deposit,
						'_deposit_default'       => $deposit_default,
						'_force_deposit'         => $force_deposit,
						'_create_balance_orders' => $create_balance_orders,
						'_product_note'          => $product_note
					) );
				}
			}
		}

		/* === PRODUCT TABS METHODS === */

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

			$deposit_default = yit_get_prop( $product, '_deposit_default', true );
			$deposit_default = ! empty( $deposit_default ) ? $deposit_default : 'default';

			$force_deposit = yit_get_prop( $product, '_force_deposit', true );
			$force_deposit = ! empty( $force_deposit ) ? $force_deposit : 'default';

			$create_balance_orders = yit_get_prop( $product, '_create_balance_orders', true );
			$create_balance_orders = ! empty( $create_balance_orders ) ? $create_balance_orders : 'default';

			$product_note = yit_get_prop( $product, '_product_note', true );

			$expiration_type                  = get_option( 'yith_wcdp_deposits_expiration_type' );
			$deposit_expires_on_specific_date = 'specific_date' == $expiration_type;

			$deposit_expiration_product_fallback = yit_get_prop( $product, '_deposit_expiration_product_fallback', true );
			$deposit_expiration_product_fallback = ! empty( $deposit_expiration_product_fallback ) ? $deposit_expiration_product_fallback : 'default';

			$deposit_expiration_date = yit_get_prop( $product, '_deposit_expiration_date', true );
			$deposit_expiration_date = ! empty( $deposit_expiration_date ) ? $deposit_expiration_date : '';

			include( YITH_WCDP_DIR . 'templates/admin/product-deposit-tab-premium.php' );
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
			parent::save_product_deposit_tabs( $post_id );

			$product = wc_get_product( $post_id );

			$create_balance_orders               = ( isset( $_POST['_create_balance_orders'] ) && in_array( $_POST['_create_balance_orders'], array(
					'yes',
					'no'
				) ) ) ? $_POST['_create_balance_orders'] : 'default';
			$deposit_default                     = ( isset( $_POST['_deposit_default'] ) && in_array( $_POST['_deposit_default'], array(
					'yes',
					'no'
				) ) ) ? $_POST['_deposit_default'] : 'default';
			$product_note                        = isset( $_POST['_product_note'] ) ? sanitize_text_field( trim( $_POST['_product_note'] ) ) : '';
			$expiration_date                     = isset( $_POST['_deposit_expiration_date'] ) ? sanitize_text_field( trim( $_POST['_deposit_expiration_date'] ) ) : '';
			$deposit_expiration_product_fallback = ( isset( $_POST['_deposit_expiration_product_fallback'] ) && in_array( $_POST['_deposit_expiration_product_fallback'], array(
					'do_nothing',
					'disable_deposit',
					'item_not_purchasable',
					'hide_item'
				) ) ) ? $_POST['_deposit_expiration_product_fallback'] : 'default';

			yit_save_prop( $product, array(
				'_create_balance_orders'               => $create_balance_orders,
				'_deposit_default'                     => $deposit_default,
				'_product_note'                        => $product_note,
				'_deposit_expiration_date'             => $expiration_date,
				'_deposit_expiration_product_fallback' => $deposit_expiration_product_fallback
			) );

		}

		/* === ADDITIONAL VARIATION OPTIONS */

		/**
		 * Print additional fields on variation tab
		 *
		 * @return void
		 * @since 1.0.4
		 */
		public function print_variation_deposit_settings( $loop, $variation_data, $variation ) {

			if (
				apply_filters( 'yith_wcdp_disable_deposit_variation_option', false, $variation ) ||
				! apply_filters( 'yith_wcdp_generate_add_deposit_to_cart_variations_field', true, $variation )
			) {
				return;
			}

			/**
			 * @var $variation \WC_Product
			 */
			$variation = wc_get_product( $variation );

			// define variables to use in template
			$enable_deposit = yit_get_prop( $variation, '_enable_deposit', true );
			$enable_deposit = ! empty( $enable_deposit ) ? $enable_deposit : 'default';

			$deposit_default = yit_get_prop( $variation, '_deposit_default', true );
			$deposit_default = ! empty( $deposit_default ) ? $deposit_default : 'default';

			$force_deposit = yit_get_prop( $variation, '_force_deposit', true );
			$force_deposit = ! empty( $force_deposit ) ? $force_deposit : 'default';

			$create_balance_orders = yit_get_prop( $variation, '_create_balance_orders', true );
			$create_balance_orders = ! empty( $create_balance_orders ) ? $create_balance_orders : 'default';

			$product_note = yit_get_prop( $variation, '_product_note', true );

			$expiration_type                  = get_option( 'yith_wcdp_deposits_expiration_type' );
			$deposit_expires_on_specific_date = 'specific_date' == $expiration_type;

			$deposit_expiration_product_fallback = yit_get_prop( $variation, '_deposit_expiration_product_fallback', true );
			$deposit_expiration_product_fallback = ! empty( $deposit_expiration_product_fallback ) ? $deposit_expiration_product_fallback : 'default';

			$deposit_expiration_date = yit_get_prop( $variation, '_deposit_expiration_date', true );
			$deposit_expiration_date = ! empty( $deposit_expiration_date ) ? $deposit_expiration_date : '';

			include( YITH_WCDP_DIR . 'templates/admin/product-deposit-variation.php' );
		}

		/**
		 * Save additional fields on variation tab
		 *
		 * @return void
		 * @since 1.0.4
		 */
		public function save_variation_deposits_settings( $variation_id, $loop ) {

			if (
				apply_filters( 'yith_wcdp_disable_deposit_variation_option', false, $variation_id ) ||
				! apply_filters( 'yith_wcdp_generate_add_deposit_to_cart_variations_field', true, $variation_id )
			) {
				return;
			}

			$variation = wc_get_product( $variation_id );

			$enable_deposit                      = isset( $_POST['_enable_deposit'][ $loop ] ) ? trim( $_POST['_enable_deposit'][ $loop ] ) : 'default';
			$force_deposit                       = isset( $_POST['_force_deposit'][ $loop ] ) ? trim( $_POST['_force_deposit'][ $loop ] ) : 'default';
			$create_balance_orders               = ( isset( $_POST['_create_balance_orders'][ $loop ] ) && in_array( $_POST['_create_balance_orders'][ $loop ], array(
					'yes',
					'no'
				) ) ) ? $_POST['_create_balance_orders'][ $loop ] : 'default';
			$deposit_default                     = ( isset( $_POST['_deposit_default'][ $loop ] ) && in_array( $_POST['_deposit_default'][ $loop ], array(
					'yes',
					'no'
				) ) ) ? $_POST['_deposit_default'][ $loop ] : 'default';
			$product_note                        = isset( $_POST['_product_note'][ $loop ] ) ? sanitize_text_field( trim( $_POST['_product_note'][ $loop ] ) ) : '';
			$expiration_date                     = isset( $_POST['_deposit_expiration_date'][ $loop ] ) ? sanitize_text_field( trim( $_POST['_deposit_expiration_date'][ $loop ] ) ) : '';
			$deposit_expiration_product_fallback = ( isset( $_POST['_deposit_expiration_product_fallback'][ $loop ] ) && in_array( $_POST['_deposit_expiration_product_fallback'][ $loop ], array(
					'do_nothing',
					'disable_deposit',
					'item_not_purchasable',
					'hide_item'
				) ) ) ? $_POST['_deposit_expiration_product_fallback'][ $loop ] : 'default';

			yit_save_prop( $variation, array(
				'_enable_deposit'                      => $enable_deposit,
				'_force_deposit'                       => $force_deposit,
				'_create_balance_orders'               => $create_balance_orders,
				'_deposit_default'                     => $deposit_default,
				'_product_note'                        => $product_note,
				'_deposit_expiration_date'             => $expiration_date,
				'_deposit_expiration_product_fallback' => $deposit_expiration_product_fallback
			) );
		}

		/* === PREMIUM OPTIONS === */

		/**
		 * Add tabs for premium options
		 *
		 * @param $tabs mixed Array of currently available tabs
		 *
		 * @return mixed Filtered array of tabs
		 * @since 1.0.0
		 */
		public function add_premium_options_tab( $tabs ) {
			$tabs['deposits'] = __( 'Deposits', 'yith-woocommerce-deposits-and-down-payments' );

			unset( $tabs['premium'] );

			return $tabs;
		}

		/**
		 * Adds premium settings to "General" tab in admin panel
		 *
		 * @param $settings mixed Original array of settings
		 *
		 * @return mixed Filtered array of settings
		 * @since 1.0.0
		 */
		public function add_premium_general_settings( $settings ) {
			$settings_array = $settings['settings'];

			$deposit_default_option = array(
				'general-deposit-default' => array(
					'title'   => __( 'Deposit checked', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'    => 'checkbox',
					'desc'    => __( 'Whether deposit option should be selected by default or not', 'yith-woocommerce-deposits-and-down-payments' ),
					'id'      => 'yith_wcdp_general_deposit_default',
					'default' => 'yes',
				),
			);

			/* @since 1.2.0 */
			$deposit_general_option = array(
				'general-enable-ajax-variation-handling' => array(
					'title'   => __( 'Enable AJAX variation', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'    => 'checkbox',
					'desc'    => __( 'Enable this option if you want to load deposits options via AJAX. This should reduce loading time for single product page', 'yith-woocommerce-deposits-and-down-payments' ),
					'id'      => 'yith_wcdp_general_enable_ajax_variation',
					'default' => 'no'
				)
			);

			$deposit_type_option = array(
				'general-deposit-type' => array(
					'title'    => __( 'Deposit type', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'     => 'select',
					'desc'     => __( 'Select the type of deposit you want to apply to selected products', 'yith-woocommerce-deposits-and-down-payments' ),
					'id'       => 'yith_wcdp_general_deposit_type',
					'options'  => array(
						'amount' => __( 'Fixed amount', 'yith-woocommerce-deposits-and-down-payments' ),
						'rate'   => __( 'Percent value of product price', 'yith-woocommerce-deposits-and-down-payments' ),
					),
					'default'  => 'amount',
					'desc_tip' => true
				)
			);

			$deposit_rate_option = array(
				'general-deposit-rate' => array(
					'title'             => __( 'Deposit Rate', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'              => 'number',
					'desc'              => __( 'Percentage of product total price required as deposit', 'yith-woocommerce-deposits-and-down-payments' ),
					'id'                => 'yith_wcdp_general_deposit_rate',
					'css'               => 'min-width: 100px;',
					'default'           => 10,
					'custom_attributes' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 'any'
					),
					'desc_tip'          => true
				),
			);

			/**
			 * @since 1.2.1
			 */
			$balance_options = array(
				'balance-options' => array(
					'title' => __( 'Balance', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'yith_wcdp_balance_options'
				),

				'balance-type' => array(
					'title'   => __( 'Balance type', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'    => 'select',
					'options' => array(
						'none'     => __( 'Do not create any balance order', 'yith-woocommerce-deposits-and-down-payments' ),
						'single'   => __( 'Create a single balance order for all items purchased with deposit', 'yith-woocommerce-deposits-and-down-payments' ),
						'multiple' => __( 'Create one balance order for each item purchased with depoist', 'yith-woocommerce-deposits-and-down-payments' ),
					),
					'id'      => 'yith_wcdp_balance_type',
					'default' => 'multiple'
				),

				'general-create-balance-orders' => array(
					'title'   => __( 'Let users pay balance orders online', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'    => 'checkbox',
					'desc'    => __( 'Check this option to create balance orders with "Pending Payment" status, so that users can complete purchases online; otherwise "On Hold" status will be applied (this behaviour can be overridden on product level)', 'yith-woocommerce-deposits-and-down-payments' ),
					'id'      => 'yith_wcdp_general_create_balance_orders',
					'default' => 'yes'
				),

				'balance-options-end' => array(
					'type' => 'sectionend',
					'id'   => 'yith_wcdp_balance_options'
				),
			);

			$deposit_labels_options = array(
				'deposit-labels-options' => array(
					'title' => __( 'Labels & Messages', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'yith_wcdp_deposit_labels_options'
				),

				'deposit-labels-deposit' => array(
					'title'   => __( 'Deposit', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'    => 'text',
					'css'     => 'min-width: 300px;',
					'id'      => 'yith_wcdp_deposit_labels_deposit',
					'default' => __( 'Deposit', 'yith-wcdp' )
				),

				'deposit-labels-pay-deposit' => array(
					'title'   => __( 'Pay deposit', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'    => 'text',
					'css'     => 'min-width: 300px;',
					'id'      => 'yith_wcdp_deposit_labels_pay_deposit',
					'default' => __( 'Pay Deposit', 'yith-woocommerce-deposits-and-down-payments' )
				),

				'deposit-labels-pay-full-amount' => array(
					'title'   => __( 'Pay full amount', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'    => 'text',
					'css'     => 'min-width: 300px;',
					'id'      => 'yith_wcdp_deposit_labels_pay_full_amount',
					'default' => __( 'Pay Full Amount', 'yith-woocommerce-deposits-and-down-payments' )
				),

				'deposit-labels-partially-paid-status' => array(
					'title'   => __( 'Partially Paid', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'    => 'text',
					'css'     => 'min-width: 300px;',
					'id'      => 'yith_wcdp_deposit_labels_partially_paid_status',
					'default' => __( 'Partially Paid', 'yith-woocommerce-deposits-and-down-payments' )
				),

				'deposit-labels-full-price-label' => array(
					'title'   => __( 'Full price label', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'    => 'text',
					'css'     => 'min-width: 300px;',
					'id'      => 'yith_wcdp_deposit_labels_full_price_label',
					'default' => __( 'Full price', 'yith-woocommerce-deposits-and-down-payments' )
				),

				'deposit-labels-balance-label' => array(
					'title'   => __( 'Balance label', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'    => 'text',
					'css'     => 'min-width: 300px;',
					'id'      => 'yith_wcdp_deposit_labels_balance_label',
					'default' => __( 'Balance', 'yith-woocommerce-deposits-and-down-payments' )
				),

				'deposit-labels-pay-in-loco' => array(
					'title'   => __( 'Pay on location', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'    => 'textarea',
					'css'     => 'width: 100%; min-height: 150px;',
					'id'      => 'yith_wcdp_deposit_labels_pay_in_loco',
					'default' => __( 'You can complete this order on location', 'yith-woocommerce-deposits-and-down-payments' )
				),

				'deposit-labels-product-note-position' => array(
					'title'   => __( 'Position of product note', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'    => 'select',
					'options' => array(
						'none'                                    => __( 'Do not show any note on product', 'yith-woocommerce-deposits-and-down-payments' ),
						'woocommerce_template_single_title'       => __( 'Below product title', 'yith-woocommerce-deposits-and-down-payments' ),
						'woocommerce_template_single_price'       => __( 'Below product price', 'yith-woocommerce-deposits-and-down-payments' ),
						'woocommerce_template_single_excerpt'     => __( 'Below product excerpt', 'yith-woocommerce-deposits-and-down-payments' ),
						'woocommerce_template_single_add_to_cart' => __( 'Below single Add to Cart', 'yith-woocommerce-deposits-and-down-payments' ),
						'woocommerce_product_meta_end'            => __( 'Below product meta', 'yith-woocommerce-deposits-and-down-payments' ),
						'woocommerce_template_single_sharing'     => __( 'Below product share', 'yith-woocommerce-deposits-and-down-payments' )
					),
					'id'      => 'yith_wcdp_deposit_labels_product_note_position',
					'default' => ''
				),

				'deposit-labels-product-note' => array(
					'title'    => __( 'Product note', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'     => 'textarea',
					'css'      => 'width: 100%; min-height: 150px;',
					'id'       => 'yith_wcdp_deposit_labels_product_note',
					'desc'     => __( 'You can override this option from single product edit page', 'yith-woocommerce-deposits-and-down-payments' ),
					'desc_tip' => true,
					'default'  => ''
				),

				'deposit-labels-options-end' => array(
					'type' => 'sectionend',
					'id'   => 'yith_wcdp_deposit_labels_options'
				),
			);

			$deposit_expiration_options = array(
				'deposit-expiration-options' => array(
					'title' => __( 'Deposit expiration', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'yith_wcdp_deposit_expiration_options'
				),

				'deposit-expiration-enable' => array(
					'title'   => __( 'Enable deposit expiration', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'    => 'checkbox',
					'desc'    => __( 'Check this option, if you want to set a number of days, after which order with deposits cannot be completed anymore', 'yith-woocommerce-deposits-and-down-payments' ),
					'id'      => 'yith_wcdp_deposit_expiration_enable',
					'default' => 'no'
				),

				'deposit-expiration-type' => array(
					'title'    => __( 'Deposit expires', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'     => 'select',
					'desc'     => __( 'Choose how plugin should calculate when a deposit is expired', 'yith-woocommerce-deposits-and-down-payments' ),
					'id'       => 'yith_wcdp_deposits_expiration_type',
					'css'      => 'min-width: 100px;',
					'default'  => 'num_of_days',
					'options'  => array(
						'num_of_days'   => __( 'After some days from its creation', 'yith-woocommerce-deposits-and-down-payments' ),
						'specific_date' => __( 'On a specific date', 'yith-woocommerce-deposits-and-down-payments' )
					),
					'desc_tip' => true
				),

				'deposit-expiration-duration' => array(
					'title'             => __( 'Days before expiration', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'              => 'number',
					'desc'              => __( 'Number of days after which order with deposit cannot be completed anymore', 'yith-woocommerce-deposits-and-down-payments' ),
					'id'                => 'yith_wcdp_deposits_expiration_duration',
					'css'               => 'min-width: 100px;',
					'default'           => 30,
					'custom_attributes' => array(
						'min'  => 1,
						'max'  => 9999999,
						'step' => 1
					),
					'desc_tip'          => true
				),

				'deposit-expiration-date' => array(
					'title'    => __( 'Expiration date', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'     => 'text',
					'desc'     => __( 'Expiration date for depist (you can override this setting in product page)', 'yith-woocommerce-deposits-and-down-payments' ),
					'id'       => 'yith_wcdp_deposits_expiration_date',
					'css'      => 'min-width: 100px;',
					'default'  => '',
					'class'    => 'date-picker',
					'desc_tip' => true
				),

				'deposit-expiration-product-fallback' => array(
					'title'    => __( 'Product status', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'     => 'select',
					'desc'     => __( 'Choose what changes in product when deposit expires', 'yith-woocommerce-deposits-and-down-payments' ),
					'id'       => 'yith_wcdp_deposits_expiration_product_fallback',
					'css'      => 'min-width: 100px;',
					'default'  => 'disable_deposit',
					'options'  => array(
						'do_nothing'           => __( 'Do nothing', 'yith-woocommerce-deposits-and-down-payments' ),
						'disable_deposit'      => __( 'Just disable deposit', 'yith-woocommerce-deposits-and-down-payments' ),
						'item_not_purchasable' => __( 'Make item no longer purchasable', 'yith-woocommerce-deposits-and-down-payments' ),
						'hide_item'            => __( 'Hide item from catalog visibility', 'yith-woocommerce-deposits-and-down-payments' )
					),
					'desc_tip' => true
				),

				'deposit-expiration-fallback' => array(
					'title'    => __( 'Expiration fallback', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'     => 'select',
					'desc'     => __( 'Select an action to carry out when a deposit expires', 'yith-woocommerce-deposits-and-down-payments' ),
					'id'       => 'yith_wcdp_deposit_expiration_fallback',
					'options'  => array(
						'none'   => __( 'Do nothing', 'yith-woocommerce-deposits-and-down-payments' ),
						'refund' => __( 'Refund deposit for the product', 'yith-woocommerce-deposits-and-down-payments' ),
					),
					'default'  => 'none',
					'desc_tip' => true
				),

				'deposit-expiration-options-end' => array(
					'type' => 'sectionend',
					'id'   => 'yith_wcdp_deposit_expiration_options'
				),
			);

			$notify_options = array(
				'notify-options' => array(
					'title' => __( 'Notify options', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'yith_wcdp_notify_options'
				),

				'notify-customer-deposit-created' => array(
					'title'   => __( 'Customer deposit created', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'    => 'checkbox',
					'desc'    => __( 'Send an email to customer when an order with deposit is created', 'yith-woocommerce-deposits-and-down-payments' ),
					'id'      => 'yith_wcdp_notify_customer_deposit_created',
					'default' => 'yes'
				),

				'notify-admin-deposit-created' => array(
					'title'   => __( 'Admin - for deposit created', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'    => 'checkbox',
					'desc'    => __( 'Send an email to admin(s) when an order with deposit is created', 'yith-woocommerce-deposits-and-down-payments' ),
					'id'      => 'yith_wcdp_notify_admin_deposit_created',
					'default' => 'yes'
				),

				'notify-customer-deposit-expiring' => array(
					'title'   => __( 'Customer - deposit expiring', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'    => 'checkbox',
					'desc'    => __( 'Send an email to customer when an order is expiring', 'yith-woocommerce-deposits-and-down-payments' ),
					'id'      => 'yith_wcdp_notify_customer_deposit_expiring',
					'default' => 'yes'
				),

				'notify-customer-deposit-expiring-days-limit' => array(
					'title'             => __( 'Days before notification', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'              => 'number',
					'desc'              => __( 'Set here the number of days before expiration of a deposit, when a notifying email will be sent to customers', 'yith-woocommerce-deposits-and-down-payments' ),
					'id'                => 'yith_wcdp_notify_customer_deposit_expiring_days_limit',
					'css'               => 'min-width: 100px;',
					'default'           => 15,
					'custom_attributes' => array(
						'min'  => 1,
						'max'  => 9999999,
						'step' => 1
					),
					'desc_tip'          => true
				),

				'notify-options-end' => array(
					'type' => 'sectionend',
					'id'   => 'yith_wcdp_notify_options'
				),
			);

			$array_chunk_1 = array_slice( $settings_array, 0, 3 );
			$array_chunk_2 = array_splice( $settings_array, 3, count( $settings_array ) - 1 );

			$settings_array = array_merge(
				$array_chunk_1,
				$deposit_default_option,
				$array_chunk_2
			);

			$array_chunk_1 = array_slice( $settings_array, 0, 5 );
			$array_chunk_2 = array_splice( $settings_array, 5, count( $settings_array ) - 1 );

			$settings_array = array_merge(
				$array_chunk_1,
				$deposit_general_option,
				$array_chunk_2
			);

			$array_chunk_1 = array_slice( $settings_array, 0, 8 );
			$array_chunk_2 = array_splice( $settings_array, 8, count( $settings_array ) - 1 );

			$settings_array = array_merge(
				$array_chunk_1,
				$deposit_type_option,
				$array_chunk_2
			);

			$array_chunk_1 = array_slice( $settings_array, 0, 10 );
			$array_chunk_2 = array_splice( $settings_array, 10, count( $settings_array ) - 1 );

			$settings_array = array_merge(
				$array_chunk_1,
				$deposit_rate_option,
				$array_chunk_2
			);

			$array_chunk_1 = $settings_array;
			$array_chunk_2 = array();

			$settings_array = array_merge(
				$array_chunk_1,
				$balance_options,
				$deposit_labels_options,
				$deposit_expiration_options,
				$notify_options,
				$array_chunk_2
			);

			$settings['settings'] = $settings_array;

			return $settings;
		}

		/* === LICENCE HANDLING METHODS === */

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_WCDP_DIR . 'plugin-fw/licence/lib/yit-licence.php';
				require_once YITH_WCDP_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}

			YIT_Plugin_Licence()->register( YITH_WCDP_INIT, YITH_WCDP_SECRET_KEY, YITH_WCDP_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once( YITH_WCDP_DIR . 'plugin-fw/lib/yit-upgrade.php' );
			}

			YIT_Upgrade()->register( YITH_WCDP_SLUG, YITH_WCDP_INIT );
		}

		/* === AJAX REQUEST METHODS === */

		/**
		 * Print json encoded list of user's role matching filter (param $term in request used to filter)
		 * Array is formatted as role_slug => Verbose role description
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function get_roles_via_ajax() {
			global $wp_roles;

			ob_start();

			check_ajax_referer( 'search-products', 'security' );

			if ( ! current_user_can( 'edit_shop_orders' ) ) {
				die( - 1 );
			}

			$term = wc_clean( stripslashes( $_GET['term'] ) );

			if ( empty( $term ) ) {
				die();
			}

			$found_roles = array();
			$roles_names = $wp_roles->get_names();

			if ( ! empty( $roles_names ) ) {
				foreach ( $roles_names as $slug => $name ) {
					$name = translate_user_role( $name );
					if ( strpos( strtolower( $name ), strtolower( $term ) ) !== false ) {
						$found_roles[ $slug ] = $name;
					}
				}
			}

			wp_send_json( $found_roles );
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCDP_Admin_Premium
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
 * Unique access to instance of YITH_WCDP_Admin_Premium class
 *
 * @return \YITH_WCDP_Admin_Premium
 * @since 1.0.0
 */
function YITH_WCDP_Admin_Premium() {
	return YITH_WCDP_Admin_Premium::get_instance();
}
