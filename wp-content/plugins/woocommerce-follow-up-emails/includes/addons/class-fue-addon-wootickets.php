<?php

/**
 * Class FUE_Addon_Wootickets
 */
class FUE_Addon_Wootickets {

	/**
	 * class constructor
	 */
	public function __construct() {
		if (self::is_installed()) {
			$this->register_hooks();
		}
	}

	protected function register_hooks() {
		add_filter( 'fue_email_types', array($this, 'register_email_type') );

		// trigger fields
		add_filter( 'fue_email_form_trigger_fields', array($this, 'register_trigger_fields') );

		// email list - import order action link
		add_action( 'fue_import_orders_supported_types', array($this, 'add_import_support') );
		add_filter( 'fue_wc_get_orders_for_email', array($this, 'get_orders_for_email'), 10, 2 );
		add_filter( 'fue_wc_import_insert', array($this, 'modify_insert_send_date'), 10, 2 );

		// saving email
		add_filter( 'fue_save_email_data', array($this, 'apply_ticket_product_id'), 10, 3 );

		add_filter( 'fue_trigger_str', array($this, 'trigger_string'), 10, 2 );
		add_action( 'fue_email_form_scripts', array($this, 'email_form_script') );

		// manual emails
		add_action( 'fue_manual_types', array($this, 'manual_types') );
		add_action( 'fue_manual_type_actions', array($this, 'manual_type_actions') );
		add_action( 'fue_manual_js', array($this, 'manual_js') );
		add_filter( 'fue_manual_email_recipients', array($this, 'manual_email_recipients'), 10, 2 );

		add_action( 'fue_email_variables_list', array($this, 'add_variables') );

		add_action( 'fue_email_form_after_interval', array($this, 'after_interval') );

		add_action( 'fue_before_variable_replacements', array($this, 'register_variable_replacements'), 10, 4 );

		add_action( 'woocommerce_order_status_completed', array($this, 'set_reminders'), 20 );
		add_action( 'init', array($this, 'hook_statuses') );

		add_filter( 'fue_skip_email_sending', array( $this, 'maybe_skip_sending' ), 10, 3 );

		// settings page
		add_action( 'fue_settings_integration', array($this, 'settings_form') );
		add_action( 'fue_settings_saved', array( $this, 'save_settings' ), 10, 1 );

		// summary email hook
		add_action( 'fue_wootickets_digest_email', array($this, 'send_email_digest') );

		// Export attendees to a newsletter list
		add_action( 'admin_head', array( $this, 'attendees_table_script' ) );
		add_filter( 'tribe_events_tickets_attendees_table_bulk_actions', array( $this, 'add_bulk_actions' ) );
		add_action( 'tribe_events_tickets_attendees_table_process_bulk_action', array( $this, 'process_bulk_action' ) );
	}

	/**
	 * Check if plugins is active
	 * @return bool
	 */
	public static function is_installed() {
		return function_exists('wootickets_init');
	}

	/**
	 * Register custom email type
	 *
	 * @param array $types
	 * @return array
	 */
	public function register_email_type( $types ) {
		$triggers = array(
			'before_tribe_event_starts' => __('before event starts', 'follow_up_emails'),
			'after_tribe_event_ends'    => __('after event ends', 'follow_up_emails')
		);

		$statuses = Follow_Up_Emails::instance()->fue_wc->get_order_statuses();

		foreach ( $statuses as $i => $status ) {
			$triggers[ 'ticket_status_'. $status ] = sprintf(
				__('after ticket status: %s', 'follow_up_emails'),
				$status
			);
		}

		$props = array(
			'label'                 => __('WooTickets', 'follow_up_emails'),
			'singular_label'        => __('WooTickets', 'follow_up_emails'),
			'triggers'              => $triggers,
			'durations'             => Follow_Up_Emails::$durations,
			'long_description'      => __(
				'Create emails based upon the event/ticket status for The Events Calendar.<br />Increase revenue with a
				custom lifecycle marketing program from Outbound Commerce. It’s email marketing for busy eCommerce
				businesses built by experienced eCommerce and marketing professionals.',
				'follow_up_emails'
			),
			'short_description'     => __(
				'Not sure where to start? Let Outbound Commerce help. Get email marketing for busy eCommerce businesses
				built by experienced eCommerce and marketing professionals.',
				'follow_up_emails'
			)
		);
		$types[] = new FUE_Email_Type( 'wootickets', $props );

		return $types;
	}

	/**
	 * Trigger string for custom events
	 *
	 * @param string $string
	 * @param FUE_Email $email
	 * @return string
	 */
	public function trigger_string( $string, $email ) {
		if ( $email->trigger == 'before_tribe_event_starts' || $email->trigger == 'after_tribe_event_ends' ) {
			$type = $email->get_email_type();
			$string = sprintf(
				__('%d %s %s'),
				$email->interval,
				Follow_Up_Emails::get_duration($email->duration),
				$type->get_trigger_name( $email->trigger )
			);
		}
		return $string;
	}

	/**
	 * JS for the email form
	 */
	public function email_form_script() {
		wp_enqueue_script( 'fue-form-the-events-calendar', FUE_TEMPLATES_URL .'/js/email-form-the-events-calendar.js' );
	}

	/**
	 * Add event attendees action for manual emails
	 */
	public function manual_types() {
		?><option value="event_attendees"><?php esc_html_e('Event Attendees', 'follow_up_emails'); ?></option><?php
	}

	/**
	 * Fields to show if event_attendees is selected
	 */
	public function manual_type_actions() {
		$events = array();

		$posts = get_posts( array(
			'post_type'     => 'tribe_events',
			'post_status'   => 'publish',
			'posts_per_page'      => -1
		) );

		?>
		<div class="send-type-event-attendees send-type-div">
			<select id="event_id" name="event_id" class="select2" style="width: 400px;">
				<?php foreach ( $posts as $post ): ?>
					<option value="<?php echo esc_attr( $post->ID ); ?>"><?php echo esc_html( $post->post_title ); ?></option>
				<?php endforeach; ?>
			</select>
			<select id="attendee_status" name="attendee_status">
				<option value="all"><?php esc_html_e('Send to all attendees', 'follow_up_emails'); ?></option>
				<option value="checked_in"><?php esc_html_e('Send to checked in attendees', 'follow_up_emails'); ?></option>
				<option value="not_checked_in"><?php esc_html_e('Send to attendees who have not yet checked in', 'follow_up_emails'); ?></option>
			</select>
		</div>
	<?php
	}

	/**
	 * Javascript code for manual emails
	 */
	public function manual_js() {
		?>
		jQuery( '#send_type' ).on( 'change', function() {
			switch (jQuery(this).val()) {
				case "event_attendees":
					jQuery(".send-type-event-attendees").show();
					break;
			}
		} );
	<?php
	}

