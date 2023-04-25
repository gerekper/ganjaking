<?php

namespace WPMailSMTP\Pro\Emails\Logs\Importers;

interface ImporterTabAbstractInterface {

	/**
	 * Get the importer object.
	 *
	 * @since 3.8.0
	 *
	 * @return ImporterAbstract
	 */
	public function get_importer();

	/**
	 * Assign the property `slug`.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	public function setup_slug();
}
