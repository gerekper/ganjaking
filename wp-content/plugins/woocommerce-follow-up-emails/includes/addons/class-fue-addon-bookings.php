<?php

/**
 * Class FUE_Addon_Bookings
 */
class FUE_Addon_Bookings {

	/**
	 * @var array The different booking statuses
	 */
	public static $statuses = array();

	/**
	 * class constructor
	 */
	public function __construct() {

		self::$statuses = array( 'unpaid', 'pending-confirmation', 'confirmed', 'paid', 'cancelled', 'complete', 'in-cart' );

		add_filter( 'fue_email_types', array($this, 'register_email_type') );

		// manual emails
		add_action( 'fue_manual_types', array($this, 'manual_types') );
		add_action( 'fue_manual_type_actions', array($this, 'manual_type_actions') );
		add_action( 'fue_manual_js', array($this, 'manual_js') );
		add_filter( 'fue_manual_email_recipients', array($this, 'manual_email_recipients'), 10, 2 );

		// trigger fields
		add_filter( 'fue_email_form_trigger_fields', array($this, 'add_product_selector') );

		add_action( 'fue_email_form_scripts', array($this, 'email_form_script') );
		add_action( 'fue_manual_js', array($this, 'manual_form_script') );

		add_filter( 'fue_trigger_str', array($this, 'date_trigger_string'), 10, 2 );

		add_action( 'fue_email_variables_list', array($this, 'email_variables_list') );
		add_action( 'fue_email_manual_variables_list', array($this, 'email_variables_list') );

		add_action( 'fue_before_variable_replacements', array($this, 'register_variable_replacements'), 11, 4 );

		// Booking created trigger.
		add_action( 'woocommerce_new_booking', array( $this, 'booking_created' ) );
		add_action( 'woocommerce_booking_in-cart_to_unpaid', array( $this, 'booking_created_from_cart' ) );
		add_action( 'woocommerce_booking_in-cart_to_pending-confirmation', array( $this, 'booking_created_from_cart' ) );

		foreach ( self::$statuses as $status ) {
			add_action( 'woocommerce_booking_'. $status, array($this, 'booking_status_updated') );
		}

		// manually trigger status changes because WC Bookings doesn't trigger these when saving from the admin screen
		add_action( 'save_post', array( $this, 'maybe_trigger_status_update' ), 11, 1 );

		add_filter( 'fue_unqueue_emails_filter_on_order_status_change', array( $this, 'maybe_unqueue_emails_on_status_change' ), 10, 4 );

		add_action( 'fue_email_form_trigger_fields', array($this, 'email_form_triggers'), 9, 3 );

		// Order Importer
		add_filter( 'fue_import_orders_supported_types', array($this, 'declare_import_support') );
		add_filter( 'fue_wc_get_orders_for_email', array($this, 'get_orders_for_email'), 10, 2 );
		add_filter( 'fue_wc_filter_orders_for_email', array($this, 'filter_orders_for_email'), 10, 2 );
		add_filter( 'fue_wc_import_insert', array($this, 'add_booking_id_to_meta'), 1, 2 );
		add_filter( 'fue_wc_import_insert', array($this, 'modify_insert_send_date'), 10, 2 );
	}

	/**
	 * Check if the WC Bookings plugin is installed and active
	 * @return bool
	 */
	public static function is_installed() {
		return class_exists('WC_Bookings');
	}

	/**
	 * Register custom email type
	 *
	 * @param array $types
	 * @return array
	 */
	public function register_email_type( $types ) {
		$triggers = array(
			'before_booking_event'      => __( 'Before Booked Date', 'follow_up_emails' ),
			'after_booking_event'       => __( 'After Booked Date', 'follow_up_emails' ),
			'booking_created'           => __( 'After Booking is Created', 'follow_up_emails' )
		);

		// add booking statuses
		foreach ( self::$statuses as $status ) {
			$triggers['booking_status_'. $status] = sprintf( __( 'After Booking Status: %s', 'follow_up_emails' ), $status );
		}

		$props = array(
			'label'                 => __('WooCommerce Bookings', 'follow_up_emails'),
			'singular_label'        => __('WooCommerce Booking', 'follow_up_emails'),
			'triggers'              => $triggers,
			'durations'             => Follow_Up_Emails::$durations,
			'long_description'      => __('Send follow-up emails to customers that book appointments, services or rentals.', 'follow_up_emails'),
			'short_description'     => __('Send follow-up emails to customers that book appointments, services or rentals.', 'follow_up_emails')
		);
		$types[] = new FUE_Email_Type( 'wc_bookings', $props );

		return $types;
	}

