<?php

/**
 * FUE_Sending_Scheduler class
 */
class FUE_Sending_Scheduler {

	/**
	 * @var Follow_Up_Emails
	 */
	private $fue;

	/**
	 * Class constructor
	 *
	 * @param Follow_Up_Emails $fue
	 */
	public function __construct( Follow_Up_Emails $fue ) {
		$this->fue = $fue;
	}

	/**
	 * Run the queue item into a filter of conditions and see if it passes or fails
	 *
	 * This method only triggers the fue_email_matches_conditions hook and
	 * addons that implement conditions should hook into this and run their
	 * own checks. Hooked methods should return TRUE if it passes or WP_Error
	 * if it fails
	 *
	 * @param FUE_Sending_Queue_Item $item
	 * @return bool|WP_Error Returns TRUE if it passes all the conditions or WP_Error if it doesn't
	 */
	public function filter_conditions( FUE_Sending_Queue_Item $item ) {

		return apply_filters( 'fue_queue_item_filter_conditions', true, $item );

	}

	/**
	 * Get queue items that need to be sent
	 *
	 * @param int $item_id Specify an email order ID to only send that specific item
	 */
	public static function send_scheduled_emails( $item_id = 0 ) {
		$scheduler = Follow_Up_Emails::instance()->scheduler;

		// If the importing of queue items from wp-cron to action-scheduler
		// is underway, do nothing until the importing finishes
		if ( true == get_transient( 'fue_importing' ) ) {
			return;
		}

		// if $email_order_id is set, send only the specified queue item
		if ( $item_id > 0 ) {
			$queue_item = new FUE_Sending_Queue_Item( $item_id );

			if ( $queue_item && $queue_item->is_sent == 0 && $queue_item->status == 1 ) {
				$status = Follow_Up_Emails::instance()->mailer->send_queue_item( $queue_item );

				if ( is_wp_error( $status ) ) {
					$error = $status->get_error_message();
					$queue_item->add_note( $error );
					/* translators: %d Email ID. */
					fue_debug_log( sprintf( __( 'Failed to send email %d', 'follow_up_emails' ), $item_id ), $error );
				} else {
					$message = __( 'Email sent successfully', 'follow_up_email' );
					$queue_item->add_note( $message );
					fue_debug_log( $message, $item_id );
				}
			}

		} else {

			$to         = current_time('timestamp');
			$results    = $scheduler->get_items( array(
				'is_sent'   => 0,
				'status'    => 1,
				'send_on'   => array('to' => $to )
			) );

			foreach ( $results as $queue_item ) {

				if ( $queue_item->send_on > $to ) {
					// send_on is still a future date
					continue;
				}

				$status = Follow_Up_Emails::instance()->mailer->send_queue_item( $queue_item );

				if ( is_wp_error( $status ) ) {
					$error = $status->get_error_message();
					$queue_item->add_note( $error );
						/* translators: %d Email ID. */
					fue_debug_log( sprintf( __( 'Failed to send email %d', 'follow_up_emails' ), $item_id ), $error );
				} else {
					$message = __( 'Email sent successfully', 'follow_up_email' );
					$queue_item->add_note( $message );
					fue_debug_log( $message, $item_id );
				}
			}

		}

	}

	/**
	 * Add 'signup' emails to the queue after a user registers
	 *
	 * The normal way of getting emails using fue_get_emails will not work at this point
	 * because the 'user_register' hook that this method is hooked into is executed first
	 * before WP executes the 'init' action so the custom post types and taxonomies aren't
	 * available at this time.
	 *
	 * @param int $user_id
	 * @return void
	 */
	public static function queue_signup_emails( $user_id ) {
		$user   = new WP_User( $user_id );

		if ( is_wp_error($user) )
			return;

		$email_ids = get_posts( array(
			'numberposts'      => -1,
			'fields'           => 'ids',
			'post_type'        => 'follow_up_email',
			'post_status'      => FUE_Email::STATUS_ACTIVE,
			'meta_key'         => '_interval_type',
			'meta_value'       => 'signup',
			'orderby'          => 'menu_order',
			'order'            => 'ASC',
			'suppress_filters' => false,
		) );

		foreach ( $email_ids as $email_id ) {
			$email = new FUE_Email( $email_id );

			// the $email will have an incorrect 'type' propery because the
			// custom post type and taxonomies have not been loaded yet.
			$email->type = 'signup';

			// look for duplicates
			$args = array(
				'email_id'  => $email->id,
				'user_id'   => $user_id
			);
			if ( count( Follow_Up_Emails::instance()->scheduler->get_items( $args ) ) == 0 ) {
				self::queue_email( array('user_id' => $user_id), $email );
				continue;
			}

		}

	}

