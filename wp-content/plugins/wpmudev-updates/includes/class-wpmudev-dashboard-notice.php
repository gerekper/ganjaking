<?php
/**
 * Notification module.
 * All logic to enqueue, display and handle notifications are collected in this
 * dashboard module.
 *
 * @since  4.0.0
 * @package WPMUDEV_Dashboard
 */

/**
 * The notification class.
 */
class WPMUDEV_Dashboard_Message {

	/**
	 * Max number of notices that are stored in the message-queue.
	 * If more messages are added then the oldest ones are removed.
	 */
	const MAX_QUEUE_COUNT = 1;

	/**
	 * The message-queue contains recently scheduled messages.
	 *
	 * Also messages that were dismissed already are listed here; the queue
	 * hold up to 20 messages, then the oldest ones are removed.
	 *
	 * @see MAX_QUEUE_COUNT
	 * @var array
	 */
	protected $queue = array();

	/**
	 * This is used to override the notice with a special message.
	 *
	 * A special action is provided to define a value for this property.
	 *
	 * @var bool
	 */
	protected $the_notice = false;

	/**
	 * Set up the Notice module. This adds all the initial hooks for the plugin
	 *
	 * @since 4.0.0
	 * @internal
	 */
	public function __construct() {
		// Notifications are completely disabled while logged out.
		if ( ! WPMUDEV_Dashboard::$api->has_key() ) {
			return;
		}

		add_action(
			'load-index.php',
			array( $this, 'maybe_setup_message' )
		);

		add_action(
			'wp_ajax_wdev_notice_dismiss',
			array( $this, 'ajax_dismiss' )
		);

		/*
		 * Scope of this action:
		 *   after plugins_loaded
		 *   before/in admin_init
		 */
		add_action(
			'wpmudev_override_notice',
			array( $this, 'override_message' ), 10, 2
		);

		// Rarely used. It's a hardcoded message like "plugins updated".
		if ( ! empty( $_GET['wpmudev_msg'] ) ) {
			// Used on all NON-Dashboard pages.
			add_action(
				'all_admin_notices',
				array( $this, 'setup_global_notice' ),
				999
			);

			//Used on all WPMU DEV Dashboard pages.
			add_filter(
				'wpmudev-admin-notice',
				array( $this, 'get_global_message' )
			);
		}

		/**
		 * Run custom initialization code for the Notice module.
		 *
		 * @since 4.0.0
		 * @var   WPMUDEV_Dashboard_Message The dashboards Notice module.
		 */
		do_action( 'wpmudev_dashboard_notice_init', $this );
	}

	/*
	 * *********************************************************************** *
	 * *     HANDLE MESSAGE QUEUE
	 * *********************************************************************** *
	 */

	/**
	 * Load the message queue from database.
	 *
	 * @since  4.0.0
	 */
	protected function load_queue() {
		static $Queue_Loaded = false;
		$changed = false;

		if ( $Queue_Loaded ) { return; }
		$Queue_Loaded = true;

		$this->queue = WPMUDEV_Dashboard::$site->get_option( 'notifications' );
		if ( ! is_array( $this->queue ) ) {
			$this->queue = array();
			$changed = true;
		}

		foreach ( $this->queue as $id => $msg ) {
			if ( is_object( $msg ) ) {
				$msg = (array) $msg;
				$changed = true;
			}
			if ( ! is_array( $msg ) ) {
				unset( $this->queue[ $id ] );
				$changed = true;
				continue;
			}
			if ( empty( $msg['content'] ) ) {
				unset( $this->queue[ $id ] );
				$changed = true;
				continue;
			}
			if ( ! isset( $msg['cta'] ) ) {
				$msg['cta'] = '';
				$changed = true;
			}
			if ( ! isset( $msg['id'] ) ) {
				$msg['id'] = intval( $id );
				$changed = true;
			}
			if ( ! isset( $msg['dismissed'] ) ) {
				$msg['dismissed'] = false;
				$changed = true;
			}
			if ( ! isset( $msg['time_create'] ) ) {
				$msg['time_create'] = time();
				$changed = true;
			}
			$this->queue[ $id ] = $msg;
		}

		if ( $changed ) {
			$this->save_queue();
		}
	}

