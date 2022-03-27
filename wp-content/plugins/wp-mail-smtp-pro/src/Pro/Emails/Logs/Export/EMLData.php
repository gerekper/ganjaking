<?php

namespace WPMailSMTP\Pro\Emails\Logs\Export;

use WPMailSMTP\MailCatcherInterface;
use WPMailSMTP\Pro\Emails\Logs\Attachments\Attachment;
use WPMailSMTP\Pro\Emails\Logs\Attachments\Attachments;
use WPMailSMTP\Pro\Emails\Logs\Email;
use WPMailSMTP\Pro\Emails\Logs\EmailsCollection;

/**
 * Email logs export data in EML format.
 *
 * @since 2.9.0
 */
class EMLData extends AbstractData {

	/**
	 * Get single email EML file content.
	 *
	 * @since 2.9.0
	 *
	 * @return \Generator
	 */
	public function get_content() {

		$emails = new EmailsCollection( $this->request->get_data( 'db_args' ) );

		foreach ( $emails->get() as $email ) {

			try {
				$phpmailer = $this->prepare_phpmailer( $email );
				$phpmailer->preSend();
				$content = $phpmailer->getSentMIMEMessage();
			} catch ( \Exception $e ) {

				$this->request->add_notice(
					sprintf( /* translators: %1$d - email log ID; %2$s - error message. */
						esc_html__( 'Email #%1$s was skipped. Reason: %2$s.', 'wp-mail-smtp-pro' ),
						$email->get_id(),
						$e->getMessage()
					),
					'warning'
				);
				continue;
			}

			/**
			 * Filters export table data row.
			 *
			 * @since 2.9.0
			 *
			 * @param array     $row   Row.
			 * @param Email     $email Current email.
			 * @param TableData $data  Data.
			 */
			$content = apply_filters( 'wp_mail_smtp_pro_emails_logs_export_eml_data_get_content', $content, $email, $this );

			yield [ $email, $content ];
		}
	}

	/**
	 * Fill in all required PHPMailer properties from email log.
	 *
	 * @since 2.9.0
	 *
	 * @param Email $email Email.
	 *
	 * @return MailCatcherInterface
	 */
	protected function prepare_phpmailer( Email $email ) {

		$phpmailer = wp_mail_smtp()->generate_mail_catcher( true );

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$phpmailer->AllowEmpty  = true;
		$phpmailer->MessageDate = $email->get_header( 'Date' );
		$phpmailer->MessageID   = $email->get_header( 'Message-ID' );
		$phpmailer->XMailer     = $email->get_header( 'X-Mailer' );

		$content_type = $email->get_content_type();

		if ( 'text/html' === $content_type ) {
			$phpmailer->isHTML( true );
		}

		$phpmailer->ContentType = $content_type;
		$phpmailer->CharSet     = $email->get_charset();
		$phpmailer->Subject     = $this->get_field_value( 'subject', $email );
		$phpmailer->Body        = $this->get_field_value( 'content', $email );
		// phpcs:enable

		$this->set_addresses( $email, $phpmailer );
		$this->set_other_addresses( $email, $phpmailer );
		$this->set_custom_headers( $email, $phpmailer );
		$this->set_attachments( $email, $phpmailer );

		return $phpmailer;
	}

	/**
	 * Set email from and to addresses.
	 *
	 * @since 2.9.0
	 *
	 * @param Email                $email     Email.
	 * @param MailCatcherInterface $phpmailer PHPMailer instance.
	 */
	protected function set_addresses( $email, $phpmailer ) {

		$phpmailer->setFrom(
			$this->get_field_value( 'people_from', $email ),
			$this->get_field_value( 'people_from_name', $email ),
			false
		);

		$phpmailer->addAddress( $this->get_field_value( 'people_to', $email ) );
	}