	/**
	 * Add 'list_signup' emails to the queue after a subscriber gets added to the system.
	 *
	 * This method only handles subscribers that are added to the system but not on any list
	 *
	 * @param int           $subscriber_id
	 * @param string|array  $lists
	 * @return void
	 */
	public static function queue_list_emails_signup( $subscriber_id, $lists = '' ) {
		if ( !empty( $lists ) ) {
			return;
		}

		$email_ids = get_posts( array(
			'numberposts'      => -1,
			'fields'           => 'ids',
			'post_type'        => 'follow_up_email',
			'post_status'      => FUE_Email::STATUS_ACTIVE,
			'meta_key'         => '_interval_type',
			'meta_value'       => 'list_signup',
			'orderby'          => 'menu_order',
			'order'            => 'ASC',
			'suppress_filters' => false,
		) );

		foreach ( $email_ids as $email_id ) {
			$email      = new FUE_Email( $email_id );
			$matched    = false;

			if ( $email->meta['list'] == 'any' ) {
				$matched = true;
			}

			if ( !$matched ) {
				continue;
			}

			$subscriber = fue_get_subscriber( $subscriber_id );

			if ( !$subscriber ) {
				continue;
			}


			// look for duplicates
			$args = array(
				'email_id'      => $email->id,
				'user_email'    => $subscriber['email'],
				'is_sent'       => 0
			);

			if ( count( Follow_Up_Emails::instance()->scheduler->get_items( $args ) ) == 0 ) {
				self::queue_email( array('user_email' => $subscriber['email']), $email );
				continue;
			}

		}

	}

	/**
	 * Add 'list_signup' emails to the queue after a subscriber gets added to a specific list.
	 *
	 * @param int           $subscriber_id
	 * @param int           $list_id
	 * @return void
	 */
	public static function queue_list_emails_added_to_list( $subscriber_id, $list_id ) {
		$email_ids = get_posts( array(
			'numberposts'      => -1,
			'fields'           => 'ids',
			'post_type'        => 'follow_up_email',
			'post_status'      => FUE_Email::STATUS_ACTIVE,
			'meta_key'         => '_interval_type',
			'meta_value'       => 'list_signup',
			'orderby'          => 'menu_order',
			'order'            => 'ASC',
			'suppress_filters' => false,
		) );

		foreach ( $email_ids as $email_id ) {
			$email      = new FUE_Email( $email_id );
			$matched    = false;

			if ( $email->meta['list'] == 'any' ) {
				$matched = true;
			} elseif ( $email->meta['list'] == $list_id ) {
				$matched = true;
			}

			if ( !$matched ) {
				continue;
			}

			$subscriber = fue_get_subscriber( $subscriber_id );

			if ( !$subscriber ) {
				continue;
			}

			// look for duplicates
			$args = array(
				'email_id'      => $email->id,
				'user_email'    => $subscriber['email'],
				'is_sent'       => 0
			);

			if ( count( Follow_Up_Emails::instance()->scheduler->get_items( $args ) ) == 0 ) {
				self::queue_email( array('user_email' => $subscriber['email']), $email );
				continue;
			}

		}

	}

