<?php

namespace WPMailSMTP\Pro\Providers\Zoho;

use WPMailSMTP\Admin\DebugEvents\DebugEvents;
use WPMailSMTP\ConnectionInterface;
use WPMailSMTP\Helpers\Helpers;
use WPMailSMTP\MailCatcher;
use WPMailSMTP\Providers\MailerAbstract;
use WPMailSMTP\Options as PluginOptions;
use WPMailSMTP\WP;

/**
 * Class Mailer implements Zoho Mail API functionality.
 *
 * @see https://www.zoho.com/mail/help/api/post-send-email-attachment.html
 *
 * @since 2.3.0
 */
class Mailer extends MailerAbstract {

	/**
	 * Which response code from HTTP provider is considered to be successful?
	 *
	 * @since 2.3.0
	 *
	 * @var int
	 */
	protected $email_sent_code = 200;

	/**
	 * URL to make an API request to.
	 *
	 * This is only the root URL, the full URL will look something like this:
	 * https://mail.zoho.<domain>/api/accounts/<accountId>/messages
	 *
	 * The domain and accountId will be added in constructor so it will look something like:
	 * https://mail.zoho.com/api/accounts/3231jq...9ej2119/
	 *
	 * The actual endpoint eg. `messages` will be added when needed.
	 *
	 * @since 2.3.0
	 *
	 * @var string
	 */
	protected $root_url = 'https://mail.zoho.';

	/**
	 * Mailer constructor.
	 *
	 * @since 2.3.0
	 *
	 * @param MailCatcher         $phpmailer  The MailCatcher object.
	 * @param ConnectionInterface $connection The Connection object.
	 */
	public function __construct( $phpmailer, $connection = null ) {

		// Init the client that checks tokens and re-saves them if needed.
		new Auth( $connection );

		// We want to prefill everything from \WPMailSMTP\MailCatcher class, which extends \PHPMailer.
		parent::__construct( $phpmailer, $connection );

		$token        = $this->connection_options->get( $this->mailer, 'access_token' );
		$domain       = $this->connection_options->get( $this->mailer, 'domain' );
		$user_details = $this->connection_options->get( $this->mailer, 'user_details' );
		$account_id   = ! empty( $user_details['account_id'] ) ? $user_details['account_id'] : '';

		$this->root_url .= $domain . '/api/accounts/' . $account_id . '/';

		if ( ! empty( $token['access_token'] ) ) {
			$this->set_header( 'Authorization', 'Zoho-oauthtoken ' . $token['access_token'] );
		}
		$this->set_header( 'Content-Type', 'application/json' );

		$this->process_phpmailer( $phpmailer );
	}

	/**
	 * PhpMailer generates certain headers, including our custom.
	 * We want to preserve them when sending an email.
	 *
	 * Zoho does not support custom email headers.
	 * So we do nothing.
	 *
	 * @since 2.3.0
	 *
	 * @param array $headers The headers to set.
	 */
	public function set_headers( $headers ) {}

	/**
	 * Define the from email and name.
	 *
	 * It doesn't support random email, should be the same as used for OAuth authentication/connection.
	 *
	 * @since 2.3.0
	 *
	 * @param string $email Not used.
	 * @param string $name  The from name.
	 */
	public function set_from( $email, $name = '' ) {

		$sender = $this->connection_options->get( $this->mailer, 'user_details' );
		$from   = sprintf(
			'"%1$s" <%2$s>',
			wp_slash( $name ),
			isset( $sender['email'] ) ? $sender['email'] : ''
		);

		$this->set_body_param(
			[
				'fromAddress' => $from,
			]
		);
	}

