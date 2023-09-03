<?php

namespace WPMailSMTP\Pro\Tasks\Logs\Sendlayer;

use WPMailSMTP\Pro\Tasks\Logs\VerifySentStatusTaskAbstract;

/**
 * Class VerifySentStatusTask for the SendLayer mailer.
 *
 * @since 3.4.0
 */
class VerifySentStatusTask extends VerifySentStatusTaskAbstract {

	/**
	 * Action name for this task.
	 *
	 * @since 3.4.0
	 */
	const ACTION = 'wp_mail_smtp_verify_sent_status_sendlayer';
}