	/**
	 * Add manual emails to the email queue
	 *
	 * $args keys:
	 *  email_id    - ID of FUE_Email
	 *  recipients  - Array of recipients ( format: [ [user_id, user_email, user_name] ] )
	 *  subject     - Email Subject
	 *  message     - Email Message
	 *  tracking    - Analytics tracking code (e.g. ?utm_campaign=xxx)
	 *  send_again  - Whether or not to send another email after a specific interval (bool)
	 *  interval    - If send_again is true, the time to wait before sending the next email (int)
	 *  interval_duration - If send_again is true, the duration type of interval (e.g. minutes/hours/weeks/months)
	 *
	 * @param array $args
	 * @return array Array of Queue Item IDs created
	 */
	public static function queue_manual_emails( $args = array() ) {
		ignore_user_abort( true );
		set_time_limit( 0 );

		$item_ids = array();
		$args = wp_parse_args( $args, array(
				'email_id'          => 0,
				'recipients'        => array(),
				'subject'           => '',
				'message'           => '',
				'tracking'          => '',
				'schedule_email'    => false,
				'schedule_date'     => '',
				'schedule_hour'     => '',
				'schedule_minute'   => '',
				'schedule_ampm'     => '',
				'send_again'        => false,
				'interval'          => '',
				'interval_duration' => '',
				'meta'              => array()
			)
		);

		// clean up the schedule if enabled
		if ( $args['schedule_email'] ) {
			if ( absint( $args['schedule_hour'] ) < 10 ) {
				$args['schedule_hour'] = '0'. absint( $args['schedule_hour'] );

				// If the hour is 00, strtotime will break if we append AM to it.
				if ( '00' === $args['schedule_hour'] ) {
					$args['schedule_ampm'] = '';
				}
			}

			if ( absint( $args['schedule_minute'] ) < 10 ) {
				$args['schedule_minute'] = '0'. absint( $args['schedule_minute'] );
			}

			$args['schedule_ampm'] = strtoupper( $args['schedule_ampm'] );
		}

		extract($args);

		if ( empty($recipients) ) {
			return array();
		}

		// process variable replacements
		$codes = array();

		if ( !empty($tracking) ) {
			parse_str( $tracking, $codes );

			foreach ( $codes as $key => $val ) {
				$codes[$key] = urlencode($val);
			}
		}

		$store_url      = home_url();
		$store_name     = get_bloginfo('name');
		$orig_message   = $args['message'];
		$orig_subject   = $args['subject'];
		$recipient_num  = 0;
		$send_time      = current_time( 'timestamp' );
		$email          = new FUE_Email( $args['email_id'] );

		$schedule_emails = false;

		// figure out the sending schedule
		if ( $args['schedule_email'] ) {
			if ( !empty( $args['schedule_timestamp'] ) ) {
				$send_time = $args['schedule_timestamp'];
			} else {
				 $send_time = strtotime( $args['schedule_date'] .' '. $args['schedule_hour'] .':'. $args['schedule_minute'] .' '. $args['schedule_ampm'] );
			 }
			$schedule_emails = true;
		}

		foreach ( $recipients as $recipient_key => $recipient ) {
			$recipient_num++;

			// create an email order
			$user_id        = $recipient[0];
			$email_address  = $recipient[1];
			$user_name      = $recipient[2];
			$unsubscribe    = add_query_arg('fue', $email_address, fue_get_unsubscribe_url() );

			$_message        = $orig_message;
			$_subject        = $orig_subject;

			$meta = wp_parse_args( array(
				'recipient_key' => $recipient_key,
				'recipient'     => $recipient,
				'user_id'       => $recipient[0],
				'email_address' => $recipient[1],
				'user_name'     => $recipient[2],
				'subject'       => $_subject,
				'message'       => $_message,
				'codes'         => $codes
			), $args['meta'] );

			$insert = array(
				'user_id'       => $user_id,
				'email_id'      => $args['email_id'],
				'user_email'    => $email_address,
				'send_on'       => $send_time,
				'subject'       => $_subject,
				'message'       => $_message,
				'email_trigger' => 'Single Email',
				'meta'          => $meta
			);
			$queue_id = FUE_Sending_Scheduler::queue_email( $insert, $email, $schedule_emails );

			$item_ids[] = $queue_id;

			if ( $args['send_again'] && !empty($interval) && $interval > 0 ) {
				$add = FUE_Sending_Scheduler::get_time_to_add( $interval, $args['interval_duration'] );

				// create an email order
				$email_data = array(
					'user_id'       => $user_id,
					'email_id'      => $args['email_id'],
					'user_email'    => $email_address,
					'send_on'       => $send_time + $add,
					'email_trigger' => 'Single Email',
					'meta'          => $meta
				);
				$queue_id = FUE_Sending_Scheduler::queue_email( $email_data, $email, true );

				//if ( !is_wp_error( $queue_id ) ) {
					$item_ids[] = $queue_id;
				//}

			}
		}

		return $item_ids;

	}

