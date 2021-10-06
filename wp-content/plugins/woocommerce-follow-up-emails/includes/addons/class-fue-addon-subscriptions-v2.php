<?php

/**
 * Class FUE_Addon_Subscriptions_V2
 */
class FUE_Addon_Subscriptions_V2 {

	private static $instance = null;

	public function __construct() {
		self::$instance = $this;
	}

	/**
	 * Get an instance of Follow_Up_Emails
	 *
	 * @return Follow_Up_Emails
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new FUE_Addon_Subscriptions_V2();
		}

		return self::$instance;
	}

	/**
	 * Register subscription variables to be replaced
	 *
	 * @param FUE_Sending_Email_Variables   $var
	 * @param array                     $email_data
	 * @param FUE_Email                 $email
	 * @param FUE_Sending_Queue_Item    $queue_item
	 */
	public function register_variable_replacements( $var, $email_data, $email, $queue_item ) {
		if ( 'subscription' !== $email->type ) {
			return;
		}

		$variables = array(
			'subs_renew_date'       => '',
			'subs_end_date'         => '',
			'days_to_renew'         => '',
			'subs_start_date'       => '',
			'subs_trial_length'     => '',
			'subs_first_payment'    => '',
			'subs_cost_term'        => '',
			'subs_cost'             => '',
			'subs_id'               => '',
			'item_names'            => '',
			'item_names_list'       => '',
			'item_categories'       => '',
			'item_name'             => '',
			'item_quantity'         => '',
		);

		if ( 'manual' == $email->type ) {
			$variables = $this->add_manual_email_variables( $variables, $email_data, $queue_item, $email );
		} else {
			// use test data if the test flag is set
			if ( isset( $email_data['test'] ) && $email_data['test'] ) {
				$variables = $this->add_test_variable_replacements( $variables, $email_data, $email );
			} else {
				$variables = $this->add_variable_replacements( $variables, $email_data, $queue_item, $email );
			}
		}

		$var->register( $variables );
	}

	/**
	 * Apply variable replacements for manual emails
	 *
	 * @param array                     $variables
	 * @param array                     $email_data
	 * @param FUE_Sending_Queue_Item    $queue_item
	 * @param FUE_Email                 $email
	 * @return array
	 */
	protected function add_manual_email_variables( $variables, $email_data, $queue_item, $email ) {
		if ( isset( $queue_item->meta['send_type'] ) && 'active_subscription' == $queue_item->meta['send_type'] ) {
			// recipient_key is the subscription ID
			if ( empty( $queue_item->meta['recipient_key'] ) ) {
				return $variables;
			}

			$subscription = wcs_get_subscription( $queue_item->meta['recipient_key'] );

			if ( ! $subscription ) {
				return $variables;
			}

			$lists = FUE_Addon_Woocommerce::list_order_items( $subscription );
			$items = $lists['items'];

			if ( ! empty( $email->product_id ) ) {
				$subscription_items = $items;
				$items              = array();

				foreach ( $subscription_items as $item ) {
					if ( in_array( $email->product_id, array( $item['product']->product_id, $item['product']->variation_id ) ) ) {
						$item[] = $item;
						break;
					}
				}
			}

			if ( empty( $items ) ) {
				return $variables;
			}

			$item_list          = '<ul>';
			$item_cats          = '<ul>';
			$item_names         = array();

			foreach ( $items as $item ) {
				$item_names[]   = $item['name'];
				$sku            = ( ! empty( $item['sku'] ) ) ? '('. $item['sku'] .')' : '';

				$item_list .= '<li><a href="'. esc_url( $item['link'] ) .'">'. esc_html( $item['name'] ) .'</a></li>';
			}

			foreach ( $lists['categories'] as $category ) {
				$item_cats .= '<li>'. esc_html( $category ) .'</li>';
			}

			$item_list          .= '</ul>';
			$item_cats          .= '</ul>';
			$item_list_csv = implode( ', ', $item_names );

			if ( count( $items ) == 1 ) {
				$item = current( $items );
				$variables['item_name']     = $item['name'];
				$variables['item_quantity'] = $item['qty'];
				$variables['item_category'] = $item_cats;
			} else {
				$variables['item_names']             = $item_list;
				$variables['item_names_list']        = $item_list_csv;
				$variables['item_categories']        = $item_cats;
				$variables['item_category']          = $item_cats;
			}

			$data = self::get_subscription_meta( $subscription );

			$variables['subs_renew_date']   = $data['next_payment_date'];
			$variables['subs_end_date']     = $data['end_date'];
			$variables['days_to_renew']     = $data['days_to_renew'];
			$variables['subs_start_date']   = $data['start_date'];
			$variables['subs_trial_length'] = $data['trial_length'];
			$variables['subs_first_payment']= $data['first_payment_cost'];
			$variables['subs_cost_term']    = $data['cost_term'];
			$variables['subs_cost']         = $data['cost'];
			$variables['subs_id']           = $subscription->get_id();

		}

		return $variables;
	}

	/**
	 * Scan through the keys of $variables and apply the replacement if one is found
	 * @param array     $variables
	 * @param array     $email_data
	 * @param object    $queue_item
	 * @param FUE_Email $email
	 * @return array
	 */
	protected function add_variable_replacements( $variables, $email_data, $queue_item, $email ) {
		if ( empty( $queue_item->meta['subs_key'] ) ) {
			return $variables;
		}

		$subscription_id    = $queue_item->meta['subs_key'];
		$subscription       = wcs_get_subscription( $subscription_id );

		if ( !$subscription ) {
			return $variables;
		}

		$lists = FUE_Addon_Woocommerce::list_order_items( $subscription );
		$items = $lists['items'];

		if ( !empty( $email->product_id ) ) {
			$subscription_items = $items;
			$items              = array();

			foreach ( $subscription_items as $item ) {
				$item_product_id = self::subs_v_gte( '2.2.0' ) ? $item['product']->get_id() : $item['product']->id;
				$item_product_variation_id = self::subs_v_gte( '2.2.0' ) ? $item['data']->get_variation_id() : $item['product']->variation_id;
				if ( in_array( $email->product_id, array( $item_product_id, $item_product_variation_id ) ) ) {
					$items[] = $item;
					break;
				}
			}

		}

		if ( empty( $items ) ) {
			return $variables;
		}

		$item_list          = '<ul>';
		$item_cats          = '<ul>';
		$item_names         = array();

		foreach ( $items as $item ) {
			$item_names[]   = $item['name'];
			$sku            = (!empty( $item['sku'] )) ? '('. $item['sku'] .')' : '';

			$item_list .= '<li><a href="'. esc_url( $item['link'] ) .'">'. esc_html( $item['name'] ) .'</a></li>';
		}

		foreach ( $lists['categories'] as $category ) {
			$item_cats .= '<li>'. esc_html( $category ) .'</li>';
		}

		$item_list          .= '</ul>';
		$item_cats          .= '</ul>';
		$item_list_csv = implode( ', ', $item_names );
		if ( count( $items ) == 1 ) {
			$item = current( $items );
			$variables['item_name']     = $item['name'];
			$variables['item_quantity'] = $item['qty'];
			$variables['item_category'] = $item_cats;
		} else {
			$variables['item_names']             = $item_list;
			$variables['item_names_list']        = $item_list_csv;
			$variables['item_categories']        = $item_cats;
			$variables['item_category']          = $item_cats;
		}

		$data = self::get_subscription_meta( $subscription_id );

		$order_id = $subscription->get_last_order();
		$renewals = wcs_get_subscriptions_for_renewal_order( $order_id );

		if ( ! empty( $renewals ) ) {
			$renewal_order = current( $renewals );
			$variables['order_pay_url'] = fue_replacement_url_var( $renewal_order->get_checkout_payment_url() );
		}

		$variables['subs_renew_date']   = $data['next_payment_date'];
		$variables['subs_end_date']     = $data['end_date'];
		$variables['days_to_renew']     = $data['days_to_renew'];
		$variables['subs_start_date']   = $data['start_date'];
		$variables['subs_trial_length'] = $data['trial_length'];
		$variables['subs_first_payment']= $data['first_payment_cost'];
		$variables['subs_cost_term']    = $data['cost_term'];
		$variables['subs_cost']         = $data['cost'];
		$variables['subs_id']           = $subscription->get_id();

		return $variables;
	}

