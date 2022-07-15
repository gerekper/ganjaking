<?php

namespace WPMailSMTP\Pro\Emails\Logs\Tracking;

use WPMailSMTP\MailCatcherInterface;
use WPMailSMTP\Pro\Emails\Logs\Tracking\Events\Events;
use WPMailSMTP\Pro\Emails\Logs\Tracking\Events\EventFactory;
use WPMailSMTP\Pro\Emails\Logs\Tracking\Events\Injectable;
use WPMailSMTP\Pro\Emails\Logs\Tracking\Events\Injectable\AbstractInjectableEvent;
use WP_REST_Request;

/**
 * Email events tracking class.
 *
 * @since 2.9.0
 */
class Tracking {

	/**
	 * The base name of the DB table for the email tracking events, without the DB prefix.
	 *
	 * @since 2.9.0
	 *
	 * @var string
	 */
	const BASE_EVENTS_DB_NAME = 'wpmailsmtp_email_tracking_events';

	/**
	 * The base name of the DB table for the email tracking links, without the DB prefix.
	 *
	 * @since 2.9.0
	 *
	 * @var string
	 */
	const BASE_LINKS_DB_NAME = 'wpmailsmtp_email_tracking_links';

	/**
	 * Whether the email tracking is enabled or not.
	 *
	 * @since 2.9.0
	 *
	 * @var bool
	 */
	protected $is_enabled;

	/**
	 * Tracking constructor.
	 *
	 * @since 2.9.0
	 *
	 * @param bool $is_enabled Whether the email tracking is enabled or not.
	 */
	public function __construct( $is_enabled = false ) {

		$this->is_enabled = $is_enabled;
	}

	/**
	 * Register hooks.
	 *
	 * @since 2.9.0
	 */
	public function hooks() {

		// Register routes to handle tracking events, regardless of tracking being enabled (link redirects have to work).
		add_action( 'rest_api_init', [ $this, 'register_rest_route' ] );

		if ( $this->is_enabled ) {
			// Inject tracking code to email content.
			add_action( 'wp_mail_smtp_mailcatcher_pre_send_before', [ $this, 'inject_tracking_code' ], 20 );
			add_action( 'wp_mail_smtp_mailcatcher_smtp_pre_send_before', [ $this, 'inject_tracking_code' ], 20 );
		}
	}

	/**
	 * Register REST routes to handle tracking events.
	 *
	 * @since 2.9.0
	 */
	public function register_rest_route() {

		register_rest_route(
			'wp-mail-smtp/v1',
			'/e/(?P<data>.+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'handle_injectable_event' ],
				'permission_callback' => '__return_true',
			]
		);
	}

	/**
	 * Inject tracking code to email content.
	 *
	 * @since 2.9.0
	 *
	 * @param MailCatcherInterface $mailcatcher The MailCatcher object.
	 */
	public function inject_tracking_code( $mailcatcher ) {

		if ( ! ( new Events() )->is_valid_db() ) {
			return;
		}

		$email_log_id = wp_mail_smtp()->get_pro()->get_logs()->get_current_email_id();

		// Skip if there is no email log ID or email content type is not html.
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		if ( empty( $email_log_id ) || $mailcatcher->ContentType !== 'text/html' ) {
			return;
		}

		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$content = $mailcatcher->Body;

		$events = [
			Injectable\OpenEmailEvent::class,
			Injectable\ClickLinkEvent::class,
		];

		foreach ( $events as $event_class ) {
			$event = new $event_class( $email_log_id );

			if ( $event->is_active() ) {
				/**
				 * Filters whether inject tracking code or not.
				 *
				 * @since 2.9.0
				 *
				 * @param bool                    $is_trackable Whether inject tracking code or not.
				 * @param AbstractInjectableEvent $event        Event object.
				 */
				if ( ! apply_filters( 'wp_mail_smtp_pro_emails_logs_tracking_tracking_inject_tracking_code', true, $event ) ) {
					continue;
				}

				$content = $event->inject( $content );
			}
		}

		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$mailcatcher->Body = $content;
	}

	/**
	 * Handle injectable tracking event.
	 * Tracking event must be handled even if tracking is disabled for getting correct
	 * response (e.g. redirect for click link event or image for open email event).
	 *
	 * @since 2.9.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return mixed REST or custom response.
	 */
	public function handle_injectable_event( $request ) {

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		$data = base64_decode( $request->get_param( 'data' ) );

		parse_str( $data, $args );

		// Exit if required event arguments are missed.
		if ( ! isset( $args['data']['email_log_id'] ) || ! isset( $args['data']['event_type'] ) || ! isset( $args['hash'] ) ) {
			return false;
		}

		$tracking_data = $args['data'];

		$email_log_id = intval( $tracking_data['email_log_id'] );
		$event_type   = sanitize_key( $tracking_data['event_type'] );

		$event_factory = new EventFactory();

		$event = $event_factory->create_event( $event_type, $email_log_id );

		// Exit if HMAC authentication is failed.
		if ( ! $event->verify_signature( $tracking_data, $args['hash'] ) ) {
			return false;
		}

		$event->set_request( $request );

		// Set event related object ID if it's present.
		if ( ! empty( $tracking_data['object_id'] ) && is_numeric( $tracking_data['object_id'] ) ) {
			$event->set_object_id( intval( $tracking_data['object_id'] ) );
		}

		// Persist event to DB only if DB tables are available and tracking is enabled.
		if ( ( new Events() )->is_valid_db() && $event->is_active() ) {
			$event->persist();
		}

		/**
		 * Fires after tracking event was processed.
		 *
		 * @since 3.5.0
		 *
		 * @param array $tracking_data The tracking data passed via the URL.
		 */
		do_action( 'wp_mail_smtp_pro_emails_logs_tracking_handle_injectable_event', $tracking_data );

		return $event->get_response( $tracking_data );
	}

	/**
	 * Get the email tracking events DB table name.
	 *
	 * @since 2.9.0
	 *
	 * @return string Email tracking events DB table name, prefixed.
	 */
	public static function get_events_table_name() {

		global $wpdb;

		return $wpdb->prefix . self::BASE_EVENTS_DB_NAME;
	}

	/**
	 * Get the email tracking links DB table name.
	 *
	 * @since 2.9.0
	 *
	 * @return string Email tracking links DB table name, prefixed.
	 */
	public static function get_links_table_name() {

		global $wpdb;

		return $wpdb->prefix . self::BASE_LINKS_DB_NAME;
	}
}
