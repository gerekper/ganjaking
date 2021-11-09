<?php

namespace WPMailSMTP\Pro\Emails\Logs\Reports\Emails;

use WPMailSMTP\Admin\Area;
use WPMailSMTP\Helpers\Helpers;
use WPMailSMTP\Reports\Emails\Summary as SummaryLite;
use WPMailSMTP\Pro\Emails\Logs\Reports\Report;

/**
 * Class Summary. Summary report email.
 *
 * @since 3.0.0
 */
class Summary extends SummaryLite {

	/**
	 * Emails stats report object.
	 *
	 * @since 3.0.0
	 *
	 * @var Report
	 */
	private $report;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param Report $report Emails stats report object.
	 */
	public function __construct( $report ) {

		$this->report = $report;
	}

	/**
	 * Get summary report email general content HTML.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	protected function get_main_html() {

		$view_all_link = $this->get_view_all_link();

		$comparison_report = $this->get_comparison_report();

		$stats_totals      = $this->report->get_stats_totals();
		$prev_stats_totals = $comparison_report->get_stats_totals();

		// Get first four stat items.
		$stats_by_subject = array_slice( $this->report->get_stats_by_subject(), 0, 4 );

		ob_start();
		?>
		<h6 class="main-heading dark-white-color" style="margin: 0;padding: 0;color: #444444;word-wrap: normal;font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;font-weight: bold;mso-line-height-rule: exactly;line-height: 22px;;text-align: left;font-size: 18px;margin-bottom: 10px;">
			<?php esc_html_e( 'Hi there,', 'wp-mail-smtp-pro' ); ?>
		</h6>
		<p class="main-description dark-white-color" style="margin: 0;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #444444;font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;font-weight: normal;padding: 0;text-align: left;mso-line-height-rule: exactly;line-height: 19px;font-size: 16px;margin-bottom: 40px;">
			<?php esc_html_e( 'Let’s see how your emails performed in the past week.', 'wp-mail-smtp-pro' ); ?>
		</p>

		<table class="stats-totals-wrapper <?php echo Helpers::mailer_without_send_confirmation() ? 'three' : 'four'; ?>" style="border-collapse: collapse;border-spacing: 0;padding: 0;vertical-align: top;text-align: left;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;width: 100%;">
			<tr style="padding: 0;vertical-align: top;text-align: left;">
				<td class="stats-totals-item-wrapper" style="word-wrap: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;padding: 0;vertical-align: top;text-align: left;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #444444;font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;font-weight: normal;margin: 0;mso-line-height-rule: exactly;line-height: 140%;font-size: 14px;border-collapse: collapse !important;">
					<?php
					echo $this->get_stats_total_html( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						__( 'Total Emails', 'wp-mail-smtp-pro' ),
						'icon-email.png',
						$this->report->get_total_count( $stats_totals ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						$this->report->get_total_count( $prev_stats_totals ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'#809EB0',
						! Helpers::mailer_without_send_confirmation() ? 'border-top-right-radius: 0;border-bottom-right-radius: 0;' : ''
					);
					?>
				</td>

				<?php if ( Helpers::mailer_without_send_confirmation() ) : ?>
					<td class="stats-totals-item-wrapper" style="word-wrap: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;padding: 0;vertical-align: top;text-align: center;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #444444;font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;font-weight: normal;margin: 0;mso-line-height-rule: exactly;line-height: 140%;font-size: 14px;border-collapse: collapse !important;">
						<?php
						echo $this->get_stats_total_html( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							__( 'Sent', 'wp-mail-smtp-pro' ),
							'icon-check.png',
							$this->report->get_sent_count( $stats_totals ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							$this->report->get_sent_count( $prev_stats_totals ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							'#6AA08B'
						);
						?>
					</td>
				<?php else : ?>
					<td class="stats-totals-item-wrapper" style="word-wrap: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;padding: 0;vertical-align: top;text-align: center;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #444444;font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;font-weight: normal;margin: 0;mso-line-height-rule: exactly;line-height: 140%;font-size: 14px;border-collapse: collapse !important;">
						<?php
						echo $this->get_stats_total_html( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							__( 'Confirmed', 'wp-mail-smtp-pro' ),
							'icon-check.png',
							$this->report->get_confirmed_count( $stats_totals ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							$this->report->get_confirmed_count( $prev_stats_totals ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							'#6AA08B',
							'border-left:none;border-radius: 0;'
						);
						?>
					</td>
					<td class="stats-totals-item-wrapper" style="word-wrap: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;padding: 0;vertical-align: top;text-align: center;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #444444;font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;font-weight: normal;margin: 0;mso-line-height-rule: exactly;line-height: 140%;font-size: 14px;border-collapse: collapse !important;">
						<?php
						echo $this->get_stats_total_html( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							__( 'Unconfirmed', 'wp-mail-smtp-pro' ),
							'icon-check-gray.png',
							$this->report->get_unconfirmed_count( $stats_totals ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							$this->report->get_unconfirmed_count( $prev_stats_totals ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							'#787C82',
							'border-left:none;border-right:none;border-radius: 0;'
						);
						?>
					</td>
				<?php endif; ?>

				<td class="stats-totals-item-wrapper" style="word-wrap: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;padding: 0;vertical-align: top;text-align: right;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #444444;font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;font-weight: normal;margin: 0;mso-line-height-rule: exactly;line-height: 140%;font-size: 14px;border-collapse: collapse !important;">
					<?php
					echo $this->get_stats_total_html( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						__( 'Failed', 'wp-mail-smtp-pro' ),
						'icon-error.png',
						$this->report->get_unsent_count( $stats_totals ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						$this->report->get_unsent_count( $prev_stats_totals ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'#D63638',
						! Helpers::mailer_without_send_confirmation() ? 'border-top-left-radius: 0;border-bottom-left-radius: 0;' : ''
					);
					?>
				</td>
			</tr>
		</table>

		<?php if ( ! empty( $stats_by_subject ) ) : ?>
			<table class="stats-heading" style="border-collapse: collapse;border-spacing: 0;padding: 0;vertical-align: top;text-align: left;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;width: 100%;border-bottom: 1px solid #dddddd;">
				<tr style="padding: 0;text-align: left;">
					<th class="first-col" style="word-wrap: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;padding: 0;vertical-align: top;text-align: left;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #444444;font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;font-weight: normal;margin: 0;mso-line-height-rule: exactly;line-height: 140%;font-size: 14px;padding-top: 40px;padding-bottom: 20px;border-collapse: collapse !important;padding-right: 10px;">
						<h2 style="margin: 0;padding: 0;color: #809EB0;word-wrap: normal;font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;font-weight: bold;mso-line-height-rule: exactly;line-height: 20px;text-align: left;font-size: 16px;text-transform: uppercase;">
							<?php esc_html_e( 'Last Week’s Top Emails', 'wp-mail-smtp-pro' ); ?>
						</h2>
					</th>
					<th class="second-col" style="word-wrap: break-word;-webkit-hyphens: auto;-moz-hyphens: auto;hyphens: auto;padding: 0;vertical-align: top;text-align: right;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #444444;font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;font-weight: normal;margin: 0;mso-line-height-rule: exactly;line-height: 140%;font-size: 14px;padding-top: 40px;padding-bottom: 20px;border-collapse: collapse !important;">
						<a href="<?php echo esc_attr( $view_all_link ); ?>" style="-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #8C8F94;font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;font-weight: normal;padding: 0;margin: 0;text-align: left;mso-line-height-rule: exactly;line-height: 17px;text-decoration: underline;font-size: 14px;text-decoration-line: underline;">
							<?php esc_html_e( 'View All Emails', 'wp-mail-smtp-pro' ); ?>
						</a>
					</th>
				</tr>
			</table>

			<div class="stats-subject stats-subject-<?php echo esc_attr( $this->get_stats_columns_count() ); ?>">
				<?php foreach ( $stats_by_subject as $item ) : ?>
					<h6 class="stats-subject-heading dark-white-color" style="margin: 0;padding: 0;color: #444444;word-wrap: normal;font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;font-weight: normal;mso-line-height-rule: exactly;line-height: 18px;text-align: left;font-size: 15px;margin-top: 20px;margin-bottom: 10px;padding-right: 10px;">
						<?php echo esc_html( $item['subject'] ); ?>
					</h6>
					<!--[if mso]>
					<table role="presentation" width="100%">
						<tr>
							<td style="line-height: 15px;background: #F8F8F8;padding: 10px 0px 0px 0px;border-radius: 4px;">
							<![endif]-->
								<div class="stats-subject-row dark-bg" style="font-size:0;line-height: 15px;text-align:left;background: #F8F8F8;padding: 10px 10px 0px 10px;border-radius: 4px;">
									<!--[if mso]>
									<table role="presentation" width="100%" style="text-align:center;">
										<tr>
										<![endif]-->
											<?php
											echo $this->get_stats_column_html( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
												__( 'Total Emails', 'wp-mail-smtp-pro' ),
												'icon-email.png',
												$this->report->get_total_count( $item ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
												'total'
											);

											if ( Helpers::mailer_without_send_confirmation() ) {
												echo $this->get_stats_column_html( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
													__( 'Sent Emails', 'wp-mail-smtp-pro' ),
													'icon-check.png',
													$this->report->get_sent_count( $item ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
													'sent'
												);
											} else {
												echo $this->get_stats_column_html( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
													__( 'Confirmed Emails', 'wp-mail-smtp-pro' ),
													'icon-check.png',
													$this->report->get_confirmed_count( $item ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
													'confirmed'
												);

												echo $this->get_stats_column_html( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
													__( 'Unconfirmed Emails', 'wp-mail-smtp-pro' ),
													'icon-check-gray.png',
													$this->report->get_unconfirmed_count( $item ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
													'unconfirmed'
												);
											}

											echo $this->get_stats_column_html( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
												__( 'Failed Emails', 'wp-mail-smtp-pro' ),
												'icon-error.png',
												$this->report->get_unsent_count( $item ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
												'unsent'
											);

											if ( wp_mail_smtp()->get_pro()->get_logs()->is_enabled_open_email_tracking() ) {
												echo $this->get_stats_column_html( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
													__( 'Opened Emails', 'wp-mail-smtp-pro' ),
													'icon-open.png',
													sprintf(
														'%1$d <span style="color: #8C8F94;white-space: nowrap !important;">(%2$d%%)</span>',
														esc_html( $this->report->get_open_count( $item ) ),
														esc_html( $this->report->get_open_percent_count( $item ) )
													),
													'opened'
												);
											}

											if ( wp_mail_smtp()->get_pro()->get_logs()->is_enabled_click_link_tracking() ) {
												echo $this->get_stats_column_html( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
													__( 'Clicked Links', 'wp-mail-smtp-pro' ),
													'icon-click.png',
													sprintf(
														'%1$d <span style="color: #8C8F94;white-space: nowrap !important;">(%2$d%%)</span>',
														esc_html( $this->report->get_click_count( $item ) ),
														esc_html( $this->report->get_click_percent_count( $item ) )
													),
													'clicked'
												);
											}
											?>
										<!--[if mso]>
										</tr>
									</table>
									<![endif]-->
								</div>
							<!--[if mso]>
							</td>
						</tr>
					</table>
					<![endif]-->
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<?php
		return ob_get_clean();
	}

	/**
	 * Get stats column block HTML.
	 *
	 * @since 3.0.0
	 *
	 * @param string $title  Heading.
	 * @param string $icon   Icon file.
	 * @param string $value  Stats value.
	 * @param string $column Column identifier.
	 *
	 * @return string
	 */
	protected function get_stats_column_html( $title, $icon, $value, $column ) {

		$width          = $this->get_stats_column_width( $column );
		$images_dir_url = wp_mail_smtp()->assets_url . '/images/reports/email/';
		$icon_width     = ( $icon === 'icon-email.png' ) ? 16 : 15;

		ob_start();
		?>
		<!--[if mso]>
		<td style="width:<?php echo intval( $width ); ?>px;padding:0px 0px 10px 0px;" valign="top">
		<![endif]-->
			<div class="stats-subject-column <?php echo esc_attr( $column ); ?>" style="width:100%;max-width:<?php echo intval( $width ); ?>px;display:inline-block;vertical-align:top;padding:0px 0px 10px 0px;font-size:13px;line-height:15px;">
				<img src="<?php echo esc_url( $images_dir_url . $icon ); ?>"
						 alt="<?php esc_attr( $title ); ?>" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;width: <?php echo esc_attr( $icon_width ); ?>px;height: 15px;max-width: 100%;clear: both;vertical-align: bottom;" width="<?php echo esc_attr( $icon_width ); ?>" height="15">&nbsp;
				<span class="stats-subject-column-value" style="line-height: 100%;white-space: nowrap !important; color: #50575E;">
					<?php echo wp_kses( $value, [ 'span' => [ 'style' => [] ] ] ); ?>
				</span>
			</div>
		<!--[if mso]>
		</td>
		<![endif]-->
		<?php
		return ob_get_clean();
	}

