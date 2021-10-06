<?php

namespace WPMailSMTP\Pro\Emails\Logs\Export;

use WPMailSMTP\WP;
use WPMailSMTP\Admin\Pages\ExportTab;

/**
 * HTML-related stuff for Admin page.
 *
 * @since 2.8.0
 */
class Admin extends ExportTab {

	/**
	 * Export request.
	 *
	 * @since 2.8.0
	 *
	 * @var Request Export request.
	 */
	protected $request;

	/**
	 * Register hooks.
	 *
	 * @since 2.8.0
	 */
	public function hooks() {

		add_action( 'wp_mail_smtp_admin_area_enqueue_assets', [ $this, 'enqueue_assets' ] );
	}

	/**
	 * Enqueue required JS and CSS.
	 *
	 * @since 2.8.0
	 */
	public function enqueue_assets() {

		$min = WP::asset_min();

		wp_enqueue_style(
			'wp-mail-smtp-flatpickr',
			wp_mail_smtp()->assets_url . '/css/vendor/flatpickr.min.css',
			[],
			'4.6.9'
		);
		wp_enqueue_script(
			'wp-mail-smtp-flatpickr',
			wp_mail_smtp()->assets_url . '/js/vendor/flatpickr.min.js',
			[ 'jquery' ],
			'4.6.9',
			true
		);

		wp_enqueue_style(
			'wp-mail-smtp-tools-export-email-logs',
			wp_mail_smtp()->plugin_url . '/assets/pro/css/smtp-pro-tools-logs-export.min.css',
			[],
			WPMS_PLUGIN_VER
		);
		wp_enqueue_script(
			'wp-mail-smtp-tools-export-email-logs',
			wp_mail_smtp()->plugin_url . "/assets/pro/js/smtp-pro-tools-logs-export{$min}.js",
			[ 'jquery', 'wp-mail-smtp-flatpickr' ],
			WPMS_PLUGIN_VER,
			true
		);

		wp_localize_script(
			'wp-mail-smtp-tools-export-email-logs',
			'wp_mail_smtp_tools_export_email_logs',
			$this->get_localized_data()
		);
	}

	/**
	 * Get localized data.
	 *
	 * @since 2.8.0
	 *
	 * @return array
	 */
	private function get_localized_data() {

		return [
			'nonce'       => wp_create_nonce( 'wp-mail-smtp-tools-export-email-logs-nonce' ),
			'lang_code'   => sanitize_key( WP::get_language_code() ),
			'export_page' => esc_url( $this->get_link() ),
			'i18n'        => Export::get_config( 'i18n' ),
		];
	}

