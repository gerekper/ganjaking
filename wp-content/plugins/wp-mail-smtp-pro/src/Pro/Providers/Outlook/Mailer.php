<?php

namespace WPMailSMTP\Pro\Providers\Outlook;

use WPMailSMTP\Admin\DebugEvents\DebugEvents;
use WPMailSMTP\Helpers\Helpers;
use WPMailSMTP\MailCatcherInterface;
use WPMailSMTP\Pro\Migration;
use WPMailSMTP\Providers\MailerAbstract;
use WPMailSMTP\Options as PluginOptions;
use WPMailSMTP\WP;

/**
 * Class Mailer implements Mailer functionality.
 *
 * @since 1.5.0
 */
class Mailer extends MailerAbstract {

	/**
	 * Regular message max body size.
	 * This is due to a limit of 4 MB on payload size for graph.microsoft.com endpoint.
	 *
	 * @since 3.4.0
	 *
	 * @var int
	 */
	const REGULAR_MESSAGE_MAX_BODY_SIZE = 1048576 * 4; // 4 MB.

	/**
	 * Email request body.
	 *
	 * @since 1.5.0
	 *
	 * @var array
	 */
	protected $body = [
		'message'         => [],
		'saveToSentItems' => true,
	];

	/**
	 * Which response code from HTTP provider is considered to be successful?
	 *
	 * @since 1.5.0
	 *
	 * @var int
	 */
	protected $email_sent_code = 202;

	/**
	 * URL to make an API request to.
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	protected $url = 'https://graph.microsoft.com/v1.0/me/sendMail';

	/**
	 * Email attachments.
	 *
	 * @since 3.4.0
	 *
	 * @var array PHPMailer attachments array.
	 */
	private $attachments = [];

	/**
	 * Mailer constructor.
	 *
	 * @since 1.5.0
	 *
	 * @param MailCatcherInterface $phpmailer The MailCatcher object.
	 */
	public function __construct( $phpmailer ) {

		// Init the client that checks tokens and re-saves them if needed.
		new Auth();

		// We want to prefill everything from MailCatcher class, which extends PHPMailer.
		parent::__construct( $phpmailer );

		$token = $this->options->get( $this->mailer, 'access_token' );

		if ( ! empty( $token['access_token'] ) ) {
			$this->set_header( 'Authorization', 'Bearer ' . $token['access_token'] );
		}
		$this->set_header( 'content-type', 'application/json' );

		/**
		 * Filters API send email url.
		 *
		 * @since 2.8.0
		 *
		 * @param string $url API sent email url.
		 */
		$this->url = apply_filters( 'wp_mail_smtp_pro_providers_outlook_mailer_api_url', $this->url );
	}

	/**
	 * PhpMailer generates certain headers, including our custom.
	 * We want to preserve them when sending an email.
	 * Thus we need to custom process them and add to message body headers.
	 *
	 * @since 1.5.0
	 *
	 * @param array $headers
	 */
	public function set_headers( $headers ) {

		foreach ( $headers as $header ) {
			$name  = isset( $header[0] ) ? $header[0] : false;
			$value = isset( $header[1] ) ? $header[1] : false;

			$this->set_body_header( $name, $value );
		}

		// Add custom PHPMailer-specific header.
		$this->set_body_header( 'X-Mailer', 'WPMailSMTP/Mailer/' . $this->mailer . ' ' . WPMS_PLUGIN_VER );
	}

	/**
	 * MS Graph object is nested inside 'message'.
	 *
	 * @since 1.5.0
	 *
	 * @param array $param
	 */
	public function set_body_param( $param ) {

		$this->body['message'] = PluginOptions::array_merge_recursive( $this->body['message'], $param );
	}

