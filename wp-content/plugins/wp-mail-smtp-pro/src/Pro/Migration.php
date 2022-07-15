<?php

namespace WPMailSMTP\Pro;

use WPMailSMTP\MigrationAbstract;
use WPMailSMTP\Options;

/**
 * Class Migration helps migrate pro plugin options, DB tables and more.
 *
 * @since 3.4.0
 */
class Migration extends MigrationAbstract {

	/**
	 * Version of the latest migration.
	 *
	 * @since 3.4.0
	 */
	const DB_VERSION = 1;

	/**
	 * Option key where we save the current migration version.
	 *
	 * @since 3.4.0
	 */
	const OPTION_NAME = 'wp_mail_smtp_pro_migration_version';

	/**
	 * Set Force From Email if outlook mailer selected.
	 * In the previous version, Outlook didn't allow redefining From and Sender email headers.
	 * For now, custom From and Sender email headers can be used, but we need to force
	 * from email for all established/existing connections.
	 *
	 * @since 3.4.0
	 */
	protected function migrate_to_1() {

		$options = Options::init();
		$mailer  = $options->get( 'mail', 'mailer' );

		if ( $mailer === 'outlook' ) {
			$all = $options->get_all();

			// To save in DB.
			$all['mail']['from_email_force'] = true;

			$options->set( $all, false, true );
		}

		// Save the current version to DB.
		$this->update_db_ver( 1 );
	}
}
