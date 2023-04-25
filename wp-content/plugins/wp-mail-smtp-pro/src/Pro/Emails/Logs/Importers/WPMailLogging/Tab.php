<?php

namespace WPMailSMTP\Pro\Emails\Logs\Importers\WPMailLogging;

use WPMailSMTP\Pro\Emails\Logs\Importers\ImporterAbstract;
use WPMailSMTP\Pro\Emails\Logs\Importers\ImporterTabAbstract;
use WPMailSMTP\WP;

/**
 * Class Tab.
 *
 * Handles the tab/page display of WP Mail Logging Importer.
 *
 * @since 3.8.0
 */
class Tab extends ImporterTabAbstract {

	/**
	 * The importer associated with this importer tab.
	 *
	 * @since 3.8.0
	 *
	 * @var null|false|ImporterAbstract
	 */
	private $importer = null;

	/**
	 * Assign the property `slug`.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	public function setup_slug() {

		$this->slug = Importer::get_slug();
	}

	/**
	 * Get the importer object associated with this importer tab.
	 *
	 * @since 3.8.0
	 *
	 * @return ImporterAbstract|null
	 */
	public function get_importer() {

		if ( $this->importer === false ) {
			return null;
		}

		if ( ! is_null( $this->importer ) ) {
			return $this->importer;
		}

		$importer = wp_mail_smtp()->get_pro()->get_importers()->get_importer( 'wpmaillogging' );

		if ( ! $importer ) {
			$this->importer = false;

			return null;
		}

		$this->importer = $importer;

		return $this->importer;
	}

	/**
	 * WP-related hooks.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	public function hooks() {

		parent::hooks();
		add_filter( 'wp_mail_smtp_pro_emails_logs_importers_importer_tab_abstract_scripts_data', [ $this, 'add_localized_strings' ] );
	}

	/**
	 * Add strings to JS.
	 *
	 * @since 3.8.0
	 *
	 * @param array $data Data to be localized.
	 *
	 * @return array
	 */
	public function add_localized_strings( $data ) {

		$data['notice_success'] = esc_html__( 'Successful import. All email logs were successfully imported.', 'wp-mail-smtp-pro' );
		$data['notice_warning'] = esc_html__( 'Some items failed to import.', 'wp-mail-smtp-pro' );
		$data['notice_fail']    = esc_html__( 'Import failed!', 'wp-mail-smtp-pro' );

		return $data;
	}

	/**
	 * Label of the tab.
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	public function get_label() {

		return esc_html__( 'WP Mail Logging Importer', 'wp-mail-smtp-pro' );
	}

	/**
	 * Tab description section.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	public function tab_description() {

		if ( empty( $this->get_importer() ) ) {
			return;
		}

		$options = $this->get_importer()->get_saved_options();

		if ( ! empty( $options['last_complete_import_date'] ) ) {
			$this->display_repeat_import_description( $options['last_complete_import_date'] );

			return;
		}

		$this->display_first_time_import_description();
	}

	/**
	 * Tab heading.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	protected function tab_heading() {
		?>
		<h2><?php esc_html_e( 'WP Mail Logging Importer', 'wp-mail-smtp-pro' ); ?></h2>
		<?php
	}

	/**
	 * Description for repeat import operation.
	 *
	 * @since 3.8.0
	 *
	 * @param int $last_import_date Timestamp of the last import.
	 *
	 * @return void
	 */
	private function display_repeat_import_description( $last_import_date ) {

		$last_import_date = date_create( '@' . $last_import_date );

		if ( empty( $last_import_date ) ) {
			return;
		}

		$last_import_date->setTimezone( WP::wp_timezone() );
		?>
		<div class="notice wp-mail-smtp-notice inline notice-info" style="display: block;">
			<p>
			<?php
			printf(
				/* translators: Date time of the last import operation. */
				esc_html__(
					'You recently imported email logs on %s. Performing this action again will import all email logs from the WP Mail Logging plugin and might result in duplicate logs.',
					'wp-mail-smtp-pro'
				),
				esc_html( $last_import_date->format( get_option( 'date_format' ) ) )
			)
			?>
			</p>
		</div>
		<?php
		$this->display_first_time_import_description();
	}

	/**
	 * Description for first time import operation.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	private function display_first_time_import_description() {

		$logs_count = 0;

		if ( ! empty( $this->get_importer() ) ) {
			$logs_count = $this->get_importer()->get_logs_to_import_count( true );
		}
		?>
		<p>
			<?php
			printf(
				/* translators: %d - Number of logs to be imported. */
				esc_html__( 'We found %d WP Mail Logging email logs available for import to WP Mail SMTP. Do you want to import them?', 'wp-mail-smtp-pro' ),
				absint( $logs_count )
			);
			?>
		</p>
		<?php
	}
}