	/**
	 * Add attendees matching the manual email's conditions
	 *
	 * @param array $recipients
	 * @param array $post
	 * @return array
	 */
	public function manual_email_recipients( $recipients, $post ) {
		if ( $post['send_type'] == 'event_attendees' ) {
			$items = Tribe__Tickets__Tickets::get_event_attendees( $post['event_id'] );

			if ( $items ) {
				foreach ( $items as $item ) {
					$key        = $item['attendee_id'];
					$user_id    = 0;
					$name       = $item['purchaser_name'];
					$email      = $item['purchaser_email'];
					$value      = array( $user_id, $name, $email );

					if ( $post['attendee_status'] == 'checked_in' && $item['check_in'] != 1 ) {
						continue;
					} elseif ( $post['attendee_status'] == 'not_checked_in' && $item['check_in'] == 1 ) {
						continue;
					}

					$recipients[ $key ] = $value;
				}
			}
		}

		return $recipients;
	}

	/**
	 * Add ticket selector to the Trigger tab
	 *
	 * @param FUE_Email $email
	 */
	public function register_trigger_fields( $email ) {
		if ( $email->type == 'wootickets' || $email->type == 'twitter' ) {
			$wootickets_type = (empty($email->meta['wootickets_type'])) ? 'all' : $email->meta['wootickets_type'];
			$categories = get_terms( 'product_cat', array('hide_empty' => false) );

			if ( !empty( $email->product_id ) ) {
				$wootickets_type = 'products';
			}

			include FUE_TEMPLATES_DIR .'/email-form/the-events-calendar/event-selector.php';
		}
	}

	/**
	 * Declare order importing support for wootickets emails
	 * @param array $types
	 * @return array
	 */
	public function add_import_support( $types ) {
		$types[] = 'wootickets';

		return $types;
	}

	/**
	 * Get orders that match the $email's criteria
	 * @param array     $order_ids Matching Order IDs
	 * @param FUE_Email $email
	 * @return array
	 */
	public function get_orders_for_email( $order_ids, $email ) {
		if ( $email->type != 'wootickets' ) {
			return $order_ids;
		}

		$wpdb = Follow_Up_Emails::instance()->wpdb;

		if ( $email->product_id ) {
			$order_ids = $wpdb->get_col($wpdb->prepare(
				"SELECT DISTINCT order_id
				FROM {$wpdb->prefix}followup_order_items
				WHERE product_id = %d
				OR variation_id = %d",
				$email->product_id,
				$email->product_id
			));
		} elseif ( $email->category_id ) {
			$order_ids = $wpdb->get_col($wpdb->prepare(
				"SELECT DISTINCT order_id
				FROM {$wpdb->prefix}followup_order_categories
				WHERE category_id = %d",
				$email->category_id
			));
		} else {
			$ticket_ids = $wpdb->get_col(
				"SELECT DISTINCT post_id
				FROM {$wpdb->postmeta}
				WHERE meta_key = '_tribe_wooticket_for_event'
				AND meta_value > 0"
			);

			if ( !empty( $ticket_ids ) ) {
				$ids_csv = implode( ',', array_map( 'absint', $ticket_ids ) );

				$order_ids = $wpdb->get_col(
					"SELECT DISTINCT order_id
					FROM {$wpdb->prefix}followup_order_items
					WHERE product_id IN ( {$ids_csv} )
					OR variation_id IN ( {$ids_csv} )"
				);
			}
		}

		if ( empty( $order_ids ) ) {
			return array();
		}

		return array( $email->id => $order_ids );
	}

	/**
	 * Change the send date of the email relative to the event's start and end dates
	 * @param array $insert
	 * @param FUE_Email $email
	 * @return array|bool
	 */
	public function modify_insert_send_date( $insert, $email ) {
		if ( $email->type != 'wootickets' ) {
			return $insert;
		}

		$now        = current_time('timestamp');
		$ticket_id  = $this->get_ticket_id_from_order( $insert['order_id'], $email );

		if ( !$ticket_id ) {
			return $insert;
		}

		// if $item is a ticket, load the event where the ticket is attached to
		$event_id = get_post_meta( $ticket_id, '_tribe_wooticket_for_event', true );

		$interval   = (int)$email->interval_num;
		$add        = FUE_Sending_Scheduler::get_time_to_add( $interval, $email->interval_duration );

		if ( $email->trigger == 'before_tribe_event_starts' ) {
			$start = get_post_meta( $event_id, '_EventStartDate', true );

			if ( empty($start) ) {
				return $insert;
			}
			$start = strtotime($start);

			// check if a limit is in place
			$email_meta = maybe_unserialize( $email->meta );
			if (
				isset($email_meta['tribe_limit'], $email_meta['tribe_limit_days'])
				&& !empty($email_meta['tribe_limit_days'])
			) {
				$days = ($start - $now) / 86400;

				if ( $days <= $email_meta['tribe_limit_days'] ) {
					// $days is within limit - skip
					return false;
				}
			}

			$send_on    = $start - $add;

			// if send_on is in the past, do not queue it
			if ( $now > $send_on ) {
				return false;
			}

			$insert['send_on'] = $send_on;
		} else {
			$end        = get_post_meta( $event_id, '_EventEndDate', true );

			if ( empty($end) ) {
				return $insert;
			}

			$end        = strtotime($end);
			$send_on    = $end + $add;

			// if send_on is in the past, do not queue it
			if ( $now > $send_on ) {
				return false;
			}

			$insert['send_on'] = $send_on;
		}

		return $insert;
	}

	/**
	 * Pull the ticket ID from the order that triggered the FUE_Email
	 *
	 * @param int $order_id
	 * @param FUE_Email $email
	 * @return int
	 */
	public function get_ticket_id_from_order( $order_id, $email ) {
		$ticket_id = 0;

		if ( $email->product_id ) {
			$ticket_id = $email->product_id;
		} else {
			$order = WC_FUE_Compatibility::wc_get_order( $order_id );
			$items = $order->get_items();

			foreach ( $items as $item ) {
				$event_id = get_post_meta( $item['product_id'], '_tribe_wooticket_for_event', true );

				if ( empty( $event_id ) ) {
					continue;
				}

				if ( $email->category_id ) {
					// get the first ticket product that matches the category id
					$categories = wp_get_object_terms( $item['product_id'], 'product_cat' );

					if ( !is_wp_error( $categories ) ) {
						foreach ( $categories as $category ) {
							if ( $category->term_id == $email->category_id ) {
								$ticket_id = $item['product_id'];
								break 2;
							}
						}
					}
				} else {
					// get the first ticket product
					$ticket_id = $item['product_id'];
					break;
				}
			}
		}

		return apply_filters( 'fue_get_ticket_id_from_order', $ticket_id, $order_id, $email );
	}