	/**
	 * After the daily summary email gets sent, queue the next summary email for the next day
	 */
	public static function queue_daily_summary_email() {
		if ( 'no' == get_option( 'fue_enable_daily_summary' ) ) {
			return;
		}

		$scheduler = Follow_Up_Emails::instance()->scheduler;
		$items = $scheduler->get_items( array(
			'is_sent'       => 0,
			'email_trigger' => 'Daily summary',
			'status'        => 1
		) );

		if ( empty( $items ) ) {
			$last_summary = get_option( 'fue_last_summary', current_time('timestamp') );
			$send_time = get_option( 'fue_daily_emails_time', '06:00 AM' );
			$next_send = strtotime( date('Y-m-d', $last_summary + 86400) .' '. $send_time );

			if ( false === $next_send ) {

				$next_send = strtotime( date('Y-m-d', current_time('timestamp')) .' '. $send_time );

				if ( current_time('timestamp') > $next_send ) {
					// already in the past. Set it for tomorrow
					$next_send = current_time('timestamp') + 86400;
				}
			}

			// FUE will use the billing_email by default. Remove the hook to stop it from changing the email
			remove_filter( 'fue_insert_email_order', array($scheduler, 'get_correct_email') );

			$scheduler->queue_email(
				array(
					'user_email'    => FUE_Reports::get_summary_recipient_emails(),
					'meta'          => array(
						'daily_summary' => true,
						'email'         => FUE_Reports::get_summary_recipient_emails(),
						'subject'       => __('Follow-up emails summary', 'follow_up_emails'),
						'message'       => ''
					),
					'email_trigger' => 'Daily summary',
					'order_id'      => 0,
					'product_id'    => 0,
					'send_on'       => $next_send
				),
				null, // ad-hoc email
				true
			);

			update_option( 'fue_last_summary', current_time( 'timestamp' ) );
			update_option( 'fue_next_summary', $next_send );

		}
	}

	/**
	 * Update the send date of the next daily summary email to the next available date and time
	 */
	public function reschedule_daily_summary_email() {
		$items = $this->get_items( array('meta' => 'daily_summary', 'is_sent' => 0, 'status' => 1) );

		if ( !empty( $items ) ) {
			$item = array_pop( $items );
			$time = get_option( 'fue_daily_emails_time', '00:00 AM' );

			$next_send = strtotime( date('Y-m-d', current_time('timestamp')) .' '. $time );

			if ( current_time('timestamp') > $next_send ) {
				// already in the past. Set it for tomorrow
				$next_send += 86400;
			}

			update_option( 'fue_next_summary', $next_send );

			$param = array( $item->id );

			as_unschedule_action( 'sfn_followup_emails', $param, 'fue' );

			$send_on_gmt = strtotime( get_gmt_from_date( date( 'Y-m-d H:i:s', $next_send ) ) );

			as_schedule_single_action( $send_on_gmt, 'sfn_followup_emails', $param, 'fue' );

			fue_debug_log( __( 'Rescheduled daily summary email', 'follow_up_emails' ), date( 'Y-m-d H:i:s', $send_on_gmt ) );

			$item->send_on = $next_send;
			$item->save();
		}
	}

	/**
	 * Checks if the send date of the email is in the past
	 *
	 * @param object $email
	 * @return bool
	 */
	public static function send_date_passed( $email ) {

		if ( is_numeric($email) ) {
			$email = new FUE_Email( $email );
		}

		$meta = maybe_unserialize( $email->meta );

		if ( !empty($email->send_date_hour) && !empty($email->send_date_minute) ) {
			$send_on = strtotime($email->send_date .' '. $email->send_date_hour .':'. $email->send_date_minute .' '. $meta['send_date_ampm']);

			if ( false === $send_on ) {
				// fallback to only using the date
				$send_on = strtotime($email->send_date);
			}
		} else {
			$send_on = strtotime($email->send_date);
		}

		if ( $send_on > time() ) {
			// Send date is in the future
			return false;
		}

		return true;
	}

	/**
	 * Get the timestamp of the send date of the FUE_Email
	 *
	 * @param int|FUE_Email $email
	 * @return int
	 */
	public static function get_email_send_timestamp( $email ) {

		if (! is_object($email) ) {
			$email = new FUE_Email( $email );
		}

		return $email->get_send_timestamp();
	}

