<?php

namespace WPMailSMTP\Pro\Emails\Logs\Importers;

use WP_Error;
use WPMailSMTP\Admin\Area;

/**
 * Class ImporterAbstract.
 *
 * @since 3.8.0
 */
abstract class ImporterAbstract implements ImporterInterface {

	/**
	 * Saved options in the DB.
	 *
	 * @since 3.8.0
	 *
	 * @var null|false|array
	 */
	private $saved_options = null;

	/**
	 * Initialized the importer.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	public function init() {

		if ( ! $this->requirements_satisfied() ) {
			return;
		}

		$this->hooks();
	}

	/**
	 * WordPress-related hooks.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	public function hooks() {

		add_filter( 'wp_mail_smtp_admin_page_tools_tabs', [ $this, 'setup_admin_tab' ] );
		add_filter( 'wp_mail_smtp_pro_emails_logs_importers_importer_tab_abstract_scripts_data', [ $this, 'add_logs_to_import_count' ] );
		add_action( 'wp_ajax_wp_mail_smtp_importer_ajax_' . static::get_slug(), [ $this, 'ajax' ] );
	}

	/**
	 * Include the importer tab in Tools page.
	 *
	 * @since 3.8.0
	 *
	 * @param array $tabs Array containing the tabs in WP Mail SMTP admin tools page.
	 *
	 * @return array
	 */
	public function setup_admin_tab( $tabs ) {

		$tabs[ static::get_slug() ] = $this->get_tab_class();

		return $tabs;
	}

	/**
	 * Add the logs to import count to the localized JS data.
	 *
	 * @since 3.8.0
	 *
	 * @param array $data Data to be localized to Importer JS script.
	 *
	 * @return array
	 */
	public function add_logs_to_import_count( $data ) {

		$data['logs_to_import_count'] = absint( $this->get_logs_to_import_count( true ) );

		return $data;
	}

	/**
	 * Perform AJAX import action.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	public function ajax() {

		if ( ! check_ajax_referer( 'wp-mail-smtp-tools-log-importer-' . static::get_slug() . '-nonce', 'nonce', false ) ) {
			wp_send_json( esc_html__( 'Access rejected.', 'wp-mail-smtp-pro' ), 403 );
		}

		if ( ! $this->is_ajax_request_valid() ) {
			wp_send_json( esc_html__( 'Import process rejected.', 'wp-mail-smtp-pro' ), 403 );
		}

		$this->perform_import_process( $_POST );

		/*
		 * In theory, we shouldn't have end up here. But in case, let's send an error
		 * response to the client.
		 */
		$error = new WP_Error(
			'wp_mail_smtp_tools_import_error',
			esc_html__( 'Import operation failed.', 'wp-mail-smtp-pro' ),
			[
				'importer' => static::get_slug(),
			]
		);

		wp_send_json_error(
			$error,
			400
		);
	}

	/**
	 * Whether the ajax request is valid or not.
	 *
	 * @since 3.8.0
	 *
	 * @return bool
	 */
	public function is_ajax_request_valid() {

		return current_user_can( wp_mail_smtp()->get_pro()->get_importers()->get_manage_capability() );
	}

	/**
	 * Get the key of the saved importer options.
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	protected function get_options_key() {

		return 'wp_mail_smtp_logs_' . static::get_slug();
	}

	/**
	 * Get the saved options for the importer from `wp_options`.
	 *
	 * @since 3.8.0
	 *
	 * @return mixed
	 */
	public function get_saved_options() {

		if ( ! is_null( $this->saved_options ) ) {
			return $this->saved_options;
		}

		$this->saved_options = get_option( $this->get_options_key(), [] );

		return $this->saved_options;
	}

	/**
	 * Save the importer options in `wp_options` table.
	 *
	 * @since 3.8.0
	 *
	 * @param array $options Array containing the data to be saved.
	 *
	 * @return bool
	 */
	public function save_options( $options ) {

		return update_option( $this->get_options_key(), $options, false );
	}

	/**
	 * Update saved options.
	 *
	 * @since 3.8.0
	 *
	 * @param array $data Array of the data to be saved/updated.
	 *
	 * @return bool
	 */
	public function update_options( $data ) {

		$new_options   = [];
		$saved_options = $this->get_saved_options();

		// Loop through each of the currently saved options.
		foreach ( $saved_options as $k => $v ) {
			if ( array_key_exists( $k, $data ) ) {
				// Update if necessary.
				$new_options[ $k ] = $data[ $k ];
			} else {
				// Make sure existing options are not removed.
				$new_options[ $k ] = $v;
			}
		}

		// We also want to save the new data.
		foreach ( $data as $k => $v ) {
			if ( ! array_key_exists( $k, $saved_options ) ) {
				$new_options[ $k ] = $v;
			}
		}

		// Save the new options.
		return $this->save_options( $new_options );
	}

	/**
	 * Returns the URL to the importer page.
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	public function get_importer_page_link() {

		return wp_mail_smtp()->get_admin()->get_admin_page_url( Area::SLUG . '-tools&tab=' . static::get_slug() );
	}
}