	/**
	 * Saves the message queue to the database.
	 *
	 * @since  4.0.0
	 */
	public function save_queue() {
		$this->load_queue();

		// Sort the queue; this moves old notices to the END.
		krsort( $this->queue, SORT_NUMERIC );

		// Remove old messages if the queue is too long.
		while ( count( $this->queue ) > self::MAX_QUEUE_COUNT ) {
			array_pop( $this->queue );
		}

		// Save the queue to database.
		WPMUDEV_Dashboard::$site->set_option( 'notifications', $this->queue );
	}

	/**
	 * Enqueue a message.
	 *
	 * Each message-ID is only enqueued once, so it is save to enqueue the
	 * same message multiple times without worring that it's displayed too
	 * often.
	 *
	 * @since  4.0.0
	 * @param  int    $id The message ID.
	 * @param  string $content The HTML content of the message.
	 * @param  bool   $can_dismiss Show the Dismiss button or not.
	 */
	public function enqueue( $id, $content, $can_dismiss = true ) {
		// Notifications are completely disabled while logged out.
		if ( ! WPMUDEV_Dashboard::$api->has_key() ) {
			return false;
		}

		$this->load_queue();
		$id = intval( $id );

		if ( empty( $id ) ) {
			return false;
		}

		if ( isset( $this->queue[ $id ] ) ) {
			return false;
		}

		$notice = array(
			'id' => $id,
			'content' => $content,
			'dismissed' => false,
			'can_dismiss' => $can_dismiss,
			'cta' => '',
			'time_create' => time(),
		);

		$this->queue[ $id ] = $notice;

		$this->save_queue();
		return true;
	}

	/**
	 * Used by Support staff to analyze issues with the message-queue.
	 *
	 * @since  4.0.3
	 * @return string HTML representation of the current message-queue.
	 */
	public function dump_queue() {
		$this->load_queue();

		$dump = '
		<table class="list-table">
		<thead><tr>
			<th width="150"><div class="tc">Created</div></th>
			<th width="150"><div class="tc">Dismissed</div></th>
			<th>Message</th>
		</tr></thead>
		<tbody>';

		foreach ( $this->queue as $id => $item ) {
			$created = '?';
			if ( $item['time_create'] ) {
				$created = date( 'Y-m-d H:i', $item['time_create'] );
			} elseif ( is_numeric( $id ) && $id > 100000 ) {
				$created = date( 'Y-m-d H:i', $id );
			}
			$dismissed = '?';
			if ( $item['dismissed'] ) {
				$dismissed = 'Yes';
			} else {
				$dismissed = 'No';
			}

			$dump .= sprintf(
				'<tr class="notice-%s">
				<td class="tc">%s<br /><span tooltip="%s"><i class="dev-icon dev-icon-info"></i></span></td>
				<td class="tc">%s</td>
				<td>%s</td>
				</tr>',
				esc_html( $item['id'] ),
				esc_html( $created ),
				esc_html( 'ID: ' . $item['id'] ),
				esc_html( $dismissed ),
				esc_html( $item['content'] )
			);
		}

		$dump .= '</tbody></table>';

		return $dump;
	}

	/**
	 * Define a custom message to be displayed on the dashboard.
	 * This message is not saved to the queue, so it does not have a state
	 * for `dismiss` either - it's always un-dismissed.
	 *
	 * @since  4.0.0
	 * @param  string $content The HTML content of the message.
	 * @param  string $cta Type/code of the CTA button.
	 */
	public function override_message( $content, $cta = 'dismiss' ) {
		// Notifications are completely disabled while logged out.
		if ( ! WPMUDEV_Dashboard::$api->has_key() ) {
			return;
		}

		$can_dismiss = true;
		if ( 'dismiss' == $cta ) {
			$cta = '';
		} else {
			$can_dismiss = false;
		}

		$this->the_notice = array(
			'id' => 0,
			'content' => $content,
			'dismissed' => false,
			'can_dismiss' => $can_dismiss,
			'cta' => $cta,
		);
	}

	/**
	 * Moves a message from the queue to the done list.
	 *
	 * @since  4.0.0
	 * @param  string $msg_id Message ID.
	 */
	protected function mark_as_done( $msg_id ) {
		$this->load_queue();

		if ( isset( $this->queue[ $msg_id ] ) ) {
			$this->queue[ $msg_id ]['dismissed'] = true;
			$this->save_queue();
		}
	}

