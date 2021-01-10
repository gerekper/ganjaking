<?php

namespace WPMailSMTP\Pro\Emails\Logs\Admin;

use WPMailSMTP\Admin\Area;
use WPMailSMTP\Pro\Emails\Logs\Email;
use WPMailSMTP\WP;

/**
 * Class SinglePage displays a single email page content.
 *
 * @since 1.5.0
 */
class SinglePage extends PageAbstract {

	/**
	 * @since 1.5.0
	 *
	 * @var Email
	 */
	protected $email;

	/**
	 * Link label of a tab.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'View Email', 'wp-mail-smtp-pro' );
	}

	/**
	 * Tab content.
	 *
	 * @since 1.5.0
	 */
	public function display() {
		?>

		<div class="wp-mail-smtp-page-title">
			<span class="page-title">
				<?php echo esc_html( $this->get_label() ); ?>
			</span>

			<a href="<?php echo esc_url( wp_mail_smtp()->get_admin()->get_admin_page_url( Area::SLUG . '-logs' ) ); ?>" class="wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-orange action">
				<?php esc_html_e( 'Back to Email Log', 'wp-mail-smtp-pro' ); ?>
			</a>
		</div>

		<h1 class="screen-reader-text">
			<?php echo esc_html( $this->get_label() ); ?>
		</h1>

		<div class="wp-mail-smtp-page-content">

			<?php
			if ( wp_mail_smtp()->pro->get_logs()->is_enabled() ) {
				if ( ! wp_mail_smtp()->pro->get_logs()->is_valid_db() ) {
					$this->display_logging_not_installed();
				} else {
					$this->email = new Email( (int) $_GET['email_id'] ); // phpcs:ignore

					if ( $this->email->is_valid() ) {
						$this->display_content_main();
						$this->display_content_side();
					} else {
						$this->display_error_happened();
					}
				}
			} else {
				$this->display_logging_disabled();
			}
			?>

		</div>

		<?php
	}

