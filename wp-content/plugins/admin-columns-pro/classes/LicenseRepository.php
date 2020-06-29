<?php

namespace ACP;

use ACP\Entity\License;
use ACP\Type\License\ExpiryDate;
use ACP\Type\License\Key;
use ACP\Type\License\RenewalDiscount;
use ACP\Type\License\RenewalMethod;
use ACP\Type\License\Status;
use DateTime;
use Exception;

final class LicenseRepository {

	const STORAGE_LICENSE = 'acp_subscription_details';
	const STORAGE_KEY = 'acp_subscription_details_key';

	const PARAM_STATUS = 'status';
	const PARAM_RENEWAL_DISCOUNT = 'renewal_discount';
	const PARAM_RENEWAL_METHOD = 'renewal_method';
	const PARAM_EXPIRY_DATE = 'expiry_date';

	/** @var bool */
	private $network_activated;

	public function __construct( $network_activated = false ) {
		$this->network_activated = (bool) $network_activated;
	}

	/**
	 * @param Key $license_key
	 *
	 * @return License|null
	 */
	public function find( Key $license_key ) {
		// Fetch and check license key
		$stored_license_key_value = $this->get_option( self::STORAGE_KEY );

		if ( ! Key::is_valid( $stored_license_key_value ) ) {
			return null;
		}

		$stored_license_key = new Key( $stored_license_key_value );

		if ( ! $stored_license_key->equals( $license_key ) ) {
			return null;
		}

		$data = $this->get_option( self::STORAGE_LICENSE );

		if ( empty( $data ) ) {
			return null;
		}

		// Check required params
		$params = [
			self::PARAM_STATUS,
			self::PARAM_RENEWAL_DISCOUNT,
			self::PARAM_RENEWAL_METHOD,
			self::PARAM_EXPIRY_DATE,
		];

		foreach ( $params as $param ) {
			if ( ! array_key_exists( $param, $data ) ) {
				return null;
			}
		}

		if ( ! Status::is_valid( $data[ self::PARAM_STATUS ] ) ) {
			return null;
		}

		if ( ! RenewalMethod::is_valid( $data[ self::PARAM_RENEWAL_METHOD ] ) ) {
			return null;
		}

		$discount = 0;

		if ( RenewalDiscount::is_valid( $data[ self::PARAM_RENEWAL_DISCOUNT ] ) ) {
			$discount = $data[ self::PARAM_RENEWAL_DISCOUNT ];
		}

		if ( null === $data[ self::PARAM_EXPIRY_DATE ] ) {
			$expire_date = null;
		} else {
			try {
				$expire_date = new DateTime();
				$expire_date->setTimestamp( $data[ self::PARAM_EXPIRY_DATE ] );
			} catch ( Exception $e ) {
				return null;
			}
		}

		return new License(
			$stored_license_key,
			new Status( $data[ self::PARAM_STATUS ] ),
			new RenewalDiscount( $discount ),
			new RenewalMethod( $data[ self::PARAM_RENEWAL_METHOD ] ),
			new ExpiryDate( $expire_date )
		);
	}

	public function save( License $license ) {
		$data = [
			self::PARAM_STATUS           => $license->get_status()->get_value(),
			self::PARAM_RENEWAL_DISCOUNT => $license->get_renewal_discount()->get_value(),
			self::PARAM_RENEWAL_METHOD   => $license->get_renewal_method()->get_value(),
			self::PARAM_EXPIRY_DATE      => $license->get_expiry_date()->exists() ? $license->get_expiry_date()->get_value()->getTimestamp() : null,
		];

		$this->update_option( self::STORAGE_LICENSE, $data );
		$this->update_option( self::STORAGE_KEY, $license->get_key()->get_value() );
	}

	public function delete() {
		$this->delete_option( self::STORAGE_LICENSE );
		$this->delete_option( self::STORAGE_KEY );
	}

	private function get_option( $option ) {
		return $this->network_activated
			? get_site_option( $option )
			: get_option( $option );
	}

	private function update_option( $option, $data ) {
		$this->network_activated
			? update_site_option( $option, $data )
			: update_option( $option, $data, false );
	}

	private function delete_option( $option ) {
		$this->network_activated
			? delete_site_option( $option )
			: delete_option( $option );
	}

}