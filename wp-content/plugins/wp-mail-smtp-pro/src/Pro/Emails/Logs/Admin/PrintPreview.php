<?php

namespace WPMailSMTP\Pro\Emails\Logs\Admin;

use WPMailSMTP\WP;
use WPMailSMTP\Admin\Area;
use WPMailSMTP\Pro\Emails\Logs\Email;

/**
 * Print preview for email.
 *
 * @since 2.8.0
 */
class PrintPreview {

	/**
	 * Email object.
	 *
	 * @since 2.8.0
	 *
	 * @var Email
	 */
	protected $email;

	/**
	 * Hooks.
	 *
	 * @since 2.8.0
	 */
	public function hooks() {

		add_action( 'admin_init', [ $this, 'print_html' ], 1 );
	}

	/**
	 * Check if current page request meets requirements for email print preview page.
	 *
	 * @since 2.8.0
	 *
	 * @return bool
	 */
	public function is_print_page() {

		$mode     = isset( $_GET['mode'] ) ? sanitize_key( $_GET['mode'] ) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$email_id = isset( $_GET['email_id'] ) ? absint( $_GET['email_id'] ) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( ! wp_mail_smtp()->get_admin()->is_admin_page( 'logs' ) || $mode !== 'print' || ! $email_id ) {
			return false;
		}

		$this->email = new Email( $email_id );

		// Check is valid email was found.
		if ( $this->email->get_id() === 0 ) {
			return false;
		}

		return true;
	}