	/**
	 * Apply the value of 'ticket_product_id' to the 'product_id' field
	 *
	 * @param array     $data
	 * @param int       $post_id
	 * @param WP_Post   $post
	 * @return array $data
	 */
	public function apply_ticket_product_id( $data, $post_id, $post ) {

		if ( $data['type'] == 'wootickets' || $data['type'] == 'twitter' ) {

			// Check the nonce.
			if ( empty( $_POST['fue_meta_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['fue_meta_nonce'] ), 'fue_save_data' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				return $data;
			}
			if ( isset( $_POST['meta']['wootickets_type'] ) && $_POST['meta']['wootickets_type'] === 'all' ) {
				$data['product_id']     = 0;
				$data['category_id']    = 0;
			} else {
				if ( !empty( $_POST['ticket_product_id'] ) ) {
					$data['product_id']     = sanitize_text_field( wp_unslash( $_POST['ticket_product_id'] ) );
					$data['category_id']    = 0;
				} else {
					if ( isset( $_POST['meta']['wootickets_type'] ) ) {
						switch ( $_POST['meta']['wootickets_type'] ) {
							case 'categories':
								if ( isset( $_POST['ticket_category_id'] ) ) {
									$data['category_id'] = sanitize_text_field( wp_unslash( $_POST['ticket_category_id'] ) );
									$data['product_id']  = 0;
								}
								break;

							case 'event_categories':
								if ( isset( $_POST['ticket_event_category_id'] ) ) {
									$data['category_id'] = sanitize_text_field( wp_unslash( $_POST['ticket_event_category_id'] ) );
									$data['product_id']  = 0;
								}
								break;
						}
					}

				}
			}

		}

		return $data;
	}

	/**
	 * Available email variables
	 */
	public function add_variables( $email ) {
		global $woocommerce;

		if ( $email->type == 'wootickets' ):
		?>
			<li class="var hideable var_events_calendar var_event_name">
				<strong>{event_name}</strong>
				<img
					class="help_tip"
					title="<?php esc_attr_e( 'The name of the event', 'follow_up_emails' ); ?>"
					src="<?php echo esc_url( $woocommerce->plugin_url() ) ; ?>/assets/images/help.png"
					width="16"
					height="16" />
			</li>
			<li class="var hideable var_events_calendar var_event_link">
				<strong>{event_link}</strong>
				<img
					class="help_tip"
					title="<?php esc_attr_e( 'The name of the event with a link to the event page', 'follow_up_emails' ); ?>"
					src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png"
					width="16"
					height="16"
					/>
			</li>
			<li class="var hideable var_events_calendar var_event_url">
				<strong>{event_url}</strong>
				<img
					class="help_tip"
					title="<?php esc_attr_e( 'The URL of the event', 'follow_up_emails' ); ?>"
					src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png"
					width="16"
					height="16" />
			</li>
			<li class="var hideable var_events_calendar var_event_location">
				<strong>{event_location}</strong>
				<img
					class="help_tip"
					title="<?php esc_attr_e( 'The name and address of the venue', 'follow_up_emails' ); ?>"
					src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png"
					width="16"
					height="16" />
			</li>
			<li class="var hideable var_events_calendar var_event_organizer">
				<strong>{event_organizer}</strong>
				<img
					class="help_tip"
					title="<?php esc_attr_e( 'The name of the event organizer', 'follow_up_emails' ); ?>"
					src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png"
					width="16"
					height="16" />
			</li>
			<li class="var hideable var_events_calendar var_event_start_datetime">
				<strong>{event_start_datetime}</strong>
				<img
					class="help_tip"
					title="<?php esc_attr_e( 'The start date/time of the event', 'follow_up_emails' ); ?>"
					src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png"
					width="16"
					height="16" />
			</li>
			<li class="var hideable var_events_calendar var_event_end_datetime">
				<strong>{event_end_datetime}</strong>
				<img
					class="help_tip"
					title="<?php esc_attr_e( 'The end date/time of the event', 'follow_up_emails' ); ?>"
					src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png"
					width="16"
					height="16" />
			</li>
			<li class="var hideable var_events_calendar var_event_venue_phone">
				<strong>{event_venue_phone}</strong>
				<img
					class="help_tip"
					title="<?php esc_attr_e( 'The phone number of the event venue', 'follow_up_emails' ); ?>"
					src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png"
					width="16"
					height="16" />
			</li>
			<li class="var hideable var_events_calendar var_event_gcal">
				<strong>{event_gcal}</strong>
				<img
					class="help_tip"
					title="<?php esc_attr_e( 'The URL to the Google Calendar linked to the even', 'follow_up_emails' ); ?>"
					src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png"
					width="16"
					height="16" />
			</li>
			<li class="var hideable var_events_calendar var_event_ical">
				<strong>{event_ical}</strong>
				<img
					class="help_tip"
					title="<?php esc_attr_e( 'The URL to the iCalendar linked to the event', 'follow_up_emails' ); ?>"
					src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png"
					width="16"
					height="16" />
			</li>
			<li class="var hideable var_events_calendar var_event_ticket_sale_start">
				<strong>{ticket_sale_start}</strong>
				<img
					class="help_tip"
					title="<?php esc_attr_e( 'The date/time that tickets go on sale', 'follow_up_emails' ); ?>"
					src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png"
					width="16"
					height="16" />
			</li>
			<li class="var hideable var_events_calendar var_event_ticket_sale_end">
				<strong>{ticket_sale_end}</strong>
				<img
					class="help_tip"
					title="<?php esc_attr_e( 'The date/time that ticket sales end', 'follow_up_emails' ); ?>"
					src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png"
					width="16"
					height="16"
					/>
			</li>
			<li class="var hideable var_events_calendar var_event_ticket_name">
				<strong>{ticket_name}</strong>
				<img
					class="help_tip"
					title="<?php esc_attr_e('The name of the ticket', 'follow_up_emails'); ?>"
					src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png"
					width="16"
					height="16"
					/>
			</li>
			<li class="var hideable var_events_calendar var_event_ticket_image">
				<strong>{ticket_image}</strong>
				<img
					class="help_tip"
					title="<?php esc_attr_e( 'The image assigned to the ticket', 'follow_up_emails' ); ?>"
					src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png"
					width="16"
					height="16"
					/>
			</li>
			<li class="var hideable var_events_calendar var_event_ticket_quantity">
				<strong>{ticket_quantity}</strong>
				<img
					class="help_tip"
					title="<?php esc_attr_e( 'The name of the ticket', 'follow_up_emails' ); ?>"
					src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png"
					width="16"
					height="16" />
			</li>
			<li class="var hideable var_events_calendar var_event_ticket_quantities">
				<strong>{ticket_quantities}</strong>
				<img
					class="help_tip"
					title="<?php esc_attr_e( 'The name of the ticket', 'follow_up_emails' ); ?>"
					src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png"
					width="16"
					height="16" />
			</li>
			<li class="var hideable var_events_calendar var_event_ticket_cost">
				<strong>{ticket_cost}</strong>
				<img
					class="help_tip"
					title="<?php esc_attr_e( 'The name of the ticket', 'follow_up_emails' ); ?>"
					src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png"
					width="16"
					height="16" />
			</li>
			<li class="var hideable var_events_calendar var_event_ticket_excerpt">
				<strong>{ticket_excerpt}</strong>
				<img
					class="help_tip"
					title="<?php esc_attr_e( 'The name of the ticket', 'follow_up_emails' ); ?>"
					src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png"
					width="16"
					height="16" />
			</li>
			<li class="var hideable var_events_calendar var_event_ticket_description">
				<strong>{ticket_description}</strong>
				<img
					class="help_tip"
					title="<?php esc_attr_e('The description of the ticket', 'follow_up_emails'); ?>"
					src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png"
					width="16"
					height="16" />
			</li>
		<?php
		endif;
	}

	/**
	 * Addition option for ticket emails
	 * @param array $defaults
	 */
	public function after_interval( $defaults ) {
		if ( $defaults['type'] != 'wootickets') {
			return;
		}

		$days = (isset($defaults['meta']['tribe_limit_days']) ) ? $defaults['meta']['tribe_limit_days'] : '';
		$tribe_limit = (isset($defaults['meta']['tribe_limit']) && $defaults['meta']['tribe_limit'] == 'yes');
		?>
		<div class="field tribe_limit_tr">
			<label for="meta_tribe_limit">
				<input
					type="checkbox"
					name="meta[tribe_limit]"
					id="meta_tribe_limit"
					value="yes"
					<?php checked( true, $tribe_limit ); ?>
					style="vertical-align: baseline;" />
				<?php
				printf(
					esc_html__(
						'Do not send email if a customer books a ticket %s days before the event starts.',
						'follow_up_emails'
					),
					'<input type="text" name="meta[tribe_limit_days]" size="2" value="'. esc_attr( $days ) .'" placeholder="5" />'
				);
				?>
			</label>
		</div>
		<?php
	}

	/**
	 * Register subscription variables to be replaced
	 *
	 * @param FUE_Sending_Email_Variables   $var
	 * @param array                 $email_data
	 * @param FUE_Email             $email
	 * @param object                $queue_item
	 */
	public function register_variable_replacements( $var, $email_data, $email, $queue_item ) {
		$variables = array(
			'event_name', 'event_start_datetime', 'event_end_datetime', 'event_link', 'event_url',
			'event_location', 'event_organizer', 'ticket_name', 'ticket_description',
			'event_venue_phone', 'event_gcal', 'event_ical', 'ticket_sale_start', 'ticket_sale_end',
			'ticket_image', 'ticket_quantity', 'ticket_quantities', 'ticket_cost', 'ticket_excerpt'
		);

		// use test data if the test flag is set
		if ( isset( $email_data['test'] ) && $email_data['test'] ) {
			$variables = $this->add_test_variable_replacements( $variables, $email_data, $email );
		} else {
			$variables = $this->add_variable_replacements( $variables, $email_data, $queue_item, $email );
		}

		$var->register( $variables );
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
		$ticket_id = $queue_item->product_id;

		if (! $ticket_id )
			return $variables;

		$event_id       = get_post_meta( $ticket_id, '_tribe_wooticket_for_event', true );
		$woo_tickets    = Tribe__Events__Tickets__Woo__Main::get_instance();
		$ticket         = $woo_tickets->get_ticket( $event_id, $ticket_id );

		// Ticket Vars
		$ticket_name    = $ticket->name;
		$ticket_desc    = $ticket->description;

		// Event Vars
		$event_name     = get_the_title( $event_id );
		$event_link     = '<a href="'. get_permalink( $event_id ) .'">'. $event_name .'</a>';
		$event_url      = get_permalink( $event_id );
		$event_location = '';
		$event_org      = '';
		$event_start    = '';
		$event_end      = '';

		$venue_id = get_post_meta( $event_id, '_EventVenueID', true );

		if (! empty($venue_id) ) {
			$venue_name     = get_the_title( $venue_id );
			$venue_address  = get_post_meta( $venue_id, '_VenueAddress', true );
			$venue_city     = get_post_meta( $venue_id, '_VenueCity', true );
			$venue_country  = get_post_meta( $venue_id, '_VenueCountry', true );
			$venue_state    = get_post_meta( $venue_id, '_VenueStateProvince', true );
			$venue_zip      = get_post_meta( $venue_id, '_VenueZip', true );

			$event_location = sprintf(
				'<b>%s</b><br/>%s<br/>%s, %s<br/>%s %s',
				$venue_name,
				$venue_address,
				$venue_city,
				$venue_state,
				$venue_country,
				$venue_zip
			);
		}

		$org_id = get_post_meta( $event_id, '_EventOrganizerID', true );

		if (! empty($org_id) ) {
			$event_org = get_post_meta( $org_id, '_OrganizerOrganizer', true );
		}

		$start_stamp    = strtotime( get_post_meta( $event_id, '_EventStartDate', true ) );
		if ( $start_stamp ) {
			$event_start    = date( get_option('date_format') .' '. get_option('time_format'), $start_stamp );
		}

		$end_stamp      = strtotime( get_post_meta( $event_id, '_EventEndDate', true ) );
		if ( $end_stamp ) {
			$event_end    = date( get_option('date_format') .' '. get_option('time_format'), $end_stamp );
		}

		$variables['event_name']            = $event_name;
		$variables['event_start_datetime']  = $event_start;
		$variables['event_end_datetime']    = $event_end;
		$variables['event_link']            = $event_link;
		$variables['event_url']             = fue_replacement_url_var( $event_url );
		$variables['event_location']        = $event_location;
		$variables['event_organizer']       = $event_org;
		$variables['ticket_name']           = $ticket_name;
		$variables['ticket_description']    = $ticket_desc;
		$variables['event_venue_phone']     = '1-800-900-0011';
		$variables['event_gcal']            = '#';
		$variables['event_ical']            = '#';
		$variables['ticket_sale_start']     = date( wc_date_format(), current_time('timestamp') + 86400 );
		$variables['ticket_sale_end']       = date( wc_date_format(), current_time('timestamp') + (86400*7) );
		$variables['ticket_image']          = '';
		$variables['ticket_quantity']       = 1;
		$variables['ticket_quantities']     = '';
		$variables['ticket_cost']           = wc_price( 15 );
		$variables['ticket_excerpt']        = 'Season ticket for the home games';

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
	protected  function add_test_variable_replacements( $variables, $email_data, $email ) {
		$now            = current_time('timestamp');
		$event_name     = 'Event Name';
		$event_start    = date( get_option('date_format') .' '. get_option('time_format'), $now + 86400 );
		$event_end      = date( get_option('date_format') .' '. get_option('time_format'), $now + (86400*2) );
		$event_link     = '<a href="'. site_url() .'">Event Name</a>';
		$event_url      = site_url();
		$event_location = 'The Venue';
		$event_org      = 'Event Organizer';
		$ticket_name    = 'Ticket A Upper Box B';
		$ticket_desc    = 'The ticket\'s description';

		$variables['event_name']            = $event_name;
		$variables['event_start_datetime']  = $event_start;
		$variables['event_end_datetime']    = $event_end;
		$variables['event_link']            = $event_link;
		$variables['event_url']             = fue_replacement_url_var( $event_url );
		$variables['event_location']        = $event_location;
		$variables['event_organizer']       = $event_org;
		$variables['ticket_name']           = $ticket_name;
		$variables['ticket_description']    = $ticket_desc;
		$variables['event_venue_phone']     = '1-800-900-0011';
		$variables['event_gcal']            = '#';
		$variables['event_ical']            = '#';
		$variables['ticket_sale_start']     = date( wc_date_format(), current_time('timestamp') + 86400 );
		$variables['ticket_sale_end']       = date( wc_date_format(), current_time('timestamp') + (86400*7) );
		$variables['ticket_image']          = '';
		$variables['ticket_quantity']       = 1;
		$variables['ticket_quantities']     = '';
		$variables['ticket_cost']           = wc_price( 15 );
		$variables['ticket_excerpt']        = 'Season ticket for the home games';

		return $variables;
	}

	/**
	 * Queue emails after an order is marked as completed
	 * @param int $order_id
	 */
	public function set_reminders( $order_id ) {
		$queued = array();

		// load reminder emails
		$emails = fue_get_emails( array('wootickets', 'twitter'), FUE_Email::STATUS_ACTIVE, array(
			'meta_query' => array(
				array(
					'key'       => '_interval_type',
					'value'     => array( 'before_tribe_event_starts', 'after_tribe_event_ends' ),
					'compare'   => 'IN'
				)
			)
		) );

		if ( empty($emails) ) {
			return;
		}

		$tickets = array();
		$order   = WC_FUE_Compatibility::wc_get_order( $order_id );
		$items   = $order->get_items();

		foreach ( $items as $item ) {
			$ticket_id = (isset($item['id'])) ? $item['id'] : $item['product_id'];

			// if $item is a ticket, load the event where the ticket is attached to
			$event_id = get_post_meta( $ticket_id, '_tribe_wooticket_for_event', true );

			if (! $event_id ) {
				continue;
			}

			if (! in_array($ticket_id, $tickets) ) {
				$tickets[] = $ticket_id;
			}
		}

		$now = current_time('timestamp');
		foreach ( $emails as $email ) {
			$interval   = (int)$email->interval_num;
			$add        = FUE_Sending_Scheduler::get_time_to_add( $interval, $email->interval_duration );

			foreach ( $tickets as $ticket_id ) {
				$event_id = get_post_meta( $ticket_id, '_tribe_wooticket_for_event', true );

				// if this email is for a specific ticket, make sure the IDs match
				if ( !empty($email->product_id) && $email->product_id != $ticket_id ) {
					continue;
				}

				// check for category matching
				if ( !empty( $email->category_id ) ) {
					$category_type  = $email->meta['wootickets_type'];

					if ( $category_type == 'event_categories' ) {
						$ticket_terms   = get_the_terms( $event_id, 'tribe_events_cat' );
					} elseif ( $category_type == 'categories' ) {
						$ticket_terms   = get_the_terms( $ticket_id, 'product_cat' );
					}

					$terms = array();

					if ( $ticket_terms && !is_wp_error( $ticket_terms ) ) {
						foreach ( $ticket_terms as $ticket_term ) {
							$terms[ $ticket_term->term_id ] = $ticket_term->name;
						}
					}

					if ( !array_key_exists( $email->category_id, $terms ) ) {
						continue;
					}
				}

				if ( $email->interval_type == 'before_tribe_event_starts' ) {
					$start = get_post_meta( $event_id, '_EventStartDate', true );

					if ( empty($start) ) {
						continue;
					}
					$start = strtotime($start);

					// check if a limit is in place
					$email_meta = maybe_unserialize( $email->meta );
					if (
						isset($email_meta['tribe_limit'], $email_meta['tribe_limit_days'])
						&& !empty($email_meta['tribe_limit_days'])
					) {
						$days = ($start - $now) / 86400;

						if ( $days <= $email_meta['tribe_limit_days'] ) {
							// $days is within limit - skip
							continue;
						}
					}

					$send_on = $start - $add;

					// if send_on is in the past, do not queue it
					if ( $now > $send_on ) {
						continue;
					}
				} else {
					$end = get_post_meta( $event_id, '_EventEndDate', true );

					if ( empty($end) ) {
						continue;
					}

					$end        = strtotime($end);
					$send_on    = $end + $add;

					// if send_on is in the past, do not queue it
					if ( $now > $send_on ) {
						continue;
					}
				}

				$insert = array(
					'user_id'       => WC_FUE_Compatibility::get_order_prop( $order, 'user_id' ),
					'order_id'      => $order_id,
					'product_id'    => $ticket_id,
					'email_id'      => $email->id,
					'send_on'       => $send_on
				);
				if ( !is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
					$queued[] = $insert;
				}
			}
		}

		Follow_Up_Emails::instance()->fue_wc->wc_scheduler->add_order_notes_to_queued_emails( $queued );
	}

	/**
	 * Register order statuses to trigger follow-up emails
	 */
	public function hook_statuses() {
		$statuses = Follow_Up_Emails::instance()->fue_wc->get_order_statuses();

		foreach ( $statuses as $status ) {
			add_action('woocommerce_order_status_'. $status, array($this, 'ticket_status_updated'), 100);
		}
	}

	/**
	 * Skip sending reminder emails if the order status is invalid
	 * @param bool $skip
	 * @param FUE_Email $email
	 * @param FUE_Sending_Queue_Item $queue_item
	 * @return bool
	 */
	public function maybe_skip_sending( $skip, $email, $queue_item ) {
		if ( $skip ) {
			return $skip;
		}

		$order = WC_FUE_Compatibility::wc_get_order( $queue_item->order_id );

		if ( !$order ) {
			return $skip;
		}

		if ( !in_array( $email->trigger, array( 'before_tribe_event_starts', 'after_tribe_event_ends' ) ) ) {
			return $skip;
		}

		if ( $order->has_status( array( 'cancelled', 'refunded' ) ) ) {
			$skip = true;
			Follow_Up_Emails::instance()->scheduler->delete_item( $queue_item->id );
		}

		return $skip;
	}

	/**
	 * Queue matching wootickets emails
	 * @param int $order_id
	 */
	public function ticket_status_updated( $order_id ) {
		$order          = WC_FUE_Compatibility::wc_get_order( $order_id );
		$queued         = array();
		$triggers       = array( 'ticket_status_' . $order->get_status() );

		$this->send_event_booking_notification( $order_id );

		$args = array(
			'meta_query'    => array(
				array(
					'key'       => '_interval_type',
					'value'     => $triggers,
					'compare'   => 'IN'
				)
			)
		);

		$emails = fue_get_emails( array('wootickets', 'twitter'), FUE_Email::STATUS_ACTIVE, $args );

		$tickets = array();

		if ( empty($emails) ) {
			return;
		}

		$items = $order->get_items();

		foreach ( $items as $item ) {
			$ticket_id = is_callable( array( $item, 'get_product_id' ) )
				? $item->get_product_id()
				: $item['product_id'];

			// if $item is a ticket, load the event where the ticket is attached to
			$event_id = get_post_meta( $ticket_id, '_tribe_wooticket_for_event', true );

			if ( ! $event_id ) {
				continue;
			}

			if ( ! in_array( $ticket_id, $tickets ) ) {
				$tickets[] = $ticket_id;
			}
		}

		$now = current_time('timestamp');
		foreach ( $emails as $email ) {
			$interval   = (int)$email->interval_num;
			$add        = FUE_Sending_Scheduler::get_time_to_add( $interval, $email->interval_duration );

			foreach ( $tickets as $ticket_id ) {

				// if this email is for a specific ticket, make sure the IDs match
				if ( ! empty( $email->product_id ) && $email->product_id != $ticket_id ) {
					continue;
				}

				// check for category matching
				if ( ! empty( $email->category_id ) ) {
					$category_type = $email->meta['wootickets_type'];
					$event_id      = get_post_meta( $ticket_id, '_tribe_wooticket_for_event', true );

					if ( 'event_categories' === $category_type ) {
						$ticket_terms = get_the_terms( $event_id, 'tribe_events_cat' );
					} elseif ( 'categories' === $category_type ) {
						$ticket_terms = get_the_terms( $ticket_id, 'product_cat' );
					}

					$terms = array();

					if ( $ticket_terms && ! is_wp_error( $ticket_terms ) ) {
						foreach ( $ticket_terms as $ticket_term ) {
							$terms[ $ticket_term->term_id ] = $ticket_term->name;
						}
					}

					if ( ! array_key_exists( $email->category_id, $terms ) ) {
						continue;
					}
				}

				$insert = array(
					'user_id'       => WC_FUE_Compatibility::get_order_prop( $order, 'user_id' ),
					'order_id'      => $order_id,
					'product_id'    => $ticket_id,
					'email_id'      => $email->id
				);
				if ( ! is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
					$queued[] = $insert;
				}
			}
		}

		Follow_Up_Emails::instance()->fue_wc->wc_scheduler->add_order_notes_to_queued_emails( $queued );

	}

	/**
	 * FUE subscriptions settings form HTML
	 */
	public function settings_form() {
		include FUE_TEMPLATES_DIR .'/settings/settings-wootickets.php';
	}

	/**
	 * Save the settings form
	 */
	public function save_settings( $post ) {
		$post = wp_unslash( $post ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce already done before this action is triggered.

		if ( $post['section'] === 'integration' ) {
			$notification   = (isset($post['event_booking_notification']) && $post['event_booking_notification'] == 1)
				? 1
				: 0;
			$schedule       = !empty($post['event_booking_notification_schedule'])
				? $post['event_booking_notification_schedule']
				: 'instant';
			$emails         = !empty($post['event_booking_notification_emails'])
				? $post['event_booking_notification_emails']
				: '';

			if ( isset($post['event_booking_notification_time_hour']) ) {
				$previous_time = get_option( 'fue_event_booking_notification_time', '07:00 AM' );
				$time = $post['event_booking_notification_time_hour'] .':'.
						$post['event_booking_notification_time_minute'] .' '.
						$post['event_booking_notification_time_ampm'];

				if ( $schedule != 'digest' ) {
					// clear any previously set summary emails
					$this->remove_notification_summary_emails();
					//$this->send_event_booking_notification(147094);
				}

				$previously_scheduled = as_next_scheduled_action(
					'fue_wootickets_digest_email',
					array('wootickets_summary'),
					'fue'
				);

				if (
					($notification && $schedule == 'digest' && $previous_time != $time) ||
					($notification && $schedule == 'digest' && !$previously_scheduled)
				) {
					update_option( 'fue_event_booking_notification_time', $time );
					$this->reschedule_notification_summary_emails();
				}

			}

			update_option( 'fue_event_booking_notification', $notification );
			update_option( 'fue_event_booking_notification_schedule', $schedule );
			update_option( 'fue_event_booking_notification_emails', $emails );
		}

	}

	/**
	 * Send a summary of bookings within the past 24 hours
	 */
	public function send_email_digest() {
		$date_from  = strtotime( date( 'F d, Y' ) .' 00:00:00' ) - 86400;
		$enabled    = get_option( 'fue_event_booking_notification', 0 );
		$emails     = get_option( 'fue_event_booking_notification_emails' );
		$schedule   = get_option( 'fue_event_booking_notification_schedule', 'instant' );

		// look for tickets generated within the given range
		$tickets = $this->get_tickets_sold_in_date( $date_from );

		if ( !$enabled || empty( $tickets ) || empty( $emails ) || $schedule != 'digest' ) {
			return;
		}

		ob_start();
		include FUE_TEMPLATES_DIR .'/add-ons/wootickets-digest-email.php';
		$message = ob_get_clean();

		$current_date   = date( wc_date_format(), $date_from );
		$subject        = sprintf( __('Event Bookings for %s', 'follow_up_emails'), $current_date );
		$message        = WC()->mailer()->wrap_message( $subject, $message );
		$wc_email       = new WC_Email();
		$message        = $wc_email->style_inline( $message );

		$queue = new FUE_Sending_Queue_Item();
		$queue->email_trigger = $subject;
		$queue->status = 1;
		$queue->meta = array(
			'adhoc'     => 1,
			'email'     => $emails,
			'subject'   => $subject,
			'message'   => $message
		);
		$queue->save();

		Follow_Up_Emails::instance()->mailer->send_adhoc_email( $queue );
	}

	/**
	 *
	 */
	public function attendees_table_script() {
		$screen = get_current_screen();

		if ( $screen->id != 'tribe_events_page_tickets-attendees' ) {
			return;
		}

		$lists = Follow_Up_Emails::instance()->newsletter->get_lists();
		?>
		<script type="text/html" id="list_template_top">
			<select name="fue_list" id="fue_list" class="fue_list" style="display: none;">
				<option value="-1"><?php esc_html_e('New List', 'follow_up_emails'); ?></option>
				<?php foreach ( $lists as $list ): ?>
					<option value="<?php echo esc_attr( $list['id'] ); ?>">
						<?php echo esc_html( $list['list_name'] ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</script>
		<script type="text/html" id="list_template_bottom">
			<select name="fue_list2" id="fue_list2" class="fue_list" style="display: none;">
				<option value="-1"><?php esc_html_e('New List', 'follow_up_emails'); ?></option>
				<?php foreach ( $lists as $list ): ?>
					<option value="<?php echo esc_attr( $list['id'] ); ?>">
						<?php echo esc_html( $list['list_name'] ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</script>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				generate_list_dropdowns();

				$("#topics-filter").prepend('<input type="hidden" name="fue_list_name" id="fue_list_name" value="" />');
				$( '#bulk-action-selector-top, #bulk-action-selector-bottom' ).on( 'change', function() {
					var $list = $(this).parent().find(".fue_list");
					if ( $(this).val() == "fue_list_import" ) {
						$list.show();
					} else {
						$list.hide();
					}
				} );

				$( '#topics-filter' ).on( 'submit', function() {
					var action = -1;
					var list;

					if ( $("#bulk-action-selector-top").val() != -1 ) {
						action  = $("#bulk-action-selector-top").val();
						list    = $("#fue_list").val();
					} else {
						action  = $("#bulk-action-selector-bottom").val();
						list    = $("#fue_list2").val();
					}

					if ( action == "fue_list_import" && list == -1 ) {
						var list_name = prompt("Please enter the name of the list");

						if ( !list_name ) {
							return false;
						}

						$("#fue_list_name").val( list_name );
						return true;
					}
				} );

				function generate_list_dropdowns() {
					var list_top = $("#list_template_top").html();
					var list_bottom = $("#list_template_bottom").html();
					$(list_top).insertAfter("#bulk-action-selector-top");
					$(list_bottom).insertAfter("#bulk-action-selector-bottom");
				}
			});
		</script>
		<?php
	}

	/**
	 * Add the 'Import to Newsletter List' bulk action to the attendees table
	 *
	 * @param array $actions
	 * @return array
	 */
	public function add_bulk_actions( $actions ) {
		$actions['fue_list_import'] = esc_attr('Add to email list', 'follow_up_emails');

		return $actions;
	}

	/**
	 * Export selected attendees to the selected newsletter list
	 * @param $action
	 */
	public function process_bulk_action( $action ) {
		if ( ! isset( $_POST['attendee'] ) || $action !== 'fue_list_import' ) {
			return;
		}

		if ( ! current_user_can( 'manage_follow_up_emails' ) && check_admin_referer( 'bulk-attendees' ) ) {
			wp_die( esc_html__( 'You do not have permission', 'follow_up_emails' ), 'Access Denied', array( 'response' => 403 ) );
		}

		$list = -1;

		if ( isset( $_POST['fue_list'] ) && $_POST['fue_list'] != -1 ) {
			$list = sanitize_text_field( wp_unslash( $_POST['fue_list'] ) );
		} elseif ( isset( $_POST['fue_list2'] ) && $_POST['fue_list2'] != -1 ) {
			$list = sanitize_text_field( wp_unslash( $_POST['fue_list2'] ) );
		}

		if ( isset( $_POST['fue_list_name']) && $list == -1 ) {
			$list = sanitize_text_field( wp_unslash( $_POST['fue_list_name'] ) );
		}

		$exported   = 0;

		foreach ( (array) array_map( 'sanitize_text_field', wp_unslash( $_POST['attendee'] ) ) as $attendee )  {
			$parts = explode( '|', $attendee );
			if ( count( $parts ) < 2 ) {
				continue;
			}

			$id = absint( $parts[0] );
			if ( $id <= 0 ) {
				continue;
			}

			$order_id = get_post_meta( $id, '_tribe_wooticket_order', true );

			$order = WC_FUE_Compatibility::wc_get_order( $order_id );

			if ( $order ) {
				$email      = WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' );
				$first_name = WC_FUE_Compatibility::get_order_prop( $order, 'billing_first_name' );
				$last_name  = WC_FUE_Compatibility::get_order_prop( $order, 'billing_last_name' );
			} else {
				$email = get_post_meta( $id, '_tribe_rsvp_email', true );

				// TODO: Try to fill these.
				$first_name = '';
				$last_name  = '';

				if ( !$email ) {
					continue;
				}
			}

			Follow_Up_Emails::instance()->newsletter->add_subscriber_to_list( $list, array(
				'email'      => $email,
				'first_name' => $first_name,
				'last_name'  => $last_name,
			) );
			$exported++;
		}
	}

	/**
	 * @param $order_id
	 */
	public function send_event_booking_notification( $order_id ) {
		$has_tickets= get_post_meta( $order_id, '_tribe_has_tickets', true );
		$enabled    = get_option( 'fue_event_booking_notification', 0 );
		$emails     = get_option( 'fue_event_booking_notification_emails' );
		$schedule   = get_option( 'fue_event_booking_notification_schedule', 'instant' );

		if ( !$enabled || !$has_tickets || empty( $emails ) || $schedule != 'instant' ) {
			return;
		}

		$notification_sent = get_post_meta( $order_id, '_fue_wooticket_notification_sent', true );

		if ( $notification_sent ) {
			//return;
		}

		$post = get_post( $order_id );

		if ( $post->post_type == 'shop_order' ) {
			$order      = WC_FUE_Compatibility::wc_get_order( $order_id );
			$customer   = WC_FUE_Compatibility::get_order_prop( $order, 'billing_first_name' ) .' '. WC_FUE_Compatibility::get_order_prop( $order, 'billing_last_name' );
			$email      = WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' );
			$amount     = WC_FUE_Compatibility::get_order_prop( $order, 'order_total' );
			$event      = '-';
			$ticket_name= '-';

			ob_start();
			include FUE_TEMPLATES_DIR .'/add-ons/wootickets-event-email.php';
			$message = ob_get_clean();

			$subject        = __('New Event Booking', 'follow_up_emails');
			$message        = WC()->mailer()->wrap_message( $subject, $message );
			$wc_email       = new WC_Email();
			$message        = $wc_email->style_inline( $message );

			$queue = new FUE_Sending_Queue_Item();
			$queue->email_trigger = $subject;
			$queue->status = 1;
			$queue->meta = array(
				'adhoc'     => 1,
				'email'     => $emails,
				'subject'   => $subject,
				'message'   => $message
			);
			$queue->save();

			Follow_Up_Emails::instance()->mailer->send_adhoc_email( $queue );

			update_post_meta( $order_id, '_fue_wooticket_notification_sent', true );
		}
	}

	/**
	 * Query the DB to get the correct tribe_wooticket ID from an order
	 * @param int $order_id
	 * @return int
	 */
	protected function get_ticket_id_by_order_id( $order_id ) {
		global $wpdb;

		$ticket_id = $wpdb->get_var($wpdb->prepare(
			"SELECT post_id
			FROM {$wpdb->postmeta}
			WHERE meta_key = '_tribe_wooticket_order'
			AND meta_value = %d",
			$order_id
		));

		return $ticket_id;
	}

	/**
	 * Returns an array of post IDs of both shop_orders and rsvp items
	 * that are within the defined date range
	 *
	 * @param $date
	 * @return array
	 */
	protected function get_tickets_sold_in_date( $date ) {
		$args = array(
			'post_type'     => 'shop_order',
			'post_status'   => array('wc-processing', 'wc-completed'),
			'fields'        => 'ids',
			'nopaging'      => true,
			'meta_query'    => array(
				array(
					'key'   => '_tribe_has_tickets',
					'value' => 1
				)
			),
			'date_query'    => array(
				array(
					'after'     => date( 'F d, Y', $date ) .' 00:00:00',
					'before'    => date( 'F d, Y', $date ) .' 23:59:59'
				),
				'inclusive' => true
			)
		);

		$orders = get_posts( $args );

		// rsvp
		$args['post_type']      = 'tribe_rsvp_attendees';
		$args['post_status']    = 'publish';
		$args['meta_query']     = null;
		$rsvp = get_posts( $args );

		$ticket_ids = array_merge( $orders, $rsvp );

		return $ticket_ids;
	}

	/**
	 * Get ticket products in the given order
	 *
	 * @param int $order_id
	 * @return array
	 */
	protected function get_tickets_from_order( $order_id ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		$ticket_ids = array();
		$products = $wpdb->get_results( $wpdb->prepare(
			"SELECT product_id, variation_id
			FROM {$wpdb->prefix}followup_order_items
			WHERE order_id = %d",
			$order_id
		), ARRAY_A );

		foreach ( $products as $product ) {
			if ( get_post_meta( $product['product_id'], '_tribe_wooticket_for_event', true ) ) {
				$ticket_ids[] = $product['product_id'];
			}

			if (
				$product['variation_id']
				&& get_post_meta( $product['variation_id'], '_tribe_wooticket_for_event', true )
			) {
				$ticket_ids[] = $product['variation_id'];
			}
		}

		return array_unique( $ticket_ids );
	}

	/**
	 * Get an array of Category IDs included in the given $order
	 * @param int|WC_Order  $order
	 * @return array
	 */
	protected function get_categories_from_order( $order ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		if ( is_numeric( $order ) ) {
			$order = WC_FUE_Compatibility::wc_get_order( $order );
		}

		if ( 1 != get_post_meta( WC_FUE_Compatibility::get_order_prop( $order, 'id' ), '_fue_recorded', true ) ) {
			FUE_Addon_Woocommerce::record_order( $order );
		}

		$category_ids = $wpdb->get_col( $wpdb->prepare(
			"SELECT category_id
			FROM {$wpdb->prefix}followup_order_categories
			WHERE order_id = %d",
			WC_FUE_Compatibility::get_order_prop( $order, 'id' )
		) );

		return array_unique( $category_ids );

	}

	/**
	 * Get the quantity of the given product in an order
	 *
	 * @param int       $product_id
	 * @param WC_Order  $order
	 * @return int
	 */
	protected function get_line_item_quantity( $product_id, $order ) {
		$items = $order->get_items();

		foreach ( $items as $item ) {
			$item_id = (isset($item['product_id'])) ? $item['product_id'] : $item['id'];

			if ( !$item['id'] == $product_id ) {
				return $item['qty'];
			}
		}

		return 0;
	}

	/**
	 * Ticket thubnail returned within an anchor tag linking back to the ticket.
	 *
	 * @param integer
	 * @return string Empty string when invalid ticket_id was given.
	 */
	protected function get_ticket_thumbnail( $ticket_id ) {
		$product        = WC_FUE_Compatibility::wc_get_product( $ticket_id );
		$ticket_image   = '';
		if ( $product ) {
			$thumbnail      = $product->get_image( 'woocommerce_thumbnail', array( 'title' => '' ) );
			$ticket_image   = sprintf( '<a href="%s">%s</a>', esc_url( get_permalink( $ticket_id ) ), $thumbnail );
		}
		return $ticket_image;
	}

	protected function get_event_data( $event_id ) {

		// Event Vars
		$event_name     = get_the_title( $event_id );
		$event_link     = '<a href="'. get_permalink( $event_id ) .'">'. $event_name .'</a>';
		$event_url      = get_permalink( $event_id );
		$event_location = '';
		$event_org      = '';
		$event_start    = '';
		$event_end      = '';
		$venue_phone    = '';

		$venue_id = get_post_meta( $event_id, '_EventVenueID', true );

		if (! empty($venue_id) ) {
			$venue_name     = get_the_title( $venue_id );
			$venue_address  = get_post_meta( $venue_id, '_VenueAddress', true );
			$venue_city     = get_post_meta( $venue_id, '_VenueCity', true );
			$venue_country  = get_post_meta( $venue_id, '_VenueCountry', true );
			$venue_state    = get_post_meta( $venue_id, '_VenueStateProvince', true );
			$venue_zip      = get_post_meta( $venue_id, '_VenueZip', true );
			$venue_phone    = get_post_meta( $venue_id, '_VenuePhone', true );

			$event_location = sprintf(
				'<b>%s</b><br/>%s<br/>%s, %s<br/>%s %s',
				$venue_name,
				$venue_address,
				$venue_city,
				$venue_state,
				$venue_country,
				$venue_zip
			);
		}

		$org_id = get_post_meta( $event_id, '_EventOrganizerID', true );

		if (! empty($org_id) ) {
			$event_org = get_post_meta( $org_id, '_OrganizerOrganizer', true );
		}

		$start_stamp = strtotime( get_post_meta( $event_id, '_EventStartDate', true ) );
		if ( $start_stamp ) {
			$event_start = date( wc_date_format() .' '. wc_time_format(), $start_stamp );
		}

		$end_stamp = strtotime( get_post_meta( $event_id, '_EventEndDate', true ) );
		if ( $end_stamp ) {
			$event_end = date( wc_date_format() .' '. wc_time_format(), $end_stamp );
		}

		// set up the global $post variable to be used by the tribe_get_gcal_link and tribe_get_ical_link methods
		global $post;
		$post = get_post( $event_id );

		$gcal = '';
		$ical = '';

		if ( function_exists( 'tribe_get_gcal_link' ) ) {
			$gcal = tribe_get_gcal_link( $event_id );
		}

		if ( function_exists( 'tribe_get_single_ical_link' ) ) {
			$ical = tribe_get_single_ical_link();
		}

		$data = array(
			'name'            => $event_name,
			'start_datetime'  => $event_start,
			'end_datetime'    => $event_end,
			'link'            => $event_link,
			'url'             => $event_url,
			'location'        => $event_location,
			'organizer'       => $event_org,
			'venue_phone'     => $venue_phone,
			'gcal'            => $gcal,
			'ical'            => $ical
		);

		return $data;
	}

	protected function remove_notification_summary_emails() {
		$params = array('wootickets_summary');
		as_unschedule_action( 'fue_wootickets_digest_email', $params, 'fue' );
	}

	protected function reschedule_notification_summary_emails() {
		as_unschedule_action( 'fue_wootickets_digest_email', array('wootickets_summary'), 'fue' );

		$time       = get_option( 'fue_event_booking_notification_time', '07:00 AM' );
		$next_send  = strtotime( date('Y-m-d', current_time('timestamp')) .' '. $time );

		if ( current_time('timestamp') > $next_send ) {
			// already in the past. Set it for tomorrow
			$next_send += 86400;
		}

		$next_send = strtotime( get_gmt_from_date( date( 'Y-m-d H:i:s', $next_send ) ) );

		as_schedule_recurring_action(
			$next_send,
			86400,
			'fue_wootickets_digest_email',
			array('wootickets_summary'),
			'fue'
		);
	}

}

$GLOBALS['fue_wootickets'] = new FUE_Addon_Wootickets();