	/**
	 * Reschedule an email
	 *
	 * This is called to add a 5-minute delay in sending due to either a
	 * previous sending attempt failure or a locked queue. Adding a delay
	 * gives FUE a chance to recover from an error or finish the previous
	 * sending process if the queue is locked
	 *
	 * @param object $queue_item
	 */
	public static function reschedule_queue_item( $queue_item ) {

		$param = array( $queue_item->id );

		as_unschedule_action( 'sfn_followup_emails', $param, 'fue' );

		$send_on_gmt = $queue_item->send_on + 300;

		as_schedule_single_action( $send_on_gmt, 'sfn_followup_emails', $param, 'fue' );

		/* translators: %d Queue ID. */
		fue_debug_log( sprintf( __( 'Rescheduling queue item %d', 'follow_up_emails' ), $queue_item->id ), date( 'Y-m-d H:i:s', $send_on_gmt ) );
	}

	/**
	 * Calculate the seconds to add based on the interval and duration
	 *
	 * @param int       $interval
	 * @param string    $duration minutes, hours, weeks, etc...
	 *
	 * @return int
	 */
	public static function get_time_to_add( $interval, $duration ) {
		$add = 0;
		switch ($duration) {
			case 'minutes':
				$add = $interval * 60;
				break;

			case 'hours':
				$add = $interval * (60*60);
				break;

			case 'days':
				$add = $interval * 86400;
				break;

			case 'weeks':
				$add = $interval * (7 * 86400);
				break;

			case 'months':
				$add = $interval * (30 * 86400);
				break;

			case 'years':
				$add = $interval * (365 * 86400);
				break;
		}

		return apply_filters('fue_get_time_to_add', $add, $duration, $interval);
	}

	/**
	 * Get the lock key unique to a queue item
	 *
	 * @param object $queue_item
	 *
	 * @return string
	 */
	public static function get_queue_item_lock_key( $queue_item ) {
		return 'fue_lock_'. $queue_item->id;
	}

	/**
	 * Place a temporary 5-minute lock into an email queue item to give enough time for the current
	 * sending process to complete.
	 *
	 * @param object $queue_item A row from the followup_email_orders table
	 */
	public static function lock_email_queue( $queue_item ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		$key        = self::get_queue_item_lock_key( $queue_item );
		$lock_until = (int)gmdate('U') + 300;

		self::delete_lock_key_from_db( $key );
		$wpdb->insert(
			$wpdb->options,
			array( 'option_name' => $key, 'option_value' => $lock_until, 'autoload' => 'no' ),
			array( '%s', '%s', '%s' )
		);
	}

	/**
	 * Unlock a queue item
	 * @param object $queue_item
	 */
	public static function remove_queue_item_lock( $queue_item ) {
		$key = self::get_queue_item_lock_key( $queue_item );
		self::delete_lock_key_from_db( $key );
	}

	/**
	 * Get the lock value from the db
	 *
	 * @param string $key The lock key
	 * @return string
	 */
	public static function get_lock_from_db( $key ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		return $wpdb->get_var( $wpdb->prepare("SELECT option_value FROM $wpdb->options WHERE option_name = %s", $key) );
	}

	/**
	 * Delete a previously stored lock key from the wp_options table
	 *
	 * @param string $key
	 */
	public static function delete_lock_key_from_db( $key ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name = %s", $key) );
	}

	/**
	 * Check if a queue_item is locked or not
	 *
	 * @param object $queue_item
	 * @return bool
	 */
	public static function is_queue_item_locked( $queue_item ) {
		$locked         = false;
		$key            = self::get_queue_item_lock_key( $queue_item );
		$current_time   = gmdate('U');

		$lock = self::get_lock_from_db( $key );

		// no lock row found
		if ( !$lock )
			return $locked;

		if ( $lock > $current_time ) {
			$locked = true;
		} else {
			// lock time has passed, delete the lock row
			self::delete_lock_key_from_db( $key );
		}

		return $locked;
	}