	/**
	 * We are allowed to provide custom header for emails.
	 *
	 * @see   https://docs.microsoft.com/en-us/graph/api/resources/internetmessageheader?view=graph-rest-1.0
	 *
	 * @since 1.5.0
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function set_body_header( $name, $value ) {

		$is_duplicated = false;

		$name = sanitize_text_field( $name );
		if ( empty( $name ) ) {
			return;
		}

		// Email will not be sent if the header's name is not prepended with 'X-'.
		if ( ! in_array( substr( $name, 0, 2 ), [ 'x-', 'X-' ], true ) ) {
			$name = 'X-' . $name;
		}

		$headers = isset( $this->body['message']['internetMessageHeaders'] ) ? (array) $this->body['message']['internetMessageHeaders'] : array();

		// Do not allow duplicate names.
		foreach ( $headers as $header ) {

			if ( $header['name'] === $name ) {
				$is_duplicated = true;
			}
		}

		if ( $is_duplicated ) {
			return;
		}

		$this->set_body_param(
			array(
				'internetMessageHeaders' => [
					[
						'name'  => $name,
						'value' => WP::sanitize_value( $value ),
					],
				],
			)
		);
	}

	/**
	 * Define the FROM (name and email) and SENDER.
	 * The authorized user should have permission to send an email from the defined email address.
	 *
	 * @see   https://docs.microsoft.com/en-us/graph/api/resources/recipient?view=graph-rest-1.0
	 * @see   https://docs.microsoft.com/en-us/graph/outlook-send-mail-from-other-user
	 *
	 * @since 1.5.0
	 * @since 3.4.0 Allow custom email address.
	 *
	 * @param string $email From mail.
	 * @param string $name  From name.
	 */
	public function set_from( $email, $name = '' ) {

		if ( $this->is_legacy_from() ) {
			$this->set_legacy_from();

			return;
		}

		if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			return;
		}

		$from['address'] = $email;

		if ( ! empty( $name ) ) {
			$from['name'] = $name;
		}

