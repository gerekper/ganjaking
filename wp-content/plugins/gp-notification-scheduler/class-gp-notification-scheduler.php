<?php

if ( ! class_exists( 'GP_Plugin' ) ) {
	return;
}

class GP_Notification_Scheduler extends GP_Plugin {
	/**
	 * Contains an instance of this class, if available.
	 *
	 * @since 0.9
	 * @access private
	 * @var GP_Notification_Scheduler $_instance If available, contains an instance of this class
	 */
	private static $_instance = null;

	/**
	 * Defines the version of the Gravity Forms Notification Scheduler Add-On Add-On.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string $_version Contains the version.
	 */
	protected $_version = GP_NOTIFICATION_SCHEDULER_VERSION;

	/**
	 * Defines the minimum Gravity Forms version required.
	 * @since 0.9
	 * @access protected
	 * @var string $_min_gravityforms_version The minimum version required.
	 */
	protected $_min_gravityforms_version = '2.5';

	/**
	 * Defines the plugin slug.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string $_slug The slug used for this plugin.
	 */
	protected $_slug = 'gp-notification-scheduler';

	/**
	 * Defines the main plugin file.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string $_path The path to the main plugin file, relative to the plugins folder.
	 */
	protected $_path = 'gp-notification-scheduler/gp-notification-scheduler.php';

	/**
	 * Defines the full path to this class file.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string $_full_path The full path.
	 */
	protected $_full_path = __FILE__;

	/**
	 * Defines the URL where this add-on can be found.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string
	 */
	protected $_url = 'http://gravitywiz.com';

	/**
	 * Defines the title of this add-on.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string $_title The title of the add-on.
	 */
	protected $_title = 'Gravity Forms Notification Scheduler Add-On Add-On';

	/**
	 * Defines the short title of the add-on.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string $_short_title The short title.
	 */
	protected $_short_title = 'Notification Scheduler';

	/**
	 * Returns an instance of this class, and stores it in the $_instance property.
	 *
	 * @return GP_Notification_Scheduler $_instance An instance of the GP_Notification_Scheduler class
	 * @since 0.9
	 * @access public
	 * @static
	 */
	public static function get_instance() {
		if ( self::$_instance === null ) {
			self::$_instance = new GP_Notification_Scheduler();
		}

		return self::$_instance;
	}

	/**
	 * Attaches any filters or actions needed to bootstrap the addon.
	 *
	 * Bootstrap was added in GF 2.5.
	 */
	public function bootstrap() {
		parent::bootstrap();

		$this->register_wpdb_tables();
	}

	public function pre_init() {

		parent::pre_init();

		$this->setup_cron();

	}

	/**
	 * Register tables with $wpdb.
	 *
	 * @return void
	 */
	public function register_wpdb_tables() {
		global $wpdb;

		$wpdb->gpns_unsubscribes = "{$wpdb->prefix}gpns_unsubscribes";
	}

	public function init() {
		parent::init();

		/**
		 * Filter to toggle the batcher to rebuild all notification queues for Notification Scheduler.
		 *
		 * @param boolean $enable_rebuild_batcher Whether the rebuild batcher should be enabled.
		 *
		 * @since 1.0
		 */
		if ( apply_filters( 'gpns_enable_rebuild_batcher', false ) ) {
			$this->initialize_rebuild_batcher();
		}

		// Hooks
		add_filter( 'gform_tooltips', array( $this, 'add_tooltips' ) );
		add_filter( 'gform_notification_settings_fields', array( $this, 'add_notification_settings' ), 10, 3 );
		add_filter( 'gform_pre_send_email', array( $this, 'catch_email_and_schedule_notification' ), 10, 4 );
		add_filter( 'gform_pre_send_email', array( $this, 'check_for_unsubscribe' ), 99, 4 );
		add_filter( 'gform_pre_send_email', array( $this, 'replace_unsubscribe_merge_tags' ), 10, 4 );
		add_filter( 'gform_admin_pre_render', array( $this, 'add_merge_tags' ) );

		add_action( 'template_redirect', array( $this, 'maybe_process_unsubscribe' ) );
		add_action( 'gform_after_update_entry', array( $this, 'update_notification_queue_after_entry_update' ), 10, 3 );
		add_action( 'gform_post_update_entry', array( $this, 'update_notification_queue_post_update_entry' ), 10, 2 );

		add_filter( 'gform_post_note_added', array( $this, 'modify_notification_note' ), 10, 7 );

		add_filter( 'gform_entry_detail_meta_boxes', array( $this, 'register_entry_scheduled_notifications_meta_box' ), 10, 3 );

		add_filter( 'gpns_current_time', array( $this, 'testing_current_time_override' ) );
	}

	public function setup() {
		$this->create_tables();
	}