	/**
	 * Output HTML of the Email Logs export form.
	 *
	 * @since 2.8.0
	 */
	public function display() {

		$this->request = new Request( 'GET', false );
		?>
		<form method="post" action="<?php echo esc_url( $this->get_link() ); ?>" id="wp-mail-smtp-tools-export-email-logs">

			<input type="hidden" name="action" value="wp_mail_smtp_tools_export_email_logs">
			<?php wp_nonce_field( 'wp-mail-smtp-tools-export-email-logs-nonce', 'nonce' ); ?>

			<div class="wp-mail-smtp-setting-row">

				<h2><?php esc_html_e( 'Export Email Logs', 'wp-mail-smtp-pro' ); ?></h2>

				<section class="wp-clearfix" id="wp-mail-smtp-tools-export-email-logs-export-type">
					<h5><?php esc_html_e( 'Export Type', 'wp-mail-smtp-pro' ); ?></h5>
					<?php $this->display_export_type_block(); ?>
				</section>

				<section class="wp-clearfix" id="wp-mail-smtp-tools-export-email-logs-common-fields">
					<h5><?php esc_html_e( 'Common Information', 'wp-mail-smtp-pro' ); ?></h5>
					<?php $this->display_common_info_selection_block(); ?>
				</section>

				<section class="wp-clearfix" id="wp-mail-smtp-tools-export-email-logs-additional-info">
					<h5><?php esc_html_e( 'Additional Information', 'wp-mail-smtp-pro' ); ?></h5>
					<?php $this->display_additional_info_selection_block(); ?>
				</section>

				<section class="wp-clearfix" id="wp-mail-smtp-tools-export-email-logs-date">
					<h5><?php esc_html_e( 'Custom Date Range', 'wp-mail-smtp-pro' ); ?></h5>
					<?php $this->display_date_range_block(); ?>
				</section>

				<section class="wp-clearfix" id="wp-mail-smtp-tools-export-email-logs-search">
					<h5><?php esc_html_e( 'Search', 'wp-mail-smtp-pro' ); ?></h5>
					<?php $this->display_search_block(); ?>
				</section>

				<section class="wp-clearfix">
					<button type="submit" name="submit-export-email-logs" id="wp-mail-smtp-tools-export-email-logs-submit"
									class="wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-orange">
						<span class="wp-mail-smtp-btn-text">
							<?php esc_html_e( 'Download Export File', 'wp-mail-smtp-pro' ); ?>
						</span>
						<span class="wp-mail-smtp-btn-spinner">
								<?php echo wp_mail_smtp()->prepare_loader( 'white', 'sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</span>
					</button>
					<a href="#" class="hidden" id="wp-mail-smtp-tools-export-email-logs-cancel">
						<?php esc_html_e( 'Cancel', 'wp-mail-smtp-pro' ); ?>
					</a>
					<div id="wp-mail-smtp-tools-export-email-logs-process-msg"></div>
				</section>
			</div>
		</form>
		<?php
	}

	/**
	 * Common fields checkboxes block HTML.
	 *
	 * @since 2.8.0
	 */
	private function display_common_info_selection_block() {

		$fields   = Export::get_common_fields();
		$selected = $this->request->get_arg( 'common_fields' );

		foreach ( $fields as $key => $name ) {
			printf(
				'<label><input type="checkbox" name="common_fields[]" value="%1$s" %2$s> %3$s</label>',
				esc_attr( $key ),
				esc_attr( $this->get_checked_property( $key, $selected ) ),
				esc_html( $name )
			);
		}
	}

	/**
	 * Additional fields checkboxes block HTML.
	 *
	 * @since 2.8.0
	 */
	private function display_additional_info_selection_block() {

		$fields   = Export::get_additional_fields();
		$selected = $this->request->get_arg( 'additional_fields' );

		foreach ( $fields as $key => $name ) {
			printf(
				'<label><input type="checkbox" name="additional_fields[]" value="%1$s" %2$s> %3$s</label>',
				esc_attr( $key ),
				esc_attr( $this->get_checked_property( $key, $selected, '' ) ),
				esc_html( $name )
			);
		}
	}

	/**
	 * Export type block HTML.
	 *
	 * @since 2.8.0
	 */
	private function display_export_type_block() {

		$fields   = Export::get_export_types();
		$selected = $this->request->get_arg( 'export_type' );

		foreach ( $fields as $key => $name ) {
			printf(
				'<label><input type="radio" name="export_type" value="%1$s" %2$s> %3$s</label>',
				esc_attr( $key ),
				esc_attr( $this->get_checked_property( $key, [ $selected ], '' ) ),
				esc_html( $name )
			);
		}
	}

	/**
	 * Date range block HTML.
	 *
	 * @since 2.8.0
	 */
	private function display_date_range_block() {

		$date = implode( ' - ', $this->request->get_arg( 'date' ) );
		?>

		<input type="text" name="date" class="wp-mail-smtp-date-selector"
					 id="wp-mail-smtp-tools-export-email-logs-date-flatpickr"
					 placeholder="<?php esc_attr_e( 'Select a date range', 'wp-mail-smtp-pro' ); ?>"
					 value="<?php echo esc_attr( $date ); ?>">
		<?php
	}

	/**
	 * Search block HTML.
	 *
	 * @since 2.8.0
	 */
	private function display_search_block() {

		$search = $this->request->get_arg( 'search' );
		?>
		<select name="search[place]" class="wp-mail-smtp-search-box-field">
			<option value="people" <?php selected( 'people', $search['place'] ); ?>>
				<?php esc_html_e( 'Email Addresses', 'wp-mail-smtp-pro' ); ?>
			</option>
			<option value="headers" <?php selected( 'headers', $search['place'] ); ?>>
				<?php esc_html_e( 'Subject & Headers', 'wp-mail-smtp-pro' ); ?>
			</option>
			<option value="content" <?php selected( 'content', $search['place'] ); ?>>
				<?php esc_html_e( 'Content', 'wp-mail-smtp-pro' ); ?>
			</option>
		</select>

		<input type="text" name="search[term]" class="wp-mail-smtp-search-box-term" value="<?php echo esc_attr( $search['term'] ); ?>">
		<?php
	}

	/**
	 * Get checked property according to value and array of values.
	 * Only for checkboxes.
	 *
	 * @since 2.8.0
	 *
	 * @param string $val     Value.
	 * @param array  $arr     Array of values.
	 * @param string $default Default value ' checked' OR ''.
	 *
	 * @return string
	 */
	public function get_checked_property( $val, $arr, $default = ' checked' ) {

		$checked = ' checked' !== $default ? '' : $default;
		if ( empty( $arr ) || ! is_array( $arr ) ) {
			return $checked;
		}

		$checked = ' checked';
		if ( ! in_array( $val, $arr, true ) ) {
			$checked = '';
		}

		return $checked;
	}
}