	/**
	 * Display the main content of the page.
	 *
	 * @since 1.5.0
	 */
	public function display_content_main() {
		?>

		<div class="email-main">

			<div class="email-section email-people-details">
				<h2><?php esc_html_e( 'Email Details', 'wp-mail-smtp-pro' ); ?></h2>

				<ul>
					<!-- Date sent. -->
					<li class="subheading"><?php esc_html_e( 'Created', 'wp-mail-smtp-pro' ); ?></li>
					<li class="subcontent">
						<?php
						echo esc_html( date_i18n(
							WP::datetime_format(),
							strtotime( get_date_from_gmt( $this->email->get_date_sent()->format( WP::datetime_mysql_format() ) ) )
						) );
						?>
					</li>

					<!-- Sent FROM. -->
					<li class="subheading"><?php esc_html_e( 'From', 'wp-mail-smtp-pro' ); ?></li>
					<li class="subcontent">
						<?php
						$data = $this->email->get_people( 'from' );

						if ( ! empty( $data ) ) {
							$people_from = $data;
						} else {
							$people_from = esc_html__( 'N/A', 'wp-mail-smtp-pro' );
						}
						echo esc_html( $people_from );
						?>
					</li>

					<!-- Sent TO. -->
					<li class="subheading"><?php esc_html_e( 'To', 'wp-mail-smtp-pro' ); ?></li>
					<li class="subcontent">
						<?php
						$data = $this->email->get_people( 'to' );

						if ( ! empty( $data ) ) {
							$people_to = implode( ', ', $data );
						} else {
							$people_to = esc_html__( 'N/A', 'wp-mail-smtp-pro' );
						}
						echo esc_html( $people_to );
						?>
					</li>

					<!-- Sent CC. -->
					<?php
					$data = $this->email->get_people( 'cc' );

					if ( ! empty( $data ) ) :
						?>
						<li class="subheading"><?php esc_html_e( 'Carbon Copy (CC)', 'wp-mail-smtp-pro' ); ?></li>
						<li class="subcontent"><?php echo esc_html( implode( ', ', $data ) ); ?></li>
					<?php endif; ?>

					<!-- Sent BCC. -->
					<?php
					$data = $this->email->get_people( 'bcc' );

					if ( ! empty( $data ) ) :
						?>
						<li class="subheading"><?php esc_html_e( 'Blind Carbon Copy (BCC)', 'wp-mail-smtp-pro' ); ?></li>
						<li class="subcontent"><?php echo esc_html( implode( ', ', $data ) ); ?></li>
					<?php endif; ?>

					<!-- Subject. -->
					<li class="subheading"><?php esc_html_e( 'Subject', 'wp-mail-smtp-pro' ); ?></li>
					<li class="subcontent">
						<?php echo esc_html( $this->email->get_subject() ); ?>
					</li>

				</ul>
			</div>

			<div class="email-section email-extra-details">
				<h2 class="js-wp-mail-smtp-pro-logs-toggle-extra-details">
					<span class="extra-details-title">
						<?php esc_html_e( 'Technical Details', 'wp-mail-smtp-pro' ); ?>
						<?php if ( $this->email->has_error() ) : ?>
							<img class="error-icon" src="<?php echo esc_url( wp_mail_smtp()->assets_url . '/images/font-awesome/exclamation-circle-solid-red.svg' ); ?>" alt="<?php esc_attr_e( 'Error icon', 'wp-mail-smtp' ); ?>">
						<?php endif; ?>
					</span>
					<span class="dashicons dashicons-arrow-down"></span>
				</h2>

				<div class="email-header-details">
					<button class="button js-wp-mail-smtp-pro-logs-close-extra-details">
						<?php esc_html_e( 'Hide Technical Details', 'wp-mail-smtp-pro' ); ?>
					</button>

					<h3><?php esc_html_e( 'Headers', 'wp-mail-smtp-pro' ); ?></h3>
					<pre>
						<?php
						$tech = WP::is_json( $this->email->get_headers() ) ? implode( "\r\n", (array) json_decode( $this->email->get_headers() ) ) : '';
						echo esc_html( trim( $tech ) );
						?>
					</pre>

					<?php if ( $this->email->has_error() ) : ?>
						<div class="email-extra-details-error">
							<h3><?php esc_html_e( 'Error', 'wp-mail-smtp-pro' ); ?></h3>
							<pre>
								<?php echo esc_html( trim( $this->email->get_error_text() ) ); ?>
							</pre>
						</div>
					<?php endif; ?>

				</div>
			</div>

		</div>

		<?php
	}