	/**
	 * Define the CC/BCC/TO email and name lists.
	 *
	 * @since 2.3.0
	 *
	 * @param array $recipients The multi dimensional array of all to/cc/bcc email addresses and names.
	 */
	public function set_recipients( $recipients ) {

		if ( empty( $recipients ) ) {
			return;
		}

		// Allow for now only these recipient types.
		$default = [ 'to', 'cc', 'bcc' ];
		$data    = [];

		foreach ( $recipients as $type => $emails ) {
			if (
				! in_array( $type, $default, true ) ||
				empty( $emails ) ||
				! is_array( $emails )
			) {
				continue;
			}

			$type_id = $type . 'Address';

			$data[ $type_id ] = [];

			// Iterate over all emails for each type.
			// There might be multiple to/cc/bcc emails.
			foreach ( $emails as $email ) {
				$addr = isset( $email[0] ) ? $email[0] : false;
				$name = isset( $email[1] ) ? $email[1] : false;

				if ( ! filter_var( $addr, FILTER_VALIDATE_EMAIL ) ) {
					continue;
				}

				if ( ! empty( $name ) ) {
					$addr = sprintf( '%1$s <%2$s>', $name, $addr );
				}

				$data[ $type_id ][] = $addr;
			}
		}

		if ( ! empty( $data ) ) {
			foreach ( $data as $type_id => $type_data ) {
				$this->set_body_param(
					[
						$type_id => implode( ',', $type_data ),
					]
				);
			}
		}
	}

	/**
	 * Set the email content.
	 * Zoho supports only HTML email content, and they create a multipart email with
	 * automatic HTML stripping for the plain text version.
	 *
	 * @since 2.3.0
	 *
	 * @param array|string $content String when text/plain, array otherwise.
	 */
	public function set_content( $content ) {

		if ( empty( $content ) ) {
			return;
		}

		if ( is_array( $content ) ) {
			if (
				! isset( $content['text'] ) ||
				! isset( $content['html'] )
			) {
				return;
			}

			$content = ! empty( $content['html'] ) ? $content['html'] : $content['text'];
		} else {
			$content = nl2br( $content );
		}

		$this->set_body_param(
			[
				'content' => $content,
			]
		);
	}

	/**
	 * Set Reply-To part of the message.
	 *
	 * Zoho requires the users to verify the reply-to email address.
	 * Only one reply-to email address is allowed, so we take the first one.
	 *
	 * @since 2.3.0
	 * @since 2.9.0 Zoho does not allow dynamic reply-to addresses, so we removed the support for them. GH #645.
	 *
	 * @param array $reply_to The multidimensional array of email addresses and names that
	 *                        should be used for the reply field.
	 */
	public function set_reply_to( $reply_to ) {}

	/**
	 * Set the return path.
	 *
	 * Zoho doesn't support return_path params.
	 * So we do nothing.
	 *
	 * @since 2.3.0
	 *
	 * @param string $from_email The email address that should be used for the return path.
	 */
	public function set_return_path( $from_email ) {}

	/**
	 * Add attachments to the body.
	 * Zoho requires the attachments to be uploaded via an endpoint prior to sending the email.
	 * The collected upload data has to be then added to the email sending request.
	 *
	 * @since 2.3.0
	 *
	 * @see https://www.zoho.com/mail/help/api/post-send-email-attachment.html
	 * @see https://www.zoho.com/mail/help/api/post-upload-attachments.html
	 *
	 * @param array $attachments Array of attachments.
	 */
	public function set_attachments( $attachments ) {

		if ( empty( $attachments ) ) {
			return;
		}

		$data = $this->prepare_attachments( $attachments );

		if ( ! empty( $data ) ) {
			$this->set_body_param(
				[
					'attachments' => $data,
				]
			);
		}
	}

