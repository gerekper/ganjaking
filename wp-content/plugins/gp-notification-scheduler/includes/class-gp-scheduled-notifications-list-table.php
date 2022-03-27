<?php

require_once( ABSPATH . '/wp-admin/includes/class-wp-list-table.php' );

class GP_Scheduled_Notifications_List_Table extends WP_List_Table {

	public function __construct( $args = array() ) {
		parent::__construct( $args );
		$this->perform_actions();
		$this->set_schedule();
		$this->set_columns();
	}

	/**
	 * Listen for various actions such as deleting.
	 *
	 * @return void
	 */
	public function perform_actions() {
		if ( rgget( 'delete_scheduled_nid' ) ) {
			$nid          = rgget( 'delete_scheduled_nid' );
			$entry_id     = rgars( $this->_args, 'entry/id' );
			$nonce_action = 'gpns-delete-' . $nid;

			$can_delete = GFCommon::current_user_can_any( 'gravityforms_delete_entries' );

			if ( ! $can_delete || ! wp_verify_nonce( rgget( '_wpnonce' ), $nonce_action ) ) {
				return;
			}

			gform_delete_meta( $entry_id, 'gpns_schedule_' . $nid );
		}
	}

	public function set_schedule() {
		$this->_args['schedule'] = gp_notification_schedule()->get_notification_queue( $this->_args['entry'] );
	}

	public function set_columns() {
		$columns = $this->get_columns();
		$hidden  = array();
		//$sortable              = $this->get_sortable_columns();
		$primary               = $this->get_primary_column_name();
		$this->_column_headers = array( $columns, $hidden, array(), $primary );
	}

	public function get_columns() {
		return array(
			'name'     => __( 'Notification Name' ),
			'schedule' => __( 'Scheduled' ),
		);
	}

	public function prepare_items() {

		$this->items = array();

		if ( empty( $this->_args['schedule'] ) ) {
			return;
		}

		foreach ( $this->_args['schedule'] as $item ) {

			$id           = $item['id'];
			$nid          = $item['nid'];
			$notification = rgars( $this->_args, "form/notifications/{$item['nid']}" );
			// It's possible the notification may have been deleted in a way that we cannot predict.
			if ( empty( $notification ) ) {
				continue;
			}

			$item['name'] = $notification['name'];

			$timezone_string = get_option( 'timezone_string', 'UTC' );

			$time_remaining     = $this->get_time_remaining_formatted( $item['timestamp'] );
			$scheduled_datetime = DateTime::createFromFormat( 'U', $item['timestamp'] );
			$scheduled_datetime->setTimezone( new DateTimeZone( $timezone_string ? $timezone_string : 'UTC' ) );
			$scheduled_formatted = $scheduled_datetime->format( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) );//date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $item['timestamp'] );
			$item['schedule']    = sprintf( '%s (%s)', $scheduled_formatted, $time_remaining );

			/* Row Actions */
			$item['name'] .= sprintf(
				'<div class="row-actions"><span class="delete"><a href="%s" class="submitdelete" onclick="if ( ! confirm(\'%s\') ) { return false; }">%s</a></span></div>',
				wp_nonce_url( add_query_arg( array( 'delete_scheduled_nid' => $nid ) ), 'gpns-delete-' . $nid ),
				__( 'Are you sure you wish to remove this scheduled notification?', 'gp-notification-scheduler' ),
				'Delete'
			);

			$this->items[] = $item;

		}

	}

	public function column_default( $item, $name ) {
		return rgar( $item, $name );
	}

	public function display_tablenav( $which ) {
		return '';
	}

	public function get_time_remaining_formatted( $timestamp ) {

		$now       = new DateTime();
		$scheduled = DateTime::createFromFormat( 'U', $timestamp );
		$diff      = $scheduled->diff( $now );
		$formatted = '';
		$formats   = array(
			array(
				'tag'   => '%a',
				'label' => _n_noop( '%d day', '%d days', 'gp-notification-scheduler' ),
			),
			array(
				'tag'   => '%h',
				'label' => _n_noop( '%d hour', '%d hours', 'gp-notification-scheduler' ),
			),
			array(
				'tag'   => '%i',
				'label' => _n_noop( '%d minute', '%d minutes', 'gp-notification-scheduler' ),
			),
			array(
				'tag'   => '%s',
				'label' => _n_noop( '%d seconds', '%d seconds', 'gp-notification-scheduler' ),
			),
		);

		foreach ( $formats as $format ) {
			$count = intval( $diff->format( $format['tag'] ) );
			if ( $format['tag'] == '%a' ) {
				$hours = intval( $diff->format( '%h' ) );
				if ( $hours > 12 ) {
					$count++;
				}
			}
			if ( $count > 0 ) {
				$formatted = sprintf( translate_nooped_plural( $format['label'], $count, 'gp-notification-scheduler' ), $count );
				break;
			}
		}

		if ( ! $formatted ) {
			return __( 'Now', 'gp-notification-scheduler' );
		}

		if ( $now > $scheduled ) {
			$formatted .= ' ' . __( 'ago', 'gp-notification-scheduler' );
		}

		return $formatted;
	}

	public function no_items() {
		_e( 'There are no notifications scheduled for this entry.', 'gp-notification-scheduler' );
	}

}