		$this->set_body_param(
			[
				'from'   => [ 'emailAddress' => $from ],
				'sender' => [ 'emailAddress' => $from ],
			]
		);
	}

	/**
	 * Define the CC/BCC/TO (with names and emails).
	 *
	 * @see   https://docs.microsoft.com/en-us/graph/api/resources/recipient?view=graph-rest-1.0
	 *
	 * @since 1.5.0
	 *
	 * @param array $recipients
	 */
	public function set_recipients( $recipients ) {

		if ( empty( $recipients ) ) {
			return;
		}

		// Allow for now only these recipient types.
		$default = array( 'to', 'cc', 'bcc' );
		$data    = array();

		foreach ( $recipients as $type => $emails ) {
			if (
				! in_array( $type, $default, true ) ||
				empty( $emails ) ||
				! is_array( $emails )
			) {
				continue;
			}

			$type_id = $type . 'Recipients';

			$data[ $type_id ] = array();

			// Iterate over all emails for each type.
			// There might be multiple to/cc/bcc emails.
			foreach ( $emails as $email ) {
				$holder = array();
				$addr   = isset( $email[0] ) ? $email[0] : false;
				$name   = isset( $email[1] ) ? $email[1] : false;

				if ( ! filter_var( $addr, FILTER_VALIDATE_EMAIL ) ) {
					continue;
				}

				$holder['address'] = $addr;
				if ( ! empty( $name ) ) {
					$holder['name'] = $name;
				}

				array_push( $data[ $type_id ], array( 'emailAddress' => $holder ) );
			}
		}

		if ( ! empty( $data ) ) {
			foreach ( $data as $type_id => $type_data ) {
				$this->set_body_param(
					array(
						$type_id => $type_data,
					)
				);
			}
		}
	}

	/**
	 * Set the email content.
	 * MS Graph supports only 1 type at a time (no multipart emails),
	 * so in case of multipart we ignore the text/plain part.
	 *
	 * @see   https://docs.microsoft.com/en-us/graph/api/resources/itembody?view=graph-rest-1.0
	 *
	 * @since 1.5.0
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

			if ( ! empty( $content['html'] ) ) {
				$body = array(
					'contentType' => 'html',
					'content'     => $content['html'],
				);
			} else {
				$body = array(
					'contentType' => 'text',
					'content'     => $content['text'],
				);
			}

			$this->set_body_param(
				array(
					'body' => $body,
				)
			);
		} else {
			$body = array(
				'contentType' => 'html',
				'content'     => $content,
			);

			if ( $this->phpmailer->ContentType === 'text/plain' ) {
				$body['contentType'] = 'text';
			}

			$this->set_body_param(
				array(
					'body' => $body,
				)
			);
		}
	}

	/**
	 * Set Reply-To part of the message.
	 * This is not in email header, but in body>message.
	 *
	 * @see   https://docs.microsoft.com/en-us/graph/api/resources/recipient?view=graph-rest-1.0
	 *
	 * @since 1.5.0
	 *
	 * @param array $reply_to
	 */
	public function set_reply_to( $reply_to ) {

		if ( empty( $reply_to ) ) {
			return;
		}

		$data = array();

		foreach ( $reply_to as $key => $emails ) {
			if (
				empty( $emails ) ||
				! is_array( $emails )
			) {
				continue;
			}

			$addr = isset( $emails[0] ) ? $emails[0] : false;
			$name = isset( $emails[1] ) ? $emails[1] : false;

			if ( ! filter_var( $addr, FILTER_VALIDATE_EMAIL ) ) {
				continue;
			}

			$holder = array();

			$holder['address'] = $addr;
			if ( ! empty( $name ) ) {
				$holder['name'] = $name;
			}

			$data[] = array( 'emailAddress' => $holder );
		}

		if ( ! empty( $data ) ) {
			$this->set_body_param(
				array(
					'replyTo' => $data,
				)
			);
		}
	}

	/**
	 * MS Graph doesn't support sender or return_path params.
	 * So we do nothing.
	 *
	 * @since 1.5.0
	 *
	 * @param string $from_email
	 */
	public function set_return_path( $from_email ) {
	}

	/**
	 * Add attachments to the body.
	 * MS Graph accepts an array of files content in body, so we will include all files and send.
	 * Doesn't handle exceeding the limits etc, as this is done and reported by MS Graph API via errors.
	 *
	 * @see   https://docs.microsoft.com/en-us/graph/api/resources/attachment?view=graph-rest-1.0
	 *
	 * @since 1.5.0
	 *
	 * @param array $attachments The array of attachments data.
	 */
	public function set_attachments( $attachments ) {

		if ( empty( $attachments ) ) {
			return;
		}

		$this->attachments = $attachments;

		$data = $this->prepare_attachments( $attachments );

		if ( ! empty( $data ) ) {
			$this->set_body_param(
				[
					'hasAttachments' => true,
					'attachments'    => $data,
				]
			);
		}
	}

	/**
	 * Prepare attachments data for Outlook API.
	 *
	 * @since 3.4.0
	 *
	 * @param array $attachments Array of attachments.
	 *
	 * @return array
	 */
	private function prepare_attachments( $attachments ) {

		$data = [];

		foreach ( $attachments as $attachment ) {
			$file = $this->get_attachment_file_content( $attachment );

			if ( $file === false ) {
				continue;
			}

			$data[] = [
				'@odata.type'  => '#microsoft.graph.fileAttachment',
				'name'         => $this->get_attachment_file_name( $attachment ),
				// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
				'contentBytes' => base64_encode( $file ),
				'contentType'  => $attachment[4],
			];
		}

		return $data;
	}

	/**
	 * Redefine the way email body is returned.
	 * Microsoft Graph needs JSON object.
	 *
	 * @see   https://docs.microsoft.com/en-us/graph/api/resources/message?view=graph-rest-1.0
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_body() {

		return wp_json_encode( $this->get_body_raw() );
	}

	/**
	 * Get raw body.
	 *
	 * @since 3.4.0
	 *
	 * @return array
	 */
	private function get_body_raw() {

		return $this->process_body_headers_unique( parent::get_body() );
	}

	/**
	 * Outlook doesn't allow duplicated headers in emails.
	 * This will make sure that no duplicates are available.
	 * The last duplicate header will be preserved, the 1st one will be removed.
	 *
	 * @since 1.5.0
	 *
	 * @param array $body Email body will all email-related headers and attachments.
	 *
	 * @return array
	 */
	protected function process_body_headers_unique( $body ) {

		$headers    = isset( $body['message']['internetMessageHeaders'] ) ? $body['message']['internetMessageHeaders'] : array();
		$to_process = array();

		// Get keys and header name for all headers, with duplicates.
		foreach ( $headers as $key => $header_outer ) {
			$to_process[ $key ] = $header_outer['name'];
		}

		if ( ! empty( $to_process ) ) {
			// With this double flipping we remove duplicates,
			// preserving the last define value with the biggest key.
			$to_process = array_flip( array_flip( $to_process ) );

			// Now get headers without duplicates.
			$headers = array_filter( $headers, function( $key ) use ( $to_process ) {
				return isset( $to_process[ $key ] );
			}, ARRAY_FILTER_USE_KEY );
		}

		// Reset keys in headers array and assign back to body.
		$body['message']['internetMessageHeaders'] = array_values( $headers );

		return $body;
	}

	/**
	 * Send the email using Microsoft Graph API.
	 *
	 * @see   https://docs.microsoft.com/en-us/graph/api/user-sendmail?view=graph-rest-1.0
	 *
	 * @since 1.5.0
	 */
	public function send() {

		if ( $this->is_legacy_from() ) {
			/*
			 * Right now Outlook doesn't allow to redefine From and Sender email headers.
			 * It always uses the email address that was used to connect to its API.
			 * With code below we are making sure that Email Log archive and single Email Log
			 * have the save value for From email header.
			 */
			$sender = $this->options->get( $this->mailer, 'user_details' );

			if ( ! empty( $sender['email'] ) ) {
				$this->phpmailer->From   = $sender['email'];
				$this->phpmailer->Sender = $sender['email'];
			}
		}

		$large_message = false;

		if (
			! empty( $this->attachments ) &&
			Helpers::strsize( $this->get_body() ) > self::REGULAR_MESSAGE_MAX_BODY_SIZE
		) {
			$large_message = true;
		}

		/**
		 * Filters whether message large.
		 *
		 * @since 3.4.0
		 *
		 * @param bool           $large_message Whether message large.
		 * @param MailerAbstract $mailer        Mailer object.
		 */
		$large_message = apply_filters( 'wp_mail_smtp_pro_providers_outlook_mailer_send_large', $large_message, $this );

		if ( $large_message === true ) {
			$response = $this->send_large();
		} else {
			$response = $this->send_regular();
		}

		DebugEvents::add_debug(
			esc_html__( 'An email request was sent to the Microsoft Graph API.', 'wp-mail-smtp-pro' )
		);

		$this->process_response( $response );
	}

	/**
	 * Send regular email using Microsoft Graph API.
	 *
	 * @see   https://docs.microsoft.com/en-us/graph/api/user-sendmail
	 *
	 * @since 3.4.0
	 *
	 * @return array|\WP_Error
	 */
	private function send_regular() {

		return $this->remote_request(
			$this->url,
			[
				'headers' => $this->get_headers(),
				'body'    => $this->get_body(),
			]
		);
	}

	/**
	 * Send large email using Microsoft Graph API.
	 * Upload attachments in a separate requests.
	 *
	 * @see   https://docs.microsoft.com/en-us/graph/api/user-post-messages
	 * @see   https://docs.microsoft.com/en-us/graph/api/message-send
	 *
	 * @since 3.4.0
	 *
	 * @return array|\WP_Error
	 */
	private function send_large() {

		$body    = $this->get_body_raw();
		$message = $body['message'];

		// Attachments will be uploaded in a separate process.
		unset( $message['hasAttachments'] );
		unset( $message['attachments'] );

		// Create draft.
		$response = $this->remote_request(
			'https://graph.microsoft.com/v1.0/me/messages',
			[
				'headers' => $this->get_headers(),
				'body'    => wp_json_encode( $message ),
			]
		);

		if ( wp_remote_retrieve_response_code( $response ) !== 201 ) {
			return $response;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! isset( $body['id'] ) ) {
			return $response;
		}

		$message_id = $body['id'];

		// Upload attachments.
		$uploader = new AttachmentsUploader( $this );

		foreach ( $this->attachments as $attachment ) {
			$is_uploaded = $uploader->upload( $attachment, $message_id );

			if ( is_wp_error( $is_uploaded ) ) {
				DebugEvents::add_debug(
					__( 'Outlook attachment upload failed.', 'wp-mail-smtp-pro' ) . WP::EOL . $is_uploaded->get_error_message()
				);
			}
		}

		// Send email.
		return $this->remote_request(
			"https://graph.microsoft.com/v1.0/me/messages/{$message_id}/send",
			[
				'headers' => $this->get_headers(),
			]
		);
	}

	/**
	 * Process Outlook-specific response with a helpful error.
	 *
	 * @see   https://docs.microsoft.com/en-us/graph/errors
	 * @see   https://docs.microsoft.com/en-us/azure/active-directory/develop/reference-aadsts-error-codes
	 * @see   https://docs.microsoft.com/en-us/exchange/client-developer/web-service-reference/responsecode
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_response_error() {

		$error_text[] = $this->error_message;

		if ( ! empty( $this->response ) ) {
			$body = wp_remote_retrieve_body( $this->response );

			if ( ! empty( $body->error->message ) ) {
				$message     = $body->error->message;
				$code        = ! empty( $body->error->code ) ? $body->error->code : '';
				$description = '';

				if ( $code === 'ErrorAccessDenied' ) {
					$description = esc_html__( 'Note: this issue could also be caused by hitting the total message size limit. If you are using big attachments, please remove the existing Outlook mailer connection in WP Mail SMTP settings and connect it again. We recently added support for bigger attachments, but the oAuth re-connection is required.', 'wp-mail-smtp-pro' );
				}

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
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_debug_info() {

		$mg_text = array();

		$auth = new Auth();

		$mg_text[] = '<strong>App ID/Pass:</strong> ' . ( $auth->is_clients_saved() ? 'Yes' : 'No' );
		$mg_text[] = '<strong>Tokens:</strong> ' . ( ! $auth->is_auth_required() ? 'Yes' : 'No' );

		return implode( '<br>', $mg_text );
	}

	/**
	 * Whether the mailer has all its settings correctly set up and saved.
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	public function is_mailer_complete() {

		if ( ! $this->is_php_compatible() ) {
			return false;
		}

		$auth = new Auth();

		if (
			$auth->is_clients_saved() &&
			! $auth->is_auth_required()
		) {
			return true;
		}

		return false;
	}

	/**
	 * Whether legacy from email address should be used.
	 *
	 * @since 3.4.0
	 *
	 * @return bool
	 */
	private function is_legacy_from() {

		/**
		 * Filters whether to use legacy from email address.
		 *
		 * @since 3.4.0
		 *
		 * @param bool $is_legacy_from Whether to use legacy from email address.
		 */
		return apply_filters(
			'wp_mail_smtp_pro_providers_outlook_mailer_is_legacy_from',
			Migration::get_current_version() < 1
		);
	}

	/**
	 * Define the FROM (name and email) and SENDER.
	 * It doesn't support random email, should be the same as used for connection.
	 *
	 * @since 3.4.0
	 */
	private function set_legacy_from() {

		$sender = $this->options->get( $this->mailer, 'user_details' );

		$email_address = [
			'emailAddress' => [
				'name'    => isset( $sender['display_name'] ) ? sanitize_text_field( $sender['display_name'] ) : '',
				'address' => isset( $sender['email'] ) ? $sender['email'] : '',
			],
		];

		// The FROM and SENDER parameters are defined the same way, so use above data for both.
		$this->set_body_param(
			[
				'from'   => $email_address,
				'sender' => $email_address,
			]
		);
	}

	/**
	 * Get the default email addresses for the reply to email parameter.
	 *
	 * @deprecated 2.1.1
	 *
	 * @since 2.1.0
	 * @since 2.1.1 Not used anymore.
	 *
	 * @return array
	 */
	public function default_reply_to_addresses() {

		_deprecated_function( __CLASS__ . '::' . __METHOD__, '2.1.1 of WP Mail SMTP plugin' );

		$sender = $this->options->get( $this->mailer, 'user_details' );

		return [
			$sender['email'] => [
				$sender['email'],
				sanitize_text_field( $sender['display_name'] ),
			],
		];
	}
}
