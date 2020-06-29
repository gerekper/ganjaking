<?php

namespace ACP\Plugin\Update;

use AC\Plugin\Update;
use ACP\Entity\License;
use ACP\LicenseKeyRepository;
use ACP\LicenseRepository;
use ACP\Type\License\ExpiryDate;
use ACP\Type\License\Key;
use ACP\Type\License\RenewalDiscount;
use ACP\Type\License\RenewalMethod;
use ACP\Type\License\Status;
use DateTime;
use DateTimeZone;
use Exception;

/**
 * Migrate license settings per site
 */
class V5000 extends Update {

	const LICENSE_OPTION_KEY = 'cpupdate_cac-pro';

	/**
	 * @throws Exception
	 */
	public function apply_update() {
		$this->migrate_license();
	}

	protected function set_version() {
		$this->version = '5.0.0';
	}

	private function migrate_license() {
		$license_key_value = defined( 'ACP_LICENCE' ) && ACP_LICENCE
			? ACP_LICENCE
			: (string) $this->get_license_option();

		if ( ! Key::is_valid( $license_key_value ) ) {
			return;
		}

		$status = $this->get_license_option( '_sts' );

		if ( ! Status::is_valid( $status ) ) {
			return;
		}

		$expiry_date = $this->get_license_option( '_expiry_date' )
			? DateTime::createFromFormat( 'U', $this->get_license_option( '_expiry_date' ), new DateTimeZone( 'Europe/Amsterdam' ) )
			: null;

		if ( $expiry_date === false ) {
			return;
		}

		$renewal_method = $this->get_license_option( '_renewal_method' );

		if ( ! RenewalMethod::is_valid( $renewal_method ) ) {
			$renewal_method = RenewalMethod::METHOD_MANUAL;
		}

		$discount = (int) $this->get_license_option( '_renewal_discount' );

		if ( ! RenewalDiscount::is_valid( $discount ) ) {
			$discount = 0;
		}

		$license = new License(
			new Key( $license_key_value ),
			new Status( $status ),
			new RenewalDiscount( $discount ),
			new RenewalMethod( $renewal_method ),
			new ExpiryDate( $expiry_date )
		);

		$this->save_license( $license );
	}

	protected function save_license( License $license ) {
		( new LicenseRepository() )->save( $license );
		( new LicenseKeyRepository() )->save( $license->get_key() );
	}

	protected function get_license_option( $key = '' ) {
		return get_option( self::LICENSE_OPTION_KEY . $key );
	}

}