	/**
	 * Get items from the email order queue
	 *
	 * @param array $args
	 * @return FUE_Sending_Queue_Item[]
	 */
	public function get_items( array $args ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		$fields     = (!empty( $args['fields'] )) ? $args['fields'] : 'all';
		$per_page   = (!empty( $args['limit'] )) ? absint( $args['limit'] ) : 0;
		$page       = (!empty( $args['page'] )) ? absint( $args['page'] ) : 0;

		unset( $args['limit'], $args['page'], $args['fields'] );

		$params = array();
		$sql    = "SELECT SQL_CALC_FOUND_ROWS id
				  FROM {$wpdb->prefix}followup_email_orders
				  WHERE 1=1";

		foreach ( $args as $field => $value ) {

			if ( $field == 'date_sent' ) {

				if ( is_array( $value ) ) {
					if ( !empty( $value['from'] ) ) {
						$sql .= " AND date_sent >= %s ";
						$params[] = $value['from'];
					}

					if ( !empty( $value['to'] ) ) {
						$sql .= " AND date_sent <= %s ";
						$params[] = $value['to'];
					}

				} else {
					// ignore
					continue;
				}

			} elseif ( $field == 'send_on' ) {

				if ( is_array( $value ) ) {
					if ( !empty( $value['from'] ) ) {
						$sql .= " AND send_on >= %s ";
						$params[] = $value['from'];
					}

					if ( !empty( $value['to'] ) ) {
						$sql .= " AND send_on <= %s ";
						$params[] = $value['to'];
					}

				} else {
					// ignore
					continue;
				}

			} elseif ( $field == 'meta' ) {
				$value = '%' . $value . '%';
				$sql .= " AND `$field` LIKE %s";
				$params[] = $value;
			} else {
				if ( is_array( $value ) ) {
					$sql .= " AND `$field` IN ('". implode('\',\'', array_filter( $value, 'esc_sql' ) ) ."')";
				} else {
					$sql .= " AND `$field` = %s";
					$params[] = $value;
				}
			}

		}

		if ( $per_page > 0 && $page > 0 ) {
			$start = ( $per_page * $page ) - $per_page;
			$sql .= " LIMIT $start, $per_page";
		}

		if ( !empty( $params ) ) {
			$sql = $wpdb->prepare( $sql, $params );
		}

		if ( $fields == 'ids' ) {
			return $wpdb->get_col( $sql );
		}

		$results    = $wpdb->get_results( $sql );
		$items      = array();

		foreach ( $results as $row ) {
			$items[] = new FUE_Sending_Queue_Item( $row->id );
		}

		return $items;
	}

	/**
	 * Delete an item from the email queue
	 *
	 * @param int $item_id
	 *
	 * @return bool
	 */
	public function delete_item( $item_id ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		// remove the existing schedule
		$this->unschedule_email( $item_id );

		$wpdb->query( $wpdb->prepare(
			"DELETE FROM {$wpdb->prefix}followup_email_orders
			WHERE id = %d",
			$item_id
		) );

		fue_debug_log( __( 'Deleted email from queue', 'follow_up_emails' ), $item_id );

		return true;
	}

	/**
	 * Add an email to the queue
	 *
	 * @param array     $values
	 * @param FUE_Email $email
	 * @param bool      $schedule_event Pass false to not register a new scheduled event
	 * @return int|WP_Error Queue ID or WP_Error on error
	 */
	public static function queue_email( $values, FUE_Email $email = null, $schedule_event = true ) {
		$defaults = array(
			'user_id'    => '',
			'user_email' => '',
			'is_cart'    => 0,
			'email_id'   => 0,
			'meta'       => '',
		);

		$values = wp_parse_args( $values, $defaults );

		if ( empty( $values['send_on'] ) ) {
			$values['send_on'] = $email->get_send_timestamp();
		}

		if ( ! is_null( $email ) ) {
			$values['email_id'] = $email->id;
		}

		if ( empty( $values['user_id'] ) && ! empty( $values['order_id'] ) ) {
			$order = WC_FUE_Compatibility::wc_get_order( $values['order_id'] );

			if ( $order ) {
				$values['user_id'] = WC_FUE_Compatibility::get_order_user_id( $order );
			}
		}

		fue_debug_log( __( 'Queued email', 'follow_up_emails' ), $values );
		return Follow_Up_Emails::instance()->scheduler->insert_email_order( $values, $schedule_event );

	}

