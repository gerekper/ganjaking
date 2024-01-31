<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Box_Office_Cron {
	/**
	 * Whether we are rendering an email or not.
	 *
	 * @var boolean
	 */
	protected static $is_rendering_email = false;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'cron_schedules', array( $this, 'cron_schedules' ) );
		add_action( 'init', array( $this, 'scheduled_batch_emails' ) );
		add_action( 'wc-box-office-send-email-after-tickets-published', array( $this, 'send_email_to_ticket_contacts' ), 10, 2 );
		add_action( 'wc-box-office-send-email-for-private-link', array( $this, 'send_email_for_private_content_link' ), 10, 3 );
		add_action( 'wc-box-office-update-user-privacy-preference', array( $this, 'update_user_preference' ), 10, 2 );
	}

	/**
	 * Returns the value of `$is_rendering_email`.
	 *
	 * @return boolean
	 */
	public static function is_rendering_email() {
		return self::$is_rendering_email;
	}

	/**
	 * Filter cron schedules.
	 *
	 * @param array $schedules Schedules.
	 *
	 * @return array Schedules
	 */
	public function cron_schedules( $schedules ) {
		$schedules['10-mins'] = array(
			'interval' => 60 * 10,
			'display'  => __( 'Once every 10 minutes', 'woocommerce-box-office' ),
		);
		return $schedules;
	}

	/**
	 * Schedule sending batch emails from tools.
	 *
	 * @return void
	 */
	public function scheduled_batch_emails() {
		add_action( 'wc-box-office-send-batch-emails', array( $this, 'send_emails_batch' ) );

		if ( ! wp_next_scheduled( 'wc-box-office-send-batch-emails' ) ) {
			wp_schedule_event( time(), '10-mins', 'wc-box-office-send-batch-emails' );
		}
	}

	/**
	 * Send batch emails. One job at a time.
	 *
	 * This worker is hooked from 'wc-box-office-send-batch-emails' schedule.
	 *
	 * @return void
	 */
	public function send_emails_batch() {
		global $wpdb;

		$jobs = get_posts(
			array(
				'post_type'      => 'event_ticket_email',
				'post_status'    => 'pending',
				'order'          => 'ASC',
				'posts_per_page' => -1,
				'cache_results'  => false,
			)
		);

		if ( empty( $jobs ) ) {
			return;
		}

		$job = array_shift( $jobs );

		WCBO()->components->logger->log( 'Processing e-mail job', $job->ID );

		$tickets_raw = $wpdb->get_results(
			$wpdb->prepare(
				"
				SELECT SQL_CALC_FOUND_ROWS meta_id, meta_value
				FROM $wpdb->postmeta
				WHERE
				$wpdb->postmeta.post_id = %d AND
				$wpdb->postmeta.meta_key = %s LIMIT %d;
				",
				$job->ID,
				'_ticket_id',
				200
			)
		);

		$total     = $wpdb->get_var( 'SELECT FOUND_ROWS();' );
		$processed = 0;

		// Force send cron emails.
		add_filter( 'woocommerce_box_office_send_ticket_email', '__return_true' );

		self::$is_rendering_email = true;

		foreach ( $tickets_raw as $meta ) {
			$deleted = $wpdb->query(
				$wpdb->prepare(
					"
					DELETE FROM $wpdb->postmeta WHERE post_id = %d AND meta_id = %d LIMIT 1;
					",
					$job->ID,
					$meta->meta_id
				)
			);

			if ( $deleted > 0 ) {
				$sent = wc_box_office_send_ticket_email( $meta->meta_value, '', $job->post_title, $job->post_content, $job->post_title );

				if ( $sent ) {
					$log_data = array(
						'ticket_id' => $meta->meta_value,
					);

					$emails = implode( ', ', wc_box_office_get_ticket_email_contacts( $meta->meta_value ) );
					WCBO()->components->logger->log( sprintf( 'E-mail successfully sent to %s.', $emails ), $job->ID );
				} else {
					WCBO()->components->logger->log( 'Processing e-mail job', $job->ID );
				}
				++$processed;
			}
		}

		remove_filter( 'woocommerce_box_office_send_ticket_email', '__return_true' );

		if ( $total - $processed < 1 ) {
			wp_update_post(
				array(
					'ID'          => $job->ID,
					'post_status' => 'publish',
				)
			);

			WCBO()->components->logger->log( 'Email job complete and published.', $job->ID );
		}

		if ( self::$is_rendering_email ) {
			self::$is_rendering_email = false;
		}
	}

	/**
	 * Schedule send email to each contact in each ticket after tickets published.
	 *
	 * @param int   $timestamp  The time to send the email.
	 * @param array $ticket_ids Ticket IDs.
	 */
	public function schedule_send_email_after_tickets_published( $timestamp, array $ticket_ids, $force_send = false ) {
		wp_schedule_single_event( $timestamp, 'wc-box-office-send-email-after-tickets-published', array( $ticket_ids, $force_send ) );
	}

	/**
	 * Send email to each contact in each ticket.
	 *
	 * Triggered by scheduled event 'wc-box-office-send-email-after-tickets-published'.
	 *
	 * @param array $ticket_ids Ticket IDs.
	 * @param bool  $force_send Force send even if ticket's product has _email_tickets
	 *                          set to false.
	 *
	 * @return void
	 */
	public function send_email_to_ticket_contacts( array $ticket_ids, $force_send = false ) {
		if ( $force_send ) {
			add_filter( 'woocommerce_box_office_send_ticket_email', '__return_true' );
		}

		self::$is_rendering_email = true;

		foreach ( $ticket_ids as $ticket_id ) {
			$product_id = get_post_meta( $ticket_id, '_product', true );
			$subject    = get_post_meta( $product_id, '_email_ticket_subject', true );
			$message    = get_post_meta( $product_id, '_ticket_email_html', true );

			wc_box_office_send_ticket_email( $ticket_id, '', $subject, $message );
		}

		if ( $force_send ) {
			remove_filter( 'woocommerce_box_office_send_ticket_email', '__return_true' );
		}

		if ( self::is_rendering_email() ) {
			self::$is_rendering_email = false;
		}
	}

	/**
	 * Schedule send email after requesting private content link.
	 *
	 * @param int    $timestamp          The time to send the email.
	 * @param string $email              Email address to send the email.
	 * @param int    $ticket_id          Ticket ID.
	 * @param int    $private_content_id Private content ID.
	 *
	 * @return void
	 */
	public function schedule_send_email_for_private_content_link( $timestamp, $email, $ticket_id, $private_content_id ) {
		wp_schedule_single_event( $timestamp, 'wc-box-office-send-email-for-private-link', array( $email, $ticket_id, $private_content_id ) );
	}

	/**
	 * Send email which contains private content link. Template for the email
	 * can be found in `templates/ticket/private-link-email.php`.
	 *
	 * Triggered by scheduled event 'wc-box-office-send-email-for-private-link'.
	 *
	 * @param string $email              Email address to send the email.
	 * @param inb    $ticket_id          Ticket ID.
	 * @param int    $private_content_id Private content ID.
	 *
	 * @return void
	 */
	public function send_email_for_private_content_link( $email, $ticket_id, $private_content_id ) {
		$content = get_post( $private_content_id );
		if ( ! $content ) {
			return;
		}

		$title = get_the_title( $content );

		$vars = array(
			'private_title' => $title,
			'private_link'  => add_query_arg( 'token', get_post_meta( $ticket_id, '_token', true ), get_permalink( $content->ID ) ),
		);

		$subject = sprintf( __( 'Your link to view %s', 'woocommerce-box-office' ), $title );
		$subject = wc_box_office_get_parsed_ticket_content( $ticket_id, $subject );

		$message = wc_get_template_html( 'ticket/private-link-email.php', $vars, 'woocommerce-box-office', WCBO()->dir . 'templates/' );
		$message = wpautop( $message );
		$message = wc_box_office_get_parsed_ticket_content( $ticket_id, $message );

		wc_box_office_send_mail( array( $email ), $subject, $message );
	}

	/**
	 * Schedule the job for updating user privacy preference.
	 *
	 * @param string $preference User preference for a ticket.
	 * @param int    $page       The current batch of tickets being updated.
	 *
	 * @return void
	 */
	public function schedule_user_privacy_update_job( $preference, $page = 1 ) {
		update_option( 'wc-box-office-update-user-privacy-preference', 'yes' );

		wp_schedule_single_event(
			time(),
			'wc-box-office-update-user-privacy-preference',
			array(
				'preference' => $preference,
				'page'       => $page,
			)
		);
	}

	/**
	 * Updates user privacy preference in all tickets.
	 *
	 * @param string $preference User preference for a ticket.
	 * @param int    $page       The current batch of tickets being updated.
	 *
	 * @return void
	 */
	public function update_user_preference( $preference, $page = 1 ) {
		$args = array(
			'post_type'      => 'event_ticket',
			'posts_per_page' => get_option( 'posts_per_page' ),
			'paged'          => (int) $page,
		);

		$tickets = get_posts( $args );

		if ( 0 === count( $tickets ) ) {
			delete_option( 'wc-box-office-update-user-privacy-preference' );
			return;
		}

		foreach ( $tickets as $ticket ) {
			$ticket_id = $ticket->ID;
			update_post_meta( $ticket_id, '_user_pii_preference', $preference );
		}

		$this->schedule_user_privacy_update_job( $preference, $page + 1 );
	}
}
