<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YWSBS_Subscription_List_Table Class.
 *
 * Manage the subscription list table.
 *
 * @class   YWSBS_Subscription_List_Table
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}



if ( ! class_exists( 'YWSBS_Subscription_List_Table' ) ) {

	/**
	 * Class YWSBS_Subscription_List_Table
	 */
	class YWSBS_Subscription_List_Table {


		/**
		 * Single instance of the class
		 *
		 * @var YWSBS_Subscription_List_Table
		 */
		protected static $instance;

		/**
		 * Store the subscription status counter query results.
		 *
		 * @var array
		 */
		protected $subscription_status = array();

		/**
		 * Returns single instance of the class
		 *
		 * @return YWSBS_Subscription_List_Table
		 * @since  1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 *
		 * Initialize actions and filters to be used
		 *
		 * @since 2.0.0
		 */
		public function __construct() {

			add_filter( 'manage_' . YITH_YWSBS_POST_TYPE . '_posts_columns', array( $this, 'manage_list_columns' ) );
			add_action( 'manage_' . YITH_YWSBS_POST_TYPE . '_posts_custom_column', array( $this, 'render_list_columns' ), 10, 2 );
			add_filter( 'manage_edit-' . YITH_YWSBS_POST_TYPE . '_sortable_columns', array( $this, 'sortable_columns' ) );
			add_filter( 'request', array( $this, 'sort_columns' ) );
			add_action( 'init', array( $this, 'set_counter' ), 30 );

			// filters.
			add_action( 'restrict_manage_posts', array( $this, 'render_filters' ), 10, 1 );
			add_action( 'manage_posts_extra_tablenav', array( $this, 'add_export_button' ), 10, 2 );
			add_action( 'pre_get_posts', array( $this, 'filter_subscriptions' ), 10, 1 );

			// filter subscription status links.
			add_filter( 'views_edit-' . YITH_YWSBS_POST_TYPE, array( $this, 'subscription_status_filter' ), 10 );

			// filter row action links.
			add_filter( 'post_row_actions', array( $this, 'manage_row_actions' ), 10, 2 );
			// bulk action.
			add_filter( 'bulk_actions-edit-' . YITH_YWSBS_POST_TYPE, array( $this, 'define_bulk_action' ), 100 );

			// blank page.
			add_action( 'manage_posts_extra_tablenav', array( $this, 'maybe_render_blank_state' ) );

			// export subscriptions.
			add_action( 'admin_action_ywsbs_export_subscription', array( $this, 'export_subscriptions_via_csv' ) );

		}

		/**
		 * Define bulk actions.
		 *
		 * @param array $actions Existing actions.
		 *
		 * @return array
		 */
		public function define_bulk_action( $actions ) {

			$actions = array(
				'trash' => esc_html__( 'Trash', 'yith-woocommerce-subscription' ),
			);

			return $actions;
		}

		/**
		 * Show export button inside the list table.
		 *
		 * @param string $witch Position.
		 * @since  2.1.0
		 */
		public function add_export_button( $witch ) {
			$screen = get_current_screen();
			if ( YITH_YWSBS_POST_TYPE === $screen->post_type && 'top' === $witch ) {
				$export_sbs_url = esc_url( add_query_arg( array( 'action' => 'ywsbs_export_subscription' ), admin_url( 'admin.php' ) ) );

				?>
				<div class="ywsbs-export">
					<a class="button-primary " href="<?php echo esc_url( $export_sbs_url ); ?>"><i
							class="ywsbs-icon-save_alt"></i><?php echo esc_html_x( 'Export', 'label of button to export subscription', 'yith-woocommerce-subscription' ); ?>
					</a>
				</div>
				<?php
			}
		}

		/**
		 * Set the counter of status.
		 */
		public function set_counter() {
			$this->subscription_status = $this->get_subscription_status_counter();
		}


		/**
		 * Manage the columns
		 *
		 * @param array $columns Columns.
		 *
		 * @return array
		 */
		public function manage_list_columns( $columns ) {

			$columns = array(
				'cb'                   => '<input type="checkbox" />',
				'info'                 => esc_html__( 'Subscription', 'yith-woocommerce-subscription' ),
				'start_date'           => esc_html__( 'Started on', 'yith-woocommerce-subscription' ),
				'customer'             => esc_html__( 'Customer', 'yith-woocommerce-subscription' ),
				'recurring_amount'     => esc_html__( 'Recurring', 'yith-woocommerce-subscription' ),
				'payment_due_date'     => esc_html__( 'Payment due', 'yith-woocommerce-subscription' ),
				'end_date'             => esc_html__( 'Ended on', 'yith-woocommerce-subscription' ),
				'expired_date'         => esc_html__( 'Expiry date', 'yith-woocommerce-subscription' ),
				'rates_payed'          => esc_html__( 'Renewals', 'yith-woocommerce-subscription' ),
				'payment_method_title' => esc_html__( 'Payment method', 'yith-woocommerce-subscription' ),
				'failed_attempts'      => esc_html__( 'Failed Attempts', 'yith-woocommerce-subscription' ),
				'status'               => esc_html__( 'Status', 'yith-woocommerce-subscription' ),
			);
			return $columns;
		}

		/**
		 * Render the columns
		 *
		 * @param array $column Column.
		 * @param int   $subscription_id Subscription id.
		 *
		 * @return void
		 */
		public function render_list_columns( $column, $subscription_id ) {

			$subscription = ywsbs_get_subscription( $subscription_id );

			switch ( $column ) {
				case 'info':
					$url = ywsbs_get_view_subscription_url( $subscription_id, true );
					printf( '<a href="%1$s">%2$s - %3$s</a>', esc_url( $url ), esc_html( $subscription->get_number() ), esc_html( $subscription->get( 'product_name' ) ) );
					break;
				case 'start_date':
					$start_date = $subscription->get( 'start_date' );
					echo esc_html( ( $start_date ) ? date_i18n( wc_date_format(), $start_date ) : '-' );
					break;
				case 'recurring_amount':
					$recurring = apply_filters( 'ywsbs_get_recurring_totals', $subscription->get( 'subscription_total' ), $subscription_id );
					echo wp_kses_post( wc_price( $recurring, array( 'currency' => $subscription->get( 'order_currency' ) ) ) );
					break;
				case 'customer':
					$customer = YWSBS_Subscription_User::get_user_info_for_subscription_list( $subscription );
					echo wp_kses_post( $customer );
					break;
				case 'payment_due_date':
					$payment_due_date = $subscription->get( 'payment_due_date' );
					echo esc_html( ( $payment_due_date ) ? date_i18n( wc_date_format(), $payment_due_date ) : '-' );
					break;
				case 'end_date':
					$end_date = $subscription->get( 'end_date' );
					echo esc_html( ( $end_date ) ? date_i18n( wc_date_format(), $end_date ) : '-' );
					break;
				case 'expired_date':
					$expired_date = $subscription->get( 'expired_date' );
					echo esc_html( ( $expired_date ) ? date_i18n( wc_date_format(), $expired_date ) : '-' );
					break;
				case 'rates_payed':
					$paid_rates = $subscription->get_paid_rates();
					echo esc_html( empty( $paid_rates ) ? '-' : $paid_rates );
					break;
				case 'payment_method_title':
					echo esc_html( $subscription->get( 'payment_method_title' ) );
					break;
				case 'failed_attempts':
					$renew_order     = $subscription->get_renew_order();
					$failed_attempts = $renew_order ? $renew_order->get_meta( 'failed_attemps' ) : false;
					$failed_attempts = $failed_attempts ? $failed_attempts : 0;
					$payment_method  = $subscription->get( 'payment_method' );
					$attempts_list   = ywsbs_get_max_failed_attempts_list();

					$failed_attempts .= isset( $attempts_list[ $payment_method ] ) ? '/' . $attempts_list[ $payment_method ] : '';
					echo esc_html( $failed_attempts );
					break;
				case 'status':
					$subscription_status_list = ywsbs_get_status();
					$status                   = $subscription->get_status();
					$subscription_status      = $subscription_status_list[ $status ];
					printf( '<span class="status %1$s">%2$s</span>', esc_attr( $subscription->get_status() ), esc_html( $subscription_status ) );
					break;
				default:
					echo '';
			}
		}

		/**
		 * Render the columns
		 *
		 * @param array $columns Column.
		 *
		 * @return array
		 */
		public function sortable_columns( $columns ) {

			$columns = array(
				'info'                 => 'product_name',
				'start_date'           => 'start_date',
				'recurring_amount'     => 'line_total',
				'payment_due_date'     => 'payment_due_date',
				'end_date'             => 'end_date',
				'expired_date'         => 'expired_date',
				'payment_method_title' => 'payment_method_title',
				'status'               => 'status',
				'rates_payed'          => 'rates_payed',
			);

			return $columns;
		}

		/**
		 * Sort columns
		 *
		 * @param array $vars Array of vars.
		 *
		 * @return array
		 */
		public function sort_columns( $vars ) {

			if ( isset( $vars['post_type'] ) && YITH_YWSBS_POST_TYPE === $vars['post_type'] ) {
				$sortable_columns = $this->sortable_columns( array() );
				switch ( $vars['orderby'] ) {
					case 'product_name':
					case 'payment_method_title':
					case 'status':
						$vars['meta_key'] = $vars['orderby']; //phpcs:ignore
						$vars['orderby']  = 'meta_value';
						break;
					case 'start_date':
					case 'line_total':
					case 'payment_due_date':
					case 'end_date':
					case 'expired_date':
					case 'rates_payed':
						$vars['meta_key'] = $vars['orderby']; //phpcs:ignore
						$vars['orderby']  = 'meta_value_num';
						break;
				}
			}

			return $vars;
		}

		/**
		 * Add filter.
		 *
		 * @param string $post_type Post Type.
		 */
		public function render_filters( $post_type ) {
			if ( YITH_YWSBS_POST_TYPE === $post_type ) {
				$this->render_status_filter();
				$this->render_payment_method_filter();
				$this->render_customer_filter();
			}
		}

		/**
		 * Render customer filter.
		 */
		protected function render_customer_filter() {

			echo wp_kses_post( '<div class="alignleft actions yith-search-customer-wrapper">' );

			// Customers select 2.
			$user_string = '';
			$customer_id = '';

			if ( ! empty( $_REQUEST['customer_user'] ) ) { // phpcs:ignore
				$customer_id = absint( $_REQUEST['customer_user'] ); // phpcs:ignore
				$user        = get_user_by( 'id', $customer_id );
				$user_string = $user ? esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) : '';
			}

			$args = array(
				'type'             => 'hidden',
				'class'            => 'wc-customer-search',
				'id'               => 'customer_user',
				'name'             => 'customer_user',
				'data-placeholder' => esc_html__( 'Show All Customers', 'yith-woocommerce-subscription' ),
				'data-allow_clear' => true,
				'data-selected'    => array( $customer_id => esc_attr( $user_string ) ),
				'data-multiple'    => false,
				'value'            => $customer_id,
				'style'            => 'width:200px',
			);

			yit_add_select2_fields( $args );

			echo '</div>';
		}

		/**
		 * Render payment method filter.
		 */
		protected function render_payment_method_filter() {

			global $wpdb;

			$current_payment = isset( $_REQUEST['payment'] ) && ! empty( $_REQUEST['payment'] ) ? $_REQUEST['payment'] : '';  // phpcs:ignore
			$gateways        = WC()->payment_gateways()->get_available_payment_gateways();
			$query           = $wpdb->prepare(
				"SELECT count(*) as counter, ywsbs_pm.meta_value as payment_method FROM {$wpdb->posts} as ywsbs_p INNER JOIN {$wpdb->postmeta} as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
WHERE ywsbs_p.post_type = %s AND ywsbs_pm.meta_key = 'payment_method' GROUP BY ywsbs_pm.meta_value", //phpcs:ignore
				YITH_YWSBS_POST_TYPE //phpcs:ignore
			);

			$subscription_gateways = $wpdb->get_results( apply_filters( 'ywsbs_payment_method_filter_query', $query ) );  //phpcs:ignore

			?>
			<div class="alignleft actions">
				<select name="payment" id="subscription_payment">
					<option
						value=""><?php esc_html_e( 'All Payment Methods', 'yith-woocommerce-subscription' ); ?></option>
					<?php
					foreach ( $subscription_gateways as $subscription_gateway ) :
						$payment_key = $subscription_gateway->payment_method;
						$counter     = $subscription_gateway->counter;
						$gateway     = isset( $gateways[ $payment_key ] ) ? $gateways[ $payment_key ]->title : $payment_key;
						?>
						<option
							value="<?php echo esc_attr( $payment_key ); ?>" <?php selected( $payment_key, $current_payment, true ); ?> >
							<?php printf( '%s (%d)', esc_html( $gateway ), esc_html( $counter ) ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>

			<?php
		}

		/**
		 * Render status filter.
		 */
		protected function render_status_filter() {

			global $wpdb;
			$label_counter  = ywsbs_get_status_label_counter();
			$current_status = isset( $_REQUEST['status'] ) && ! empty( $_REQUEST['status'] ) ? $_REQUEST['status'] : '';  // phpcs:ignore
			?>
			<div class="alignleft actions">
				<select name="status" id="status">
					<option value=""><?php esc_html_e( 'All status', 'yith-woocommerce-subscription' ); ?></option>
					<?php
					foreach ( $this->subscription_status as $status ) :
						$status_key = $status->status;
						$counter    = $status->counter;
						$status     = $label_counter[ $status_key ];
						?>
						<option
							value="<?php echo esc_attr( $status_key ); ?>" <?php selected( $status_key, $current_status, true ); ?> >
							<?php printf( '%s (%d)', esc_html( $status ), esc_html( $counter ) ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<?php
		}

		/**
		 * Filter subscriptions
		 *
		 * @param WP_Query $query WP_Query.
		 */
		public function filter_subscriptions( $query ) {

			global $wpdb;
			if ( $query->is_main_query() && isset( $query->query['post_type'] ) && YITH_YWSBS_POST_TYPE === $query->query['post_type'] ) {
				$meta_query = ! ! $query->get( 'meta_query' ) ? $query->get( 'meta_query' ) : array();
				$changed    = false;
				$posted     = $_REQUEST; //phpcs:ignore

				if ( ! empty( $posted['status'] ) ) {
					$changed      = true;
					$meta_query[] = array(
						'key'   => 'status',
						'value' => $posted['status'],
					);
				}

				if ( ! empty( $posted['payment'] ) ) {
					$changed        = true;
					$payment_method = wc_clean( $posted['payment'] );

					$meta_query[] = array(
						'key'   => 'payment_method',
						'value' => $payment_method,
					);
				}

				if ( ! empty( $posted['customer_user'] ) ) {
					$changed       = true;
					$customer_user = abs( $posted['customer_user'] );

					$meta_query[] = array(
						'key'   => 'user_id',
						'value' => $customer_user,
					);
				}

				if ( ! empty( $posted['s'] ) && is_numeric( $posted['s'] ) ) {
					$changed = true;
					$search  = abs( $posted['s'] );

					$search_query = $wpdb->prepare(
						"SELECT p.ID FROM $wpdb->posts p
                            LEFT JOIN $wpdb->postmeta pm2 ON ( pm2.post_id = p.ID)
                            WHERE p.post_type = %s
                            AND pm2.meta_key='id' AND pm2.meta_value LIKE %s
                            GROUP BY p.ID",
						YITH_YWSBS_POST_TYPE,
						$search
					);

					$results = $wpdb->get_col( $search_query ); //phpcs:ignore
					$query->set( 'post__in', $results );
					$query->set( 's', '' );
				}

				if ( $changed ) {
					$query->set( 'meta_query', $meta_query );
				}
			}
		}

		/**
		 * Return the subscription status
		 *
		 * @return array|object|null
		 */
		protected function get_subscription_status_counter() {
			global $wpdb;

			$query = $wpdb->prepare(
				"SELECT count(*) as counter, ywsbs_pm.meta_value as status FROM {$wpdb->posts} as ywsbs_p LEFT JOIN {$wpdb->postmeta} as ywsbs_pm ON ( ywsbs_p.ID = ywsbs_pm.post_id )
WHERE ywsbs_p.post_type = %s AND ywsbs_pm.meta_key = 'status' GROUP BY ywsbs_pm.meta_value",
				YITH_YWSBS_POST_TYPE
			);
			$query = apply_filters( 'ywsbs_subscription_status_counter_query', $query );

			return $wpdb->get_results( $query ); //phpcs:ignore
		}

		/**
		 * Subscription status filters.
		 *
		 * @param array $views Array if view.
		 */
		public function subscription_status_filter( $views ) {
			$i = 1;

			echo '<ul class="subsubsub">';

			$total = 0;
			foreach ( $this->subscription_status as $status ) {
				$total += $status->counter;
			}

			printf(
				'<li class="%1$s"><a href="%2$s">%3$s <span class="count">(%4$d)</span></a></li>',
				'all',
				esc_url( admin_url( 'edit.php?post_type=' . YITH_YWSBS_POST_TYPE ) ),
				esc_html(
					_nx(
						'All',
						'All',
						$total,
						'number of subscription',
						'yith-woocommerce-subscription'
					)
				),
				wp_kses_post( $total )
			);

			foreach ( $this->subscription_status as $status ) {
				$status_key   = $status->status;
				$counter      = $status->counter;
				$status_label = ywsbs_get_status_label( $status_key );
				printf( '<li class="%1$s">| <a href="%2$s">%3$s <span class="count">(%4$d)</span></a></li>', esc_attr( $status_key ), esc_url( admin_url( 'edit.php?post_type=' . YITH_YWSBS_POST_TYPE . '&status=' . $status_key ) ), esc_html( ucfirst( $status_label ) ), esc_html( $status->counter ) );
			}

			echo wp_kses_post( isset( $views['trash'] ) ? '<li class="trash">| ' . $views['trash'] . '</li>' : '' );
			echo wp_kses_post( '</ul>' );
			// empty the default list of post status filter.
			return array();
		}

		/**
		 * Manage the row actions in the Subscription List
		 *
		 * @param array   $actions Actions.
		 * @param WP_Post $post Current Post.
		 * @return array
		 */
		public function manage_row_actions( $actions, $post ) {
			if ( YITH_YWSBS_POST_TYPE !== get_post_type( $post ) ) {
				return $actions;
			}

			$subcription       = ywsbs_get_subscription( $post->ID );
			$is_valid_to_trash = apply_filters( 'ywsbs_valid_status_to_trash', array( 'pending', 'cancelled', 'expired' ) );

			if ( ! in_array( $subcription->get( 'status' ), $is_valid_to_trash, true ) ) {
				unset( $actions['trash'] );
			}

			unset( $actions['inline hide-if-no-js'] );
			unset( $actions['edit'] );

			return $actions;
		}

		/**
		 * Show blank slate.
		 *
		 * @param string $which String which tablenav is being shown.
		 * @since 2.1.
		 */
		public function maybe_render_blank_state( $which ) {
			global $post_type;

			if ( YITH_YWSBS_POST_TYPE === $post_type && 'bottom' === $which ) {
				$counts = (array) wp_count_posts( $post_type );
				unset( $counts['auto-draft'] );
				$count = array_sum( $counts );

				if ( 0 < $count ) {
					return;
				}

				$this->render_blank_state();

				echo '<style type="text/css">#posts-filter .wp-list-table, #posts-filter .tablenav.top, .tablenav.bottom .actions, .wrap .subsubsub, .wrap .wp-heading-inline + a.page-title-action  { display: none; } #posts-filter .tablenav.bottom { height: auto; } </style>';
			}
		}

		/**
		 * Column cb
		 *
		 * @param array|object $item Item.
		 * @return string|void
		 */
		public function column_cb( $item ) {
			return sprintf(
				'<input type="checkbox" name="ywsbs_subscription_ids[]" value="%s" />',
				$item->ID
			);
		}

		/**
		 * Bulk action
		 *
		 * @return array
		 */
		public function get_bulk_actions() {

			$actions = $this->current_action();
			if ( ! empty( $actions ) && isset( $_POST['ywsbs_subscription_ids'] ) ) { //phpcs:ignore

				$subscriptions = (array) $_POST['ywsbs_subscription_ids']; //phpcs:ignore

				if ( 'delete' === $actions ) {
					foreach ( $subscriptions as $subscriptions_id ) {
						wp_delete_post( $subscriptions_id, true );
					}
				}

				$this->prepare_items();
			}

			$actions = array(
				'delete' => __( 'Delete', 'yith-woocommerce-subscription' ),
			);

			return $actions;
		}

		/**
		 * Show the blank page when the subscription list is empty.
		 *
		 * @since 2.1.0
		 */
		public function render_blank_state() {

			?>
			<div class="ywsbs-admin-no-posts">
				<div class="ywsbs-admin-no-posts-container">
					<div class="ywsbs-admin-no-posts-logo"><img width="100"
							src="<?php echo esc_url( YITH_YWSBS_ASSETS_URL . '/images/dollar.svg' ); ?>"></div>
					<div class="ywsbs-admin-no-posts-text">
									<span>
										<strong><?php echo esc_html_x( 'You don\'t have any active subscriptions yet.', 'Text showed when the list of email is empty.', 'yith-woocommerce-subscription' ); ?></strong>
									</span>
						<p><?php echo esc_html_x( 'But don\'t worry, your first subscription will appear here soon!', 'Text showed when the list of email is empty.', 'yith-woocommerce-subscription' ); ?></p>
					</div>
					<div class="ywsbs-admin-no-posts-button">
						<a href="<?php echo esc_url( add_query_arg( array( 'post_type' => YITH_YWSBS_POST_TYPE ), admin_url( 'post-new.php' ) ) ); ?>"
							class="page-title-action"><?php echo esc_html_x( 'Add a new subscription', 'Button showed when the list of email is empty.', 'yith-woocommerce-subscription' ); ?></a>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Export subscriptions via csv.
		 *
		 * @since 2.1.0
		 */
		public function export_subscriptions_via_csv() {

			global $wpdb;

			$args = array(
				'post_type'   => YITH_YWSBS_POST_TYPE,
				'numberposts' => -1,
				'fields'      => 'ids',
			);

			$subscriptions = get_posts( $args );

			if ( ! empty( $subscriptions ) ) {

				$formatted_subscriptions = array();

				foreach ( $subscriptions as $subscription_id ) {
					$subscription = ywsbs_get_subscription( $subscription_id );

					if ( ! $subscription ) {
						continue;
					}

					$billing  = $subscription->get_address_fields( 'billing', true );
					$shipping = $subscription->get_address_fields( 'shipping', true );

					$billing_fields  = WC()->countries->get_address_fields( '', 'billing_' );
					$shipping_fields = WC()->countries->get_address_fields( '', 'shipping_' );

					$formatted_subscriptions[ $subscription_id ] = array(
						$subscription_id,
						$subscription->get_number(),
						$subscription->get( 'product_name' ),
						$subscription->get( 'product_id' ),
						$subscription->get( 'variation_id' ),
						! empty( $subscription->get( 'variation' ) ) ? wp_json_encode( $subscription->get( 'variation' ) ) : '',
						$subscription->get( 'quantity' ),
						$subscription->get( 'price_is_per' ),
						$subscription->get( 'price_time_option' ),
						$subscription->get( 'trial_per' ),
						$subscription->get( 'trial_time_option' ),
						$subscription->get_user_id(),
						$subscription->get_billing_email(),
						$subscription->get( 'subscription_total' ),
						$subscription->get( 'line_subtotal' ),
						$subscription->get( 'line_subtotal_tax' ),
						$subscription->get( 'order_subtotal' ),
						$subscription->get( 'order_tax' ),
						$subscription->get_order_shipping(),
						$subscription->get_order_shipping_tax(),
						$subscription->get_fee(),
						$subscription->get( 'order_currency' ),
						$subscription->get_payment_due_date() ? gmdate( 'Y-m-d H:i:s', $subscription->get_payment_due_date() ) : '',
						! empty( $subscription->get_start_date() ) ? gmdate( 'Y-m-d H:i:s', $subscription->get_start_date() ) : '',
						! empty( $subscription->get_end_date() ) ? gmdate( 'Y-m-d H:i:s', $subscription->get_end_date() ) : '',
						! empty( $subscription->get_expired_date() ) ? gmdate( 'Y-m-d H:i:s', $subscription->get_expired_date() ) : '',
						! empty( $subscription->get( 'cancelled_date' ) ) ? gmdate( 'Y-m-d H:i:s', $subscription->get( 'cancelled_date' ) ) : '',
						$subscription->get( 'status' ),
						$subscription->get_order_id(),
						! empty( $subscription->get( 'payed_order_list' ) ) ? implode( ',', $subscription->get( 'payed_order_list' ) ) : '',
						$subscription->get_paid_rates(),
						empty( $subscription->get_num_of_rates() ) ? '' : $subscription->get_num_of_rates(),
						$subscription->get_payment_method(),
						$subscription->get_payment_method_title(),
						! empty( $subscription->get( 'subscriptions_shippings' ) ) ? wp_json_encode( $subscription->get( 'subscriptions_shippings' ) ) : '',
						$billing ? WC()->countries->get_formatted_address( $billing, ' ' ) : '',
						$shipping ? WC()->countries->get_formatted_address( $shipping, ' ' ) : '',
					);

					foreach ( $billing_fields as $k => $bf ) {
						$k = str_replace( 'billing_', '', $k );
						$formatted_subscriptions[ $subscription_id ][] = isset( $billing[ $k ] ) ? $billing[ $k ] : '';
					}

					foreach ( $shipping_fields as $k => $sf ) {
						$k = str_replace( 'shipping_', '', $k );
						$formatted_subscriptions[ $subscription_id ][] = isset( $shipping[ $k ] ) ? $shipping[ $k ] : '';
					}
				}

				if ( ! empty( $formatted_subscriptions ) ) {
					$sitename  = sanitize_key( get_bloginfo( 'name' ) );
					$sitename .= ( ! empty( $sitename ) ) ? '-' : '';
					$filename  = $sitename . 'ywsbs-subscriptions-' . gmdate( 'Y-m-d-H-i' ) . '.csv';

					// Add Labels to CSV.
					$formatted_labels[] = apply_filters(
						'ywsbs_csv_labels',
						array(
							__( 'Subscription ID', 'yith-woocommerce-subscription' ),
							__( 'Subscription Number', 'yith-woocommerce-subscription' ),
							__( 'Product name', 'yith-woocommerce-subscription' ),
							__( 'Product id', 'yith-woocommerce-subscription' ),
							__( 'Variation id', 'yith-woocommerce-subscription' ),
							__( 'Variations', 'yith-woocommerce-subscription' ),
							__( 'Quantity', 'yith-woocommerce-subscription' ),
							__( 'Period interval', 'yith-woocommerce-subscription' ),
							__( 'Period', 'yith-woocommerce-subscription' ),
							__( 'Trial period interval', 'yith-woocommerce-subscription' ),
							__( 'Trial Period', 'yith-woocommerce-subscription' ),
							__( 'Customer ID', 'yith-woocommerce-subscription' ),
							__( 'Customer Email', 'yith-woocommerce-subscription' ),
							__( 'Recurring Amount', 'yith-woocommerce-subscription' ),
							__( 'Line Subtotal', 'yith-woocommerce-subscription' ),
							__( 'Line Subtotal Tax', 'yith-woocommerce-subscription' ),
							__( 'Order Subtotal', 'yith-woocommerce-subscription' ),
							__( 'Order Tax', 'yith-woocommerce-subscription' ),
							__( 'Shipping amount', 'yith-woocommerce-subscription' ),
							__( 'Shipping tax amount', 'yith-woocommerce-subscription' ),
							__( 'Fee', 'yith-woocommerce-subscription' ),
							__( 'Currency', 'yith-woocommerce-subscription' ),
							__( 'Payment due', 'yith-woocommerce-subscription' ),
							__( 'Started on', 'yith-woocommerce-subscription' ),
							__( 'Ended on', 'yith-woocommerce-subscription' ),
							__( 'Expiry date', 'yith-woocommerce-subscription' ),
							__( 'Cancelled on', 'yith-woocommerce-subscription' ),
							__( 'Status', 'yith-woocommerce-subscription' ),
							__( 'Order id', 'yith-woocommerce-subscription' ),
							__( 'Paid Orders', 'yith-woocommerce-subscription' ),
							__( 'Rates paid', 'yith-woocommerce-subscription' ),
							__( 'Number of rates', 'yith-woocommerce-subscription' ),
							__( 'Payment method', 'yith-woocommerce-subscription' ),
							__( 'Payment method title', 'yith-woocommerce-subscription' ),
							__( 'Shipping Package', 'yith-woocommerce-subscription' ),
							__( 'Billing info', 'yith-woocommerce-subscription' ),
							__( 'Shipping info', 'yith-woocommerce-subscription' ),
						)
					);

					foreach ( $billing_fields as $bf ) {
						$formatted_labels[0][] = isset( $bf['label'] ) ? esc_html__( 'Billing', 'yith-woocommerce-subscription' ) . ' ' . $bf['label'] : '';
					}

					foreach ( $shipping_fields as $sf ) {
						$formatted_labels[0][] = isset( $sf['label'] ) ? esc_html__( 'Shipping', 'yith-woocommerce-subscription' ) . ' ' . $sf['label'] : '';
					}

					$formatted_subscriptions = array_merge( $formatted_labels, $formatted_subscriptions );

					header( 'Content-Description: File Transfer' );
					header( 'Content-Disposition: attachment; filename=' . $filename );
					header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );

					$df = fopen( 'php://output', 'w' );

					foreach ( $formatted_subscriptions as $row ) {
						fputcsv( $df, $row, ';' );
					}

					fclose( $df ); //phpcs:ignore
				}
			}

			die();

		}
	}
}
