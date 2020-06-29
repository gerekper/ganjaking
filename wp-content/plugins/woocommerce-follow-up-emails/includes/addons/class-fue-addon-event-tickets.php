<?php

class FUE_Addon_Event_Tickets extends  FUE_Addon_Wootickets {

	public function __construct() {
		if (self::is_installed()) {
			$this->register_hooks();
			
			add_action( 'event_tickets_rsvp_tickets_generated_for_product', array($this, 'queue_rsvp_emails'), 10, 3 );
			add_action( 'event_tickets_rsvp_tickets_generated', array($this, 'send_event_rsvp_booking_notification' ));

			add_filter( 'fue_send_email_data', array( $this, 'apply_rsvp_data' ), 50, 3 );
		}
	}

	public static function is_installed() {
		return class_exists('Tribe__Tickets__Main');
	}

	/**
	 * Register custom email type
	 *
	 * @param array $types
	 * @return array
	 */
	public function register_email_type( $types ) {
		$triggers = array(
			'after_tribe_rsvp'          => __('after RSVP', 'follow_up_emails'),
			'before_tribe_event_starts' => __('before event starts', 'follow_up_emails'),
			'after_tribe_event_ends'    => __('after event ends', 'follow_up_emails')
		);

		$statuses = Follow_Up_Emails::instance()->fue_wc->get_order_statuses();

		foreach ( $statuses as $status ) {
			$triggers[ 'ticket_status_'. $status ] = sprintf(
				__('after ticket status: %s', 'follow_up_emails'),
				$status
			);
		}

		$props = array(
			'label'                 => __('Events Emails', 'follow_up_emails'),
			'singular_label'        => __('Events Email', 'follow_up_emails'),
			'triggers'              => $triggers,
			'durations'             => Follow_Up_Emails::$durations,
			'long_description'      => __(
				'Create emails based upon the event/ticket status for The Events Calendar.',
				'follow_up_emails'
			),
			'short_description'     => __(
				'Create emails based upon the event/ticket status for The Events Calendar.',
				'follow_up_emails'
			)
		);
		$types[] = new FUE_Email_Type( 'wootickets', $props );

		return $types;
	}

	/**
	 * Add 'after_tribe_rsvp', 'before_tribe_event_starts' and 'after_tribe_event_ends' emails to the queue
	 *
	 * @param int $product_id
	 * @param int $order_id
	 * @param int $quantity
	 */
	public function queue_rsvp_emails( $product_id, $order_id, $quantity ) {
		global $wpdb;

		$email_ids = $wpdb->get_col(
			"SELECT p.ID
			FROM {$wpdb->posts} p, {$wpdb->postmeta} pm
			WHERE p.post_type = 'follow_up_email'
			AND p.post_status = '". FUE_Email::STATUS_ACTIVE ."'
			AND pm.post_id = p.ID
			AND pm.meta_key = '_interval_type'
			AND pm.meta_value IN ('after_tribe_rsvp', 'before_tribe_event_starts', 'after_tribe_event_ends')
			ORDER BY menu_order ASC"
		);

		if ( empty( $email_ids ) ) {
			return;
		}

		$attendee_id = $wpdb->get_var($wpdb->prepare(
			"SELECT post_id
			FROM {$wpdb->postmeta}
			WHERE meta_key = '_tribe_rsvp_order'
			AND meta_value = %s LIMIT 1",
			$order_id
		));

		if ( $attendee_id ) {
			$full_name  = get_post_meta( $attendee_id, '_tribe_rsvp_full_name', true );
			$user_email = get_post_meta( $attendee_id, '_tribe_rsvp_email', true );

			foreach ( $email_ids as $email_id ) {
				$email = new FUE_Email( $email_id );

				$search = Follow_Up_Emails::instance()->scheduler->get_items(array(
					'user_email'    => $user_email,
					'email_id'      => $email_id,
					'meta'          => $order_id,
					'is_sent'       => 0
				));

				if ( count( $search ) > 0 ) {
					continue;
				}

				$insert = array(
					'order_id'      => 0,
					'product_id'    => $product_id,
					'user_email'    => $user_email,
					'meta'          => array(
						'rsvp_order_id' => $order_id,
						'name'          => $full_name,
						'email'         => $user_email,
						'quantity'      => $quantity
					)
				);

				FUE_Sending_Scheduler::queue_email( $insert, $email );
			}

		}
	}