	/**
	 * Insert a queue item to the followup_email_orders table.
	 *
	 * @since 1.0.0
	 * @version 4.5.2
	 *
	 * @param array $data
	 * @param bool  $schedule_event Pass false to not register a new scheduled event
	 * @return int|WP_Error The email order ID or WP_Error on error
	 */
	private function insert_email_order( $data, $schedule_event = true ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;
		$item = new FUE_Sending_Queue_Item();

		$data = apply_filters( 'fue_insert_email_order', $data );

		/**
		 * Filters whether to preempt email schduling return value.
		 *
		 * Returning a non-false value from the filter will short-circuit
		 * email scheduling with that value.
		 *
		 * @since 4.5.2
		 *
		 * @param false|WP_Error|int $preempt        Whether to preempt email
		 *                                           insertion's return value.
		 * @param array              $data           Email data.
		 * @param bool               $schedule_event Whether to schedule email
		 *                                           or not.
		 */
		$pre = apply_filters( 'pre_fue_insert_email_order', false, $data, $schedule_event );
		if ( false !== $pre ) {
			return $pre;
		}

		foreach ( $data as $field => $value ) {
			if ( isset( $item->$field ) ) {
				$item->$field = $value;
			}
		}

		// get the correct email address
		if ( $item->user_id > 0 && empty( $item->user_email ) ) {
			$user = new WP_User( $item->user_id );
			$item->user_email = $user->user_email;
		}

		if ( empty( $item->email_id ) ) {
			// to be able to queue emails without an email_id,
			// a meta named subscription_notification or daily_summary must be present
			if (
				isset( $item->meta['subscription_notification'] ) ||
				isset( $item->meta['daily_summary'] )
			) {
				$queue_id = $item->save();

				$message = sprintf(
					__('Email has been added to the queue (Queue ID #%d)', 'follow_up_emails'),
					$queue_id
				);
				$item->add_note( $message );

				if ( $schedule_event ) {
					$this->schedule_email( $queue_id, $item->send_on );
				}
			}
		}

		$email       = new FUE_Email( $item->email_id );
		$email_meta  = $email->meta;
		$adjust_date = false;

		// do not queue if email is missing the subject or the message
		if ( empty( $email->message ) && empty( $data['message'] ) ) {
			$error = __( 'Cannot schedule an email with a missing message', 'follow_up_emails' );
			fue_debug_log( $error, $data );
			return new WP_Error( 'fue_insert_email_order', $error );
		}

		$passed_conditions = $this->filter_conditions( $item );

		if ( is_wp_error( $passed_conditions ) ) {
			$error = $passed_conditions->get_error_message();
			fue_debug_log( $error );
			return new WP_Error( 'fue_insert_email_order', $error );
		}

		if ( ! empty( $email_meta ) ) {

			if ( isset( $email_meta['adjust_date'] ) && $email_meta['adjust_date'] == 'yes' ) {
				$adjust_date = true;
			}

			// send email only once
			if ( $email->type != 'manual' && isset($email_meta['one_time']) && $email_meta['one_time'] == 'yes' ) {
				$count_sent = $wpdb->get_var( $wpdb->prepare(
					"SELECT COUNT(*)
					FROM {$wpdb->prefix}followup_email_orders
					WHERE `user_email` = %s
					AND `email_id` = %d",
					$item->user_email,
					$item->email_id
				) );

				if ( $count_sent > 0 ) {
					// Do not send more of the same emails to this user.
					// translators: %s User email.
					$error = sprintf( __( 'One-time email has already been sent to %s', 'follow_up_emails' ), $item->user_email );
					fue_debug_log( $error );
					return new WP_Error( 'fue_insert_email_order', $error );
				}
			}
		}

		// adjust date only applies to non-guest orders
		if ( $adjust_date && $item->user_id ) {
			// check for similar existing and unsent email orders
			// and adjust the date to send instead of inserting a duplicate row
			$similar_emails = $this->get_items( array(
				'email_id'      => $item->email_id,
				'user_id'       => $item->user_id,
				'product_id'    => $item->product_id,
				'is_cart'       => $item->is_cart,
				'is_sent'       => 0
			) );

			if ( count( $similar_emails ) > 0 ) {
				$similar_email  = current( $similar_emails );
				$similar_item   = new FUE_Sending_Queue_Item( $similar_email->id );
				$similar_item->send_on = $item->send_on;
				$similar_item->save();

				// remove the existing schedule and save the new one
				$param = array( $similar_email->id );

				as_unschedule_action( 'sfn_followup_emails', $param, 'fue' );

				if ( $schedule_event ) {
					$this->schedule_email( $similar_email->id, $item->send_on );
				}

				$message = sprintf(
					/* translators: %1$d Similar item ID, %2$s New date to send item. */
					__( 'Similar queue item found (#%1$d). Adjusting schedule to send on %2$s', 'follow_up_emails' ),
					$similar_item->id,
					date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $item->send_on )
				);
				$similar_item->add_note( $message );
				fue_debug_log( $message );

				return $similar_item->id;
			}
		}

