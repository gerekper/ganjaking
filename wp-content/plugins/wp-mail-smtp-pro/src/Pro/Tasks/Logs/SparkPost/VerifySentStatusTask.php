<?php

namespace WPMailSMTP\Pro\Tasks\Logs\SparkPost;

use WPMailSMTP\Pro\Tasks\Logs\VerifySentStatusTaskAbstract;

/**
 * Class VerifySentStatusTask for the SparkPost mailer.
 *
 * @since 3.3.0
 */
class VerifySentStatusTask extends VerifySentStatusTaskAbstract {

	/**
	 * Action name for this task.
	 *
	 * @since 3.3.0
	 */
	const ACTION = 'wp_mail_smtp_verify_sent_status_sparkpost';
}
