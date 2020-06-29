<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class FUE_Bounce_Handler.
 */
class FUE_Bounce_Handler {

	public $settings = array();

	public function __construct() {
		$this->settings = wp_parse_args(
			get_option( 'fue_bounce_settings', array() ),
			self::get_default_settings()
		);

		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	public function init() {
	}

	public static function get_default_settings() {
		return apply_filters( 'fue_default_bounce_settings', array(
			'email'                       => '',
			'handle_bounces'              => 0,
			'server'                      => '',
			'port'                        => 110,
			'ssl'                         => 0,
			'username'                    => '',
			'password'                    => '',
			'delete_messages'             => 0,
			'soft_bounce_resend_limit'    => 3,
			'soft_bounce_resend_interval' => 60,
		) );
	}

	/**
	 * Enable or disable the lock used for checking and connecting to the POP3 account.
	 *
	 * @param bool $lock
	 */
	public function set_lock( $lock = true ) {
		set_transient( 'fue_check_bounces_lock', $lock, 300 );
	}

	/**
	 * Check if a lock has been set on the POP3 account.
	 *
	 * @return bool
	 */
	public function is_locked() {
		$lock = get_transient( 'fue_check_bounces_lock' );

		if ( ! is_bool( $lock ) ) {
			$lock = false;
		}

		return $lock;

	}

	public function is_bounce_handling_enabled() {
		return (bool) $this->settings['handle_bounces'];
	}

	public function schedule_bounce_handling() {
		if ( as_next_scheduled_action( 'fue_bounce_handler', null, 'fue' ) ) {
			return;
		}

		as_schedule_recurring_action( current_time( 'timestamp', true ), 300, 'fue_bounce_handler', array(), 'fue' );
	}

	public function unschedule_bounce_handling() {
		as_unschedule_action( 'fue_bounce_handler', array(), 'fue' );
	}

	/**
	 * Establish a connection to the POP3 account server
	 *
	 * @return POP3|WP_Error
	 */
	public function connect() {
		if ( ! $this->is_bounce_handling_enabled() ) {
			return new WP_Error( 'fue_bounce_handler', 'Bounce handling is disabled' );
		}

		if ( $this->is_locked() ) {
			return new WP_Error( 'fue_bounce_handler', 'Lock is active on account. Try again in 5 minutes' );
		}

		if ( ! $this->settings['server'] || ! $this->settings['username'] || ! $this->settings['password'] ) {
			return new WP_Error( 'fue_bounce_handler', 'POP3 account is not set up.' );
		}

		$this->set_lock( true );

		if ( $this->settings['ssl'] ) {
			$this->settings['server'] = 'ssl://' . $this->settings['server'];
		}

		require_once ABSPATH . WPINC . '/class-pop3.php';
		$pop3 = new POP3();

		if ( ! $pop3->connect( $this->settings['server'], $this->settings['port'] ) ) {
			return new WP_Error( 'fue_bounce_handler', 'Connection could not be established. ' . $pop3->ERROR );
		}

		if ( ! $pop3->login( $this->settings['username'], $this->settings['password'] ) ) {
			return new WP_Error( 'fue_bounce_handler', $pop3->ERROR );
		}

		return $pop3;
	}

	/**
	 * Scan messages from the $pop3 account and process bounce emails.
	 *
	 * @param POP3|WP_Error $pop3 Resource must already have an active connection
	 */
	public function handle_bounce_messages( $pop3 ) {
		require_once FUE_INC_DIR . '/lib/bounce/bounce_driver.class.php';

		// TODO: Implement error handling.
		if ( is_wp_error( $pop3 ) ) {
			return;
		}

		$delete_messages = $this->settings['delete_messages'];

		$mail_id = FUE_Newsletter::get_site_id();
		$stat    = $pop3->popstat();
		$count   = $stat[0];

		if ( ! $stat ) {
			$pop3->quit();
			return;
		}

		// Only max 1000 at once.
		$count = min( $count, 1000 );

		for ( $i = 1; $i <= $count; $i++ ) {
			$message = $pop3->get( $i );

			if ( ! $message ) {
				if ( $delete_messages ) {
					$pop3->delete( $i );
				}
				continue;
			}

			$message = implode( $message );

			preg_match( '#X-FUE: ([a-f0-9]{32})#i', $message, $site_id );
			preg_match( '#X-FUE-EMAIL: (\d+)#i', $message, $email_id );
			preg_match( '#X-FUE-QUEUE-ID: (\d+)#i', $message, $queue_id );

			if ( ! empty( $site_id ) && ! empty( $email_id ) && ! empty( $queue_id ) ) {

				if ( $site_id[1] == $mail_id ) {

					$bouncehandler = new Bouncehandler();
					$bounceresult = $bouncehandler->parse_email( $message );
					$bounceresult = (object) $bounceresult[0];

					$email = new FUE_Email( $email_id[1] );
					$item  = new FUE_Sending_Queue_Item( $queue_id[1] );

					if ( $email->exists() && $item->exists() ) {

						switch ( $bounceresult->action ) {

							case 'success':
								break;

							case 'failed':
								// Hardbounce.
								$this->bounce( $email, $item, true );
								break;

							case 'transient':
							default:
								// Softbonuce.
								$this->bounce( $email, $item, false );

						}
					}

					$pop3->delete( $i );

				}
			} else {
				if ( $delete_messages ) {
					$pop3->delete( $i );
				}
			}
		}

		$pop3->quit();
	}

	/**
	 * Handle bounced emails.
	 *
	 * Emails are added to the optout/excludes list in case a hard bounce happens.
	 * Otherwise, the email is rescheduled to be sent again until it reaches the limit
	 * of soft bounces where it will then be upgraded to a hard bounce.
	 *
	 * @param FUE_Email $fue_email
	 * @param FUE_Sending_Queue_Item $queue_item
	 * @param bool $hard
	 * @return bool
	 */
	public function bounce( $fue_email, $queue_item, $hard = false ) {
		if ( $hard ) {

			fue_exclude_email_address( $queue_item->user_email );
			$queue_item->add_note( 'The email hard bounced. Recipient has been added to the excludes list' );
			$queue_item->status = FUE_Sending_Queue_Item::STATUS_BOUNCED;
			$queue_item->save();

			do_action( 'fue_email_bounced', $fue_email, $queue_item, true );
			return true;

		}

		// Soft bounce.
		$bounce_attempts = $this->settings['soft_bounce_resend_limit'];

		// Check if bounce limit has been reached => hardbounce.
		$made_attempts = 0;

		if ( isset( $queue_item->meta['soft_bounces'] ) ) {
			$made_attempts = absint( $queue_item->meta['soft_bounces'] );
		}

		if ( 1 == $bounce_attempts || $made_attempts > $bounce_attempts ) {
			// Upgrade to a hard bounce.
			return $this->bounce( $fue_email, $queue_item, true );
		}

		// Soft bounce - increase the attempts and reschedule the email.
		$delay      = absint( $this->settings['soft_bounce_resend_interval'] ) * 60;
		$send_on    = current_time( 'timestamp', true ) + $delay;
		$made_attempts++;
		$queue_item->meta['soft_bounces'] = $made_attempts;
		$queue_item->is_sent = 0;
		$queue_item->send_on = $send_on;
		$queue_item->save();
		$queue_item->add_note( 'Email soft bounced and will be rescheduled to send again.' );

		Follow_Up_Emails::instance()->scheduler->schedule_email( $queue_item->id, $send_on, false );

		do_action( 'fue_email_bounced', $fue_email, $queue_item, false );

		return true;
	}
}
