<?php

namespace WPMailSMTP\Pro\Tasks\Logs\Mailgun;

use WPMailSMTP\Pro\Tasks\Logs\VerifySentStatusTaskAbstract;

/**
 * Class VerifySentStatusTask for the Mailgun mailer.
 *
 * @since 2.5.0
 */
class VerifySentStatusTask extends VerifySentStatusTaskAbstract {

	/**
	 * Action name for this task.
	 *
	 * @since 2.5.0
	 */
	const ACTION = 'wp_mail_smtp_verify_sent_status_mailgun';
}