	/*
	 * *********************************************************************** *
	 * *     AJAX HANDLER FUNCTIONS
	 * *********************************************************************** *
	 */

	/**
	 * Ajax handler that marks a enqueued message as "dismissed".
	 *
	 * @since  4.0.0
	 */
	public function ajax_dismiss() {
		$msg_id = intval( $_POST['msg_id'] );

		if ( ! empty( $msg_id ) ) {
			$this->mark_as_done( $msg_id );
			wp_send_json_success();
		}

		wp_send_json_error();
	}

	/*
	 * *********************************************************************** *
	 * *     DISPLAY THE MESSAGE
	 * *********************************************************************** *
	 */

	/**
	 * This function is only called on pages that can display a WPMUDEV message.
	 * By design the only page for this is the main WordPress Dashboard page
	 *
	 * @since  4.0.0
	 */
	public function maybe_setup_message() {
		// Initialize the WPMUDEV message only for authorized admins.
		if ( WPMUDEV_Dashboard::$site->allowed_user() ) {
			add_action( 'all_admin_notices', array( $this, 'setup_message' ), 999 );
		}
	}

	/**
	 * Choose a message to display and render it.
	 *
	 * This function is only called when those two conditions are true:
	 * 1. We display the main WP Dashboard page
	 * 2. Current user has access to the WPMUDEV Dashboard plugin
	 *
	 * @since  4.0.0
	 */
	public function setup_message() {

		//message details.
		$msg 	= $this->choose_message();

		if ( ! $msg ) { return; }

		//flag to show notice
		$show_notice = apply_filters( 'wpmudev_show_notice', true, $msg );
		if ( ! $show_notice ) { return; }

		//filter to select template
		$sui_template = apply_filters( 'wpmudev_notice_template', true, $msg );

		if( true === $sui_template ){
			WDEV_Plugin_Ui::render_dev_notification(
				WPMUDEV_Dashboard::$site->plugin_url . 'shared-ui/',
				$msg
			);
		} else {
			WPMUDEV_Dashboard::$ui->load_sui_template(
				'wpmudev_default_notice',
				array(
					'module_url'=> WPMUDEV_Dashboard::$site->plugin_url . 'assets/js/',
					'msg'		=> $msg,
					'type'  	=> apply_filters( 'wpmudev_default_notice_type', 'info', $msg ), //use this filter to set notice types. Default is info.
				),
				true
			);
		}

	}

	/**
	 * Render a global message.
	 * This can be displayed on any screen (i.e. global)
	 *
	 * Those messages are hardcoded status updates that are displayed when
	 * stuff was done in the background, like auto-upgrading a plugin...
	 *
	 * @since  4.0.0
	 */
	public function setup_global_notice() {
		$msg = $this->get_global_message();
		if ( ! $msg ) { return; }

		$allowed = array(
			'a' => array( 'href' => array(), 'title' => array(), 'target' => array(), 'class' => array() ),
			'br' => array(),
			'hr' => array(),
			'em' => array(),
			'strong' => array(),
		);

		printf(
			'<div id="message" class="updated notice is-dismissible"><p>%s</p></div>',
			wp_kses( $msg, $allowed )
		);
	}

	/**
	 * Fetches the next message from the queue and returns the notice-details.
	 *
	 * If no message is enqueued for display the function returns false.
	 *
	 * @since  4.0.0
	 * @return object|false The notice-details.
	 */
	protected function choose_message() {
		$res = false;

		if ( $this->the_notice && ! empty( $this->the_notice['content'] ) ) {
			$res = $this->the_notice;
		} else {
			// Populate $this->queue.
			$this->load_queue();

			foreach ( $this->queue as $id => $msg ) {
				if ( $msg['dismissed'] ) { continue; }

				$res = $msg;
				break;
			}
		}

		return $res;
	}

	/**
	 * Determines the global message to display.
	 *
	 * @since  4.0.0
	 * @param  string $default A default message.
	 * @return string The global message text, or empty string.
	 */
	public function get_global_message( $default = '' ) {
		$res = $default;
		if ( empty( $_GET['wpmudev_msg'] ) ) { return $res; }

		$id = intval( $_GET['wpmudev_msg'] );
		switch ( $id ) {
			case 1:
				$res = __( 'A WPMU DEV plugin was automatically updated.', 'wpmudev' );
				break;
		}

		return $res;
	}
}
