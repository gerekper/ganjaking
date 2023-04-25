<?php

namespace WPMailSMTP\Pro\Emails\Logs\Importers;

/**
 * Class Importers.
 *
 * @since 3.8.0
 */
class Importers {

	/**
	 * Container for the Importer objects.
	 *
	 * @since 3.8.0
	 *
	 * @var ImporterAbstract[]
	 */
	private $importers_object = [];

	/**
	 * Array containing the supported importers.
	 *
	 * @since 3.8.0
	 *
	 * @var string[]
	 */
	private $importers = [
		'wpmaillogging' => WPMailLogging\Importer::class,
	];

	/**
	 * Importers constructor.
	 *
	 * @since 3.8.0
	 */
	public function __construct() {

		$this->init();
	}

	/**
	 * Register hooks.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	public function init() {

		if ( is_admin() && current_user_can( $this->get_manage_capability() ) ) {
			$this->register_importers();
		}
	}

	/**
	 * Register supported importers.
	 *
	 * @since 3.8.0
	 *
	 * @return void
	 */
	private function register_importers() {

		$importers = $this->get_importers();

		foreach ( $importers as $key => $importer ) {

			if ( ! is_a( $importer, ImporterAbstract::class, true ) ) {
				continue;
			}

			$this->importers_object[ $key ] = new $importer();

			$this->importers_object[ $key ]->init();
		}
	}

	/**
	 * Get all importers.
	 *
	 * @since 3.8.0
	 *
	 * @return array
	 */
	private function get_importers() {

		/**
		 * Filter the supported importers.
		 *
		 * @since 3.8.0
		 *
		 * @param array $importers Array containing the supported importers.
		 */
		return apply_filters( 'wp_mail_smtp_pro_emails_logs_importers_get_importers', $this->importers );
	}

	/**
	 * Get import logs manage capability.
	 *
	 * @since 3.8.0
	 *
	 * @return string
	 */
	public function get_manage_capability() {

		/**
		 * Filter import logs manage capability.
		 *
		 * @since 3.8.0
		 *
		 * @param string  $capability Import logs manage capability.
		 */
		return apply_filters( 'wp_mail_smtp_pro_emails_logs_importers_get_manage_capability', 'manage_options' );
	}

	/**
	 * Get the importer object.
	 *
	 * @since 3.8.0
	 *
	 * @param string $importer Importer slug of the importer object to fetch.
	 *
	 * @return ImporterAbstract|null
	 */
	public function get_importer( $importer ) {

		if ( array_key_exists( $importer, $this->importers_object ) ) {
			return $this->importers_object[ $importer ];
		}

		return null;
	}
}
