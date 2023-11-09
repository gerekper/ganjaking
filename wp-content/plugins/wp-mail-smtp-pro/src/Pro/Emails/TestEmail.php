<?php

namespace WPMailSMTP\Pro\Emails;

use WPMailSMTP\Options;
use WPMailSMTP\Pro\AdditionalConnections\AdditionalConnections;
use WPMailSMTP\Pro\Alerts\Alerts;
use WPMailSMTP\Reports\Emails\Summary as SummaryReportEmail;

/**
 * Class TestEmail.
 *
 * @since 3.10.0
 */
class TestEmail {

	/**
	 * Register hooks.
	 *
	 * @since 3.10.0
	 */
	public function hooks() {

		add_action(
			'wp_mail_smtp_admin_pages_test_tab_get_email_message_html_head',
			[ $this, 'display_email_head' ]
		);

		add_action(
			'wp_mail_smtp_admin_pages_test_tab_get_email_message_html_footer',
			[ $this, 'display_email_footer' ]
		);
	}

	/**
	 * Display the email head.
	 *
	 * @since 3.10.0
	 */
	public function display_email_head() {

		ob_start();
		?>
		<style type="text/css">@media only screen and (max-width: 599px) {.sendlayer-section-wrap{padding: 15px 40px !important;}.sendlayer-section{padding:15px 15px 15px 15px !important;}.sendlayer-section-title{margin-bottom:10px !important;}.sendlayer-section td{display:block !important;}.sendlayer-section-left{padding:0 0 15px 0 !important;}.sendlayer-section td p{text-align:center !important;}.sendlayer-section-img {margin-left: auto !important;margin-right: auto !important;}}</style>
		<?php

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo ob_get_clean();
	}

	/**
	 * Display the email footer.
	 *
	 * @since 3.10.0
	 */
	public function display_email_footer() {

		$last_displayed_footer_option_key = 'wp_mail_smtp_test_email_last_displayed_footer';
		$last_displayed_footer            = get_option( $last_displayed_footer_option_key, 'sendlayer' );
		$features_section                 = $this->get_features_section();
		$send_layer_section               = $this->get_sendlayer_section();

		if (
			( $last_displayed_footer === 'sendlayer' && ! empty( $features_section ) ) ||
			( empty( $send_layer_section ) && ! empty( $features_section ) )
		) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $this->get_features_section();
			update_option( $last_displayed_footer_option_key, 'features', false );
		} elseif ( ! empty( $send_layer_section ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $this->get_sendlayer_section();
			update_option( $last_displayed_footer_option_key, 'sendlayer', false );
		}
	}

