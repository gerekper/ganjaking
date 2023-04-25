<?php

namespace WPMailSMTP\Pro\Emails\Logs\Admin;

use WPMailSMTP\Admin\Area;
use WPMailSMTP\Pro\Emails\Logs\Migration;
use WPMailSMTP\WP;

/**
 * Class PageAbstract to handle Logs pages specific needs.
 *
 * @since 1.5.0
 */
abstract class PageAbstract extends \WPMailSMTP\Admin\PageAbstract {

	/**
	 * @since 1.5.0
	 *
	 * @var string Slug of a page.
	 */
	protected $slug = 'logs';

	/**
	 * Title of a tab.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->get_label();
	}

	/**
	 * Get the page/tab link.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_link() {

		return add_query_arg(
			'page',
			Area::SLUG . '-' . $this->slug,
			WP::admin_url( 'admin.php' )
		);
	}

	/**
	 * Notify user that email logging is disabled.
	 *
	 * @since 1.5.0
	 */
	public function display_logging_disabled() {

		?>
		<div class="wp-mail-smtp-logs-note">
			<h2><?php esc_html_e( 'Email Log is Not Enabled', 'wp-mail-smtp-pro' ); ?></h2>
			<p>
				<?php
				esc_html_e( 'Emails sent when logging is disabled are not stored in the database and will not display when enabled. ', 'wp-mail-smtp-pro' );
				?>
			</p>
			<a href="<?php echo esc_url( wp_mail_smtp()->get_admin()->get_admin_page_url( Area::SLUG . '&tab=logs' ) ); ?>" class="wp-mail-smtp-btn wp-mail-smtp-btn-orange wp-mail-smtp-btn-md">
				<?php esc_html_e( 'Enable Email Log', 'wp-mail-smtp-pro' ); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Notify user that email logging is not installed correctly.
	 *
	 * @since 1.7.0
	 * @since 2.2.0 Display the error logged for the Email log table creation.
	 */
	public function display_logging_not_installed() {

		$error_message = get_option( Migration::ERROR_OPTION_NAME );

		$create_missing_tables_url = wp_nonce_url(
			add_query_arg(
				[
					'create-missing-db-tables' => 1,
				],
				wp_mail_smtp()->get_admin()->get_admin_page_url( Area::SLUG . '-logs' )
			),
			Area::SLUG . '-create-missing-db-tables'
		);

		$contact_support_url = wp_mail_smtp()->get_utm_url(
			'https://wpmailsmtp.com/account/support/',
			[
				'medium'  => 'email-logs',
				'content' => 'Email Log not installed correctly',
			]
		);
		?>

		<div class="wp-mail-smtp-logs-note errored">
			<h2><?php esc_html_e( 'Email Logging is Not Installed Correctly', 'wp-mail-smtp-pro' ); ?></h2>

			<p>
				<?php
				if ( ! empty( $error_message ) ) {
					echo wp_kses(
						sprintf( /* translators: %1$s - create missing tables link; %2$s - contact support link. */
							__( 'WP Mail SMTP is using custom database tables for some of its features. In order to work properly, the custom tables should be created, and it seems they are missing. Please try to <a href="%1$s">create the missing DB tables by clicking on this link</a>. If this issue persists, please <a href="%2$s" target="_blank" rel="noopener noreferrer">contact our support</a> and provide the error message bellow:', 'wp-mail-smtp-pro' ),
							esc_url( $create_missing_tables_url ),
							esc_url( $contact_support_url )
						),
						[
							'a' => [
								'href'   => [],
								'target' => [],
								'rel'    => [],
							],
						]
					);
					echo '<br><br>';
					echo '<code>' . esc_html( $error_message ) . '</code>';
				} else {
					echo wp_kses(
						sprintf( /* translators: %1$s - create missing tables link; %2$s - contact support link. */
							__( 'WP Mail SMTP is using custom database tables for some of its features. In order to work properly, the custom tables should be created, and it seems they are missing. Please try to <a href="%1$s">create the missing DB tables by clicking on this link</a>. If this issue persists, please <a href="%2$s" target="_blank" rel="noopener noreferrer">contact our support</a>.', 'wp-mail-smtp-pro' ),
							esc_url( $create_missing_tables_url ),
							esc_url( $contact_support_url )
						),
						[
							'a' => [
								'href'   => [],
								'target' => [],
								'rel'    => [],
							],
						]
					);
				}
				?>
				<br><br>
				<?php esc_html_e( 'Right now all sent emails are not logged.', 'wp-mail-smtp-pro' ); ?>
			</p>
		</div>

		<?php
	}
}