	/**
	 * Output HTML markup for the email print preview page.
	 *
	 * @since 2.8.0
	 */
	public function print_html() {

		if ( ! $this->is_print_page() ) {
			return;
		}

		if ( ! current_user_can( wp_mail_smtp()->get_admin()->get_logs_access_capability() ) ) {
			wp_die( esc_html__( 'Access rejected.', 'wp-mail-smtp-pro' ) );
		}

		?>
		<!doctype html>
		<html>
		<head>
			<meta charset="<?php bloginfo( 'charset' ); ?>">
			<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
			<title><?php esc_html_e( 'WP Mail SMTP - Email Details', 'wp-mail-smtp-pro' ); ?></title>
			<meta name="description" content="">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<meta name="robots" content="noindex,nofollow,noarchive">
			<?php // phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet, WordPress.WP.EnqueuedResources.NonEnqueuedScript, WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<link rel="stylesheet" href="<?php echo esc_url( includes_url( 'css/buttons.min.css' ) ); ?>" type="text/css">
			<link rel="stylesheet" href="<?php echo esc_url( wp_mail_smtp()->plugin_url . '/assets/pro/css/smtp-pro-logs-print.min.css' ); ?>" type="text/css">
			<script type="text/javascript" src="<?php echo esc_url( includes_url( 'js/utils.js' ) ); ?>"></script>
			<script type="text/javascript" src="<?php echo esc_url( includes_url( 'js/jquery/jquery.js' ) ); ?>"></script>
			<?php // phpcs:enable ?>
			<script type="text/javascript">
				jQuery(function ($) {
					var showCompact = false;
					// Print page.
					$(document).on('click', '.print', function (event) {
						event.preventDefault();
						window.print();
					});
					// Close page.
					$(document).on('click', '.close-window', function (event) {
						event.preventDefault();
						window.close();
					});
					// Toggle compact view.
					$(document).on('click', '.toggle-view', function (event) {
						event.preventDefault();
						if (!showCompact) {
							$(this).text('<?php esc_html_e( 'Normal view', 'wp-mail-smtp-pro' ); ?>');
						} else {
							$(this).text('<?php esc_html_e( 'Compact view', 'wp-mail-smtp-pro' ); ?>');
						}
						$('#print').toggleClass('compact');
						showCompact = !showCompact;
					});
				});

				// Function for adjust iframe height to iframe content height.
				function resizeIframe(obj) {
					obj.style.height = (obj.contentWindow.document.documentElement.scrollHeight + 2) + 'px';
				}
			</script>

			<?php
			/**
			 * Fires on email print preview page in head.
			 *
			 * @since 2.8.0
			 *
			 * @param Email $email Email object.
			 */
			do_action( 'wp_mail_smtp_pro_emails_logs_admin_printpreview_print_html_head', $this->email );
			?>

		</head>
		<body class="wp-core-ui">
		<div id="print">

			<?php
			/**
			 * Fires on email print preview page before header.
			 *
			 * @since 2.8.0
			 *
			 * @param Email $email Email object.
			 */
			do_action( 'wp_mail_smtp_pro_emails_logs_admin_printpreview_print_html_header_before', $this->email );
			?>

			<h1>&nbsp;
				<div class="buttons">
					<a href="#" class="button button-secondary close-window"><?php esc_html_e( 'Close', 'wp-mail-smtp-pro' ); ?></a>
					<a href="#" class="button button-primary print"><?php esc_html_e( 'Print', 'wp-mail-smtp-pro' ); ?></a>
				</div>
			</h1>
			<div class="actions">
				<a href="#" class="toggle-view"><?php esc_html_e( 'Compact view', 'wp-mail-smtp-pro' ); ?></a>
			</div>

			<?php
			/**
			 * Fires on email print preview page after header.
			 *
			 * @since 2.8.0
			 *
			 * @param Email $email Email object.
			 */
			do_action( 'wp_mail_smtp_pro_emails_logs_admin_printpreview_print_html_header_after', $this->email );
			?>

			<div class="fields">
				<!-- Date sent. -->
				<div class="field">
					<p class="field-name"><?php esc_html_e( 'Created', 'wp-mail-smtp-pro' ); ?></p>
					<p class="field-value">
						<?php
						echo esc_html(
							date_i18n(
								WP::datetime_format(),
								strtotime( get_date_from_gmt( $this->email->get_date_sent()->format( WP::datetime_mysql_format() ) ) )
							)
						);
						?>
					</p>
				</div>

				<!-- Sent FROM. -->
				<div class="field">
					<p class="field-name"><?php esc_html_e( 'From', 'wp-mail-smtp-pro' ); ?></p>
					<p class="field-value">
						<?php
						$data = $this->email->get_people( 'from' );

						if ( ! empty( $data ) ) {
							$people_from = $data;
						} else {
							$people_from = esc_html__( 'N/A', 'wp-mail-smtp-pro' );
						}
						echo esc_html( $people_from );
						?>
					</p>
				</div>

				<!-- Sent TO. -->
				<div class="field">
					<p class="field-name"><?php esc_html_e( 'To', 'wp-mail-smtp-pro' ); ?></p>
					<p class="field-value">
						<?php
						$data = $this->email->get_people( 'to' );

						if ( ! empty( $data ) ) {
							$people_to = implode( ', ', $data );
						} else {
							$people_to = esc_html__( 'N/A', 'wp-mail-smtp-pro' );
						}
						echo esc_html( $people_to );
						?>
					</p>
				</div>

				<!-- Sent CC. -->
				<?php
				$data = $this->email->get_people( 'cc' );

				if ( ! empty( $data ) ) :
					?>
					<div class="field">
						<p class="field-name"><?php esc_html_e( 'Carbon Copy (CC)', 'wp-mail-smtp-pro' ); ?></p>
						<p class="field-value"><?php echo esc_html( implode( ', ', $data ) ); ?></p>
					</div>
				<?php endif; ?>

				<!-- Sent BCC. -->
				<?php
				$data = $this->email->get_people( 'bcc' );

				if ( ! empty( $data ) ) :
					?>
					<div class="field">
						<p class="field-name"><?php esc_html_e( 'Blind Carbon Copy (BCC)', 'wp-mail-smtp-pro' ); ?></p>
						<p class="field-value"><?php echo esc_html( implode( ', ', $data ) ); ?></p>
					</div>
				<?php endif; ?>

				<!-- Subject. -->
				<div class="field">
					<p class="field-name"><?php esc_html_e( 'Subject', 'wp-mail-smtp-pro' ); ?></p>
					<p class="field-value"><?php echo esc_html( $this->email->get_subject() ); ?></p>
				</div>

				<!-- Status. -->
				<div class="field">
					<p class="field-name"><?php esc_html_e( 'Status', 'wp-mail-smtp-pro' ); ?></p>
					<p class="field-value"><?php echo esc_html( $this->email->get_status_name() ); ?></p>
				</div>

				<!-- Mailer. -->
				<?php
				if ( ! empty( $this->email->get_mailer() ) ) :
					$provider = wp_mail_smtp()->get_providers()->get_options( $this->email->get_mailer() );
					?>
					<div class="field">
						<p class="field-name"><?php esc_html_e( 'Mailer', 'wp-mail-smtp-pro' ); ?></p>
						<p class="field-value">
							<?php
							if ( $provider !== null ) {
								$mailer_name = $provider->get_title();
							} else {
								$mailer_name = $this->email->get_mailer();
							}

							if ( $this->email->get_header( 'X-WP-Mail-SMTP-Connection-Type' ) === 'backup' ) {
								$mailer_name .= ' ' . esc_html__( '(backup)', 'wp-mail-smtp-pro' );
							}

							echo esc_html( $mailer_name );
							?>
						</p>
					</div>
				<?php endif; ?>

			</div>

			<?php
			/**
			 * Fires on email print preview page after details fields.
			 *
			 * @since 2.8.0
			 *
			 * @param Email $email Email object.
			 */
			do_action( 'wp_mail_smtp_pro_emails_logs_admin_printpreview_print_html_fields_after', $this->email );
			?>

			<?php
			if ( ! empty( $this->email->get_content() ) ) :
				$preview_url = add_query_arg(
					[
						'email_id' => $this->email->get_id(),
						'mode'     => 'preview',
					],
					wp_nonce_url(
						wp_mail_smtp()->get_admin()->get_admin_page_url( Area::SLUG . '-logs' ),
						'wp_mail_smtp_pro_logs_log_preview'
					)
				);
				?>
				<iframe src="<?php echo esc_url( $preview_url ); ?>"
						title="<?php esc_attr_e( 'Print preview email content', 'wp-mail-smtp-pro' ); ?>"
						frameborder="0" class="email-preview" onload="resizeIframe(this)"></iframe>
			<?php endif; ?>

			<?php
			/**
			 * Fires on email print preview page after email content.
			 *
			 * @since 2.8.0
			 *
			 * @param Email $email Email object.
			 */
			do_action( 'wp_mail_smtp_pro_emails_logs_admin_printpreview_print_html_content_after', $this->email );
			?>

		</div>
		<p class="site">
			<a href="<?php echo esc_url( home_url() ); ?>"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a>
		</p>
		</body>
		</html>
		<?php
		exit();
	}
}
