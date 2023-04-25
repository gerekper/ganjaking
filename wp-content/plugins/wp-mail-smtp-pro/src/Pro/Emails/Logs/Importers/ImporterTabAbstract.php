<?php

namespace WPMailSMTP\Pro\Emails\Logs\Importers;

use WPMailSMTP\Admin\Area;
use WPMailSMTP\Admin\PageAbstract;
use WPMailSMTP\WP;

/**
 * Class ImporterTabAbstract.
 *
 * @since 3.8.0
 */
abstract class ImporterTabAbstract extends PageAbstract implements ImporterTabAbstractInterface {

	/**
	 * Constructor.
	 *
	 * @since 3.8.0
	 *
	 * @param string $parent_page Tab parent page.
	 */
	public function __construct( $parent_page = null ) {

		parent::__construct( $parent_page );

		$this->setup_slug();
	}

	/**
	 * WordPress-related hooks.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	public function hooks() {

		add_action( 'wp_mail_smtp_admin_area_enqueue_assets', [ $this, 'enqueue_assets' ] );
	}

	/**
	 * Enqueue JS and CSS files in the importer tab.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	public function enqueue_assets() {

		$min = WP::asset_min();

		wp_enqueue_script(
			'wp-mail-smtp-tools-importer',
			wp_mail_smtp()->get_pro()->assets_url . "/js/smtp-pro-tools-logs-importer{$min}.js",
			[ 'jquery' ],
			WPMS_PLUGIN_VER,
			true
		);

		/**
		 * Filters the localized JS data.
		 *
		 * @since 3.8.0
		 *
		 * @param array $localized_data Data to be localized.
		 */
		$localized_data = apply_filters(
			'wp_mail_smtp_pro_emails_logs_importers_importer_tab_abstract_scripts_data',
			[
				'nonce' => wp_create_nonce( 'wp-mail-smtp-tools-log-importer-' . $this->get_slug() . '-nonce' ),
			]
		);

		wp_localize_script(
			'wp-mail-smtp-tools-importer',
			'wp_mail_smtp_tools_importer',
			$localized_data
		);
	}

	/**
	 * Display the importer UI.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	public function display() {
		?>
		<form id="wp-mail-smtp-tools-<?php echo esc_attr( $this->slug ); ?>" method="POST" action="<?php echo esc_url( $this->get_link() ); ?>">
			<div class="wp-mail-smtp-setting-row">

				<?php $this->tab_heading(); ?>

				<?php $this->tab_result_notice(); ?>

				<div id="wp-mail-smtp-pro-import-description-section">
					<?php $this->tab_description(); ?>
				</div>

				<div id="wp-mail-smtp-pro-import-progress-section" style="display: none;">
					<?php $this->tab_response_section(); ?>
				</div>

				<div id="wp-mail-smtp-pro-import-summary" style="display: none;">
					<strong><?php echo esc_html__( 'Import Summary:' , 'wp-mail-smtp-pro' ); ?></strong>
					<p>
						<ul>
							<li>
								<?php echo esc_html__( 'Successfully imported:', 'wp-mail-smtp-pro' ); ?> <span id="wp-mail-smtp-pro-import-summary-success-count"></span>
							</li>
							<li>
								<?php echo esc_html__( 'Failed to import:', 'wp-mail-smtp-pro' ); ?> <span id="wp-mail-smtp-pro-import-summary-failed-count"></span>
							</li>
							<li>
								<?php echo esc_html__( 'Failed attachment imports:', 'wp-mail-smtp-pro' ); ?> <span id="wp-mail-smtp-pro-import-summary-attachment-failed-count"></span>
							</li>
						</ul>
					</p>
				</div>

				<?php $this->import_button_html(); ?>
			</div>
		</form>
		<?php
	}

	/**
	 * Notice for import results.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	protected function tab_result_notice() {
		?>
		<div id="wp-mail-smtp-log-importer-result-notice" class="notice wp-mail-smtp-notice inline"><p></p></div>
		<?php
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
		<h2><?php esc_html_e( 'Import Email Logs', 'wp-mail-smtp-pro' ); ?></h2>
		<?php
	}

	/**
	 * Tab description section.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	protected function tab_description() {

		echo esc_html__( 'We found records that need to be imported. Click the import button to get started.', 'wp-mail-smtp-pro' );
	}

	/**
     * Tab ajax response section.
     *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	protected function tab_response_section() {
		?>
		<p>
			<?php echo esc_html__( 'Importing:', 'wp-mail-smtp-pro' ); ?> <span class="wp-mail-smtp-pro-importer-imported-logs-count"></span>/<span class="wp-mail-smtp-pro-importer-logs-to-import-count"></span>
		</p>
		<?php
	}

	/**
	 * Import button HTML.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	public function import_button_html() {
		?>
		<section class="wp-clearfix">
			<p>
				<button type="submit"
						name="wp-mail-smtp-tools-log-importer-button"
						id="wp-mail-smtp-tools-log-importer-button"
						class="wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-orange wp-mail-smtp-tools-log-importer-<?php echo esc_attr( $this->slug ); ?>-button">

					<span class="wp-mail-smtp-btn-text">
						<?php esc_html_e( 'Import', 'wp-mail-smtp-pro' ); ?>
					</span>

				</button>

				<span id="wp-mail-smtp-tools-log-importer-loader" class="wp-mail-smtp-btn-spinner" style="display: none;">
					<?php echo wp_mail_smtp()->prepare_loader( 'white', 'sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</span>

				<div id="wp-mail-smtp-tools-process-msg"
					class="wp-mail-smtp-tools-<?php echo esc_attr( $this->get_slug() ); ?>-process-msg">
				</div>
			</p>
		</section>
		<?php
	}

	/**
	 * URL to the importer tab.
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	public function get_link() {

		return wp_mail_smtp()->get_admin()->get_admin_page_url( Area::SLUG . '-tools&tab=' . $this->get_slug() );
	}
}
