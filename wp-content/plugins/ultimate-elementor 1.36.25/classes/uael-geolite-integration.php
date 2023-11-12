<?php
/**
 * MaxMind Geolocation Integration
 *
 * @package UAEL
 */

namespace UltimateElementor\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class UAEL_Geolite_Integration
 */
class UAEL_Geolite_Integration {
	/**
	 * MaxMind GeoLite2 database path.
	 *
	 * @var string
	 * @since 1.35.1
	 */
	private $database;

	/**
	 * Constructor.
	 *
	 * @param string $database MaxMind GeoLite2 database path.
	 * @since 1.35.1
	 */
	public function __construct( $database ) {
		$this->database = $database;

		if ( ! class_exists( 'Maxmind\Db\\Reader', false ) ) {
			$this->require_geolite_library();
		}
	}

	/**
	 * Get country 2-letters ISO by IP address.
	 * Retuns empty string when not able to find any ISO code.
	 *
	 * @param string $ip_address User IP address.
	 * @return string
	 * @since 1.35.1
	 */
	public function get_country_iso( $ip_address ) {
		$iso_code = '';

		try {
			$reader   = new Maxmind\Db\Reader( $this->database );
			$data     = $reader->get( $ip_address );
			$iso_code = $data['country']['iso_code'];

			$reader->close();
		} catch ( \Exception $e ) {
			return $e;
		}

		return sanitize_text_field( strtoupper( $iso_code ) );
	}

	/**
	 * Require maxmind library.
	 *
	 * @since 1.35.1
	 */
	private function require_geolite_library() {
		require_once UAEL_DIR . 'lib/MaxMind/Db/Reader/Decoder.php';
		require_once UAEL_DIR . 'lib/MaxMind/Db/Reader/InvalidDatabaseException.php';
		require_once UAEL_DIR . 'lib/MaxMind/Db/Reader/Metadata.php';
		require_once UAEL_DIR . 'lib/MaxMind/Db/Reader/Util.php';
		require_once UAEL_DIR . 'lib/MaxMind/Db/Reader.php';
	}
}
