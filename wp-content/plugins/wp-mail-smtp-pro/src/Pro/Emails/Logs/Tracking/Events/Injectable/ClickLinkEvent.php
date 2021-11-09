<?php

namespace WPMailSMTP\Pro\Emails\Logs\Tracking\Events\Injectable;

use WPMailSMTP\Helpers\Helpers;
use WPMailSMTP\Pro\Emails\Logs\Tracking\Tracking;

/**
 * Email tracking click link event class.
 *
 * @since 2.9.0
 */
class ClickLinkEvent extends AbstractInjectableEvent {

	/**
	 * Get the event type.
	 *
	 * @since 2.9.0
	 *
	 * @return string
	 */
	public static function get_type() {

		return 'click-link';
	}

	/**
	 * Whether the tracking event is enabled or not.
	 *
	 * @since 2.9.0
	 *
	 * @return bool
	 */
	public function is_active() {

		return wp_mail_smtp()->get_pro()->get_logs()->is_enabled_click_link_tracking();
	}

	/**
	 * Inject tracking link to each link in email content.
	 *
	 * @since 2.9.0
	 *
	 * @param string $email_content Email content.
	 *
	 * @return string Email content with injected tracking code.
	 */
	public function inject( $email_content ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		$html_dom                = new \DOMDocument();
		$old_libxml_errors_value = libxml_use_internal_errors( true );

		$html = make_clickable( $email_content );

		// Encode email content if charset is not included.
		$should_encode = preg_match( '/<meta.*charset=.*>/i', $html ) === 0;

		/**
		 * Filters whether email content should be encoded.
		 *
		 * @since 3.0.0
		 *
		 * @param bool $should_encode Whether email content should be encoded.
		 */
		$should_encode = apply_filters( 'wp_mail_smtp_pro_emails_logs_tracking_events_injectable_click_link_event_inject_encode_content', $should_encode );

		if ( $should_encode ) {

			// Include polyfill if mbstring PHP extension is not enabled.
			if ( ! function_exists( 'mb_convert_encoding' ) ) {
				Helpers::include_mbstring_polyfill();
			}

			// Convert non-ascii code into html-readable stuff.
			$encoded_html = mb_convert_encoding( $html, 'HTML-ENTITIES', 'auto' );
			if ( $encoded_html !== false ) {
				$html = $encoded_html;
			}
		}

		$html_dom->loadHTML( $html );

		$links = $html_dom->getElementsByTagName( 'a' );

		$created_links = [];

		foreach ( $links as $link ) {

			$url = $link->getAttribute( 'href' );

			// Skip empty, anchor or mailto links.
			if (
				strlen( trim( $url ) ) === 0 ||
				substr( trim( $url ), 0, 1 ) === '#' ||
				substr( trim( $url ), 0, 6 ) === 'mailto'
			) {
				continue;
			}

			/**
			 * Filters whether current url is trackable or not.
			 *
			 * @since 2.9.0
			 *
			 * @param bool   $is_trackable Whether url is trackable or not.
			 * @param string $url          Current url.
			 */
			if ( ! apply_filters( 'wp_mail_smtp_pro_emails_logs_tracking_events_injectable_click_link_event_inject_link', true, $url ) ) {
				continue;
			}

			if ( ! isset( $created_links[ $url ] ) ) {
				$link_id = $this->add_link( $url );

				// Skip if link was not created.
				if ( $link_id === false ) {
					continue;
				}

				$created_links[ $url ] = $link_id;
			} else {
				$link_id = $created_links[ $url ];
			}

			$tracking_url = $this->get_tracking_url(
				[
					'object_id' => $link_id,
					'url'       => rawurlencode( $url ),
				]
			);

			$link->setAttribute( 'href', $tracking_url );
		}

		$modified_content = $html_dom->saveHTML();

		libxml_clear_errors();
		libxml_use_internal_errors( $old_libxml_errors_value );

		if ( $modified_content !== false ) {
			return $modified_content;
		}

		return $email_content;
	}

	/**
	 * Persist event data to DB.
	 *
	 * @since 2.9.0
	 *
	 * @return int|false Event ID or false if saving failed.
	 */
	public function persist() {

		// In case if images loading disabled in email, create open email event when first link in email clicked.
		$open_event = new OpenEmailEvent( $this->get_email_log_id() );
		if ( ! $open_event->was_event_already_triggered() ) {
			$open_event->persist();
		}

		return parent::persist();
	}

	/**
	 * Redirect user to actual url.
	 *
	 * @since 2.9.0
	 *
	 * @param array $event_data Event data from request.
	 *
	 * @return \WP_REST_Response REST response.
	 */
	public function get_response( $event_data ) {

		$response = new \WP_REST_Response();

		$response->header( 'Cache-Control', 'must-revalidate, no-cache, no-store, max-age=0, no-transform' );
		$response->header( 'Pragma', 'no-cache' );
		$response->set_status( 301 );
		$response->header( 'Location', urldecode( $event_data['url'] ) );

		return $response;
	}

	/**
	 * Save tracked link to DB.
	 *
	 * @since 2.9.0
	 *
	 * @param string $url Actual url form email.
	 *
	 * @return false|int Link ID.
	 */
	public function add_link( $url ) {

		global $wpdb;

		$data = [
			'email_log_id' => intval( $this->get_email_log_id() ),
			'url'          => esc_url_raw( $url ),
		];

		$result = $wpdb->insert( Tracking::get_links_table_name(), $data, [ '%d', '%s' ] );

		return $result !== false ? $wpdb->insert_id : false;
	}

	/**
	 * Delete all email related tracked links.
	 *
	 * @since 2.9.0
	 *
	 * @return bool
	 */
	public function delete_links() {

		global $wpdb;

		$email_log_id = intval( $this->get_email_log_id() );

		return $wpdb->delete( Tracking::get_links_table_name(), [ 'email_log_id' => $email_log_id ], [ '%d' ] ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
	}
}