	protected function create_tables() {
		global $wpdb;

		require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
		}

		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE {$wpdb->collate}";
		}

		$sql = "
			CREATE TABLE {$wpdb->gpns_unsubscribes} (
			    id bigint(20) unsigned auto_increment,
                email varchar(100) not null,
                form_id mediumint(10) unsigned,
                notification_id varchar(20),
                scope varchar(20) default 'all',
                timestamp_gmt datetime not null,
                PRIMARY KEY  (id),
                KEY email (email),
                KEY email_scope (email, scope)
            ) $charset_collate;";

		gf_upgrade()->dbDelta( $sql );
	}

	public function init_admin() {
		parent::init_admin();
	}

	public function uninstall() {
		global $wpdb;

		wp_clear_scheduled_hook( 'gpns_cron' );

		$wpdb->query( "DROP TABLE {$wpdb->gpns_unsubscribes}" );

		parent::uninstall();
	}

	public function scripts() {

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

		$scripts = array(
			array(
				'handle'  => 'gp-notification-scheduler-admin',
				'src'     => $this->get_base_url() . "/js/gp-notification-scheduler-admin{$min}.js",
				'deps'    => array( 'jquery', 'jquery-ui-datepicker' ),
				'enqueue' => array(
					array(
						'admin_page' => 'form_settings',
						'tab'        => 'notification',
					),
				),
			),
		);

		return array_merge( parent::scripts(), $scripts );
	}

	public function styles() {

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

		$styles = array(
			array(
				'handle'  => 'gp-notification-scheduler-admin',
				'src'     => $this->get_base_url() . "/css/gp-notification-scheduler-admin{$min}.css",
				'enqueue' => array(
					array(
						'admin_page' => 'form_settings',
						'tab'        => 'notification',
					),
				),
			),
		);

		return array_merge( parent::styles(), $styles );
	}

	public function setup_cron() {

		add_filter( 'cron_schedules', array( $this, 'add_cron_schedule' ) );

		if ( ! wp_next_scheduled( 'gpns_cron' ) ) {
			wp_schedule_event( time(), 'five_minutes', 'gpns_cron' );
		}

		add_action( 'gpns_cron', array( $this, 'cron' ) );

	}

	public function add_cron_schedule( $schedules ) {

		$schedules['five_minutes'] = array(
			'interval' => 5 * MINUTE_IN_SECONDS,
			'display'  => esc_html__( 'Every Five Minutes' ),
		);

		return $schedules;
	}

	public function cron() {

		$queue   = $this->get_notification_queue( false, true );
		$entries = array();
		$sending = array();

		$this->log( sprintf( 'Notification queue: %s', print_r( $queue, true ) ) );

		foreach ( $queue as $scheduled_notification ) {

			$entry_id = $scheduled_notification['entry_id'];
			if ( ! isset( $entries[ $entry_id ] ) ) {
				$entries[ $entry_id ] = GFAPI::get_entry( $entry_id );
			}

			$entry = $entries[ $entry_id ];
			if ( is_wp_error( $entry ) ) {
				continue;
			}

			$form = GFAPI::get_form( $entry['form_id'] );

			if ( ! isset( $sending[ $entry_id ] ) ) {
				$sending[ $entry_id ] = array(
					'notifications' => array(),
					'entry'         => $entry,
					'form'          => $form,
				);
			}

			$sending[ $entry_id ]['notifications'][] = $scheduled_notification;

		}

		$this->log( sprintf( 'Sending: (%d) %s', count( $sending ), print_r( wp_list_pluck( $sending, 'notifications' ), true ) ) );

		if ( empty( $sending ) ) {
			$this->log( sprintf( 'Aborting: nothing to send.', print_r( $queue, true ) ) );

			return;
		}

		$sent = array();

		foreach ( $sending as $entry_id => $_sending ) {
			// $event param is only for writing (at the time of writing this comment 😆)
			$notifications = $_sending['notifications'];

			$form  = $_sending['form'];
			$entry = $_sending['entry'];

			/**
			 * Whether conditional logic should be evaluated when sending scheduled notifications.
			 *
			 * Note: this does not control whether conditional logic is evaluated when the notification is initially
			 * scheduled when the original event is triggered.
			 *
			 * @param boolean $do_conditional_logic Whether conditional logic should be evaluated.
			 * @param array $form The current form.
			 * @param array $entry The current entry.
			 * @param array $notifications The notifications queued for the current form.
			 *
			 * @since 1.0.2
			 */
			$do_conditional_logic = gf_apply_filters( array(
				'gpns_evaluate_conditional_logic_on_send',
				$form['id'],
			), false, $form, $entry, $notifications );

			GFCommon::send_notifications( wp_list_pluck( $notifications, 'nid' ), $form, $entry, $do_conditional_logic, 'scheduled' );
			$sent = array_merge( $sent, wp_list_pluck( $notifications, 'id' ) );
			$this->add_recurring_notifications( wp_list_pluck( $notifications, 'nid' ), $entry, $form );
		}

		$this->delete_notification_queue_by_meta_id( array_filter( $sent ) );

	}

	public function get_current_group_field( $group, $entry, $timestamp ) {

		foreach ( $group['fields'] as $field_id ) {

			$schedule_date_gmt = gmdate( 'Y-m-d H:i:s', strtotime( rgar( $entry, $field_id ) ) );
			$schedule_datetime = strtotime( $schedule_date_gmt );

			if ( $schedule_datetime === $timestamp ) {
				return $field_id;
			}
		}

		return false;
	}

	public function get_group_by_slug( $groups, $slug ) {

		$groups = wp_list_filter( $groups, array( 'slug' => $slug ) );
		$group  = reset( $groups );

		return $group;
	}

	public function modify_notification_note( $note_id ) {

		if ( ! doing_action( 'gpns_cron' ) ) {
			return;
		}

		$note = GFFormsModel::get_notes( array( 'id' => $note_id ) )[0];
		if ( $note->note_type !== 'notification' ) {
			return;
		}

		// Parse out the notification ID from the note's username. Default looks something like: `Notification Name (ID: 618af20b8308b)`.
		preg_match( '/\(ID: ([a-z0-9]+)\)/', $note->user_name, $matches );
		if ( empty( $matches ) ) {
			return;
		}

		$notification_id = $matches[1];
		$entry           = GFAPI::get_entry( $note->entry_id );
		$form            = GFAPI::get_form( $entry['form_id'] );
		$notification    = $this->get_notification( $form, $notification_id );

		if ( ! $notification['scheduleType'] || $notification['scheduleType'] === 'immediate' ) {
			return;
		}

		$user_name = $note->user_name . ' ' . esc_html__( 'via Notification Scheduler', 'gp-notification-scheduler' );

		GFFormsModel::update_note( $note_id, $note->entry_id, $note->user_id, $user_name, $note->date_created, $note->value, $note->note_type, $note->sub_type );

	}

	/**
	 * @param $tooltips
	 *
	 * @return mixed
	 */
	public function add_tooltips( $tooltips ) {
		$tooltips['notification_schedule']        = sprintf( '<h6>%s</h6> %s', __( 'Notification Schedule', 'gp-notification-scheduler' ), __( 'Schedule this notification to be sent before, after or on a specific date.', 'gp-notification-scheduler' ) );
		$tooltips['notification_schedule_repeat'] = sprintf( '<h6>%s</h6> %s', __( 'Repeat Notification', 'gp-notification-scheduler' ), __( 'Resend this notification at a set interval. Interval will always begin from the scheduled notification date and time.', 'gp-notification-scheduler' ) );

		return $tooltips;
	}

	/**
	 * Add the notification scheduler setting to notification settings array.
	 *
	 * @param array $fields Form settings fields.
	 * @param array $notification The current notification.
	 * @param array $form   Form Object.
	 */
	public function add_notification_settings( $settings, $notification, $form ) {

		require_once( $this->get_base_path() . '/includes/class-gpns-settings-field-schedule.php' );

		array_splice( $settings[0]['fields'], count( $settings[0]['fields'] ) - 2, 0, array(
			array(
				'name'    => 'schedule',
				'label'   => esc_html__( 'Schedule', 'gp-notification-scheduler' ),
				'type'    => 'notification_schedule',
				'tooltip' => 'notification_schedule',
			),
		) );

		return $settings;

	}

	public function get_schedule_types( $form ) {

		$types = array(
			'immediate' => __( 'Send Immediately', 'gp-notification-scheduler' ),
			'delay'     => __( 'Delay', 'gp-notification-scheduler' ),
			'date'      => __( 'Date', 'gp-notification-scheduler' ),
		);

		if ( $this->has_date_fields( $form ) ) {
			$types['field'] = __( 'Date Field', 'gp-notification-scheduler' );
		}

		/**
		 * Filter the schedule types available to Notification Scheduler.
		 *
		 * @param array $types Associative array containing the ID of the schedule type and its label.
		 *
		 * @since 1.0
		 */
		return gf_apply_filters( array( 'gpns_schedule_types', $form['id'] ), $types );
	}

	public function get_units() {
		/**
		 * Filter the units available to Notification Scheduler.
		 *
		 * @param array $units Associative array containing the ID of the unit as a plural (e.g. "minutes") and
		 *   the value being its label (e.g. "minute(s)").
		 *
		 * @since 1.0.1
		 */
		return apply_filters( 'gpns_units', array(
			'minutes' => __( 'minute(s)', 'gp-notification-scheduler' ),
			'hours'   => __( 'hour(s)', 'gp-notification-scheduler' ),
			'days'    => __( 'day(s)', 'gp-notification-scheduler' ),
			'weeks'   => __( 'week(s)', 'gp-notification-scheduler' ),
			'months'  => __( 'month(s)', 'gp-notification-scheduler' ),
			'years'   => __( 'year(s)', 'gp-notification-scheduler' ),
		) );
	}

	public function get_date_fields( $form ) {
		$fields = GFCommon::get_fields_by_type( $form, array( 'date' ) );

		/**
		 * Filter the fields in a form that Notification Scheduler will treat as Date fields.
		 *
		 * @param array $fields The date fields in the form.
		 * @param array $form   The current form.
		 *
		 * @since 1.0
		 */
		return gf_apply_filters( array( 'gpns_date_fields', $form['id'] ), $fields, $form );
	}

	public function get_date_groups( $form ) {
		/**
		 * @todo document or remove
		 */
		return gf_apply_filters( array( 'gpns_date_groups', $form['id'] ), array(), $form );
	}

	public function has_date_fields( $form ) {
		return count( $this->get_date_fields( $form ) ) >= 1;
	}

	public function get_numeric_choices( $min, $max ) {
		$choices = array();
		for ( $i = $min; $i <= $max; $i ++ ) {
			$choices[] = array(
				'label' => $i,
				'value' => $i,
			);
		}

		return $choices;
	}

	/**
	 * If a Notification is has a schedule but is not scheduled yet, catch it when the email is being sent out and
	 * add it to the schedule while also aborting the email so the initial notification is not sent.
	 *
	 * @param array $email An array containing the email to address, subject, message, headers, attachments and abort email flag.
	 *          'to', 'subject', 'message', 'headers', 'attachments', 'abort_email'
	 * @param string $message_format The message format: html or text.
	 * @param array $notification The current Notification object.
	 * @param array $entry The current Entry object.
	 *
	 * @return array
	 */
	public function catch_email_and_schedule_notification( $email, $message_format, $notification, $entry ) {
		/* This key is set by GP_Notification_Scheduler::add_to_notification_queue() */
		$scheduled_key = 'gpns_schedule_' . $notification['id'];

		/* Do not intercept if the Notification isn't scheduled. */
		if ( rgar( $notification, 'scheduleType', 'immediate' ) === 'immediate' ) {
			return $email;
		}

		$scheduled_timestamp = gform_get_meta( $entry['id'], $scheduled_key );

		/**
		 * If already scheduled, do not re-schedule.
		 *
		 * Abort sending if the time hasn't been reached yet. Otherwise, let the email be sent.
		 */
		if ( $scheduled_timestamp ) {
			if ( $scheduled_timestamp <= $this->get_current_time() ) {
				return $email;
			}
		} else {
			$timestamp = $this->get_schedule_timestamp( $notification, $entry );

			if ( $timestamp && $timestamp >= $this->get_current_time() ) {
				$notifications = array(
					array(
						'nid'       => $notification['id'],
						'timestamp' => $timestamp,
					),
				);

				if ( ! $this->is_email_unsubscribed( $email['to'], $entry['form_id'], $notification['id'] ) ) {
					$this->add_to_notification_queue( $entry['id'], $entry['form_id'], $notifications );
				} else {
					// translators: Placeholder is the email address that the notification was sent to.
					$message = sprintf( esc_html__( 'Notification not scheduled due to email (%s) being unsubscribed.', 'gp-notification-scheduler' ), $email['to'] );
					$this->add_notification_note( $entry['id'], $notification, $message, 'notification', 'warning' );
				}
			}
		}

		$email['abort_email'] = true;

		return $email;
	}

	public function add_notification_note( $entry_id, $notification, $message, $type, $subtype ) {
		// translators: Notification name followed by its ID. e.g. Admin Notification (ID: 5d4c0a2a37204).
		GFFormsModel::add_note( $entry_id, 0, sprintf( esc_html__( '%1$s (ID: %2$s) via Notification Scheduler', 'gp-notification-scheduler' ), $notification['name'], $notification['id'] ), $message, $type, $subtype );
	}

	/**
	 * If the notification is a scheduled notification, check that the current email is not opted out.
	 *
	 * @param array $email An array containing the email to address, subject, message, headers, attachments and abort email flag.
	 *          'to', 'subject', 'message', 'headers', 'attachments', 'abort_email'
	 * @param string $message_format The message format: html or text.
	 * @param array $notification The current Notification object.
	 * @param array $entry The current Entry object.
	 *
	 * @return array
	 */
	public function check_for_unsubscribe( $email, $message_format, $notification, $entry ) {
		$scheduled_key = 'gpns_schedule_' . $notification['id'];

		// Do not check unsubscribe status for unscheduled notifications.
		if ( rgar( $notification, 'scheduleType', 'immediate' ) === 'immediate' || ! gform_get_meta( $entry['id'], $scheduled_key ) ) {
			return $email;
		}

		/*
		 * Do not check subscription status of already-aborted emails as it can cause a notification note to be added to
		 * the entry as the notification is being scheduled in GP_Notification_Scheduler::catch_email_and_schedule_notification()
		 */
		if ( rgar( $email, 'abort_email' ) ) {
			return $email;
		}

		/**
		 * @todo Do we need to support comma-list of to recipients? If so, do we remove them from the array or immediately
		 *   abort the email?
		 */
		if ( $this->is_email_unsubscribed( $email['to'], $entry['form_id'], $notification['id'] ) ) {
			$email['abort_email'] = true;

			GFFormsModel::add_notification_note( $entry['id'], null, $notification, null, $email, array(
				'type'    => 'notification',
				'subtype' => 'warning',
				'text'    => sprintf( esc_html__( 'Notification not sent due to email (%s) being unsubscribed.', 'gp-notification-scheduler' ), $email['to'] ),
			) );
		}

		return $email;
	}

	/**
	 * Replace merge tags for unsubscribe URLs/links in notification emails.
	 *
	 * @param array $email An array containing the email to address, subject, message, headers, attachments and abort email flag.
	 *          'to', 'subject', 'message', 'headers', 'attachments', 'abort_email'
	 * @param string $message_format The message format: html or text.
	 * @param array $notification The current Notification object.
	 * @param array $entry The current Entry object.
	 *
	 * @return array
	 */
	public function replace_unsubscribe_merge_tags( $email, $message_format, $notification, $entry ) {
		$email['message'] = str_replace( '{unsubscribe_url}', $this->create_unsubscribe_url( $email['to'], $entry, $notification ), $email['message'] );
		$email['message'] = str_replace( '{unsubscribe_link}', $this->create_unsubscribe_link( $email['to'], $entry, $notification ), $email['message'] );

		return $email;
	}

	/**
	 * Create URL to unsubscribe an email.
	 *
	 * @param string $email Email address to unsubscribe.
	 * @param array $entry The current Entry.
	 * @param array $notification The current Notification object.
	 *
	 * @return string The unsubscribe URL.
	 */
	public function create_unsubscribe_url( $email, $entry, $notification ) {
		$form_id = $entry['form_id'];
		$nid     = $notification['id'];

		/**
		 * Filter the arguments used to build the unsubscribe URL.
		 *
		 * @param array $unsubscribe_info {
		 *     Settings used to construct the unsubscribe URL that will be sent in the Notification.
		 *
		 *     @type string $email      The email address to unsubscribe.
		 *     @type int $fid           The form ID that the current Notification belongs to.
		 *     @type string $nid        The current Notification's ID.
		 *     @type string $scope      The scope of the unsubscribe action. Defaults to unsubscribing from all scheduled notifications.
		 *                              To unsubscribe the email from the current form's scheduled notifications, change the `scope` to `form_id`.
		 *                              To unsubscribe from only the current notification, change `scope` to `nid`.
		 *     @type string|null $url   The URL to redirect to after unsubscribing. If left `null`, a generic unsubscribe page will be used.
		 * }
		 * @param array $entry The current entry.
		 * @param array $notification The current notification.
		 * @param string $email The email address being unsubscribed.
		 *
		 * @since 1.1
		 */
		$unsubscribe_info = gf_apply_filters( array( 'gpns_unsubscribe_url_args', $form_id, $nid ), array(
			'email' => $email,
			'fid'   => $form_id,
			'nid'   => $nid,
			'scope' => 'all',
			'url'   => null,
		), $entry, $notification, $email );

		return add_query_arg( array(
			'gpns_unsubscribe' => rawurlencode( GFCommon::openssl_encrypt( json_encode( $unsubscribe_info ) ) ),
		), home_url() );
	}

	/**
	 * Create an unsubscribe HTML link.
	 *
	 * @param string $email Email address to unsubscribe.
	 * @param array $entry The current Entry.
	 * @param array $notification The current Notification object.
	 *
	 * @return string The unsubscribe HTML link.
	 */
	public function create_unsubscribe_link( $email, $entry, $notification ) {
		$url = $this->create_unsubscribe_url( $email, $entry, $notification );

		/**
		 * Filter the text inside unsubscribe links.
		 *
		 * @param string $text The unsubscribe link text. Defaults to "Unsubscribe"
		 * @param array $notification The current Notification.
		 * @param array $entry The current entry.
		 *
		 * @since 1.1
		 */
		$text = apply_filters( 'gpns_unsubscribe_link_text', __( 'Unsubscribe', 'gp-notification-scheduler' ), $notification, $entry );

		return sprintf( '<a href="%s">%s</a>', $url, $text );
	}

	/**
	 * Check if an email is unsubscribed to a form and/or notification.
	 *
	 * We initially looked into making this method work off of the results of a single MySQL query that gets all the
	 * unsubscribed emails to reduce the number of overall queries. However, when adding in the additional WHERE's for
	 * the form ID and notification ID check, if there are notifications being sent for multiple forms/notifications
	 * during a cron request, it mostly defeats the purpose. Additionally, if an unsubscribe list grows to a large number,
	 * it could eventually take a toll on the PHP memory.
	 *
	 * @param string $email The email to check.
	 * @param number $form_id The current form.
	 * @param string $notification_id The current notification being sent.
	 *
	 * @return boolean Whether the email is unsubscribed to the specified form and notification.
	 */
	public function is_email_unsubscribed( $email, $form_id, $notification_id ) {
		global $wpdb;

		$count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->gpns_unsubscribes}
			WHERE `email` = %s
			AND (
			    `scope` = 'all'
				OR (`scope` = 'form_id' AND form_id = %d)
				OR (`scope` = 'nid' AND form_id = %d AND notification_id = %s)
			)", $email, $form_id, $form_id, $notification_id ) );

		return $count > 0;
	}

	/**
	 * Process email unsubscribe if query param is present.
	 *
	 * @return void
	 */
	public function maybe_process_unsubscribe() {
		global $wpdb;

		$unsubscribe_param = rawurldecode( rgget( 'gpns_unsubscribe' ) );

		if ( ! $unsubscribe_param ) {
			return;
		}

		$unsubscribe_info = self::maybe_decode_json( GFCommon::openssl_decrypt( $unsubscribe_param ) );

		if ( empty( $unsubscribe_info ) ) {
			return;
		}

		$wpdb->insert( $wpdb->gpns_unsubscribes, array(
			'email'           => $unsubscribe_info['email'],
			'form_id'         => $unsubscribe_info['fid'],
			'notification_id' => $unsubscribe_info['nid'],
			'scope'           => $unsubscribe_info['scope'],
			'timestamp_gmt'   => current_time( 'mysql', true ),
		) );

		// If specified in 'gpns_unsubscribe_url_args', redirect to URL rather than using wp_die().
		if ( rgar( $unsubscribe_info, 'url' ) ) {
			wp_safe_redirect( add_query_arg( array(
				'gpns_unsubscribe' => rgget( 'gpns_unsubscribe' ),
			), rgar( $unsubscribe_info, 'url' ) ) );

			return;
		}

		/**
		 * Filter the unsubscribe confirmation message and title. Note, this will not be used if setting `url` in the gpns_unsubscribe_url_args filter.
		 *
		 * @param array $unsubscribe_confirmation {
		 *     The contents of the unsubscribe confirmation.
		 *
		 *     @type string $title   The unsubscribe confirmation title.
		 *     @type string $message   The unsubscribe confirmation message.
		 * }
		 * @param int $form_id The form ID.
		 * @param string $nid The notification ID.
		 * @param string $scope The scope of the unsubscribe action. Can be `all`, `form_id`, or `nid`
		 *
		 * @since 1.1
		 */
		$unsubscribe_confirmation = apply_filters( 'gpns_unsubscribe_confirmation', array(
			// translators: placeholder is the site name
			'title'   => sprintf( esc_html__( 'Unsubscribe Successful &ndash; %s', 'gp-notification-scheduler' ), get_option( 'blogname' ) ),
			'message' => esc_html__( 'You have been successfully unsubscribed.', 'gp-notification-scheduler' ),
		), $unsubscribe_info['fid'], $unsubscribe_info['nid'], $unsubscribe_info['scope'] );

		wp_die( $unsubscribe_confirmation['message'], $unsubscribe_confirmation['title'], array(
			'response' => 200,
		) );
	}

	/**
	 * Add Notification Scheduler merge tags.
	 *
	 * @since  1.1
	 *
	 * @param array $form The form object.
	 *
	 * @return array
	 */
	public function add_merge_tags( $form ) {
		// If the header has already been output, add merge tags script in the footer.
		if ( ! did_action( 'admin_head' ) ) {
			add_action( 'admin_footer', array( $this, 'add_merge_tags_footer' ) );

			return $form;
		}

		?>

		<script type="text/javascript">

			( function ( $ ) {

				if ( window.gform ) {

					gform.addFilter( 'gform_merge_tags', function ( mergeTags ) {

						mergeTags[ 'gp_notification_scheduler' ] = {
							label: '<?php _e( 'Notification Scheduler', 'gp-notification-scheduler' ); ?>',
							tags:  [
								{
									tag:   '{unsubscribe_link}',
									label: '<?php _e( 'Unsubscribe Link', 'gp-notification-scheduler' ); ?>'
								},
								{
									tag:   '{unsubscribe_url}',
									label: '<?php _e( 'Unsubscribe URL', 'gp-notification-scheduler' ); ?>'
								}
							]
						};

						return mergeTags;

					} );

				}

			} )( jQuery );

		</script>

		<?php
		return $form;

	}

	/**
	 * Add Notification Scheduler merge tags in admin footer.
	 *
	 * @since  1.1
	 */
	public function add_merge_tags_footer() {
		$form = $this->get_current_form();

		if ( $form ) {
			$this->add_merge_tags( $form );
		}
	}

	/**
	 * Rebuild notification queue for entries.
	 *
	 * @param $entry
	 * @param $form
	 *
	 * @since 0.9.11
	 */
	public function rebuild_notification_queue( $entry, $form ) {

		$this->delete_notification_queue( array(), $entry );

		$queue = array();

		foreach ( rgar( $form, 'notifications', array() ) as $notification ) {
			if ( rgar( $notification, 'scheduleType', 'immediate' ) === 'immediate' || rgar( $notification, 'isActive' ) === false ) {
				continue;
			}

			// Decision: evaluate conditional logic here *before* adding to the queue; not when sending from queue.
			//
			// When Notifications are scheduled normally (not when being rebuilt), the conditional logic is handled by
			// the notification itself as we intercept the notification right before it is being mailed.
			if ( ! GFCommon::evaluate_conditional_logic( rgar( $notification, 'conditionalLogic' ), $form, $entry ) ) {
				continue;
			}

			$timestamp = $this->get_schedule_timestamp( $notification, $entry );
			if ( $timestamp && $timestamp > $this->get_current_time() ) {
				$queue[] = array(
					'nid'       => $notification['id'],
					'timestamp' => $timestamp,
				);
			}
		}

		if ( empty( $queue ) ) {
			return;
		}

		/**
		 * @todo This does not take into account whether or not the notification fired.
		 *       See comment in the foreach of update_notification_queue_after_entry_update.
		 */
		$this->add_to_notification_queue( $entry['id'], $entry['form_id'], $queue );

	}

	/**
	 * Delete notifications from the notification queue.
	 *
	 * Pass an $entry to delete all scheduled notifications for that entry (i.e. if an entry is deleted).
	 * Pass specific $notifications to remove those notifications from the queue for all entries (i.e. if a notification template is deleted).
	 * Pass an $entry and specific $notifications to remove those notifications from the queue for the specified entry (i.e. the scheduled notification is manually canceled).
	 *
	 * @param array $notifications
	 * @param array $entry
	 *
	 * @return bool
	 */
	public function delete_notification_queue( $notifications = array(), $entry = false ) {
		global $wpdb;

		if ( ! $entry && ! empty( $notifications ) ) {
			return false;
		}

		$query = array(
			'delete' => "DELETE FROM {$wpdb->prefix}{$this->get_entry_meta_table()}",
		);

		$where = array();

		if ( $entry !== false ) {
			$entry_id_column_name = $this->is_new_db() ? 'entry_id' : 'lead_id';

			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$where[] = $wpdb->prepare( "{$entry_id_column_name} = %d", $entry['id'] );
		}

		if ( ! empty( $notifications ) ) {
			$notification_ids = wp_list_pluck( $notifications, 'nid' );
			$where[]          = 'meta_key IN( ' . implode( ', ', $notification_ids ) . ')';
		} else {
			$where[] = 'meta_key like "gpns_schedule_%"';
		}

		$query['where'] = sprintf( 'WHERE %s', implode( "\n AND ", $where ) );

		$sql = implode( "\n", $query );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$result = $wpdb->query( $sql );

		return (bool) $result;
	}

	public function add_recurring_notifications( $notification_ids, $entry, $form ) {

		$queue = array();

		foreach ( $notification_ids as $notification_id ) {
			$notification = $form['notifications'][ $notification_id ];

			if ( ! rgar( $notification, 'scheduleEnableRecurring' ) ) {
				continue;
			}

			$ending = $this->get_schedule_recurring_ending( $entry, $notification );
			if ( $ending && $this->get_current_time() > $ending ) {
				continue;
			}

			$timestamp = $this->get_schedule_timestamp( $notification, $entry, true );
			if ( $timestamp && $timestamp > $this->get_current_time() ) {
				$queue[] = array(
					'nid'       => $notification['id'],
					'timestamp' => $timestamp,
				);
			}
		}

		$this->add_to_notification_queue( $entry['id'], $entry['form_id'], $queue );

	}

	public function get_schedule_recurring_ending( $entry, $notification ) {
		if ( $notification['scheduleRecurringEnding'] !== 'after' ) {
			return null;
		}

		$origination = strtotime( $this->get_entry_created_date( $entry ) ) + $this->get_offset_in_seconds( $notification['scheduleRecurringEndingValue'], $notification['scheduleRecurringEndingUnit'] );

		return $origination;
	}

	public function get_entry_meta_table() {
		return $this->is_new_db() ? 'gf_entry_meta' : 'rg_lead_meta';
	}

	public function is_new_db() {
		return version_compare( $this->get_gf_db_version(), '2.3', '>=' );
	}

	public function get_gf_db_version() {
		return method_exists( 'GFFormsModel', 'get_database_version' ) ? GFFormsModel::get_database_version() : GFForms::$version;
	}

	/**
	 * Update notification queue when a Date-field-dependency is updated.
	 *
	 * @param array $form           The current form.
	 * @param int   $entry_id       The entry ID.
	 * @param array $original_entry The entry before it was updated.
	 *
	 * @return void
	 */
	public function update_notification_queue_after_entry_update( $form, $entry_id, $original_entry ) {
		$entry       = GFAPI::get_entry( $entry_id );
		$date_fields = GFAPI::get_fields_by_type( $form, 'date' );

		if ( empty( $date_fields ) ) {
			return;
		}

		// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
		if ( $entry == $original_entry ) {
			return;
		}

		$date_field_ids = wp_list_pluck( $date_fields, 'id' );
		$queue          = $this->get_notification_queue( $entry );

		foreach ( $form['notifications'] as $notification ) {
			// Only need to update scheduled notifications that are field-based.
			if ( rgar( $notification, 'scheduleType', 'immediate' ) === 'immediate' || ! in_array( (int) $notification['scheduleField'], $date_field_ids, true ) ) {
				continue;
			}

			/**
			 * @todo In a future iteration (heh) of this, we should keep track of what events have fired for a particular notification. Then, with that we can
			 *       delete/add notifications safely rather than only rescheduling notifications that are already scheduled.
			 */
			// Find scheduled notification for this field reschedule it
			foreach ( $queue as $scheduled_notification ) {
				if ( $scheduled_notification['nid'] === $notification['id'] ) {
					$this->reschedule_notification( $entry['id'], $scheduled_notification['nid'], $this->get_schedule_timestamp( $notification, $entry ) );
				}
			}
		}
	}

	/**
	 * Update notification queue when a Date-field-dependency is updated. This callback is for the
	 * gform_post_update_entry action which fires when entries are updated using the API.
	 *
	 * @param array $entry           The entry
	 * @param array $original_entry The entry before it was updated.
	 *
	 * @return void
	 */

	public function update_notification_queue_post_update_entry( $entry, $original_entry ) {
		$form = GFAPI::get_form( $entry['form_id'] );
		$this->update_notification_queue_after_entry_update( $form, $entry['id'], $original_entry );
	}

	/**
	 * Returns the schedule timestamp (UTC) calculated from the schedule settings.
	 *
	 * Thanks, Gravity Flow!
	 *
	 * @return int
	 */
	public function get_schedule_timestamp( $notification, $entry, $is_recurring = false, $current_time = false ) {

		$schedule_datetime = false;

		if ( $is_recurring ) {

			// @todo Update $current_time to a date closer to the current date based on what $noun is seleccted.
			// Otherwise, we might iterate through this loop an insane amount of times.
			$schedule_datetime = $current_time ? $current_time : $this->get_current_time();
			$nouns             = array(
				'daily'   => 'day',
				'weekly'  => 'week',
				'monthly' => 'month',
				'yearly'  => 'year',
			);
			$noun              = rgar( $nouns, $notification['scheduleRecurringInterval'], 'year' );
			$schedule_datetime = strtotime( "+1 {$noun}", $schedule_datetime );

			/**
			 * See documentation for this filter at the bottom of this method.
			 */
			return gf_apply_filters( array( 'gpns_schedule_timestamp', $entry['form_id'] ), $schedule_datetime, $notification, $entry, $is_recurring, $current_time );
		}

		switch ( $notification['scheduleType'] ) {

			case 'date':
				$time_string       = sprintf( '%s %s:%s%s', $notification['scheduleDate'], $notification['scheduleHour'], str_pad( $notification['scheduleMinute'], 2, '0', STR_PAD_LEFT ), $notification['scheduleAmpm'] );
				$schedule_datetime = strtotime( $time_string );
				// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
				$schedule_date     = date( 'Y-m-d H:i:s', $schedule_datetime );
				$schedule_date_gmt = get_gmt_from_date( $schedule_date );
				$schedule_datetime = strtotime( $schedule_date_gmt );

				break;

			case 'field':
				$field_id = $notification['scheduleField'];

				if ( ! is_numeric( $field_id ) ) {

					$form  = GFAPI::get_form( $entry['form_id'] );
					$group = $this->get_group_by_slug( $this->get_date_groups( $form ), $field_id );

					if ( ! $group ) {
						return false;
					}

					foreach ( $group['fields'] as $_field_id ) {

						$date     = $entry[ (string) $_field_id ];
						$datetime = strtotime( $date );

						// find soonest date in group that is greater than today
						if ( $datetime > $this->get_current_time() && ( ! $schedule_datetime || $datetime < $schedule_datetime ) ) {
							$schedule_datetime = $datetime;
						}
					}
				} else {

					$schedule_date = $entry[ (string) $notification['scheduleField'] ];
					if ( ! $schedule_date ) {
						break;
					}

					$schedule_datetime = strtotime( $schedule_date );

				}

				// phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
				$schedule_date_gmt = get_gmt_from_date( date( 'Y-m-d H:i:s', $schedule_datetime ) );
				$schedule_datetime = strtotime( $schedule_date_gmt );

				$offset = $this->get_offset_in_seconds( $notification['scheduleFieldOffset'], $notification['scheduleFieldOffsetUnit'] );

				if ( $notification['scheduleFieldTiming'] === 'before' ) {
					$schedule_datetime -= $offset;
				} else {
					$schedule_datetime += $offset;
				}

				break;

			case 'delay':
				$schedule_datetime  = strtotime( $this->get_entry_created_date( $entry ) );
				$schedule_datetime += $this->get_offset_in_seconds( $notification['scheduleDelayOffset'], $notification['scheduleDelayOffsetUnit'] );

				break;
		}

		// If timestamp is in the past - and - this is a recurring notification; get next valid schedule time.
		if ( $schedule_datetime && $schedule_datetime < $this->get_current_time() && rgar( $notification, 'scheduleEnableRecurring' ) ) {
			while ( $schedule_datetime < $this->get_current_time() ) {
				$schedule_datetime = $this->get_schedule_timestamp( $notification, $entry, true, $schedule_datetime );
			}
		}

		/**
		 * Filter the timestamp (Unix epoch) used to schedule a notification
		 *
		 * @param int       $schedule_datetime  Unix epoch (seconds) that will be used as the time of when the
		 *   notification should send.
		 * @param array     $notification       The notification.
		 * @param array     $entry              The current entry.
		 * @param boolean   $is_recurring       Whether the notification is being scheduled as a recurring
		 *   notification.
		 * @param int       $current_time       The current server Unix epoch timestamp.
		 *
		 * @since 1.0.2
		 */
		return gf_apply_filters( array( 'gpns_schedule_timestamp', $entry['form_id'] ), $schedule_datetime, $notification, $entry, $is_recurring, $current_time );
	}

	public function get_schedule_ending( $notification, $entry ) {

		switch ( $notification['scheduleType'] ) {

			case 'date':
				$time_string       = sprintf( '%s %s:%s%s', $notification['scheduleDate'], $notification['scheduleHour'], str_pad( $notification['scheduleMinute'], 2, '0', STR_PAD_LEFT ), $notification['scheduleAmpm'] );
				$schedule_datetime = strtotime( $time_string );
				$schedule_date_gmt = gmdate( 'Y-m-d H:i:s', $schedule_datetime );
				$schedule_datetime = strtotime( $schedule_date_gmt );

				break;

			case 'field':
				$field_id = $notification['scheduleField'];

				if ( ! is_numeric( $field_id ) ) {

					$form  = GFAPI::get_form( $entry['form_id'] );
					$group = $this->get_group_by_slug( $this->get_date_groups( $form ), $field_id );

					if ( ! $group ) {
						return false;
					}

					foreach ( $group['fields'] as $_field_id ) {

						$date     = $entry[ (string) $_field_id ];
						$datetime = strtotime( $date );

						// find soonest date in group that is greater than today
						if ( $datetime > $this->get_current_time() && ( ! $schedule_datetime || $datetime < $schedule_datetime ) ) {
							$schedule_datetime = $datetime;
						}
					}
				} else {

					$schedule_date     = $entry[ (string) $notification['scheduleField'] ];
					$schedule_datetime = strtotime( $schedule_date );

				}

				$schedule_date_gmt = gmdate( 'Y-m-d H:i:s', $schedule_datetime );
				$schedule_datetime = strtotime( $schedule_date_gmt );

				$offset = $this->get_offset_in_seconds( $notification['scheduleFieldOffset'], $notification['scheduleFieldOffsetUnit'] );

				if ( $notification['scheduleFieldTiming'] === 'before' ) {
					$schedule_datetime -= $offset;
				} else {
					$schedule_datetime += $offset;
				}

				break;

			case 'delay':
				$schedule_datetime  = strtotime( $this->get_entry_created_date( $entry ) );
				$schedule_datetime += $this->get_offset_in_seconds( $notification['scheduleDelayOffset'], $notification['scheduleDelayOffsetUnit'] );

				break;
		}

		return $schedule_datetime;

	}

	public function get_offset_in_seconds( $offset, $unit ) {

		$seconds = 0;

		switch ( $unit ) {
			case 'minutes':
				$seconds = ( MINUTE_IN_SECONDS * $offset );
				break;
			case 'hours':
				$seconds = ( HOUR_IN_SECONDS * $offset );
				break;
			case 'days':
			case 'daily':
				$seconds = ( DAY_IN_SECONDS * $offset );
				break;
			case 'weeks':
			case 'weekly':
				$seconds = ( WEEK_IN_SECONDS * $offset );
				break;
			case 'months':
			case 'monthly':
				$seconds = ( MONTH_IN_SECONDS * $offset );
				break;
			case 'yearly':
				$seconds = ( YEAR_IN_SECONDS * $offset );
				break;
		}

		return $seconds;
	}

	public function get_notification( $form, $notification_id ) {

		foreach ( $form['notifications'] as $id => $notification ) {
			if ( $id === $notification_id ) {
				return $notification;
			}
		}

		return array();
	}

	public function register_entry_scheduled_notifications_meta_box( $meta_boxes, $entry, $form ) {
		if ( $this->has_scheduled_notification_configured( $form ) ) {
			$meta_boxes['scheduled_notifications'] = array(
				'title'         => esc_html__( 'Scheduled Notifications', 'gp-notification-scheduler' ),
				'callback'      => array( $this, 'entry_scheduled_notifications_meta_box' ),
				'context'       => 'normal',
				'callback_args' => array( $entry, $form ),
			);
		}

		return $meta_boxes;
	}

	public function entry_scheduled_notifications_meta_box( $args ) {

		require_once( $this->get_base_path() . '/includes/class-gp-scheduled-notifications-list-table.php' );

		$table = new GP_Scheduled_Notifications_List_Table( array(
			'form'  => $args['form'],
			'entry' => $args['entry'],
		) );

		$table->prepare_items();
		$table->display();

	}

	public function has_scheduled_notification_configured( $form ) {
		foreach ( $form['notifications'] as $notification ) {
			if ( rgar( $notification, 'scheduleType', 'immediate' ) !== 'immediate' ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the full notification queue across all entries or pass an entry to get the notification queue for that entry.
	 *
	 * @param bool $entry
	 *
	 * @return array
	 */
	public function get_notification_queue( $entry = false, $overdue = false ) {
		global $wpdb;

		$entry_id_column_name = $this->is_new_db() ? 'entry_id' : 'lead_id';
		$entry_table          = $this->is_new_db() ? 'gf_entry' : 'rg_lead';

		$query = array(
			'select' => "SELECT meta.id, {$entry_id_column_name} AS entry_id, SUBSTRING_INDEX( meta_key, '_', -1 ) AS `nid`, meta_value AS `timestamp`",
			'from'   => "FROM {$wpdb->prefix}{$this->get_entry_meta_table()} meta, {$wpdb->prefix}{$entry_table} entry",
			'where'  => '',
			'order'  => 'ORDER BY meta_value ASC',
		);

		$where = array(
			'meta.entry_id = entry.id',
			'entry.status != "trash"',
			'meta_key like "gpns_schedule_%"',
		);

		// Only retrieve notifications for the passed $entry.
		if ( $entry !== false ) {
			$where[] = sprintf( "{$entry_id_column_name} = %d", $entry['id'] );
		}

		// Only retrieve notifications that are ready to be sent.
		if ( $overdue ) {
			$where[] = sprintf( 'meta_value <= %d', $this->get_current_time() );
		}

		$query['where'] = sprintf( 'WHERE %s', implode( "\nAND ", $where ) );

		$sql = implode( "\n", $query );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$queue = $wpdb->get_results( $sql, ARRAY_A );

		return $queue;
	}

	public function add_to_notification_queue( $entry_id, $form_id, $notifications ) {
		/**
		 * @todo is there a way to batch this into one query?
		 */
		foreach ( $notifications as $notification ) {
			gform_add_meta( $entry_id, "gpns_schedule_{$notification['nid']}", $notification['timestamp'], $form_id );
		}
	}

	public function reschedule_notification( $entry_id, $nid, $new_timestamp ) {
		global $wpdb;

		$entry_id_column_name = $this->is_new_db() ? 'entry_id' : 'lead_id';
		$table_name           = $wpdb->prefix . $this->get_entry_meta_table();

		$wpdb->update( $table_name, array( 'meta_value' => $new_timestamp ), array(
			$entry_id_column_name => $entry_id,
			'meta_key'            => "gpns_schedule_{$nid}",
		) );
	}

	public function delete_notification_queue_by_notification_id( $notification_ids, $entry_id ) {
		if ( ! is_array( $notification_ids ) ) {
			$notification_ids = array( $notification_ids );
		}
		foreach ( $notification_ids as $notification_id ) {
			$this->log( 'Deleting from notification queue using ID: ' . $notification_id );
			gform_delete_meta( $entry_id, 'gpns_schedule_' . $notification_id );
		}
	}

	public function delete_notification_queue_by_meta_id( $ids ) {
		global $wpdb;

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}

		$this->log( 'Deleting from notification queue with meta IDs: ' . implode( ', ', $ids ) );

		$sql = "DELETE FROM {$wpdb->prefix}{$this->get_entry_meta_table()} WHERE id IN( " . implode( ', ', $ids ) . ' )';

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$result = $wpdb->query( $sql );

		return $result;
	}

	public function initialize_rebuild_batcher() {
		require_once( $this->get_base_path() . '/includes/class-gwiz-batcher.php' );

		new \GP_Notification_Scheduler\GWiz_Batcher( array(
			'title'              => 'GPNS Updater',
			'id'                 => 'gpns-updater',
			'size'               => 100,
			'show_form_selector' => true,
			'get_items'          => function ( $size, $offset, $form_id = null ) {
				global $wpdb;

				$entry_table = GFFormsModel::get_entry_table_name();
				$total = rgpost( 'total' );

				if ( $form_id ) {
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					$entry_ids = $wpdb->get_col( $wpdb->prepare( "select id from {$entry_table} WHERE form_id = %d limit %d offset %d", $form_id, $size, $offset ) );

					if ( ! $total ) {
						// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
						$total = $wpdb->get_var( $wpdb->prepare( "select count( id ) from {$entry_table} WHERE form_id = %d", $form_id ) );
					}
				} else {
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					$entry_ids = $wpdb->get_col( $wpdb->prepare( "select id from {$entry_table} limit %d offset %d", $size, $offset ) );

					if ( ! $total ) {
						// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
						$total = $wpdb->get_var( "select count( id ) from {$entry_table}" );
					}
				}

				$entries = array();
				foreach ( $entry_ids as $entry_id ) {
					$entries[] = GFAPI::get_entry( $entry_id );
				}

				$this->log( sprintf( 'Rebuilding notifications for the following entry IDs: %s', print_r( $entry_ids, true ) ) );
				$this->log( sprintf( 'Total number of entries to rebuild for: %d', $total ) );

				return array(
					'items' => $entries,
					'total' => $total,
				);
			},
			'process_item'       => function ( $entry ) {
				$form = GFAPI::get_form( $entry['form_id'] );

				$this->log( sprintf( 'Rebuilding notification for entry: %s', print_r( $entry, true ) ) );

				gp_notification_schedule()->rebuild_notification_queue( $entry, $form );
			},
			'on_finish'          => function( $count, $total ) {
				$this->log( sprintf( 'Finished rebuilding notifications for %d of %d entries.', $count, $total ) );
			},
		) );
	}

	/**
	 * Return the plugin's icon for the plugin/form settings menu.
	 *
	 * @return string
	 */
	public function get_menu_icon() {
		return 'dashicons-email-alt';
	}

	public function plugin_settings_init() {
		parent::plugin_settings_init();
		GFSettings::$addon_pages[ $this->_slug ]['tab_label'] = __( 'Notif Scheduler', 'gp-notification-scheduler' );
	}

	/**
	 * Configures the settings which should be rendered on the add-on settings tab.
	 *
	 * @return array
	 */
	public function plugin_settings_fields() {
		$stop = true;
		return array(
			array(
				'title'         => esc_html__( 'Notification Scheduler', 'gp-notification-scheduler' ),
				'save_callback' => function () {
					var_dump( func_get_args() );
				},
				'description'   => esc_html__( 'Manage emails for Notification Scheduler.', 'gp-notification-scheduler' ),
				'fields'        => array(
					array(
						'name'                => 'email',
						'tooltip'             => esc_html__( 'Enter an email address to either unsubscribe or subscribe.', 'gp-notification-scheduler' ),
						'label'               => esc_html__( 'Email Address', 'gp-notification-scheduler' ),
						'type'                => 'text',
						'input_type'          => 'email',
						'class'               => 'medium',
						'validation_callback' => function ( $field, $value ) {
							global $wpdb;

							// Get action
							$action = $this->get_settings_renderer()->get_value( 'email_action' );

							if ( ! GFCommon::is_valid_email( $value ) ) {
								$field->set_error( __( 'Please enter a valid email.', 'gp-notification-scheduler' ) );
								return;
							}

							if ( ! $action ) {
								$field->set_error( __( 'Please select an action for the email.', 'gp-notification-scheduler' ) );
								return;
							}

							$is_unsubscribed = 0 < (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->gpns_unsubscribes} WHERE `email` = %s", $value ) );

							if ( ! $is_unsubscribed && $action === 'resubscribe' ) {
								$field->set_error( __( 'The email address was not found in the unsubscribe list.', 'gp-notification-scheduler' ) );
							}
						},
					),
					array(
						'name'          => 'email_action',
						'label'         => esc_html__( 'Action', 'gp-notification-scheduler' ),
						'type'          => 'radio',
						'default_value' => 'unsubscribe',
						'choices'       => array(
							array(
								'label'   => esc_html__( 'Unsubscribe email', 'gp-notification-scheduler' ),
								'value'   => 'unsubscribe',
								'tooltip' => esc_html__( 'When unsubscribed, the email will no longer receive any scheduled notifications.', 'gp-notification-scheduler' ),
							),
							array(
								'label'   => esc_html__( 'Resubscribe email', 'gp-notification-scheduler' ),
								'value'   => 'resubscribe',
								'tooltip' => esc_html__( 'When resubscribed, the email will be able to receive scheduled notifications again. Previously scheduled notifications will not be automatically rescheduled.', 'gp-notification-scheduler' ),
							),
						),
					),
					array(
						'id'       => 'save_button',
						'type'     => 'save',
						'value'    => esc_attr__( 'Update Email Preferences', 'gp-notification-scheduler' ),
						'messages' => array(
							'save'  => rgpost( '_gform_setting_email_action' ) === 'unsubscribe' ? esc_html__( 'Email unsubscribed successfully.', 'gp-notification-scheduler' ) : esc_html__( 'Email resubscribed successfully.', 'gp-notification-scheduler' ),
							'error' => rgpost( '_gform_setting_email_action' ) === 'unsubscribe' ? esc_html__( 'There was an error unsubscribing the email.', 'gp-notification-scheduler' ) : esc_html__( 'There was an error resubscribing the email.', 'gp-notification-scheduler' ),
						),
					),
				),
			),
		);
	}

	/**
	 * Updates plugin settings with the provided settings
	 *
	 * @param array $settings Plugin settings to be saved.
	 */
	public function update_plugin_settings( $settings ) {
		global $wpdb;

		$action = rgar( $settings, 'email_action' );

		if ( $action === 'unsubscribe' ) {
			$wpdb->insert( $wpdb->gpns_unsubscribes, array(
				'email'         => $settings['email'],
				'scope'         => 'all',
				'timestamp_gmt' => current_time( 'mysql', true ),
			) );
		} elseif ( $action === 'resubscribe' ) {
			$wpdb->delete( $wpdb->gpns_unsubscribes, array(
				'email' => $settings['email'],
			) );
		}

		// Do not save settings.
		return;
	}

	/**
	 * Get the date an entry was created. This method is needed as date_created isn't always available for
	 * entries such as Partial Entries. If that's the case, we fallback to the current timestamp.
	 *
	 * @param array $entry
	 * @return string The time the entry was created.
	 */
	public function get_entry_created_date( $entry ) {
		global $wpdb;

		if ( empty( $entry['date_created'] ) ) {
			return $wpdb->get_var( 'SELECT utc_timestamp()' );
		}

		return $entry['date_created'];
	}

	public function get_current_time() {
		/**
		 * Filter to change what time is used for the cutoff when getting notifications that are due for sending.
		 * The primary use for this is for testing.
		 *
		 * @since 1.0
		 *
		 * @param int $time The current time in epoch seconds.
		 */
		return apply_filters( 'gpns_current_time', time() );
	}

	/**
	 * Overrides the current time used for determining which notifications are overdue during test runs.
	 *
	 * @param int $current_time
	 *
	 * @return int
	 */
	public function testing_current_time_override( $current_time ) {
		$override = get_option( 'gwiz_cypress_gpns_current_time_override' );

		if ( $override ) {
			return $override;
		}

		return $current_time;
	}

}

function gp_notification_schedule() {
	return GP_Notification_Scheduler::get_instance();
}

GFAddOn::register( 'GP_Notification_Scheduler' );
