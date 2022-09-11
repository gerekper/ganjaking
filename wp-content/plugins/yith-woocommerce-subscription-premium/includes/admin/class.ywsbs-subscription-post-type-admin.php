<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YWSBS_Subscription_Post_Type_Admin Class.
 *
 * Manage the subscription post type in admin.
 *
 * @class   YWSBS_Subscription_Post_Type_Admin
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}
// phpcs:disable WordPress.Security.NonceVerification.Missing
if ( ! class_exists( 'YWSBS_Subscription_Post_Type_Admin' ) ) {

	/**
	 * Class YWSBS_Subscription_Post_Type_Admin
	 */
	class YWSBS_Subscription_Post_Type_Admin {


		/**
		 * Single instance of the class
		 *
		 * @var YWSBS_Subscription_Post_Type_Admin
		 */
		protected static $instance;

		/**
		 * Flag to avoid multiple execution of code.
		 *
		 * @var bool
		 */
		protected $saved_metabox = false;

		/**
		 * Subscription post name
		 *
		 * @var string
		 */
		private $post_name = '';

		/**
		 * Returns single instance of the class
		 *
		 * @return YWSBS_Subscription_Post_Type_Admin
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
			$this->post_name = YITH_YWSBS_POST_TYPE;

			add_action( 'add_meta_boxes', array( $this, 'show_info_subscription' ) );
			add_action( 'add_meta_boxes', array( $this, 'show_action_subscription' ) );

			add_action( 'add_meta_boxes', array( $this, 'show_product_subscription' ) );
			add_action( 'add_meta_boxes', array( $this, 'show_subscription_history' ) );
			add_action( 'add_meta_boxes', array( $this, 'show_subscription_delivery_schedules' ), 100, 2 );
			add_action( 'add_meta_boxes', array( $this, 'show_schedule_subscription' ), 10, 2 );
			add_action( 'add_meta_boxes', array( $this, 'show_activity_subscription' ) );
			add_action( 'admin_menu', array( $this, 'remove_publish_box' ) );

			add_action( 'admin_init', array( $this, 'remove_meta_fix' ) );
			add_action( 'save_post', array( $this, 'before_data_saving' ), 0, 2 );

			add_filter( 'yit_fw_metaboxes_type_args', array( $this, 'format_date_fields' ) );

			add_filter( 'yith_plugin_fw_metabox_class', array( $this, 'add_custom_metabox_class' ), 10, 2 );
			add_filter( 'is_protected_meta', array( $this, 'show_protected_meta_data' ), 10, 3 );
		}

		/**
		 * Show also private meta
		 *
		 * @param bool   $protected Protected.
		 * @param string $meta_key Meta key.
		 * @param string $meta_type Meta type.
		 * @return bool
		 */
		public function show_protected_meta_data( $protected, $meta_key, $meta_type ) {
			global $post;

			if ( $post && YITH_YWSBS_POST_TYPE === $post->post_type && '_edit_lock' !== $meta_key ) {
				$protected = false;
			}

			return $protected;
		}

		/**
		 * Add the metabox to show the info of subscription
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function show_info_subscription() {
			add_meta_box( 'ywsbs-info-subscription', esc_html__( 'Subscription Info', 'yith-woocommerce-subscription' ), array( $this, 'show_subscription_info_metabox' ), $this->post_name, 'normal', 'high' );
		}

		/**
		 * Metabox to show the info of the current subscription.
		 *
		 * @param object $post WP_Post.
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function show_subscription_info_metabox( $post ) {
			$subscription = ywsbs_get_subscription( $post->ID );
			$args         = array(
				'subscription' => $subscription,
			);
			wc_get_template( 'admin/metabox/metabox_subscription_info_content.php', $args, '', YITH_YWSBS_TEMPLATE_PATH . '/' );
		}

		/**
		 * Add the metabox to show the action of subscription
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function show_action_subscription() {
			add_meta_box( 'ywsbs-action-subscription', esc_html__( 'Subscription Action', 'yith-woocommerce-subscription' ), array( $this, 'show_subscription_action_metabox' ), $this->post_name, 'side', 'high' );
		}

		/**
		 * Metabox to show the action of the current subscription
		 *
		 * @access public
		 *
		 * @param object $post WP_Post.
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function show_subscription_action_metabox( $post ) {
			$subscription = ywsbs_get_subscription( $post->ID );
			$args         = array( 'subscription' => $subscription );
			wc_get_template( 'admin/metabox/metabox_subscription_action_content.php', $args, '', YITH_YWSBS_TEMPLATE_PATH . '/' );
		}

		/**
		 * Add the metabox to show the activities of subscription.
		 *
		 * @access public
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function show_activity_subscription() {
			add_meta_box( 'ywsbs-activity-subscription', esc_html_x( 'Subscription Activities', 'Admin info widget in the subscription details', 'yith-woocommerce-subscription' ), array( $this, 'show_subscription_activity_metabox' ), $this->post_name, 'side', 'high' );
		}

		/**
		 * Metabox to show the activities of the current subscription
		 *
		 * @access public
		 *
		 * @param object $post WP_Post.
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function show_subscription_activity_metabox( $post ) {
			$subscription = ywsbs_get_subscription( $post->ID );
			$limit        = apply_filters( 'ywsbs_num_of_activities_on_subscription_editor_page', 10 );
			$activities   = YITH_WC_Activity()->get_activity_by_subscription( $post->ID, $limit + 1 );
			$view_more    = false;
			if ( count( $activities ) > $limit ) {
				array_pop( $activities );
				$args      = array(
					'page'         => 'yith_woocommerce_subscription',
					'tab'          => 'subscription',
					'sub_tab'      => 'subscription-activities',
					'subscription' => $post->ID,
				);
				$view_more = add_query_arg( $args, admin_url( 'admin.php' ) );
			}

			$args = array(
				'subscription' => $subscription,
				'activities'   => $activities,
				'view_more'    => $view_more,
			);
			wc_get_template( 'admin/metabox/metabox_subscription_activity_content.php', $args, '', YITH_YWSBS_TEMPLATE_PATH . '/' );
		}

		/**
		 * Add the metabox to show the products of subscription
		 *
		 * @access public
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function show_product_subscription() {
			add_meta_box( 'ywsbs-product-subscription', esc_html__( 'Subscription Product', 'yith-woocommerce-subscription' ), array( $this, 'show_subscription_product_metabox' ), $this->post_name, 'normal', 'high' );
		}

		/**
		 * Metabox to show the product detail of the current subscription
		 *
		 * @access public
		 *
		 * @param object $post WP_Post.
		 *
		 * @return void
		 *
		 * @since 1.0.0
		 */
		public function show_subscription_product_metabox( $post ) {
			$subscription = ywsbs_get_subscription( $post->ID );
			$product      = wc_get_product( ( $subscription->variation_id ) ? $subscription->variation_id : $subscription->product_id );
			$args         = array(
				'product'      => $product,
				'subscription' => $subscription,
			);
			wc_get_template( 'admin/metabox/metabox_subscription_product.php', $args, '', YITH_YWSBS_TEMPLATE_PATH . '/' );
		}

		/**
		 * Add the metabox to show the orders of subscription
		 *
		 * @access public
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function show_subscription_history() {
			add_meta_box( 'ywsbs-subscription-history', esc_html__( 'Subscription\'s History', 'yith-woocommerce-subscription' ), array( $this, 'show_subscription_history_metabox' ), $this->post_name, 'normal', 'high' );
		}

		/**
		 * Metabox to show the order history of the current subscription
		 *
		 * @access public
		 *
		 * @param object $post WP_Post.
		 *
		 * @return void
		 *
		 * @since 1.0.0
		 */
		public function show_subscription_history_metabox( $post ) {
			$subscription = ywsbs_get_subscription( $post->ID );

			$history     = (array) $subscription->get( 'payed_order_list' );
			$main_order  = (int) $subscription->get( 'order_id' );
			$renew_order = $subscription->get( 'renew_order' );

			$parent_resubscribe_subscription = $subscription->get( 'parent_subscription' );
			$child_resubscribe_subscription  = $subscription->get( 'child_subscription' );

			if ( ! in_array( $main_order, $history, true ) ) {
				$history = array_merge( array( $main_order ), $history );
			}

			if ( ! empty( $renew_order ) && ! in_array( (int) $renew_order, $history, true ) ) {
				array_push( $history, (int) $renew_order );
			}

			if ( ! empty( $parent_resubscribe_subscription ) ) {
				$parent_resubscribe_subscription = ywsbs_get_subscription( $parent_resubscribe_subscription );
				if ( $parent_resubscribe_subscription ) {
					$parent_resubscribe_order = (int) $parent_resubscribe_subscription->get( 'order_id' );
					if ( ! empty( $parent_resubscribe_order ) && ! in_array( $parent_resubscribe_order, $history, true ) ) {
						array_push( $history, ( $parent_resubscribe_order ) );
					}
				}
			}

			if ( ! empty( $child_resubscribe_subscription ) ) {
				$child_resubscribe_subscription = ywsbs_get_subscription( $child_resubscribe_subscription );
				if ( $child_resubscribe_subscription ) {
					$child_resubscribe_order = (int) $child_resubscribe_subscription->get( 'order_id' );
					if ( ! empty( $child_resubscribe_order ) && ! in_array( $child_resubscribe_order, $history, true ) ) {
						array_push( $history, ( $child_resubscribe_order ) );
					}
				}
			}

			$history = wc_get_orders(
				array(
					'post__in' => $history,
					'order_by' => 'id',
					'order'    => 'DESC',
				)
			);

			$args = array(
				'subscription' => $subscription,
				'history'      => $history,
			);

			wc_get_template( 'admin/metabox/metabox_subscription_history.php', $args, '', YITH_YWSBS_TEMPLATE_PATH . '/' );
		}

		/**
		 * Add the metabox to show the product of subscription
		 *
		 * @access public
		 *
		 * @param string $post_type Post type.
		 * @param object $post WP_Post.
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function show_subscription_delivery_schedules( $post_type, $post ) {
			if ( $this->post_name !== $post_type ) {
				return;
			}

			$subscription       = ywsbs_get_subscription( $post->ID );
			$delivery_schedules = $subscription->get( 'delivery_schedules' );

			if ( ! $subscription || ! $delivery_schedules ) {
				return;
			}

			$each = '';
			switch ( $delivery_schedules['delivery_period'] ) {
				case 'weeks':
					$day_weeks = ywsbs_get_period_options( 'day_weeks' );
					$each     .= $day_weeks[ $delivery_schedules['sych_weeks'] ];
					break;
				case 'months':
					$day_months = ywsbs_get_period_options( 'day_months' );
					$each      .= $day_months[ $delivery_schedules['months'] ] . ' ' . esc_html__( 'of each month', 'yith-woocommerce-subscription' );
					break;
				case 'years':
					$day_months = ywsbs_get_period_options( 'day_months' );
					$months     = ywsbs_get_period_options( 'months' );
					$each      .= $day_months[ $delivery_schedules['years_day'] ] . ' ' . $months[ $delivery_schedules['years_month'] ];
					break;
			}
			$each .= ')';
			// translators: 1.delivery gap 2. delivery period, 2. date.
			$label = sprintf( __( 'Every %1$d %2$s - on %3$s', 'yith-woocommerce-subscription' ), $delivery_schedules['delivery_gap'], ywsbs_get_time_options_sing_plur( $delivery_schedules['delivery_period'], $delivery_schedules['delivery_gap'] ), $each );

			add_meta_box( 'ywsbs-delivery-schedules-subscription', esc_html__( 'Delivery Schedules', 'yith-woocommerce-subscription' ) . ' (' . $label, array( $this, 'show_delivery_schedules_metabox' ), $this->post_name, 'normal', 'high' );
		}


		/**
		 * Delivery schedules metabox
		 *
		 * @param WP_Post $post Current post.
		 */
		public function show_delivery_schedules_metabox( $post ) {

			$args = array(
				'delivery_schedules' => YWSBS_Subscription_Delivery_Schedules()->get_delivery_schedules_ordered( $post->ID ),
			);

			wc_get_template( 'admin/metabox/metabox_subscription_delivery_schedules.php', $args, '', YITH_YWSBS_TEMPLATE_PATH . '/' );
		}

		/**
		 * Add the metabox to show the product of subscription
		 *
		 * @access public
		 *
		 * @param string $post_type Post type.
		 * @param object $post WP_Post.
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function show_schedule_subscription( $post_type, $post ) {

			if ( $this->post_name !== $post_type ) {
				return;
			}

			$subscription = ywsbs_get_subscription( $post->ID );

			if ( ! $subscription || ! $subscription->can_be_editable( 'payment_date' ) ) {
				return;
			}

			add_meta_box( 'ywsbs-schedule-subscription', esc_html__( 'Subscription Schedule', 'yith-woocommerce-subscription' ), array( $this, 'show_subscription_schedule_metabox' ), $this->post_name, 'side', 'high' );
		}

		/**
		 * Metabox to show the schedule options of the current subscription
		 *
		 * @access public
		 *
		 * @param object $post WP_Post.
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function show_subscription_schedule_metabox( $post ) {

			$subscription = ywsbs_get_subscription( $post->ID );
			$args         = array(
				'fields'       => $this->get_schedule_data_subscription_fields(),
				'subscription' => $subscription,
				'time_format'  => apply_filters( 'ywsbs_time_format', 'Y-m-d H:i:s' ),
			);
			wc_get_template( 'admin/metabox/metabox_subscription_schedule.php', $args, '', YITH_YWSBS_TEMPLATE_PATH . '/' );
		}

		/**
		 * Return the labels of schedule metabox options.
		 *
		 * @return mixed|void
		 */
		public function get_schedule_data_subscription_fields() {
			$fields = array(
				'start_date'        => esc_html__( 'Start Date', 'yith-woocommerce-subscription' ),
				'payment_due_date'  => esc_html__( 'Payment Due Date', 'yith-woocommerce-subscription' ),
				'expired_date'      => esc_html__( 'Expired date', 'yith-woocommerce-subscription' ),
				'next_attempt_date' => esc_html__( 'Next attempt date', 'yith-woocommerce-subscription' ),
			);

			return apply_filters( 'ywsbs_schedule_data_subscription_fields', $fields );
		}

		/**
		 * Remove publish box from single page page of subscription.
		 *
		 * @access public
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function remove_publish_box() {
			remove_meta_box( 'submitdiv', $this->post_name, 'side' );
		}

		/**
		 * Remove the item 'meta' from $_POST to avoid issue during the data saving
		 */
		public function remove_meta_fix() {
			if ( isset( $_POST['meta'] ) && isset( $_POST['post_type'] ) && YITH_WC_Subscription()->post_name === $_POST['post_type'] ) {  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				unset( $_POST['meta'] );
			}
		}

		/**
		 * Save meta data
		 *
		 * @param integer $post_id Post ID.
		 * @param WP_Post $post Current post.
		 *
		 * @return void
		 * @throws Exception Return Exception.
		 */
		public function before_data_saving( $post_id, $post ) {

			if ( $this->saved_metabox || empty( $_POST['post_ID'] ) || (int) $_POST['post_ID'] !== $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			// Do not save meta boxes for revisions or auto save.
			if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
				return;
			}

			$subscription = ywsbs_get_subscription( $post_id );
			$posted       = $_POST;

			// Schedule save option.
			if ( isset( $posted['ywsbs_safe_submit_field'] ) && 'schedule_subscription' === $posted['ywsbs_safe_submit_field'] ) {

				// Recurring period.
				if ( isset( $posted['ywsbs_price_is_per'], $posted['ywsbs_price_time_option'] )
					&& ( $posted['ywsbs_price_is_per'] !== $subscription->get( 'price_is_per' ) || $posted['ywsbs_price_time_option'] !== $subscription->get( 'price_time_option' ) )
				) {
					$recurring_old = ywsbs_get_price_per_string( $subscription->price_is_per, $subscription->price_time_option, true );
					$subscription->set( 'price_is_per', intval( $posted['ywsbs_price_is_per'] ) );
					$subscription->set( 'price_time_option', $posted['ywsbs_price_time_option'] );
					$recurring_new = ywsbs_get_price_per_string( $subscription->get( 'price_is_per' ), $subscription->get( 'price_time_option' ), true );
					// translators: 1. start date 2. end date.
					YITH_WC_Activity()->add_activity( $post_id, 'changed', $status = 'success', $order = 0, sprintf( esc_html_x( 'Changed recurring period from: %1$s to %2$s', 'First placeholder: old recurring period; second placeholder: new recurring period', 'yith-woocommerce-subscription' ), $recurring_old, $recurring_new ) );
				}

				// Schedule fields.
				$schedule_fields = $this->get_schedule_data_subscription_fields();
				foreach ( $schedule_fields as $key => $value ) {
					$field_name = 'ywsbs_' . $key;
					$new_value  = isset( $posted[ $field_name ] ) ? strtotime( $posted[ $field_name ] ) : '';
					$old_value  = $subscription->get( $key );
					if ( $old_value != $new_value ) { //phpcs:ignore
						$subscription->set( $key, $new_value );
						do_action( 'ywsbs_scheduled_data_updated', $key, $new_value, $old_value, $subscription );
						$new_value_formatted = date_i18n( wc_date_format(), $new_value ) . ' ' . date_i18n( __( wc_time_format() ), $new_value ); //phpcs:ignore
						$old_value_formatted = date_i18n( wc_date_format(), $old_value ) . ' ' . date_i18n( __( wc_time_format() ), $old_value );  //phpcs:ignore
						/* translators: 1: Option name changed, 2: old value, 3: new value */
						YITH_WC_Activity()->add_activity( $post_id, 'changed', $status = 'success', $order = 0, sprintf( esc_html_x( 'Changed %1$s from: %2$s to %3$s', '1: Option name changed, 2: old value, 3: new value', 'yith-woocommerce-subscription' ), $value, $old_value_formatted, $new_value_formatted ) );
					}
				}
			}

			// Save Billing and Shipping Meta if different from parent order.
			$meta            = array();
			$billing_fields  = ywsbs_get_order_fields_to_edit( 'billing' );
			$shipping_fields = ywsbs_get_order_fields_to_edit( 'shipping' );

			foreach ( $billing_fields as $key => $billing_field ) {
				$field_id = '_billing_' . $key;
				if ( ! isset( $posted[ $field_id ] ) ) {
					continue;
				}
				$meta[ $field_id ] = $posted[ $field_id ];
			}

			foreach ( $shipping_fields as $key => $shipping_field ) {
				$field_id = '_shipping_' . $key;
				if ( ! isset( $posted[ $field_id ] ) ) {
					continue;
				}
				$meta[ $field_id ] = $posted[ $field_id ];
			}

			if ( isset( $posted['customer_note'] ) ) {
				$meta['customer_note'] = $posted['customer_note'];
			}

			if ( isset( $posted['user_ID'], $posted['user_id'] ) && $posted['user_id'] !== $posted['user_ID'] ) {
				$meta['user_id'] = $posted['user_id'];
			}

			$meta && $subscription->update_subscription_meta( $meta );

			if ( ! empty( $posted['ywsbs_subscription_actions'] ) ) {

				if ( 'renew-order' === $posted['ywsbs_subscription_actions'] ) {
					YWSBS_Subscription_Order()->renew_order( $post_id );
				} elseif ( 'delete-current-renew-order' === $posted['ywsbs_subscription_actions'] ) {
					YWSBS_Subscription_Order()->delete_current_renew_order( $subscription );
				} elseif ( 'pay-current-renew-order' === $posted['ywsbs_subscription_actions'] ) {
					yith_subscription_log( 'The admin tried to pay manually the renew order for the subscription ' . $subscription->get_id(), 'subscription_payment' );
					YWSBS_Subscription_Order()->pay_renew_order( $subscription->get_renew_order_id() );
				} elseif ( 'set-status-during-the-renew' === $posted['ywsbs_subscription_actions'] ) {
					yith_subscription_log( 'The admin tried to schedule the status changing during the renew.' . $subscription->get_id(), 'subscription_payment' );
					$subscription->set_status_during_the_renew();
				} else {
					$subscription = ywsbs_get_subscription( $post_id );
					$new_status   = $posted['ywsbs_subscription_actions'];
					YITH_WC_Subscription()->manual_change_status( $new_status, $subscription, 'administrator' );
				}
			}

			$this->saved_metabox = true;

		}

		/**
		 * Format Timestamps into dates
		 *
		 * @access public
		 * @param array $args Arguments.
		 *
		 * @return mixed
		 * @since  1.0.0
		 */
		public function format_date_fields( $args ) {

			$date_fields = apply_filters( 'ywsbs_date_fields', array( 'start_date', 'payment_due_date', 'expired_date', 'cancelled_date', 'end_date' ) );

			if ( in_array( $args['args']['args']['id'], $date_fields ) ) { //phpcs:ignore
				$args['args']['args']['value'] = ( $time_stamp = $args['args']['args']['value'] ) ? date_i18n( 'Y-m-d', $time_stamp ) : ''; //phpcs:ignore
			}

			return $args;
		}

		/**
		 * Update the subscription prices by admin.
		 *
		 * @param array              $posted Posted Data.
		 * @param YWSBS_Subscription $subscription Subscription Object.
		 * @deprecated
		 * @since 2.0.0
		 */
		public function update_prices( $posted, $subscription ) {

			$new_values = array();
			$old_values = array();

			if ( isset( $posted['ywsbs_quantity'] ) ) {
				$new_values['quantity'] = $posted['ywsbs_quantity'];
				$old_values['quantity'] = $subscription->get( 'quantity' );
			}

			if ( isset( $posted['ywsbs_line_total'] ) ) {
				$new_values['line_total'] = wc_format_decimal( $posted['ywsbs_line_total'] );
				$old_values['line_total'] = $subscription->get_line_total();
			}

			if ( isset( $posted['ywsbs_line_tax'] ) ) {
				$new_values['line_tax'] = wc_format_decimal( $posted['ywsbs_line_tax'] );
				$old_values['line_tax'] = $subscription->get_line_tax();
			}

			if ( isset( $posted['ywsbs_shipping_cost_line_cost'] ) ) {
				$new_values['order_shipping'] = wc_format_decimal( $posted['ywsbs_shipping_cost_line_cost'] );
				$old_values['order_shipping'] = floatval( $subscription->get( 'order_shipping' ) );
			}

			if ( isset( $posted['ywsbs_shipping_cost_line_tax'] ) ) {
				$new_values['order_shipping_tax'] = wc_format_decimal( $posted['ywsbs_shipping_cost_line_tax'] );
				$old_values['order_shipping_tax'] = floatval( $subscription->get( 'order_shipping_tax' ) );
			}

			$changes = array_diff_assoc( $new_values, $old_values );

			if ( $changes ) {
				$message = '';
				foreach ( $changes as $key => $change ) {
					$currency = ( 'quantity' !== $key ) ? get_woocommerce_currency_symbol( $subscription->get( 'order_currency' ) ) : '';
					// translators: $1: Option changed label; $2: Old value; $3: New value.
					$message .= sprintf( esc_html_x( '%$1s from %$2s to %$3s', '$1: Option changed label; $2: Old value; $3: New value', 'yith-woocommerce-subscription' ), str_replace( '_', ' ', $key ), $old_values[ $key ] . $currency, $new_values[ $key ] . $currency . '<br>' );
				}
				// translators:placeholder option changed.
				YITH_WC_Activity()->add_activity( $subscription->id, 'changed', 'success', 0, sprintf( esc_html_x( 'Changed %s', 'placeholder option changed', 'yith-woocommerce-subscription' ), $message ) );
			}
			// Save the array of shipping.
			$new_values['subscriptions_shippings'] = $subscription->get( 'subscriptions_shippings' );

			if ( isset( $posted['ywsbs_shipping_method_name'] ) ) {
				$new_values['subscriptions_shippings']['name'] = $posted['ywsbs_shipping_method_name'];
			}
			if ( isset( $new_values['order_shipping'] ) ) {
				$new_values['subscriptions_shippings']['cost'] = $new_values['order_shipping'];
			}

			$changes['subscriptions_shippings'] = $new_values['subscriptions_shippings'];

			if ( $changes ) {
				$subscription->update_subscription_meta( $changes );
			}

			YWSBS_Subscription_Helper()->calculate_totals_from_changes( $subscription );
		}


		/**
		 * Add new plugin-fw style.
		 *
		 * @param string  $class Class.
		 * @param WP_Post $post Post.
		 *
		 * @return string
		 */
		public function add_custom_metabox_class( $class, $post ) {

			$allow_post_types = array( YITH_YWSBS_POST_TYPE );

			if ( in_array( $post->post_type, $allow_post_types, true ) ) {
				$class .= ' ' . yith_set_wrapper_class();
			}
			return $class;
		}

	}
}
