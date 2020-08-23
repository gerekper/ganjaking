<?php
/**
 * WooCommerce Twilio SMS Notifications
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Twilio SMS Notifications to newer
 * versions in the future. If you wish to customize WooCommerce Twilio SMS Notifications for your
 * needs please refer to http://docs.woocommerce.com/document/twilio-sms-notifications/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Twilio_SMS\Integrations;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Integration class for Bookings plugin.
 *
 * @since 1.12.0
 */
class Bookings {


	/**
	 * Bookings integration constructor.
	 *
	 * @since 1.12.0
	 */
	public function __construct() {

		// this integration requires at least WooCommerce 3.4
		if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_lt( '3.4' ) ) {
			return;
		}

		if ( is_admin() ) {

			// add bookings section to the Twilio SMS tab
			add_filter( 'wc_twilio_sms_sections', array( $this, 'add_settings_section' ) );

			// add Bookings settings to the WooCommerce SMS tab
			add_filter( 'wc_twilio_sms_settings', array( $this, 'get_settings' ) );

			// save Bookings settings page
			add_action( 'woocommerce_update_options_' . \WC_Twilio_SMS_Admin::$tab_id, array( $this, 'process_settings' ) );

			// add bookings notification tab
			add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'add_booking_notification_tab' ), 11 );

			// add bookings notification fields
			add_action( 'woocommerce_product_data_panels', array( $this, 'add_booking_notification_tab_options' ), 11 );

			// save bookings notification
			add_action( 'woocommerce_process_product_meta', array( $this, 'save_booking_notification_tab_options' ) );

			// add custom booking schedule field type
			add_action( 'woocommerce_admin_field_wc_twilio_sms_booking_schedule', array( $this, 'output_booking_schedule_field_html' ) );
		}

		// modify checkout optin
		add_filter( 'wc_twilio_sms_checkout_optin_label', array( $this, 'modify_checkout_label' ) );

		/**
		 * Send booking notifications and schedule future notifications.
		 * @see https://docs.woocommerce.com/document/bookings-action-and-filter-reference/
		 */

		// handle scheduled events

		// schedule booking reminder notifications for both the customer and the admin
		add_action( 'woocommerce_booking_paid', array( $this, 'schedule_booking_reminder_notifications' ) );

		// schedule booking follow-up notifications for the customer
		add_action( 'woocommerce_booking_complete', array( $this, 'schedule_booking_follow_up_notifications' ) );

		// clear scheduled notifications if a booking is cancelled or deleted
		add_action( 'woocommerce_booking_cancelled', array( $this, 'clear_scheduled_notifications' ) );
		add_action( 'woocommerce_delete_booking',    array( $this, 'clear_scheduled_notifications' ) );

		// reschedule notifications when needed
		add_action( 'woocommerce_booking_process_meta', array( $this, 'reschedule_booking_notifications' ) );

		// send notifications

		// send booking confirmation to the customer
		add_action( 'woocommerce_booking_confirmed', array( $this, 'send_customer_confirmed_booking_notification' ) );

		// send booking cancellation notifications to both the customer and the admin
		add_action( 'woocommerce_booking_cancelled', array( $this, 'send_cancelled_booking_notifications' ) );

		// send a booking reminder notification to the admin
		add_action( 'wc_twilio_sms_bookings_admin_reminder_notification', array( $this, 'send_admin_reminder_booking_notification' ) );

		// send a booking reminder notification to the customer
		add_action( 'wc_twilio_sms_bookings_customer_reminder_notification', array( $this, 'send_customer_reminder_booking_notification' ) );

		// send a booking follow-up notification to the customer
		add_action( 'wc_twilio_sms_bookings_customer_follow_up_notification', array( $this, 'send_customer_follow_up_booking_notification' ) );
	}


	/** Settings methods ******************************************************/


	/**
	 * Adds the Bookings section to the SMS tab.
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 *
	 * @param array $sections the existing SMS sections
	 * @return array the SMS sections plus Bookings
	 */
	public function add_settings_section( $sections ) {

		$sections['bookings'] = __( 'Bookings', 'woocommerce-twilio-sms-notifications' );

		return $sections;
	}


	/**
	 * Builds array of plugin settings in format needed to use WC admin settings API.
	 *
	 * @see woocommerce_admin_fields()
	 * @see woocommerce_update_options()
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 *
	 * @param array $settings the current settings
	 * @return array settings
	 */
	public function get_settings( $settings = array() ) {

		if ( ! isset( $_GET['section'] ) || 'bookings' !== $_GET['section'] ) {
			return $settings;
		}

		$settings = array(

			array(
				'name' => __( 'Bookings Settings', 'woocommerce-twilio-sms-notifications' ),
				'type' => 'title',
			),

			array(
				'id'       => 'wc_twilio_sms_bookings_optin_checkbox_label',
				'name'     => __( 'Opt-in Checkbox Label', 'woocommerce-twilio-sms-notifications' ),
				'desc_tip' => __( 'This message overrides the default label when a booking is purchased.', 'woocommerce-twilio-sms-notifications' ),
				'css'      => 'min-width: 275px;',
				'default'  => __( 'Please send booking reminders via text message', 'woocommerce-twilio-sms-notifications' ),
				'type'     => 'text',
			),

			array( 'type' => 'sectionend' ),

			array(
				'name' => __( 'Admin Notifications', 'woocommerce-twilio-sms-notifications' ),
				'type' => 'title',
			),
		);

		// add admin settings
		$all_notifications = $this->get_booking_notifications();

		foreach ( $all_notifications as $notification => $label ) {

			if ( $this->is_admin_notification( $notification ) ) {
				$settings = array_merge( $settings, $this->build_setting( $notification, $all_notifications ) );
			}
		}

		$settings[] = array( 'type' => 'sectionend' );

		$settings[] = array(
			'name' => __( 'Customer Notifications', 'woocommerce-twilio-sms-notifications' ),
			'type' => 'title'
		);

		foreach( $all_notifications as $notification => $label ) {

			if ( ! $this->is_admin_notification( $notification ) ) {
				$settings = array_merge( $settings, $this->build_setting( $notification, $all_notifications ) );
			}
		}

		$settings[] = array( 'type' => 'sectionend' );

		return $settings;
	}


	/**
	 * Build the settings options for a notification.
	 *
	 * @since 1.12.0
	 *
	 * @param string $notification the notification slug
	 * @param array $all_notifications all available notifications
	 * @return array the notification settings
	 */
	protected function build_setting( $notification, $all_notifications ) {

		/* translators: Placeholders: %1$s is <code>, %2$s is </code> */
		$default_description = sprintf( __( 'Use these tags to customize your message: %1$s{billing_name}%2$s, %1$s{booking_start_time}%2$s, %1$s{booking_end_time}%2$s, %1$s{booking_date}%2$s, %1$s{person_count}%2$s, %1$s{resource}%2$s. Remember that SMS messages may be limited to 160 characters or less.', 'woocommerce-twilio-sms-notifications' ), '<code>', '</code>' );

		$setting = array(
			array(
				'id'                => "wc_twilio_sms_bookings_send_{$notification}",
				'name'              => $all_notifications[ $notification ],
				/* translators: Placeholder: %s = notification label */
				'desc'              => sprintf( __( 'Send %s SMS notifications', 'woocommerce-twilio-sms-notifications' ), strtolower( $all_notifications[ $notification ] ) ),
				'default'           => 'no',
				'type'              => 'checkbox',
				'class'             => 'wc_twilio_sms_enable',
				'custom_attributes' => array(
					'data-notification' => $notification,
				),
			),
		);

		if ( $this->is_admin_notification( $notification ) ) {

			$setting[] = array(
				'id'          => "wc_twilio_sms_bookings_{$notification}_recipients",
				'name'        => __( 'Notification recipient(s)', 'woocommerce-twilio-sms-notifications' ),
				'desc_tip'    => __( 'Enter the mobile number (starting with the country code) where the notification should be sent. Send to multiple recipients by separating numbers with commas.', 'woocommerce-twilio-sms-notifications' ),
				'placeholder' => '1-555-867-5309',
				'type'        => 'text',
			);
		}

		if ( $this->notification_has_schedule( $notification ) ) {

			$schedule = $suffix = '';

			switch ( $notification ) {

				case 'customer_follow_up':
					$schedule = '3:days';
					$suffix   = __( 'after booking ends', 'woocommerce-twilio-sms-notifications' );
				break;

				case 'customer_reminder':
					$schedule = '24:hours';
					$suffix   = __( 'before booking starts', 'woocommerce-twilio-sms-notifications' );
				break;

				case 'admin_reminder':
					$schedule = '15:minutes';
					$suffix   = __( 'before booking starts', 'woocommerce-twilio-sms-notifications' );
				break;
			}

			$setting[] = array(
				'id'         => "wc_twilio_sms_bookings_{$notification}_schedule",
				'name'       => __( 'Send this message', 'woocommerce-twilio-sms-notifications' ),
				'default'    => $schedule,
				'value'      => get_option( "wc_twilio_sms_bookings_{$notification}_schedule", '' ),
				'post_field' => $suffix,
				'type'       => 'wc_twilio_sms_booking_schedule',
			);
		}

		$setting[] = array(
			'id'      => "wc_twilio_sms_bookings_{$notification}_template",
			/* translators: Placeholder: %s = notification label */
			'name'    => sprintf( __( '%s message', 'woocommerce-twilio-sms-notifications' ), $all_notifications[ $notification ] ),
			'desc'    => $default_description,
			'css'     => 'min-width:500px;',
			'default' => $this->get_default_template( $notification ),
			'type'    => 'textarea',
		);

		return $setting;
	}


	/**
	 * Updates bookings notifications settings.
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 *
	 * @see woocommerce_update_options()
	 * @uses Bookings::get_settings() to get settings array
	 */
	public function process_settings() {

		// save the booking notification schedules
		$booking_schedule_fields = array(
			'wc_twilio_sms_bookings_admin_reminder_schedule',
			'wc_twilio_sms_bookings_customer_reminder_schedule',
			'wc_twilio_sms_bookings_customer_follow_up_schedule',
		);

		foreach ( $booking_schedule_fields as $field_name ) {

			if ( isset( $_POST[ $field_name . '_number' ], $_POST[ $field_name . '_modifier' ] ) ) {

				$number   = absint( $_POST[ $field_name . '_number' ] );
				$modifier = wc_clean( $_POST[ $field_name . '_modifier' ] );

				// validate and sanitize a the booking schedule
				$booking_schedule = new Bookings\Notification_Schedule();

				// restrict reminder to 48 hours prior to event
				$restricted_number = ( strstr( $field_name, 'reminder' ) ) ? $booking_schedule->get_restricted_reminder( $number, $modifier ) : $number;

				$booking_schedule->set_value( $restricted_number, $modifier );

				update_option( $field_name, $booking_schedule->get_value() );

				// inform the user if they have set a reminder schedule greater than 48 hours before the event
				if ( $restricted_number !== $number ) {

					$this->add_restricted_schedule_message();
				}
			}
		}

		// save all other options
		woocommerce_update_options( $this->get_settings() );
	}


	/**
	 * Adds an 'SMS Notifications' tab to the product options.
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 */
	public function add_booking_notification_tab() {

		?>
		<li class="wc-twilio-sms-tab show_if_booking">
			<a href="#wc-twilio-sms-bookings-data"><span><?php esc_html_e( 'SMS Notification', 'woocommerce-twilio-sms-notifications' ); ?></span></a>
		</li>
		<?php
	}


	/**
	 * Adds bookings notification options to the products.
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 */
	public function add_booking_notification_tab_options() {
		global $post;

		$product = wc_get_product( $post->ID );

		if ( ! $product ) {
			return;
		}

		$bookings_options  = $product->get_meta( '_wc_twilio_sms_bookings_options' );
		$available_options = array(
			'global'   =>  __( 'Use global settings', 'woocommerce-twilio-sms-notifications' ),
			'override' =>  __( 'Override global settings', 'woocommerce-twilio-sms-notifications' ),
			'disabled' =>  __( "Don't send", 'woocommerce-twilio-sms-notifications' ),
		);

		/* translators: %1$s is <code>, %2$s is </code> */
		$default_description = sprintf( __( 'Use these tags to customize your message: %1$s{billing_name}%2$s, %1$s{booking_start_time}%2$s, %1$s{booking_end_time}%2$s, %1$s{booking_date}%2$s, %1$s{person_count}%2$s, %1$s{resource}%2$s. Remember that SMS messages may be limited to 160 characters or less.', 'woocommerce-twilio-sms-notifications' ), '<code>', '</code>' );

		?>
		<div id="wc-twilio-sms-bookings-data" class="panel woocommerce_options_panel">
			<?php foreach ( $this->get_booking_notifications() as $notification => $label ) : ?>
			<div class="options_group">
				<?php
				woocommerce_wp_select( array(
					'id'                => "wc_twilio_sms_bookings_{$notification}_override",
					'label'             => $label,
					'options'           => $available_options,
					'value'             => ( ! empty( $bookings_options[ $notification ] ) ) ? $bookings_options[ $notification ] : 'global',
					'class'             => 'wc_twilio_sms_notification_toggle',
					'custom_attributes' => array(
						'data-notification' => $notification,
					),
				) );

				if ( $this->notification_has_schedule( $notification ) ) : ?>

					<?php
					$title      = 'customer_follow_up' === $notification ? __( 'Send follow-up', 'woocommerce-twilio-sms-notifications' )     : __( 'Send reminder', 'woocommerce-twilio-sms-notifications' );
					$post_field = 'customer_follow_up' === $notification ? __( 'after booking ends', 'woocommerce-twilio-sms-notifications' ) : __( 'before booking starts', 'woocommerce-twilio-sms-notifications' );
					?>

					<p class="form-field">
						<?php
						$this->output_booking_schedule_field_html( array(
							'id'         => "wc_twilio_sms_bookings_{$notification}_schedule",
							'title'      => $title,
							'default'    => '24:hours',
							'value'      => ! empty( $bookings_options[ "{$notification}_schedule" ] ) ? $bookings_options[ "{$notification}_schedule" ] : '',
							'post_field' => $post_field,
							'type'       => 'wc_twilio_sms_booking_schedule',
						) );
						?>
					</p>
				<?php endif; ?>

				<?php
				woocommerce_wp_textarea_input( array(
					'id'          => "wc_twilio_sms_bookings_{$notification}_template",
					'label'       => __( 'Message', 'woocommerce-twilio-sms-notifications' ),
					'description' => $default_description,
					'value'       => ! empty( $bookings_options[ "{$notification}_template" ] ) ? $bookings_options[ "{$notification}_template" ] : $this->get_default_template( $notification ),
				) );
				?>
			</div>
			<?php endforeach; ?>
		</div>
		<?php
	}


	/**
	 * Saves booking notification options at the product level.
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 *
	 * @param int $product_id the ID of the product being saved
	 */
	public function save_booking_notification_tab_options( $product_id ) {

		$options = array();
		$product = wc_get_product( $product_id );

		if ( $product ) {

			foreach ( array_keys( $this->get_booking_notifications() ) as $notification ) {

				if ( isset( $_POST[ "wc_twilio_sms_bookings_{$notification}_override" ] ) && $this->is_valid_product_override_option( $_POST[ "wc_twilio_sms_bookings_{$notification}_override" ] ) ) {

					$options[ $notification ]              = wc_clean( $_POST[ "wc_twilio_sms_bookings_{$notification}_override" ] );
					$options[ "{$notification}_template" ] = wc_clean( $_POST[ "wc_twilio_sms_bookings_{$notification}_template" ] );

					if ( $this->notification_has_schedule( $notification ) ) {

						$schedule_number   = (int) $_POST[ "wc_twilio_sms_bookings_{$notification}_schedule_number" ];
						$schedule_modifier = wc_clean( $_POST[ "wc_twilio_sms_bookings_{$notification}_schedule_modifier" ] );

						$notification_schedule = new Bookings\Notification_Schedule();

						// restrict reminder to 48 hours prior to event
						$restricted_schedule_number = in_array( $notification, array( 'admin_reminder', 'customer_reminder' ), true ) ? $notification_schedule->get_restricted_reminder( $schedule_number, $schedule_modifier ) : $schedule_number;

						// use defaults if schedule is not set
						if ( $restricted_schedule_number > 0 ) {

							$notification_schedule->set_value( $restricted_schedule_number, $schedule_modifier );

							$options[ "{$notification}_schedule" ] = $notification_schedule->get_value();

							// inform the user if they have set a reminder schedule greater than 48 hours before the event
							if ( $restricted_schedule_number !== $schedule_number ) {

								$this->add_restricted_schedule_message();
							}

						} else {
							$options[ "{$notification}_schedule" ] = get_option( "wc_twilio_sms_bookings_{$notification}_schedule" );
						}
					}
				}
			}

			$product->update_meta_data( '_wc_twilio_sms_bookings_options', $options );
			$product->save();
		}
	}


	/**
	 * Outputs HTML markup for the booking schedule field.
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 *
	 * @param array $args
	 */
	public function output_booking_schedule_field_html( $args ) {

		// get the current booking schedule from the field ID or default value
		$value = isset( $args['value'] ) && ! empty( $args['value'] ) ? $args['value'] : $args['default'];

		$booking_schedule = new Bookings\Notification_Schedule( $value );

		echo $booking_schedule->get_field_html( $args, true );
	}


	/** Notification schedules & sending ********************************************/


	/**
	 * Modify the optin checkbox label when the cart contains a booking.
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 *
	 * @param string $label the initial checkbox label
	 * @return string updated label
	 */
	public function modify_checkout_label( $label ) {

		foreach ( WC()->cart->get_cart() as $cart_item ) {

			if ( $cart_item['data'] instanceof \WC_Product && is_wc_booking_product( $cart_item['data'] ) ) {

				$label = get_option( 'wc_twilio_sms_bookings_optin_checkbox_label', '' );
				break;
			}
		}

		return $label;
	}


	/**
	 * Clears all scheduled events related to a booking.
	 *
	 * @internal
	 *
	 * @param int $booking_id the ID of the related booking
	 */
	public function clear_scheduled_notifications( $booking_id ) {

		$hook_args = array(
			'booking_id' => $booking_id,
		);

		as_unschedule_action( 'wc_twilio_sms_bookings_admin_reminder_notification',     $hook_args );
		as_unschedule_action( 'wc_twilio_sms_bookings_customer_reminder_notification',  $hook_args );
		as_unschedule_action( 'wc_twilio_sms_bookings_customer_follow_up_notification', $hook_args );
	}


	/**
	 * Reschedules booking notifications when a booking is saved in case of changes.
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 *
	 * @param int $booking_id the booking ID
	 */
	public function reschedule_booking_notifications( $booking_id ) {

		$booking = get_wc_booking( $booking_id );

		if ( $booking ) {

			// we could probably check if the time has changed, but for now, just clear + reschedule
			$this->clear_scheduled_notifications( $booking_id );

			if ( $booking->has_status( 'complete' ) ) {

				$this->schedule_booking_follow_up_notifications( $booking_id );

			} elseif ( $booking->has_status( array( 'paid', 'confirmed' ) ) ) {

				$this->schedule_booking_reminder_notifications( $booking_id );
			}
		}
	}


	/**
	 * Schedules admin and customer SMS message reminders for a confirmed booking.
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 *
	 * @param int $booking_id the ID of the related booking
	 */
	public function schedule_booking_reminder_notifications( $booking_id ) {

		$this->schedule_booking_notification( $booking_id, 'admin_reminder' );
		$this->schedule_booking_notification( $booking_id, 'customer_reminder' );
	}


	/**
	 * Schedules customer SMS post-booking messages.
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 *
	 * @param int $booking_id the ID of the related booking
	 */
	public function schedule_booking_follow_up_notifications( $booking_id ) {

		// clear any reminders just to be sure
		$this->clear_scheduled_notifications( $booking_id );

		$this->schedule_booking_notification( $booking_id, 'customer_follow_up', true );
	}


	/**
	 * Schedules a booking notification to send later.
	 *
	 * @since 1.12.0
	 *
	 * @param int $booking_id the booking ID
	 * @param string $notification the notification slug
	 * @param bool $after_event true if the notification should be sent after the event is complete
	 */
	public function schedule_booking_notification( $booking_id, $notification, $after_event = false ) {

		$booking = get_wc_booking( $booking_id );

		if ( $booking ) {

			$hook_args = array(
				'booking_id' => $booking_id,
			);

			$schedule = $this->get_notification_schedule( $notification, $booking->get_product() );

			if ( ! empty( $schedule ) ) {

				try {

					$timezone = new \DateTimeZone( wc_timezone_string() );

					$start_date = ( new \DateTime( date( 'Y-m-d H:i:s', $booking->get_start() ), $timezone ) )->getTimestamp();
					$end_date   = ( new \DateTime( date( 'Y-m-d H:i:s', $booking->get_end() ), $timezone ) )->getTimestamp();

				} catch ( \Exception $e ) {

					$start_date = $booking->get_start() - wc_timezone_offset();
					$end_date   = $booking->get_end() - wc_timezone_offset();
				}

				// determine when to send this notification and schedule action
				$notification_schedule  = new Bookings\Notification_Schedule( $schedule );
				$notification_timestamp = $after_event ? $notification_schedule->get_time_after( $end_date ) : $notification_schedule->get_time_before( $start_date );

				if ( ! as_next_scheduled_action( "wc_twilio_sms_bookings_{$notification}_notification", $hook_args ) ) {

					as_schedule_single_action( $notification_timestamp, "wc_twilio_sms_bookings_{$notification}_notification", $hook_args, 'woocommerce-twilio-sms-notifications' );
				}
			}
		}
	}


	/**
	 * Sends an admin reminder SMS message for an upcoming booking.
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 *
	 * @param int $booking_id the ID of the related booking
	 */
	public function send_admin_reminder_booking_notification( $booking_id ) {

		if ( $this->is_notification_enabled( $booking_id, 'admin_reminder' ) ) {
			$this->send_booking_notification( $booking_id, 'admin_reminder' );
		}
	}


	/**
	 * Sends a booking reminder SMS message to a customer.
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 *
	 * @param int $booking_id the ID of the related booking
	 */
	public function send_customer_reminder_booking_notification( $booking_id ) {

		if ( $this->is_notification_enabled( $booking_id, 'customer_reminder' ) ) {
			$this->send_booking_notification( $booking_id, 'customer_reminder' );
		}
	}


	/**
	 * Sends a booking follow-up SMS message to a customer.
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 *
	 * @param int $booking_id the ID of the related booking
	 */
	public function send_customer_follow_up_booking_notification( $booking_id ) {

		if ( $this->is_notification_enabled( $booking_id, 'customer_follow_up' ) ) {
			$this->send_booking_notification( $booking_id, 'customer_follow_up' );
		}
	}


	/**
	 * Sends a customer SMS message when a booking is confirmed.
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 *
	 * @param int $booking_id the ID of the related booking
	 */
	public function send_customer_confirmed_booking_notification( $booking_id ) {

		if ( $this->is_notification_enabled( $booking_id, 'customer_confirmation' ) ) {
			$this->send_booking_notification( $booking_id, 'customer_confirmation' );
		}

		// now schedule reminders if they're not set already
		$this->schedule_booking_reminder_notifications( $booking_id );
	}


	/**
	 * Sends SMS messages relevant to a cancelled booking.
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 *
	 * @param int $booking_id the ID of the related booking
	 */
	public function send_cancelled_booking_notifications( $booking_id ) {

		if ( $this->is_notification_enabled( $booking_id, 'admin_cancellation' ) ) {
			$this->send_booking_notification( $booking_id, 'admin_cancellation' );
		}

		if ( $this->is_notification_enabled( $booking_id, 'customer_cancellation' ) ) {
			$this->send_booking_notification( $booking_id, 'customer_cancellation' );
		}
	}


	/** Notification helpers ******************************************************/


	/**
	 * Sends a booking notification.
	 *
	 * @since 1.12.0
	 *
	 * @param int $booking_id the booking ID
	 * @param string $notification the notification type
	 */
	public function send_booking_notification( $booking_id, $notification ) {

		if ( $booking = get_wc_booking( $booking_id ) ) {

			$template = $this->get_template( $notification, $booking->get_product() );

			if ( ! $template ) {
				return;
			}

			$message = $this->build_sms_message( $template, $booking );

			if ( $this->is_admin_notification( $notification ) ) {

				$phone_numbers = $this->parse_mobile_numbers( get_option( "wc_twilio_sms_bookings_{$notification}_recipients", array() ) );

				foreach ( $phone_numbers as $number ) {
					$this->send_sms_message( $number, $message );
				}

			} else {

				$booking_order = $booking->get_order();
				$phone_number  = $booking_order ? $booking_order->get_billing_phone() : '';
				$country_code  = $this->get_customer_country_code( $booking );

				if ( '' !== $phone_number ) {
					$this->send_sms_message( $phone_number, $message, $country_code );
				}
			}
		}
	}


	/**
	 * Takes a CSV string of mobile phone numbers and returns those in an array.
	 *
	 * @since 1.12.0
	 *
	 * @param string $mobile_numbers CSV string of mobile phone numbers
	 * @return array array of mobile phone numbers
	 */
	public function parse_mobile_numbers( $mobile_numbers ) {

		return array_map( 'trim', explode( ',', $mobile_numbers ) );
	}


	/**
	 * Replaces tokens in an SMS message template with data from a booking.
	 *
	 * @since 1.12.0
	 *
	 * @param string $message
	 * @param \WC_Booking $booking
	 * @return string $message
	 */
	public function build_sms_message( $message, \WC_Booking $booking ) {

		/** @var \WC_Order $order */
		$order = $booking->get_order();

		$billing_name = $order ? $order->get_formatted_billing_full_name() : '';

		$date_format = wc_date_format();
		$time_format = wc_time_format();
		$resource    = new \WC_Product_Booking_Resource( $booking->get_resource_id() );

		$token_map = array(
			'{shop_name}'          => Framework\SV_WC_Helper::get_site_name(),
			'{billing_name}'       => $billing_name,
			'{booking_start_time}' => date_i18n( $time_format, $booking->get_start() ),
			'{booking_end_time}'   => date_i18n( $time_format, $booking->get_end() ),
			'{booking_date}'       => date_i18n( $date_format, $booking->get_start() ),
			'{person_count}'       => (int) $booking->get_persons_total(),
			'{resource}'           => $resource->get_title(),
		);

		/**
		 * Allow actors to change the SMS message tokens.
		 *
		 * @since 1.12.0
		 *
		 * @param bool $token_map
		 */
		$token_map = (array) apply_filters( 'wc_twilio_sms_bookings_token_map', $token_map );

		foreach ( $token_map as $key => $value ) {

			$message = str_replace( $key, $value, $message );
		}

		return $message;
	}


	/**
	 * Sends a message to a mobile phone number via SMS.
	 *
	 * @since 1.12.0
	 *
	 * @param string $mobile_number the phone number to send the message to
	 * @param string $message the message to send
	 * @param string $country_code (optional) country code in ISO_3166-1_alpha-2 format
	 */
	public function send_sms_message( $mobile_number, $message, $country_code = null ) {

		// sanitize input
		$mobile_number = trim( $mobile_number );
		$message       = sanitize_text_field( $message );

		try {

			if ( \WC_Twilio_SMS_URL_Shortener::using_shortened_urls() ) {

				$message = \WC_Twilio_SMS_URL_Shortener::shorten_urls( $message );
			}

			wc_twilio_sms()->get_api()->send( $mobile_number, $message, $country_code );

		} catch ( \Exception $e ) {

			$error_message = sprintf( __( 'Error sending SMS: %s', 'woocommerce-twilio-sms-notifications' ), $e->getMessage() );

			wc_twilio_sms()->log( $error_message );
		}
	}


	/**
	 * Gets the available booking notifications.
	 *
	 * @since 1.12.0
	 *
	 * @return array available notifications as slug => label
	 */
	public function get_booking_notifications() {

		/**
		 * Filter the available booking notifications. Lets actors add their own notification key.
		 *
		 * @since 1.12.0
		 *
		 * @param array notifications as slug => label
		 */
		return (array) apply_filters( 'wc_twilio_sms_bookings_notifications', array(
			'admin_reminder'        => __( 'Admin reminder', 'woocommerce-twilio-sms-notifications' ),
			'admin_cancellation'    => __( 'Admin cancellation', 'woocommerce-twilio-sms-notifications' ),
			'customer_reminder'     => __( 'Customer reminder', 'woocommerce-twilio-sms-notifications' ),
			'customer_follow_up'    => __( 'Customer follow up', 'woocommerce-twilio-sms-notifications' ),
			'customer_cancellation' => __( 'Customer cancellation', 'woocommerce-twilio-sms-notifications' ),
			'customer_confirmation' => __( 'Customer confirmation', 'woocommerce-twilio-sms-notifications' ),
		) );
	}


	/**
	 * Gets the template for a notification.
	 *
	 * @since 1.12.0
	 *
	 * @param string $notification the notification slug
	 * @param \WC_Product|null $product
	 * @return string|null $template
	 */
	protected function get_template( $notification, \WC_Product $product = null ) {

		// default to global template before checking for product-specific template
		$template = get_option( "wc_twilio_sms_bookings_{$notification}_template", '' );

		/**
		 * Get the product-specific bookings notification options.
		 * @see save_booking_notification_tab_options()
		 */
		if ( $product ) {

			$bookings_options = $product->get_meta( '_wc_twilio_sms_bookings_options' );

			if ( ! empty( $bookings_options ) && isset( $bookings_options[ $notification ] ) && 'override' === $bookings_options[ $notification ] ) {

				$template = $bookings_options[ "{$notification}_template" ];
			}
		}

		/**
		 * Allow actors to change the SMS template.
		 *
		 * @since 1.12.0
		 *
		 * @param string the found message template.
		 */
		return apply_filters( 'wc_twilio_sms_bookings_notification_template', $template, $notification );
	}


	/**
	 * Gets the default notification templates.
	 *
	 * @since 1.12.0
	 *
	 * @param string $notification the notification slug
	 * @return string the default notification template
	 */
	public function get_default_template( $notification ) {

		$default = __( 'The {shop_name} booking for {billing_name} starts at {booking_start_time} on {booking_date}', 'woocommerce-twilio-sms-notifications' );

		/**
		 * Filters the default SMS templates for notifications.
		 *
		 * @since 1.12.0
		 *
		 * @param array default templates
		 */
		$templates = (array) apply_filters( 'wc_twilio_sms_bookings_default_notification_templates', array(
			'admin_reminder'        => __( "Heads up: your appointment with {billing_name} starts at {booking_start_time}", 'woocommerce-twilio-sms-notifications' ),
			'admin_cancellation'    => __( 'Your appointment with {billing_name} at {booking_start_time} on {booking_date} has been cancelled', 'woocommerce-twilio-sms-notifications' ),
			'customer_reminder'     => __( 'Hi {billing_name}! This is a reminder that your {shop_name} booking starts at {booking_start_time} on {booking_date}. See you soon!', 'woocommerce-twilio-sms-notifications' ),
			'customer_follow_up'    => __( 'Thanks again for booking with {shop_name}, {billing_name}! We hope to see you again soon.', 'woocommerce-twilio-sms-notifications' ),
			'customer_cancellation' => __( 'Your appointment with {shop_name} at {booking_start_time} on {booking_date} has been cancelled', 'woocommerce-twilio-sms-notifications' ),
			'customer_confirmation' => __( 'Your appointment with {shop_name} at {booking_start_time} on {booking_date} has been confirmed. See you there!', 'woocommerce-twilio-sms-notifications' ),
		) );

		return in_array( $notification, array_keys( $templates ), true ) ? $templates[ $notification ] : $default;
	}


	/**
	 * Checks if a notification has a schedule.
	 *
	 * @since 1.12.0
	 *
	 * @param string $notification notification key
	 * @return bool true if it has a schedule
	 */
	public function notification_has_schedule( $notification ) {

		$has_schedule = in_array( $notification, array( 'admin_reminder', 'customer_reminder', 'customer_follow_up' ), true );

		/**
		 * Filters whether the notification has a schedule.
		 *
		 * @since 1.12.0
		 *
		 * @param bool if the notification has a schedule
		 * @param string notification key
		 */
		return (bool) apply_filters( 'wc_twilio_sms_bookings_notification_has_schedule', $has_schedule, $notification );
	}


	/**
	 * Returns a notification schedule in the format used by the Bookings\Notification_Schedule class.
	 *
	 * @since 1.12.0
	 *
	 * @param string $notification the notification slug
	 * @param \WC_Product|null $product
	 * @return string|null schedule in the format d:s+ (example: 5:days)
	 */
	public function get_notification_schedule( $notification, \WC_Product $product = null ) {

		// default to global schedule before checking for product-specific schedule
		$schedule = get_option( "wc_twilio_sms_bookings_{$notification}_schedule", '' );

		if ( $product ) {

			/**
			 * Get the product-specific bookings notification options.
			 * @see save_booking_notification_tab_options()
			 */
			$bookings_options = $product->get_meta( '_wc_twilio_sms_bookings_options' );

			if ( ! empty( $bookings_options ) && isset( $bookings_options[ $notification ] ) && 'override' === $bookings_options[ $notification ] ) {

				$schedule = $bookings_options[ "{$notification}_schedule" ];
			}
		}

		/**
		 * Allow actors to change the schedule.
		 *
		 * @since 1.12.0
		 *
		 * @param string|null the configured schedule
		 */
		return apply_filters( 'wc_twilio_sms_bookings_notification_schedule', $schedule, $notification );
	}


	/**
	 * Determines if a booking notification should be sent.
	 *
	 * @since 1.12.0
	 *
	 * @param int $booking_id the booking ID
	 * @param string $notification the notification type
	 * @return bool true if a booking confirmation notification should be sent
	 */
	public function is_notification_enabled( $booking_id, $notification ) {

		// default to global setting before checking for product-specific setting
		$enabled = 'yes' === get_option( "wc_twilio_sms_bookings_send_{$notification}" );
		$booking = get_wc_booking( $booking_id );

		if ( $booking && $product = $booking->get_product() ) {

			/**
			 * Get the product-specific bookings notification options.
			 * @see save_booking_notification_tab_options()
			 */
			$bookings_options = $product->get_meta( '_wc_twilio_sms_bookings_options' );

			if ( ! empty( $bookings_options ) && isset( $bookings_options[ $notification ] ) ) {

				// check for options other than 'global' and set enabled status accordingly
				if ( 'override' === $bookings_options[ $notification ] ) {

					$enabled = true;

				} elseif ( 'disabled' === $bookings_options[ $notification ] ) {

					$enabled = false;
				}
			}

			// for customer notifications, if we think we should be sending it, confirm the customer has opted in
			if ( ! $this->is_admin_notification( $notification ) && $enabled && $booking->get_order() ) {
				$enabled = '1' === $booking->get_order()->get_meta( '_wc_twilio_sms_optin' );
			}
		}

		/**
		 * Allow actors to change the status of the notification.
		 *
		 * @since 1.12.0
		 *
		 * @param bool $enabled
		 * @param string $notification the notification type
		 */
		return (bool) apply_filters( 'wc_twilio_sms_bookings_notification_enabled', $enabled, $notification );
	}


	/**
	 * Checks if a notification is an admin-notification (and thus has recipients).
	 *
	 * @since 1.12.0
	 *
	 * @param string $notification notification key
	 * @return bool true if it is for admins
	 */
	public function is_admin_notification( $notification ) {

		$for_admins = in_array( $notification, array( 'admin_reminder', 'admin_cancellation' ), true );

		/**
		 * Filters whether the notification is for admins.
		 *
		 * @since 1.12.0
		 *
		 * @param bool if the notification has a schedule
		 * @param string notification key
		 */
		return (bool) apply_filters( 'wc_twilio_sms_bookings_is_admin_notification', $for_admins, $notification );
	}


	/** General helpers ******************************************************/


	/**
	 * When overriding a global booking schedule option, determines if a valid selection is made
	 *
	 * @since 1.12.0
	 *
	 * @param string $option option name
	 * @return bool true if the product override option is valid
	 */
	private function is_valid_product_override_option( $option ) {

		return ( 'global' === $option || 'override' === $option || 'disabled' === $option );
	}


	/**
	 * Returns a customer's mobile number country code based on their billing information.
	 *
	 * @since 1.12.0
	 *
	 * @param \WC_Booking $booking
	 * @return string the country code
	 */
	private function get_customer_country_code( \WC_Booking $booking ) {

		$order = $booking->get_order();

		return $order ? $order->get_billing_country() : null;
	}


	/**
	 * Adds an admin message informing the user their notification schedule has been adjusted.
	 *
	 * @since 1.12.0
	 */
	private function add_restricted_schedule_message() {

		wc_twilio_sms()->get_message_handler()->add_info( __( 'Reminder SMS notifications can only be sent up to 48 hours before an event starts. Your schedule has been automatically adjusted.', 'woocommerce-twilio-sms-notifications' ) );
	}


}
