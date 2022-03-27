<?php

namespace ACP\Plugin\Update;

use AC\Plugin\Update;
use AC\Plugin\Version;
use ACP\Type\Activation\Status;
use DateTime;
use DateTimeZone;
use Exception;

/**
 * Migrate license settings per site
 */
class V5000 extends Update {

	const LICENSE_OPTION_KEY = 'cpupdate_cac-pro';

	public function __construct() {
		parent::__construct( new Version( '5.0.0' ) );
	}

	/**
	 * @throws Exception
	 */
	public function apply_update() {
		$this->migrate_license();
	}

	private function migrate_license() {
		$license_key_value = defined( 'ACP_LICENCE' ) && ACP_LICENCE
			? ACP_LICENCE
			: (string) $this->get_license_option();

		if ( ! $license_key_value ) {
			return;
		}

		$status = $this->get_license_option( '_sts' );

		if ( ! Status::is_valid( $status ) ) {
			return;
		}

		$expiry_date = null;

		$expiry_date_raw = $this->get_license_option( '_expiry_date' );

		if ( $expiry_date_raw ) {
			$expiry_date = is_numeric( $expiry_date_raw )
				? DateTime::createFromFormat( 'U', $expiry_date_raw, new DateTimeZone( 'Europe/Amsterdam' ) )
				: DateTime::createFromFormat( 'Y-m-d H:i:s', $expiry_date_raw, new DateTimeZone( 'Europe/Amsterdam' ) );
		}

		if ( $expiry_date === false ) {
			return;
		}

		$renewal_method = $this->get_license_option( '_renewal_method' );

		if ( $renewal_method !== 'manual' ) {
			$renewal_method = 'auto';
		}

		$this->update_option( 'acp_subscription_key', $license_key_value );
		$this->update_option( 'acp_subscription_details_key', $license_key_value );
		$this->update_option( 'acp_subscription_details', [
			'status'         => $status,
			'renewal_method' => $renewal_method,
			'expiry_date'    => $expiry_date ? $expiry_date->getTimestamp() : null,
		] );
	}

	protected function get_license_option( $key = '' ) {
		return $this->get_option( self::LICENSE_OPTION_KEY . $key );
	}

	protected function get_option( $key ) {
		return get_option( $key );
	}

	protected function update_option( $key, $value ) {
		return update_option( $key, $value );
	}

}