	/**
	 * Booking option for manual emails
	 */
	public function manual_types() {
		?><option value="booked_event"><?php esc_html_e('Customers who booked this event', 'follow_up_emails'); ?></option><?php
	}

	/**
	 * Action for manual emails when booking is selected
	 */
	public function manual_type_actions() {
		$products = array();

		$posts = get_posts( array(
				'post_type'     => 'product',
				'post_status'   => 'publish',
				'nopaging'      => true
			) );

		foreach ($posts as $post) {
			$product = WC_FUE_Compatibility::wc_get_product( $post->ID );

			if ( $product->is_type( array( 'booking' ) ) )
				$products[] = $product;
		}

		?>
		<div class="send-type-bookings send-type-div">
			<select id="booking_event_id" name="booking_event_id" class="select2" style="width: 400px;">
				<?php foreach ( $products as $product ): ?>
					<option value="<?php echo esc_attr( $product->get_id() ); ?>"><?php echo esc_html( $product->get_title() ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	<?php
	}

	/**
	 * JS for manual emails
	 */
	public function manual_js() {
		?>
		jQuery( '#send_type' ).on( 'change', function() {
			switch (jQuery(this).val()) {
				case "booked_event":
					jQuery(".send-type-bookings").show();
					break;
			}
		} ).trigger( 'change' );
	<?php
	}

	/**
	 * Get users who booked the selected event
	 *
	 * @param array $recipients
	 * @param array $post
	 *
	 * @return array
	 */
	public function manual_email_recipients( $recipients, $post ) {
		global $wpdb;

		if ( $post['send_type'] == 'booked_event' ) {

			$search_args = array(
				'post_type'     => 'wc_booking',
				'post_status'   => array( 'complete', 'paid' ),
				'meta_query'    => array(
										array(
											'key'       => '_booking_product_id',
											'value'     => $post['booking_event_id'],
											'compare'   => '='
										)
									)
			);

			$bookings = get_posts( $search_args );

			foreach ( $bookings as $booking ) {

				$order_item_id  = get_post_meta( $booking->ID, '_booking_order_item_id', true );
				$user_id        = get_post_meta( $booking->ID, '_booking_customer_id', true );
				$order_id       = $wpdb->get_var( $wpdb->prepare("SELECT order_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = %d", $order_item_id) );
				$order          = WC_FUE_Compatibility::wc_get_order( $order_id );

				$key = $user_id .'|'. WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' ) .'|'. WC_FUE_Compatibility::get_order_prop( $order, 'billing_first_name' ) .' '. WC_FUE_Compatibility::get_order_prop( $order, 'billing_last_name' );
				$recipients[$key] = array($user_id, WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' ), WC_FUE_Compatibility::get_order_prop( $order, 'billing_first_name' ) .' '. WC_FUE_Compatibility::get_order_prop( $order, 'billing_last_name' ));

			}

		}

		return $recipients;
	}

	public function add_product_selector( $email ) {
		if ( in_array( $email->type, apply_filters('fue_booking_form_products_selector_email_types', array('wc_bookings') ) ) ) {
			// load the categories
			$categories     = get_terms( 'product_cat', array( 'order_by' => 'name', 'order' => 'ASC' ) );
			$has_variations = (!empty($email->product_id) && FUE_Addon_Woocommerce::product_has_children($email->product_id)) ? true : false;
			$storewide_type = (!empty($email->meta['storewide_type'])) ? $email->meta['storewide_type'] : 'all';

			include FUE_TEMPLATES_DIR .'/email-form/bookings/email-form.php';
		}
	}

	/**
	 * Javascript for the email form
	 */
	public function email_form_script() {
		wp_enqueue_script( 'fue-form-bookings', FUE_TEMPLATES_URL .'/js/email-form-bookings.js' );
	}

	/**
	 * Javascript for manual emails
	 */
	public function manual_form_script() {
		?>
		jQuery( '#send_type' ).on( 'change', function() {
			if ( jQuery(this).val() == "booked_event" ) {
				jQuery(".var_wc_bookings").show();
			} else {
				jQuery(".var_wc_bookings").hide();
			}
		} ).trigger( 'change' );
		<?php
	}

	/**
	 * Return the correct trigger string for date-based emails
	 *
	 * @param string $trigger
	 * @param $email $email
	 * @return string
	 */
	public function date_trigger_string( $trigger, $email ) {
		if ( $email->type != 'wc_bookings' ) {
			return $trigger;
		}

		if ( $email->duration == 'date' ) {
			$trigger = sprintf( __('Send on %s'), fue_format_send_datetime( $email ) );
		}
		return $trigger;
	}

	/**
	 * List of available variables
	 * @param FUE_Email $email
	 */
	public function email_variables_list( $email ) {
		global $woocommerce;

		if ( $email->type != 'wc_bookings') {
			return;
		}
		?>
		<li class="var hideable var_wc_bookings"><strong>{item_name}</strong> <img class="help_tip" title="<?php esc_attr_e('The name of the purchased item.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_wc_bookings"><strong>{item_category}</strong> <img class="help_tip" title="<?php esc_attr_e('The list of categories where the purchased item is under.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_wc_bookings var_wc_bookings_item_quantity"><strong>{item_quantity}</strong> <img class="help_tip" title="<?php esc_attr_e('The quantity of the purchased item.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_wc_bookings"><strong>{booking_start}</strong> <img class="help_tip" title="<?php esc_attr_e('The start date of the booked product or service', 'follow_up_emails'); ?>" src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_wc_bookings"><strong>{booking_end}</strong> <img class="help_tip" title="<?php esc_attr_e('The end date of the booked product or service', 'follow_up_emails'); ?>" src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_wc_bookings"><strong>{booking_duration}</strong> <img class="help_tip" title="<?php esc_attr_e('The duration of the booked product or service', 'follow_up_emails'); ?>" src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_wc_bookings"><strong>{booking_date}</strong> <img class="help_tip" title="<?php esc_attr_e('The date of the booked product or service', 'follow_up_emails'); ?>" src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_wc_bookings"><strong>{booking_time}</strong> <img class="help_tip" title="<?php esc_attr_e('The time of the booked product or service', 'follow_up_emails'); ?>" src="<?php echo esc_url(  $woocommerce->plugin_url() ); ?>/assets/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_wc_bookings"><strong>{booking_amount}</strong> <img class="help_tip" title="<?php esc_attr_e('The amount or cost of the booked product or service', 'follow_up_emails'); ?>" src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_wc_bookings"><strong>{booking_resource}</strong> <img class="help_tip" title="<?php esc_attr_e('The resource booked', 'follow_up_emails'); ?>" src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png" width="16" height="16" /></li>
		<li class="var hideable var_wc_bookings"><strong>{booking_persons}</strong> <img class="help_tip" title="<?php esc_attr_e( 'The list of persons with counts for this booking', 'follow_up_emails' ); ?>" src="<?php echo esc_url( $woocommerce->plugin_url() ); ?>/assets/images/help.png" width="16" height="16" /></li>
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
		if ( $email->type != 'wc_bookings' ) {
			return;
		}

		$variables = array(
			'item_category' => '',
			'item_name' => '',
			'item_quantity' => '',
			'booking_start' => '',
			'booking_end' => '',
			'booking_duration' => '',
			'booking_date' => '',
			'booking_time' => '',
			'booking_amount' => '',
			'booking_resource' => '',
			'booking_persons' => ''
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
		if ( $queue_item->order_id || $queue_item->product_id ) {
			$item_id = $queue_item->product_id;

			// booking data
			$meta       = maybe_unserialize( $queue_item->meta );
			$booking_id = !empty( $meta['booking_id'] ) ? $meta['booking_id'] : 0;

			if ( $booking_id == 0 ) {
				return $variables;
			}

			/**
			 * @var $booking WC_Booking
			 * @var $booking_product WC_Product_Booking
			 */
			$booking            = get_wc_booking( $booking_id );
			$booking_product    = $booking->get_product();
			$booking_order      = $booking->get_order();
			$booking_start      = $booking->get_start_date( wc_date_format() .' ', wc_time_format(), wc_should_convert_timezone( $booking ) );
			$booking_end        = $booking->get_end_date( wc_date_format() .' ', wc_time_format(), wc_should_convert_timezone( $booking ) );
			$booking_date     = $booking->get_start_date( wc_date_format(), '', wc_should_convert_timezone( $booking ) );
			$booking_time     = $booking->get_start_date( '', wc_time_format(), wc_should_convert_timezone( $booking ) );
			$booking_amount   = wc_price( $booking->cost );
			$booking_persons  = '';
			$booking_resource = ( $booking->resource_id > 0 ) ? get_the_title( $booking->resource_id ) : '';

			if ( $booking->get_persons_total() > 0 ) {
				$booking_persons = $booking->get_persons();
				$persons_html = '<strong>' . esc_html( __( 'No. of persons', 'follow_up_emails' ) ) . '</strong>';

				if ( $booking_product->has_person_types() ) {
					$persons_html .= '<br /><ul>';
					foreach ( $booking_persons as $person_id => $count ) {
						$person_type = new WC_Product_Booking_Person_Type( $person_id );
						$person_name = $person_type->get_name();
						$persons_html .= "<li>$person_name: $count</li>";
					}
					$persons_html .= '</ul>';
				} else {
					// No person types are used
					$persons_html .= ' ' . array_sum( $booking_persons );
				}

				$booking_persons = $persons_html;
			}

			$used_cats  = array();
			$item_cats  = '<ul>';

			$categories = get_the_terms($booking->product_id, 'product_cat');

			if ( is_array( $categories ) ) {
				foreach ( $categories as $category ) {

					if ( !in_array( $category->term_id, $used_cats ) ) {
						$item_cats .= apply_filters(
							'fue_email_cat_list',
							'<li>'. $category->name .'</li>',
							$queue_item->id,
							$categories
						);
						$used_cats[] = $category->term_id;
					}

				}
			}

			$item_url = FUE_Sending_Mailer::create_email_url(
				$queue_item->id,
				$queue_item->email_id,
				$email_data['user_id'],
				$email_data['email_to'],
				get_permalink($queue_item->product_id)
			);

			$variables['item_name']                 = FUE_Addon_Woocommerce::get_product_name( $booking_product );
			$variables['item_url']                  = fue_replacement_url_var( $item_url );
			$variables['item_category']             = $item_cats;
			$variables['booking_start']             = $booking_start;
			$variables['booking_end']               = $booking_end;
			$variables['booking_date']              = $booking_date;
			$variables['booking_time']              = $booking_time;
			$variables['booking_amount']            = $booking_amount;
			$variables['booking_resource']          = $booking_resource;
			$variables['booking_persons']           = $booking_persons;
			$variables['order_billing_address']     = '';
			$variables['order_shipping_address']    = '';
			$variables['item_quantity']             = 1;

			if ( $booking_order ) {
				$variables['order_billing_address']     = $booking_order->get_formatted_billing_address();
				$variables['order_shipping_address']    = $booking_order->get_formatted_shipping_address();

				foreach ( $booking_order->get_items() as $item_id => $item ) {
					$product_id     = !empty( $item['product_id'] ) ? $item['product_id'] : $item['id'];

					if ( $booking_product->get_id() == $product_id ) {
						$variables['item_quantity'] = $item['qty'];

						if ( ! empty( $booking_product->get_duration() ) ) {
							$variables['booking_duration'] = $booking_product->get_duration();
						}
						break;
					}
				}
			}

		}

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
		$variables['item_url']                  = fue_replacement_url_var( '#' );
		$variables['item_category']             = 'Appointments';
		$variables['item_quantity']             = 1;
		$variables['booking_start']             = date( wc_date_format()  .' '. wc_time_format() , current_time('timestamp') + 86400 );
		$variables['booking_end']               = date( wc_date_format() .' '. wc_time_format(), current_time('timestamp') + (86400*2) );
		$variables['booking_duration']          = '3 Hours';
		$variables['booking_date']              = date( wc_date_format(), current_time('timestamp') + 86400 );
		$variables['booking_time']              = date( wc_time_format(), current_time('timestamp') + 86400 );
		$variables['booking_amount']            = wc_price( 77 );
		$variables['booking_resource']          = '';
		$variables['booking_persons']           = '';
		$variables['order_billing_address']     = '77 North Beach Dr., Miami, FL 35122';
		$variables['order_shipping_address']    = '77 North Beach Dr., Miami, FL 35122';

		return $variables;
	}

	/**
	 * Queue emails after a booking has been created.
	 *
	 * Since this excludes `in-cart` status when booking is created initially,
	 * this gets triggered only when booking is created manually.
	 *
	 * @since 1.0.0
	 * @version 4.5.2
	 *
	 * @param int $booking_id Booking ID.
	 */
	public function booking_created( $booking_id ) {
		$booking = get_wc_booking( $booking_id );

		// Stop FUE from scheduling blank emails after adding a booking product
		// to the cart.
		if ( 'in-cart' === $booking->status ) {
			return;
		}

		$this->create_email_order( $booking_id, array( 'booking_created' ) );
	}

	/**
	 * Queue emails after a booking has been created.
	 *
	 * Created bookings from checkout flow always start with `in-cart` status
	 * which then transition to either `unpaid` or `pending-confirmation`.
	 *
	 * @since 4.5.2
	 * @version 4.5.2
	 *
	 * @param int $booking_id Booking ID.
	 */
	public function booking_created_from_cart( $booking_id ) {
		$this->create_email_order( $booking_id, array( 'booking_created' ) );
	}

	/**
	 * Fires after a booking's status has been updated
	 * @param $booking_id
	 */
	public function booking_status_updated( $booking_id ) {
		global $wpdb;

		// get the status directly from wp_posts to make sure that we have the latest
		$status = $wpdb->get_var( $wpdb->prepare(
			"SELECT post_status FROM {$wpdb->posts} WHERE ID = %d",
			$booking_id
		));

		$triggers = array('booking_status_'. $status);

		if ( $status == 'paid' || $status == 'confirmed' ) {
			$triggers[] = 'before_booking_event';
			$triggers[] = 'after_booking_event';
		}

		$this->create_email_order( $booking_id, $triggers );

		// update the _last_status meta
		update_post_meta( $booking_id, '_last_status', $status );

	}

	/**
	 * Triggered from the save_post hook, we have to manually trigger
	 * status update hooks because WC Bookings does not broadcast
	 * status updates when a booking is updated using the admin screen
	 * @param $post_id
	 */
	public function maybe_trigger_status_update( $post_id ) {
		if ( !empty($_POST['post_type']) && $_POST['post_type'] !== 'wc_booking' ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Handled before action.
			return $post_id;
		}

		if ( empty( $_POST['_booking_status'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Handled before action.
			return $post_id;
		}

		// remove hook to avoid inifinite loop
		remove_action( 'save_post', array( $this, 'maybe_trigger_status_update' ), 11 );

		$booking_status     = fue_clean( wp_unslash( $_POST['_booking_status'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Handled before action.
		$last_status        = get_post_meta( $post_id, '_last_status', true );


		if ( $last_status != $booking_status ) {
			$this->booking_status_updated( $post_id );
		}

		return $post_id;
	}

	/**
	 * Override `fue_get_emails` filter when removing emails from the queue.
	 *
	 * This only happens if **Remove on status change** option is enabled.
	 *
	 * @since 4.5.2
	 * @version 4.5.2
	 *
	 * @see https://github.com/woocommerce/woocommerce-follow-up-emails/issues/344
	 *
	 * @param array  $filter     Filter arg for `fue_get_emails`.
	 * @param int    $order_id   Order ID.
	 * @param string $old_status Old order status.
	 * @param string $new_status New order status.
	 */
	public function maybe_unqueue_emails_on_status_change( $filter, $order_id, $old_status, $new_status ) {
		// Order statuses criteria for Booking follow-up type when ever order
		// status is updated.
		if ( ! in_array( $old_status, array( 'processing', 'completed' ) ) ) {
			return $filter;
		}
		if ( ! in_array( $new_status, array( 'on-hold', 'pending', 'cancelled' ) ) ) {
			return $filter;
		}

		if ( ! class_exists( 'WC_Booking_Data_Store' ) ) {
			return $filter;
		}
		if ( ! is_callable( array( 'WC_Booking_Data_Store', 'get_booking_ids_from_order_id' ) ) ) {
			return $filter;
		}

		// Bail out if no bookings in the order.
		$booking_ids = WC_Booking_Data_Store::get_booking_ids_from_order_id( $order_id );
		if ( empty( $booking_ids ) ) {
			return $filter;
		}

		// Triggers that fit for unqueue emails when ever order status is updated.
		$triggers = array(
			'booking_created',
			'booking_status_confirmed',
			'booking_status_paid',
			'booking_status_complete',
			'before_booking_event',
			'after_booking_event',
		);

		$current_triggers = ! empty( $filter['meta_query'][0]['value'] )
			? $filter['meta_query'][0]['value']
			: array();
		$current_triggers = ! is_array( $current_triggers )
			? array( $current_triggers )
			: $current_triggers;

		$filter['meta_query'][0]['value'] = array_merge( $current_triggers, $triggers );
		$filter['meta_query'][0]['compare'] = 'IN';

		return $filter;
	}

	/**
	 * Add the ability to additionally check the last status before executing a trigger
	 *
	 * @param FUE_Email $email
	 */
	public function email_form_triggers( FUE_Email $email ) {
		include FUE_TEMPLATES_DIR .'/email-form/bookings/triggers.php';
	}

	/**
	 * Add bookings to the email types that support order importing
	 *
	 * @param array $types
	 * @return array
	 */
	public function declare_import_support( $types ) {
		$types[] = 'wc_bookings';
		return $types;
	}

	/**
	 * Get orders that match the $email's criteria
	 * @param array     $orders Matching Order IDs
	 * @param FUE_Email $email
	 * @return array
	 */
	public function get_orders_for_email( $orders, $email ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		if ( $email->type != 'wc_bookings' ) {
			return $orders;
		}

		$valid_statuses = array('confirmed', 'paid', 'complete');

		// add booking statuses
		$status_triggers = array();
		foreach ( self::$statuses as $status ) {
			$status_triggers[ $status ] = 'booking_status_'. $status;
		}

		if ( ( $status = array_search( $email->trigger, $status_triggers ) ) ) {
			$booking_posts = get_posts( array(
				'nopaging'      => true,
				'post_type'     => 'wc_booking',
				'post_status'   => $status,
				'fields'        => 'ids'
			) );

			foreach ( $booking_posts as $booking_id ) {
				$last_status = get_post_meta( $booking_id, '_last_status', true );

				if ( !empty( $email->meta['bookings_last_status'] ) && $email->meta['bookings_last_status'] != $last_status ) {
					continue;
				}

				$orders[] = $booking_id;
			}
		} elseif ( $email->trigger == 'before_booking_event' ) {
			$now    = date('Ymd') . '000000';
			$booking_ids = $wpdb->get_col($wpdb->prepare(
				"SELECT post_id
				FROM {$wpdb->postmeta}
				WHERE meta_key = '_booking_start'
				AND meta_value > %s",
				$now
			));

			if ( !empty( $booking_ids ) ) {
				foreach ( $booking_ids as $booking_id ) {
					if ( !in_array( get_post_status( $booking_id ), $valid_statuses ) ) {
						continue;
					}
					$orders[] = $booking_id;
				}

			}
		} elseif ( $email->trigger = 'after_booking_event' ) {
			$now    = date('Ymd') . '000000';
			$booking_ids = $wpdb->get_col($wpdb->prepare(
				"SELECT post_id
				FROM {$wpdb->postmeta}
				WHERE meta_key = '_booking_end'
				AND meta_value < %s",
				$now
			));

			if ( !empty( $booking_ids ) ) {
				foreach ( $booking_ids as $booking_id ) {
					if ( !in_array( get_post_status( $booking_id ), $valid_statuses ) ) {
						continue;
					}
					$orders[] = $booking_id;
				}

			}
		}

		return array( $email->id => $orders );
	}

	/**
	 * Run filters on bookings to remove invalid orders
	 *
	 * @param array $data
	 * @param FUE_Email $email
	 * @return array
	 */
	public function filter_orders_for_email( $data, $email ) {
		if ( $email->type == 'wc_bookings' ) {
			foreach ( $data as $email_id => $orders ) {
				foreach ( $orders as $idx => $booking_id ) {
					$booking = new WC_Booking( $booking_id );
					$order   = $booking->get_order();

					if ( $booking->post->post_type != 'wc_booking' ) {
						unset( $data[ $email_id ][ $idx ] );
						continue;
					}

					if ( $this->is_category_excluded( $booking, $email ) ) {
						unset( $data[ $email_id ][ $idx ] );
						continue;
					}

					// A booking can have no order linked to it
					if ( $order ) {
						$customer = fue_get_customer_from_order( $order );
						if ( Follow_Up_Emails::instance()->fue_wc->wc_scheduler->exclude_customer_based_on_purchase_history( $customer, $email ) ) {
							unset( $data[ $email_id ][ $idx ] );
							continue;
						}
					}

					// limit to selected product or category
					if ( $email->meta['storewide_type'] == 'products' && $email->product_id > 0 && $email->product_id != $booking->product_id ) {
						unset( $data[ $email_id ][ $idx ] );
						continue;
					}  elseif ( $email->meta['storewide_type'] == 'categories' && $email->category_id > 0 ) {
						$categories = wp_get_object_terms( $booking->product_id, 'product_cat', array('fields' => 'ids') );

						if ( is_wp_error( $categories ) ) {
							unset( $data[ $email_id ][ $idx ] );
							continue;
						}

						if ( empty( $categories ) || !in_array( $email->category_id, $categories ) ) {
							unset( $data[ $email_id ][ $idx ] );
							continue;
						}
					}

					// look for a possible duplicate item in the queue
					$dupes = Follow_Up_Emails::instance()->scheduler->get_items(array(
						'email_id'      => $email->id,
						'is_sent'       => 0,
						'order_id'      => $booking->order_id,
						'product_id'    => $booking->product_id
					));

					if ( count( $dupes ) > 0 ) {
						foreach ( $dupes as $dupe_item ) {
							if ( !empty( $dupe_item->meta['booking_id'] ) && $dupe_item->meta['booking_id'] == $booking_id ) {
								// found exact booking match
								unset( $data[ $email_id ][ $idx ] );
								continue 2;
							}
						}
					}
				}
			}
		}

		return $data;
	}

	/**
	 * If the post pointing to the order ID is of type 'wc_booking', use the
	 * order_id as the booking_id and fill in the proper order ID.
	 *
	 * @param array     $insert
	 * @param FUE_Email $email
	 * @return array
	 */
	public static function add_booking_id_to_meta( $insert, $email ) {
		if ( empty( $insert['order_id'] ) ) {
			return $insert;
		}

		$post = get_post( $insert['order_id'] );

		if ( $post->post_type == 'wc_booking' ) {
			$insert['meta']['booking_id'] = $insert['order_id'];
			$insert['order_id'] = $post->post_parent;
		}

		return $insert;
	}

	/**
	 * Change the send date of the email for 'before_booking_event' and 'after_booking_event' triggers
	 * @param array $insert
	 * @param FUE_Email $email
	 * @return array
	 */
	public function modify_insert_send_date( $insert, $email ) {
		if ( $email->type !== 'wc_bookings' ) {
			return $insert;
		}

		$booking_id = $insert['meta']['booking_id'];

		if ( $email->trigger == 'before_booking_event' ) {
			$start  = strtotime( get_post_meta( $booking_id, '_booking_start', true ) );
			$time   = FUE_Sending_Scheduler::get_time_to_add( $email->interval_num, $email->interval_duration );

			$insert['send_on'] = $start - $time;
		} elseif ( $email->trigger == 'after_booking_event' ) {
			$start  = strtotime( get_post_meta( $booking_id, '_booking_end', true ) );
			$time   = FUE_Sending_Scheduler::get_time_to_add( $email->interval_num, $email->interval_duration );

			$insert['send_on'] = $start + $time;
		}

		return $insert;
	}

	/**
	 * Send emails that matches the provided triggers to the queue
	 * @param int $booking_id
	 * @param array $triggers
	 */
	private function create_email_order( $booking_id, $triggers = array() ) {
		/**
		 * @var $booking WC_Booking
		 * @var $order WC_Order
		 */
		$booking    = get_wc_booking( $booking_id );
		$last_status= get_post_meta( $booking_id, '_last_status', true );
		$order      = WC_FUE_Compatibility::wc_get_order( $booking->order_id );

		$emails     = fue_get_emails( 'any', '', array(
			'meta_query'    => array(
				array(
					'key'       => '_interval_type',
					'value'     => $triggers,
					'compare'   => 'IN'
				)
			)
		) );

		foreach ( $emails as $email ) {

			if ( $email->status != 'fue-active' ) {
				continue;
			}

			if ( !empty( $email->meta['bookings_last_status'] ) && $email->meta['bookings_last_status'] != $last_status ) {
				continue;
			}

			if ( $this->is_category_excluded( $booking, $email ) ) {
				continue;
			}

			// A booking can have no order linked to it
			if ( $order ) {
				$customer = fue_get_customer_from_order( $order );
				if ( Follow_Up_Emails::instance()->fue_wc->wc_scheduler->exclude_customer_based_on_purchase_history( $customer, $email ) ) {
					continue;
				}
			}

			// limit to selected product or category
			if ( $email->meta['storewide_type'] == 'products' && $email->product_id > 0 && $email->product_id != $booking->product_id ) {
				continue;
			}  elseif ( $email->meta['storewide_type'] == 'categories' && $email->category_id > 0 ) {
				$categories = wp_get_object_terms( $booking->product_id, 'product_cat', array('fields' => 'ids') );

				if ( is_wp_error( $categories ) ) {
					continue;
				}

				if ( empty( $categories ) || !in_array( $email->category_id, $categories ) ) {
					continue;
				}
			}

			// look for a possible duplicate item in the queue
			$dupes = Follow_Up_Emails::instance()->scheduler->get_items(array(
				'email_id'      => $email->id,
				'is_sent'       => 0,
				'order_id'      => $booking->order_id,
				'product_id'    => $booking->product_id
			));

			if ( count( $dupes ) > 0 ) {
				foreach ( $dupes as $dupe_item ) {
					if ( !empty( $dupe_item->meta['booking_id'] ) && $dupe_item->meta['booking_id'] == $booking_id ) {
						// found exact booking match
						continue 2;
					}
				}
			}

			if ( $email->duration == 'date' ) {
				$email->interval_type = 'date';
				$send_on = $email->get_send_timestamp();
			} else {
				if ( $email->interval_type == 'before_booking_event' ) {
					$start  = strtotime( get_post_meta( $booking_id, '_booking_start', true ) );
					$time   = FUE_Sending_Scheduler::get_time_to_add( $email->interval_num, $email->interval_duration );

					$send_on = $start - $time;
				} elseif ( $email->interval_type == 'after_booking_event' ) {
					$start  = strtotime( get_post_meta( $booking_id, '_booking_end', true ) );
					$time   = FUE_Sending_Scheduler::get_time_to_add( $email->interval_num, $email->interval_duration );

					$send_on = $start + $time;
				} else {
					$send_on    = $email->get_send_timestamp();
				}
			}

			$insert = array(
				'send_on'       => $send_on,
				'email_id'      => $email->id,
				'product_id'    => $booking->product_id,
				'order_id'      => $booking->order_id,
				'meta'          => array('booking_id' => $booking_id)
			);

			// Assume the user id is Booking's customer ID.
			$user_id = $booking->customer_id;

			if ( $order && empty( $user_id ) ) {
				$user_id              = WC_FUE_Compatibility::get_order_user_id( $order );

				// Assume user's email is order's billing email (if the user_id exists it will use the customer's email instead).
				$insert['user_email'] = WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' );
			}

			if ( $user_id ) {
				$user                 = new WP_User( $user_id );
				$insert['user_id']    = $user_id;
				$insert['user_email'] = empty( $insert['user_email'] ) ? $user->user_email : $insert['user_email'];
			}

			// Skip queueing e-mails that have no recipient.
			if ( empty( $insert['user_email'] ) ) {
				continue;
			}

			// Remove the nonce to avoid infinite loop because doing a
			// remove_action on WC_Bookings_Details_Meta_Box doesnt work
			unset( $_POST['wc_bookings_details_meta_box_nonce'] );  // phpcs:ignore WordPress.Security.NonceVerification.Missing

			if ( !is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
				// Tell FUE that an email order has been created
				// to stop it from sending storewide emails
				if (! defined('FUE_ORDER_CREATED')) {
					define('FUE_ORDER_CREATED', true);
				}

				if ( $order ) {

					if ( empty( $insert['send_on'] ) ) {
						$insert['send_on'] = $email->get_send_timestamp();
					}

					$email_trigger  = apply_filters( 'fue_interval_str', $email->get_trigger_string(), $email );
					$send_date      = date( get_option('date_format') .' '. get_option('time_format'), $insert['send_on'] );

					$note = sprintf(
						__('Email queued: %s scheduled on %s<br/>Trigger: %s', 'follow_up_emails'),
						$email->name,
						$send_date,
						$email_trigger
					);

					$order->add_order_note( $note );
				}
			}

		}
	}

	/**
	 * Checks if $booking is under a category that is excluded in $email
	 *
	 * @param WC_Booking    $booking
	 * @param FUE_Email     $email
	 * @return bool
	 */
	private function is_category_excluded( $booking, $email ) {
		$excluded = false;

		$categories = wp_get_object_terms( $booking->product_id, 'product_cat' );

		if ( is_wp_error( $categories ) ) {
			return false;
		}

		$excludes = (isset($email->meta['excluded_categories'])) ? $email->meta['excluded_categories'] : array();

		if ( !is_array( $excludes ) ) {
			$excludes = array();
		}

		if ( count($excludes) > 0 ) {
			foreach ( $categories as $category ) {
				if ( in_array( $category->term_id, $excludes ) ) {
					$excluded = true;
					break;
				}
			}
		}

		return apply_filters( 'fue_bookings_category_excluded', $excluded, $booking, $email );
	}

	private function duration_to_string( $duration, $unit ) {
		$unit = rtrim($unit, 's');

		return ($duration == 1) ? $duration .' '. $unit : $duration .' '. $unit .'s';
	}

}

if ( FUE_Addon_Bookings::is_installed() )
	new FUE_Addon_Bookings();
