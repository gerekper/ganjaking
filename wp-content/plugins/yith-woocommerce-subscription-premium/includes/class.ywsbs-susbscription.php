<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements YWSBS_Subscription Class
 *
 * @class   YWSBS_Subscription
 * @since   1.0.0
 * @author  YITH
 *
 * @package YITH WooCommerce Subscription
 */
if ( ! class_exists( 'YWSBS_Subscription' ) ) {

	class YWSBS_Subscription {

		/**
		 * The subscription (post) ID.
		 *
		 * @var int
		 */
		public $id = 0;

		/**
		 * $post Stores post data
		 *
		 * @var $post WP_Post
		 */
		public $post = null;

		/**
		 * @var $order WC_Order
		 */
		public $order = null;

		/**
		 * @var $product WC_Product
		 */
		public $product = null;

		/**
		 * Stores the properties of a subscription.
		 *
		 * @var array
		 */
		private $_array_prop = array();

		/**
		 * Constructor
		 *
		 * Initialize the YWSBS_Subscription Object
		 *
		 * @param int   $subscription_id
		 * @param array $args
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function __construct( $subscription_id = 0, $args = array() ) {

			// initialize the subscription if $subscription_id is defined
			if ( $subscription_id ) {
				$this->set( 'id', $subscription_id );
				$this->post = get_post( $subscription_id );
				$this->empty_cache();
			}

			// create a new subscription if $args is passed
			if ( $subscription_id == '' && ! empty( $args ) ) {
				$this->add_subscription( $args );
			}

		}

		/**
		 * Returns the unique ID for this object.
		 *
		 * @return int
		 * @since  1.7.2
		 */
		public function get_id() {
			return $this->id;
		}

		/**
		 * __get function.
		 *
		 * @param string $key
		 *
		 * @return mixed
		 */
		public function __get( $key ) {

			if ( ! isset( $this->_array_prop[ $key ] ) ) {
				$this->_array_prop[ $key ] = get_post_meta( $this->id, $key, true );
			}

			return $this->_array_prop[ $key ];
		}

		/**
		 * Magic Method isset
		 *
		 * @param $key
		 *
		 * @return bool
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function __isset( $key ) {
			if ( ! $this->id ) {
				return false;
			}

			return metadata_exists( 'post', $this->id, $key );
		}

		/**
		 * set function.
		 *
		 * @param string $property
		 * @param mixed  $value
		 *
		 * @return bool|int
		 */
		public function set( $property, $value ) {
			$this->$property                = $value;
			$this->_array_prop[ $property ] = $value;

			return update_post_meta( $this->id, $property, $value );
		}

		/**
		 * Get function.
		 *
		 * @param string $prop
		 * @param string $context change this string if you want the value stored in database
		 *
		 * @return mixed
		 */
		public function get( $prop, $context = 'view' ) {

			$value = $this->$prop;
			if ( 'view' === $context ) {
				// APPLY_FILTER : ywsbs_subscription_{$key}: filtering the post meta of a subscription
				$value = apply_filters( 'ywsbs_subscription_' . $prop, $value, $this );
			}

			return $value;
		}

		/**
		 * Reset the data saved on subscription object.*
		 */
		public function empty_cache() {
			$this->_array_prop = array();
		}

		/**
		 * Populate the subscription
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function populate() {

			$this->set( 'post', get_post( $this->get( 'id' ) ) );

			foreach ( $this->get_subscription_meta() as $key => $value ) {
				$this->set( $key, $value );
			}

			do_action( 'ywsbs_subscription_loaded', $this );
		}

		/**
		 * Add new subscription.
		 *
		 * @param array $args
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function add_subscription( $args ) {

			$subscription_id = wp_insert_post(
				array(
					'post_status' => 'publish',
					'post_type'   => 'ywsbs_subscription',
				)
			);

			if ( $subscription_id ) {
				$this->set( 'id', $subscription_id );
				// APPLY_FILTER: ywsbs_add_subscription_args : to filter the meta data of a subscription before the creation
				$meta = apply_filters( 'ywsbs_add_subscription_args', wp_parse_args( $args, $this->get_default_meta_data() ), $this );
				$this->update_subscription_meta( $meta );

				YITH_WC_Activity()->add_activity( $subscription_id, 'new', 'success', $this->get( 'order_id' ), __( 'Subscription successfully created.', 'yith-woocommerce-subscription' ) );
			}
		}

		/**
		 * Update post meta in subscription
		 *
		 * @param array $meta
		 *
		 * @return void
		 * @since  1.0.0
		 */
		function update_subscription_meta( $meta ) {
			foreach ( $meta as $key => $value ) {
				$this->set( $key, $value );
			}
		}

		/**
		 * Fill the default metadata with the post meta stored in db
		 *
		 * @return array
		 * @since  1.0.0
		 */
		function get_subscription_meta() {
			$subscription_meta = array();
			foreach ( $this->get_default_meta_data() as $key => $value ) {
				$subscription_meta[ $key ] = get_post_meta( $this->id, $key, true );
			}

			return $subscription_meta;
		}

		/**
		 * Start the subscription if a first payment is done
		 * order_id is the id of the first order created
		 *
		 * @param int $order_id
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function start( $order_id ) {

			$payed = $this->payed_order_list;

			// do not nothing if this subscription has payed with this order
			if ( ! empty( $payed ) && is_array( $payed ) && in_array( $order_id, $payed ) ) {
				return;
			}

			$new_status  = 'active';
			$rates_payed = 1;

			if ( $this->start_date == '' ) {
				$this->set( 'start_date', current_time( 'timestamp' ) );
			}

			$trial_period = 0;

			// if there's a trial period shift the date of payment due
			if ( $this->trial_per != '' && $this->trial_per > 0 ) {
				$trial_period = ywsbs_get_timestamp_from_option( 0, $this->trial_per, $this->trial_time_option );
				$rates_payed  = 0; // if there's a trial period the first payment is for signup
				$new_status   = 'trial';
			}

			if ( $this->payment_due_date == '' ) {
				$payment_due_date = apply_filters( 'ywsbs_payment_due_date_at_start', $this->get_next_payment_due_date( $trial_period, $this->start_date ), $this );
				// Change the next payment_due_date
				$this->set( 'payment_due_date', $payment_due_date );
			}

			if ( $this->expired_date == '' && $this->max_length != '' ) {

				$trial_period = 0;

				if ( $this->trial_per != '' && $this->trial_per > 0 ) {
					$trial_period = ywsbs_get_timestamp_from_option( 0, $this->trial_per, $this->trial_time_option );
				}

				$timestamp = ywsbs_get_timestamp_from_option( current_time( 'timestamp' ), $this->max_length, $this->price_time_option ) + $trial_period;

				$this->set( 'expired_date', $timestamp );

			}

			// Change the status to active
			$update = $this->update_status( $new_status );
			if ( $update ) {
				YITH_WC_Activity()->add_activity( $this->id, 'activated', 'success', $this->order_id, sprintf( __( 'Payment received for #%s', 'yith-woocommerce-subscription' ), $order_id ) );
			} else {
				YITH_WC_Activity()->add_activity( $this->id, 'activated', 'info', $this->order_id, sprintf( __( 'Payment received for #%s no status changed', 'yith-woocommerce-subscription' ), $order_id ) );
			}

			// correct the payment methods
			$this->update_payment_method();

			if ( $new_status != 'trial' ) {
				// DO_ACTION: ywsbs_customer_subscription_payment_done_mail : used to send an email to customer after the payment
				do_action( 'ywsbs_customer_subscription_payment_done_mail', $this );
			}

			$payed[] = $order_id;

			$this->set( 'rates_payed', $rates_payed );
			$this->set( 'payed_order_list', $payed );

			// if there's an upgrade/downgrade
			$subscription_to_cancel_info = get_post_meta( $order_id, '_ywsbs_subscritpion_to_cancel', true );

			if ( ! empty( $subscription_to_cancel_info ) ) {

				YITH_WC_Subscription()->cancel_subscription_after_upgrade( $subscription_to_cancel_info['subscription_to_cancel'] );
				update_post_meta( $subscription_to_cancel_info['subscription_to_cancel'], 'ywsbs_switched', 'yes' );

				if ( $subscription_to_cancel_info['process_type'] == 'upgrade' ) {
					delete_user_meta( $subscription_to_cancel_info['user_id'], 'ywsbs_upgrade_' . $subscription_to_cancel_info['product_id'] );
				} elseif ( $subscription_to_cancel_info['process_type'] == 'downgrade' ) {
					delete_user_meta( $subscription_to_cancel_info['user_id'], 'ywsbs_downgrade_' . $subscription_to_cancel_info['product_id'] );
				}
			}

			// DO_ACTION: ywsbs_subscription_started : trigger an action after that the subscription started
			do_action( 'ywsbs_subscription_started', $this->id );
		}

		/**
		 * Update the subscription.
		 * Usually is called after a payment of a renew order.
		 *
		 * @param int $order_id
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function update( $order_id ) {

			$payed = $this->payed_order_list;
			$order = wc_get_order( $order_id );

			// do not nothing if this subscription has payed with this order
			if ( ! empty( $payed ) && is_array( $payed ) && in_array( $order_id, $payed ) && ! $order ) {
				return;
			}

			// Change the status to active
			$this->update_status( 'active' );

			// Change the next payment_due_date
			$this->set( 'payment_due_date', $this->get_next_payment_due_date() );

			// reset failed payment in order parent
			$parent_order = wc_get_order( $this->order_id );
			yit_save_prop(
				$parent_order,
				array(
					'failed_attemps'       => 0,
					'next_payment_attempt' => '',
				)
			);

			// reset failed payment
			yit_save_prop(
				$order,
				array(
					'failed_attemps'       => 0,
					'next_payment_attempt' => '',
				)
			);

			$message = sprintf( __( 'Payment received for #%s. Next payment due date set.', 'yith-woocommerce-subscription' ), $order_id );
			yith_subscription_log( $message, 'subscription_payment' );

			YITH_WC_Activity()->add_activity( $this->id, 'activated', 'success', $this->order_id, $message );

			// DO_ACTION: ywsbs_customer_subscription_payment_done_mail : it is used to send email to customer for payment done
			do_action( 'ywsbs_customer_subscription_payment_done_mail', $this );

			// DO_ACTION: ywsbs_renew_order_payed : trigger an action after that the subscription renew order is paid
			do_action( 'ywsbs_renew_order_payed', $this->id, $order_id );

			// update _payed_order_list
			$payed[] = $order_id;
			$this->set( 'payed_order_list', $payed );
			$this->set( 'rates_payed', $this->rates_payed + 1 );

			// reset _renew_order
			$this->set( 'renew_order', 0 );

			// DO_ACTION: ywsbs_subscription_updated : trigger an action after that the subscription was updated
			do_action( 'ywsbs_subscription_updated', $this->id );
		}

		/**
		 * Updates status of subscription
		 *
		 * @param string $new_status
		 * @param string $from
		 *
		 * @return  bool
		 * @since   1.0.0
		 */
		public function update_status( $new_status, $from = '' ) {
			if ( ! $this->id ) {
				return false;
			}

			$old_status = $this->status;
			$from_list  = ywsbs_get_from_list();

			if ( $new_status !== $old_status || ! in_array( $new_status, array_keys( ywsbs_get_status() ) ) ) {

				$from_text = ( $from != '' && isset( $from_list[ $from ] ) ) ? 'By ' . $from_list[ $from ] : '';

				switch ( $new_status ) {
					case 'active':
						// reset some custom data
						$this->set( 'expired_pause_date', '' );
						// Check if subscription is cancelled. Es. for echeck payments
						if ( $old_status == 'cancelled' ) {
							if ( $from == 'administrator' ) {
								$this->set( 'status', $new_status );
								do_action( 'ywsbs_customer_subscription_actived_mail', $this );
								YITH_WC_Activity()->add_activity( $this->id, 'activated', 'success', $this->order_id, sprintf( __( 'Subscription is now active. %s ', 'yith-woocommerce-subscription' ), $from_text ) );

								$this->set( 'payment_due_date', $this->end_date );
								$this->set( 'end_date', '' );
								$this->set( 'cancelled_date', '' );
							} else {
								$this->set( 'end_date', $this->payment_due_date );
								$this->set( 'payment_due_date', '' );
								do_action( 'ywsbs_no_activated_just_cancelled', $this );

								return false;
							}
						} else {
							$this->set( 'status', $new_status );
							do_action( 'ywsbs_customer_subscription_actived_mail', $this );
							YITH_WC_Activity()->add_activity( $this->id, 'activated', 'success', $this->order_id, sprintf( __( 'Subscription is now active. %s', 'yith-woocommerce-subscription' ), $from_text ) );
						}

						break;

					case 'paused':
						$pause_options = $this->get_subscription_product_pause_options();

						// add the date of pause
						$date_of_pauses   = $this->get( 'date_of_pauses' );
						$date_of_pauses = !empty( $date_of_pauses ) ? $date_of_pauses : array();
						$date_of_pauses[] = current_time( 'timestamp' );
						$this->set( 'date_of_pauses', $date_of_pauses );

						// increase the num of pauses done
						$this->set( 'num_of_pauses', $this->num_of_pauses + 1 );

						// expired_pause_date
						if ( $pause_options['max_pause_duration'] != '' ) {
							$this->set( 'expired_pause_date', current_time( 'timestamp' ) + $pause_options['max_pause_duration'] * 86400 );
						}

						// Update the subscription status
						$this->set( 'status', $new_status );
						do_action( 'ywsbs_customer_subscription_paused_mail', $this );
						YITH_WC_Activity()->add_activity( $this->id, 'paused', 'success', $this->order_id, sprintf( __( 'Subscription paused. %s', 'yith-woocommerce-subscription' ), $from_text ) );

						break;
					case 'resume':
						$this->set( 'expired_pause_date', '' );

						// change payment_due_date
						$offset           = $this->get_payment_due_date_paused_offset();
						$payment_due_date = $this->payment_due_date + $offset;

						$this->set( 'sum_of_pauses', $this->sum_of_pauses + $offset );
						$this->set( 'payment_due_date', $payment_due_date );

						if ( $this->expired_date ) {
							// shift expiry date
							$this->set( 'expired_date', $this->expired_date + $offset );
						}

						// Update the subscription status
						$this->set( 'status', 'active' );
						do_action( 'ywsbs_customer_subscription_resumed_mail', $this );
						YITH_WC_Activity()->add_activity( $this->id, 'resumed', 'success', $this->order_id, sprintf( __( 'Subscription resumed. Payment due on %1$s. %2$s', 'yith-woocommerce-subscription' ), date_i18n( wc_date_format(), $payment_due_date ), $from_text ) );

						break;

					case 'overdue':
						// Update the subscription status
						$this->set( 'status', $new_status );
						do_action( 'ywsbs_customer_subscription_request_payment_mail', $this );
						YITH_WC_Activity()->add_activity( $this->id, 'overdue', 'success', $this->order_id, __( 'Overdue subscription.', 'yith-woocommerce-subscription' ) );
						break;

					case 'trial':
						if ( $old_status == 'cancelled' ) {
							$this->set( 'end_date', $this->payment_due_date );
							$this->set( 'payment_due_date', '' );
							do_action( 'ywsbs_no_activated_just_cancelled', $this );

							return false;
						} else {
							$this->set( 'status', $new_status );
							YITH_WC_Activity()->add_activity( $this->id, 'trial', 'success', $this->order_id, sprintf( __( 'Started a trial period of %1$s %2$s', 'yith-woocommerce-subscription' ), $this->trial_per, $this->trial_time_option ) );
						}
						break;

					case 'cancelled':
						// if the subscription is cancelled the payment_due_date become the expired_date
						// the subscription will be actin until the date of the next payment

						$this->set( 'end_date', $this->payment_due_date );
						$this->set( 'payment_due_date', '' );
						$this->set( 'cancelled_date', current_time( 'timestamp' ) );
						$this->set( 'status', $new_status );
						$this->cancel_renew_order();
						do_action( 'ywsbs_customer_subscription_cancelled_mail', $this );
						YITH_WC_Activity()->add_activity( $this->id, 'cancelled', 'success', $this->order_id, sprintf( __( 'The subscription has been cancelled. %s', 'yith-woocommerce-subscription' ), $from_text ) );
						break;
					case 'cancel-now':
						// if the subscription is cancelled now the end_date is the current timestamp
						$new_status = 'cancelled';
						$tstamp     = current_time( 'timestamp' );
						$this->set( 'end_date', $tstamp );
						$this->set( 'payment_due_date', '' );
						$this->set( 'cancelled_date', $tstamp );
						$this->set( 'status', $new_status );

						$this->cancel_renew_order();
						do_action( 'ywsbs_customer_subscription_cancelled_mail', $this );

						YITH_WC_Activity()->add_activity( $this->id, 'cancelled', 'success', $this->order_id, sprintf( __( 'The subscription has been NOW cancelled. %s', 'yith-woocommerce-subscription' ), $from_text ) );
						break;
					case 'expired':
						$this->set( 'status', $new_status );
						do_action( 'ywsbs_customer_subscription_expired_mail', $this );
						YITH_WC_Activity()->add_activity( $this->id, 'expired', 'success', $this->order_id, __( 'Subscription expired.', 'yith-woocommerce-subscription' ) );
						break;
					case 'suspended':
						$this->set( 'status', $new_status );
						do_action( 'ywsbs_customer_subscription_suspended_mail', $this );
						YITH_WC_Activity()->add_activity( $this->id, 'suspended', 'success', $this->order_id, __( 'Subscription suspended.', 'yith-woocommerce-subscription' ) );
						break;
					default:
				}

				// Status was changed
				do_action( 'ywsbs_subscription_status_' . $new_status, $this->id );
				do_action( 'ywsbs_subscription_status_' . $old_status . '_to_' . $new_status, $this->id );
				do_action( 'ywsbs_subscription_status_changed', $this->id, $old_status, $new_status );
				do_action( 'ywsbs_subscription_admin_mail', $this );

				return true;
			}

			return false;
		}


		/**
		 * Change the status of renew order if exists
		 *
		 * @author Emanuela Castorina
		 */
		public function cancel_renew_order() {
			if ( $this->renew_order ) {
				$order = wc_get_order( $this->renew_order );
				$order->update_status( 'cancelled' );
			}
		}

		/**
		 * Get the next payment due date.
		 *
		 * If paused, calculate the next date for payment, checking
		 */
		public function get_payment_due_date_paused_offset() {
			if ( 'paused' != $this->status ) {
				return 0;
			}

			$date_pause = $this->date_of_pauses;
			$last       = ( $date_pause[ count( $date_pause ) - 1 ] );
			$offset     = current_time( 'timestamp' ) - $last;

			return $offset;
		}

		/**
		 * Return the subscription recurring price formatted
		 *
		 * @param string $tax_display
		 *
		 * @return  string
		 * @since   1.0.0
		 */
		public function get_formatted_recurring( $tax_display = '', $show_time_option = true ) {
			$price_time_option_string = ywsbs_get_price_per_string( $this->get( 'price_is_per' ), $this->get( 'price_time_option' ) );

			$tax_inc = get_option( 'woocommerce_prices_include_tax' ) === 'yes';

			if ( wc_tax_enabled() && $tax_inc ) {
				$sbs_price = $this->get( 'line_total' ) + $this->get( 'line_tax' );

			} else {
				$sbs_price = $this->get( 'line_total' );
			}

			$recurring  = wc_price( $sbs_price, array( 'currency' => $this->get( 'order_currency' ) ) );
			$recurring .= $show_time_option ? ' / ' . $price_time_option_string : '';

			return apply_filters( 'ywsbs-recurring-price', $recurring, $this );
		}


		/**
		 * Return the subscription detail page url
		 *
		 * @param bool $admin
		 *
		 * @return  string
		 * @since   1.0.0
		 */
		public function get_view_subscription_url( $admin = false ) {

			if ( $admin ) {
				$view_subscription_url = admin_url( 'post.php?post=' . $this->id . '&action=edit' );
			} else {
				$view_subscription_url = wc_get_endpoint_url( 'view-subscription', $this->id, wc_get_page_permalink( 'myaccount' ) );
			}

			return apply_filters( 'ywsbs_get_subscription_url', $view_subscription_url, $this->id, $admin );
		}

		/**
		 * Return if the subscription can be stopped by user
		 *
		 * @return  bool
		 * @since   1.0.0
		 */
		public function can_be_paused() {

			$pause_info = $this->get_subscription_product_pause_options();

			if ( $pause_info['allow_pause'] == 'yes' && $pause_info['max_pause'] && $this->num_of_pauses < $pause_info['max_pause'] ) {
				return true;
			}

			if ( $pause_info['allow_pause'] != 'yes' || $this->status != 'active' ) {
				return false;
			}

			return false;
		}


		/**
		 * Return if the subscription can be set active
		 *
		 * @return  bool
		 * @since   1.0.0
		 */
		public function can_be_active() {
			$status = array( 'pending', 'overdue', 'suspended', 'cancelled' );

			// the administrator and shop manager can switch the status to cancelled
			$post_type_object = get_post_type_object( YITH_WC_Subscription()->post_name );
			if ( current_user_can( $post_type_object->cap->delete_post, $this->ID ) && in_array( $this->status, $status ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Return if the subscription can be set as suspended
		 *
		 * @return  bool
		 * @since   1.0.0
		 */
		public function can_be_suspended() {

			if ( ! YITH_WC_Subscription()->suspension_time() ) {
				return false;
			}

			$status = array( 'active', 'overdue', 'cancelled' );

			// the administrator and shop manager can switch the status to suspended
			$post_type_object = get_post_type_object( YITH_WC_Subscription()->post_name );
			if ( current_user_can( $post_type_object->cap->delete_post, $this->ID ) && in_array( $this->status, $status ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Return if the subscription can be set as suspended
		 *
		 * @return  bool
		 * @since   1.0.0
		 */
		public function can_be_overdue() {

			if ( ! YITH_WC_Subscription()->overdue_time() ) {
				return false;
			}

			$status = array( 'active', 'suspended', 'cancelled' );

			// the administrator and shop manager can switch the status to cancelled
			$post_type_object = get_post_type_object( YITH_WC_Subscription()->post_name );
			if ( current_user_can( $post_type_object->cap->delete_post, $this->ID ) && in_array( $this->status, $status ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Return if the subscription can be resumed by user
		 *
		 * @return  bool
		 * @since   1.0.0
		 */
		public function can_be_resumed() {
			if ( $this->status != 'paused' ) {
				return false;
			} else {
				return true;
			}
		}

		/**
		 * Return if the subscription can be cancelled by user
		 *
		 * @return  bool
		 * @since   1.0.0
		 */
		public function can_be_cancelled() {
			$status = array( 'pending', 'overdue', 'suspended', 'cancelled' );

			// the administrator and shop manager can switch the status to cancelled
			$post_type_object = get_post_type_object( YITH_WC_Subscription()->post_name );
			if ( current_user_can( $post_type_object->cap->delete_post, $this->ID ) ) {
				$return = true;
			} elseif ( ! in_array( $this->status, $status ) && get_option( 'ywsbs_allow_customer_cancel_subscription' ) == 'yes' ) {
				$return = true;
			} else {
				$return = false;
			}

			return apply_filters( 'ywsbs_can_be_cancelled', $return, $this );
		}

		/**
		 * Return if the subscription can be reactivate by user
		 *
		 * @return  bool
		 * @since   1.0.0
		 */
		public function can_be_renewed() {
			$status = array( 'cancelled', 'expired' );

			if ( in_array( $this->status, $status ) && get_option( 'ywsbs_allow_customer_renew_subscription' ) == 'yes' ) {
				return true;
			} else {
				return false;
			}

		}

		/**
		 * Return if the subscription can be edited
		 *
		 * @return  bool
		 * @since   1.0.0
		 */
		public function can_be_editable( $key ) {
			$is_editable = false;
			$status      = array( 'cancelled', 'expired' );
			$gateway     = ywsbs_get_payment_gateway_by_subscription( $this );

			switch ( $key ) {
				case 'payment_date':
					if ( ! in_array( $this->status, $status ) && $gateway && $gateway->supports( 'yith_subscriptions_payment_date' ) ) {
						$is_editable = true;
					}
					break;
				case 'recurring_amount':
					if ( ! in_array( $this->status, $status ) && $gateway && $gateway->supports( 'yith_subscriptions_recurring_amount' ) ) {
						$is_editable = true;
					}
					break;
				default:
			}

			return apply_filters( 'ywsbs_subscription_is_editable', $is_editable, $key, $this );
		}

		/**
		 * Return if the shipping address can be edited.
		 *
		 * @return bool
		 * @since  1.4.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function can_edit_shipping() {
			$status = array( 'active', 'suspended', 'overdue' );

			return ( $this->needs_shipping() && in_array( $this->status, $status ) );
		}

		/**
		 * Check if the subscription must be shipping
		 *
		 * @return bool
		 * @since  1.4.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function needs_shipping() {
			return ! empty( $this->subscriptions_shippings ) && apply_filters( 'ywsbs_edit_shipping_address', true, $this );
		}

		/**
		 * Get method of payment
		 *
		 * @return mixed|string
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function get_payment_method() {
			return apply_filters( 'ywsbs_get_payment_method', $this->payment_method, $this );
		}

		/**
		 * Return if the subscription can be reactivate by user
		 *
		 * @return  bool
		 * @since   1.0.0
		 */
		public function can_be_create_a_renew_order() {
			$status = array( 'pending', 'expired' );

			// exit if no valid subscription status
			if ( in_array( $this->status, $status ) || $this->payment_due_date == $this->expired_date ) {
				yith_subscription_log( 'a renew order cannot created because the subscription is  ' . $this->status, 'subscription_payment' );

				return false;
			}

			// check if the subscription have a renew order
			$renew_order = $this->has_a_renew_order();

			// if order doesn't exist, or is cancelled, we create order
			if ( ! $renew_order || ( $renew_order && ( $renew_order->get_status() == 'cancelled' ) ) ) {
				$result = true;
			} // otherwise we return order id
			else {
				$result = yit_get_order_id( $renew_order );
			}

			return apply_filters( 'ywsbs_can_be_create_a_renew_order', $result, $this );
		}

		/**
		 * Return the renew order if exists
		 *
		 * @return  bool|WC_Order
		 * @since   1.1.5
		 */
		public function has_a_renew_order() {

			$return         = false;
			$renew_order_id = $this->renew_order;

			if ( $renew_order_id ) {
				$order            = wc_get_order( $renew_order_id );
				$order && $return = $order;
			}

			return $return;
		}

		/**
		 * Return if the subscription can be switchable
		 *
		 * @return  bool
		 * @since   1.0.0
		 */
		public function can_be_switchable() {

			$status = array( 'pending', 'paused', 'cancelled', 'expired' );

			if ( ! $this->variation_id || in_array( $this->status, $status ) ) {
				return false;
			}

			if ( get_post_meta( $this->id, 'ywsbs_switched', true ) == 'yes' ) {
				return false;
			}

			$variation = wc_get_product( $this->variation_id );

			if ( isset( $variation->parent ) ) {
				$parent_id  = yit_get_base_product_id( $variation );
				$product    = wc_get_product( $parent_id );
				$variations = $product->get_available_variations();

				$available_switch = array();

				if ( ! empty( $variations ) ) {

					$current_subscription_priority = yit_get_prop( $variation, '_ywsbs_switchable_priority' );
					foreach ( $variations as $item ) {
						$current_subscription_days_of_activity = ywsbs_get_days( $this->get_activity_period() );

						if ( $item['variation_id'] != $this->variation_id && $item['is_subscription'] && $item['is_switchable'] && $item['variation_is_visible'] && $item['variation_is_active'] && $item['is_purchasable'] ) {
							$item_product     = wc_get_product( $item['variation_id'] );
							$max_length       = yit_get_prop( $item_product, '_ywsbs_max_length' );
							$time_option      = yit_get_prop( $item_product, '_ywsbs_price_time_option' );
							$prorate_length   = yit_get_prop( $item_product, '_ywsbs_prorate_length' );
							$duration_in_days = '';

							if ( $max_length != '' ) {
								$duration         = ywsbs_get_timestamp_from_option( 0, $max_length, $time_option );
								$duration_in_days = ywsbs_get_days( $duration );
							}

							$gap_payment = yit_get_prop( $item_product, '_ywsbs_gap_payment' );
							$priority    = yit_get_prop( $item_product, '_ywsbs_switchable_priority' );
							// if is an upgrade and if the admin has checked gap payment the choice must be showed to the user
							if ( $priority >= $current_subscription_priority && $gap_payment == 'yes' ) {
								$item['has_gap_payment'] = 'yes';
								$item['gap']             = $this->calculate_gap_payment( $item['variation_id'] );
							}

							if ( $prorate_length == 'yes' && ( $max_length == '' || $duration_in_days >= $current_subscription_days_of_activity ) ) {
								$available_switch[] = $item;
							} elseif ( $prorate_length == 'no' ) {
								$available_switch[] = $item;
							}
						}
					}

					return $available_switch;
				}
			}

			return false;

		}

		/**
		 * Calculate the gap payment in the upgrade processing
		 *
		 * @param int $variation_id
		 *
		 * @return  float
		 * @since   1.0.0
		 */
		function calculate_gap_payment( $variation_id ) {

			$activity_period = $this->get_activity_period();
			$variation       = wc_get_product( $variation_id );
			$time_option     = yit_get_prop( $variation, '_ywsbs_price_time_option' );
			$num_old_rates   = ceil( $activity_period / ywsbs_get_timestamp_from_option( 0, 1, $time_option ) );
			$var_price       = ( $variation->get_price() - ( $this->line_total + $this->line_tax ) ) * $num_old_rates;

			return ( $var_price > 0 ) ? $var_price : 0;
		}

		/**
		 * Change the total amount meta on a subscription after a change without
		 * recalculate taxes.
		 */
		public function calculate_totals_from_changes() {
			$changes = array();

			$changes['order_subtotal']     = floatval( $this->get( 'line_total' ) ) + floatval( $this->get( 'line_tax' ) );
			$changes['subscription_total'] = floatval( $this->get( 'order_shipping' ) ) + floatval( $this->get( 'order_shipping_tax' ) ) + $changes['order_subtotal'];
			$changes['order_total']        = $changes['subscription_total'];
			$changes['line_subtotal']      = round( floatval( $this->get( 'line_total' ) ) / $this->get( 'quantity' ), wc_get_price_decimals() );

			$changes['line_subtotal_tax'] = round( floatval( $this->get( 'line_tax' ) ) / $this->get( 'quantity' ), wc_get_price_decimals() );

			$changes['line_tax_data'] = array(
				'subtotal' => array( $changes['line_subtotal_tax'] ),
				'total'    => array( $this->get( 'line_tax' ) ),
			);

			$this->update_subscription_meta( $changes );
		}

		/**
		 * Update the subscription prices by admin.
		 *
		 * @param $posted
		 *
		 * @since 1.4.5
		 */
		public function update_prices( $posted ) {

			$new_values = array();
			$old_values = array();

			if ( isset( $posted['ywsbs_quantity'] ) ) {
				$new_values['quantity'] = $posted['ywsbs_quantity'];
				$old_values['quantity'] = $this->get( 'quantity' );
			}

			if ( isset( $posted['ywsbs_line_total'] ) ) {
				$new_values['line_total'] = floatval( $posted['ywsbs_line_total'] );
				$old_values['line_total'] = floatval( $this->get( 'line_total' ) );
			}

			if ( isset( $posted['ywsbs_line_tax'] ) ) {
				$new_values['line_tax'] = floatval( $posted['ywsbs_line_tax'] );
				$old_values['line_tax'] = floatval( $this->get( 'line_tax' ) );
			}

			if ( isset( $posted['ywsbs_shipping_cost_line_cost'] ) ) {
				$new_values['order_shipping'] = floatval( $posted['ywsbs_shipping_cost_line_cost'] );
				$old_values['order_shipping'] = floatval( $this->get( 'order_shipping' ) );
			}

			if ( isset( $posted['ywsbs_shipping_cost_line_tax'] ) ) {
				$new_values['order_shipping_tax'] = floatval( $posted['ywsbs_shipping_cost_line_tax'] );
				$old_values['order_shipping_tax'] = floatval( $this->get( 'order_shipping_tax' ) );
			}

			$changes = array_diff_assoc( $new_values, $old_values );

			if ( $changes ) {
				$message = '';
				foreach ( $changes as $key => $change ) {

					$currency = 'quantity' != $key ? get_woocommerce_currency_symbol( $this->get( 'order_currency' ) ) : '';

					$message .= sprintf( __( '%1$s from %2$s to %3$s', 'yith-woocommerce-subscription' ), str_replace( '_', ' ', $key ), $old_values[ $key ] . "{$currency}", $new_values[ $key ] . "{$currency}<br>" );
				}

				YITH_WC_Activity()->add_activity( $this->id, 'changed', $status = 'success', $order = 0, sprintf( __( 'Changed %s ', 'yith-woocommerce-subscription' ), $message ) );
			}
			// Save the array of shipping
			$new_values['subscriptions_shippings'] = $this->get( 'subscriptions_shippings' );

			if ( isset( $posted['ywsbs_shipping_method_name'] ) ) {
				$new_values['subscriptions_shippings']['name'] = $posted['ywsbs_shipping_method_name'];
			}
			if ( isset( $new_values['order_shipping'] ) ) {
				$new_values['subscriptions_shippings']['cost'] = $new_values['order_shipping'];
			}

			$changes['subscriptions_shippings'] = $new_values['subscriptions_shippings'];

			if ( $changes ) {
				$this->update_subscription_meta( $changes );
			}

			$this->calculate_totals_from_changes();
		}

		/**
		 * Calculate subscription total from other total meta data.
		 *
		 * @return mixed|void
		 * @since  1.4.5
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function recalculate_prices() {
			$this->calculate_taxes();
			$this->calculate_totals_from_changes();
		}

		/**
		 * @return array|bool|int
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		private function calculate_taxes() {
			$subtotal          = $total = $shipping_tax = 0;
			$calculate_tax_for = $this->get_tax_location();
			$subtotal_taxes    = $taxes = array();
			$product           = wc_get_product( $this->get( 'product_id' ) );
			$shipping_data     = $this->get( 'subscriptions_shippings' );

			if ( ! isset( $calculate_tax_for['country'], $calculate_tax_for['state'], $calculate_tax_for['postcode'], $calculate_tax_for['city'] ) ) {
				return false;
			}

			if ( wc_tax_enabled() ) {
				$tax_rates              = WC_Tax::find_shipping_rates( $calculate_tax_for );
				$taxes                  = WC_Tax::calc_tax( $this->get( 'order_shipping' ), $tax_rates, false );
				$shipping_data['taxes'] = $taxes;
				$shipping_tax           = $taxes ? array_sum( $taxes ) : 0;
			}

			if ( '0' !== $product->get_tax_class() && 'taxable' === $product->get_tax_status() && wc_tax_enabled() ) {
				$calculate_tax_for['tax_class'] = $product->get_tax_class();
				$tax_rates                      = WC_Tax::find_rates( $calculate_tax_for );
				$taxes                          = WC_Tax::calc_tax( $this->get( 'line_total' ), $tax_rates, false );
				$subtotal_taxes                 = WC_Tax::calc_tax( $this->get( 'line_subtotal' ), $tax_rates, false );
				$subtotal                       = $subtotal_taxes ? array_sum( $subtotal_taxes ) : 0;
				$total                          = $taxes ? array_sum( $taxes ) : 0;
			}

			$this->set( 'line_tax', $total );
			$this->set( 'order_tax', $total );
			$this->set( 'order_shipping_tax', $shipping_tax );
			$this->set( 'subscriptions_shippings', $shipping_data );
			$this->set( 'line_subtotal_tax', $subtotal );
			$this->set(
				'line_tax_data',
				array(
					'subtotal' => $subtotal_taxes,
					'total'    => $taxes,
				)
			);
		}

		/**
		 * Cancel the subscription
		 *
		 * @param bool $now
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		function cancel( $now = true ) {

			if ( $now ) {
				$this->update_status( 'cancel-now' );
			} else {
				$this->update_status( 'cancelled' );
			}

			// Change the status to active

			do_action( 'ywsbs_subscription_cancelled', $this->id );

			// if there's a pending order for this subscription change the status of the order to cancelled
			if ( $this->renew_order ) {
				$order = wc_get_order( $this->renew_order );
				if ( $order ) {
					$order->update_status( 'cancelled' );
					$order->add_order_note( sprintf( __( 'This order has been cancelled because subscription #%d has been cancelled', 'yith-woocommerce-subscription' ), $this->id ) );
				}
			}
		}

		/**
		 * Delete the subscription
		 *
		 * @since  1.0.0
		 */
		public function delete() {
			do_action( 'ywsbs_before_subscription_deleted', $this->id );
			wp_delete_post( $this->id, true );
			do_action( 'ywsbs_subscription_deleted', $this->id );
		}

		/**
		 * Return an array of all custom fields subscription
		 *
		 * @return array
		 * @since  1.0.0
		 */
		private function get_default_meta_data() {
			$subscription_meta_data = array(
				'status'                  => 'pending',
				'start_date'              => '',
				'payment_due_date'        => '',
				'expired_date'            => '',
				'cancelled_date'          => '',
				'end_date'                => '',
				// pauses
				'num_of_pauses'           => 0,
				'date_of_pauses'          => array(),
				'expired_pause_date'      => '',
				'sum_of_pauses'           => '',
				// paypal
				'paypal_subscriber_id'    => '',
				'paypal_transaction_id'   => '',
				'payed_order_list'        => array(),
				'product_id'              => '',
				'variation_id'            => '',
				'variation'               => '',
				'product_name'            => '',
				'quantity'                => '',
				'line_subtotal'           => '',
				'line_total'              => '',
				'line_subtotal_tax'       => '',
				'line_tax'                => '',
				'line_tax_data'           => '',
				'cart_discount'           => '',
				'cart_discount_tax'       => '',
				'coupons'                 => '',
				'order_total'             => '',
				'order_subtotal'          => '',
				'order_tax'               => '',
				'order_discount'          => '',
				'order_shipping'          => '',
				'order_shipping_tax'      => '',
				'order_currency'          => '',
				'renew_order'             => 0,
				'prices_include_tax'      => '',
				'payment_method'          => '',
				'payment_method_title'    => '',
				'transaction_id'          => '',
				'subscriptions_shippings' => '',
				'subscription_total'      => '',
				'price_is_per'            => '',
				'price_time_option'       => '',
				'max_length'              => '',
				'trial_per'               => '',
				'trial_time_option'       => '',
				'fee'                     => '',
				'num_of_rates'            => '',
				'rates_payed'             => '',
				'order_ids'               => array(),
				'order_id'                => '',
				'order_item_id'           => '',
				'user_id'                 => 0,
				'customer_ip_address'     => '',
				'customer_user_agent'     => '',
			);

			return $subscription_meta_data;
		}

		/**
		 * Return an array of pause options
		 *
		 * @return array|void
		 * @since  1.0.0
		 */
		private function get_subscription_product_pause_options() {
			$id      = ( $this->variation_id ) ? $this->variation_id : $this->product_id;
			$product = wc_get_product( $id );
			if ( ! $product ) {
				return;
			}
			$product_pause_options['max_pause']          = yit_get_prop( $product, '_ywsbs_max_pause' );
			$product_pause_options['max_pause_duration'] = yit_get_prop( $product, '_ywsbs_max_pause_duration' );

			if ( $product_pause_options['max_pause'] ) {
				$product_pause_options['allow_pause'] = true;
			} else {
				$product_pause_options['allow_pause'] = false;
			}

			return $product_pause_options;
		}

		/**
		 * Return the a link for change the status of subscription
		 *
		 * @param string $status
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function get_change_status_link( $status ) {

			$action_link = add_query_arg(
				array(
					'subscription'  => $this->id,
					'change_status' => $status,
				)
			);
			$action_link = wp_nonce_url( $action_link, $this->id );

			return apply_filters( 'ywsbs_change_status_link', $action_link, $this, $status );
		}

		/**
		 * Return the next payment due date if there are rates not payed
		 *
		 * @param int $trial_period
		 *
		 * @param int $start_date
		 *
		 * @return bool|int|string
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function get_next_payment_due_date( $trial_period = 0, $start_date = 0 ) {

			$start_date = ( $start_date ) ? $start_date : current_time( 'timestamp' );

			$rates_payed = ! empty( $this->rates_payed ) ? $this->rates_payed : 0;
			if ( $this->num_of_rates == '' || ( $this->num_of_rates - $rates_payed ) > 0 ) {
				$payment_due_date = ( $this->payment_due_date == '' ) ? $start_date : $this->payment_due_date;
				if ( $trial_period != 0 ) {
					$timestamp = $start_date + $trial_period;
				} else {
					$timestamp = ywsbs_get_timestamp_from_option( $payment_due_date, $this->price_is_per, $this->price_time_option );
				}

				return $timestamp;
			}

			return false;

		}

		/**
		 * Return the next payment due date if there are rates not payed
		 *
		 * @return int
		 * @since  1.0.0
		 */
		public function get_left_time_to_next_payment() {

			$left_time = 0;

			if ( $this->payment_due_date ) {
				$left_time = $this->payment_due_date - current_time( 'timestamp' );
			} elseif ( $this->expired_date ) {
				$left_time = $this->expired_date - current_time( 'timestamp' );
			}

			return $left_time;
		}

		/**
		 * Return the timestamp from activation of subscription escluding pauses
		 *
		 * @param bool $exclude_pauses
		 *
		 * @return float|int
		 * @since  1.0.0
		 */
		public function get_activity_period( $exclude_pauses = true ) {
			$timestamp = current_time( 'timestamp' ) - intval( $this->start_date );
			if ( $exclude_pauses && $this->sum_of_pauses ) {
				$timestamp -= $this->sum_of_pauses;
			}

			return abs( $timestamp );
		}

		/**
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @deprecated
		 */
		public function has_failed_attemps() {
			$this->has_failed_attempts();
		}

		/**
		 * Return an array with the details of failed attempts.
		 *
		 * @return array|bool
		 */
		public function has_failed_attempts() {
			$return = false;
			$order  = wc_get_order( $this->order_id );

			if ( ! $order ) {
				return $return;
			}

			$payment_method = $order->get_payment_method();
			$renew_order    = $this->renew_order ? wc_get_order( $this->renew_order ) : false;

			$order_ref            = ( $renew_order && ywsbs_support_scheduling( $payment_method ) ) ? $renew_order : $order;
			$failed_attempts      = $order_ref->get_meta( 'failed_attemps' );
			$next_payment_attempt = $order_ref->get_meta( 'next_payment_attempt' );

			$max_attempts         = ywsbs_get_max_failed_attempts_by_gateway( $payment_method );
			$gap_between_attempts = ywsbs_get_num_of_days_between_attempts_by_gateway( $payment_method );

			$return = array(
				'num_of_failed_attempts' => $failed_attempts,
				'max_failed_attempts'    => $max_attempts,
				'day_between_attempts'   => $gap_between_attempts,
				'next_payment_attempt'   => $next_payment_attempt,
			);

			return $return;
		}

		/**
		 * Get subscription customer billing or shipping fields.
		 *
		 * @param string  $type
		 * @param boolean $no_type
		 *
		 * @return array
		 */
		public function get_address_fields( $type = 'billing', $no_type = false, $prefix = '' ) {

			$indentation = '--------';
			$message     = $indentation . 'Check for ' . $type;
			yith_subscription_log( $message, 'subscription_payment' );

			$fields         = array();
			$value_to_check = $this->get( '_' . $type . '_first_name' );

			if ( empty( $value_to_check ) || apply_filters( 'yith_subscription_get_address_by_order', false ) ) {
				$fields = $this->get_address_fields_from_order( $type, $no_type, $prefix );
			} else {
				$meta_fields = ywsbs_get_order_fields_to_edit( $type );
				$order       = $this->get_order();
				if ( $order instanceof WC_Order ) {
					$meta_fields = $order->get_address( $type );
				}

				$message = $indentation . ' Get the information from subscription #' . $this->get_id() . ' with user ' . $this->user_id . '( Order customer: ' . $order->get_user_id() . ' )';
				yith_subscription_log( $message, 'subscription_payment' );

				foreach ( $meta_fields as $key => $value ) {
					$field_key = $no_type ? $key : $type . '_' . $key;

					$fields[ $prefix . $field_key ] = $this->get( '_' . $type . '_' . $key );
					$message                        = $indentation . $indentation . $fields[ $prefix . $field_key ] . ' ' . $this->get( $field_key );
					yith_subscription_log( $message, 'subscription_payment' );
				}
			}

			return $fields;
		}

		/**
		 * Return the fields billing or shipping of the parent order
		 *
		 * @param string $type
		 * @param bool   $no_type
		 *
		 * @return array
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function get_address_fields_from_order( $type = 'billing', $no_type = false, $prefix = '' ) {
			$fields = array();
			$order  = $this->get_order();

			if ( ! $order ) {
				return $fields;
			}

			yith_subscription_log( '-------- ' . $type . ' for ' . $order->get_id() . ' and user ' . $order->get_customer_id() . ' (' . $order->get_user_id() . ' ) ', 'subscription_payment' );
			if ( $order ) {
				$meta_fields = $order->get_address( $type );

				if ( is_array( $meta_fields ) ) {
					foreach ( $meta_fields as $key => $value ) {
						$field_key                      = $no_type ? $key : $type . '_' . $key;
						$fields[ $prefix . $field_key ] = $value;
					}
				}
			}

			return $fields;
		}

		/**
		 * Return the customer order note of subscription or parent order.
		 *
		 * @return mixed
		 * @since  1.4.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function get_customer_order_note() {
			$order         = wc_get_order( $this->order_id );
			$customer_note = $this->customer_note;
			if ( $order && empty( $customer_note ) ) {
				$customer_note = $order->get_customer_note();
			}

			return $customer_note;
		}

		/**
		 * Get billing customer email
		 *
		 * @return string
		 */
		public function get_billing_email() {
			$order         = wc_get_order( $this->order_id );
			$billing_email = ! empty( $this->billing_email ) ? $this->billing_email : yit_get_prop( $order, 'billing_email' );

			return apply_filters( 'ywsbs_customer_billing_email', $billing_email, $this );
		}

		/**
		 * Get billing customer email
		 *
		 * @return string
		 */
		public function get_billing_phone() {
			$order         = wc_get_order( $this->order_id );
			$billing_phone = ! empty( $this->billing_phone ) ? $this->billing_phone : yit_get_prop( $order, 'billing_phone' );

			return apply_filters( 'ywsbs_customer_billing_phone', $billing_phone, $this );
		}

		/**
		 * Get the order object.
		 *
		 * @return
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function get_order() {
			$this->order = ! is_null( $this->order ) ? $this->order : wc_get_order( $this->get( 'order_id' ) );

			return $this->order;
		}

		/**
		 * Get the product object.
		 *
		 * @return WC_Product
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function get_product() {
			$variation_id  = $this->get( 'variation_id' );
			$this->product = wc_get_product( ( isset( $variation_id ) && ! empty( $variation_id ) ) ? $variation_id : $this->get( 'product_id' ) );

			return $this->product;
		}

		/**
		 * Update the payment method after that the order is completed
		 *
		 * @return string
		 */
		public function update_payment_method() {
			$order = wc_get_order( $this->order_id );

			if ( ! $order ) {
				return;
			}

			$this->payment_method       = yit_get_prop( $order, '_payment_method' );
			$this->payment_method_title = yit_get_prop( $order, '_payment_method_title' );
		}

		/**
		 * Add failed attempt
		 *
		 * @param bool $attempts
		 * @param bool $latest_attempt if is the last attempt doesn't send email
		 *
		 * @since      1.1.3
		 * @author     Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @deprecated use register_failed_attempt instead
		 */
		public function register_failed_attemp( $attempts = false, $latest_attempt = false ) {
			$this->register_failed_attempt( $attempts, $latest_attempt );
		}

		/**
		 * Register a failed attempt on the parent order of a subscription.
		 *
		 * @param bool $attempts
		 * @param bool $latest_attempt if is the last attempt doesn't send email
		 *
		 * @since  1.1.3
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function register_failed_attempt( $attempts = false, $latest_attempt = false, $next_attempt_date = '' ) {
			$order_id = $this->get( 'order_id' );
			$order    = wc_get_order( $order_id );

			if ( false === $attempts ) {
				$failed_attempt = $order->get_meta( 'failed_attemps' );
				$attempts       = intval( $failed_attempt ) + 1;
			}

			if ( ! $latest_attempt ) {
				YITH_WC_Activity()->add_activity( $this->id, 'failed-payment', 'success', $order_id, sprintf( __( 'Failed payment for order %d', 'yith-woocommerce-subscription' ), $order_id ) );
				$order->update_meta_data( 'failed_attemps', $attempts );
				// DO_ACTION : ywsbs_customer_subscription_payment_failed_mail : do action when the subscription is failed
				do_action( 'ywsbs_customer_subscription_payment_failed_mail', $this );
			}

			if ( ! empty( $next_attempt_date ) ) {
				$order->update_meta_data( 'next_payment_attempt', $next_attempt_date );
			}

			$order->save();

			// Suspend the subscription if is activated
			if ( 'yes' == get_option( 'ywsbs_suspend_for_failed_recurring_payment' ) ) {
				if ( $this->get( 'status' ) != 'suspended' ) {
					$this->update_status( 'suspended', $this->get_payment_method() );
					YITH_WC_Subscription()->log( sprintf( __( 'Subscription suspended. Order %1$s. Subscription %2$s', 'yith-paypal-express-checkout-for-woocommerce' ), $order_id, $this->id ) );
				}
			}

		}

		/**
		 * Get tax location for this order.
		 *
		 * @param array $args array Override the location.
		 *
		 * @return array
		 * @since 1.4.5
		 */
		protected function get_tax_location( $args = array() ) {
			$tax_based_on = get_option( 'woocommerce_tax_based_on' );

			$shipping_fields = $this->get_address_fields( 'shipping' );
			$billing_fields  = $this->get_address_fields( 'billing' );

			if ( 'shipping' === $tax_based_on && ! $shipping_fields['shipping_country'] ) {
				$tax_based_on = 'billing';
			}

			$args = wp_parse_args(
				$args,
				array(
					'country'  => 'billing' === $tax_based_on ? $billing_fields['billing_country'] : $shipping_fields['shipping_country'],
					'state'    => 'billing' === $tax_based_on ? $billing_fields['billing_state'] : $shipping_fields['shipping_state'],
					'postcode' => 'billing' === $tax_based_on ? $billing_fields['billing_postcode'] : $shipping_fields['shipping_postcode'],
					'city'     => 'billing' === $tax_based_on ? $billing_fields['billing_city'] : $shipping_fields['shipping_city'],
				)
			);

			// Default to base.
			if ( 'base' === $tax_based_on || empty( $args['country'] ) ) {
				$default          = wc_get_base_location();
				$args['country']  = $default['country'];
				$args['state']    = $default['state'];
				$args['postcode'] = '';
				$args['city']     = '';
			}

			return $args;
		}

	}

}
