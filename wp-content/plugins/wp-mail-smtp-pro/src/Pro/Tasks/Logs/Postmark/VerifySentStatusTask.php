<?php

namespace WPMailSMTP\Pro\Tasks\Logs\Postmark;

use WPMailSMTP\Pro\Tasks\Logs\VerifySentStatusTaskAbstract;

/**
 * Class VerifySentStatusTask for the Postmark mailer.
 *
 * @since 3.3.0
 */
class VerifySentStatusTask extends VerifySentStatusTaskAbstract {

	/**
	 * Action name for this task.
	 *
	 * @since 3.3.0
	 */
	const ACTION = 'wp_mail_smtp_verify_sent_status_postmark';
}