	/**
	 * Display the sidebar content of the page.
	 *
	 * @since 1.5.0
	 */
	public function display_content_side() {
		?>

		<div class="email-side">

			<div class="email-section email-meta">
				<h2><?php esc_html_e( 'Log Details', 'wp-mail-smtp-pro' ); ?></h2>

				<ul>
					<li>
						<?php
						if ( Email::STATUS_DELIVERED === $this->email->get_status() ) {
							$label        = '<strong>' . esc_html__( 'Delivered', 'wp-mail-smtp-pro' ) . '</strong>';
							$icon_classes = 'dashicons dashicons-yes-alt delivered';
						} elseif ( Email::STATUS_SENT === $this->email->get_status() ) {
							$label        = '<strong>' . esc_html__( 'Sent', 'wp-mail-smtp-pro' ) . '</strong>';
							$icon_classes = 'dot sent';
						} elseif ( Email::STATUS_WAITING === $this->email->get_status() ) {
							$label        = '<strong>' . esc_html__( 'Waiting for confirmation', 'wp-mail-smtp-pro' ) . '</strong>';
							$icon_classes = 'circle waiting';
						} else {
							$label        = '<strong>' . esc_html__( 'Not Sent', 'wp-mail-smtp-pro' ) . '</strong>';
							$icon_classes = 'dot notsent';
						}
						?>

						<span class="<?php echo esc_attr( $icon_classes ); ?>"></span>

						<?php
						printf(
							/* translators: %s - Sent status text (like Delivered or Not Sent, ...) */
							esc_html__( 'Status: %s', 'wp-mail-smtp-pro' ),
							$label // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						);
						?>
					</li>

					<?php
					if ( ! empty( $this->email->get_mailer() ) ) {
						$provider = wp_mail_smtp()->get_providers()->get_options( $this->email->get_mailer() );

						?>
						<li>
							<img src="<?php echo esc_url( wp_mail_smtp()->pro->assets_url ); ?>/images/logs/icon-envelope.svg" class="icon" alt="">
							<?php
							if ( $provider !== null ) {
								$mailer_name = '<strong>' . esc_html( wp_mail_smtp()->get_providers()->get_options( $this->email->get_mailer() )->get_title() ) . '</strong>';
							} else {
								$mailer_name = '<code>' . esc_html( $this->email->get_mailer() ) . '</code>';
							}

							printf(
								/* translators: %s - name of the mailer. */
								esc_html__( 'Mailer: %s', 'wp-mail-smtp-pro' ),
								$mailer_name // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							);
							?>
						</li>
						<?php
					}
					?>

					<li>
						<img src="<?php echo esc_url( wp_mail_smtp()->pro->assets_url ); ?>/images/logs/icon-paperclip.svg" class="icon" alt="">
						<?php
						printf(
							/* translators: %s - number of attachments. */
							esc_html__( 'Attachments: %s', 'wp-mail-smtp-pro' ),
							'<strong>' . (int) $this->email->get_attachments() . '</strong>'
						);
						?>
					</li>
					<li>
						<img src="<?php echo esc_url( wp_mail_smtp()->pro->assets_url ); ?>/images/logs/icon-file-alt.svg" class="icon" alt="">
						<?php
						printf(
							/* translators: %s - ID of an email log. */
							esc_html__( 'Log ID: %s', 'wp-mail-smtp-pro' ),
							'<strong>' . (int) $this->email->get_id() . '</strong>'
						);
						?>
					</li>
				</ul>

				<div class="email-actions">
					<?php
					$delete_url = wp_nonce_url(
						add_query_arg(
							array(
								'email_id' => $this->email->get_id(),
								'mode'     => 'delete',
							),
							wp_mail_smtp()->get_admin()->get_admin_page_url( Area::SLUG . '-logs' )
						),
						'wp_mail_smtp_pro_logs_log_delete'
					);
					?>
					<a href="<?php echo esc_url( $delete_url ); ?>" class="email-delete js-wp-mail-smtp-pro-logs-email-delete">
						<?php esc_html_e( 'Delete Log', 'wp-mail-smtp-pro' ); ?>
					</a>

					<?php
					if ( ! empty( $this->email->get_content() ) ) {
						// Register WP built-in Thickbox for popup.
						add_thickbox();

						$preview_url = add_query_arg(
							array(
								'email_id'  => $this->email->get_id(),
								'mode'      => 'preview',
								'TB_iframe' => true,
								'width'     => 600,
								'height'    => '',
							),
							wp_nonce_url( wp_mail_smtp()->get_admin()->get_admin_page_url( Area::SLUG . '-logs' ), 'wp_mail_smtp_pro_logs_log_preview' )
						);
						?>
						<a href="<?php echo \esc_url( $preview_url ); ?>"
							title="<?php echo esc_attr( $this->email->get_subject() ); ?>"
							class="thickbox wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-orange email-preview">
							<?php esc_html_e( 'View Email', 'wp-mail-smtp-pro' ); ?>
						</a>
					<?php } ?>
					<div class="clear"></div>
				</div>
			</div>

		</div>

		<?php
	}

	/**
	 * Display a generic error message that something went wrong.
	 *
	 * @since 1.5.0
	 */
	public function display_error_happened() {
		?>

		<div class="wp-mail-smtp-logs-error">
			<h2><?php esc_html_e( 'Something went wrong', 'wp-mail-smtp-pro' ); ?></h2>
			<p>
				<?php esc_html_e( 'You are trying to access an email log entry that is no longer available or never existed.', 'wp-mail-smtp-pro' ); ?>
			</p>
			<p>
				<?php esc_html_e( 'Please use the "Back to Email Log" button to return to the list of all saved emails.', 'wp-mail-smtp-pro' ); ?>
			</p>
		</div>

		<?php
	}
}
