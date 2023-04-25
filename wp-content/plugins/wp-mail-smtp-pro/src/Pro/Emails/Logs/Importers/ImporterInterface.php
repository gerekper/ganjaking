<?php

namespace WPMailSMTP\Pro\Emails\Logs\Importers;

/**
 * Interface ImporterInterface.
 *
 * @since 3.8.0
 */
interface ImporterInterface {

	/**
	 * Initialized the importer.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	public function init();

	/**
	 * Whether the requirements to initialized/support importer is satisfied.
	 *
	 * @since 3.8.0
	 *
	 * @return bool
	 */
	public function requirements_satisfied();

	/**
	 * Returns the importer slug.
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	public static function get_slug();

	/**
	 * Returns the number of logs that can be imported to WP Mail SMTP.
	 *
	 * @since 3.8.0
	 *
	 * @param bool $get_fresh Whether to use the cached data or get the fresh data. Default `false`.
	 *
	 * @return int
	 */
	public function get_logs_to_import_count( $get_fresh = false );

	/**
	 * Returns the Tab class of the importer.
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	public function get_tab_class();

	/**
	 * AJAX-related hooks.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	public function ajax();

	/**
	 * Whether the ajax request is valid or not.
	 *
	 * @since 3.8.0
	 *
	 * @return bool
	 */
	public function is_ajax_request_valid();

	/**
	 * Perform the import.
	 *
	 * @since 3.8.0
	 *
	 * @param array $data Contains data regarding the import action.
	 */
	public function perform_import_process( $data );

	/**
	 * Get the saved options for the importer from `wp_options`.
	 *
	 * @since 3.8.0
	 *
	 * @return mixed
	 */
	public function get_saved_options();

	/**
	 * Save the importer options in `wp_options` table.
	 *
	 * @since 3.8.0
	 *
	 * @param array $options Array containing the data to be saved.
	 *
	 * @return bool
	 */
	public function save_options( $options );
}
