<?php

namespace WPMailSMTP\Pro\Emails\Logs\Admin;

use WPMailSMTP\Admin\Area;
use WPMailSMTP\Pro\Emails\Logs\Attachments\Attachments;
use WPMailSMTP\Pro\Emails\Logs\Email;
use WPMailSMTP\Pro\Emails\Logs\Tracking\Events\Injectable\OpenEmailEvent;
use WPMailSMTP\Pro\Emails\Logs\Tracking\Events\Injectable\ClickLinkEvent;
use WPMailSMTP\WP;

/**
 * Class SinglePage displays a single email page content.
 *
 * @since 1.5.0
 */
class SinglePage extends PageAbstract {

	/**
	 * The Email object for displaying on the single log page.
	 *
	 * @since 1.5.0
	 *
	 * @var Email
	 */
	protected $email;

	/**
	 * SinglePage class constructor.
	 *
	 * @since 2.8.0
	 */
	public function __construct() {
		parent::__construct();

		$this->hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @since 2.8.0
	 */
	public function hooks() {

		// Output single Email Log content and sidebar metabox.
		add_action( 'wp_mail_smtp_pro_emails_logs_admin_single_page_display_content', [ $this, 'email_details' ], 10 );
		add_action( 'wp_mail_smtp_pro_emails_logs_admin_single_page_display_content', [ $this, 'email_extra_details' ], 20 );
		add_action( 'wp_mail_smtp_pro_emails_logs_admin_single_page_display_sidebar', [ $this, 'email_meta' ], 10 );
		add_action( 'wp_mail_smtp_pro_emails_logs_admin_single_page_display_sidebar', [ $this, 'email_actions' ], 20 );
		add_action( 'wp_mail_smtp_pro_emails_logs_admin_single_page_display_sidebar', [ $this, 'email_attachments' ], 30 );
	}

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

			<a href="<?php echo esc_url( wp_mail_smtp()->get_admin()->get_admin_page_url( Area::SLUG . '-logs' ) ); ?>" class="button wp-mail-smtp-btn wp-mail-smtp-btn-orange action">
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

					// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
					$this->email = new Email( intval( $_GET['email_id'] ) );

					if ( $this->email->is_valid() ) {
						?>
						<div id="poststuff">

							<div id="post-body" class="metabox-holder columns-2">

								<!-- Left column -->
								<div id="post-body-content" style="position: relative;">
									<?php
									/**
									 * Single email log content area.
									 *
									 * @since 2.8.0
									 *
									 * @param \WPMailSMTP\Pro\Emails\Logs\Email            $email Email instance.
									 * @param \WPMailSMTP\Pro\Emails\Logs\Admin\SinglePage $page  Log single page instance.
									 */
									do_action( 'wp_mail_smtp_pro_emails_logs_admin_single_page_display_content', $this->email, $this );
									?>
								</div>

								<!-- Right column -->
								<div id="postbox-container-1" class="postbox-container">
									<?php
									/**
									 * Single email log sidebar area.
									 *
									 * @since 2.8.0
									 *
									 * @param \WPMailSMTP\Pro\Emails\Logs\Email            $email Email instance.
									 * @param \WPMailSMTP\Pro\Emails\Logs\Admin\SinglePage $page  Log single page instance.
									 */
									do_action( 'wp_mail_smtp_pro_emails_logs_admin_single_page_display_sidebar', $this->email, $this );
									?>
								</div>

							</div>

						</div>
						<?php
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
	 * Email details metabox.
	 *
	 * @since 2.8.0
	 *
	 * @param Email $email Email instance.
	 */
	public function email_details( $email ) {
		?>
		<div id="wp-mail-smtp-people-details" class="postbox">

			<div class="postbox-header">
				<h2 class="hndle"><?php esc_html_e( 'Email Details', 'wp-mail-smtp-pro' ); ?></h2>
			</div>

			<div class="inside">
				<ul>
					<!-- Date sent. -->
					<li class="subheading"><?php esc_html_e( 'Created', 'wp-mail-smtp-pro' ); ?></li>
					<li class="subcontent">
						<?php
						echo esc_html(
							date_i18n(
								WP::datetime_format(),
								strtotime( get_date_from_gmt( $email->get_date_sent()->format( WP::datetime_mysql_format() ) ) )
							)
						);
						?>
					</li>

					<!-- Sent FROM. -->
					<li class="subheading"><?php esc_html_e( 'From', 'wp-mail-smtp-pro' ); ?></li>
					<li class="subcontent">
						<?php
						$data = $email->get_people( 'from' );

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
						$data = $email->get_people( 'to' );

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
					$data = $email->get_people( 'cc' );

					if ( ! empty( $data ) ) :
						?>
						<li class="subheading"><?php esc_html_e( 'Carbon Copy (CC)', 'wp-mail-smtp-pro' ); ?></li>
						<li class="subcontent"><?php echo esc_html( implode( ', ', $data ) ); ?></li>
					<?php endif; ?>

					<!-- Sent BCC. -->
					<?php
					$data = $email->get_people( 'bcc' );

					if ( ! empty( $data ) ) :
						?>
						<li class="subheading"><?php esc_html_e( 'Blind Carbon Copy (BCC)', 'wp-mail-smtp-pro' ); ?></li>
						<li class="subcontent"><?php echo esc_html( implode( ', ', $data ) ); ?></li>
					<?php endif; ?>

					<!-- Subject. -->
					<li class="subheading"><?php esc_html_e( 'Subject', 'wp-mail-smtp-pro' ); ?></li>
					<li class="subcontent">
						<?php echo esc_html( $email->get_subject() ); ?>
					</li>

				</ul>
			</div>

		</div>
		<?php
	}

	/**
	 * Email extra details metabox.
	 *
	 * @since 2.8.0
	 *
	 * @param Email $email Email instance.
	 */
	public function email_extra_details( $email ) {
		?>
		<div id="wp-mail-smtp-extra-details" class="postbox closed">

			<div class="postbox-header js-wp-mail-smtp-pro-logs-toggle-extra-details">
				<h2 class="hndle">
					<span>
						<?php esc_html_e( 'Technical Details', 'wp-mail-smtp-pro' ); ?>
						<?php if ( $email->has_error() ) : ?>
							<img class="error-icon" src="<?php echo esc_url( wp_mail_smtp()->assets_url . '/images/font-awesome/exclamation-circle-solid-red.svg' ); ?>" alt="<?php esc_attr_e( 'Error icon', 'wp-mail-smtp' ); ?>">
						<?php endif; ?>
					</span>
				</h2>
				<div class="handle-actions hide-if-no-js">
					<button type="button" class="handlediv" aria-expanded="false">
						<span class="screen-reader-text">
							<?php esc_html_e( 'Toggle panel: Technical Details', 'wp-mail-smtp-pro' ); ?>
						</span>
						<span class="dashicons dashicons-arrow-down"></span>
					</button>
				</div>
			</div>

			<div class="inside">

				<button class="button js-wp-mail-smtp-pro-logs-close-extra-details">
					<?php esc_html_e( 'Hide Technical Details', 'wp-mail-smtp-pro' ); ?>
				</button>

				<h3><?php esc_html_e( 'Headers', 'wp-mail-smtp-pro' ); ?></h3>
				<pre>
					<?php
					$tech = WP::is_json( $email->get_headers() ) ? implode( "\r\n", (array) json_decode( $email->get_headers() ) ) : '';
					echo esc_html( trim( $tech ) );
					?>
				</pre>

				<?php if ( ! empty( $email->get_initiator_file() ) ) : ?>
					<h3><?php esc_html_e( 'Source', 'wp-mail-smtp-pro' ); ?></h3>
					<pre>
						<b><?php echo esc_html( $email->get_initiator_name() ); ?></b> <?php echo esc_html( $email->get_initiator_file() ); ?>
					</pre>
				<?php endif; ?>

				<?php if ( $email->has_error() ) : ?>
					<div class="email-extra-details-error">
						<h3><?php esc_html_e( 'Error', 'wp-mail-smtp-pro' ); ?></h3>
						<pre>
							<?php echo esc_html( trim( $email->get_error_text() ) ); ?>
						</pre>
					</div>
				<?php endif; ?>

			</div>
		</div>
		<?php
	}

	/**
	 * Email meta metabox.
	 *
	 * @since 2.8.0
	 *
	 * @param Email $email Email instance.
	 */
	public function email_meta( $email ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded
		?>
		<div id="wp-mail-smtp-email-meta" class="postbox">

			<div class="postbox-header">
				<h2 class="hndle">
					<?php esc_html_e( 'Log Details', 'wp-mail-smtp-pro' ); ?>
				</h2>
			</div>

			<div class="inside">

				<ul>
					<li>
						<?php
						if ( Email::STATUS_DELIVERED === $email->get_status() ) {
							$label        = '<strong>' . esc_html__( 'Delivered', 'wp-mail-smtp-pro' ) . '</strong>';
							$icon_classes = 'wp-mail-smtp-dashicons-yes-alt-green status delivered';
						} elseif ( Email::STATUS_SENT === $email->get_status() ) {
							$label        = '<strong>' . esc_html__( 'Sent', 'wp-mail-smtp-pro' ) . '</strong>';
							$icon_classes = 'status dot sent';
						} elseif ( Email::STATUS_WAITING === $email->get_status() ) {
							$label        = '<strong>' . esc_html__( 'Waiting for confirmation', 'wp-mail-smtp-pro' ) . '</strong>';
							$icon_classes = 'status circle waiting';
						} else {
							$label        = '<strong>' . esc_html__( 'Not Sent', 'wp-mail-smtp-pro' ) . '</strong>';
							$icon_classes = 'status dot notsent';
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
					if ( ! empty( $email->get_mailer() ) ) {
						$provider = wp_mail_smtp()->get_providers()->get_options( $email->get_mailer() );

						?>
						<li>
							<img src="<?php echo esc_url( wp_mail_smtp()->pro->assets_url ); ?>/images/logs/icon-envelope.svg" class="icon" alt="">
							<?php
							if ( $provider !== null ) {
								$mailer_name = '<strong>' . esc_html( wp_mail_smtp()->get_providers()->get_options( $email->get_mailer() )->get_title() ) . '</strong>';
							} else {
								$mailer_name = '<code>' . esc_html( $email->get_mailer() ) . '</code>';
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
							'<strong>' . (int) $email->get_attachments() . '</strong>'
						);
						?>
					</li>
					<li>
						<img src="<?php echo esc_url( wp_mail_smtp()->pro->assets_url ); ?>/images/logs/icon-file-alt.svg" class="icon" alt="">
						<?php
						printf(
							/* translators: %s - ID of an email log. */
							esc_html__( 'Log ID: %s', 'wp-mail-smtp-pro' ),
							'<strong>' . (int) $email->get_id() . '</strong>'
						);
						?>
					</li>
					<?php if ( wp_mail_smtp()->get_pro()->get_logs()->is_enabled_open_email_tracking() ) : ?>
						<li>
							<img src="<?php echo esc_url( wp_mail_smtp()->pro->assets_url ); ?>/images/logs/icon-envelope-open.svg" class="icon" alt="">
							<?php
							printf( /* translators: %s - whether email was opened. */
								esc_html__( 'Opened: %s', 'wp-mail-smtp-pro' ),
								'<strong>' . $this->display_was_email_already_opened() . '</strong>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							);
							?>
						</li>
					<?php endif; ?>
					<?php if ( wp_mail_smtp()->get_pro()->get_logs()->is_enabled_click_link_tracking() ) : ?>
						<li>
							<img src="<?php echo esc_url( wp_mail_smtp()->pro->assets_url ); ?>/images/logs/icon-hand-pointer.svg" class="icon" alt="">
							<?php
							printf( /* translators: %s - whether one of email links was clicked. */
								esc_html__( 'Clicked: %s', 'wp-mail-smtp-pro' ),
								'<strong>' . $this->display_was_email_link_already_clicked() . '</strong>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							);
							?>
						</li>
					<?php endif; ?>
					<?php if ( ! empty( $email->get_initiator_name() ) ) : ?>
						<li>
							<i class="dashicons dashicons-admin-plugins"></i>
							<?php
							printf( /* translators: %s - email source. */
								esc_html__( 'Source: %s', 'wp-mail-smtp-pro' ),
								'<strong>' . esc_html( $email->get_initiator_name() ) . '</strong>'
							);
							?>
						</li>
					<?php endif; ?>
				</ul>

				<div id="major-publishing-actions">

					<?php if ( current_user_can( wp_mail_smtp()->get_pro()->get_logs()->get_manage_capability() ) ) : ?>
						<div id="delete-action">
							<?php
							$delete_url = wp_nonce_url(
								add_query_arg(
									[
										'email_id' => $email->get_id(),
										'mode'     => 'delete',
									],
									wp_mail_smtp()->get_admin()->get_admin_page_url( Area::SLUG . '-logs' )
								),
								'wp_mail_smtp_pro_logs_log_delete'
							);
							?>
							<a href="<?php echo esc_url( $delete_url ); ?>" class="submitdelete deletion email-delete js-wp-mail-smtp-pro-logs-email-delete">
								<?php esc_html_e( 'Delete Log', 'wp-mail-smtp-pro' ); ?>
							</a>
						</div>
					<?php endif; ?>

					<div id="publishing-action">
						<?php
						if ( ! empty( $email->get_content() ) ) {
							// Register WP built-in Thickbox for popup.
							add_thickbox();

							$preview_url = add_query_arg(
								[
									'email_id'  => $email->get_id(),
									'mode'      => 'preview',
									'TB_iframe' => true,
									'width'     => 600,
									'height'    => '',
								],
								wp_nonce_url( wp_mail_smtp()->get_admin()->get_admin_page_url( Area::SLUG . '-logs' ), 'wp_mail_smtp_pro_logs_log_preview' )
							);
							?>
							<a href="<?php echo esc_url( $preview_url ); ?>" title="<?php echo esc_attr( $email->get_subject() ); ?>" class="thickbox wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-orange email-preview">
								<?php esc_html_e( 'View Email', 'wp-mail-smtp-pro' ); ?>
							</a>
						<?php } ?>
					</div>

					<div class="clear"></div>
				</div>

			</div>

		</div>
		<?php
	}

	/**
	 * Email actions metabox.
	 *
	 * @since 2.8.0
	 *
	 * @param Email $email Email instance.
	 */
	public function email_actions( $email ) {

		// Print Email URL.
		$print_url = add_query_arg(
			[
				'mode'     => 'print',
				'email_id' => $email->get_id(),
			],
			wp_mail_smtp()->get_admin()->get_admin_page_url( Area::SLUG . '-logs' )
		);

		$action_links = [
			'print'       => [
				'url'    => $print_url,
				'target' => 'blank',
				'icon'   => 'dashicons-media-text',
				'label'  => esc_html__( 'Print', 'wp-mail-smtp-pro' ),
			],
			'export'      => [
				'url'   => $this->get_export_url( $email->get_id(), 'csv' ),
				'icon'  => 'dashicons-migrate',
				'label' => esc_html__( 'Export (CSV)', 'wp-mail-smtp-pro' ),
			],
			'export_xlsx' => [
				'url'   => $this->get_export_url( $email->get_id(), 'xlsx' ),
				'icon'  => 'dashicons-media-spreadsheet',
				'label' => esc_html__( 'Export (XLSX)', 'wp-mail-smtp-pro' ),
			],
			'export_eml'  => [
				'url'   => $this->get_export_url( $email->get_id(), 'eml' ),
				'icon'  => 'dashicons-email',
				'label' => esc_html__( 'Export (EML)', 'wp-mail-smtp-pro' ),
			],
		];

		if ( ! empty( $email->get_content() ) ) {
			$action_links['resend'] = [
				'url'   => '#',
				'icon'  => 'dashicons-update',
				'label' => esc_html__( 'Resend', 'wp-mail-smtp-pro' ),
			];
		}

		/**
		 * Filters single email log actions.
		 *
		 * @since 2.8.0
		 *
		 * @param array $action_links Actions.
		 * @param Email $email        Email.
		 */
		$action_links = apply_filters( 'wp_mail_smtp_pro_emails_logs_admin_single_page_email_actions', $action_links, $email );
		?>
		<div id="wp-mail-smtp-email-actions" class="postbox">
			<div class="postbox-header">
				<h2 class="hndle"><?php esc_html_e( 'Actions', 'wp-mail-smtp-pro' ); ?></h2>
			</div>
			<div class="inside">
				<ul>
					<?php
					foreach ( $action_links as $slug => $link ) {
						$window = ! empty( $link['target'] ) ? 'target="_blank" rel="noopener noreferrer"' : '';
						printf( '<li class="wp-mail-smtp-email-log-%s">', esc_attr( $slug ) );
							printf( '<a href="%1$s" %2$s>', esc_url( $link['url'] ), $window ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								printf( '<span class="dashicons %s"></span>', esc_attr( $link['icon'] ) );
								echo esc_html( $link['label'] );
							echo '</a>';
						echo '</li>';
					}
					?>
				</ul>
			</div>
		</div>
		<?php
	}

	/**
	 * Email attachments metabox.
	 *
	 * @since 2.9.0
	 *
	 * @param Email $email Email instance.
	 *
	 * @return string|false
	 */
	public function email_attachments( $email ) {

		$attachments = ( new Attachments() )->get_attachments( $email->get_id() );

		if ( empty( $attachments ) ) {
			return false;
		}

		/**
		 * Filters single email log attachments.
		 *
		 * @since 2.9.0
		 *
		 * @param array $attachments  The array of Attachment objects.
		 * @param Email $email        Email.
		 */
		$attachments = apply_filters( 'wp_mail_smtp_pro_emails_logs_admin_single_page_attachments', $attachments, $email );
		?>
		<div id="wp-mail-smtp-email-attachments" class="postbox">
			<div class="postbox-header">
				<h2 class="hndle"><?php esc_html_e( 'Attachments', 'wp-mail-smtp-pro' ); ?></h2>
			</div>
			<div class="inside">
				<ul>
					<?php
					foreach ( $attachments as $attachment ) {
						echo '<li class="wp-mail-smtp-email-log-attachment">';
							printf( '<a href="%1$s" target="_blank" rel="noopener noreferrer">', esc_url( $attachment->get_url() ) );
								printf( '<span class="dashicons %s"></span>', esc_attr( $attachment->get_icon() ) );
								echo esc_html( $attachment->get_filename() );
							echo '</a>';
						echo '</li>';
					}
					?>
				</ul>
			</div>
		</div>
		<?php
	}

	/**
	 * Display the main content of the page.
	 *
	 * @deprecated 2.8.0 Use wp_mail_smtp_pro_emails_logs_admin_single_page_content action.
	 *
	 * @since 1.5.0
	 */
	public function display_content_main() {
		_deprecated_function( __METHOD__, '2.8.0' );

		$this->email_details( $this->email );
		$this->email_extra_details( $this->email );
	}

	/**
	 * Display the sidebar content of the page.
	 *
	 * @deprecated 2.8.0 Use wp_mail_smtp_pro_emails_logs_admin_single_page_sidebar action.
	 *
	 * @since 1.5.0
	 */
	public function display_content_side() {
		_deprecated_function( __METHOD__, '2.8.0' );

		$this->email_meta( $this->email );
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

	/**
	 * Get Export URL.
	 *
	 * @since 2.8.0
	 *
	 * @param int    $email_id Email ID.
	 * @param string $type     Export type.
	 *
	 * @return string
	 */
	private function get_export_url( $email_id, $type ) {

		return wp_nonce_url(
			add_query_arg(
				[
					'tab'         => 'export',
					'action'      => 'wp_mail_smtp_tools_export_single_email_log',
					'email_id'    => $email_id,
					'export_type' => $type,
				],
				wp_mail_smtp()->get_admin()->get_admin_page_url( Area::SLUG . '-tools' )
			),
			'wp-mail-smtp-tools-export-single-email-log-nonce',
			'nonce'
		);
	}

	/**
	 * Display whether email was opened.
	 *
	 * @since 2.9.0
	 *
	 * @return string
	 */
	private function display_was_email_already_opened() {

		if ( ! $this->email->is_content_type_html_based() ) {
			return esc_html__( 'N/A', 'wp-mail-smtp-pro' );
		}

		return ( new OpenEmailEvent( $this->email->get_id() ) )->was_event_already_triggered() ?
			esc_html__( 'Yes', 'wp-mail-smtp-pro' ) :
			esc_html__( 'No', 'wp-mail-smtp-pro' );
	}

	/**
	 * Display whether one of email links was clicked.
	 *
	 * @since 2.9.0
	 *
	 * @return string
	 */
	private function display_was_email_link_already_clicked() {

		if ( ! $this->email->is_content_type_html_based() ) {
			return esc_html__( 'N/A', 'wp-mail-smtp-pro' );
		}

		return ( new ClickLinkEvent( $this->email->get_id() ) )->was_event_already_triggered() ?
			esc_html__( 'Yes', 'wp-mail-smtp-pro' ) :
			esc_html__( 'No', 'wp-mail-smtp-pro' );
	}
}