		$queue_id = $item->save();

		$message = sprintf(
			/* translators: %1$s Email name %2$d Queue ID. */
			__( 'The email "%1$s" has been added to the queue (Queue ID #%2$d)', 'follow_up_emails' ),
			$email->name,
			$queue_id
		);
		$item->add_note( $message );
		fue_debug_log( $message );

		if ( $schedule_event ) {
			$this->schedule_email( $queue_id, $item->send_on );
		}

		return $queue_id;
	}

	/**
	 * Get the parameters used in by action-scheduler in scheduling emails
	 *
	 * @param int $queue_id
	 * @return array
	 */
	public function get_scheduler_parameters( $queue_id ) {
		return apply_filters( 'fue_scheduler_params', array( absint( $queue_id ) ), $queue_id, $this );
	}

	/**
	 * Schedule an email for sending via action-scheduler
	 *
	 * @param int   $queue_id
	 * @param int   $timestamp
	 * @param bool  $convert_to_gmt ActionScheduler uses GMT to schedule the events
	 * @return void
	 */
	public function schedule_email( $queue_id, $timestamp, $convert_to_gmt = true ) {

		$param = $this->get_scheduler_parameters( $queue_id );

		$local_timestamp = $timestamp;

		if ( $convert_to_gmt ) {
			// because ActionScheduler uses GMT for scheduling events, convert the send date to GMT
			$timestamp = strtotime( get_gmt_from_date( date( 'Y-m-d H:i:s', $timestamp ) ) );
		}

		as_schedule_single_action( $timestamp, 'sfn_followup_emails', $param, 'fue' );

		$item = new FUE_Sending_Queue_Item( $queue_id );

		$message = sprintf(
			/* translators: %s Send date. */
			__( 'Email is scheduled to be sent on %s', 'follow_up_emails' ),
			date( 'Y-m-d H:i:s', $local_timestamp )
		);
		$item->add_note( $message );
		fue_debug_log( $message, $queue_id );
	}

	/**
	 * Remove the scheduled action to prevent the email from sending
	 *
	 * @param int $queue_id
	 * @return void
	 */
	public function unschedule_email( $queue_id ) {
		$param = $this->get_scheduler_parameters( $queue_id );
		as_unschedule_action( 'sfn_followup_emails', $param, 'fue' );

		$item = new FUE_Sending_Queue_Item( $queue_id );

		if ( $item->exists() ) {
			$message = __( 'Email has been removed from the scheduler', 'follow_up_emails' );
			$item->add_note( $message );
			fue_debug_log( $message, $queue_id );
		}
	}

	/**
	 * Schedule unsent emails to use action-scheduler
	 *
	 * @param int $pos
	 * @param int $length
	 *
	 * @return bool|int
	 */
	public static function action_scheduler_import( $pos = 0, $length = 0 ) {
		$pos    = intval( $pos );
		$length = intval( $length );

		$wpdb = Follow_Up_Emails::instance()->wpdb;

		if ( $length > 0 )
			$rows = $wpdb->get_results("SELECT id, send_on FROM {$wpdb->prefix}followup_email_orders WHERE is_sent = 0 ORDER BY id ASC LIMIT $pos, $length");
		else
			$rows = $wpdb->get_results("SELECT id, send_on FROM {$wpdb->prefix}followup_email_orders WHERE is_sent = 0 ORDER BY id ASC");

		if (! $rows ) {
			return false;
		}

		foreach ( $rows as $row ) {
			$data = array( $row->id );

			$job_id = as_schedule_single_action( $row->send_on, 'sfn_followup_emails', $data, 'fue' );

			$pos++;
		}

		return $pos;
	}

}
