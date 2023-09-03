<?php
namespace WPMailSMTP\Pro\Emails\Logs\DeliveryVerification;

use Exception;
use WP_Error;
use WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\Mailgun\DeliveryVerifier as MailgunDeliveryVerifier;
use WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\Postmark\DeliveryVerifier as PostmarkDeliveryVerifier;
use WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\Sendlayer\DeliveryVerifier as SendlayerDeliveryVerifier;
use WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\SMTPcom\DeliveryVerifier as SMTPcomDeliveryVerifier;
use WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\SparkPost\DeliveryVerifier as SparkPostDeliveryVerifier;
use WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\Sendinblue\DeliveryVerifier as SendinblueDeliveryVerifier;
use WPMailSMTP\Pro\Emails\Logs\Email;
use WPMailSMTP\Pro\Tasks\Logs\Mailgun\VerifySentStatusTask as MailgunVerifySentStatusTask;
use WPMailSMTP\Pro\Tasks\Logs\Postmark\VerifySentStatusTask as PostmarkVerifySentStatusTask;
use WPMailSMTP\Pro\Tasks\Logs\Sendinblue\VerifySentStatusTask as SendinblueVerifySentStatusTask;
use WPMailSMTP\Pro\Tasks\Logs\Sendlayer\VerifySentStatusTask as SendlayerVerifySentStatusTask;
use WPMailSMTP\Pro\Tasks\Logs\SMTPcom\VerifySentStatusTask as SMTPcomVerifySentStatusTask;
use WPMailSMTP\Pro\Tasks\Logs\SparkPost\VerifySentStatusTask as SparkPostVerifySentStatusTask;
use WPMailSMTP\Pro\Tasks\Logs\VerifySentStatusTaskAbstract;

/**
 * Class DeliveryVerification.
 *
 * @since 3.9.0
 */
class DeliveryVerification {

	/**
	 * Delivery verifiers.
	 *
	 * @since 3.9.0
	 *
	 * @var AbstractDeliveryVerifier[]
	 */
	const DELIVERY_VERIFIERS_PER_MAILER = [
		'mailgun'    => MailgunDeliveryVerifier::class,
		'postmark'   => PostmarkDeliveryVerifier::class,
		'sendlayer'  => SendlayerDeliveryVerifier::class,
		'smtpcom'    => SMTPcomDeliveryVerifier::class,
		'sparkpost'  => SparkPostDeliveryVerifier::class,
		'sendinblue' => SendinblueDeliveryVerifier::class,
	];

	/**
	 * Verify sent status tasks classes.
	 *
	 * @since 3.9.0
	 *
	 * @var VerifySentStatusTaskAbstract[]
	 */
	const VERIFY_TASKS_PER_MAILER = [
		'mailgun'    => MailgunVerifySentStatusTask::class,
		'postmark'   => PostmarkVerifySentStatusTask::class,
		'sendlayer'  => SendlayerVerifySentStatusTask::class,
		'smtpcom'    => SMTPcomVerifySentStatusTask::class,
		'sparkpost'  => SparkPostVerifySentStatusTask::class,
		'sendinblue' => SendinblueVerifySentStatusTask::class,
	];

	/**
	 * Get the verifier for a given email log ID.
	 *
	 * @since 3.9.0
	 *
	 * @param int $email_id Email Log ID.
	 *
	 * @return WP_Error|AbstractDeliveryVerifier Returns the DeliveryVerifier instance or false if not found.
	 */
	public function get_verifier( $email_id ) {

		$email = new Email( $email_id );

		// Check if email exists (was not deleted).
		if ( $email->get_id() === 0 ) {
			return new WP_Error(
				'wp_mail_smtp_pro_emails_logs_delivery_verification_get_verifier_no_email',
				esc_html__( 'Unable to find email.', 'wp-mail-smtp-pro' )
			);
		}

		$mailer = $email->get_mailer();

		if ( empty( $mailer ) || ! isset( self::DELIVERY_VERIFIERS_PER_MAILER[ $mailer ] ) ) {
			return new WP_Error(
				'wp_mail_smtp_pro_emails_logs_delivery_verification_get_verifier_invalid_mailer',
				esc_html__( 'Invalid mailer.', 'wp-mail-smtp-pro' )
			);
		}

		$mailer_class = self::DELIVERY_VERIFIERS_PER_MAILER[ $mailer ];

		try {
			$verifier = new $mailer_class( $email );
		} catch ( Exception $e ) {
			return new WP_Error(
				'wp_mail_smtp_pro_emails_logs_delivery_verification_get_verifier_invalid_mailer',
				$e->getMessage()
			);
		}

		if ( ! is_a( $verifier, AbstractDeliveryVerifier::class ) ) {
			return new WP_Error(
				'wp_mail_smtp_pro_emails_logs_delivery_verification_get_verifier_invalid_verifier',
				esc_html__( 'Invalid verifier.', 'wp-mail-smtp-pro' )
			);
		}

		return $verifier;
	}

	/**
	 * Create AS task that will verify the email delivery status.
	 *
	 * @since 3.9.0
	 *
	 * @param int $email_log_id Email Log ID.
	 *
	 * @return void
	 */
	public function schedule_verification( $email_log_id ) {

		// Get the email.
		$email = new Email( $email_log_id );

		// Check if email exists (was not deleted).
		if ( $email->get_id() === 0 ) {
			return;
		}

		$mailer = $email->get_mailer();

		if ( ! isset( self::VERIFY_TASKS_PER_MAILER[ $mailer ] ) ) {
			return;
		}

		$as_class = self::VERIFY_TASKS_PER_MAILER[ $mailer ];
		$as_task  = new $as_class();

		if ( ! is_a( $as_task, VerifySentStatusTaskAbstract::class ) ) {
			return;
		}

		$as_task->params( $email_log_id, 1 )
			->once( time() + $as_task::SCHEDULE_TASK_IN )
			->register();
	}
}