	/**
	 * Prepare attachments data for Zoho API.
	 *
	 * @since 3.1.0
	 *
	 * @param array $attachments Array of attachments.
	 *
	 * @return array
	 */
	protected function prepare_attachments( $attachments ) {

		$data = [];

		// Prepare request headers.
		$headers                 = $this->get_headers();
		$headers['Content-Type'] = 'application/octet-stream';

		foreach ( $attachments as $attachment ) {
			$file = $this->get_attachment_file_content( $attachment );

			if ( $file === false ) {
				continue;
			}

			// Upload the attachment via Zoho API.
			$url = add_query_arg(
				'fileName',
				$this->get_attachment_file_name( $attachment ),
				$this->root_url . 'messages/attachments'
			);

			$params = [
				'headers' => $headers,
				'body'    => $file,
			];

			$response = wp_safe_remote_post( $url, $params );

			if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
				continue;
			}

			$body = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( ! empty( $body['data'] ) ) {
				$data[] = $body['data'];
			}
		}

		return $data;
	}

	/**
	 * Redefine the way email body is returned.
	 * Zoho API needs JSON object.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_body() {

		return wp_json_encode( parent::get_body() );
	}

	/**
	 * Send the email using Zoho Mail API.
	 *
	 * @since 2.3.0
	 */
	public function send() {

		$params = PluginOptions::array_merge_recursive(
			$this->get_default_params(),
			[
				'headers' => $this->get_headers(),
				'body'    => $this->get_body(),
			]
		);

		/*
		 * Right now Zoho doesn't allow to redefine From and Sender email headers.
		 * It always uses the email address that was used to connect to its API.
		 * With code below we are making sure that Email Log archive and single Email Log
		 * have the save value for From email header.
		 */
		$sender = $this->connection_options->get( $this->mailer, 'user_details' );

		if ( ! empty( $sender['email'] ) ) {
			$this->phpmailer->From   = $sender['email'];
			$this->phpmailer->Sender = $sender['email'];
		}

		$response = wp_safe_remote_post( $this->root_url . 'messages', $params );

		DebugEvents::add_debug(
			esc_html__( 'An email request was sent to the Zoho Mail API.', 'wp-mail-smtp-pro' )
		);

		$this->process_response( $response );
	}

	/**
	 * Process Zoho API response with a helpful error.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_response_error() {

		$error_text[] = $this->error_message;

		if ( ! empty( $this->response ) ) {
			$body = wp_remote_retrieve_body( $this->response );

			if ( ! empty( $body->data->errorCode ) || ! empty( $body->data->moreInfo ) ) {
				$message     = ! empty( $body->status->description ) ? $body->status->description : '';
				$code        = ! empty( $body->status->code ) ? $body->status->code : '';
				$description = ! empty( $body->data->errorCode ) ? $body->data->errorCode : $body->data->moreInfo;

				$error_text[] = Helpers::format_error_message( $message, $code, $description );
			} else {
				$error_text[] = WP::wp_remote_get_response_error_message( $this->response );
			}
		}

		return implode( WP::EOL, array_map( 'esc_textarea', array_filter( $error_text ) ) );
	}

	/**
	 * Get mailer debug information, that is helpful during support.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_debug_info() {

		$mg_text = [];

		$auth = new Auth( $this->connection );

		$mg_text[] = '<strong>Domain:</strong> ' . (string) $this->connection_options->get( $this->mailer, 'domain' );
		$mg_text[] = '<strong>Client ID/Secret:</strong> ' . ( $auth->is_clients_saved() ? 'Yes' : 'No' );
		$mg_text[] = '<strong>Tokens:</strong> ' . ( ! $auth->is_auth_required() ? 'Yes' : 'No' );

		return implode( '<br>', $mg_text );
	}

	/**
	 * Whether the mailer has all its settings correctly set up and saved.
	 *
	 * @since 2.3.0
	 *
	 * @return bool
	 */
	public function is_mailer_complete() {

		if ( ! $this->is_php_compatible() ) {
			return false;
		}

		$auth = new Auth( $this->connection );

		if (
			$auth->is_clients_saved() &&
			! $auth->is_auth_required() &&
			! empty( $this->connection_options->get( $this->mailer, 'domain' ) )
		) {
			return true;
		}

		return false;
	}
}
