<?php

namespace WPMailSMTP\Pro\Tasks\Logs\SMTPcom;

use WPMailSMTP\Pro\Tasks\Logs\VerifySentStatusTaskAbstract;

/**
 * Class VerifySentStatusTask for the SMTP.com mailer.
 *
 * @since 2.5.0
 */
class VerifySentStatusTask extends VerifySentStatusTaskAbstract {

	/**
	 * Action name for this task.
	 *
	 * @since 2.5.0
	 */
	const ACTION = 'wp_mail_smtp_verify_sent_status_smtpcom';

	/**
	 * Number of seconds in the future to schedule the background task in.
	 *
	 * @since 2.5.0
	 */
	const SCHEDULE_TASK_IN = 480;
}