	/**
	 * Add variable replacements for test emails
	 *
	 * @param array     $variables
	 * @param array     $email_data
	 * @param FUE_Email $email
	 *
	 * @return array
	 */
	protected function add_test_variable_replacements( $variables, $email_data, $email ) {
		$variables['subs_start_date']   = date( wc_date_format(), time()-(86400*7) );
		$variables['subs_renew_date']   = date( wc_date_format(), time()+86400);
		$variables['subs_end_date']     = date( wc_date_format(), time()+(86400*7) );
		$variables['days_to_renew']     = 1;
		$variables['item_name']         = 'Test Subscription';
		$variables['item_quantity']     = 2;
		$variables['item_names']        = '';
		$variables['item_names_list']   = '';
		$variables['subs_trial_length']  = '1 week';
		$variables['subs_first_payment'] = date( wc_date_format(), time()+(86400*7) );;
		$variables['subs_cost_term']     = '$5 / week';
		$variables['subs_cost']          = '$5';
		$variables['subs_id']            = 1121;

		if ( !empty( $_REQUEST['subscription_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$subscription   = wcs_get_subscription( absint( $_REQUEST['subscription_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$used_cats      = array();
			$items_array    = array();
			$lists          = array(
				'items'         => array(),
				'categories'    => array(),
			);

			if ( $subscription ) {
				$lists = FUE_Addon_Woocommerce::list_order_items( $subscription );
				$items = $lists['items'];

				if ( empty( $items ) ) {
					return $variables;
				}

				$item_list          = '<ul>';
				$item_cats          = '<ul>';
				$item_names         = array();

				foreach ( $items as $item ) {
					$item_names[]   = $item['name'];
					$sku            = (!empty( $item['sku'] )) ? '('. $item['sku'] .')' : '';

					$item_list .= '<li><a href="'. esc_url( $item['link'] ) .'">'. esc_html( $item['name'] ) .'</a></li>';
				}

				foreach ( $lists['categories'] as $category ) {
					$item_cats .= '<li>'. esc_html( $category ) .'</li>';
				}

				$item_list          .= '</ul>';
				$item_cats          .= '</ul>';
				$item_list_csv = implode( ', ', $item_names );

				if ( count( $items ) == 1 ) {
					$item = current( $items );
					$variables['item_name']     = $item['name'];
					$variables['item_quantity'] = $item['qty'];
					$variables['item_category'] = $item_cats;
				} else {
					$variables['item_names']             = $item_list;
					$variables['item_names_list']        = $item_list_csv;
					$variables['item_categories']        = $item_cats;
					$variables['item_category']          = $item_cats;
				}

				$data = self::get_subscription_meta( $subscription );

				$variables['subs_renew_date']   = $data['next_payment_date'];
				$variables['subs_end_date']     = $data['end_date'];
				$variables['days_to_renew']     = $data['days_to_renew'];
				$variables['item_url']          = fue_replacement_url_var( '' );
				$variables['subs_start_date']   = $data['start_date'];
				$variables['subs_trial_length'] = $data['trial_length'];
				$variables['subs_first_payment']= $data['first_payment_cost'];
				$variables['subs_cost_term']    = $data['cost_term'];
				$variables['subs_cost']         = $data['cost'];
				$variables['subs_id']           = $subscription->get_id();

				$variables['customer_first_name']   = $subscription->get_billing_first_name();
				$variables['customer_name']         = $subscription->get_billing_first_name() .' '. $subscription->get_billing_last_name();
				$variables['customer_email']        = $subscription->get_billing_email();

				$user = $subscription->get_user();
				$variables['customer_username'] = $user->user_login;

			}
		}

		return $variables;
	}

	/**
	 * Get the users with active subscriptions for the selected product
	 *
	 * @param array $recipients
	 * @param array $post
	 *
	 * @return array
	 */
	public function manual_email_recipients( $recipients, $post ) {

		if ( $post['send_type'] == 'active_subscription' ) {
			$subscription_product = absint( $post['subscription_id'] );

			/* @var $subscriptions WC_Subscription[] */
			$subscriptions = wcs_get_subscriptions_for_product( $subscription_product, 'subscription' );

			foreach ( $subscriptions as $subscription ) {
				// The following statuses are as active subscription.
				// @see https://github.com/woocommerce/woocommerce-subscriptions/blob/2.2.18/includes/class-wc-subscription.php#L357
				if ( ! in_array( $subscription->get_status(), array( 'active', 'pending-cancel' ) ) ) {
					continue;
				}

				$user_id = get_post_meta( $subscription->get_id(), '_customer_user', true );

				$user_email = $subscription->get_billing_email();
				$first_name = $subscription->get_billing_first_name();
				$last_name  = $subscription->get_billing_last_name();

				$recipients[ $subscription->get_id() ] = array( $user_id, $user_email, $first_name . ' ' . $last_name );

			}

		}

		return $recipients;
	}

	/**
	 * Allow admin to simulate an email using real orders or products
	 * @param FUE_Email $email
	 */
	public function test_email_form( $email ) {
		if ($email->type == 'subscription') {
			include FUE_TEMPLATES_DIR .'/email-form/subscriptions/test-fields-subscriptions-v2.php';
		}
	}

	/**
	 * Trigger subscription emails based on the new status
	 *
	 * @param WC_Subscription $subscription
	 * @param string $new_status
	 * @param string $old_status
	 */
	public static function trigger_subscription_status_emails( $subscription, $new_status, $old_status ) {

		if ( $new_status == 'active' ) {
			if ( $subscription->get_suspension_count() > 0 ) {
				// reactivated
				self::subscription_reactivated( $subscription );
			}

			// activated
			self::subscription_activated( $subscription );

			self::set_renewal_reminder( $subscription );
			self::set_expiration_reminder( $subscription );
		} else {
			switch ( $new_status ) {

				case 'pending-cancel':
					self::remove_active_subscription_emails( $subscription );
					self::subscription_pending_cancellation( $subscription );
					break;

				case 'cancelled':
					// Do not cancel when previous status was pending payment (user has not signed up for subscription yet)
					if ( 'pending' !== $old_status ) {
						self::subscription_cancelled( $subscription );
					}
					break;

				case 'expired':
					self::subscription_expired( $subscription );
					break;

				case 'on-hold':
					// When transitioning from pending to on-hold, the subscription is not active yet.
					if ( 'pending' !== $old_status ) {
						self::suspended_subscription( $subscription );
					}
					break;

			}

		}

	}

	/**
	 * Update unsent follow-ups in the queue after
	 * @param WC_Subscription $wc_subscription
	 * @param string $date_type
	 * @param string $datetime
	 */
	public static function update_reminder_dates( $wc_subscription, $date_type, $datetime ) {
		$scheduler  = Follow_Up_Emails::instance()->scheduler;
		$order_id   = $wc_subscription->get_parent() ? WC_FUE_Compatibility::get_order_prop( $wc_subscription->get_parent(), 'id' ) : 0;

		if ( ! $order_id ) {
			return;
		}

		$items = $scheduler->get_items(array(
			'is_sent'   => 0,
			'order_id'  => $order_id
		));

		/* @var FUE_Sending_Queue_Item $item */
		foreach ( $items as $item ) {
			$email = new FUE_Email( $item->email_id );

			if ( ( 'end' === $date_type && 'subs_before_expire' === $email->trigger ) || ( 'next_payment' === $date_type && 'subs_before_renewal' === $email->trigger ) ) {
				// convert to local time
				$timestamp = strtotime( $datetime );

				if ( current_time('timestamp', true) > $timestamp ) {
					continue;
				}

				// add this email to the queue
				$interval   = (int)$email->interval;
				$add        = FUE_Sending_Scheduler::get_time_to_add( $interval, $email->duration );
				$send_on    = $timestamp - $add;

				$item->send_on = $send_on;
				$item->save();

				$scheduler->unschedule_email( $item->id );
				$scheduler->schedule_email( $item->id, $send_on, false );
			}
		}

	}

	/**
	 * Fired after a subscription gets activated. All unsent items in the queue
	 * with the same subscription key and the subs_cancelled and
	 * subs_suspended trigger will get deleted to avoid sending emails
	 * with incorrect subscription status
	 *
	 * @param WC_Subscription $subscription
	 */
	public static function subscription_activated( $subscription ) {
		global $wpdb;

		$product_ids    = self::get_subscription_product_ids( $subscription );
		$search_key     = self::get_queue_meta_search_string( $subscription->get_id() );

		array_push( $product_ids, 0 );

		$product_ids =  implode( ',', array_map( 'absint', $product_ids ) );

		if ( $subscription->get_payment_count() > 1 ) {
			$triggers[] = 'subs_renewed';
		}  else {
			$triggers[] = 'subs_activated';
		}

		// delete queued emails with the same product id and the 'subs_cancelled' or 'subs_suspended' trigger
		$rows = $wpdb->get_results("
			SELECT eo.id
			FROM {$wpdb->prefix}followup_email_orders eo, {$wpdb->postmeta} pm
			WHERE eo.is_sent = 0
			AND eo.product_id IN ($product_ids)
			AND eo.email_id = pm.post_id
			AND eo.meta LIKE '%{$search_key}%'
			AND pm.meta_key = '_interval_type'
			AND (
			  pm.meta_value = 'subs_cancelled' OR pm.meta_value = 'subs_suspended'
			)
		");

		if ( $rows ) {
			foreach ( $rows as $row ) {
				Follow_Up_Emails::instance()->scheduler->delete_item( $row->id );
			}
		}

		$order_id = ($subscription->get_parent()) ? WC_FUE_Compatibility::get_order_prop( $subscription->get_parent(), 'id' ) : 0;
		self::add_to_queue( $order_id, $triggers, $subscription->get_id(), $subscription->get_user_id() );

	}

	/**
	 * Fired after a subscription gets cancelled by customer
	 *
	 * @param WC_Subscription $subscription
	 */
	public static function subscription_pending_cancellation( $subscription ) {
		$order_id = ($subscription->get_parent()) ? WC_FUE_Compatibility::get_order_prop( $subscription->get_parent(), 'id' ) : 0;
		$triggers = array('subs_pending_cancel');

		// get the user's email address
		$user = new WP_User( $subscription->get_user_id() );

		self::add_to_queue($order_id, $triggers, $subscription->get_id(), $user->user_email);
	}

	/**
	 * Fired after a subscription gets cancelled
	 *
	 * @param WC_Subscription $subscription
	 */
	public static function subscription_cancelled( $subscription ) {
		self::remove_active_subscription_emails( $subscription );

		$order_id = ($subscription->get_parent()) ? WC_FUE_Compatibility::get_order_prop( $subscription->get_parent(), 'id' ) : 0;
		$triggers = array('subs_cancelled');

		// get the user's email address
		$user = new WP_User( $subscription->get_user_id() );

		self::add_to_queue($order_id, $triggers, $subscription->get_id(), $user->user_email);
	}

	/**
	 * Fired after a subscription expires.
	 *
	 * @param WC_Subscription $subscription
	 */
	public static function subscription_expired( $subscription ) {

		$order_id   = ($subscription->get_parent()) ? WC_FUE_Compatibility::get_order_prop( $subscription->get_parent(), 'id' ) : 0;
		$triggers[] = 'subs_expired';

		self::add_to_queue($order_id, $triggers, $subscription->get_id(), $subscription->get_user_id());
	}

	/**
	 * Fired after a subscription get reactivated
	 *
	 * @param WC_Subscription $subscription
	 */
	public static function subscription_reactivated( $subscription ) {
		global $wpdb;

		$order_id       = ($subscription->get_parent()) ? WC_FUE_Compatibility::get_order_prop( $subscription->get_parent(), 'id' ) : 0;
		$product_ids    = self::get_subscription_product_ids( $subscription );
		$search_key     = self::get_queue_meta_search_string( $subscription->get_id() );

		array_push( $product_ids, 0 );

		$product_ids =  implode( ',', array_map( 'absint', $product_ids ) );

		// delete queued emails with the same product id and the 'subs_cancelled' or 'subs_suspended' trigger
		$rows = $wpdb->get_results("
			SELECT eo.id
			FROM {$wpdb->prefix}followup_email_orders eo, {$wpdb->postmeta} pm
			WHERE eo.is_sent = 0
			AND eo.product_id IN ($product_ids)
			AND eo.meta LIKE '%{$search_key}%'
			AND eo.email_id = pm.post_id
			AND pm.meta_key = '_interval_type'
			AND (
			  pm.meta_value = 'subs_cancelled' OR pm.meta_value = 'subs_suspended'
			)
		" );

		if ( $rows ) {
			foreach ( $rows as $row ) {
				Follow_Up_Emails::instance()->scheduler->delete_item( $row->id );
			}
		}

		$triggers[] = 'subs_reactivated';

		self::add_to_queue( $order_id, $triggers, $subscription->get_id(), $subscription->get_user_id() );
	}

	/**
	 * Fired after a subscription gets suspended
	 *
	 * @param WC_Subscription $subscription
	 */
	public static function suspended_subscription( $subscription ) {
		$order_id       = ($subscription->get_parent()) ? WC_FUE_Compatibility::get_order_prop( $subscription->get_parent(), 'id' ) : 0;
		$triggers[]     = 'subs_suspended';

		self::add_to_queue( $order_id, $triggers, $subscription->get_id(), $subscription->get_user_id() );
	}

	/**
	 * Send follow-ups after a subscription payment fails
	 *
	 * @param WC_Subscription $subscription
	 * @param string $status
	 */
	public function subscription_payment_failed( $subscription, $status ) {
		$order_id = $subscription->get_last_order();

		// Only add to queue if this is a resubscribe/renewal order.
		if ( ! wcs_order_contains_resubscribe( $order_id ) && ! wcs_order_contains_renewal( $order_id ) ) {
			return;
		}

		self::add_to_queue(
			$order_id,
			array( 'subs_payment_failed' ),
			$subscription->get_id(),
			$subscription->get_user_id()
		);
	}

	/**
	 * Fires after a renewal order is created to allow admin to
	 * send emails after every subscription payment
	 *
	 * @param WC_Order $renewal_order
	 * @param WC_Subscription $subscription
	 * @return WC_Order
	 */
	public static function subscription_renewal_order_created( $renewal_order, $subscription ) {
		$triggers[] = 'subs_renewal_order';

		// remove the _fue_recorded meta key that WCS copies from the original order
		delete_post_meta( WC_FUE_Compatibility::get_order_prop( $renewal_order, 'id' ), '_fue_recorded' );

		$order_id = ($subscription->get_parent()) ? WC_FUE_Compatibility::get_order_prop( $subscription->get_parent(), 'id' ) : 0;
		self::add_to_queue( $order_id, $triggers, $subscription->get_id(), $subscription->get_user_id() );

		return $renewal_order;
	}

	/**
	 * Removes emails from the queue that have been triggered by an active subscription.
	 *
	 * @param WC_Subscription $subscription
	 */
	public static function remove_active_subscription_emails( $subscription ) {
		global $wpdb;

		$order_id       = ($subscription->get_parent()) ? WC_FUE_Compatibility::get_order_prop( $subscription->get_parent(), 'id' ) : 0;
		$product_ids    = self::get_subscription_product_ids( $subscription );
		$search_key     = self::get_queue_meta_search_string( $subscription->get_id() );

		array_push( $product_ids, 0 );

		$product_ids =  implode( ',', array_map( 'absint', $product_ids ) );

		// delete queued emails with the same product id/order id and the following triggers
		$triggers = array(
			'subs_activated', 'subs_renewed', 'subs_reactivated',
			'subs_suspended', 'subs_before_renewal', 'subs_before_expire'
		);
		$sql = "
			SELECT eo.id
			FROM {$wpdb->prefix}followup_email_orders eo, {$wpdb->postmeta} pm
			WHERE eo.is_sent = 0
			AND eo.product_id IN ($product_ids)
			AND eo.meta LIKE '%{$search_key}%'
			AND eo.email_id = pm.post_id
			AND pm.meta_key = '_interval_type'
			AND pm.meta_value IN ('". implode( "','", $triggers ) ."')
		";
		$rows = $wpdb->get_results( $sql );

		if ( $rows ) {
			foreach ( $rows as $row ) {
				Follow_Up_Emails::instance()->scheduler->delete_item( $row->id );
			}
		}
	}

	/**
	 * Add renewal reminder emails to the queue right after the subscription has been activated
	 * @param WC_Subscription $subscription
	 */
	public static function set_renewal_reminder( $subscription ) {
		$order_id   = ($subscription->get_parent()) ? WC_FUE_Compatibility::get_order_prop( $subscription->get_parent(), 'id' ) : 0;
		$queued     = array();

		$renewal_date = $subscription->get_date( "next_payment" );

		if (! $renewal_date )
			return;

		// convert to local time
		$renewal_timestamp = get_date_from_gmt( $renewal_date, 'U' );

		if ( current_time('timestamp', true) > $renewal_timestamp ) {
			return;
		}

		// look for renewal emails
		$emails = fue_get_emails( 'any', FUE_Email::STATUS_ACTIVE, array(
			'meta_query'    => array(
				array(
					'key'   => '_interval_type',
					'value' => 'subs_before_renewal'
				)
			)
		) );

		if ( count($emails) > 0 ) {
			$product_ids = self::get_subscription_product_ids( $subscription );
			$search_key = self::get_queue_meta_search_string( $subscription->get_id() );

			foreach ( $emails as $email ) {
				// product_id filter
				if ( !empty( $email->product_id ) && !in_array( $email->product_id, $product_ids ) ) {
					continue;
				} elseif ( $email->category_id > 0 ) {
					$matched_category = false;
					foreach ( $product_ids as $product_id ) {
						$categories = array();
						$cat_terms  = wp_get_object_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );

						if ( ! is_wp_error( $cat_terms ) ) {
							$categories = $cat_terms;
						}

						if ( empty( $categories ) || ! in_array( $email->category_id, $categories ) ) {
							continue;
						}

						$matched_category = true;
						break;
					}

					if ( ! $matched_category ) {
						continue;
					}
				}

				// look for a possible duplicate item in the queue
				$dupes = Follow_Up_Emails::instance()->scheduler->get_items(array(
					'email_id'  => $email->id,
					'is_sent'   => 0,
					'meta'      => $search_key,
					'user_id'   => $subscription->get_user_id()
				));

				if ( count( $dupes ) > 0 ) {
					// there already is an unsent queue item for the exact same order
					continue;
				}

				// add this email to the queue
				$interval   = (int)$email->interval_num;
				$add        = FUE_Sending_Scheduler::get_time_to_add( $interval, $email->interval_duration );
				$send_on    = $renewal_timestamp - $add;

				if ( $send_on < current_time( 'timestamp' ) ) {
					// Only queue future emails. Do not send a renewal notice if this subscription was just created.
					continue;
				}

				$insert = array(
					'user_id'       => $subscription->get_user_id(),
					'send_on'       => $send_on,
					'email_id'      => $email->id,
					'product_id'    => 0,
					'order_id'      => $order_id
				);

				$insert['meta']['subs_key'] = $subscription->get_id();

				if ( !is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
					$queued[] = $insert;
				}

			}
		}

		if ( count( $queued ) > 0 ) {
			Follow_Up_Emails::instance()->fue_wc->wc_scheduler->add_order_notes_to_queued_emails( $queued );
		}

	}

	/**
	 * Set expiration reminder after the subscription gets activated
	 *
	 * @param WC_Subscription $subscription
	 */
	public static function set_expiration_reminder( $subscription ) {
		$order      = is_callable( array( $subscription, 'get_parent' ) ) ? $subscription->get_parent() : $subscription->order;
		$order_id   = ( $order ) ? WC_FUE_Compatibility::get_order_prop( $order, 'id' ) : 0;
		$queued     = array();

		$expiry_date = $subscription->get_date('end');

		if (! $expiry_date )
			return;

		// convert to local time
		$expiry_timestamp = get_date_from_gmt( $expiry_date, 'U' );

		if ( current_time('timestamp', true) > $expiry_timestamp ) {
			return;
		}

		// look for renewal emails
		$emails = fue_get_emails( 'any', FUE_Email::STATUS_ACTIVE, array(
			'meta_query'    => array(
				array(
					'key'   => '_interval_type',
					'value' => 'subs_before_expire'
				)
			)
		) );

		if ( count($emails) > 0 ) {
			$product_ids    = self::get_subscription_product_ids( $subscription );
			$search_key     = self::get_queue_meta_search_string( $subscription->get_id() );

			foreach ( $emails as $email ) {
				// product_id filter
				if ( !empty( $email->product_id ) && !in_array( $email->product_id, $product_ids ) ) {
					continue;
				} elseif ( $email->category_id > 0 ) {
					$matched_category = false;
					foreach ( $product_ids as $product_id ) {
						$categories = array();
						$cat_terms  = wp_get_object_terms( $product_id, 'product_cat', array('fields' => 'ids') );

						if ( !is_wp_error( $cat_terms ) ) {
							$categories = $cat_terms;
						}

						if ( empty( $categories ) || !in_array( $email->category_id, $categories ) ) {
							continue;
						}

						$matched_category = true;
						break;
					}

					if ( !$matched_category ) {
						continue;
					}

				}

				// look for a possible duplicate item in the queue
				$dupes = Follow_Up_Emails::instance()->scheduler->get_items(array(
					'email_id'  => $email->id,
					'is_sent'   => 0,
					'meta'      => $search_key,
					'user_id'   => $subscription->get_user_id()
				));

				if ( count( $dupes ) > 0 ) {
					// there already is an unsent queue item for the exact same order
					continue;
				}

				// add this email to the queue
				$interval   = (int)$email->interval_num;
				$add        = FUE_Sending_Scheduler::get_time_to_add( $interval, $email->interval_duration );
				$send_on    = $expiry_timestamp - $add;

				$insert = array(
					'user_id'       => $subscription->get_user_id(),
					'send_on'       => $send_on,
					'email_id'      => $email->id,
					'product_id'    => 0,
					'order_id'      => $order_id
				);

				$insert['meta']['subs_key'] = $subscription->get_id();

				if ( !is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
					$queued[] = $insert;
				}
			}
		}

		if ( count( $queued ) > 0 ) {
			Follow_Up_Emails::instance()->fue_wc->wc_scheduler->add_order_notes_to_queued_emails( $queued );
		}

	}

	/**
	 * Do not send email if the status has changed from the time it was queued
	 *
	 * @param bool      $skip
	 * @param FUE_Email $email
	 * @param object    $queue_item
	 *
	 * @return bool
	 */
	public static function skip_sending_if_status_changed( $skip, $email, $queue_item ) {
		if ( isset($queue_item->meta) && !empty($queue_item->meta) ) {

			$meta = maybe_unserialize($queue_item->meta);

			if ( isset($meta['subs_key']) ) {
				$delete         = false;
				$subscription   = wcs_get_subscription( $meta['subs_key'] );

				if ( $subscription ) {
					$resubscribe_order_ids = $subscription->get_related_orders( 'ids', 'resubscribe' );

					$already_resubscribed = ! empty( $resubscribe_order_ids );

					if ( $email->interval_type == 'subs_suspended' && $subscription->get_status() != 'on-hold'  ) {
						$delete = true;
						$skip = true;
					} elseif ( $email->interval_type == 'subs_expired' && ( $subscription->get_status() != 'expired' || $already_resubscribed ) ) {
						$delete = true;
						$skip = true;
					} elseif ( ($email->interval_type == 'subs_activated' || $email->interval_type == 'subs_renewed' || $email->interval_type == 'subs_reactivated') && $subscription->get_status() != 'active' ) {
						$delete = true;
						$skip = true;
					} elseif ( $email->interval_type == 'subs_cancelled' && ( $subscription->get_status() != 'cancelled' || $already_resubscribed ) ) {
						$delete = true;
						$skip = true;
					} elseif ( $email->interval_type == 'subs_before_renewal' && $subscription->get_status() != 'active' ) {
						$delete = true;
						$skip = true;
					} elseif ( $email->interval_type == 'subs_before_expire' && ( $subscription->get_status() != 'active' || $already_resubscribed ) ) {
						$delete = true;
						$skip = true;
					}

					if ( $delete ) {
						Follow_Up_Emails::instance()->scheduler->delete_item( $queue_item->id );
					}

				} // if ($subscription)
			} // if ( isset($meta['subs_key']) )

		} // if ( isset($email_order->meta) && !empty($email_order->meta) )

		return $skip;

	}

	/**
	 * Skip sending reminder emails if the expiration/renewal has changed
	 *
	 * @param bool                      $skip
	 * @param FUE_Email                 $email
	 * @param FUE_Sending_Queue_Item    $queue_item
	 * @return bool
	 */
	public static function skip_sending_if_reminder_changed( $skip, $email, $queue_item ) {
		if ( !in_array( $email->trigger, array( 'subs_before_renewal', 'subs_before_expire') ) ) {
			return $skip;
		}

		if ( !isset( $queue_item->meta ) || empty( $queue_item->meta ) || empty( $queue_item->meta['subs_key'] ) ) {
			return $skip;
		}

		$meta   = maybe_unserialize($queue_item->meta);

		$subscription = wcs_get_subscription( $meta['subs_key'] );

		if ( $subscription ) {

			if ( $email->trigger == 'subs_before_expire' ) {
				$expiry = $subscription->get_date('end');
				$now    = current_time( 'timestamp', true );

				if ( $expiry == 0 ) {
					return $skip;
				}

				$interval   = (int)$email->interval_num;
				$add        = FUE_Sending_Scheduler::get_time_to_add( $interval, $email->interval_duration );
				$send_on    = $expiry - $add;

				if ( $send_on > $now ) {
					$skip = true;

					$queue_item->send_on = $send_on;
					$queue_item->save();

					// reschedule
					$param = Follow_Up_Emails::instance()->scheduler->get_scheduler_parameters( $queue_item->id );
					as_unschedule_action( 'sfn_followup_emails', $param, 'fue' );
					as_schedule_single_action( $send_on, 'sfn_followup_emails', $param, 'fue' );
				}
			} elseif ( $email->trigger == 'subs_before_renewal' ) {
				$renewal    = $subscription->get_date('next_payment');
				$now        = current_time( 'timestamp', true );

				if ( $renewal == 0 ) {
					return $skip;
				}

				$interval   = (int)$email->interval_num;
				$add        = FUE_Sending_Scheduler::get_time_to_add( $interval, $email->interval_duration );
				$send_on    = $renewal - $add;

				if ( $send_on > $now ) {
					$skip = true;

					$queue_item->send_on = $send_on;
					$queue_item->save();

					// reschedule
					$param = Follow_Up_Emails::instance()->scheduler->get_scheduler_parameters( $queue_item->id );
					as_unschedule_action( 'sfn_followup_emails', $param, 'fue' );
					as_schedule_single_action( $send_on, 'sfn_followup_emails', $param, 'fue' );
				}
			}

		} // if ($subscription)

		return $skip;
	}

	/**
	 * Add email to the queue
	 *
	 * @param $order_id
	 * @param $triggers
	 * @param int $subscription_id
	 * @param int $user_id
	 */
	public static function add_to_queue( $order_id, $triggers, $subscription_id, $user_id = 0 ) {
		$subscription = wcs_get_subscription( $subscription_id );
		$emails = fue_get_emails( 'any', FUE_Email::STATUS_ACTIVE, array(
			'meta_query'    => array(
				array(
					'key'       => '_interval_type',
					'value'     => $triggers,
					'compare'   => 'IN'
				)
			)
		) );

		foreach ( $emails as $email ) {
			$interval   = (int)$email->interval_num;

			$add            = FUE_Sending_Scheduler::get_time_to_add( $interval, $email->interval_duration );
			$send_on        = current_time('timestamp') + $add;

			foreach ( $subscription->get_items() as $item_id => $item ) {
				if ( $email->product_id > 0 && !in_array( $email->product_id, array( $item['product_id'], $item['variation_id'] ) ) ) {
					continue;
				} elseif ( $email->category_id > 0 ) {
					$cat_terms = wp_get_object_terms( $item['product_id'], 'product_cat', array('fields' => 'ids') );
					$categories = array();

					if ( !is_wp_error( $cat_terms ) ) {
						foreach ( $cat_terms as $category_id ) {
							$categories[] = $category_id;
						}
					}

					if ( empty( $categories ) || !in_array( $email->category_id, $categories ) ) {
						continue;
					}
				}

				$insert = array(
					'send_on'       => $send_on,
					'email_id'      => $email->id,
					'product_id'    => $email->product_id,
					'order_id'      => $order_id
				);

				$insert['meta']['subs_key'] = $subscription_id;

				if ($user_id) {
					$user = new WP_User($user_id);
					$insert['user_id']      = $user->ID;
					$insert['user_email']   = $user->user_email;
				}

				if ( !is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
					Follow_Up_Emails::instance()->fue_wc->wc_scheduler->add_order_notes_to_queued_emails( array( $insert ) );
				}
			}

		}

	}

	public static function get_subscription_product_ids( $subscription ) {
		$items = $subscription->get_items();
		$product_ids = array();

		foreach ( $items as $item ) {
			$product_ids[] = $item['product_id'];

			if ( $item['variation_id'] ) {
				$product_ids[] = $item['variation_id'];
			}
		}

		$product_ids = array_unique( $product_ids );

		return $product_ids;
	}

	/**
	 * Sets a `fue_subscription_needs_update` option flag if we need
	 * to update data in emails that are in the queue
	 */
	public static function set_subscription_data_update_flag() {
		$updated = get_option( 'fue_subscription_2.0_updated', false );

		if ( !$updated && version_compare( WC_Subscriptions::$version, '2.0', '>=' ) ) {
			if ( !self::queue_items_need_updating() ) {
				update_option( 'fue_subscription_2.0_updated', true );
				return;
			}

			update_option( 'fue_subscription_needs_update', 1 );
		}

	}

	/**
	 * Looks at the queue and see if there are subscription emails
	 * that need to be updated.
	 *
	 * @return bool
	 */
	public static function queue_items_need_updating() {
		$needs_update   = false;
		$email_ids      = fue_get_emails( 'subscription', '', array('fields' => 'ids') );

		if ( !empty( $email_ids ) ) {
			$queue_items = Follow_Up_Emails::instance()->scheduler->get_items(array(
				'email_id'  => $email_ids
			));

			if ( !empty( $queue_items ) ) {
				$needs_update = true;
			}
		}

		return apply_filters( 'fue_subscription_queue_items_need_updating', $needs_update );
	}

	/**
	 * Get orders that match the $email's criteria
	 * @param array     $orders Matching Order IDs
	 * @param FUE_Email $email
	 * @return array
	 */
	public static function get_orders_for_email( $orders, $email ) {
		if ( $email->type != 'subscription' ) {
			return $orders;
		}

		/* @var $all_subscriptions WC_Subscription[] */
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		$status_array = array(
			'subs_activated'    => 'active',
			'subs_cancelled'    => 'cancelled',
			'subs_expired'      => 'expired',
			'subs_suspended'    => 'suspended'
		);
		$status_triggers = array_keys( $status_array );

		if ( in_array( $email->trigger, $status_triggers ) ) {
			$status = $status_array[ $email->trigger ];

			$args = array(
				'subscription_status' => $status,
			);

			if ( $email->product_id > 0 ) {
				$args['product_id'] = $email->product_id;
			} elseif ( $email->category_id > 0 ) {
				$args['category_id'] = $email->category_id;
			}

			$all_subscriptions = self::get_subscriptions( $args );

			foreach ( $all_subscriptions as $the_subscription ) {

				if ( $the_subscription->post_parent == 0 ) {
					continue;
				}

				$in_queue = $wpdb->get_var( $wpdb->prepare(
					"SELECT COUNT(*)
					FROM {$wpdb->prefix}followup_email_orders
					WHERE order_id = %d
					AND email_id = %d",
					$the_subscription->post_parent,
					$email->id
				) );

				if ( $in_queue ) {
					continue;
				}

				$orders[] = $the_subscription->ID;
			}
		} elseif ( $email->trigger == 'subs_renewed' ) {
			// get orders with active subscriptions AND renewals
			$args = array(
				'subscription_status' => 'active',
			);

			if ( $email->product_id > 0 ) {
				$args['product_id'] = $email->product_id;
			} elseif ( $email->category_id > 0 ) {
				$args['category_id'] = $email->category_id;
			}

			$all_subscriptions = self::get_subscriptions( $args );

			foreach ( $all_subscriptions as $the_subscription ) {
				if ( $the_subscription->post_parent == 0 ) {
					continue;
				}

				$subscription = wcs_get_subscription( $the_subscription->ID );

				if ( $subscription->get_payment_count() >= 2 ) {
					$order_id = ($subscription->get_parent()) ? WC_FUE_Compatibility::get_order_prop( $subscription->get_parent(), 'id' ) : 0;
					$in_queue = $wpdb->get_var( $wpdb->prepare(
						"SELECT COUNT(*)
						FROM {$wpdb->prefix}followup_email_orders
						WHERE order_id = %d
						AND email_id = %d",
						$order_id,
						$email->id
					) );

					if ( $in_queue ) {
						continue;
					}

					$orders[] = $subscription->get_id();
				}
			}
		} elseif ( $email->trigger == 'subs_reactivated' ) {
			// get active subscriptions with at least 1 suspension count
			$args = array(
				'subscription_status' => 'active',
			);

			if ( $email->product_id > 0 ) {
				$args['product_id'] = $email->product_id;
			} elseif ( $email->category_id > 0 ) {
				$args['category_id'] = $email->category_id;
			}

			$all_subscriptions = self::get_subscriptions( $args );

			foreach ( $all_subscriptions as $the_subscription ) {
				if ( $the_subscription->post_parent == 0 ) {
					continue;
				}

				$subscription   = wcs_get_subscription( $the_subscription->ID );
				$order_id       = ($subscription->get_parent()) ? WC_FUE_Compatibility::get_order_prop( $subscription->get_parent(), 'id' ) : 0;
				if ( !empty( $subscription->get_suspension_count() ) && $subscription->get_suspension_count() > 0 ) {

					$in_queue = $wpdb->get_var( $wpdb->prepare(
						"SELECT COUNT(*)
						FROM {$wpdb->prefix}followup_email_orders
						WHERE order_id = %d
						AND email_id = %d",
						$order_id,
						$email->id
					) );

					if ( $in_queue ) {
						continue;
					}

					$orders[] = $subscription->get_id();
				}
			}
		} elseif ( $email->trigger == 'subs_renewal_order' ) {
			$order_ids = $wpdb->get_col(
				"SELECT post_id
				FROM {$wpdb->postmeta}
				WHERE meta_key = '_original_order'
				AND meta_value > 0"
			);

			foreach ( $order_ids as $order_id ) {
				$subscription_id = get_post_meta( $order_id, '_subscription_renewal', true );

				$in_queue = $wpdb->get_var( $wpdb->prepare(
					"SELECT COUNT(*)
					FROM {$wpdb->prefix}followup_email_orders
					WHERE order_id = %d
					AND email_id = %d",
					$order_id,
					$email->id
				) );

				if ( $in_queue ) {
					continue;
				}

				$orders[] = $subscription_id;
			}

		} elseif ( $email->trigger == 'subs_before_renewal' || $email->trigger == 'subs_before_expire' ) {
			$args = array(
				'subscription_status' => 'active',
			);

			if ( $email->product_id > 0 ) {
				$args['product_id'] = $email->product_id;
			} elseif ( $email->category_id > 0 ) {
				$args['category_id'] = $email->category_id;
			}

			$all_subscriptions = self::get_subscriptions( $args );
			foreach ( $all_subscriptions as $subscription ) {
				$in_queue = $wpdb->get_var( $wpdb->prepare(
					"SELECT COUNT(*)
					FROM {$wpdb->prefix}followup_email_orders
					WHERE order_id = %d
					AND email_id = %d
					AND is_sent = 0",
					$subscription->post_parent,
					$email->id
				) );

				if ( $in_queue ) {
					continue;
				}
				// $subscription is of type stdClass
				$orders[] = $subscription->ID;
			}
		} elseif ( $email->trigger == 'subs_payment_failed' ) {
			// Get subscriptions which are on-hold and have their last payment set to failed.
			$args = array(
				'subscription_status' => 'on-hold',
			);

			if ( $email->product_id > 0 ) {
				$args['product_id'] = $email->product_id;
			} elseif ( $email->category_id > 0 ) {
				$args['category_id'] = $email->category_id;
			}

			$all_subscriptions = self::get_subscriptions( $args );

			foreach ( $all_subscriptions as $the_subscription ) {
				if ( $the_subscription->post_parent == 0 ) {
					continue;
				}

				$subscription = wcs_get_subscription( $the_subscription->ID );
				$last_order   = $subscription ? $subscription->get_last_order( 'all' ) : false;

				if ( $last_order && $last_order->has_status( 'failed' ) ) {
					$order_id = ($subscription->get_parent()) ? WC_FUE_Compatibility::get_order_prop( $subscription->get_parent(), 'id' ) : 0;

					// Only add to queue if this is a resubscribe/renewal order.
					if ( ! wcs_order_contains_resubscribe( $last_order ) && ! wcs_order_contains_renewal( $last_order ) ) {
						continue;
					}

					$in_queue = $wpdb->get_var( $wpdb->prepare(
						"SELECT COUNT(*)
						FROM {$wpdb->prefix}followup_email_orders
						WHERE order_id = %d
						AND email_id = %d",
						$order_id,
						$email->id
					) );

					if ( $in_queue ) {
						continue;
					}

					$orders[] = $subscription->get_id();
				}
			}
		}

		if ( empty( $orders ) ) {
			return array();
		}

		return array( $email->id => $orders );
	}

	/**
	 * Run filters on subscription emails to remove invalid orders
	 *
	 * @param  array $data
	 * @param FUE_Email $email
	 * @return array Filtered $orders
	 */
	public static function filter_orders_for_email( $data, $email ) {
		if ( $email->type == 'subscription' ) {
			if ( $email->product_id > 0 ) {
				foreach ( $data as $email_id => $orders ) {
					foreach ( $orders as $idx => $order_id ) {
						if ( !self::subscription_has_item( $email->product_id, $order_id ) ) {
							unset( $data[ $email_id ][ $idx ] );
						}
					}
				}
			}
		}

		return $data;
	}

	/**
	 * If the post pointing to the order ID is of type 'shop_subscription', use the
	 * order_id as the subscription_key and fill in the proper order ID.
	 *
	 * @param array     $insert
	 * @param FUE_Email $email
	 * @return array
	 */
	public static function add_subscription_id_to_meta( $insert, $email ) {
		if ( empty( $insert['order_id'] ) ) {
			return $insert;
		}

		if ( get_post_type( $insert['order_id'] ) == 'shop_subscription' ) {
			$post = get_post( $insert['order_id'] );
			$insert['meta']['subs_key'] = $insert['order_id'];
			$insert['order_id'] = $post->post_parent;
		}

		return $insert;
	}

	/**
	 * Set the correct send date when importing subscription follow-ups
	 *
	 * @param array         $insert
	 * @param FUE_Email     $followup
	 * @return array
	 */
	public static function set_followup_send_date( $insert, $followup ) {
		if ( $followup->is_type( 'subscription' ) ) {
			if ( empty( $insert['meta']['subs_key'] ) ) {
				return $insert;
			}

			$subscription = wcs_get_subscription( $insert['meta']['subs_key'] );
			switch ( $followup->trigger ) {
				case 'subs_activated':
					$trigger_date = $subscription->get_date( 'start', 'site' );
					break;

				case 'subs_expired':
				case 'subs_before_expire':
					$trigger_date = $subscription->get_date( 'end', 'site' );
					break;

				case 'subs_cancelled':
					// Do not import when no payments have been completed
					$trigger_date = ( $subscription->get_payment_count() >= 1 ) ? $subscription->get_date( 'end', 'site' ) : 0;
					break;

				case 'subs_before_renewal':
					$trigger_date = $subscription->get_date( 'next_payment' );
					break;

				case 'subs_payment_failed':
					// Trigger date is set to the modified date of the last failed renewal order.
					$last_order   = $subscription->get_last_order( 'all' );
					$trigger_date = $last_order && $last_order->has_status( 'failed' ) ? get_date_from_gmt( $last_order->get_date_modified() ) : 0;
					break;

		        default:
			        $trigger_date = 0;
			        break;
	        }

	        if ( ! $trigger_date ) {
		        $insert = false;
	        } else {
		        if ( $followup->trigger == 'subs_before_expire' ) {
			        $diff = FUE_Sending_Scheduler::get_time_to_add( $followup->interval, $followup->interval_duration );
			        $trigger_timestamp = strtotime( $trigger_date ) - $diff;
			        $insert['send_on'] = $trigger_timestamp;
		        } else {
			        $insert['send_on'] = $followup->get_send_timestamp( $trigger_date );
		        }
	        }
		}

		return $insert;
	}

	/**
	 * Send an email notification when a subscription payment fails
	 * @param WC_Subscription $subscription
	 */
	public static function payment_failed_for_subscription( $subscription ) {

		if ( 1 == get_option('fue_subscription_failure_notification', 0) ) {
			// notification enabled
			$emails_string = get_option('fue_subscription_failure_notification_emails', '');

			if ( empty($emails_string) )
				return;

			$subject    = sprintf( __('Subscription payment failed for #%s'), $subscription->get_id() );
			$message    = sprintf( __('<p>A subscription payment has failed. The subscription has now been automatically put on hold.</p><p><a href="%s">View Subscription</a></p>'), admin_url('post.php?post='. $subscription->get_id() .'&action=edit') );

			$recipients = array();

			if ( strpos( $emails_string, ',') !== false ) {
				$recipients = array_map('trim', explode( ',', $emails_string ) );
			} else {
				$recipients = array($emails_string);
			}

			$scheduler = Follow_Up_Emails::instance()->scheduler;

			// FUE will use the billing_email by default. Remove the hook to stop it from changing the email
			remove_filter( 'fue_insert_email_order', array($scheduler, 'get_correct_email') );

			foreach ( $recipients as $email ) {
				$items = $scheduler->get_items(array(
					'user_email'    => $email,
					'meta'          => 'subscription_notification',
					'order_id'      => 0,
					'is_sent'       => 0
				));

				if ( count( $items ) > 0 ) {
					continue;
				}

				$scheduler->queue_email(
					array(
						'user_email'    => $email,
						'meta'          => array(
							'subscription_notification' => true,
							'email'     => $email,
							'subject'   => $subject,
							'message'   => $message
						),
						'email_trigger' => 'After a subscription payment fails',
						'order_id'      => 0,
						'product_id'    => 0,
						'send_on'       => current_time('timestamp') + 120
					),
					null, // ad-hoc email
					true
				);
			}
		}

	}

	/**
	 * Fetch all subscriptions matching the given arguments.
	 *
	 * @param array $args
	 * @return object[]
	 */
	public static function get_subscriptions( $args ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		$default = array(
			'subscriptions_per_page' => -1,
			'paged'                  => 1,
			'offset'                 => 0,
			'orderby'                => 'start_date',
			'order'                  => 'DESC',
			'customer_id'            => 0,
			'product_id'             => 0,
			'category_id'            => 0,
			'variation_id'           => 0,
			'order_id'               => 0,
			'subscription_status'    => 'any',
			'meta_query_relation'    => 'AND',
		);
		$args = wp_parse_args( $args, $default );

		// Make sure status starts with 'wc-'
		if ( ! in_array( $args['subscription_status'], array( 'any', 'trash' ) ) ) {
			$args['subscription_status'] = wcs_sanitize_subscription_status_key( $args['subscription_status'] );
		}

		// Prepare the args for WP_Query
		$query_args = array(
			'post_type'      => 'shop_subscription',
			'post_status'    => $args['subscription_status'],
			'posts_per_page' => $args['subscriptions_per_page'],
			'paged'          => $args['paged'],
			'offset'         => $args['offset'],
			'order'          => $args['order'],
			'fields'         => 'ids',
			'meta_query'     => array(), // just in case we need to filter or order by meta values later
		);

		// We need to restrict subscriptions to those which contain a certain product/variation
		if ( ( 0 != $args['product_id'] && is_numeric( $args['product_id'] ) ) || ( 0 != $args['variation_id'] && is_numeric( $args['variation_id'] ) ) ) {
			$query_args['post__in'] = wcs_get_subscriptions_for_product( array( $args['product_id'], $args['variation_id'] ) );
		} elseif ( 0 != $args['category_id'] && is_numeric( $args['category_id'] ) ) {
			$category_products = get_posts( array(
				'post_type'     => 'product',
				'nopaging'      => true,
				'post_status'   => 'publish',
				'fields'        => 'ids',
				'tax_query'     => array(
					array(
						'taxonomy'  => 'product_cat',
						'field'     => 'term_id',
						'terms'     => $args['category_id']
					)
				)
			) );

			if ( !empty( $category_products ) ) {
				foreach ( $category_products as $idx => $product_id ) {
					$product = WC_FUE_Compatibility::wc_get_product( $product_id );
					if ( !$product->is_type( array('subscription', 'subscription_variation', 'variable-subscription') ) ) {
						unset( $category_products[ $idx ] );
					}
				}

				if ( !empty( $category_products ) ) {
					$query_args['post__in'] = wcs_get_subscriptions_for_product( $category_products );
				}

			}
		}

		if ( ! empty( $query_args['meta_query'] ) ) {
			$query_args['meta_query']['relation'] = $args['meta_query_relation'];
		}

		if ( ! empty( $query_args['meta_query'] ) ) {
			$query_args['meta_query']['relation'] = $args['meta_query_relation'];
		}

		$subscription_post_ids = get_posts( $query_args );
		$subscriptions = array();

		foreach ( $subscription_post_ids as $post_id ) {
			$subscriptions[] = $wpdb->get_row("SELECT ID, post_parent, post_status FROM {$wpdb->posts} WHERE ID = {$post_id}");
		}

		return $subscriptions;
	}

	public static function subscription_has_item( $product_id, $subscription_id ) {
		$wpdb           = Follow_Up_Emails::instance()->wpdb;
		$get_items_sql  = $wpdb->prepare( "SELECT order_item_id, order_item_name, order_item_type FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id = %d ", $subscription_id );
		$get_items_sql .= "AND order_item_type = 'line_item' ORDER BY order_item_id;";
		$line_items     = $wpdb->get_results( $get_items_sql );

		// Loop items
		foreach ( $line_items as $item ) {
			$item_product_id     = wc_get_order_item_meta( $item->order_item_id, '_product_id', true );
			$item_variation_id   = wc_get_order_item_meta( $item->order_item_id, '_variation_id', true );

			if ( $item_product_id == $product_id || $item_variation_id == $product_id ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get data about the given subscription
	 *
	 * @param int|WC_Subscription   $subscription
	 * @return array
	 */
	public static function get_subscription_meta( $subscription ) {
		if ( is_numeric( $subscription ) ) {
			$subscription = wcs_get_subscription( $subscription );
		}
		// last_payment_date deprecated
		$last_payment_key = self::subs_v_gte( '2.2.0' ) ? 'last_order_date_paid' : 'last_payment_date';
		$start_key  = self::subs_v_gte( '2.2.0' ) ? 'date_created' : 'start';

		$data = array(
			'start_date'        => $subscription->get_date( $start_key, 'site' ) ,
			'trial_end_date'    => $subscription->get_date( 'trial_end', 'site' ),
			'trial_length'      => '',
			'last_payment_date' => $subscription->get_date( $last_payment_key, 'site' ),
			'next_payment_date' => $subscription->get_date( 'next_payment', 'site' ),
			'end_date'          => $subscription->get_date( 'end', 'site' ),
			'days_to_renew'     => '',
			'first_payment_cost'=> wc_price( $subscription->get_total_initial_payment() ),
			'cost'              => wc_price( $subscription->get_total() ),
			'cost_term'         => $subscription->get_formatted_order_total(),
		);

		if ( $data['next_payment_date'] ) {
			$timestamp  = strtotime( $data['next_payment_date'] );

			$data['next_payment_date'] = fue_format_date( $timestamp );

			// calc days to renew
			$now    = current_time( 'timestamp' );
			$diff   = $timestamp - $now;

			if ( $diff > 0 ) {
				$data['days_to_renew'] = floor( $diff / 86400 );
			}
		}

		if ( $data['end_date'] == 0 ) {
			$data['end_date'] = __('Until cancelled', 'follow_up_emails');
		} else {
			$data['end_date'] = fue_format_date( $data['end_date'] );
		}

		if ( $data['start_date'] ) {
			$data['start_date'] = fue_format_date( $data['start_date'] );
		}

		if ( $data['trial_end_date'] ) {
			$data['trial_end_date'] = fue_format_date( $data['trial_end_date'] );

			$trial_period = $subscription->get_trial_period();

			$start_time = $subscription->get_time( 'start', 'site' );

			if ( $trial_period ) {
				$trial_length = wcs_estimate_periods_between( $start_time, strtotime( $data['trial_end_date'] ), $trial_period );

				if ( $trial_length ) {
					if ( $trial_length == 1 ) {
						$data['trial_length'] = sprintf(__('1 %s', 'woocommerce-subscriptions'), $trial_period );
					} else {
						$data['trial_length'] = wcs_get_subscription_period_strings( $trial_length, $trial_period );
					}
				}
			}
		}

		if ( $data['last_payment_date'] ) {
			$data['last_payment_date'] = fue_format_date( $data['last_payment_date'] );
		}



		return $data;
	}

	/**
	 * @param $subscription_id
	 * @return string
	 */
	public static function get_queue_meta_search_string( $subscription_id ) {
		$serialized = serialize( array( 'subs_key' => $subscription_id ) );

		return rtrim( ltrim( $serialized, 'a:1{' ), '}' );
	}

	/**
	 * Is subscription version greater than given version?
	 *
	 * @param string $version
	 * @return bool
	 */
	public static function subs_v_gte( $version ){
		return version_compare( WC_Subscriptions::$version, $version , '>=' );
	}

	/**
	 * Remove queued emails when subscription status changes.
	 *
	 * @param $subscription
	 * @param $old_status
	 * @param $new_status
	 */
	public function remove_subscription_payment_failed_email( $subscription, $new_status, $old_status ) {
		if ( 'active' !== $new_status ) {
			return;
		}
		$order_id = $subscription->get_last_order();
		$filter     = array(
			'meta_query'    => array(
				array(
					'key'       => '_interval_type',
					'value'     => 'subs_payment_failed',
				),
			),
		);

		$emails     = fue_get_emails( 'any', '', $filter );
		$email_ids  = array();
		foreach ( $emails as $email ) {
			$key = 'remove_email_status_change';
			if ( ! empty( $email->meta[ $key ] ) && 'yes' == $email->meta[ $key ] ) {
				$email_ids[] = $email->id;
			}
		}
		$queue = Follow_Up_Emails::instance()->scheduler->get_items( array(
			'is_sent'   => 0,
			'order_id'  => $order_id,
			'email_id'  => $email_ids,
		) );
		foreach ( $queue as $item ) {
			$email_name = get_the_title( $item->email_id );
			$subscription->add_order_note( sprintf( __( 'The email &quot;%s&quot; has been removed due to the order status changing to active.', 'follow_up_emails' ), $email_name ) );
			Follow_Up_Emails::instance()->scheduler->delete_item( $item->id );
		}
	}

	/**
	 * Remove queued emails for failed subscription renewal orders when order status changes.
	 *
	 * @param $subscription
	 * @param $old_status
	 * @param $new_status
	 */
	public function remove_subscription_renewal_payment_failed_email( $order_id, $old_status, $new_status ) {

		if ( 'cancelled' !== $new_status ) {
			// On status change to failed Follow-up emails are created
			return;
		}

		if( ! wcs_order_contains_renewal( $order_id ) ) {
			// It is not a subscription renewal order.
			return;
		}

		$filter     = array(
			'meta_query'    => array(
				array(
					'key'       => '_interval_type',
					'value'     => 'subs_payment_failed',
				),
			),
		);

		$emails     = fue_get_emails( 'any', '', $filter );
		$email_ids  = array();
		foreach ( $emails as $email ) {
			$key = 'remove_email_status_change';
			if ( ! empty( $email->meta[ $key ] ) && 'yes' == $email->meta[ $key ] ) {
				$email_ids[] = $email->id;
			}
		}

		$queue = Follow_Up_Emails::instance()->scheduler->get_items( array(
			'is_sent'   => 0,
			'order_id'  => $order_id,
			'email_id'  => $email_ids,
		) );

		foreach ( $queue as $item ) {
			Follow_Up_Emails::instance()->scheduler->delete_item( $item->id );
		}
	}

	/**
	 * Remove queued emails for renewal order created when order status changes.
	 *
	 * @since 4.9.15
	 *
	 * @param $order_id
	 * @param $old_status
	 * @param $new_status
	 */
	public function remove_subscription_renewal_order_created_email( $order_id, $old_status, $new_status ) {

		if ( ! wcs_order_contains_renewal( $order_id ) ) {
			// It is not a subscription renewal order.
			return;
		}

		$filter = array(
			'meta_query' => array(
				array(
					'key'   => '_interval_type',
					'value' => 'subs_renewal_order',
				),
			),
		);

		$emails    = fue_get_emails( 'any', '', $filter );
		$email_ids = array();
		foreach ( $emails as $email ) {
			$key = 'remove_email_status_change';
			if ( ! empty( $email->meta[ $key ] ) && 'yes' == $email->meta[ $key ] ) {
				$email_ids[] = $email->id;
			}
		}

		$items = array();
		foreach ( wcs_get_subscriptions_for_renewal_order( $order_id ) as $subscription ) {
			$items = array_merge(
				Follow_Up_Emails::instance()->scheduler->get_items(
					array(
						'is_sent'  => 0,
						'order_id' => $subscription->get_parent_id(),
						'email_id' => $email_ids,
					)
				),
				$items
			);
		}

		foreach ( $items as $item ) {
			Follow_Up_Emails::instance()->scheduler->delete_item( $item->id );
		}
	}

}