	/**
	 * Apply the correct email and name for RSVP emails
	 *
	 * @param string                    $email_data
	 * @param FUE_Sending_Queue_Item    $queue_item
	 * @param FUE_Email                 $email
	 * @return array
	 */
	public function apply_rsvp_data( $email_data, $queue_item, $email ) {
		if ( $email->trigger == 'after_tribe_rsvp' ) {
			if ( $queue_item->meta['email'] ) {
				$email_data['email_to'] = $queue_item->meta['email'];
			}

			if ( $queue_item->meta['name'] ) {
				$email_data['cname'] = $queue_item->meta['name'];
			}
		}

		return $email_data;
	}

	/**
	 * @param $order_id
	 */
	public function send_event_rsvp_booking_notification( $order_id ) {
		global $wpdb;

		$enabled    = get_option( 'fue_event_booking_notification', 0 );
		$emails     = get_option( 'fue_event_booking_notification_emails' );
		$schedule   = get_option( 'fue_event_booking_notification_schedule', 'instant' );

		if ( !$enabled || empty( $emails ) || $schedule != 'instant' ) {
			return;
		}

		$attendee_id = $wpdb->get_var($wpdb->prepare(
			"SELECT post_id
			FROM {$wpdb->postmeta}
			WHERE meta_key = '_tribe_rsvp_order'
			AND meta_value = %s",
			$order_id
		));

		ob_start();
		include FUE_TEMPLATES_DIR .'/add-ons/wootickets-rsvp-event-email.php';
		$message = ob_get_clean();

		$subject        = __('New Event Booking', 'follow_up_emails');
		$message        = WC()->mailer()->wrap_message( $subject, $message );
		$wc_email       = new WC_Email();
		$message        = $wc_email->style_inline( $message );

		$queue = new FUE_Sending_Queue_Item();
		$queue->email_trigger = $subject;
		$queue->status = 1;
		$queue->meta = array(
			'email'     => $emails,
			'subject'   => $subject,
			'message'   => $message
		);
		$queue->save();

		Follow_Up_Emails::instance()->mailer->send_adhoc_email( $queue );
	}

