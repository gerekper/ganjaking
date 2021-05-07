<?php

namespace WPMailSMTP\Pro\Emails\Logs\Export;

use WPMailSMTP\WP;
use WPMailSMTP\Pro\Emails\Logs\Email;

/**
 * Email logs export data.
 *
 * @since 2.8.0
 */
abstract class AbstractData {

	/**
	 * Export request.
	 *
	 * @since 2.8.0
	 *
	 * @var Request
	 */
	protected $request;

	/**
	 * Constructor.
	 *
	 * @since 2.8.0
	 *
	 * @param Request $request Export request.
	 */
	public function __construct( $request ) {

		$this->request = $request;
	}

	/**
	 * Get field value.
	 *
	 * @since 2.8.0
	 *
	 * @param string $key   Field key.
	 * @param Email  $email Email object.
	 *
	 * @return string
	 */
	public function get_field_value( $key, $email ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded, Generic.Metrics.NestingLevel.MaxExceeded

		$val = '';

		switch ( $key ) {

			case 'people_to':
				$val = ! empty( $email->get_people( 'to' ) ) ? implode( ', ', $email->get_people( 'to' ) ) : '';
				break;

			case 'people_from':
				$val = ! empty( $email->get_people( 'from' ) ) ? $email->get_people( 'from' ) : '';
				break;

			case 'people_from_name':
				$val = $email->get_from_name();
				break;

			case 'subject':
				$val = $email->get_subject();
				break;

			case 'content':
				$val = $email->get_content();
				break;

			case 'date_sent':
				$val = date_i18n(
					WP::datetime_format(),
					strtotime( get_date_from_gmt( $email->get_date_sent()->format( WP::datetime_mysql_format() ) ) )
				);
				break;

			case 'attachments_count':
				$val = (int) $email->get_attachments();
				break;

			case 'status':
				$val = $email->get_status_name();
				break;

			case 'error_text':
				$val = trim( $email->get_error_text() );
				break;

			case 'people_cc':
				$val = ! empty( $email->get_people( 'cc' ) ) ? implode( ',', $email->get_people( 'cc' ) ) : '';
				break;

			case 'people_bcc':
				$val = ! empty( $email->get_people( 'bcc' ) ) ? implode( ',', $email->get_people( 'bcc' ) ) : '';
				break;

			case 'headers':
				$val = trim( implode( "\r\n", (array) json_decode( $email->get_headers() ) ) );
				break;

			case 'mailer':
				if ( ! empty( $email->get_mailer() ) ) {
					$provider = wp_mail_smtp()->get_providers()->get_options( $email->get_mailer() );
					if ( $provider !== null ) {
						$val = $provider->get_title();
					} else {
						$val = $email->get_mailer();
					}
				}
				break;

			case 'log_id':
				$val = $email->get_id();
				break;
		}

		/**
		 * Filters export field value.
		 *
		 * @since 2.8.0
		 *
		 * @param mixed  $val   Field value.
		 * @param string $key   Field key.
		 * @param Email  $email Current email.
		 */
		return apply_filters( 'wp_mail_smtp_pro_emails_logs_export_data_get_field_value', $val, $key, $email );
	}
}