	/**
	 * Set email cc, bcc and reply to addresses.
	 *
	 * @since 2.9.0
	 *
	 * @param Email                $email     Email.
	 * @param MailCatcherInterface $phpmailer PHPMailer instance.
	 */
	protected function set_other_addresses( $email, $phpmailer ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh, Generic.Metrics.NestingLevel.MaxExceeded

		$address_headers = [
			'cc'       => array_filter( explode( ',', $this->get_field_value( 'header_cc', $email ) ) ),
			'bcc'      => array_filter( explode( ',', $this->get_field_value( 'header_bcc', $email ) ) ),
			'reply_to' => array_filter( explode( ',', $this->get_field_value( 'header_reply_to', $email ) ) ),
		];

		foreach ( $address_headers as $address_header => $addresses ) {
			if ( empty( $addresses ) ) {
				continue;
			}

			foreach ( (array) $addresses as $address ) {

				$recipient_name = '';

				if ( preg_match( '/(.*)<(.+)>/', $address, $matches ) ) {
					if ( count( $matches ) === 3 ) {
						$recipient_name = $matches[1];
						$address        = $matches[2];
					}
				}

				switch ( $address_header ) {
					case 'cc':
						$phpmailer->addCc( $address, $recipient_name );
						break;
					case 'bcc':
						$phpmailer->addBcc( $address, $recipient_name );
						break;
					case 'reply_to':
						$phpmailer->addReplyTo( $address, $recipient_name );
						break;
				}
			}
		}
	}

	/**
	 * Set email custom headers.
	 *
	 * @since 2.9.0
	 *
	 * @param Email                $email     Email.
	 * @param MailCatcherInterface $phpmailer PHPMailer instance.
	 */
	protected function set_custom_headers( $email, $phpmailer ) {

		$headers = (array) json_decode( $email->get_headers() );

		// Exclude from custom headers.
		$exclude = [
			'Date'         => true,
			'To'           => true,
			'From'         => true,
			'Cc'           => true,
			'Bcc'          => true,
			'Reply-To'     => true,
			'Subject'      => true,
			'Message-ID'   => true,
			'X-Mailer'     => true,
			'MIME-Version' => true,
			'Content-Type' => true,
		];

		foreach ( $headers as $header ) {
			if ( strpos( $header, ':' ) === false ) {
				continue;
			}

			list( $name, $content ) = explode( ':', trim( $header ), 2 );

			$name    = trim( $name );
			$content = trim( $content );

			if ( ! isset( $exclude[ $name ] ) ) {
				$phpmailer->addCustomHeader( sprintf( '%1$s: %2$s', $name, $content ) );
			}
		}
	}

	/**
	 * Set email attachments.
	 *
	 * @since 2.9.0
	 *
	 * @param Email                $email     Email.
	 * @param MailCatcherInterface $phpmailer PHPMailer instance.
	 */
	protected function set_attachments( $email, $phpmailer ) {

		/** Array of Attachment objects. @var Attachment[] $attachments */
		$attachments = ( new Attachments() )->get_attachments( $email->get_id() );

		if ( empty( $attachments ) ) {
			return;
		}

		foreach ( $attachments as $attachment ) {
			try {
				$phpmailer->addAttachment( $attachment->get_path(), $attachment->get_filename() );
			} catch ( \Exception $e ) {
				continue;
			}
		}
	}

	/**
	 * Get field value.
	 *
	 * @since 2.9.0
	 *
	 * @param string $key   Field key.
	 * @param Email  $email Email object.
	 *
	 * @return string
	 */
	public function get_field_value( $key, $email ) {

		switch ( $key ) {
			case 'header_cc':
				$val = $email->get_header( 'Cc' );
				break;

			case 'header_bcc':
				$val = $email->get_header( 'Bcc' );
				break;

			case 'header_reply_to':
				$val = $email->get_header( 'Reply-To' );
				break;

			default:
				$val = parent::get_field_value( $key, $email );
		}

		/**
		 * Filters export field value.
		 *
		 * @since 2.9.0
		 *
		 * @param mixed  $val   Field value.
		 * @param string $key   Field key.
		 * @param Email  $email Current email.
		 */
		return apply_filters( 'wp_mail_smtp_pro_emails_logs_export_eml_data_get_field_value', $val, $key, $email );
	}
}