	/**
	 * Scan through the keys of $variables and apply the replacement if one is found
	 * @param array                     $variables
	 * @param array                     $email_data
	 * @param FUE_Sending_Queue_Item    $queue_item
	 * @param FUE_Email                 $email
	 * @return array
	 */
	protected function add_variable_replacements( $variables, $email_data, $queue_item, $email ) {
		$ticket_id      = $queue_item->product_id;
		$ticket_qty     = '';
		$ticket_qtys    = '';

		if (! $ticket_id )
			return $variables;

		if ( $queue_item->order_id ) {
			$order      = WC_FUE_Compatibility::wc_get_order( $queue_item->order_id );
			$event_id   = get_post_meta( $ticket_id, '_tribe_wooticket_for_event', true );

			if ( $email->product_id > 0 ) {
				$ticket_qty = $this->get_line_item_quantity( $ticket_id, $order );
			} else {
				// load the ticket_quantities variable HTML template
				ob_start();
				fue_get_template(
					'ticket-quantities.php',
					array('items' => $order->get_items()),
					'follow-up-emails/email-variables/',
					FUE_TEMPLATES_DIR .'/email-variables/'
				);
				$ticket_qtys = ob_get_clean();
			}
		} else {
			$event_id = get_post_meta( $ticket_id, '_tribe_rsvp_for_event', true );

			if ( $email->product_id > 0 ) {
				$ticket_qty = $queue_item->meta['quantity'];
			} else {
				$rsvp_tickets = $this->get_rsvp_tickets( $queue_item->meta['rsvp_order_id'] );

				// load the ticket_quantities variable HTML template
				ob_start();
				fue_get_template(
					'ticket-quantities.php',
					array('items' => $rsvp_tickets),
					'follow-up-emails/email-variables/',
					FUE_TEMPLATES_DIR .'/email-variables/'
				);
				$ticket_qtys = ob_get_clean();
			}
		}

		if ( class_exists( 'Tribe__Tickets_Plus__Commerce__WooCommerce__Main' ) ) {
			$woo_tickets    = Tribe__Tickets_Plus__Commerce__WooCommerce__Main::get_instance();
			$ticket         = $woo_tickets->get_ticket( $event_id, $ticket_id );
			if ( is_a( $ticket, 'Tribe__Tickets__Ticket_Object' ) ) {
				// Ticket Vars
				$ticket_sale_start  = '';
				$ticket_sale_end    = '';

				if ( $ticket->start_date ) {
					$ticket_sale_start = date_i18n( wc_date_format() .' '. wc_time_format(), strtotime( $ticket->start_date ) );
				}

				if ( $ticket->end_date ) {
					$ticket_sale_end = date_i18n( wc_date_format() .' '. wc_time_format(), strtotime( $ticket->end_date ) );
				}

				$variables['ticket_name']           = $ticket->name;
				$variables['ticket_description']    = $ticket->description;
				$variables['ticket_sale_start']     = $ticket_sale_start;
				$variables['ticket_sale_end']       = $ticket_sale_end;
				$variables['ticket_cost']           = wc_price( $ticket->price );
				$variables['ticket_excerpt']        = $ticket->description;
			}
		}

		$event = $this->get_event_data( $event_id );

		$variables['event_name']            = $event['name'];
		$variables['event_start_datetime']  = $event['start_datetime'];
		$variables['event_end_datetime']    = $event['end_datetime'];
		$variables['event_link']            = $event['link'];
		$variables['event_url']             = fue_replacement_url_var( $event['url'] );
		$variables['event_location']        = $event['location'];
		$variables['event_organizer']       = $event['organizer'];
		$variables['event_venue_phone']     = $event['venue_phone'];
		$variables['event_gcal']            = $event['gcal'];
		$variables['event_ical']            = $event['ical'];
		$variables['ticket_image']          = $this->get_ticket_thumbnail( $ticket_id );
		$variables['ticket_quantity']       = $ticket_qty;
		$variables['ticket_quantities']     = $ticket_qtys;

		return $variables;
	}

	protected function get_rsvp_tickets( $order_id ) {
		global $wpdb;

		$items = array();

		$attendee_ids = $wpdb->get_col($wpdb->prepare(
			"SELECT post_id
			FROM {$wpdb->postmeta}
			WHERE meta_key = '_tribe_rsvp_order'
			AND meta_value = %s",
			$order_id
		));

		if ( $attendee_ids ) {
			$product_quantities = array();
			foreach ( $attendee_ids as $attendee_id ) {
				$product_id = get_post_meta( $attendee_id, '_tribe_rsvp_product', true );

				if ( $product_id ) {
					isset( $product_quantities[ $product_id ] )
						? $product_quantities[ $product_id ]++
						: $product_quantities[ $product_id ] = 1;

				}
			}

			foreach ( $product_quantities as $product_id => $quantity ) {
				$items[] = array(
					'product_id'    => $product_id,
					'qty'           => $quantity,
					'name'          => get_the_title( $product_id )
				);
			}
		}

		return $items;
	}

}

$GLOBALS['fue_event_tickets'] = new FUE_Addon_Event_Tickets();