	/**
	 * Get stats column min width.
	 *
	 * @since 3.0.0
	 *
	 * @param string $column Column identifier.
	 *
	 * @return int
	 */
	protected function get_stats_column_min_width( $column ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		$result = 0;

		switch ( $column ) {
			case 'total':
				$result = 64;
				break;
			case 'sent':
			case 'confirmed':
			case 'unconfirmed':
			case 'unsent':
				$result = 58;
				break;
			case 'opened':
			case 'clicked':
				$result = 111;
				break;
		}

		return $result;
	}

	/**
	 * Get stats column width based on configuration.
	 *
	 * @since 3.0.0
	 *
	 * @param string $column Column identifier.
	 *
	 * @return int
	 */
	protected function get_stats_column_width( $column ) {

		static $cache = [];

		if ( isset( $cache[ $column ] ) ) {
			return $cache[ $column ];
		}

		$result = $this->get_stats_column_min_width( $column );

		if ( Helpers::mailer_without_send_confirmation() ) {
			$result += $this->get_stats_column_min_width( 'confirmed' ) / $this->get_stats_columns_count();
		}

		if ( ! wp_mail_smtp()->get_pro()->get_logs()->is_enabled_open_email_tracking() ) {
			$result += $this->get_stats_column_min_width( 'opened' ) / $this->get_stats_columns_count();
		}

		if ( ! wp_mail_smtp()->get_pro()->get_logs()->is_enabled_click_link_tracking() ) {
			$result += $this->get_stats_column_min_width( 'clicked' ) / $this->get_stats_columns_count();
		}

		$cache[ $column ] = $result;

		return $result;
	}