	/**
	 * Get the features section.
	 *
	 * @since 3.10.0
	 *
	 * @return string
	 */
	private function get_features_section() {

		$options  = Options::init();
		$features = [
			[
				'title'    => 'Backup Connection',
				'disabled' => empty( $options->get( 'backup_connection', 'connection_id' ) ),
			],
			[
				'title'    => 'Email Alerts',
				'disabled' => ! ( new Alerts() )->is_enabled(),
			],
			[
				'title'    => 'Weekly Email Summary',
				'disabled' => SummaryReportEmail::is_disabled(),
			],
			[
				'title'    => 'Dashboard Widget',
				'disabled' => ! empty( $options->get( 'general', 'dashboard_widget_hidden' ) ),
			],
		];

		$features = array_filter(
			$features,
			function ( $feature ) {
				return $feature['disabled'];
			}
		);

		if ( empty( $features ) ) {
			return '';
		}

		ob_start();
		?>
		<tr style="padding: 0; vertical-align: top; text-align: left;">
			<td align="left" valign="top" class="aside" style="word-wrap: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #444; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-weight: normal; margin: 0; Margin: 0; font-size: 14px; mso-line-height-rule: exactly; line-height: 140%; background-color: #f8f8f8; border-top: 1px solid #dddddd; text-align: center !important; padding: 45px 75px 45px 75px;">
				<h6 style="padding: 0; color: #444444; word-wrap: normal; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-weight: bold; mso-line-height-rule: exactly; line-height: 130%; font-size: 18px; text-align: center; margin: 0 0 15px 0; Margin: 0 0 15px 0;">
					Take advantage of powerful features
				</h6>
				<p style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #444; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-weight: normal; padding: 0; mso-line-height-rule: exactly; line-height: 140%; margin: 0 0 15px 0; Margin: 0 0 15px 0; font-size: 16px; text-align: center;">
					<?php echo wp_kses( implode( '<br>', array_column( $features, 'title' ) ), [ 'br' => [] ] ); ?>
				</p>
				<p style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #444; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-weight: normal; padding: 0; mso-line-height-rule: exactly; line-height: 140%; font-size: 13px; text-align: center; margin: 0 0 0 0; Margin: 0 0 0 0;">
					These powerful features are available in your WP Mail SMTP Pro <a href="<?php echo esc_url( wp_mail_smtp()->get_admin()->get_admin_page_url() ); ?>">settings</a>.
				</p>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Get the SendLayer section.
	 *
	 * @since 3.10.0
	 *
	 * @return string
	 */
	private function get_sendlayer_section() {

		$options              = Options::init();
		$primary_mailer       = $options->get( 'mail', 'mailer' );
		$backup_mailer        = '';
		$backup_connection_id = $options->get( 'backup_connection', 'connection_id' );

		if ( ! empty( $backup_connection_id ) ) {
			$backup_connection = ( new AdditionalConnections() )->get_connection( $backup_connection_id );

			if ( $backup_connection !== false ) {
				$backup_mailer = $backup_connection->get_mailer_slug();
			}
		}

		// Don't display this section if primary or backup connection is already SendLayer.
		if ( $primary_mailer === 'sendlayer' || $backup_mailer === 'sendlayer' ) {
			return '';
		}

		$cta_link_url = wp_mail_smtp()->get_utm_url(
			'https://sendlayer.com/wp-mail-smtp/',
			[
				'source'  => 'wpmailsmtpplugin',
				'medium'  => 'test-email',
				'content' => 'Try SendLayer',
			]
		);

		ob_start();
		?>
		<tr style="padding: 0; vertical-align: top; text-align: left;">
			<td class="sendlayer-section-wrap" align="left" valign="top" style="word-wrap: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #444; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-weight: normal; margin: 0; Margin: 0; font-size: 14px; mso-line-height-rule: exactly; line-height: 140%; padding: 0 60px 45px 60px;">
				<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-spacing: 0; padding: 0; vertical-align: top; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; margin: 0; Margin: 0; text-align: inherit;">
					<tr style="padding: 0; vertical-align: top; text-align: left;">
						<td class="sendlayer-section" align="left" valign="top" style="word-wrap: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #444; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-weight: normal; margin: 0; Margin: 0; font-size: 14px; mso-line-height-rule: exactly; line-height: 140%; background-color: #F8F8FC; padding: 30px 30px 30px 30px; border-radius: 4px;">
							<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border-spacing: 0; padding: 0; vertical-align: top; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; margin: 0; Margin: 0; text-align: inherit;">
								<tr style="padding: 0; vertical-align: top; text-align: left;">
									<td class="sendlayer-section-left" align="center" valign="middle" style="word-wrap: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #444; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-weight: normal; margin: 0; Margin: 0; font-size: 14px; mso-line-height-rule: exactly; line-height: 140%; padding: 0px 20px 0px 0px;">
										<img src="<?php echo esc_url( wp_mail_smtp()->plugin_url . '/assets/pro/images/email/sendlayer-icon.png' ); ?>" width="70" height="70" alt="SendLayer Icon" class="sendlayer-section-img" style="vertical-align: middle;">
									</td>
									<td class="sendlayer-section-right" align="center" valign="middle" style="word-wrap: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #444; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-weight: normal; margin: 0; Margin: 0; font-size: 14px; mso-line-height-rule: exactly; line-height: 140%; padding: 0;">
										<p class="sendlayer-section-title" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #444; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-weight: normal; padding: 0; mso-line-height-rule: exactly; line-height: 140%; font-size: 16px; font-weight: bold; text-align: left; margin: 0; Margin: 0;">
											Tired of Missing or Delayed Emails?
										</p>
										<p style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #777; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; font-weight: normal; padding: 0; mso-line-height-rule: exactly; line-height: 140%; font-size: 14px; text-align: left; margin: 0; Margin: 0;">
											<a href="<?php echo esc_url( $cta_link_url ); ?>" style="color: #211F9A; font-weight: bold;">Try SendLayer</a>, a reliable email provider that’s powerful and easy to use. Send your first 200 emails for free!
										</p>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}
}