	/**
	 * Get stats total block width in px.
	 *
	 * @since 3.0.0
	 *
	 * @return int
	 */
	protected function get_stats_total_item_width() {

		return Helpers::mailer_without_send_confirmation() ? 140 : 120;
	}

	/**
	 * Get view all emails report link.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	protected function get_view_all_link() {

		$link = add_query_arg(
			[
				'orderby' => 'total',
				'order'   => 'desc',
			],
			wp_mail_smtp()->get_admin()->get_admin_page_url( Area::SLUG . '-reports' )
		);

		if ( ! empty( $this->report->get_params( 'date' ) ) ) {
			$link = add_query_arg(
				[
					'timespan' => 'custom',
					'date'     => implode(
						' - ',
						[
							$this->report->get_from_date()->format( 'Y-m-d' ),
							$this->report->get_to_date()->format( 'Y-m-d' ),
						]
					),
				],
				$link
			);
		}

		return $link;
	}

	/**
	 * Get stats columns count.
	 *
	 * @since 3.0.0
	 *
	 * @return int
	 */
	private function get_stats_columns_count() {

		$columns = 3;

		if ( ! Helpers::mailer_without_send_confirmation() ) {
			$columns ++;
		}

		if ( wp_mail_smtp()->get_pro()->get_logs()->is_enabled_open_email_tracking() ) {
			$columns ++;
		}

		if ( wp_mail_smtp()->get_pro()->get_logs()->is_enabled_click_link_tracking() ) {
			$columns ++;
		}

		return $columns;
	}

	/**
	 * Get previous period report for comparison.
	 *
	 * @since 3.0.0
	 *
	 * @return Report
	 */
	private function get_comparison_report() {

		$params = $this->report->get_raw_params();

		if ( ! empty( $this->report->get_params( 'date' ) ) ) {
			$from_date = clone $this->report->get_from_date();
			$to_date   = clone $this->report->get_from_date();

			$from_date->modify( '- ' . ( $this->report->get_date_range() + 1 ) . ' days' );
			$to_date->modify( '- 1 day' );

			$params['date'] = [
				$from_date->format( 'Y-m-d' ),
				$to_date->format( 'Y-m-d' ),
			];
		}

		$report = new Report( $params );

		return $report;
	}
}
